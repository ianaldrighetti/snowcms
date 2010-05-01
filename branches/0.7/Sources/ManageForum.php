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
//                ManageForum.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));
 
// Displays the page
function ManageForum() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  
  // Add link to the link tree
  AddTree('Forum','index.php?action=admin;sa=forum');
  
  // Are they allowed to manage the forum?
  if (can('manage_forum')) {
    // We need another sub action kind of thing don't we? .-.
    // Lets call it forum action, or fa for short :-]
    $fa = array(
      'boards' => array('ManageForum.php','ManageBoards'),
      'categories' => array('ManageForum.php','ManageCats'),
      'permissions' => array('Permissions.php','ForumPerms')
    );
    // We can only do it if its in the array...
    if(is_array(@$fa[$_REQUEST['fa']])) {
      require_once($source_dir.'/'.$fa[$_REQUEST['fa']][0]);
        $fa[$_REQUEST['fa']][1]();
    }
    else {
      // It's not :( we don't want an error, so show the ForumHome();
      ForumHome();
    }
  }
  // They don't have permission, so redrect them to the main control panel
  else
    redirect('index.php?action=admin');
}

function ForumHome() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // Get the forum management control panel menu options
  $options = array();
  if (can('manage_forum_edit') || can('manage_forum_create') || can('manage_forum_delete'))
    $options[] = 'categories';
  if (can('manage_forum_edit') || can('manage_forum_create') || can('manage_forum_delete'))
    $options[] = 'boards';
  if (can('manage_forum_perms'))
    $options[] = 'permissions';
  // We need to use this
  $settings['page']['options'] = $options;
  
  // Load the theme
  $settings['page']['title'] = $l['manageforum_title'];
  loadTheme('ManageForum');
}

// Awwww, kitty ^^
function ManageCats() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // Add link to the link tree
  AddTree('Categories','index.php?action=admin;sa=forum;fa=categories');
  
  // Manage the categories! :O
  // Are they updating categories?
  if (!empty($_REQUEST['update_cats'])) {
    // Are they allowed to update categories?
    if (can('manage_forum_edit')) {
      $rows = array();
      // Loop through each category, so we can save it
      foreach ($_POST['cat_name'] as $cat_id => $name) {
        $cat_id = (int)$cat_id;
        $name = clean($name);
        $corder = (int)$_POST['cat_order'][$cat_id];
        $rows[] = "('$cat_id','$corder','$name')";
      }
      $updated = implode(',', $rows);
      sql_query("REPLACE INTO {$db_prefix}categories (`cid`,`corder`,`cname`) VALUES $updated");
    }
    // They are not allowed to update categories
    else
      $_SESSION['error'] = $l['managecats_error_edit_notallowed'];
    redirect('index.php?action=admin;sa=forum;fa=categories');
  }
  // Are they trying to delete a category?
  if (!empty($_REQUEST['did'])) {
    // Are they allowed to?
    if (can('manage_forum_delete')) {
      // Is their session valid?
      if (validateSession($_REQUEST['sc'])) {
        $cat_id = (int)@$_REQUEST['did'];
        sql_query("DELETE FROM {$db_prefix}categories WHERE `cid` = '$cat_id'");
      }
      // Their session isn't valid
      else
        $_SESSION['error'] = $l['managecats_error_delete_invalidsession'];
    }
    // They are not allowed to delete categories
    else
      $_SESSION['error'] = $l['managecats_error_delete_notallowed'];
    redirect('index.php?action=admin;sa=forum;fa=categories');
  }
  // Are they trying to create a new category?
  if (!empty($_REQUEST['add_cat'])) {
    // Are they allowed to create new categories?
    if (can('manage_forum_create')) {
      $cat_name = clean($_REQUEST['cat_name']);
      $corder = (int)$_REQUEST['order'];
      sql_query("INSERT INTO {$db_prefix}categories (`corder`,`cname`) VALUES('$corder','$cat_name')");
    }
    // They are not allowed to create new categories
    else
      $_SESSION['error'] = $l['managecats_error_create_notallowed'];
    redirect('index.php?action=admin;sa=forum;fa=categories');
  }
  // Show a list of categories
  $result = sql_query("
    SELECT
      c.cid, c.corder, c.cname
    FROM {$db_prefix}categories AS c
    ORDER BY c.corder ASC");
  // Define an array
  $settings['cats'] = array();
  // Loop through each :)
  while ($row = mysql_fetch_assoc($result)) {
    $settings['cats'][] = array(
      'id' => $row['cid'],
      'order' => $row['corder'],
      'name' => $row['cname']
    );
  }
  $settings['page']['title'] = $l['managecats_title'];
  loadTheme('ManageForum','ShowCats');
}

