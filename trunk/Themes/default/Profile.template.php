<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//          Profile.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $l, $settings, $cmsurl;
  
  $profile = $settings['profile'];
  
  if ($profile['avatar'])
    echo '
  <p style="float: right"><img src="'.$profile['avatar'].'" alt="'.str_replace('%user%',$profile['username'],$l['profile_avatar_own']).'" /></p>';
  
  echo '
  <h1>'.str_replace('%user%',$profile['display_name'],$l['profile_own_header']).'</h1>
  ';
  
  if (can('change_settings'))
    echo '<p style="margin-top: 0"><a href="'.$cmsurl.'index.php?action=profile;sa=edit">'.$l['profile_edit_link'].'</a></p>
  ';
  
  Info();
}

function View() {
global $l, $settings, $theme_url;
  
  $profile = $settings['profile'];
  
  if ($profile['avatar'])
    echo '
  <p style="float: right"><img src="'.$profile['avatar'].'" alt="'.str_replace('%user%',$profile['username'],$l['profile_avatar']).'" /></p>';
  
  echo '
  <h1>'.str_replace('%user%',$profile['display_name'],$l['profile_header']).'</h1>
        <table>
         '.($profile['online']
          ? '<tr><td><img src="'.$theme_url.'/'.$settings['theme'].'/images/status_online.png"
              alt="'.$l['profile_online'].'" width="16" height="16" /></td><td>
            '.$l['profile_online']
          : '<tr><td><img src="'.$theme_url.'/'.$settings['theme'].'/images/status_offline.png"
              alt="'.$l['profile_offline'].'" width="16" height="16" /></td><td>
            '.$l['profile_offline'])
          .'</td></tr>
        </table>';
  
  Info();
}

function AdminView() {
global $l, $settings, $theme_url, $cmsurl;
  
  $profile = $settings['profile'];
  
  if ($profile['avatar'])
    echo '
  <p style="float: right"><img src="'.$profile['avatar'].'" alt="'.str_replace('%user%',$profile['username'],$l['profile_avatar']).'" /></p>';
  
  echo '
  <h1>'.str_replace('%user%',$profile['display_name'],$l['profile_header']).'</h1>
        <table>
         '.($profile['online']
          ? '<tr><td><img src="'.$theme_url.'/'.$settings['theme'].'/images/status_online.png"
              alt="'.$l['profile_online'].'" width="16" height="16" /></td><td>
            '.$l['profile_online']
          : '<tr><td><img src="'.$theme_url.'/'.$settings['theme'].'/images/status_offline.png"
              alt="'.$l['profile_offline'].'" width="16" height="16" /></td><td>
            '.$l['profile_offline'])
          .' - <a href="'.$cmsurl.'index.php?action=admin;sa=members;u='.$profile['id'].'">'.$l['profile_moderate'].'</a></td></tr>
        </table>';
  
  Info();
}

function Info() {
global $l, $settings, $user, $cmsurl;
  
  $profile = $settings['profile'];
        
  echo '<br />
        
        <table width="100%">
        <tr><th style="text-align: left">'.$l['profile_group'].'</th><td>'.$profile['group_name'].'</td></tr>
        <tr><th style="text-align: left; width: 30%">'.$l['profile_joindate'].'</th><td>'.$profile['reg_date'].'</td></tr>
        <tr><th style="text-align: left; width: 30%">'.$l['profile_birthdate'].'</th><td>'.($profile['birthdate'] ? $profile['birthdate'] : '<i>'.$l['profile_birthdate_unknown'].'</i>').'</td></tr>
        <tr><th style="text-align: left">'.$l['profile_posts'].'</th><td>'.$profile['posts'].'</td></tr>
        ';
  if ($user['group'] != -1 || $settings['captcha'])
    echo '<tr><th style="text-align: left">'.$l['profile_email'].'</th><td><a href="mailto:'.$profile['email'].'">'.$profile['email'].'</a></td></tr>';
  else
    echo '<tr><th style="text-align: left">'.$l['profile_email'].'</th><td><a href="'.$cmsurl.'index.php?action=profile;sa=show-email;u='.$profile['id'].'">'.$profile['email_guest'].'</a></td></tr>';
  echo '
        </table>
        
        <p>
        '.bbc($profile['text']).'
        </p>
        ';
}

