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

# Title: Control Panel - Themes

if(!function_exists('admin_themes'))
{
  /*
    Function: admin_themes

    Provides an interface for the selecting and uploading/downloading of themes.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_themes()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_dir, $theme_url;

    $api->run_hooks('admin_themes');

    # Can you view the error log? Don't try and be sneaky now!
    if(!$member->can('manage_themes'))
    {
      # Get out of here!!!
      admin_access_denied();
    }

    # Time for a Form, awesomeness!!!
    admin_themes_generate_form();
    $form = $api->load_class('Form');

    if(isset($_POST['install_theme_form']))
    {
      $form->process('install_theme_form');
    }

    # A couple things could happen :P
    # So let's just group them.
    if((!empty($_GET['set']) || !empty($_GET['delete'])) && verify_request('get'))
    {
      if(!empty($_GET['set']))
      {
        # Pretty simple to change the current theme ;-)
        $new_theme = basename($_GET['set']);

        # Check to see if the theme exists.
        if(file_exists($theme_dir. '/'. $new_theme) && theme_load($theme_dir. '/'. $new_theme) !== false)
        {
          # Simple enough, set the theme.
          $settings->set('theme', $new_theme, 'string');
        }
      }
      elseif(!empty($_GET['delete']))
      {
        # Deleting, are we?
        $delete_theme = basename($_GET['delete']);

        # Make sure it isn't the current theme.
        if($settings->get('theme', 'string', 'default') != $delete_theme && theme_load($theme_dir. '/'. $delete_theme) !== false)
        {
          # It's not, so we can delete it.
          # Which is simply a recursive delete.
          recursive_unlink($theme_dir. '/'. $delete_theme);
        }
      }

      # Let's get you out of here now :-)
      redirect($base_url. '/index.php?action=admin&sa=themes');
    }

    $theme->set_current_area('manage_themes');

    $theme->set_title(l('Manage themes'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/manage_themes-small.png" alt="" /> ', l('Manage themes'), '</h1>
  <p style="margin-bottom: 20px;">', l('Here you can set the sites theme and also install themes as well.'), '</p>';

    # Get a listing of all the themes :-).
    $themes = theme_list();

    # Now load the information of the current theme.
    $current_theme = theme_load($theme_dir. '/'. $settings->get('theme', 'string', 'default'));

    echo '
  <div style="float: left; width: 200px;">
    <img src="', $theme_url, '/', $settings->get('theme', 'string', 'default'), '/image.png" alt="" title="', $current_theme['name'], '" />
  </div>
  <div style="float: right; width: 590px;">
    <h1 style="margin-top: 0px;">', l('Current theme: %s', $current_theme['name']), '</h1>
    <h3 style="margin-top: 0px;">', l('By %s', (!empty($current_theme['website']) ? '<a href="'. $current_theme['website']. '">' : ''). $current_theme['author']. (!empty($current_theme['website']) ? '</a>' : '')), '</h3>
    <p>', $current_theme['description'], '</p>
  </div>
  <div class="break">
  </div>
  <h1 style="margin-top: 20px;">', l('Available themes'), '</h1>
  <table class="theme_list">
    <tr>';

    # List all the themes ;-)
    $length = count($themes);
    for($i = 0; $i < $length; $i++)
    {
      $theme_info = theme_load($themes[$i]);

      if(($i + 1) % 3 == 1)
      {
        echo '
    </tr>
  </table>
  <table class="theme_list">
    <tr>';
      }

      // Check to see if there is an update available.
      $update_available = false;

      // There is a file containing the new version...
      if(file_exists($theme_info['path']. '/available-update') && version_compare(file_get_contents($theme_info['path']. '/available-update'), $theme_info['version'], '>'))
      {
        $update_available = file_get_contents($theme_info['path']. '/available-update');
      }

      echo '
      <td><a href="', $base_url, '/index.php?action=admin&amp;sa=themes&amp;set=', urlencode(basename($theme_info['path'])), '&amp;sid=', $member->session_id(), '" title="', l('Set as site theme'), '"', (basename($theme_info['path']) == $settings->get('theme', 'string', 'default') ? ' class="selected"' : ''), '><img src="', $theme_url, '/', basename($theme_info['path']), '/image.png" alt="" title="', $theme_info['description'], '" /><br />', $theme_info['name'], ' </a><br /><a href="', $base_url, '/index.php?action=admin&amp;sa=themes&amp;delete=', urlencode(basename($theme_info['path'])), '&amp;sid=', $member->session_id(), '" title="', l('Delete %s', $theme_info['name']), '" onclick="', ($settings->get('theme', 'string', 'default') == basename($theme_info['path']) ? 'alert(\''. l('You cannot delete the current theme.'). '\'); return false;' : 'return confirm(\''. l('Are you sure you want to delete this theme?\r\nThis cannot be undone!'). '\');"'), '" class="button">', l('Delete'), '</a>', !empty($update_available) ? '<a href="'. $base_url. '/index.php?action=admin&amp;sa=themes&amp;update='. urlencode(basename($theme_info['path'])). '&amp;version='. urlencode($update_available). '&amp;sid='. $member->session_id(). '" title="'. l('Update theme to version %s', htmlchars($update_available)). '" class="button important">'. l('Update available'). '</a>' : '', '</td>';
    }

    echo '
    </tr>
  </table>

  <h1>', l('Install a theme'), '</h1>
  <p>', l('Below you can specify a file to upload or a URL at which to download a theme (zip, tar and tar.gz only).'), '</p>';

    $form->show('install_theme_form');

    $theme->footer();
  }
}

if(!function_exists('admin_themes_generate_form'))
{
  /*
    Function: admin_themes_generate_form

    Generates the form which allows themes to be installed.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_themes_generate_form()
  {
    global $api, $base_url;

    $form = $api->load_class('Form');

    $form->add('install_theme_form', array(
                                       'action' => $base_url. '/index.php?action=admin&amp;sa=themes',
                                       'method' => 'post',
                                       'callback' => 'admin_themes_handle',
                                       'submit' => l('Install theme'),
                                     ));

    $form->add_field('install_theme_form', 'theme_file', array(
                                                           'type' => 'file',
                                                           'label' => l('From a file:'),
                                                           'subtext' => l('Select the theme file you want to install as a theme.'),
                                                         ));

    $form->add_field('install_theme_form', 'theme_url', array(
                                                          'type' => 'string',
                                                          'label' => l('From a URL:'),
                                                          'subtext' => l('Enter the URL of the theme you want to download and install.'),
                                                          'value' => 'http://',
                                                        ));
  }
}

if(!function_exists('admin_themes_handle'))
{
  /*
    Function: admin_themes_handle

    Handles the installation of the theme.

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      This function is overloadable.
  */
  function admin_themes_handle($data, &$errors = array())
  {
    global $api, $base_url, $member, $theme_dir;

    # Make a temporary file name which will be used for either downloading or uploading.
    $filename = $theme_dir. '/'. uniqid('theme_'). '.tmp';
    while(file_exists($filename))
    {
      $filename = $theme_dir. '/'. uniqid('theme_'). '.tmp';
    }

    # Downloading a theme, are we?
    if(!empty($data['theme_url']) && strtolower($data['theme_url']) != 'http://')
    {
      # We will need the HTTP class.
      $http = $api->load_class('HTTP');

      # Now attempt to download it.
      if(!$http->request($data['theme_url'], array(), 0, $filename))
      {
        # Sorry, but it appears that didn't work!
        $errors[] = l('Failed to download the theme from "%s"', htmlchars($data['theme_url']));
        return false;
      }

      # We want the name of the file...
      $name = basename($data['theme_url']);
    }
    # Did you want to upload a theme?
    elseif(!empty($data['theme_file']['tmp_name']))
    {
      # Now attempt to move the file.
      if(move_uploaded_file($data['theme_file']['tmp_name'], $filename))
      {
        # Keep the original file name...
        $name = $data['theme_file']['name'];
      }
      else
      {
        $errors[] = l('Failed to move the uploaded file.');
        return false;
      }
    }
    else
    {
      $errors[] = l('No file or URL specified.');
      return false;
    }

    // We will need to test the theme to make sure it is okay, not
    // deprecated and so on and so forth.
    redirect($base_url. '/index.php?action=admin&sa=themes&install='. urlencode(basename($filename)). '&sid='. $member->session_id());
  }
}

