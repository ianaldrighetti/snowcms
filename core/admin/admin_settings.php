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

// Title: System Settings

if(!function_exists('admin_settings'))
{
	/*
		Function: admin_settings

		Displays an interface to change some basic (though core!) settings
		for your system

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_settings()
	{
		api()->run_hooks('admin_settings');

		// Let's make sure you can manage system settings.
		if(!member()->can('manage_system_settings'))
		{
			admin_access_denied();
		}

		// We have a few different settings forms we can display.
		$form_types = api()->apply_filters('admin_settings_forms', array(
																															   'basic' => array(
																																							l('Basic Settings'),
																																							l('Manage basic settings'),
																																							l('Basic settings include changing the name of your website, along with a website description keywords, and more.'),
																																						),
																															   'date' => array(
																																						 l('Date &amp; Time Settings'),
																																						 l('Manage date and time settings'),
																																						 l('The format of how a date or time is displayed can be modified here.'),
																																					 ),
																															   'mail' => array(
																																						 l('Email Settings'),
																																						 l('Manage email settings'),
																																						 l('In order for activation, password resetting and other email messages to be sent to your users, you must select how these emails are sent. If SMTP is chosen, then you may be required to supply information such as the location of the server, a username, and password.'),
																																					 ),
																																 'security' => array(
																																								 l('Security Settings'),
																																								 l('Manage security related settings'),
																																								 l('There are a couple administrative security options which can be configured, such as disabling administrative security all together and also the timeout period for administrative authentication.'),
																																							 ),
																															   'other' => array(
																																							l('Other Settings'),
																																							l('Manage miscellaneous settings'),
																																							l('Other settings that do not belong to any other category can be found here, such as UTF-8 support, disabling administrative security, and others.'),
																																						),
																															 ));

		// Which one are we going to generate?
		$form_type = !empty($_GET['type']) && isset($form_types[$_GET['type']]) ? $_GET['type'] : 'basic';

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
																					 'href' => baseurl. '/index.php?action=admin&amp;sa=settings&amp;type='. $type_id,
																					 'title' => $type_info[1],
																					 'is_first' => $is_first,
																					 'is_selected' => $form_type == $type_id,
																					 'text' => $type_info[0],
																				 );

			// Nothing else will be first.
			$is_first = false;
		}

		admin_settings_generate_form($form_type);
		$form = api()->load_class('Form');

		// Submitting the form? Alright.
		if(!empty($_POST[$form_type. '_settings_form']))
		{
			// We shall process it! But through AJAX?
			if(isset($_GET['ajax']))
			{
				echo $form->json_process($form_type. '_settings_form');
				exit;
			}
			else
			{
				// Just regular ol' submitting ;)
				$form->process($form_type. '_settings_form');
			}
		}

		admin_current_area('system_settings');

		theme()->set_title(htmlchars_decode($form_types[$form_type][0]));

		api()->context['form'] = $form;
		api()->context['form_type'] = $form_type;
		api()->context['settings_title'] = $form_types[$form_type][0];
		api()->context['settings_description'] = $form_types[$form_type][2];

		theme()->render('admin_settings');
	}
}

if(!function_exists('admin_settings_generate_form'))
{
	/*
		Function: admin_settings_generate_form

		Generates the right settings form according to the type requested.

		Parameters:
			string $form_type - The settings form to generate.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_settings_generate_form($form_type)
	{
		$form = api()->load_class('Form');

		$form->add($form_type. '_settings_form', array(
																				'action' => baseurl. '/index.php?action=admin&amp;sa=settings&amp;type='. $form_type,
																				'callback' => 'admin_settings_handle',
																				'submit' => l('Update settings'),
																			));

		$form->current($form_type. '_settings_form');

		// Now, which input's do we need to add?
		if($form_type == 'basic')
		{
			// Basic includes things such as website name, sub title, description
			// and so on.
			$form->add_input(array(
												 'name' => 'site_name',
												 'type' => 'string',
												 'label' => l('Website name'),
												 'subtext' => l('The name of your website.'),
												 'default_value' => htmlchars_decode(settings()->get('site_name', 'string')),
											 ));

			// The sub title for the website. Kind of like a slogan.
			$form->add_input(array(
												 'name' => 'site_sub_title',
												 'type' => 'string',
												 'label' => l('Website subtitle'),
												 'subtext' => l('Kind of like a slogan.'),
												 'default_value' => htmlchars_decode(settings()->get('site_sub_title', 'string')),
											 ));

			// Website description.
			$form->add_input(array(
												 'name' => 'site_meta_desc',
												 'type' => 'textarea',
												 'label' => l('Website description'),
												 'subtext' => l('A description of your website which will appear in the &lt;head&gt; of your website.'),
												 'default_value' => htmlchars_decode(settings()->get('site_meta_desc')),
												 'rows' => 4,
											 ));

			// Some keywords, perhaps.
			$form->add_input(array(
												 'name' => 'site_meta_keywords',
												 'type' => 'string',
												 'label' => l('Website keywords'),
												 'subtext' => l('A list of a comma separated keywords which will appear in the &lt;head&gt; of your website.'),
												 'default_value' => htmlchars_decode(settings()->get('site_meta_keywords')),
											 ));

			// Whether or not to display your systems SnowCMS version.
			$form->add_input(array(
												 'name' => 'show_version',
												 'type' => 'checkbox',
												 'label' => l('Display SnowCMS version'),
												 'subtext' => l('When enabled the version of SnowCMS you are running will be displayed.'),
												 'default_value' => htmlchars_decode(settings()->get('show_version', 'int')),
											 ));

		}
		// Anything relating to date and time should go here.
		elseif($form_type == 'date')
		{
			// Time formatting information!
			// This is for when strictly the date (no time) is to be shown.
			$form->add_input(array(
												 'name' => 'date_format',
												 'type' => 'string-html',
												 'label' => l('Date format:'),
												 'subtext' => l('Date only format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information. <span class="bold">HTML is allowed.</span>'),
												 'default_value' => settings()->get('date_format', 'string'),
											 ));

			// This one is for just when time is to be displayed.
			$form->add_input(array(
												 'name' => 'time_format',
												 'type' => 'string-html',
												 'label' => l('Time format:'),
												 'subtext' => l('Time only format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information. <span class="bold">HTML is allowed.</span>'),
												 'default_value' => settings()->get('time_format', 'string'),
											 ));

			// As you probably guessed, this is a combination.
			$form->add_input(array(
												 'name' => 'datetime_format',
												 'type' => 'string-html',
												 'label' => l('Date and time format:'),
												 'subtext' => l('Date and time format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information. <span class="bold">HTML is allowed.</span>'),
												 'default_value' => settings()->get('datetime_format', 'string'),
											 ));

			// The timeformat function will say Today at ... or Yesterday at ...
			// when it is relevant, but not everyone likes that. Maybe.
			$form->add_input(array(
												 'name' => 'disable_today_yesterday',
												 'type' => 'checkbox',
												 'label' => l('Disable today/yesterday feature'),
												 'subtext' => l('Disable date and times from being displayed as <strong>Today</strong> at <em>[...]</em> and <strong>Yesterday</strong> at <em>[...]</em>.'),
												 'default_value' => settings()->get('disable_today_yesterday', 'string', false),
											 ));
		}
		// Sending email, SMTP and mail settings belong here.
		elseif($form_type == 'mail')
		{
			// The email address to, of course, send any emails from.
			$form->add_input(array(
												 'name' => 'site_email',
												 'type' => 'string',
												 'label' => l('Website email address'),
												 'subtext' => l('The email address from which emails will appear to come from.'),
												 'default_value' => htmlchars_decode(settings()->get('site_email', 'string')),
											 ));

			// What should handle sending emails..?
			$form->add_input(array(
												 'name' => 'mail_handler',
												 'type' => 'select',
												 'label' => l('Mail handler'),
												 'subtext' => l('Allows you to set which protocol (or function) handles sending emails.'),
												 'options' => api()->apply_filters('admin_mail_handler', array(
																																									 'smtp' => 'SMTP',
																																									 'mail' => 'PHP mail()',
																																								 )),
												 'default_value' => htmlchars_decode(settings()->get('mail_handler', 'string')),
											 ));

			// Your SMTP host, quite important, you know?
			$form->add_input(array(
												 'name' => 'smtp_host',
												 'type' => 'string',
												 'label' => l('SMTP host'),
												 'subtext' => l('The host address of the SMTP server.'),
												 'default_value' => htmlchars_decode(settings()->get('smtp_host', 'string')),
											 ));

			// The port of the SMTP server.
			$form->add_input(array(
												 'name' => 'smtp_port',
												 'type' => 'int',
												 'label' => l('SMTP port'),
												 'subtext' => l('The port of the SMTP server, usually 25 or 465 (if it uses SSL).'),
												 'length' => array(
																			 'min' => 1,
																			 'max' => 65535,
																		 ),
												 'default_value' => settings()->get('smtp_port', 'int'),
											 ));

			// SMTP username.
			$form->add_input(array(
												 'name' => 'smtp_user',
												 'type' => 'string',
												 'label' => l('SMTP username'),
												 'default_value' => htmlchars_decode(settings()->get('smtp_user', 'string')),
											 ));

			// SMTP password.
			$form->add_input(array(
												 'name' => 'smtp_pass',
												 'type' => 'password',
												 'label' => l('SMTP password'),
												 'subtext' => l('Your SMTP password will only be updated if this field is set.'),
												 'default_value' => '',
											 ));

			// Does the SMTP host use TLS?
			$form->add_input(array(
												 'name' => 'smtp_is_tls',
												 'type' => 'checkbox',
												 'label' => l('SMTP host uses TLS'),
												 'subtext' => l('Check this box if the SMTP host uses TLS, such as Gmail or Hotmail.'),
												 'default_value' => settings()->get('smtp_is_tls', 'int'),
												));

			// Number of seconds before the SMTP connection attempt is aborted.
			$form->add_input(array(
												 'name' => 'smtp_timeout',
												 'type' => 'int',
												 'label' => l('SMTP timeout'),
												 'subtext' => l('The maximum number, in seconds, that the server will wait for a response from the SMTP host.'),
												 'length' => array(
																			 'min' => 1,
																		 ),
												 'default_value' => settings()->get('smtp_timeout', 'int'),
											 ));

			// Additional mail parameters.
			$form->add_input(array(
												 'name' => 'mail_additional_parameters',
												 'type' => 'string',
												 'label' => l('Additional mail parameters'),
												 'subtext' => l('Any additional PHP mail() function parameters (the $additional_parameters parameter).'),
												 'default_value' => htmlchars_decode(settings()->get('mail_additional_parameters', 'string')),
											 ));

		}
		elseif($form_type == 'security')
		{
			// How long should their authentication last?
			$form->add_input(array(
												 'name' => 'admin_login_timeout',
												 'type' => 'int',
												 'length' => array(
																			 'min' => 1,
																		 ),
												 'label' => l('Authentication timeout'),
												 'subtext' => l('How often should a user have to authenticate themselves by entering their password in order to access the control panel, in minutes. Requires administrative security to be enabled.'),
												 'default_value' => settings()->get('admin_login_timeout', 'int', 15),
											 ));

			// Disable admin security? Not a good idea, but hey, it's your site!!!
			$form->add_input(array(
												 'name' => 'disable_admin_security',
												 'type' => 'checkbox',
												 'label' => l('Disable administrative security'),
												 'subtext' => l('If administrative security is disabled, then users who are allowed to access the control panel will never be prompted for their password. It is <em>not</em> recommended that this be disabled.'),
												 'default_value' => settings()->get('disable_admin_security', 'int'),
											 ));
		}
		// Anything else belongs here.
		elseif($form_type == 'other')
		{
			// Whether or not you want to enable the task system.
			$form->add_input(array(
												 'name' => 'enable_tasks',
												 'type' => 'checkbox',
												 'label' => l('Enable tasks'),
												 'subtext' => l('If enabled, scheduled tasks will be allowed to run, this is not run by a cron, but by people browsing your site.'),
												 'default_value' => settings()->get('enable_tasks', 'int'),
											 ));

			// The maximum number of tasks to run at a time.
			$form->add_input(array(
												 'name' => 'max_tasks',
												 'type' => 'int',
												 'label' => l('Maximum tasks to run at a time'),
												 'subtext' => l('The maximum number of tasks which can be ran at once at any given time.'),
												 'length' => array(
																			 'min' => 0,
																		 ),
												 'default_value' => settings()->get('max_tasks', 'int'),
											 ));

			// Enable even more UTF8 support? You crazy! :P
			$form->add_input(array(
												 'name' => 'enable_utf8',
												 'type' => 'checkbox',
												 'label' => l('Enable UTF8 support'),
												 'subtext' => l('If enabled (and if the Multibyte PHP extension is enabled), UTF8 capable functions will be used to handle data. Please note that this can, in cases, slow your site down.'),
												 'disabled' => !function_exists('mb_internal_encoding'),
												 'default_value' => settings()->get('enable_utf8', 'int'),
											 ));

			// Log errors in the database?
			$form->add_input(array(
												 'name' => 'errors_log',
												 'type' => 'checkbox',
												 'label' => l('Log errors in database'),
												 'subtext' => l('When enabled, SnowCMS will log any PHP errors (not fatal errors) in the database, instead of the error logging system set in the php.ini.'),
												 'default_value' => settings()->get('errors_log', 'int'),
											 ));
		}

		// You may need to do this yourself.
		api()->run_hooks('admin_settings_generate_form', array($form_type));
	}
}

if(!function_exists('admin_settings_handle'))
{
	/*
		Function: admin_settings_handle

		Handles the admin_settings_form information.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.

			Even if false is returned, in the case that certain settings
			were invalid, all the valid settings do get saved.
	*/
	function admin_settings_handle($data, &$errors = array())
	{
		// We will need to update the values so we don't have to redirect.
		$form = api()->load_class('Form');

		// Loop through all the settings and save them!
		foreach($form->inputs($_GET['type']. '_settings_form') as $input)
		{
			// Ignore this if it is a CSRF token.
			if(substr($input->name(), -6, 6) == '_token' && $input->type() == 'hidden')
			{
				continue;
			}

			$variable = $input->name();
			$value = $data[$variable];

			// This one is special :P
			if($variable == 'smtp_pass')
			{
				if(empty($value))
				{
					// Don't update it!
					continue;
				}
			}

			// Set it :)
			settings()->set($variable, $value, 'string');
		}

		api()->add_hook($_GET['type']. '_settings_form_messages', create_function('&$value', '
																																$value[] = l(\'Settings have been updated successfully.\');'), 10, 1);

		return true;
	}
}
?>