function Settings() {
global $l, $settings, $cmsurl, $user;
  
  $profile = $settings['profile'];
  
  echo '
  <h1>'.$l['profile_edit_header'].'</h1>
  ';
  
  if (@$_SESSION['error'])
	  echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
	
	echo '
  <p>'.$l['profile_edit_desc'].'</p>
        ';
        
  echo '<form action="'.$cmsurl.'index.php?action=profile;sa=edit" method="post" style="display: inline">
        
        <p><input type="hidden" name="ssa" value="process-edit" /></p>
        
        <table style="width: 100%" class="padding">
        <tr><th style="width: 130px"></th><td></td></tr>
        ';
  
  if (can('change_display_name'))
    echo '<tr><th style="text-align: left">'.$l['profile_edit_display_name'].'</th><td><input name="display_name" value="'.$profile['display_name'].'" /></td></tr>
        ';
  else
    echo '<tr style="display: none"><td colspan="2"><input type="hidden" name="display_name" value="'.$profile['display_name'].'" /></td></tr>';
  
  if (can('change_email'))
    echo '<tr><th style="text-align: left">'.$l['profile_edit_email'].'</th><td><input name="email" value="'.$profile['email'].'" /></td></tr>
        ';
  else
    echo '<tr style="display: none"><td colspan="2"><input type="hidden" name="email" value="'.$profile['email'].'" /></td></tr>';
  
  if (can('change_birthdate')) {
    echo '<tr><th style="text-align: left">'.$l['profile_edit_birthdate'].'</th><td>
          <input name="day" value="'.$profile['birthdate_day'].'" size="1" />
          -
          <select name="month" style="width: 50px">
            ';
    
    $i = 1;
    while ($i <= 12) {
      if ($profile['birthdate_month'] == $i)
        echo '<option value="'.$i.'" selected="selected">'.$l['main_month_'.$i.'_short'].'</option>
          ';
      else
        echo '<option value="'.$i.'">'.$l['main_month_'.$i.'_short'].'</option>
          ';
      $i += 1;
    }
    
    echo '</select>
          -
          <input name="year" value="'.$profile['birthdate_year'].'" size="1" />
        </td></tr>
        ';
  }
  else
    echo '<tr style="display: none"><td colspan="2"><input type="hidden" name="day" value="'.$profile['birthdate_day'].'" />
          <input type="hidden" name="month" value="'.$profile['birthdate_month'].'" />
          <input type="hidden" name="year" value="'.$profile['birthdate_year'].'" /></td></tr>';
  
  if (can('change_avatar'))
    echo '<tr><th style="text-align: left">'.$l['profile_edit_avatar'].'</th><td><input name="avatar" value="'.$profile['avatar'].'" /></td></tr>
        ';
  else
    echo '<tr style="display: none"><td colspan="2"><input type="hidden" name="avatar" value="'.$profile['avatar'].'" /></td></tr>';
  
  if (can('change_signature'))
    echo '<tr><th style="text-align: left">'.$l['profile_edit_signature'].'</th><td><textarea name="signature" cols="45" rows="4">'.$profile['signature'].'</textarea></td></tr>
        ';
  else
    echo '<tr style="display: none"><td colspan="2"><input type="hidden" name="signature" value="'.$profile['signature'].'" /></td></tr>';
  
  if (can('change_profile'))
    echo '<tr><th style="text-align: left">'.$l['profile_edit_profile'].'</th><td><textarea name="profile" cols="45" rows="4">'.$profile['text'].'</textarea></td></tr>
        ';
  else
    echo '<tr style="display: none"><td colspan="2"><input type="hidden" name="profile" value="'.$profile['text'].'" /></td></tr>';
  
  if (can('change_password'))
  echo '<tr><td colspan="2"><br /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_password_old'].'</th><td><input type="password" name="password-old" /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_password_new'].'</th><td><input type="password" name="password-new" /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_password_verify'].'</th><td><input type="password" name="password-verify" /></td></tr>
        ';
  
  echo '</table>
        
        <br />
        
        <p style="display: inline"><input type="submit" value="'.$l['profile_edit_change'].'" /></p>
        </form>
        
        <form action="'.$cmsurl.'index.php?action=profile" method="post" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="profile" />
        <input type="submit" value="'.$l['profile_edit_cancel'].'" />
        </p>
        </form>
       <br />
       <br />
       ';
}

function NoProfile() {
global $cmsurl, $l, $settings;
  
  echo '
  <h1>'.$l['profile_noprofile_header'].'</h1>
  <p>'.$l['profile_noprofile_desc'].'</p>';
}

function NotAllowed() {
global $cmsurl, $l, $settings, $user;
  
  echo '
  <h1>'.$l['profile_notallowed_header'].'</h1>
  ';
  if ($user['is_logged'])
    echo '<p>'.$l['profile_notallowed_desc'].'</p>';
  else
    echo '<p>'.$l['profile_notallowed_desc_loggedout'].'</p>';
}

function NotAllowedSettings() {
global $cmsurl, $l, $settings, $user;
  
  echo '
  <h1>'.$l['profile_edit_notallowed_header'].'</h1>
  ';
  if ($user['is_logged'])
    echo '<p>'.$l['profile_edit_notallowed_desc'].'</p>';
  else
    echo '<p>'.$l['profile_edit_notallowed_desc_loggedout'].'</p>';
}

function ShowEmail() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.str_replace('%user%',$settings['page']['username'],$l['profile_showemail_header']).'</h1>
  ';
  
  if (@$_SESSION['error'])
	 echo '<p>'.$_SESSION['error'].'</p>';
  
  echo '
  <p>'.$l['profile_showemail_desc'].'</p>
  
  <form action="'.$cmsurl.'index.php?action=profile;sa=show-email;u='.$settings['page']['uid'].'" method="post">
    <p><img src="'.$cmsurl.'image.php" alt="CAPTCHA" /></p>
    <p>
      <input name="captcha" />
      <input type="submit" value="'.$l['profile_showemail_submit'].'" />
    </p>
  </form>
  ';
}
?>