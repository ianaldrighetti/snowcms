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

// Halt the sending of data to the browser
ob_start();
// Start the session, so we can load data across pages
session_start();
// Report all errors, even notices
error_reporting(E_ALL);
// Defined so other files can detect if this file has been included
define("Snow", true);
// Load the config.php file, this has our MySQL connection info and other important stuff
require_once('./config.php'); 
// Load Core.php, this file contains heaps of function definitions that we will call on soon :)
require_once($source_dir.'/Core.php');
// Dumb Magic Quotes! DIE DIE DIE!
if(function_exists('set_magic_quotes_runtime'))
  @set_magic_quotes_runtime(0);
if(get_magic_quotes_gpc())
  WizardMagic();
// If SnowCMS is not installed take them to the installer so they can install it
if(!$scms_installed)
  header("Location: install.php");
// Connect to MySQL, or if it fails, make the error pretty :]
@mysql_connect($mysql_host, $mysql_user, $mysql_passwd) or die(MySQLError(mysql_error()));
// Formats the query correctly, i.e. allows the use of ; instead of &, clever eh?
cleanQuery();
// Check for IP bans
checkIP();
// Load the settings out of the database into the $settings[] array
loadSettings();
// Load user info, such as if they're logged in, what their member group is, etc.
loadUser();
// Check if the language is being changed and if so, process the change
changeLanguage();
// Load member permissions, y'know, what they are allowed to do
loadPerms();
// Load the links to be displayed on the menus
loadMenus();
// Load the language files appropriate for the selected language
loadLanguage();
// Process the fact that this visitor is online and what they are viewing
WriteOnline();
// Check if in Maintenance Mode is on and act accordingly
maintenanceMode();
// Unserialize the array containing field data if an error occurred
$values = @unserialize($_SESSION['error_values']);
// Add the home page to the links in the link tree
AddTree($l['main_linktree'],'.');
// Are they viewing the home page?
if(empty($_REQUEST['action']) && empty($_REQUEST['page'])) {
  // Neither ?action= nor ?page= are set so load Home()
  require_once($source_dir.'/Main.php');
    Home();
}
// Okay, so are they viewing a page?
elseif(isset($_REQUEST['page']) && empty($_REQUEST['action'])) {
  // Include the approprate source file and load the page
  require_once($source_dir.'/Page.php');
  Page();
}
// Okay, so an action is set
else {
  // To add an action, it is quite simple, add a row like this:
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
    'register3' => array('Register.php','Register3'),
    'captcha' => array('Captcha.php','Captcha')
  );
  // Is the TOS enabled? If so, add that action too
  if ($settings['enable_tos'])
    $actions['tos'] = array('TOS.php','TOS');
  // Is the action NOT in the $actions array?
  if(!is_array(@$actions[$_REQUEST['action']])) {
    // It's an invalid action, so let's load the home page
    require_once($source_dir.'/Main.php');
    Home();
  }
  // So it's a real action, well, let's do it
  else {
    // Require the appropriate source file
    require_once($source_dir.'/'.$actions[$_REQUEST['action']][0]);
    // Call the appropriate function
    $actions[$_REQUEST['action']][1]();
  }
}
// Remove any error messages so they aren't displayed again
unset($_SESSION['error']);
// Remove the field data for errors too
unset($_SESSION['error_values']);
// Remove the fact that they have completed the CAPTCHA
// Otherwise they just need a person to complete it once and the bot gets free roaming forever
unset($_SESSION['passed_captcha']);
// Salt used in hashing
$salt = 'salt4me';
// Unset information about the CAPTCHA
unset($_SESSION['captcha_'.sha1(sha1($salt))]);
// Flush it down the tube :P
ob_end_flush();
?>
