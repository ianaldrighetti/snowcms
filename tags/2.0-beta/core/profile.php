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

// Title: Profile

if(!function_exists('profile_view'))
{
	/*
		Function: profile_view

		Handles the viewing of members profiles, but also editing of their
		profiles as well.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function profile_view()
	{
		global $_GET;

		api()->run_hooks('profile_view');

		// Just in case.
		$_GET['id'] = isset($_GET['id']) && (int)$_GET['id'] > 0 ? (int)$_GET['id'] : 0;

		// We need to load a members information... So yeah.
		if(empty($_GET['id']))
		{
			// Use your member id.
			$_GET['id'] = member()->id();
		}

		// Can you even view other profiles (If it is someone elses.)
		if(!member()->can('view_other_profile') && $_GET['id'] != member()->id())
		{
			// Nope, you cannot.
			member_access_denied(null, l('Sorry, but you do not have permission to view other members profiles.'));
		}
		else
		{
			// Does the member even exist..? If not, then we will show a denied page.
			$members = api()->load_class('Members');
			$members->load($_GET['id']);

			$member_info = $members->get($_GET['id']);

			// So did it load?
			if($member_info == false)
			{
				// No it did not. So the member doesn't exist :-(
				member_access_denied(l('Member doesn\'t exist'), l('Sorry, but the member you are requesting does not exist.'));
			}
		}

		// Perhaps you want to edit the member?
		if(isset($_GET['edit']))
		{
			// Let's do it! (Permission checking is done in the profile_edit function.
			profile_edit($member_info);
			exit;
		}

		$handled = false;
		api()->run_hooks('profile_view_display', array(&$handled, &$member_info));

		// It looks like we will have to handle the display of a users data
		// ourselves. What fun!
		if(empty($handled))
		{
			theme()->set_title(l('Viewing profile of %s', $member_info['name']));

			// You will be able to modify this later :P
			$display_data = array(
												array(
													'label' => l('Username:'),
													'value' => $member_info['username'],
													'show' => strtolower($member_info['username']) != strtolower($member_info['name']) && (member()->id() == $member_info['id'] || member()->can('edit_other_profiles') || member()->can('manage_members')),
												),
												array(
													'label' => l('Name:'),
													'value' => $member_info['name'],
													'show' => true,
												),
												array(
													'label' => l('Member groups:'),
													'value' => implode(', ', $member_info['groups']),
													'show' => true,
												),
												array(
													'label' => l('Email:'),
													'value' => '<a href="mailto:'. $member_info['email']. '" title="'. $member_info['email']. '">'. $member_info['email']. '</a>',
													'show' => $_GET['id'] == member()->id() || member()->can('edit_other_profiles'),
												),
												array(
													'is_hr' => true,
												),
												array(
													'label' => l('Last active:'),
													'title' => l('The last time this member browsed the site'),
													'value' => timeformat($member_info['last_active']),
													'show' => true,
												),
												array(
													'label' => l('Date registered:'),
													'title' => l('When the member was originally created'),
													'value' => timeformat($member_info['registered']),
													'show' => true,
												),
												array(
													'is_hr' => true,
												),
												array(
													'label' => l('IP:'),
													'title' => l('Last known IP address'),
													'value' => $member_info['ip'],
													'show' => $_GET['id'] == member()->id() || member()->can('edit_other_profiles'),
												),
												array(
													'label' => l('Activated?'),
													'title' => l('Whether or not the account is activated'),
													'value' => $member_info['is_activated'] ? l('Yes') : l('No'),
													'show' => member()->can('edit_other_profiles'),
												),
											);

			api()->context['display_data'] = api()->apply_filters('profile_view_data', $display_data);
			api()->context['member_info'] = $member_info;

			theme()->render('profile_view');
		}
	}
}

if(!function_exists('profile_edit'))
{
	/*
		Function: profile_edit

		Provides an interface for editing a members information.

		Parameters:
			mixed $member_info - This is either an array containing all the members information,
													 including their id, or an integer which is the id of the member
													 you want to edit.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function profile_edit($member_info)
	{
		api()->run_hooks('profile_edit', array(&$member_info));

		// We will need the Members class either way.
		$members = api()->load_class('Members');

		// So an array?
		if(is_array($member_info))
		{
			// Load their information (we will stil use the stuff you gave us, but
			// this will check to make sure the member actually exists).
			$members->load((int)$member_info['id']);
			$member_id = (int)$member_info['id'];
		}
		else
		{
			$members->load((int)$member_info);
			$member_id = (int)$member_info;
		}

		// So can they edit anothers profile or manage another's profile?
		if($member_id != member()->id() && !member()->can('edit_other_profiles') && !member()->can('manage_members'))
		{
			member_access_denied(l('Access denied'), l('Sorry, but you do not have permission to edit other members profiles.'));
		}
		// Do they exist?
		elseif($members->get($member_id) === false)
		{
			member_access_denied(l('Member doesn\'t exist'), l('Sorry, but the member you are requesting does not exist.'));
		}

		// Generate the form, now!
		profile_edit_generate_form(is_array($member_info) ? $member_info : $members->get($member_id));

		$form = api()->load_class('Form');

		if(!empty($_POST['member_edit_'. $member_id]))
		{
			$form->process('member_edit_'. $member_id);
		}

		api()->context['form'] = $form;
		api()->context['member_info'] = $member_info;

		// Maybe there is an update message to display?
		if(isset($_GET['message']) && ($_GET['message'] == 1 || $_GET['message'] == 2))
		{
			// We may have multiple messages.
			api()->context['messages'] = array(l('Your profile has been updated successfully.'));

			// If there is a message code of 2 that means they need to verify
			// their new email address.
			if($_GET['message'] == 2)
			{
				api()->context['messages'][] = l('Before your email address change goes into effect you must verify it by clicking the link sent to that new email address.');
			}
		}

		theme()->render('profile_edit');
	}
}

if(!function_exists('profile_edit_generate_form'))
{
	/*
		Function: profile_edit_generate_form

		Generates the form used to modify the specified member.

		Parameters:
			array $member_info - An array containing all the members information, including
													 their id.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function profile_edit_generate_form($member_info)
	{
		// If there is no ID supplied within the member info, then there is
		// nothing we can do!
		if(empty($member_info['id']))
		{
			return;
		}

		$GLOBALS['editing_member_id'] = $member_info['id'];
		$GLOBALS['member_info'] = $member_info;

		$form = api()->load_class('Form');

		$form->add('member_edit_'. $member_info['id'], array(
																								'callback' => 'profile_edit_handle',
																								'method' => 'post',
																								'submit' => l('Update profile'),
																								'id' => 'member_edit',
																							));

		$form->current('member_edit_'. $member_info['id']);

		// The display name can be changed, which is just what it sounds like,
		// a name displayed on their account and in other locations around the
		// website. It defaults to the user name they signed up with, but can
		// be changed... However, changing their display name does not change
		// the name they log in with.
		$form->add_input(array(
											 'name' => 'display_name',
											 'type' => 'string',
											 'label' => l('Display name:'),
											 'subtext' => l('This does not change the name you log in with, simply the name that is displayed in your profile.'),
											 'length' => array(
																		 'min' => settings()->get('members_min_name_length', 'int', 3),
																		 'max' => settings()->get('members_max_name_length', 'int', 80),
																	 ),
											 'callback' => create_function('$name, $value, &$error', '
																			 $members = api()->load_class(\'Members\');

																			 // Is the name in use? Not by this member, though...
																			 if(!$members->name_allowed($value, $GLOBALS[\'editing_member_id\']))
																			 {
																				 $error = l(\'That display name is in use by another member or not allowed.\');

																				 return false;
																			 }

																			 return true;'),
											 'default_value' => $member_info['name'],
										 ));

		// They can change their email address too, which can be used to log in
		// to their account with. There is a setting in the members settings
		// section of the control panel which allows the administrators to
		// require that users (that cannot manage members or are administrators)
		// verify their email address before the change takes effect -- disabled
		// by default, but it is highly encouraged this option be enabled!
		$form->add_input(array(
											 'name' => 'member_email',
											 'type' => 'string',
											 'label' => l('Email address:'),
											 'subtext' => settings()->get('require_email_verification', 'bool', false) ? l('If you change your email address you will be required to verify it through a link emailed to the new address.') : '',
											 'callback' => create_function('$name, $value, &$error', '
																			 $members = api()->load_class(\'Members\');

																			 // Make sure the email address isn\'t in use or banned or anything.
																			 if(!$members->email_allowed($value, $GLOBALS[\'editing_member_id\']))
																			 {
																				 $error = l(\'That email address is in use by another member or not allowed.\');

																				 return false;
																			 }

																			 return true;'),
											 'default_value' => $member_info['email'],
										 ));

		// A user can change their password as often as they want, which is
		// certainly recommended! We will want to make sure that they did make
		// any typos by asking for their new password twice.
		$form->add_input(array(
											 'name' => 'member_pass',
											 'type' => 'password',
											 'label' => l('Password:'),
											 'subtext' => l('Leave blank if you don\'t want to change your password.'),
											 'function' => create_function('$name, $value, &$error', '
																			 if(!empty($value) && (empty($_POST[\'verify_pass\']) || $_POST[\'verify_pass\'] != $value))
																			 {
																				 $error = l(\'The supplied passwords don\\\'t match.\');

																				 return false;
																			 }
																			 else
																			 {
																				 $members = api()->load_class(\'Members\');

																				 if($members->password_allowed($GLOBALS[\'member_info\'][\'username\'], $value))
																				 {
																					 return true;
																				 }
																				 else
																				 {
																					 $security = settings()->get(\'password_security\', \'int\');

																					 if($security == 1)
																					 {
																						 $error = l(\'The password must be at least 3 characters long.\');
																					 }
																					 elseif($security == 2)
																					 {
																						 $error = l(\'The password must be at least 4 characters long and cannot contain your username.\');
																					 }
																					 elseif($security == 3)
																					 {
																						 $error = l(\'The password must be at least 5 characters long, cannot contain your username and contain at least 1 number.\');
																					 }
																					 else
																					 {
																						 api()->run_hooks(\'password_error_message\', array(&$security, &$error));
																					 }

																					 return false;
																				 }
																			 }'),
											 'default_value' => '',
										 ));

		// We will need you to verify that ;)
		$form->add_input(array(
											 'name' => 'verify_pass',
											 'type' => 'password',
											 'label' => l('Verify password:'),
											 'subtext' => l('Just to make sure, re-enter the password.'),
											 'default_value' => '',
										 ));

		// How about which groups they are in, whether they are activated or not? etc.
		if(member()->can('manage_members'))
		{
			// Are they an administrator?
			$form->add_input(array(
												 'name' => 'is_administrator',
												 'type' => 'checkbox',
												 'label' => l('Administrator?'),
												 'subtext' => l('If checked, the member will be an administrator, otherwise they will be just a member and additional groups can be selected below.'),
												 'default_value' => in_array('administrator', $member_info['groups']),
											 ));

			// Additional groups?
			$groups = api()->return_group();

			// Remove administrator and the member group.
			unset($groups['administrator'], $groups['member']);
			$form->add_input(array(
												 'name' => 'member_groups',
													'type' => 'select-multi',
													'label' => l('Additional member groups:'),
													'subtext' => l('Select any additional groups the member should be in, ignored if the member is an administrator.'),
													'options' => $groups,
													'rows' => 4,
													'default_value' => array_values($member_info['groups']),
												));

			// Should the account be activated? ;)
			$form->add_input(array(
												 'name' => 'member_activated',
												 'type' => 'checkbox',
												 'label' => l('Account activated:'),
												 'subtext' => l('Whether or not their account is activated. If you deactivate their account, that means they will not be able to log in.'),
												 'default_value' => $member_info['is_activated'],
											 ));
		}

		// Just make sure it is you changing your stuffs (or someone who has the
		// permission to do so, like an administrator)!
		$form->add_input(array(
											 'name' => 'verify_password',
											 'type' => 'password',
											 'label' => l('Enter your password:'),
											 'subtext' => l('For security purposes, please enter your current password.'),
											 'callback' => create_function('$name, $value, &$error', '
																			 $members = api()->load_class(\'Members\');

																			 // Make sure their password is right!
																			 if($members->authenticate(member()->name(), $value))
																			 {
																				 return true;
																			 }
																			 else
																			 {
																				 // Uh oh, wrong!
																				 $error = l(\'The password you entered did not match your current password.\');
																				 return false;
																			 }'),
										));
	}
}

if(!function_exists('profile_edit_handle'))
{
	/*
		Function: profile_edit_handle

		Handles the information received from an editing form.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function profile_edit_handle($data, &$errors = array())
	{
		// Thankfully we have the Members class, which will do all the dirty
		// work of updating a user account.
		$members = api()->load_class('Members');
		$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		$members->load($member_id);

		// Let's just make sure that the member that is being updated exists.
		if($member_info = $members->get($member_id))
		{
			// We will use the $update_info array to set the, you guessed it,
			// updated information.
			$update_info = array(
											 'display_name' => $data['display_name'],
										 );

			// We only need to do update the email address (or start the
			// required verification process) if the email address was actually
			// changed.
			$verification_required = false;
			if(strtolower(trim($member_info['email'])) != strtolower(trim($data['member_email'])))
			{
				// The administrator may require that users verify their new email
				// address. It's certainly a good idea as to ensure that the email
				// address is truly valid. However, if the user can edit other
				// users profiles or can manage members then we will assume they
				// know what they're doing ;-).
				if(settings()->get('require_email_verification', 'bool', false) && !member()->can('edit_other_profiles') && !member()->can('manage_members'))
				{
					// Let's get this started... We will certainly need to remember
					// which email address they are going to verify.
					$update_info['data']['member_email'] = $data['member_email'];

					// We will want a verification code as well.
					$update_info['data']['email_acode'] = sha1($members->rand_str(160));

					// Then send the email! Simple as that...
					$mail = api()->load_class('Mail');
					$mail->set_html(true);

					$sent = $mail->send($update_info['data']['member_email'], api()->apply_filters('verify_email_address_subject', l('Activate Your New Email Address for %s', settings()->get('site_name', 'string'))), api()->apply_filters('verify_email_address_body', l("Hello there %s, this is comes from <a href=\"%s\">%s</a>.<br /><br />You are receiving this email because you or someone else requested that this email address become their new primary email address for their account on %s. If you did not make this request please ignore this message, and if it continues feel free to contact the website&#039;s administrator.<br /><br />If you did make this request you can complete the email verification process by clicking the link below:<br /><a href=\"%s/index.php?action=verify&amp;id=%s&amp;code=%s\">%s/index.php?action=verify&amp;id=%s&amp;code=%s</a><br /><br />Regards,<br />The %s team<br />%s", $member_info['name'], baseurl(), settings()->get('site_name', 'string'), settings()->get('site_name', 'string'), baseurl(), $member_info['id'], $update_info['data']['email_acode'], baseurl(), $member_info['id'], $update_info['data']['email_acode'], settings()->get('site_name', 'string'), baseurl())));

					// Let's make sure the email was sent, that's as much as we can
					// be sure of.
					if(empty($sent))
					{
						// Looks like it didn't work! D:
						$errors[] = l('An error occurred while trying to send the email verification message. Please contact an administrator if this issue continues.');

						return false;
					}

					// We will want to show a message to them that before the new
					// email address change will go into effect they will need to
					// verify it as per administrative request.
					$verification_required = true;
				}
				else
				{
					// Just update it!
					$update_info['member_email'] = $data['member_email'];
				}
			}

			// If their password is being updated we will need to supply their
			// log in name as well because their password is salted with their
			// user name.
			if(!empty($data['member_pass']))
			{
				$update_info['member_name'] = $member_info['username'];
				$update_info['member_pass'] = $data['member_pass'];
			}

			// If you can manage members you can update additional information.
			// Honestly, this permission is somewhat pointless, as if the user is
			// not an administrator and is in a group that is given this
			// permission they can make their account an administrator and do
			// anything and everything they want... But, whatever!
			if(member()->can('manage_members'))
			{
				// Such as making an account an administrator.
				if(!empty($data['is_administrator']))
				{
					// If they are marked as an administrator we don't need to save
					// any other member groups to their account as an administrator
					// is all powerful!
					$update_info['member_groups'] = array('administrator');
				}
				else
				{
					// Otherwise we will want to make sure they are always assigned to
					// the default group of 'member.'
					$update_info['member_groups'] = array_merge(array('member'), is_array($data['member_groups']) ? $data['member_groups'] : array());
				}

				// Changing their activation status?
				$update_info['member_activated'] = !empty($data['member_activated']);

				// Do we need to send a welcome email? We will if the account
				// requires administrator approval...
				if($member_info['acode'] == 'admin_approval' && !$member_info['is_activated'])
				{
					$update_info['member_acode'] = '';

					if(!function_exists('register_send_welcome_email'))
					{
						require_once(coredir. '/register.php');
					}

					// This will send a welcome email telling them that they may now
					// log into their account -- if they so please.
					register_send_welcome_email($member_info['id']);
				}
			}

			// Update the users account.
			$members->update($member_info['id'], $update_info);

			// We updated everything... So let's go back!
			redirect(baseurl. '/index.php?action=profile'. (member()->id() == $member_info['id'] ? '' : '&id='. $member_info['id']). '&edit&message='. (!empty($verification_required) ? 2 : 1));
		}
		else
		{
			$errors[] = l('An unknown error has occurred.');

			return false;
		}
	}
}
?>