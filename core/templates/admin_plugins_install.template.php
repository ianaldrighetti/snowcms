<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

      echo '
    <h1><img src="', theme()->url(), '/style/images/plugins_add-small.png" alt="" /> ', l('Installing plugin'), '</h1>
    <p>', l('Please wait while the plugin is being installed.'), '</p>

    <h3>', l('Extracting plugin'), '</h3>';

      /*
        Why in the world do I have it extracting the plugin THEN checking
        to see if it approved?

        Did I do it to validate whether or not it was a plugin before
        it warning that it is not an approved plugin when it isn't a plugin
        at all?

        Interesting... I will have to think about it.
      */

      // The Update class will be very useful!
      $update = api()->load_class('Update');

      // Get the name of the file.
      $name = explode('.', basename(api()->context['filename']), 2);

      // and just the first index.
      $name = $name[0];

      // We need to make the directory where the plugin will be extracted to.
      if(!file_exists(plugindir. '/'. $name) && !@mkdir(plugindir. '/'. $name, 0755, true))
      {
        echo '
    <p>', l('Failed to create the temporary plugin directory. Make sure the plugins directory is writable.'), '</p>';
      }
      // Try extracting the plugin.
      elseif($update->extract(api()->context['filename'], plugindir. '/'. $name))
      {
        // Just because the package could be extracted means nothing. We
        // will use the <plugin_load> function to check to see if it is a
        // valid plugin. If it isn't, the function will return false.
        if(plugin_load(plugindir. '/'. $name) === false)
        {
          echo '
    <p>', l('The uploaded package was not a valid plugin.'), '</p>';

          recursive_unlink(plugindir. '/'. $name);
          unlink(api()->context['filename']);
        }
        else
        {
          echo '
    <p>', l('The plugin was successfully extracted. Proceeding...'), '</p>';

          // The package was extracted successfully, so we can continue to
          // the next step.
          $package_extracted = true;
        }
      }
      else
      {
        // Hmmm, the Update class could not extract the package. Must not be
        // a ZIP, Tarball or Gzipped tarball. That sucks.
        echo '
    <p>', l('The uploaded package could not be extracted.'), '</p>';

        // Delete everything. It is no use to us now.
        recursive_unlink(plugindir. '/'. $name);
        unlink(api()->context['filename']);
      }

      // Was the package extracted? If so, we may continue.
      if(!empty($package_extracted))
      {
        // Get the current status of the plugin.
        $status = plugin_check_status(api()->context['filename'], $reason);
        $plugin_info = plugin_load(plugindir. '/'. $name);

        // Get the status message, and the color that the message should be.
        $response = admin_plugins_get_message($status, $plugin_info['name'], $reason);

        // Is the plugin safe to proceed?
        $install_proceed = isset($_GET['proceed']) || $status == 'approved';
        api()->run_hooks('plugin_install_proceed', array(&$install_proceed, $status, 'plugin'));

        echo '
  <h3>', l('Verifying plugin status'), '</h3>
  <p style="color: ', $response['color'], ';">', $response['message'], '</p>';

        // Is it safe to proceed?
        if(!empty($install_proceed))
        {
          // Time to make the finishing touches.
          echo '
  <h3>', l('Finishing installation'), '</h3>';

          // Add the plugin to the database.
          $result= db()->insert('ignore', '{db->prefix}plugins',
            array(
              'guid' => 'string-255', 'directory' => 'string-255',
            ),
            array(
              $plugin_info['guid'], $name,
            ), array('guid'), 'admin_plugins_add_query');

          // No rows affected? Then a plugin with the same guid already
          // exists.
          if($result->affected_rows() == 0)
          {
            // Delete it.
            recursive_unlink(plugindir. '/'. $name);

            echo '
  <p>', l('A plugin with that globally unique identifier (%s) is already installed.', htmlchars($plugin_info['guid']));
          }
          else
          {
            // Is there a file which you want run once installed?
            if(file_exists(plugindir. '/'. $name. '/install.php'))
            {
              require_once(plugindir. '/'. $name. '/install.php');
              unlink(plugindir. '/'. $name. '/install.php');
            }

            // If there is an update file, there is no need for it now.
            if(file_exists(plugindir. '/'. $name. '/update.php'))
            {
              unlink(plugindir. '/'. $name. '/update.php');
            }

            echo '
  <p>', l('The installation of the plugin was completed successfully. <a href="%s">Back to plugin management</a>.', baseurl. '/index.php?action=admin&sa=plugins_manage'), '</p>';
          }

          // We are done with the package.
          unlink(api()->context['filename']);
        }
        else
        {
          // No, it's not... But you can decide to continue anyways. Just be
          // sure you know what you are doing, and until you decide to
          // proceed, we will delete the extracted plugin, for safety
          // reasons.
          recursive_unlink(plugindir. '/'. $name);

          echo '
  <form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Are you sure you want to proceed with the installation of this plugin?\r\nBe sure you trust the source of this plugin.'), '\');">
    <input type="submit" value="', l('Proceed'), '" />
    <input type="hidden" name="action" value="admin" />
    <input type="hidden" name="sa" value="plugins_add" />
    <input type="hidden" name="install" value="', urlencode($_GET['install']), '" />
    <input type="hidden" name="sid" value="', member()->session_id(), '" />
    <input type="hidden" name="proceed" value="true" />
  </form>';
        }
      }
?>