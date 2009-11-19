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
# array online_get(array $get[, bool $in_forum = false[, int $fetch = 0[, bool $only_links = true]]]);
#

function online_get($get, $in_forum = false, $fetch = 0, $only_links = true)
{
  global $base_url, $db, $settings, $source_dir, $user;

  # This function is for a SPECIFIC page, nothing to search for, I have nothing for you :P
  if(!is_array($get) || !count($get))
    return false;

  # Cached..?
  if(!empty($cache_key) && ($cache = cache_get($cache_key)) != null)
    return $cache;

  # Now let's get what your looking for :D!!!
  $get_search = array();
  foreach($get as $q => $v)
  {
    # Get it all formed up...
    $get_search[] = 'INSTR(data, \''. $db->escape('s:'. mb_strlen($q). ':"'. $q. '";s:'. mb_strlen($v). ':"'. $v. '";'). '\')';
  }

  # Might have one more thing to contribute myself, in forum true?
  if(!empty($in_forum))
    $get_search[] = 'INSTR(data, \'s:8:"in_forum";b:1;\')';

  # Now get ready to query it up :D
  # Here are the fetch options:
  #   0 - Only numbers (An array returned like this: array('total_members' => MEMBERS_VIEWING_COUNT, 'total_guests' => GUESTS_VIEWING_COUNT))
  #   1 - Members names and so on is loaded but of course, guests is returned as a number, an array like this is returned:
  #       array('total_members' => MEMBERS_VIEWING_COUNT, 'total_guests' => GUESTS_VIEWING_COUNT, 'members' => MEMBERS_VIEWING*, )
  #       * This array is sorted in the order of most active, so: More recently active -> less

  # We do this either way :D
  $result = $db->query("
    SELECT
      CASE
        WHEN member_id > 0 THEN 1
        ELSE 0
      END AS is_member, COUNT(*) AS num_viewing
    FROM scms_online
    WHERE %get_search AND last_active > %breakpoint
    GROUP BY is_member
    ORDER BY is_member DESC",
    array(
      'get_search' => array('raw', implode(' AND ', $get_search)),
      'breakpoint' => array('int', time_utc() - ($settings['online_timeout'] * 60)),
    ));

  # Holds all our stuff to return!!!
  $online = array('total_members' => 0, 'total_guests' => 0);

  # Almost pointless while loop, but =P
  while($row = $db->fetch_assoc($result))
  {
    if(!empty($row['is_member']))
      $online['total_members'] = $row['num_viewing'];
    else
      $online['total_guests'] = $row['num_viewing'];
  }

  # Load member information?
  if(!empty($fetch))
  {
    $result = $db->query("
      SELECT
        o.member_id, mem.member_id, mem.loginName, mem.displayName, mem.group_id, mem.post_group_id,
        grp.group_id, grp.group_color AS online_color, grp2.group_id, grp2.group_color,
        CASE mem.group_id
          WHEN 3 THEN grp2.group_color
          ELSE grp.group_color
        END AS online_color
      FROM {$db->prefix}online AS o
        INNER JOIN {$db->prefix}members AS mem ON o.member_id = mem.member_id
        LEFT JOIN {$db->prefix}membergroups AS grp ON grp.group_id = mem.group_id
        LEFT JOIN {$db->prefix}membergroups AS grp2 ON grp2.group_id = mem.post_group_id
      WHERE %get_search
      ORDER BY o.last_active DESC",
      array(
        'get_search' => array('raw', implode(' AND ', $get_search)),
      ));

    $online['members'] = array();

    # Begin to load those members!!!
    while($row = $db->fetch_assoc($result))
    {
      if(!empty($only_links))
        $online['members'][] = '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. '"'. (!empty($row['online_color']) ? ' style="color: '. $row['online_color']. '"' : ''). '>'. $row['displayName']. '</a>';
      else
        $online['members'][] = array(
          'id' => $row['member_id'],
          'username' => $row['loginName'],
          'name' => $row['displayName'],
          'color' => $row['online_color'],
          'href' => $base_url. '/index.php?action=profile;u='. $row['member_id'],
          'link' => '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. '"'. (!empty($row['online_color']) ? ' style="color: '. $row['online_color']. '"' : ''). '>'. $row['displayName']. '</a>',
        );
    }
  }

  if(!empty($cache_key))
    cache_save($cache_key, $online, 30);

  return $online;
}
?>