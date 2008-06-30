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
  
function loadBoard() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  $Board_ID = addslashes(mysql_real_escape_string($_REQUEST['board']));
  // Mark this board now as read... Only if they are logged in :)
  if($user['is_logged']) {
    $result = sql_query("SELECT * FROM {$db_prefix}board_logs WHERE `bid` = '$Board_ID'");
    if(mysql_num_rows($result)==0) {
      sql_query("
        INSERT INTO {$db_prefix}board_logs
				  (`bid`,`uid`)
	      VALUES ($Board_ID, {$user['id']})");
	  }
	}
  $result = sql_query("SELECT * FROM {$db_prefix}boards WHERE `bid` = '$Board_ID'");
    while($row = mysql_fetch_assoc($result)) {
      $board = array(
        'id' => $row['bid'],
        'cid' => $row['cid'],
        'name' => $row['name'],
        'who_view' => @explode(",", $row['who_view'])
      );
    }
  if(count($board)>0) {  
    // Before we do anything else, are they allowed to see this board? :o!
    if((!in_array($user['group'], $board['who_view'])) && ($user['group']!=1)) {
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Error','CantViewB');    
    }
    else {
      $start = 0;
      $result = sql_query("
        SELECT 
          t.tid, t.sticky, t.locked, t.bid, t.first_msg, t.last_msg, IFNULL(t.last_msg, t.first_msg) AS last_msg, t.topic_starter, t.topic_ender AS topic_ender, IFNULL(t.topic_ender, t.topic_starter) AS topic_ender, t.num_replies,
          t.numviews, t.starter_id, t.ender_id, IFNULL(t.ender_id, t.starter_id) AS ender_id, msg.mid, msg.tid, msg.uid, msg.subject, msg.post_time, msg.poster_name,
          msg2.mid AS mid2, IFNULL(msg2.mid, msg.mid) AS mid2, msg2.uid AS uid2, IFNULL(msg2.uid, msg.uid) AS uid2, 
          msg2.subject AS subject2, IFNULL(msg2.subject, msg.subject) AS subject2, msg2.post_time AS last_post_time, IFNULL(msg2.post_time, msg.post_time) AS last_post_time,
          msg2.poster_name AS poster_name2, IFNULL(msg2.poster_name, msg.poster_name) AS poster_name2
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = t.first_msg
          LEFT JOIN {$db_prefix}messages AS msg2 ON msg2.mid = t.last_msg
          LEFT JOIN {$db_prefix}members AS mem ON mem.id = t.starter_id
          LEFT JOIN {$db_prefix}members AS mem2 ON mem2.id = t.ender_id
        WHERE 
          t.bid = $Board_ID
        ORDER BY t.sticky DESC, last_post_time ASC");
        $topics = array();
        while($row = mysql_fetch_assoc($result)) {
          $topics[] = $row;
        }
      $settings['topics'] = $topics;
      $settings['page']['title'] = $settings['site_name'].' - '.$board['name'];
      loadForum('MessageIndex');
    }
  }
  else {
    $settings['page']['title'] = $l['forum_error_title'];
    loadForum('Error','NoBoard');
  }
}
?>