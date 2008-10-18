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
//                  index.php file

ob_start();
session_start();
error_reporting(E_ALL);

// Stops people from trying to access files in other directories
define("Snow", true);
if(function_exists('set_magic_quotes_runtime'))
  @set_magic_quotes_runtime(0);
// Load the config.php file, this has our MySQL connection info, and other stuff needed
require_once('./config.php'); 
// SnowCMS is not installed, so take them to the installer so they can install SnowCMS
if(!$scms_installed)
  header("Location: install.php");
// Load Core.php, this file has many things we need, such as the functions we will call on soon :)
require_once($source_dir.'/Core.php');

// Dumb Magic Quotes! DIE DIE DIE!
if(get_magic_quotes_gpc())
  WizardMagic();

  // Connect to MySQL, or if it fails, make the error pretty :]
  @mysql_connect($mysql_host, $mysql_user, $mysql_passwd) or die(MySQLError(mysql_error()));
  
  cleanQuery();
// Load up a few things :P Such as $settings, $l[anguage], and $user, the permissions too!
  checkIP();
  loadSettings();
  loadUser();
  changeLanguage();
  loadPerms();
  loadMenus();
  loadLanguage();
// Write that this Guest/User is online
  WriteOnline();
  
  $values = @unserialize($_SESSION['error_values']);
  
  // What should we do? ._.
  if(empty($_REQUEST['action']) && empty($_REQUEST['page'])) {
    // Neither the ?action= is set, nor the ?page= is set, so load Home()
    require_once($source_dir.'/Main.php');
      Home();
  }
  elseif(!empty($_REQUEST['page']) && empty($_REQUEST['action'])) {
    // ?page= is not empty, load the page
    require_once($source_dir.'/Page.php');
      Page();
  }
  else {
    // ?action is not empty! load it
    // To add an action, it is Quite Simple, Simply add a row like this:
    // 'ACTION_NAME' => array('ACTION_SOURCE_FILE','FUNCTION_TO_CALL_ON_IN_FILE'),
    $actions = array(
      'activate' => array('Register.php','Activate'),
      'admin' => array('Admin.php','Admin'),
      'login' => array('Login.php','Login'),
      'login2' => array('Login.php','Login2'),
      'logout' => array('Login.php','Logout'),
      'news' => array('News.php','News'),
      'online' => array('Online.php','Online'),
      'profile' => array('Profile.php','Profile'),
      'register' => array('Register.php','Register'),
      'register3' => array('Register.php','Register3')
    );
    if ($settings['enable_tos'])
      $actions['tos'] = array('TOS.php','TOS');
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
  // Remove any error messages so they aren't displayed again
  unset($_SESSION['error']);
  unset($_SESSION['error_values']);
  // Remove the fact that they have completed the CAPTCHA
  // Otherwise they just need a person to complete it once and the bot gets free roaming forever
  unset($_SESSION['passed_captcha']);
// Flush it down the tube :P
ob_end_flush();
?>