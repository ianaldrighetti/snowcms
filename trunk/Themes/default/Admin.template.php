<?php
// default/Admin.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <div style="overflow: auto; width: 503px; height: 100px;">
    '.$settings['page']['news'].'
  </div>';
}