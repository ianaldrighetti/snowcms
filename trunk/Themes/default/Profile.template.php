<?php
// default/Profile.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $l, $settings, $cmsurl;
  
  echo '<h1 style="margin-bottom: 0">'.$settings['page']['title'].'</h1>
        <p style="margin-top: 0"><a href="'.$cmsurl.'index.php?action=profile&sa=edit">'.$l['profile_edit_link'].'</a></p>';
  
  Info();
}

function View() {
global $l, $settings, $theme_url;
  
  $profile = $settings['profile'];
  
  echo '<h1 style="margin-bottom: 0">'.$settings['page']['title'].'</h1>
        <table>
         '.($profile['online']
          ? '<tr><td><img src="'.$theme_url.'/'.$settings['theme'].'/online.gif"
              alt="'.$l['profile_online'].'" width="16" height="16" /></td><td><b>
            '.$l['profile_online']
          : '<tr><td><img src="'.$theme_url.'/'.$settings['theme'].'/offline.gif"
              alt="'.$l['profile_offline'].'" width="16" height="16" /></td><td><b>
            '.$l['profile_offline'])
          .'</b></td></tr>
        </table>';
  
  Info();
}

function Info() {
global $l, $settings;
  
  $profile = $settings['profile'];
        
  echo '<br />
        
        <table width="100%">
        <tr><th style="text-align: left">Member Group:</th><td>'.$profile['group_name'].'</td></tr>
        <tr><th style="text-align: left; width: 30%">Member Since:</th><td>'.$profile['reg_date'].'</td></tr>
        <tr><th style="text-align: left">Total Posts:</th><td>'.$profile['posts'].'</td></tr>
        <tr><th style="text-align: left">Email:</th><td><a href="mailto:'.$profile['email'].'">'.$profile['email'].'</a></td></tr>
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
        <h1>'.$settings['page']['title'].'</h1>
        
        <form action="'.$cmsurl.'index.php?action=profile&sa=edit" method="post" style="display: inline">
        
        <p><input type="hidden" name="ssa" value="process-edit" /></p>
        
        <table style="width: 100%" class="padding">
        <tr><th style="text-align: left">'.$l['profile_edit_display_name'].':</th><td><input name="display_name" value="'.$profile['display_name'].'" /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_email'].':</th><td><input name="email" value="'.$profile['email'].'" /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_signature'].':</th><td><textarea name="signature" cols="45" rows="4">'.$profile['signature'].'</textarea></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_profile_text'].':</th><td><textarea name="profile" cols="45" rows="4">'.$profile['text'].'</textarea></td></tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_password_old'].':</th><td><input type="password" name="password-old" /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_password_new'].':</th><td><input type="password" name="password-new" /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_password_verify'].':</th><td><input type="password" name="password-verify" /></td></tr>
        </table>
        
        <br />
        
        <p style="display: inline"><input type="submit" value="'.$l['profile_edit_change'].'" /></p>
        </form>
        
        <form action="'.$cmsurl.'index.php" method="get" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="profile" />
        <input type="hidden" name="u" value="'.$profile['id'].'" />
        <input type="submit" value="'.$l['profile_edit_cancel'].'" />
        </p>
        </form>
       <br />
       <br />
       ';
}

function NotAllowed() {
global $cmsurl, $l, $settings;
echo '
  <h1>'.$l['profile_error_header'].'</h1>
  <p>'.$l['profile_error_desc'].'</p>';
}
?>