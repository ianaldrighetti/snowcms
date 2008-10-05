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
//                  Online.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));
  
function Online() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // Can they view the who is online?
  if(can('view_online')) {
    // Set online as an array, just incase their is nothing in the DB, but if you view ?action=online, 1 should be there by now
    $online = array();
    // Get them, Ordered by `last_active` Descending (Show the newest on top)
    $result = sql_query("
       SELECT
         o.user_id, o.ip, o.page, o.last_active,
         m.id, m.username, m.display_name
       FROM {$db_prefix}online AS o
         LEFT JOIN {$db_prefix}members AS m ON m.id = o.user_id
       ORDER BY o.last_active DESC");
      while($row = mysql_fetch_assoc($result)) {
        // Are they on a ?page= or ?action=
        $type = @explode(":", $row['page']);
        if($type[0]=='action') {
          // Get its title... Or say it is unknown
          if(isset($l[$type[1].'_title']))
            $page_title = $l[$type[1].'_title'];
          else
            $page_title = $l['online_title_unknown'];
        }
        elseif($type[0]=='page') {
          // They are on a ?page=, so get its title, or say it is unknown
          if(isset($settings['page_titles'][$type[1]]))
            $page_title = $settings['page_title'][$type[1]];
          else
            $page_title = $l['online_title_unknown'];
        }
        else
          $page_title = $l['online_title_unknown'];
        
        $username = $l['online_user_guest'];
        if(($row['id']==null) && ($row['user_id'])) {
          // The member doesn't exist, delete it!
          sql_query("DELETE FROM {$db_prefix}online WHERE `user_id` = '{$row['user_id']}'");
        }
        elseif(($row['display_name']==null) && ($row['user_id']!=0)) {
          $username = $row['username'];
        }
        elseif(($row['display_name']!=null) && ($row['user_id']!=0)) {
          $username = $row['display_name'];
        }
        // Add them to the $online array, give name, ID, page, ip, time last active
        $online[] = array(
          'user_id' => $row['user_id'],
          'is_user' => $row['user_id'] ? true : false,
          'user' => $username,
          'ip' => can('view_online_special') ? $row['ip'] : false,
          'page' => $page_title,
          'time' => date("g:i:sA", $row['last_active'])
        );
      }
    // Save the $online to $settings so we can pass it on, also set title and load template
    $settings['page']['online'] = $online;
    $settings['page']['title'] = $l['online_title'];
    loadTheme("Online");
  }
  else {
    // They can't view it, I haven't gotten this far yet... lol. I have been a bad Dev :P
  }
}