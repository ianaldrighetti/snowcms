<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                 Settings.php file


if(!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

function Settings() {
  if(@$_REQUEST['redirect'] == 'settings')
    redirect('index.php?action=admin;sa=settings');
  
  if(can('settings')) {
    if(!empty($_REQUEST['ssa'])) {
      $ssa = array(
        'basic' => 'BasicSettings',
        'mail' => 'MailSettings',
        'lengths' => 'FieldLengthSettings'
      );
      if(@$ssa[$_REQUEST['ssa']])
        $ssa[$_REQUEST['ssa']]();
      else
        SettingsHome();
    }
    else
      SettingsHome();
  }
}

function SettingsHome() {
global $l, $settings;
  
  // Get the control panel menu options
  $options = array();
  if (can('manage_pages'))
    $options[] = 'basic';
  if (can('manage_basic-settings'))
    $options[] = 'mail';
  if (can('manage_members'))
    $options[] = 'lengths';
  
  $settings['page']['options'] = $options;
  
  $settings['page']['title'] = $l['settings_title'];
  loadTheme('Settings');
}

function BasicSettings() {
global $cmsurl, $db_prefix, $l, $settings, $user, $language_dir, $theme_dir;
  
  if (can('manage_basic-settings')) {
    // An array of all the settings that can be set on this page...
    //  A  = text of any length
    // 3A  = at least three characters
    //  A5 = five or less characters
    // 2A7 = two to seven characters
    //  1  = any number
    // >3 = number higher than three
    // <5  = number lower than five
    // 5-8 = number between five and eight
    $basic = array(
      'site_name' => 
        array(
          'type' => 'text',
          'validation' => '1A'
         ),
      'slogan' =>
        array(
          'type' => 'text',
          'validation' => 'A'
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
      'page_type' =>
        array(
          'type' => 'select',
          'values' => array(
                        $l['settings_basic_value_html'], 1,
                        $l['settings_basic_value_bbcode'], 0
                      )
        ),
      'account_activation' =>
        array(
          'type' => 'select',
          'values' => array(
                        $l['settings_basic_value_no_activation'], 0,
                        $l['settings_basic_value_email_activation'], 1,
                        $l['settings_basic_value_admin_activation'], 2
                      )
        ),
      'captcha' =>
        array(
          'type' => 'select',
          'values' => array(
                        $l['settings_basic_value_none'], 0,
                        $l['settings_basic_value_nofonts_weak'], 1,
                        $l['settings_basic_value_nofonts_medium'], 2,
                        $l['settings_basic_value_nofonts_strong'], 3,
                        $l['settings_basic_value_weak'], 4,
                        $l['settings_basic_value_medium'], 5,
                        $l['settings_basic_value_strong'], 6,
                        $l['settings_basic_value_superstrong'], 7
                      )
        ),
      'login_threshold' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'remember_time' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'num_news_items' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'num_search_results' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'num_pages' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'num_members' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'num_pms' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'num_topics' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'num_posts' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'hot_posts' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'timeformat' =>
        array(
          'type' => 'text',
          'validation' => 'A'
        ),
      'dateformat' =>
        array(
          'type' => 'text',
          'validation' => 'A'
        ),
      'dateshort' =>
        array(
          'type' => 'text',
          'validation' => 'A'
        ),
      'avatar_size' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'avatar_width' =>
        array(
          'type' => 'text',
          'validation' => '>0'
        ),
      'avatar_height' =>
        array(
          'type' => 'text',
          'validation' => '>0'
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
        $basic['theme']['values'][] = $settings['theme_name'];
        $basic['theme']['values'][] = $theme;
      }
    
    // Are we updating them?
    if(!empty($_REQUEST['update'])) {
      // Check the validation for each on individually
      foreach($basic as $key => $value)
        if (!@$_SESSION['error']) {
          $setting = clean(@$_REQUEST[$key]);
          // If it's suppose to be a number, make sure it is
          if (preg_match('/^[<>]?-?[0-9]+$/',@$value['validation']) || preg_match('/^-?[0-9]+--?[0-9]+$/',@$value['validation']))
            $setting = (int)$setting;
          // Check validation for listboxes
          if (@$value['values']) {
            $i = 0;
            $valid = false;
            while ($i < count($value['values'])) {
              if ($setting == $value['values'][$i+1])
                $valid = true;
              $i += 2;
            }
            if (!$valid)
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_invalid'];
          }
          // Check validation for 'at least # characters'
          elseif (preg_match('/^[0-9]+A$/i',$value['validation'])) {
            if (strlen($setting) < preg_replace('/^([0-9]+)A$/i','$1',$value['validation']))
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_short'];
          }
          // Check validation for '# or less characters'
          elseif (preg_match('/^A[0-9]+$/i',$value['validation'])) {
            if (strlen($setting) > preg_replace('/^A([0-9]+)$/i','$1',$value['validation']))
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_long'];
          }
          // Check validation for '# to # characters'
          elseif (preg_match('/^[0-9]+A[0-9]+$/i',$value['validation'])) {
            if (strlen($setting) > preg_replace('/^[0-9]+A([0-9]+)$/i','$1',$value['validation']))
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_short'];
            if (strlen($setting) < preg_replace('/^([0-9]+)A[0-9]+$/i','$1',$value['validation']))
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_long'];
          }
          // Check validation for 'number higher than #'
          elseif (preg_match('/^>-?[0-9]+$/',$value['validation'])) {
            if ($setting <= preg_replace('/^>(-?[0-9]+)$/','$1',$value['validation']))
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_low'];
          }
          // Check validation for 'number lower than #'
          elseif (preg_match('/^<-?[0-9]+$/',$value['validation'])) {
            if ($setting >= preg_replace('/^<(-?[0-9]+)$/','$1',$value['validation']))
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_high'];
          }
          // Check validation for 'number between # and #'
          elseif (preg_match('/^-?[0-9]+--?[0-9]+$/',$value['validation'])) {
            if ($setting <= preg_replace('/^(-?[0-9]+)--?[0-9]+$/','$1',$value['validation']))
            if ($setting >= preg_replace('/^-?[0-9]+-(-?[0-9]+)$/','$1',$value['validation']))
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_low'];
              $_SESSION['error'] = $l['basicsettings_error_'.$key.'_high'];
          }
        }
      // Was there an error in validation?
      if (!@$_SESSION['error']) {
        // There wasn't, so set them all!
        foreach($basic as $key => $value) {
          $setting = clean(@$_REQUEST[$key]);
          // Set setting
          $query = "UPDATE {$db_prefix}settings SET `value` = '$setting' WHERE `variable` = '$key'";
          $result = sql_query($query);
        }
        redirect('index.php?action=admin;sa=settings');
      }
      // There was an error in validation
      else
        redirect('index.php?action=admin;sa=settings;ssa=basic');
    }
    // Set title, pass on $basic, and load Settings template with the Basic function
    $settings['page']['title'] = $l['settings_basic_title'];
    $settings['page']['settings'] = $basic;
    loadTheme('Settings','Basic');
  }
  // They don't have permission to change basic settings
  else
    redirect('index.php?action=admin;sa=settings');
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
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$smtp_pass' WHERE `variable` = 'smtp_pass'");
        else
          // It isn't
          $_SESSION['error'] = $l['mailsettings_error_verification'];
      }
      
      // Do this only if there hasn't been an error yet
      if (!@$_SESSION['error']) {
        if ($mail_with_fsockopen) {
          // Change all the settings
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$mail_with_fsockopen' WHERE `variable` = 'mail_with_fsockopen'");
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$smtp_host' WHERE `variable` = 'smtp_host'");
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$smtp_port' WHERE `variable` = 'smtp_port'");
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$smtp_user' WHERE `variable` = 'smtp_user'");
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$from_email' WHERE `variable` = 'from_email'");
        }
        else {
          // Change only the non-fsockopen related settings
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$mail_with_fsockopen' WHERE `variable` = 'mail_with_fsockopen'");
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$from_email' WHERE `variable` = 'from_email'");
        }
      }
      
      // Redirect the page to make it so if they refresh the settings aren't updated again
      redirect('index.php?action=admin;sa=settings;ssa=mail');
    }
    // Set the page title and load the theme
    $settings['page']['title'] = $l['settings_mail_title'];
    loadTheme('Settings','ManageMailSettings');
  }
  // They don't have permission, so redrect them to the main control panel
  else
    redirect('index.php?action=admin;sa=settings');
}

