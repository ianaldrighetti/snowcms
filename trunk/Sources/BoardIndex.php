<?php


if(!defined('Snow'))
  die("Hacking Attempt...");

// This function loads up the forum index  
function BoardIndex() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('view_forum')) {
    // Get All the categories! :D!
    $result = mysql_query("SELECT * FROM {$db_prefix}categories ORDER BY `corder` ASC");
    $cats = array();
      while($row = mysql_fetch_assoc($result)) {
        $cats[$row['cid']] = array(
          'id' => $row['cid'],
          'name' => $row['cname'],
          'desc' => $row['cdesc']
        );
      }
    // Get all the boards :)
    $result = mysql_query("SELECT * FROM {$db_prefix}boards ORDER BY `border` ASC");
    while($row = mysql_fetch_assoc($result)) {
      $cats[$row['cid']][$row['bid']] = array(
        'id' => $row['bid'],
        'catid' => $row['cid'],
        'who_view' => @trim(@explode(",", $row['who_view'])),
        'name' => $row['name'],
        'desc' => $row['bdesc'],
        'numtopics' => $row['numtopics'],
        'numposts' => $row['numposts']
      );
    }
    foreach($cats as $cat) {
      if(count($cats[$cat['id']])>0) {
        // K, their are some here, just because they are here, don't mean they are allowed to see them :P
        foreach($cat as $board) {
          if(is_array($board['who_view'])) {
            if(!in_array($user['group'], $board['who_view'])) {
              // Their user group isn't in the array =S so they can't view it, unset it!
              unset($cats[$cat['id']][$board['id']]);
            }
          }
          else {
            // Okay, so their weren't any more then 1 user group allowed, maybe they are in it :)
            if($user['group']!=$board['who_view']) {
              // Or not ._.
              unset($cats[$cat['id']][$board['id']]);
            }
          }
        }
        // Maybe now their are no boards in that category? :0
        if(count($cats[$cat['id']])==0) {
          unset($cats[$cat['id']]);
        }
      }
      else {
        // Nothing hear... Unset it...
        unset($cats[$cat['id']]);
      }
    }
  }
  else {
    $settings['page']['title'] = $l['forum_error_title'];
    loadForum('Error','BNotAllowed');
  }
}