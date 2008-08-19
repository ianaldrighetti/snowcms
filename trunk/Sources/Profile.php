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
//                Profile.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

function Profile() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user, $perms;
  // Maybe they are trying to view someone's profile? o.O
  if((can('view_profile')) && ((!empty($_REQUEST['u'])) && ($_REQUEST['u']!=$user['id']))) {
    $UID = (int)addslashes(mysql_real_escape_string($_REQUEST['u']));
    $result = sql_query("
       SELECT
         m.id, m.username, m.email, m.display_name, m.reg_date, m.reg_ip, m.last_login,
         m.last_ip, m.group, m.numposts, m.signature, m.profile, grp.group_id, 
         grp.groupname, o.last_active
       FROM {$db_prefix}members AS m
         LEFT JOIN {$db_prefix}membergroups AS grp ON grp.group_id = m.group
         LEFT JOIN {$db_prefix}online AS o ON o.user_id = m.id
       WHERE m.id = $UID") or die(mysql_error());
    // Hmmm, is this account in this DB? D:
    if(mysql_num_rows($result)) {
      // It exists! :D
      while($row = mysql_fetch_assoc($result)) {
        $mem = array(
          'id' => $row['id'],
          'name' => $row['display_name'] ? $row['display_name'] : $row['username'],
          'username' => $row['display_name'] ? $row['display_name'] : $row['username'],
          'email' => $row['email'],
          'reg_date' => formattime($row['reg_date']),
          'online' => $row['last_active'] < time() - $settings['login_detection_time'] * 60,
          'ip' => $row['last_ip'] ? $row['last_ip'] : $row['reg_ip'],
          'group_name' => $row['groupname'],
          'group_id' => $row['group'],
          'posts' => $row['numposts'],
          'signature' => $row['signature'],
          'text' => $row['profile'],
        );
      }
      $settings['page']['title'] = str_replace("%user%", $mem['name'], $l['profile_profile_of']);
      $settings['profile'] = $mem;
      loadTheme('Profile','View');
    }
    else {
      // Oh noes! It doesnt! Tell'em :P
      $settings['page']['title'] = $l['profile_error_title'];
      loadTheme('Profile','NoProfile');
    }
  }
  else
  // Are they logged in? .-.
  if($user['is_logged']) {
    // Hmmm, Are 
    $settings['page']['title'] = $l['profile_title'];
    $UID = $user['id'];
    $result = sql_query("
       SELECT
         m.id, m.username, m.email, m.display_name, m.reg_date, m.reg_ip, m.last_login,
         m.last_ip, m.group, m.numposts, m.signature, m.profile, grp.group_id, 
         grp.groupname, o.last_active
       FROM {$db_prefix}members AS m
         LEFT JOIN {$db_prefix}membergroups AS grp ON grp.group_id = m.group
         LEFT JOIN {$db_prefix}online AS o ON o.user_id = m.id
       WHERE m.id = $UID") or die(mysql_error());
    // Hmmm, is this account in this DB? D:
    if(mysql_num_rows($result)) {
      // It exists! :D
      while($row = mysql_fetch_assoc($result)) {
        $mem = array(
          'id' => $row['id'],
          'name' => $row['display_name'] ? $row['display_name'] : $row['username'],
          'username' => $row['display_name'] ? $row['display_name'] : $row['username'],
          'email' => $row['email'],
          'reg_date' => formattime($row['reg_date']),
          'online' => $row['last_active'],
          'ip' => $row['last_ip'] ? $row['last_ip'] : $row['reg_ip'],
          'group_name' => $row['groupname'],
          'group_id' => $row['group'],
          'posts' => $row['numposts'],
          'signature' => $row['signature'],
          'text' => $row['profile'],
        );
      }
      $settings['page']['title'] = str_replace("%user%", $mem['name'], $l['profile_profile_of']);
      $settings['profile'] = $mem;
    }
    loadTheme('Profile');
  }
  else {
    // No! Go away! :)
    $settings['page']['title'] = $l['profile_error_title'];
    loadTheme('Profile','NotAllowed');
  }
}
?>