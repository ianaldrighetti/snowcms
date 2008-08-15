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
      // Lets see, we need the Board ID before anything, lets get some stuff :)
      $Topic_ID = (int)addslashes(mysql_real_escape_string($_REQUEST['topic']));
      $result = sql_query("
         SELECT
           t.tid, t.bid, b.bid, b.who_view
         FROM {$db_prefix}topics AS t
           LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
         WHERE t.tid = $Topic_ID");
    // K, they are replying to a topic...
    // But can they :)    
    if(canforum('post_reply', $row['bid'])) {         
      // But does it exist? D:!
      if(mysql_num_rows($result)>0) {
        echo '=D';
        // The topic DOES exist, now we can check if they are allowed to see it
        while($row = mysql_fetch_assoc($result))
          $who_view = @explode(",", $row['who_view']);
        if((in_array($user['group'], $who_view)) || ($user['is_admin'])) {
          $settings['page']['title'] = $l['forum_postreply'];
          // This is some STUFF to preload, maybe, if you were redirected from ?action=post2 back due to errors :)
          $settings['locked'] = $_SESSION['locked'] ? (int)$_SESSION['locked'] : (int)$_REQUEST['locked'];
          $settings['sticky'] = $_SESSION['sticky'] ? (int)$_SESSION['sticky'] : (int)$_REQUEST['sticky'];
          $settings['subject'] = $_SESSION['subject'] ? clean($_SESSION['subject']) : clean(@$_REQUEST['subject']);
          $settings['body'] = $_SESSION['body'] ? clean($_SESSION['body']) : clean(@$_REQUEST['body']);
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
    // :o What Board?
    $Board_ID = (int)addslashes(mysql_real_escape_string($_REQUEST['board']));    
    // K, they are making a new topic
    // But can they? :)
    if(canforum('post_new', $Board_ID)) {
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
        if((in_array($user['group'], $who_view))) {
          $settings['page']['title'] = $l['forum_startnew'];
          // This is some STUFF to preload, maybe, if you were redirected from ?action=post2 back due to errors :)
          $settings['locked'] = $_SESSION['locked'] ? (int)$_SESSION['locked'] : (int)$_REQUEST['locked'];
          $settings['sticky'] = $_SESSION['sticky'] ? (int)$_SESSION['sticky'] : (int)$_REQUEST['sticky'];
          $settings['subject'] = $_SESSION['subject'] ? clean($_SESSION['subject']) : clean(@$_REQUEST['subject']);
          $settings['body'] = $_SESSION['body'] ? clean($_SESSION['body']) : clean(@$_REQUEST['body']);          
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
      // They couldn't have posted in the first place ._.
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Error','NoSpecified');
  }
}

function Post2() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // This actually submits the post itself... 
  // So, what are we doing? New topic? Posting a Reply?
  $what = strtolower($_REQUEST['what']);
  // Do they want to post a new topic? And can they post it in the board?
  if($what == 'new_topic' && canforum('post_new') && postable($_REQUEST['board'])) {
    $Board_ID = (int)$_REQUEST['board'];
  }
  elseif($what == 'post_reply' && canforum('post_reply', boardfromTopic($_REQUEST['topic'])) && postable($_REQUEST['topic'])) {
    $Topic_ID = (int)$_REQUEST['topic'];
    $subject = clean($_REQUEST['subject']);
    $body = clean($_REQUEST['body']);
    // Get a couple options, like locked topic, sticky topic, etc.
    $options = array();
    $options['sticky'] = canforum('sticky_topic') ? (int)$_REQUEST['isSticky'] : 0;
    $options['locked'] = canforum('lock_topic') ? (int)$_REQUEST['isLocked'] : 0;
    // If they stickied the topic or locked it, we need to update the original topic :)
    if($options['sticky'] || $options['locked']) {
      sql_query("UPDATE {$db_prefix}topics SET `sticky` = '{$options['sticky']}', `locked` = '{$options['locked']}'  WHERE tid = '$Topic_ID'");
    }
    $result = sql_query("
      SELECT
        b.bid, t.tid, t.bid
      FROM {$db_prefix}topics AS t
        LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
      WHERE 
        t.tid = $Topic_ID");
    $row = mysql_fetch_row($result);
    $Board_ID = $row['bid'];
    $post_time = time();
    mysql_free_result($result);
    sql_query("
      INSERT INTO {$db_prefix}messages
        (`tid`,`bid`,`uid`,`subject`,`post_time`,`poster_name`,`poster_email`,`ip`,`body`)
        VALUES('$Topic_ID','$Board_ID','{$user['id']}','{$subject}','{$post_time}','{$user['name']}','{$user['email']}','{$user['ip']}','{$body}')");
    // D: You think we are done? Hahaha, You are so funny... ._.
    $result = sql_query("
      SELECT 
        m.mid, m.tid, m.bid, m.uid, m.poster_name, m.post_time
      FROM {$db_prefix}messages AS m
      WHERE
        m.tid = '$Topic_ID' AND m.bid = '$Board_ID' AND m.uid = '{$user['id']}' AND m.post_time = '{$post_time}'
      LIMIT 1");
    $row = mysql_fetch_row($result);
    mysql_free_result($result);
    sql_query("
      UPDATE {$db_prefix}topics
      SET
        `last_msg` = '{$row['mid']}', `ender_id` = '{$user['id']}', `topic_ender` = '{$user['name']}', `num_replies` = num_replies + 1
      WHERE `tid` = '{$row['tid']}'");
    sql_query("UPDATE {$db_prefix}boards SET `numposts` = numposts + 1 WHERE `bid` = '$Board_ID'");
    sql_query("UPDATE {$db_prefix}members SET `numposts` = numposts + 1 WHERE `id` = '{$user['id']}'");
    header("Location: {$cmsurl}forum.php?board={$Board_ID}");
  }
}

/* This function checks if they can post in the board or topic by ID */
function postable($id, $which = 0) {
global $db_prefix, $settings, $user;
  // Make sure it is an int ;]
  $id = (int)$id;
  $which = (int)$which;
  // Which are we checking? Board ID or Topic ID? 0 = board, 1 = topic
  if($which) {
    $result = sql_query("
      SELECT
        b.bid, b.who_view
      FROM {$db_prefix}boards AS b
      WHERE b.bid = $id
      LIMIT 1");
    // Does the board even exist?
    if(mysql_num_rows($result)) {
      $row = mysql_fetch_row($result);
      $who_view = explode(",", $row['who_view']);
      // Is their user group in the array?
      if(in_array($user['group'], $who_view)) {
        // It is!
        return true;
      }
      else {
        // Oh noes!
        return false;
      }
    }
    else {
      // This board doesn't even exist!
      return false;
    }
  }
  else {
    // Checking a topic, a little bit more to do then the board check
    $result = sql_query("
      SELECT
        b.bid, b.who_view, t.bid, t.tid, t.locked
      FROM {$db_prefix}topics AS t
        LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
      WHERE t.tid = $id");
    // Does this topic exist? :P
    if(mysql_num_rows($result)) {
      // It exists...
      $row = mysql_fetch_row($result);
      $who_view = explode(",", $row['who_view']);
      // Now, is there group in the array AND is this topic NOT locked?
      if(in_array($user['group'], $who_view) && !$row['locked']) {
        return true;
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }
}

// returns the board ID from a given topic ID
function boardfromTopic($topic_id) {
global $db_prefix;
  $topic_id = (int)$topic_id;
  $result = sql_query("
    SELECT
      t.tid, t.bid
    FROM {$db_prefix}topics AS t
    WHERE t.tid = $topic_id");
  if(mysql_num_rows($result)) {
    $row = mysql_fetch_assoc($result);
    return $row['bid'];
  }
  else
    return 0;
}
?>