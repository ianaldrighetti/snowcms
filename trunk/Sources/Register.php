<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//          redistribute it as your wish!
//
//                 Register.php file


if(!defined("Snow"))
  die("Hacking Attempt...");

function Register() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  
  // Check if they are not logged in
  if ($user['is_guest']) {
    // If they have already been registered then inform them
    if (@$_REQUEST['u']) {
      $settings['page']['title'] = $l['register_title'];
      $u = clean($_REQUEST['u']);
      $row = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '$u'"));
      $settings['page']['username'] = $row['username'];
      
      loadTheme('Register', 'Success');
    }
    // If they have already been registered (With required activation by email) then inform them
    else if (@$_GET['email'] && @$_REQUEST['sa'] == 'success') {
      $settings['page']['title'] = $l['register_title'];
      $settings['register']['email'] = $_REQUEST['email'];
      loadTheme('Register', 'SuccessBut1');
    }
    // If they have already been registered (With required activation by admin) then inform them
    else if (@$_REQUEST['sa'] == 'success') {
      $settings['page']['title'] = $l['register_title'];
      loadTheme('Register', 'SuccessBut2');
    }
    // If it there was an error with sending the activation email then inform them
    else if (@$_GET['email'] && @$_REQUEST['sa'] == 'error') {
      $settings['page']['title'] = $l['register_title'];
      loadTheme('Register','Failure');
    }
    // Error in formation given
    else if (@$_SESSION['register-error']) {
      $settings['page']['title'] = $l['register_title'];
      $settings['page']['error'] = explode('\n',$_SESSION['register-error']);
      unset($_SESSION['register-error']);
      loadTheme('Register');
    }
    // Unknown error
    else if (@$_REQUEST['sa'] == 'error') {
      $settings['page']['title'] = $l['register_title'];
      $settings['page']['error'][] = $l['register_error_unknown'];
      loadTheme('Register');
    }
    // Process information sent
    else if (@$_REQUEST['register']) {
      // Load the CAPTCHA Image Source
      require_once($source_dir.'/Captcha.php');
      // Get their username, password, verification pass, email, and the captcha
      $username = $_REQUEST['username'];
      $password = $_REQUEST['password'];
      $vpassword = $_REQUEST['vpassword'];
      $email = $_REQUEST['email'];
      $captcha = $_REQUEST['captcha'];
      // Set error as array, so if no errors, we won't get a PHP error
      $settings['page']['error'] = array();
      // Is the username taken?
      $result = sql_query("SELECT * FROM {$db_prefix}members WHERE `username` = '".clean($username)."'");
      if(mysql_num_rows($result)>0) {
        $settings['page']['error'][] = $l['register_error_user_taken'];
      }
      // Is the username to short?
      if(strlen($username)<3) {
        $settings['page']['error'][] = $l['register_error_username_to_short'];
      }
      //Do the passwords match?
      if($password!=$vpassword) {
        $settings['page']['error'][] = $l['register_error_passwords'];
      }
      // If the passwords match, is it long enough?
      if(($password==$vpassword) && (strlen($password)<5)) {
        $settings['page']['error'][] = $l['register_error_password_to_short'];
      }
      // Email valid?
      if(!preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i", $email)) {
        $settings['page']['error'][] = $l['register_error_invalid_email'];
      }
      // Is the CAPTCHA valid?
      if(PhpCaptcha::Validate($captcha)) { } else {
        $settings['page']['error'][] = $l['register_error_captcha'];
      }
      // Did they except the TOS?
      if (!@$_REQUEST['tos'] && $settings['enable_tos'])
        $settings['page']['error'][] = $l['register_error_tos'];
      // Did we get no errors? If no, register them
      if(count($settings['page']['error'])==0) {
        // Clean username, encrypt pass, clean email, set time registered
        $username = clean($username);
        $password = md5($password);
        $email = clean($email);
        $time = time();
        $activated = 1;
        $acode = base64_encode(md5(time().$email.$password.rand(1,500)));
        // What is account Activation?
        if($settings['account_activation']>0) {
          $activated = 0;
        }
        $settings['page']['error'] = null;
        // Insert it
        $result = sql_query("INSERT INTO {$db_prefix}members (`username`,`display_name`,`password`,`email`,`reg_date`,`reg_ip`,`group`,`activated`,`acode`) VALUES('{$username}','{$username}','{$password}','{$email}','{$time}','{$user['ip']}','{$settings['default_group']}','{$activated}','{$acode}')");
        if($result) {
          // It was a Success! Weeee!
          if($settings['account_activation'] == 0) {
            $row = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}members WHERE `username` = '$username'"));
            redirect('index.php?action=register;sa=success;u='.$row['id']);
          }
          // It was a success, but they need to activate their account via email
          elseif($settings['account_activation'] == 1) {
            require_once($source_dir.'/Mail.php');
            $msg = $l['email_register_tpl'];
            $msg = str_replace("%username%", $username, $msg);
            $msg = str_replace("%alink%", $cmsurl.'index.php?action=activate&acode='.$acode.'&u='.$username, $msg);
            $info = SendMail($_REQUEST['email'], $l['email_register_subject'], $msg);
            // Was sending the email successful?
            if(!@$info['error'])
              redirect('index.php?action=register;sa=success;email='.clean_header($_REQUEST['email']));
            else
              redirect('index.php?action=register;sa=error;email='.clean_header($_REQUEST['email']));
          }
          // It was a success, but their account needs to be activated by an administrator
          elseif($settings['account_activation'] == 2)
            redirect('index.php?action=register;sa=success');
        }
        else
          // It failed...!
          redirect('index.php?action=register;sa=error');
      }
      else {
        // Their were errors! Load the Register template so it can show them
        $_SESSION['register-error'] = implode('\n',$settings['page']['error']);
        redirect('index.php?action=register;sa=error');
      }
    }
    // Show the registration form
    else {
      // Set the error as an array, so we dont get errors, set, the title, load the Register template
      $settings['page']['title'] = $l['register_title'];
      $settings['page']['error'] = array();
      loadTheme('Register');
    }
  }
  // They are logged in, so inform them
  else {
    $settings['page']['title'] = $l['register_loggedin_title'];
    loadTheme('Register','LoggedIn');
  }
}

