<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# register.php does what its file name leads to :P
# Registering of course!
#
# void register_view();
#   - Recieves nothing and returns nothing, all it does
#     is load up the registration page with the form and such
#
# void register_process();
#   - Just as register_view() does, it recieves and returns nothing
#     but it processes the registration information and such
#     and shows error messages if need be
#
# void register_activate();
#   - Recieves and returns nothing.
#   - register_activate(); is used to activate members accounts with their user id or name and activation code
#
# void register_resend();
#   - Just incase you never got that email activation letter... you can get it resent by
#     accessing ?action=resend :)
#   - Of course you either enter your username or email to get the email resent and it only
#     works if the account is not activated yet (DUH! :P)
#
# mixed register_user(array $user_options);
#   array $user_options - The options of the user which is to be registered,
#                        you give information like the username, password and stuff
#                        but for more details on whats accepted, look at the function :P
#   returns mixed - Depending upon what you give, it may return a bool or an int.
#                   If you do not give enough information, like the username or password
#                   or an email, you will get a bool of false because those ARE REQUIRED
#                   but if it was a success it will return an int, the new users id. 
#
# bool register_validate_name(string $username);
#   string $username - The username to be checked if it is allowed or not
#   returns bool - This will return true if the username is allowed, false
#                  if it is not
#
# bool register_validate_email(string $checkEmail);
#   string $checkEmail - The email to be checked if it is allowed or not
#   returns bool - Will return true if the email address is allowed, however
#                  false if its not allowed
#

function register_view()
{
  global $base_dir, $base_url, $db, $l, $page, $source_dir, $settings, $theme_url, $user;

  language_load('register');

  # So is registration enabled?
  if($settings['registration_enabled'] && !$user['is_logged'])
  {
    # Simple as being disabled below XD
    $page['title'] = $l['register_title'];

    # We need some JavaScript :)
    $page['scripts'][] = $theme_url . '/default/js/register.js';
    $page['scripts'][] = $theme_url . '/default/js/captcha.js';
    $page['js_vars']['password_minimum'] = $settings['password_minimum'];
    $page['js_vars']['password_recommended'] = $settings['password_recommended'];
    $page['js_vars']['password_strength'] = $settings['password_strength'];

    # We need the agreement... gotta see it.
    $page['agreement'] = is_readable($base_dir . '/agreement.txt') ? bbc(file_get_contents($base_dir . '/agreement.txt')): $l['error_agreement_missing'];

    # Ok, maybe not :P
    $page['username'] = !empty($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES) : '';
    $page['email'] = !empty($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES) : '';
    $page['show_email'] = !empty($_POST['show_email']) ? true : false;
    $page['accepted_agreement'] = !empty($_POST['accepted_agreement']) ? true : false;
    
    # Load the theme
    theme_load('register', 'register_view_show');
  }
  elseif(!$user['is_logged'])
  {
    # Set the title and call on the theme and we are done.
    $page['title'] = $l['register_disabled_title'];
    theme_load('register', 'register_view_show_disabled');
  }
  else
    # Silly pants, you're logged in :P
    redirect();
}

