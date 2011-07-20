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

// Title: Registration Handler

if(!function_exists('register_view'))
{
	/*
		Function: register_view

		Displays the registration form, that is if registration is enabled.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function register_view()
	{
		api()->run_hooks('register_view');

		// Are you logged in? You don't need to register an account because you obviously have one!
		if(member()->is_logged())
		{
			redirect(baseurl. '/index.php');
		}

		// Is registration enabled?
		if(settings()->get('registration_type', 'int', 1) == 0)
		{
			theme()->set_title(l('Registration Disabled'));
			theme()->add_meta(array('name' => 'robots', 'content' => 'noindex'));

			api()->run_hooks('registration_disabled');

			api()->context['error_title'] = l('Registration Disabled');
			api()->context['error_message'] = l('We apologize for the inconvience, but registration is currently not open to the public. Please check back at a later time.')

			theme()->render('error');
			exit;
		}

		// Generate that form, pretty please!
		register_generate_form();

		theme()->set_title(l('Register'));

		api()->context['form'] = api()->load_class('Form');

		theme()->render('register_view');
	}
}

if(!function_exists('register_process'))
{
	/*
		Function: register_process

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function register_process()
	{
		api()->run_hooks('register_process');

		// Already logged in? You don't need another account! ;)
		if(member()->is_logged())
		{
			redirect(baseurl. '/index.php');
		}

		// Registration disabled? We will let register_view() handle that.
		if(settings()->get('registration_type', 'int', 1) == 0)
		{
			register_view();
			exit;
		}

		register_generate_form();
		$form = api()->load_class('Form');

		$member_id = $form->process('registration_form');

		// Processing failed? Then show the errors!
		if($member_id === false)
		{
			// The register_view function will handle it nicely:
			register_view();
			exit;
		}
		else
		{
			// Now just output a message ;)
			theme()->set_title(l('Registration Complete'));

			// Let's get some member information, shall we?
			$members = api()->load_class('Members');
			$members->load($member_id);

			api()->context['member_info'] = $members->get($member_id);

			theme()->render('register_process');
		}
	}
}

if(!function_exists('register_generate_form'))
{
	/*
		Function: register_generate_form

		Generates the registration form, for of course, registration!

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function register_generate_form()
	{
		static $generated = false;

		// Already been done? Don't need to do it again.
		if(!empty($generated))
		{
			return;
		}

		// Let's get that form going!
		$form = api()->load_class('Form');
		$form->add('registration_form', array(
																			'callback' => 'register_member',
																			'action' => api()->apply_filters('register_action_url', baseurl. '/index.php?action=register2'),
																			'submit' => l('Register account'),
																		));

		// Add the fields we need you to fill out.
		// Your requested member name, you know? That thing you use to login.
		$form->add_field('registration_form', 'member_name', array(
																													 'type' => 'string',
																													 'label' => l('Choose username:'),
																													 'subtext' => l('Used to log in to your account.'),
																													 'length' => array(
																																				 'min' => 1,
																																				 'max' => 80,
																																			 ),
																													 'function' => create_function('$value, $form_name, &$error', '
																														 $members = api()->load_class(\'Members\');

																														 if($members->name_allowed($value))
																															 return true;
																														 else
																														 {
																															 $error = l(\'The requested username is already in use or not allowed.\');
																															 return false;
																														 }'),
																													 'value' => !empty($_REQUEST['member_name']) ? $_REQUEST['member_name'] : '',
																												 ));

		// Your password.
		$form->add_field('registration_form', 'member_pass', array(
																													 'type' => 'password',
																													 'label' => l('Password:'),
																													 'subtext' => l('Be sure to use a strong password!'),
																													 'function' => create_function('$value, $form_name, &$error', '
																														 // Passwords don\'t match? That isn\'t right.
																														 if(empty($_POST[\'pass_verification\']) || $_POST[\'pass_verification\'] != $value)
																														 {
																															 $error = l(\'Your passwords do not match.\');
																															 return false;
																														 }

																														 $members = api()->load_class(\'Members\');

																														 if($members->password_allowed($_POST[\'member_name\'], $value))
																															 return true;
																														 else
																														 {
																															 $security = settings()->get(\'password_security\', \'int\');

																															 if($security == 1)
																																 $error = l(\'Your password must be at least 3 characters long.\');
																															 elseif($security == 2)
																																 $error = l(\'Your password must be at least 4 characters long and cannot contain your username.\');
																															 else
																																 $error = l(\'Your password must be at least 5 characters long, cannot contain your username and contain at least 1 number.\');

																															 return false;
																														 }')));

		// Just to make sure you didn't type your password wrong or anything ;)
		$form->add_field('registration_form', 'pass_verification', array(
																																'type' => 'password',
																																'label' => l('Verify password:'),
																																'subtext' => l('Please enter your password here again.'),
																																'save' => false,
																															));
		// Email address is important too!
		$form->add_field('registration_form', 'member_email', array(
																														'type' => 'string',
																														'label' => l('Email:'),
																														'subtext' => l('Please enter a valid email address.'),
																														'length' => array(
																																					'max' => 255,
																																				),
																														'function' => create_function('$value, $form_name, &$error', '
																															$members = api()->load_class(\'Members\');

																															if($members->email_allowed($value))
																																return true;
																															else
																															{
																																$error = l(\'The supplied email address is already in use or not allowed.\');
																																return false;
																															}'),
																														'value' => !empty($_REQUEST['member_email']) ? $_REQUEST['member_email'] : '',
																													));

		// Add the agreement here... Eventually ;)

		// Now it has been generated, as once is enough.
		$generated = true;
	}
}

if(!function_exists('register_member'))
{
	/*
		Function: register_member

		Processes the registration form information.

		Parameters:
			array $options - Receives the array containing all the new members
											 options and what not, from <Form>.
			array &$errors

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function register_member($options, &$errors = array())
	{
		$handled = null;
		api()->run_hooks('register_member', array(&$handled, $options, &$errors));

		if($handled !== null)
		{
			return $handled;
		}

		// Sweet! Registration time!
		// So we will need the Members class, super useful!
		$members = api()->load_class('Members');

		// A couple things, possibly.
		$add_options = array(
										 'member_groups' => explode(',', settings()->get('default_member_groups', 'string', 'member')),
										 'member_activated' => settings()->get('registration_type', 'int', 1) == 1,
									 );

		// Hmm, is it administrative approval?
		if(settings()->get('registration_type', 'int', 0) == 2)
		{
			// Set their activation code to admin_approval.
			$add_options['member_acode'] = 'admin_approval';
		}

		// Got something to add, perhaps?
		api()->run_hooks('register_member_add_options', array(&$add_options, &$options, &$errors));

		// Just incase if any hooks added any errors.
		if(count($errors) > 0)
		{
			return false;
		}

		// Now add that member, or at least, try.
		$member_id = $members->add($options['member_name'], $options['member_pass'], $options['member_email'], $add_options);

		// Was it a success? Do we need to send an activation email?
		if($member_id > 0 && settings()->get('registration_type', 'int', 1) == 3)
		{
			register_send_email($member_id);
		}

		// Now return the member id.
		return $member_id;
	}
}

if(!function_exists('register_send_email'))
{
	/*
		Function: register_send_email

		Sends the activation email to the specified address with the
		specified information.

		Parameters:
			string $member_id - The member's ID of who to send the email to.

		Returns:
			bool - Returns true if the email was successfully sent, false if not.

		Note:
			Just because the email was successfully sent, does not mean that the
			email was successfully received by the address specified.
	*/
	function register_send_email($member_id)
	{
		// Great! You get the wonders of email activation :P
		// The activation code and what not has already been set, we just need to get it out.
		$members = api()->load_class('Members');
		$members->load($member_id);
		$member_info = $members->get($member_id);

		if(empty($member_info))
		{
			return false;
		}

		// We need the Mail class to do this, of course!
		$mail = api()->load_class('Mail');

		$handled = null;
		api()->run_hooks('register_member_send_email', array(&$handled, $mail, $member_info));

		if($handled === null)
		{
			$handled = $mail->send($member_info['email'], api()->apply_filters('register_member_email_subject', l('Account activation for %s', settings()->get('site_name', 'string'))), api()->apply_filters('register_member_email_body', l("Hello there %s, this email comes from %s.\r\n\r\nYou are receiving this email because someone has attempted to register an account on our site with your email address. If this was not you who did this, please disregard this email, no further actions are required.\r\n\r\nIf you did, however, request this account, please activate your account by clicking on the link below:\r\n%s/index.php?action=activate&id=%s&code=%s\r\n\r\nThank you for registering! Hope to see you around!", $member_info['name'], baseurl, baseurl, $member_info['id'], $member_info['acode'])), api()->apply_filters('register_member_alt_email', ''), api()->apply_filters('register_member_email_options', array()));
		}

		return !empty($handled);
	}
}

