<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//          redistribute it as your wish!
//
//               Permisions.php file


if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Make an Array of Permissions...
// You can add permissions by 'WHAT' => Default(true/false),
// WHAT as in, how will you check if they can or cant do it? accessed by can('WHAT')
// Default as in, when a new member group is made, and they go to edit permissions, is it checked by default or not?
$settings['permissions']['group'] = array(
  'view_forum' => true,
  'view_online' => true,
  'view_profile' => true,
  'search' => true,
  'pm_view' => true,
  'pm_compile' => true,
  'pm_delete' => true,
  'change_display_name' => true,
  'change_email' => true,
  'change_birthdate' => true,
  'change_avatar' => true,
  'change_signature' => true,
  'change_profile' => true,
  'change_password' => true,
  'admin' => false,
  'manage_basic-settings' => false,
  'manage_forum' => false,
  'manage_groups' => false,
  'manage_mail_settings' => false,
  'manage_members' => false,
  'manage_menus' => false,
  'manage_news' => false,
  'manage_pages_modify_html' => false,
  'manage_pages_modify_bbcode' => false,
  'manage_pages_create' => false,
  'manage_pages_delete' => false,
  'manage_pages_home' => false,
  'manage_permissions' => false,
  'manage_forum_perms' => false,
  'manage_ips_ban' => false,
  'manage_ips_unban' => false,
  'manage_tos' => false,
  'moderate_username' => false,
  'moderate_display_name' => false,
  'moderate_email' => false,
  'moderate_password' => false,
  'moderate_birthdate' => false,
  'moderate_avatar' => false,
  'moderate_group' => false,
  'moderate_signature' => false,
  'moderate_profile' => false,
  'moderate_activate' => false,
  'moderate_suspend' => false,
  'moderate_unsuspend' => false,
  'moderate_ban' => false,
  'moderate_unban' => false
);
// The above array was a member group specific, these are forum specific permissions for boards
$settings['permissions']['forum'] = array(
  'delete_any' => false,  
  'delete_own' => true,
  'lock_topic' => false,
  'move_any' => false,
  'edit_any' => false,
  'edit_own' => true,
  'post_new' => true,
  'post_reply' => true,
  'sticky_topic' => false
);

function GroupPermissions() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('manage_permissions')) {
    // Editing a Member Group already? Maybe just maybe we should load that...
    if((empty($_REQUEST['mid'])) && (empty($_REQUEST['me']))) {
      // Hmmm, updating being done? :O
      if(!empty($_REQUEST['update_perms'])) {
        // What member group are they setting these for?
        $membergroup = (int)addslashes(mysql_real_escape_string($_REQUEST['membergroup']));
        // Check if this group exists
        $result = mysql_query("SELECT * FROM {$db_prefix}membergroups WHERE `group_id` = '{$membergroup}'");
        if(mysql_num_rows($result)>0) {
          // Ok, the member group does exist! dang, :P
            foreach($settings['permissions']['group'] as $perm => $value) {
              if(!empty($_POST[$perm]) || !empty($_POST['all'])) 
                $can = 1;
              else
                $can = 0;
              if($can)
                sql_query("REPLACE INTO {$db_prefix}permissions (`group_id`,`what`,`can`) VALUES('{$membergroup}','{$perm}','{$can}')") or die(mysql_error());
              else
                sql_query("DELETE FROM {$db_prefix}permissions WHERE `group_id` = '{$membergroup}' AND `what` = '{$perm}'");
            }
          // Weeeee! Done!
          redirect('index.php?action=admin;sa=permissions');
        }
      }
      // Change groups' names and which one is default
      if (@$_REQUEST['change_groups']) {
        $default_group = clean($_REQUEST['default_group']);
        if ($default_group != -1) {
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$default_group' WHERE `variable` = 'default_group'") or ($_SESSION['error'] = $l['permissions_error_change']);
          foreach ($_REQUEST as $key => $value) {
            if (substr($key,0,6) == 'group_') {
              $group_id = clean(substr($key,6,strlen($key)));
              $group_name = clean($value);
              sql_query("UPDATE {$db_prefix}membergroups SET `groupname` = '$group_name' WHERE `group_id` = '$group_id'") or ($_SESSION['error'] = $l['permissions_error_change']);
            }
          }
        }
        else
          $_SESSION['error'] = $l['permissions_error_default_guest'];
        redirect('index.php?action=admin;sa=permissions');
      }
      // Create a new group
      if (@$_REQUEST['new_group']) {
        $new_group = clean($_REQUEST['new_group']);
        sql_query("INSERT INTO {$db_prefix}membergroups (`groupname`) VALUES ('$new_group')") or ($_SESSION['error'] = $l['permissions_error_new']);
        redirect('index.php?action=admin;sa=permissions');
      }
      // Delete a group
      if (@$_REQUEST['did']) {
        $group = clean($_REQUEST['did']);
        if ($group != 1)
          if ($group != -1)
            if ($group != $settings['default_group']) {
              $continue = sql_query("UPDATE {$db_prefix}members SET `group` = '{$settings['default_group']}' WHERE `group` = '$group'") or ($_SESSION['error'] = $l['permissions_error_delete']);
              if ($continue)
                $continue = sql_query("DELETE FROM {$db_prefix}permissions WHERE `group_id` = $group");
              if ($continue)
                sql_query("DELETE FROM {$db_prefix}board_permissions WHERE `group_id` = $group");
              if ($continue)
                $continue = sql_query("DELETE FROM {$db_prefix}membergroups WHERE `group_id` = '$group'") or ($_SESSION['error'] = $l['permissions_error_delete']);
            }
          else
            $_SESSION['error'] = $l['permissions_error_delete_default'];
          else
            $_SESSION['error'] = $l['permissions_error_delete_guest'];
        else
          $_SESSION['error'] = $l['permissions_error_delete_admin'];
        redirect('index.php?action=admin;sa=permissions');
      }
      // Load the list of member groups, etc
      $result = sql_query("
        SELECT 
          grp.group_id AS id, grp.groupname AS name
        FROM {$db_prefix}membergroups AS grp 
        ORDER BY grp.group_id ASC");
      while($row = mysql_fetch_assoc($result)) {
        $groups[$row['id']] = array(
                                'id' => $row['id'],
                                'name' => $row['name'],
                                'numusers' => 0,
                                'numperms' => 0
                              );
      }
      $result = sql_query("SELECT `group`, COUNT(*) FROM {$db_prefix}members GROUP BY `group`");
        while($row = mysql_fetch_assoc($result)) {
          $groups[$row['group']]['numusers'] = $row['COUNT(*)']; 
        }
      $result = sql_query("SELECT `group_id`, COUNT(*) FROM {$db_prefix}permissions WHERE `can` = '1' GROUP BY `group_id`");
        while($row = mysql_fetch_assoc($result)) {
          $groups[$row['group_id']]['numperms'] = $row['COUNT(*)']; 
        }
      $settings['page']['total_permissions'] = count($settings['permissions']['group']);
      $settings['page']['title'] = $l['permissions_title'];
      $settings['groups'] = $groups;
      loadTheme('Permissions');
    }
    elseif((!empty($_REQUEST['me'])) && (empty($_REQUEST['mid']))) {
      // They want to edit a member group...
      loadME();
      // And no, we aren't loading Microsoft Windows ME, lol.
    }
    else {
      // Now they are actually in the place where the check and uncheck things member groups can and cant do
      loadMID();
    }
  }
  else {
    $settings['page']['title'] = $l['permissions_error_title'];
    loadTheme('Admin','Error');
  }
}

