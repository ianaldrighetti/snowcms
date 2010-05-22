<?php
#########################################################################
#                             SnowCMS v2.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#                  Released under the GNU GPL v3 License                #
#                     www.gnu.org/licenses/gpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#                SnowCMS v2.0 began in November 2009                    #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 2.0                         #
#########################################################################

if(!defined('IN_SNOW'))
  die;

# Title: Control Panel - Members - Manage

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
    global $api, $base_url, $member, $theme, $theme_url;

    $api->run_hooks('admin_members_manage');

    # How about managing members? Can you do that?
    if(!$member->can('manage_members'))
    {
      # You can't handle managing members! Or so someone thinks ;)
      admin_access_denied();
    }

    # Generate our table ;)
    admin_members_manage_generate_table();
    $table = $api->load_class('Table');

    $theme->set_current_area('members_manage');

    $theme->set_title(l('Manage Members'));

    $theme->add_js_var('delete_confirm', l('Are you sure you want to delete the selected members?\\r\\nThis cannot be undone!'));
    $theme->add_js_file(array('src' => $theme_url. '/default/js/members_manage.js'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/members_manage-small.png" alt="" /> ', l('Manage Members'), '</h1>
  <p>', l('All existing members can be managed here, such as editing, deleting, approving, etc.'), '</p>';

    $table->show('admin_members_manage_table');

    $theme->footer();
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
    global $api, $base_url;

    $table = $api->load_class('Table');

    $table->add('admin_members_manage_table', array(
                                                'db_query' => '
                                                                SELECT
                                                                  member_id, member_name, display_name, member_email, member_groups, member_registered, member_activated
                                                                FROM {db->prefix}members',
                                                'db_vars' => array(),
                                                'callback' => 'admin_members_manage_table_handle',
                                                'primary' => 'member_id',
                                                'sort' => array('member_id', 'asc'),
                                                'base_url' => $base_url. '/index.php?action=admin&sa=members_manage',
                                                'cellpadding' => '4px',
                                                'options' => array(
                                                               'activate' => 'Activate',
                                                               'deactivate' => 'Deactivate',
                                                               'delete' => 'Delete',
                                                             ),
                                              ));

    # Their member id!
    $table->add_column('admin_members_manage_table', 'member_id', array(
                                                                    'column' => 'member_id',
                                                                    'label' => l('ID'),
                                                                    'title' => l('Member ID'),
                                                                  ));

    # Name too!
    $table->add_column('admin_members_manage_table', 'member_name', array(
                                                                      'column' => 'display_name',
                                                                      'label' => l('Member name'),
                                                                      'title' => l('Member name'),
                                                                      'function' => create_function('$row', '
                                                                                      global $base_url;

                                                                                      return l(\'<a href="%s/index.php?action=admin&amp;sa=members_manage&amp;id=%s" title="Edit %s\\\'s account">%s</a>\', $base_url, $row[\'member_id\'], $row[\'display_name\'], $row[\'display_name\']);'),
                                                                    ));

    # How about that email? :P
    $table->add_column('admin_members_manage_table', 'member_email', array(
                                                                       'column' => 'member_email',
                                                                       'label' => l('Email address'),
                                                                     ));

    # Is their account activated..?
    $table->add_column('admin_members_manage_table', 'member_activated', array(
                                                                           'column' => 'member_activated',
                                                                           'label' => l('Activated'),
                                                                           'function' => create_function('$row', '
                                                                                           return $row[\'member_activated\'] == 0 ? l(\'No\') : l(\'Yes\');'),
                                                                         ));

    # Registered date?
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
    global $api, $core_dir, $db;

    # No point on executing anything if nothing was selected.
    if(!is_array($selected) || count($selected) == 0)
      return;

    # Activating accounts?
    if($action == 'activate')
    {
      # A different member system?
      $handled = false;
      $api->run_hooks('admin_members_manage_handle_activate', array(&$handled, 'activate', $selected));

      # So do we need to do it ourselves?
      if(empty($handled))
      {
        # Maybe we need to send them welcome emails (if administrative approval
        # was on at the time of their registration).
        $members = $api->load_class('Members');
        $members->load($selected);
        $members_info = $members->get($selected);

        if(count($members_info) > 0)
        {
          # Their activation code is admin_approval if they need an email.
          $send = array();
          foreach($members_info as $member_info)
          {
            if($member_info['acode'] == 'admin_approval')
            {
              # So they will need one!
              $send[] = $member_info['id'];
            }
          }

          # Did any need it..?
          if(count($send) > 0)
          {
            # Yup... The function to send them is in the register.php file.
            if(!function_exists('register_send_welcome_email'))
            {
              require_once($core_dir. '/register.php');
            }

            # Simple :-), I like it!
            register_send_welcome_email($send);
          }
        }

        # Make them activated (delete their activation code, too)!
        $db->query('
          UPDATE {db->prefix}members
          SET member_activated = 1, member_acode = \'\'
          WHERE member_id IN({int_array:selected}) AND member_activated != 1',
          array(
            'selected' => $selected,
          ), 'admin_members_activate_query');
      }
    }
    # Deactivating? Alright.
    elseif($action == 'deactivate')
    {
      $handled = false;
      $api->run_hooks('admin_members_manage_handle_deactivate', array(&$handled, 'deactivate', $selected));

      if(empty($handled))
      {
        # Turn 'em off!
        $db->query('
          UPDATE {db->prefix}members
          SET member_activated = 0
          WHERE member_id IN({int_array:selected}) AND member_activated != 0',
          array(
            'selected' => $selected,
          ), 'admin_members_deactivate_query');
      }
    }
    elseif($action == 'delete')
    {
      # No need for a hook here for other member systems, that's in <Members::delete>!

      # I guess you want to delete them. That's your problem ;)
      # Luckily, the Members class can handle all this!
      $members = $api->load_class('Members');
      $members->delete($selected);
    }
  }
}

if(!function_exists('admin_members_manage_edit'))
{
  /*
    Function: admin_members_manage_edit

    Provides the interface to edit the specified user and their information.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_members_manage_edit()
  {
    global $api, $member, $theme;

    $member_id = $_GET['id'];

    $api->run_hooks('admin_members_manage_edit', array($member_id));

    # Can you edit members?
    if(!$member->can('manage_members'))
    {
      # Nope...
      admin_access_denied();
    }

    $theme->set_current_area('members_manage');

    # We will need the Members class, that's for sure!
    $members = $api->load_class('Members');

    $members->load($member_id);
    $member_info = $members->get($member_id);

    # Does the member exist?
    if($member_info === false)
    {
      $theme->set_title(l('Edit member'));

      $theme->header();

      echo '
  <h1>', l('An error has occurred'), '</h1>
  <p>', l('It appears that the member you are trying to edit does not exist.'), '</p>';

      $theme->footer();
    }
    else
    {
      # Generate the form for the specified member!
      admin_members_manage_edit_generate_form($member_id);
      $form = $api->load_class('Form');

      # Process the form? Perhaps?
      if(isset($_POST['members_edit_'. $member_id]))
      {
        if(isset($_GET['ajax']))
        {
          echo $form->json_process('members_edit_'. $member_id);
          exit;
        }
        else
        {
          $form->process('members_edit_'. $member_id);
        }
      }

      $theme->set_title(l('Editing member "%s"', $member_info['name']));

      $theme->header();

      echo '
  <h1><img src="', $theme->url(), '/members_manage-small.png" alt="" /> ', l('Editing member "%s"', $member_info['name']), '</h1>
  <p>', l('Changes can be made to the account "%s" here.', $member_info['name']), '</p>';

      $form->show('members_edit_'. $member_id);

      $theme->footer();
    }
  }
}

if(!function_exists('admin_members_manage_edit_generate_form'))
{
  /*
    Function: admin_members_manage_edit_generate_form

    Parameters:
      int $member_id - The id of the member being edited.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_members_manage_edit_generate_form($member_id)
  {
    global $api, $base_url;

    $members = $api->load_class('Members');
    $member_info = $members->get($member_id);
    $form = $api->load_class('Form');

    # Create the form.
    $form->add('members_edit_'. $member_id, array(
                                              'action' => $base_url. '/index.php?action=admin&amp;sa=members_manage&amp;id='. $member_id,
                                              'ajax_submit' => true,
                                              'callback' => 'admin_members_manage_edit_handle',
                                              'method' => 'post',
                                              'submit' => l('Edit'),
                                            ));

    # Display name..?
    $form->add_field('members_edit_'. $member_id, 'display_name', array(
                                                                    'type' => 'string',
                                                                    'label' => l('Display name:'),
                                                                    'value' => !empty($_POST['display_name']) ? $_POST['display_name'] : $member_info['name'],
                                                                    'function' => create_function('$value, $form_name, &$error', '
                                                                                    global $api;

                                                                                    $members = $api->load_class(\'Members\');

                                                                                    if($members->name_allowed($value, $_GET[\'id\']))
                                                                                    {
                                                                                      return true;
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                      $error = l(\'The supplied display name is not allowed or in use by another member.\');
                                                                                      return false;
                                                                                    }'),
                                                                  ));

    # Email address, woo!
    $form->add_field('members_edit_'. $member_id, 'member_email', array(
                                                                    'type' => 'string',
                                                                    'label' => l('Email address:'),
                                                                    'value' => !empty($_POST['member_email']) ? $_POST['member_email'] : $member_info['email'],
                                                                    'function' => create_function('$value, $form_name, &$error', '
                                                                                    global $api;

                                                                                    $members = $api->load_class(\'Members\');

                                                                                    if($members->email_allowed($value, $_GET[\'id\']))
                                                                                    {
                                                                                      return true;
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                      $error = l(\'The supplied email address is not allowed or in use by another member.\');
                                                                                      return false;
                                                                                    }'),
                                                                  ));

    # Change the password? Why not!
    $form->add_field('members_edit_'. $member_id, 'member_pass', array(
                                                                   'type' => 'password',
                                                                   'label' => l('Password:'),
                                                                   'subtext' => l('Leave blank if you don\'t want to change the password.'),
                                                                   'function' => create_function('$value, $form_name, &$error', '
                                                                                   global $api;

                                                                                   if(!empty($value) && (empty($_POST[\'verify_pass\']) || $_POST[\'verify_pass\'] != $value))
                                                                                   {
                                                                                     $error = l(\'The supplied passwords don\\\'t match.\');
                                                                                     return false;
                                                                                   }
                                                                                   elseif(!empty($value) && !empty($_POST[\'verify_pass\']))
                                                                                   {
                                                                                     $members = $api->load_class(\'Members\');

                                                                                     if(!$members->password_allowed($_POST[\'display_name\'], $value))
                                                                                     {
                                                                                       $error = l(\'The supplied password is not allowed.\');
                                                                                       return false;
                                                                                     }
                                                                                   }

                                                                                   return true;'),
                                                                   'value' => '',
                                                                 ));

    # We will need you to verify that ;)
    $form->add_field('members_edit_'. $member_id, 'verify_pass', array(
                                                                   'type' => 'password',
                                                                   'label' => l('Verify password:'),
                                                                   'subtext' => l('Just to make sure, re-enter the password.'),
                                                                   'value' => '',
                                                                 ));

    # Are they an administrator?
    $form->add_field('members_edit_'. $member_id, 'is_administrator', array(
                                                                        'type' => 'checkbox',
                                                                        'label' => l('Administrator?'),
                                                                        'subtext' => l('If checked, the member will be an administrator, otherwise they will be just a member and additional groups can be selected below.'),
                                                                        'value' => !empty($_POST['is_administrator']) ? $_POST['is_administrator'] : in_array('administrator', $member_info['groups']),
                                                                      ));

    # Additional groups?
    $groups = $api->return_group();

    # Remove administrator and the member group.
    unset($groups['administrator'], $groups['member']);
    $form->add_field('members_edit_'. $member_id, 'member_groups', array(
                                                                     'type' => 'select-multi',
                                                                     'label' => l('Additional member groups:'),
                                                                     'subtext' => l('Select any additional groups the member should be in, ignored if the member is an administrator.'),
                                                                     'options' => $groups,
                                                                     'rows' => 4,
                                                                     'value' => !empty($_POST['member_groups']) ? $_POST['member_groups'] : $member_info['groups'],
                                                                   ));

    # Should the account be activated? ;)
    $form->add_field('members_edit_'. $member_id, 'member_activated', array(
                                                                        'type' => 'checkbox',
                                                                        'label' => l('Account activated:'),
                                                                        'value' => !empty($_POST['member_activated']) ? $_POST['member_activated'] : $member_info['is_activated'],
                                                                      ));
  }
}

if(!function_exists('admin_members_manage_edit_handle'))
{
  /*
    Function: admin_members_manage_edit_handle

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns true if the member was successfully edited, false if not.

    Note:
      This function is overloadable.
  */
  function admin_members_manage_edit_handle($data, &$errors = array())
  {
    global $api, $core_dir;

    # We will most certainly need the Members class!
    $members = $api->load_class('Members');

    # And to update the values!
    $form = $api->load_class('Form');

    $members->load($_GET['id']);
    $member_info = $members->get($_GET['id']);

    # Alright, set our options that are updated.
    if($member_info['name'] != $data['display_name'])
    {
      $options['display_name'] = $data['display_name'];
      $form->edit_field('members_edit_'. $_GET['id'], 'display_name', array(
                                                                        'value' => $data['display_name'],
                                                                      ));
    }

    if($member_info['email'] != $data['member_email'])
    {
      $options['member_email'] = $data['member_email'];
      $form->edit_field('members_edit_'. $_GET['id'], 'member_email', array(
                                                                        'value' => $data['member_email'],
                                                                      ));
    }

    if(!empty($data['member_pass']))
    {
      # Changing the password needs the member name too.
      $options['member_name'] = $member_info['username'];
      $options['member_pass'] = $data['member_pass'];
    }

    if(!empty($data['is_administrator']))
    {
      $options['member_groups'] = array('administrator');
      $form->edit_field('members_edit_'. $_GET['id'], 'is_administrator', array(
                                                                            'value' => 1,
                                                                          ));

    }
    elseif(empty($data['is_administrator']))
    {
      $options['member_groups'] = array('member');
      $form->edit_field('members_edit_'. $_GET['id'], 'is_administrator', array(
                                                                            'value' => 0,
                                                                          ));

      $data['member_groups'] = explode(',', $data['member_groups']);

      if(count($data['member_groups']) > 0)
      {
        foreach($data['member_groups'] as $member_group)
          $options['member_groups'][] = $member_group;
      }

      $form->edit_field('members_edit_'. $_GET['id'], 'member_groups', array(
                                                                         'value' => $data['member_groups'],
                                                                       ));
    }

    if($member_info['is_activated'] != $data['member_activated'])
    {
      $options['member_activated'] = !empty($data['member_activated']);
      $form->edit_field('members_edit_'. $_GET['id'], 'member_activated', array(
                                                                            'value' => !empty($data['member_activated']),
                                                                          ));
    }

    # Do we need to send a welcome email?
    if($member_info['acode'] == 'admin_approval')
    {
      $options['member_acode'] = '';

      if(!function_exists('register_send_welcome_email'))
      {
        require_once($core_dir. '/register.php');
      }

      register_send_welcome_email($member_info['id']);
    }

    # Now update the member!
    $members->update($_GET['id'], $options);

    $api->add_filter('members_edit_'. $_GET['id']. '_message', create_function('$value', '
                                                                 return l(\'The account has been successfully updated.\');'));

    return true;
  }
}
?>