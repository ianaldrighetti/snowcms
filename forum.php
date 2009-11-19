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
//                  forum.php file

ob_start();
session_start();
error_reporting(E_ALL);

// This is to stop access to files in other directories, Security I suppose
define('Snow', true);
define('InForum', true);
// We don't like you magic quotes...
if(function_exists('set_magic_quotes_runtime'))
  @set_magic_quotes_runtime(0);
  
// Load Some Important Files
require_once('./config.php');
// SnowCMS is not installed, so take them to the installer so they can install SnowCMS
if(!$scms_installed)
  header('Location: install.php');
// Load Core.php, this file has many things we need, such as the functions we will call on soon :)
require_once($source_dir.'/Core.php');

// Dumb Magic Quotes! DIE DIE DIE!
if(get_magic_quotes_gpc())
  WizardMagic();
  
  // Connect to MySQL, if an Error Occurs, lets not make it look ugly =|
  @mysql_connect($mysql_host, $mysql_user, $mysql_passwd) or die(MySQLError(mysql_error()));
  
  cleanQuery();
// Load up a few things :P Such as $settings, $l[anguage], and $user, the permissions too!
  loadUser();
  loadSettings();
  changeLanguage();
  loadPerms();
  loadBPerms();
  loadMenus();
  loadTree();
  loadLanguage();
  loadQuickEdit();
// Write that this Guest/User is online
  WriteOnline();
// Maintenance Mode?
  maintenanceMode();

$values = @unserialize($_SESSION['error_values']);

// What do we do? :O
  if((empty($_REQUEST['action'])) && (empty($_REQUEST['board'])) && (empty($_REQUEST['topic']))) {
    // Okay, show the BoardIndex :)
    require_once($source_dir.'/BoardIndex.php');
    BoardIndex();
  }
  elseif((!empty($_REQUEST['topic'])) && (empty($_REQUEST['action']))) {
    // Someone Wants to view a topic, I guess if we have to, we should show it ._.
    require_once($source_dir.'/Topic.php');
    loadTopic();
  }
  elseif((!empty($_REQUEST['board'])) && (empty($_REQUEST['action']))) {
    // Now they want to see a board, do I have to do EVERYTHING? ;D
    require_once($source_dir.'/Board.php');
    loadBoard(); 
  }
  else {
    // they want an action O.o Want some fries with that shake? lol
    $actions = array(
      'lock' => array('Topic.php','Lock'),
      'members' => array('Members.php','loadMlist'),
      'pm' => array('PersonalMessages.php','PM'),
      'post' => array('Post.php','Post'),
      'post2' => array('Post.php','Post2'),
      'search' => array('Search.php','FSearch'),
      'search2' => array('Search.php','FSearch2'),
      'delete' => array('Topic.php','Delete'),
      'sticky' => array('Topic.php','Sticky'),
      'move' => array('Topic.php','Move')
    );
    if(is_array(@$actions[$_REQUEST['action']])) {
      // It is something defined, hope it works right >.<
      require_once($source_dir.'/'.$actions[$_REQUEST['action']][0]);
        $actions[$_REQUEST['action']][1]();
    }
    else {
      // Oh noes! That action isn't an action! BoardIndex ^_^
      require_once($source_dir.'/BoardIndex.php');
      BoardIndex();
    }
  }
  // Remove any error messages so they aren't displayed again
  unset($_SESSION['error']);
  unset($_SESSION['error_values']);
  // Remove the fact that they have completed the CAPTCHA
  // Otherwise they just need a person to complete it once and the bot gets free roaming forever
  unset($_SESSION['passed_captcha']);
  // Salt used in hashing
  $salt = 'salt4me';
  // Unset information about the CAPTCHA
  unset($_SESSION['captcha_'.sha1(sha1($salt))]);
// Buh bye! xD
ob_end_flush();
?>