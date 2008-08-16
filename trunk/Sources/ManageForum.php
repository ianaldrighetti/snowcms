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
    // Load up a list of pre-existing boards, so we can let them say, I want it before {Cat} or After {Cat}
    $result = sql_query("
      SELECT
        c.cid, c.corder, c.cname
      FROM {$db_prefix}categories AS c
      ORDER BY c.corder ASC");
    $cats = array();
    while($row = mysql_fetch_assoc($result)) {
      $cats[] = array(
        'id' => $row['cid'],
        'order' => $row['corder'],
        'name' => $row['cname']
      );
    }
    mysql_free_result($result);
    $settings['cats'] = $cats;
    unset($cats);
    // Hmmm, I feel like I need more stuff to code, but what?
    $settings['page']['title'] = $l['managecats_add_title'];
    loadTheme('ManageForum','AddCat');
  }
  elseif($_REQUEST['do']=="edit") {
    // Editing an already existing category
    $cat_id = (int)$_REQUEST['id'];
    $result = sql_query("
      SELECT
        c.cid, c.corder, c.cname
      FROM {$db_prefix}categories AS c
      WHERE c.cid = $cat_id");
    if(mysql_num_rows($result)) {
      // Dang, it exists... P:
      $row = mysql_fetch_assoc($result);
      $settings['cat']['id'] = $row['cid'];
      $settings['cat']['order'] = $row['corder'];
      $settings['cat']['name'] = $row['cname'];

      // We need to load a list of categories for this too...
      $result = sql_query("
        SELECT
          c.cid, c.corder, c.cname
        FROM {$db_prefix}categories AS c
        WHERE c.cid != $cat_id
        ORDER BY c.corder ASC");
      $cats = array();
      while($row = mysql_fetch_assoc($result)) {
        $cats[] = array(
          'id' => $row['cid'],
          'order' => $row['corder'],
          'name' => $row['cname']
        );
      }
      $settings['cats'] = $cats;
      unset($cats);
      $settings['page']['title'] = $l['managecats_edit_title'];
      loadTheme('ManageForum','EditCat');
    }
    else {
      // That Category doesn't exist! :O!
      $settings['page']['title'] = $l['managecats_edit_title'];
      loadTheme('ManageForum','NoCat');
    }
  }
  else {
    // Show a list of categories...
    $result = sql_query("
      SELECT
        c.cid, c.corder, c.cname
      FROM {$db_prefix}categories AS c
      ORDER BY c.corder ASC");
    $cats = array();
    while($row = mysql_fetch_assoc($result)) {
      $cats[] = array(
        'id' => $row['cid'],
        'order' => $row['corder'],
        'name' => $row['name']
      );
    }
    $settings['page']['title'] = $l['managecats_title'];
    loadTheme('ManageForum','ManageCats');
  }
}
?>