function register_process()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  language_load('register');

  # Gotta check if registration is enabled here too :P
  if($settings['registration_enabled'] && !$user['is_logged'])
  {
    # Are they trying to flood? :S
    if(!security_flood('register', 3)) {
      # Now that we have checked if they are flooding relog it
      security_log('register', 3);

      # Start with no errors
      $page['errors'] = array();

      # So lets make sure the username is filled in.
      if(empty($_POST['username']))
        $page['errors'][] = $l['error_username_empty'];
      # To long or to short?
      elseif(mb_strlen($_POST['username']) < 3 || mb_strlen($_POST['username']) > 80)
        $page['errors'][] = $l['error_username_length'];
      # Or maybe the name is in use or not allowed?
      elseif(!register_validate_name($_POST['username']))
        $page['errors'][] = $l['error_username_taken'];

      # Now the password :)
      if(empty($_POST['passwrd']))
        $page['errors'][] = $l['error_password_empty'];
      # Must be at least 4 characters... don't care about the length
      elseif(mb_strlen($_POST['passwrd']) < 4)
        $page['errors'][] = $l['error_password_length'];

      # Passwords match..?
      if(!empty($_POST['passwrd']) && mb_strlen($_POST['passwrd']) > 3 && (empty($_POST['vPasswrd']) || $_POST['passwrd'] != $_POST['vPasswrd']))
        $page['errors'][] = $l['error_passwords_dont_match'];

      # The email...
      if(empty($_POST['email']))
        $page['errors'][] = $l['error_email_empty'];
      elseif(!register_validate_email($_POST['email']))
        $page['errors'][] = $l['error_email_disallowed'];
      elseif(!preg_match('/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i', $_POST['email']))
        $page['errors'][] = $l['error_email_invalid'];

      # And of course the CAPTCHA
      if($settings['captcha_strength'] && empty($_POST['captcha']))
        $page['errors'][] = $l['error_captcha_empty'];
      elseif($settings['captcha_strength'] && !captcha_check($_POST['captcha']))
        $page['errors'][] = $l['error_captcha_invalid'];

      # You gotta accept the TOS, ACCEPT IT!
      if(empty($_POST['accepted_agreement']))
        $page['errors'][] = $l['error_didnt_accept_agreement'];

      # Any errors? If not we can register them :)
      if(!count($page['errors']))
      {
        # In SnowCMS v1 we have a function to register the users... makes it nicer :)
        $user_options = array(
          'loginName' => $_POST['username'],
          'password' => $_POST['passwrd'],
          'email' => $_POST['email'],
          'displayName' => $_POST['username'],
          'reg_time' => time_utc(),
          'reg_ip' => $user['ip'],
          'group_id' => $settings['registration_group'],
          'birthdate' => null,
          'language' => '',
          'timezone' => $settings['timezone'],
          'site_name' => '',
          'site_url' => '',
          'show_email' => !empty($_POST['show_email']),
          'icq' => '',
          'aim' => '',
          'msn' => '',
          'yim' => '',
          'gtalk' => '',
          # We want to follow the settings
          'override_activation' => false,
          # Checked that ourselves :P
          'check_username' => false,
          # This too ;)
          'check_email' => false,
        );

        # This will register them :)
        register_user($user_options);

        # Just a couple variables :P
        $page['username'] = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $page['email'] = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

        # The title :)
        $page['title'] = $l['register_title'];
        $page['no_index'] = true;

        # Now show the page according to activation type
        if($settings['account_activation'] == 0)
        {
          # Instantly activated
          theme_load('register', 'register_process_show_welcome');
        }
        elseif($settings['account_activation'] == 1)
        {
          # Email Activation
          theme_load('register', 'register_process_show_sent');
        }
        else
          # Admin Approval :P
          theme_load('register', 'register_process_show_admin');
      }
      else
        # Call on the register_view(); function which will show the errors :)
        register_view();
    }
    else
    {
      # Stop ):
      $page['title'] = $l['register_flood_title'];
      $page['no_index'] = true;

      theme_load('register', 'register_process_show_flood');
    }
  }
  elseif(!$user['is_logged'])
  {
    # Set the title and call on the theme and we are done.
    $page['title'] = $l['register_disabled_title'];

    theme_load('register', 'register_view_show_disabled');
  }
  else
  {
    # Your already registered :|
    redirect();
  }
}

