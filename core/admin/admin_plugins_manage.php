<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
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

if(!defined('IN_SNOW'))
{
  die('Nice try...');
}

# Title: Control Panel - Plugins - Manage

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
    global $api, $member, $settings, $theme;

    $api->run_hooks('admin_plugins_manage');

    # Can you manage plugin settings?
    if(!$member->can('manage_plugins'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    # Generate the table which shows all the plugin information :-)
    admin_plugins_manage_generate_table();
    $table = $api->load_class('Table');

    # Activating, deactivating or deleting a plugin..?
    if(!empty($_GET['activate']) || !empty($_GET['deactivate']) || !empty($_GET['delete']))
    {
      # Gotta make sure it's you ;-)
      verify_request('get');

      # Just use the function used in the table.
      admin_plugins_manage_table_handle(!empty($_GET['activate']) ? 'activate' : (!empty($_GET['deactivate']) ? 'deactivate' : 'delete'), array(!empty($_GET['activate']) ? $_GET['activate'] : (!empty($_GET['deactivate']) ? $_GET['deactivate'] : $_GET['delete'])));
    }

    $theme->set_current_area('plugins_manage');

    $theme->set_title(l('Manage plugins'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_manage-small.png" alt="" /> ', l('Manage plugins'), '</h1>
  <p>', l('Manage your current plugins.'), '</p>';

    $table->show('manage_plugins_table');

    $theme->footer();
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
    global $api;

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

    $table = $api->load_class('Table');

    # Add our table.
    $table->add('manage_plugins_table', array(
                                          'base_url' => baseurl. '/index.php?action=admin&amp;sa=plugins_manage',
                                          'db_query' => '
                                            SELECT
                                              dependency_name, directory, runtime_error, is_activated, available_update
                                            FROM {db->prefix}plugins
                                            WHERE directory IN('. (count($plugin_dirs) > 0 ? '{string_array:directories}' : 'NULL'). ')',
                                          'db_vars' => array(
                                                         'directories' => $plugin_dirs,
                                                       ),
                                          'primary' => 'dependency_name',
                                          'sort' => array('dependency_name', 'desc'),
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
                                                                         global $member;

                                                                         $plugin_info = plugin_load(plugindir. \'/\'. $row[\'directory\']);

                                                                         return \'<p style="font-weight: bold; margin-bottom: 10px;">\'. $plugin_info[\'name\']. \'</p><p>\'. (!empty($row[\'is_activated\']) ? \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;deactivate=\'. urlencode($row[\'dependency_name\']). \'&amp;sid=\'. $member->session_id(). \'" title="\'. l(\'Deactivate this plugin\'). \'">\'. l(\'Deactivate\'). \'</a>\' : \'<a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;activate=\'. urlencode($row[\'dependency_name\']). \'&amp;sid=\'. $member->session_id(). \'" title="\'. l(\'Activate this plugin\'). \'">\'. l(\'Activate\'). \'</a> | <a href="\'. baseurl. \'/index.php?action=admin&amp;sa=plugins_manage&amp;delete=\'. urlencode($row[\'dependency_name\']). \'&amp;sid=\'. $member->session_id(). \'" title="\'. l(\'Delete this plugin\'). \'" onclick="return confirm(\\\'\'. l(\'Are you sure you want to delete this plugin?\'). \'\\\');">\'. l(\'Delete\'). \'</a>\'). \'</p>\';'),
                                                         'width' => '20%',
                                                       ));

    $table->add_column('manage_plugins_table', 'description', array(
                                                                'label' => l('Description'),
                                                                 'title' => l('Plugin information'),
                                                                 'sortable' => true,
                                                                 'function' => create_function('$row', '
                                                                                 global $member;

                                                                                 $plugin_info = plugin_load(plugindir. \'/\'. $row[\'directory\']);

                                                                                 # Let\'s get some extra information displayed too.
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
                                                                                   $plugin_data[] = \'<span style="font-weight: bold;">\'. l(\'v%s of this plugin is available! <a href="%s/index.php?action=admin&amp;sa=plugins_manage&amp;update=%s&amp;version=%s&amp;sid=%s">Update now</a>.\', $row[\'available_update\'], baseurl, urlencode($row[\'dependency_name\']), urlencode($row[\'available_update\']), $member->session_id()). \'</span>\';
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
                        dependency names.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_manage_table_handle($action, $selected)
  {
    global $api, $db;

    # Make sure the supplied plugins are legit... Along with that, load their information.
    $plugins = array();

    if(count($selected) > 0)
    {
      foreach($selected as $plugin_id)
      {
        # This will check to see if it is a valid plugin.
        if($plugin_info = plugin_load($plugin_id, false))
        {
          $plugins[$plugin_id] = $plugin_info;
        }
      }
    }

    # No plugins? No doing anything then...
    if(count($plugins) == 0)
    {
      redirect(baseurl. '/index.php?action=admin&sa=plugins_manage');
    }

    if($action == 'activate')
    {
      # Activating a plugin, are we? Alright. Simple enough.
      $db->query('
        UPDATE {db->prefix}plugins
        SET is_activated = 1, runtime_error = 0
        WHERE dependency_name IN({array_string:plugin_ids})',
        array(
          'plugin_ids' => array_keys($plugins),
        ), 'admin_plugins_manage_activate_query');
    }
    elseif($action == 'deactivate')
    {
      # Looks like we are deactivating a plugin.
      $db->query('
        UPDATE {db->prefix}plugins
        SET is_activated = 0
        WHERE dependency_name IN({array_string:plugin_ids})',
        array(
          'plugin_ids' => array_keys($plugins),
        ), 'admin_plugins_manage_deactivate_query');
    }
    elseif($action == 'delete')
    {
      # Deleting, huh? Well... Delete it from the database then.
      $db->query('
        DELETE FROM {db->prefix}plugins
        WHERE dependency_name IN({array_string:plugin_ids})',
        array(
          'plugin_ids' => array_keys($plugins),
        ), 'admin_plugins_manage_delete_query');

      # Remove it from the plugins directory too.
      foreach($plugins as $plugin_info)
      {
        # Recursive unlink, please!
        recursive_unlink($plugin_info['path']);
      }
    }

    # Redirect!
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
    global $api, $db, $member, $theme;

    $api->run_hooks('admin_plugins_update');

    // Can you add plugins?
    if(!$member->can('manage_plugins'))
    {
      // That's what I thought!
      admin_access_denied();
    }

    $theme->set_current_area('plugins_manage');

    // Check the session id.
    verify_request('get');

    // Which plugin are you updating?
    $guid = isset($_GET['update']) ? $_GET['update'] : '';
    $plugin_info = plugin_load($guid, false);
    $version = basename($_GET['version']);

    # So does it exist? Is it in the plugin directory? It better be!
    if(empty($plugin_info))
    {
      $theme->set_title(l('An error has occurred'));

      $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_manage-small.png" alt="" /> ', l('An error has occurred'), '</h1>
  <p>', l('Sorry, but the plugin you are wanting to update does not exist.'), '</p>';

      $theme->footer();
    }
    else
    {
      $theme->set_title(l('Updating plugin'));

      $theme->header();

      echo '
  <h1><img src="', $theme->url(), '/plugins_manage-small.png" alt="" /> ', l('Updating plugin'), '</h1>
  <p>', l('Please wait while we are updating the %s plugin.', $plugin_info['name']), '</p>

  <h3>', l('Downloading update'), '</h3>';

      // The HTTP class is always useful.
      $http = $api->load_class('HTTP');

      // Hmm, make a POST request to the plugins GUID, with the version we
      // want... Be sure to store it in a file for later use!
      if(!$http->request('http://'. $plugin_info['guid'], array('download' => 1, 'version' => $version), 0, $plugin_info['path']. '/update-package'))
      {
        echo '
  <p class="red">', l('The update package version "%s" was not found. Update process failed.', $version), '</p>';
      }
      else
      {
        echo '
  <p class="green">', l('The update package was successfully downloaded. Proceeding...'), '</p>

  <h3>', l('Verifying plugin status'), '</h3>';

        // We need a file which has the <plugin_check_status> function,
        // which we really need.
        require_once(coredir. '/admin/admin_plugins_add.php');

        // So get the status, please.
        $status = plugin_check_status($plugin_info['path']. '/update-package', $reason);

        // This next function interprets the status message into something
        // slightly more useful, to real people, that is.
        $response = admin_plugins_get_message($status, $plugin_info['name'], $reason);

        // So, shall we proceed?
        $update_proceed = isset($_GET['proceed']) || $status == 'approved';
        $api->run_hooks('plugin_install_proceed', array(&$update_proceed, $status));

        echo '
  <p style="color: ', $response['color'], '">', $response['message'], '</p>';

        // Is it okay to proceed?
        if(!empty($update_proceed))
        {
          // Time to extract the plugin!
          echo '
  <h3>', l('Extracting plugin'), '</h3>';

          $update = $api->load_class('Update');

          // We need to make the temporary directory.
          if(!file_exists($plugin_info['path']. '/update~/') && !@mkdir($plugin_info['path']. '/update~', 0755, true))
          {
            echo '
  <p class="red">', l('Failed to create the temporary update folder. Make sure the plugins directory is writable.'), '</p>';
          }
          // If we made that directory successfully, extract it to that
          // temporary location.
          elseif($update->extract($plugin_info['path']. '/update-package', $plugin_info['path']. '/update~'))
          {
            echo '
  <p class="green">', l('The update package was successfully extracted. Proceeding...'), '</p>';

            // No longer need the package containing the update...
            unlink($plugin_info['path']. '/update-package');

            // Time to move on to the next step, then.
            // Which is just copying the files from the update~ directory to
            // the root directory of the plugin. If you are wondering why we
            // extracted the files to update~ instead of the root directory
            // in the first place, well, it is done just to make sure the
            // package could actually be extracted.
            $files = scandir($plugin_info['path']. '/update~');

            foreach($files as $filename)
            {
              if($filename == '.' || $filename == '..')
              {
                continue;
              }

              // Just rename them!
              rename($plugin_info['path']. '/update~/'. $filename, $plugin_info['path']. '/'. $filename);
            }

            // Now delete the update~ directory.
            recursive_unlink($plugin_info['path']. '/update~/');

            // Get the new plugin information. Though it certainly might not
            // have changed. Just incase!
            $new_plugin_info = plugin_load($plugin_info['path']);

            // We will do this just incase the GUID changed, and clear any
            // possible runtime error, oh, and set the available update
            // column to empty.
            $db->query('
              UPDATE {db->prefix}plugins
              SET dependency_name = {string:updated_guid}, runtime_error = 0, available_update = \'\'
              WHERE dependency_name = {string:current_guid}
              LIMIT 1',
              array(
                'updated_guid' => $new_plugin_info['guid'],
                'current_guid' => $plugin_info['guid'],
              ), 'update_plugin_guid');

            // Is there an installation file?
            if(file_exists($plugin_info['path']. '/install.php'))
            {
              // Set the current plugin version.
              $current_plugin_version = $plugin_info['version'];

              require_once($plugin_info['path']. '/install.php');

              // And delete it.
              unlink($plugin_info['path']. '/install.php');
            }

            // Sweet! The update is complete!
            echo '
  <h3>', l('Update finished'), '</h3>
  <p>', l('You have successfully updated the plugin "%s" to version %s. <a href="%s">Back to plugin management</a>.', htmlchars($new_plugin_info['name']), htmlchars($new_plugin_info['version']), baseurl. '/index.php?action=admin&sa=plugins_manage'), '</p>';
          }
          else
          {
            echo '
  <p class="red">', l('Failed to extract the update package.'), '</p>';

            recursive_unlink($plugin_info['path']. '/update~/');
            unlink($plugin_info['path']. '/update-package');
          }
        }
        else
        {
          // Seems like it isn't!
          unlink($plugin_info['path']. '/update-package');

          echo '
  <form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Are you sure you want to proceed with the installation of this plugin?\r\nBe sure you trust the source of this plugin.'), '\');">
    <input type="submit" value="', l('Proceed'), '" />
    <input type="hidden" name="action" value="admin" />
    <input type="hidden" name="sa" value="plugins_manage" />
    <input type="hidden" name="update" value="', htmlchars($guid), '" />
    <input type="hidden" name="version" value="', urlencode($_GET['version']), '" />
    <input type="hidden" name="sid" value="', $member->session_id(), '" />
    <input type="hidden" name="proceed" value="true" />
  </form>';
        }
      }

      $theme->footer();
    }
  }
}

if(!function_exists('admin_plugins_check_updates'))
{
  /*
    Function: admin_plugins_check_updates

    Checks to see if the plugins require any updating. Plugin dependency
    names can be supplied, but if none are supplied, all plugins will be
    checked.

    Parameters:
      array $dependencies - An array of plugin dependency names to check for updates.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_check_updates($guids = array())
  {
    global $api, $db, $settings;

    // No GUIDs supplied?
    if(count($guids) == 0)
    {
      // Load some up! Unless we recently checked.
      if($settings->get('last_plugin_update_check', 'int', 0) + 3600 < time_utc())
      {
        // We can use the <plugin_list> function to get all the plugins.
        $plugins = plugin_list();

        // But, we only need the GUIDs!
        foreach($plugins as $plugin)
        {
          $guids[] = $plugin['guid'];
        }

        // Woops! Don't forget to set the last time we checked for updates!
        $settings->set('last_plugin_update_check', time_utc(), 'int');
      }
    }

    // You know, just incase ;-)
    if(count($guids) > 0)
    {
      // The HTTP class will be mighty useful!
      $http = $api->load_class('HTTP');

      foreach($guids as $guid)
      {
        // Load the plugins information... If it exists.
        $plugin_info = plugin_load($guid, false);

        // Does it not exist?
        if($plugin_info === false)
        {
          continue;
        }

        // The dependency name is the URL to check for updates at ;-)
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
        $db->query('
          UPDATE {db->prefix}plugins
          SET available_update = {string:version_available}
          WHERE dependency_name = {string:dependency_name}
          LIMIT 1',
          array(
            'version_available' => version_compare($request, $plugin_info['version'], '>') ? $request : '',
            'dependency_name' => $plugin_info['guid'],
          ), 'plugins_check_updates_query');
      }
    }
  }
}
?>