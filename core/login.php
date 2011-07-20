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

// Title: Login Handler

if(!function_exists('login_view'))
{
	/*
		Function: login_view

		Simply displays the login form.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function login_view()
	{
		global $func;

		api()->run_hooks('login_view');

		// Are you already logged in? If you are, you don't need this!
		if(member()->is_logged())
		{
			redirect(baseurl. '/index.php');
		}

		theme()->set_title(l('Log In'));

		// Generate that lovely login form.
		login_generate_form();
		api()->context['form'] = api()->load_class('Form');

		theme()->render('login_view');
	}
}

if(!function_exists('login_generate_form'))
{
	/*
		Function: login_generate_form

		Generates the login form.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function login_generate_form()
	{
		static $generated = false;

		// Don't generate the form twice.
		if(!empty($generated))
		{
			return;
		}

		// Add the core part first.
		$form = api()->load_class('Form');
		$form->add('login_form', array(
															 'callback' => 'login_process',
															 'action' => api()->apply_filters('login_action_url', baseurl. '/index.php?action=login2'),
															 'method' => 'post',
															 'submit' => l('Log In'),
														 ));

		// Let's make this a bit easier.
		$form->current('login_form');

		// Now the rest of the stuff.
		$form->add_input(array(
											 'name' => 'member_name',
											 'type' => 'string',
											 'label' => l('Username:'),
											 'callback' => create_function('$name, &$value, &$error', '
																			 if(empty($value))
																			 {
																				 api()->run_hooks(\'login_process_empty_username\');

																				 $error = l(\'Please enter a username.\');
																				 return false;
																			 }

																			 return true;'),
											 'default_value' => !empty($_REQUEST['member_name']) ? $_REQUEST['member_name'] : '',
										 ));

		$form->add_input(array(
											 'name' => 'member_pass',
											 'type' => 'password',
										 	 'label' => l('Password:'),
											 'callback' => create_function('$name, &$value, &$error', '
																			 if(empty($value) && empty($_POST[\'secured_password\']))
																			 {
																				 api()->run_hooks(\'login_process_empty_password\');

																				 $error = l(\'Please enter a password.\');
																				 return false;
																			 }

																			 return true;'),
										 ));

		$form->add_input(array(
											 'name' => 'session_length',
											 'type' => 'select',
											 'label' => l('Stay logged in for'),
											 'options' => array(
																			0 => l('This session'),
																			3600 => l('An hour'),
																			86400 => l('A day'),
																			604800 => l('A week'),
																			2419200 => l('A month'),
																			31536000 => l('A year'),
																			-1 => l('Forever'),
																		),
											 'default_value' => !empty($_REQUEST['session_length']) ? (int)$_REQUEST['session_length'] : -1,
										 ));

		$form->add_input(array(
											 'name' => 'redir_to',
											 'type' => 'hidden',
											 'label' => true,
											 'default_value' => !empty($_REQUEST['redir_to']) ? $_REQUEST['redir_to'] : '',
										 ));

		// It has been generated, so don't generate it again!
		$generated = true;
	}
}

if(!function_exists('login_view2'))
{
	/*
		Function: login_view2

		Handles the submission of the login form.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function login_view2()
	{
		// Are you logged in? You Silly Pants you!
		if(member()->is_logged())
		{
			redirect(baseurl. '/index.php');
		}

		// Generate the login form :)
		login_generate_form();
		$form = api()->load_class('Form');

		// Process the form, and we are good to go!
		// Unless it failed, of course.
		$member_id = $form->process('login_form');
		if(empty($member_id))
		{
			// No indexing, robots!
			theme()->add_meta(array('name' => 'robots', 'content' => 'noindex'));

			// Let login_view() handle the displaying of errors.
			login_view();
			exit;
		}

		// Redirect to check that login cookie! :)
		redirect(baseurl. '/index.php?action=checkcookie&id='. $member_id. (!empty($_POST['redir_to']) ? '&redir_to='. urlencode($_POST['redir_to']) : ''));
	}
}

if(!function_exists('login_process'))
{
	/*
		Function: login_process

		Processes the data submitted by the login form.
		This is the callback for the login form.

		Parameters:
			array $login - An array containing login information.
			array &$errors - An array containing any errors which occurred
											 while processing the login data.

		Returns:
			int - Returns the member id on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function login_process($login, &$errors = array())
	{
		global $func;

		api()->run_hooks('login_process');

		// So you got the stuff, but is it the right stuff? Let's see!
		$result = db()->query('
			SELECT
				member_id, member_pass, member_hash, member_activated
			FROM {db->prefix}members
			WHERE '. (db()->case_sensitive ? 'LOWER(member_name) = LOWER({string:member_name})' : 'member_name = {string:member_name}'). '
			LIMIT 1',
			array(
				'member_name' => $login['member_name'],
			), 'login_process_query');

		// Did we get anything? If we got 0 rows, this member doesn't even exist!
		if($result->num_rows() == 0)
		{
			// Wrong username or password :P
			$errors[] = l('Invalid username or password supplied.');

			return false;
		}

		// Now let's check that password!
		$row = $result->fetch_assoc();

		// Your account not yet activated? No logging in then!
		if($row['member_activated'] != 1)
		{
			// So, yeah!
			if($row['member_activated'] == 11)
			{
				$errors[] = l('Your account has been disabled until you verify your new email address.');
			}
			else
			{
				$errors[] = l('Your account has not yet been activated.'. (settings()->get('registration_type', 'int') == 2 ? ' An administrator should approve your account shortly.' : (settings()->get('registration_type', 'int') == 3 ? ' Check your email for further instructions.' : '')));
			}

			return false;
		}

		// No success as of yet.
		$login_success = false;

		// Maybe it is just plain text, pssssh!
		if(sha1($func['strtolower']($login['member_name']). $login['member_pass']) == $row['member_pass'])
		{
			$login_success = true;
		}
		// You want to check something?
		else
		{
			api()->run_hooks('login_process_check_custom', array(&$login_success, $login, $row, &$errors));
		}

		// Failed to login? Sucks to be you.
		if(empty($login_success))
		{
			api()->run_hooks('login_process_failed', array($login));

			$errors[] = l('Invalid username or password supplied.');

			return false;
		}

		// So how long did you want to be remembered?
		// Forever?
		if(isset($login['session_length']) && $login['session_length'] == -1)
		{
			$cookie_expires = time_utc() + 315360000;
		}
		// A more specific time?
		elseif(!empty($login['session_length']))
		{
			$cookie_expires = time_utc() + $login['session_length'];
		}
		// Just until you close your browser?
		else
		{
			$cookie_expires = 0;
		}

		// Set the cookie, a chocolate chip cookie :) No one likes oatmeal cookies, they are nasty...
		// Oh yeah, and give the password a touch of salt, :D Take that HTTP sniffers!
		setcookie(api()->apply_filters('login_cookie_name', cookiename), api()->apply_filters('login_cookie_value', $row['member_id']. '|'. sha1($row['member_pass']. $row['member_hash'])), $cookie_expires);

		$_SESSION['member_id'] = (int)$row['member_id'];
		$_SESSION['member_pass'] = sha1($row['member_pass']. $row['member_hash']);

		api()->run_hooks('login_success', array($row));

		return (int)$row['member_id'];
	}
}
?>