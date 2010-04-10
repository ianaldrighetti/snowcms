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
      # That's what I thought!
      admin_access_denied();

    $api->run_hooks('admin_update');

    # We will need some JavaScript.
    $theme->add_js_file(array('src' => $theme_url. '/default/js/admin_update.js'));

    $theme->set_title(l('Update'));

    $theme->header();

    echo '
  <h1><img src="', $base_url, '/core/admin/icons/update-small.png" alt="" /> ', l('Check for updates'), '</h1>
  <p>', l('Just as with computers, it is a good idea to ensure that your system is up to date to make sure that you are not vulnerable to any security issues, or just to fix any bugs in the system.'), '</p>
  <br />
  <p>Your version: <span id="your_version">', $settings->get('version', 'string'), '</span></p>
  <p>Latest version: <span id="latest_version" style="font-style: italic;" title="Currently checking for the latest version. Please hold.">Checking...</span></p>

  <div id="response">
  </div>';

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

if(!function_exists('admin_update_system'))
{
  /*
    Function: admin_update_system

    By calling on this function, the process of updating begins.
    There are three steps, the first being the downloading of the
    update (and checking its integrity), the second is extracting
    the update from the tarball (and gzip, if the system supports
    it), and lastly, the application of the update.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.

      This function is meant to be called via an AJAX request.
  */
  function admin_update_system()
  {
    global $api, $base_dir, $member, $settings;

    $api->run_hooks('admin_update_system');

    if(!$member->can('update_system'))
    {
      echo json_encode(array('error' => l('Access denied.')));
      exit;
    }

    # There is no stopping now!
    ignore_user_abort(true);
    @set_time_limit(600);

    # Which step are you on?
    if(empty($_GET['step']))
    {
      echo json_encode(array('error' => l('No update step specified!')));
      exit;
    }
    elseif($_GET['step'] == 1)
    {
      # Check to see if there is a need to update the system.
      $http = $api->load_class('HTTP');
      $latest_info = @unserialize($http->request($api->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'). '?version='. $settings->get('version', 'string')));

      if(empty($latest_info['uptodate']))
      {
        # Looks like you do need to update your system.
        # Let's download it!
        if($http->request($api->apply_filters('admin_update_download_url', 'http://download.snowcms.com/updates/'). $latest_info['version']. '.tar'. (function_exists('gzinflate') ? '.gz' : ''), array(), 0, $base_dir. '/'. $latest_info['version']. '.tar'. (function_exists('gzinflate') ? '.gz' : '')))
        {
          echo json_encode(array('text' => l('The update was downloaded successfully.'),'file' => ($api->apply_filters('admin_update_download_url', 'http://download.snowcms.com/updates/'). $latest_info['version']. '.tar'. (function_exists('gzinflate') ? '.gz' : '')), 'next_step' => 2));
          exit;
        }
        else
        {
          echo json_encode(array('error' => l('Failed to download the update.'), 'next_step' => false));
          exit;
        }
      }
      else
      {
        echo json_encode(array('error' => l('Your system is already up to date.')));
        exit;
      }
    }
  }
}
?>