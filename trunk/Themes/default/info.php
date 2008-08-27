<?php
// default/info.php by SnowCMS Dev's

global $user, $settings, $theme_name,$cookie_prefix;

switch (clean($user['language'] ? $user['language'] : (@$_COOKIE[$cookie_prefix.'language'] ? @$_COOKIE[$cookie_prefix.'language'] : $settings['language']))) {
  case 'English': $theme_name = 'Default'; break;
  default:        $theme_name = 'Default';
}
?>
