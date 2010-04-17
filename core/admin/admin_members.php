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

# Title: Control Panel - Members
if(!function_exists('admin_members_add'))
{
  /*
    Function: admin_members_add

    An interface for the manual addition of members.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_members_add()
  {
    global $api, $base_url, $member, $theme;

    $api->run_hooks('admin_members_add');

    # Trying to access something you can't? Not if I can help it!
    if(!$member->can('add_new_member'))
      admin_access_denied();

    admin_members_add_generate_form();
    $form = $api->load_class('Form');

    if(!empty($_POST['admin_members_add_form']))
      $form->process('admin_members_add_form');

    $theme->set_current_area('members_add');

    $theme->set_title(l('Add a new member'));

    $theme->header();

    echo '
  <h1><img src="', $base_url, '/core/admin/icons/add_member-small.png" alt="" /> ', l('Add a new member'), '</h1>
  <p>', l('If registration is enabled, guests on your site can create their own member, but if you need to create a new member, you can do so here.'), '</p>';

    $form->show('admin_members_add_form');

    $theme->footer();
  }
}

if(!function_exists('admin_members_add_generate_form'))
{
  /*
    Function: admin_members_add_generate_form

    Generates the form for adding a new member.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_members_add_generate_form()
  {
    global $api;

    $form = $api->load_class('Form');

    $form->add('admin_members_add_form', array(
                                          'action' => '',
                                          'callback' => 'admin_members_add_handle',
                                          'submit' => 'Add member',
                                         ));

    # Their username.
    $form->add_field('admin_members_add_form', 'member_name', array(
                                                                'type' => 'string',
                                                                'label' => l('New username:'),
                                                                'subtext' => l('The username of the new member.'),
                                                                'length' => array(
                                                                              'min' => 1,
                                                                              'max' => 80,
                                                                            ),
                                                                'function' => create_function('$value, $form_name, &$error', '
                                                                  global $api;

                                                                  $members = $api->load_class(\'Members\');

                                                                  if($members->name_allowed($value))
                                                                    return true;
                                                                  else
                                                                  {
                                                                    $error = l(\'The requested username is already in use or not allowed.\');
                                                                    return false;
                                                                  }'),
                                                                'value' => !empty($_REQUEST['member_name']) ? $_REQUEST['member_name'] : '',
                                                              ));

    # Password, twice though!
    $form->add_field('admin_members_add_form', 'member_pass', array(
                                                                'type' => 'password',
                                                                'label' => l('Password:'),
                                                                'function' => create_function('$value, $form_name, &$error', '
                                                                   global $api, $settings;

                                                                   # Passwords don\'t match? That isn\'t right.
                                                                   if(empty($_POST[\'pass_verification\']) || $_POST[\'pass_verification\'] != $value)
                                                                   {
                                                                     $error = l(\'Your passwords do not match.\');
                                                                     return false;
                                                                   }

                                                                   $members = $api->load_class(\'Members\');

                                                                   if($members->password_allowed($_POST[\'member_name\'], $value))
                                                                     return true;
                                                                   else
                                                                   {
                                                                     $security = $settings->get(\'password_security\', \'int\');

                                                                     if($security == 1)
                                                                       $error = l(\'Your password must be at least 3 characters long.\');
                                                                     elseif($security == 2)
                                                                       $error = l(\'Your password must be at least 4 characters long and cannot contain your username.\');
                                                                     else
                                                                       $error = l(\'Your password must be at least 5 characters long, cannot contain your username and contain at least 1 number.\');

                                                                     return false;
                                                                   }')
                                                              ));

    # As said, twice ;)
    $form->add_field('admin_members_add_form', 'pass_verification', array(
                                                                      'type' => 'password',
                                                                      'label' => l('Verify password:'),
                                                                      'subtext' => l('Just to be sure!'),
                                                                    ));

    # Now for an email, please!
    $form->add_field('admin_members_add_form', 'member_email', array(
                                                            'type' => 'string',
                                                            'label' => l('Email:'),
                                                            'subtext' => l('Please enter a valid email address.'),
                                                            'length' => array(
                                                                          'max' => 255,
                                                                        ),
                                                            'function' => create_function('$value, $form_name, &$error', '
                                                              global $api;

                                                              $members = $api->load_class(\'Members\');

                                                              if($members->email_allowed($value))
                                                                return true;
                                                              else
                                                              {
                                                                $error = l(\'The supplied email address is already in use or not allowed.\');
                                                                return false;
                                                              }'),
                                                            'value' => !empty($_REQUEST['member_email']) ? $_REQUEST['member_email'] : '',
                                                          ));

    # Should they be an administrator, or just a regular member?
    $form->add_field('admin_members_add_form', 'is_administrator', array(
                                                                    'type' => 'checkbox',
                                                                    'label' => l('Administrator?'),
                                                                    'subtext' => l('If checked, the new member will be an administrator, otherwise they will be just a member.'),
                                                                    'value' => !empty($_POST['is_administrator']),
                                                                   ));

    # What other member group(s) should they be in..?
    $groups = $api->return_group();

    # We want to remove the administrator and member group ;)
    unset($groups['administrator'], $groups['member']);

    $form->add_field('admin_members_add_form', 'member_groups', array(
                                                                 'type' => 'select-multi',
                                                                 'label' => l('Member groups:'),
                                                                 'subtext' => l('Select any other member groups you want to be applied to the user. If you checked the administrator box, this will be ignored.'),
                                                                 'options' => $groups,
                                                                 'rows' => 4,
                                                                 'value' => !empty($_POST['member_groups']) ? $_POST['member_groups'] : '',
                                                               ));
  }
}

if(!function_exists('admin_members_add_handle'))
{
  /*
    Function: admin_members_add_handle

    Handles the adding of the member called by the Form class.

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns true if the member was successfully created,
             false if not.

    Note:
      This function is overloadable.
  */
  function admin_members_add_handle($data, &$errors = array())
  {
    global $api;

    # Alright, all that's left to do is create the member!
    $members = $api->load_class('Members');

    # Did you want them to be an administrator?
    if(!empty($data['is_administrator']))
      $groups = array('administrator');
    else
    {
      $groups = array('member');

      if(!empty($data['member_groups']))
      {
        foreach(explode(',', $data['member_groups']) as $group_id)
          $groups[] = $group_id;
      }
    }

    # So create it! ;)
    $member_id = $members->add($data['member_name'], $data['member_pass'], $data['member_email'], array(
                                                                                                    'member_activated' => 1,
                                                                                                    'member_groups' => $groups,
                                                                                                  ));

    $api->add_filter('admin_members_add_form_message', create_function('$value', '
                                                         return l(\'The member was successfully added!\');'));

    return true;
  }
}

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
    global $api, $member, $theme;

    $api->run_hooks('admin_members_settings');

    # Let's see, can you manage member settings?
    if(!$member->can('manage_member_settings'))
      # I didn't think so!
      admin_access_denied();

    # Generate our form!
    admin_members_settings_generate_form();
    $form = $api->load_class('Form');

    # Time to save?
    if(!empty($_POST['admin_members_settings_form']))
      # Save all the data.
      $form->process('admin_members_settings_form');

    $theme->set_current_area('members_settings');

    $theme->set_title(l('Member settings'));

    $theme->header();

    echo '
  <h1><img src="', $base_url, '/core/admin/icons/member_settings-small.png" alt="" /> ', l('Member settings'), '</h1>
  <p>', l('Member settings can be managed here, which includes setting the registration mode, or disabling it all together.'), '</p>';

    $form->show('admin_members_settings_form');

    $theme->footer();
  }
}

