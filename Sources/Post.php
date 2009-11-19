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
//                   Post.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));
  
function Post() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // Are they posting in a board or a topic? None? o.o
  if(!empty($_REQUEST['topic'])) {
      // Lets see, we need the Board ID before anything, lets get some stuff :)
      $Topic_ID = (int)addslashes(mysql_real_escape_string($_REQUEST['topic']));
      $result = sql_query("
         SELECT
           t.tid, t.bid, m.subject, b.bid, b.who_view
         FROM {$db_prefix}topics AS t
           LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
           LEFT JOIN {$db_prefix}messages AS m ON m.tid = t.tid
         WHERE t.tid = $Topic_ID");
    // K, they are replying to a topic...
    // But can they :)    
    $row = mysql_fetch_assoc($result);
    if (canforum('post_reply', $row['bid']) || (canforum('edit_own', $row['bid']) && @$_REQUEST['edit'] && postOwner(@$_REQUEST['edit']) == $user['id']) || (canforum('edit_any', $row['bid']) && @$_REQUEST['edit'])) {
      // But does it exist? D:!
      if (mysql_num_rows($result) > 0) {
        // The topic DOES exist, now we can check if they are allowed to see it
        //while($row = mysql_fetch_assoc($result))
          $who_view = @explode(",",$row['who_view']);
        if (in_array($user['group'],$who_view) || $user['is_admin']) {
          // Are they editing a post?
          if ($edit = clean(@$_REQUEST['edit'])) {
            $edit = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages WHERE `mid` = '$edit'"));
            $subject = $edit['subject'];
            $body = $edit['body'];
            $settings['edit'] = $edit['mid'];
          }
          else
            $settings['edit'] = '';
          // Get quote information
          if ($quote = clean(@$_REQUEST['quote'])) {
            $quote = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages LEFT JOIN {$db_prefix}members ON `uid` = `id` WHERE `mid` = '$quote'"));
            $quote = '[quote by="'.$quote['display_name'].'"]'."\n".$quote['body']."\n".'[/quote]'."\n";
          }
          $settings['page']['title'] = $l['forum_postreply'];
          // This is some STUFF to preload, maybe, if you were redirected from ?action=post2 back due to errors :)
          $settings['locked'] = @$_SESSION['locked'] ? (int)$_SESSION['locked'] : (int)@$_REQUEST['locked'];
          $settings['sticky'] = @$_SESSION['sticky'] ? (int)$_SESSION['sticky'] : (int)@$_REQUEST['sticky'];
          $settings['subject'] = @$subject ? $subject : (@$_SESSION['subject'] ? clean($_SESSION['subject']) : clean(@$_REQUEST['subject']));
          // If the subject is empty, make it Re: Topic Subject
          $settings['subject'] = $settings['subject'] ? $settings['subject'] : 'Re: '.$row['subject'];
          $settings['body'] = @$body ? $body : (@$_SESSION['body'] ? clean($_SESSION['body']) : ($quote ? $quote : clean(@$_REQUEST['body'])));
          $settings['board'] = $row['bid'];
          $settings['topic'] = $row['tid'];
          // Load the preview of the topic ;)
          loadPreview();
          if ($edit)
            loadForum('Post','Edit');
          else
            loadForum('Post','Reply');
          // Undelete a few things ;)
          unset($_SESSION['subject'], $_SESSION['body'], $_SESSION['sticky'], $_SESSION['locked'], $_SESSION['board']);
        }
        else {
          // They can't access the board that the topic is in, why should they be able to post, make them think this topic doesnt exist.
          $settings['page']['title'] = $l['forum_error_title'];
          loadForum('Post','CantPost');
        }
      }
      else {
        // The topic doesn't exist :o
        $settings['page']['title'] = $l['forum_error_title'];
        loadForum('Post','CantPost');        
      }
    }
    else {
      // They couldn't have posted in the first place ._.
      $settings['page']['title'] = $l['forum_error_title'];
      loadForum('Post','CantPost');    
    }
  }
  elseif(!empty($_REQUEST['board'])) {
    // :o What Board?
    $board_id = (int)addslashes(mysql_real_escape_string($_REQUEST['board']));    
    // K, they are making a new topic
    // But can they? :)
    if(canforum('post_new', $board_id)) {
      // Okay, their permissions say yes, but lets see what the forum says! :D!
      $result = sql_query("
         SELECT 
           b.bid, b.who_view
         FROM {$db_prefix}boards AS b
         WHERE b.bid = $board_id");
      // Well, does this board exist? ._.
      if(mysql_num_rows($result)>0) {
        while($row = mysql_fetch_assoc($result))
          $who_view = @explode(",", $row['who_view']);
        if((in_array($user['group'], $who_view)) || $user['group']==1) {
          $settings['page']['title'] = $l['forum_startnew'];
          // This is some STUFF to preload, maybe, if you were redirected from ?action=post2 back due to errors :)
          $settings['locked'] = @$_SESSION['locked'] ? (int)$_SESSION['locked'] : (int)@$_REQUEST['locked'];
          $settings['sticky'] = @$_SESSION['sticky'] ? (int)$_SESSION['sticky'] : (int)@$_REQUEST['sticky'];
          $settings['subject'] = @$_SESSION['subject'] ? clean($_SESSION['subject']) : clean(@$_REQUEST['subject']);
          $settings['body'] = @$_SESSION['body'] ? clean($_SESSION['body']) : clean(@$_REQUEST['body']);          
          $settings['board'] = (int)$_REQUEST['board'];
          unset($_SESSION['subject'], $_SESSION['body'], $_SESSION['sticky'], $_SESSION['locked'], $_SESSION['board']);
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
      loadForum('Post','CantPost');
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
  $what = isset($_REQUEST['board']) ? 'new_topic' : 'post_reply';
  // Do they want to post a new topic? And can they post it in the board?
  if($what == 'new_topic' && canforum('post_new', (int)@$_REQUEST['board']) && postable(@$_REQUEST['board'])) {
    // Just sanitize a variable
    $board_id = (int)@$_REQUEST['board'];
    // Is the subject within the allowed length?
    if (inlength('post_subject',strlen(@$_REQUEST['subject'])))
      $_SESSION['error'] = $l['post_error_subject_'.inlength('post_subject',strlen(@$_REQUEST['subject']))];
    // Is the post text within the allowed length?
    elseif (inlength('post',strlen($_REQUEST['body'])))
      $_SESSION['error'] = $l['post_error_body_'.inlength('post',strlen(@$_REQUEST['body']))];
    // Was there an error?
    if (isset($_SESSION['error'])) {
      $_SESSION['error_values'] = serialize(array('subject'=>clean(@$_REQUEST['subject']),'body'=>clean(@$_REQUEST['body']),
                                        'sticky'=>(int)@$_REQUEST['sticky'],'locked'=>(int)@$_REQUEST['locked']));
      redirect("forum.php?action=post;board={$board_id}");
    }
    // There wasn't an error
    else {
      $isSticky = canforum('sticky_topic') ? (int)@$_REQUEST['sticky'] : 0;
      $isLocked = canforum('lock_topic') ? (int)@$_REQUEST['locked'] : 0;
      $result = sql_query("
        INSERT INTO {$db_prefix}topics
        (`sticky`,`locked`,`bid`,`starter_id`,`topic_starter`,`ender_id`,`topic_ender`)
        VALUES('$isSticky','$isLocked','$board_id','{$user['id']}','{$user['name']}','{$user['id']}','{$user['name']}')");
      // Inserting the topic in the topic table is done, but that doesn't mean its finished :P
      $topic_id = mysql_insert_id();
      $subject = clean($_REQUEST['subject']);
      $post_time = time();
      $body = clean($_REQUEST['body']);
      $result = sql_query("
      INSERT INTO {$db_prefix}messages
      (`tid`,`bid`,`uid`,`subject`,`post_time`,`poster_name`,`poster_email`,`ip`,`body`)
      VALUES('$topic_id','$board_id','{$user['id']}','$subject','$post_time','{$user['name']}','{$user['email']}','{$user['ip']}','{$body}')");
      $msg_id = mysql_insert_id();
      sql_query("UPDATE {$db_prefix}topics SET `first_msg` = '$msg_id', `last_msg` = '$msg_id' WHERE `tid` = '$topic_id'");
      // Update a few things :o like post count, number of posts and topics inside the board  
      sql_query("UPDATE {$db_prefix}members SET `numposts` = numposts + 1 WHERE `id` = '{$user['id']}'");
      sql_query("UPDATE {$db_prefix}boards SET `numtopics` = numtopics + 1, `numposts` = numposts + 1, `last_msg` = '$msg_id', `last_uid` = '{$user['id']}', `last_name` = '{$user['name']}' WHERE `bid` = '$board_id'");
      // Insert a row to show they have posted here :)
      sql_query("REPLACE INTO {$db_prefix}message_logs (`uid`,`tid`,`mid`) VALUES('{$user['id']}','$topic_id','$msg_id')");
      // Delete anything from board logs with the board ID of $board_id, there is a new post in town!
      sql_query("DELETE FROM {$db_prefix}board_logs WHERE `bid` = '$board_id' AND `uid` != '{$user['id']}'");
      // Log that this member has viewed this topic
      if ($user['is_logged']) {
        $result = sql_query("SELECT * FROM {$db_prefix}topic_logs WHERE `tid` = '$topic_id' AND `uid` = '{$user['id']}'");
        if(mysql_num_rows($result)==0) {
          sql_query("
            REPLACE INTO {$db_prefix}topic_logs
            (`tid`,`uid`)
           VALUES ($topic_id, {$user['id']})");
        }
      }
      unset($_SESSION['subject'], $_SESSION['body'], $_SESSION['sticky'], $_SESSION['locked'], $_SESSION['board']);
      redirect("forum.php?board=".clean_header($board_id));
    }
  }
  // Are they trying to create or edit a post?
  elseif ($what == 'post_reply' && (canforum('post_reply', BoardFromTopic(@$_REQUEST['topic'])) || canforum('edit_own', BoardFromTopic(@$_REQUEST['topic'])) && @$_REQUEST['edit'] && postOwner(@$_REQUEST['edit']) == $user['id']) || (canforum('edit_any', BoardFromTopic(@$_REQUEST['topic'])) && @$_REQUEST['edit'])) {
    $topic_id = (int)$_REQUEST['topic'];
    // Check if the topic is locked
    $locked = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}topics WHERE `tid` = '$topic_id'"));
    if (!$locked['locked']) {
      // It isn't
      // Hmm, make sure it is at least filled out, ya know? :P
      if (strlen($_REQUEST['body']) > 2) {
        $subject = @clean($_REQUEST['subject']);
        $body = clean($_REQUEST['body']);
        
        if (count(explode(" ",$body)) > 1337 && stripos($body,"xkcd") !== false) {
          $body.="\n\n    [url=http://xkcd.com/406/]-Summer Glau[/url]";
        }
        
        $board_id = boardfromTopic($topic_id);
        // Get a couple options, like locked topic, sticky topic, etc.
        $options = array();
        $options['sticky'] = canforum('sticky_topic', $board_id) ? (int)@$_REQUEST['sticky'] : 0;
        $options['locked'] = canforum('lock_topic', $board_id) ? (int)@$_REQUEST['locked'] : 0;
        // If they stickied the topic or locked it, we need to update the original topic :)
        if(canforum('sticky_topic', $board_id) || canforum('lock_topic', $board_id)) {
          sql_query("UPDATE {$db_prefix}topics SET `sticky` = '{$options['sticky']}', `locked` = '{$options['locked']}' WHERE tid = '$topic_id'");
        }
        // No Subject? No Problem!
        if (empty($subject)) {
          // Are they editing a post?
          if ($edit = (int)@$_REQUEST['edit']) {
            $subject = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages WHERE mid = '$edit'"));
            $subject = $subject['subject'];
          }
          else {
            $subject = mysql_fetch_assoc(sql_query("
              SELECT
                *
              FROM {$db_prefix}topics AS t
                LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = t.first_msg
              WHERE t.tid = '$topic_id'"));
            $subject = "Re: ".$subject['subject'];
          }
        }
        // Is a message being edited and are they allowed to do it?
        if ($edit = (int)@$_REQUEST['edit']) {
          if ((canforum('edit_own', BoardFromTopic($topic_id)) && postOwner(@$_REQUEST['edit']) == $user['id']) || (canforum('edit_any', BoardFromTopic($topic_id)))) {
            // Yep!
            sql_query("UPDATE {$db_prefix}messages SET `subject` = '$subject', `body` = '$body', `uid_editor` = '{$user['id']}', `editor_name` = '{$user['name']}', `edit_time` = '".time()."' WHERE `mid` = '$edit'");
            redirect('forum.php?topic='.clean_header($topic_id));
          }
        }
        else {
          // Nope!
          $post_time = time();
          sql_query("INSERT INTO {$db_prefix}messages
                     (`tid`,`bid`,`uid`,`subject`,`post_time`,`poster_name`,`poster_email`,`ip`,`body`)
               VALUES('$topic_id','$board_id','{$user['id']}','$subject','$post_time','{$user['name']}','{$user['email']}','{$user['ip']}','{$body}')");
          $msg_id = mysql_insert_id();
          sql_query("UPDATE {$db_prefix}topics SET `last_msg` = '$msg_id', `ender_id` = '{$user['id']}', `topic_ender` = '{$user['name']}', `num_replies` = num_replies + 1 WHERE `tid` = '$topic_id'");
          sql_query("UPDATE {$db_prefix}members SET `numposts` = numposts + 1 WHERE `id` = '{$user['id']}'");
          sql_query("UPDATE {$db_prefix}boards SET `numposts` = numposts + 1, `last_msg` = '$msg_id', `last_uid` = '{$user['id']}', `last_name` = '{$user['name']}' WHERE `bid` = '$board_id'");
          // Delete anything from board logs with the board ID of $board_id, unless they are th current member
          sql_query("DELETE FROM {$db_prefix}board_logs WHERE `bid` = '$board_id' AND `uid` != '{$user['id']}'");
          // Delete anything from topic logs with the topic ID of $topic_id, unless they are th current member
          sql_query("DELETE FROM {$db_prefix}topic_logs WHERE `tid` = '$topic_id' AND `uid` != '{$user['id']}'");
          // Show they posted in this topic =D
          sql_query("REPLACE INTO {$db_prefix}message_logs (`uid`,`tid`,`mid`) VALUES('{$user['id']}','$Topic_ID','$msg_id')");
          unset($_SESSION['subject'], $_SESSION['body'], $_SESSION['sticky'], $_SESSION['locked'], $_SESSION['board']);
          redirect("forum.php?board=$board_id");
        }
      }
      else {
        $_SESSION['error'] = $l['post_error_body_short'];
        $_SESSION['subject'] = @$_REQUEST['subject'];
        $_SESSION['body'] = @$_REQUEST['body'];
        $_SESSION['sticky'] = (int)@$_REQUEST['sticky'];
        $_SESSION['locked'] = (int)@$_REQUEST['locked'];
        $_SESSION['board'] = (int)@$_REQUEST['board'];
        if (@$_REQUEST['edit'])
          redirect("forum.php?action=post;topic=$topic_id;edit=".clean(@$_REQUEST['edit']));
        else
          redirect("forum.php?action=post;topic=$topic_id");
      }
    }
    else
      // This is for if the topic is locked
      die('Topic is locked.'); // This also needs work
  }
  else
    // Uhh, yeah, this is for if they cant post or reply to the requested thingy...
    die('You don\'t have permission to do that.'); // Yeah this needs work
}

/* This function checks if they can post in the board or topic by ID */
function postable($id, $which = 0) {
global $db_prefix, $settings, $user;
  // Make sure it is an int ;]
  $id = (int)$id;
  $which = (int)$which;
  // Which are we checking? Board ID or Topic ID? 0 = board, 1 = topic
  if($user['group']!=1) {
    if(!$which) {
      $result = sql_query("
        SELECT
          b.bid, b.who_view
        FROM {$db_prefix}boards AS b
        WHERE b.bid = $id
        LIMIT 1");
      // Does the board even exist?
      if(mysql_num_rows($result)) {
        $row = mysql_fetch_assoc($result);
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
  else {
    return true;
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

// This will load the preview of the current topic...
function loadPreview() {
global $db_prefix, $settings, $Topic_ID, $user;
  $result = sql_query("
    SELECT
     msg.tid, msg.mid, msg.uid, msg.poster_name, msg.body,
     mem.id, mem.display_name AS username, IFNULL(mem.display_name, msg.poster_name) AS username
    FROM {$db_prefix}messages AS msg
      LEFT JOIN {$db_prefix}boards AS b ON b.bid = msg.bid
      LEFT JOIN {$db_prefix}members AS mem ON mem.id = msg.uid
    WHERE msg.tid = '$Topic_ID' AND msg.uid = {$user['board_query']}
    ORDER BY msg.mid DESC LIMIT 6");
  $settings['preview'] = array();
  while($row = mysql_fetch_assoc($result)) {
  
  }
}
?>
