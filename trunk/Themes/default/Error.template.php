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
?>