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
  // An array of all the settings that can be set on this page...
  $basic = array(
    'site_name',
    'slogan',
    'login_threshold',
    'remember_time',
    'timeformat'
  );
  // Are we updating them?
  if(!empty($_REQUEST['update'])) {
    // Set them all!
    foreach($basic as $setting) {
      $_REQUEST[$setting] = clean($_REQUEST[$setting]);
      $query = "UPDATE {$db_prefix}settings SET `value` = '{$_REQUEST[$setting]}' WHERE `variable` = '{$setting}' LIMIT 1";
      $result = mysql_query($query);
    }
    // Reload Settings D: Or they won't be the latest, menu's need to be reset too O.o
    $settings = loadSettings();
    $settings['menu'] = loadMenus();
  }
  // Set title, pass on $basic, and load Settings template with the Basic function
  $settings['page']['title'] = $l['basicsettings_title'];
  $settings['page']['settings'] = $basic;
  loadTheme('Settings','Basic');
}
?>