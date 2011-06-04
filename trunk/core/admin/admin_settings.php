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

if(!defined('IN_SNOW'))
{
  die('Nice try...');
}

# Title: Control Panel - Settings

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
    global $api, $base_url, $theme, $member;

    $api->run_hooks('admin_settings');

    # Let's make sure you can manage system settings.
    if(!$member->can('manage_system_settings'))
    {
      admin_access_denied();
    }

    admin_settings_generate_form();
    $form = $api->load_class('Form');

    # Submitting the form? Alright.
    if(!empty($_POST['admin_settings_form']))
    {
      # We shall process it! But through AJAX?
      if(isset($_GET['ajax']))
      {
        echo $form->json_process('admin_settings_form');
        exit;
      }
      else
      {
        # Just regular ol' submitting ;)
        $form->process('admin_settings_form');
      }
    }

    $theme->set_current_area('system_settings');

    $theme->set_title(l('System settings'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/settings-small.png" alt="" /> ', l('System Settings'), '</h1>
  <p>', l('Manage some basic, though core, system settings including your sites name, email address, and so forth.'), '</p>';

    $form->show('admin_settings_form');

    $theme->footer();
  }
}

if(!function_exists('admin_settings_generate_form'))
{
  /*
    Function: admin_settings_generate_form

    Generates the form which allows you to edit core system settings.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_settings_generate_form()
  {
    global $api, $base_url, $settings, $theme_dir;

    $form = $api->load_class('Form');

    $form->add('admin_settings_form', array(
                                        'action' => $base_url. '/index.php?action=admin&sa=settings',
                                        'ajax_submit' => true,
                                        'callback' => 'admin_settings_handle',
                                        'submit' => l('Save settings'),
                                      ));

    # The name of the site, simple enough!
    $form->add_field('admin_settings_form', 'site_name', array(
                                                           'type' => 'string',
                                                           'label' => l('Site name:'),
                                                           'subtext' => l('The name of your website.'),
                                                           'value' => $settings->get('site_name', 'string'),
                                                         ));

    # The email address to, of course, send any emails from.
    $form->add_field('admin_settings_form', 'site_email', array(
                                                            'type' => 'string',
                                                            'label' => l('Site email address:'),
                                                            'subtext' => l('The email address from which emails will appear to come from.'),
                                                            'value' => $settings->get('site_email', 'string'),
                                                          ));

    # Whether or not to display your systems SnowCMS version.
    $form->add_field('admin_settings_form', 'show_version', array(
                                                              'type' => 'checkbox',
                                                              'label' => l('Show SnowCMS version:'),
                                                              'subtext' => l('If enabled, the current SnowCMS version you are running will be displayed.'),
                                                              'value' => $settings->get('show_version', 'int'),
                                                            ));

    # Whether or not you want to enable the task system.
    $form->add_field('admin_settings_form', 'enable_tasks', array(
                                                              'type' => 'checkbox',
                                                              'label' => l('Enable tasks:'),
                                                              'subtext' => l('If enabled, scheduled tasks will be allowed to run, this is not run by a cron, but by people browsing your site.'),
                                                              'value' => $settings->get('enable_tasks', 'int'),
                                                            ));

    # The maximum number of tasks to run at a time.
    $form->add_field('admin_settings_form', 'max_tasks', array(
                                                           'type' => 'int',
                                                           'label' => l('Maximum tasks to run at a time:'),
                                                           'subtext' => l('The maximum number of tasks which can be ran at once at any given time.'),
                                                           'length' => array(
                                                                         'min' => 0,
                                                                       ),
                                                           'value' => $settings->get('max_tasks', 'int'),
                                                         ));

    # Enable even more UTF8 support? You crazy! :P
    $form->add_field('admin_settings_form', 'enable_utf8', array(
                                                             'type' => 'checkbox',
                                                             'label' => l('Enable UTF8 support:'),
                                                             'subtext' => l('If enabled (and if the Multibyte PHP extension is enabled), UTF8 capable functions will be used to handle data. Please note that this can, in cases, slow your site down.'),
                                                             'disabled' => !function_exists('mb_internal_encoding'),
                                                             'value' => $settings->get('enable_utf8', 'int'),
                                                           ));

    # Disable admin security? Not a good idea, but hey, it's your site!!!
    $form->add_field('admin_settings_form', 'disable_admin_security', array(
                                                                        'type' => 'checkbox',
                                                                        'label' => l('Disable administrative security:'),
                                                                        'subtext' => l('Though not a good idea, if disabled, accessors of the control panel won\'t have to authenticate themselves periodically.'),
                                                                        'value' => $settings->get('disable_admin_security', 'int'),
                                                                      ));

    # Log errors in the database?
    $form->add_field('admin_settings_form', 'errors_log', array(
                                                            'type' => 'checkbox',
                                                            'label' => l('Log errors in database:'),
                                                            'subtext' => l('When enabled, SnowCMS will log any PHP errors (not fatal errors) in the database, instead of the error logging system set in the php.ini.'),
                                                            'value' => $settings->get('errors_log', 'int'),
                                                          ));

    # What should handle sending emails..?
    $form->add_field('admin_settings_form', 'mail_handler', array(
                                                              'type' => 'select',
                                                              'label' => l('Mail handler:'),
                                                              'subtext' => l('Allows you to set which protocol (or function) handles sending emails.'),
                                                              'options' => $api->apply_filters('admin_mail_handler', array(
                                                                                                                       'smtp' => 'SMTP',
                                                                                                                       'mail' => 'PHP mail()',
                                                                                                                     )),
                                                              'value' => $settings->get('mail_handler', 'string'),
                                                            ));

    # Your SMTP host, quite important, you know?
    $form->add_field('admin_settings_form', 'smtp_host', array(
                                                           'type' => 'string',
                                                           'label' => l('SMTP host:'),
                                                           'subtext' => l('The host address of the SMTP server.'),
                                                           'value' => $settings->get('smtp_host', 'string'),
                                                         ));

    # The port of the SMTP server.
    $form->add_field('admin_settings_form', 'smtp_port', array(
                                                           'type' => 'int',
                                                           'label' => l('SMTP port:'),
                                                           'subtext' => l('The port of the SMTP server, usually 25 or 465 (if it uses SSL).'),
                                                           'length' => array(
                                                                         'min' => 1,
                                                                         'max' => 65535,
                                                                       ),
                                                           'value' => $settings->get('smtp_port', 'int'),
                                                         ));

    # SMTP username
    $form->add_field('admin_settings_form', 'smtp_user', array(
                                                           'type' => 'string',
                                                           'label' => l('SMTP username:'),
                                                           'value' => $settings->get('smtp_user', 'string'),
                                                         ));
    # SMTP username
    $form->add_field('admin_settings_form', 'smtp_pass', array(
                                                           'type' => 'password',
                                                           'label' => l('SMTP password:'),
                                                           'subtext' => l('Your SMTP password will only be updated if this field is set.'),
                                                           'value' => '',
                                                         ));

    # Does the SMTP host use TLS?
    $form->add_field('admin_settings_form', 'smtp_is_tls', array(
                                                            'type' => 'checkbox',
                                                            'label' => l('SMTP host uses TLS:'),
                                                            'subtext' => l('Check this box if the SMTP host uses TLS, such as Gmail.'),
                                                            'value' => $settings->get('smtp_is_tls', 'int'),
                                                           ));

    # Number of seconds before the SMTP connection attempt is aborted.
    $form->add_field('admin_settings_form', 'smtp_timeout', array(
                                                              'type' => 'int',
                                                              'label' => l('SMTP timeout:'),
                                                              'subtext' => l('The maximum number, in seconds, that the server will wait for a response from the SMTP host.'),
                                                              'length' => array(
                                                                            'min' => 1,
                                                                          ),
                                                              'value' => $settings->get('smtp_timeout', 'int'),
                                                            ));

    # Additional mail parameters.
    $form->add_field('admin_settings_form', 'mail_additional_parameters', array(
                                                                            'type' => 'string',
                                                                            'label' => l('Additional mail parameters:'),
                                                                            'subtext' => l('Any additional PHP mail() function parameters (the $additional_parameters parameter).'),
                                                                            'value' => $settings->get('mail_additional_parameters', 'string'),
                                                                          ));

    # Time formatting information!
    $form->add_field('admin_settings_form', 'date_format', array(
                                                             'type' => 'string-html',
                                                             'label' => l('Date format:'),
                                                             'subtext' => l('Date only format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information.'),
                                                             'value' => $settings->get('date_format', 'string'),
                                                           ));

    $form->add_field('admin_settings_form', 'time_format', array(
                                                             'type' => 'string-html',
                                                             'label' => l('Time format:'),
                                                             'subtext' => l('Time only format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information.'),
                                                             'value' => $settings->get('time_format', 'string'),
                                                           ));

    $form->add_field('admin_settings_form', 'datetime_format', array(
                                                                 'type' => 'string-html',
                                                                 'label' => l('Date and time format:'),
                                                                 'subtext' => l('Date and time format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information.'),
                                                                 'value' => $settings->get('datetime_format', 'string'),
                                                               ));

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
    global $api, $settings;

    # We will need to update the values so we don't have to redirect.
    $form = $api->load_class('Form');

    # Loop through all the settings and save them!
    foreach($data as $variable => $value)
    {
      # This one is special :P
      if($variable == 'smtp_pass')
      {
        if(empty($value))
        {
          # Don't update it!
          continue;
        }
      }

      # Set it :)
      $settings->set($variable, $value, 'string');

      # Update the value, unless it is the SMTP password!
      if($variable != 'smtp_pass')
      {
        $form->edit_field('admin_settings_form', $variable, array(
                                                              'value' => $value,
                                                            ));
      }
    }

    $api->add_filter('admin_settings_form_message', create_function('$value', '
                                                      return l(\'Settings have been updated successfully.\');'));

    return true;
  }
}
?>