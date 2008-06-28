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
$permissions['group'] = array(
  'admin' => false,
  'manage_basic-settings' => false,
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
$permissions['forum'] = array(
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
global $cmsurl, $db_prefix, $l, $settings, $permissions, $user;
  if(can('manage_permissions')) {
    // Editing a Member Group already? Maybe just maybe we shoudl load that...
    if((empty($_REQUEST['mid'])) && (empty($_REQUEST['me']))) {  
      // Hmmm, updating being done? :O
      if(!empty($_REQUEST['update_perms'])) {
        // What member group are they setting these for?
        $membergroup = (int)addslashes(mysql_real_escape_string($_REQUEST['membergroup']));
        // Check if this group exists
        $result = mysql_query("SELECT * FROM {$db_prefix}membergroups WHERE `group_id` = '{$membergroup}'");
        if(mysql_num_rows($result)>0) {
          // Ok, the member group does exist! dang, :P
            foreach($permissions['group'] as $perm) {
              if(!empty($_POST[$perm])) 
                $can = 1;
              else
                $can = 0;
              mysql_query("REPLACE INTO {$db_prefix}permissions (`group_id`,`what`,`can`) VALUES('{$membergroup}','{$perm}','{$can}')") or die(mysql_error());
            }
          // Weeeee! Done!
        }
      }
      // Load the list of member groups, etc
      $result = mysql_query("
        SELECT 
          grp.group_id, grp.groupname AS name, p.what, COUNT(p.what) AS numperms,
          p.group_id, m.id, m.group, COUNT(m.id) AS numusers
        FROM {$db_prefix}membergroups AS grp 
          LEFT JOIN {$db_prefix}members AS m ON m.group = grp.group_id
          LEFT JOIN {$db_prefix}permissions AS p ON p.group_id = grp.group_id
        ORDER BY `group_id` DESC");
      while($row = mysql_fetch_assoc($result)) {
        $groups[] = $row;
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
    $settings['page']['title'] = $l['permissions_error_title'];
    loadTheme('Admin','Error');
  }
}
?>