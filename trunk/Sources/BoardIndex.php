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
//           BoardIndex.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

// This function loads up the forum index  
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
    foreach($cats as $cat) {
      if(count($cats[$cat['id']]['boards'])>0) {
        foreach($cat['boards'] as $board) {
          // Can they view this board? :o But of course, the admin can view anything :D!
          if((!in_array($user['group'], $board['who_view'])) && ($user['group']!=1)){
            // They can't! UNSET it so the can't see hehe :)
            unset($cats[$cat['id']]['boards'][$board['id']]);
          }
        }
        // Check if this cat has any boards again...
        if(count($cats[$cat['id']]['boards'])==0)
          unset($cats[$cat['id']]);
      }
      else {
        // Dont show a category if it has no boards...
        unset($cats[$cat['id']]);
      }
    }
    $settings['forum']['cats'] = $cats;
    $settings['page']['title'] = $l['forum_title'];
    loadForum('BoardIndex');
  }
  else {
    $settings['page']['title'] = $l['forum_error_title'];
    loadForum('Error','BNotAllowed');
  }
}