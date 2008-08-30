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
  
  // Redirect post data into get data
  if (@$_POST['cat'])
    redirect('index.php?action=news;cat='.$_POST['cat']);
  
  // Are they adding a comment?
  if (@$_REQUEST['add-comment']) {
    // Clean the data of dirty injections
    $nid = clean(@$_REQUEST['nid']); // News ID
    $subject = clean(@$_REQUEST['subject']); // Subject
    $body = clean(@$_REQUEST['body']); // Body text
    
    // Process SQL query
    sql_query("INSERT {$db_prefix}news_comments (`nid`, `poster_id`, `poster_name`, `subject`, `body`, `post_time`) VALUES ('$nid','{$user['id']}','{$user['name']}','$subject','$body', '".time()."')");
    
    // Redirect the page to the main manage news page
    redirect('index.php?action=news;id='.clean_header(@$_GET['id']));
  }
  
  // Are they viewing the ?action=news, or ?action=news&id=specific_news
  if (empty($_REQUEST['id'])) {
    // The current page number
    $page = @$_REQUEST['pg'];
    
    // The first news article number of this page
    $start = $page * $settings['num_news_items'];
    
    // Setup category SQL
    if ($cat = (int)@$_REQUEST['cat'])
      $cat_sql = "WHERE `cat_id` = '$cat'";
    else
      $cat_sql = "";
    
    $result = sql_query("
      SELECT
        *, mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username
      FROM {$db_prefix}news AS n
        LEFT JOIN {$db_prefix}members AS mem ON mem.id = n.poster_id
      $cat_sql
      ORDER BY n.post_time DESC
      LIMIT $start, {$settings['num_news_items']}");
    $cat_sql = "AND `cat_id` = '$cat'";
    $news = array();
    // Is there even any news? :O
    if (mysql_num_rows($result)) {
      while ($row = mysql_fetch_assoc($result)) {
        $comments = mysql_fetch_assoc(sql_query("SELECT COUNT(*) FROM {$db_prefix}news_comments WHERE `nid` = '{$row['news_id']}' GROUP BY `nid`"));
        $category = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}news_categories WHERE `cat_id` = '{$row['cat_id']}'"));
        $news[] = array(
          'id' => $row['news_id'],
          'poster_id' => $row['poster_id'],
          'poster_name' => $row['username'],
          'subject' => $row['subject'],
          'cat_id' => $category['cat_id'],
          'cat_name' => $category['cat_name'],
          'body' => stripslashes($row['body']),
          'user_id' => $row['id'],
          'username' => $row['username'],
          'post_date' => formattime($row['post_time'],2),
          'numViews' => $row['numViews'],
          'numComments' => (int)$comments['COUNT(*)'],
          'allow_comments' => $row['allow_comments']
        );
      }
      
      // The first page number
      $settings['page']['first_page'] = $page + 1;
      // The previous page number
      $settings['page']['previous_page'] = $page - 1;
      // The current page number
      $settings['page']['current_page'] = $page;
      // The next page number
      $settings['page']['next_page'] = $page + 1;
      // The last page number
      $settings['page']['last_page'] = $page - 1;
      
      // Load news categories
      $result = sql_query("SELECT * FROM {$db_prefix}news_categories");
      while ($row = mysql_fetch_array($result))
        $settings['page']['categories'][] = $row;
      
      // Load it up :D (the theme thingy)
      $settings['page']['title'] = $l['news_title'];
      $settings['news'] = $news;
      unset($news);
      loadTheme('News');
    }
    else {
      // No news? :O
      $settings['page']['title'] = $l['news_nonews_title'];
      loadTheme('News','None');
    }
  }
  else {
    // What news do they want?
    $news_id = (int)$_REQUEST['id'];
    $result = sql_query("
      SELECT
        *, mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username
      FROM {$db_prefix}news AS n
        LEFT JOIN {$db_prefix}members AS mem ON mem.id = n.poster_id
      WHERE n.news_id = $news_id");
    $news = array();
    // Is there even any news? :O
    if (mysql_num_rows($result)) {
      while ($row = mysql_fetch_assoc($result)) {
        $category = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}news_categories WHERE `cat_id` = '{$row['cat_id']}'"));
        $news = array(
          'id' => $row['news_id'],
          'poster_id' => $row['poster_id'],
          'poster_name' => $row['username'],
          'subject' => $row['subject'],
          'cat_id' => $category['cat_id'],
          'cat_name' => $category['cat_name'],
          'body' => stripslashes($row['body']),
          'user_id' => $row['id'],
          'username' => $row['username'],
          'post_date' => formattime($row['post_time'],2),
          'numViews' => $row['numViews'],
          'allow_comments' => $row['allow_comments']
        );
      }
      // We need to do comments too! Awww :[ Only if comments are allowed :D!
      $comments = array();
      if($news['allow_comments']) {
        $result = sql_query("
          SELECT
            c.post_id, c.nid, c.poster_id, c.poster_name, c.subject, c.body,
            c.post_time, c.isApproved, c.isSpam, mem.id,
            mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username
          FROM {$db_prefix}news_comments AS c
            LEFT JOIN {$db_prefix}members AS mem ON mem.id = c.poster_id
          WHERE 
            c.nid = $news_id AND isApproved = 1 AND isSpam = 0
          ORDER BY c.post_time DESC");
        // Load up the comments into an array
        while ($row = mysql_fetch_assoc($result)) {
          $comments[] = array(
            'id' => $row['post_id'],
            'news_id' => $row['nid'],
            'poster_id' => $row['poster_id'],
            'poster_name' => $row['username'],
            'subject' => $row['subject'] ? $row['subject'] : NULL,
            'body' => bbc($row['body']),
            'user_id' => $row['id'],
            'username' => $row['username'],
            'post_date' => formattime($row['post_time'],2),
            'isApproved' => $row['isApproved'],
            'isSpam' => $row['isSpam']
          );
        }
        // Free! IT'S FREE!
        mysql_free_result($result);
      }
      // Load it up :D (The theme thingy)
      $settings['page']['title'] = $news['subject'];
      $settings['news'] = $news;
      $settings['comments'] = $comments;
      unset($news);
      if ($settings['news']['allow_comments'])
        loadTheme('News','SingleComments');
      else
        loadTheme('News','Single');
    }
    else {
      // It doesn't exist? :O
      $settings['page']['title'] = $l['news_nonews_title'];
      loadTheme('News','NoNews');
    }  
  }
}

