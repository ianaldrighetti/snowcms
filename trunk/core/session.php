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

// Title: Session

// Another pluggable function, sessions!!! Woo!
if(!function_exists('init_session'))
{
	/*
		Function: init_session

		Begins the session for the current, well, session of browsing.
		If session.auto_start is enabled, this function closes that session
		to open up another one as there are some possible modifications
		done to how sessions are handled.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This is a pluggable function.
	*/
	function init_session()
	{
		// Are sessions set to automatically start upon load? Turn it off :P
		if(@ini_get('session.auto_start') == 1)
		{
			session_write_close();
		}

		// Custom session save path..? Make sure it is readable and writeable.
		if(strlen(settings()->get('session.save_path', 'string', '')) > 0 && is_writeable(settings()->get('session.save_path', 'string')) && is_readable(settings()->get('session.save_path', 'string')))
		{
			@ini_set('session.save_path', settings()->get('session.save_path', 'string'));
		}

		// Use cookies, mmm...
		@ini_set('session.use_cookies', 1);

		// Increase the GC probability a bit.
		@ini_set('session.gc_divisor', settings()->get('session.gc_divisor', 'int', 0) > 0 ? settings()->get('session.gc_divisor', 'int') : 200);

		// Extend the lifetime of the sessions.
		@ini_set('session.gc_maxlifetime', settings()->get('session.gc_maxlifetime', 'int', 0) > 0 ? settings()->get('session.gc_maxlifetime', 'int') : 3600);

		// Along with the cookie itself.
		@ini_set('session.cookie_lifetime', time_utc() + 432000);

		// And use ONLY cookies! Otherwise people can do that ?PHPSESSID attack crap...
		@ini_set('session.use_only_cookies', 1);

		// Only allow the cookie to be accessed via HTTP, not something like JavaScript.
		// Though, not all browsers currently support it.
		@ini_set('session.cookie_httponly', 1);

		// Maybe you have something to add, or change?
		api()->run_hooks('init_session');

		// Now start the session.
		session_start();
	}
}

if(!function_exists('verify_request'))
{
	/*
		Function: verify_request

		Even though the <Form> class uses <Token> class which helps stop CSRF,
		sometimes using the <Token> class can be a bit much for what is needed
		to be done. By calling on this function the current members session id
		will be compared to that of the supplied. The supplied session id will
		be either in the GET, POST or both as the variable sid. If they do not
		match, then an error message will be shown, and any further execution
		halted.

		Parameters:
			string $where - Where the sid variable should be looked for in, either
											get for $_GET['sid'], post for $_POST['sid'] or request
											for $_REQUEST['sid']. Defaults to request.

		Returns:
			bool - Returns true on success.

		Note:
			This function is overloadable.
	*/
	function verify_request($where = 'request')
	{
		$where = strtolower($where);

		// Make sure it is a known where.
		if(!in_array($where, array('get', 'post', 'request')))
		{
			return false;
		}

		// Now fetch the session id, or at least, try.
		if($where == 'get')
		{
			$sid = !empty($_GET['sid']) ? $_GET['sid'] : '';
		}
		elseif($where == 'post')
		{
			$sid = !empty($_POST['sid']) ? $_POST['sid'] : '';
		}
		else
		{
			$sid = !empty($_REQUEST['sid']) ? $_REQUEST['sid'] : '';
		}

		// So do they match?
		if(member()->session_id() != $sid)
		{
			// We have a function to do this already ;-)
			member_access_denied(l('Session Verification Error'), l('Sorry, but session verification failed. Please <a href="javascript:history.go(-1);">go back</a> and try again.'));
		}

		return true;
	}
}


if(!function_exists('member_access_denied'))
{
	/*
		Function: member_access_denied

		Shows an error screen denying the member access to the page they requested.

		Parameters:
			string $title - The title of the page, defaults to Access denied
			string $message - The error message to display, defaults to "Sorry,
												but you are not allowed to access the page you have requested."

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function member_access_denied($title = null, $message = null)
	{
		@ob_clean();

		if(empty($title))
		{
			$title = l('Access denied');
		}

		if(empty($message))
		{
			$message = l('Sorry, but you are not allowed to access the page you have requested.');
		}

		theme()->set_title($title);

		theme()->add_meta(array('name' => 'robots', 'content' => 'noindex'));

		api()->context['error_title'] = $title;
		api()->context['error_message'] = $message;

		theme()->render('error');

		// Exit!
		exit;
	}
}
?>