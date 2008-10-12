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
//                  Topic.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));
  
function loadTopic() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if (can('view_forum')) {
    $Topic_ID = (int)$_REQUEST['topic'];
    $result = sql_query("
      SELECT 
        t.tid, t.bid, b.who_view, b.bid
      FROM {$db_prefix}topics AS t
        LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
      WHERE t.tid = '$Topic_ID' AND {$user['board_query']}");
    // Can they even view this topic? As in, is this in a board they aren't allowed to view?
    if (mysql_num_rows($result)) {
      // Log that this member has viewed this topic
      if ($user['is_logged']) {
        $result = sql_query("SELECT * FROM {$db_prefix}topic_logs WHERE `tid` = '$Topic_ID' AND `uid` = '{$user['id']}'");
        if (!mysql_num_rows($result)) {
          sql_query("
            REPLACE INTO {$db_prefix}topic_logs
				      (`tid`,`uid`)
	          VALUES ($Topic_ID, {$user['id']})");
	      }
	    }
	    // Update times this topic has been viewed, only if this wasn't the last viewed topic :P
      if(@$_SESSION['last_topic'] != $Topic_ID)
        sql_query("UPDATE {$db_prefix}topics SET `numviews` = numviews + 1 WHERE `tid` = $Topic_ID");
      // Now set this as the last viewed topic xD
      $_SESSION['last_topic'] = $Topic_ID;
      if (!empty($_REQUEST['msg'])) {
        // The fun part! WEEEEEEEEEE!
        // We need to redirect ?topic=topic_id;msg=msg_id to the right paginated page...
        $msg_id = (int)$_REQUEST['msg'];
        $topic_id = $Topic_ID;
        // Select all messages with the tid of $topic_id
        $result = sql_query("
          SELECT
            msg.mid, msg.tid
          FROM {$db_prefix}messages AS msg
          WHERE msg.tid = $topic_id");
        // How many messages are there? :o
        $num_msg = mysql_num_rows($result)-1;
        // If the number of messages is less then $settings['num_posts'], no need to do anything fancy :]
        if ($num_msg < $settings['num_posts']) {
          redirect("forum.php?topic={$topic_id}#mid{$msg_id}");
        }
        else {
          $num_pages = ceil($num_msg/$settings['num_posts']);
          $mids = array();
          while ($row = mysql_fetch_assoc($result))
            $mids[] = $row['mid'];
          // If $mid_page is not 0, then we found the page =D!
          if (in_array($msg_id, $mids)) {
            $msgs = 0;
            $page = 0;
            $mid_page = 0;
            foreach ($mids as $i => $mid_val) {
              $msgs++;
              if($mids[$i] == $msg_id) {
               $mid_page = $page;
              }
              if ($msgs == $settings['num_posts']) {
                $msgs = 1;
                $page++;
              }
            }
            if ($mid_page == 0)
              redirect("forum.php?topic=$topic_id#mid$msg_id");
            else
              redirect("forum.php?topic=$topic_id;pg=$mid_page#mid$msg_id");
          }
          else {
            // Sorry bub, we didn't find that Message ID...
            redirect("forum.php?topic={$topic_id}");
          }
        }
      }
      $start = (int)@$_REQUEST['pg'] * $settings['num_posts'];
      $result = sql_query("
        SELECT
          t.tid, t.first_msg, msg.subject, msg.mid
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = t.first_msg
        WHERE t.tid = $Topic_ID");
      while($row = mysql_fetch_assoc($result))
        $topic_name = $row['subject'];
      $posts = array();
      $result = sql_query("
        SELECT
          *, t.tid, t.sticky, t.locked, t.bid, t.first_msg, grp.group_id, grp.groupname,
          msg.mid, msg.tid, msg.bid, msg.uid, msg.subject, msg.post_time, msg.poster_name, msg.ip, msg.body,
          mem.id AS uid, mem.username, IFNULL(mem.username, msg.poster_name) AS username, mem.display_name, mem.avatar,
          mem.signature, mem.group, mem.email, mem.numposts, ol.user_id, ol.last_active, editor.display_name as editor_display_name
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS msg ON msg.tid = t.tid
          LEFT JOIN {$db_prefix}members AS mem ON mem.id = msg.uid
          LEFT JOIN {$db_prefix}members AS editor ON editor.id = msg.uid_editor
          LEFT JOIN {$db_prefix}membergroups AS grp ON grp.group_id = mem.group
          LEFT JOIN {$db_prefix}online AS ol ON ol.user_id = mem.id
        WHERE t.tid = '$Topic_ID'
        ORDER BY msg.mid ASC LIMIT $start, {$settings['num_posts']}");
        while ($row = mysql_fetch_assoc($result)) {
          if ($row['display_name'] != null)
            $row['username'] = $row['display_name'];
          $posts[] = array(
            'tid' => $row['tid'],
            'mid' => $row['mid'],
            'bid' => $row['bid'],
            'uid' => $row['uid'],
            'sticky' => $row['sticky'],
            'locked' => $row['locked'],
            'subject' => $row['subject'],
            'post_time' => formattime($row['post_time'],2),
            'body' => bbc($row['body']),
            'username' => $row['display_name'] ? $row['display_name'] : $row['poster_name'],
            'uid_editor' => $row['uid_editor'],
            'editor_name' => $row['editor_display_name'] ? $row['editor_display_name'] : $row['editor_name'],
            'edit_time' => $row['edit_time'],
            'avatar' => $row['avatar'],
            'signature' => bbc($row['signature']),
            'membergroup' => $row['groupname'],
            'numposts' => $row['numposts'],
            'status' => $row['last_active'] ? true : false,
            'can' => array(
                         'edit' => canforum('edit_any', $row['bid']) ? true : edit($row['uid'], canforum('edit_own', $row['bid'])),
                         'del' => canforum('delete_any', $row['bid']) ? true : del($row['uid'], canforum('delete_own', $row['bid'])),
                         'split' => Splitable($row['first_msg'], $row['mid'], canforum('split_topic', $row['bid']))
                       )
          );
          $bid = $row['bid'];
        }
        $settings['page']['page'] = (int)@$_REQUEST['pg'];
        $total_posts = mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}messages WHERE `tid` = '$Topic_ID'"));
        $settings['page']['page_last'] = $total_posts / $settings['num_posts'];
        $settings['page']['title'] = $topic_name;
        $settings['page']['topic-name'] = $topic_name;
        $settings['posts'] = $posts;
        $settings['topic'] = (int)@$_REQUEST['topic'];
        $settings['sticky'] = @$posts[0]['sticky'];
        $settings['locked'] = @$posts[0]['locked'];
        $settings['bid'] = (int)@$bid;
        loadForum('Topic');
    }
    else {
      // Sneaky one aren't you? Trying to access a topic in a board you can't access :P Well we won't have it! ^_^
      $settings['page']['title'] = $l['topic_unknown_title'];    
      loadForum('Topic','UnknownTopic');
    }
  }
  // You aren't allowed to view the forum
  else
    redirect('forum.php');
}

function Sticky() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // We need to get the board, and check the permissions :P
  $topic_id = (int)$_REQUEST['topic'];
  $result = sql_query("
    SELECT
      t.tid, t.bid, t.sticky
    FROM {$db_prefix}topics AS t
    WHERE t.tid = $topic_id");
  if(!mysql_num_rows($result))
    $board_id = 0;
  else {
    $row = mysql_fetch_assoc($result);
    $board_id = $row['bid'];
  }
  // So. Can they?
  if(canforum('sticky_topic', $board_id) && validateSession(@$_REQUEST['sc'])) {
    // This seems simple :P just an update... Then Redirect back to the topic :D
    if($row['sticky'])
      $sticky = 0;
    else
      $sticky = 1;
    sql_query("UPDATE {$db_prefix}topics SET `sticky` = '$sticky' WHERE `tid` = '$topic_id'");
    redirect("forum.php?topic={$topic_id}");
  }
  else {
    $settings['page']['title'] = $l['topic_sticky_error'];
    loadTheme('Topic','ErrorSticky');
  }
}

function Lock() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // Same as Sticky(), can they? :P
  $topic_id = (int)$_REQUEST['topic'];
  $result = sql_query("
    SELECT
      t.tid, t.bid, t.locked
    FROM {$db_prefix}topics AS t
    WHERE t.tid = $topic_id");
  if(!mysql_num_rows($result)) 
    $board_id = 0;
  else {
    $row = mysql_fetch_assoc($result);
    $board_id = $row['bid'];
  }
  // Check if they can...
  if(canforum('sticky_topic', $board_id) && validateSession($_REQUEST['sc'])) {
    if($row['locked'])
      $lock = 0;
    else
      $lock = 1;
    sql_query("UPDATE {$db_prefix}topics SET `locked` = '$lock' WHERE `tid` = '$topic_id'");
    redirect("forum.php?topic={$topic_id}");
  }
  else {
    $settings['page']['title'] = $l['topic_lock_error'];
    loadTheme('Topic','ErrorLock');
  }
}

// Delete a post
function Delete() {
global $db_prefix;
  
  $msg = clean($_REQUEST['msg']);
  $msg_info = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages WHERE `mid` = '$msg'"));
  $member = $msg_info['uid'];
  $topic = $msg_info['tid'];
  $board = $msg_info['bid'];
  
  // Are they allowed to delete it?
  if (canforum('delete_any', $board) || del(PostOwner($msg), canforum('delete_own', $board))) {
    // Delete the message
    sql_query("DELETE FROM {$db_prefix}messages WHERE `mid` = '$msg'");
    // Lower the member's post count by one
    sql_query("UPDATE {$db_prefix}members SET `numposts` = `numposts` - 1 WHERE `id` = '$member'");
    // Lower the topic's reply count by one
    sql_query("UPDATE {$db_prefix}topics SET `num_replies` = `num_replies` - 1 WHERE `tid` = '$topic'");
    // Lower the boards's post count by one
    sql_query("UPDATE {$db_prefix}boards SET `numposts` = `numposts` - 1 WHERE `bid` = '$board'");
    // Are they trying to a delete a topic?
    $first_msg = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}topics WHERE `tid` = '$topic'"));
    if ($first_msg['first_msg'] == $msg) {
      // Lower the board's topic count by one
      sql_query("UPDATE {$db_prefix}boards SET `numtopics` = `numtopics` - 1 WHERE `bid` = '$board'");
      // Lower the board's message count by topic's replies
      sql_query("UPDATE {$db_prefix}topics AS `t` LEFT JOIN {$db_prefix}boards AS `b` ON `t`.`bid` = `b`.`bid` SET `b`.`numposts` = `b`.`numposts` - `t`.`num_replies` - 1 WHERE `t`.`tid` = '$topic'");
      // Delete the topic
      sql_query("DELETE FROM {$db_prefix}topics WHERE `tid` = '$topic'");
      // Delete all of the messages in the topic
      sql_query("DELETE FROM {$db_prefix}messages WHERE `tid` = '$topic'");
      // Delete the topic log
      sql_query("DELETE FROM {$db_prefix}topic_logs WHERE `tid` = '$topic'");
      redirect('forum.php?board='.$board);
    }
  }
  redirect('forum.php?topic='.$topic);
}

function Move() {
global $l, $settings, $user, $db_prefix;
  
  $topic = (int)@$_REQUEST['topic'];
  
  // Get the topic information
  $topic = $settings['page']['topic'] = mysql_fetch_assoc(sql_query("
                               SELECT
                                 *
                               FROM {$db_prefix}topics AS `t`
                                 LEFT JOIN {$db_prefix}messages AS `m`
                                   ON `t`.`first_msg` = `m`.`mid`
                                 LEFT JOIN {$db_prefix}boards AS `b`
                                   ON `t`.`bid` = `b`.`bid`
                               WHERE `t`.`tid` = '$topic'"));
  
  // Are they allowed to move topics and does the topic exist?
  if (canforum('move_any',$topic['bid']) && $topic) {
    // Have they already filled out the form and we are suppose to be just submitting it?
    if (!empty($_REQUEST['move-topic'])) {
      // Get the new board ID
      $new_board = (int)@$_REQUEST['board'];
      // Are they trying to move the topic to its current board?
      if ($new_board != $topic['bid']) {
        // Are they allowed to create topics where they are moving this topic to?
        if (canforum('post_new',$new_board)) {
          // Get the moved topic announcement topic's subject
          $subject = clean(@$_REQUEST['subject']);
          // Get the information about the new board to get its name
          $new_board_name = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}boards WHERE `bid` = '$new_board'"));
          // Get the moved topic announcement topic's body
          $body = str_replace('%board%',$new_board_name['name'],clean(@$_REQUEST['body']));
          // Lower the board's message count by topic's replies
          sql_query("UPDATE {$db_prefix}topics AS `t` LEFT JOIN {$db_prefix}boards AS `b` ON `t`.`bid` = `b`.`bid` SET `b`.`numposts` = `b`.`numposts` - `t`.`num_replies` WHERE `t`.`tid` = '{$topic['tid']}'");
          // Update the topic data
          sql_query("UPDATE {$db_prefix}topics SET `bid` = '$new_board' WHERE `tid` = '{$topic['tid']}'");
          // Update the message data
          sql_query("UPDATE {$db_prefix}messages SET `bid` = '$new_board' WHERE `tid` = '{$topic['tid']}'");
          // Raise the new board's topic count by one
          sql_query("UPDATE {$db_prefix}boards SET `numtopics` = `numtopics` + 1 WHERE `bid` = '$new_board'");
          // Raise the new board's message count by topic's posts
          sql_query("UPDATE {$db_prefix}topics AS `t` LEFT JOIN {$db_prefix}boards AS `b` ON `t`.`bid` = `b`.`bid` SET `b`.`numposts` = `b`.`numposts` + `t`.`num_replies` + 1 WHERE `t`.`tid` = '{$topic['tid']}'");
          // Create the moved topic announement topic
          sql_query("INSERT INTO {$db_prefix}topics (`sticky`,`locked`,`bid`,`starter_id`,`topic_starter`,`ender_id`,`topic_ender`)
                     VALUES('0','1','{$topic['bid']}','{$user['id']}','{$user['name']}','{$user['id']}','{$user['name']}')");
          $tid = mysql_insert_id();
          sql_query("INSERT INTO {$db_prefix}messages (`tid`,`bid`,`uid`,`subject`,`post_time`,`poster_name`,`poster_email`,`ip`,`body`)
                     VALUES('$tid','{$topic['bid']}','{$user['id']}','$subject','".time()."','{$user['name']}','{$user['email']}','{$user['ip']}','$body')");
          $mid = mysql_insert_id();
          sql_query("UPDATE {$db_prefix}topics SET `first_msg` = '$mid', `last_msg` = '$mid' WHERE `tid` = '$tid'");
          // Redirect them to the new board
          redirect('forum.php?topic='.$tid);
        }
        // They are not allowed to move topics to that board
        else {
          $_SESSION['error'] = $l['topic_move_error_notallowed'];
          redirect('forum.php?action=move;topic='.$topic['tid']);
        }
      }
      // They are trying to move the topic to its current board
      else {
          $_SESSION['error'] = $l['topic_move_error_sameboard'];
          redirect('forum.php?action=move;topic='.$topic['tid']);
      }
    }
    
    // Get the list of boards they can move the topic to
    $settings['page']['boards'] = array();
    $result = sql_query("SELECT * FROM {$db_prefix}boards WHERE `bid` != '{$topic['bid']}'");
    while ($row = mysql_fetch_assoc($result))
      // Are they are not allowed to create topics there
      if (canforum('post_new',$row['bid']))
        $settings['page']['boards'][] = $row;
    
    // Are there any boards they are allowed to move this topic to?
    if ($settings['page']['boards']) {
      // Load the theme
      $settings['page']['title'] = $l['topic_move_title'];
      loadForum('Topic','MoveTopic');
    }
    // They can't move the topic to any boards
    else {
      // Load the theme
      $settings['page']['title'] = $l['topic_move_noboards_title'];
      loadForum('Topic','MoveNoBoards');
    }
  }
  // They are not allowed to move topics or it doesn't exist, which?
  elseif ($topic)
    redirect('forum.php?topic='.$topic['tid']);
  // The topic doesn't exist
  else
    redirect('forum.php');
}

// More simple functions to aid in moderation...
function del($uid, $can) {
global $db_prefix, $user;
  if(!$can)
    return false;
  elseif($uid==$user['id'] && $can)
    return true;
  else
    return false;
}
function edit($uid, $can) {
global $db_prefix, $user;
  if(!$can)
    return false;
  elseif($uid==$user['id'] && $can)
    return true;
  else
    return false;
}
function Splitable($first_msg, $mid, $can) {
global $user;
  if($first_msg==$mid)
    return false;
  elseif($can)
    return true;
  else
    return false;
}
?>