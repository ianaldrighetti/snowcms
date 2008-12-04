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
//                 Profile.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

function Profile() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user, $perms;
  
  // Get member information
  $UID = @$_REQUEST['u'] ? (int)addslashes(mysql_real_escape_string($_REQUEST['u'])) : $user['id'];
  $result = sql_query("
     SELECT
       *
     FROM {$db_prefix}members AS m
       LEFT JOIN {$db_prefix}membergroups AS grp ON grp.group_id = m.group
       LEFT JOIN {$db_prefix}online AS o ON o.user_id = m.id
     WHERE m.id = $UID");
  
  // Are they a guest trying to view someone's email address?
  if ($settings['captcha'] && @$_REQUEST['sa'] == 'show-email' && $user['group'] == -1) {
    // Have they already completed the CAPTCHA?
    if (@$_REQUEST['captcha']) {
      // Salt used in hashing
      $salt = 'salt4me';
      if (sha1(strtolower(@$_REQUEST['captcha']).sha1($salt)) == @$_SESSION['captcha_'.sha1(sha1($salt))]) {$
        // It is, now let's redirect them back to their profile with this fact
        $_SESSION['passed_captcha'] = true;
        redirect('index.php?action=profile;u='.clean_header($UID));
      }
      else {
        // It isn't, let's inform them, incase they are human
        $_SESSION['error'] = $l['profile_showemail_error_captcha'];
        redirect('index.php?action=profile;sa=show-email;u='.clean_header($UID));
      }
    }
    else {
      // Nope, so let's make 'em
      $row = mysql_fetch_assoc($result);
      $settings['page']['uid'] = $UID;
      $settings['page']['username'] = $row['display_name'];
      $settings['page']['title'] = str_replace("%user%",$row['display_name'],$l['profile_showemail_title']);
      loadTheme('Profile','ShowEmail');
    }
  }
  else {
    // Hmmm, is this account in the DB? D:
    if(mysql_num_rows($result))
      // It exists! :D
      while($row = mysql_fetch_assoc($result)) {
            $mem = array(
              'id' => $row['id'],
              'name' => $row['display_name'] ? $row['display_name'] : $row['username'],
              'username' => $row['display_name'] ? $row['display_name'] : $row['username'],
              'email' => $row['email'],
              'email_guest' => hideEmail($row['email']),
              'birthdate' => ($row['birthdate']) ? formattime($row['birthdate']) : '',
              'birthdate_day' => ($row['birthdate']) ? date('j',$row['birthdate']) : '',
              'birthdate_month' => ($row['birthdate']) ? date('n',$row['birthdate']) : '',
              'birthdate_year' => ($row['birthdate']) ? date('Y',$row['birthdate']) : '',
              'avatar' => $row['avatar'],
              'display_name' => $row['display_name'],
              'reg_date' => formattime($row['reg_date']),
              'online' => $row['last_active'] > time() - $settings['login_detection_time'] * 60,
              'ip' => $row['last_ip'] ? $row['last_ip'] : $row['reg_ip'],
              'group_name' => $row['groupname'],
              'group_id' => $row['group'],
              'posts' => $row['numposts'],
              'icq' => $row['icq'],
              'aim' => $row['aim'],
              'msn' => $row['msn'],
              'yim' => $row['yim'],
              'gtalk' => $row['gtalk'],
              'site_name' => $row['site_name'],
              'site_url' => $row['site_url'],
              'signature' => $row['signature'],
              'text' => $row['profile'],
              'activated' => $row['activated'],
            );
            $settings['page']['title'] = str_replace("%user%", $mem['name'], $l['profile_profile_of']);
            $settings['profile'] = $mem;
          }
    else
      $invalid = true;
    
    // Is this an invalid profile?
    if (@$invalid) {
      // Oh noes! It is! Tell'em :P
      $settings['page']['title'] = $l['profile_noprofile_title'];
      loadTheme('Profile','NoProfile');
    }
    else
    // Are they changing settings?
    if ($UID == $user['id'] && @$_REQUEST['sa'] == 'edit') {
      $settings['page']['title'] = $l['profile_edit_title'];
      
      // Are they allowed to change their settings?
      if (can('change_settings')) {
        // Have they already submitted their settings to be changed?
        if (@$_REQUEST['ssa'] == 'process-edit')
          processEdit();
        else
          loadTheme('Profile','Settings');
      }
      else
        loadTheme('Profile','NotAllowedSettings');
    }
    // Is an admin trying to view someone's profile?
    elseif (can('view_profile') && $UID != $user['id'] && can('manage_members'))
      loadTheme('Profile','AdminView');
    // Maybe they are trying to view someone's profile? o.O
    elseif (can('view_profile') && $UID != $user['id']) {
      $settings['captcha'] = @$_SESSION['passed_captcha'];
      echo $settings['captcha'] === true;
      loadTheme('Profile','View');
    }
    // Are they logged in? .-.
    elseif ($user['is_logged'])
    loadTheme('Profile');
    // No! Go away! :)
    else {
      $settings['page']['title'] = $l['profile_notallowed_title'];
      loadTheme('Profile','NotAllowed');
    }
  }
}

