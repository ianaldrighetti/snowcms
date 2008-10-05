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
//                BoardIndex.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

// This function loads up the forum index
// It shows the categories and then the boards within the categories with board information
// Like last post, how many posts and topics, etc...
function BoardIndex() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('view_forum')) {
    // Get All the categories! :D!
    $result = sql_query("SELECT * FROM {$db_prefix}categories ORDER BY `corder` ASC");
    $cats = array();
      while($row = mysql_fetch_assoc($result)) {
        $cats[$row['cid']] = array(
          'id' => $row['cid'],
          'name' => $row['cname'],
          'boards' => array()
        );
      }
    // Query for the boards! LEFT JOINs galore!
    // !!! Needs Improvement :)
    $result = sql_query("
      SELECT 
        b.bid AS board_id, b.name, b.bdesc, b.who_view, b.numtopics, b.numposts, b.last_msg, b.last_uid, b.last_name, b.cid, log.uid AS is_new, log.bid,
        msg.tid, msg.mid, msg.uid, msg.subject, msg.post_time, msg.poster_name, mem.id, mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username
      FROM {$db_prefix}boards AS b
        LEFT JOIN {$db_prefix}board_logs AS log ON log.uid = {$user['id']} AND log.bid = b.bid
        LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = b.last_msg
        LEFT JOIN {$db_prefix}members AS mem ON mem.id = msg.uid
      WHERE {$user['board_query']}
      ORDER BY b.border ASC");
      while($row = mysql_fetch_assoc($result)) {  
      if(isset($row['is_new']))
        $new = false;
      else
        $new = true;
        $cats[$row['cid']]['boards'][$row['board_id']] = array(
          'id' => $row['board_id'],
          'name' => $row['name'],
          'desc' => $row['bdesc'],
          'who_view' => @explode(",", $row['who_view']),
          'topics' => $row['numtopics'],
          'posts' => $row['numposts'],
          'is_new' => $new,
          'last_post' => array(
                           'tid' => $row['tid'],
                           'mid' => $row['mid'],
                           'subject' => $row['subject'],
                           'username' => $row['username'],
                           'time' => formattime($row['post_time']),
                           'uid' => $row['uid'],
                           'is_post' => $row['mid'] ? true : false
                         )
        );
      }
    // Just some stuff and checks :)
    foreach($cats as $cat) {
      if(!count($cats[$cat['id']]['boards'])) {
        // Dont show a category if it has no boards...
        unset($cats[$cat['id']]);
      }
    }
    // Load it up
    $settings['forum']['cats'] = $cats;
    $settings['page']['title'] = $l['forum_title'];
    loadForum('BoardIndex');
  }
  else {
    // Go away! :P You should not be here
    $settings['page']['title'] = $l['forum_notallowed_title'];
    loadForum('BoardIndex','NotAllowed');
  }
}