if(!function_exists('admin_themes_install'))
{
  /*
    Function: admin_themes_install

    Handles the installation of new themes.

    Parameters:
      none

    Returns:
      void

    Notes:
      This function is overloadable.
  */
  function admin_themes_install()
  {
    global $api, $base_url, $core_dir, $member, $theme, $theme_dir;

    // Can you do this? If not, get out of here!
    if(!$member->can('manage_themes'))
    {
      admin_access_denied();
    }

    $theme->set_current_area('manage_themes');

    // Check their session id supplied in the URL.
    verify_request('get');

    // Get the filename of the theme we are installing.
    $filename = realpath($theme_dir. '/'. basename($_GET['install']));
    $extension = explode('.', $filename);

    // Do some file checks, making sure it is in the right place and what
    // not. Don't want to let anyone do anything tricky, that's for sure.
    if(empty($filename) || !is_file($filename) || substr($filename, 0, strlen(realpath($theme_dir))) != realpath($theme_dir) || count($extension) < 2 || $extension[count($extension) - 1] != 'tmp')
    {
      $theme->set_title(l('An error has occurred'));

      $theme->header();

      echo '
    <h1><img src="', $theme->url(), '/manage_themes-small.png" alt="" /> ', l('An error has occurred'), '</h1>
    <p>', l('Sorry, but the supplied theme file either does not exist or is not a valid file.'), '</p>';

      $theme->footer();
    }
    else
    {
      // Install that theme! Maybe.
      $theme->set_title(l('Installing theme'));

      $theme->header();

      echo '
    <h1><img src="', $theme->url(), '/manage_themes-small.png" alt="" /> ', l('Installing theme'), '</h1>
    <p>', l('Please wait while the theme is being installed.'), '</p>

    <h3>', l('Extracting theme'), '</h3>';

      // The Update class can do the work for us.
      $update = $api->load_class('Update');

      // Get the name of the theme.
      $name = explode('.', basename($filename), 2);

      // We did this to remove the extension.
      $name = $name[0];

      // Make the directory where the theme will be extracted to.
      if(!file_exists($theme_dir. '/'. $name) && !@mkdir($theme_dir. '/'. $name, 0755, true))
      {
        echo '
    <p>', l('Failed to create the temporary theme directory. Make sure the theme directory is writable.'), '</p>';
      }
      elseif($update->extract($filename, $theme_dir. '/'. $name))
      {
        // If we were able to extract the theme package, that doesn't mean
        // it is a valid theme. Time to do some checking with <theme_load>!
        if(theme_load($theme_dir. '/'. $name) === false)
        {
          echo '
    <p>', l('The uploaded package was not a valid theme.'), '</p>';

          // Delete the NOT theme (:P) and the package that was uploaded.
          recursive_unlink($theme_dir. '/'. $name);
          unlink($filename);
        }
        else
        {
          echo '
    <p>', l('The theme was successfully extracted. Proceeding...'), '</p>';

          // The theme was extracted, and it appears to be a real theme,
          // so we may continue!
          $package_extracted = true;
        }
      }
      else
      {
        // Well, the Update class couldn't extract the package, so it isn't
        // a supported format (ZIP, Tarball, or Gzip tarball). That sucks.
        echo '
    <p>', l('The uploaded package could not be extracted.'), '</p>';

        recursive_unlink($theme_dir. '/'. $name);
        unlink($filename);
      }

      // Was the package extracted? If so, we can go on!
      if(!empty($package_extracted))
      {
        // Yes, yes I know! This is for checking the status of a plugin, but
        // it can do themes too! (Not like it knows better)
        // Why are we checking, you ask? Well, think about it! A theme is
        // also PHP, and in reality, it can do just as much as any plugin
        // can, meaning it can be as dangerous as any plugin.
        $status = plugin_check_status($filename, $reason);
        $theme_info = theme_load($theme_dir. '/'. $name);

        // Get the status message, and the color that the message should be.
        // But first, include a file.
        require_once($core_dir. '/admin/admin_plugins_add.php');

        // Okay, now get the response!
        $response = admin_plugins_get_message($status, $theme_info['name'], $reason, true);

        // Is it okay? Can we continue without prompting?
        $install_proceed = isset($_GET['proceed']) || $status == 'approved';
        $api->run_hooks('plugin_install_proceed', array(&$install_proceed, $status, 'theme'));

        echo '
    <h3>', l('Verifying theme status'), '</h3>
    <p style="color: ', $response['color'], ';">', $response['message'], '</p>';

        // Was it deemed okay?
        if(!empty($install_proceed))
        {
          // Yup! Sure was!
          echo '
    <h3>', l('Finishing installation'), '</h3>
    <p>', l('The installation of the theme was completed successfully. <a href="%s">Back to theme management</a>.', $base_url. '/index.php?action=admin&sa=themes'), '</p>';

          // Delete the file, and we really are done!
          unlink($filename);
        }
        else
        {
          // Uh oh!
          // It was not safe, but if you still want to continue installing
          // it, be my guest! Just be sure you know what you're getting
          // yourself into, please!
          // We will delete the extracted theme, you know, just incase ;).
          recursive_unlink($theme_dir. '/'. $name);

          echo '
      <form action="', $base_url, '/index.php" method="get" onsubmit="return confirm(\'', l('Are you sure you want to proceed with the installation of this theme?\r\nBe sure you trust the source of this plugin.'), '\');">
        <input type="submit" value="', l('Proceed'), '" />
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="themes" />
        <input type="hidden" name="install" value="', urlencode($_GET['install']), '" />
        <input type="hidden" name="sid" value="', $member->session_id(), '" />
        <input type="hidden" name="proceed" value="true" />
      </form>';
        }
      }

      $theme->footer();
    }
  }
}

