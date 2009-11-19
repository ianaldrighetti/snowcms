<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# CoreCMS.php has some of the core stuffs... the stuff
# that is pretty much vital to Snow's core operation
#
# void init_member();
#   - Loads up the current. Gets the information from the cookie
#     and checks to see if the info is valid.
#   - It loads up other information about the current user like
#     their IP, Session ID, and other permission stuff :)
#
# void db_connect();
#   - Connects to your database, whether it be MySQL, SQLite or
#     whatever it is that SnowCMS supports.
#
# void load_settings();
#   - Loads up all the settings from the settings table
#
# void load_menu();
#   - When called on it will load the menu from the menus
#     tables, the menu is added into the $settings['menu']
#     array, and inside contains more arrays, their indices
#     are their Menu IDs
#
# void clean_query();
#   - This function is great :D and well without it some things
#     may actually not work, or at least correctly.
#   - clean_query() turns the query separator from & to ; why? I think
#     it looks better then the ugly &'s ;)
#
# array remove_magic(array $arrayToClean);
#   - This is a helper function of cleanQuery(); when Magic Quotes
#     is determined to be on.
#   array $arrayToClean - The array to have slashes stripped from
#   returns array - This is the stripped array
#
# void log_online([bool $force_flush = false]);
#   bool $force_flush - If set to true, the function will for sure flush timed
#                       out sessions, otherwise there is a 25% chance it will happen.
#   - Handles the who's online for your SnowCMS powered website :)
#
# void handle_gzip();
#   - Handles GZip compressed output.
#
# void need_task();
#   - Simply flags a variable ($page['run_task']) and the things
#     in the tasks table to be ran, :) it is sort of like crons
#     just don't rely on them because if someone doesn't access
#     the page, it won't be ran until it is :P
#
# void snow_close();
#   - Put calls and what not in this function to be executed before SnowCMS stops.
#

