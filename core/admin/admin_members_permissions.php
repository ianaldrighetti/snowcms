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

// Title: Control Panel - Member - Permissions

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

			// Now how about the total number of permissions assigned, and to each
			// section (denied, disallowed or allowed). If this isn't the
			// administrator group, which can do everything!
			if($group_id != 'administrator')
			{
				$result = db()->query('
										SELECT
											status, COUNT(*) AS assigned
										FROM {db->prefix}permissions
										WHERE group_id = {string:group_id}
										GROUP BY status',
										array(
											'group_id' => $group_id,
										));

				// Save the totals.
				while($row = $result->fetch_assoc())
				{
					if($row['status'] <= 1 && $row['status'] >= -1)
					{
						$group['assigned'][$row['status'] == -1 ? 'deny' : ($row['status'] == 0 ? 'disallow' : 'allow')] = $row['assigned'];
					}
				}
			}
			else
			{
				$group['assigned'] = array(
															 'deny' => '&ndash;',
															 'disallow' => '&ndash;',
															 'allow' => '&ndash;',
														 );
			}

			// Add this group to the list.
			$group_list[] = $group;
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

			api()->context['group_id'] = $group_id;
			api()->context['group_name'] = $group_id != 'guest' ? htmlchars(api()->return_group($group_id)) : l('Guest');
			api()->context['form'] = $form;

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

		// Now is your time to add your permission!
		$permissions = array(
										 array(
											 'permission' => 'manage_system_settings', // The permission in the table.
											 'label' => l('Manage system settings:'), // The label of the field
											 'subtext' => '', // Subtext too, if you want.
										 ),
										 array(
											 'permission' => 'manage_themes',
											 'label' => l('Manage themes:'),
											 'subtext' => l('Allow the group to select the site theme, download and upload themes to the site.'),
										 ),
										 array(
											 'permission' => 'update_system',
											 'label' => l('Update system:'),
											 'subtext' => l('Whether or not they can update SnowCMS.'),
										 ),
										 array(
											 'permission' => 'view_error_log',
											 'label' => l('View error log:'),
										 ),
										 array(
											 'permission' => 'add_new_member',
											 'label' => l('Add a new member:'),
											 'subtext' => l('Allow them to add a new member through the control panel (keep in mind they would be able to make accounts administrators!).'),
										 ),
										 array(
											 'permission' => 'manage_members',
											 'label' => l('Manage members:'),
											 'subtext' => l('Allow them to manage members, which would allow them to also make accounts administrators.'),
										 ),
										 array(
											 'permission' => 'search_members',
											 'label' => l('Search for members:'),
											 'subtext' => l('Through the control panel.'),
										 ),
										 array(
											 'permission' => 'manage_member_settings',
											 'label' => l('Manage member settings:'),
										 ),
										 array(
											 'permission' => 'manage_permissions',
											 'label' => l('Manage permissions:'),
											 'subtext' => l('Not a very good idea.'),
										 ),
										 array(
											 'permission' => 'add_plugins',
											 'label' => l('Add a new plugin:'),
										 ),
										 array(
											 'permission' => 'manage_plugins',
											 'label' => l('Manage plugins:'),
											 'subtext' => l('Which includes activating, deactivating and updating of plugins.'),
										 ),
										 array(
											 'permission' => 'manage_plugin_settings',
											 'label' => l('Manage plugin settings:'),
											 'subtext' => l('Allow the group to manage miscellaneous plugin settings (Not recommended, as plugins can add various settings).'),
										 ),
										 array(
											 'permission' => 'view_other_profiles',
											 'label' => l('View other profiles:'),
											 'subtext' => l('Should they be allowed to view other members profiles? (Not recommended for guests)'),
										 ),
										 array(
											 'permission' => 'edit_other_profiles',
											 'label' => l('Edit other profiles:'),
											 'subtext' => l(''),
										 ),
									 );

		// So yeah, add your permissions!
		$permissions = api()->apply_filters('member_group_permissions', $permissions);

		if(is_array($permissions) && count($permissions))
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

			foreach($permissions as $permission)
			{
				if(empty($permission['permission']))
				{
					// We really kinda need the permissions identifier.
					continue;
				}

				$form->add_input(array(
													 'name' => $permission['permission'],
													 'type' => 'select',
													 'label' => isset($permission['label']) ? $permission['label'] : '',
													 'subtext' => isset($permission['subtext']) ? $permission['subtext'] : '',
													 'options' => array(
																					-1 => l('Deny'),
																					0 => l('Disallow'),
																					1 => l('Allow'),
																				),
													 'default_value' => isset($loaded[$permission['permission']]) ? $loaded[$permission['permission']] : 0,
												 ));
			}
		}
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
			// You can add more DENIED permissions via the guest_denied_permissions hook ;)
			// (Sorry, but I will not allow plugins to remove denied permissions, at least built in functionality)
			$denied = array_merge(array('manage_system_settings', 'manage_themes', 'update_system', 'view_error_log', 'add_new_member', 'manage_members', 'search_members', 'manage_member_settings', 'manage_permissions', 'add_plugins', 'manage_plugins', 'manage_plugin_settings', 'edit_other_profiles'), api()->apply_filters('denied_guest_permissions', array()));

			foreach($denied as $deny)
			{
				// Deny it by giving it a -1.
				$data[$deny] = -1;
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

		// This will force the form to use the data we just inserted into the
		// permissions table.
		$form->clear_data();

		api()->add_hook(strtolower($group_id). '_permissions_messages', create_function('&$value', '
																																			$value[] = l(\'%s permissions have been updated successfully.\', ($_GET[\'grp\'] == \'guest\' ? l(\'Guest\') : htmlchars(api()->return_group($_GET[\'grp\']))));'));

		return true;
	}
}
?>