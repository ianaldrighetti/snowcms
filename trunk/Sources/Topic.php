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
//             Topic.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function loadTopic() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('view_forum')) {
    $Topic_ID = (int)$_REQUEST['topic'];
    $result = sql_query("
      SELECT 
        t.tid, t.bid, b.who_view, b.bid
      FROM {$db_prefix}topics AS t
        LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
      WHERE t.tid = '$Topic_ID' AND {$user['board_query']}");
    // Can they even view this topic? As in, is this in a board they aren't allowed to view?
    if(mysql_num_rows($result)) {
      // Log that this member has viewed this topic
      if ($user['is_logged']) {
        $result = sql_query("SELECT * FROM {$db_prefix}topic_logs WHERE `tid` = '$Topic_ID' AND `uid` = '{$user['id']}'");
        if(mysql_num_rows($result)==0) {
          sql_query("
            REPLACE INTO {$db_prefix}topic_logs
				      (`tid`,`uid`)
	          VALUES ($Topic_ID, {$user['id']})");
	      }
	      }
	    // Update times this topic has been viewed
      $row = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}topics WHERE `tid` = '$Topic_ID'"));
      $numviews = $row['numviews'] + 1;
      sql_query("UPDATE {$db_prefix}topics SET `numviews` = '$numviews' WHERE `tid` = '$Topic_ID'");
      
      if(!empty($_REQUEST['msg'])) {
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
        // If the number of messages is less then $settings['topic_posts_per_page'], no need to do anything fancy :]
        if($num_msg<$settings['topic_posts_per_page']) {
          redirect("forum.php?topic={$topic_id}#mid{$msg_id}");
        }
        else {
          $num_pages = ceil($num_msg/$settings['topic_posts_per_page']);
          $mids = array();
          while($row = mysql_fetch_assoc($result))
            $mids[] = $row['mid'];
          // If $mid_page is not 0, then we found the page =D!
          if(in_array($msg_id, $mids)) {
            $msgs = 0;
            $page = 1;
            $mid_page = 0;
            foreach($mids as $i => $mid_val) {
              $msgs++;
              if($mids[$i]==$msg_id) {
               $mid_page = $page;
              }
              if($msgs==$settings['topic_posts_per_page']) {
                $msgs = 1;
                $page++;
              }
            }
            // !!! $page_id != 1 BAD!
            if($mid_page!=1)
              redirect("forum.php?topic={$topic_id};page={$mid_page}#mid{$msg_id}");
            else
              redirect("forum.php?topic={$topic_id}#mid{$msg_id}");
          }
          else {
            // Sorry bub, we didn't find that Message ID...
            redirect("forum.php?topic={$topic_id}");
          }
        }
      }
      // We don't want ;page=1 :P
      if(!empty($_REQUEST['page']) && $_REQUEST['page']==1)
        redirect("forum.php?topic={$_REQUEST['topic']}");
      $info = paginate($Topic_ID);
      $pagination = $info['pagination'];
      $start = $info['start'];
      $result = sql_query("
        SELECT
          t.tid, t.first_msg, msg.subject, msg.mid
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS msg ON msg.mid = t.first_msg
        WHERE t.tid = $Topic_ID");
      while($row = mysql_fetch_assoc($result))
        $title = $row['subject'];
      $result = sql_query("
        SELECT
          t.tid, t.sticky, t.locked, t.bid, t.first_msg, grp.group_id, grp.groupname,
          msg.mid, msg.tid, msg.bid, msg.uid, msg.subject, msg.post_time, msg.poster_name, msg.ip, msg.body,
          mem.id AS uid, mem.username, IFNULL(mem.username, msg.poster_name) AS username, mem.display_name, 
          mem.signature, mem.group, mem.email, mem.numposts, ol.user_id, ol.last_active
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS msg ON msg.tid = t.tid
          LEFT JOIN {$db_prefix}members AS mem ON mem.id = msg.uid
          LEFT JOIN {$db_prefix}membergroups AS grp ON grp.group_id = mem.group
          LEFT JOIN {$db_prefix}online AS ol ON ol.user_id = mem.id
        WHERE t.tid = $Topic_ID
        ORDER BY msg.mid ASC LIMIT $start,{$settings['topic_posts_per_page']}");
        while($row = mysql_fetch_assoc($result)) {
          if($row['display_name']!=null)
            $row['username'] = $row['display_name'];
          $posts[] = array(
            'tid' => $row['tid'],
            'mid' => $row['mid'],
            'bid' => $row['bid'],
            'uid' => $row['uid'],
            'subject' => $row['subject'],
            'post_time' => formattime($row['post_time']),
            'body' => bbc($row['body']),
            'username' => $row['username'] ? $row['username'] : $row['poster_name'],
            'signature' => bbc($row['signature']),
            'membergroup' => $row['groupname'],
            'numposts' => $row['numposts'],
            'status' => $row['last_active'] ? true : false,
            'can' => array(
                         'edit' => canforum('edit_any', $row['bid']) ? true : edit($row['uid'], canforum('edit_own', $row['bid'])),
                         'del' => canforum('delete_any', $row['bid']) ? true : del($row['uid'], canforum('delete_own')),
                         'split' => Splitable($row['first_msg'], $row['mid'], canforum('split_topic', $row['bid']))
                       )
          );
          $bid = $row['bid'];
        }
        $settings['page']['title'] = $title;
        $settings['posts'] = $posts;
        $settings['pagination'] = $pagination;
        $settings['bid'] = (int)$bid;
        loadForum('Topic');
    }
    else {
      // Sneaky one aren't you? Trying to access a topic in a board you can't access :P Well we won't have it! ^_^
      $settings['page']['title'] = $l['forum_error_title'];    
      loadForum('Error','TNotAllowed');
    }
  }
  else {
    $settings['page']['title'] = $l['forum_error_title'];
    loadForum('Error','BNotAllowed');
  }   
}
function paginate($topic) {
global $cmsurl, $db_prefix, $settings;

 $query = mysql_query("SELECT * FROM {$db_prefix}messages WHERE `tid` = $topic");
 $num_posts = mysql_num_rows($query);

 $lastpage = ceil($num_posts/$settings['topic_posts_per_page']);

 if(!empty($_REQUEST['page'])) {
   $page = (int)$_REQUEST['page'];
 }
 else {
   $page = 1;
 }
 if($page > $lastpage) {
   $page = $lastpage;
 }
 elseif($page < 1) {
   $page = 1;
 }
 if($page==1) {
   $start = 0;
 }
 elseif($page > 1) {
   $start = ($page-1)*$settings['topic_posts_per_page'];
 }

 $pagination = '';
 if($lastpage > 3) {
   if($page!=$lastpage) {
     if($page==1) {
       for($i = 1; $i <= 3; $i++) {
         if($page==$i) {
           $pagination .= '[ <a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$i.'">'.$i.'</a> ] ';
         }
         else {
           $pagination .= '<a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$i.'">'.$i.'</a> ';
         }
       }
       $pagination .= ' <a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$lastpage.'">'.$l['topic_lastpage'].'</a>';
     }
     else {
       if(($page-1)!=1)
         $pagination .= ' <a href="'.$cmsurl.'forum.php?topic='.$topic.';page=1">'.$l['topic_firstpage'].'</a> ';
       for($i = ($page-1); $i <= ($page+1); $i++) {
         if($page==$i) {
           $pagination .= '[ <a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$i.'">'.$i.'</a> ] ';
         }
         else {
           $pagination .= '<a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$i.'">'.$i.'</a> ';
         }       
       }
       if(($page+1)!=$lastpage)
         $pagination .= ' <a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$lastpage.'">'.$l['topic_lastpage'].'</a>';
     }
   }
   else {
     $pagination .= ' <a href="'.$cmsurl.'forum.php?topic='.$topic.';page=1">'.$l['topic_firstpage'].'</a> ';
     for($i = ($lastpage-2); $i <= $lastpage; $i++) {
       if($page==$i) {
         $pagination .= '[ <a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$i.'">'.$i.'</a> ] ';
       }
       else {
         $pagination .= '<a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$i.'">'.$i.'</a> ';
       }
     }     
   }     
 }
 elseif($lastpage==1) {
   $pagination = '[ 1 ]';
 }
 else {
   for($i = 1; $i <= $lastpage; $i++) {
     if($page==$i) {
       $pagination .= '[ <a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$i.'">'.$i.'</a> ] ';
     }
     else {
       $pagination .= '<a href="'.$cmsurl.'forum.php?topic='.$topic.';page='.$i.'">'.$i.'</a> ';
     }
   }
 }
 $info['pagination'] = $pagination;
 $info['start'] = $start;
 return $info;
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
  if(canforum('sticky_topic', $board_id) && validateSession($_REQUEST['sc'])) {
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
    FROM {$db_prefix}topics
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