<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           Error.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

function BNotAllowed() {
global $cmsurl, $l, $settings, $user;
  
  echo '
  <h1>'.$l['forum_error_header'].'</h1>
  
  <p>'.$l['forum_error_message'].'</p>
  ';
}

function CantViewB() {
global $cmsurl, $l, $settings, $user;
  
  echo '
  <h1>'.$l['forum_error_header'].'</h1>
  
  <p>'.$l['forum_error_cantviewb_message'].'</p>
  ';
}

function NoBoard() {
global $cmsurl, $l, $settings, $user;
  
  echo '
  <h1>'.$l['forum_error_header'].'</h1>
  
  <p>'.$l['forum_error_noboard_message'].'</p>
  ';
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