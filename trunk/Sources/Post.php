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
//                Post.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function Post() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // Are they posting in a board or a topic? None? o.o
  if(!empty($_REQUEST['topic'])) {
    // K, they are replying to a topic...
    // But can they :)
    if(canforum('post_reply')) {
      // Okay, their permissions say they can reply, but is this even a topic in a board they can see? :O!
      $Topic_ID = (int)addslashes(mysql_real_escape_string($_REQUEST['topic']));
      $result = sql_query("
         SELECT
           t.tid, t.bid, b.bid, b.who_view
         FROM {$db_prefix}topics AS t
           LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
         WHERE t.tid = $Topic_ID");
      // But does it exist? D:!
      if(mysql_num_rows($result)>0) {
        // The topic DOES exist, now we can check if they are allowed to see it
        while($row = mysql_fetch_assoc($result))
          $who_view = @explode(",", $row['who_view']);
        if((in_array($user['group'], $who_view)) || ($user['is_admin'])) {
          $settings['page']['title'] = $l['forum_postreply'];
          loadForum('Post','Reply');
        }
        else {
          // They can't access the board that the topic is in, why should they be able to post, make them think this topic doesnt exist.
          $settings['page']['title'] = $l['forum_error_title'];
          loadForum('Error','CantPost');
        }
      }
      else {
      // The topic doesn't exist :o
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Error','CantPost');        
      }
    }
    else {
      // They couldn't have posted in the first place ._.
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Error','CantPost');    
    }
  }
  elseif(!empty($_REQUEST['board'])) {
    // K, they are making a new topic
    // But can they? :)
    if(canforum('post_new')) {
      // :o What Board?
      $Board_ID = (int)addslashes(mysql_real_escape_string($_REQUEST['board']));
      // Okay, their permissions say yes, but lets see what the forum says! :D!
      $result = sql_query("
         SELECT 
           b.bid, b.who_view
         FROM {$db_prefix}boards AS b
         WHERE b.bid = $Board_ID");
      // Well, does this board exist? ._.
      if(mysql_num_rows($result)>0) {
        while($row = mysql_fetch_assoc($result))
          $who_view = @explode(",", $row['who_view']);
        if((in_array($user['group'], $who_view)) || ($user['is_admin'])) {
          $settings['page']['title'] = $l['forum_startnew'];
          loadForum('Post','Topic');
        }
        else {
          // It does exist, lets just make them think it doesn't, hehe
          $settings['page']['title'] = $l['forum_error_title'];
          loadForum('Error','NoBoard');
        }
      }
      else {
        // Its doesn't exist :O
        $settings['page']['title'] = $l['forum_error_title'];
        loadForum('Error','NoBoard');
      }
    }
    else {
      // Awww, sucks to be them :P
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Error','CantPost');
    }
  }
  else {
    // D:! No post or BOARD, but could they have posted in the first place? :P
    if(canforum('post_new')) {
      // They could have posted, but they can't :P
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Error','NoSpecified');
    }
    else {
      // They couldn't have posted in the first place ._.
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Error','CantPost');
    }
  }
}

function Post2() {
global $cmsurl, $db_prefix, $l, $settings, $user;
}
?>