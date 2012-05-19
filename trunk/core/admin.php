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

// Title: Admin switch

if(!function_exists('admin_prepend'))
{
	/*
		Function: admin_prepend

		With events there is no switch function which all administrative
		sections branch off from, and we would rather not require each
		plugin to have to check and see if the user needs to authenticate
		themselves again. So it is done here, via a hook :).

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_prepend()
	{
		global $icons, $icon_map;

		// Clear everything...
		theme()->clear();

		// We shouldn't just create a new instance of the Theme class in order
		// to get a custom theme for the administrator control panel, but we can
		// do a couple things to have the same effect.
		theme()->set_main_title(api()->apply_filters('admin_main_title', l('Control Panel'). ' - '. settings()->get('site_name', 'string')));
		theme()->set_url(api()->apply_filters('admin_theme_url', baseurl. '/core/admin/theme'));
		theme()->set_themedir(api()->apply_filters('admin_themedir', coredir. '/admin/theme'));

		// Set the current area to nothing.
		admin_current_area(null);

		// And a few other things...
		theme()->add_js_file(array('src' => themeurl. '/default/js/snowobj.js'));
		theme()->add_js_file(array('src' => theme()->url(). '/js/main.js'));
		theme()->add_js_file(array('src' => theme()->url(). '/js/notifications.js'));
		theme()->add_js_file(array('src' => theme()->url(). '/js/jquery.min.js'));
		theme()->add_js_file(array('src' => theme()->url(). '/js/jquery-ui.min.js'));
		theme()->add_js_var('notificationTitle', l('Notifications'));
		theme()->add_js_var('base_url', baseurl);
		theme()->add_js_var('baseurl', baseurl);
		theme()->add_js_var('session_id', member()->session_id());
		theme()->add_link(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => theme()->url(). '/style/main.css'));

		api()->run_hooks('post_admin_theme_init');

		if(member()->can('access_admin_cp'))
		{
			// Generate all the icons, done here as it is used in other places
			// than just the control panel home.
			$icons = array(
				l('System') => array(
													array(
														'id' => 'system_settings',
														'href' => baseurl('index.php?action=admin&amp;sa=settings'),
														'title' => l('System settings'),
														'src' => theme()->url(). '/style/images/settings.png',
														'label' => l('Settings'),
														'show' => member()->can('manage_system_settings'),
														'children' => array(
																						array(
																							'id' => 'basic_system_settings',
																							'href' => baseurl('index.php?action=admin&amp;sa=settings&amp;type=basic'),
																							'title' => l('Change your website name, subtitle, description and keywords'),
																							'label' => l('Basic Settings'),
																							'show' => true,
																						),
																						array(
																							'id' => 'date_system_settings',
																							'href' => baseurl('index.php?action=admin&amp;sa=settings&amp;type=date'),
																							'title' => l('Manage date and time format settings'),
																							'label' => l('Date &amp; Time Settings'),
																							'show' => true,
																						),
																						array(
																							'id' => 'mail_system_settings',
																							'href' => baseurl('index.php?action=admin&amp;sa=settings&amp;type=email'),
																							'title' => l('Manage email settings, such as the email address to send emails with'),
																							'label' => l('Email Settings'),
																							'show' => true,
																						),
																						array(
																							'id' => 'security_system_settings',
																							'href' => baseurl('index.php?action=admin&amp;sa=settings&amp;type=security'),
																							'title' => l('Manage security related settings'),
																							'label' => l('Security Settings'),
																							'show' => true,
																						),
																						array(
																							'id' => 'other_system_settings',
																							'href' => baseurl('index.php?action=admin&amp;sa=settings&amp;type=other'),
																							'title' => l('Manage miscellaneous settings'),
																							'label' => l('Other Settings'),
																							'show' => true,
																						),
																					),
													),
													array(
														'id' => 'system_update',
														'href' => baseurl('index.php?action=admin&amp;sa=update'),
														'title' => l('Check for updates'),
														'src' => theme()->url(). '/style/images/update.png',
														'label' => l('Update'),
														'tree_label' => l('Check for Updates'),
														'show' => member()->can('update_system'),
													),
													array(
														'id' => 'system_about',
														'href' => baseurl('index.php?action=admin&amp;sa=about'),
														'title' => l('About SnowCMS and system information'),
														'src' => theme()->url(). '/style/images/about.png',
														'label' => l('About'),
														'tree_label' => l('About SnowCMS'),
														'show' => true,
													),
													array(
														'id' => 'system_error_log',
														'href' => baseurl('index.php?action=admin&amp;sa=error_log'),
														'title' => l('View the error log'),
														'src' => theme()->url(). '/style/images/error_log.png',
														'label' => l('Error Log'),
														'show' => member()->can('view_error_log') && settings()->get('errors_log', 'bool'),
													),
												),
				l('Plugins') => array(
													array(
														'id' => 'plugins_add',
														'href' => baseurl('index.php?action=admin&amp;sa=plugins_add'),
														'title' => l('Add a new plugin'),
														'src' => theme()->url(). '/style/images/plugins_add.png',
														'label' => l('Add'),
														'tree_label' => l('Add a New Plugin'),
														'show' => member()->can('add_plugins'),
													),
													array(
														'id' => 'plugins_manage',
														'href' => baseurl('index.php?action=admin&amp;sa=plugins_manage'),
														'title' => l('Manage plugins'),
														'src' => theme()->url(). '/style/images/plugins_manage.png',
														'label' => l('Manage'),
														'tree_label' => l('Manage Plugins'),
														'show' => member()->can('manage_plugins'),
													),
													array(
														'id' => 'plugins_settings',
														'href' => baseurl('index.php?action=admin&amp;sa=plugins_settings'),
														'title' => l('Manage plugin settings'),
														'src' => theme()->url(). '/style/images/plugins_settings.png',
														'label' => l('Settings'),
														'tree_label' => l('Plugin Settings'),
														'show' => member()->can('manage_plugin_settings'),
													),
												),
				l('Themes') => array(
												 array(
													 'id' => 'manage_manage_themes',
													 'href' => baseurl('index.php?action=admin&amp;sa=themes&amp;section=manage'),
													 'title' => l('Manage currently installed themes'),
													 'src' => theme()->url(). '/style/images/manage_themes.png',
													 'label' => l('Manage Themes'),
													 'show' => member()->can('manage_themes') || member()->can('select_theme'),
												 ),
												 array(
													 'id' => 'install_manage_themes',
													 'href' => baseurl('index.php?action=admin&amp;sa=themes&amp;section=install'),
													 'title' => l('Install a new theme from a file or from the Internet'),
													 'src' => theme()->url(). '/style/images/manage_themes.png',
													 'label' => l('Install Theme'),
													 'show' => member()->can('manage_themes'),
												 ),
												 array(
													'id' => 'widgets_manage_themes',
													'href' => baseurl('index.php?action=admin&amp;sa=themes&amp;section=widgets'),
													'title' => l('Manage widgets'),
													'src' => theme()->url(). '/style/images/manage_themes.png',
													'label' => l('Widgets'),
													'tree_label' => l('Manage Widgets'),
													'show' => member()->can('manage_themes') || member()->can('manage_widgets'),
												 ),
											 ),
				l('Members') => array(
													array(
														'id' => 'members_add',
														'href' => baseurl('index.php?action=admin&amp;sa=members_add'),
														'title' => l('Add a new member'),
														'src' => theme()->url(). '/style/images/members_add.png',
														'label' => l('Add'),
														'tree_label' => l('Add a New Member'),
														'show' => member()->can('add_new_member'),
													),
													array(
														'id' => 'members_manage',
														'href' => baseurl('index.php?action=admin&amp;sa=members_manage'),
														'title' => l('Manage existing members'),
														'src' => theme()->url(). '/style/images/members_manage.png',
														'label' => l('Manage'),
														'tree_label' => l('Manage Members'),
														'show' => member()->can('manage_members'),
													),
													array(
														'id' => 'members_settings',
														'href' => baseurl('index.php?action=admin&amp;sa=members_settings'),
														'title' => l('Member settings'),
														'src' => theme()->url(). '/style/images/members_settings.png',
														'label' => l('Settings'),
														'show' => member()->can('manage_member_settings'),
														'children' => array(
																						array(
																							'id' => 'register_members_settings',
																							'href' => baseurl('index.php?action=admin&amp;sa=members_settings&amp;type=register'),
																							'title' => l('Manage registration settings'),
																							'label' => l('Registration Settings'),
																							'show' => true,
																						),
																						array(
																							'id' => 'disallowed_members_settings',
																							'href' => baseurl('index.php?action=admin&amp;sa=members_settings&amp;type=disallowed'),
																							'title' => l('Manage usernames and email addresses which are not allowed to be used'),
																							'label' => l('Disallowed Names &amp; Emails'),
																							'show' => true,
																						),
																						array(
																							'id' => 'other_members_settings',
																							'href' => baseurl('index.php?action=admin&amp;sa=members_settings&amp;type=other'),
																							'title' => l('Change the length requirements for usernames and passwords, along with how email address changes should be handled'),
																							'label' => l('Username, Email &amp; Password Settings'),
																							'show' => true,
																						),
																					),
													),
													array(
														'id' => 'members_permissions',
														'href' => baseurl('index.php?action=admin&amp;sa=members_permissions'),
														'title' => l('Set member group permissions'),
														'src' => theme()->url(). '/style/images/members_permissions.png',
														'label' => l('Permissions'),
														'tree_label' => l('Manage Permissions'),
														'show' => member()->can('manage_permissions'),
													),
												),
			);

			// You can make changes via this filter:
			$icons = api()->apply_filters('admin_icons', $icons);

			// Remove any that don't need showing, though.
			$tmp = array();

			// We will also generate a mapping for the ID's of the links, which
			// will be used to generate the link tree.
			$icon_map = array(
										'data' => array(),
										'index' => array(),
									);
			foreach($icons as $header => $icon)
			{
				foreach($icon as $key => $info)
				{
					if(empty($info['show']) || empty($info['id']) || empty($info['href']) || empty($info['label']))
					{
						unset($icon[$key]);

						continue;
					}

					// Add this location to the index, this way we can generate the
					// link tree.
					$icon_map['data'][$info['id']] = array(
																						 'href' => $info['href'],
																						 'title' => !empty($info['title']) ? $info['title'] : '',
																						 'label' => !empty($info['tree_label']) ? $info['tree_label'] : $info['label'],
																					 );
					$icon_map['index'][$info['id']] = array($header);

					// Does this have any children?
					if(isset($info['children']) && count($info['children']) > 0)
					{
						foreach($info['children'] as $c_key => $child)
						{
							// Make sure this one should be displayed.
							if(empty($child['show']) || empty($child['id']) || empty($child['href']) || empty($child['label']))
							{
								// Nope!
								unset($info['children'][$c_key]);

								continue;
							}

							$icon_map['data'][$child['id']] = array(
																									'href' => $child['href'],
																									'title' => !empty($child['title']) ? $child['title'] : '',
																									'label' => !empty($child['tree_label']) ? $child['tree_label'] : $child['label'],
																								);
							$icon_map['index'][$child['id']] = array($header, $info['id']);
						}
					}
				}

				if(count($icon) > 0)
				{
					$tmp[$header] = $icon;
				}
			}

			// Put it back :P
			$icons = $tmp;
		}

		// You could be making an ajax request (Oh yeah, did I mention any ajax
		// requests dealing with control panel stuff should be prepended by
		// action=admin&sa=ajax{rest of your stuff}? It should be!!!)
		if(member()->can('access_admin_cp') && substr($_SERVER['QUERY_STRING'], 0, 20) == 'action=admin&sa=ajax')
		{
			// So it's an ajax request!
			// Is an administrative prompt required?
			if(admin_prompt_required())
			{
				// You sending us a password? Cool!
				if(isset($_POST['admin_password']))
				{
					// Did it work?
					if(!admin_prompt_password($_POST['admin_password']))
					{
						// Nope :(
						echo json_encode(array('error' => l('Incorrect password'), 'admin_prompt_required' => true));
						exit;
					}
				}
				else
				{
					// Yes, it is.
					echo json_encode(array('error' => l('Your session has timed out'), 'admin_prompt_required' => true));
					exit;
				}
			}

			if(isset($_POST['request']) && $_POST['request'] == 'set' && !empty($_POST['sid']) && $_POST['sid'] == member()->session_id() && in_array('group_id', array_keys($_POST)) && strlen($_POST['group_id']) == 40)
			{
				$groups_state = member()->data('admin_groups_state', 'array', array());

				$groups_state[$_POST['group_id']] = !empty($_POST['state']) ? 1 : 0;

				// We require the Members class to update their data.
				$members = api()->load_class('Members');

				$members->update(member()->id(), array(
																					 'data' => array(
																											 'admin_groups_state' => serialize($groups_state),
																										 ),
																				 ));

				echo json_encode(true);
				exit;
			}
		}
		else
		{
			// Not allowed to access the Admin Control Panel?
			if(!member()->can('access_admin_cp'))
			{
				// There's a function for that. :P
				admin_access_denied();
			}

			// We may require you to enter a password, for security reasons!
			admin_prompt_password();
		}

		theme()->add_link(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => theme()->url(). '/style/index.css'));
		api()->add_filter('admin_theme_container_id', create_function('$element_id', '
																										return \'content-container\';'));

		// Load some default notifications and what not.
		api()->add_filter('admin_notifications', 'admin_load_notifications');

		// The important notifications is an alert that there is a system
		// update available. But if the user cannot update the system, there is
		// no point in telling them.
		if(member()->can('update_system'))
		{
			api()->add_filter('admin_important_notifications', 'admin_important_notifications');
		}

		// Do we have any notifications?
		$admin_notifications = api()->apply_filters('admin_notifications', array());
		$GLOBALS['notifications'] = array();

		if(is_array($admin_notifications) && count($admin_notifications) > 0)
		{
			// Yes, there are notifications.
			foreach($admin_notifications as $message)
			{
				// A message is required.
				if(empty($message['message']))
				{
					continue;
				}

				$GLOBALS['notifications'][] = array(
																				'attr_class' => !empty($message['attr_class']) ? $message['attr_class'] : 'notification-message',
																				'message' => $message['message'],
																			);
			}
		}

		theme()->add_js_var('notifications', $GLOBALS['notifications']);

		// The section menus will come in handy.
		require(coredir. '/admin/admin_section_menus.php');

		// You can make changes to the theme and what not now :)
		api()->run_hooks('admin_prepend_authenticated', array('ajax' => substr($_SERVER['QUERY_STRING'], 0, 20) == 'action=admin&sa=ajax'));
	}
}

if(!function_exists('admin_prompt_required'))
{
	/*
		Function: admin_prompt_required

		Checks to see if the user needs to verify their session with
		their account password. Useful for AJAX kind of things, ;).

		Parameters:
			bool $force_check

		Returns:
			bool - Returns true if the user needs to supply their password
						 in order to continue, false if not.

		Note:
			This function is overloadable.
	*/
	function admin_prompt_required($force_check = false)
	{
		static $cache = null;

		if($cache === null || $force_check === true)
		{
			// Check to see if your last check has now timed out, quite simple
			// really! But if you for some strange reason have it disabled,
			// nevermind!
			if(!settings()->get('disable_admin_security', 'bool', false) && (empty($_SESSION['admin_password_prompted']) || ((int)$_SESSION['admin_password_prompted'] + (settings()->get('admin_login_timeout', 'int', 15) * 60)) < time_utc()))
			{
				$cache = true;
				return true;
			}

			// Your good, for now!
			$cache = false;
			return false;
		}
		else
		{
			return $cache;
		}
	}
}

