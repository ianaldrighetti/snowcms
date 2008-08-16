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
global $l, $settings, $db_prefix;
  
  $settings['page']['title'] = $l['managemembers_title'];
  
  $settings['manage_members']['id_desc'] = '';
  $settings['manage_members']['username_desc'] = '';
  $settings['manage_members']['group_desc'] = '';
  $settings['manage_members']['joindate_desc'] = '';
  
  switch (@$_REQUEST['sort']) {
    case 'id':
      $sort = 'ORDER BY `id`, `reg_date`';
      $settings['manage_members']['id_desc'] = '_desc';
      break;
    case 'id_desc':
      $sort = 'ORDER BY `id` DESC, `reg_date`';
      break;
    case 'username':
      $sort = 'ORDER BY `username`, `reg_date`';
      $settings['manage_members']['username_desc'] = '_desc';
      break;
    case 'username_desc':
      $sort = 'ORDER BY `username` DESC, `reg_date`';
      break;
    case 'group':
      $sort = 'ORDER BY `groupname`, `reg_date`';
      $settings['manage_members']['group_desc'] = '_desc';
      break;
    case 'group_desc':
      $sort = 'ORDER BY `groupname` DESC, `reg_date`';
      break;
    case 'joindate':
      $sort = 'ORDER BY `reg_date`';
      $settings['manage_members']['joindate_desc'] = '_desc';
      break;
    case 'joindate_desc':
      $sort = 'ORDER BY `reg_date` DESC';
      break;
    default:
      $sort = 'ORDER BY `reg_date`';
      break;
  }
  
  $member_rows = sql_query("SELECT * FROM {$db_prefix}members LEFT JOIN {$db_prefix}membergroups ON `group` = `group_id`$sort") or die(mysql_error());
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
  
  // Get GET data
  if (@$_REQUEST['pg'])
    $settings['manage_members']['page_get'] = '&pg='.@$_REQUEST['pg'];
  else
    $settings['manage_members']['page_get'] = '';
  if (@$_REQUEST['sort'])
    $settings['manage_members']['sort_get'] = '&sort='.@$_REQUEST['sort'];
  else
    $settings['manage_members']['sort_get'] = '';
  
  // Check if there are any members on the page
  if ($settings['manage_members_per_page'] * @$_REQUEST['pg'] < mysql_num_rows($member_rows) && @$_REQUEST['pg'] >= 0)
    loadTheme('ManageMembers');
  else
    loadTheme('ManageMembers','NoMembers');
}

function loadProf() {
global $l, $settings, $db_prefix;
  
  $loadTheme = 0;
  
  $result = sql_query("SELECT * FROM {$db_prefix}members LEFT JOIN {$db_prefix}membergroups ON `group` = `group_id` WHERE id = ".@$_REQUEST['u']) or die(mysql_error());
  if (mysql_num_rows($result))
    if ($row = mysql_fetch_assoc($result)) {
      $settings['page']['title'] = str_replace("%name%",$row['username'],$l['managemembers_moderate_title']);
      $settings['managemembers']['member'] = $row;
      $loadTheme += 1;
    }
  
  $result = sql_query("SELECT * FROM {$db_prefix}membergroups") or die(mysql_error());
  if (mysql_num_rows($result)) {
    $settings['managemembers']['groups'] = $result;
    $loadTheme += 1;
  }
  
  if ($loadTheme == 2)
    loadTheme('ManageMembers','Profile');
}
?>