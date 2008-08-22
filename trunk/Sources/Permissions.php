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
//          Permissions.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Make an Array of Permissions...
// You can add permissions by 'WHAT' => Default(true/false),
// WHAT as in, how will you check if they can or cant do it? accessed by can('WHAT')
// Default as in, when a new member group is made, and they go to edit permissions, is it checked by default or not?
$settings['permissions']['group'] = array(
  'admin' => false,
  'manage_basic-settings' => false,
  'manage_forum' => false,
  'manage_groups' => false,
  'manage_mail_settings' => false,
  'manage_members' => false,
  'manage_menus' => false,
  'manage_news' => false,
  'manage_pages' => false,
  'manage_permissions' => false,
  'manage_forum_perms' => false,
  'view_forum' => true,
  'view_online' => true,
  'view_profile' => true
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
              if(!empty($_POST[$perm])) 
                $can = 1;
              else
                $can = 0;
              if($can)
                sql_query("REPLACE INTO {$db_prefix}permissions (`group_id`,`what`,`can`) VALUES('{$membergroup}','{$perm}','{$can}')") or die(mysql_error());
              else
                sql_query("DELETE FROM {$db_prefix}permissions WHERE `group_id` = '{$membergroup}' AND `what` = '{$perm}'");
            }
          // Weeeee! Done!
        }
      }
      // Change groups' names and which one is default
      if (@$_REQUEST['change_groups']) {
        $default_group = clean($_REQUEST['default_group']);
        sql_query("UPDATE {$db_prefix}settings SET `value` = '$default_group' WHERE `variable` = 'default_group'") or die(mysql_error());
        foreach ($_REQUEST as $key => $value) {
         if (substr($key,0,6) == 'group_') {
           $group_id = clean(substr($key,6,strlen($key)));
           $group_name = clean($value);
           sql_query("UPDATE {$db_prefix}membergroups SET `groupname` = '$group_name' WHERE `group_id` = '$group_id'") or die(mysql_error());
         }
        }
      }
      // Create a new group
      if (@$_REQUEST['new_group']) {
        $new_group = clean($_REQUEST['new_group']);
        sql_query("INSERT INTO {$db_prefix}membergroups (`groupname`) VALUES ('$new_group')") or die(mysql_error());
      }
      // Delete a group
      if (@$_REQUEST['did']) {
        $group = clean($_REQUEST['did']);
        sql_query("DELETE FROM {$db_prefix}membergroups WHERE `group_id` = '$group'") or die(mysql_error());
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
    loadTheme('Permissions','Edit');
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Permissions','NoGroup');
  }
}
?>