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

    $table = $api->load_class('Table');

    # Add our table.
    $table->add('manage_plugins_table', array(
                                          'base_url' => $base_url. '/index.php?action=admin&amp;sa=plugins_manage',
                                          'db_query' => '
                                            SELECT
                                              dependency_name, directory, runtime_error, is_activated
                                            FROM {db->prefix}plugins',
                                          'primary' => 'dependency_name',
                                          'sort' => array('dependency_name', 'desc'),
                                          'options' => array(
                                                         'activate' => l('Activate'),
                                                         'deactivate' => l('Deactivate'),
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
                                                                                 global $plugin_dir;

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

/*
  Function: admin_plugins_check_updates

  Checks to see if the plugins require any updating. Plugin dependency
  names can be supplied, but if none are supplied, all plugins will be
  checked.

  Parameters:
    array $dependencies - An array of plugin dependency names to check for updates.

  Returns:
    void - Nothing is returned by this function.
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

      # Let's check, shall we?
      if(version_compare($request, $plugin_info['version'], '>'))
      {
        # Yup, there is a newer version available.
      }
    }
  }
}
?>