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

# Title: Registration Handler

if(!function_exists('register_view'))
{
  /*
    Function: register_view

    Displays the registration form, that is if registration is enabled.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function register_view()
  {
    global $api, $base_url, $member, $settings, $theme;

    $api->run_hook('register_view');

    # Are you logged in? You don't need to register an account because you obviously have one!
    if($member->is_logged())
    {
      header('Location: '. $base_url);
      exit;
    }

    # Is registration enabled?
    if(!$settings->get('registration_enabled', 'bool'))
    {
      $theme->set_title(l('Registration disabled'));
      $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));

      $api->run_hook('registration_disabled');

      $theme->header();

      echo '
      <h1>', l('Registration disabled'), '</h1>
      <p>', l('We apologize for the inconvience, but registration is currently not open to the public. Please check back at a later time.'), '</p>';

      $theme->footer();
      exit;
    }

    # Generate that form, pretty please!
    register_generate_form();
    $form = $api->load_class('Form');

    $theme->set_title(l('Register'));

    $theme->header();

    echo '
    <h1>', l('Register an account'), '</h1>
    <p>', l('Here you can register an account on %s and get access to certain features that only registered members are allowed to use.', $settings->get('site_name')), '</p>';

    $form->show('registration_form');

    $theme->footer();
  }
}

if(!function_exists('register_process'))
{
  /*
    Function: register_process

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function register_process()
  {
    global $api, $base_url, $member, $settings, $theme;

    $api->run_hook('register_process');

    # Already logged in? You don't need another account! ;)
    if($member->is_logged())
    {
      header('Location: '. $base_url);
      exit;
    }

    # Registration disabled? We will let register_view() handle that.
    if(!$settings->get('registration_enabled', 'bool'))
    {
      register_view();
      exit;
    }

    register_generate_form();
    $form = $api->load_class('Form');

    $success = $form->process('registration_form');

    # Processing failed? Then show the errors!
    if($success === false)
      # The register_view function will handle it nicely:
      register_view();
  }
}

if(!function_exists('register_generate_form'))
{
  /*
    Function: register_generate_form

    Generates the registration form, for of course, registration!

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function register_generate_form()
  {
    global $api, $base_url;
    static $generated = false;

    # Already been done? Don't need to do it again.
    if(!empty($generated))
      return;

    # Let's get that form going!
    $form = $api->load_class('Form');
    $form->add('registration_form', 'register_member', $api->apply_filter('registration_submit_url', $base_url. '/index.php?action=register2'));
    $form->set_submit('registration_form', l('Register account'));

    # Add the fields we need you to fill out.
    # Your requested member name, you know? That thing you use to login.
    $form->add_field('registration_form', 'member_name', array(
                                                           'type' => 'string-html',
                                                           'label' => l('Choose username:'),
                                                           'subtext' => l('Used to log in to your account.'),
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

    # Your password.
    $form->add_field('registration_form', 'member_pass', array(
                                                           'type' => 'password',
                                                           'label' => l('Password:'),
                                                           'subtext' => l('Be sure to use a strong password!'),
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
                                                               elseif($security == 1)
                                                                 $error = l(\'Your password must be at least 4 characters long and cannot contain your username.\');
                                                               else
                                                                 $error = l(\'Your password must be at least 5 characters long, cannot contain your username and contain at least 1 number.\');

                                                               return false;
                                                             }')));

    # Just to make sure you didn't type your password wrong or anything ;)
    $form->add_field('registration_form', 'pass_verification', array(
                                                                'type' => 'password',
                                                                'label' => l('Verify password:'),
                                                                'subtext' => l('Please enter your password here again.'),
                                                                'save' => false,
                                                              ));
    # Email address is important too!
    $form->add_field('registration_form', 'member_email', array(
                                                            'type' => 'string-html',
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

    # Add the agreement here... Eventually ;)

    # Now it has been generated, as once is enough.
    $generated = true;
  }
}

if(!function_exists('register_member'))
{
  /*
    Function: register_member

    Parameters:
      array $options - Receives the array containing all the new members
                       options and what not, from <Form>.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function register_member($options)
  {
    echo '<pre>';
    var_dump($options);
  }
}
?>