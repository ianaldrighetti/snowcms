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

// Title: System Update

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
		api()->run_hooks('admin_update');

		// Can you update the system?
		if(!member()->can('update_system'))
		{
			// That's what I thought!
			admin_access_denied();
		}

		// Do you want to force an update check? Well, if you say so.
		if(isset($_REQUEST['check']))
		{
			admin_update_check(true);

			redirect('index.php?action=admin&sa=update');
		}

		// Get the information.
		$latest_version = settings()->get('system_latest_version', null, settings()->get('version', 'string'));
		$latest_info = settings()->get('system_latest_info', 'array', array('up-to-date' => null));

		// Was there an update?
		if($latest_info['up-to-date'] === null)
		{
			// Couldn't get anything!
			$latest_info['header'] = l('Connection Failed');
			$latest_info['message'] = l('Sorry, but it appears that the update server is down. Please try again later.');
		}
		elseif($latest_info['up-to-date'] === false)
		{
			$latest_info['header'] = l('There is an Update Available');
		}
		else
		{
			$latest_info['header'] = l('No Update Available');
			$latest_info['message'] = l('There is no update available for your version of SnowCMS.');
		}

		// Do they want to start the update process? That's great! But why don't
		// we make sure that there is a need to do so.
		if(isset($_GET['start']) && $latest_version !== false && compare_versions($latest_version, settings()->get('version', 'string'), '>'))
		{
			verify_request('get');

			// During the update process the entire site will be, technically,
			// taken offline. This means that the the session of the user updating
			// the SnowCMS system could time out, which would be bad. So in order
			// to get passed that hurdle we will assign an update key, and anyone
			// with that key can apply the update.
			// We will generate the key using the rand_str method within the
			// Members class.
			$members = api()->load_class('Members');

			$update_key = $members->rand_str(mt_rand(64, 128));

			// We will need to save this key to a safe, but accessible, location.
			$fp = fopen(basedir. '/update-key.php', 'w');

			if(!empty($fp))
			{
				// No one should be able to see this.
				fwrite($fp, '<?php die; ?>'. $update_key);
				fclose($fp);

				// Now redirect!
				redirect(baseurl. '/index.php?action=admin&sa=update&apply='. urlencode($update_key));
			}
			else
			{
				api()->context['start_update_failure'] = true;
			}
		}

		admin_current_area('system_update');

		theme()->set_title(l('Update'));

		api()->context['current_version'] = settings()->get('version', 'string');
		api()->context['latest_version'] = $latest_version;
		api()->context['latest_info'] = $latest_info;
		api()->context['update_available'] = $latest_version !== false ? compare_versions($latest_version, settings()->get('version', 'string'), '>') : false;

		theme()->render('admin_update');
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
		$apply_key = $_GET['apply'];

		api()->run_hooks('admin_update_system');

		if(!member()->can('update_system'))
		{
			admin_access_denied();
		}

		admin_current_area('system_update');

		// Why don't we see if the update keys match.
		$update_key = false;
		if(file_exists(basedir. '/update-key.php'))
		{
			$fp = fopen(basedir. '/update-key.php', 'r');

			// Seek passed all the junk (the security junk, that is ;-)).
			fseek($fp, 13);

			// Then read what is left of the file, which is our update key.
			$update_key = fread($fp, filesize(basedir. '/update-key.php') - 13);

			fclose($fp);
		}

		// Let's make sure that they started the update process in the first
		// place.
		if($update_key === false || strlen($apply_key) == 0 || $apply_key != $update_key)
		{
			theme()->set_title(l('An error has occurred'));

			api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/update-small.png" alt="" /> '. l('Invalid Update Key');
			api()->context['error_message'] = l('Sorry, but your update key was invalid. <a href="%s">Back to system update</a>.', baseurl. '/index.php?action=admin&amp;sa=update');

			theme()->render('error');
		}
		elseif(($version = admin_latest_version()) !== false && compare_versions(settings()->get('version', 'string', null), $version, '>'))
		{
			theme()->set_title(l('An error has occurred'));

			api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/update-small.png" alt="" /> '. l('No update required');
			api()->context['error_message'] = l('No update needs to be applied at this time. <a href="%s">Back to system update</a>.', baseurl. '/index.php?action=admin&amp;sa=update');

			theme()->render('error');
		}
		else
		{
			theme()->set_title(l('Applying Update'));

			// We have a few resources we will require while the update process
			// chugs along.
			theme()->add_link(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => theme()->url(). '/style/update.css'));
			theme()->add_js_file(array('src' => theme()->url(). '/js/update.js'));
			theme()->add_js_var('update_key', $update_key);
			theme()->add_js_var('l', array(
																 'compatibility' => l('Checking Plugin and Theme Compatibility'),
																 'compat_error_header' => l('Compatibility Issues Found'),
																 'compat_error_message' => l('There are some plugins or themes which may cause compatibility issues when the system has been updated to v%s. If there are any updates available for the components it is recommended you install those updates first then come back and try installing the system update again. Otherwise, you can simply ignore these warnings and continue anyways.', $version),
																 'compat_plugin_header' => l('Incompatible Plugins'),
																 'compat_theme_header' => l('Incompatible Theme'),
																 'available' => l('available'),
																 'no_update_available' => l('No update available'),
																 'check_compat_again' => l('Recheck Compatibility'),
																 'compat_ignore_warnings' => l('Continue with Update &raquo;'),
																 'compat_ignore_confirm' => l('Are you sure you want to continue with the update even though some plugins or themes may not be compatible?'. "\r\n". 'Proceed at your own risk.'),
																 'downloading' => l('Downloading Update'),
																 'download_error_header' => l('Download Failed'),
																 'download_error_message' => l('The update file could not be downloaded from the SnowCMS update service. Please make sure that your system has either fsockopen or cURL enabled, and make sure that the SnowCMS website is up. You can always try again later.'),
																 'cancelling' => l('Please wait... Cancelling.'),
																 'download_again' => l('Download Again &raquo;'),
																 'cancel_update' => l('Cancel Update'),
																 'verifying' => l('Verifying Update Integrity'),
																 'verify_error_header' => l('Update Integrity Issue'),
																 'verify_error_1' => l('The checksum could not be downloaded from the SnowCMS update service.'),
																 'verify_error_2' => l('The update package downloaded from the SnowCMS update service appears to be corrupt.'),
																 'verify_error_3' => l('The update package is in an unknown format and cannot be extracted.'),
																 'verify_again' => l('Verify Again &raquo;'),
																 'extracting' => l('Extracting File Information from Package'),
																 'extract_error_header' => l('Extraction Issue'),
																 'extract_error_1' => l('The system does not have write permission to the base, <em>/core/</em>, or <em>/themes/</em> directory. Please make sure that the permissions allow the PHP process to have write access and try again.'),
																 'extract_error_2' => l('A temporary index file for extraction information could not be created, please make sure that the base directory is writable.'),
																 'extract_error_3' => l('An error occurred which prevented the system from reading the update package&#039;s file information.'),
																 'extract_again' => l('Try Again &raquo;'),
															 ));

			api()->context['version'] = $version;

			theme()->render('admin_update_apply');
		}
	}
}

