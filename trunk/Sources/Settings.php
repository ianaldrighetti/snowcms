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


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));
  
function BasicSettings() {
global $cmsurl, $db_prefix, $l, $settings, $user, $language_dir, $theme_dir, $theme_name;
  
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
                        $l['basicsettings_value_html'], 1,
                        $l['basicsettings_value_bbcode'], 0
                      )
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
          $query = "UPDATE {$db_prefix}settings SET `value` = '$setting' WHERE `variable` = '{$key}' LIMIT 1";
          $result = sql_query($query);
        }
        redirect('index.php?action=admin');
      }
      // There was an error in validation
      else
        redirect('index.php?action=admin;sa=basic-settings');
    }
    // Set title, pass on $basic, and load Settings template with the Basic function
    $settings['page']['title'] = $l['basicsettings_title'];
    $settings['page']['settings'] = $basic;
    loadTheme('Settings','Basic');
  }
  // They don't have permission to change basic settings
  else
    redirect('index.php?action=admin');
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
  // They don't have permission, so redrect them to the main control panel
  else
    redirect('index.php?action=admin');
}
?>