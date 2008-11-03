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
//                   Core.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

// This loads all the settings in the {db_prefix}settings table, also loads usernames into the settings array
function loadSettings() {
global $db_prefix, $settings;
  // Get all the settings, cause we need them!
  $result = sql_query("SELECT * FROM {$db_prefix}settings");
    while($row = mysql_fetch_assoc($result)) 
      $settings[$row['variable']] = $row['value'];
} 

// Instead of using addslashes and or mysql_real_escape_string, we use this mostly to sanitize
// It makes it so later on, once we have a forum in, you can post PHP and HTML stuff, but it
// Won't be parsed, but keep the site safe from SQL injections 
function clean($str) {
  // Then ENT_QUOTES make it encode both " and '
  $str = htmlspecialchars($str, ENT_QUOTES);
  return $str;
}

// And a version for use instead HTTP headers
function clean_header($str) {
  $replace = array(
    ':' => '&#58;',
    '\n' => ' ',
    '\r' => ' '
  );
  return str_replace(array_keys($replace), array_values($replace), $str);
}

// Loads up the $user array, such as their member group, IP, email, if they are logged in or not
// It will also revive their session if they left, but had remember me on
function loadUser() {
global $db_prefix, $user, $cookie_prefix;
  
  // Set some default info, incase they are guests
  $user = array(
            'id' => 0,
            'ip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
            'group' => -1,
            'is_guest' => true,
            'is_logged' => false,
            'is_admin' => false,
            'name' => '',
            'email' => '',
            'language' => false,
            'sc' => session_id(),
            'board_query' => 'FIND_IN_SET(\'-1\', b.who_view)',
            'unread_pms' => 0
          );
  if(empty($_SESSION['id'])) {
    // We need to sanitize the cookies, last thing we need is to be hacked by cookies, Those are some bad cookies (Like Oatmeal ones, Ewww!)
    $_SESSION['id'] = !empty($_COOKIE[$cookie_prefix. 'user_id']) ? (int)clean($_COOKIE[$cookie_prefix. 'user_id']) : '';
    $_SESSION['password'] = !empty($_COOKIE[$cookie_prefix. 'password']) ? clean($_COOKIE[$cookie_prefix. 'password']) : '';
  }
  // So are they possibly logged in?
  // Also... make sure the Session data
  // Matches that of the Cookie Data
  // Just an extra precaution :P
  if(!empty($_SESSION['id']) && !empty($_SESSION['password']) && $_SESSION['id'] == $_COOKIE[$cookie_prefix. 'user_id'] && $_SESSION['password'] == $_COOKIE[$cookie_prefix. 'password']) {
    $result = sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '{$_SESSION['id']}' AND `password` = '{$_SESSION['password']}'");
    // If user ID and password are in the database
    if(mysql_num_rows($result)) {
      while($row = mysql_fetch_assoc($result)) {
        $user = array(
          'id' => $row['id'],
          'name' => $row['display_name'],
          'username' => $row['username'],
          'group' => $row['group'],
          'is_logged' => true,
          'is_guest' => false,
          'is_admin' => ($row['group'] == 1) ? true : false,
          'email' => $row['email'],
          'language' => $row['language'],
          'board_query' => ($row['group'] == 1) ? '1=1' : "FIND_IN_SET('{$row['group']}', b.who_view)",
          'unread_pms' => $row['unread_pms'],
          'ip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
          'sc' => create_sid()
        );
      }
    }
    else {
      // Delete the cookie contents >:D!
      setLoginCookie('', '', time() - (60*60*24*365));
    }
  }
  // Check session validation
  if(!ValidateSession($user['sc']) && $user['group'] != -1 && @$_REQUEST['action'] != 'logout') {
    $result = sql_query("SELECT `sc` FROM {$db_prefix}members WHERE `id` = '{$user['id']}'");
    $row = mysql_fetch_assoc($result);
    redirect('index.php?action=logout;sc='.$row['sc']);
  }
}

// This function makes it all easy =D
// This sets the User ID, Password, and Session ID
// Of the user for later use... if they choose to
// Stay logged in
function setLoginCookie($user_id = '', $password = '', $expire = 0) {
global $cookie_prefix, $cmsurl;
  // Figure the Cookie Path and Cookie Domain
  $url = parse_url($cmsurl);
  if(empty($url['host'])) {
    // Uh oh! no host O.o..?
    $url['host'] = (substr($_SERVER['SERVER_NAME'], 0, 7) == 'http://') ? substr($_SERVER['SERVER_NAME'], 7, strlen($_SERVER['SERVER_NAME'])) : $_SERVER['SERVER_NAME'];
  }
  // Cookie path :D
  if(empty($url['path'])) {
    // None, forget it... just set it to /
    $url['path'] = '/';
  }
  setcookie($cookie_prefix. 'user_id', $user_id, $expire, $url['path']);
  setcookie($cookie_prefix. 'password', $password, $expire, $url['path']);
}

