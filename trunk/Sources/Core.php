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

// Loads up the $user array, such as their member group, IP, email, if they are logged in or not
// It will also revive their session if they left, but had remember me on
function loadUser() {
global $db_prefix, $user;
  $user = array();
  // Set some default info, incase they are guests
  $user['id'] = 0;
  $user['group'] = -1;
  $user['is_logged'] = false;
  $user['is_guest'] = true;
  $user['is_admin'] = false;
  $user['name'] = null;
  $user['email'] = null;
  $user['board_query'] = 'FIND_IN_SET('. $user['group']. ', b.who_view)';
  // Make sure we get their real IP :)
  $user['ip'] = @isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  if(empty($_SESSION['id'])) {
    // We need to sanitize the cookies, last thing we need is to be hacked by cookies, Those are some bad cookies (Like Oatmeal ones, Ewww!)
    $_SESSION['id'] = @addslashes(mysql_real_escape_string($_COOKIE['uid']));
    $_SESSION['user'] = @clean($_COOKIE['username']);
    $_SESSION['pass'] = @clean($_COOKIE['password']);
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
        setcookie("uid","",time()-60*60*24);
        setcookie("username","",time()-60*60*24);
        setcookie("password","",time()-60*60*24);
      }
    }
  }
  return $user;
}

// This loads the Language file, from the language directory, later on (Maybe SnowCMS 0.8 or later)
// Will allow each theme to have its own language file(s)
function loadLanguage() {
global $cmsurl, $l, $language_dir, $settings, $theme_dir;  
  $l = array();
  require_once($language_dir.'/'.$settings['language'].'.language.php');
  // Does the current theme have its own language support?
  if(file_exists($theme_dir.'/'.$settings['theme'].'/'.$settings['language'].'.language.php')) {
    $tmp['1'] = $l;
    unset($l);
    $l = array();
    require_once($theme_dir.'/'.$settings['theme'].'/languages/'.$settings['language'].'.language.php');
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
  $settings['menu'] = $menu;
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
  $result = sql_query("SELECT * FROM {$db_prefix}online") or die(mysql_error());
    while($row = mysql_fetch_assoc($result)) {
      // Delete this row if it is them
      // Or if this is an expired row, delete it too
      if($row['ip']==$user['ip'])
        sql_query("DELETE FROM {$db_prefix}online WHERE `ip` = '{$row['ip']}'");
      elseif(($row['last_active']+($settings['login_threshold']*60))<time()) {
        sql_query("DELETE FROM {$db_prefix}online WHERE `ip` = '{$row['ip']}'");
      }
    }
  // Insert their information into the database
  sql_query("INSERT INTO {$db_prefix}online (`user_id`,`ip`,`page`,`last_active`) VALUES('{$user['id']}','{$user['ip']}','{$action_or_page}','".time()."')") or die(mysql_error());
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

function bbc($str) {
global $theme_dir, $theme_url, $settings;
  
  // Process newline characters
  $str = strtr($str, array("\r\n" => "\n", "\r" => "\n"));
  
  // Process BBCode
  $simple_search = array(
    '/\[b\](.*?)\[\/b\]/is',
    '/\[i\](.*?)\[\/i\]/is',
    '/\[u\](.*?)\[\/u\]/is',
    '/\[s\](.*?)\[\/s\]/is',
    '/\[url\]((http:\/\/|ftp:\/\/|https:\/\/|ftps:\/\/).*?)\[\/url\]/is',
    '/\[url="?((http:\/\/|ftp:\/\/|https:\/\/|ftps:\/\/).*?)"?\](.*?)\[\/url\]/is',
    '/\[code\](.*?)\[\/code\]/is',
    '/\[quote\](.*?)\[\/quote\]/is',
    '/\[quote by="?(.*?)"?\](.*?)\[\/quote\]/is',
    '/\[br\]/is',
    '/\[hr\]/is',
    '/\[img\]((http:\/\/|https:\/\/).*?)\[\/img\]/is',
    '/\[img="?((http:\/\/|https:\/\/).*?)"?\](.*?)\[\/img\]/is'
  );
  $simple_replace = array(
    '<strong>$1</strong>',
    '<em>$1</em>',
    '<span style="text-decoration: underline;">$1</span>',
    '<del>$1</del>',
    '<a href="$1">$1</a>',
    '<a href="$1">$3</a>',
    '<div class="code"><p>$1</p></div>',
    '<p style="font-weight: bold; padding: 0px; margin: 0px;">Quote:</p><blockquote style="padding: 0px; margin: 0px;">$1</blockquote>',
    '<p style="font-weight: bold; padding: 0px; margin: 0px;">Quote from $1:</p><blockquote style="padding: 0px; margin: 0px;">$2</blockquote>',
    '<br />',
    '<hr />',
    '<img src="$1" alt=""/>',
    '<img src="$1" alt="$3"/>'
  );
  $str = preg_replace($simple_search, $simple_replace, $str);
  
  // Process emoticons
  $str = '<bbcode-process>'.$str.'</bbcode-process>';
  
  global $smileys;
  require_once($theme_dir.'/'.$settings['theme'].'/emoticons/emoticons.php');
  
  $sm_search = array();
  $sm_replace = array();
  foreach($smileys as $smiley => $file) {
    $sm_search[] = ' '.$smiley.' ';
    $sm_replace[] = ' <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" /> ';
    $sm_search[] = "\n".$smiley."\n";
    $sm_replace[] = "\n".'<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" />'."\n";
    $sm_search[] = ' '.$smiley."\n";
    $sm_replace[] = ' <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" />'."\n";
    $sm_search[] = "\n".$smiley.' ';
    $sm_replace[] = "\n".'<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" /> ';
    $sm_search[] = '<bbcode-process>'.$smiley.' ';
    $sm_replace[] = '<bbcode-process><img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" /> ';
    $sm_search[] = '<bbcode-process>'.$smiley."\n";
    $sm_replace[] = '<bbcode-process><img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" />'."\n";
    $sm_search[] = ' '.$smiley.'</bbcode-process>';
    $sm_replace[] = ' <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" /></bbcode-process>';
    $sm_search[] = "\n".$smiley.'</bbcode-process>';
    $sm_replace[] = "\n".'<img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$file.'" alt="'.$smiley.'" class="emoticon" /></bbcode-process>';
  }
  $str = str_replace($sm_search, $sm_replace, $str);
  
  $str = substr($str,16,strlen($str)-33);
  
  // Process newlines
  $str = strtr($str, array("\n" => "<br />"));
  
  // Return finished product
  return $str;
}

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
    'sticky_topic' => false
  ); 
// !!! This function changes (forcefully) the separator from & to ;
// !!! This function needs improvement!!!!
function cleanQuery() {
global $_REQUEST, $_GET;
  if(!empty($_SERVER['QUERY_STRING'])) {
  $matches = explode(";", $_SERVER['QUERY_STRING']);
    if(count($matches)) {
      foreach($matches as $arg) {
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
?>