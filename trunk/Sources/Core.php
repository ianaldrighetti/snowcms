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
//              Core.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

function loadSettings() {
global $db_prefix;
  $result = mysql_query("SELECT * FROM {$db_prefix}settings");
    while($row = mysql_fetch_assoc($result)) 
      $settings[$row['variable']] = $row['value'];
  return $settings;
} 
 
function clean($str) {
  $str = str_replace('&', '&amp;', $str);
  $str = str_replace('"', '&quot;', $str);
  $str = str_replace("'", '&#39;', $str);
  $str = str_replace("<", "&lt;", $str);
  $str = str_replace(">", "&gt;", $str);
  return $str;
}

function loadUser() {
global $db_prefix;
  $user = array();
  // Set some default info, incase they are guests
  $user['id'] = 0;
  $user['group'] = 0;
  $user['is_logged'] = false;
  $user['is_guest'] = true;
  $user['name'] = null;
  $user['email'] = null;
  $user['ip'] = @$_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  if(isset($_SESSION['id'])) {
    if(isset($_SESSION['pass'])) {
      $result = mysql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '{$_SESSION['id']}' AND `password` = '{$_SESSION['pass']}'");
      if(mysql_num_rows($result)>0) {
        while($row = mysql_fetch_assoc($result)) {
          $user = array(
            'id' => $row['id'],
            'name' => $row['display_name'] ? $row['display_name'] : $row['username'],
            'group' => $row['group'],
            'is_logged' => true,
            'is_guest' => false,
            'email' => $row['email'],
            'ip' => @$_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
          );
        }
      }
    }
  }
  return $user;
}

function loadLanguage() {
global $cmsurl, $language_dir, $settings;
  $l = array();
  require_once($language_dir.'/'.$settings['language'].'.language.php');
  return $l;
}

function loadTheme($file, $function = 'Main') {
global $cmsurl, $l, $theme_dir, $settings;

  // Load up the templates!
  if(file_exists($theme_dir.'/'.$settings['theme'].'/Main.template.php'))
    require_once($theme_dir.'/'.$settings['theme'].'/Main.template.php');
  else
    require_once($theme_dir.'/default/Main.template.php');
  
  if(file_exists($theme_dir.'/'.$settings['theme'].'/'.$file.'.template.php'))
    require_once($theme_dir.'/'.$settings['theme'].'/'.$file.'.template.php');
  else
    require_once($theme_dir.'/default/'.$file.'.template.php');
    
  // Get the header of the template...
  theme_header();
  
  // Call on the function that is needed...
  $function();
  
  // Theme Footer
  theme_footer();
}

function loadPerms() {
global $db_prefix, $user;
  $perms = array();
  $perms[$user['group']] = array();
  $result = mysql_query("SELECT * FROM {$db_prefix}permissions");
    while($row = mysql_fetch_assoc($result)) {
      $perms[$row['group_id']][$row['what']] = $row['can'] ? true : false;
    }
  return $perms;
}

function loadMenus() {
global $db_prefix;
  $menu =array();
  $menu['main'] = array();
  $menu['side'] = array();
  $result = mysql_query("SELECT * FROM {$db_prefix}menus ORDER BY `order` ASC");
  if(mysql_num_rows($result)>0) {  
    while($row = mysql_fetch_assoc($result)) {
      if(($row['menu']==0) || ($row['menu']==2)) {
        // This one goes on the main menu...
        $menu['main'][] = array(
          'id' => $row['link_id'],
          'order' => $row['order'],
          'name' => $row['link_name'],
          'href' => $row['href'],
          'target' => $row['target'] ? 'target="_blank"' : '',
          'menu' => $row['menu']
        );
      }
      elseif(($row['menu']==1) || ($row['menu']==2)) {
        $menu['side'][] = array(
          'id' => $row['link_id'],
          'order' => $row['order'],
          'name' => $row['link_name'],
          'href' => $row['href'],
          'target' => $row['target'] ? 'target="_blank"' : '',
          'menu' => $row['menu']
        );        
      }
    }
  }
  return $menu;
}

function WriteOnline() {
global $db_prefix, $settings, $user;
  $threshold = time()+($settings['login_threshold']*60);
  mysql_query("DELETE FROM {$db_prefix}online WHERE `last_active` < '$threshold' OR `ip` = '{$user['ip']}'") or die(mysql_error());
  mysql_query("INSERT INTO {$db_prefix}online (`user_id`,`ip`,`page`,`last_active`) VALUES('{$user['id']}','{$user['ip']}','Nowhere','".time()."')");
}

function can($what) {
global $perms, $user;
  // This is a super simple Permission handler, simply, can they do the requested $what or not?
  // If it isn't set, we say false because we dont know ._.
  if(empty($perms[$user['group']][$what]))
    return false;
  elseif($perms[$user['group']][$what]) 
    return true;
  else
    return false;
}

function randStr($length) {
  $chars = "abcdefghijkmnopqrstuvwxyz023456789";
  srand((double)microtime()*1000000);
  $i = 0;
  $string = '' ;
  while ($i <= $length) {
    $num = rand() % 33;
    $tmp = substr($chars, $num, 1);
    $string = $string . $tmp;
    $i++;
  }
  return $string;
}
?>