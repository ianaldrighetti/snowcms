<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

      echo '
    <h1><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Installing theme'), '</h1>
    <p>', l('Please wait while the theme is being installed.'), '</p>

    <h3>', l('Extracting theme'), '</h3>';

      // The Update class can do the work for us.
      $update = api()->load_class('Update');

      // Get the name of the theme.
      $name = explode('.', basename(api()->context['filename']), 2);

      // We did this to remove the extension.
      $name = $name[0];

      // Make the directory where the theme will be extracted to.
      if(!file_exists(themedir. '/'. $name) && !@mkdir(themedir. '/'. $name, 0755, true))
      {
        echo '
    <p>', l('Failed to create the temporary theme directory. Make sure the theme directory is writable.'), '</p>';
      }
      elseif($update->extract(api()->context['filename'], themedir. '/'. $name))
      {
        // If we were able to extract the theme package, that doesn't mean
        // it is a valid theme. Time to do some checking with <theme_load>!
        if(theme_load(themedir. '/'. $name) === false)
        {
          echo '
    <p>', l('The uploaded package was not a valid theme.'), '</p>';

          // Delete the NOT theme (:P) and the package that was uploaded.
          recursive_unlink(themedir. '/'. $name);
          unlink(api()->context['filename']);
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

        recursive_unlink(themedir. '/'. $name);
        unlink(api()->context['filename']);
      }

      // Was the package extracted? If so, we can go on!
      if(!empty($package_extracted))
      {
        // Yes, yes I know! This is for checking the status of a plugin, but
        // it can do themes too! (Not like it knows better)
        // Why are we checking, you ask? Well, think about it! A theme is
        // also PHP, and in reality, it can do just as much as any plugin
        // can, meaning it can be as dangerous as any plugin.
        $status = plugin_check_status(api()->context['filename'], $reason);
        $theme_info = theme_load(themedir. '/'. $name);

        // Get the status message, and the color that the message should be.
        // But first, include a file.
        require_once(coredir. '/admin/admin_plugins_add.php');

        // Okay, now get the response!
        $response = admin_plugins_get_message($status, $theme_info['name'], $reason, true);

        // Is it okay? Can we continue without prompting?
        $install_proceed = isset($_GET['proceed']) || $status == 'approved';
        api()->run_hooks('plugin_install_proceed', array(&$install_proceed, $status, 'theme'));

        echo '
    <h3>', l('Verifying theme status'), '</h3>
    <p style="color: ', $response['color'], ';">', $response['message'], '</p>';

        // Was it deemed okay?
        if(!empty($install_proceed))
        {
          // Yup! Sure was!
          echo '
    <h3>', l('Finishing installation'), '</h3>
    <p>', l('The installation of the theme was completed successfully. <a href="%s">Back to theme management</a>.', baseurl. '/index.php?action=admin&sa=themes'), '</p>';

          // Delete the file, and we really are done!
          unlink(api()->context['filename']);
        }
        else
        {
          // Uh oh!
          // It was not safe, but if you still want to continue installing
          // it, be my guest! Just be sure you know what you're getting
          // yourself into, please!
          // We will delete the extracted theme, you know, just incase ;).
          recursive_unlink(themedir. '/'. $name);

          echo '
      <form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Are you sure you want to proceed with the installation of this theme?\r\nBe sure you trust the source of this plugin.'), '\');">
        <input type="submit" value="', l('Proceed'), '" />
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="themes" />
        <input type="hidden" name="install" value="', urlencode($_GET['install']), '" />
        <input type="hidden" name="sid" value="', member()->session_id(), '" />
        <input type="hidden" name="proceed" value="true" />
      </form>';
        }
      }
?>