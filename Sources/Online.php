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
         o.user_id, o.ip, o.url_data, o.last_active, o.sc,
         o.inForum, m.id, m.username, m.display_name
       FROM {$db_prefix}online AS o
         LEFT JOIN {$db_prefix}members AS m ON m.id = o.user_id
       ORDER BY o.last_active DESC");
    $settings['page']['online'] = array();
    $urls = array();
    while($row = mysql_fetch_assoc($result)) {
      if($row['user_id'] == 0)
        $row['display_name'] = $l['online_user_guest'];
      $settings['page']['online'][$row['sc']] = array(
                                        'id' => $row['user_id'] ? $row['user_id'] : 0,
                                        'ip' => $row['ip'],
                                        'name' => $row['display_name'],
                                        'last_active' => formattime($row['last_active'], 2),
                                        'viewing' => '<i>'.$l['online_unknown'].'</i>'
                                      );
      $url_data = unserialize(stripslashes($row['url_data']));
      $url_data['sc'] = $row['sc'];
      $url_data['inForum'] = $row['inForum'] ? true : false;
      $urls[] = $url_data;
    }
    mysql_free_result($result);
    $figured_urls = figureURLs($urls);
    // Now assign the thing being viewed to the right thing
    foreach($settings['page']['online'] as $sc => $data) {
      $settings['page']['online'][$sc]['viewing'] = $figured_urls[$sc];
    }
    // Save the $online to $settings so we can pass it on, also set title and load template
    $settings['page']['title'] = $l['online_title'];
    loadTheme('Online');
  }
  else {
    // They can't view it, I haven't gotten this far yet... lol. I have been a bad Dev :P
  }
}