if(!function_exists('admin_prompt_password'))
{
	/*
		Function: admin_prompt_password

		Unlike <admin_prompt_required>, this function actually prompts
		for the password itself. A form is shown where the user can enter
		their password, or, a parameter can be passed containing their
		password (plain text, SHA-1'd or secured) for use by AJAX means.
		Hint hint ;)

		Parameters:
			string $password - The users plain text or SHA-1'd,
												 if left blank, the form is displayed.

		Returns:
			mixed - This function returns nothing if password is null,
							otherwise it returns a bool, true if the password
							was correct, false if not.

		Note:
			This function is overloadable.
	*/
	function admin_prompt_password($password = null)
	{
		// Is it time for you to re-enter your password?
		if(admin_prompt_required())
		{
			// Did you supply a password?
			if($password !== null)
			{
				$errors = array();
				if(admin_prompt_handle(array('admin_verification_password' => $password), $errors))
				{
					return true;
				}
				else
				{
					return false;
				}
			}

			// Generate the login form.
			admin_prompt_generate_form();

			$form = api()->load_class('Form');

			// Has the form been submitted? Process it!
			if(isset($_POST['proc_login']))
			{
				$success = $form->process('admin_prompt');

				// Did they pass?
				if(!empty($success))
				{
					// Yup, no need to continue!
					return;
				}
			}

			theme()->set_title(l('Log In'));

			api()->context['form'] = $form;

			theme()->render('admin_prompt_password');

			// Don't execute anything else.
			exit;
		}
	}
}

