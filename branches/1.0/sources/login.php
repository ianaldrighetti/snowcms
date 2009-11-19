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
# All logging in verification is handled here sort of, except
# init_user(); in corecms.php
#
# void login_view();
#   - Just shows the login form to login with... :P
#
# void login_process();
#   - Processes the login information submitted and sets the
#     cookies and such to stay logged in with :)
#
# void login_cookie(int $member_id, string $hashedPassword[, int $expires = 0]);
#   int $member_id - The member ID to save to the cookie
#   string $hashedPassword - The SHA-1 hashed password of the user
#   int $expires - the 10 int timestamp at when the cookie expires, 0 is the
#                  default which means it will expire once they close their browser
#   returns void - Returns nothing.
#
# void login_reminder();
#   - If you forgot your password you use this to request a new one, this only does step 1
#     the function below (login_reminder2()) processes the reminder details sent in the email
#
# void login_reminder2();
#   - Once you click the link in the email, or whatever :P, the details are processed, and if
#     they are correct you are allowed to set a new password for that account ;)
#
# void login_logout();
#   - Simply logs out the user and destroys their current session
#

function login_view()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # You logged in already? O.o
  if(!$user['is_logged'])
  {
    # Simple... load the page is all :)
    language_load('login');
    $page['title'] = $l['login_title'];

    # Oh yeah, possible username too =P
    $page['username'] = !empty($_REQUEST['username']) ? htmlspecialchars($_REQUEST['username'], ENT_QUOTES, 'UTF-8') : '';
    
    # Now load the theme.
    theme_load('login', 'login_view_show');
  }
  else
    # Just redirect to the home page.
    redirect();
}

