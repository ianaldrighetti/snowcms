<?php
// default/info.php by SnowCMS Dev's

global $user, $settings, $theme_name;

switch (clean($user['language'] ? $user['language'] : (@$_COOKIE['language'] ? @$_COOKIE['language'] : $settings['language']))) {
  case 'English': $theme_name = 'Default'; break;
  default:        $theme_name = 'Default';
}
?>
