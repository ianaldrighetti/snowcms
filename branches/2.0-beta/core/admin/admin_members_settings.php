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

// Title: Member Settings

if(!function_exists('admin_members_settings'))
{
	/*
		Function: admin_members_settings

		An interface for managing member settings.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_members_settings()
	{
		api()->run_hooks('admin_members_settings');

		// Let's see, can you manage member settings?
		if(!member()->can('manage_member_settings'))
		{
			// I didn't think so!
			admin_access_denied();
		}

		// We have a few different settings forms we can display.
		$form_types = api()->apply_filters('admin_settings_forms', array(
																															   'register' => array(
																																								 l('Registration Settings'),
																																								 l('Manage registration settings'),
																																								 l('Registration settings can be managed here, such as choosing the process of a user activating their account.'),
																																							 ),
																															   'disallowed' => array(
																																									 l('Disallowed Names &amp; Emails'),
																																									 l('Manage usernames and email addresses which are not allowed to be used'),
																																									 l('If you want to prevent members (and possibly guests) from using certain names and email addresses, you may add them to the lists below.'),
																																								 ),
																																 'other' => array(
																																						  l('Username, Email &amp; Password Settings'),
																																						  l('Manage security related settings'),
																																						  l('Password and username length requirements can be changed here, along with how email address changes should be handled.'),
																																					  ),
																															 ));

		// Which one are we going to generate?
		$form_type = !empty($_GET['type']) && isset($form_types[$_GET['type']]) ? $_GET['type'] : 'register';

		// Just to make sure.
		$GLOBALS['_GET']['type'] = $form_type;

		// This will come in handy.
		api()->context['section_menu'] = array();
		$GLOBALS['settings_identifiers'] = array();
		$is_first = true;
		foreach($form_types as $type_id => $type_info)
		{
			$GLOBALS['settings_identifiers'][$type_id] = $type_info[0];

			api()->context['section_menu'][] = array(
																					 'href' => baseurl. '/index.php?action=admin&amp;sa=members_settings&amp;type='. $type_id,
																					 'title' => $type_info[1],
																					 'is_first' => $is_first,
																					 'is_selected' => $form_type == $type_id,
																					 'text' => $type_info[0],
																				 );

			// Nothing else will be first.
			$is_first = false;
		}

		// Generate our form!
		admin_members_settings_generate_form($form_type);
		$form = api()->load_class('Form');

		// Time to save?
		if(!empty($_POST[$form_type. '_settings_form']))
		{
			// Save all the data.
			if(isset($_GET['ajax']))
			{
				echo $form->json_process($form_type. '_settings_form');
				exit;
			}
			else
			{
				$form->process($form_type. '_settings_form');
			}
		}

		admin_current_area('members_settings');

		theme()->set_title(htmlchars_decode($form_types[$form_type][0]));

		api()->context['form'] = $form;
		api()->context['form_type'] = $form_type;
		api()->context['settings_title'] = $form_types[$form_type][0];
		api()->context['settings_description'] = $form_types[$form_type][2];

		theme()->render('admin_members_settings');
	}
}

if(!function_exists('admin_members_settings_generate_form'))
{
	/*
		Function: admin_members_settings_generate_form

		Generates the right settings form according to the type requested.

		Parameters:
			string $form_type - The settings form to generate.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_members_settings_generate_form($form_type)
	{
		$form = api()->load_class('Form');

		$form->add($form_type. '_settings_form', array(
																							 'action' => baseurl. '/index.php?action=admin&sa=members_settings&amp;type='. $form_type,
																							 'callback' => 'admin_members_settings_handle',
																							 'submit' => l('Save settings'),
																						 ));

		$form->current($form_type. '_settings_form');

		// You viewing registration settings?
		if($form_type == 'register')
		{
			// Types of registration.
			$types = api()->apply_filters('registration_types', array(
																													 0 => l('Disabled'),
																													 1 => l('Instant activation'),
																													 2 => l('Administrative activation'),
																													 3 => l('Email activation'),
																												 ));
			$form->add_input(array(
												 'name' => 'registration_type',
												 'type' => 'select',
												 'label' => l('Registration mode'),
												 'subtext' => l('<em>Disabled</em> - Registration disabled.<br /><em>Instant activation</em> - account activated upon registration.<br /><em>Administrative activation</em> - administrators must activate new accounts.<br /><em>Email activation:</em> new registrations must verify their email address.'),
												 'options' => $types,
												 'default_value' => settings()->get('registration_type', 'int'),
											 ));
		}
		// How about disallowed names and email addresses?
		elseif($form_type == 'disallowed')
		{
			// Disallowed names...
			$form->add_input(array(
												 'name' => 'disallowed_names',
												 'type' => 'textarea',
												 'label' => l('Disallowed names'),
												 'subtext' => l('These are names which cannot be registered or used by members (such as display names). Enter one name per line, with an asterisk (*) denoting a wildcard.'),
												 'rows' => 5,
												 'columns' => 35,
												 'default_value' => settings()->get('disallowed_names', 'string'),
											 ));

			// Disallowed email addresses.
			$form->add_input(array(
												 'name' => 'disallowed_emails',
												 'type' => 'textarea',
												 'label' => l('Disallowed email addresses'),
												 'subtext' => l('These are email addresses which cannot be registered with or used by members. Enter an email address per line, with an asterisk (*) denoting a wildcard. To disallow entire domains, do: *@domain.com.'),
												 'rows' => 5,
												 'columns' => 35,
												 'default_value' => settings()->get('disallowed_emails', 'string'),
											 ));
		}
		elseif($form_type == 'other')
		{
			// Password security :P
			$levels = api()->apply_filters('password_security_levels', array(
																																	1 => 'Low',
																																	2 => 'Medium',
																																	3 => 'High',
																																));

			$form->add_input(array(
												 'name' => 'password_security',
												 'type' => 'select',
												 'label' => l('Password security'),
												 'subtext' => l('<em>Low</em> - must be at least 4 characters.<br /><em>Medium</em> - Must be at least 6 characters, cannot contain username.<br /><em>High</em> - Must be at least 8 characters, be alphanumeric and cannot contain their username.'),
												 'options' => $levels,
												 'default_value' => settings()->get('password_security', 'int'),
											 ));

			// Minimum length of a username/display name.
			$form->add_input(array(
												 'name' => 'members_min_name_length',
												 'type' => 'int',
												 'label' => l('Minimum username length'),
												 'subtext' => l('The minimum length of a username can range from 1 to 80 characters. This also applies to display names.'),
												 'length' => array(
																			 'min' => 1,
																			 'max' => 80,
																		 ),
													'callback' => create_function('$name, $value, &$error', '

																					if(isset($_POST[\'members_max_name_length\']) && (int)$value > (int)$_POST[\'members_max_name_length\'])
																					{
																						$error = l(\'Minimum username length can\\\'t be larger than the maximum username length.\');
																						return false;
																					}

																					return true;'),
													'default_value' => settings()->get('members_min_name_length', 'int', 1),
												));

			// Maximum length of a username/display name.
			$form->add_input(array(
												 'name' => 'members_max_name_length',
												 'type' => 'int',
												 'label' => l('Maximum username length'),
												 'subtext' => l('The maximum length of a username can range from 1 to 80 characters. This also applies to display names.'),
												 'length' => array(
																			 'min' => 1,
																			 'max' => 80,
																		 ),
													'callback' => create_function('$name, $value, &$error', '

																					if(isset($_POST[\'members_min_name_length\']) && (int)$value < (int)$_POST[\'members_min_name_length\'])
																					{
																						return false;
																					}

																					return true;'),
													'default_value' => settings()->get('members_max_name_length', 'int', 80),
												));

			$form->add_input(array(
												 'name' => 'require_email_verification',
												 'type' => 'checkbox',
												 'label' => l('Require email verification'),
												 'subtext' => l('If enabled, this option requires that a user verify their new email address before the email address can be used.'),
												 'default_value' => settings()->get('require_email_verification', 'bool', false),
											 ));
		}

		// You may need to do this yourself.
		api()->run_hooks('admin_member_settings_generate_form', array($form_type));
	}
}

if(!function_exists('admin_members_settings_handle'))
{
	/*
		Function: admin_members_settings_handle

		Handles the form data from the member settings form.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function admin_members_settings_handle($data, &$errors = array())
	{
		$form = api()->load_class('Form');

		// Update them, easy!
		foreach($data as $variable => $value)
		{
			// Ignore this if it is a CSRF token.
			if(substr($variable, -6, 6) == '_token')
			{
				continue;
			}

			// Save it.
			settings()->set($variable, $value);
		}

		api()->add_hook($_GET['type']. '_settings_form_messages', create_function('&$value', '
																																$value[] = l(\'Settings have been updated successfully.\');'));

		return true;
	}
}
?>