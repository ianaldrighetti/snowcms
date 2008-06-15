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
//              Login.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

// This function prepares to show the login page  
function Login() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  $settings['page']['title'] = $l['login_title'];
  
  loadTheme('Login');
}

// This processes the login form
function Login2() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  $username = @clean(strtolower($_REQUEST['username']));
  $password = @md5($_REQUEST['password']);
  if((!empty($username)) && (!empty($password))) {
    $result = mysql_query("SELECT * FROM {$db_prefix}members WHERE `username` = '{$username}' AND `password` = '{$password}'");
    if(mysql_num_rows($result)>0) {
      while($row = mysql_fetch_assoc($result)) {
        $id = $row['id'];
      }
      $_SESSION['id'] = $id;
      $_SESSION['pass'] = $password;
      header("Location: {$cmsurl}");
    }
    else {
      $settings['page']['error'] = $l['login_error'];
      $settings['page']['title'] = $l['login_title'];
      loadTheme('Login');
    }    
  }
  else {
    $settings['page']['error'] = $l['login_error'];
    $settings['page']['title'] = $l['login_title'];
    loadTheme('Login');
  }
}

// Logout, need I explain? :P
function Logout() {
global $cmsurl, $db_prefix, $settings, $user;
  if($user['is_logged']) {
    session_destroy();
    mysql_query("DELETE FROM {$db_prefix}online WHERE `user_id` = '{$user['id']}'");
    header("Location: {$cmsurl}");
  }
  else {
    // Your not logged in -.-'
    header("Location: {$cmsurl}");
  }
}