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

// This loads all the settings in the {db_prefix}settings table, also loads usernames into the settings array
function loadSettings() {
global $db_prefix;
  $result = mysql_query("SELECT * FROM {$db_prefix}settings");
    while($row = mysql_fetch_assoc($result)) 
      $settings[$row['variable']] = $row['value'];
  $result = mysql_query("SELECT `id`,`username`,`display_name` FROM {$db_prefix}members");
    while($row = mysql_fetch_assoc($result)) 
      $settings['users'][$row['id']] = $row['display_name'] ? $row['display_name'] : $row['username'];
  return $settings;
} 

// Instead of using addslashes and or mysql_real_escape_string, we use this mostly to sanitize
// It makes it so later on, once we have a forum in, you can post PHP and HTML stuff, but it
// Won't be parsed, but keep the site safe from SQL Injections 
function clean($str) {
  $str = str_replace('&', '&amp;', $str);
  $str = str_replace('"', '&quot;', $str);
  $str = str_replace("'", '&#39;', $str);
  $str = str_replace("<", "&lt;", $str);
  $str = str_replace(">", "&gt;", $str);
  return $str;
}

// Loads up the $user array, such as their member group, IP, email, if they are logged in or not
// It will also revive their session if they left, but had remember me on
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
  // Make sure we get their real IP :)
  $user['ip'] = @$_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  if(empty($_SESSION['id'])) {
    // We need to sanitize the cookies, last thing we need is to be hacked by cookies, Those are some bad cookies (Like Oatmeal ones, Ewww!)
    $_SESSION['id'] = @addslashes(mysql_real_escape_string($_COOKIE['uid']));
    $_SESSION['user'] = @clean($_COOKIE['username']);
    $_SESSION['pass'] = @clean($_COOKIE['password']);
  }
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

// This loads the Language file, from the language directory, later on (Maybe SnowCMS 0.8 or later)
// Will allow each theme to have its own language file(s)
function loadLanguage() {
global $cmsurl, $language_dir, $settings;
  $l = array();
  require_once($language_dir.'/'.$settings['language'].'.language.php');
  return $l;
}

// This function loads the Theme file requested in a Source File, More comment inside :P
function loadTheme($file, $function = 'Main') {
global $cmsurl, $l, $theme_dir, $settings;

  // Does this theme even have its own Main.template.php file? ._.
  // Why did they make it if they don't have a Main.template.php file!!! (Which has like the <html><body> stuff :P)
  if(file_exists($theme_dir.'/'.$settings['theme'].'/Main.template.php'))
    require_once($theme_dir.'/'.$settings['theme'].'/Main.template.php');
  else
    require_once($theme_dir.'/default/Main.template.php');
  
  // Do they have the $FILE (Template) in this theme? If not, fall back on the default one :)
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

// Loads up the permissions into an array, so we can know what you can and can't do :)
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

// Load all the menus, both the Sidebar menu (if their is one) and the Main one (If their is one :P)
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
        // And this little piggy goes on the sidebar menu
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

// Writes the user or guest online, also deletes old ones expired guests/users
function WriteOnline() {
global $db_prefix, $settings, $user;
  // Are they at a ?action= page? If so, thats where we need to save them as
  // Or are they  on a Page? save its Page ID
  // Nothing? D:!
  if(isset($_REQUEST['action']))
    $action_or_page = 'action:'.clean($_REQUEST['action']);
  elseif(isset($_REQUEST['page']))
    $action_or_page = 'page:'.clean($_REQUEST['page']);
  else 
    $action_or_page = 0;
  // Get those peeps online
  $result = mysql_query("SELECT * FROM {$db_prefix}online") or die(mysql_error());
    while($row = mysql_fetch_assoc($result)) {
      // Delete this row if it is them
      // Or if this is an expired row, delete it too
      if($row['ip']==$user['ip'])
        mysql_query("DELETE FROM {$db_prefix}online WHERE `ip` = '{$row['ip']}'");
      elseif(($row['last_active']+($settings['login_threshold']*60))<time()) {
        mysql_query("DELETE FROM {$db_prefix}online WHERE `ip` = '{$row['ip']}'");
      }
    }
  // Insert their information into the database
  mysql_query("INSERT INTO {$db_prefix}online (`user_id`,`ip`,`page`,`last_active`) VALUES('{$user['id']}','{$user['ip']}','{$action_or_page}','".time()."')");
}

// This returns true or false (bool) of whether or not they can do said function
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

// Creates a random string, doesn't have a use... Yet =D
function randStr($length = 6) {
  $chars = "abcdefghijkmnopqrstuvwxyz023456789";
  srand((double)microtime()*1000000);
  $i = 0;
  $string = '' ;
  while ($i <= ($length-1)) {
    $num = rand() % 33;
    $tmp = substr($chars, $num, 1);
    $string = $string . $tmp;
    $i++;
  }
  return $string;
}

// Formats the time with the time format in settings If timestamp is unset, get the current time
function formattime($timestamp = 0) {
global $settings;
  if(!$timestamp)
    $timestamp = time();
  return date($settings['timeformat'], $timestamp);
}
?>