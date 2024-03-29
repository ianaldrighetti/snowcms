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

// Title: Logout Handler

if(!function_exists('logout_process'))
{
	/*
		Function: logout_process

		Logs you out of your account, as long as your session id is supplied.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function logout_process()
	{
		// Not even logged in? Then you can't log out!
		if(member()->is_guest())
		{
			redirect(baseurl());
		}

		// Check that session identifier, make sure it is yours.
		if(empty($_GET['sc']) || $_GET['sc'] != member()->session_id())
		{
			api()->run_hooks('logout_failed');

			theme()->set_title(l('An error has occurred'));
			theme()->add_meta(array('name' => 'robots', 'content' => 'noindex'));

			api()->context['error_title'] = l('Logging out failed');
			api()->context['error_message'] = l('Sorry, but the supplied session identifider was invalid, so your request to be logged out was denied. Please try again.');

			theme()->render('error');
			exit;
		}

		// Remove the cookie and session information.
		setcookie(api()->apply_filters('login_cookie_name', cookiename), '', time_utc() - 604800);

		// This token is now done with!
		db()->query('
			DELETE FROM {db->prefix}auth_tokens
			WHERE member_id = {int:member_id} AND token_id = {string:auth_token}
			LIMIT 1',
			array(
				'member_id' => member()->id(),
				'auth_token' => $_SESSION['auth_token'],
			));

		// Destroy their session.
		session_destroy();

		api()->run_hooks('logout_success');

		// Let's go home...
		redirect(baseurl());
	}
}
?>