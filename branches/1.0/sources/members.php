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
# Handles member data which can be used to display member information.
#
# bool members_load(mixed $members[, string $level = 'normal']);
#   mixed $members - Either an array of member IDs to load, or a single one.
#   string $level - Defines the amount of information which is loaded. Acceptable
#                   values are basic, normal, and extended.
#
# returns bool - The only time anything other than TRUE would be returned
#                is if the level you have entered is not understood, or if
#                you supplied no members to load (Like an empty array).
#
# NOTE: Please realize this function does not check permissions to see if the
#       current user can few such information. That is up to you.
#
# array members_info(int $member_id[, string $level = 'normal']);
#   int $member_id - The ID of the member you want the information returned.
#   string $level - The level of information you want, either basic, normal or extended.
#
# returns array - If the information has been loaded, the array is returned, however
#                 if you have yet to have the information loaded by members_load()
#                 or if the member does not exist, FALSE will be returned.
#
# void members_clear_id(int $member_id);
#   !!!
#

function members_load($members, $level = 'normal')
{
  global $base_url, $db, $l, $page, $settings, $source_dir;

  $level = mb_strtolower($level);

  # Only 3 levels I personally know of :P
  if(!in_array($level, array('basic', 'normal', 'extended')))
    return false;

  # We store the data here... Why load it over and over? :P
  if(!isset($page['member_data']) || !is_array($page['member_data']))
    $page['member_data'] = array();

  # So you don't have an array of members you want loaded? Only one you say? Fine.
  if(!is_array($members))
    $members = array($members);


  # First unset any members that are already loaded. Waste of time really...
  if(count($members))
  {
    # Let's be a *little* efficient and count how many members we need to load still
    # you know, after we filter some out.
    $members_to_load = 0;

    foreach($members as $key => $member_id)
    {
      # Make sure it is the SAME level too... (We also can't load guest stuff!!! Or this member could be cached!!!)
      if(empty($member_id) || isset($page['member_data'][$member_id][$level]) || ($cache_data = cache_get('member_data_'. $level. '-'. $member_id)) != null)
      {
        if(isset($cache_data))
        {
          $page['member_data'][$member_id][$level] = $cache_data;
          unset($cache_data);
        }
        unset($members[$key]);
      }
      else
      {
        $members[$key] = (int)$member_id;
        $members_to_load++;
      }
    }
  }
  else
    # No members to load? You silly goose!
    return false;

  # None to load? We won't return false, because you might not have known th(ose|at) member(s)
  # were already loaded... ;)
  if($members_to_load == 0)
    return false;

  # No duplicates!!!
  $members = array_unique($members);
  $members_to_load = count($members);

  # So what we loading? Basic..?
  if($level == 'basic')
  {
    # Oh yeah! Simplicity at its max I suppose :P
    $result = $db->query("
      SELECT
        member_id, loginName, email, displayName, reg_time, last_login, last_online,
        last_ip, group_id, num_posts, num_topics, language, show_email
      FROM {$db->prefix}members
      WHERE member_id IN(%members)
      LIMIT %total_members",
      array(
        'members' => array('int_array', $members),
        'total_members' => array('int', $members_to_load),
      ));
  }
  elseif($level == 'normal')
  {
    # Normal... More than basic, less than extended :P
    $result = $db->query("
      SELECT
        mem.member_id, mem.loginName, mem.email, mem.displayName, mem.reg_time, mem.reg_ip,
        mem.last_login, mem.last_online, mem.last_ip, mem.time_online, mem.group_id,
        mem.num_posts, mem.num_topics, mem.avatar, mem.signature, mem.gender, mem.is_suspended,
        mem.is_banned, mem.language, mem.site_name, mem.site_url, mem.show_email, mem.icq,
        mem.aim, mem.msn, mem.yim, mem.gtalk, grp.group_id, grp.group_name, grp.group_color,
        grp.stars, o.member_id AS is_online, IFNULL(o.member_id, 0) AS is_online
      FROM {$db->prefix}members AS mem
        LEFT JOIN {$db->prefix}membergroups AS grp ON grp.group_id = mem.group_id
        LEFT JOIN {$db->prefix}online AS o ON o.member_id = mem.member_id
      WHERE mem.member_id IN(%members)
      LIMIT %total_members",
      array(
        'members' => array('int_array', $members),
        'total_members' => array('int', $members_to_load),
      ));
  }
  else
  {
    # Extended information, quite a bit, sure you need it all..? :P
    $result = $db->query("
      SELECT
        mem.member_id, mem.loginName, mem.email, mem.displayName, mem.reg_time, mem.reg_ip,
        mem.last_login, mem.last_online, mem.last_ip, mem.time_online, mem.group_id,
        mem.post_group_id, mem.num_posts, mem.num_topics, mem.birthdate, mem.avatar,
        mem.signature, mem.profile_text, mem.custom_title, mem.location, mem.gender,
        mem.timezone, mem.dst, mem.is_suspended, mem.is_banned, mem.language, mem.site_name,
        mem.site_url, mem.show_email, mem.icq, mem.aim, mem.msn, mem.yim, mem.gtalk,
        grp.group_id, grp.group_name, grp.group_color, grp.stars, grp2.group_id AS post_group_id,
        grp2.group_name AS post_group_name, grp2.group_color AS post_group_color,
        grp2.stars AS post_group_stars, o.member_id AS is_online, IFNULL(o.member_id, 0) AS is_online
      FROM {$db->prefix}members AS mem
        LEFT JOIN {$db->prefix}membergroups AS grp ON grp.group_id = mem.group_id
        LEFT JOIN {$db->prefix}membergroups AS grp2 ON grp2.group_id = mem.post_group_id
        LEFT JOIN {$db->prefix}online AS o ON o.member_id = mem.member_id
      WHERE mem.member_id IN(%members)
      LIMIT %total_members",
      array(
        'members' => array('int_array', $members),
        'total_members' => array('int', $members_to_load),
      ));
  }

  # We will use this for caching!!!
  $cache = array();

  # So now lets load up that information XD!!!
  while($row = $db->fetch_assoc($result))
  {
    $member = array();

    # Load all the basic stuff :)
    $member['id'] = $row['member_id'];
    $member['name'] = $row['displayName'];
    $member['username'] = $row['loginName'];
    $member['email'] = $row['email'];
    $member['registration'] = array(
      'time' => $row['reg_time'],
      'date' => timeformat($row['reg_time']),
    );
    $member['last_login'] = array(
      'time' => $row['last_login'],
      'date' => timeformat($row['last_login']),
    );
    $member['last_online'] = array(
      'time' => $row['last_online'],
      'date' => timeformat($row['last_online']),
    );
    $member['ip'] = $row['last_ip'];
    $member['group'] = array(
      'id' => $row['group_id'],
    );
    $member['num'] = array(
      'posts' => $row['num_posts'],
      'topics' => $row['num_topics'],
    );
    $member['language'] = mb_convert_case($row['language'], MB_CASE_TITLE);
    $member['show_email'] = !empty($row['show_email']);
    $member['href'] = $base_url. '/index.php?action=profile;u='. $member['id'];

    # Hmmm, basic? You get off the train here...
    if($level == 'basic')
    {
      $page['member_data'][$member['id']][$level] = $member;
      $cache[] = $member;
      continue;
    }

    # Now its time for normal level information.
    $member['reg_ip'] = $row['reg_ip'];
    $member['time_online'] = array(
      'time' => $row['time_online'],
      'parsed' => $row['time_online'], # !!! Turn this into a function in profile.php Myles? Pwez? :)
    );
    $member['avatar'] = return_avatar($row['avatar'], $member['id']);
    $member['signature'] = bbc($row['signature'], true, 'signature_id-'. $member['id']);
    $member['gender'] = ($row['gender'] == 0 ? $l['profile_gender_unspecified'] : ($row['gender'] == 1 ? $l['profile_gender_female'] : $l['profile_gender_male']));
    $member['is_suspended'] = $row['is_suspended'] >= time_utc();
    $member['is_banned'] = !empty($row['is_banned']);
    $member['site'] = array(
      'name' => !empty($row['site_name']) && mb_strtolower(trim($row['site_url'])) != 'http://' && !empty($row['site_url']) ? $row['site_name'] : false,
      'href' => mb_strtolower(trim($row['site_url'])) != 'http://' && !empty($row['site_url']) ? $row['site_url'] : false,
    );
    $member['aim'] = array(
                       'id' => !empty($row['aim']) ? $row['aim'] : false,
                       'href' => 'aim:goim?screenname='. htmlspecialchars($row['aim'], ENT_QUOTES, 'UTF-8'),
                       'link' => '<a href="aim:goim?screenname='. htmlspecialchars($row['aim'], ENT_QUOTES). '" title="'. sprintf($l['aim_messenger'], $row['displayName']). '">'. $l['aim']. '</a>',
                     );
    $member['msn'] = array(
                       'id' => !empty($row['msn']) ? $row['msn'] : false,
                       'href' => 'http://spaces.live.com/profile.aspx?mem='. htmlspecialchars($row['msn'], ENT_QUOTES, 'UTF-8'),
                       'link' => '<a href="http://spaces.live.com/profile.aspx?mem='. htmlspecialchars($row['msn'], ENT_QUOTES). '" title="'. sprintf($l['msn_messenger'], $row['displayName']). '">'. $l['msn']. '</a>',
                     );
    $member['yim'] = array(
                       'id' => !empty($row['yim']) ? $row['yim'] : false,
                       'href' => 'http://webmessenger.yahoo.com/?im='. htmlspecialchars($row['yim'], ENT_QUOTES, 'UTF-8'),
                       'link' => '<a href="http://webmessenger.yahoo.com/?im='. htmlspecialchars($row['yim'], ENT_QUOTES). '" title="'. sprintf($l['yim_messenger'], $row['displayName']). '">'. $l['yim']. '</a>',
                     );

    # Google Talk isn't special like AIM, MSN, YIM or even ICQ (ICQ!!!)
    $member['gtalk'] = array(
                         'id' => !empty($row['gtalk']) ? $row['gtalk'] : false,
                         'href' => false,
                         'link' => false,
                       );
    $member['icq'] = array(
                       'id' => !empty($row['icq']) || (string)$row['icq'] !== (string)(int)$row['icq'] ? $row['icq'] : false,
                       'href' => 'http://www.icq.com/whitepages/about_me.php?uin='. (int)$row['icq'],
                       'link' => '<a href="http://www.icq.com/whitepages/about_me.php?uin='. (int)$row['icq']. '" title="'. sprintf($l['icq_messenger'], $row['displayName']). '">'. $l['icq']. '</a>',
                     );

    $member['group']['name'] = $row['group_name'];
    $member['group']['color'] = $row['group_color'];
    $member['group']['num_stars'] = max((int)mb_substr($row['stars'], 0, mb_strpos($row['stars'], '|')), 0);
    $member['group']['star'] = $settings['images_url']. '/'. mb_substr($row['stars'], mb_strpos($row['stars'], '|') + 1, mb_strlen($row['stars']));
    $member['group']['stars'] = '';

    # Make your life easier...
    if($member['group']['num_stars'] > 0)
      for($i = 0; $i < $member['group']['num_stars']; $i++)
        $member['group']['stars'] .= '<img src="'. $member['group']['star']. '" alt="*" title="*" />';

    # One more thing for this level... Are they online..?
    $member['is_online'] = !empty($row['is_online']);

    # Are we there yet?
    if($level == 'normal')
    {
      $page['member_data'][$member['id']][$level] = $member;
      $cache[] = $member;
      continue;
    }

    # Advanced are you?
    $member['post_group'] = array(
      'id' => false,
      'name' => false,
      'color' => false,
      'num_stars' => false,
      'star' => false,
      'stars' => false,
    );

    if(!empty($row['post_group_id']))
    {
      $member['post_group']['id'] = $row['post_group_id'];
      $member['post_group']['name'] = $row['post_group_name'];
      $member['post_group']['color'] = $row['post_group_color'];
      $member['post_group']['num_stars'] = max((int)mb_substr($row['post_group_stars'], 0, mb_strpos($row['post_group_stars'], '|')), 0);
      $member['post_group']['star'] = $settings['images_url']. '/'. mb_substr($row['post_group_stars'], mb_strpos($row['post_group_stars'], '|') + 1, mb_strlen($row['post_group_stars']));
      $member['post_group']['stars'] = '';

      # Only if you have sufficient stars :)
      if($member['post_group']['num_stars'] > 0)
        for($i = 0; $i < $member['post_group']['num_stars']; $i++)
          $member['post_group']['stars'] .= '<img src="'. $member['post_group']['star']. '" alt="*" title="*" />';
    }

    # Converting your birthday is in profile.php ;)
    if(!function_exists('profile_birthday_convert'))
      require_once($source_dir. '/profile.php');

    # I hope you are happy! It took me 2+ hours to make profile_birthday_convert!!!
    $member['birthdate'] = array(
      'time' => $row['birthdate'] != '0000-00-00' ? $row['birthdate'] : false,
      'date' => $row['birthdate'] != '0000-00-00' ? profile_birthday_convert($row['birthdate'], 'birthdate') : false,
      'age' => $row['birthdate'] != '0000-00-00' ? profile_birthday_convert($row['birthdate'], 'age') : false,
    );
    $member['profile_text'] = bbc($row['profile_text'], true, 'profile_text_id-'. $member['id']);
    $member['custom_title'] = $row['custom_title'];
    $member['location'] = $row['location'];

    # Timezone information...
    $timezone = timezone_get($row['timezone'], $row['dst']);
    $member['timezone'] = ($timezone >= 0 ? '+' : ''). floor($timezone). ':'. mb_substr(number_format(($timezone - floor($timezone)) * 0.6, 2), 2);

    # Getting off the gravy train! ^.^
    $page['member_data'][$member['id']][$level] = $member;
    $cache[] = $member;
  }

  # Anything to cache perhaps..?
  if(count($cache))
    foreach($cache as $member)
      cache_save('member_data_'. $level. '-'. $member['id'], $member, 60);

  # Unset some things... Just incase :)
  unset($level, $member, $cache);

  # I did it!
  return true;
}

function members_info($member_id, $level = 'normal')
{
  global $db, $l, $page, $settings, $source_dir;

  # Does it exist..?
  if(isset($page['member_data'][$member_id][$level]))
    return $page['member_data'][$member_id][$level];
  else
    return false;
}

function members_clear_id($member_id)
{
  global $settings;

  # Simply remove all three...
  $levels = array('basic', 'normal', 'extended');
  foreach($levels as $level)
    cache_remove('member_data_'. $level. '-'. $member_id);
}
?>