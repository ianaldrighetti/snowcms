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
			redirect(baseurl. '/index.php');
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
																'action' => baseurl. '/index.php?action=resend',
																'method' => 'post',
																'submit' => l('Resend activation'),
																'callback' => 'resend_process',
															));

		$form->add_field('resend_form', 'member_name', array(
																										 'type' => 'string',
																										 'label' => l('Username:'),
																										 'subtext' => l('The name you used to register your account.'),
																										 'function' => create_function('&$value, $form_name, &$error', '
																																		 if(empty($value))
																																		 {
																																			 $error = l(\'Please enter your username.\');
																																			 return false;
																																		 }

																																		 return true;'),
																										 'value' => !empty($_REQUEST['member_name']) ? $_REQUEST['member_name'] : '',
																									 ));

		// So, you submitting it?
		if(!empty($_POST['resend_form']))
		{
			$form->process('resend_form');
		}

		theme()->set_title(l('Resend Activation Email'));

		api()->context['form'] = $form;

		theme()->render('resend_form');
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
			$errors[] = l('The name you entered does not exist.');
			return false;
		}

		// Load up the member information.
		$members->load($member_id);
		$member_info = $members->get($member_id);

		// Is the account already activated? No go!
		if($member_info['is_activated'] == 1)
		{
			$errors[] = l('The account is already activated. If this is your account, you can proceed to <a href="%s">login</a>.', baseurl. '/index.php?action=login');
			return false;
		}

		// Well, let's regenerate your activation code.
		$member_acode = sha1($members->rand_str(mt_rand(30, 40)));

		$members->update($member_id, array(
																	 'member_activated' => 0,
																	 'member_acode' => $member_acode,
																 ));

		// Resend it! Woo!
		if(!function_exists('register_send_email'))
		{
			require_once(coredir. '/register.php');
		}

		register_send_email($member_id);

		api()->add_filter('resend_message', create_function('$value', '
																				 return l(\'Your activation email has been resent successfully.\');'));

		return true;
	}
}
?>