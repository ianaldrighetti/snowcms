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
  
  // Get member information
  $UID = @$_REQUEST['u'] ? (int)addslashes(mysql_real_escape_string($_REQUEST['u'])) : $user['id'];
  $result = sql_query("
     SELECT
       m.id, m.username, m.email, m.display_name, m.reg_date, m.reg_ip, m.last_login,
       m.last_ip, m.group, m.numposts, m.signature, m.profile, m.activated, grp.group_id, 
       grp.groupname, o.last_active
     FROM {$db_prefix}members AS m
       LEFT JOIN {$db_prefix}membergroups AS grp ON grp.group_id = m.group
       LEFT JOIN {$db_prefix}online AS o ON o.user_id = m.id
     WHERE m.id = $UID") or die(mysql_error());
  
  // Hmmm, is this account in the DB? D:
  if(mysql_num_rows($result))
    // It exists! :D
    while($row = mysql_fetch_assoc($result)) {
          $mem = array(
            'id' => $row['id'],
            'name' => $row['display_name'] ? $row['display_name'] : $row['username'],
            'username' => $row['display_name'] ? $row['display_name'] : $row['username'],
            'email' => $row['email'],
            'display_name' => $row['display_name'],
            'reg_date' => formattime($row['reg_date']),
            'online' => $row['last_active'] > time() - $settings['login_detection_time'] * 60,
            'ip' => $row['last_ip'] ? $row['last_ip'] : $row['reg_ip'],
            'group_name' => $row['groupname'],
            'group_id' => $row['group'],
            'posts' => $row['numposts'],
            'signature' => $row['signature'],
            'text' => $row['profile'],
            'activated' => $row['activated'],
          );
          $settings['page']['title'] = str_replace("%user%", $mem['name'], $l['profile_profile_of']);
          $settings['profile'] = $mem;
        }
  else {
      // Oh noes! It doesn't! Tell'em :P
      $settings['page']['title'] = $l['profile_error_title'];
      loadTheme('Profile','NoProfile');
    }
  
  // Are they changing settings?
  if ($UID == $user['id'] && @$_REQUEST['sa'] == 'edit') {
    $settings['page']['title'] = $l['profile_edit_title'];
    
    if (@$_REQUEST['ssa'] == 'process-edit')
      processEdit();
    else
      loadTheme('Profile','Settings');
  }
  // Is an admin trying to view someone's profile?
  elseif (can('view_profile') && $UID != $user['id'] && can('manage_members'))
    loadTheme('Profile','AdminView');
  // Maybe they are trying to view someone's profile? o.O
  elseif ((can('view_profile')) && ($UID!=$user['id']))
    loadTheme('Profile','View');
  // Are they logged in? .-.
  elseif ($user['is_logged'])
    loadTheme('Profile');
  // No! Go away! :)
  else {
    $settings['page']['title'] = $l['profile_error_title'];
    loadTheme('Profile','NotAllowed');
  }
}

function processEdit() {
global $settings, $db_prefix, $user, $cmsurl,$cookie_prefix;
  
  // Note: Error handling needs work
  
  // Check if someone else is using that display name
  $result = sql_query("SELECT * FROM {$db_prefix}members") or die(mysql_error());
  if (mysql_num_rows($result))
    while ($row = mysql_fetch_assoc($result)) {
      if ($row['id'] != $user['id'] && $_REQUEST['display_name'] != '' && ($_REQUEST['display_name'] == $row['username'] || $_REQUEST['display_name'] == $row['display_name']))
        die("Display name already in use");
    }
  
  // Check if the password is valid
  if ($_REQUEST['password-new']) {
    $result = sql_query("SELECT password FROM {$db_prefix}members WHERE `id` = '{$user['id']}'");
    $row = mysql_fetch_assoc($result) or die("Internal error");
  
    if (md5(@$_REQUEST['password-old']) != @$row['password'])
      die("Your current password is incorrect");
    if (@$_REQUEST['password-new'] != @$_REQUEST['password-verify'])
      die("Your verification password is incorrect");
    if (strlen($_REQUEST['password-new']) < 5)
      die("Your password is under five characters long");
  }
  
  // Check if the email address is valid
  if (!@$_REQUEST['email'])
    die("No email address");
  if(!preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i", @$_REQUEST['email']))
    die("Invalid email address");
  
  if (clean($_REQUEST['display_name']))
    $display_name = clean($_REQUEST['display_name']);
  else {
    $result = sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '{$user['id']}'");
    $row = mysql_fetch_assoc($result) or die("Internal error");
    
    $display_name = $row['username'];
  }
  $email = clean($_REQUEST[ 'email']);
  $signature = clean($_REQUEST['signature']);
  $profile = clean($_REQUEST['profile']);
  
  // Update member's data
  if (@$_REQUEST['password-new']) {
    $password_new = md5(@$_REQUEST['password-new']);
    sql_query("UPDATE {$db_prefix}members SET `display_name` = '$display_name', `email` = '$email', `signature` = '$signature', `profile` = '$profile', `password` = '$password_new' WHERE `id` = '{$user['id']}'");
    
    setcookie($cookie_prefix."password", $password_new);
    $_SESSION['pass'] = $password_new;
  }
  else
    sql_query("UPDATE {$db_prefix}members SET `display_name` = '$display_name', `email` = '$email', `signature` = '$signature', `profile` = '$profile' WHERE `id` = '{$user['id']}'");
  
  redirect('index.php?action=profile;u='.$user['id']);
}
?>