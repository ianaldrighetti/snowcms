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
    if(($settings->get('system_last_update_check', 'int', 0) + $settings->get('system_update_interval', 'int', 3600)) < time() || isset($_REQUEST['check']))
    {
      $http = $api->load_class('HTTP');

      $latest_version = $http->request($api->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'));
      $latest_info = @unserialize($http->request($api->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'). '?version='. $settings->get('version', 'string')));

      $settings->set('system_last_update_check', time(), 'int');
      $settings->set('system_latest_version', $latest_version, 'string');
      $settings->set('system_latest_info', serialize($latest_info), 'string');

      redirect('index.php?action=admin&sa=update');
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
  <p>', !empty($is_update_required) ? '<a href="'. $base_url. '/index.php?action=admin&amp;sa=update&amp;apply='. $latest_version. '&amp;sid='. $member->session_id(). '" title="'. l('Apply update'). '">'. l('Apply update'). '</a> | ' : '', ' <a href="', $base_url, '/index.php?action=admin&amp;sa=update&amp;check" title="', l('Check for updates'), '">', l('Check for updates'), '</a></p>';

    $theme->footer();
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
    global $api, $base_dir, $base_url, $member, $settings, $theme, $theme_url;

    $version = $_GET['apply'];

    $api->run_hooks('admin_update_system');

    if(!$member->can('update_system'))
    {
      admin_access_denied();
    }

    $version = basename($version);

    $theme->set_current_area('system_update');

    // Verify your session id.
    verify_request('get');

    // !!! TODO: Make sure $version is a number.

    // Do we not need to apply an update?
    if(version_compare($settings->get('version', 'string', null), $version) > -1)
    {
      $theme->set_title(l('An error has occurred'));

      $theme->header();

      echo '
  <h1><img src="', $theme->url(), '/update-small.png" alt="" /> ', l('No update required'), '</h1>
  <p>', l('No update needs to be applied at this time. <a href="%s">Back to system update</a>.', $base_url. '/index.php?action=admin&amp;sa=update'), '</p>';

      $theme->footer();
    }
    else
    {
      $theme->set_title(l('Applying system update'));

      $theme->header();

      echo '
  <h1><img src="', $theme->url(), '/update-small.png" alt="" /> ', l('Apply update v%s', $version), '</h1>
  <p>', l('Please wait while SnowCMS applies the system update.'), '</p>

  <h3>Downloading the update</h3>';

      // Man, that Update class is awesome, isn't it?!
      $update = $api->load_class('Update');

      // We will download the gzip package, if the system will allow it.
      $filename = $version. '.tar'. (function_exists('gzdeflate') ? '.gz' : '');

      // This is where we will download the update from :-)
      $download_url = $api->apply_filters('admin_update_url', 'http://download.snowcms.com/updates/'. $filename);

      // Our checksum, as well. Want to be sure of the packages integrity.
      $checksum_download_url = $api->apply_filters('admin_update_checksum_url', 'http://download.snowcms.com/updates/'. $filename. '.chksum');

      // and now, to download the update.
      $package = $update->download($download_url, $base_dir. '/'. $filename, $checksum_download_url);

      // Did the package actually get downloaded?
      if(empty($package['downloaded']))
      {
        echo '
    <p class="red">', l('Failed to download the update package "%s" from "%s".', $filename, $download_url), '</p>';
      }
      elseif(empty($package['valid']))
      {
        echo '
    <p class="red">', l('The update package "%s" is corrupt. Update process failed.', $filename), '</p>';
      }
      else
      {
        echo '
    <p class="green">', l('The update package "%s" was downloaded successfully. Proceeding...', $filename), '</p>

    <h3>Extracting update</h3>';

        // Does the update directory exist? Delete it...
        if(!@recursive_unlink($base_dir. '/update/') && is_dir($base_dir. '/update/'))
        {
          echo '
    <p class="red">', l('Could not delete the update directory. Update process failed.'), '</p>';

          // Delete the package. Sorry.
          @unlink($base_dir. '/'. $filename);
        }
        // Make a temporary directory.
        elseif(!@mkdir($base_dir. '/update/', 0777, true))
        {
          echo '
    <p class="red">', l('Could not create the temporary update directory. Update process failed.'), '</p>';

          // Delete the package. Sorry.
          @unlink($base_dir. '/'. $filename);
        }
        else
        {
          // Sure, we ought to extract it right from the base directory, but
          // just to be safe, we will try extracting it to another location
          // first.
          if(!$update->extract($base_dir. '/'. $filename, $base_dir. '/update/', 'tar'))
          {
            echo '
    <p class="red">', l('The update package "%s" could not be extracted due to an unknown error. Update process failed.', $filename), '</p>';

            // Delete...
            @unlink($base_dir. '/'. $filename);
          }
          else
          {
            echo '
    <p class="green">', l('The update package "%s" was successfully extracted. Proceeding...', $filename), '</p>

    <h3>Copying update files</h3>';

            // Time to do some copying.
            $copied_files = 0;
            foreach($update->get_listing($base_dir. '/update/') as $updated_filename)
            {
              $update->copy($base_dir. '/update/', $base_dir, $updated_filename);

              $copied_files++;
            }

            echo '
    <p class="green">', l('A total of %u update files were successfully copied. Proceeding...', $copied_files), '</p>

    <h3>Completing update</h3>';

    // Alright, we are DONE! Woo!
    $update->finish($base_dir. '/update/', $base_dir);

    // We don't need this anymore.
    @unlink($base_dir. '/'. $filename);

    echo '
    <p class="green">', l('You have been successfully updated to v%s. <a href="%s">Go to the control panel</a>.', $settings->get('version', 'string'), $base_url. '/index.php?action=admin'), '</p>';
          }
        }
      }

      $theme->footer();
    }
  }
}
?>