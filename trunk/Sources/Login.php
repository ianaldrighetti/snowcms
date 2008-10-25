<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                   Login.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

// Prepares to show the login page  
function Login() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // Check if they are not logged in
  if ($user['is_guest']) {
    // They are not logged in, so let them
    $settings['page']['title'] = $l['login_title'];
    loadTheme('Login');
  }
  else {
    // They are logged in, so inform them, silly pants :P
    $settings['page']['title'] = $l['login_loggedin_title'];
    loadTheme('Login','LoggedIn');
  }
}

// This processes the login form
function Login2() {
global $cmsurl, $db_prefix, $l, $settings, $user, $cookie_prefix;
  
  // Get and sanitize the username and encrypt the password
  $username = !empty($_REQUEST['username']) ? clean($_REQUEST['username']) : '';
  // Is this password pre hashed? If not, hash it...
  $password = (!empty($_REQUEST['pass_hash']) && $_REQUEST['pass_hash'] != 1) ? clean($_REQUEST['pass_hash']) : @md5($_REQUEST['password']);
  if(!empty($username) && !empty($password)) {
    $result = sql_query("SELECT * FROM {$db_prefix}members WHERE `username` = '{$username}' AND `password` = '{$password}'");
    if(mysql_num_rows($result)) {
      while($row = mysql_fetch_assoc($result)) {
        // We need their user ID
        $id = $row['id'];
        // And if they are activated :P
        $is_activated = $row['activated'];
        // Banned?
        $is_banned = $row['banned'];
        // Suspended...?
        $is_suspended = $row['suspension'];
      }
      // Just cause their password and username is right, doesn't mean they can login :P
      if($is_activated && $is_banned==0 && $is_suspended < time()) {
        // Set cookies :) Mmmmm, the good kind too, like Chocolate Chip, but not Oatmeal! Ewww!
        $login_length = (int)$_REQUEST['login_length'];
        if($login_length == 0)
          $login_length = $settings['remember_time']*60;
        // Set the login cookie...
        setLoginCookie($id, $password, time() + $login_length);
        
        // Set the Session variables, like ID and Pass, enables them to be validated
        // Its more secure to authenticate them on each page load, or at least we think so :P
        $_SESSION['id'] = $id;
        $_SESSION['password'] = $password;
        // Update a few things, lkike last login, last ip, their session ID
        sql_query("UPDATE {$db_prefix}members SET `last_login` = '".time()."', `last_ip` = '{$user['ip']}' WHERE `id` = '{$_SESSION['id']}'");
        // Redirect them to the CMSURL URL :P
        redirect('index.php');
      }
      elseif(!$is_activated) {
        // Not activated... Give them a message and a link to a resend form
        $settings['page']['title'] = $l['login_title'];
        loadTheme('Login','NotActivated');
      }
      elseif($is_banned) {
        // You cant login! Your banned! Bad boy :P
        $settings['page']['title'] = $l['login_title'];
        loadTheme('Login','Banned');
      }
      elseif($is_suspended > time()) {
        // Sorry, your still suspended :)
        $settings['page']['title'] = $l['login_title'];
        $settings['time'] = formattime($is_suspended);
        loadTheme('Login','Suspended');
      }
    }
    else {
      // That username doesn't exist, or it is a wrong password! but we won't say which, hehe. Error!
      $settings['page']['error'] = $l['login_error'];
      $settings['page']['title'] = $l['login_title'];
      loadTheme('Login');
    }    
  }
  else {
    // No Username and password, Error!
    $settings['page']['error'] = $l['login_error'];
    $settings['page']['title'] = $l['login_title'];
    loadTheme('Login');
  }
}

// Logout, need I explain? :P
function Logout() {
global $cmsurl, $db_prefix, $l, $settings, $user, $cookie_prefix;
  
  // Are they even logged in? Lol.
  if($user['is_logged']) {
    // Destroy! Destroy! Their session :D
    session_destroy();
    // Delete them from the {db_prefix}online table
    sql_query("DELETE FROM {$db_prefix}online WHERE `user_id` = '{$user['id']}'");
    // Delete the Cookies... Simple enough :P
    setLoginCookie('', '', time() - (60*60*24*365));
    // Redirect to the CMSURL URL
    redirect("index.php");
  }
  elseif(!$user['is_logged']) {
    // Your not logged in -.-'
    redirect("index.php");
  }
  else {
    // Odd, an error occurred... O.O
    $settings['page']['title'] = $l['logout_error_title'];
    loadTheme('Login','LogoutError');
  }
}
?>