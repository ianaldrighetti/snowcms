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
		$_GET['id'] = (int)$_GET['id'] > 0 ? (int)$_GET['id'] : 0;

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

		if(empty($handled))
		{
			theme()->set_title(l('Viewing profile of %s', $member_info['name']));

			// You will be able to modify this later :P
			$display_data = array(
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
													'show' => true,
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

		$form->add_input(array(
											 'name' => 'member_email',
											 'type' => 'string',
											 'label' => l('Email address:'),
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
			$form->add_field('member_edit_'. $member_info['id'], 'is_administrator', array(
																																								 'type' => 'checkbox',
																																								 'label' => l('Administrator?'),
																																								 'subtext' => l('If checked, the member will be an administrator, otherwise they will be just a member and additional groups can be selected below.'),
																																								 'value' => in_array('administrator', $member_info['groups']),
																																							 ));

			// Additional groups?
			$groups = api()->return_group();

			// Remove administrator and the member group.
			unset($groups['administrator'], $groups['member']);
			$form->add_field('member_edit_'. $member_info['id'], 'member_groups', array(
																																							'type' => 'select-multi',
																																							'label' => l('Additional member groups:'),
																																							'subtext' => l('Select any additional groups the member should be in, ignored if the member is an administrator.'),
																																							'options' => $groups,
																																							'rows' => 4,
																																							'value' => $member_info['groups'],
																																						));

			// Should the account be activated? ;)
			$form->add_field('member_edit_'. $member_info['id'], 'member_activated', array(
																																								 'type' => 'checkbox',
																																								 'label' => l('Account activated:'),
																																								 'subtext' => l('Whether or not their account is activated. If you deactivate their account, that means they will not be able to log in.'),
																																								 'value' => $member_info['is_activated'],
																																							 ));
		}

		// Just make sure it is you changing your stuffs!
		$form->add_input(array(
											 'name' => 'verify_password',
											 'type' => 'password',
											 'label' => l('Enter your password:'),
											 'subtext' => l('For security purposes, please enter your current password.'),
											 'callback' => create_function('$value, $form_name, &$error', '
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
		$members = api()->load_class('Members');
		$members->load($data['member_id']);

		if($member_info = $members->get($data['member_id']))
		{
			$update_info = array(
											 'display_name' => $data['display_name'],
											 'member_email' => $data['member_email'],
										 );

			if(!empty($data['member_pass']))
			{
				$update_info['member_name'] = $member_info['username'];
				$update_info['member_pass'] = $data['member_pass'];
				$redir_login = member()->id() == $member_info['id'];
			}

			if(member()->can('manage_members'))
			{
				if(!empty($data['is_administrator']))
				{
					$update_info['member_groups'] = array('administrator');
				}
				else
				{
					$update_info['member_groups'] = array_merge(array('member'), $data['member_groups']);
				}

				$update_info['member_activated'] = !empty($data['member_activated']);

				// Do we need to send a welcome email?
				if($member_info['acode'] != 'admin_approval' && !$member_info['is_activated'])
				{
					$update_info['member_acode'] = '';

					if(!function_exists('register_send_welcome_email'))
					{
						require_once(coredir. '/register.php');
					}

					register_send_welcome_email($member_info['id']);
				}
			}

			$members->update($member_info['id'], $update_info);

			if(!empty($redir_login))
			{
				redirect(baseurl. '/index.php?action=login&member_name='. urlencode($member_info['username']));
			}
			else
			{
				redirect(baseurl. '/index.php?action=profile'. (member()->id() == $member_info['id'] ? '' : '&id='. $member_info['id']));
			}
		}
		else
		{
			$errors[] = l('An unknown error has occurred.');
			return false;
		}
	}
}
?>