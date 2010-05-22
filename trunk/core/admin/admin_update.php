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

    # We will need some JavaScript.
    $theme->add_js_file(array('src' => $theme_url. '/default/js/admin_update.js'));

    $theme->set_current_area('system_update');

    $theme->set_title(l('Update'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/update-small.png" alt="" /> ', l('Check for updates'), '</h1>
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
      admin_access_denied();

    # You doing an AJAX request?
    if(!empty($_GET['step']) && (string)$_GET['step'] == (string)(int)$_GET['step'])
    {
      echo json_encode(admin_update_apply_step($_GET['step']));
      exit;
    }

    $version = basename($version);

    $theme->set_current_area('system_update');
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

    # Downloading the update?
    if($step == 1)
    {
      # The download URL, somewhat important, ya know?
      $download_url = $api->apply_filters('admin_update_url', 'http://download.snowcms.com/updates/'. $filename);

      # And the checksum (SHA1) of the update.
      $checksum_download_url = $api->apply_filters('admin_update_checksum_url', 'http://download.snowcms.com/updates/'. $filename. '.chksum');

      # HTTP class will sure come in handy ;)
      $http = $api->load_class('HTTP');

      $downloaded = $http->request($download_url, array(), 0, $base_dir. '/'. $filename);

      # Did it work?
      if(empty($downloaded))
      {
        $response['error'] = l('Failed to download the update package (%s).', $filename);
      }
      else
      {
        # Now check the files integrity.
        $checksum = $http->request($checksum_download_url);

        # Didn't get the checksum?
        if(empty($checksum) || strlen($checksum) != 40)
        {
          $response['error'] = l('Failed to obtain the checksum of %s.', $filename);
          unlink($base_dir. '/'. $filename);
        }
        # Is the update bad?
        elseif(sha1_file($base_dir. '/'. $filename) != $checksum)
        {
          $response['error'] = l('The update package (%s) is corrupt. Update failed.', $filename);
          unlink($base_dir. '/'. $filename);
        }
        else
        {
          $response['message'] = l('The update package (%s) was downloaded successfully. Proceeding...', $filename);
        }
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
          $created = @mkdir($base_dir. '/update/', 0777, true);

          # Did it not work?
          if(empty($created))
          {
            $response['error'] = l('Failed to create the <em>update</em> directory.');
          }
        }

        if(empty($response['error']))
        {
          # Time for the Tar class. Extract away!!!
          $tar = $api->load_class('Tar');

          # Open the tarball : ) Or try, at least.
          $is_open = $tar->open($base_dir. '/'. $filename);

          # Did it not work? That's not good!
          if(!$is_open)
          {
            $response['error'] = l('Failed to open the update package (%s).', $filename);
          }
          else
          {
            # Is the package a gzipped tarball? If so, extract it from there!!!
            if($tar->is_gzipped())
            {
              # Try to extract it, anyways.
              if(!$tar->ungzip())
                $response['error'] = l('Failed to extract the update package (%s) from its gzipped state.', $filename);
            }

            if(empty($response['error']))
            {
              # Now to finally extract the update package from the tarball format. Woo!
              if($tar->extract($base_dir. '/update/'))
              {
                $response['message'] = l('The update package (%s) has been extracted. Proceeding...', $filename);
              }
              else
              {
                $response['error'] = l('Failed to extract the update package (%s) from its tarball state.', $filename);
              }
            }
          }
        }
      }
    }
    elseif($step == 3)
    {
      if(!file_exists($base_dir. '/update/'))
        $response['error'] = l('The update directory was not found.');
      else
      {
        # Awesome, now it is time for some copying!!!
        # Well at least to load the data.
        $_SESSION['files'] = get_listing($base_dir. '/update/', true);
        $response['message'] = $_SESSION['files'];
      }
    }
    elseif($step == 4)
    {
      # Now to actually copy the files.
      if(!file_exists($base_dir. '/update/') || empty($_SESSION['files']))
        $response['error'] = l('The update directory was not found.');
      else
      {
        # So which file?
        $filename = $_POST['filename'];

        # Is it a valid file?
        if(!in_array($filename, $_SESSION['files']))
        {
          $response['error'] = l('The file (%s) was invalid.', htmlchars($filename));
        }
        else
        {
          # Do we need to make a directory?
          $dirname = dirname($base_dir. '/'. $filename);
          if(!file_exists($dirname))
            @mkdir($dirname, 0755, true);

          # Is it not a directory?
          if(!is_dir($base_dir. '/update/'. $filename))
          {
            $fp = fopen($base_dir. '/'. $filename, 'wb');
            flock($fp, LOCK_EX);

            $new_fp = fopen($base_dir. '/update/'. $filename, 'rb');
            flock($new_fp, LOCK_SH);

            # Now copy!!!
            while(!feof($new_fp))
              fwrite($fp, fread($new_fp, 4096));

            fclose($fp);
            fclose($new_fp);
          }
        }
      }
    }
    elseif($step == 5)
    {
      # Now it is time to finalize everything.
      # Such as removing the update folder.
      recursive_unlink($base_dir. '/update/');

      # And the update package.
      unlink($base_dir. '/'. $filename);

      # Now to execute the update file. If any.
      if(file_exists($base_dir. '/update.php'))
      {
        require_once($base_dir. '/update.php');

        # Now delete it. We don't need it anymore!
        unlink($base_dir. '/update.php');
      }

      $response['message'] = '<span style="color: green;">'. l('You have successfully updated to v%s.', $settings->get('version', 'string')). '</span> <a href="'. $base_url. '/index.php?action=admin&amp;sa=update">'. l('Check for updates'). '</a>.';
    }

    return $response;
  }
}

/*
  Function: get_listing

  Parameters:
    string $path - The path to get the recursive listing of.
    bool $implode - !!!

  Returns:
    array - Returns an array containing the listing.
*/
function get_listing($path, $implode = false)
{
  $files = scandir($path);
  $listing = array();

  if(count($files) > 0)
  {
    foreach($files as $file)
    {
      if($file == '.' || $file == '..')
        continue;

      if(is_dir($path. '/'. $file))
        $listing[$file. '/'] = array();
      else
        $listing[$file] = $file;

      if(is_dir($path. '/'. $file))
        # Woo for recursion!
        $listing[$file] = get_listing($path. '/'. $file);
    }
  }

  if(!empty($implode))
  {
    $tmp = array();

    if(count($listing))
    {
      foreach($listing as $file => $f)
      {
        $tmp[] = $file;

        if(is_array($f))
        {
          $append = get_listing_implode($f);

          if(count($append))
            foreach($append as $a)
              $tmp[] = $file. '/'. $a;
        }
      }

      $listing = $tmp;
    }
  }

  return $listing;
}

function get_listing_implode($array)
{
  $tmp = array();

  if(count($array))
  {
    foreach($array as $a => $d)
    {
      $tmp[] = $a;

      if(is_array($d))
      {
        $append = get_listing_implode($d);

        if(count($append))
          foreach($append as $g)
            $tmp[] = $a. '/'. $g;
      }
    }
  }

  return $tmp;
}
?>