function register_activate()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # You can only do this if Email Activation is enabled...
  if($settings['account_activation'] == 1)
  {
    language_load('register');

    # You trying to activate your account..?
    if((!empty($_REQUEST['id']) || !empty($_REQUEST['user'])) && !empty($_REQUEST['code']))
    {
      # So lets see if the details are right.
      $db->query("
        UPDATE {$db->prefix}members
        SET isActivated = 1
        WHERE (member_id = %member_id OR ". ($db->case_sensitive ? 'LOWER(loginName) = LOWER(%loginName)' : 'loginName = %loginName'). ") AND isActivated = 0 AND acode = %code",
        array(
          'member_id' => array('int', !empty($_REQUEST['id']) ? $_REQUEST['id'] : 0),
          'loginName' => array('string', !empty($_REQUEST['user']) ? $_REQUEST['user'] : ''),
          'code' => array('string', $_REQUEST['code'])
        ));

      # Anything updated?
      if($db->affected_rows())
      {
        # Activated :P
        $page['title'] = $l['register_activation_title'];
        theme_load('register', 'registe_activate_show_success');
      }
      else
      {
        # Failed.. :/
        # So say there was an error :P
        $page['error'] = true;
        $page['title'] = $l['register_activation_title'];

        # Now load the activation form which will show the error
        theme_load('register', 'register_activate_show');
      }
    }
    else
    {
      # Show the form ;)
      # A couple of variables...
      $page['user'] = htmlspecialchars(!empty($_REQUEST['user']) ? $_REQUEST['user'] : '', ENT_QUOTES, 'UTF-8');
      $page['code'] = htmlspecialchars(!empty($_REQUEST['code']) ? $_REQUEST['code'] : '', ENT_QUOTES, 'UTF-8');

      # Page title...
      $page['title'] = $l['register_activation_title'];
      theme_load('register', 'register_activate_show');
    }
  }
  else
    # Just redirect otherwise...
    redirect();
}

function register_resend()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  language_load('register');

  # OOPS! Needs flood protection :P

  # Only if email activation is what is set...
  if($settings['account_activation'] == 1)
  {
    # Resending already? o_o
    if(!empty($_REQUEST['email'])) {
      # So lets see if the user exists, or at least is not yet activated.
      $result = $db->query("
        SELECT
          mem.member_id, mem.loginName, mem.email, mem.passwrd, mem.isActivated
        FROM {$db->prefix}members AS mem
        WHERE ". ($db->case_sensitive ? '(LOWER(mem.loginName) = LOWER(%loginName OR mem.email = %loginName))' : '(mem.loginName = %loginName OR mem.email = %loginName)'). " AND mem.isActivated = 0",
        array(
          'loginName' => array('string', $_REQUEST['email'])
        ));

      # Hmm...
      if($db->num_rows($result))
      {
        $row = $db->fetch_assoc($result);
        # It does exist so now we want to resend the email activation, yay.
        # We want to regenerate the activation code of course though.
        if(mt_rand(23, 30) == 25 || mt_rand(1, 4) == 3)
          $acode = sha1(mt_rand(999, 1005). $row['passwrd']. filemtime($source_dir. '/Meta.php'). $row['member_id']. (mt_rand(1, 2) == 1 ? $source_dir : $base_url));
        else
          $acode = sha1($row['loginName']. filemtime($source_dir. '/Register.php'). mt_rand(100, 999). $row['member_id']. time_utc());

        # Now randomly do some more randomness ;)
        $acode = mb_substr(mb_substr($acode, mt_rand(0, 10), mt_rand(30, 40)), 0, 10);

        # Don't forget to save the new activation code to the user! ._.
        $db->query("
          UPDATE {$db->prefix}members
          SET acode = %code
          WHERE member_id = %member_id",
          array(
            'code' => array('string', $acode),
            'member_id' => array('int', $row['member_id'])
          ));

        # Now parse the "template"
        $l['register_email_activation_tpl'] = strtr($l['register_email_activation_tpl'], array('{$LOGIN_NAME}' => $row['loginName'], '{$MEMBER_ID}' => $row['member_id'], '{$ACODE}' => $acode));

        # Template should be all done, now we need to send the email.
        # Get Mail.php which has the stuff to send it XD
        require_once($source_dir. '/Mail.php');

        # Now send it :P
        sendmb_send_mail($row['email'], $l['register_email_subject'], $l['register_email_activation_tpl']);

        # Now that that is done we can show that it was sent...
        $page['no_index'] = true;
        $page['success'] = true;

        # Now the theme to show it and thats it :)
        $page['title'] = $l['register_resend_title'];
        theme_load('register', 'register_resend_show');
      }
      else
      {
        # There was an error! D:!
        $page['error'] = true;

        # Don't index this please.
        $page['no_index'] = true;

        # Set the attempted name/email
        $page['email'] = htmlspecialchars(!empty($_REQUEST['email']) ? $_REQUEST['email'] : '', ENT_QUOTES, 'UTF-8');

        # Now the other stuff...
        $page['title'] = $l['register_resend_title'];
        theme_load('register', 'register_resend_show');
      }
    }
    else
    {
      # Just set the title and what not...
      $page['title'] = $l['register_resend_title'];

      theme_load('register', 'register_resend_show');
    }
  }
  else
  {
    # Don't index this!
    $page['no_index'] = true;

    # The title then load the theme and I think we are done here...
    $page['title'] = $l['register_resend_error_title'];

    theme_load('register', 'register_resend_show_disabled');
  }
}

