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
//           ManageForum.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Displays the page
function ManageForum() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // :O We need another sub action kind of thing don't we? .-.
  // Lets call it Forum Action, or fa for short :-]
  if(can('manage_forum')) {  
    $fa = array(
      'boards' => array('ManageForum.php','ManageBoards'),
      'categories' => array('ManageForum.php','ManageCats'),
    );
    if(is_array(@$fa[$_REQUEST['fa']])) {
      require_once($source_dir.'/'.$fa[$_REQUEST['fa']][0]);
        $fa[$_REQUEST['fa']][1]();
    }
    else {
      // Its not :( We don't want an error, so show the ForumHome();
      ForumHome();
    }
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }  
}
function ForumHome() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  /*
    We really don't have a whole lot to do here :-/
    All we do here is loadTheme D:
  */
  $settings['page']['title'] = $l['manageforum_title'];
  loadTheme('ManageForum');
}

function ManageBoards() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // They want to manage the boards =D
  if($_REQUEST['do']=="add") {
    // Show a form to add a new board =D
  }
  elseif($_REQUEST['do']=="edit") {
    // Show a form to edit a board
  }
  else {
    // Show a list of Boards...
  }
}

// Awwww, Kitty ^^
function ManageCats() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // Manage the Categories! :O
  if($_REQUEST['do']=="add") {
    // Adding a category
  }
  elseif($_REQUEST['do']=="edit") {
    // Editing an already existing category
  }
  else {
    // Show a list of categories...
  }
}
?>