function processEdit() {
global $l, $settings, $db_prefix, $user, $cmsurl, $cookie_prefix;
  
  // Get current member information
  $member_data = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = {$user['id']}"));
  
  // If the display name is empty, make it the username
  if (!@$_REQUEST['display_name'])
    $display_name = $member_data['username'];
  // Otherwise keep it as is
  else
    $display_name = clean($_REQUEST['display_name']);
  
  // Clean the email address
  $email = clean(@$_REQUEST['email']);
  // Clean the birthdate
  $day = (int)@$_REQUEST['day'];
  $month = (int)@$_REQUEST['month'];
  $year = (int)@$_REQUEST['year'];
  if ($day && $year)
    $birthdate = strtotime($year.'-'.$month.'-'.$day);
  else
    $birthdate = 0;
  // Clean the avatar
  $avatar = clean(@$_REQUEST['avatar']);
  if (substr($avatar,0,7) != 'http://' && substr($avatar,0,8) != 'https://' && substr($avatar,0,6) != 'ftp://' && substr($avatar,0,7) != 'ftps://' && $avatar != '')
    $avatar = 'http://'.$avatar;
  // Clean the ICQ
  $icq = clean(@$_REQUEST['icq']);
  // Clean the AIM
  $aim = clean(@$_REQUEST['aim']);
  // Clean the MSN
  $msn = clean(@$_REQUEST['msn']);
  // Clean the YIM
  $yim = clean(@$_REQUEST['yim']);
  // Clean the GTalk
  $gtalk = clean(@$_REQUEST['gtalk']);
  // Clean the site name
  $site_name = clean(@$_REQUEST['site_name']);
  // Clean the site URL
  $site_url = clean(@$_REQUEST['site_url']);
  // Clean the signature
  $signature = clean(@$_REQUEST['signature']);
  // Clean the profile text
  $profile = clean(@$_REQUEST['profile']);
  
  // Check if the password is valid
  if (@$_REQUEST['password-new']) {
    $result = sql_query("SELECT password FROM {$db_prefix}members WHERE `id` = '{$user['id']}'");
    $row = mysql_fetch_assoc($result);
  
    if (md5(@$_REQUEST['password-old']) != @$row['password'])
      $_SESSION['error'] = $l['profile_error_password_wrong'];
    elseif (@$_REQUEST['password-new'] != @$_REQUEST['password-verify'])
      $_SESSION['error'] = $l['profile_error_password_verify'];
    elseif (strlen($_REQUEST['password-new']) < 5)
      $_SESSION['error'] = $l['profile_error_password_short'];
  }
  
  // Check if the avatar is within the allowed sizes (Requires cURL and GD)
  // Only do so if the avatar is set, has changed and they are allowed to change it
  if($avatar && $avatar != $member_data['avatar'] && can('change_avatar') && function_exists('curl_init')) {
    $curl = curl_init($avatar);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_TIMEOUT,3);
    $curl = curl_exec($curl);
    // Did it load?
    if($curl) {
      // Is the file size small enough?
      if(strlen($curl) < $settings['avatar_size'] * 1028) {
        $gd = @imagecreatefromstring($curl);
        // Is the image too large?
        if(@imagesx($gd) > $settings['avatar_width'] || @imagesy($gd) > $settings['avatar_height'])
          $_SESSION['error'] = str_replace('%width%',$settings['avatar_width'],
                             str_replace('%height%',$settings['avatar_height'],$l['profile_error_avatar_size']));
        unset($gd);
      }
      // The file size is too large
      else
        $_SESSION['error'] = str_replace('%filesize%',$settings['avatar_size'],$l['profile_error_avatar_filesize']);
    }
    // It didn't load, so the avatar is invalid (Or cURL isn't working)
    else
      $_SESSION['error'] = $l['profile_error_avatar_invalid'];
  }
  
  // Check if the email address is valid
  if (!$email)
    $_SESSION['error'] = $l['profile_error_email_none'];
  elseif(!preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i", $email))
    $_SESSION['error'] = $l['profile_error_email_invalid'];
  
  // Check if someone else is using that display name
  $result = sql_query("SELECT * FROM {$db_prefix}members");
  while($row = mysql_fetch_assoc($result))
    if($row['id'] != $user['id'] && ($display_name == $row['username'] || $display_name == $row['display_name']))
      $_SESSION['error'] = $l['profile_error_displayname_used'];
  
  // Encode the new password
  $password_new = md5(@$_REQUEST['password-new']);
  
  // Are they trying to change their display name and are they allowed to?
  if(!can('change_display_name') && $display_name != $member_data['display_name'])
    $_SESSION['error'] = $l['profile_error_notallowed_displayname'];
  // Are they trying to change their email address and are they allowed to?
  elseif(!can('change_email') && $email != $member_data['email'])
    $_SESSION['error'] = $l['profile_error_notallowed_email'];
  // Are they trying to change their birthdate and are they allowed to?
  elseif(!can('change_birthdate') && $birthdate != $member_data['birthdate'])
    $_SESSION['error'] = $l['profile_error_notallowed_birthdate'];
  // Are they trying to change their avatar and are they allowed to?
  elseif(!can('change_avatar') && $avatar != $member_data['avatar'])
    $_SESSION['error'] = $l['profile_error_notallowed_avatar'];
  // Are they trying to change their ICQ and are they allowed to?
  elseif(!can('change_icq') && $icq != $row['icq'])
    $_SESSION['error'] = $l['profile_error_notallowed_icq'];
  // Are they trying to change their AIM and are they allowed to?
  elseif(!can('change_aim') && $aim != $row['aim'])
    $_SESSION['error'] = $l['profile_error_notallowed_aim'];
  // Are they trying to change their MSN and are they allowed to?
  elseif(!can('change_msn') && $msn != $row['msn'])
    $_SESSION['error'] = $l['profile_error_notallowed_msn'];
  // Are they trying to change their YIM and are they allowed to?
  elseif(!can('change_yim') && $yim != $row['yim'])
    $_SESSION['error'] = $l['profile_error_notallowed_yim'];
  // Are they trying to change their GTalk and are they allowed to?
  elseif(!can('change_gtalk') && $gtalk != $row['gtalk'])
    $_SESSION['error'] = $l['profile_error_notallowed_gtalk'];
  // Are they trying to change their site and are they allowed to?
  elseif(!can('change_site') && ($site_name != $row['site_name'] || $site_url != $row['site_url']))
    $_SESSION['error'] = $l['profile_error_notallowed_site'];
  // Are they trying to change their signature and are they allowed to?
  elseif(!can('change_signature') && $signature != $row['signature'])
    $_SESSION['error'] = $l['profile_error_notallowed_signature'];
  // Are they trying to change their profile text and are they allowed to?
  elseif(!can('change_profile') && $profile != $member_data['profile'])
    $_SESSION['error'] = $l['profile_error_notallowed_profile'];
  // Are they trying to change their password and are they allowed to?
  elseif(!can('change_password') && $password_new != $member_data['password'] && @$_REQUEST['password-new'] != '')
    $_SESSION['error'] = $l['profile_error_notallowed_password'];
  
  // Does the site URL not have a protocol?
  if (!preg_match('/^(ht|f)tps?:\/\//',$site_url))
    // Add http://
    $site_url = 'http://'.$site_url;
  
  // Is there an error?
  if (!@$_SESSION['error']) {
    // Update member's data
    if (@$_REQUEST['password-new']) {
      sql_query("UPDATE {$db_prefix}members SET `display_name` = '$display_name', `email` = '$email', `birthdate` = '$birthdate', `avatar` = '$avatar', `icq` = '$icq', `aim` = '$aim', `msn` = '$msn', `yim` = '$yim', `gtalk` = '$gtalk', `site_name` = '$site_name', `site_url` = '$site_url', `signature` = '$signature', `profile` = '$profile', `password` = '$password_new' WHERE `id` = '{$user['id']}'");
      
      setcookie($cookie_prefix."password", $password_new);
      $_SESSION['pass'] = $password_new;
    }
    else
      sql_query("UPDATE {$db_prefix}members SET `display_name` = '$display_name', `email` = '$email', `birthdate` = '$birthdate', `avatar` = '$avatar', `icq` = '$icq', `aim` = '$aim', `msn` = '$msn', `yim` = '$yim', `gtalk` = '$gtalk', `site_name` = '$site_name', `site_url` = '$site_url', `signature` = '$signature', `profile` = '$profile' WHERE `id` = '{$user['id']}'");
  redirect('index.php?action=profile;u='.$user['id']);
  }
  // There was an error
  else
    redirect('index.php?action=profile;sa=edit');
}
?>