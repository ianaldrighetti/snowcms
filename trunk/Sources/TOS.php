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
//                  TOS.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

function TOS() {
global $l, $settings, $db_prefix, $user;
  
  // Get the language record from either the user's profile, the cookies or the site's default
  $language = clean(@$_REQUEST['l'] ? $_REQUEST['l'] : ($user['language'] ? $user['language'] : (@$_COOKIE[$cookie_prefix.'change-language'] ? @$_COOKIE[$cookie_prefix.'change-language'] : $settings['language'])));
  
  // Get the TOS
  if (!($settings['page'] = mysql_fetch_assoc(sql_query("SELECT `body` FROM {$db_prefix}tos WHERE `tos_lang` = '$language'"))))
    $settings['page']['body'] = str_replace('%lang%',$language,$l['tos_notos']);
  
  $settings['page']['title'] = str_replace('%site%',$settings['site_name'],$l['tos_title']);
  loadTheme('TOS');
}

function ManageTOS() {
global $l, $settings, $db_prefix, $user, $language_dir;
  
  // Are they allowed to manage the TOS?
  if (can('manage_tos')) {
    // Convert post data into get data
    if (@$_POST['l'])
      redirect('index.php?action=admin;sa=tos;l='.clean_header($_POST['l']));
    
    // Are they enabling the TOS?
    if (@$_REQUEST['enable_tos']) {
      sql_query("UPDATE {$db_prefix}settings SET `value` = '1' WHERE `variable` = 'enable_tos'");
      redirect('index.php?action=admin;sa=tos');
    }
    // Are they disabling the TOS?
    elseif (@$_REQUEST['disable_tos']) {
      sql_query("UPDATE {$db_prefix}settings SET `value` = '0' WHERE `variable` = 'enable_tos'");
      redirect('index.php?action=admin;sa=tos');
    }
    // Are they updating a language's TOS?
    elseif (@$_REQUEST['body']) {
      $tos_lang = clean(@$_REQUEST['tos_lang']);
      $body = addslashes($_REQUEST['body']);
      sql_query("REPLACE INTO {$db_prefix}tos (`tos_lang`,`body`) VALUES ('$tos_lang','$body')");
      redirect('index.php?action=admin;sa=tos');
    }
    
    // Get the active language
    $settings['page']['language'] = clean(@$_REQUEST['tos_lang'] ? $_REQUEST['tos_lang'] : ($user['language'] ? $user['language'] : (@$_COOKIE[$cookie_prefix.'change-language'] ? @$_COOKIE[$cookie_prefix.'change-language'] : $settings['language'])));
    
    // Get all installed languages
    $settings['page']['languages']['English'] = 'English';
    
    // Get the amount of languages there are
    $languages = 0;
    foreach (scandir($language_dir) as $language)
      if (substr($language,0,1) != '.') {
        $languages += 1;
      }
    
    // Is there only one language?
    if ($languages == 1) {
      // Get the current TOS
      $settings['page']['tos'] = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}tos WHERE `tos_lang` = '{$settings['page']['language']}'"));
      $settings['page']['tos'] = $settings['page']['tos']['body'];
      
      // Load the theme
      $settings['page']['title'] = str_replace('%lang%',$settings['page']['language'],$l['tos_onelang_title']);
      loadTheme('TOS','OneLanguage');
    }
    // Are they veiwing the main TOS management page?
    elseif (!@$_REQUEST['l']) {
      // Load the theme
      $settings['page']['title'] = str_replace('%lang%',$settings['page']['language'],$l['tos_manage_title']);
      loadTheme('TOS','Manage');
    }
    // Are they changing a language's TOS?
    else {
      // Get the current TOS
      $settings['page']['tos'] = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}tos WHERE `tos_lang` = '{$settings['page']['language']}'"));
      $settings['page']['tos'] = $settings['page']['tos']['body'];
      
      // Load the theme
      $settings['page']['title'] = $l['tos_change_title'];
      loadTheme('TOS','Change');
    }
  }
  // They don't have permission to manage the TOS
  else
    redirect('index.php?action=admin');
}
?>