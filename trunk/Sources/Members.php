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
//            Members.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function ManageMembers() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('manage_members')) {
    $settings['page']['title'] = $l['managemembers_title'];
    // So they can, yippe for you! :P
    // Are they just viewing the list, or managing a member, or something else perhaps?
    if((empty($_REQUEST['u'])) && (empty($_REQUEST['ssa']))) {
      // K, just load the list of members
      loadMlist();
    }
    elseif((!empty($_REQUEST['u'])) && (empty($_REQUEST['ssa']))) {
      // :o They are moderating/viewing someones profile
      loadProf();
    }
    else {
      // A Super Sub Action :D!
      if($_REQUEST['ssa']=='ua') {
        // Okay, list all unactivated accounts...
        loadUA();
      }
    }
  }
  else {
    // You can't Manage Members silly!
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}

function loadMlist() {
global $db_prefix, $settings;
  
  $member_rows = sql_query("SELECT * FROM {$db_prefix}members LEFT JOIN {$db_prefix}membergroups ON `group` = `group_id`") or die(mysql_error());
  $settings['manage_members']['member_rows'] = $member_rows;
  
  $settings['manage_members']['total_members'] = mysql_num_rows($member_rows);
  $settings['manage_members']['page'] = @$_REQUEST['pg'];
  $settings['manage_members']['page_start'] = $settings['manage_members_per_page'] * @$_REQUEST['pg'] + 1;
  if ($settings['manage_members_per_page'] * (@$_REQUEST['pg'] + 1) > mysql_num_rows($member_rows))
    $settings['manage_members']['page_end'] = mysql_num_rows($member_rows);
  else
    $settings['manage_members']['page_end'] = $settings['manage_members_per_page'] * (@$_REQUEST['pg'] + 1);
  $settings['manage_members']['next_page'] = @$_REQUEST['pg'] + 1;
  $settings['manage_members']['prev_page'] = @$_REQUEST['pg'] - 1;
  
  // Check if there are any members on the page
  if ($settings['manage_members_per_page'] * @$_REQUEST['pg'] <= mysql_num_rows($member_rows) && @$_REQUEST['pg'] >= 0)
    loadTheme('ManageMembers');
  else
    loadTheme('ManageMembers','NoMembers');
}
?>