// This loads the Language file, from the language directory, later on (Maybe SnowCMS 0.8 or later)
// Will allow each theme to have its own language file(s)
function loadLanguage() {
global $cmsurl, $l, $language_dir, $settings, $theme_dir, $user, $db_prefix, $cookie_prefix;
  
  // Get the member's selected language from either their profile or cookies
  $language = clean($user['language'] ? $user['language'] : (@$_COOKIE[$cookie_prefix.'change-language'] ? @$_COOKIE[$cookie_prefix.'change-language'] : ''));
  
  $l = array();
  // Load English
  if (file_exists($language_dir.'/English.language.php'))
    include_once($language_dir.'/English.language.php');
  // Load the theme specific English
  if (file_exists($theme_dir.'/'.$settings['theme'].'/languages/English.language.php'))
    include_once($theme_dir.'/'.$settings['theme'].'/languages/English.language.php');
  // Load the site default language
  if (file_exists($language_dir.'/'.$settings['language'].'.language.php'))
    include_once($language_dir.'/'.$settings['language'].'.language.php');
  // Load the theme specific site default language
  if (file_exists($theme_dir.'/'.$settings['theme'].'/languages/'.$settings['language'].'.language.php'))
    include_once($theme_dir.'/'.$settings['theme'].'/languages/'.$settings['language'].'.language.php');
  // Load the member's selected language
  if (file_exists($language_dir.'/'.$language.'.language.php'))
    include_once($language_dir.'/'.$language.'.language.php');
  // Load the theme specific member's selected language
  if (file_exists($theme_dir.'/'.$settings['theme'].'/languages/'.$language.'.language.php'))
    include_once($theme_dir.'/'.$settings['theme'].'/languages/'.$language.'.language.php');
  
  // Check if any of them loaded
  if ($l == array()) {
    $settings['page']['title'] = 'Language Error';
    $l['main_powered_by'] = 'Powered by %snowcms%';
    $l['main_theme_by'] = 'Theme by %whom%';
    loadTheme('Error','LanguageError');
    exit;
  }
}

// This function loads the Theme file requested in a Source File, More comment inside :P
function loadTheme($file, $function = 'Main') {
global $cmsurl, $l, $theme_dir, $settings;
  
  // We have no Loading Error, yet...
  $loadError = false;
  
  // Does this theme even have its own Main.template.php file? ._.
  // Why did they make it if they don't have a Main.template.php file!!! (Which has like the <html><body> stuff :P)
  if(file_exists($theme_dir.'/'.$settings['theme'].'/Main.template.php'))
    require_once($theme_dir.'/'.$settings['theme'].'/Main.template.php');
  else
    require_once($theme_dir.'/default/Main.template.php');
  
  // Do they have the $FILE (Template) in this theme? If not, fall back on the default one :)
  if(file_exists($theme_dir.'/'.$settings['theme'].'/'.$file.'.template.php'))
    require_once($theme_dir.'/'.$settings['theme'].'/'.$file.'.template.php');
  elseif(file_exists($theme_dir.'/default/'.$file.'.template.php'))
    require_once($theme_dir.'/default/'.$file.'.template.php');
  else {
    $loadError = true;
    $settings['page']['title'] = $l['themeerror_title'];
    $replace = array(
      '%func%' => $function,
      '%file%' => $file
    );
    $l['themeerror_msg'] = str_replace(array_keys($replace), array_values($replace), $l['themeerror_msg']);
    require_once($theme_dir.'/default/Error.template.php');
    $function = 'ThemeError';
  } 
  // Get the header of the template...
  theme_header();
  
  // Call on the function that is needed...
    $function();
  
  // Theme Footer
  theme_footer();
}

// This function is almost EXACTLY (Copied and Pasted tbh) like the loadTheme() function
// But it is to load up the Forum.template.php as the main parts
function loadForum($file, $function = 'Main') {
global $cmsurl, $l, $theme_dir, $settings;

  // Does this theme even have its own Main.template.php file? ._.
  // Why did they make it if they don't have a Main.template.php file!!! (Which has like the <html><body> stuff :P)
  if(file_exists($theme_dir.'/'.$settings['theme'].'/Forum.template.php'))
    require_once($theme_dir.'/'.$settings['theme'].'/Forum.template.php');
  else
    require_once($theme_dir.'/default/Forum.template.php');
  
  // Do they have the $FILE (Template) in this theme? If not, fall back on the default one :)
  if(file_exists($theme_dir.'/'.$settings['theme'].'/'.$file.'.template.php'))
    require_once($theme_dir.'/'.$settings['theme'].'/'.$file.'.template.php');
  else
    require_once($theme_dir.'/default/'.$file.'.template.php');
    
  // Get the header of the forum...
  forum_header();
  
  // Call on the function that is needed...
  $function();
  
  // Forum Footer
  forum_footer();
}

// Loads up the permissions into an array, so we can know what you can and can't do :)
function loadPerms() {
global $db_prefix, $perms, $user, $forumperms;
  // Set an array, so we don't get any possible
  // PHP Notices later
  $perms = array();
  // Set the current users group as an array too, just incase
  $perms[$user['group']] = array();
  // Select them!
  $result = sql_query("SELECT * FROM {$db_prefix}permissions");
  while($row = mysql_fetch_assoc($result)) {
    $perms[$row['group_id']][$row['what']] = $row['can'] ? true : false;
  }
}

