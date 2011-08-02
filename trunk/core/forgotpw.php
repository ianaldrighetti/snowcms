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

// Title: Password Reset

if(!function_exists('forgotpw_view'))
{
	/*
		Function: forgotpw_view

		Lost your password? That's fine! You can request a new one through
		this. Of course, it will only work if email works :P

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function forgotpw_view()
	{
		api()->run_hooks('forgotpw_view');

		if(member()->is_logged())
		{
			redirect(baseurl. '/index.php');
		}

		// We just need a form for you to enter your username ;)
		$form = api()->load_class('Form');

		$form->add('forgotpw_form', array(
																	'action' => baseurl. '/index.php?action=forgotpw',
																	'method' => 'post',
																	'callback' => 'forgotpw_process',
																	'submit' => l('Reset password'),
																));

		$form->current('forgotpw_form');

		// Member name field, the only one!
		$form->add_input(array(
											 'name' => 'member_name',
											 'type' => 'string',
											 'label' => l('Username or email address'),
											 'subtext' => l('The username or email address you use to log in.'),
											 'callback' => create_function('$name, &$value, &$error', '
																			 if(empty($value))
																			 {
																				 $error = l(\'Please enter a username.\');
																				 return false;
																			 }

																			 return true;'),
											 'default_value' => !empty($_POST['member_name']) ? $_POST['member_name'] : '',
										 ));


		// Submitting the form? Process it...
		if(!empty($_POST['forgotpw_form']))
		{
			$form->process('forgotpw_form');
		}

		theme()->set_title(l('Request Password Reset'));

		api()->context['form'] = $form;

		theme()->render('forgotpw_view');
	}
}

if(!function_exists('forgotpw_process'))
{
	/*
		Function: forgotpw_process

		Sends the email containing the link to reset your password.

		Parameters:
			array $remind
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function forgotpw_process($remind, &$errors = array())
	{
		global $_POST;

		// We will need the name_to_id method in the Members class, along with others...
		$members = api()->load_class('Members');

		$member_id = $members->name_to_id($remind['member_name']);

		if(empty($member_id))
		{
			$errors[] = l('There is no account with that username or email address.');

			return false;
		}

		// Get some member information first.
		$members->load($member_id);
		$member_info = $members->get($member_id);

		// Have you requested a password reminder in the last hour? Slow down!!!
		if(isset($member_info['data']['pwreset_requested_time']) && ($member_info['data']['pwreset_requested_time'] + 3600) > time_utc())
		{
			$errors[] = l('Sorry, but you can only request a password reset every hour. Please try again in %u minutes.', ceil((3600 - (time_utc() - $member_info['data']['pwreset_requested_time'])) / 60));

			return false;
		}

		// Alrighty then, we need to generate a reminder key ;)
		$reset_key = sha1(time_utc(). $members->rand_str(mt_rand(32, 64)). (microtime(true) / mt_rand(4, 16)));

		$members->update($member_id, array(
																	 'data' => array(
																							 'pwreset_requested' => 1,
																							 'pwreset_requested_time' => time_utc(),
																							 'pwreset_requested_ip' => member()->ip(),
																							 'pwreset_requested_user_agent' => htmlchars($_SERVER['HTTP_USER_AGENT']),
																							 'reset_key' => $reset_key,
																						 ),
																 ));

		// Email time! :) and that's pretty much it!
		$mail = api()->load_class('Mail');
		$mail->set_html(true);
		$mail->send($member_info['email'], l('Password Reset Instructions for %s', settings()->get('site_name', 'string')), l("Hello, %s.<br /><br />You are receiving this email because someone has requested a password reset for your account on <a href=\"%s\">%s</a>. If you did not make this request to reset your password, <a href=\"%s\">log in</a> to your account or click the link below:<br /><a href=\"%s\">%s</a><br /><br />If you did request to have your password reset you can finish the process by clicking on the following link:<br /><a href=\"%s\">%s</a><br /><br />This password reset request will only remain valid for the next 24 hours.<br /><br />Regards,<br />%s<br /><a href=\"%s\">%s</a>", $member_info['username'], baseurl, settings()->get('site_name', 'string'), baseurl. '/index.php?action=login&amp;member_name='. urlencode($member_info['username']), baseurl. '/index.php?action=forgotpw2&amp;id='. $member_info['id']. '&amp;code='. $reset_key. '&amp;block=true', baseurl. '/index.php?action=forgotpw2&amp;id='. $member_info['id']. '&amp;code='. $reset_key. '&amp;block=true', baseurl. '/index.php?action=forgotpw2&amp;id='. $member_info['id']. '&amp;code='. $reset_key, baseurl. '/index.php?action=forgotpw2&amp;id='. $member_info['id']. '&amp;code='. $reset_key, settings()->get('site_name', 'string'), baseurl, baseurl));

		api()->add_filter('forgotpw_form_messages', create_function('$value', '
																								$value[] = l(\'An email containing instructions on how to reset your password have been sent.<br />This request will only be valid for the next 24 hours.\');

																								return $value;'));

		unset($_POST['member_name']);

		api()->run_hooks('post_reminder_process', array($member_info));

		return true;
	}
}

if(!function_exists('forgotpw_view2'))
{
	/*
		Function: forgotpw_view2

		Handles the actual changing of the password, as long as the information
		supplied is right... That is.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function forgotpw_view2()
	{
		api()->run_hook('forgotpw_view2');

		if(member()->is_logged())
		{
			redirect(baseurl. '/index.php');
		}

		// Do you have the data required?
		if(!empty($_REQUEST['id']) && !empty($_REQUEST['code']))
		{
			$members = api()->load_class('Members');

			// Does that member even exist? Let's check ;)
			$members->load($_REQUEST['id']);
			$member_info = $members->get($_REQUEST['id']);

			if(!empty($member_info))
			{
				// Well, seems alright... Now to see if the code has expired, that is, if a password request was made!
				if(isset($member_info['data']['reminder_requested_time']) && ($member_info['data']['reminder_requested_time'] + 86400) > time_utc() && $member_info['data']['reminder_requested'] == 1)
				{
					// Make the form to display the form to enter your new password :)
					$form = api()->load_class('Form');

					$form->add('reset_password_form', array(
																							'action' => baseurl. '/index.php?action=forgotpw2',
																							'method' => 'post',
																							'callback' => 'reminder_process2',
																							'submit' => l('Reset password'),
																						));

					$form->add_field('reset_password_form', 'new_password', array(
																																		'type' => 'password',
																																		'label' => l('Your new password:'),
																																		'function' => create_function('&$value, $form_name, &$error', '
																																							if($value != $_POST[\'verify_password\'])
																																							{
																																								$error = l(\'The passwords you supplied did not match.\');
																																								return false;
																																							}

																																							return true;'),
																																	));

					$form->add_field('reset_password_form', 'verify_password', array(
																																			 'type' => 'password',
																																			 'label' => l('Verify your password:'),
																																			 'subtext' => l('Type your password again, just for verification.'),
																																			 'save' => false,
																																		 ));

					$form->add_field('reset_password_form', 'id', array(
																													'type' => 'hidden',
																													'value' => $_REQUEST['id'],
																												));

					$form->add_field('reset_password_form', 'code', array(
																														'type' => 'hidden',
																														'value' => $_REQUEST['code'],
																													));

					// Process the form?
					if(!empty($_POST['reset_password_form']))
					{
						$form->process('reset_password_form');
					}

					theme()->set_title(l('Reset Password'));

					api()->context['form'] = $form;

					theme()->render('forgotpw_view2');
					exit;
				}
			}
		}

		// We will just say the information you submitted was wrong, but calling exit; before this will stop it ;)
		theme()->set_title(l('An Error Occurred'));

		api()->context['error_title'] = l('An Error Occurred');
		api()->context['error_message'] = l('Sorry, but your password change request could not be completed as the information you supplied was incorrect or the password request has expired.');

		theme()->render('error');
	}
}

if(!function_exists('forgotpw_process2'))
{
	/*
		Function: forgotpw_process2

		Actually changes the password of the specified user.

		Parameters:
			array $reset
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function forgotpw_process2($reset, &$errors = array())
	{
		api()->run_hook('forgotpw_process2');

		$members = api()->load_class('Members');

		// Load up the members information, we need it!
		$members->load($reset['id']);

		$member_info = $members->get($reset['id']);

		// Check to make sure their password is allowed.
		if(!$members->password_allowed($member_info['username'], $reset['new_password']))
		{
			$errors[] = l('Sorry, but the password you supplied is invalid.');
			return false;
		}

		// Seems to be all good. Update the members information.
		$members->update($reset['id'], array(
																		 'member_name' => $member_info['username'],
																		 'member_pass' => $reset['new_password'],
																		 'member_hash' => $members->rand_str(16),
																		 'data' => array(
																								 'reminder_requested' => 0,
																							 ),
																	 ));

		api()->run_hook('password_reset', array($member_info));

		// Alright, redirecting you to the login screen :)
		redirect(baseurl. '/index.php?action=login&member_name='. urlencode($member_info['username']));
	}
}
?>