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

/*
	Title: Core actions

	Function: init_core

	Registers actions which are default "features", such as logging in/out,
	registration, and other such operations. Plus a couple other things ;)

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.

	Note:
		All the actions registered in this function can be overloaded, simply
		by registering the actions before init_core is called, but also, all
		the functions which are used are overloadable as well.
*/
function init_core()
{
	// We have a couple default actions of our own :) (Remember, you can
	// register these actions before they are registered here! :) But also
	//all these functions are overloadable, so simply define them before
	// this too!!!).
	api()->add_event('action=activate', 'activate_view', l('Account Activation'), coredir. '/activate.php');
	api()->add_event('action=admin', 'admin_home', l('Control Panel'), coredir. '/admin/admin_home.php');
	api()->add_event('action=admin&sa=about', 'admin_about', l('About SnowCMS'), coredir. '/admin/admin_home.php');
	api()->add_event('action=admin&sa=update&apply=*', 'admin_update_apply', l('Applying Update'), coredir. '/admin/admin_update.php');
	api()->add_event('action=admin&sa=error_log', 'admin_error_log', l('Error Log'), coredir. '/admin/admin_error_log.php');
	api()->add_event('action=admin&sa=error_log&id=*', 'admin_error_log_view', create_function('$value', '
																																							 if((string)$value == (string)(int)$value)
																																							 {
																																								 return l(\'Viewing Error #%u\', (int)$value);
																																							 }
																																							 else
																																							 {
																																								 return l(\'Viewing Error\');
																																							 }'), coredir. '/admin/admin_error_log.php');
	api()->add_event('action=admin&sa=members_add', 'admin_members_add', l('Add a New Member'), coredir. '/admin/admin_members_add.php');
	api()->add_event('action=admin&sa=members_manage', 'admin_members_manage', l('Manage Members'), coredir. '/admin/admin_members_manage.php');
	api()->add_event('action=admin&sa=members_permissions', 'admin_members_manage_permissions', l('Manage Permissions'), coredir. '/admin/admin_members_permissions.php');
	api()->add_event('action=admin&sa=members_permissions&grp=*', 'admin_members_manage_group_permissions', create_function('$value', '
																																																						if($value != \'guest\')
																																																						{
																																																							$group_name = api()->return_group($value);
																																																						}
																																																						else
																																																						{
																																																							$group_name = l(\'Guest\');
																																																						}

																																																						return $group_name !== false ? l(\'Editing %s Permissions\', $group_name) : l(\'Editing Permissions\');'), coredir. '/admin/admin_members_permissions.php');
	api()->add_event('action=admin&sa=members_settings', 'admin_members_settings', create_function('$value', '
																																									 return array(
																																														array(
																																															\'query_string\' => \'sa=members_settings\',
																																															\'identifier\' => l(\'Manage Member Settings\'),
																																														),
																																														array(
																																															\'query_string\' => \'type=\'. htmlchars($_GET[\'type\']),
																																															\'identifier\' => $GLOBALS[\'settings_identifiers\'][$_GET[\'type\']],
																																														),
																																													);'), coredir. '/admin/admin_members_settings.php');
	api()->add_event('action=admin&sa=plugins_add', 'admin_plugins_add', l('Add a New Plugin'), coredir. '/admin/admin_plugins_add.php');
	api()->add_event('action=admin&sa=plugins_add&install=*', 'admin_plugins_install', l('Installing Plugin'), coredir. '/admin/admin_plugins_add.php');
	api()->add_event('action=admin&sa=plugins_manage', 'admin_plugins_manage', l('Manage Plugins'), coredir. '/admin/admin_plugins_manage.php');
	api()->add_event('action=admin&sa=plugins_manage&update=*', 'admin_plugins_update', l('Updating Plugin'), coredir. '/admin/admin_plugins_manage.php');
	api()->add_event('action=admin&sa=plugins_settings', 'admin_plugins_settings', l('Plugin Settings'), coredir. '/admin/admin_plugins_settings.php');
	api()->add_event('action=admin&sa=settings', 'admin_settings', create_function('$value', '
																																	 return array(
																																						array(
																																							\'query_string\' => \'sa=settings\',
																																							\'identifier\' => l(\'Manage Settings\'),
																																						),
																																						array(
																																							\'query_string\' => \'type=\'. htmlchars($_GET[\'type\']),
																																							\'identifier\' => $GLOBALS[\'settings_identifiers\'][$_GET[\'type\']],
																																						),
																																					);'), coredir. '/admin/admin_settings.php');
	api()->add_event('action=admin&sa=themes', 'admin_themes', create_function('$value', '
																															 return array(
																																				array(
																																					\'query_string\' => \'sa=themes&amp;section=\'. ($_GET[\'section\'] == \'install\' ? \'install\' : \'manage\'),
																																					\'identifier\' => ($_GET[\'section\'] == \'install\' ? l(\'Install Themes\') : l(\'Manage Themes\')),
																																				),
																																			);'), coredir. '/admin/admin_themes.php');
	api()->add_event('action=admin&sa=themes&install=*', 'admin_themes_install', l('Installing Theme'), coredir. '/admin/admin_themes.php');
	api()->add_event('action=admin&sa=themes&update=*', 'admin_themes_update', l('Updating Theme'), coredir. '/admin/admin_themes.php');
	api()->add_event('action=admin&sa=update', 'admin_update', l('System Update'), coredir. '/admin/admin_update.php');
	api()->add_event('action=checkcookie', 'checkcookie_verify', l('Log In'), coredir. '/checkcookie.php');
	api()->add_event('action=login', 'login_view', l('Log In'), coredir. '/login.php');
	api()->add_event('action=login2', 'login_view2', l('Log In'), coredir. '/login.php');
	api()->add_event('action=logout', 'logout_process', l('Log Out'), coredir. '/logout.php');
	api()->add_event('action=profile', 'profile_view', l('Viewing Profile'), coredir. '/profile.php');
	api()->add_event('action=profile&id=*', 'profile_view', l('Viewing Profile'), coredir. '/profile.php');
	api()->add_event('action=register', 'register_view', l('Register'), coredir. '/register.php');
	api()->add_event('action=register2', 'register_process', l('Register'), coredir. '/register.php');
	api()->add_event('action=resend', 'resend_view', l('Resend Activation Email'), coredir. '/resend.php');
	api()->add_event('action=resource', 'api_handle_resource', null);
	api()->add_event('action=forgotpw', 'forgotpw_view', l('Request a Password Reset'), coredir. '/forgotpw.php');
	api()->add_event('action=forgotpw2', 'forgotpw_view2', l('Reset Password'), coredir. '/forgotpw.php');
	api()->add_event('action=tasks', 'tasks_run', null, coredir. '/tasks.class.php');
	api()->add_event('action=popup', 'core_popup', l('Viewing Popup'));

	// Stop output buffering which was started in the <load_api> function.
	ob_end_clean();

	// Start output buffering.
	ob_start(api()->apply_filters('output_callback', null));

	// Check to see if admin_prepend needs to be called.
	reset($_GET);
	if(key($_GET) == 'action' && current($_GET) == 'admin')
	{
		require_once(coredir. '/admin.php');

		admin_prepend();
	}
}

if(!function_exists('core_popup'))
{
	/*
		Function: core_popup

		Displays the popup dialog content of the specified popup.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			To make a popup, simply apply a filter to popup_{ID_HERE}, for
			example if the popup identifier is timeformat, apply a filter to
			popup_timeformat. Be sure that if the popup information should only
			be available to a certain member group, check using the
			<Member::is_a> method before applying the filter.
	*/
	function core_popup()
	{
		global $func;

		if(empty($_GET['id']))
		{
			die(l('No popup identifier supplied.'));
		}

		// Collect the popup information.
		$popup = api()->apply_filters('popup_'. $_GET['id'], array('title' => '', 'content'));

		// We need title and content for the popup.
		if(empty($popup['title']) || empty($popup['content']))
		{
			die(l('Invalid popup identifier'));
		}

		// Now simply output the popup.
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>', $func['htmlspecialchars']($popup['title']), '</title>
	<style type="text/css">
		', api()->apply_filters('core_popup_css', 'body { background: #FFFFFF; font-family: Tahoma, Arial, sans-serif; font-size: 90%; }
h1 { font-size: 115%; color: #3465A7; margin-top: 15px; }'), '
	</style>
</head>
<body>
	<h1>', $func['htmlspecialchars']($popup['title']), '</h1>
	', $popup['content'], '
</body>
</html>';
	}
}
?>