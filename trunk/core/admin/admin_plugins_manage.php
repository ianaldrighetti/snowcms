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
		$table = api()->load_class('Table');

		// Add our table.
		$table->add('manage_plugins_table', array(
																					'base_url' => baseurl. '/index.php?action=admin&amp;sa=plugins_manage',
																					'function' => 'admin_plugins_manage_table_data',
																					'primary' => 'directory',
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
																												 'function' => create_function('$plugin', '
																																				 // Let\'s generate a list of links.
																																				 $link_list = array();

																																				 // Is the plugin activated?
																																				 if($plugin[\'is_activated\'])
																																				 {
																																					 // Then we shall show you a link to deactivate the plugin, along with deleting it.
																																					 $link_list[] = \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;deactivate=\'. htmlchars(basename($plugin[\'directory\'])). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'Deactivate this plugin\'). \'">\'. l(\'Deactivate\'). \'</a>\';
																																				 }
																																				 else
																																				 {
																																					 $link_list[] = \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;activate=\'. htmlchars(basename($plugin[\'directory\'])). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'Activate this plugin\'). \'">\'. l(\'Activate\'). \'</a>\';
																																					 $link_list[] = \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;delete=\'. htmlchars(basename($plugin[\'directory\'])). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'Delete this plugin\'). \'" class="red">\'. l(\'Delete\'). \'</a>\';
																																				 }

																																				 // Is there an update available?
																																				 if($plugin[\'is_update_available\'])
																																				 {
																																					 $link_list[] = \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;update=\'. htmlchars(basename($plugin[\'directory\'])). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'v%s of this plugin is available! Click to update\', $plugin[\'update_version\']). \'" class="bold red">\'. l(\'Update\'). \'</a>\';
																																				 }
																																				 return \'<span class="plugin-name">\'. $plugin[\'name\']. \'</span><span class="plugin-link-list">\'. implode(\' | \', $link_list). \'</span>\';'),
																												 'width' => '20%',
																											 ));

		$table->add_column('manage_plugins_table', 'description', array(
																																'label' => l('Description'),
																																 'title' => l('Plugin information'),
																																 'sortable' => true,
																																 'function' => create_function('$plugin', '
																																								 // Let\'s get some extra information displayed too.
																																								 $plugin_data = array();

																																								 if(!empty($plugin[\'version\']))
																																								 {
																																									 $plugin_data[] = \'Version \'. $plugin[\'version\'];
																																								 }

																																								 if(!empty($plugin[\'author\']))
																																								 {
																																									 $plugin_data[] = l(\'By %s\', ((!empty($plugin[\'website\']) ? \'<a href="\'. $plugin[\'website\']. \'" target="_blank">\' : \'\'). $plugin[\'author\']. (!empty($plugin[\'website\']) ? \'</a>\' : \'\')));
																																								 }

																																								 if(!empty($plugin[\'runtime_error\']))
																																								 {
																																									 switch($plugin[\'runtime_error\'])
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
																																										 $plugin_data[] = \'<span style="font-weight: bold;">\'. l(\'Error:\'). \'</span> <span style="color: red;" title="\'. $plugin[\'error_message\']. \'">\'. $error_string. \'</span>\';
																																									 }
																																								 }

																																								 return \'<span class="plugin-description">\'. $plugin[\'description\']. \'</span><span class="plugin-info">\'. implode(\' | \', $plugin_data). \'</span>\';'),
																																 'width' => '78%',
																															 ));
	}
}

if(!function_exists('admin_plugins_manage_table_data'))
{
	/*
		Function: admin_plugins_manage_table_data

		Loads the data for the plugin management table.

		Parameters:
			int $page - The current page being viewed.
			int $per_page - The number of items per page.
			string $sort - The column being sorted.
			string $order - The order in which $column is being sorted.
			int &$num_rows - The total number of items to display on the current
											page.
			int &$overall_rows - The total number of rows overall.
			array &$filters

		Returns:
			array - Returns an array containing the data to pass on.
	*/
	function admin_plugins_manage_table_data($page, $per_page, $sort, $order, &$num_rows, &$overall_rows, &$filters)
	{
		// Alrighty then. We shall load all the plugins!
		$plugin_list = plugin_list();

		// Which plugins are activated?
		$result = db()->query('
			SELECT
				directory, is_activated, runtime_error, error_message
			FROM {db->prefix}plugins',
			array());

		$db_plugin_list = array();
		while($row = $result->fetch_assoc())
		{
			$db_plugin_list[$row['directory']] = array(
																						 'is_activated' => !empty($row['is_activated']),
																						 'runtime_error' => $row['runtime_error'],
																						 'error_message' => $row['error_message'],
																					 );
		}

		// Get the plugin updates array, just in case there are any updates
		// available!
		$plugin_updates = settings()->get('plugin_updates', 'array', array());

		$plugins = array();
		foreach($plugin_list as $plugindir)
		{
			// Load the plugins information.
			$plugin_info = plugin_load($plugindir);

			$plugins[] = array_merge($plugin_info, array(
																							 'is_activated' => isset($db_plugin_list[basename($plugin_info['directory'])]) ? $db_plugin_list[basename($plugin_info['directory'])]['is_activated'] : false,
																							 'runtime_error' => isset($db_plugin_list[basename($plugin_info['directory'])]) ? $db_plugin_list[basename($plugin_info['directory'])]['runtime_error'] : 0,
																							 'is_update_available' => isset($plugin_updates[basename($plugin_info['directory'])]) && compare_versions($plugin_updates[basename($plugin_info['directory'])], $plugin_info['version'], '>'),
																							 'update_version' => isset($plugin_updates[basename($plugin_info['directory'])]) ? $plugin_updates[basename($plugin_info['directory'])] : false,
																							 'error_message' => isset($db_plugin_list[basename($plugin_info['directory'])]) ? $db_plugin_list[basename($plugin_info['directory'])]['error_message'] : false,
																						 ));
		}

		$num_rows = count($plugins);
		$overall_rows = $num_rows;

		// Time for some sorting action!
		for($comparison = 0; $comparison < ($overall_rows - 1); $comparison++)
		{
			$address = $comparison;
			$dummy = $plugins[$address + 1];

			while($address >= 0 && $dummy['name'] < $plugins[$address]['name'])
			{
				$plugins[$address + 1] = $plugins[$address];
				$address--;
			}

			$plugins[$address + 1] = $dummy;
		}


		return $plugins;
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
			foreach($selected as $plugindir)
			{
				// This will check to see if it is a valid plugin.
				if($plugin_info = plugin_load(plugindir. '/'. $plugindir))
				{
					$plugins[$plugindir] = $plugin_info;
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
			// Activating a plugin, are we? Alright, I can handle that.
			$rows = array();
			foreach($plugins as $plugindir => $plugin_info)
			{
				$rows[] = array($plugindir, 1);
			}

			db()->insert('replace', '{db->prefix}plugins',
				array(
					'directory' => 'string-255', 'is_activated' => 'int',
				),
				$rows,
				array('directory'), 'admin_plugins_manage_activate_query');
		}
		elseif($action == 'deactivate')
		{
			// Looks like we are deactivating a plugin.
			db()->query('
				DELETE FROM {db->prefix}plugins
				WHERE directory IN({array_string:plugin_ids})',
				array(
					'plugindirs' => array_keys($plugins),
				), 'admin_plugins_manage_deactivate_query');
		}
		elseif($action == 'delete')
		{
			// Deleting, huh? Well... Delete it from the database then.
			db()->query('
				DELETE FROM {db->prefix}plugins
				WHERE directory IN({array_string:plugin_ids})',
				array(
					'plugindirs' => array_keys($plugins),
				), 'admin_plugins_manage_delete_query');

			// Remove it from the plugins directory too.
			foreach($plugins as $plugin_info)
			{
				// Recursive unlink, please!
				recursive_unlink($plugin_info['directory']);
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
			mixed - Returns an array containing all the plugins that have an
							update available, if multiple plugins were supplied in the
							$guids parameter. If only one plugin was supplied then the
							update version available will be returned (a string) or false
							if there are no updates available.

		Note:
			This function is overloadable.

			If the $guids parameter is empty, it is assumed that this function is
			being called upon by the SnowCMS task system and will check for
			updates for all existing plugins, so long as it hasn't been done
			within the last hour.
	*/
	function admin_plugins_check_updates($guids = array())
	{
		// This array will keep track of available updates.
		$plugin_updates = array();

		// No an array? We'll make it one!
		if(!is_array($guids))
		{
			$guids = array($guids);
		}

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
					$plugin_info = plugin_load($plugin);

					// This shouldn't be empty, but hey, just to be sure!
					if(!empty($plugin_info['guid']))
					{
						$guids[] = $plugin_info['guid'];
					}
				}

				// Woops! Don't forget to set the last time we checked for updates!
				//settings()->set('last_plugin_update_check', time_utc(), 'int');

				// This is a system update check... So yeah.
				$system_update_check = true;
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

				// Is this version actually newer? Save it!
				if(compare_versions($request, $plugin_info['version'], '>') && !isset($plugin_updates[strtolower($plugin_info['guid'])]))
				{
					// The GUID will be the index and the value will be the version
					// available.
					$plugin_updates[strtolower($plugin_info['guid'])] = $request;
				}
			}
		}

		// Save the available updates to the database, if this was invoked by
		// the tasks system, that is.
		if(!empty($system_update_check))
		{
			settings()->set('plugin_updates', $plugin_updates);
		}

		// Did you give us one plugin to check for updates?
		if(count($guids) == 0)
		{
			// Yup, so let's see if we can return a version.
			if(count($plugin_updates) > 0)
			{
				// Take it off the bottom!
				return array_pop($plugin_updates);
			}
			else
			{
				// No update was available.
				return false;
			}
		}
		else
		{
			return $plugin_updates;
		}
	}
}
?>