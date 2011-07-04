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

// Title: Control Panel - Members - Settings

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

    // Generate our form!
    admin_members_settings_generate_form();
    $form = api()->load_class('Form');

    // Time to save?
    if(!empty($_POST['admin_members_settings_form']))
    {
      // Save all the data.
      if(isset($_GET['ajax']))
      {
        echo $form->json_process('admin_members_settings_form');
        exit;
      }
      else
      {
        $form->process('admin_members_settings_form');
      }
    }

    theme()->set_current_area('members_settings');

    theme()->set_title(l('Member settings'));

    theme()->header();

    echo '
  <h1><img src="', theme()->url(), '/members_settings-small.png" alt="" /> ', l('Member settings'), '</h1>
  <p>', l('Member settings can be managed here, which includes setting the registration mode, or disabling it all together.'), '</p>';

    $form->show('admin_members_settings_form');

    theme()->footer();
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
    $form = api()->load_class('Form');

    $form->add('admin_members_settings_form', array(
                                                'action' => baseurl. '/index.php?action=admin&sa=members_settings',
                                                'ajax_submit' => true,
                                                'callback' => 'admin_members_settings_handle',
                                                'submit' => l('Save settings'),
                                              ));

    // Registration enabled?
    $form->add_field('admin_members_settings_form', 'registration_enabled', array(
                                                                              'type' => 'checkbox',
                                                                              'label' => l('Enable registration:'),
                                                                              'subtext' => l('If disabled, people won\'t be able to manually create accounts through the registration feature.'),
                                                                              'value' => settings()->get('registration_enabled', 'int'),
                                                                            ));

    // Types of registration.
    $types = api()->apply_filters('registration_types', array(
                                                         0 => l('Instant activation'),
                                                         1 => l('Administrative activation'),
                                                         2 => l('Email activation'),
                                                       ));
    $form->add_field('admin_members_settings_form', 'registration_type', array(
                                                                           'type' => 'select',
                                                                           'label' => l('Registration mode:'),
                                                                           'subtext' => l('Instant activation: no further action, Administrative activation: administrators must activate accounts, Email activation: new registrations must verify their email address.'),
                                                                           'options' => $types,
                                                                           'value' => settings()->get('registration_type', 'int'),
                                                                         ));

    // Minimum length of a username/display name.
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
                                                                                  'value' => settings()->get('members_min_name_length', 'int'),
                                                                                ));

    // Maximum length of a username/display name.
    $form->add_field('admin_members_settings_form', 'members_max_name_length', array(
                                                                                 'type' => 'int',
                                                                                 'label' => l('Maximum username length:'),
                                                                                 'subtext' => l('Can be from 1 to 80. This also applies to display names.'),
                                                                                 'length' => array(
                                                                                               'min' => 1,
                                                                                               'max' => 80,
                                                                                             ),
                                                                                  'function' => create_function('$value, $form_name, &$error', '

                                                                                                  if((int)$value < (int)$_POST[\'members_min_name_length\'])
                                                                                                  {
                                                                                                    return false;
                                                                                                  }

                                                                                                  return true;'),
                                                                                  'value' => settings()->get('members_max_name_length', 'int'),
                                                                                ));

    // Password security :P
    $levels = api()->apply_filters('password_security_levels', array(
                                                                1 => 'Must be at least 3 characters',
                                                                2 => 'Must be at least 4 chars, cannot contain name',
                                                                3 => 'Must be at least 5 chars, cannot contain name, must contain a number',
                                                              ));

    $form->add_field('admin_members_settings_form', 'password_security', array(
                                                                           'type' => 'select',
                                                                           'label' => l('Password security:'),
                                                                           'options' => $levels,
                                                                           'value' => settings()->get('password_security', 'int'),
                                                                         ));

    // Reserved names...
    $form->add_field('admin_members_settings_form', 'reserved_names', array(
                                                                        'type' => 'textarea',
                                                                        'label' => l('Reserved names:'),
                                                                        'subtext' => l('These are names which cannot be registered or used. Simply enter one name per line, an asterisk (*) denotes a wildcard.'),
                                                                        'rows' => 5,
                                                                        'cols' => 25,
                                                                        'value' => settings()->get('reserved_names', 'string'),
                                                                      ));

    // Disallowed email addresses.
    $form->add_field('admin_members_settings_form', 'disallowed_emails', array(
                                                                           'type' => 'textarea',
                                                                           'label' => l('Disallowed email addresses:'),
                                                                           'subtext' => l('Enter an email address per line, An asterisk (*) denotes a wildcard. To disallow an entire domain (in this case, Yahoo), do this: *@yahoo.com.'),
                                                                           'rows' => 5,
                                                                           'cols' => 25,
                                                                           'value' => settings()->get('disallowed_emails', 'string'),
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
    $form = api()->load_class('Form');

    // Update them, easy!
    foreach($data as $variable => $value)
    {
      // Save it.
      settings()->set($variable, $value, is_int($value) ? 'int' : (is_double($value) ? 'double' : 'string'));

      // Update the value in the form ;)
      $form->edit_field('admin_members_settings_form', $variable, array(
                                                                    'value' => $value,
                                                                  ));
    }

    api()->add_filter('admin_members_settings_form_message', create_function('$value', '
                                                              return l(\'Member settings have been successfully updated.\');'));

    return true;
  }
}
?>