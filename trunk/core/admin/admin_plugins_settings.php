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

// Title: Plugin Settings

if(!function_exists('admin_plugins_settings'))
{
	/*
		Function: admin_plugins_settings

		This is meant for plugins to add their various settings to, plugins
		can of course create their own settings pages and such in the control
		panel, however, their are certain plugins which really don't need
		their own, so this is for them!

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_settings()
	{
		api()->run_hooks('admin_plugins_settings');

		// Can you manage plugin settings?
		if(!member()->can('manage_plugin_settings'))
		{
			// That's what I thought!
			admin_access_denied();
		}

		// We will need the form for this ;)
		admin_plugins_settings_generate_form();
		$form = api()->load_class('Form');

		// Submitting the form? Alright.
		if(!empty($_POST['admin_plugins_settings_form']))
		{
			// We shall process it! But through AJAX?
			if(isset($_GET['ajax']))
			{
				echo $form->json_process('admin_plugins_settings_form');
				exit;
			}
			else
			{
				// Just regular ol' submitting ;)
				$form->process('admin_plugins_settings_form');
			}
		}

		admin_current_area('plugins_settings');

		theme()->set_title(l('Plugin Settings'));

		api()->context['form'] = $form;

		theme()->render('admin_plugins_settings');
	}
}

if(!function_exists('admin_plugins_settings_generate_form'))
{
	/*
		Function: admin_plugins_settings_generate_form

		Generates the form which plugins can add their various settings to.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_settings_generate_form()
	{
		// We need the Form class, that's for sure!
		$form = api()->load_class('Form');

		$form->add('admin_plugins_settings_form', array(
																								'action' => baseurl. '/index.php?action=admin&sa=plugins_settings',
																								'callback' => 'admin_plugins_settings_handle',
																								'submit' => l('Save settings'),
																							));

		// There is actually nothing to add, lol... It's all for the plugins ;)
		api()->run_hooks('admin_plugins_settings_add');
	}
}

if(!function_exists('admin_plugins_settings_handle'))
{
	/*
		Function: admin_plugins_settings_handle

		Handles the saving of the settings from the plugins settings form.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_settings_handle($data, &$errors = array())
	{
		// We will need to update the values so we don't have to redirect.
		$form = api()->load_class('Form');

		// Loop through all the settings and save them!
		foreach($data as $variable => $value)
		{
			// Ignore this if it is a CSRF token.
			if(substr($variable, -6, 6) == '_token')
			{
				continue;
			}

			// Set it :)
			settings()->set($variable, $value);
		}

		api()->add_hook('admin_plugins_settings_form_messages', create_function('&$value', '
																															$value[] = l(\'Plugin settings have been successfully updated.\');'));

		return true;
	}
}
?>