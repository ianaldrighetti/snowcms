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
global $db_prefix, $settings;
  $result = sql_query("SELECT * FROM {$db_prefix}settings");
    while($row = mysql_fetch_assoc($result)) 
      $settings[$row['variable']] = $row['value'];
  return $settings;
} 

// Instead of using addslashes and or mysql_real_escape_string, we use this mostly to sanitize
// It makes it so later on, once we have a forum in, you can post PHP and HTML stuff, but it
// Won't be parsed, but keep the site safe from SQL Injections 
function clean($str) {
  $replace = array(
    '&' => '&amp;',
    '"' => '&quot;',
    "'" => '&#39;',
    '<' => '&lt;',
    '>' => '&gt;'
  );
  $str = str_replace(array_keys($replace), array_values($replace), $str);
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
  $user = array();
  // Set some default info, incase they are guests
  $user['id'] = 0;
  $user['group'] = -1;
  $user['is_logged'] = false;
  $user['is_guest'] = true;
  $user['is_admin'] = false;
  $user['name'] = null;
  $user['email'] = null;
  $user['language'] = false;
  $user['sc'] = 'guest';
  $user['board_query'] = 'FIND_IN_SET('. $user['group']. ', b.who_view)';
  // Make sure we get their real IP :)
  $user['ip'] = @isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  if(empty($_SESSION['id'])) {
    // We need to sanitize the cookies, last thing we need is to be hacked by cookies, Those are some bad cookies (Like Oatmeal ones, Ewww!)
    $_SESSION['id'] = @addslashes(mysql_real_escape_string($_COOKIE[$cookie_prefix.'uid']));
    $_SESSION['user'] = @clean($_COOKIE[$cookie_prefix.'username']);
    $_SESSION['pass'] = @clean($_COOKIE[$cookie_prefix.'password']);
  }
  if(isset($_SESSION['id'])) {
    if(isset($_SESSION['pass'])) {
      $result = sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '{$_SESSION['id']}' AND `password` = '{$_SESSION['pass']}'");
      // If user ID and password are in the database
      if (mysql_num_rows($result)) {
        while($row = mysql_fetch_assoc($result)) {
          $user = array(
            'id' => $row['id'],
            'name' => $row['display_name'] ? $row['display_name'] : $row['username'],
            'group' => $row['group'],
            'is_logged' => true,
            'is_guest' => false,
            'is_admin' => false,
            'email' => $row['email'],
            'language' => $row['language'],
            'board_query' => 'FIND_IN_SET('. $user['group']. ', b.who_view)',
            'ip' => @$_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
            'sc' => create_sid()
          );
          if($user['group']==1) {
            $user['is_admin'] = true;
            $user['board_query'] = '1=1';
          }
        }
      }
      else {
        setcookie($cookie_prefix."uid","",time()-60*60*24);
        setcookie($cookie_prefix."username","",time()-60*60*24);
        setcookie($cookie_prefix."password","",time()-60*60*24);
      }
    }
  }
  // Check session validation
  if (!ValidateSession($user['sc']) && $user['group'] != -1 && @$_REQUEST['action'] != 'logout') {
    $result = sql_query("SELECT `sc` FROM {$db_prefix}members WHERE `id` = '{$user['id']}'");
    $row = mysql_fetch_assoc($result);
    redirect('index.php?action=logout;sc='.$row['sc']);
  }
  
  return $user;
}

// This loads the Language file, from the language directory, later on (Maybe SnowCMS 0.8 or later)
// Will allow each theme to have its own language file(s)
function loadLanguage() {
global $cmsurl, $l, $language_dir, $settings, $theme_dir, $user, $db_prefix, $cookie_prefix;
  
  // Get the language record from either the user's profile, the cookies or the site's default
  $language = clean($user['language'] ? $user['language'] : (@$_COOKIE[$cookie_prefix.'change-language'] ? @$_COOKIE[$cookie_prefix.'change-language'] : $settings['language']));
  
  $l = array();
  require_once($language_dir.'/'.$language.'.language.php');
  // Does the current theme have its own language support?
  if(file_exists($theme_dir.'/'.$settings['theme'].'/'.$language.'.language.php')) {
    $tmp['1'] = $l;
    unset($l);
    $l = array();
    require_once($theme_dir.'/'.$settings['theme'].'/languages/'.$language.'.language.php');
    $tmp['2'] = $l;
    $l = merge_languages($tmp);
  }
  return $l;
}