// Loads up the permissions, except this is for the forum permissions,
// So we can make sure you are allowed to edit/delete/move/etc
function loadBPerms() {
global $bperms, $db_prefix, $user, $forumperms;
  // Set an array
  $bperms = array();
  // Set their group as an array too
  $bperms[$user['group']] = array();
  // Load 'em up!
  $result = sql_query("SELECT * FROM {$db_prefix}board_permissions");
  // Loop through them, and save them to the array
  while($row = mysql_fetch_assoc($result)) {
    $bperms[$row['group_id']][$row['bid']][$row['what']] = $row['can'] ? true : false;
  }
}

// Load all the menus, both the sidebar menu (if there is one) and the main one (If there is one :P)
function loadMenus() {
global $db_prefix, $settings, $user;
  // Setup a couple variables as an array...
  $menu = array();
  $menu['main'] = array();
  $menu['side'] = array();
  $result = sql_query("SELECT * FROM {$db_prefix}menus ORDER BY `order` ASC");
  // No point of continuing if no links...
  if(mysql_num_rows($result)) {  
    while ($row = mysql_fetch_assoc($result)) {
      // Who can view it?
      if ($row['permission'] == 1 || // Everyone
         ($row['permission'] == 2 && $user['is_guest']) || // Guests only
         ($row['permission'] == 3 && !$user['is_guest']) || // Members only
         ($row['permission'] == 4 && can('admin')) || // Admin only
         ($row['permission'] == 5 && !$user['is_guest'] && !$user['unread_pms']) || // No new messages
         ($row['permission'] == 6 && !$user['is_guest'] && $user['unread_pms'])) { // New messages
        if($row['menu'] == 1 || $row['menu'] == 3) {
          // This one goes on the main menu...
          $menu['main'][] = array(
            'id' => $row['link_id'],
            'order' => $row['order'],
            'name' => str_replace('%unread_pms%',$user['unread_pms'],$row['link_name']),
            'href' => str_replace('%sc%',$user['sc'],$row['href']),
            'target' => $row['target'] ? 'target="_blank"' : '',
            'menu' => $row['menu']
          );
        }
        if($row['menu'] == 2 || $row['menu'] == 3) {
          // And this little piggy goes on the sidebar menu
          $menu['side'][] = array(
            'id' => $row['link_id'],
            'order' => $row['order'],
            'name' => str_replace('%unread_pms%', $user['unread_pms'], $row['link_name']),
            'href' => str_replace('%sc%', $user['sc'], $row['href']),
            'target' => $row['target'] ? 'target="_blank"' : '',
            'menu' => $row['menu']
          );        
        }
      }
    }
  }
  // Save it to the settings array for later use...
  $settings['menu'] = $menu;
}

