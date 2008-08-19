<?php
// default/Profile.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $l, $settings, $cmsurl;
  
  echo '<h1 style="margin-bottom: 0">'.$settings['page']['title'].'</h1>
        <p style="margin-top: 0"><a href="'.$cmsurl.'index.php?action=profile&sa=edit">'.$l['profile_edit'].'</a></p>';
  
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
global $l, $settings;
  
  echo '<h1>'.$settings['page']['title'].'</h1>';
}

function NotAllowed() {
global $cmsurl, $l, $settings;
echo '
  <h1>'.$l['profile_error_header'].'</h1>
  <p>'.$l['profile_error_desc'].'</p>';
}
?>