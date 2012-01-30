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
	<h3><img src="', theme()->url(), '/style/images/update-small.png" alt="" /> ', l('Applying Update v%s', api()->context['version']), '</h3>
	<p>', l('Please wait while SnowCMS applies the system update. The website will be put into maintenance mode until the update process has completed.'), '</p>
	<p>', l('<strong>Warning:</strong> Do <em>not</em> close this window until the update process has completed, otherwise the update may not be properly applied resulting in possible damage to your website.'), '</p>

	<h3>', l('Update Progress'), '</h3>

	<div class="update-progress-bar-box" style="margin-top: 10px;">
		<div class="update-progress-bar-fill" id="update-overall" style="width: 0%;">
		</div>
		<div class="update-progress-message">
			<p id="update-overall-message">', l('Loading...'), '</p>
		</div>
		<div id="overall-underlay" class="update-progress-underlay" style="visibility: hidden;">
		</div>
	</div>
	<div class="update-progress-bar-box" style="margin-top: 15px; visibility: hidden;">
		<div class="update-progress-bar-fill" id="update-step" style="width: 0%;">
		</div>
		<div class="update-progress-message">
			<p id="update-step-message">0%</p>
		</div>
		<div class="update-progress-underlay" style="visibility: hidden;">
		</div>
	</div>
	<div id="update-box" style="display: none;">
	</div>';

/*			// Man, that Update class is awesome, isn't it?!

Plugin/Theme Compat Check | Downloading update | Verifying Update | Extracting File Information | Copying Files | Applying Update | Completed

			$update = api()->load_class('Update');

			// We will download the gzip package, if the system will allow it.
			$filename = api()->context['version']. '.tar'. (function_exists('gzdeflate') ? '.gz' : '');

			// This is where we will download the update from :-)
			$download_url = api()->apply_filters('admin_update_url', 'http://download.snowcms.com/updates/'. $filename);

			// Our checksum, as well. Want to be sure of the packages integrity.
			$checksum_download_url = api()->apply_filters('admin_update_checksum_url', 'http://download.snowcms.com/updates/'. $filename. '.chksum');

			// and now, to download the update.
			$package = $update->download($download_url, basedir. '/'. $filename, $checksum_download_url);

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
				if(!@recursive_unlink(basedir. '/update/') && is_dir(basedir. '/update/'))
				{
					echo '
		<p class="red">', l('Could not delete the update directory. Update process failed.'), '</p>';

					// Delete the package. Sorry.
					@unlink(basedir. '/'. $filename);
				}
				// Make a temporary directory.
				elseif(!@mkdir(basedir. '/update/', 0777, true))
				{
					echo '
		<p class="red">', l('Could not create the temporary update directory. Update process failed.'), '</p>';

					// Delete the package. Sorry.
					@unlink(basedir. '/'. $filename);
				}
				else
				{
					// Sure, we ought to extract it right from the base directory, but
					// just to be safe, we will try extracting it to another location
					// first.
					if(!$update->extract(basedir. '/'. $filename, basedir. '/update/', 'tar'))
					{
						echo '
		<p class="red">', l('The update package "%s" could not be extracted due to an unknown error. Update process failed.', $filename), '</p>';

						// Delete...
						@unlink(basedir. '/'. $filename);
					}
					else
					{
						echo '
		<p class="green">', l('The update package "%s" was successfully extracted. Proceeding...', $filename), '</p>

		<h3>Copying update files</h3>';

						// Time to do some copying.
						$copied_files = 0;
						foreach($update->get_listing(basedir. '/update/') as $updated_filename)
						{
							$update->copy(basedir. '/update/', basedir, $updated_filename);

							$copied_files++;
						}

						echo '
		<p class="green">', l('A total of %u update files were successfully copied. Proceeding...', $copied_files), '</p>

		<h3>Completing update</h3>';

		// Alright, we are DONE! Woo!
		$update->finish(basedir. '/update/', basedir);

		// We don't need this anymore.
		@unlink(basedir. '/'. $filename);

		echo '
		<p class="green">', l('You have been successfully updated to v%s. <a href="%s">Go to the control panel</a>.', settings()->get('version', 'string'), baseurl. '/index.php?action=admin'), '</p>';
					}
				}
			}*/
?>