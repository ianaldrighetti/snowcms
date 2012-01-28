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

// Title: Manage Plugins

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
			admin_plugins_manage_table_handle(!empty($_GET['activate']) ? 'activate' : (!empty($_GET['deactivate']) ? 'deactivate' : 'delete'), array(plugindir. '/'. (!empty($_GET['activate']) ? $_GET['activate'] : (!empty($_GET['deactivate']) ? $_GET['deactivate'] : $_GET['delete']))));
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
																																					 $link_list[] = \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;delete=\'. htmlchars(basename($plugin[\'directory\'])). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'Delete this plugin\'). \'" class="red" onclick="return confirm(\\\'\'. l(\'Are you sure you want to delete this plugin?\'). \'\\\');">\'. l(\'Delete\'). \'</a>\';
																																				 }

																																				 return \'<a name="p\'. sha1(basename($plugin[\'directory\'])). \'"></a><span class="plugin-name">\'. $plugin[\'name\']. \'</span><span class="plugin-link-list">\'. implode(\' | \', $link_list). \'</span>\';'),
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

																																								 // Is there an update available?
																																								 if($plugin[\'is_update_available\'])
																																								 {
																																									 $plugin_data[] = \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;update=\'. htmlchars(basename($plugin[\'directory\'])). \'&amp;sid=\'. member()->session_id(). \'" title="\'. l(\'An update is available for this plugin (v%s)\', $plugin[\'update_version\']). \'" class="bold red">\'. l(\'Update\'). \'</a>\';
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
				$plugindir = realpath($plugindir);
				if($plugindir === false)
				{
					continue;
				}

				// This will check to see if it is a valid plugin, and within the
				// plugin directory.
				if(substr($plugindir, 0, strlen(plugindir)) == realpath(plugindir) && ($plugin_info = plugin_load($plugindir)) !== false)
				{
					$plugins[basename($plugindir)] = $plugin_info;
				}
			}
		}

		// No plugins? No doing anything then...
		if(count($plugins) == 0)
		{
			redirect(baseurl. '/index.php?action=admin&sa=plugins_manage', 301);
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
				WHERE directory IN({array_string:plugindirs})',
				array(
					'plugindirs' => array_keys($plugins)
				), 'admin_plugins_manage_deactivate_query');
		}
		elseif($action == 'delete')
		{
			// Deleting, huh? Well... Delete it from the database then.
			db()->query('
				DELETE FROM {db->prefix}plugins
				WHERE directory IN({array_string:plugindirs})',
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
		redirect(baseurl. '/index.php?action=admin&sa=plugins_manage', 301);
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
		$update_plugin = isset($_GET['update']) ? basename($_GET['update']) : '';
		$plugin_info = plugin_load(plugindir. '/'. $update_plugin);

		// So does it exist? Is it in the plugin directory? It better be!
		if(empty($plugin_info))
		{
			theme()->set_title(l('An Error Occurred'));

			api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/plugins_manage-small.png" alt="" /> '. l('Plugin Not Found');
			api()->context['error_message'] = l('Sorry, but the plugin you are requesting to update does not exist.');

			theme()->render('error');
		}
		else
		{
			// Get the latest version of this theme.
			// This function will handle that for us.
			$update_version = admin_plugins_check_updates($plugin_info['directory']);

			// So, how did that go?
			if($update_version === false || compare_versions($plugin_info['version'], $update_version, '>=') || $theme_info['update_url'] === false)
			{
				// No update needed!
				theme()->set_title(l('No Update Available'));

				api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/manage_plugins-small.png" alt="" /> '. l('No Update Available');
				api()->context['error_message'] = l('There is no update available for the plugin &quot;%s.&quot; <a href="%s">Back to plugin management &raquo;</a>', htmlchars($plugin_info['name']), baseurl. '/index.php?action=admin&amp;sa=plugins_manage');

				theme()->render('error');
			}
			else
			{
				// If this plugin is currently activated then we want to deactivate
				// it for the time being.
				$result = db()->query('
					SELECT
						is_activated
					FROM {db->prefix}plugins
					WHERE directory = {string:directory} AND is_activated = 1
					LIMIT 1',
					array(
						'directory' => basename($plugin_info['directory']),
					));

				// So, is it?
				if($result->num_rows() > 0)
				{
					// Yes, it is.
					db()->query('
						UPDATE {db->prefix}plugins
						SET is_activated = 0
						WHERE directory = {string:directory}
						LIMIT 1',
						array(
							'directory' => basename($plugin_info['directory']),
						));

					$reactivate_plugin = true;
				}

				// The Component class makes this very easy :-)
				$component = api()->load_class('Component');

				$result = $component->update($plugin_info['directory'], $update_version, 'plugin', array(
																																														 'ignore_status' => isset($_GET['status']) && $_GET['status'] == 'ignore',
																																														 'ignore_compatibility' => isset($_GET['compat']) && $_GET['compat'] == 'ignore',
																																													 ));

				// Let's make this a bit easier.
				foreach($result as $index => $value)
				{
					api()->context[$index] = $value;
				}

				// Was the update a success? Awesome.
				if(!empty($result['completed']))
				{
					// But we need to remove the update notification, after we check
					// for any more updates, that is.
					$response = admin_plugins_check_updates($plugin_info['directory']);

					$plugin_updates = settings()->get('plugin_updates', 'array', array());

					// So, delete it or change it?
					if(empty($response))
					{
						// Delete it.
						unset($plugin_updates[basename($plugin_info['directory'])]);
					}
					else
					{
						// Looks like you have some more updates to do.
						$plugin_updates[basename($plugin_info['directory'])] = $response;
					}

					// Now save it.
					settings()->set('plugin_updates', $plugin_updates);

					// Do we need to reactivate this plugin?
					if(!empty($reactivate_plugin))
					{
						db()->query('
							UPDATE {db->prefix}plugins
							SET is_activated = 1
							WHERE directory = {string:directory}
							LIMIT 1',
							array(
								'directory' => basename($theme_info['directory']),
							));
					}
				}

				api()->context['update'] = htmlchars($_GET['update']);
				api()->context['plugin_name'] = $plugin_info['name'];
				api()->context['plugin_version'] = $plugin_info['version'];
				api()->context['update_version'] = $update_version;

				theme()->set_title(l('Updating Plugin'));

				theme()->render('admin_plugins_update');
			}
		}
	}
}

