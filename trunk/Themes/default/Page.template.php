<?php
// default/Page.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $cmsurl, $l, $settings, $user;
  echo '
		<h2>'.$settings['page']['title'].'</h2>
		<hr>
		<p>'.$settings['page']['content'].'</p> 
		<br />
		;
}

function Error() {
global $cmsurl, $l, $settings, $user;
  echo '
  <h1>'.$l['page_error_header'].'</h1>
  <p>'.$l['page_error_details'].'</p>';
}