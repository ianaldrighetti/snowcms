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
global $cmsurl, $db_prefix, $l, $settings, $user, $language_dir, $theme_dir, $theme_name;
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
      'language' =>
        array(
          'type' => 'select',
          'values' => array()
        ),
      'theme' =>
        array(
          'type' => 'select',
          'values' => array()
        ),
      'account_activation' =>
        array(
          'type' => 'select',
          'values' => array(
                        $l['basicsettings_value_no_activation'], 0,
                        $l['basicsettings_value_email_activation'], 1,
                        $l['basicsettings_value_admin_activation'], 2
                      )
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
      'num_search_results' =>
        array(
          'type' => 'text'
        ),
      'manage_members_per_page' =>
        array(
          'type' => 'text'
        ),
      'timeformat' =>
        array(
          'type' => 'text'
        ),
      'dateformat' =>
        array(
          'type' => 'text'
        )
    );
    // You can add options into the above array, with the name of the setting in the settings table...
    // Make sure you made a $l var for it and inserted a row for it in the table
    
    // Get the language values
    foreach (scandir($language_dir) as $language)
      if (substr($language,0,1) != '.') {
        $l_temp = $l;
        include $language_dir.'/'.$language;
        $basic['language']['values'][] = $l['language_name'];
        $l = $l_temp;
        $basic['language']['values'][] = strrev(substr(strrev($language),strlen(strstr($language,'.language.php')),strlen($language)));
      }
    
    // Get the theme values
    foreach (scandir($theme_dir) as $theme)
      if (substr($theme,0,1) != '.') {
        include $theme_dir.'/'.$theme.'/info.php';
        $basic['theme']['values'][] = $theme_name;
        $basic['theme']['values'][] = $theme;
      }
    
    // Are we updating them?
    if(!empty($_REQUEST['update'])) {
      // Set them all!
      foreach($basic as $key => $value) {
        $_REQUEST[$key] = clean($_REQUEST[$key]);
        $query = "UPDATE {$db_prefix}settings SET `value` = '{$_REQUEST[$key]}' WHERE `variable` = '{$key}' LIMIT 1";
        $result = sql_query($query);
      }
      // Reload Settings D: Or they won't be the latest, menu's need to be reset too O.o
      loadSettings();
      loadMenus();
      unset($menus);
      redirect('index.php?action=admin');
      
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
global $cmsurl, $db_prefix, $l, $settings;
  // Do they have permission?
  if(can('manage_mail_settings')) {
    // Are they changing settings?
    if (@$_REQUEST['change_settings']) {
      
      $mail_with_fsockopen = clean(@$_REQUEST['mail_with_fsockopen']);
      $smtp_host = clean(@$_REQUEST['smtp_host']);
      $smtp_port = clean(@$_REQUEST['smtp_port']);
      $smtp_user = clean(@$_REQUEST['smtp_user']);
      $smtp_pass = clean(@$_REQUEST['smtp_pass']);
      $smtp_pass_2 = clean(@$_REQUEST['smtp_pass_2']);
      $from_email = clean(@$_REQUEST['from_email']);
      
      // Are they changing the password?
      if ($smtp_pass) {
        // Is the verification password correct?
        if ($smtp_pass == $smtp_pass_2)
          // It is
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$smtp_pass' WHERE `variable` = 'smtp_pass'") or $_SESSION['error'] = $l['mailsettings_error'];
        else
          // It isn't
          $_SESSION['error'] = $l['mailsettings_error_verification'];
      }
      
      // Do this only if there hasn't been an error yet
      if (!@$_SESSION['error']) {
        if ($mail_with_fsockopen) {
          // Change all the settings
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$mail_with_fsockopen' WHERE `variable` = 'mail_with_fsockopen'") or $_SESSION['error'] = $l['mailsettings_error'];
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$smtp_host' WHERE `variable` = 'smtp_host'") or $_SESSION['error'] = $l['mailsettings_error'];
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$smtp_port' WHERE `variable` = 'smtp_port'") or $_SESSION['error'] = $l['mailsettings_error'];
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$smtp_user' WHERE `variable` = 'smtp_user'") or $_SESSION['error'] = $l['mailsettings_error'];
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$from_email' WHERE `variable` = 'from_email'") or $_SESSION['error'] = $l['mailsettings_error'];
        }
        else {
          // Change only the non-fsockopen related settings
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$mail_with_fsockopen' WHERE `variable` = 'mail_with_fsockopen'") or $_SESSION['error'] = $l['mailsettings_error'];
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$from_email' WHERE `variable` = 'from_email'") or $_SESSION['error'] = $l['mailsettings_error'];
        }
      }
      
      // Redirect the page to make it so if they refresh the settings aren't updated again
      redirect('index.php?action=admin;sa=mail-settings');
    }
    // Set the page title and load the theme
    $settings['page']['title'] = $l['mailsettings_title'];
    loadTheme('Settings','ManageMailSettings');
  }
}
?>