function init_member()
{
  global $base_url, $theme_url, $db, $l, $page, $settings, $user;

  # Just a couple things.
  $member_id = 0;
  $passwrd = '';
  $user_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

  # If your session variables are empty, maybe the cookie isn't.
  if(empty($_SESSION['member_id']) && empty($_SESSION['passwrd']) && !empty($_COOKIE[$settings['cookie_name']]))
  {
    # Try to turn the cookie back into an array
    @list($data['member_id'], $data['passwrd']) = explode(':', $_COOKIE[$settings['cookie_name']]);

    # So is the things we need set?
    if(!empty($data['member_id']) && (string)$data['member_id'] == (string)(int)$data['member_id'] && !empty($data['passwrd']))
    {
      # Ya, its all set :)
      $member_id = $data['member_id'];
      $passwrd = $data['passwrd'];

      # Now we can save them to the session stuffs.
      $_SESSION['member_id'] = $member_id;
      $_SESSION['passwrd'] = $passwrd;
    }
  }
  else
  {
    # Check your session data against your cookie data, that way
    # its a bit harder for someone to steal your PHPSESSID :)
    if(!empty($_COOKIE[$settings['cookie_name']]))
    {
      # So try to set the data back into an array.
      @list($data['member_id'], $data['passwrd']) = explode(':', $_COOKIE[$settings['cookie_name']]);

      if(!empty($data['member_id']) && !empty($data['passwrd']) && !empty($_SESSION['member_id']) && !empty($_SESSION['passwrd']) && $data['member_id'] == $_SESSION['member_id'] && $data['passwrd'] == $_SESSION['passwrd'])
      {
        # All seems to be in order...
        $member_id = $_SESSION['member_id'];
        $passwrd = $_SESSION['passwrd'];
      }
    }
  }

  # So it cannot be a 0...
  if($member_id > 0)
  {
    # So they might be a member, lets check and see...
    $result = $db->query("
      SELECT
        mem.member_id, mem.loginName, mem.passwrd, mem.email, mem.displayName, mem.reg_time,
        mem.reg_ip, mem.last_ip, mem.group_id, mem.post_group_id, mem.num_posts, mem.birthdate, mem.avatar,
        mem.signature, mem.is_activated, mem.is_banned, mem.suspended, mem.language, mem.timezone,
        mem.dst, mem.total_pms, mem.unread_pms, mem.pm_size, mem.site_name, mem.site_url, mem.show_email,
        mem.icq, mem.aim, mem.msn, mem.msn, mem.yim, mem.gtalk, mem.format_datetime, mem.format_date,
        mem.format_time, mem.theme, mem.preference_quick_reply, mem.preference_avatars,
        mem.preference_signatures, mem.preference_post_images, mem.preference_emoticons, mem.preference_recently_online,
        mem.preference_return_topic, mem.preference_pm_display, mem.preference_thousands_separator, mem.preference_decimal_point,
        mem.preference_today_yesterday, mem.per_page_topics,
        mem.per_page_posts, mem.per_page_news, mem.per_page_downloads,
        mem.per_page_comments, mem.per_page_members, mem.menus_last_cached, mem.adminSc, mem.last_online,
        grp.group_id, grp.group_name, grp.allowed_pm_size, grp2.group_id AS post_group_id,
        grp2.group_name AS post_group_name, grp2.allowed_pm_size AS post_allowed_pm_size
      FROM {$db->prefix}members AS mem
        LEFT JOIN {$db->prefix}membergroups AS grp ON grp.group_id = mem.group_id
        LEFT JOIN {$db->prefix}membergroups AS grp2 ON grp2.group_id = mem.post_group_id
      WHERE mem.member_id = %member_id AND mem.passwrd = %passwrd AND mem.is_activated = 1
            AND mem.suspended < %time AND mem.is_banned = 0
      LIMIT 1",
      array(
        'member_id' => array('int', $member_id),
        'passwrd' => array('string-40', $passwrd),
        'time' => array('int', time_utc()),
      ));
    
    # So are they a member or not?
    if($db->num_rows($result))
    {
      $row = $db->fetch_assoc($result);
      
      # Now we need to quickly reload the main language file with this user's custom language, if applicable
      if($row['language'] != $settings['default_language'])
      {
        $user['language'] = $row['language'];
        language_load('main');
      }
      
      # Why yes, yes they are.
      # So build this huge array of arrayness! :D
      $user = array(
        'id' => $row['member_id'],
        'name' => $row['displayName'],
        'password' => $row['passwrd'],
        'email' => $row['email'],
        'username' => $row['loginName'],
        'group' => array(
                     'id' => $row['group_id'],
                     'name' => $row['group_name'],
                   ),
        'post_group' => array(
                          'id' => (int)$row['post_group_id'],
                          'name' => $row['post_group_name'],
                        ),
        # Your avatar... Might be local, might not be :P
        'avatar' => (mb_substr($row['avatar'], 0, 7) == 'http' ? $row['avatar'] : (mb_substr($row['avatar'], 0, 5) == 'local' ? $base_url. '/index.php?action=avatar;u='. $row['member_id'] : false)),
        'is_guest' => false,
        'is_logged' => true,
        'is_admin' => $row['group_id'] == 1 ? true : false,
        'is_moderator' => false,
        'posts' => $row['num_posts'],
        'total_pms' => $row['total_pms'],
        'unread_pms' => $row['unread_pms'],
        'pm_size' => $row['pm_size'],
        # If any of their groups have 0 PM space or if they're an admin...
        # Then we set the allowed PM size to 0 (a.k.a. as much as they want)
        # Otherwise we set it to the highest of the groups they're in
        'allowed_pm_size' => $row['allowed_pm_size'] === 0 && $row['post_allowed_pm_size'] === 0 || $row['group_id'] == 1 ? 0 : max($row['allowed_pm_size'], $row['post_allowed_pm_size']),
        'site' => array(
                    'name' => $row['site_name'],
                    'href' => $row['site_url'],
                  ),
        'language' => $row['language'],
        'signature' => bbc($row['signature'], true, 'signature_id-'. $row['member_id']),
        'show_email' => !empty($row['show_email']),
        # All your messengers :P
        'aim' => $row['aim'],
        'msn' => $row['msn'],
        'yim' => $row['yim'],
        'gtalk' => $row['gtalk'],
        'icq' => $row['icq'] ? $row['icq'] : null,
        'find_in_set' => $row['group_id'] == 1 ? '1 = 1' : '(FIND_IN_SET('. $row['group_id']. ', alias.who_view) OR FIND_IN_SET('. (int)$row['post_group_id']. ', alias.who_view))',
        # Theme and view preferences
        'theme' => $row['theme'],
        'format_datetime' => $row['format_datetime'],
        'format_date' => $row['format_date'],
        'format_time' => $row['format_time'],
        'timezone' => timezone_get($row['timezone'], $row['dst']),
        'preference' => array(
                        'quick_reply' => $row['preference_quick_reply'],
                        'avatars' => $row['preference_avatars'],
                        'signatures' => $row['preference_signatures'],
                        'post_images' => $row['preference_post_images'],
                        'emoticons' => $row['preference_emoticons'],
                        'recently_online' => $row['preference_recently_online'],
                        'return_topic' => $row['preference_return_topic'],
                        'pm_display' => $row['preference_pm_display'],
                        'thousands_separator' => $row['preference_thousands_separator'],
                        'decimal_point' => $row['preference_decimal_point'],
                        'today_yesterday' => $row['preference_today_yesterday'],
                      ),
        'per_page' => array(
                        'topics' => !empty($row['per_page_topics']) ? $row['per_page_topics'] : $settings['per_page_topics'],
                        'posts' => !empty($row['per_page_posts']) ? $row['per_page_posts'] : $settings['per_page_posts'],
                        'news' => !empty($row['per_page_news']) ? $row['per_page_news'] : $settings['per_page_news'],
                        'downloads' => !empty($row['per_page_downoads']) ? $row['per_page_downloads'] : $settings['per_page_downloads'],
                        'comments' => !empty($row['per_page_comments']) ? $row['per_page_comments'] : $settings['per_page_comments'],
                        'members' => !empty($row['per_page_members']) ? $row['per_page_members'] : $settings['per_page_members'],
                      ),
        'theme_url' => $theme_url. '/'. $row['theme'],
        'images_url' => $theme_url. '/'. $row['theme']. '/images',
        'numberformat_hash' => sha1($row['preference_thousands_separator']. $row['preference_thousands_separator']),
        'last_online' => $row['last_online'],
        # Deliberately from database and not from $user_ip, but this will change in a bit
        'ip' => $row['last_ip'],
        'sc' => session_id(),
        'permissions' => array(),
        'menus_last_cached' => $row['menus_last_cached'],
        'adminSc' => $row['adminSc'],
        'loginHash' => '',
      );
      
      # Update last online, total online time and last IP address
      $db->query("
        UPDATE {$db->prefix}members
        SET last_online = %last_online, time_online = time_online + %time_online, last_ip = %last_ip
        WHERE member_id = %member_id",
        array(
          'last_online' => array('int', time_utc()),
          'time_online' => array('int', time_utc() <= $user['last_online'] + 60 * 10 ? time_utc() - $user['last_online'] : 0),
          'last_ip' => array('string', $user_ip),
          'member_id' => array('int', $user['id']),
        ));
      
      # Check if their IP has changed from their last used one
      if($user['ip'] != $user_ip)
      {
        # Update the last used time of the previous IP
        $db->query("
          UPDATE {$db->prefix}ip_logs
          SET last_time = %last_online
          WHERE ip = %ip AND member_id = %member_id
          LIMIT 1",
          array(
            'ip' => array('string', $user['ip']),
            'member_id' => array('string', $user['id']),
            'last_online' => array('int', $user['last_online']),
          ));
        
        # Check if the nwe IP has been used by this member before
        $result = $db->query("
          SELECT
            TRUE
          FROM {$db->prefix}ip_logs
          WHERE ip = %ip AND member_id = %member_id
          LIMIT 1",
          array(
            'ip' => array('string', $user['ip']),
            'member_id' => array('string', $user['id']),
          ));
        $ip_exists = $db->fetch_assoc($result);
        
        if($ip_exists)
        {
          # It has been used by them before, so let's update its last used time
          $db->query("
            UPDATE {$db->prefix}ip_logs
            SET last_time = %now
            WHERE ip = %ip AND member_id = %member_id
            LIMIT 1",
            array(
              'ip' => array('string', $user['ip']),
              'member_id' => array('string', $user['id']),
              'now' => array('int', time_utc()),
            ));
        }
        else
        {
          # It's the first time they've used this IP, so let's insert it
          $db->insert('insert', $db->prefix. 'ip_logs',
            array(
              'ip' => 'string-16', 'member_id' => 'int', 'first_time' => 'int',
              'last_time' => 'int',
            ),
            array(
              $user['ip'], $user['id'], time_utc(), time_utc(),
            ),
            array());
        }
        
        # Update $user['ip'] for the change
        $user['ip'] = $user_ip;
      }
    }
  }

  # A guest..?
  if(empty($user))
    # So they are a guest, we don't need everything though :P
    $user = array(
      'id' => 0,
      'name' => $l['guest_name'],
      'password' => false,
      'email' => false,
      'username' => $l['guest_name'],
      'group' => array(
                   'id' => -1,
                   'name' => $l['guest_group'],
                 ),
      'post_group' => array(
                        'id' => 0,
                        'name' => false,
                      ),
      'is_guest' => true,
      'is_logged' => false,
      'is_admin' => false,
      'is_moderator' => false,
      'language' => !empty($settings['language']) ? $settings['language'] : 'english',
      'find_in_set' => 'FIND_IN_SET(-1, alias.who_view)',
      'theme' => $settings['theme'],
      'format_datetime' => $settings['format_datetime'],
      'format_date' => $settings['format_date'],
      'format_time' => $settings['format_time'],
      'timezone' => timezone_get($settings['timezone'], $settings['dst']),
      'preference' => array(
                      'quick_reply' => $settings['preference_quick_reply'],
                      'avatars' => $settings['preference_avatars'],
                      'signatures' => $settings['preference_signatures'],
                      'post_images' => $settings['preference_post_images'],
                      'emoticons' => $settings['preference_emoticons'],
                      'recently_online' => $settings['preference_recently_online'],
                      'return_topic' => $settings['preference_return_topic'],
                      'thousands_separator' => $settings['preference_thousands_separator'],
                      'decimal_point' => $settings['preference_decimal_point'],
                      'today_yesterday' => $settings['preference_today_yesterday'],
                    ),
      'per_page' => array(
                      'topics' => $settings['per_page_topics'],
                      'posts' => $settings['per_page_posts'],
                      'news' => $settings['per_page_news'],
                      'downloads' => $settings['per_page_downloads'],
                      'comments' => $settings['per_page_comments'],
                      'members' => $settings['per_page_members'],
                    ),
      'theme_url' => $theme_url. '/'. $settings['theme'],
      'images_url' => $theme_url. '/'. $settings['theme']. '/images',
      'numberformat_hash' => sha1($settings['preference_thousands_separator']. $settings['preference_decimal_point']),
      'ip' => $user_ip,
      'sc' => 'ip'. $user_ip,
      'permissions' => array(),
      'menus_last_cached' => $settings['guest_menus_last_cached'],
      'adminSc' => '',
      'loginHash' => '',
    );

  # We don't want to reset this if they are doing something
  # AJAXy or JSy or anythingy :P
  if(empty($_GET['action']) || !in_array($_GET['action'], array('interface', 'tasks', 'keepalive')))
  {
    # Move the sessions around... loginHash becomes old_loginHash
    # and then loginHash gets a new one :D!
    $_SESSION['old_loginHash'] = !empty($_SESSION['loginHash']) ? $_SESSION['loginHash'] : '';
    $_SESSION['loginHash'] = rand_string(mt_rand(10, 20));

    # The users loginHash is the old one...
    $user['loginHash'] = $_SESSION['loginHash'];

    # But the page one is in the JS vars.
    if(!isset($page['js_vars']))
      $page['js_vars'] = array();

    $page['js_vars']['loginHash'] = $_SESSION['loginHash'];
  }
}

