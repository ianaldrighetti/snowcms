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

# Title: Control Panel - Plugins - Add

if(!function_exists('admin_plugins_add'))
{
  /*
    Function: admin_plugins_add

    Handles the downloading and extracting of plugins.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_url;

    $api->run_hooks('admin_plugins_add');

    # Can you add plugins?
    if(!$member->can('add_plugins'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    admin_plugins_add_generate_form();
    $form = $api->load_class('Form');

    if(!empty($_POST['add_plugins_form']))
    {
      $form->process('add_plugins_form');
    }

    $theme->set_current_area('plugins_add');

    $theme->set_title(l('Add plugin'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_add-small.png" alt="" /> ', l('Add a new plugin'), '</h1>
  <p>', l('Plugins can be added to your site by entering the plugins dependency name (the address at which the plugins package is downloaded) or by uploading the plugin package.'), '</p>';

    $form->show('add_plugins_form');

    $theme->footer();
  }
}

if(!function_exists('admin_plugins_add_generate_form'))
{
  /*
    Function: admin_plugins_add_generate_form

    Generates the form which allows you to upload or download a plugin.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add_generate_form()
  {
    global $api, $base_url;

    $form = $api->load_class('Form');

    # Let's get to making our form, shall we?
    $form->add('add_plugins_form', array(
                                     'action' => $base_url. '/index.php?action=admin&amp;sa=plugins_add',
                                     'callback' => 'admin_plugins_add_handle',
                                     'method' => 'post',
                                     'submit' => l('Add plugin'),
                                   ));

    # Do you want to upload the plugin?
    $form->add_field('add_plugins_form', 'plugin_file', array(
                                                          'type' => 'file',
                                                          'label' => l('From a file:'),
                                                          'subtext' => l('Select the plugin file you want to install.'),
                                                        ));

    # A URL? Sure!
    $form->add_field('add_plugins_form', 'plugin_url', array(
                                                         'type' => 'string',
                                                         'label' => l('From a URL:'),
                                                         'subtext' => l('Enter the URL of the plugin you want to download and install.'),
                                                         'value' => 'http://',
                                                       ));
  }
}

if(!function_exists('admin_plugins_add_handle'))
{
  /*
    Function: admin_plugins_add_handle

    Handles the form data submitted through the add plugins form.

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns false on failure, the user gets redirected to
             {$base_url}/index.php?action=admin&sa=plugins_add&install={filename}
             where the status of the plugin is checked and then installed.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add_handle($data, &$errors = array())
  {
    global $api, $base_url, $member, $plugin_dir;

    # Where should this plugin go..?
    $filename = $plugin_dir. '/'. uniqid('plugin_'). '.tmp';
    while(file_exists($filename))
    {
      $filename = $plugin_dir. '/'. uniqid('plugin_'). '.tmp';
    }

    # Uploading a file, are we?
    if(!empty($data['plugin_file']['tmp_name']))
    {
      # Simply try to move the file now.
      if(!move_uploaded_file($data['plugin_file']['tmp_name'], $filename))
      {
        # Woops, didn't work!
        $errors[] = l('Plugin upload failed.');
        return false;
      }
    }
    # You want us to download it? I can do that.
    elseif(!empty($data['plugin_url']) && strtolower($data['plugin_url']) != 'http://')
    {
      # The HTTP class can do all this, awesomely, of course!
      $http = $api->load_class('HTTP');

      if(!$http->request($data['plugin_url'], array(), 0, $filename))
      {
        # Sorry, but looks like it didn't work!!!
        $errors[] = l('Failed to download the plugin from "%s"', htmlchars($data['plugin_url']));
        return false;
      }
    }
    else
    {
      $errors[] = l('No file or URL specified.');
      return false;
    }

    # If it worked, we get redirected!
    redirect($base_url. '/index.php?action=admin&sa=plugins_add&install='. urlencode(basename($filename)). '&sid='. $member->session_id());
  }
}

if(!function_exists('admin_plugins_install'))
{
  /*
    Function: admin_plugins_install

    Handles the actual installing of the plugin, after things
    such as the plugins status is checked on SnowCMS.com

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_install()
  {
    global $api, $base_url, $member, $plugin_dir, $theme, $theme_url;

    $api->run_hooks('admin_plugins_install');

    # Can you add plugins?
    if(!$member->can('add_plugins'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    $theme->set_current_area('plugins_add');

    # Check the session id.
    verify_request('get');

    # Which file are you installing?
    $filename = realpath($plugin_dir. '/'. $_GET['install']);

    # So does it exist? Is it in the plugin directory? It better be!
    if(empty($filename) || !file_exists($filename) || !is_file($filename) || substr($filename, 0, strlen($plugin_dir)) != realpath($plugin_dir))
    {
      $theme->set_title(l('An error has occurred'));

      $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_add-small.png" alt="" /> ', l('An error has occurred'), '</h1>
  <p>', l('Sorry, but the supplied plugin file either does not exist or is not a valid file.'), '</p>';

      $theme->footer();
    }
    else
    {
      # Time for some JavaScript!
      $theme->add_js_file(array('src' => $theme_url. '/default/js/admin_plugin_add.js'));
      $theme->add_js_var('filename', $_GET['install']);
      $theme->add_js_var('l', array(
                                'extracting plugin' => l('Extracting plugin'),
                                'checking status' => l('Checking plugin status'),
                                'please wait' => l('Please wait...'),
                                'proceed with install' => l('Proceed with plugin installation'),
                                'cancel install' => l('Cancel plugin installation'),
                                'are you sure' => l("Are you sure you want to install this plugin?\r\nPlease be aware that damage to your website could result from the installation of this plugin."),
                                'canceling' => l('Canceling install. Please wait...'),
                                'finalize install' => l('Finalizing install'),
                              ));

      $theme->set_title(l('Installing plugin'));

      $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_add-small.png" alt="" /> ', l('Installing plugin'), '</h1>
  <p>', l('Please wait while the plugin is being installed.'), '</p>

  <div id="plugin_progress">
  </div>';

      $theme->footer();
    }
  }
}

if(!function_exists('admin_plugins_add_ajax'))
{
  /*
    Function: admin_plugins_add_ajax

    Installs the plugin through AJAX requests.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add_ajax()
  {
    global $api, $base_url, $db, $member, $plugin_dir, $theme_url;

    if(!$member->can('add_plugins'))
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

    # Gotta make sure the file supplied is valid.
    $filename = realpath($plugin_dir. '/'. $_POST['filename']);
    $extension = explode('.', $filename);

    if(empty($filename) || !file_exists($filename) || !is_file($filename) || substr($filename, 0, strlen($plugin_dir)) != realpath($plugin_dir) || count($extension) < 2 || $extension[count($extension) - 1] != 'tmp')
    {
      echo json_encode(array('error' => l('The supplied plugin file either does not exist or is not a valid file.')));
      exit;
    }

    # Our response will be held here :-)
    $response = array('error' => '');

    # Canceling? Maybe!
    if($_GET['step'] == 'cancel')
    {
      # Cancel it by deleting everything.
      @recursive_unlink($plugin_dir. '/'. substr(basename($filename), 0, strlen(basename($filename)) - 4));
      @unlink($filename);
    }
    elseif($_GET['step'] == 1)
    {
      # An array, please :-)
      $response['message'] = array(
                               'border' => null,
                               'background' => null,
                               'text' => null,
                               'proceed' => false,
                             );

      $status = plugin_check_status($filename, $reason);
      $plugin_info = plugin_load($plugin_dir. '/'. substr(basename($filename), 0, strlen(basename($filename)) - 4));

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
    elseif($_GET['step'] == 2)
    {
      # The Update class can extract a file for us.
      $update = $api->load_class('Update');

      $name = basename($filename);
      $name = substr($name, 0, strlen($name) - 4);

      # We need to make the directory.
      if(!file_exists($plugin_dir. '/'. $name) && !@mkdir($plugin_dir. '/'. $name, 0755, true))
      {
        $response['error'] = l('Failed to create the temporary plugin folder. Make sure the plugins directory is writable.');
      }
      elseif($update->extract($filename, $plugin_dir. '/'. $name))
      {
        # Just because it extracted doesn't mean it is a valid plugin!!!
        if(plugin_load($plugin_dir. '/'. $name) === false)
        {
          $response['error'] = l('The uploaded file was not a valid plugin.');

          @recursive_unlink($plugin_dir. '/'. $name);
          @unlink($filename);
        }
        else
        {
          $response['message'] = l('The plugin was successfully extracted. Proceeding...');
        }
      }
      else
      {
        $response['error'] = l('Failed to extract the plugin file.');
        @recursive_unlink($plugin_dir. '/'. $name);
      }
    }
    elseif($_GET['step'] == 3)
    {
      # Get the directory of where the plugin is currently at.
      $path = $plugin_dir. '/'. substr(basename($filename), 0, strlen(basename($filename)) - 4);
      $plugin_info = plugin_load($path);
      $name = basename($path);

      # Add the plugin to the database.
      $result = $db->insert('ignore', '{db->prefix}plugins',
        array(
          'dependency_name' => 'string-255', 'directory' => 'string',
        ),
        array(
          $plugin_info['dependency'], $name,
        ), array(), 'admin_plugins_add_query');

      # Does this plugin already exist?
      if($result->affected_rows() == 0)
      {
        @unlink($filename);
        @recursive_unlink($path);
        $response['error'] = l('A plugin with that dependency name already exists!');
      }
      else
      {
        # Any install file? Run it!
        if(file_exists($path. '/install.php'))
        {
          require_once($path. '/install.php');
          @unlink($path. '/install.php');
        }

        @unlink($filename);

        $response['message'] = l('Plugin installed successfully! <a href="%s">Go back to plugin management</a>.', $base_url. '/index.php?action=admin&sa=plugins_manage');
      }
    }

    echo json_encode($response);
  }
}
?>