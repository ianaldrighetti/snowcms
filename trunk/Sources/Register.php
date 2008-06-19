<?php
//                 SnowCMS
//           By aldo and soren121
//  Founded by soren121 & co-founded by aldo
//    http://snowcms.northsalemcrew.net
//
// SnowCMS is released under the GPL v3 License
// Which means you are free to edit it and then
//       redistribute it as your wish!
// 
//            Register.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

function Register() {
global $cmsurl, $l, $settings, $user;
  // Set the error as an array, so we dont get errors, set, the title, load the Register template
  $settings['page']['title'] = $l['register_title'];
  $settings['page']['error'] = array();
  loadTheme('Register');
}

function Register2() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
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
  $result = mysql_query("SELECT * FROM {$db_prefix}members WHERE `username` = '".clean(strtolower($username))."'");
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
  // Did we get no errors? If no, register them
  if(count($settings['page']['error'])==0) {
    // Clean username, encrypt pass, clean email, set time registered
    $username = clean(strtolower($username));
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
    $result = mysql_query("INSERT INTO {$db_prefix}members (`username`,`password`,`email`,`reg_date`,`reg_ip`,`group`,`activated`,`acode`) VALUES('{$username}','{$password}','{$email}','{$time}','{$user['ip']}','2','{$activated}','{$acode}')");
    if($result) {
      if($settings['account_activation']==0) {    
        // It was a Success! Weeee!
        $settings['page']['title'] = $l['register_title'];
        loadTheme('Register', 'Success');
      }
      elseif($settings['account_activation']==1) {
        // It was a success, but they need to activate their account via email... 
        // Was Sending the email successful?
        require_once($source_dir.'/Mail.php');
        $msg = $l['email_register_tpl'];
        $msg = str_replace("%username%", $username, $msg);
        $msg = str_replace("%alink%", $cmsurl.'index.php?action=activate&acode='.$acode.'&u='.$username, $msg);
        $info = SendMail($_REQUEST['email'], $l['email_register_subject'], $msg);
        if($info['error'])
          $settings['page']['error'] = $info['msg'];
        $settings['page']['title'] = $l['register_title'];
        loadTheme('Register', 'SuccessBut1');
      }
      elseif($settings['account_activation']==2) {  
        $settings['page']['title'] = $l['register_title'];
        loadTheme('Register', 'SuccessBut2');
      }
    }
    else {
      // It failed...!
      $settings['page']['title'] = $l['register_title'];
      $settings['page']['error'][] = $l['register_error_unknown'];
      loadTheme('Register');     
    }
  }
  else {
    // Their were errors! Load the Register template so it can show them
    $settings['page']['title'] = $l['register_title'];
    loadTheme('Register');  
  }
}
?>