function register_user($user_options)
{
  global $db, $l, $settings, $source_dir, $user;

  #
  # Register the user! Woo!
  # As said above, here is a list of accepted parameters in the
  # user_options array:
  #   string loginName => The name the user will use to login with (required)
  #   string password => Password of course... just what it is, don't encrypt it! (required)
  #   string email => The users email (required)
  #   string displayName => The name displayed, if not given or blank their username will be used
  #   int reg_time => timestamp of registration time... if its blank current will be used
  #   string reg_ip => their IP at registration... if blank the current one will be used
  #   string group_id => The users group... if nothing it will be set to $settings['registration_group']
  #   string birthdate => Their birthday :D (YYYY-MM-DD)
  #   string language => the users language, if blank the sites default is used
  #   string site_name => their site name, if blank its left blank :P
  #   string site_url => the url to their site, if blank its left blank xD
  #   bool show_email => Whether or not to show their email to the public.
  #   int icq => Their ICQ number (Whoa, people still use that thing? lol)
  #   string aim => Their AIM username
  #   string msn => Their MSN username
  #   string yim => Their Yahoo! Username
  #   string gtalk => Their Google Mail email :P
  #   bool override_activation => true if you dont want any activation required, false if you want it to go by the settings
  #   bool check_username => Whether to check if the username is in use or not or not allowed
  #   bool check_email => Whether to check if the email is allowed or not or in use
  #
  
  # Make sure a couple things are filled out...
  if(!empty($user_options['loginName']) && !empty($user_options['password']) && !empty($user_options['email']))
  {
    # Now we need to fill in some details if they aren't already.
    if(empty($user_options['displayName']))
      $user_options['displayName'] = $user_options['loginName'];
    if(empty($user_options['reg_time']))
      $user_options['reg_time'] = time_utc();
    if(empty($user_options['reg_ip']))
      $user_options['rep_ip'] = $user['ip'];
    if(empty($user_options['group_id']))
      $user_options['group_id'] = $settings['registration_group'];
    if(empty($user_options['language']))
      $user_options['language'] = $settings['default_language'];

    # Now we need to verify a couple things...
    $success = true;
    if($user_options['check_username'])
    {
      if(!register_validate_name($user_options['loginName']))
        # No success :P
        $success = false;
    }

    # Check email now... if it's still a success
    if($success && $user_options['check_email'])
    {
      if(!register_validate_email($user_options['email']))
        # Nope... not allowed
        $success = false;
    }

    # If its still a success, well then we can make the account...
    if($success)
    {
      # Hash the password with SHA-1 first...
      $user_options['password'] = sha1($user_options['password']);

      # Untampered with email XD
      $userEmail = $user_options['email'];

      # Make the show_email thing right
      $user_options['show_email'] = $user_options['show_email'] ? 1 : 0;

      # One more thing :P Are they activated..?
      $user_options['isActivated'] = $user_options['override_activation'] ? 1 : ($settings['account_activation'] == 0 ? 1 : 0);

      # Now we may crate the user :)
      $result = $db->insert('insert', $db->prefix. 'members',
        array(
          'loginName' => 'string', 'passwrd' => 'string-40', 'email' => 'string',
          'displayName' => 'string', 'reg_time' => 'int', 'reg_ip' => 'string-16',
          'group_id' => 'int', 'birthdate' => 'string-10', 'is_activated' => 'int',
          'language' => 'string', 'site_name' => 'string', 'site_url' => 'string',
          'show_email' => 'int', 'icq' => 'int', 'aim' => 'string', 
          'msn' => 'string', 'yim' => 'string', 'gtalk' => 'string',
          'theme' => 'string', 'format_datetime' => 'string',
          'format_date' => 'string', 'format_time' => 'string',
          'timezone' => 'int', 'dst' => 'int', 'preference_quick_reply' => 'int',
          'preference_avatars' => 'int', 'preference_signatures' => 'int',
          'preference_post_images' => 'int', 'preference_emoticons' => 'int',
          'preference_return_topic' => 'int', 'preference_pm_display' => 'int',
          'preference_recently_online' => 'int',
          'preference_thousands_separator' => 'string-1',
          'preference_decimal_point' => 'string-1',
          'preference_today_yesterday' => 'int', 'per_page_topics' => 'int',
          'per_page_posts' => 'int', 'per_page_news' => 'int',
          'per_page_downloads' => 'int', 'per_page_comments' => 'int',
          'per_page_members' => 'int',
        ),
        array(
          $user_options['loginName'], $user_options['password'], $user_options['email'],
          $user_options['displayName'], $user_options['reg_time'], $user_options['reg_ip'],
          $user_options['group_id'], $user_options['birthdate'], $user_options['isActivated'],
          $user_options['language'], $user_options['site_name'], $user_options['site_url'],
          $user_options['show_email'], (int)$user_options['icq'], $user_options['aim'],
          $user_options['msn'], $user_options['yim'], $user_options['gtalk'],
          $settings['theme'], $settings['format_datetime'], $settings['format_date'],
          $settings['format_time'],  $settings['timezone'], $settings['dst'],
          $settings['preference_quick_reply'], $settings['preference_avatars'],
          $settings['preference_signatures'], $settings['preference_post_images'],
          $settings['preference_emoticons'], $settings['preference_return_topic'],
          $settings['preference_pm_display'], $settings['preference_recently_online'],
          $settings['preference_thousands_separator'], $settings['preference_decimal_point'],
          $settings['preference_today_yesterday'], $settings['per_page_topics'],
          $settings['per_page_posts'], $settings['per_page_news'],
          $settings['per_page_downloads'], $settings['per_page_comments'],
          $settings['per_page_members'],
        ),
        array());
      
      # Update the total amount of members
      update_settings(array('total_members' => $settings['total_members'] + 1));

      # Was it a success?
      if($result)
      {
        # Yeah it was... get their user id.
        $member_id = $db->last_id();
        $success = $member_id;

        # Now do we need to send off an activation email?
        if($settings['account_activation'] == 1)
        {
          # Why yes we do.
          # But lets first make their activation key... randomly XD
          if(mt_rand(1, 6) == 5)
            # We are gonna make it this way :P
            $user_options['acode'] = sha1($member_id. filemtime($source_dir. '/CoreCMS.php'). $user_options['password']. mt_rand(1, 100). time_utc());
          else
            # Another way... oOoOoOo!
            $user_options['acode'] = sha1($user_options['password']. $user_options['show_email']. time_utc(). mt_rand(1, 46). filemtime($source_dir. '/Register.php'));

          # Wasn't that cool? :D Yeah I know your excited -.-
          # Oh yeah, and we only want part of the activation code... random too! =D
          $user_options['acode'] = mb_substr(mb_substr($user_options['acode'], mt_rand(0, 5), mt_rand(35, 40)), 0, 20);

          # Now update it to their account or its of no use.
          $db->query("
            UPDATE {$db->prefix}members
            SET acode = %acode'
            WHERE member_id = %member_id",
            array(
              'acode' => array('string', $user_options['acode']),
              'member_id' => array('int', $member_id)
            ));

          language_load('register');
          # Now parse the "template"
          $l['register_email_activation_tpl'] = strtr($l['register_email_activation_tpl'], array('{$LOGIN_NAME}' => stripslashes($user_options['loginName']), '{$MEMBER_ID}' => $member_id, '{$ACODE}' => $user_options['acode']));

          # Template should be all done, now we need to send the email.
          # Get Mail.php which has the stuff to send it XD
          require_once($source_dir. '/Mail.php');

          # Now send it :P
          $success = sendmb_send_mail($userEmail, $l['register_email_subject'], $l['register_email_activation_tpl']);
        }
      }
      else
        # Nope O.o
        $success = false;
    }

    # Return whatever it is it is :P
    return $success;
  }
  else 
    # Nope :P
    return false;
}

