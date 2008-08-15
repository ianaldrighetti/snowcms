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
//              News.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

function News() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // This prepares the news for display...
  
  // Are they viewing the ?action=news, or ?action=news&id=specific_news
  if(empty($_REQUEST['id'])) {
    $result = sql_query("
      SELECT
        n.news_id AS id, n.poster_id, n.poster_name, n.subject, n.body, n.body,
        n.modify_time AS post_date, IFNULL(n.modify_time, n.post_time) AS post_date,
        n.numViews, n.numComments, n.allow_comments,
        mem.id, mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username
      FROM {$db_prefix}news AS n
        LEFT JOIN {$db_prefix}members AS mem ON mem.id = n.poster_id
      ORDER BY n.post_time DESC");
    $news = array();
    // Is there even any news? :O
    if(mysql_num_rows($result)) {
      while($row = mysql_fetch_assoc($result)) {
        $news[] = array(
          'id' => $row['news_id'],
          'poster_id' => $row['poster_id'],
          'poster_name' => $row['username'],
          'subject' => $row['subject'],
          'body' => stripslashes($row['body']),
          'post_date' => formattime($row['post_date']),
          'numViews' => $row['numViews'],
          'numComments' => $row['numComments'],
          'allow_comments' => $row['allow_comments']
        );
        mysql_free_result($result);
      }
      // Load it up :D (the theme thingy)
      $settings['page']['title'] = $l['news_title'];
      $settings['news'] = $news;
      unset($news);
      loadTheme('News');
    }
    else {
      // No news? :O
      $settings['page']['title'] = $l['news_title'];
      loadTheme('News','None');
    }
  }
  else {
    // What News do they want?
    $news_id = (int)$_REQUEST['id'];
    $result = sql_query("
      SELECT
        n.news_id AS id, n.poster_id, n.poster_name, n.subject, n.body, n.body,
        n.modify_time AS post_date, IFNULL(n.modify_time, n.post_time) AS post_date,
        n.numViews, n.numComments, n.allow_comments,
        mem.id, mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username
      FROM {$db_prefix}news AS n
        LEFT JOIN {$db_prefix}members AS mem ON mem.id = n.poster_id
      WHERE n.news_id = $news_id");
    $news = array();
    // Is there even any news? :O
    if(mysql_num_rows($result)) {
      while($row = mysql_fetch_assoc($result)) {
        $news = array(
          'id' => $row['news_id'],
          'poster_id' => $row['poster_id'],
          'poster_name' => $row['username'],
          'subject' => $row['subject'],
          'body' => stripslashes($row['body']),
          'post_date' => formattime($row['post_date']),
          'numViews' => $row['numViews'],
          'numComments' => $row['numComments'],
          'allow_comments' => $row['allow_comments']
        );
        mysql_free_result($result);
      }
      // Load it up :D (the theme thingy)
      $settings['page']['title'] = $news['subject'];
      $settings['news'] = $news;
      unset($news);
      loadTheme('News','Single');
    }
    else {
      // It doesn't exist? :O
      $settings['page']['title'] = $l['news_title'];
      loadTheme('News','DoesntExist');
    }  
  }
}
?>