function db_connect()
{
  global $db, $db_type, $db_host, $db_user, $dbass, $db_name;
  global $tbl_prefix, $db_persistent, $source_dir, $user;

  # Now lets see if the database engine exists...
  if(file_exists($source_dir. '/engines/'. $db_type. '.engine.php'))
  {
    require_once($source_dir. '/engines/'. $db_type. '.engine.php');

    # So lets see if the class exists... It better be defined ._.
    if(isset($db_class) && class_exists($db_class, false))
    {
      # Let's make a new instance shall we?
      $db = new $db_class();

      # Let's try to connect, shall we?
      if(!$db->connect())
      {
        # Oh noes! Connection failed!
        trigger_error('Could not connect to the database server, Engine: '. $db->sql_name. ', Error: '. $db->error(), E_USER_ERROR);
      }
    }
    # Is $db_class not set?
    elseif(!isset($db_class))
      trigger_error('Undefined variable db_class in '. $db_type. '.engine.php', E_USER_ERROR);
    # So then that means it's an invalid class
    else
      trigger_error('The class '. $db_class. ' is nonexistent in '. $db_type. '.engine.php', E_USER_ERROR);
  }
  else
    # Oh noes! It doesn't!
    trigger_error('The database engine "'. $db_type. '" was not found', E_USER_ERROR);
}

