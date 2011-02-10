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

# Title: Control Panel - System Update

if(!function_exists('admin_update'))
{
  /*
    Function: admin_update

    Handles the interface of updating the SnowCMS system.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_update()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_url;

    $api->run_hooks('admin_update');

    # Can you update the system?
    if(!$member->can('update_system'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    // When was the last time we checked for system updates? (Or do you want us to check anyways?)
    if($settings->get('system_last_update_check', 'int', 0) < (time() + $settings->get('system_update_interval', 'int', 3600)) || isset($_REQUEST['check']))
    {
      $http = $api->load_class('HTTP');

      $latest_version = $http->request($api->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'));
      $latest_info = @unserialize($http->request($api->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'). '?version='. $settings->get('version', 'string')));


    }
    else
    {
      $latest_version = $settings->get('system_latest_version', 'string', null);
      $latest_info = @unserialize($settings->get('system_latest_info', 'string', 'a:0:{}'));
    }

    // Is an update required?
    $is_update_required = version_compare($settings->get('version', 'string'), $latest_version) == -1;
    $latest_info = array_merge(array('header' => '', 'text' => ''), $latest_info);

    $theme->set_current_area('system_update');

    $theme->set_title(l('Update'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/update-small.png" alt="" /> ', l('Check for updates'), '</h1>
  <p>', l('Just as with computers, it is a good idea to ensure that your system is up to date to make sure that you are not vulnerable to any security issues, or just to fix any bugs in the system.'), '</p>
  <br />
  <p>Your version: <span class="', !empty($is_update_required) ? 'red bold' : 'green', '">', $settings->get('version', 'string'), '</span></p>
  <p>Latest version: ', $latest_version, '</p>

  <h1 style="font-size: 14px;">', l($latest_info['header']), '</h1>
  <p>', l($latest_info['text']), '</p>
  <br />
  <p>', !empty($is_update_required) ? '<a href="'. $base_url. '/index.php?action=admin&amp;sa=update&amp;apply='. $latest_version. '&amp;sc='. $member->session_id(). '" title="'. l('Apply update'). '">'. l('Apply update'). '</a> | ' : '', ' <a href="', $base_url, '/index.php?action=admin&amp;sa=update&amp;check" title="', l('Check for updates'), '">', l('Check for updates'), '</a></p>';

    $theme->footer();
  }
}

if(!function_exists('admin_update_ajax'))
{
  /*
    Function: admin_update_ajax

    Returns current SnowCMS version information.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_update_ajax()
  {
    global $api, $member, $settings;

    $api->run_hooks('admin_update_ajax');

    if(!$member->can('update_system'))
    {
      echo json_encode(array('error' => l('Access denied.')));
      exit;
    }

    $http = $api->load_class('HTTP');

    $latest_version = $http->request($api->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'));
    $latest_info = @unserialize($http->request($api->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'). '?version='. $settings->get('version', 'string')));

    echo json_encode(array(
                      'version' => $latest_version,
                      'needs_update' => version_compare($settings->get('version', 'string'), $latest_version) == -1,
                      'header' => $latest_info['header'],
                      'text' => $latest_info['text'],
                     ));
  }
}

if(!function_exists('admin_update_apply'))
{
  /*
    Function: admin_update_apply

    Handles the updating of the system to the specified version.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_update_apply()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_url;

    $version = $_GET['apply'];

    $api->run_hooks('admin_update_system');

    if(!$member->can('update_system'))
    {
      admin_access_denied();
    }

    # You doing an AJAX request?
    if(!empty($_GET['step']) && (string)$_GET['step'] == (string)(int)$_GET['step'])
    {
      echo json_encode(admin_update_apply_step($_GET['step']));
      exit;
    }

    $version = basename($version);

    $theme->set_current_area('system_update');

    # Verify your session id.
    verify_request('get');

    $theme->add_js_file(array('src' => $theme_url. '/default/js/admin_update_apply.js'));
    $theme->add_js_var('current_version', $settings->get('version', 'string'));
    $theme->add_js_var('version', $version);
    $theme->add_js_var('start_update', version_compare($settings->get('version', 'string'), $version));
    $theme->add_js_var('l', array(
                              'downloading' => l('Downloading update...'),
                              'extracting' => l('Extracting update package...'),
                              'copying' => l('Copying files...'),
                              'currently copying' => l('Copying:'),
                              'copy success' => l('Copying of the files was successful. Proceeding...'),
                              'executing' => l('Executing update...'),
                              'please wait' => l('Please wait.'),
                            ));

    $theme->set_title(l('Applying system update'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/update-small.png" alt="" /> ', l('Apply update v%s', $version), '</h1>';

  if(version_compare($settings->get('version', 'string'), $version) > -1)
  {
    echo '
  <h3>', l('Update not required'), '</h3>
  <p>', l('The supplied version is older than the current SnowCMS version. No update required.'), '</p>';
  }
  else
  {
    echo '
  <div id="update_progress">

  </div>';
  }

    $theme->footer();
  }
}

if(!function_exists('admin_update_apply_step'))
{
  /*
    Function: admin_update_apply_step

    Applies the specified update step.

    Parameters:
      int $step - The step number.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_update_apply_step($step)
  {
    global $api, $base_dir, $base_url, $member, $settings;

    $step = (int)$step;
    $version = !empty($_GET['apply']) ? basename($_GET['apply']) : $settings->get('version', 'string');

    if(!$member->can('update_system'))
    {
      return array('error' => l('Access denied.'));
    }
    elseif(version_compare($settings->get('version', 'string'), $version) > -1)
    {
      return array('error' => $version);
    }

    # Just to make life easier ;)
    $filename = $version. '.tar'. (function_exists('gzdeflate') ? '.gz' : '');

    $response = array('error' => '');

    # The update class can make this nice and easy for us!
    $update = $api->load_class('Update');

    # Downloading the update?
    if($step == 1)
    {
      # The download URL, somewhat important, ya know?
      $download_url = $api->apply_filters('admin_update_url', 'http://download.snowcms.com/updates/'. $filename);

      # And the checksum (SHA1) of the update.
      $checksum_download_url = $api->apply_filters('admin_update_checksum_url', 'http://download.snowcms.com/updates/'. $filename. '.chksum');

      # The download method of the Update class does this all for us...
      # cool, huh?
      $response = $update->download($download_url, $base_dir. '/'. $filename, $checksum_download_url);

      # Did it work?
      if(!$response['downloaded'])
      {
        $response['error'] = l('Failed to download the update package (%s).', $filename);
      }
      elseif($response['valid'] === false)
      {
        # Looks like the checksum didn't match. Woops!
        $response['error'] = l('The update package (%s) is corrupt. Update failed.', $filename);
      }
      else
      {
        # Cool, worked!
        $response['message'] = l('The update package (%s) was downloaded successfully. Proceeding...', $filename);
      }
    }
    elseif($step == 2)
    {
      # Does the update package not exist?
      if(!file_exists($base_dir. '/'. $filename))
      {
        $response['error'] = l('The update package (%s) was not found.', $filename);
      }
      else
      {
        # We will need to make a temporary directory.
        if(!file_exists($base_dir. '/update/'))
        {
          # Did it not work?
          if(!mkdir($base_dir. '/update/', 0777, true))
          {
            $response['error'] = l('Failed to create the <em>update</em> directory.');
          }
        }

        # Once again, the Update class has us covered :-)
        if($update->extract($base_dir. '/'. $filename, $base_dir. '/update/', 'tar'))
        {
          $response['message'] = l('The update package (%s) has been extracted. Proceeding...', $filename);
        }
        else
        {
          $response['error'] = l('Failed to extract the update package (%s) from its gzipped state.', $filename);
        }
      }
    }
    elseif($step == 3)
    {
      if(!file_exists($base_dir. '/update/'))
      {
        $response['error'] = l('The update directory was not found.');
      }
      else
      {
        # Awesome, now it is time for some copying!!!
        # Well at least to load the data.
        $response['message'] = $update->get_listing($base_dir. '/update/');
      }
    }
    elseif($step == 4)
    {
      # Now to actually copy the files.
      if(!file_exists($base_dir. '/update/'))
      {
        $response['error'] = l('The update directory was not found.');
      }
      else
      {
        # Once again, the Update class can handle this :P
        $update->copy($base_dir. '/update/', $base_dir, $_POST['filename']);
      }
    }
    elseif($step == 5)
    {
      # Now it is time to finalize everything.
      # Such as removing the update folder and what not.
      $update->finish($base_dir. '/update/', $base_dir);

      $response['message'] = '<span style="color: green;">'. l('You have successfully updated to v%s.', $settings->get('version', 'string')). '</span> <a href="'. $base_url. '/index.php?action=admin&amp;sa=update">'. l('Check for updates'). '</a>.';
    }

    return $response;
  }
}
?>