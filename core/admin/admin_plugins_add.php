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

if(!defined('INSNOW'))
{
  die('Nice try...');
}

// Title: Control Panel - Plugins - Add

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
    api()->run_hooks('admin_plugins_add');

    // Can you add plugins?
    if(!member()->can('add_plugins'))
    {
      // That's what I thought!
      admin_access_denied();
    }

    admin_plugins_add_generate_form();
    $form = api()->load_class('Form');

    if(!empty($_POST['add_plugins_form']))
    {
      $form->process('add_plugins_form');
    }

    admin_current_area('plugins_add');

    theme()->set_title(l('Add Plugin'));

    api()->context['form'] = $form;

    theme()->render('admin_plugins_add');
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
    $form = api()->load_class('Form');

    // Let's get to making our form, shall we?
    $form->add('add_plugins_form', array(
                                     'action' => baseurl. '/index.php?action=admin&amp;sa=plugins_add',
                                     'callback' => 'admin_plugins_add_handle',
                                     'method' => 'post',
                                     'submit' => l('Add plugin'),
                                   ));

    // Do you want to upload the plugin?
    $form->add_field('add_plugins_form', 'plugin_file', array(
                                                          'type' => 'file',
                                                          'label' => l('From a file:'),
                                                          'subtext' => l('Select the plugin file you want to install.'),
                                                        ));

    // A URL? Sure!
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
             {baseurl}/index.php?action=admin&sa=plugins_add&install={filename}
             where the status of the plugin is checked and then installed.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add_handle($data, &$errors = array())
  {
    // Where should this plugin go..?
    $filename = plugindir. '/'. uniqid('plugin_'). '.tmp';
    while(file_exists($filename))
    {
      $filename = plugindir. '/'. uniqid('plugin_'). '.tmp';
    }

    // Uploading a file, are we?
    if(!empty($data['plugin_file']['tmp_name']))
    {
      // Simply try to move the file now.
      if(!move_uploaded_file($data['plugin_file']['tmp_name'], $filename))
      {
        // Woops, didn't work!
        $errors[] = l('Plugin upload failed.');
        return false;
      }
    }
    // You want us to download it? I can do that.
    elseif(!empty($data['plugin_url']) && strtolower($data['plugin_url']) != 'http://')
    {
      // The HTTP class can do all this, awesomely, of course!
      $http = api()->load_class('HTTP');

      if(!$http->request($data['plugin_url'], array(), 0, $filename))
      {
        // Sorry, but looks like it didn't work!!!
        $errors[] = l('Failed to download the plugin from "%s"', htmlchars($data['plugin_url']));
        return false;
      }
    }
    else
    {
      $errors[] = l('No file or URL specified.');
      return false;
    }

    // If it worked, we get redirected!
    redirect(baseurl. '/index.php?action=admin&sa=plugins_add&install='. urlencode(basename($filename)). '&sid='. member()->session_id());
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
    api()->run_hooks('admin_plugins_install');

    // Can you add plugins?
    if(!member()->can('add_plugins'))
    {
      // That's what I thought!
      admin_access_denied();
    }

    admin_current_area('plugins_add');

    // Check the session id.
    verify_request('get');

    // Which file are you installing as a plugin?
    $filename = realpath(plugindir. '/'. basename($_GET['install']));
    $extension = explode('.', $filename);

    // Make sure the file exists, that it is a file, that it is within the
    // plugin directory, and that the extension is valid.
    if(empty($filename) || !is_file($filename) || substr($filename, 0, strlen(realpath(plugindir))) != realpath(plugindir) || count($extension) < 2 || $extension[count($extension) - 1] != 'tmp')
    {
      // Must not be valid, from what we can tell.
      theme()->set_title(l('An Error Occurred'));

			api()->context['error_title'] = l('Plugin Installation Error');
			api()->context['error_message'] = l('Sorry, but the supplied plugin file either does not exist or is not a valid file.');

      theme()->render('error');
    }
    else
    {
      // Time to get to installation!
      theme()->set_title(l('Installing Plugin'));

			api()->context['filename'] = $filename;

      theme()->render('admin_plugins_install');
    }
  }
}

if(!function_exists('admin_plugins_get_message'))
{
  /*
    Function: admin_plugins_get_message

    Parameters:
      string $status
      string $plugin_name
      string $reason
      bool $is_theme

    Returns:
      array
  */
  function admin_plugins_get_message($status, $plugin_name, $reason = null, $is_theme = false)
  {
    $response = array(
                  'color' => '',
                  'message' => '',
                );

    // Is the package approved?
    if($status == 'approved')
    {
      $response['color'] = 'green';
      $response['message'] =  l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" has been reviewed and approved by the SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database.<br />Proceeding...', $plugin_name);
    }
    // Disapproved?
    elseif($status == 'disapproved')
    {
      $response['color'] = '//DB2929';
      $response['message'] = l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" has been reviewed and disapproved by the SnowCMS Dev Team.<br />Reason: %s<br />Proceed at your own risk.', $plugin_name, !empty($reason) ? l($reason) : l('None given.'));
    }
    // Deprecated? Pending..?
    elseif($status == 'deprecated' || $status == 'pending')
    {
      $response['color'] = '#1874CD';
      $response['message'] = ($status == 'deprecated' ? l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" is deprecated and a newer version is available at the <a href="http://'. (empty($is_theme) ? 'plugins' : 'themes'). '.snowcms.com/" target="_blank" title="SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database">SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database</a> site.<br />Proceed at your own risk.', $plugin_name) : l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" is currently under review by the SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database, so no definitive status can be given.<br />Proceed at your own risk.', $plugin_name));
    }
    elseif(in_array($status, array('unknown', 'malicious', 'insecure')))
    {
      if($status == 'unknown')
      {
        $response['message'] = l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" is unknown to the <a href="http://'. (empty($is_theme) ? 'plugins' : 'themes'). '.snowcms.com/" target="_blank" title="SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database">SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database</a> site.<br />Proceed at your own risk.', $plugin_name);
      }
      elseif($status == 'malicious')
      {
        $response['message'] = l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" has been identified as malicious and it is not recommended you continue.<br />Reason: %s<br />Proceed at your own risk.', $plugin_name, !empty($reason) ? l($reason) : l('None given.'));
      }
      elseif($status == 'insecure')
      {
        $response['message'] = l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" has known security issues, it is recommended that you not continue.<br />Reason: %s<br />Proceed at your own risk.', $plugin_name, !empty($reason) ? l($reason) : l('None given.'));
      }

      $response['color'] = '#DB2929';
    }
    else
    {
      api()->run_hooks('admin_plugins_handle_status', array(&$response, $plugin_name, $reason, !empty($is_theme)));
    }

    return $response;
  }
}
?>