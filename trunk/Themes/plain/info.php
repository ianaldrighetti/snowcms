<?php
//              Plain Theme
// By The SnowCMS Team (www.snowcms.com)
//               info.php

global $user, $settings, $theme_name, $cookie_prefix;

switch (clean($user['language'] ? $user['language']
                                : (@$_COOKIE[$cookie_prefix.'language']
                                ? @$_COOKIE[$cookie_prefix.'language']
                                : $settings['language']))) {
  case 'English': $theme_name = 'Plain'; break;
  default:        $theme_name = 'Plain';
}
?>