if(!function_exists('admin_themes_update'))
{
  /*
    Function: admin_themes_update

    When there is an update for a theme available, this function will handle
    the update process by downloading, extracting, and installing the new
    version of the theme.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_themes_update()
  {
    global $api, $base_url, $core_dir, $member, $theme, $theme_dir;

    // Can you do this? If not, get out of here!
    if(!$member->can('manage_themes'))
    {
      admin_access_denied();
    }

    $theme->set_current_area('manage_themes');

    // Check their session id supplied in the URL.
    verify_request('get');

		// Which theme are we updating?
		$update = !empty($_GET['update']) ? htmlchars(basename($_GET['update'])) : false;

		// To which version? (If none given, we will check for the latest)
		$version = !empty($_GET['version']) ? htmlchars($_GET['version']) : false;

		// Make sure this stuff given to us is valid. Though we only need to
		// make sure the theme given is actually a real theme.
		if(empty($update) || theme_load($theme_dir. '/'. $update) === false)
		{
      $theme->set_title(l('An error has occurred'));

      $theme->header();

      echo '
    <h1><img src="', $theme->url(), '/manage_themes-small.png" alt="" /> ', l('An error has occurred'), '</h1>
    <p>', l('Sorry, but the specified theme does not exist.'), '</p>';

      $theme->footer();
		}
		else
		{
			// Get the latest version of this theme.
			// This function will handle that for us.
			admin_themes_check_updates(array($theme_dir. '/'. $update));

			// Let's make our lives a bit easier...
			$current_dir = $theme_dir. '/'. $update;
			$theme_info = theme_load($current_dir);

			// Any update available?
			if(file_exists($current_dir. '/available-update'))
			{
				// There could be, but let's make sure...
				$new_version = file_get_contents($current_dir. '/available-update');

				// Well, if you specified a version.
				if(!empty($version))
				{
					$continue_update = version_compare($new_version, $theme_info['version'], '>');
				}
				else
				{
					// We will update you to this version, then.
					$version = $new_version;
					$continue_update = true;
				}
			}
			else
			{
				// No, no updating needed, apparently.
				$continue_update = false;
			}

			// So, how did that go?
			if(empty($continue_update))
			{
				// No update needed!
				$theme->set_title(l('Theme up-to-date'));

				$theme->header();

				echo '
			<h1><img src="', $theme->url(), '/manage_themes-small.png" alt="" /> ', l('Theme up-to-date'), '</h1>
			<p>', l('The theme "%s" is already up-to-date, so no action is required.', htmlchars($theme_info['name'])), '</p>';

				$theme->footer();
			}
			else
			{
				// Here we go!
				$theme->set_title(l('Updating theme'));

				$theme->header();

				echo '
    <h1><img src="', $theme->url(), '/manage_themes-small.png" alt="" /> ', l('Updating theme'), '</h1>
    <p>', l('Please wait while the theme is being updated.'), '</p>

    <h3>', l('Downloading update'), '</h3>';

				// Download that puppy!
				$http = $api->load_class('HTTP');

				if(!$http->request($theme_info['update_url'], array('download' => 1, 'version' => $version), 0, $current_dir. '/update-package'))
				{
					echo '
		<p class="red">', l('Failed to download v%s of "%s"', htmlchars($version), htmlchars($theme_info['name'])), '</p>';
				}
				else
				{
					echo '
		<p class="green">', l('Successfully downloaded v%s of "%s." Proceeding...', htmlchars($version), htmlchars($theme_info['name'])), '</p>

		<h3>', l('Extracting update'), '</h3>';

					// We want to extract this theme, now.
					// The Update class can do the work for us.
					$update = $api->load_class('Update');

					// A bit easier to do this:
					$filename = $current_dir. '/update-package';

					// Make the directory where the theme will be extracted to.
					if(!file_exists($current_dir. '/~update') && !@mkdir($current_dir. '/~update', 0755, true))
					{
						echo '
				<p class="red">', l('Failed to create the temporary update directory. Make sure the theme directory is writable.'), '</p>';
					}
					elseif($update->extract($filename, $current_dir. '/~update'))
					{
						// If we were able to extract the theme package, that doesn't mean
						// it is a valid theme. Time to do some checking with <theme_load>!
						if(theme_load($current_dir. '/~update') === false)
						{
							echo '
				<p class="red">', l('The update package was not a valid theme.'), '</p>';

							// Delete the NOT theme (:P) and the package that was uploaded.
							recursive_unlink($current_dir. '/~update');
							unlink($filename);
						}
						else
						{
							echo '
				<p class="green">', l('The update was successfully extracted. Proceeding...'), '</p>';

							// The theme was extracted, and it appears to be a real theme,
							// so we may continue!
							$package_extracted = true;
						}
					}
					else
					{
						// Well, the Update class couldn't extract the package, so it isn't
						// a supported format (ZIP, Tarball, or Gzip tarball). That sucks.
						echo '
				<p class="red">', l('The update package could not be extracted.'), '</p>';

						recursive_unlink($current_dir. '/~update');
						unlink($filename);
					}

					if(!empty($package_extracted))
					{
						// Yes, yes I know! This is for checking the status of a plugin, but
						// it can do themes too! (Not like it knows better)
						// Why are we checking, you ask? Well, think about it! A theme is
						// also PHP, and in reality, it can do just as much as any plugin
						// can, meaning it can be as dangerous as any plugin.
						$status = plugin_check_status($filename, $reason);

						// Get the status message, and the color that the message should be.
						// But first, include a file.
						require_once($core_dir. '/admin/admin_plugins_add.php');

						// Okay, now get the response!
						$response = admin_plugins_get_message($status, $theme_info['name'], $reason, true);

						// Is it okay? Can we continue without prompting?
						$install_proceed = isset($_GET['proceed']) || $status == 'approved';
						$api->run_hooks('plugin_install_proceed', array(&$install_proceed, $status, 'theme'));

						echo '
				<h3>', l('Verifying theme status'), '</h3>
				<p style="color: ', $response['color'], ';">', $response['message'], '</p>';

						// Was it deemed okay?
						if(!empty($install_proceed))
						{
							// Yup! Sure was!
							// Just copy over the update files.
							$update->copy($current_dir. '/~update', $current_dir);

							echo '
				<h3>', l('Finishing installation'), '</h3>
				<p>', l('The installation of the theme update was completed successfully. <a href="%s">Back to theme management</a>.', $base_url. '/index.php?action=admin&sa=themes'), '</p>';

							// Delete the file, and we really are done!
							unlink($filename);
						}
						else
						{
							// Uh oh!
							// It was not safe, but if you still want to continue installing
							// it, be my guest! Just be sure you know what you're getting
							// yourself into, please!
							// We will delete the extracted theme, you know, just incase ;).
							recursive_unlink($current_dir. '/~update');
							unlink($filename);

							echo '
					<form action="', $base_url, '/index.php" method="get" onsubmit="return confirm(\'', l('Are you sure you want to proceed with the installation of this theme update?\r\nBe sure you trust the source of this plugin.'), '\');">
						<input type="submit" value="', l('Proceed'), '" />
						<input type="hidden" name="action" value="admin" />
						<input type="hidden" name="sa" value="themes" />
						<input type="hidden" name="update" value="', urlencode($_GET['update']), '" />
						<input type="hidden" name="version" value="', urlencode(!empty($_GET['version']) ? $_GET['version'] : ''), '" />
						<input type="hidden" name="sid" value="', $member->session_id(), '" />
						<input type="hidden" name="proceed" value="true" />
					</form>';
						}
					}
				}

				$theme->footer();
			}
		}
  }
}

if(!function_exists('admin_themes_check_updates'))
{
  /*
    Function: admin_themes_check_updates

    Checks to see if the themes require any updating. If no specific theme
    directories are supplied, all themes will be checked for updates, if
    they support it.

    Parameters:
      array $themes - An array containing an array of directories which are
											at the root of the theme.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.

      If the $themes parameter is empty, it is assumed that this function is
      being called upon by the SnowCMS task system and will check for
      updates for all existing themes, so long as it hasn't been done within
      the last hour.
  */
  function admin_themes_check_updates($themes = array(), $force_check = false)
  {
    global $api, $settings;

    // Did any theme directories get supplied?
    if(count($themes) == 0)
    {
      // Since you didn't supply any themes, we will get them on our own!
      // It is likely this is being called on by the task scheduling system,
      // so let's make sure we aren't doing this too often!
      if($settings->get('last_theme_update_check', 'int', 0) + 3600 < time_utc())
      {
        // <theme_load> will get us the information we want.
        $theme_list = theme_list();

        foreach($theme_list as $theme_location)
        {
          $themes[] = $theme_location;
        }

        // We are checking now.
        $settings->set('last_theme_update_check', time_utc(), 'int');
      }
    }

    // Now for the actual checking.
    if(count($themes) > 0)
    {
      // The HTTP class will do everything we want.
      $http = $api->load_class('HTTP');

      foreach($themes as $theme_location)
      {
        // Get the theme information.
        $theme_info = theme_load($theme_location);

        // Does the theme not exist, no update URL, no version?
        if($theme_info === false || empty($theme_info['update_url']) || empty($theme_info['version']))
        {
          continue;
        }

        // We will use the supplied update URL to query for any available
        // updates. This of course requires both the update URL (of course)
        // and a current version to be supplied.
        $request = $http->request('http://'. $theme_info['update_url'], array('updatecheck' => 1, 'version' => $theme_info['version']));

        // Did we get an answer?
        if(empty($request))
        {
          // Nope, nothing. How rude!
          continue;
        }

        // If there is a new version, we will save it in a file.
        if(version_compare($request, $theme_info['version'], '>'))
        {
          $fp = fopen($theme_info['path']. '/available-update', 'w');

          flock($fp, LOCK_EX);
          fwrite($fp, htmlchars(substr(strip_tags($request), 0, 10)));
          flock($fp, LOCK_UN);

          fclose($fp);
        }
        // There is no newer version available. Delete the new version file
        // if there is one.
        elseif(file_exists($theme_info['path']. '/available-update'))
        {
          unlink($theme_info['path']. '/available-update');
        }
      }
    }
  }
}
?>