/*
	Function: admin_update_check

	Checks for any available update for SnowCMS.

	Parameters:
		bool $force_check - Forces the function to check for updates, regardless
												of how long ago updates were checked for.

	Returns:
		void - Nothing is returned by this function.

	Note:
		This function is automatically called by the <Task> system if enabled.
		If the task system is disabled the user must manually check for updates
		under the System Update section of the control panel.
*/
function admin_update_check($force_check = false)
{
	if(!empty($force_check) || (settings()->get('system_last_update_check', 'int', 0) + settings()->get('system_update_interval', 'int', 3600)) < time_utc())
	{
		// Let's get the latest version of SnowCMS.
		$latest_version = admin_latest_version();

		if(!empty($latest_version))
		{
			$http = api()->load_class('HTTP');

			// Now the information about the current version you are running.
			$latest_info = $http->request(api()->apply_filters('admin_update_version_url', 'http://download.snowcms.com/updates/latest-version.php'), array('version' => settings()->get('version', 'string'), 'news' => 'true'));

			// Alright, separate the headers from the message.
			list($tmp, $message) = explode("\r\n\r\n", $latest_info);

			// Now separate the headers from one another.
			$headers = array();
			foreach(explode("\r\n", $tmp) as $header)
			{
				list($key, $value) = explode(':', $header);

				$headers[strtolower($key)] = trim($value);
			}

			// Trim up the message, just in case.
			$message = trim($message);

			settings()->set('system_last_update_check', time_utc());
			settings()->set('system_latest_info', array(
																							'version' => isset($headers['version']) ? $headers['version'] : false,
																							'up-to-date' => $headers['up-to-date'] == 'null' ? null : ($headers['up-to-date'] == 'true' ? true : false),
																							'message' => $message,
																						));
		}

		settings()->set('system_latest_version', $latest_version);
	}
}

/*
	Function: admin_latest_version

	Fetches the latest version of SnowCMS available, for this system to update
	to, that is.

	Parameters:
		none

	Returns:
		string - Returns the latest version of SnowCMS available, or false if
						 download.snowcms.com could not be accessed.
*/
function admin_latest_version()
{
	// I love you, HTTP class!
	$http = api()->load_class('HTTP');

	// Just make the request, and return the data we fetched.
	return $http->request(api()->apply_filters('admin_update_version_url', 'http://download.snowcms.com/updates/latest-version.php'), array('version' => settings()->get('version', 'string')));
}
?>