if(!function_exists('admin_prompt_generate_form'))
{
	/*
		Function: admin_prompt_generate_form

		Generates the form which displays the administrative security prompt.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_prompt_generate_form()
	{
		// We need a couple things.
		theme()->add_link(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => theme()->url(). '/style/login.css'));
		api()->add_filter('admin_theme_container_id', create_function('$element_id', '
																										return \'login-box\';'));

		// Create the form so you can enter your password.
		$form = api()->load_class('Form');

		// Create our authentication form.
		$form->add('admin_prompt', array(
																 'action' => member()->is_guest() ? api()->apply_filters('login_url', baseurl. '/index.php?action=login2') : '',
																 'callback' => 'admin_prompt_handle',
																 'id' => 'login-form',
																 'submit' => l('Log in'),
															 ));

		$form->current('admin_prompt');

		// Add the username input, but that may just be disabled anyways.
		$form->add_input(array(
											 'name' => 'member_name',
											 'label' => l('Username'),
											 'type' => 'string',
											 'default_value' => member()->is_logged() ? member()->name() : '',
											 'disabled' => member()->is_logged(),
										 ));

		// Now for their password.
		$form->add_input(array(
											 'name' => 'member_pass',
											 'label' => l('Password'),
											 'type' => 'password',
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
	}
}

if(!function_exists('admin_prompt_handle'))
{
	/*
		Function: admin_prompt_handle

		Handles the verification of the supplied administrator password.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns true if the supplied password was correct, false if
						 not.

		Note:
			This function is overloadable.
	*/
	function admin_prompt_handle($data, &$errors = array())
	{
		global $func;

		// No password? Pfft.
		if(empty($data['member_pass']) || $func['strlen']($data['member_pass']) == 0)
		{
			$errors[] = l('Please enter your password.');
			return false;
		}

		// The Members class has a useful method called authenticate :)
		$members = api()->load_class('Members');

		// Pretty simple to do. There are a couple hooks in that method, fyi.
		if($members->authenticate(member()->name(), $data['member_pass']))
		{
			// Set the last time you verified in your session information ;)
			$_SESSION['admin_password_prompted'] = time_utc();

			return true;
		}
		else
		{
			$errors[] = l('Incorrect password supplied.');
			return false;
		}
	}
}