// This is how they activate their account, if you can activate it by email...
function Activate() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  $acode = clean(@$_REQUEST['acode']);
  $u = clean(@$_REQUEST['u']);
  $settings['acode'] = '';
  $settings['user'] = '';
  if(empty($acode) || empty($u)) {
    // One or both is empty, lets give them a form...
    $settings['page']['title'] = $l['activate_title'];
    $settings['acode'] = !empty($acode) ? $acode : '';
    $settings['user'] = !empty($u) ? $u : '';
    loadTheme('Register','AForm');
  }
  else {
    // Both are there :D
    $result = sql_query("
      SELECT
        mem.username, mem.acode, mem.activated
      FROM {$db_prefix}members AS mem
      WHERE
        mem.username = '{$u}'");
    if(mysql_num_rows($result)) {
      // The user does exist, but thats only part 1
      $row = mysql_fetch_assoc($result);
      // Does the activation code match? *AND* is the account not yet activate? ._.
      if($acode==$row['acode'] && !$row['activated']) {
        // Success! =D!
        sql_query("UPDATE {$db_prefix}members SET `activated` = '1' WHERE `username` = '{$u}'");
        $settings['page']['title'] = $l['activate_title'];
        $settings['user'] = $row['username'];
        loadTheme('Register','ASuccess');
      }
      elseif($acode!=$row['acode'] && !$row['activated']) {
        $settings['page']['title'] = $l['activate_title'];
        $settings['errors'][] = $l['activate_acode_no_match'];
        loadTheme('Register','AForm');      
      }
      else {
        // That account is already activated! D:
        $settings['page']['title'] = $l['activate_title'];
        $settings['errors'][] = $l['activate_account_already_activated'];
        loadTheme('Register','AForm');        
      }
    }
    else {
      // The user doesn't exist
      $settings['page']['title'] = $l['activate_title'];
      $settings['errors'][] = $l['activate_no_such_user'];
      loadTheme('Register','AForm');
    }
  }
}

function Register3() {
  // Resend activation email
  require_once($source_dir.'/Mail.php');
  $msg = $l['email_register_tpl'];
  $msg = str_replace("%username%", $username, $msg);
  $msg = str_replace("%alink%", $cmsurl.'index.php?action=activate&acode='.$acode.'&u='.$username, $msg);
  $info = SendMail($_REQUEST['email'], $l['email_register_subject'], $msg);
  if(@$info['error']) {
    $settings['page']['error'] = $info['msg'];
    $settings['page']['title'] = $l['register_title'];
    $settings['register']['email'] = $_REQUEST['email'];
    loadTheme('Register', 'SuccessBut1');
  }
}
?>