// Writes the user or guest online, also deletes old ones expired guests/users
function WriteOnline() {
global $db_prefix, $settings, $user;
  
  // Delete all rows that well, are no longer valid... expired, dead, you get it :P
  $timeout = time() - ($settings['login_threshold'] * 60);
  sql_query("
    DELETE FROM {$db_prefix}online
    WHERE `last_active` < '$timeout'
    AND `sc` != '". clean(session_id()). "'");
  // Only if they are not logging in at step two
  if (@$_REQUEST['action'] != 'login2') {
    // We deleted theirs, now make a new one
    $url_data = (!empty($_GET) && is_array($_GET)) ? addslashes(addslashes(serialize($_GET))) : '';
    // They in the forum?
    $inForum = (defined('InForum') && InForum == true) ? 1 : 0;
    // Now put it in, or replace it with an old one...
    sql_query("REPLACE INTO {$db_prefix}online (`user_id`,`sc`,`ip`,`url_data`,`inForum`,`last_active`) VALUES('{$user['id']}','". clean(session_id()). "','{$user['ip']}','{$url_data}','{$inForum}','".time()."')");
  }
  // They have just finished logging in, so delete references to them as a guest
  else
    sql_query("DELETE FROM {$db_prefix}online WHERE `sc` = '". clean(session_id()). "'");
}

// This returns true or false (bool) of whether or not they can do said function
function can($what) {
global $perms, $user;
  
  // Groups of permissions
  $groups = array(
    'change_settings' => array('change_displayname','change_email','change_birthdate','change_avatar','change_signature','change_profile',
                               'change_password'),
    'manage_pages' => array('manage_pages_modify_html','manage_pages_modify_bbcode','manage_pages_create','manage_pages_delete','manage_pages_home'),
    'manage_members' => array('moderate_username','moderate_display_name','moderate_email','moderate_password','moderate_birthdate','moderate_avatar','moderate_group','moderate_signature','moderate_profile','moderate_activate','moderate_suspend','moderate_unsuspend','moderate_ban','moderate_unban'),
    'manage_forum' => array('manage_forum_edit','manage_forum_create','manage_forum_delete','manage_forum_perms')
  );
  
  // Check if they are allowed to perform any action in the group
  foreach ($groups as $group => $permissions)
    if ($what == $group)
      foreach ($permissions as $permission)
        if (can($permission))
          return true;
  
  // This is a super simple Permission handler, simply, can they do the requested $what or not?
  // If it isn't set, we say false because we dont know ._.
  if($user['group']!=1) {
    if(empty($perms[$user['group']][$what]) && $user['group']!=1)
      return false;
    elseif($perms[$user['group']][$what] || $user['group']==1) 
      return true;
    else
      return false;
  }
  else 
    return true;
}

// Call this function like so inlength('length',50) to check if the
// field size 50 is within the range length_short to length_long
function inlength($length, $numb) {
global $settings;
  
  if($numb < $settings[$length.'_short'])
    return 'short';
  elseif($numb > $settings[$length.'_long'])
    return 'long';
  else
    return '';
}

function canforum($what, $board = 0) {
global $bperms, $user;
  // This is a super simple Permission handler, simply, can they do the requested $what or not?
  // If it isn't set, we say false because we dont know ._.
  // $in, if it is 0, it means a board, 1 means topic...
  if($user['group']!=1) {
    if(empty($bperms[$user['group']][$board][$what]))
      return false;
    elseif($bperms[$user['group']][$board][$what]) 
      return true;
    else
      return false;
  }
  else
    return true;
}

// Creates a random session id
function create_sid() {
global $db_prefix;
  // This creates a random session ID, and it saves it into their users row...
  // !!! This function will probably be removed later on... Maybe
  if(empty($_SESSION['sc'])) {  
    $string = mkstring();
    $result = sql_query("SELECT * FROM {$db_prefix}members WHERE `sc` = '{$string}'");
    while(mysql_num_rows($result)>0) {
      $string = mkstring();
      $result = sql_query("SELECT * FROM {$db_prefix}members WHERE `sc` = '{$string}'");
    }
    // Got it! Now set it to the session and the user name...
    $_SESSION['sc'] = $string;
    sql_query("UPDATE {$db_prefix}members SET `sc` = '$string' WHERE `id` = '{$_SESSION['id']}'");
  }
  else
    $string = $_SESSION['sc'];
  return $string;
}

function mkstring() {
  // Randomly choose how long the session id will be
  $length = rand(40,50);
  // Allowed characters ;)
  $chars = "GhHiIjJyYzAbvlLmMnTuaBcC78NoOpPqWxVwuXZ023eE1f45kKUvU69QStrRsdDFg";
  srand((double)microtime()*1000000);
  $string = '';
  // Make it now, how long? Nobody knows!
  for($i = 0; $i < $length; $i++) {
    $num = rand() % 33;
    $tmp = substr($chars, $num, 1);
    $string = $string.$tmp;
  }
  // Returns it to the place it was called upon
  return $string;
}
// Formats the time with the time format in settings If timestamp is unset, get the current time
function formattime($timestamp = 0, $timedate = 0) {
global $settings;
  // No time stamp? (10 digit number), set the current...
  if(!$timestamp)
    $timestamp = time();
  // How should it be formatted? There are a few ways...
  switch ($timedate) {
    case 0: return date($settings['dateformat'], $timestamp); break;
    case 1: return date($settings['timeformat'], $timestamp); break;
    case 2: return date($settings['timeformat'].', '.$settings['dateformat'], $timestamp); break;
    case 3: return date($settings['dateshort'], $timestamp); break;
  }
}

// Process BBCode
function bbc($str) {
global $l, $settings, $theme_dir, $theme_url, $smileys;
  
  // The BBCode to replace with regex
  $bbcode = array(
    // Encode [ and ] inside code tags
    '/(\[code\].*?)\[(.*?\[\/code\])/is',
    '/(\[code\].*?)\](.*?\[\/code\])/is',
    // [br]
    '/\[br\]/is',
    // [hr]
    '/\[hr\]/is',
    // [b]...[/b]
    '/\[b\](.*?)\[\/b\]/is',
    // [i]...[/i]
    '/\[i\](.*?)\[\/i\]/is',
    // [u]...[/u]
    '/\[u\](.*?)\[\/u\]/is',
    // [s]...[/s]
    '/\[s\](.*?)\[\/s\]/is',
    // [tt]...[/tt]
    '/\[tt\](.*?)\[\/tt\]/is',
    // [sup]...[/sup]
    '/\[sup\](.*?)\[\/sup\]/is',
    // [sub]...[/sub]
    '/\[sub\](.*?)\[\/sub\]/is',
    // [small]...[/small]
    '/\[small\](.*?)\[\/small\]/is',
    // [big]...[/big]
    '/\[big\](.*?)\[\/big\]/is',
    // [left]...[/left]
    '/\[left\](.*?)\[\/left\]/is',
    // [center]...[/center]
    '/\[center\](.*?)\[\/center\]/is',
    // [right]...[/right]
    '/\[right\](.*?)\[\/right\]/is',
    // http://... & https://...
    '/(\s)(https?:\/\/[^\s]+?)(\s)/is',
    // ftp://... & ftps://...
    '/(\s)(ftps?:\/\/[^\s]+?)(\s)/is',
    // mailto:...
    '/(\s)(mailto:\/\/[^\s]+?)(\s)/is',
    // javascript:...
    '/(\s)(javascript:\/\/[^\s]+?)(\s)/is',
    // www....
    '/(\s)(www\.[^\s]+?)(\s)/is',
    // ...@...
    '/(\s)([^\s]+?@[^\s]+?)(\s)/is',
    // [url=http://...]...[/url] & [url=https://...]...[/url]
    '/\[url=(https?:\/\/[^] ]+?)\](.*?)\[\/url\]/is',
    // [url=ftp://...]...[/url] & [url=ftps://...]...[/url]
    '/\[url=(ftps?:\/\/[^] ]+?)\](.*?)\[\/url\]/is',
    // [url=mailto:...]...[/url]
    '/\[url=(mailto:[^] ]+?)\](.*?)\[\/url\]/is',
    // [url=javascript...]...[/url]
    '/\[url=(javascript:[^] ]+?)\](.*?)\[\/url\]/is',
    // [url=...]...[/url]
    '/\[url=([^] ]+?)\](.*?)\[\/url\]/is',
    // [img]http://...[/img] & [img]https://...[/img]
    '/\[img\](https?:\/\/.*?)\[\/img\]/is',
    // [img]ftp://...[/img] & [img]ftps://...[/img]
    '/\[img\](ftps?:\/\/.*?)\[\/img\]/is',
    // [img]...[/img]
    '/\[img\](.*?)\[\/img\]/is',
    // [font=...]...[/font]
    '/\[font=([a-z ,]+?)\](.*?)\[\/font\]/is',
    // [color=...]...[/color]
    '/\[color=([a-z]+?)\](.*?)\[\/color\]/is',
    // [color=#...]...[/color]
    '/\[color=(#[0-9a-f]{1,6})\](.*?)\[\/color\]/is',
    // [size=...pt]...[/size] & [size=...px]...[/size]
    '/\[size=([0-9]{1,2}p[tx])\](.*?)\[\/size\]/is',
    // [size=...cm]...[/size] & [size=...mm]...[/size]
    '/\[size=([0-9]{1,2}[cm]m)\](.*?)\[\/size\]/is',
    // [size=...in]...[/size]
    '/\[size=([0-9]{1,2}in)\](.*?)\[\/size\]/is',
    // [size=0-7]...[/size]
    '/\[size=1\](.*?)\[\/size\]/is',
    '/\[size=2\](.*?)\[\/size\]/is',
    '/\[size=3\](.*?)\[\/size\]/is',
    '/\[size=4\](.*?)\[\/size\]/is',
    '/\[size=5\](.*?)\[\/size\]/is',
    '/\[size=6\](.*?)\[\/size\]/is',
    '/\[size=7\](.*?)\[\/size\]/is',
    // [size=xx-small-xx-large]...[/size]
    '/\[size=(small)\](.*?)\[\/size\]/is',
    '/\[size=(x?x-small)\](.*?)\[\/size\]/is',
    '/\[size=(medium)\](.*?)\[\/size\]/is',
    '/\[size=(large)\](.*?)\[\/size\]/is',
    '/\[size=(x?x-large)\](.*?)\[\/size\]/is'
  );
  // The HTML to replace with the BBCode
  $html = array(
    '$1&#091;$2',
    '$1&#093;$2',
    '<br />',
    '<hr />',
    '<b>$1</b>',
    '<i>$1</i>',
    '<u>$1</u>',
    '<span style="text-decoration: line-through">$1</span>',
    '<tt>$1</tt>',
    '<sup>$1</sup>',
    '<sub>$1</sub>',
    '<small>$1</small>',
    '<big>$1</big>',
    '<div style="text-align: left">$1</div>',
    '<div style="text-align: center">$1</div>',
    '<div style="text-align: right">$1</div>',
    '$1<a href="$2">$2</a>$3',
    '$1<a href="$2">$2</a>$3',
    '$1<a href="$2">$2</a>$3',
    '$1<a href="$2" onclick="return confirm(\'Warning: Clicking this link may be potentially dangerous, if you do not trust the poster click cancel.\')">$2</a>$3',
    '$1<a href="http://$2">$2</a>$3',
    '$1<a href="mailto:$2">$2</a>$3',
    '<a href=`$1`>$2</a>',
    '<a href=`$1`>$2</a>',
    '<a href=`$1`>$2</a>',
    '<a href=`$1` onclick="return confirm(\'Warning: Clicking this link may be potentially dangerous, if you do not trust the poster click cancel.\')">$2</a>',
    '<a href="http://$1">$2</a>',
    '<img src="$1" alt="Member posted image" />',
    '<img src="$1" alt="Member posted image" />',
    '<img src="http://$1" alt="Member posted image" />',
    '<span style="font-family: $1">$2</span>',
    '<span style="color: $1">$2</span>',
    '<span style="color: $1">$2</span>',
    '<span style="font-size: $1">$2</span>',
    '<span style="font-size: $1">$2</span>',
    '<span style="font-size: $1">$2</span>',
    '<span style="font-size: xx-small">$1</span>',
    '<span style="font-size: x-small">$1</span>',
    '<span style="font-size: small">$1</span>',
    '<span style="font-size: medium">$1</span>',
    '<span style="font-size: large">$1</span>',
    '<span style="font-size: x-large">$1</span>',
    '<span style="font-size: xx-large">$1</span>',
    '<span style="font-size: $1">$2</span>',
    '<span style="font-size: $1">$2</span>',
    '<span style="font-size: $1">$2</span>',
    '<span style="font-size: $1">$2</span>',
    '<span style="font-size: $1">$2</span>'
  );
  // Temporarily convert advanced quote tags to normal quote tags
  $temp = preg_replace('/\r?\n? *\[quote=([^]]+?)\]\r?\n?(.*?)/is','[quote]',$str);
  $temp = preg_replace('/\r?\n? *\[quote by=([^]]+?)\]\r?\n?(.*?)/is','[quote]',$temp);
  // If the amount of quote tags are equal, parse them too
  if (substr_count($temp,"[quote]") == substr_count($str,"[/quote]")) {
    // [quote]
    $bbcode[] = '/\r?\n? *\[quote\]\r?\n?(.*?)/is';
    // [quote=...]
    $bbcode[] = '/\r?\n? *\[quote=([^]]+?)\]\r?\n?(.*?)/is';
    // [quote by=...]
    $bbcode[] = '/\r?\n? *\[quote by=([^]]+?)\]\r?\n?(.*?)/is';
    // [/quote]
    $bbcode[] = '/\[\/quote\] *\r?\n?/is';
    // Corresponding HTML replacements
    $html[] = '<br /><b>Quote:</b><div class="quote">$1';
    $html[] = '<br /><b>Quote from $1:</b><div class="quote">$2';
    $html[] = '<br /><b>Quote from $1:</b><div class="quote">$2';
    $html[] = '</div>';
  }
  // If the amount of code tags are equal, parse them too
  if (substr_count($temp,"[code]") == substr_count($str,"[/code]")) {
    // [code]...[/code]
    $bbcode[] = '/\[code\]\r?\n?(.*?)\r?\n?\[\/code\] *\r?\n?/is';
    $html[] = '<br /><b>Code:</b><pre class="code">$1</pre>';
  }
  // The amount of code tags are not equal, so don't parse them
  else {
    unset($bbcode[0]);
    unset($bbcode[1]);
    unset($html[0]);
    unset($html[1]);
  }
  // Add the emoticon replacements
  require_once($theme_dir.'/'.$settings['theme'].'/emoticons/emoticons.php');
  foreach ($smileys as $smiley => $file) {
    $encoded = str_replace(array('[','^','$','.','|','?','*','+','(',')'),array('\[','\^','\$','\.','\|','\?','\*','#','\(','\)'),$smiley);
    $bbcode[] = '/([^a-z0-9:()><[\]])'.$encoded.'([^a-z0-9:()><[\]])/is';
    if ($smiley == ':P')
      $smiley = 'Mr. Yucky-Poo';
    $html[] = '$1<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" title="'.$smiley.'" class="emoticon" />$2';
  }
  // Add the newline replacement
  $bbcode[] = '/\n/is';
  $html[] = '<br />';
  // Replace the BBCode with HTML and then return
  return substr(preg_replace($bbcode,$html,' '.$str),1);
}

// Our Version of mysql_query(), this function looks sad right now, but will be improved sooner or later...
function sql_query($query) {
global $num_queries;
  // Number of MySQL Queries?
  $num_queries = isset($num_queries) ? $num_queries + 1 : 0;
  $result = mysql_query($query);
  if(!$result) {
    /*
      NOTE:
      The way errors are logged will be changed in probably
      SnowCMS v0.8, maybe even in SunSpot, but most likely
      not until v0.8 :)
    */
    // Oh noes! An SQL Error maybe? We don't want to die(mysql_error()); but we should save these errors in a file ;)
    if(!file_exists('error_log.log')) 
      @file_put_contents('error_log.log', mysql_error()."\n");
    else {
      $errors = @file_get_contents('error_log.log');
      $errors.= mysql_error()."\n";
      @file_put_contents('error_log.log', $errors);
    }
  }
  // Return it now =-D
  return $result;
}

/* 
  This is called upon when a MySQL Connection Error Occurs, and hopefully won't look so devastating
  when it occurs, hopefully...
*/
function MySQLError($error) {
echo '
<html>
<head>
  <title>MySQL Connection Error</title>
  <style type="text/css">
  body {
    font-family: Verdana;
    font-size: 12px;
  }
  p {
    text-align: center;
  }
  </style>
</head>
<body>
  <p>A MySQL Error has occurred! Error: ', $error, '</p>
</body>
</html>';
}

// A simple function to redirect
function redirect($relative_url = false) {
global $cmsurl;
  // If its false, then redirect to the index.php
  if($relative_url === false)
    $relative_url = 'index.php';
  // Now redirect XD
  header("Location: {$cmsurl}{$relative_url}");
  exit;
}

// Validates if the Session ID matches that of the users...
function ValidateSession($sc) {
global $db_prefix, $user;
  // Select the current user id
  $result = sql_query("SELECT `sc` FROM {$db_prefix}members WHERE `id` = '{$user['id']}'");
  // Get it...
  $row = mysql_fetch_assoc($result);
  // This is simple :D
  if($sc == $row['sc'])
    return true;
  else
    return false;
}
  /* This is an array of permissions that can be done on the forum ;) */
  // 'PERM' => 'Defaultly (Is that a word?) Set'
  $forumperms = array(
    'delete_any' => false,  
    'delete_own' => true,
    'lock_topic' => false,
    'move_any' => false,
    'edit_any' => false,
    'edit_own' => true,
    'post_new' => true,
    'post_reply' => true,
    'sticky_topic' => false,
    'split_topic' => false
  ); 
  // ^^^ Should be moved somewhere else, such as inside the loadPerms(); function (No, not the loadBPerms(); function!) ^^^
  
// This turns the & separator to a ; cause it looks nicer :)
function cleanQuery() {
global $_REQUEST, $_GET;
  // Remove current request variables
  unset($_REQUEST);
  // Add post variables to request
  foreach ($_POST as $key => $value) {
    $_REQUEST[$key] = $value;
  } 
  // Make sure there is even somehting that needs handling, we don't want errors
  if(!empty($_SERVER['QUERY_STRING'])) {
	  // The Query String...
	  $query_str = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
	  // Split... like EXPLOSION, just not the same :P
	  $query_strings = split('[;&]', urldecode($query_str));
	  // Loop ftw
	  foreach($query_strings as $tmp)
		  if(preg_match("/^([^=]+)([=](.*))*/", $tmp, $parts))
			  $new[$parts[1]] = !empty($parts[2]) ? $parts[2] : '';
		// Anything in the new?
		if(count($new)) {
		  // Set them
		  foreach($new as $key => $value) {
		    // Bug fix...
		    $value = substr($value, 1, strlen($value));
		    // Set them :)
		    $_REQUEST[$key] = $value;
		    $_GET[$key] = $value;
		  }
		}
  }
}

// This function is for the forum! It loads a link tree of the current position...
function loadTree() {
global $cmsurl, $db_prefix, $settings, $user;
  $settings['linktree'] = array();
  // Where are? Topic? Board?
  if(empty($_REQUEST['topic']) && empty($_REQUEST['board'])) {
    // Hmmm, we must be at Home Sweet Home...
    $settings['linktree'][] = array(
                                'name' => $settings['site_name'],
                                'href' => $cmsurl.'forum.php'
                              );
  }
  elseif(!empty($_REQUEST['board'])) {
    // A board :o
    $board_id = (int)$_REQUEST['board'];
    // We still need the home link :)
    $settings['linktree'][] = array(
                                'name' => $settings['site_name'],
                                'href' => $cmsurl.'forum.php'
                              );    
    $result = sql_query("
      SELECT
        b.bid, b.name
      FROM {$db_prefix}boards AS b
      WHERE {$user['board_query']} AND b.bid = $board_id");
    // If no boards, well, they can't see it OR, it really doesn't exist...
    if(mysql_num_rows($result)) {
      $board = mysql_fetch_assoc($result);
      $settings['linktree'][] = array(
                                  'name' => $board['name'],
                                  'href' => $cmsurl.'forum.php?board='.$board['bid']
                                );
    }
  }
  else {
    // Its got to be a topic
    $topic_id = (int)$_REQUEST['topic'];
    // We still need the home link :)
    $settings['linktree'][] = array(
                                'name' => $settings['site_name'],
                                'href' => $cmsurl.'forum.php'
                              );     
    // Get out the stuff we need
    $result = sql_query("
      SELECT
        t.tid, t.bid, t.first_msg, msg.subject, msg.mid, b.bid, b.name, b.who_view
      FROM {$db_prefix}topics AS t
        LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = t.first_msg
        LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
      WHERE {$user['board_query']} AND t.tid = $topic_id");
    // If no rows, they can't see it, nanananana! Or it doesn't exist, Lol. We will never know.
    if(mysql_num_rows($result)) {
      $topic = mysql_fetch_assoc($result);
      $settings['linktree'][] = array(
                                  'name' => $topic['name'],
                                  'href' => $cmsurl.'forum.php?board='.$topic['bid']
                                );
      $settings['linktree'][] = array(
                                  'name' => $topic['subject'],
                                  'href' => $cmsurl.'forum.php?topic='.$topic['tid']
                                );
    }
  }
}

// Just because this function is getting called doesn't mean the language is going to change
function changeLanguage() {
global $db_prefix, $user, $cookie_prefix;
  
  if (@$_POST['change-language']) {
    // Oh wait, it is
    $language = clean(@$_POST['change-language']);
    // If they are a user, it can be saved, however, if it is a guest, it can only be done via a cookie
    if ($user['is_logged'])
      sql_query("UPDATE {$db_prefix}members SET `language` = '$language' WHERE `id` = '{$user['id']}'");
    else
      setcookie($cookie_prefix.'change-language', $language, time()+60*60*24*365);
    // Set the users language....
    $user['language'] = $language;
  }
}

// Return the post's owner
function PostOwner($post) {
global $db_prefix;
  // Simple cleaning, but it does the job :P
  $post = (int)$post;
  $post = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages WHERE `mid` = $post"));
  return $post['uid'];
}

// Hide email address from bots
function hideEmail($email) {
  $return = preg_replace('/(.+)...@(.*)/','$1...@$2',$email);
  if ($return == $email)
    $return = preg_replace('/(.+)..@(.*)/','$1...@$2',$email);
  if ($return == $email)
    $return = preg_replace('/(.+).@(.*)/','$1...@$2',$email);
  if ($return == $email)
    $return = preg_replace('/(.+)@(.*)/','$1...@$2',$email);
  if ($return == $email)
    $return = preg_replace('/.*@(.*)/','...@$1',$email);
  // Return the hidden email address now
  return $return;
}

// Check if their IP has been banned
function checkIP() {
global $l, $settings, $db_prefix;
  // Get their IP Address
  $ip = @$_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  // Now their IP Range...
  $ip_range = preg_replace('/^([12]?[0-9]{1,2}\.[12]?[0-9]{1,2}\.[12]?[0-9]{1,2}\.)[12]?[0-9]{1,2}/','$1',$ip);
  // Is either their IP or IP Range banned?
  if (mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}banned_ips WHERE `ip` = '$ip' OR `ip` = '$ip_range'"))) {
    echo 'Your IP address is banned.';
    exit;
  }
}

// This is done to allow AJAX to access it
function loadQuickEdit() {
global $user, $db_prefix;
  if ($bbcode = @$_REQUEST['bbcode']) {
    if ($bbcode = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages LEFT JOIN {$db_prefix}boards AS b ON b.bid = {$db_prefix}messages.bid WHERE `mid` = '$bbcode' AND (FIND_IN_SET('{$user['group']}', `b`.`who_view`) OR '{$user['group']}' = '1')"))) {
      echo '{
      poster_name: "'.str_replace('"','\\"',$bbcode['poster_name']).'", 
      bbcode: "'.urlencode(html_entity_decode($bbcode['body'])).'", 
      post_time: "'.str_replace('"','\\"',$bbcode['post_time']).'", 
      }';
      exit;
    }
    else
      exit;
  }
  elseif ($html = @$_REQUEST['html']) {
    if ($html = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages LEFT JOIN {$db_prefix}boards AS b ON b.bid = {$db_prefix}messages.bid WHERE `mid` = '$html' AND (FIND_IN_SET('{$user['group']}', `b`.`who_view`) OR '{$user['group']}' = '1')"))) {
      echo '{
      poster_name: "'.str_replace('"','\\"',$html['poster_name']).'", 
      html: "'.urlencode(bbc($html['body'])).'", 
      post_time: "'.str_replace('"','\\"',$html['post_time']).'", 
      }';
      exit;
    }
    else
      exit;
  }
}

