<?php
#########################################################################
#                             SnowCMS v2.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#                  Released under the GNU GPL v3 License                #
#                     www.gnu.org/licenses/gpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#                SnowCMS v2.0 began in November 2009                    #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 2.0                         #
#########################################################################

if(!defined('IN_SNOW'))
  die;

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
    global $api, $base_url, $member, $plugin_dir, $settings, $theme, $theme_url;

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
    global $api, $base_url, $plugin_dir;

    // Only display plugins for directories that exist.
    $plugin_directories = scandir($plugin_dir);

    foreach($plugin_directories as $index => $directory)
    {
      // We don't want ., .., or objects that are not directories.
      if($directory == '.' || $directory == '..' || !is_dir($plugin_dir. '/'. $directory))
      {
        unset($plugin_directories[$index]);
      }
    }

    $table = $api->load_class('Table');

    # Add our table.
    $table->add('manage_plugins_table', array(
                                          'base_url' => $base_url. '/index.php?action=admin&amp;sa=plugins_manage',
                                          'db_query' => '
                                            SELECT
                                              dependency_name, directory, runtime_error, is_activated, available_update
                                            FROM {db->prefix}plugins
                                            WHERE directory IN('. (count($plugin_directories) > 0 ? '{string_array:directories}' : 'NULL'). ')',
                                          'db_vars' => array(
                                                         'directories' => $plugin_directories,
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
                                                                         global $base_url, $member, $plugin_dir;

                                                                         $plugin_info = plugin_load($plugin_dir. \'/\'. $row[\'directory\']);

                                                                         return \'<p style="font-weight: bold; margin-bottom: 10px;">\'. $plugin_info[\'name\']. \'</p><p>\'. (!empty($row[\'is_activated\']) ? \'<a href="\'. $base_url. \'/index.php?action=admin&amp;sa=plugins_manage&amp;deactivate=\'. urlencode($row[\'dependency_name\']). \'&amp;sid=\'. $member->session_id(). \'" title="\'. l(\'Deactivate this plugin\'). \'">\'. l(\'Deactivate\'). \'</a>\' : \'<a href="\'. $base_url. \'/index.php?action=admin&amp;sa=plugins_manage&amp;activate=\'. urlencode($row[\'dependency_name\']). \'&amp;sid=\'. $member->session_id(). \'" title="\'. l(\'Activate this plugin\'). \'">\'. l(\'Activate\'). \'</a> | <a href="\'. $base_url. \'/index.php?action=admin&amp;sa=plugins_manage&amp;delete=\'. urlencode($row[\'dependency_name\']). \'&amp;sid=\'. $member->session_id(). \'" title="\'. l(\'Delete this plugin\'). \'" onclick="return confirm(\\\'\'. l(\'Are you sure you want to delete this plugin?\'). \'\\\');">\'. l(\'Delete\'). \'</a>\'). \'</p>\';'),
                                                         'width' => '20%',
                                                       ));

    $table->add_column('manage_plugins_table', 'description', array(
                                                                'label' => l('Description'),
                                                                 'title' => l('Plugin information'),
                                                                 'sortable' => true,
                                                                 'function' => create_function('$row', '
                                                                                 global $base_url, $member, $plugin_dir;

                                                                                 $plugin_info = plugin_load($plugin_dir. \'/\'. $row[\'directory\']);

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
                                                                                   $plugin_data[] = \'<span style="font-weight: bold;">\'. l(\'v%s of this plugin is available! <a href="%s?action=admin&amp;sa=plugins_manage&amp;update=%s&amp;version=%s&amp;sid=%s">Update now</a>.\', $row[\'available_update\'], $base_url, urlencode($row[\'dependency_name\']), urlencode($row[\'available_update\']), $member->session_id()). \'</span>\';
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
    global $api, $base_url, $db, $plugin_dir;

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
      redirect($base_url. '/index.php?action=admin&sa=plugins_manage');
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
    redirect($base_url. '/index.php?action=admin&sa=plugins_manage');
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
    global $api, $base_url, $member, $plugin_dir, $theme, $theme_url;

    $api->run_hooks('admin_plugins_update');

    # Can you add plugins?
    if(!$member->can('manage_plugins'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    $theme->set_current_area('plugins_manage');

    # Check the session id.
    verify_request('get');

    # Which file are you installing?
    $dependency_name = $_GET['update'];
    $plugin_info = plugin_load($dependency_name, false);

    # So does it exist? Is it in the plugin directory? It better be!
    if(empty($plugin_info))
    {
      $theme->set_title(l('An error has occurred'));

      $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_manage-small.png" alt="" /> ', l('An error has occurred'), '</h1>
  <p>', l('Sorry, but the plugin you are wanting to update is not installed.'), '</p>';

      $theme->footer();
    }
    else
    {
      # Time for some JavaScript!
      $theme->add_js_file(array('src' => $theme_url. '/default/js/admin_plugin_update.js'));
      $theme->add_js_var('dependency_name', $_GET['update']);
      $theme->add_js_var('version', $_GET['version']);
      $theme->add_js_var('l', array(
                                'downloading update' => l('Downloading update'),
                                'extracting plugin' => l('Extracting plugin'),
                                'checking status' => l('Checking plugin status'),
                                'please wait' => l('Please wait...'),
                                'proceed with install' => l('Proceed with plugin installation'),
                                'cancel install' => l('Cancel plugin installation'),
                                'are you sure' => l("Are you sure you want to install this plugin?\r\nPlease be aware that damage to your website could result from the installation of this plugin."),
                                'canceling' => l('Canceling install. Please wait...'),
                                'finalize install' => l('Finalizing update'),
                              ));

      $theme->set_title(l('Updating plugin'));

      $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_manage-small.png" alt="" /> ', l('Updating plugin'), '</h1>
  <p>', l('Please wait while we are updating the %s plugin.', $plugin_info['name']), '</p>

  <div id="plugin_progress">
  </div>';

      $theme->footer();
    }
  }
}

if(!function_exists('admin_plugins_update_ajax'))
{
  /*
    Function: admin_plugins_update_ajax

    Basically just the <admin_plugins_add_ajax> function with a few modifications ;-)

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_update_ajax()
  {
    global $api, $base_url, $db, $member, $plugin_dir, $theme_url;

    if(!$member->can('manage_plugins'))
    {
      # That's what I thought!
      echo json_encode(array('error' => l('Access denied.')));
      exit;
    }
    elseif((empty($_GET['step']) || (string)$_GET['step'] != (string)(int)$_GET['step']) && $_GET['step'] != 'cancel')
    {
      echo json_encode(array('error' => l('Unknown step number.')));
      exit;
    }
    elseif(empty($_GET['sid']) || $_GET['sid'] != $member->session_id())
    {
      echo json_encode(array('error' => l('Your session id is invalid.')));
      exit;
    }

    # Gotta make sure the plugin you want to update is valid.
    $dependency_name = isset($_POST['dependency_name']) ? $_POST['dependency_name'] : '';
    $version = isset($_POST['version']) ? $_POST['version'] : '';

    $plugin_info = plugin_load($dependency_name, false);

    if(empty($dependency_name) || empty($version) || empty($plugin_info))
    {
      echo json_encode(array('error' => l('The plugin you are wanting to update is not installed.')));
      exit;
    }

    # Our response will be held here :-)
    $response = array('error' => '');

    # Canceling? Maybe!
    if($_GET['step'] == 'cancel')
    {
      @recursive_unlink($plugin_info['path']. '/update~/');
      @unlink($plugin_info['path']. '/update-package');
      @unlink($plugin_info['path']. '/previous-version');
    }
    elseif($_GET['step'] == 1)
    {
      # Downloading the update are we? Alright.
      $http = $api->load_class('HTTP');

      # Download it! Maybe... (Return a 404 if the update version is invalid/unknown!!!)
      if($http->request('http://'. $plugin_info['dependency'], array('download' => 1, 'version' => $version), 0, $plugin_info['path']. '/update-package'))
      {
        # Write the current version (well, will be previous) of the plugin in a file!
        $success = file_put_contents($plugin_info['path']. '/previous-version', $plugin_info['version']);

        $response['message'] = l('Plugin update package downloaded successfully. Proceeding... %s', print_r($success, true));
      }
      else
      {
        $response['error'] = l('The update package for the version %s was not found.', htmlchars($version));
      }
    }
    elseif($_GET['step'] == 2)
    {
      # An array, please :-)
      $response['message'] = array(
                               'border' => null,
                               'background' => null,
                               'text' => null,
                               'proceed' => false,
                             );

      $status = plugin_check_status($plugin_info['path']. '/update-package', $reason);

      # Is it approved? Sweet!
      if($status == 'approved')
      {
        $response['message']['border'] = '2px solid green';
        $response['message']['background'] = '#90EE90';
        $response['message']['text'] = '<table width="100%"><tr><td valign="middle"><img src="'. $theme_url. '/default/style/images/approved.png" alt="" title="" /></td><td valign="middle" align="center">'. l('The plugin "%s" has been reviewed and approved by the SnowCMS Dev Team.<br />Proceeding...', $plugin_info['name']). '</td></tr></table>';
        $response['message']['proceed'] = true;
      }
      # Disapproved?
      elseif($status == 'disapproved')
      {
        $response['message']['border'] = '2px solid #DB2929';
        $response['message']['background'] = '#F08080';
        $response['message']['text'] = '<table width="100%"><tr><td valign="middle"><img src="'. $theme_url. '/default/style/images/disapproved.png" alt="" title="" /></td><td valign="middle" align="center">'. l('The plugin "%s" has been reviewed and disapproved by the SnowCMS Dev Team.<br />Reason: %s<br />Proceed at your own risk.', $plugin_info['name'], !empty($reason) ? l($reason) : l('None given.')). '</td></tr></table>';
      }
      # Deprecated? Pending..?
      elseif($status == 'deprecated' || $status == 'pending')
      {
        $response['message']['border'] = '2px solid #1874CD';
        $response['message']['background'] = '#CAE1FF';
        $response['message']['text'] = '<table width="100%"><tr><td valign="middle"><img src="'. $theme_url. '/default/style/images/information.png" alt="" title="" /></td><td valign="middle" align="center">'. ($status == 'deprecated' ? l('The plugin "%s" is deprecated and a newer version is available at the <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a> site.<br />Proceed at your own risk.', $plugin_info['name']) : l('The plugin "%s" is currently under review by the SnowCMS Dev Team, so no definitive status can be given.<br />Proceed at your own risk.', $plugin_info['name'])). '</td></tr></table>';
      }
      elseif(in_array($status, array('unknown', 'malicious', 'insecure')))
      {
        if($status == 'unknown')
        {
          $text = l('The plugin "%s" is unknown to the <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a> site.<br />Proceed at your unknown risk.', $plugin_info['name']);
        }
        elseif($status == 'malicious')
        {
          $text = l('The plugin "%s" has been identified as malicious and it is not recommended you continue.<br />Reason: %s<br />Proceed at your own risk.', $plugin_info['name'], !empty($reason) ? l($reason) : l('None given.'));
        }
        elseif($status == 'insecure')
        {
          $text = l('The plugin "%s" has known security issues, it is recommended you not continue.<br />Reason: %s<br />Proceed at your own risk.', $plugin_info['name'], !empty($reason) ? l($reason) : l('None given.'));
        }

        $response['message']['border'] = '2px solid #FCD116';
        $response['message']['background'] = '#FFF68F';
        $response['message']['text'] = '<table width="100%"><tr><td valign="middle"><img src="'. $theme_url. '/default/style/images/warning.png" alt="" title="" /></td><td valign="middle" align="center">'. $text. '</td></tr></table>';
      }
      else
      {
        $api->run_hooks('admin_plugins_handle_status', array(&$response['message']));
      }
    }
    elseif($_GET['step'] == 3)
    {
      # The Update class can extract a file for us.
      $update = $api->load_class('Update');

      # We need to make the temporary directory.
      if(!file_exists($plugin_info['path']. '/update~/') && !@mkdir($plugin_info['path']. '/update~', 0755, true))
      {
        $response['error'] = l('Failed to create the temporary update folder. Make sure the plugins directory is writable.');
      }
      elseif($update->extract($plugin_info['path']. '/update-package', $plugin_info['path']))
      {
        $response['message'] = l('The update package was successfully extracted. Proceeding...');
      }
      else
      {
        $response['error'] = l('Failed to extract the update package.');
        @recursive_unlink($plugin_info['path']. '/update~/');
        @unlink($plugin_info['path']. '/update-package');
        @unlink($plugin_info['path']. '/previous-version');
      }
    }
    elseif($_GET['step'] == 4)
    {
      # Add the plugin to the database (well, the new one, you never know, stuff might have changed!)
      // !!! TODO: Probably should delete the old row first, incase the guid changed.
      $result = $db->insert('replace', '{db->prefix}plugins',
        array(
          'dependency_name' => 'string-255', 'directory' => 'string',
        ),
        array(
          $plugin_info['dependency'], basename($plugin_info['path']),
        ), array(), 'admin_plugins_add_query');

      # Any install file? Run it!
      if(file_exists($plugin_info['path']. '/install.php'))
      {
        # Set the current plugin version. Maybe.
        if(file_exists($plugin_info['path']. '/previous-version'))
        {
          $current_plugin_version = file_get_contents($plugin_info['path']. '/previous-version');
        }

        require_once($plugin_info['path']. '/install.php');
        @unlink($plugin_info['path']. '/install.php');
      }

      @recursive_unlink($plugin_info['path']. '/update~/');
      @unlink($plugin_info['path']. '/update-package');
      @unlink($plugin_info['path']. '/previous-version');

      $response['message'] = l('Plugin updated successfully! <a href="%s">Go back to plugin management</a>.', $base_url. '/index.php?action=admin&sa=plugins_manage');
    }

    echo json_encode($response);
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
  function admin_plugins_check_updates($dependencies = array())
  {
    global $api, $db, $settings;

    # No dependency names supplied?
    if(count($dependencies) == 0)
    {
      # Load some up! Unless we didn't check too long ago.
      if($settings->get('last_plugin_update_check', 'int', 0) + 3600 < time_utc())
      {
        $result = $db->query('
          SELECT
            dependency_name
          FROM {db->prefix}plugins',
          array());

        $dependencies = array();
        while($row = $result->fetch_assoc())
        {
          $dependencies[] = $row['dependency_name'];
        }
      }
    }

    # You know, just incase ;-)
    if(count($dependencies) > 0)
    {
      # The HTTP class will be mighty useful!
      $http = $api->load_class('HTTP');

      foreach($dependencies as $dependency)
      {
        # Load the plugins information... If it exists.
        $plugin_info = plugin_load($dependency, false);

        # Does it not exist?
        if($plugin_info === false)
        {
          continue;
        }

        # The dependency name is the URL to check for updates at ;-)
        # I don't quite know how to explain this, but here it goes. Say you
        # have a plugin with all of these version: 1.0, 1.0.1 and 1.1, when
        # an update check is requested and the supplied version is 1.0, the
        # response to the request should give 1.0.1, not 1.1... However, you
        # can of course respond with 1.1 IF when the 1.1 plugin is installed
        # that it will be completely updated from 1.0 to 1.1 (including anything
        # that was also done in 1.0.1). It's your choice, of course! Also note
        # that during a plugin update, a variable ($current_plugin_version) will
        # be set before running the install.php file, that way, if required, you
        # can do anything special :-).
        $request = $http->request('http://'. $dependency, array('updatecheck' => 1, 'version' => $plugin_info['version']));

        # Is it empty?
        if(empty($request))
        {
          # Sorry, couldn't check/nothing returned!
          continue;
        }

        # Even if there isn't a newer version, still update the plugins
        # information. This is just incase, for some odd reason, an update
        # has been taken down.
        $db->query('
          UPDATE {db->prefix}plugins
          SET available_update = {string:version_available}
          WHERE dependency_name = {string:dependency_name}
          LIMIT 1',
          array(
            'version_available' => version_compare($request, $plugin_info['version'], '>') ? $request : '',
            'dependency_name' => $plugin_info['dependency'],
          ), 'plugins_check_updates_query');
      }
    }
  }
}
?>