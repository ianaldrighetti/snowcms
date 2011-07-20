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

// Title: Cookie verification

if(!function_exists('checkcookie_verify'))
{
	/*
		Function: checkcookie_verify

		Verifies that your login cookie was actually saved by the browser.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function checkcookie_verify()
	{
		api()->run_hooks('checkcookie_verify');

		// This is a pretty simple check...
		$cookie = isset($_COOKIE[api()->apply_filters('login_cookie_name', cookiename)]) ? $_COOKIE[api()->apply_filters('login_cookie_name', cookiename)] : '';
		list($member_id) = explode('|', $cookie);

		if(api()->apply_filters('checkcookie_check', empty($cookie) || empty($_GET['id']) || $_GET['id'] != $member_id))
		{
			// The cookie didn't save :(
			api()->add_filter('login_message', create_function('$value', '
				return l(\'It appears your login cookie couldn\\\'t be saved. Please be sure you have cookies enabled in your browser settings and try again.\');'));

			api()->run_hooks('checkcookie_failed');

			// Login view function exist?
			$login_view_func = api()->apply_filters('login_view_function', 'login_view');
			if(!function_exists($login_view_func))
			{
				require_once(api()->apply_filters('login_view_path', coredir. '/login.php'));
			}

			theme()->add_meta(array('name' => 'robots', 'content' => 'noindex'));

			$login_view_func();
			exit;
		}

		api()->run_hooks('checkcookie_success');

		// Seemed to have worked, so let's go home! Unless you had somewhere
		// else in mind.
		if(!empty($_GET['redir_to']))
		{
			// Let's just check and see.
			$redir_to = $_GET['redir_to'];

			// It shouldn't have any slashes in it.
			if(strpos($redir_to, '/') === false)
			{
				redirect(baseurl. '/index.php?'. $redir_to);
			}
		}

		// Guess not.
		redirect(baseurl. '/index.php');
	}
}
?>