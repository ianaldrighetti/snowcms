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
      admin_access_denied();

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
          # Don't update it!
          continue;
      }

      # Set it :)
      $settings->set($variable, $value, 'string');

      # Update the value, unless it is the SMTP password!
      if($variable != 'smtp_pass')
        $form->edit_field('admin_settings_form', $variable, array(
                                                              'value' => $value,
                                                            ));
    }

    $api->add_filter('admin_settings_form_message', create_function('$value', '
                                                      return l(\'Settings have been updated successfully.\');'));

    return true;
  }
}

if(!function_exists('admin_error_log'))
{
  /*
    Function: admin_error_log

    Displays the list of errors from the database error log, if enabled.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_error_log()
  {
    global $api, $base_url, $member, $theme;

    $api->run_hooks('admin_error_log');

    # Can you view the error log? Don't try and be sneaky now!
    if(!$member->can('view_error_log'))
      # Get out of here!!!
      admin_access_denied();

    # Generate the table which we will use to display the errors.
    admin_error_log_generate_table();
    $table = $api->load_class('Table');

    $theme->set_current_area('system_error_log');

    $theme->set_title(l('Error log'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/error_log-small.png" alt="" /> ', l('Error log'), '</h1>
  <p>', l('View a list of errors generated by PHP.'), '</p>';

    $table->show('error_log');

    $theme->footer();
  }
}

if(!function_exists('admin_error_log_generate_table'))
{
  /*
    Function: admin_error_log_generate_table

    Generates the table which displays the errors in the error log.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_error_log_generate_table()
  {
    global $api, $base_url;

    $table = $api->load_class('Table');

    # Add our error log table.
    $table->add('error_log', array(
                               'base_url' => $base_url. '/index.php?action=admin&amp;sa=error_log',
                               'db_query' => '
                                              SELECT
                                                error_id, error_time, member_id, member_name, member_ip, error_type, error_message, error_file, error_line, error_url
                                              FROM {db->prefix}error_log',
                               'primary' => 'error_id',
                               'options' => array(
                                              'delete' => l('Delete'),
                                              'truncate' => l('Delete all'),
                                              'export' => l('Export selected'),
                                            ),
                               'callback' => 'admin_error_log_table_handle',
                               'sort' => array('error_id', 'DESC'),
                               'cellpadding' => '4px',
                               'filters' => array(
                                              'error_type' => array(
                                                                'label' => l('Show only:'),
                                                                'title' => l('Filter by error type'),
                                                                'options' => $api->apply_filters('error_log_types', array(
                                                                                                                      'all' => l('Show all'),
                                                                                                                      'fatal' => l('Fatal run-time error'),
                                                                                                                      'general' => l('General'),
                                                                                                                      'parse' => l('Compile-time parse error'),
                                                                                                                      'undefined' => l('Undefined variable'),
                                                                                                                      'interopt' => l('Interopability issue'),
                                                                                                                      'deprecated' => l('Deprecated'),
                                                                                                                      'database' => l('Database'),
                                                                                                                    )),
                                                                'callback' => create_function('&$table, $filter', '
                                                                                  if($filter == \'fatal\')
                                                                                  {
                                                                                    $in = array(E_ERROR, E_USER_ERROR);
                                                                                  }
                                                                                  elseif($filter == \'general\')
                                                                                  {
                                                                                    $in = array(E_WARNING, E_USER_WARNING);
                                                                                  }
                                                                                  elseif($filter == \'parse\')
                                                                                  {
                                                                                    $in = array(E_PARSE);
                                                                                  }
                                                                                  elseif($filter == \'undefined\')
                                                                                  {
                                                                                    $in = array(E_NOTICE, E_USER_NOTICE);
                                                                                  }
                                                                                  elseif($filter == \'interopt\')
                                                                                  {
                                                                                    $in = array(E_STRICT);
                                                                                  }
                                                                                  elseif($filter == \'deprecated\')
                                                                                  {
                                                                                    $in = array(E_DEPRECATED, E_USER_DEPRECATED);
                                                                                  }
                                                                                  elseif($filter == \'database\')
                                                                                  {
                                                                                    $in = array(\'database\');
                                                                                  }

                                                                                  if(isset($in))
                                                                                  {
                                                                                    $table[\'db_vars\'][\'error_type\'] = $in;
                                                                                    $table[\'db_query\'] .= \' WHERE error_type IN({array_string:error_type})\';
                                                                                  }
                                                                                '),
                                                              ),
                                            ),
                             ));

    # The id of the error.
    $table->add_column('error_log', 'error_id', array(
                                                  'column' => 'error_id',
                                                  'label' => l('ID'),
                                                  'function' => create_function('$row', '
                                                                  global $base_url;

                                                                  return \'<a href="\'. $base_url. \'/index.php?action=admin&amp;sa=error_log&amp;id=\'. $row[\'error_id\']. \'" title="\'. l(\'View full error\'). \'">\'. $row[\'error_id\']. \'</a>\';'),
                                                  'width' => '6%',
                                                ));

    # When did it occur?
    $table->add_column('error_log', 'error_time', array(
                                                    'column' => 'error_time',
                                                    'label' => l('Time'),
                                                    'subtext' => l('The time at which the error occurred.'),
                                                    'function' => create_function('$row', '
                                                                    return timeformat($row[\'error_time\']);'),
                                                    'width' => '22%',
                                                  ));

    $table->add_column('error_log', 'error_message', array(
                                                       'column' => 'error_message',
                                                       'label' => l('Error message'),
                                                     ));

    $table->add_column('error_log', 'error_type', array(
                                                    'column' => 'error_type',
                                                    'label' => l('Type'),
                                                    'subtext' => l('The type of error which occurred.'),
                                                    'function' => create_function('$row', '
                                                                    $error_type = $row[\'error_type\'];

                                                                    if($error_type == 8)
                                                                      return l(\'<abbr title="Undefined variable">Undefined</abbr>\');
                                                                    elseif($error_type == 2)
                                                                      return l(\'General\');
                                                                    elseif($error_type == \'database\')
                                                                      return l(\'Database\');
                                                                    else
                                                                      return l(\'Other\');'),
                                                  ));
  }
}

if(!function_exists('admin_error_log_table_handle'))
{
  /*
    Function: admin_error_log_table_handle

    Performs the specified action on the selected errors, or not!

    Parameters:
      string $action - The action to perform.
      array $selected - The errors selected.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_error_log_table_handle($action, $selected)
  {
    global $api, $db;

    # Deleting all? Cool.
    if($action == 'truncate')
    {
      # A simple truncate will do the job!
      $db->query('
        TRUNCATE {db->prefix}error_log',
        array(), 'admin_error_log_truncate_query');
    }
    elseif($action == 'delete' && is_array($selected) && count($selected) > 0)
    {
      # Deleting the selected errors? Alrighty then!
      $db->query('
        DELETE FROM {db->prefix}error_log
        WHERE error_id IN({int_array:selected})',
        array(
          'selected' => $selected,
        ), 'admin_error_log_delete_query');
    }
    elseif($action == 'export')
    {
      # We will need these :-)
      $error_const = array(
                       E_ERROR => 'E_ERROR',
                       E_WARNING => 'E_WARNING',
                       E_PARSE => 'E_PARSE',
                       E_NOTICE => 'E_NOTICE',
                       E_USER_ERROR => 'E_USER_ERROR',
                       E_USER_WARNING => 'E_USER_WARNING',
                       E_USER_NOTICE => 'E_USER_NOTICE',
                       E_STRICT => 'E_STRICT',
                       E_DEPRECATED => 'E_DEPRECATED',
                       E_USER_DEPRECATED => 'E_USER_DEPRECATED',
                       'database' => 'database',
                     );
      ob_clean();
      header('Content-Type: text/plain; charset=utf-8');
      header('Content-Disposition: attachment; filename="error log.txt"');

      # Load the selected errors to download.
      $result = $db->query('
        SELECT
          *
        FROM {db->prefix}error_log
        WHERE error_id IN({array_int:selected})',
        array(
          'selected' => $selected,
        ), 'admin_error_log_export_query');

      $num_errors = $result->num_rows();
      $current = 0;
      while($row = $result->fetch_assoc())
      {
        echo (isset($error_const[$row['error_type']]) ? '['. $error_const[$row['error_type']]. '] ' : ''), $row['error_message'], l(' in %s on line %s', $row['error_file'], $row['error_line']), ($current + 1 < $num_errors ? "\r\n" : '');
        $current++;
      }

      # Stop executing, otherwise this won't work ;-).
      exit;
    }
  }
}

if(!function_exists('admin_error_log_view'))
{
  /*
    Function: admin_error_log_view

    Displays the specific error, showing more details information.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_error_log_view()
  {
    global $api, $base_url, $db, $member, $theme;

    $api->run_hooks('admin_error_log_view');

    # Can you view the error log? Don't try and be sneaky now!
    if(!$member->can('view_error_log'))
      # Get out of here!!!
      admin_access_denied();

    # Get the error id.
    $error_id = $_GET['id'];

    # Does the error exist?
    $result = $db->query('
      SELECT
        *
      FROM {db->prefix}error_log
      WHERE error_id = {int:error_id}
      LIMIT 1',
      array(
        'error_id' => $error_id,
      ), 'admin_error_log_view_query');

    if($result->num_rows() == 0)
    {
      # Nope, it does not exist.
      $theme->set_current_area('system_error_log');

      $theme->set_title(l('An error has occurred - Error log'));

      $theme->header();

      echo '
  <h1><img src="', $theme->url(), '/error_log-small.png" alt="" /> ', l('An error has occurred'), '</h1>
  <p>', l('The error you are trying to view does not exist. <a href="%s/index.php?action=admin&amp;sa=error_log" title="Back to error log">Back to error log</a>.', $base_url), '</p>';

      $theme->footer();
    }
    else
    {
      # Fetch the error information.
      $error = $result->fetch_assoc();

      # A list of error identifiers.
      $error_const = array(
                       E_ERROR => array('E_ERROR', l('Fatal run-time error')),
                       E_WARNING => array('E_WARNING', l('General')),
                       E_PARSE => array('E_PARSE', l('Compile-time parse error')),
                       E_NOTICE => array('E_NOTICE', l('Undefined variable')),
                       E_USER_ERROR => array('E_USER_ERROR', l('Fatal run-time error')),
                       E_USER_WARNING => array('E_USER_WARNING', l('General')),
                       E_USER_NOTICE => array('E_USER_NOTICE', l('Undefined variable')),
                       E_STRICT => array('E_STRICT', l('Interopability issue')),
                       E_DEPRECATED => array('E_DEPRECATED', l('Deprecated')),
                       E_USER_DEPRECATED => array('E_USER_DEPRECATED', l('Deprecated')),
                       'database' => array('database', l('Database')),
                     );

      $theme->set_current_area('system_error_log');

      $theme->set_title(l('Viewing error #%s - Error log', $error_id));

      $theme->header();

      echo '
  <h1><img src="', $theme->url(), '/error_log-small.png" alt="" /> ', l('Viewing error #%s', $error_id), '</h1>
  <p>', l('You are currently viewing error #%s. <a href="%s/index.php?action=admin&amp;sa=error_log" title="Back to error log">Back to error log</a>.', $error_id, $base_url), '</p>';

      # Output all the information.
      echo '
  <p style="margin-top: 10px;"><span class="bold">', l('Time:'), '</span> ', timeformat($error['error_time']), '</p>
  <p><span class="bold">', l('Type:'), '</span> ', isset($error_const[$error['error_type']]) ? $error_const[$error['error_type']][1] : l('Unknown'), ' ', ($error['error_type'] != $error_const[$error['error_type']][0] ? '('. $error_const[$error['error_type']][0]. ')' : ''), '</p>';

      # Was this a guest..?
      if($error['member_id'] == 0)
      {
        echo '
  <p><span class="bold">', l('Member:'), '</span> ', l('Guest (IP: %s)', $error['member_ip']), '</p>';
      }
      else
      {
        # Load up their information, if they exist.
        $members = $api->load_class('Members');
        $members->load($error['member_id']);
        $member = $members->get($error['member_id']);

        # Do they?
        if($member === false)
        {
          echo '
    <p><span class="bold">', l('Member:'), '</span> ', l('%s (No longer exists, IP: %s)', $error['member_name'], $error['member_ip']), '</p>';
        }
        else
        {
          echo '
    <p><span class="bold">', l('Member:'), '</span> ', l('<a href="%s/index.php?action=profile&amp;id=%s" target="_blank">%s</a> (IP: %s)', $base_url, $member['id'], $member['name'], $error['member_ip']), '</p>';
        }
      }

      # Now for the actual message, file and line.
      echo '
    <p><span class="bold">', l('Message:'), '</span></p>
    <p style="margin-left: 10px;">', $error['error_message'], '</p>
    <p><span class="bold">', l('File:'), '</span> ', $error['error_file'], ' (Line: ', $error['error_line'], ')</p>
    <p><span class="bold">', l('URL:'), '</span> <a href="', htmlchars($error['error_url']), '" target="_blank">', htmlchars($error['error_url']), '</a></p>';

      $theme->footer();
    }
  }
}

if(!function_exists('admin_themes'))
{
  /*
    Function: admin_themes

    Provides an interface for the selecting and uploading/downloading of themes.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_themes()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_dir, $theme_url;

    $api->run_hooks('admin_themes');

    # Can you view the error log? Don't try and be sneaky now!
    if(!$member->can('manage_themes'))
    {
      # Get out of here!!!
      admin_access_denied();
    }

    # Time for a Form, awesomeness!!!
    admin_themes_generate_form();
    $form = $api->load_class('Form');

    if(isset($_POST['install_theme_form']))
    {
      $form->process('install_theme_form');
    }

    # Setting the theme..?
    if(!empty($_REQUEST['set']))
    {
      $new_theme = basename($_REQUEST['set']);

      # Check to see if the theme exists.
      if(file_exists($theme_dir. '/'. $new_theme))
      {
        # Simple enough, set the theme.
        $settings->set('theme', $new_theme, 'string');
      }

      # Let's get you out of here now :-)
      redirect($base_url. '/index.php?action=admin&sa=themes');
    }

    $theme->set_current_area('manage_themes');

    $theme->set_title(l('Manage themes'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/manage_themes-small.png" alt="" /> ', l('Manage themes'), '</h1>
  <p style="margin-bottom: 20px;">', l('Here you can set the sites theme and also install themes as well.'), '</p>';

    # Get a listing of all the themes :-).
    $themes = theme_list();

    # Now load the information of the current theme.
    $current_theme = theme_load($theme_dir. '/'. $settings->get('theme', 'string', 'default'));

    echo '
  <div style="float: left; width: 200px;">
    <img src="', $theme_url, '/', $settings->get('theme', 'string', 'default'), '/image.png" alt="" title="', $current_theme['name'], '" />
  </div>
  <div style="float: right; width: 590px;">
    <h1 style="margin-top: 0px;">', l('Current theme: %s', $current_theme['name']), '</h1>
    <h3 style="margin-top: 0px;">', l('By %s', (!empty($current_theme['website']) ? '<a href="'. $current_theme['website']. '">' : ''). $current_theme['author']. (!empty($current_theme['website']) ? '</a>' : '')), '</h3>
    <p>', $current_theme['description'], '</p>
  </div>
  <div class="break">
  </div>
  <h1 style="margin-top: 20px;">', l('Available themes'), '</h1>
  <table class="theme_list">
    <tr>';

    # List all the themes ;-)
    $length = count($themes);
    for($i = 0; $i < $length; $i++)
    {
      $theme_info = theme_load($themes[$i]);

      if(($i + 1) % 3 == 0)
      {
        echo '
    </tr>
  </table>
  <table class="theme_list">
    <tr>';
      }

      echo '
      <td', (basename($theme_info['path']) == $settings->get('theme', 'string', 'default') ? ' class="selected"' : ''), '><a href="', $base_url, '/index.php?action=admin&amp;sa=themes&amp;set=', urlencode(basename($theme_info['path'])), '" title="', l('Set as site theme'), '"><img src="', $theme_url, '/', basename($theme_info['path']), '/image.png" alt="" title="', $theme_info['description'], '" /><br />', $theme_info['name'], '</a></td>';
    }

    echo '
    </tr>
  </table>

  <h1>', l('Install a theme'), '</h1>
  <p>', l('Below you can specify a file to upload or a URL at which to download a theme (tarballs and gzipped tarballs only).'), '</p>';

    $form->show('install_theme_form');

    $theme->footer();
  }
}

if(!function_exists('admin_themes_generate_form'))
{
  /*
    Function: admin_themes_generate_form

    Generates the form which allows themes to be installed.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_themes_generate_form()
  {
    global $api;

    $form = $api->load_class('Form');

    $form->add('install_theme_form', array(
                                       'action' => $base_url. '/index.php?action=admin&amp;sa=themes',
                                       'method' => 'post',
                                       'callback' => 'admin_themes_handle',
                                       'submit' => l('Install theme'),
                                     ));

    $form->add_field('install_theme_form', 'theme_file', array(
                                                           'type' => 'file',
                                                           'label' => l('From a file:'),
                                                           'subtext' => l('Select the theme file you want to install as a theme.'),
                                                         ));

    $form->add_field('install_theme_form', 'theme_url', array(
                                                          'type' => 'string',
                                                          'label' => l('From a URL:'),
                                                          'subtext' => l('Enter the URL of the theme you want to download and install.'),
                                                          'value' => !empty($_POST['theme_url']) ? $_POST['theme_url'] : 'http://',
                                                        ));
  }
}

if(!function_exists('admin_themes_handle'))
{
  /*
    Function: admin_themes_handle

    Handles the installation of the theme.

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      This function is overloadable.
  */
  function admin_themes_handle($data, &$errors = array())
  {
    global $api, $theme_dir;

    # Did you want to upload a theme?
    if(!empty($data['theme_file']) && is_array($data['theme_file']))
    {
      # Looks like you uploaded something, let's see what we can do!
      # First make a temporary file name.
      $filename = $theme_dir. '/'. uniqid('theme_'). '.tmp';

      # Now attempt to move the file.
      if(move_uploaded_file($data['theme_file']['tmp_name'], $filename))
      {
        # It ought to be a tarball ;-)
        $tar = $api->load_class('Tar');
        $tar->open($filename);

        # We are done with it, delete it!
        @unlink($filename);
      }
      else
      {
        # Uh oh! Didn't work!
        $errors[] = l('Failed to move the uploaded theme to the theme directory.');
      }
    }
    elseif(!empty($data['theme_url']) && strtolower($data['theme_url']) != 'http://')
    {

    }
    else
    {
      $errors[] = l('No file or URL specified.');
    }

    return false;
  }
}
?>