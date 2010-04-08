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
    global $api, $base_url, $theme;

    admin_members_add_generate_form();
    $form = $api->load_class('Form');

    if(!empty($_POST['admin_members_add_form']))
      $form->process('admin_members_add_form');

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
                                                                 'value' => !empty($_POST['member_groups']) ? explode(',', $_POST['member_groups']) : '',
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
    global $api, $theme;

    # Generate our form!
    admin_members_settings_generate_form();
    $form = $api->load_class('Form');

    # Time to save?
    if(!empty($_POST['admin_members_settings_form']))
      # Save all the data.
      $form->process('admin_members_settings_form');

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
    global $api, $base_url, $theme, $theme_url;

    # Generate our table ;)
    admin_members_manage_generate_table();
    $table = $api->load_class('Table');

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

if(!function_exists('admin_members_manage_edit'))
{
  /*
    Function: admin_members_manage_edit

    Provides the interface to edit the specified user and their information.

    Parameters:
      int $member_id - The id of the member to edit.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_members_manage_edit($member_id)
  {
    global $api, $theme;

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
    global $api;

    $form = $api->load_class('Form');

    # Create the form.
    $form->add('members_edit_'. $member_id, array(
                                              'action' => $base_url. '/index.php?action=admin&amp;sa=members_manage&amp;id='. $member_id,
                                              'callback' => 'admin_members_manage_edit_handle',
                                              'method' => 'post',
                                              'submit' => l('Edit'),
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
  }
}
?>