function login_process()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # Login language
  language_load('login');
  
  # You floodin?
  $is_flooding = security_flood('login', 3);

  # Now to process the information you have submitted...
  if(!empty($_REQUEST['username']) && (!empty($_REQUEST['passwrd']) || !empty($_REQUEST['hashed_passwrd'])) && !$is_flooding)
  {
    # Lets stop brute forcing :)
    security_log('login', 5);

    # Hmmm... If the hashed password field is empty, use the other.
    if(empty($_REQUEST['hashed_passwrd']))
      $password = sha1($_REQUEST['passwrd']);
    else
      $password = $_REQUEST['hashed_passwrd'];

    # Now lets see about if this user even exists.
    # We'll check with the email too, since they might want to login with that.
    $result = $db->query("
      SELECT
        member_id, loginName, passwrd, email, is_activated, suspended, is_banned, group_id, post_group_id
      FROM {$db->prefix}members
      WHERE ". ($db->case_sensitive ? 'LOWER(loginName) = LOWER(%username) OR LOWER(email) = LOWER(%username)' : 'loginName = %username OR email = %username'). "
      LIMIT 1",
      array(
        'username' => array('string-80', $_REQUEST['username'])
      ));

    # Anything?
    if($db->num_rows($result))
    {
      $row = $db->fetch_assoc($result);

      # Start out with a failure.
      $loggedIn = false;

      # Is it right?
      if(sha1($row['passwrd']. $_SESSION['old_loginHash']) == $password && !empty($user['loginHash']))
        # Yay! The secure(r :P) way!
        $loggedIn = true;
      elseif($password == $row['passwrd'])
        # Success :P but the not so secure way xD
        $loggedIn = true;

      # Okay... they were logged in successfully
      # But wait! Make sure they aren't suspended, banned or the account is not activated :P
      if($loggedIn && $row['is_activated'] && !$row['is_banned'] && $row['suspended'] <= time_utc())
      {
        # So like, lets see if they set the expiration time XD
        $expiration_time = time_utc();
        if(empty($_POST['expires']))
          # Not set, well just for this session ;)
          $expiration_time = 0;
        elseif($_POST['expires'] == -1)
          # Forever? :0 Lets do about 5 years then :P
          $expiration_time += 160704000;
        else
          # Whatever you want...
          $expiration_time += (int)$_POST['expires'];

        # Set the cookie
        login_cookie($row['member_id'], $row['passwrd'], $expiration_time);

        # Set the session data too.
        $_SESSION['member_id'] = $row['member_id'];
        $_SESSION['passwrd'] = $row['passwrd'];

        # Update their last login and last IP
        $db->query("
          UPDATE {$db->prefix}members
          SET last_login = %now,
            last_ip = %ip, adminSc = ''
          WHERE member_id = %member_id
          LIMIT 1",
          array(
            'now' => array('int', time_utc()),
            'ip' => array('string-16', $user['ip']),
            'member_id' => array('int', $row['member_id'])
          ));

        # Now remove their old row in `online`
        $db->query("
          DELETE FROM {$db->prefix}online
          WHERE session_id = %session_id
          LIMIT 1",
          array(
            'session_id' => array('string-40', $user['sc']),
          ));

        # We may need to do a little something special for those who can view the
        # Admin CP, we don't want to prompt them to verify again until 15 minutes
        # after they first login, cause that makes antimatter15 mad >:(
        $result = $db->query("
          SELECT
            COUNT(*)
          FROM {$db->prefix}permissions
          WHERE (group_id = %member_group OR group_id = %member_post_group) AND can = 1 AND what = 'view_admin_panel'
          LIMIT 2",
          array(
            'member_group' => array('int', $row['group_id']),
            'member_post_group' => array('int', $row['post_group_id']),
          ));
        @list($found) = $db->fetch_row($result);

        # Found anything? Or are you an Administrator? OoOoOO!
        if($found > 0 || $row['group_id'] == 1)
        {
          # Set the last time they 'verified'
          $_SESSION['last_admin_verify'] = time();

          # We need to generate and administrative session id.
          $adminSc = sha1(filemtime($source_dir. '/corecms.php'). microtime(true). mt_rand(). rand_string(6));

          # Save the administrative session id to the session AND database.
          $_SESSION['adminSc'] = $adminSc;
          $db->query("
            UPDATE {$db->prefix}members
            SET adminSc = %adminSc
            WHERE member_id = %member_id
            LIMIT 1",
            array(
              'member_id' => array('int', $row['member_id']),
              'adminSc' => array('string-40', $adminSc),
            ));
        }

        # Now redirect to where they were last
        if(isset($_SERVER['HTTP_REFERER']))
        {
          redirect($_SERVER['HTTP_REFERER'], true, false);
        }
        else
        {
          # They didn't come from somewhere? o.O
          # The homepage then
          redirect();
        }
      }
      else
      {
        # :/ failed. But what?
        if(!$row['is_activated'])
          $page['errors'][] = $l['error_login_account_not_activated'];
        elseif($row['is_banned'])
          $page['errors'][] = $l['error_login_account_banned'];
        elseif($row['suspended'] > time_utc())
          $page['errors'][] = sprintf($l['error_login_account_suspended'], timeformat($row['suspended']));
        else
          $page['errors'][] = $l['error_login_wrong_username_or_password'];

        # Now show the error
        login_view();
      }
    }
    else
    {
      # The username doesn't even exist!
      $page['errors'][] = $l['error_login_wrong_username_or_password'];

      # Now the Login(); function to show the error
      login_view();
    }
  }
  elseif($is_flooding)
  {
    # Flooding == BAD
    $page['title'] = $l['login_flood_title'];
    $page['no_index'] = true;

    theme_load('login', 'login_process_show_flood');
  }
  else {
    # Empty :( Load the Login function and an error.
    $page['errors'][] = $l['error_login_empty_info'];

    login_view();
  }
}

function login_cookie($member_id, $hashed_password, $cookie_expires = 0)
{
  global $base_url, $source_dir, $settings;

  # Setup the cookie data
  $data = (int)$member_id. ':'. $hashed_password;
  
  # Now the cookie domain and path...
  $url = parse_url($base_url, PHP_URL_PATH);

  # Check if there is a path, if not just do null
  if(empty($url['path']))
    $url['path'] = '/';
  
  setcookie($settings['cookie_name'], $data, (int)$cookie_expires, $url['path']);
}