function ForumPermissions() {
global $cmsurl, $db_prefix, $l, $settings, $permissions, $user;
  if(can('manage_forum_perms')) {
  
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}
function loadMID() {
global $cmsurl, $db_prefix, $l, $settings, $permissions, $user;
  $MID = (int)addslashes(mysql_real_escape_string($_REQUEST['mid']));
  $result = sql_query("
     SELECT
       grp.group_id, grp.groupname, p.group_id, p.what, p.can
     FROM {$db_prefix}permissions AS p
       LEFT JOIN {$db_prefix}membergroups AS grp ON grp.group_id = p.group_id
     WHERE grp.group_id = $MID");
  $group = sql_query("SELECT * FROM {$db_prefix}membergroups WHERE `group_id` = '$MID'");
  if(mysql_num_rows($group)>0) {
    $settings['perms'] = array();
    while($row = mysql_fetch_assoc($result))
      $settings['perms'][$row['what']] = array(
                               'groupname' => $row['groupname'],
                               'id' => $row['group_id'],
                               'what' => $row['what'],
                               'can' => $row['can']
                             );
    $settings['page']['title'] = $l['permissions_editperms_title'];
    $settings['page']['total_permissions'] = count($settings['permissions']['group']);
    loadTheme('Permissions','Edit');
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Permissions','NoGroup');
  }
}

function ForumPerms() {
global $cmsurl, $db_prefix, $forumperms, $l, $settings, $user;
  if(can('manage_forum_perms')) {
    // Are they editing a groups board permission right now?
    if(!empty($_REQUEST['bid']) && !empty($_REQUEST['gid'])) {
      // Get some data from the URL
      $board_id = (int)$_REQUEST['bid'];
      $group_id = (int)$_REQUEST['gid'];
      // Get the groups name, just for prettiness :P
      $result = sql_query("
        SELECT
          grp.group_id, grp.groupname
        FROM {$db_prefix}membergroups AS grp
        WHERE grp.group_id = $group_id");
      // I said fetch! Respect my authoritay!
      $row = mysql_fetch_assoc($result);
      $group_name = $row['groupname'];
      // Now the board name ;)
      $result = sql_query("
        SELECT
          b.bid, b.name
        FROM {$db_prefix}boards AS b
        WHERE b.bid = $board_id");
      // Now get it :P I don't pay you for nothing, oh... :\
      $row = mysql_fetch_assoc($result);
      $board_name = $row['name'];
      $result = sql_query("
        SELECT
          p.bid, p.group_id, p.what, p.can
        FROM {$db_prefix}board_permissions AS p
        WHERE p.group_id = $group_id AND p.bid = $board_id");
      $settings['permissions'] = array();
      while($row = mysql_fetch_assoc($result)) {
        $settings['permissions'][$row['what']] = array(
                                       'bid' => $row['bid'],
                                       'group_id' => $row['group_id'],
                                       'what' => $row['what'],
                                       'can' => $row['can'] ? true : false,
                                       'checked' => $row['can'] ? 'checked="checked"' : ''
                                     );
      }
      $settings['board'] = $board_id;
      $settings['group_id'] = $group_id;
      $settings['page']['title'] = $l['mf_gp_board_title'];
      $settings['perms'] = $forumperms;
      $replace = array(
                   '%group%' => $group_name,
                   '%boardname%' => $board_name
                 );
      $l['mf_gp_board_desc'] = str_replace(array_keys($replace), array_values($replace), $l['mf_gp_board_desc']);
      loadTheme('Permissions','BoardEdit');
    }
    elseif(!empty($_REQUEST['bid'])) {
      // Choosing a group they want to edit :o
      $board_id = (int)$_REQUEST['bid'];
      // We need to do some updating for you?
      if(!empty($_REQUEST['update_perms'])) {
        // What group id are we editing?
        $group_id = (int)$_REQUEST['group_id'];
        if(count($_REQUEST['perms'])) {
          $perms = array();
          // Go through all the permissions setable and set them ;)
          foreach($forumperms as $perm => $default) {
            // Can they or can't they? If it isn't set, they can't :P
            $can = isset($_REQUEST['perms'][$perm]) ? 1 : 0;
            // Guests Shouldn't be able to post... Hopefully we will actually make this work later on in SnowCMS
            if($group_id==-1)
              $can = 0;
            $perms[] = "'$board_id','$group_id','$perm','$can'";
          }
          $perms_query = implode("),(", $perms);
          sql_query("REPLACE INTO {$db_prefix}board_permissions (`bid`,`group_id`,`what`,`can`) VALUES({$perms_query})");
        }
        else {
          // D: No permissions were given, set them all to 0 >:D
          sql_query("UPDATE {$db_prefix}board_permissions SET `can` = '0' WHERE `bid` = $board_id AND `group_id` = $group_id");
        }
      }
      $result = sql_query("
        SELECT
          b.bid, b.name, b.who_view, b.cid, c.cid, c.cname
        FROM {$db_prefix}boards AS b
          LEFT JOIN {$db_prefix}categories AS c ON c.cid = b.cid
        WHERE b.bid = $board_id");
      if(mysql_num_rows($result)) {
        // Ok. It exists, now we need to get the allowed groups ;)
        $row = mysql_fetch_assoc($result);
        $result = sql_query("
          SELECT
            grp.group_id, grp.groupname
          FROM {$db_prefix}membergroups AS grp
          WHERE grp.group_id IN ($row[who_view])");
        $settings['groups'] = array();
        while($row = mysql_fetch_assoc($result)) {
          $settings['groups'][] = array(
                                    'id' => $row['group_id'],
                                    'name' => $row['groupname']
                                  );
        }
        $settings['board'] = $board_id;
        $settings['page']['title'] = $l['mf_bp_board_title'];
        loadTheme('Permissions','MGList');      
      }
      else {
        // That board doesn't exist o.o
        $settings['page']['title'] = $l['mf_bp_board_title'];
        loadTheme('Permissions','NoBoard');
      }
    }
    else {
      // Show a list of boards O_O
      $result = sql_query("
        SELECT
          c.cname, c.cid, c.corder
        FROM {$db_prefix}categories AS c
        ORDER BY c.corder ASC");
      if(!mysql_num_rows($result)) {
        // Oh noes! No categories?!?#!
        $settings['page']['title'] = $l['mf_perms_title'];
        loadTheme('Permissions','NoCats');
      }
      else {
        $cats = array();
        while($row = mysql_fetch_assoc($result)) {
          $cats[$row['cid']] = array(
            'id' => $row['cid'],
            'name' => $row['cname'],
            'boards' => array()
          );
        }
        $result = sql_query("
          SELECT
            b.bid, b.name, b.cid, b.border
          FROM {$db_prefix}boards AS b
          ORDER BY b.border ASC");
        while($row = mysql_fetch_assoc($result)) {
          $cats[$row['cid']]['boards'][] = array(
            'id' => $row['bid'],
            'name' => $row['name']
          );
        }
        $settings['cats'] = $cats;
        $settings['page']['title'] = $l['mf_perms_title'];
        loadTheme('Permissions','BoardPerms');
      }
    }
  }
  else {
    $settings['page']['title'] = $l['permissions_error_title'];
    loadTheme('Admin','Error');
  }
}
?>