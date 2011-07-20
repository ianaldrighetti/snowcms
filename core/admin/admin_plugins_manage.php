<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

if(!defined('INSNOW'))
{
	die('Nice try...');
}

// Title: Control Panel - Plugins - Manage

if(!function_exists('admin_plugins_manage'))
{
	/*
		Function: admin_plugins_manage

		Provides the interface for managing plugins.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_manage()
	{
		api()->run_hooks('admin_plugins_manage');

		// Can you manage plugin settings?
		if(!member()->can('manage_plugins'))
		{
			// That's what I thought!
			admin_access_denied();
		}

		// Generate the table which shows all the plugin information :-)
		admin_plugins_manage_generate_table();
		$table = api()->load_class('Table');

		// Activating, deactivating or deleting a plugin..?
		if(!empty($_GET['activate']) || !empty($_GET['deactivate']) || !empty($_GET['delete']))
		{
			// Gotta make sure it's you ;-)
			verify_request('get');

			// Just use the function used in the table.
			admin_plugins_manage_table_handle(!empty($_GET['activate']) ? 'activate' : (!empty($_GET['deactivate']) ? 'deactivate' : 'delete'), array(!empty($_GET['activate']) ? $_GET['activate'] : (!empty($_GET['deactivate']) ? $_GET['deactivate'] : $_GET['delete'])));
		}

		admin_current_area('plugins_manage');

		theme()->set_title(l('Manage Plugins'));

		api()->context['table'] = $table;

		theme()->render('admin_plugins_manage');
	}
}

if(!function_exists('admin_plugins_manage_generate_table'))
{
	/*
		Function: admin_plugins_manage_generate_table

		Generates the table which shows all the plugin information.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_manage_generate_table()
	{
		// Only display plugins for directories that exist.
		$plugin_dirs = scandir(plugindir);

		foreach($plugin_dirs as $index => $directory)
		{
			// We don't want ., .., or objects that are not directories.
			if($directory == '.' || $directory == '..' || !is_dir(plugindir. '/'. $directory))
			{
				unset($plugin_dirs[$index]);
			}
		}

		$table = api()->load_class('Table');

		// Add our table.
		$table->add('manage_plugins_table', array(
																					'base_url' => baseurl. '/index.php?action=admin&amp;sa=plugins_manage',
																					'db_query' => '
																						SELECT
																							guid, directory, runtime_error, is_activated, available_update
																						FROM {db->prefix}plugins
																						WHERE directory IN('. (count($plugin_dirs) > 0 ? '{string_array:directories}' : 'NULL'). ')',
																					'db_vars' => array(
																												 'directories' => $plugin_dirs,
																											 ),
																					'primary' => 'guid',
																					'sort' => array('guid', 'desc'),
																					'options' => array(
																												 'activate' => l('Activate'),
																												 'deactivate' => l('Deactivate'),
																												 'update' => l('Update'),
																												 'delete' => l('Delete'),
																											 ),
																					'callback' => 'admin_plugins_manage_table_handle',
																					'cellpadding' => '5px',
																				));

		$table->add_column('manage_plugins_table', 'name', array(
																												 'label' => l('Plugin'),
																												 'title' => l('Plugin name'),
																												 'function' => create_function('$row', '
																																				 $plugin_info = plugin_load(plugindir. \'/\'. $row[\'directory\']);

																																				 return \'<p style="font-weight: bold; margin-bottom: 10px;">\'. $plugin_info[\'name\']. \'</p><p>\'. (!empty($row[\'is_activated\']) ? \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;deactivate=\'. urlencode($row[\'guid\']). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'Deactivate this plugin\'). \'">\'. l(\'Deactivate\'). \'</a>\' : \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;activate=\'. urlencode($row[\'guid\']). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'Activate this plugin\'). \'">\'. l(\'Activate\'). \'</a> | <a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;delete=\'. urlencode($row[\'guid\']). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'Delete this plugin\'). \'" onclick="return confirm(\\\'\'. l(\'Are you sure you want to delete this plugin?\'). \'\\\');">\'. l(\'Delete\'). \'</a>\'). \'</p>\';'),
																												 'width' => '20%',
																											 ));

		$table->add_column('manage_plugins_table', 'description', array(
																																'label' => l('Description'),
																																 'title' => l('Plugin information'),
																																 'sortable' => true,
																																 'function' => create_function('$row', '
																																								 $plugin_info = plugin_load(plugindir. \'/\'. $row[\'directory\']);

																																								 // Let\'s get some extra information displayed too.
																																								 $plugin_data = array();

																																								 if(!empty($plugin_info[\'version\']))
																																								 {
																																									 $plugin_data[] = \'Version \'. $plugin_info[\'version\'];
																																								 }

																																								 if(!empty($plugin_info[\'author\']))
																																								 {
																																									 $plugin_data[] = l(\'By %s\', ((!empty($plugin_info[\'website\']) ? \'<a href="\'. $plugin_info[\'website\']. \'" target="_blank">\' : \'\'). $plugin_info[\'author\']. (!empty($plugin_info[\'website\']) ? \'</a>\' : \'\')));
																																								 }

																																								 if(!empty($row[\'runtime_error\']))
																																								 {
																																									 switch($row[\'runtime_error\'])
																																									 {
																																										 case 1:
																																											 $error_string = l(\'Could not find plugin.php\');
																																											 break;

																																										 case 2:
																																											 $error_string = l(\'Plugin caused a fatal PHP error\');
																																											 break;
																																									 }

																																									 if(!empty($error_string))
																																									 {
																																										 $plugin_data[] = \'<span style="font-weight: bold;">\'. l(\'Error:\'). \'</span> <span style="color: red;">\'. $error_string. \'</span>\';
																																									 }
																																								 }

																																								 if(!empty($row[\'available_update\']))
																																								 {
																																									 $plugin_data[] = \'<span style="font-weight: bold;">\'. l(\'v%s of this plugin is available! <a href="%s/index.php?action=admin&amp;sa=plugins_manage&amp;update=%s&amp;version=%s&amp;sid=%s">Update now</a>.\', $row[\'available_update\'], baseurl, urlencode($row[\'guid\']), urlencode($row[\'available_update\']), member()->session_id()). \'</span>\';
																																								 }

																																								 return \'<p style="margin-bottom: 10px;">\'. $plugin_info[\'description\']. \'</p><p>\'. implode(\' | \', $plugin_data). \'</p>\';'),
																																 'width' => '78%',
																															 ));
	}
}

if(!function_exists('admin_plugins_manage_table_handle'))
{
	/*
		Function: admin_plugins_manage_table_handle

		Does the specified action on the selected plugins.

		Parameters:
			string $action - The action selected.
			array $selected - An array containing the selected plugin
												guid's.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_manage_table_handle($action, $selected)
	{
		// Make sure the supplied plugins are legit... Along with that, load their information.
		$plugins = array();

		if(count($selected) > 0)
		{
			foreach($selected as $plugin_id)
			{
				// This will check to see if it is a valid plugin.
				if($plugin_info = plugin_load($plugin_id, false))
				{
					$plugins[$plugin_id] = $plugin_info;
				}
			}
		}

		// No plugins? No doing anything then...
		if(count($plugins) == 0)
		{
			redirect(baseurl. '/index.php?action=admin&sa=plugins_manage');
		}

		if($action == 'activate')
		{
			// Activating a plugin, are we? Alright. Simple enough.
			db()->query('
				UPDATE {db->prefix}plugins
				SET is_activated = 1, runtime_error = 0
				WHERE guid IN({array_string:plugin_ids})',
				array(
					'plugin_ids' => array_keys($plugins),
				), 'admin_plugins_manage_activate_query');
		}
		elseif($action == 'deactivate')
		{
			// Looks like we are deactivating a plugin.
			db()->query('
				UPDATE {db->prefix}plugins
				SET is_activated = 0
				WHERE guid IN({array_string:plugin_ids})',
				array(
					'plugin_ids' => array_keys($plugins),
				), 'admin_plugins_manage_deactivate_query');
		}
		elseif($action == 'delete')
		{
			// Deleting, huh? Well... Delete it from the database then.
			db()->query('
				DELETE FROM {db->prefix}plugins
				WHERE guid IN({array_string:plugin_ids})',
				array(
					'plugin_ids' => array_keys($plugins),
				), 'admin_plugins_manage_delete_query');

			// Remove it from the plugins directory too.
			foreach($plugins as $plugin_info)
			{
				// Recursive unlink, please!
				recursive_unlink($plugin_info['path']);
			}
		}

		// Redirect!
		redirect(baseurl. '/index.php?action=admin&sa=plugins_manage');
	}
}

if(!function_exists('admin_plugins_update'))
{
	/*
		Function: admin_plugins_update

		Handles the actual updating of the plugin.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_update()
	{
		api()->run_hooks('admin_plugins_update');

		// Can you add plugins?
		if(!member()->can('manage_plugins'))
		{
			// That's what I thought!
			admin_access_denied();
		}

		admin_current_area('plugins_manage');

		// Check the session id.
		verify_request('get');

		// Which plugin are you updating?
		$guid = isset($_GET['update']) ? $_GET['update'] : '';
		$plugin_info = plugin_load($guid, false);
		$version = basename($_GET['version']);

		// So does it exist? Is it in the plugin directory? It better be!
		if(empty($plugin_info))
		{
			theme()->set_title(l('An Error Occurred'));

			api()->context['error_title'] = l('Plugin Not Found');
			api()->context['error_message'] = l('Sorry, but the plugin you are wanting to update does not exist.');

			theme()->render('error');
		}
		else
		{
			theme()->set_title(l('Updating Plugin'));

			api()->context['guid'] = $guid;
			api()->context['plugin_info'] = $plugin_info;
			api()->context['version'] = $version;

			theme()->render('admin_plugins_update');
		}
	}
}

if(!function_exists('admin_plugins_check_updates'))
{
	/*
		Function: admin_plugins_check_updates

		Checks to see if the plugins require any updating. Plugin guid can be
		supplied, but if none are supplied, all plugins will be checked.

		Parameters:
			array $guids - An array of plugin guid's to check for updates.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_check_updates($guids = array())
	{
		// This array will keep track of available updates.
		$plugin_updates = array();

		// No GUIDs supplied?
		if(count($guids) == 0)
		{
			// Load some up! Unless we recently checked.
			if(settings()->get('last_plugin_update_check', 'int', 0) + 3600 < time_utc())
			{
				// We can use the <plugin_list> function to get all the plugins.
				$plugins = plugin_list();

				// But, we only need the GUIDs!
				foreach($plugins as $plugin)
				{
					$guids[] = $plugin['guid'];
				}

				// Woops! Don't forget to set the last time we checked for updates!
				settings()->set('last_plugin_update_check', time_utc(), 'int');
			}
		}

		// You know, just incase ;-)
		if(count($guids) > 0)
		{
			// The HTTP class will be mighty useful!
			$http = api()->load_class('HTTP');

			foreach($guids as $guid)
			{
				// Load the plugins information... If it exists.
				$plugin_info = plugin_load($guid, false);

				// Does it not exist?
				if($plugin_info === false)
				{
					continue;
				}

				// The globally unique identifier is the URL to check for updates at ;-)
				// I don't quite know how to explain this, but here it goes. Say you
				// have a plugin with all of these version: 1.0, 1.0.1 and 1.1, when
				// an update check is requested and the supplied version is 1.0, the
				// response to the request should give 1.0.1, not 1.1... However, you
				// can of course respond with 1.1 IF when the 1.1 plugin is installed
				// that it will be completely updated from 1.0 to 1.1 (including anything
				// that was also done in 1.0.1). It's your choice, of course! Also note
				// that during a plugin update, a variable ($current_plugin_version) will
				// be set before running the install.php file, that way, if required, you
				// can do anything special :-).
				$request = $http->request('http://'. $guid, array('updatecheck' => 1, 'version' => $plugin_info['version']));

				// Is it empty?
				if(empty($request))
				{
					// Sorry, couldn't check/nothing returned!
					continue;
				}

				// Even if there isn't a newer version, still update the plugins
				// information. This is just incase, for some odd reason, an update
				// has been taken down.
				db()->query('
					UPDATE {db->prefix}plugins
					SET available_update = {string:version_available}
					WHERE guid = {string:guid}
					LIMIT 1',
					array(
						'version_available' => compare_versions($request, $plugin_info['version'], '>') ? $request : '',
						'guid' => $plugin_info['guid'],
					), 'plugins_check_updates_query');
			}
		}
	}
}
?>