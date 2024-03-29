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

// Title: Manage Members

if(!function_exists('admin_members_manage'))
{
	/*
		Function: admin_members_manage

		Provides the interface for the management of members.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_members_manage()
	{
		api()->run_hooks('admin_members_manage');

		// How about managing members? Can you do that?
		if(!member()->can('manage_members'))
		{
			// You can't handle managing members! Or so someone thinks ;)
			admin_access_denied();
		}

		// Generate our table ;)
		admin_members_manage_generate_table();

		admin_current_area('members_manage');

		theme()->set_title(l('Manage Members'));

		theme()->add_js_var('delete_confirm', l('Are you sure you want to delete the selected members?'. "\r\n". 'This cannot be undone!'));
		theme()->add_js_file(array('src' => themeurl. '/default/js/members_manage.js'));

		api()->context['table'] = api()->load_class('Table');

		theme()->render('admin_members_manage');
	}
}

if(!function_exists('admin_members_manage_generate_table'))
{
	/*
		Function: admin_members_manage_generate_table

		Generates the table which displays currently existing members.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_members_manage_generate_table()
	{
		$table = api()->load_class('Table');

		// There are a couple options we will only want to show when certain
		// filters are applied.
		$action_options = array(
												'activate' => 'Activate',
												'deactivate' => 'Deactivate',
												'delete' => 'Delete',
											);

		if(isset($_GET['filter']))
		{
			// Such as if they are viewing the pending activation members, we will
			// allow them to resend the activation emails.
			if($_GET['filter'] == 'unactivated')
			{
				$action_options['resend'] = l('Resend Activation');
			}

			// But we don't need to show the deactivate option if they are viewing
			// already unactivated accounts.
			if($_GET['filter'] == 'unactivated' || $_GET['filter'] == 'awaiting')
			{
				unset($action_options['deactivate']);
			}
		}

		$table->add('admin_members_manage_table', array(
																								'db_query' => '
																																SELECT
																																	member_id, member_name, display_name, member_email, member_groups, member_registered, member_activated
																																FROM {db->prefix}members',
																								'db_vars' => array(),
																								'callback' => 'admin_members_manage_table_handle',
																								'primary' => 'member_id',
																								'sort' => array('member_id', 'desc'),
																								'base_url' => baseurl. '/index.php?action=admin&sa=members_manage',
																								'cellpadding' => '4px',
																								'options' => $action_options,
																								'filters' => array(
																															 'activated' => array(
																																								'label' => l('Activated'),
																																								'title' => l('View only activated members'),
																																								'where' => 'member_activated = 1',
																																							),
																															 'unactivated' => array(
																																									'label' => l('Pending Activation'),
																																									'title' => l('View only unactivated members who have yet to activate their account via email'),
																																									'where' => 'member_activated = 0 AND member_acode != \'admin_approval\'',
																																								),
																															 'awaiting' => array(
																																							 'label' => l('Awaiting Approval'),
																																							 'title' => l('View only unactivated members awaiting administrative approval'),
																																							 'where' => 'member_activated = 0 AND (member_acode = \'admin_approval\' OR member_acode = \'\')',
																																						 ),
																														 ),
																							));

		// Their member id!
		$table->add_column('admin_members_manage_table', 'member_id', array(
																																		'column' => 'member_id',
																																		'label' => l('ID'),
																																		'title' => l('Member ID'),
																																	));

		// Name too!
		$table->add_column('admin_members_manage_table', 'member_name', array(
																																			'column' => 'display_name',
																																			'label' => l('Member name'),
																																			'title' => l('Member name'),
																																			'function' => create_function('$row', '
																																											return l(\'<a href="%s/index.php?action=profile&amp;id=%s" title="Edit %s\\\'s account">%s</a>\', baseurl, $row[\'member_id\'], $row[\'display_name\'], $row[\'display_name\']);'),
																																		));

		// How about that email? :P
		$table->add_column('admin_members_manage_table', 'member_email', array(
																																			 'column' => 'member_email',
																																			 'label' => l('Email address'),
																																		 ));

		// Is their account activated..?
		$table->add_column('admin_members_manage_table', 'member_activated', array(
																																					 'column' => 'member_activated',
																																					 'label' => l('Activated'),
																																					 'function' => create_function('$row', '
																																													 return $row[\'member_activated\'] == 0 ? l(\'No\') : l(\'Yes\');'),
																																				 ));

		// Registered date?
		$table->add_column('admin_members_manage_table', 'member_registered', array(
																																						'column' => 'member_registered',
																																						'label' => l('Registered'),
																																						'function' => create_function('$row', '
																																														return timeformat($row[\'member_registered\']);'),
																																					));
	}
}

if(!function_exists('admin_members_manage_table_handle'))
{
	/*
		Function: admin_members_manage_table_handle

		Handles the option selected in the options list of the generated table.

		Parameters:
			string $action - The action wanting to be executed.
			array $selected - The selected members to perform the action on.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_members_manage_table_handle($action, $selected)
	{
		// No point on executing anything if nothing was selected.
		if(!is_array($selected) || count($selected) == 0)
		{
			return;
		}

		// Activating accounts?
		if($action == 'activate')
		{
			// A different member system?
			$handled = false;
			api()->run_hooks('admin_members_manage_handle_activate', array(&$handled, 'activate', $selected));

			// So do we need to do it ourselves?
			if(empty($handled))
			{
				// Maybe we need to send them welcome emails (if administrative approval
				// was on at the time of their registration).
				$members = api()->load_class('Members');
				$members->load($selected);
				$members_info = $members->get($selected);

				if(count($members_info) > 0)
				{
					// Their activation code is admin_approval if they need an email.
					$send = array();
					foreach($members_info as $member_info)
					{
						if($member_info['acode'] == 'admin_approval')
						{
							// So they will need one!
							$send[] = $member_info['id'];
						}
					}

					// Did any need it..?
					if(count($send) > 0)
					{
						// Yup... The function to send them is in the register.php file.
						if(!function_exists('register_send_welcome_email'))
						{
							require_once(coredir. '/register.php');
						}

						// Simple :-), I like it!
						register_send_welcome_email($send);
					}
				}

				// Make them activated (delete their activation code, too)!
				db()->query('
					UPDATE {db->prefix}members
					SET member_activated = 1, member_acode = \'\'
					WHERE member_id IN({int_array:selected}) AND member_activated != 1',
					array(
						'selected' => $selected,
					), 'admin_members_activate_query');
			}
		}
		// Deactivating? Alright.
		elseif($action == 'deactivate')
		{
			$handled = false;
			api()->run_hooks('admin_members_manage_handle_deactivate', array(&$handled, 'deactivate', $selected));

			if(empty($handled))
			{
				// Turn 'em off!
				db()->query('
					UPDATE {db->prefix}members
					SET member_activated = 0
					WHERE member_id IN({int_array:selected}) AND member_activated != 0',
					array(
						'selected' => $selected,
					), 'admin_members_deactivate_query');
			}
		}
		// Resending the users their activation emails? Good idea!
		elseif($action == 'resend')
		{
			// We will need to load up all the information about these accounts,
			// as we want to make sure they aren't activated yet (or are pending
			// email activation)
			$members = api()->load_class('Members');
			$members->load($selected);

			// An error might prevent these messages from being sent, and we will
			// only want to log that error once.
			$email_failed = false;

			foreach($members->get($selected) as $member_info)
			{
				// So, does it meet the criteria?
				if(!$member_info['is_activated'] && $member_info['acode'] != 'admin_approval' && strlen($member_info['acode']) > 0)
				{
					// The register_send_email function in register.php will do
					// exactly what we want. But we just may need to fetch it.
					if(!function_exists('register_send_email'))
					{
						require(coredir. '/register.php');
					}

					// We need to regenerate their activation code...
					$members->update($member_info['id'], array(
																								 'member_acode' => sha1($members->rand_str(mt_rand(30, 40))),
																							 ));

					if(!register_send_email($member_info['id']))
					{
						// Looks like it didn't get sent, woops!
						$email_failed = true;
					}
				}
			}

			// Did something go wrong?
			if($email_failed)
			{
				trigger_error(l('An error occurred while trying to send users their activation email. This could indicate that the SMTP settings are incorrect or the server does not have the mail() function enabled.'), E_USER_WARNING);
			}
		}
		elseif($action == 'delete')
		{
			// No need for a hook here for other member systems, that's in <Members::delete>!

			// I guess you want to delete them. That's your problem ;)
			// Luckily, the Members class can handle all this!
			$members = api()->load_class('Members');
			$members->delete($selected);
		}
	}
}
?>