<?php
// default/Profile.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {

}

function NotAllowed() {
global $cmsurl, $l, $settings;
echo '
  <h1>'.$l['profile_error_header'].'</h1>
  <p>'.$l['profile_error_desc'].'</p>';
}
?>