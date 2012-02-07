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

// Title: Resend activation email

if(!function_exists('resend_view'))
{
	/*
		Function: resend_view

		Handles the resending of activation emails, you know, just incase ;)

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function resend_view()
	{
		api()->run_hooks('resend_view');

		if(member()->is_logged())
		{
			redirect(baseurl());
		}
		elseif(settings()->get('registration_type', 'int', 1) != 3)
		{
			theme()->set_title(l('An Error Occurred'));

			theme()->header();

			api()->context['error_title'] = l('Registration Error');
			api()->context['error_message'] = l('It appears that the current type of registration isn\'t email activation, so you cannot resend your activation email.');

			theme()->render('error');
			exit;
		}

		$form = api()->load_class('Form');

		$form->add('resend_form', array(
																'action' => baseurl('index.php?action=resend'),
																'method' => 'post',
																'submit' => l('Resend activation'),
																'callback' => 'resend_process',
															));

		$form->current('resend_form');

		$form->add_input(array(
											 'name' => 'member_name',
											 'type' => 'string',
											 'label' => l('Username'),
											 'subtext' => l('The name you used to register your account.'),
											 'callback' => create_function('$name, &$value, &$error', '
																			 if(empty($value))
																			 {
																				 $error = l(\'Please enter a username or email address.\');

																				 return false;
																			 }

																			 return true;'),
											 'default_value' => !empty($_REQUEST['member_name']) ? $_REQUEST['member_name'] : '',
										 ));

		// So, you submitting it?
		if(!empty($_POST['resend_form']))
		{
			$form->process('resend_form');
		}

		theme()->set_title(l('Request a New Activation Email'));

		api()->context['form'] = $form;

		theme()->render('resend_view');
	}
}

if(!function_exists('resend_process'))
{
	/*
		Function: resend_process

		Processes the form for resending your activation email.

		Parameters:
			array $resend - The array containing the form data.
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function resend_process($resend, &$errors = array())
	{
		$members = api()->load_class('Members');

		// Let's see if the member even exists... ;)
		$member_id = $members->name_to_id($resend['member_name']);

		if(empty($member_id))
		{
			$errors[] = l('There is no account with that username or email address.');
			return false;
		}

		// Load up the member information.
		$members->load($member_id);
		$member_info = $members->get($member_id);

		// Is the account already activated? No go!
		if($member_info['is_activated'] != 0)
		{
			$errors[] = l('That account is already activated and you can <a href="%s">log in</a> if it is your account.', baseurl('index.php?action=login'));

			return false;
		}
		elseif(isset($member_info['data']['activation_last_resent']) && ($member_info['data']['activation_last_resent'] + 900) > time_utc())
		{
			$errors[] = l('You can only request a new activation email every 15 minutes. Please try again in %u minutes.', ceil((900 - (time_utc() - $member_info['data']['activation_last_resent'])) / 60));

			return false;
		}

		// Well, let's regenerate your activation code.
		$member_acode = sha1($members->rand_str(mt_rand(30, 40)));

		$members->update($member_id, array(
																	 'member_acode' => $member_acode,
																	 'data' => array(
																							 'activation_last_resent' => time_utc(),
																						 ),
																 ));

		// Resend it! Woo!
		if(!function_exists('register_send_email'))
		{
			require_once(coredir. '/register.php');
		}

		if(!register_send_email($member_id))
		{
			// We should tell the administrator that the message couldn't be sent.
			trigger_error(l('An error occurred while trying to resend the user their activation email. This could indicate that the SMTP settings are incorrect or the server does not have the mail() function enabled.'), E_USER_WARNING);

			// Then tell the user! D:
			$errors[] = l('An error occurred while trying to resend your activation email. Please contact the administrator if this issue continues.');

			return false;
		}

		api()->add_filter('resend_form_messages', create_function('$value', '
																								$value[] = l(\'A new activation email has been sent. The email should be received shortly.\');

																								return $value;'));

		return true;
	}
}
?>