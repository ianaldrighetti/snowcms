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
//              Board.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");


/*
   loadBoard() loads the board for your forum, it loads the specified board through forum.php?board=BOARD_ID
   It also handles if this board is new or not, however it will only log that if you are a registered and 
   logged in user on this site.
   
   Of course, this also makes sure you can view the board via the super useful SQL Function FIND_IN_SET
*/
function loadBoard() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('view_forum')) {
    // Get the Board ID
    $board_id = (int)$_REQUEST['board'];
    // Query! This is just kind of a Query to see if they can view this board or not ;)
    $result = sql_query("
      SELECT
        b.bid, b.name, b.who_view
      FROM {$db_prefix}boards AS b
      WHERE b.bid = $board_id AND {$user['board_query']}");
    // So does it exist / Can they view it?
    if(mysql_num_rows($result)) {
      $board = mysql_fetch_assoc($result);
      if($user['is_logged']) {
        $result = sql_query("SELECT * FROM {$db_prefix}board_logs WHERE `bid` = '$board_id' AND `uid` = '{$user['id']}'");
          if(mysql_num_rows($result)==0) {
            sql_query("
              REPLACE INTO {$db_prefix}board_logs
				        (`bid`,`uid`)
	            VALUES ($board_id, {$user['id']})");
	        }
	      }
        $result = sql_query("
          SELECT 
            t.tid, t.sticky, t.locked, t.bid, t.first_msg, t.last_msg, IFNULL(t.last_msg, t.first_msg) AS last_msg, t.topic_starter, t.topic_ender AS topic_ender, IFNULL(t.topic_ender, t.topic_starter) AS topic_ender, t.num_replies, log.uid AS is_new, log.tid,
            t.numviews, t.starter_id, t.ender_id, IFNULL(t.ender_id, t.starter_id) AS ender_id, msg.mid, msg.tid, msg.uid, msg.subject, msg.post_time, msg.poster_name,
            msg2.mid AS mid2, IFNULL(msg2.mid, msg.mid) AS mid2, msg2.uid AS uid2, IFNULL(msg2.uid, msg.uid) AS uid2, 
            msg2.subject AS subject2, IFNULL(msg2.subject, msg.subject) AS subject2, msg2.post_time AS last_post_time, IFNULL(msg2.post_time, msg.post_time) AS last_post_time,
            msg2.poster_name AS poster_name2, IFNULL(msg2.poster_name, msg.poster_name) AS poster_name2,
            mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username,
            mem2.display_name AS username2, IFNULL(mem2.display_name, mem2.username) AS username2
          FROM {$db_prefix}topics AS t
            LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = t.first_msg
            LEFT JOIN {$db_prefix}messages AS msg2 ON msg2.mid = t.last_msg
            LEFT JOIN {$db_prefix}members AS mem ON mem.id = t.starter_id
            LEFT JOIN {$db_prefix}members AS mem2 ON mem2.id = t.ender_id
            LEFT JOIN {$db_prefix}topic_logs AS log ON log.uid = {$user['id']} AND log.tid = t.tid
          WHERE 
            t.bid = $board_id
          ORDER BY t.sticky DESC, last_post_time DESC") or die(mysql_error());
          $topics = array();
          while($row = mysql_fetch_assoc($result)) {
            if(isset($row['is_new']))
              $new = false;
            else
              $new = true;
            if (mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}messages WHERE `tid` = '{$row['tid']}' AND `uid` = '{$user['id']}'")))
              $own = true;
            else
              $own = false;
            $topics[] = array(
              'tid' => $row['tid'],
              'subject' => $row['subject'],
              'sticky' => $row['sticky'],
              'locked' => $row['locked'],
              'bid' => $row['bid'],
              'username' => $row['username'],
              'numReplies' => $row['num_replies'],
              'numViews' => $row['numviews'],
              'starter_id' => $row['starter_id'],
              'is_new' => $new,
              'is_own' => $own
            );
          }
          mysql_free_result($result);
        $settings['topics'] = $topics;
        $settings['page']['title'] = $settings['site_name'].' - '.$board['name'];
        loadForum('MessageIndex');    
    }
    else {
      // No board has been found
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Error','NoBoard');
    }
  }
  else {
    // You aren't allowed to view this
    $settings['page']['title'] = $l['forum_error_title'];
    loadForum('Error','BNotAllowed');
  }
}
?>