if(!function_exists('admin_access_denied'))
{
	/*
		Function: admin_access_denied

		Shows an error screen denying the member access to the page they
		requested.

		Parameters:
			string $title - The title of the page, defaults to Access denied
			string $message - The error message to display, defaults to "Sorry,
												but you are not allowed to access the page you
												have requested."

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_access_denied($title = null, $message = null)
	{
		// But hold on! If the user isn't logged in, why don't we show a log in
		// form.
		if(member()->is_guest())
		{
			admin_prompt_generate_form();
			$form = api()->load_class('Form');

			// Most of the form was generated... Except one last thing!
			$form->add_input(array(
												 'name' => 'redir_to',
												 'label' => true,
												 'type' => 'hidden',
												 'default_value' => $_SERVER['QUERY_STRING'],
											 ));

			theme()->set_title(l('Log In'));

			api()->context['form'] = $form;

			theme()->render('admin_prompt_password');
		}
		else
		{
			// No title? Just use a generic one, then.
			if(empty($title))
			{
				$title = l('Access Denied');
			}

			// No special message? We will take it that they just don't have the
			// right to access whatever it is you are wanting to block them from :P
			if(empty($message))
			{
				$message = l('Sorry, but you do not have sufficient permissions to access the page requested.');
			}

			// We need a couple things.
			theme()->add_link(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => theme()->url(). '/style/login.css'));
			api()->add_filter('admin_theme_container_id', create_function('$element_id', '
																											return \'login-box\';'));

			theme()->set_title($title);

			api()->context['error_title'] = $title;
			api()->context['error_message'] = $message;
			api()->context['cp_access_denied'] = true;

			theme()->render('admin_access_denied');
		}

		// Either way, we won't continue executing.
		exit;
	}
}

if(!function_exists('admin_current_area'))
{
	/*
		Function: admin_current_area

		Will both set and return the current area being viewed within the
		Administrative Control Panel.

		Parameters:
			string $area_id - The string which identifies the current area being
												viewed by the user.

		Returns:
			mixed - If $area_id is left empty (null), then a string containing the
							current area will be returned, otherwise nothing will be
							returned.

		Note:
			This function is overloadable.
	*/
	function admin_current_area($area_id = null)
	{
		// Are you setting the current area?
		if(!empty($area_id))
		{
			// Yup...
			$GLOBALS['admin_current_area'] = $area_id;
		}
		else
		{
			return isset($GLOBALS['admin_current_area']) ? $GLOBALS['admin_current_area'] : null;
		}
	}
}

