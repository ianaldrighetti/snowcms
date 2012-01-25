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
		$version = $_GET['apply'];

		api()->run_hooks('admin_update_system');

		if(!member()->can('update_system'))
		{
			admin_access_denied();
		}

		$version = basename($version);

		admin_current_area('system_update');

		// Verify your session id.
		verify_request('get');

		// !!! TODO: Make sure $version is a number.

		// Do we not need to apply an update?
		if(compare_versions(settings()->get('version', 'string', null), $version) > -1)
		{
			theme()->set_title(l('An error has occurred'));

			theme()->header();

			echo '
	<h1><img src="', theme()->url(), '/update-small.png" alt="" /> ', l('No update required'), '</h1>
	<p>', l('No update needs to be applied at this time. <a href="%s">Back to system update</a>.', baseurl. '/index.php?action=admin&amp;sa=update'), '</p>';

			api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/update-small.png" alt="" /> '. l('No update required');
			api()->context['error_message'] = l('No update needs to be applied at this time. <a href="%s">Back to system update</a>.', baseurl. '/index.php?action=admin&amp;sa=update');

			theme()->render('error');
		}
		else
		{
			theme()->set_title(l('Applying Update'));

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
		$http = api()->load_class('HTTP');

		// Let's get the latest version of SnowCMS.
		$latest_version = $http->request(api()->apply_filters('admin_update_version_url', 'http://download.snowcms.com/updates/latest-version.php'), array('version' => settings()->get('version', 'string')));

		if(!empty($latest_version))
		{
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
?>