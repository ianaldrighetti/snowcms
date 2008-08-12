<?php
// Error.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function BNotAllowed() {
global $cmsurl, $l, $settings, $user;
echo '
  <div id="error">
    <p class="title">'.$l['forum_error_header'].'</p>
    <p class="message">'.$l['forum_error_message'].'</p>
  </div>';
}

function CantViewB() {
global $cmsurl, $l, $settings, $user;
echo '
  <div id="error">
    <p class="title">'.$l['forum_error_header'].'</p>
    <p class="message">'.$l['forum_error_cantviewb_message'].'</p>
  </div>';
}

function NoBoard() {
global $cmsurl, $l, $settings, $user;
echo '
  <div id="error">
    <p class="title">'.$l['forum_error_header'].'</p>
    <p class="message">'.$l['forum_error_noboard_message'].'</p>
  </div>';
}

function ThemeError() {
global $cmsurl, $l, $settings;
echo '
  <h3>', $l['themeerror_header'], '</h3>
  <p>', $l['themeerror_msg'], '</p>';
}
?>