/*
	Function: admin_link_tree

	Generates a link tree to display in the control panel.

	Parameters:
		bool $displaying_login - Whether the log in form is being displayed.

	Returns:
		string - Returns a string containing the link tree.
*/
function admin_link_tree($displaying_login = false)
{
	// If the log in form is being displayed, show a different link tree.
	if(!empty($displaying_login))
	{
		return '<a href="'. baseurl('index.php?action=admin'). '">'. l('Control Panel'). '</a> &raquo; '. l('Log In');
	}

	// We will always at least show the Control Panel link.
	$links[] = '<a href="'. baseurl('index.php?action=admin'). '">'. l('Control Panel'). '</a>';

	// We want to make sure we know where we're at.
	$area_id = admin_current_area();
	if(isset($GLOBALS['icon_map']['index'][$area_id]))
	{
		// Let's get the list of ID's that this location is within.
		$index = $GLOBALS['icon_map']['index'][$area_id];

		// Also go ahead and add the current ID to the list as well ;-).
		$index[] = $area_id;

		$length = count($index);
		for($i = 0; $i < $length; $i++)
		{
			$item = $index[$i];

			// The very first item is just a category title.
			if($i == 0)
			{
				$links[] = '<a href="'. baseurl('index.php?action=admin#'. urlencode(strtolower(str_replace(' ', '', $item)))). '" title="'. htmlchars($item). '">'. htmlchars($item). '</a>';
			}
			elseif(isset($GLOBALS['icon_map']['data'][$item]))
			{
				$item = $GLOBALS['icon_map']['data'][$item];

				$links[] = ($i + 1 != $length || admin_link_tree_add() !== null ? '<a href="'. $item['href']. '" title="'. $item['title']. '">' : ''). $item['label']. ($i + 1 != $length || admin_link_tree_add() !== null ? '</a>' : '');
			}
		}

		if(admin_link_tree_add() !== null)
		{
			$links[] = admin_link_tree_add();
		}
	}

	return implode(' &raquo; ', $links);
}

