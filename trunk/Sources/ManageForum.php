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
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
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
  // Process stuff, like updating or adding categories! =o
  if(!empty($_REQUEST['update_cats'])) {  
    $rows = array();
    foreach($_POST['cat_name'] as $cat_id => $name) {
      $cat_id = (int)$cat_id;
      $name = clean($name);
      $corder = (int)$_POST['cat_order'][$cat_id];
      $rows[] = "('$cat_id','$corder','$name')";
    }
    $updated = implode(",", $rows);
    sql_query("REPLACE INTO {$db_prefix}categories (`cid`,`corder`,`cname`) VALUES{$updated}");
  }
  if(!empty($_REQUEST['delete']) && validateSession($_REQUEST['sc'])) {
    $cat_id = (int)$_REQUEST['delete'];
    sql_query("DELETE FROM {$db_prefix}categories WHERE `cid` = '$cat_id'");
  }
  if(!empty($_REQUEST['add_cat'])) {
    $cat_name = clean($_REQUEST['cat_name']);
    $corder = (int)$_REQUEST['order'];
    sql_query("INSERT INTO {$db_prefix}categories (`corder`,`cname`) VALUES('$corder','$cat_name')");
  }
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
      'name' => $row['cname']
    );
  }
  $settings['cats'] = $cats;
  $settings['page']['title'] = $l['managecats_title'];
  loadTheme('ManageForum','ShowCats');
}

function ManageBoards() {
global $cmsurl, $db_prefix, $l, $settings, $user;

  // Load up all the boards and such...
  $result = sql_query("
    SELECT
      c.cid, c.corder, c.cname
    FROM {$db_prefix}categories AS c
    ORDER BY c.corder ASC");
  $settings['cats'] = array();
  if(mysql_num_rows($result)) {
    while($row = mysql_fetch_assoc($result)) {
      $settings['cats'][$row['cid']] = array(
        'id' => $row['cid'],
        'order' => $row['corder'],
        'name' => $row['cname'],
        'boards' => array()
      );
    }
    $result = sql_query("
      SELECT
        b.bid, b.cid, b.border, b.name
      FROM {$db_prefix}boards AS b
      ORDER BY b.border ASC");
    while($row = mysql_fetch_assoc($result)) {
      $settings['cats'][$row['cid']]['boards'][] = array(
        'id' => $row['bid'],
        'cid' => $row['cid'],
        'order' => $row['border'],
        'name' => $row['name']
      );
    }
  }
  $settings['page']['title'] = $l['manageboards_title'];
  loadTheme('ManageForum','ShowBoards');
}
?>