function load_settings()
{
  global $db, $theme_url, $settings;

  # Get them out ^^
  # Still trying to figure out how to get this to be cached
  # you know since the cache_get function USES $settings
  $result = $db->query("SELECT * FROM {$db->prefix}settings", array());
  while($row = $db->fetch_assoc($result))
    $settings[$row['variable']] = $row['value'];

  # Makes life easier :)
  $settings['theme_url'] = $theme_url. '/'. $settings['theme'];

  # Create another useful setting variable.
  $settings['images_url'] = $settings['theme_url']. '/images';

  # Default stuff...
  $settings['default_theme_url'] = $theme_url. '/default';
  $settings['default_images_url'] = $theme_url. '/default/images';
}

function load_menu()
{
  global $base_url, $db, $l, $settings, $user;

  # Menu time ^^
  # After we check if its cached or not.
  if(!empty($settings['cache_enabled']) && ($cache = cache_get('menu-'. $user['id'])) != null && $settings['menus_last_updated'] < $user['menus_last_cached'])
    # Its cached, so put it into the ['menu'] array
    $settings['menu'] = $cache;
  else
  {
    # Either caching isn't enabled or we need to recache it
    $result = $db->query("
      SELECT
        l.link_id, l.link_name, l.link_order, l.link_href,
        l.link_target, l.link_menu, l.link_follow, l.who_view
      FROM {$db->prefix}menus AS l
      WHERE ". strtr($user['find_in_set'], array('alias' => 'l')). "
      ORDER BY l.link_order ASC",
      array());

    # Loopiness :P (Why are there 5 predefined arrays? Because there can be 5 menus ;))
    $settings['menu'] = array(1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array());
    while($row = $db->fetch_assoc($result))
    {
      # Add it to the array...
      $settings['menu'][$row['link_menu']][] = array(
        'id' => $row['link_id'],
        'name' => $row['link_name'],
        'href' => $row['link_href'],
        'link' => '<a href="'. $row['link_href']. '" title="'. $row['link_name']. '"'. ($row['link_follow'] ? '' : ' rel="nofollow"'). ''. ($row['link_target'] ? ' target="_blank"' : ''). '>'. $row['link_name']. '</a>',
        'order'=> $row['link_order'],
        'target' => $row['link_target'],
        'menu' => $row['link_menu'],
        'follow' => $row['link_follow'],
        'who_view' => $row['who_view'],
      );
    }

    # Needs caching?
    if($settings['cache_enabled'])
    {
      cache_save('menu-'. $user['id'], $settings['menu'], 1800);
      
      if($user['is_logged'])
        $db->query("
          UPDATE {$db->prefix}members
          SET menus_last_cached = %menus_last_cached
          WHERE member_id = %member_id
          LIMIT 1",
          array(
            'menus_last_cached' => array('int', time_utc()),
            'member_id' => array('int', $user['id']),
          ));
      else
        update_settings(array('guest_menus_last_cached' => time_utc()));
    }
  }
}

