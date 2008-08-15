<?php
// default/Profile.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $settings;
  echo '<h1>'.$settings['page']['title'].'</h1>';
}

function View() {
global $settings;
  echo '<h1>'.$settings['page']['title'].'</h1>';
}

function AdminView() {
global $settings;
  echo '<h1>'.$settings['page']['title'].'</h1>';
}

function NotAllowed() {
global $cmsurl, $l, $settings;
echo '
  <h1>'.$l['profile_error_header'].'</h1>
  <p>'.$l['profile_error_desc'].'</p>';
}
?>