if(!function_exists('admin_plugins_check_updates'))
{
	/*
		Function: admin_plugins_check_updates

		Checks to see if the plugins require any updating. A plugin directory
		can be supplied, but if none are supplied all plugins will be checked.

		Parameters:
			array $plugins - An array of directories containing plugins to check.

		Returns:
			mixed - Returns an array containing all the plugins that have an
							update available, if multiple plugins were supplied in the
							$plugindirs parameter. If only one plugin was supplied then
							the update version available will be returned (a string) or
							false if there are no updates available.

		Note:
			This function is overloadable.

			If the $plugindirs parameter is empty, it is assumed that this
			function is being called upon by the SnowCMS task system and will
			check for updates for all existing plugins, so long as it hasn't been
			done within the last hour.
	*/
	function admin_plugins_check_updates($plugindirs = array())
	{
		global $func;

		// This array will keep track of available updates.
		$plugin_updates = array();

		// No an array? We'll make it one!
		if(!is_array($plugindirs))
		{
			$plugindirs = array($plugindirs);
		}

		// No GUIDs supplied?
		if(count($plugindirs) == 0)
		{
			// Load some up! Unless we recently checked.
			if(settings()->get('last_plugin_update_check', 'int', 0) + 3600 < time_utc())
			{
				// We can use the <plugin_list> function to get all the plugins.
				$plugins = plugin_list();

				// But, we only need the directories!
				foreach($plugins as $plugin)
				{
					$plugin_info = plugin_load($plugin);

					// This shouldn't be empty, but hey, just to be sure!
					if(!empty($plugin_info['guid']))
					{
						$plugindirs[] = $plugin_info['directory'];
					}
				}

				// Woops! Don't forget to set the last time we checked for updates!
				settings()->set('last_plugin_update_check', time_utc(), 'int');

				// This is a system update check... So yeah.
				$system_update_check = true;
			}
		}

		// You know, just incase ;-)
		if(count($plugindirs) > 0)
		{
			// The HTTP class will be mighty useful!
			$http = api()->load_class('HTTP');

			foreach($plugindirs as $plugindir)
			{
				// Load the plugins information... If it exists.
				$plugin_info = plugin_load($plugindir);

				// Does it not exist?
				if($plugin_info === false)
				{
					continue;
				}

				// Set up the POST data we will be sending.
				$post_data = array('requesttype' => 'updatecheck', 'version' => $plugin_info['version']);

				// Want to add some sort of update key or something?
				if($func['strlen'](api()->apply_filters(sha1($plugin_info['directory']). '_updatekey', '') > 0))
				{
					$post_data['updatekey'] = api()->apply_filters(sha1($plugin_info['directory']). '_updatekey', '');
				}

				// Now let's check for updates! If you want to know more information
				// about how SnowCMS asks for update information and the like, check
				// out http://code.google.com/p/snowcms/wiki/SUTP.
				$request = $http->request(strtolower(substr($plugin_info['guid'], 0, 8)) == 'https://' ? $plugin_info['guid'] : 'http://'. $plugin_info['guid'], $post_data);

				// Is it empty?
				if(empty($request) || trim(strtoupper($request)) == 'UPTODATE')
				{
					// Sorry, couldn't check/nothing returned!
					continue;
				}

				// Is this version actually newer? Save it!
				if(compare_versions($request, $plugin_info['version'], '>') && !isset($plugin_updates[basename($plugin_info['directory'])]))
				{
					// The GUID will be the index and the value will be the version
					// available.
					$plugin_updates[basename($plugin_info['directory'])] = $request;
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
		if(count($plugindirs) == 1)
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