// This will merge the $l arrays which will allow themes to have their own specific language files
function merge_languages($array) {
  $tmp = array();
  foreach($array as $l) {
    foreach($l as $key => $value) {
      $tmp[$key] = $value;
    }
  }
  return $tmp;
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
  $perms = array();
  $perms[$user['group']] = array();
  $result = sql_query("SELECT * FROM {$db_prefix}permissions") or die(mysql_error());
    while($row = mysql_fetch_assoc($result)) {
      $perms[$row['group_id']][$row['what']] = $row['can'] ? true : false;
    }
  return $perms;
}

// Loads up the permissions, except this is for the forum permissions,
// So we can make sure you are allowed to edit/delete/move/etc
function loadBPerms() {
global $bperms, $db_prefix, $user, $forumperms;
  $bperms = array();
  $bperms[$user['group']] = array();
  $result = sql_query("SELECT * FROM {$db_prefix}board_permissions") or die(mysql_error());
    while($row = mysql_fetch_assoc($result)) {
      $bperms[$row['group_id']][$row['bid']][$row['what']] = $row['can'] ? true : false;
    }
  return $bperms;
}

// Load all the menus, both the Sidebar menu (if their is one) and the Main one (If their is one :P)
function loadMenus() {
global $db_prefix, $settings;
  $menu = array();
  $menu['main'] = array();
  $menu['side'] = array();
  $result = sql_query("SELECT * FROM {$db_prefix}menus ORDER BY `order` ASC") or die(mysql_error());
  if (mysql_num_rows($result)) {  
    while ($row = mysql_fetch_assoc($result)) {
      if ($row['menu'] == 1 || $row['menu'] == 3) {
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
      if ($row['menu'] == 2 || $row['menu'] == 3) {
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
  $settings['menu'] = $menu;
}

// Writes the user or guest online, also deletes old ones expired guests/users
function WriteOnline() {
global $db_prefix, $settings, $user;
  if(!empty($_REQUEST['action']))
    $action_or_page = 'action:'. clean($_REQUEST['action']);
  elseif(!empty($_REQUEST['page']))
    $action_or_page = 'page:'. clean($_REQUEST['page']);
  else
    $action_or_page = 0;
  // Select all rows
  $result = sql_query("
    SELECT
      *
    FROM {$db_prefix}online");
  $id_del = array();
  $ip_del = array();
  // While Loop =D Load all expired IP's and user_id's, and the current users as well
  while($row = mysql_fetch_assoc($result)) {
    if($user['is_logged'] && $row['user_id'] = $user['id'])
      $id_del[] = $user['id'];
    elseif($row['user_id'] != 0 && ($row['last_active']+($settings['login_threshold']*60))<time())
      $id_del[] = $row['user_id'];
    elseif($row['user_id'] == 0 && ($row['last_active']+($settings['login_threshold']*60))<time())
      $ip_del[] = $row['ip'];
    elseif($user['id'] == 0 && $row['ip'] = $user['ip'])
      $ip_del[] = $row['ip'];
  }
  // Delete them all that aren't needed :)
  if(count($id_del))
    sql_query("DELETE FROM {$db_prefix}online WHERE `user_id` IN(". implode(",", $id_del). ")");
  if(count($ip_del))
    sql_query("DELETE FROM {$db_prefix}online WHERE `ip` IN('". implode("','", $ip_del). "') AND `sc` = 'guest'");
  // We deleted theirs, make a new one...
  sql_query("INSERT INTO {$db_prefix}online (`user_id`,`sc`,`ip`,`page`,`last_active`) VALUES('{$user['id']}','{$user['sc']}','{$user['ip']}','{$action_or_page}','".time()."')") or die(mysql_error());
}

// This returns true or false (bool) of whether or not they can do said function
function can($what) {
global $perms, $user;
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
  $chars = "GhHiIjJyYzAbvlLmMnTuaBcC78NoOpPqWxVwuXZ023eE1f45kKUvU69QStrRsdDFg";
  srand((double)microtime()*1000000);
  $i = 0;
  $string = '';
  while ($i <= ($length-1)) {
    $num = rand() % 33;
    $tmp = substr($chars, $num, 1);
    $string = $string.$tmp;
    $i++;
  }
  // Returns it to the place it was called upon
  return $string;
}
// Formats the time with the time format in settings If timestamp is unset, get the current time
function formattime($timestamp = 0, $timedate = 0) {
global $settings;
  if(!$timestamp)
    $timestamp = time();
  switch ($timedate) {
    case 0: return date($settings['dateformat'], $timestamp); break;
    case 1: return date($settings['timeformat'], $timestamp); break;
    case 2: return date($settings['timeformat'].', '.$settings['dateformat'], $timestamp); break;
  }
}

/*
* Features super-hackish but functional BBCODE Quote Hack courtesy of Antimatter15
* Also features insanely complicated regex to ignore BBCode inside code tags courtesy of Myles
*/
function bbc($str, $code_tags = true) {
global $theme_dir, $theme_url, $settings;
  
  // Keep the original product incase we need it later
  $str_start = $str;
  
  // Process newline characters
  $str = strtr($str, array("\r\n" => "\n", "\r" => "\n"));
  
  // These are added to make it so that if there isn't any code tags before or after some BBCode, it will still detect that it is outside code tags
  $str = '[/code]'.$str.'[code]';
  
  // These three characters will have special meanings later, so wee need to encode them
  $str = str_replace('!','&#33;',$str);
  $str = str_replace('%','&#37;',$str);
  $str = str_replace('|','&#124;',$str);
  
  // [code] will now be known as | and [/code] as \r
  // This is needed because regex can't do things with groups of characters, so they need to be represented as single characters
  $str = str_replace('[code]','|',$str);
  $str = str_replace('[/code]','\r',$str);
  
  // This is for any span of characters
  $all_chars = '^\r\|%';
  // This is for any span of characters, without whitespace, for use it URLs
  $link_chars = '^ \n\r\|%';
  
  // If the string is the same as the previous loop, stop looping
  $str_prev = '';
  while ($str_prev != $str) {
    $str_prev = $str;
    
    // These regex are complicated because they make sure they aren't inside code tags
    // [b]...[/b]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[b\](['.$all_chars.']*)\[\/b\](['.$all_chars.']*\|)/is','$1<b>$2</b>$3',$str);
    // [i]...[/i]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[i\](['.$all_chars.']*)\[\/i\](['.$all_chars.']*\|)/is','$1<i>$2</i>$3',$str);
    // [u]...[/u]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[u\](['.$all_chars.']*)\[\/u\](['.$all_chars.']*\|)/is','$1<u>$2</u>$3',$str);
    // [s]...[/s]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[s\](['.$all_chars.']*)\[\/s\](['.$all_chars.']*\|)/is','$1<s>$2</s>$3',$str);
    // [tt]...[/tt]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[tt\](['.$all_chars.']*)\[\/tt\](['.$all_chars.']*\|)/is','$1<tt>$2</tt>$3',$str);
    
    // [url=http://...]...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url=(http:\/\/['.$link_chars.']*)\](['.$all_chars.']*\|)\[\/url\](['.$all_chars.']*)/is','$1<a href="$2">$3</a>$4',$str);
    // [url=https://...]...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url=(https:\/\/['.$link_chars.']*)\](['.$all_chars.']*\|)\[\/url\](['.$all_chars.']*)/is','$1<a href="$2">$3</a>$4',$str);
    // [url=ftp://...]...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url=(ftp:\/\/['.$link_chars.']*)\](['.$all_chars.']*\|)\[\/url\](['.$all_chars.']*)/is','$1<a href="$2">$3</a>$4',$str);
    // [url=ftps://...]...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url=(ftps:\/\/['.$link_chars.']*)\](['.$all_chars.']*\|)\[\/url\](['.$all_chars.']*)/is','$1<a href="$2">$3</a>$4',$str);
    // [url=...]...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url=(['.$link_chars.']*)\](['.$all_chars.']*)\[\/url\](['.$all_chars.']*\|)/is','$1<a href="http://$2">$3</a>$4',$str);
    
    // www....
    $str = preg_replace('/(\\\r['.$all_chars.']*) (http:\/\/['.$link_chars.']*) (['.$all_chars.']*\|)/is','$1 <a href="$2">$2</a> $3',$str);
    // http://...
    $str = preg_replace('/(\\\r['.$all_chars.']*) (https:\/\/['.$link_chars.']*) (['.$all_chars.']*\|)/is','$1 <a href="$2">$2</a> $3',$str);
    // https://...
    $str = preg_replace('/(\\\r['.$all_chars.']*) (ftp:\/\/['.$link_chars.']*) (['.$all_chars.']*\|)/is','$1 <a href="$2">$2</a> $3',$str);
    // ftp://...
    $str = preg_replace('/(\\\r['.$all_chars.']*) (ftps:\/\/['.$link_chars.']*) (['.$all_chars.']*\|)/is','$1 <a href="$2">$2</a> $3',$str);
    // ftps://...
    $str = preg_replace('/(\\\r['.$all_chars.']*) (www\.['.$link_chars.']*) (['.$all_chars.']*\|)/is','$1 <a href="http://$2">$2</a> $3',$str);
    
    // [url]http://...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url\](http:\/\/['.$link_chars.']*)\[\/url\](['.$all_chars.']*\|)/is','$1 <a href="$2">$2</a> $3',$str);
    // [url]https://...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url\](https:\/\/['.$link_chars.']*)\[\/url\](['.$all_chars.']*\|)/is','$1 <a href="$2">$2</a> $3',$str);
    // [url]ftp://...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url\](ftp:\/\/['.$link_chars.']*)\[\/url\](['.$all_chars.']*\|)/is','$1 <a href="$2">$2</a> $3',$str);
    // [url]ftps://...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url\](ftps:\/\/['.$link_chars.']*)\[\/url\](['.$all_chars.']*\|)/is','$1 <a href="$2">$2</a> $3',$str);
    // [url]...[/url]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[url\](['.$link_chars.']*)\[\/url\](['.$all_chars.']*\|)/is','$1 <a href="http://$2">$2</a> $3',$str);
    
    // [quote]\n
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[quote\]\n(['.$all_chars.']*\|)/is','$1<p style="font-weight: bold; padding: 0px; margin: 0px;">Quote:</p><blockquote style="padding: 5px; margin: 0px;">$2',$str);
    // [quote=...]\n
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[quote=(['.$all_chars.']*)\]\n(['.$all_chars.']*\|)/is','$1<p style="font-weight: bold; padding: 0px; margin: 0px;">Quote from $2:</p><blockquote style="padding: 5px; margin: 0px;">$3',$str);
    // [quote by=...]\n
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[quote by=(['.$link_chars.']*)\]\n(['.$all_chars.']*\|)/is','$1<p style="font-weight: bold; padding: 0px; margin: 0px;">Quote from $2:</p><blockquote style="padding: 5px; margin: 0px;">$3',$str);
    // [/quote]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[\/quote\](['.$all_chars.']*\|)/is','$1</blockquote>$2',$str);
    
    // [br]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[br\](['.$all_chars.']*\|)/is','$1<br />$2',$str);
    
    // [hr]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[hr\](['.$all_chars.']*\|)/is','$1<hr />$2',$str);
    
    // [img]http://...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img\](http:\/\/['.$link_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1 <img src="$2" alt="" /> $3',$str);
    // [img]https://...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img\](https:\/\/['.$link_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1 <img src="$2" alt="" /> $3',$str);
    // [img]ftp://...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img\](ftp:\/\/['.$link_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1 <img src="$2" alt="" /> $3',$str);
    // [img]ftps://...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img\](ftps:\/\/['.$link_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1 <img src="$2" alt="" /> $3',$str);
    // [img]...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img\](['.$link_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1 <img src="http://$2" alt="" /> $3',$str);
    
    // [img=http://...]...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img=(http:\/\/['.$link_chars.']*)\](['.$all_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1<img src="$2" alt="$3" />$4',$str);
    // [img=https://...]...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img=(https:\/\/['.$link_chars.']*)\](['.$all_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1<img src="$2" alt="$3" />$4',$str);
    // [img=ftp://...]...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img=(ftp:\/\/['.$link_chars.']*)\](['.$all_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1<img src="$2" alt="$3" />$4',$str);
    // [img=ftps://...]...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img=(ftps:\/\/['.$link_chars.']*)\](['.$all_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1<img src="$2" alt="$3" />$4',$str);
    // [img=...]...[/img]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[img=(['.$link_chars.']*)\](['.$all_chars.']*)\[\/img\](['.$all_chars.']*\|)/is','$1<img src="http://$2" alt="$3" />$4',$str);
  }
  
  // These quotes don't have newlines at the start, they have to be done seperately
  $str_prev = '';
  while ($str_prev != $str) {
    $str_prev = $str;
    
    // [quote]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[quote\](['.$all_chars.']*\|)/is','$1<p style="font-weight: bold; padding: 0px; margin: 0px;">Quote:</p><blockquote style="padding: 5px; margin: 0px;">$2',$str);
    // [quote=...]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[quote=(['.$all_chars.']*)\](['.$all_chars.']*\|)/is','$1<p style="font-weight: bold; padding: 0px; margin: 0px;">Quote from $2:</p><blockquote style="padding: 5px; margin: 0px;">$3',$str);
    // [quote by=...]
    $str = preg_replace('/(\\\r['.$all_chars.']*)\[quote by=(['.$link_chars.']*)\](['.$all_chars.']*\|)/is','$1<p style="font-weight: bold; padding: 0px; margin: 0px;">Quote from $2:</p><blockquote style="padding: 5px; margin: 0px;">$3',$str);
  }
  
  // Process emoticons
  global $smileys;
  require_once($theme_dir.'/'.$settings['theme'].'/emoticons/emoticons.php');
  $sm_search = array();
  $sm_replace = array();
  foreach($smileys as $smiley => $file) {
    // Escape metacharacters, so they don't mess up the followeing regexes
    $smiley = str_replace(array('[','^','$','.','|','?','*','+','(',')'),array('\[','\^','\$','\.','\|','\?','\*','\+','\(','\)'),$smiley);
    // <space>:)<space>
    $sm_search[] = '/(\\\r['.$all_chars.']*) '.$smiley.' (['.$all_chars.']*\|)/';
    $sm_replace[] = '$1 <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" /> $2';
    // <newline>:)<newline>
    $sm_search[] = '/(\\\r['.$all_chars.']*)'."\n".$smiley."\n".'(['.$all_chars.']*\|)/';
    $sm_replace[] = '$1'."\n".'<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" />'."\n".'$2';
    // <space>:)<newline>
    $sm_search[] = '/(\\\r['.$all_chars.']*) '.$smiley."\n".'(['.$all_chars.']*\|)/';
    $sm_replace[] = '$1 <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" />'."\n".'$2';
    // <newline>:)<space>
    $sm_search[] = '/(\\\r['.$all_chars.']*)'."\n".$smiley.' (['.$all_chars.']*\|)/';
    $sm_replace[] = '$1'."\n".'<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" /> $2';
    // <start>:)<space>
    $sm_search[] = '/(\\\r)'.$smiley.' (['.$all_chars.']*\|)/';
    $sm_replace[] = '$1<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" /> $2';
    // <start>:)<newline>
    $sm_search[] = '/(\\\r)'.$smiley."\n".'(['.$all_chars.']*\|)/';
    $sm_replace[] = '$1<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" />'."\n".'$2';
    // <space>:)</end>
    $sm_search[] = '/(\\\r['.$all_chars.']*) '.$smiley.'(\|)/';
    $sm_replace[] = '$1 <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" />$2';
    // <newline>:)</end>
    $sm_search[] = '/(\\\r['.$all_chars.']*)'."\n".$smiley.'(\|)/';
    $sm_replace[] = '$1'."\n".'<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" />$2';
  }
  // Now put all those emoticon regexes into action
  $str = preg_replace($sm_search, $sm_replace, $str);
  
  // Time to process the code tags. But not if the second attribute is false, why? Explained in a sec
  if ($code_tags) {
    $str_prev = '';
    while ($str_prev != $str) {
      $str_prev = $str;
      
      // [code]\n
      $str = preg_replace('/([\\\r|!]['.$all_chars.']*)\|\n(['.$all_chars.']*\|)/is','$1<p style="font-weight: bold; padding: 0px; margin: 0px;">Code:</p><div class="code-outer"><div class="code-inner">$2',$str);
      // [/code]\n - You may notice we added % and ! they stop code tags from being embedded inside each other
      $str = preg_replace('/([\\\r|!]['.$all_chars.']*)\\\r(['.$all_chars.']*\|)/is','$1%</div></div>!$2',$str);
    }
    
    // Now do it again, this time without newlines
    $str_prev = '';
    while ($str_prev != $str) {
      $str_prev = $str;
      
      // [code]
      $str = preg_replace('/([\\\r|!]['.$all_chars.']*)\|(['.$all_chars.']*\|)/is','$1<p style="font-weight: bold; padding: 0px; margin: 0px;">Code:</p><div class="code-outer"><div class="code-inner">$2',$str);
      // [/code] - Again we add those characters
      $str = preg_replace('/([\\\r|!]['.$all_chars.']*)\\\r(['.$all_chars.']*\|)/is','$1%</div></div>!$2',$str);
    }
  }
  
  // Time to remove those special characters, don't worry about the ones entered by members, they are safe and encoded
  $str = str_replace('%','',$str);
  $str = str_replace('!','',$str);
  
  // Time to convert | back to [code] and \r back to [/code]
  $str = str_replace('|','[code]',$str);
  $str = str_replace('\r','[/code]',$str);
  
  // Time to remove the [/code] from the start and [code] from the end
  $str = substr($str,7,strlen($str)-13);
  
  // Count the amount of times code tags appear
  $code_starts = substr_count($str,'<p style="font-weight: bold; padding: 0px; margin: 0px;">Code:</p><div class="code-outer"><div class="code-inner">');
  $code_ends = substr_count($str,'</div></div>');
  
  // If they are not code tags don't start and end the same amount of times then we need to ignore them
  if ($code_starts != $code_ends && $code_tags)
    // So we start the entire process again, the second argument being false, makes it ignore code tags
    $str = bbc($str_start,false);
  
  // Process newlines
  $str = strtr($str, array("\n" => "<br />"));
  
  // Finally, finally we are done
  return $str;
}


// Our Version of mysql_query(), this function looks sad right now, but will be improved sooner or later...
function sql_query($query) {
  $result = mysql_query($query);
  if(!$result) {
    // Oh noes! An SQL Error maybe? We don't want to die(mysql_error()); but we should save these errors in a file ;)
    if(!file_exists('error_log')) 
      @file_put_contents('error_log', mysql_error()."\n");
    else {
      $errors = @file_get_contents('error_log');
      $errors.= mysql_error()."\n";
      @file_put_contents('error_log', $errors);
    }
  }
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
function redirect($relative_url) {
global $cmsurl;
  header("Location: {$cmsurl}{$relative_url}");
  exit;
}

// Validates if the Session ID matches that of the users...
function ValidateSession($sc) {
global $db_prefix, $user;
  $result = sql_query("SELECT `sc` FROM {$db_prefix}members WHERE `id` = '{$user['id']}'");
  $row = mysql_fetch_assoc($result);
  mysql_free_result($result);
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
  
// !!! This function changes (forcefully) the separator from & to ;
// !!! This function needs improvement!!!!
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
  // EXPLOSION! Quick and Dirty
  $matches = explode(";", $_SERVER['QUERY_STRING']);
    if(count($matches)) {
      foreach($matches as $arg) {
        // EXPLODED! Again, make the new $_GET and $_REQUEST variables
        $new = explode("=", $arg);
        $_GET[$new[0]] = $new[1];
        $_REQUEST[$new[0]] = $new[1];
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
    if ($user['is_logged'] == true)
      sql_query("UPDATE {$db_prefix}members SET `language` = '$language' WHERE `id` = '{$user['id']}'");
    else
      setcookie($cookie_prefix.'change-language',$language,time()+60*60*24*365);
    
    $user['language'] = $language;
  }
}

// Return the post's owner
function PostOwner($post) {
global $db_prefix;
  
  $post = clean($post);
  $post = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages WHERE `mid` = '$post'"));
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
  
  return $return;
}
?>