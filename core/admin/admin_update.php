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

if(!defined('INSNOW'))
{
	die('Nice try...');
}

// Title: Control Panel - System Update

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

		// When was the last time we checked for system updates? (Or do you want us to check anyways?)
		if((settings()->get('system_last_update_check', 'int', 0) + settings()->get('system_update_interval', 'int', 3600)) < time() || isset($_REQUEST['check']))
		{
			$http = api()->load_class('HTTP');

			$latest_version = $http->request(api()->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'));
			$latest_info = @unserialize($http->request(api()->apply_filters('admin_update_version_url', 'http://download.snowcms.com/news/v2.x-line/latest.php'). '?version='. settings()->get('version', 'string')));

			settings()->set('system_last_update_check', time(), 'int');
			settings()->set('system_latest_version', serialize($latest_version), 'string');
			settings()->set('system_latest_info', serialize($latest_info), 'string');

			redirect('index.php?action=admin&sa=update');
		}
		else
		{
			$latest_version = @unserialize(settings()->get('system_latest_version', 'string', 'b:0;'));
			$latest_info = @unserialize(settings()->get('system_latest_info', 'string', 'a:0:{}'));
		}

		// Is an update required?
		$is_update_required = $latest_version !== false ? compare_versions(settings()->get('version', 'string'), $latest_version) == -1 : false;
		$latest_info = array_merge(array('header' => '', 'text' => ''), $latest_info);

		admin_current_area('system_update');

		theme()->set_title(l('Update'));

		api()->context['latest_version'] = $latest_version;
		api()->context['latest_info'] = $latest_info;
		api()->context['is_update_required'] = $is_update_required;

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
?>