function clean_query()
{
  global $_COOKIE, $_GET, $_POST, $_SERVER, $_REQUEST;

  # Unset some things in $GLOBALS we don't want...
  # This could stop some possible register_globals attacks :P
  # WHICH YOU SHOULD HAVE OFF! BAD!
  # First some reserved ones though.
  $reserved = array('_COOKIE','_ENV','_FILES','GLOBALS','_POST','_SERVER','_SESSION','db_type','db_host','db_user','db_pass','db_name','tbl_prefix','db_persistent','avatar_dir','base_dir','base_url','cache_dir','download_dir','emoticon_dir','emoticon_url','snowcms_installed','source_dir','started_time','theme_dir','theme_url','reserved');

  # Now loop through them, and unset them :)
  foreach($GLOBALS as $GKEY => $GVALUE)
    if(!in_array($GKEY, $reserved))
      unset($GLOBALS[$GKEY]);

  # Unset just a couple more things...
  unset($GLOBALS['GKEY'], $GLOBALS['GVALUE']);

  # Reset $_GET and $_REQUEST
  $_GET = array();
  $_REQUEST = array();

  # Just incase, add the $_POST values back to $_REQUEST
  # but just to let you know, they might be replaced with
  # the ones from the query string in the url ;)
  # Of course only if $_POST isn't empty...
  if(count($_POST))
    foreach($_POST as $key => $value)
      $_REQUEST[$key] = $value;

  # Possibly need to get QUERY_STRING elsewhere..?
  if(empty($_SERVER['QUERY_STRING']))
    $_SERVER['QUERY_STRING'] = getenv('QUERY_STRING');

  # Now lets go :D If ; isn't the separator already.
  if(!empty($_SERVER['QUERY_STRING']) && trim(@ini_get('arg_separator.input')) != ';')
  {
    # The Query String, which we need now more then ever.
    $query_string = $_SERVER['QUERY_STRING'];

    # EXPLOSION! WEEEEEEEEEE!
    $query_strings = mb_split('[;&]', urldecode($query_string));
    $new = array();

    # Now loop through them...
    $total = array();
    foreach($query_strings as $query)
      if(preg_match('/^([^=]+)([=](.*))*/', $query, $parts))
      {
        if(isset($new[$parts[1]]) && mb_strpos($parts[1], '[]') !== false)
        {
          if(!isset($total[$parts[1]]))
            $total[$parts[1]] = 0;
          $total[$parts[1]]++;
          $new[str_replace('[]', '['. $total[$parts[1]]. ']', $parts[1])] = !empty($parts[2]) ? $parts[2] : '';
        }
        else
          $new[$parts[1]] = !empty($parts[2]) ? $parts[2] : '';
      }

    # So now if anything is new then we need to put them
    # into the $_GET and $_REQUEST variables for use :)
    if(count($new))
    {
      foreach($new as $key => $value)
      {
        # Bug Fix D:
        $value = mb_substr($value, 1, mb_strlen($value));

        # Now set them...
        # But wait! Is it an array..?
        if(mb_strpos($key, ']') !== false && mb_strpos($key, ']') == mb_strlen($key) - 1)
        {
          # We also need a [ that is at a lower position than ]
          if(mb_strpos($key, '[') !== false && mb_strpos($key, ']') > mb_strpos($key, '['))
          {
            @list($key, $pos) = explode('[', $key, 2);
            # Make our array now, if it doesn't yet exist!
            if(!isset($_GET[$key]))
              $_GET[$key] = array();
            if(!isset($_REQUEST[$key]))
              $_REQUEST[$key] = array();

            if(mb_substr($pos, 0, 1) == ']')
            {
              $_GET[$key][] = $value;
              $_REQUEST[$key][] = $value;
            }
            else
            {
              $sub_key = mb_substr($pos, 0, mb_strlen($pos) - 1);
              $_GET[$key][$sub_key] = $value;
              $_REQUEST[$key][$sub_key] = $value;
            }

            # Don't go any further!
            continue;
          }
        }

        # Did we get here..? Then...
        # Nope, just a variable...
        $_GET[$key] = $value;
        $_REQUEST[$key] = $value;
      }
    }
  }

  # Magic Quotes on perhaps..?
  if((function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == 1) || @ini_get('magic_quotes_sybase'))
  {
    # But it could be Sybase to :P!!! Which replaces ' with ''
    # Now remove them.
    $_COOKIE = remove_magic($_COOKIE);
    $_GET = remove_magic($_GET);
    $_POST = remove_magic($_POST);
    $_REQUEST = remove_magic($_REQUEST);
    $_SERVER = remove_magic($_SERVER);
  }

  # Uhh... Don't try it!!! (Though I have tried I guess it gets unset) :P
  if(isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS']))
    die('Hacking Attempt...');
}

function remove_magic($arrayToClean)
{
  # Not an array..? Whats wrong with you!
  if(is_array($arrayToClean))
  {
    # Make a temporary array which we will use.
    $tmp = array();

    # Loop through all of them in the original array, if any.
    if(count($arrayToClean))
    {
      foreach($arrayToClean as $key => $value)
      {
        # Clean the key XD
        $key = stripslashes($key);
        # Is it an array? If so, go at it once more.
        if(is_array($value))
        {
          foreach($value as $sub_key => $sub_value)
          {
            $tmp[$key][stripslashes($sub_key)] = stripslashes($sub_value);
          }
        }
        else
          # Just a regular one
          $tmp[$key] = stripslashes($value);
      }
    }

    # Give it back now.
    return $tmp;
  }
  else
    return false;
}

function log_online($force_flush = false)
{
  global $db, $settings, $user;


  # Do we want to delete the old ones..?
  if((empty($force_flush) && mt_rand(1, 4) == 3) || !empty($force_flush))
  {
    # So yeah... we want to delete the old ones...
    $db->query("
      DELETE FROM {$db->prefix}online
      WHERE last_active < %break_point",
    array(
      'break_point' => array('int', time_utc() - ($settings['online_timeout'] * 60)),
    ));
  }

  # This shouldn't have been ran :P
  if(!empty($_GET['action']) && $_GET['action'] == 'tasks')
    return false;

  # Get total users online
  $result = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}online",
    array());
  @list($users_online) = $db->fetch_row($result);

  # Check for most online today or ever
  if($users_online > $settings['most_online_ever'])
  {
    # Most online ever! :D
    update_settings(array('most_online_ever' => $users_online), array('most_online_today' => $users_online));
  }
  elseif($users_online > $settings['most_online_today'])
  {
    # Most online today :)
    update_settings(array('most_online_today' => $users_online));
  }

  # Hold on though, is this a KEEP ALIVE request..?
  if(!empty($_GET['action']) && $_GET['action'] == 'keepalive')
  {
    # Simply update the time you were last seen :)
    $db->query("
      UPDATE IGNORE {$db->prefix}online
      SET last_active = %current_time
      WHERE member_id = %member_id AND session_id = %session_id
      LIMIT 1",
      array(
        'current_time' => array('int', time_utc()),
        'member_id' => array('int', $user['id']),
        'session_id' => array('string-40', $user['sc']),
      ));

    # Quit... :P
    exit;
  }

  # Update, well replace, the current one of the users...
  # But first we need some dataz :)
  $data = array();

  # Are you in the forum...?
  $data['in_forum'] = defined('InSnowForum') ? (bool)InSnowForum : false;

  # We need the $_GET as well...
  $data['GET'] = $_GET;

  # The URI too... :P
  $data['uri'] = $_SERVER['REQUEST_URI'];

  # Like their User Agent :P
  $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

  # Serialize :)
  $data = serialize($data);
  $db->insert('replace', $db->prefix. 'online',
    array(
      'session_id' => 'string-40', 'last_active' => 'int',
      'member_id' => 'int', 'ip' => 'string-16', 'data' => 'text'
    ),
    array(
      $user['sc'], time_utc(),
      $user['id'], $user['ip'], $data
    ),
    array('session_id'));

  return true;
}

