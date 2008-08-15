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
  if(can('manage_basic-settings')) {  
    // An array of all the settings that can be set on this page...
    $basic = array(
      'site_name' => 
        array(
          'type' => 'text'
         ),
      'slogan' =>
        array(
          'type' => 'text'
        ),
      'login_threshold' =>
        array(
          'type' => 'text'
        ),
      'remember_time' =>
        array(
          'type' => 'text'
        ),
      'num_news_items' =>
        array(
          'type' => 'text'
        ),
      'timeformat' =>
        array(
          'type' => 'text'
        )
    );
    // Are we updating them?
    if(!empty($_REQUEST['update'])) {
      // Set them all!
      foreach($basic as $setting) {
        $_REQUEST[$setting] = clean($_REQUEST[$setting]);
        $query = "UPDATE {$db_prefix}settings SET `value` = '{$_REQUEST[$setting]}' WHERE `variable` = '{$setting}' LIMIT 1";
        $result = sql_query($query);
      }
      // Reload Settings D: Or they won't be the latest, menu's need to be reset too O.o
      $settings += loadSettings();
    }
    // Set title, pass on $basic, and load Settings template with the Basic function
    $settings['page']['title'] = $l['basicsettings_title'];
    $settings['page']['settings'] = $basic;
    loadTheme('Settings','Basic');
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}

function MailSettings() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('manage_mail_settings')) {
  
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  } 
}
?>