function login_reminder()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  language_load('login');

  # So lets see if they are trying to request a reminder already.
  # First off, if you are logged in, go away :P
  if(!empty($_REQUEST['email']) && !$user['is_logged'])
  {
    # Make sure we aren't flooding... This should be a bit longer
    # Since you don't need to resubmit constantly :P
    if(!security_flood('reminder'))
    {
      # Check...
      $result = $db->query("
        SELECT
          memmember_id, memloginName, mempasswrd, memdisplayName, mememail, memis_activated, memsuspended, memis_banned
        FROM {$db->prefix}members AS mem
        WHERE ". ($db->case_sensitive ? '(LOWER(memloginName) = LOWER(%email) OR LOWER(mememail) = LOWER(%email))' : '(memloginName = %email OR mememail = %email)'). " AND memis_activated = 1 AND memsuspended < UNIX_TIMESTAMP() AND memis_banned = 0
        LIMIT 1",
        array(
          'email' => array('string', $_REQUEST['email'])
        ));

      # Any users found..?
      if($db->num_rows($result))
      {
        # Log the action... it was a success so make them wait :P
        security_log('reminder', 15);

        $row = $db->fetch_assoc($result);
        # So it was a success... one exists lets generate a code to send to the email.
        # Here we go with randomness!
        if(mt_rand(1, 5) == 4)
          $code = sha1($row['member_id']. (mt_rand(0, 1) ? filemtime($source_dir. '/CoreCMS.php') : $row['passwrd']). time_utc(). mt_rand(50, 345));
        else
          $code = sha1(mt_rand(1, 10). filemtime($source_dir. '/Login.php'). $row['passwrd']. mt_rand(45, 60). time_utc());

        # Now lets shorten it up... randomly :D
        $code = mb_substr($code, mt_rand(0, 10), mt_rand(30, 40));
        $code = mb_substr($code, min(mt_rand(0, mb_strlen($code)), mb_strlen($code) - 10), 10);

        # Now parse the template...
        $email = strtr($l['reminder_email_tpl'], array('{$MEMBER_NAME}' => $row['displayName'], '{$MEMBER_ID}' => $row['member_id'], '{$CODE}' => $code));

        # Update a couple things in that account
        # Like the code and that this account has
        # requested an activation code :)
        $db->query("
          UPDATE {$db->prefix}members
          SET reminder_requested = 1,
          acode = %code
          WHERE member_id = %member_id
          LIMIT 1",
          array(
            'code' => array('string', $code),
            'member_id' => array('int', $row['member_id'])
          ));

        # Get the Mail.php, which has the sendmb_send_mail() function in it
        require_once($source_dir. '/Mail.php');

        # Now we will send the email to them.
        sendmb_send_mail($row['email'], $l['reminder_email_subject'], $email);

        # It was a success :)
        $page['success'] = true;

        # Load the theme...
        $page['title'] = $l['login_reminder_title'];
        theme_load('login', 'login_reminder_show');
      }
      else
      {
        # There was an error.
        $page['errors'] = true;
        $page['no_index'] = true;

        # Log the action even though it failed... but not 
        # as long as if it was a success...
        security_log('reminder', 3);

        # Load the theme which will show it :)
        $page['title'] = $l['login_reminder_title'];
        theme_load('login', 'login_reminder_show');
      }
    }
    else
    {
      # Flooding is BAD, especially here
      $page['title'] = $l['login_flood_title'];
      $page['no_index'] = true;

      theme_load('login', 'login_reminder_show_flood');
    }
  }
  elseif(!$user['is_logged'])
  {
    # Show the form.
    $page['title'] = $l['login_reminder_title'];

    theme_load('login', 'login_reminder_show');
  }
  else
  {
    # Your logged in, so go home.
    redirect();
  }
}

