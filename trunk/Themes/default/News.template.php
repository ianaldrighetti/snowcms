<?php
// ManageForum.template.php by the SnowCMS Team
if(!defined('Snow'))
  die("Hacking Attempt..");

function Main() {
global $cmsurl, $db_prefix, $l, $settings, $user;

}

function None() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  echo '
  <h3>', $l['news_nonews_header'], '</h3>
  <p>', $l['news_nonews_desc'], '</p>';
}