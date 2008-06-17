<?php
//                 SnowCMS
//           By aldo and soren121
//  Founded by soren121 & co-founded by aldo
//    http://snowcms.northsalemcrew.net
//
// SnowCMS is released under the GPL v3 License
// Which means you are free to edit it and then
//       redistribute it as your wish!
// 
//            Settings.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function BasicSettings() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  $basic = array(
    'site_name',
    'slogan',
    'login_threshold',
    'remember_time',
    'timeformat'
  );
  if(!empty($_REQUEST['update'])) {
    $queries = array();
    foreach($basic as $setting) {
      $queries[] = "UPDATE {$db_prefix}settings SET `value` = '{$_REQUEST['site_name']}' WHERE `variable` = '{$setting}' LIMIT 1";
    }
    $query = implode(",", $queries);
    $result = mysql_query($query) or die(mysql_error());
    // Reload Settings D:
    $settings = loadSettings();
  }
  $settings['page']['title'] = $l['basicsettings_title'];
  loadTheme('Settings','BasicSettings');
}
?>