function WizardMagic() {
global $_REQUEST, $_GET, $_POST, $_COOKIE;
  // This fixes magic quotes with the
  // Wizard Magical powers! =D
  $_REQUEST = assistWizard($_REQUEST);
  $_GET = assistWizard($_GET);
  $_POST = assistWizard($_POST);
  $_COOKIE = assistWizard($_COOKIE);
}

// This assists WizardMagic();
function assistWizard($array) {
  // Is it an array? Lol... and something in it...
  if(is_array($array) && count($array)) {
    // Set a tmp var...
    $tmp = array();
    foreach($array as $key => $value) {
      // If it isn't an array, clean it...
      if(!is_array($value))
        $tmp[$key] = stripslashes($value);
      else {
        // Its another array? ZOMG! D:
        // I could do this recursively, but that could be bad
        // Because people are mean like that, poopie heads!
        // So yeah, we will only go into this once :P
        foreach($value as $v_key => $v_data)
          $tmp[$key][$v_key] = stripslashes($v_data);
      }
    }
    return $tmp;
  }
  else
    return array();
}

function maintenanceMode() {
global $user, $l, $settings;
  // Only Administrators can access
  // the site when in Maintenance Mode
  // If they aren't an admin, show the
  // Maintenance Screen ;) with a login
  // Form so other administrators may login
  if(!$user['is_admin'] && $settings['maintenance_mode'] && @$_GET['action'] != 'login2') {
    // They aren't an admin, and maintenance
    // mode is enabled... Show a page with a
    // Login Screen
    $settings['page']['title'] = $l['maintenance_mode'];
    loadTheme('Main','MaintenanceScr');
    exit;
  }
}
?>