function ManageBoards() {
global $cmsurl, $db_prefix, $forumperms, $l, $settings, $user;
  
  // Add link to the link tree
  AddTree('Boards','index.php?action=admin;sa=forum;fa=boards');
  
  // Are they allowed to manage boards?
  if (can('manage_forum_edit') || can('manage_forum_create') || can('manage_forum_delete')) {
    $do = @$_REQUEST['do'] ? $_REQUEST['do'] : null;
    // Are they creating a new board?
    if ($do == "add") {
      // Add link to the link tree
      AddTree('Add','index.php?action=admin;sa=forum;fa=boards;do=add');
      // Are they allowed to create new boards?
      if (can('manage_forum_create')) {
        // Adding a board, load up category list, member groups and such
        $result = sql_query("
          SELECT
            c.cid, c.cname, c.corder
          FROM {$db_prefix}categories AS c
          ORDER BY c.corder ASC");
        $settings['cats'] = array();
        // Any categories? They can't add boards if there are no categories! D:
        if(mysql_num_rows($result)) {
          while($row = mysql_fetch_assoc($result)) {
            $settings['cats'][] = array(
              'id' => $row['cid'],
              'name' => $row['cname']
            );
          }
          // Load the member groups ;) Except #1 :D cause Admins can do ANYTHING Respect my authoritay!
          $result = sql_query("
            SELECT
              m.group_id, m.groupname
            FROM {$db_prefix}membergroups AS m
            WHERE m.group_id != 1
            ORDER BY m.group_id ASC");
          $settings['groups'] = array();
          while ($row = mysql_fetch_assoc($result)) {
            $settings['groups'][] = array(
              'id' => $row['group_id'],
              'name' => $row['groupname']
            );
          }
        }
        $settings['page']['title'] = $l['manageboards_add_title'];
        loadTheme('ManageForum','AddBoard');
      }
      // They don't have permission, so redrect them to main board management
      else
        redirect('index.php?action=admin;sa=forum;fa=boards');
    }
    // Are they editing a board?
    elseif ($do == "edit") {
      // Are they allowed to edit boards?
      if (can('manage_forum_edit')) {
        // Clean the board ID
        $board_id = (int)$_REQUEST['id'];
        // Load up the board we need to edit and such.
        $result = sql_query("
          SELECT
            b.bid, b.cid, b.border, b.who_view, b.name, b.bdesc
          FROM {$db_prefix}boards AS b
          WHERE b.bid = $board_id");
        $settings['board'] = array();
        if(mysql_num_rows($result)) {
          // The board exists... Dang!
          $row = mysql_fetch_assoc($result);
          $settings['board']['name'] = $row['name'];
          $settings['board']['bid'] = $row['bid'];
          $settings['board']['cid'] = $row['cid'];
          $settings['board']['order'] = $row['border'];
          $settings['board']['who_view'] = @explode(",", $row['who_view']);
          $settings['board']['desc'] = $row['bdesc'];
          // Add link to the link tree
          AddTree($settings['board']['name'],'index.php?action=admin;sa=forum;fa=boards;do=edit;id='.$board_id);
          // Now load up the member groups (Except #1) and see which are checked :P
          $result = sql_query("
            SELECT
              m.group_id, m.groupname
            FROM {$db_prefix}membergroups AS m
            WHERE m.group_id != 1
            ORDER BY m.group_id ASC");
          $settings['groups'] = array();
          if(in_array(-1, $settings['board']['who_view']))
            $settings['groups']['-1']['checked'] = true;
          else
            $settings['groups']['-1']['checked'] = false;
          while($row = mysql_fetch_assoc($result)) {
            $settings['groups'][$row['group_id']] = array(
              'id' => $row['group_id'],
              'name' => $row['groupname'],
              'checked' => @in_array($row['group_id'], $settings['board']['who_view']) ? true : false
            );
          }
          $result = sql_query("
            SELECT
              c.cid, c.cname
            FROM {$db_prefix}categories AS c
            ORDER BY c.cid ASC");
          $settings['cats'] = array();
          while($row = mysql_fetch_assoc($result)) {
            $settings['cats'][] = array(
              'id' => $row['cid'],
              'name' => $row['cname'],
              'selected' => isSelected($row['cid']) ? true : false
            );
          }  
            
          $settings['page']['title'] = $l['manageboards_edit_title'];
          loadTheme('ManageForum','EditBoard');
        }
        else {
          // That board doesn't exist! D:
          $settings['page']['title'] = $l['manageboards_no_board_title'];
          loadTheme('ManageForum','NoBoard');
        }
      }
      // They don't have permission, so redrect them to main board management
      else
        redirect('index.php?action=admin;sa=forum;fa=boards');
    }
    elseif ($do == "permissions") {
      // We set board permissions by group here...
    }
    else {
      // Add boards or edit them as requested...
      if (!empty($_REQUEST['add_board'])) {
        $in_category = (int)$_REQUEST['in_category'];
        $board_name = clean($_REQUEST['board_name']);
        $board_desc = clean($_REQUEST['board_desc']);
        // Have they entered a board name?
        if (strlen(str_replace(' ','',$board_name)) <= 3) {
          $_SESSION['error'] = $l['manageboards_error_name'];
          //$_SESSION['error_values'] = serialize(array('cat_id'=>$cat_id,'subject'=>$subject,'body'=>$body,'allow_comments'=>!$allow_comments));
          redirect('index.php?action=admin;sa=forum;fa=boards;do=add');
        }
        if(is_array(@$_REQUEST['groups'])) {
          $who_view = array();
          foreach($_REQUEST['groups'] as $group) {
            $who_view[] = (int)$group;
          }
          $who_view = implode(',', $who_view);
        }
        else {
          $who_view = (int)@$_REQUEST['groups'];
        }
        sql_query("INSERT INTO {$db_prefix}boards (`cid`,`who_view`,`name`,`bdesc`) VALUES('$in_category','$who_view','$board_name','$board_desc')");
        redirect('index.php?action=admin;sa=forum;fa=boards');
      }
      if (!empty($_REQUEST['update_boards'])) {
        // Looks like we need to update a board... Ok!
        $result = sql_query("SELECT * FROM {$db_prefix}boards");
        while($row = mysql_fetch_assoc($result)) {
          $settings['boards'][$row['bid']] = array(
            'bid' => $row['bid'],
            'cid' => $row['cid'],
            'who_view' => $row['who_view'],
            'bdesc' => $row['bdesc'],
            'numtopics' => $row['numtopics'],
            'numposts' => $row['numposts'],
            'last_msg' => $row['last_msg'],
            'last_uid' => $row['last_uid'],
            'last_name' => $row['last_name']
          );
        }
        foreach ($_POST['board_name'] as $board_id => $board_name) {
          $board_id = (int)$board_id;
          $board_name = clean($board_name);
          $board_order = (int)$_POST['board_order'][$board_id];
          $boards[] = "('$board_id','{$settings['boards'][$board_id]['cid']}','$board_order','{$settings['boards'][$board_id]['who_view']}','$board_name','{$settings['boards'][$board_id]['bdesc']}','{$settings['boards'][$board_id]['numtopics']}','{$settings['boards'][$board_id]['numposts']}','{$settings['boards'][$board_id]['last_msg']}','{$settings['boards'][$board_id]['last_uid']}','{$settings['boards'][$board_id]['last_name']}')";
        }
        $query = implode(',', $boards);
        sql_query("REPLACE INTO {$db_prefix}boards (`bid`,`cid`,`border`,`who_view`,`name`,`bdesc`,`numtopics`,`numposts`,`last_msg`,`last_uid`,`last_name`) VALUES{$query}");
      }
      if (!empty($_REQUEST['update_board'])) {
        $board_id = (int)$_REQUEST['board_id'];
        $in_category = (int)$_REQUEST['in_category'];
        $board_name = clean($_REQUEST['board_name']);
        $board_desc = clean($_REQUEST['board_desc']);
        $who_view = @$_REQUEST['groups'] ? $_REQUEST['groups'] : null;
        $tmp_array = array();
        if(count($who_view)) {       
          foreach($who_view as $group_id) {
            $tmp_array[] = (int)$group_id;
          }
        }
        $who_view = implode(',', $tmp_array);
        sql_query("UPDATE {$db_prefix}boards SET `cid` = $in_category, `name` = '$board_name', `bdesc` = '$board_desc', `who_view` = '$who_view' WHERE `bid` = '$board_id'");
        setPermissions($board_id, @$_REQUEST['groups'], true);
      }
      // Are they trying to delete a board?
      if (!empty($_REQUEST['did'])) {
        // Are they allowed to?
        if (can('manage_forum_delete')) {
          // Is their session valid?
          if (validateSession($_REQUEST['sc'])) {
            $board_id = (int)$_REQUEST['did'];
          sql_query("DELETE FROM {$db_prefix}boards WHERE `bid` = $board_id");
          }
          // Their session isn't valid
          else
            $_SESSION['error'] = $l['managecats_error_delete_invalidsession'];
        }
        // They are not allowed to delete categories
        else
          $_SESSION['error'] = $l['managecats_error_delete_notallowed'];
        redirect('index.php?action=admin;sa=forum;fa=boards');
      }
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
  }
  // They don't have permission, so redrect them to the main forum management
  else
    redirect('index.php?action=admin;sa=forum');
}

// A quick function to help out :P
function isSelected($cid) {
global $settings;
  if($cid == $settings['board']['cid'])
    return true;
  else
    return false;
}

// This is called upon when a new board is made, that sets permissions ;)
function setPermissions($board_id, $groups_allowed, $type = false) {
global $db_prefix, $forumperms;
  
  $board_id = (int)$board_id;
  /*
    $type:
    1 = updating current ones
    0 = adding the default ones
  */
  if(!$type) {  
    /* Adding a new board, so just insert the permissions */
    foreach($groups_allowed as $group_id) {
      $group_id = (int)$group_id; 
      $perms = array();
      foreach($forumperms as $perm => $default) {
        if($default)
          $can = 1;
        else
          $can = 0;
        $perms[] = "('$board_id','$group_id','$perm','$can')"; 
      }
      $perms_query = implode(',', $perms);
      sql_query("REPLACE INTO {$db_prefix}board_permissions (`bid`,`group_id`,`what`,`can`) VALUES{$perms_query}");
    }
    sql_query("UPDATE {$db_prefix}board_permissions SET `can` = 0 WHERE `bid` = '$board_id' AND `group_id` = '-1'");
  }
  else {
    // Updating!
    if(!count($groups_allowed)) {
      // Hmmm, none allowed? :o except of course admins...
      sql_query("DELETE FROM {$db_prefix}board_permissions WHERE `bid` = '$board_id'");
    }
    else {
      $groups = array();
      foreach ($groups_allowed as $group_id) {
        $group_id = (int)$group_id;
        $result = sql_query("SELECT * FROM {$db_prefix}board_permissions WHERE `bid` = '$board_id' AND `group_id` = '$group_id'");
        echo mysql_error();
        if (!mysql_num_rows($result)) {
          // No permissions for this group yet, insert them :]
          $perms = array();
          foreach ($forumperms as $perm => $default) {
            if ($default)
              $can = 1;
            else
              $can = 0;
            $perms[] = "('$board_id','$group_id','$perm','$can')"; 
          }
          $perms_query = implode(',',$perms);
          sql_query("REPLACE INTO {$db_prefix}board_permissions (`bid`,`group_id`,`what`,`can`) VALUES{$perms_query}");
        }
        $groups[] = $group_id;
      }
      // OK! Now, lets delete groups that are not in the $groups_allowed array >:D
      sql_query("DELETE FROM {$db_prefix}board_permissions WHERE `bid` = '$board_id' AND `group_id` NOT IN(".implode(',',$groups).")");
      sql_query("UPDATE {$db_prefix}board_permissions SET `can` = 0 WHERE `bid` = '$board_id' AND `group_id` = '-1'");
    }
  }
}
?>