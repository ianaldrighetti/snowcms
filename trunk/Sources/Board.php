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
//                  Board.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

/*
   loadBoard() loads the board for your forum, it loads the specified board through forum.php?board=BOARD_ID
   It also handles if this board is new or not, however it will only log that if you are a registered and 
   logged in user on this site.
   
   Of course, this also makes sure you can view the board via the super useful SQL Function FIND_IN_SET
*/

function loadBoard() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if (can('view_forum')) {
    // Get the Board ID
    $board_id = (int)$_REQUEST['board'];
    // Query! This is just kind of a query to see if they can view this board or not ;)
    $result = sql_query("
      SELECT
        b.bid, b.name, b.who_view
      FROM {$db_prefix}boards AS b
      WHERE b.bid = $board_id AND {$user['board_query']}");
    // So does it exist / Can they view it?
    if(mysql_num_rows($result)) {
      $board = mysql_fetch_assoc($result);
      // We can only keep track of new/old boards with members :)
      if($user['is_logged']) {
        // Replace it
        sql_query("
          REPLACE INTO {$db_prefix}board_logs
				    (`bid`,`uid`)
	        VALUES ($board_id, {$user['id']})");
	      }
	      $start = (int)@$_REQUEST['pg'] * $settings['num_topics'];
        // :O Look at that honkin query! ITS HUGE!!!!!!!!!
        $result = sql_query("
          SELECT 
            t.tid AS topic_id, t.sticky, t.locked, t.bid, t.first_msg, t.last_msg, IFNULL(t.last_msg, t.first_msg) AS last_msg, t.topic_starter, t.topic_ender AS topic_ender, IFNULL(t.topic_ender, t.topic_starter) AS topic_ender, t.num_replies, log.uid AS is_new, log.tid,
            t.numviews, t.starter_id, t.ender_id, IFNULL(t.ender_id, t.starter_id) AS ender_id, msg.mid, msg.tid, msg.uid, msg.subject, msg.post_time, msg.poster_name,
            msg2.mid AS mid2, IFNULL(msg2.mid, msg.mid) AS mid2, msg2.uid AS uid2, IFNULL(msg2.uid, msg.uid) AS uid2, 
            msg2.subject AS subject2, IFNULL(msg2.subject, msg.subject) AS subject2, msg2.post_time AS last_post_time, IFNULL(msg2.post_time, msg.post_time) AS last_post_time,
            msg2.poster_name AS poster_name2, IFNULL(msg2.poster_name, msg.poster_name) AS poster_name2,
            mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username, ml.uid, ml.tid, ml.mid AS is_own,
            IFNULL(ml.mid, 0) AS is_own,mem2.display_name AS username2, IFNULL(mem2.display_name, mem2.username) AS username2
          FROM {$db_prefix}topics AS t
            LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = t.first_msg
            LEFT JOIN {$db_prefix}messages AS msg2 ON msg2.mid = t.last_msg
            LEFT JOIN {$db_prefix}members AS mem ON mem.id = t.starter_id
            LEFT JOIN {$db_prefix}members AS mem2 ON mem2.id = t.ender_id
            LEFT JOIN {$db_prefix}topic_logs AS log ON log.uid = {$user['id']} AND log.tid = t.tid
            LEFT JOIN {$db_prefix}message_logs AS ml ON (ml.uid = {$user['id']} AND ml.tid = t.tid)
          WHERE 
            t.bid = $board_id
          ORDER BY t.sticky DESC, last_post_time DESC
          LIMIT $start, {$settings['num_topics']}");
        // Define topics as an array
        $topics = array();
        // Loop through them, if there are any...
        while($row = mysql_fetch_assoc($result)) {
          $topics[] = array(
            'tid' => $row['topic_id'],
            'subject' => $row['subject'],
            'sticky' => $row['sticky'],
            'locked' => $row['locked'],
            'bid' => $row['bid'],
            'username' => $row['username'],
            'numReplies' => $row['num_replies'],
            'numViews' => $row['numviews'],
            'starter_id' => $row['starter_id'],
            'is_new' => isset($row['is_new']) ? false : true,
            'is_own' => $row['is_own'] ? true : false,
            'last_post' => array(
                           'mid' => $row['mid2'],
                           'subject' => $row['subject2'],
                           'username' => $row['username2'],
                           'time' => $row['last_post_time'],
                           'uid' => $row['uid2'],
                           'is_post' => $row['mid2'] ? true : false
                         )
          );
          // Do some other stuff
          // !!! Should be done in the above ARRAY!!!
          $topics[count($topics)-1]['pages'] = floor($topics[count($topics)-1]['numReplies'] / $settings['num_posts'] + 1);
          $topics[count($topics)-1]['hot'] = $topics[count($topics)-1]['numReplies'] >= @$settings['hot_posts'];
        }
        // Free the result
        mysql_free_result($result);
        // Qhat page we on?
        $settings['page']['page'] = (int)@$_REQUEST['pg'];
        $total_topics = mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}topics WHERE `bid` = '$board_id'"));
        $settings['page']['page_last'] = $total_topics / $settings['num_topics'];
        $settings['page']['board-name'] = $board['name'];
        $settings['topics'] = $topics;
        $settings['page']['title'] = $board['name'].' - '.$settings['site_name'];
        loadForum('Board');
    }
    else {
      // No board has been found
      $settings['page']['title'] = $l['board_unknown_title'];
      loadForum('Board','UnknownBoard');
    }
  }
  // You aren't allowed to view the forum
  else
    redirect('forum.php');
}
?>