if(!function_exists('admin_members_settings_generate_form'))
{
  /*
    Function: admin_members_settings_generate_form

    Generates the member settings form.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_members_settings_generate_form()
  {
    global $api, $settings;

    $form = $api->load_class('Form');

    $form->add('admin_members_settings_form', array(
                                                'action' => '',
                                                'callback' => 'admin_members_settings_handle',
                                                'submit' => l('Save settings'),
                                              ));

    # Registration enabled?
    $form->add_field('admin_members_settings_form', 'registration_enabled', array(
                                                                              'type' => 'checkbox',
                                                                              'label' => l('Enable registration:'),
                                                                              'subtext' => l('If disabled, people won\'t be able to manually create accounts through the registration feature.'),
                                                                              'value' => $settings->get('registration_enabled', 'int'),
                                                                            ));

    # Types of registration.
    $types = $api->apply_filters('registration_types', array(
                                                         0 => l('Instant activation'),
                                                         1 => l('Administrative activation'),
                                                         2 => l('Email activation'),
                                                       ));
    $form->add_field('admin_members_settings_form', 'registration_type', array(
                                                                           'type' => 'select',
                                                                           'label' => l('Registration mode:'),
                                                                           'subtext' => l('Instant activation: no further action, Administrative activation: administrators must activate accounts, Email activation: new registrations must verify their email address.'),
                                                                           'options' => $types,
                                                                           'value' => $settings->get('registration_type', 'int'),
                                                                         ));

    # Minimum length of a username/display name.
    $form->add_field('admin_members_settings_form', 'members_min_name_length', array(
                                                                                 'type' => 'int',
                                                                                 'label' => l('Minimum username length:'),
                                                                                 'subtext' => l('Can be from 1 to 80. This also applies to display names.'),
                                                                                 'length' => array(
                                                                                               'min' => 1,
                                                                                               'max' => 80,
                                                                                             ),
                                                                                  'function' => create_function('$value, $form_name, &$error', '

                                                                                                  if((int)$value > (int)$_POST[\'members_max_name_length\'])
                                                                                                  {
                                                                                                    $error = l(\'Minimum username length can\\\'t be larger than the maximum username length.\');
                                                                                                    return false;
                                                                                                  }

                                                                                                  return true;'),
                                                                                  'value' => $settings->get('members_min_name_length', 'int'),
                                                                                ));

    # Maximum length of a username/display name.
    $form->add_field('admin_members_settings_form', 'members_max_name_length', array(
                                                                                 'type' => 'int',
                                                                                 'label' => l('Maximum username length:'),
                                                                                 'subtext' => l('Can be from 1 to 80. This also applies to display names.'),
                                                                                 'length' => array(
                                                                                               'min' => 1,
                                                                                               'max' => 80,
                                                                                             ),
                                                                                  'function' => create_function('$value, $form_name, &$error', '

                                                                                                  if((int)$value < (int)$_POST[\'members_mmin_name_length\'])
                                                                                                  {
                                                                                                    return false;
                                                                                                  }

                                                                                                  return true;'),
                                                                                  'value' => $settings->get('members_max_name_length', 'int'),
                                                                                ));

    # Password security :P
    $levels = $api->apply_filters('password_security_levels', array(
                                                                1 => 'Must be at least 3 characters',
                                                                2 => 'Must be at least 4 chars, cannot contain name',
                                                                3 => 'Must be at least 5 chars, cannot contain name, must contain a number',
                                                              ));

    $form->add_field('admin_members_settings_form', 'password_security', array(
                                                                           'type' => 'select',
                                                                           'label' => l('Password security:'),
                                                                           'options' => $levels,
                                                                           'value' => $settings->get('password_security', 'int'),
                                                                         ));

    # Reserved names...
    $form->add_field('admin_members_settings_form', 'reserved_names', array(
                                                                        'type' => 'textarea',
                                                                        'label' => l('Reserved names:'),
                                                                        'subtext' => l('These are names which cannot be registered or used. Simply enter one name per line, an asterisk (*) denotes a wildcard.'),
                                                                        'rows' => 5,
                                                                        'cols' => 25,
                                                                        'value' => $settings->get('reserved_names', 'string'),
                                                                      ));

    # Disallowed email addresses.
    $form->add_field('admin_members_settings_form', 'disallowed_emails', array(
                                                                           'type' => 'textarea',
                                                                           'label' => l('Disallowed email addresses:'),
                                                                           'subtext' => l('Enter an email address per line, An asterisk (*) denotes a wildcard. To disallow an entire domain (in this case, Yahoo), do this: *@yahoo.com.'),
                                                                           'rows' => 5,
                                                                           'cols' => 25,
                                                                           'value' => $settings->get('disallowed_emails', 'string'),
                                                                         ));

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
    global $api, $settings;

    $form = $api->load_class('Form');

    # Update them, easy!
    foreach($data as $variable => $value)
    {
      # Save it.
      $settings->set($variable, $value, is_int($value) ? 'int' : (is_double($value) ? 'double' : 'string'));

      # Update the value in the form ;)
      $form->edit_field('admin_members_settings_form', $variable, array(
                                                                    'value' => $value,
                                                                  ));
    }

    return true;
  }
}

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
      # You can't handle managing members! Or so someone thinks ;)
      admin_access_denied();

    # Generate our table ;)
    admin_members_manage_generate_table();
    $table = $api->load_class('Table');

    $theme->set_current_area('members_manage');

    $theme->set_title(l('Manage Members'));

    $theme->add_js_var('delete_confirm', l('Are you sure you want to delete the selected members?\\r\\nThis cannot be undone!'));
    $theme->add_js_file(array('src' => $theme_url. '/default/js/members_manage.js'));

    $theme->header();

    echo '
  <h1><img src="', $base_url, '/core/admin/icons/manage_members-small.png" alt="" /> ', l('Manage Members'), '</h1>
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
    global $api, $db;

    # No point on executing anything if nothing was selected.
    if(!is_array($selected) || count($selected) == 0)
      return;

    # Activating accounts?
    if($action == 'activate')
    {
      # Make them activated!
      $db->query('
        UPDATE {db->prefix}members
        SET member_activated = 1
        WHERE member_id IN({int_array:selected})',
        array(
          'selected' => $selected,
        ), 'admin_members_activate_query');
    }
    # Deactivating? Alright.
    elseif($action == 'deactivate')
    {
      # Turn 'em off!
      $db->query('
        UPDATE {db->prefix}members
        SET member_activated = 0
        WHERE member_id IN({int_array:selected})',
        array(
          'selected' => $selected,
        ), 'admin_members_deactivate_query');
    }
    elseif($action == 'delete')
    {
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
      # Nope...
      admin_access_denied();

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
        $form->process('members_edit_'. $member_id);

      $theme->set_title(l('Editing member "%s"', $member_info['name']));

      $theme->header();

      echo '
  <h1>', l('Editing member "%s"', $member_info['name']), '</h1>
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
                                                                                      return true;
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
                                                                                      return true;
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
    global $api;

    # We will most certainly need the Members class!
    $members = $api->load_class('Members');

    # And to update the values!
    $form = $api->load_class('Form');

    $members->load($_GET['id']);
    $member_info = $members->get($_GET['id']);

    # Alright, set our options that are updated.
    if($member_info['display_name'] != $data['display_name'])
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

    # Now update the member!
    $members->update($_GET['id'], $options);

    return true;
  }
}

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
    global $api, $base_url, $member, $theme;

    $api->run_hooks('admin_members_manage_permissions');

    # Do you have the permission to edit permissions!?
    if(!$member->can('manage_permissions'))
      admin_access_denied();

    $theme->set_current_area('members_permissions');

    $theme->set_title(l('Manage permissions'));

    $theme->header();

    echo '
  <h1><img src="', $base_url, '/core/admin/icons/permissions-small.png" alt="" /> ', l('Manage permissions'), '</h1>
  <p>', l('The permissions of member groups can all be modified here. Simply click on the member group below to edit their permissions.'), '</p>';

    $groups = $api->return_group();

    # Remove the administrator group, as administrators are ALL POWERFUL!
    unset($groups['administrator']);

    $group_list = array();
    foreach($groups as $group_id => $group_name)
    {
      $group_list[] = '<a href="'. $base_url. '/index.php?action=admin&amp;sa=members_permissions&amp;grp='. urlencode($group_id). '">'. $group_name. '</a>';
    }

    echo '
  <h3>', l('Member groups'), '</h3>
  <p>', implode(', ', $group_list), '</p>';

    $theme->footer();
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
    global $api, $base_url, $member, $theme;

    $group_id = $_GET['grp'];

    $api->run_hooks('admin_members_manage_group_permissions');

    if(!$member->can('manage_permissions'))
      admin_access_denied();

    $theme->set_current_area('members_permissions');

    # Check to see if the specified group even exists!
    if(!$api->return_group($group_id))
    {
      $theme->set_title(l('An error has occurred'));

      $theme->header();

      echo '
    <h1>', l('An error has occurred'), '</h1>
    <p>', l('Sorry, but it appears the group you have requested doesn\'t exist.'), '</p>';

      $theme->footer();
    }
    else
    {
      # Time to generate that form!
      admin_members_permissions_generate_form(strtolower($api->return_group($group_id)). '_permissions', $group_id);
      $form = $api->load_class('Form');

      if(!empty($_POST[strtolower($api->return_group($group_id)). '_permissions']))
        # Process the form!
        $form->process(strtolower($api->return_group($group_id)). '_permissions');

      $theme->set_title(l('Managing %s permissions', $api->return_group($group_id)));

      $theme->header();

      echo '
    <h1><img src="', $base_url, '/core/admin/icons/permissions-small.png" alt="" /> ', l('Managing %s permissions', $api->return_group($group_id)), '</h1>
    <p>', l('Changes to member groups permissions can be applied here. If deny is selected, no matter what other groups the member may be in, the permission will be denied. If disallow is selected and another one of the member groups they are in allows the permission, the disallow will be overridden. <a href="%s" title="Back to Manage Permissions">Back to Manage Permissions</a>.', $base_url. '/index.php?action=admin&amp;sa=members_permissions'), '</p>';

      $form->show(strtolower($api->return_group($group_id)). '_permissions');

      $theme->footer();
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
    global $api, $db;

    $form = $api->load_class('Form');

    # Add our form, before we do anything else, of course!
    $form->add($form_name, array(
                             'action' => '',
                             'callback' => 'admin_members_permissions_handle',
                             'method' => 'post',
                             'submit' => l('Save'),
                           ));

    # Now is your time to add your permission!
    $permissions = array(
                     array(
                       'permission' => 'manage_system_settings', # The permission in the table.
                       'label' => l('Manage system settings:'), # The label of the field
                       'subtext' => '', # Subtext too, if you want.
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
                   );

    # So yeah, add your permissions!
    $permissions = $api->apply_filters('member_group_permissions', $permissions);

    if(is_array($permissions) && count($permissions))
    {
      # Time to load up the permissions in the database, or elsewhere.
      $loaded = null;
      $api->run_hooks('load_permissions', array(&$loaded, $group_id));

      # Oh, I need to do it?
      if($loaded === null)
      {
        # They are in the database ;)
        $result = $db->query('
                    SELECT
                      permission, status
                    FROM {db->prefix}permissions
                    WHERE group_id = {string:group_id}',
                    array(
                      'group_id' => $group_id,
                    ), 'load_permissions_query');

        $loaded = array();
        while($row = $result->fetch_assoc())
          $loaded[$row['permission']] = $row['status'];
      }

      foreach($permissions as $permission)
      {
        if(empty($permission['permission']))
          # We really kinda need the permissions identifier.
          continue;

        $form->add_field($form_name, $permission['permission'], array(
                                                                  'type' => 'select',
                                                                  'label' => isset($permission['label']) ? $permission['label'] : '',
                                                                  'subtext' => isset($permission['subtext']) ? $permission['subtext'] : '',
                                                                  'options' => array(
                                                                                 -1 => l('Deny'),
                                                                                 0 => l('Disallow'),
                                                                                 1 => l('Allow'),
                                                                               ),
                                                                  'value' => isset($loaded[$permission['permission']]) ? $loaded[$permission['permission']] : 0,
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
    global $api, $db;

    $group_id = $_GET['grp'];

    # We will need to update the value in the form.
    $form = $api->load_class('Form');

    # Simple enough! Replace the values in the database!!!
    foreach($data as $permission => $status)
    {
      $db->insert('replace', '{db->prefix}permissions',
        array(
          'group_id' => 'string-128', 'permission' => 'string-128', 'status' => 'int',
        ),
        array(
          $group_id, $permission, $status,
        ),
        array('group_id', 'permission'), 'permissions_handle_query');

      $form->edit_field(strtolower($group_id). '_permissions', $permission, array(
                                                                              'value' => $status,
                                                                            ));
    }

    return true;
  }
}
?>