<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
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

			echo '
	<h1><img src="', theme()->url(), '/plugins_manage-small.png" alt="" /> ', l('Updating plugin'), '</h1>
	<p>', l('Please wait while we are updating the %s plugin.', api()->context['plugin_info']['name']), '</p>

	<h3>', l('Downloading update'), '</h3>';

			// The HTTP class is always useful.
			$http = api()->load_class('HTTP');

			// Hmm, make a POST request to the plugins GUID, with the version we
			// want... Be sure to store it in a file for later use!
			if(!$http->request('http://'. api()->context['plugin_info']['guid'], array('download' => 1, 'version' => api()->context['version']), 0, api()->context['plugin_info']['path']. '/update-package'))
			{
				echo '
	<p class="red">', l('The update package version "%s" was not found. Update process failed.', api()->context['version']), '</p>';
			}
			else
			{
				echo '
	<p class="green">', l('The update package was successfully downloaded. Proceeding...'), '</p>

	<h3>', l('Verifying plugin status'), '</h3>';

				// We need a file which has the <plugin_check_status> function,
				// which we really need.
				require_once(coredir. '/admin/admin_plugins_add.php');

				// So get the status, please.
				$status = plugin_check_status(api()->context['plugin_info']['path']. '/update-package', $reason);

				// This next function interprets the status message into something
				// slightly more useful, to real people, that is.
				$response = admin_plugins_get_message($status, api()->context['plugin_info']['name'], $reason);

				// So, shall we proceed?
				$update_proceed = isset($_GET['proceed']) || $status == 'approved';
				api()->run_hooks('plugin_install_proceed', array(&$update_proceed, $status));

				echo '
	<p style="color: ', $response['color'], '">', $response['message'], '</p>';

				// Is it okay to proceed?
				if(!empty($update_proceed))
				{
					// Time to extract the plugin!
					echo '
	<h3>', l('Extracting plugin'), '</h3>';

					$update = api()->load_class('Update');

					// We need to make the temporary directory.
					if(!file_exists(api()->context['plugin_info']['path']. '/update~/') && !@mkdir(api()->context['plugin_info']['path']. '/update~', 0755, true))
					{
						echo '
	<p class="red">', l('Failed to create the temporary update folder. Make sure the plugins directory is writable.'), '</p>';
					}
					// If we made that directory successfully, extract it to that
					// temporary location.
					elseif($update->extract(api()->context['plugin_info']['path']. '/update-package', api()->context['plugin_info']['path']. '/update~'))
					{
						echo '
	<p class="green">', l('The update package was successfully extracted. Proceeding...'), '</p>';

						// No longer need the package containing the update...
						unlink(api()->context['plugin_info']['path']. '/update-package');

						// Time to move on to the next step, then.
						// Which is just copying the files from the update~ directory to
						// the root directory of the plugin. If you are wondering why we
						// extracted the files to update~ instead of the root directory
						// in the first place, well, it is done just to make sure the
						// package could actually be extracted.
						$files = scandir(api()->context['plugin_info']['path']. '/update~');

						foreach($files as $filename)
						{
							if($filename == '.' || $filename == '..')
							{
								continue;
							}

							// Just rename them!
							rename(api()->context['plugin_info']['path']. '/update~/'. $filename, api()->context['plugin_info']['path']. '/'. $filename);
						}

						// Now delete the update~ directory.
						recursive_unlink(api()->context['plugin_info']['path']. '/update~/');

						// Get the new plugin information. Though it certainly might not
						// have changed. Just incase!
						$new_plugin_info = plugin_load(api()->context['plugin_info']['path']);

						// We will do this just incase the GUID changed, and clear any
						// possible runtime error, oh, and set the available update
						// column to empty.
						db()->query('
							UPDATE {db->prefix}plugins
							SET guid = {string:updated_guid}, runtime_error = 0, available_update = \'\'
							WHERE guid = {string:current_guid}
							LIMIT 1',
							array(
								'updated_guid' => $new_plugin_info['guid'],
								'current_guid' => api()->context['plugin_info']['guid'],
							), 'update_plugin_guid');

						// Is there an installation file?
						if(file_exists(api()->context['plugin_info']['path']. '/install.php'))
						{
							// Set the current plugin version.
							$current_plugin_version = api()->context['plugin_info']['version'];

							require_once(api()->context['plugin_info']['path']. '/install.php');

							// And delete it.
							unlink(api()->context['plugin_info']['path']. '/install.php');
						}

						// Sweet! The update is complete!
						echo '
	<h3>', l('Update finished'), '</h3>
	<p>', l('You have successfully updated the plugin "%s" to version %s. <a href="%s">Back to plugin management</a>.', htmlchars($new_plugin_info['name']), htmlchars($new_plugin_info['version']), baseurl. '/index.php?action=admin&sa=plugins_manage'), '</p>';
					}
					else
					{
						echo '
	<p class="red">', l('Failed to extract the update package.'), '</p>';

						recursive_unlink(api()->context['plugin_info']['path']. '/update~/');
						unlink(api()->context['plugin_info']['path']. '/update-package');
					}
				}
				else
				{
					// Seems like it isn't!
					unlink(api()->context['plugin_info']['path']. '/update-package');

					echo '
	<form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Are you sure you want to proceed with the installation of this plugin?\r\nBe sure you trust the source of this plugin.'), '\');">
		<input type="submit" value="', l('Proceed'), '" />
		<input type="hidden" name="action" value="admin" />
		<input type="hidden" name="sa" value="plugins_manage" />
		<input type="hidden" name="update" value="', htmlchars(api()->context['guid']), '" />
		<input type="hidden" name="version" value="', urlencode($_GET['version']), '" />
		<input type="hidden" name="sid" value="', member()->session_id(), '" />
		<input type="hidden" name="proceed" value="true" />
	</form>';
				}
			}
?>