function FieldLengthSettings() {
global $cmsurl, $db_prefix, $l, $settings, $user, $language_dir, $theme_dir;
  
  if (can('manage_basic-settings')) {
    $lengths = array(
      'username',
      'display_name',
      'password',
      'email',
      'avatar',
      'icq',
      'aim',
      'msn',
      'yim',
      'gtalk',
      'site',
      'site_url',
      'signature',
      'profile',
      'post_subject',
      'post',
      'pm_subject',
      'pm',
      'page_title',
      'page',
      'menu',
      'menu_url',
      'tos',
      'ip',
      'news_cat',
      'news_subject',
      'news',
      'news_comment',
      'group',
      'board_cat',
      'board',
      'board_desc'
    );
    
    // You can add options into the above array, with the name of the setting (Minus short/long) in the settings table
    // Make sure you made a $l var for it and inserted a row for it in the table (e.g. ip needs ip_short and ip_long in the table)
    
    // Are we updating them?
    if(!empty($_REQUEST['update'])) {
      // There wasn't, so set them all!
      foreach($lengths as $length) {
        $length = clean($length);
        $short = (int)@$_REQUEST[$length.'_short'];
        $long = (int)@$_REQUEST[$length.'_long'];
        // Set settings
        sql_query("REPLACE INTO {$db_prefix}settings (`variable`,`value`) VALUES('{$length}_short','$short'),('{$length}_long','$long')");
      }
      redirect('index.php?action=admin;sa=settings');
    }
    // Set title, pass on $basic, and load Settings template with the Basic function
    $settings['page']['title'] = $l['settings_lengths_title'];
    $settings['page']['settings'] = $lengths;
    loadTheme('Settings','FieldLengths');
  }
  // They don't have permission to change basic settings
  else
    redirect('index.php?action=admin;sa=settings');
}
?>