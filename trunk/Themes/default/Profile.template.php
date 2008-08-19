<?php
// default/Profile.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $settings;
  View();
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
        </table>
        
        <br />
        
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

function NotAllowed() {
global $cmsurl, $l, $settings;
echo '
  <h1>'.$l['profile_error_header'].'</h1>
  <p>'.$l['profile_error_desc'].'</p>';
}
?>