function register_validate_name($username)
{
  global $db, $settings;

  # Check reserved names ;) 
  # Start out with its allowed first though.
  $name_allowed = true;

  # Lets check the username length.
  if(mb_strlen($username) < 3 || mb_strlen($username) > 80)
    $name_allowed = false;

  # Name still allowed..?
  if($name_allowed)
  {
    # Sanitize the username partially...
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

    # Explode at the line breaks :)
    $reserved_names = @explode("\n", mb_strtolower($settings['reservedNames']));

    if(count($reserved_names))
    {
      foreach($reserved_names as $name)
      {
        # Trim it up... and lowercase it
        $name = trim($name);

        # Now check if its the same :P
        if($username == $name)
        {
          $name_allowed = false;

          # You can stop now ;)
          break;
        }
      }
    }
  }
  
  # If the name is still allowed lets check if
  # someone else might be using it, either display or
  # username ;)
  if($name_allowed)
  {
    $result = $db->query("
      SELECT
        mem.loginName, mem.displayName
      FROM {$db->prefix}members AS mem
      WHERE ". ($db->case_sensitive ? 'LOWER(mem.loginName) = LOWER(%username) OR LOWER(mem.displayName) = LOWER(%username)' : 'mem.loginName = %username OR mem.displayName = %username'),
      array(
        'username' => array('string', htmlspecialchars_decode($username, ENT_QUOTES)),
      ));

    # Anything?
    if($db->num_rows($result))
      $name_allowed = false;
  }

  # now return whether or not its allowed
  return $name_allowed;
}