function login_reminder2()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  language_load('login');

  # We might want to fill out the username if the ID is filled out, you know, from the link...
  if(!empty($_REQUEST['id']) && empty($_REQUEST['proc_reset']) && empty($_REQUEST['username']))
  {
    $request_id = (int)$_REQUEST['id'];
    if($request_id > 0)
    {
      # Hope it exists :P
      $result = $db->query("
        SELECT
          mem.member_id, mem.loginName
        FROM {$db->prefix}members AS mem
        WHERE mem.member_id = %request_id",
        array(
          'request_id' => array('int', $request_id)
        ));
      # Lets see...
      if($db->num_rows($result))
        @list(, $_REQUEST['username']) = $db->fetch_row($result);
    }
  }

  # Trying..?
  if((!empty($_REQUEST['id']) || !empty($_REQUEST['username'])) && !empty($_REQUEST['code']) && !empty($_REQUEST['proc_reset']))
  {
    # Before we set the new password we want to verify a couple things... :P
    $member_id = !empty($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
    $username = !empty($_REQUEST['username']) ? $_REQUEST['username'] : '';
    $code = $_REQUEST['code'];

    # We need to be sure that everything is correct and that a password
    # reminder was indeed requested for this account :)!
    $result = $db->query("
      SELECT
        member_id, loginName, email, reminder_requested, acode, is_activated, is_banned, suspended
      FROM {$db->prefix}members AS mem
      WHERE (member_id = %member_id OR ". ($db->case_sensitive ? 'LOWER(loginName) = LOWER(%username) OR LOWER(email) = LOWER(%username)' : 'loginName = %username OR email = %username'). ") AND reminder_requested = 1 AND acode = %code AND is_activated = 1 AND is_banned = 0 AND suspended < UNIX_TIMESTAMP()
      LIMIT 1",
      array(
        'member_id' => array('int', $member_id),
        'username' => array('string-80' => $username),
        'code' => array('string', $code)
      ));

    # Find anything..?
    if($db->num_rows($result))
    {
      # Okie dokie... seemed to work XD! Lets make sure the password match though...
      $newPassword = !empty($_REQUEST['newPasswrd']) ? $_REQUEST['newPasswrd'] : '';
      $vNewPassword = !empty($_REQUEST['vNewPasswrd']) ? $_REQUEST['vNewPasswrd'] : '';

      # Make sure the password is at least long enough :P
      if(mb_strlen($newPassword) > 3 && $newPassword == $vNewPassword)
      {
        $row = $db->fetch_assoc($result);
        # They both match... so yeah... :)
        $newPassword = sha1($newPassword);

        # So yeah... update their profile :)
        db_query("
          UPDATE {$db->prefix}members
          SET passwrd = %newPassword,
          reminder_requested = 0, acode = ''
          WHERE member_id = %member_id",
          array(
            'newPassword' => array('string', $newPassword),
            'member_id' => array('int', $row['member_id'])
          ));

        # Now that we are done... redirect them to the login :)
        redirect('index.php?action=login;username='. urlencode($row['loginName']), false, true);
      }
      else
      {
        # Password is to short or they don't match...
        # Error
        $page['errors'][] = $l['error_reminder2_passwords'];

        # Show the form =P
        $page['title'] = $l['login_reminder2_title'];
        $page['no_index'] = true;

        # The code :) and username... maybe
        $page['username'] = htmlspecialchars(!empty($_REQUEST['username']) ? $_REQUEST['username'] : '', ENT_QUOTES, 'UTF-8');
        $page['code'] = htmlspecialchars(!empty($_REQUEST['code']) ? $_REQUEST['code'] : '', ENT_QUOTES, 'UTF-8');

        theme_load('login', 'login_reminder2_show');
      }
    }
    else
    {
      # Nope... :P
      # Error
      $page['errors'][] = $l['error_reminder2_unknown'];

      # Show the form =P
      $page['title'] = $l['login_reminder2_title'];
      $page['no_index'] = true;

      # The code :) and username... maybe
      $page['username'] = htmlspecialchars(!empty($_REQUEST['username']) ? $_REQUEST['username'] : '', ENT_QUOTES, 'UTF-8');
      $page['code'] = htmlspecialchars(!empty($_REQUEST['code']) ? $_REQUEST['code'] : '', ENT_QUOTES, 'UTF-8');

      theme_load('login', 'login_reminder2_show');
    }
  }
  else
  {
    # Show the form =P
    $page['title'] = $l['login_reminder2_title'];
    $page['no_index'] = true;

    # The code :) and username... maybe
    $page['username'] = htmlspecialchars(!empty($_REQUEST['username']) ? $_REQUEST['username'] : '', ENT_QUOTES, 'UTF-8');
    $page['code'] = htmlspecialchars(!empty($_REQUEST['code']) ? $_REQUEST['code'] : '', ENT_QUOTES, 'UTF-8');

    theme_load('login', 'login_reminder2_show');
  }
}

function login_logout()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # Only do this if you are logged in...
  if($user['is_logged'])
  {
    # Hold on! Be sure you didn't click something by accident :P
    security_sc('request');

    # Destroy their session
    session_destroy();
    
    # Unset their login cookie ;)
    login_cookie(0, null, time_utc() - 604800);

    # Before we redirect delete their session in the online table
    $db->query("
      DELETE FROM {$db->prefix}online
      WHERE member_id = %member_id
      LIMIT 1",
      array(
        'member_id' => array('int', $user['id'])
      ));

    # And redirect.
    redirect();
  }
  else
    # Silly :P
    redirect();
}
?>