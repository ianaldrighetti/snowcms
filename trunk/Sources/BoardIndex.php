<?php


if(!defined('Snow'))
  die("Hacking Attempt...");

// This function loads up the forum index  
function BoardIndex() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('view_forum')) {
    // Get All the categories! :D!
    $result = mysql_query("SELECT * FROM {$db_prefix}categories ORDER BY `corder` ASC") or die(mysql_error());
    $cats = array();
      while($row = mysql_fetch_assoc($result)) {
        $cats[$row['cid']] = array(
          'id' => $row['cid'],
          'name' => $row['cname'],
          'desc' => $row['cdesc'],
          'boards' => array()
        );
      }
    $result = mysql_query("
      SELECT 
        b.bid, b.name, b.bdesc, b.who_view, b.numtopics, b.numposts,
        b.cid, log.uid, log.new AS is_new
      FROM {$db_prefix}boards AS b
        LEFT JOIN {$db_prefix}board_logs AS log ON b.bid = {$user['id']}
      ORDER BY b.border ASC") or die(mysql_error());
      while($row = mysql_fetch_assoc($result)) {
        if($row['is_new']==null)
          $row['is_new'] = 1;
        $cats[$row['cid']]['boards'][$row['bid']] = array(
          'id' => $row['bid'],
          'name' => $row['name'],
          'desc' => $row['bdesc'],
          'who_view' => @explode(",", $row['who_view']),
          'topics' => $row['numtopics'],
          'posts' => $row['numposts'],
          'is_new' => $row['is_new'] ? true : false
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