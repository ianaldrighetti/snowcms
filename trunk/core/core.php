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
	reset($_GET);

	// Why don't we speed up this process a bit? Sounds like a good idea, no?
	if(key($_GET) == 'action')
	{
		// I mean, we don't need to load all of these if we don't need to, right?
		if(current($_GET) == 'activate')
		{
			api()->add_event('action=activate', 'activate_view', coredir. '/activate.php');
		}
		elseif(current($_GET) == 'admin')
		{
			api()->add_event('action=admin', 'admin_home', coredir. '/admin/admin_home.php');
			api()->add_event('action=admin&sa=about', 'admin_about', coredir. '/admin/admin_home.php');
			api()->add_event('action=admin&sa=update&apply=*', 'admin_update_apply', coredir. '/admin/admin_update.php');
			api()->add_event('action=admin&sa=error_log', 'admin_error_log', coredir. '/admin/admin_error_log.php');
			api()->add_event('action=admin&sa=error_log&id=*', 'admin_error_log_view', coredir. '/admin/admin_error_log.php');
			api()->add_event('action=admin&sa=members_add', 'admin_members_add', coredir. '/admin/admin_members_add.php');
			api()->add_event('action=admin&sa=members_manage', 'admin_members_manage', coredir. '/admin/admin_members_manage.php');
			api()->add_event('action=admin&sa=members_permissions', 'admin_members_manage_permissions', coredir. '/admin/admin_members_permissions.php');
			api()->add_event('action=admin&sa=members_permissions&grp=*', 'admin_members_manage_group_permissions', coredir. '/admin/admin_members_permissions.php');
			api()->add_event('action=admin&sa=members_settings', 'admin_members_settings', coredir. '/admin/admin_members_settings.php');
			api()->add_event('action=admin&sa=plugins_add', 'admin_plugins_add', coredir. '/admin/admin_plugins_add.php');
			api()->add_event('action=admin&sa=plugins_add&install=*', 'admin_plugins_install', coredir. '/admin/admin_plugins_add.php');
			api()->add_event('action=admin&sa=plugins_manage', 'admin_plugins_manage', coredir. '/admin/admin_plugins_manage.php');
			api()->add_event('action=admin&sa=plugins_manage&update=*', 'admin_plugins_update', coredir. '/admin/admin_plugins_manage.php');
			api()->add_event('action=admin&sa=plugins_settings', 'admin_plugins_settings', coredir. '/admin/admin_plugins_settings.php');
			api()->add_event('action=admin&sa=settings', 'admin_settings', coredir. '/admin/admin_settings.php');
			api()->add_event('action=admin&sa=themes&section=manage', 'admin_themes_manage', coredir. '/admin/admin_themes_manage.php');
			api()->add_event('action=admin&sa=themes&section=install', 'admin_themes_install', coredir. '/admin/admin_themes_install.php');
			api()->add_event('action=admin&sa=themes&section=widgets', 'admin_themes_widgets', coredir. '/admin/admin_themes_widgets.php');
			api()->add_event('action=admin&sa=themes&install=*', 'admin_themes_perform_install', coredir. '/admin/admin_themes_install.php');
			api()->add_event('action=admin&sa=themes&update=*', 'admin_themes_update', coredir. '/admin/admin_themes_manage.php');
			api()->add_event('action=admin&sa=update', 'admin_update', coredir. '/admin/admin_update.php');

			// Yup, we will want to call on the admin_prepend function.
			$start_admin_prepend = true;
		}
		elseif(current($_GET) == 'checkcookie')
		{
			api()->add_event('action=checkcookie', 'checkcookie_verify', coredir. '/checkcookie.php');
		}
		elseif(substr(current($_GET), 0, 3) == 'log')
		{
			api()->add_event('action=login', 'login_view', coredir. '/login.php');
			api()->add_event('action=login2', 'login_view2', coredir. '/login.php');
			api()->add_event('action=logout', 'logout_process', coredir. '/logout.php');
		}
		elseif(current($_GET) == 'profile')
		{
			api()->add_event('action=profile', 'profile_view', coredir. '/profile.php');
			api()->add_event('action=profile&id=*', 'profile_view', coredir. '/profile.php');
		}
		elseif(substr(current($_GET), 0, 2) == 're')
		{
			api()->add_event('action=register', 'register_view', coredir. '/register.php');
			api()->add_event('action=register2', 'register_process', coredir. '/register.php');
			api()->add_event('action=resend', 'resend_view', coredir. '/resend.php');
			api()->add_event('action=resource', 'api_handle_resource');
		}
		elseif(substr(current($_GET), 0, 6) == 'forgot')
		{
			api()->add_event('action=forgotpw', 'forgotpw_view', coredir. '/forgotpw.php');
			api()->add_event('action=forgotpw2', 'forgotpw_view2', coredir. '/forgotpw.php');
		}
		elseif(current($_GET) == 'tasks')
		{
			api()->add_event('action=tasks', 'tasks_run', coredir. '/tasks.class.php');
		}
		elseif(current($_GET) == 'popup')
		{
			api()->add_event('action=popup', 'core_popup');
		}
		elseif(current($_GET) == 'verify')
		{
			api()->add_event('action=verify', 'verify_email', coredir. '/verify.php');
		}
	}

	// Stop output buffering which was started in the <load_api> function.
	ob_end_clean();

	// Start output buffering.
	ob_start(api()->apply_filters('output_callback', null));

	// Do we need to load up admin_prepend?
	if(!empty($start_admin_prepend))
	{
		require_once(coredir. '/admin.php');

		admin_prepend();
	}

	api()->add_menu_category('main', 'Main Menu');

	// Let's add a few default links to allow some basic navigation throughout
	// the website.
	api()->add_menu_item('main', array(
																 'href' => baseurl(),
																 'content' => l('Home'),
															 ));

	// We won't need to show logged in members links to registering, now do
	// we?
	if(member()->is_guest())
	{
		api()->add_menu_item('main', array(
																	 'href' => baseurl('index.php?action=register'),
																	 'content' => l('Register'),
																 ));

		api()->add_menu_item('main', array(
																	 'href' => baseurl('index.php?action=login'),
																	 'content' => l('Log In'),
																 ));
	}
	else
	{
		api()->add_menu_item('main', array(
																	 'href' => baseurl('index.php?action=profile'),
																	 'content' => l('My Profile'),
																 ));

		if(member()->can('access_admin_cp'))
		{
			api()->add_menu_item('main', array(
																		 'href' => baseurl('index.php?action=admin'),
																		 'content' => l('Control Panel'),
																	 ));
		}

		api()->add_menu_item('main', array(
																	 'href' => baseurl('index.php?action=logout&amp;sc='. member()->session_id()),
																	 'content' => l('Log Out'),
																 ));
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
		$popup = api()->apply_filters('popup_'. $_GET['id'], array(
																													 'title' => false,
																													 'content' => false,
																												 ));

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