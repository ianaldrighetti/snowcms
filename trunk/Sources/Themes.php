<?php
// SnowCMS
// By soren121, aldo and Myles Grey
// http://www.snowcms.com/

function ManageThemes() {
global $l, $settings, $db_prefix, $language_dir, $user;
  
  if (@$_POST['install-language']) {
    $install_language = clean($_POST['install-language']);
    if (file_exists($language_dir.'/'.$install_language.'.language.php')) {
      if (!mysql_num_rows(mysql_query("SELECT * FROM {$db_prefix}languages WHERE `lang_name` = '$install_language'")))
        sql_query("INSERT INTO {$db_prefix}languages (`lang_name`) VALUES ('$install_language')");
      else
        $settings['error'] = str_replace('%language%',$install_language,$l['managethemes_language_already_installed']);
    }
    else
     $settings['error'] = str_replace('%language%',$install_language,$l['managethemes_language_file_missing']);
  }
  else if (@$_POST['delete-language']) {
    $delete_language = clean($_POST['delete-language']);
    
    if (mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}languages")) > 1) {
      $current_language = clean($user['language'] ? $user['language'] : (@$_COOKIE['language'] ? @$_COOKIE['language'] : $settings['language']));
      if ($delete_language != $current_language)
        if ($delete_language != $settings['language'])
          sql_query("DELETE FROM {$db_prefix}languages WHERE `lang_id` = '$delete_language'");
        else
          $settings['error'] = $l['managethemes_language_delete_default'];
      else
        $settings['error'] = $l['managethemes_language_delete_your'];
    }
    else
      $settings['error'] = $l['managethemes_language_delete_last'];
  }
  else if (@$_POST['default-language']) {
    $default_language = clean($_POST['default-language']);
    sql_query("UPDATE {$db_prefix}settings SET `value` = '$default_language' WHERE `variable` = 'language'");
    $settings['language'] = $default_language;
  }
  
  $settings['page']['title'] = $l['managethemes_title'];
  loadTheme('ManageThemes');
}

?>