// This is for the Admin CP, to manage the news
function ManageNews() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('manage_news')) {
    // Dang, they can do this, now I have to code it :(
    // Some actions they can do...
    $ssa = array('add','categories');
    if(empty($_REQUEST['id']) && (!in_array(@$_REQUEST['ssa'], $ssa))) {
      // No news ID, and no $na action that exists
      $result = sql_query("
        SELECT
          n.news_id, n.poster_id, n.poster_name, n.cat_id, n.subject, n.post_time, n.numViews, n.allow_comments,
          nc.cat_id, nc.cat_name, m.id, m.display_name AS username, IFNULL(m.display_name, m.username) AS username
        FROM {$db_prefix}news AS n
          LEFT JOIN {$db_prefix}news_categories AS nc ON nc.cat_id = n.cat_id
          LEFT JOIN {$db_prefix}members AS m ON m.id = n.poster_id
        ORDER BY n.news_id DESC");
      $comments = mysql_fetch_array(sql_query("SELECT COUNT(*) FROM {$db_prefix}news_comments GROUP BY nid"));
      $comments = $comments['COUNT(*)'];
      $settings['news'] = array();
      while($row = mysql_fetch_assoc($result)) {
        $settings['news'][] = array(
          'id' => $row['id'],
          'poster_id' => $row['poster_id'],
          'username' => $row['username'],
          'cat_id' => $row['cat_id'],
          'cat_name' => $row['cat_name'],
          'subject' => $row['subject'],
          'time' => formattime($row['post_time']),
          'numViews' => $row['numViews'],
          'allow_comments' => $row['allow_comments']
        );
      }
      mysql_free_result($result);
      $settings['page']['title'] = $l['news_manage_title'];
      loadTheme('News','Manage');
    }
    elseif (empty($_REQUEST['id']) && $_REQUEST['ssa']=='add') {
      // Adding news =D
      if (@$_REQUEST['add-news']) {
        // Clean the data of dirty injections
        $cat_id = clean(@$_REQUEST['cat_id']); // Category ID
        $subject = clean(@$_REQUEST['subject']); // Subject
        $body = clean(@$_REQUEST['body']); // Body text
        $allow_comments = @$_REQUEST['allow_comments'] == true; // Are comments for this post allowed
        // Process SQL query
        sql_query("INSERT {$db_prefix}news (`poster_id`, `cat_id`, `poster_name`, `subject`, `body`, `post_time`, `allow_comments`) VALUES ('{$user['id']}','$cat_id','{$user['name']}','$subject','$body', '".time()."', '$allow_comments')");
        
        // Redirect the page to the main manage news page
        redirect('index.php?action=admin;sa=news');
      }
      
      // Get categories
      $result = sql_query("SELECT * FROM {$db_prefix}news_categories");
      $i = 0;
      while ($row = mysql_fetch_assoc($result)) {
        $categories[$i]['id'] = $row['cat_id'];
        $categories[$i]['name'] = $row['cat_name'];
        $i += 1;
      }
      
      $settings['page']['categories'] = @$categories;
      
      $settings['page']['title'] = $l['news_add_title'];
      loadTheme('News','Add');
    }
    elseif (empty($_REQUEST['id']) && $_REQUEST['ssa']=='categories') {
      // Managing categories
      ManageCats();
    }
    else {
      // Editing news... =D
    }
  }
  else {
    // Go away! You cant touch this, nah nah nah nah nah nah, cant touch this =D
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}

// Manage news categories
function ManageCats() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // Rename categories
  if(!empty($_REQUEST['update_cats'])) {  
    $rows = array();
    foreach($_POST['cat_name'] as $cat_id => $name) {
      $cat_id = (int)$cat_id;
      $name = clean($name);
      $rows[] = "('$cat_id','$name')";
    }
    $updated = implode(",", $rows);
    sql_query("REPLACE INTO {$db_prefix}news_categories (`cat_id`,`cat_name`) VALUES{$updated}");
    // Redirect to stop the action of reoccuring if the page is refreshed
    redirect('index.php?action=admin;sa=news;ssa=categories');
  }
  // Delete a category
  if(!empty($_REQUEST['delete']) && validateSession($_REQUEST['sc'])) {
    $cat_id = (int)$_REQUEST['delete'];
    sql_query("DELETE FROM {$db_prefix}news_categories WHERE `cat_id` = '$cat_id'");
    // Redirect to stop the action of reoccuring if the page is refreshed
    redirect('index.php?action=admin;sa=news;ssa=categories');
  }
  // Add a category
  if(!empty($_REQUEST['add_cat'])) {
    $cat_name = clean($_REQUEST['cat_name']);
    sql_query("INSERT INTO {$db_prefix}news_categories (`cat_name`) VALUES('$cat_name')");
    // Redirect to stop the action of reoccuring if the page is refreshed
    redirect('index.php?action=admin;sa=news;ssa=categories');
  }
  // Show a list of categories...
  $result = sql_query("SELECT * FROM {$db_prefix}news_categories");
  $cats = array();
  while($row = mysql_fetch_assoc($result)) {
    $cats[] = array(
      'id' => $row['cat_id'],
      'name' => $row['cat_name']
    );
  }
  $settings['cats'] = $cats;
  $settings['page']['title'] = $l['news_cats_title'];
  loadTheme('News','ShowCats');
}
?>