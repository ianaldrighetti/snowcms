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

# Title: Password reminder

if(!function_exists('reminder_view'))
{
  /*
    Function: reminder_view

    Lost your password? That's fine! You can request a new one through
    this. Of course, it will only work if email works :P

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function reminder_view()
  {
    global $api, $member, $settings, $theme;

    $api->run_hooks('reminder_view');

    if($member->is_logged())
    {
      header('Location: '. baseurl);
      exit;
    }

    # We just need a form for you to enter your username ;)
    $form = $api->load_class('Form');

    $form->add('reminder_form', array(
                                  'action' => baseurl. '/index.php?action=reminder',
                                  'method' => 'post',
                                  'callback' => 'reminder_process',
                                  'submit' => l('Request reminder'),
                                ));

    # Member name field, the only one!
    $form->add_field('reminder_form', 'member_name', array(
                                                       'type' => 'string',
                                                       'label' => l('Username:'),
                                                       'subtext' => l('The username you use to log in.'),
                                                       'function' => create_function('&$value, $form_name, &$error', '
                                                                       if(empty($value))
                                                                       {
                                                                         $error = l(\'Please enter a username.\');
                                                                         return false;
                                                                       }

                                                                       return true;'),
                                                       'value' => !empty($_POST['member_name']) ? $_POST['member_name'] : '',
                                                     ));


    # Submitting the form? Process it...
    if(!empty($_POST['reminder_form']))
      $form->process('reminder_form');

    $theme->set_title(l('Request a password reset'));

    $theme->header();

    echo '
      <h1>', l('Request a password reset'), '</h1>
      <p>', l('Did you forget your password? No problem! Just enter your username into the form below, and you can then start the process of resetting your password. Due to how the passwords are stored, we cannot give you your currently stored password.'), '</p>';

    if(strlen($api->apply_filters('reminder_message', '')) > 0)
    {
      echo '
      <div id="', $api->apply_filters('reminder_message_id', 'reminder_success'), '">
        ', $api->apply_filters('reminder_message', ''), '
      </div>';
    }

    $form->show('reminder_form');

    $theme->footer();
  }
}

if(!function_exists('reminder_process'))
{
  /*
    Function: reminder_process

    Sends the email containing the link to reset your password.

    Parameters:
      array $remind
      array &$errors

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      This function is overloadable.
  */
  function reminder_process($remind, &$errors = array())
  {
    global $api, $member, $_POST, $settings;

    # We will need the name_to_id method in the Members class, along with others...
    $members = $api->load_class('Members');

    $member_id = $members->name_to_id($remind['member_name']);

    if(empty($member_id))
    {
      $errors[] = l('The name you supplied does not exist.');
      return false;
    }

    # Get some member information first.
    $members->load($member_id);
    $member_info = $members->get($member_id);

    # Have you requested a password reminder in the last hour? Slow down!!!
    if(isset($member_info['data']['reminder_requested_time']) && ($member_info['data']['reminder_requested_time'] + 86400) > time_utc())
    {
      $errors[] = l('Sorry, but you can only request a password reminder every hour.');
      return false;
    }

    # Alrighty then, we need to generate a reminder key ;)
    $reminder_key = sha1(time_utc(). $members->rand_str(mt_rand(32, 64)). (microtime(true) / mt_rand(4, 16)));

    $members->update($member_id, array(
                                   'data' => array(
                                               'reminder_requested' => 1,
                                               'reminder_requested_time' => time_utc(),
                                               'reminder_requested_ip' => $member->ip(),
                                               'reminder_requested_user_agent' => $_SERVER['HTTP_USER_AGENT'],
                                               'reminder_key' => $reminder_key,
                                             ),
                                 ));

    # Email time! :) and that's pretty much it!
    $mail = $api->load_class('Mail');
    $mail->send($member_info['email'], l('Reset your password instructions'), l("Here there %s, this email comes from %s.\r\n\r\nYou are receiving this email as some has requested a password change for your account at %s. If you did not request this password reset, please contact the site administrators promptly.\r\n\r\nIf you did request this password change however, simply click the link below to proceed with the password change:\r\n%s/index.php?action=reminder2&id=%s&code=%s\r\n\r\nPlease realize that this link will only work for the next 24 hours.", $member_info['username'], baseurl, $settings->get('site_name', 'string'), baseurl, $member_info['id'], $reminder_key));

    $api->add_filter('reminder_message', create_function('$value', '
                                           return l(\'Further instructions have been sent to your account\\\'s email address, which cannot be disclosed for security reasons. Be sure to click the link within the next 24 hours or else it will be rendered useless.\');'));

    unset($_POST['member_name']);

    $api->run_hook('post_reminder_process', array($member_info));

    return true;
  }
}

if(!function_exists('reminder_view2'))
{
  /*
    Function: reminder_view2

    Handles the actual changing of the password, as long as the information
    supplied is right... That is.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function reminder_view2()
  {
    global $api, $member, $theme;

    $api->run_hook('reminder_view2');

    if($member->is_logged())
    {
      header('Location: '. baseurl);
      exit;
    }

    # Do you have the data required?
    if(!empty($_REQUEST['id']) && !empty($_REQUEST['code']))
    {
      $members = $api->load_class('Members');

      # Does that member even exist? Let's check ;)
      $members->load($_REQUEST['id']);
      $member_info = $members->get($_REQUEST['id']);

      if(!empty($member_info))
      {
        # Well, seems alright... Now to see if the code has expired, that is, if a password request was made!
        if(isset($member_info['data']['reminder_requested_time']) && ($member_info['data']['reminder_requested_time'] + 86400) > time_utc() && $member_info['data']['reminder_requested'] == 1)
        {
          # Make the form to display the form to enter your new password :)
          $form = $api->load_class('Form');

          $form->add('reset_password_form', array(
                                              'action' => baseurl. '/index.php?action=reminder2',
                                              'method' => 'post',
                                              'callback' => 'reminder_process2',
                                              'submit' => l('Reset password'),
                                            ));

          $form->add_field('reset_password_form', 'new_password', array(
                                                                    'type' => 'password',
                                                                    'label' => l('Your new password:'),
                                                                    'function' => create_function('&$value, $form_name, &$error', '
                                                                              if($value != $_POST[\'verify_password\'])
                                                                              {
                                                                                $error = l(\'The passwords you supplied did not match.\');
                                                                                return false;
                                                                              }

                                                                              return true;'),
                                                                  ));

          $form->add_field('reset_password_form', 'verify_password', array(
                                                                       'type' => 'password',
                                                                       'label' => l('Verify your password:'),
                                                                       'subtext' => l('Type your password again, just for verification.'),
                                                                       'save' => false,
                                                                     ));

          $form->add_field('reset_password_form', 'id', array(
                                                          'type' => 'hidden',
                                                          'value' => $_REQUEST['id'],
                                                        ));

          $form->add_field('reset_password_form', 'code', array(
                                                            'type' => 'hidden',
                                                            'value' => $_REQUEST['code'],
                                                          ));

          # Process the form?
          if(!empty($_POST['reset_password_form']))
            $form->process('reset_password_form');

          $theme->set_title(l('Set your new password'));

          $theme->header();

          echo '
      <h1>', l('Set your new password'), '</h1>
      <p>', l('Simply enter your new password below to reset your password.'), '</p>';

          $form->show('reset_password_form');

          $theme->footer();
          exit;
        }
      }
    }

    # We will just say the information you submitted was wrong, but calling exit; before this will stop it ;)
    $theme->set_title(l('An error has occurred'));

    $theme->header();

    echo '
      <h1>', l('An error has occurred'), '</h1>
      <p>', l('Sorry, but your password change request could not be completed as the information you supplied was incorrect or the password request has expired.'), '</p>';

    $theme->footer();
  }
}

if(!function_exists('reminder_process2'))
{
  /*
    Function: reminder_process2

    Actually changes the password of the specified user.

    Parameters:
      array $reset
      array &$errors

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      This function is overloadable.
  */
  function reminder_process2($reset, &$errors = array())
  {
    global $api;

    $api->run_hook('reminder_process2');

    $members = $api->load_class('Members');

    # Load up the members information, we need it!
    $members->load($reset['id']);

    $member_info = $members->get($reset['id']);

    # Check to make sure their password is allowed.
    if(!$members->password_allowed($member_info['username'], $reset['new_password']))
    {
      $errors[] = l('Sorry, but the password you supplied is invalid.');
      return false;
    }

    # Seems to be all good. Update the members information.
    $members->update($reset['id'], array(
                                     'member_name' => $member_info['username'],
                                     'member_pass' => $reset['new_password'],
                                     'member_hash' => $members->rand_str(16),
                                     'data' => array(
                                                 'reminder_requested' => 0,
                                               ),
                                   ));

    $api->run_hook('password_reset', array($member_info));

    # Alright, redirecting you to the login screen :)
    header('Location: '. baseurl. '/index.php?action=login&member_name='. urlencode($member_info['username']));
    exit;
  }
}
?>