function register_validate_email($check_email)
{
  global $db, $settings;

  # Check to see if the email is allowed in a couple different ways
  # Through settings in the ACP you can ban emails and email domains :)

  # Lower case the email...
  $check_email = htmlspecialchars(mb_strtolower($check_email), ENT_QUOTES, 'UTF-8');

  # Start out with a success so far.
  $email_allowed = true;

  # Check if the email is banned.
  $disallowed_emails = @explode("\n", mb_strtolower($settings['disallowed_emails']));

  if(count($disallowed_emails))
  {
    foreach($disallowed_emails as $email)
    {
      # Trim it up...
      $email = trim($email);

      if($check_email == $email)
      {
        # Disallowed :P
        $email_allowed = false;

        # K, you can stop now.
        break;
      }
    }
  }

  # Still allowed? I can change that :)
  if($email_allowed)
  {
    # Check the domains now
    $disallowed_domains = @explode("\n", mb_strtolower($settings['disallowed_email_domains']));

    if(count($disallowed_domains))
    {
      # Get the emails domain...
      list(, $email_domain) = @explode('@', $check_email);

      # Lowercase it and we're good to go
      $email_domain = trim(mb_strtolower($email_domain));

      foreach($disallowed_domains as $domain)
      {
        # Trim it up...
        $domain = trim($domain);

        if($email_domain == $domain)
        {
          # That domain isn't allowed xD
          $email_allowed = false;

          # Stop now.
          break;
        }
      }
    }
  }

  # Still allowed? o_o
  # Now see if its in use maybe..?
  if($email_allowed)
  {
    $result = $db->query("
      SELECT
        mem.email
      FROM {$db->prefix}members AS mem
      WHERE ". ($db->case_sensitive ? 'LOWER(mem.email) = LOWER(%check_email)' : 'mem.email = %check_email'),
      array(
        'check_email' => array('string', htmlspecialchars_decode($check_email, ENT_QUOTES)),
      ));
    # Anything?
    if($db->num_rows($result))
      $email_allowed = false;
  }

  # Now return it.
  return $email_allowed;
}
?>