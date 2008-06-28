<?php
session_start();
error_reporting(E_ALL);
//                 SnowCMS
//           By aldo and soren121
//  Founded by soren121 & co-founded by aldo
//    http://snowcms.northsalemcrew.net
//
// SnowCMS is released under the GPL v3 License
// Which means you are free to edit it and then
//       redistribute it as your wish!
// 
//            index.php file 

define("Snow", true);

// Load Some Important Files
require_once('./config.php');
// SnowCMS is not installed, so take them to the installer so they can install SnowCMS
if(!$scms_installed)
  header("Location: install.php");
// Load Core.php, this file has many things we need, such as the functions we will call on soon :)
require_once($source_dir.'/Core.php');

  // Connect to MySQL
  mysql_connect($mysql_host, $mysql_user, $mysql_passwd) or die(mysql_error());

// Load up a few things :P Such as $settings, $l[anguage], and $user, the permissions too!
  $user = loadUser();
  $settings = loadSettings();
  $l = loadLanguage();
  $perms = loadPerms();
  $settings['menu'] = loadMenus();
// Write that this Guest/User is online
  WriteOnline();
  
  // What should we do? ._.
  if((empty($_REQUEST['action'])) && (empty($_REQUEST['page']))) {
    // Neither the ?action= is set, nor the ?page= is set, so load Home()
    require_once($source_dir.'/Main.php');
      Home();
  }
  elseif(!empty($_REQUEST['page'])) {
    // ?page= is not empty, load the page
    require_once($source_dir.'/Page.php');
      Page();
  }
  else {
    // ?action is not empty! load it
    // To add an action, it is Quite Simple, Simply add a row like this:
    // 'ACTION_NAME' => array('ACTION_SOURCE_FILE','FUNCTION_TO_CALL_ON_IN_FILE'),
    $actions = array(
      'admin' => array('Admin.php','Admin'),
      'login' => array('Login.php','Login'),
      'login2' => array('Login.php','Login2'),
      'logout' => array('Login.php','Logout'),
      'news' => array('News.php','News'),
      'online' => array('Online.php','Online'),
      'profile' => array('Profile.php','Profile'),
      'register' => array('Register.php','Register'),
      'register2' => array('Register.php','Register2'),
    );
    // Is this action they are requesting even in the $actions array? If not, we don't want an error message.
    // So, lets load the Home() Function
    if(!is_array(@$actions[$_REQUEST['action']])) {
      require_once($source_dir.'/Main.php');
        Home();  
    }
    else {
      require_once($source_dir.'/'.$actions[$_REQUEST['action']][0]);
        $actions[$_REQUEST['action']][1]();
    }
  }
?>