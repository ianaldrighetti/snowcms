<?php
// default/Settings.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user;
  
}

function Basic() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['basicsettings_header'].'</h1>
  <p>'.$l['basicsettings_desc'].'</p>';
  echo '
  <table>';
  foreach($settings['page']['settings'] as $setting) {
    echo '
      <tr>
        <td>'.$l['basicsettings_'.$setting].'</td><td><input name="'.$setting.'" type="text" value="'.$settings[$setting].'"/></td>
      </tr>';
  }
  echo '
  </table>';
}