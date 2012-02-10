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

// Title: Member Group Permissions

if(!function_exists('admin_members_manage_permissions'))
{
	/*
		Function: admin_members_manage_permissions

		An interface for the management of group permissions.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_members_manage_permissions()
	{
		api()->run_hooks('admin_members_manage_permissions');

		// Do you have the permission to edit permissions!?
		if(!member()->can('manage_permissions'))
		{
			admin_access_denied();
		}

		admin_current_area('members_permissions');

		theme()->set_title(l('Manage Permissions'));

		// Add the guest group.
		api()->context['groups'] = array_merge(array('guest' => l('Guest')), api()->return_group());

		$group_list = array();
		$count = 0;
		$group_ids = array();
		foreach(api()->context['groups'] as $group_id => $group_name)
		{
			$group = array(
								 'id' => htmlchars($group_id),
								 'name' => htmlchars($group_name),
								 'href' => $group_id == 'administrator' ? false : baseurl. '/index.php?action=admin&amp;sa=members_permissions&amp;grp='. urlencode($group_id),
								 'members' => $group_id == 'guest' ? '&ndash;' : 0,
								 'assigned' => array(
																 'deny' => 0,
																 'disallow' => 0,
																 'allow' => 0,
															 ),
								'position' => $count++,
							);

			// How many members are in this group? Of course, none can be in the
			// Guest group, so skip that.
			if($group_id != 'guest')
			{
				$result = db()->query('
										SELECT
											COUNT(*)
										FROM {db->prefix}members
										WHERE FIND_IN_SET({string:group_id}, member_groups)',
										array(
											'group_id' => $group_id,
										));

				list($group['members']) = $result->fetch_row();

				// Format the number.
				$group['members'] = format_number($group['members']);
			}

			// The administrator group can do everything! Because they're awesome!
			if($group_id == 'administrator')
			{
				$group['assigned'] = array(
															 'deny' => '&ndash;',
															 'disallow' => '&ndash;',
															 'allow' => '&ndash;',
														 );
			}

			// Add this group to the list.
			$group_list[$group_id] = $group;
			$group_ids[] = $group_id;
		}

		// We want to get the count of permissions denied, disallowed and
		// denied.
		$result = db()->query('
								SELECT
									group_id, status, COUNT(*) AS assigned
								FROM {db->prefix}permissions
								GROUP BY status, group_id',
								array(
									'group_id' => $group_ids,
								));

		while($row = $result->fetch_assoc())
		{
			// Let's just make sure...
			if($row['group_id'] == 'administrator')
			{
				continue;
			}

			$group_list[$row['group_id']]['assigned'][$row['status'] == -1 ? 'deny' : ($row['status'] == 0 ? 'disallow' : 'allow')] = $row['assigned'];
		}


		// The template will be needing this.
		api()->context['group_list'] = $group_list;

		theme()->render('admin_members_manage_permissions');
	}
}

if(!function_exists('admin_members_manage_group_permissions'))
{
	/*
		Function: admin_members_manage_group_permissions

		An interface for actually editing group permissions.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_members_manage_group_permissions()
	{
		$group_id = $_GET['grp'];

		api()->run_hooks('admin_members_manage_group_permissions');

		if(!member()->can('manage_permissions'))
		{
			admin_access_denied();
		}

		admin_current_area('members_permissions');
		admin_link_tree_add(l('Managing Group'));

		// Check to see if the specified group even exists!
		if(!api()->return_group($group_id) && strtolower($group_id) != 'guest')
		{
			theme()->set_title(l('An Error Occurred'));

			api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/members_permissions-small.png" alt="" /> '. l('Group Not Found');
			api()->context['error_message'] = l('Sorry, but it appears that the group you have requested does not exist. <a href="%s" title="Back to Manage Permissions">Back to permissions management &raquo;</a>', baseurl. '/index.php?action=admin&amp;sa=members_permissions');

			theme()->render('error');
		}
		elseif($group_id == 'administrator')
		{
			theme()->set_title('An Error Occurred');

			api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/members_permissions-small.png" alt="" /> '. l('Cannot Edit Group');
			api()->context['error_message'] = l('Sorry, but the administrator group&#039;s permissions cannot be modified. <a href="%s" title="Back to Manage Permissions">Back to permissions management &raquo;</a>', baseurl. '/index.php?action=admin&amp;sa=members_permissions');

			theme()->render('error');
		}
		else
		{
			// Time to generate that form!
			admin_members_permissions_generate_form($group_id. '_permissions', $group_id);
			$form = api()->load_class('Form');

			if(!empty($_POST[$group_id. '_permissions']))
			{
				if(isset($_GET['ajax']))
				{
					// Using AJAX? Well aren't you Mr. Fancy Pants!
					echo $form->json_process($group_id. '_permissions');
					exit;
				}
				else
				{
					// Process the form! The boring way!
					$form->process($group_id. '_permissions');
				}
			}

			theme()->set_title(l('Managing %s Permissions', htmlchars(api()->return_group($group_id))));

			// Some CSS, please!
			theme()->add_link(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => theme()->url(). '/style/permissions.css'));

			api()->context['group_id'] = $group_id;
			api()->context['group_name'] = $group_id != 'guest' ? htmlchars(api()->return_group($group_id)) : l('Guest');
			api()->context['form'] = $form;

			// Do we need to set a message?
			if(!empty($_GET['message']))
			{
				api()->context['message'] = l('Permissions updated successfully.');
			}

			admin_link_tree_add(l('Editing &quot;%s&quot; Permissions', api()->context['group_name']));

			theme()->render('admin_members_manage_group_permissions');
		}
	}
}

if(!function_exists('admin_members_permissions_generate_form'))
{
	/*
		Function: admin_members_permissions_generate_form

		Generates the form which displays the permissions editor.

		Parameters:
			string $form_name - The name of the form.
			string $group_id - The id of the group being edited.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_members_permissions_generate_form($form_name, $group_id)
	{
		$form = api()->load_class('Form');

		// Add our form, before we do anything else, of course!
		$form->add($form_name, array(
														 'action' => baseurl. '/index.php?action=admin&sa=members_permissions&grp='. urlencode($_GET['grp']),
														 'callback' => 'admin_members_permissions_handle',
														 'method' => 'post',
														 'submit' => l('Save'),
													 ));

		$form->current($form_name);

		// Let's define all the default permissions available.
		$permissions = array(
										 'system' => array(
																	 'label' => l('System'),
																	 'subtext' => l('Permissions relating to the core functionality in SnowCMS.'),
																	 'permissions' => array(
																											array(
																												'id' => 'manage_system_settings',
																												'label' => l('Manage system settings'),
																												'subtext' => l('Allows them to modify such settings as website name, date & time, and other various settings.'),
																											),
																											array(
																												'id' => 'update_system',
																												'label' => l('Apply updates'),
																												'subtext' => l('Enables them to apply any available system updates.'),
																											),
																											array(
																												'id' => 'view_error_log',
																												'label' => l('View error log'),
																											),
																										),
																 ),
										 'members' => array(
																		'label' => l('Members'),
																		'subtext' => l('Member management and other profile permissions'),
																		'permissions' => array(
																											 array(
																												 'id' => 'add_new_member',
																												 'label' => l('Add new members'),
																												 'subtext' => l('Allows them to create new members (keep in mind they would be able to create administrative accounts).'),
																											 ),
																											 array(
																												 'id' => 'manage_members',
																												 'label' => l('Manage members'),
																												 'subtext' => l('Managing members includes creating, activating and modifying accounts, along with making accounts administrators.'),
																											 ),
																											 array(
																												 'id' => 'manage_member_settings',
																												 'label' => l('Manage member settings'),
																												 'subtext' => l('Includes settings such as registration type, disallowed usernames and emails, and password requirements.'),
																											 ),
																											 array(
																												 'id' => 'manage_permissions',
																												 'label' => l('Manage permissions'),
																												 'subtext' => l('Allows users of the group to do what you\'re doing right now.'),
																											 ),
																											 array(
																												 'id' => 'view_other_profiles',
																												 'label' => l('View other\'s profiles'),
																												 'subtext' => l('Allows them to view the profiles of other users.'),
																											 ),
																										 ),
																	),
										 'themes' => array(
																	 'label' => l('Themes'),
																	 'subtext' => l('Theme management permissions'),
																	 'permissions' => array(
																											array(
																												'id' => 'select_theme',
																												'label' => l('Select theme'),
																												'subtext' => l('Allows them to select the current theme and install any theme updates.'),
																											),
																											array(
																												'id' => 'manage_widgets',
																												'label' => l('Manage widgets'),
																											),
																											array(
																												'id' => 'manage_themes',
																												'label' => l('Manage Themes'),
																												'subtext' => l('This permission includes installing, updating and selecting themes, along with widget management.'),
																											),
																										),
																 ),
										 'plugins' => array(
																		'label' => l('Plugins'),
																		'subtext' => l('Plugin management permissions'),
																		'permissions' => array(
																											 array(
																												 'id' => 'add_plugins',
																												 'label' => l('Add new plugin'),
																												 'subtext' => l('Allows them to upload and install a new plugin.'),
																											 ),
																											 array(
																												 'id' => 'manage_plugins',
																												 'label' => l('Manage plugins'),
																												 'subtext' => l('Plugin management includes adding, updating, activating and deactivating plugins.'),
																											 ),
																											 array(
																												 'id' => 'manage_plugin_settings',
																												 'label' => l('Manage plugin settings'),
																												 'subtext' => l('These settings are any settings that activated plugins may add to the plugin settings page.'),
																											 ),
																										 ),
																	),
									 );

		// If you have any permissions you want to add, now is your chance!
		$permissions = api()->apply_filters('member_group_permissions', $permissions);

		if(is_array($permissions))
		{
			// Time to load up the permissions in the database, or elsewhere.
			$loaded = null;
			api()->run_hooks('load_permissions', array(&$loaded, $group_id));

			// Oh, I need to do it?
			if($loaded === null)
			{
				// They are in the database ;)
				$result = db()->query('
										SELECT
											permission, status
										FROM {db->prefix}permissions
										WHERE group_id = {string:group_id}',
										array(
											'group_id' => $group_id,
										), 'load_permissions_query');

				$loaded = array();
				while($row = $result->fetch_assoc())
				{
					$loaded[$row['permission']] = $row['status'];
				}
			}

			foreach($permissions as $group_id => $group)
			{
				foreach($group['permissions'] as $permission)
				{
					if(empty($permission['id']))
					{
						// We really kinda need the permissions identifier.
						continue;
					}

					$form->add_input(array(
														 'name' => $permission['id'],
														 'type' => 'select',
														 'label' => isset($permission['label']) ? $permission['label'] : '',
														 'subtext' => isset($permission['subtext']) ? $permission['subtext'] : '',
														 'options' => array(
																						-1 => l('Deny'),
																						0 => l('Disallow'),
																						1 => l('Allow'),
																					),
														 'default_value' => isset($loaded[$permission['id']]) ? $loaded[$permission['id']] : 0,
													 ));
				}
			}
		}

		api()->context['permissions'] = $permissions;
	}
}

if(!function_exists('admin_members_permissions_handle'))
{
	/*
		Function: admin_members_permissions_handle

		Handles the form of permissions editor.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function admin_members_permissions_handle($data, &$errors = array())
	{
		$group_id = strtolower($_GET['grp']);

		// We will need to update the value in the form.
		$form = api()->load_class('Form');

		// Sorry guests, there are certain permissions you just cannot have!!!
		if($group_id == 'guest')
		{
			// You can add any permissions that are ALLOWED via the
			// allowed_guest_permissions hook -- otherwise if the permission is
			// not in the list it will be denied.
			$allowed = array_merge(array('view_other_profiles'), api()->apply_filters('allowed_guest_permissions', array()));

			foreach($data as $permission => $value)
			{
				// Is this permission in the allowed list?
				if(!in_array($permission, $allowed))
				{
					// No, it's not, so deny it.
					$data[$permission] = -1;
				}
			}
		}

		// Get all of our rows built :-).
		$rows = array();
		foreach($data as $permission => $status)
		{
			// Ignore the security token.
			if($permission == $group_id. '_permissions_token')
			{
				continue;
			}

			$rows[] = array($group_id, $permission, $status);
		}

		// Now save the new permissions.
		db()->insert('replace', '{db->prefix}permissions',
			array(
				'group_id' => 'string-128', 'permission' => 'string-128', 'status' => 'int',
			),
			$rows,
			array('group_id', 'permission'), 'permissions_handle_query');

		// We saved the changes, let's be sure to show it.
		redirect(baseurl('index.php?action=admin&sa=members_permissions&grp='. urlencode($_GET['grp']). '&message=1'));
	}
}
?>