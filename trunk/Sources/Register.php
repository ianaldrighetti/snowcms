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
  
  $settings['page']['title'] = $l['register_title'];
  $settings['page']['error'] = array();
  loadTheme('Register');
}

function Register2() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  require('Captcha.php');
  $username = $_REQUEST['username'];
  $password = $_REQUEST['password'];
  $vpassword = $_REQUEST['vpassword'];
  $email = $_REQUEST['email'];
  $captcha = $_REQUEST['captcha'];
  $settings['page']['error'] = array();
  $result = mysql_query("SELECT * FROM {$db_prefix}members WHERE `username` = '".clean(strtolower($username))."'");
  if(mysql_num_rows($result)>0) {
    $settings['page']['error'][] = $l['register_error_user_taken'];
  }
  if(strlen($username)<3) {
    $settings['page']['error'][] = $l['register_error_username_to_short'];
  }
  if($password!=$vpassword) {
    $settings['page']['error'][] = $l['register_error_passwords'];
  }
  if(($password==$vpassword) && (strlen($password)<5)) {
    $settings['page']['error'][] = $l['register_error_password_to_short'];
  }
  if(!preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i", $email)) {
    $settings['page']['error'][] = $l['register_error_invalid_email'];
  }
  if(PhpCaptcha::Validate($captcha)) { } else {
    $settings['page']['error'][] = $l['register_error_captcha'];
  }
  if(count($settings['page']['error'])==0) {
    $username = clean(strtolower($username));
    $password = md5($password);
    $email = clean($email);
    $time = time();
    $result = mysql_query("INSERT INTO {$db_prefix}members (`username`,`password`,`email`,`reg_date`,`reg_ip`,`group`) VALUES('{$username}','{$password}','{$email}','{$time}','{$user['ip']}','2')");
    if($result) {
      $settings['page']['title'] = $l['register_title'];
      loadTheme('Register', 'Success');
    }
    else {
      $settings['page']['title'] = $l['register_title'];
      $settings['page']['error'][] = $l['register_error_unknown'];
      loadTheme('Register');     
    }
  }
  else {
    $settings['page']['title'] = $l['register_title'];
    loadTheme('Register');  
  }
}
?>
