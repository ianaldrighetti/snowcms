<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           Error.template.php

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
  <h1>'.$l['themeerror_header'].'</h1>
  
  <p>'.$l['themeerror_msg'].'</p>
  ';
}

function LanguageError() {
global $cmsurl, $l, $settings;
  
  echo '
  <h1>Language Error</h1>
  
  <p>The language files failed to load. If you see this message again, contact a site administrator.</p>
  ';
}
?>