if(!function_exists('register_send_welcome_email'))
{
	/*
		Function: register_send_welcome_email

		Sends a welcome email to the specified members.

		Parameters:
			mixed $member_id - Either a single member id, or an array containing
												 multiple member id's.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	function register_send_welcome_email($member_id)
	{
		if(!is_array($member_id))
		{
			$member_id = array($member_id);
		}

		foreach($member_id as $_id)
		{
			if(typecast()->typeof($member_id) != 'int')
			{
				// It appears that this is not an integer, so we will decline your
				// request. Sorry!
				return false;
			}
		}

		// Load up their information, namely their email addresses.
		$members = api()->load_class('Members');
		$members->load($member_id);
		$members_info = $members->get($member_id);

		// We will need that Mail class, as you know, we need to send some email ;)
		$mail = api()->load_class('Mail');

		$handled = null;
		api()->run_hooks('register_welcome_member_send_email', array(&$handled, $mail, $member_info));

		if($handled !== null)
		{
			return !empty($handled);
		}

		// Now time to send those emails!
		if(count($members_info))
		{
			// Now dispatch them emails!
			foreach($members_info as $member_info)
			{
				$mail->send($member_info['email'], api()->apply_filters('register_welcome_member_email_subject', l('Welcome to %s', settings()->get('site_name', 'string'))), api()->apply_filters('register_welcome_member_email_body', l("Hello there %s, this email comes from %s.\r\n\r\nYou are receiving this email because your account on %s is now activated, and you can now log in to your account. If you never registered an account on %s, please disregard this email.\r\n\r\nIf you did, however, you can now log in to your account at %s/index.php?action=login&member_name=%s\r\n\r\nThank you for registering! Hope to see you around!", $member_info['name'], baseurl, settings()->get('site_name', 'string'), settings()->get('site_name', 'string'), baseurl, urlencode($member_info['username']))), api()->apply_filters('register_welcome_member_alt_email', ''), api()->apply_filters('register_welcome_member_email_options', array()));
			}

			return true;
		}
		else
		{
			// Or not.
			return false;
		}
	}
}
?>