/*
	Function: admin_link_tree_add

	Allows the addition of one more item to be appended to the link tree
	displayed within the control panel.

	Parameters:
		string $label

	Returns:
		void - Nothing is returned by this function.
*/
function admin_link_tree_add($label = null)
{
	static $label_value = null;

	if(!empty($label))
	{
		$label_value = $label;
	}
	else
	{
		return $label_value;
	}
}

/*
	Function: admin_show_sidebar

	Determines whether or not the sidebar in the control panel should be
	displayed. This function can also be used to hide or show the sidebar as
	well.

	Parameters:
		bool $show - Whether or not to display the sidebar. If left blank, then
								 this function will return whether the sidebar should be
								 shown.

	Returns:
		bool - Returns whether the sidebar should be displayed in the control
					 panel.
*/
function admin_show_sidebar($show = null)
{
	static $show_sidebar = true;

	if($show !== null)
	{
		$show_sidebar = !empty($show);
	}

	return $show_sidebar;
}

/*
	Function: admin_load_notifications

	A function called upon by the admin_notifications filter. This function
	will return any default system notifications, such as available plugin
	and theme updates.

	Parameters:
		array $notifications - An array containing any notifications.

	Returns:
		array - Returns an array containing the notifications.
*/
function admin_load_notifications($notifications)
{
	// We only need to notify them of available plugin updates if they can
	// update them, of course!
	if(member()->can('manage_plugins'))
	{
		// So, do we have any notifications we need to add?
		$plugin_updates = settings()->get('plugin_updates', 'array', array());

		$tmp = array();
		foreach($plugin_updates as $plugindir => $version)
		{
			$plugin_info = plugin_load(plugindir. '/'. basename($plugindir));

			// Make sure the plugin even exists.
			if($plugin_info === false)
			{
				continue;
			}

			$tmp[] = '<a href="'. baseurl('index.php?action=admin&amp;sa=plugins_manage'. (!isset($_GET['go']) ? '&amp;go=1' : ''). '#p'. sha1($plugindir)). '" title="'. l('v%s of this plugin is available', htmlchars($version)). '">'. $plugin_info['name']. '</a>';
		}

		// So, were there any plugin updates?
		if(count($tmp) > 0)
		{
			// Yup, there is!
			if(count($tmp) > 1)
			{
				// Let's make this purty!
				$last = $tmp[count($tmp) - 1];
				unset($tmp[count($tmp) - 1]);

				$updates = implode(', ', $tmp). ' '. l('and'). ' '. $last;
			}
			else
			{
				$updates = $tmp[0];
			}

			$notifications[] = array(
													 'attr_class' => 'alert-notification',
													 'message' => l('The following plugins have updates available: %s.', $updates),
												 );
		}
	}

	// The user can update themes if they are allowed to select the current
	// theme or manage themes.
	if(member()->can('manage_themes') || member()->can('select_theme'))
	{
		// Now, what about themes?
		$theme_updates = settings()->get('theme_updates', 'array', array());

		$tmp = array();
		foreach($theme_updates as $themedir => $version)
		{
			$theme_info = theme_load(themedir. '/'. basename($themedir));

			// Make sure the theme exists.
			if($theme_info === false)
			{
				continue;
			}

			$tmp[] = '<a href="'. baseurl('index.php?action=admin&amp;sa=themes'. (!isset($_GET['go']) ? '&amp;go=1' : ''). '#t'. sha1($themedir)). '" title="'. l('v%s of this theme is available', htmlchars($version)). '">'. $theme_info['name']. '</a>';

		}

		if(count($tmp) > 0)
		{
			if(count($tmp) > 1)
			{
				// Let's make this purty!
				$last = $tmp[count($tmp) - 1];
				unset($tmp[count($tmp) - 1]);

				$updates = implode(', ', $tmp). ' '. l('and'). ' '. $last;
			}
			else
			{
				$updates = $tmp[0];
			}

			$notifications[] = array(
													 'attr_class' => 'alert-notification',
													 'message' => l('The following themes have updates available: %s.', $updates),
												 );
		}
	}

	return $notifications;
}

/*
	Function: admin_important_notifications

	A function called upon by the admin_important_notifications filter. This
	function will return any important notifications, such as a system update.

	Parameters:
		array $notifications - An array containing notifications.

	Returns:
		array - Returns an array containing any notifications.
*/
function admin_important_notifications($notifications)
{
	// Is there a system update available?
	$latest_version = settings()->get('system_latest_version', 'string', settings()->get('version', 'string'));

	if($latest_version !== false && compare_versions($latest_version, settings()->get('version', 'string'), '>'))
	{
		// Yes, there is!
		$notifications[] = array(
												 'attr_class' => 'alert-message',
												 'message' => '<p>'. l('There is an update available for SnowCMS. Please <a href="%s">update to v%s now</a>.', baseurl('index.php?action=admin&amp;sa=update'), $latest_version). '</p>',
											 );
	}

	return $notifications;
}
?>