function handle_gzip()
{
  global $settings;

  # Check if GZip is on and supported by the server
  if($settings['gz_compressed'] && function_exists('ob_gzhandler') && !headers_sent() && !ob_get_length())
  {
    # Check if browser is IE5 or IE6, they think they can handle GZip, but we know better.
    # Some versions of Opera want us to think they're IE, they aren't :P
    if(preg_match('/MSIE [56]/', $_SERVER['HTTP_USER_AGENT']) && !mb_strpos($_SERVER['HTTP_USER_AGENT'], 'Opera'))
      ob_start();
    else
      # GZip support :)
      ob_start('ob_gzhandler');
  }
  # No GZip
  else
    ob_start();
}

function need_task()
{
  global $db, $page, $settings;

  $page['run_task'] = false;

  # Only if tasks are enabled, and if you haven't ran it within 1/3 of the online timeout.
  if(!empty($settings['enable_tasks']) && (!isset($_SESSION['ran_task_last']) || ((int)$_SESSION['ran_task_last'] + ($settings['online_timeout'] * 20)) < time_utc()))
  {
    # Set the last time this session ran a task. We don't want to do it too often...
    $_SESSION['ran_task_last'] = time_utc();

    # Just select the ones that are enabled :)
    $result = $db->query("
      SELECT
        task_id, last_ran, run_every, queued, enabled
      FROM {$db->prefix}tasks
      WHERE enabled = 1 AND (last_ran + run_every) < %now",
      array(
        'now' => array('int', time_utc()),
      ));

    # Now loop through the rows
    $task_array = array();
    while($row = $db->fetch_assoc($result))
      $task_array[] = $row['task_id'];

    # Any? :P
    if(count($task_array))
    {
      # Flag the run_task variable :)
      $page['run_task'] = true;

      # Oh, and their Session :P
      $_SESSION['run_task'] = true;

      # Now update the row for it to be queued!
      $db->query("
        UPDATE {$db->prefix}tasks
        SET queued = 1
        WHERE task_id IN(%tasks)
        LIMIT %total_tasks",
      array(
        'tasks' => array('int_array', $task_array),
        'total_tasks' => array('int', count($task_array)),
      ));
    }
  }
}

function snow_close()
{
  global $db, $page, $settings;

  # Actually save all the updates you made :)
  update_settings_close();

  # Gotta do this, or else session data won't save right!
  session_write_close();
}
?>