function figureURLs($url_list) {
global $cmsurl, $db_prefix, $l, $user;
  
  // Setup a few array thingys...
  $pages = array();
  $topics = array();
  $boards = array();
  $figured_urls = array();
  // This foreach is for 1 thing, to get the topics, boards and pages we need...
  foreach($url_list as $url) {
    if($url['inForum']) {
      // They are in the forum...
      if(!empty($url['topic'])) {
        // A topic we need to get the title for!
        $topics[] = (int)$url['topic'];
      }
      elseif(!empty($url['board'])) {
        // A board we need to get a title for :P
        $boards[] = (int)$url['board'];
      }
    }
    else {
      // Are they viewing a page? Its all we care about right now...
      if(!empty($url['page'])) {
        // A Page we need to get a title for xD!
        $pages[] = (int)$url['page'];
      }
    }
  }
  // Done Step #1... Now for getting the titles and stoof
  // Define a few more arrays! ARRAY LOCO! Lol.
  $page_names = array();
  $board_names = array();
  $topic_names = array();
  // Any pages?
  if(count($pages)) {
    // Ok. Figure the pages out :)
    // Weeeee! Implosions!
    $page_ids = implode(",", $pages);
    $result = mysql_query("
      SELECT
        p.page_id, p.title
      FROM {$db_prefix}pages AS p
      WHERE p.page_id IN($page_ids)");
    while($row = mysql_fetch_assoc($result)) {
      $page_names[$row['page_id']] = $row['title'];
    }
  }
  // Board Names needed perhaps?
  if(count($boards)) {
    // Now Board Time =D
    // More implosions...
    $board_ids = implode(",", $boards);
    // Dont forget permissions :P
    $result = mysql_query("
      SELECT
        b.bid, b.who_view, b.name
      FROM {$db_prefix}boards AS b
      WHERE b.bid IN($board_ids) AND {$user['board_query']}");
    while($row = mysql_fetch_assoc($result)) {
      $board_names[$row['bid']] = $row['name'];
    }
  }
  // Now topics! WOOT, Almost done!
  if(count($topics)) {
    // Topic name time... Weee... -.-
    // More implosions, I wish it were an explosion, its cold :S
    $topic_ids = implode(",", $topics);
    // Gotta check permissions here as well!
    $result = mysql_query("
      SELECT
        t.tid, t.bid, t.first_msg, t.bid, msg.mid, msg.subject, b.bid, b.who_view
      FROM {$db_prefix}topics AS t
        LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = t.first_msg
        LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
      WHERE t.tid IN($topic_ids) AND {$user['board_query']}");
    while($row = mysql_fetch_assoc($result)) {
      $topic_names[$row['tid']] = $row['subject'];
    }
  }
  // Now the grand finale! Actually using everything we made! :P
  foreach($url_list as $url) {
    // Are they in the forum..?
    if($url['inForum']) {
      // Why yes, yes they are
      if(!empty($url['topic'])) {
        // Ok... So, name plz?
        $replace = array(
                     '%topic_url%' => $cmsurl. 'forum.php?topic='. (int)$url['topic'],
                     '%topic_name%' => empty($topic_names[(int)$url['topic']]) ? '' : $topic_names[(int)$url['topic']]
                   );
        // Get 'er done!
        $figured_urls[$url['sc']] = empty($topic_names[(int)$url['topic']]) ? '<i>'.$l['online_unknown'].'</i>' : str_replace(array_keys($replace), array_values($replace), $l['online_topic_template']);
      }
      elseif(!empty($url['board'])) {
        // Board Name :)
        $replace = array(
                     '%board_url%' => $cmsurl. 'forum.php?board='. (int)$url['board'],
                     '%board_name%' => empty($board_names[(int)$url['board']]) ? '' : $board_names[(int)$url['board']]
                   );
        // Set it :D
        $figured_urls[$url['sc']] = empty($board_names[(int)$url['board']]) ? '<i>'.$l['online_unknown'].'</i>' : str_replace(array_keys($replace), array_values($replace), $l['online_board_template']);
      }
      else {
        // There is nothing else left but viewing the forum index...
        $figured_urls[$url['sc']] = $l['online_forum_index'];
      }
    }
    else {
      // They aren't viewing the forum, so it must be the main site thing :P
      if(!empty($url['page'])) {
        // They are viewing a page
        $replace = array(
                     '%page_url%' => $cmsurl. 'index.php?page='. (int)$url['page'],
                     '%page_name%' => empty($page_names[(int)$url['page']]) ? '' : $page_names[(int)$url['page']]
                   );
        $figured_urls[$url['sc']] = empty($page_names[(int)$url['page']]) ? '<i>'.$l['online_unknown'].'</i>' : str_replace(array_keys($replace), array_values($replace), $l['online_page_template']);
      }
      elseif(!empty($url['action'])) {
        // If they are viewing another member's profile, then get their username
        if ($url['action'] == 'profile' && !empty($url['u'])) {
          $url['username'] = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '{$url['u']}'"));
          $url['username'] = $url['username']['username'];
        }
        // Viewing an action... This is the easiest one! :P
        // Here is an array of actions... :D that are set...
        $set_actions = array(
                         'activate' => $l['online_activating_account'],
                         'admin' => $l['online_admin'],
                         'login' => $l['online_login'],
                         'logout' => $l['online_logout'],
                         'news' => $l['online_news'],
                         'online' => $l['online_online'],
                         'profile' => empty($url['u']) ? $l['online_profile'] : str_replace('%user%',
                           '<a href="'.$cmsurl.'index.php?action=profile;u='.$url['u'].'">'.$url['username'].'</a>',$l['online_viewing_profile']),
                         'register' => $l['online_register'],
                         'register3' => $l['online_register']
                       );
        // Now, set it and forget it! lol.
        $figured_urls[$url['sc']] = empty($set_actions[strtolower($url['action'])]) ? '<i>'.$l['online_unknown'].'</i>' : $set_actions[strtolower($url['action'])];
      }
      else {
        // They must be viewing home..?
        $figured_urls[$url['sc']] = $l['online_home'];
      }
    }
  }
  return $figured_urls;
}
?>