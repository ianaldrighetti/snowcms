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
  $Topic_ID = addslashes(mysql_real_escape_string($_REQUEST['topic']));
  $result = sql_query("
    SELECT 
      t.tid, t.bid, b.who_view, b.bid
    FROM {$db_prefix}topics AS t
      LEFT JOIN {$db_prefix}boards AS b ON b.bid = t.bid
    WHERE t.tid = '$Topic_ID'");
  // Can they even view this topic? As in, is this in a board they aren'tallowed to view?
  while($row = mysql_fetch_assoc($result))  
    $who_view = @explode(",", $row['who_view']);
  $who_view[] = 1;
  if(in_array($user['group'], $who_view)) {
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
        mem.signature, mem.group, mem.email, mem.numposts
      FROM {$db_prefix}topics AS t
        LEFT JOIN {$db_prefix}messages AS msg ON msg.tid = t.tid
        LEFT JOIN {$db_prefix}members AS mem ON mem.id = msg.uid
        LEFT JOIN {$db_prefix}membergroups AS grp ON grp.group_id = mem.group        
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
          'numposts' => $row['numposts']
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
function paginate($topic) {
global $db_prefix, $settings;

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
           $pagination .= '[ <a href="'.$cmsurl.'?topic='.$topic.'&page='.$i.'">'.$i.'</a> ] ';
         }
         else {
           $pagination .= '<a href="'.$cmsurl.'?topic='.$topic.'&page='.$i.'">'.$i.'</a> ';
         }
       }
       $pagination .= ' <a href="'.$cmsurl.'?topic='.$topic.'&page='.$lastpage.'">'.$l['topic_lastpage'].'</a>';
     }
     else {
       if(($page-1)!=1)
         $pagination .= ' <a href="'.$cmsurl.'?topic='.$topic.'&page=1">'.$l['topic_firstpage'].'</a> ';
       for($i = ($page-1); $i <= ($page+1); $i++) {
         if($page==$i) {
           $pagination .= '[ <a href="'.$cmsurl.'?topic='.$topic.'&page='.$i.'">'.$i.'</a> ] ';
         }
         else {
           $pagination .= '<a href="'.$cmsurl.'?topic='.$topic.'&page='.$i.'">'.$i.'</a> ';
         }       
       }
       if(($page+1)!=$lastpage)
         $pagination .= ' <a href="'.$cmsurl.'?topic='.$topic.'&page='.$lastpage.'">'.$l['topic_lastpage'].'</a>';
     }
   }
   else {
     $pagination .= ' <a href="'.$cmsurl.'?topic='.$topic.'&page=1">'.$l['topic_firstpage'].'</a> ';
     for($i = ($lastpage-2); $i <= $lastpage; $i++) {
       if($page==$i) {
         $pagination .= '[ <a href="'.$cmsurl.'?topic='.$topic.'&page='.$i.'">'.$i.'</a> ] ';
       }
       else {
         $pagination .= '<a href="'.$cmsurl.'?topic='.$topic.'&page='.$i.'">'.$i.'</a> ';
       }
     }     
   }     
 }
 elseif($lastpage==1) {
   $pagination = '[1]';
 }
 else {
   for($i = 1; $i <= 3; $i++) {
     if($page==$i) {
       $pagination .= '[ <a href="'.$cmsurl.'?topic='.$topic.'&page='.$i.'">'.$i.'</a> ] ';
     }
     else {
       $pagination .= '<a href="'.$cmsurl.'?topic='.$topic.'&page='.$i.'">'.$i.'</a> ';
     }
   }
 }
 $info['pagination'] = $pagination;
 $info['start'] = $start;
 return $info;
}
?>