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
//                   News.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

function News() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // This prepares the news for display...
  
  // Redirect post data into get data
  if (@$_POST['cat'] == 'all')
    redirect('index.php?action=news');
  elseif (@$_POST['cat'])
    redirect('index.php?action=news;cat='.$_POST['cat']);
  
  // Are they adding a comment?
  if (@$_REQUEST['add-comment']) {
    // Clean the data of dirty injections
    $nid = clean(@$_REQUEST['nid']); // News ID
    $subject = clean(@$_REQUEST['subject']); // Subject
    $body = clean(@$_REQUEST['body']); // Body text
    
    // Update the amount total of comments for the news article
    sql_query("UPDATE {$db_prefix}news SET `num_comments` = `num_comments` + 1 WHERE `news_id` = '$nid'");
    // Insert comment into database
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
      $cat = "WHERE `cat_id` = '$cat'";
    else
      $cat = '';
    // Get the news posts
    $result = sql_query("
      SELECT
        *, mem.display_name AS username, IFNULL(mem.display_name, mem.username) AS username
      FROM {$db_prefix}news AS n
        LEFT JOIN {$db_prefix}members AS mem ON mem.id = n.poster_id
        *, `mem`.`display_name` AS `username`, IFNULL(`mem`.`display_name`, `mem`.`username`) AS `username`
      FROM {$db_prefix}news AS `n`
        LEFT JOIN {$db_prefix}members AS `mem` ON `mem`.`id` = `n`.`poster_id`
        LEFT JOIN {$db_prefix}news_categories AS `cat` ON `cat`.`cat_id` = `n`.`cat_id`

      $cat
      ORDER BY n.post_time DESC
      ORDER BY `n`.`post_time` DESC
      LIMIT $start, {$settings['num_news_items']}");
    $news = array();
    // Is there even any news? :O
    if (mysql_num_rows($result)) {
      while ($row = mysql_fetch_assoc($result)) {
        $news[] = array(
          'id' => $row['news_id'],
          'poster_id' => $row['poster_id'],
          'poster_name' => $row['username'],
          'subject' => $row['subject'],
          'cat_id' => $row['cat_id'],
          'cat_name' => $row['cat_name'],
          'body' => stripslashes($row['body']),
          'user_id' => $row['id'],
          'username' => $row['username'],
          'post_date' => formattime($row['post_time'],2),
          'num_views' => $row['num_views'],
          'num_comments' => (int)$row['num_comments'],
          'allow_comments' => $row['allow_comments']
        );
      }
      // Total amount of news articles
      $news_count = sql_query("SELECT * FROM {$db_prefix}news $cat");
      $total_news = mysql_num_rows($news_count);
      // The current page number
      $settings['page']['page'] = $page;
      // The last page number
      $settings['page']['page_last'] = $total_news / $settings['num_news_items'];
      
      // The current category
      $settings['page']['cat'] = @$_REQUEST['cat'];
      
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
      
      // The previous page number
      $settings['page']['previous_page'] = $page - 1;
      // The current page number
      $settings['page']['current_page'] = $page;
      // The next page number
      $settings['page']['next_page'] = $page + 1;
      // Total amount of news articles
      $news_count = sql_query("SELECT * FROM {$db_prefix}news $cat");
      $settings['page']['total_news'] = mysql_num_rows($news_count);
      
      // The current category
      $settings['page']['cat'] = @$_REQUEST['cat'];
      
      // Load news categories
      $result = sql_query("SELECT * FROM {$db_prefix}news_categories");
      while ($row = mysql_fetch_array($result))
        $settings['page']['categories'][] = $row;
      
      // Load the them
      $settings['page']['title'] = $l['news_nonews_title'];
      loadTheme('News','Nonews');
    }
  }
  else {
    // What news do they want?
    $news_id = (int)$_REQUEST['id'];
    // Are they deleting a comment?
    if ($did = (int)@$_REQUEST['did']) {
      // Are they allowed to delete comments?
      if (can('manage_comments_delete')) {
        // Is there session verification valid?
        if (@$_REQUEST['sc'] == $user['sc']) {
          // Delete the comment
          sql_query("DELETE FROM {$db_prefix}news_comments WHERE `post_id` = '$did'");
        }
        // Their session verification is invalid
        else
          $_SESSION['error'] = $l['news_error_delete_invalidsession'];
      }
      // They are not allowed to delete comments
      else
        $_SESSION['error'] = $l['news_error_delete_notallowed'];
      redirect('index.php?action=news;id='.$news_id);
    }
    // Are they editing a comment?
    if ($edit = (int)@$_REQUEST['edit']) {
      // Are they allowed to edit comments?
      if (can('manage_comments_edit')) {
        // Have they already edited a comment and we're suppose to be just processing it?
        if (!empty($_REQUEST['edit-comment'])) {
          $subject = clean(@$_REQUEST['subject']);
          $body = clean(@$_REQUEST['body']);
          sql_query("UPDATE {$db_prefix}news_comments SET `subject` = '$subject', `body` = '$body' WHERE `post_id` = '$edit'");
          redirect('index.php?action=news;id='.$news_id);
        }
        else {
          // Load the comment data
          $settings['page']['comment'] = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}news_comments WHERE `post_id` = '$edit'"));
          // Load the theme
          $settings['page']['title'] = $l['news_editcomment_title'];
          loadTheme('News','EditComment');
        }
      }
      // They are not allowed to edit comments
      else
        redirect('index.php?action=news;id='.$news_id);
    }
    else {
      $result = sql_query("
        SELECT
          *, `mem`.`display_name` AS `username`, IFNULL(`mem`.`display_name`, `mem`.`username`) AS `username`
        FROM {$db_prefix}news AS `n`
          LEFT JOIN {$db_prefix}members AS `mem` ON `mem`.`id` = `n`.`poster_id`
          LEFT JOIN {$db_prefix}news_categories AS `cat` ON `cat`.`cat_id` = `n`.`cat_id`
        WHERE `n`.`news_id` = '$news_id'");
      $news = array();
      // Is there even any news? :O
      if (mysql_num_rows($result)) {
        while ($row = mysql_fetch_assoc($result)) {
          $news = array(
            'id' => $row['news_id'],
            'poster_id' => $row['poster_id'],
            'poster_name' => $row['username'],
            'subject' => $row['subject'],
            'cat_id' => $row['cat_id'],
            'cat_name' => $row['cat_name'],
            'body' => stripslashes($row['body']),
            'user_id' => $row['id'],
            'username' => $row['username'],
            'post_date' => formattime($row['post_time'],2),
            'num_views' => $row['num_views'],
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
        $settings['page']['title'] = $l['news_doesntexist_title'];
        loadTheme('News','DoesntExist');
      }
    }
  }
}

// This is for the Admin CP, to manage the news
function ManageNews() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // This variable will be set if redirection is required to remove post data
  if (@$_REQUEST['redirect'])
    redirect('index.php?action=admin;sa=pages');
  
  // Are they allowed to manage news?
  if (can('manage_news')) {
    // Dang, they can do this, now I have to code it :(
    // Some actions they can do...
    $ssa = array('add','categories','manage');
    if (!in_array(@$_REQUEST['ssa'], $ssa)) {
      // No news ID, and no $na action that exists
      $result = sql_query("
        SELECT
          n.news_id, n.poster_id, n.poster_name, n.cat_id, n.subject, n.post_time, n.num_views, n.num_comments, n.allow_comments,
          nc.cat_id, nc.cat_name, m.id, m.display_name AS username, IFNULL(m.display_name, m.username) AS username
        FROM {$db_prefix}news AS n
          LEFT JOIN {$db_prefix}news_categories AS nc ON nc.cat_id = n.cat_id
          LEFT JOIN {$db_prefix}members AS m ON m.id = n.poster_id
        ORDER BY n.news_id DESC");
      $comments = mysql_fetch_array(sql_query("SELECT COUNT(*) FROM {$db_prefix}news_comments GROUP BY nid"));
      $comments = $comments['COUNT(*)'];
      $settings['news'] = array();
      while ($row = mysql_fetch_assoc($result)) {
        $settings['news'][] = array(
          'id' => $row['id'],
          'poster_id' => $row['poster_id'],
          'username' => $row['username'],
          'cat_id' => $row['cat_id'],
          'cat_name' => $row['cat_name'],
          'subject' => $row['subject'],
          'time' => formattime($row['post_time']),
          'num_views' => $row['num_views'],
          'allow_comments' => $row['allow_comments']
        );
      }
      // Get the control panel menu options
    $options = array();
    //if (can('manage_permissions'))
      $options[] = 'add';
    //if (can('manage_permissions'))
      $options[] = 'categories';
    //if (can('manage_permissions'))
      $options[] = 'manage';
    $settings['page']['options'] = $options;
      // Load the theme
      $settings['page']['title'] = $l['managenews_title'];
      loadTheme('ManageNews');
    }
    // Adding news =D
    elseif ($_REQUEST['ssa'] == 'add') {
      // Have they already submitted the data and we are suppose to be just processing it?
      if (@$_REQUEST['add-news']) {
        // Clean the data of dirty injections
        $cat_id = clean(@$_REQUEST['cat_id']); // Category ID
        $subject = clean(@$_REQUEST['subject']); // Subject
        $body = clean(@$_REQUEST['body']); // Body text
        $allow_comments = @$_REQUEST['allow_comments'] == true; // Are comments for this post allowed
        // Have they entered a subject?
        if (strlen(str_replace(' ','',$subject)) <= 3) {
          $_SESSION['error'] = $l['managenews_error_subject'];
          $_SESSION['error_values'] = serialize(array('cat_id'=>$cat_id,'subject'=>$subject,'body'=>$body,'allow_comments'=>!$allow_comments));
          redirect('index.php?action=admin;sa=news;ssa=add');
        }
        // Have they entered body text?
        elseif (strlen(str_replace(' ','',$body)) <= 3) {
          $_SESSION['error'] = $l['managenews_error_body'];
          redirect('index.php?action=admin;sa=news;ssa=add');
        }
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
      // Load the theme
      $settings['page']['title'] = $l['managenews_add_title'];
      loadTheme('ManageNews','AddNews');
    }
    elseif ($_REQUEST['ssa'] == 'categories') {
      // Managing categories
      ManageCats();
    }
    elseif ($_REQUEST['ssa'] == 'manage') {
      // Editing news... =D
      NewsList();
    }
  }
  // Go away! You can't touch this, nah nah nah nah nah nah, can't touch this =D
  else
    redirect('index.php?action=admin');
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
  $settings['page']['title'] = $l['managenews_cats_title'];
  loadTheme('ManageNews','ShowCats');
}

function NewsList() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // This prepares the news for display...
  
  // Redirect post data into get data
  if (@$_POST['cat'] == 'all')
    redirect('index.php?action=admin;sa=news;ssa=manage');
  elseif (@$_POST['cat'])
    redirect('index.php?action=admin;sa=news;ssa=manage;cat='.$_POST['cat']);
  
  // Are they deleting news?
  if ($did = (int)@$_REQUEST['did']) {
    // Are they allowed to delete news?
    if (can('manage_news_delete')) {
      // Is there session verification valid?
      if (@$_REQUEST['sc'] == $user['sc']) {
        // Delete the news
        sql_query("DELETE FROM {$db_prefix}news WHERE `news_id` = '$did'");
      }
      // Their session verification is invalid
      else
        $_SESSION['error'] = $l['managenews_error_manage_delete_invalidsession'];
    }
    // They are not allowed to delete news
    else
      $_SESSION['error'] = $l['managenews_error_manage_delete_notallowed'];
    redirect('index.php?action=admin;sa=news;ssa=manage');
  }
  // Are they editing news?
  if ($nid = (int)@$_REQUEST['id']) {
    // Have they already edited it and we are just suppose to be processing it?
    if (@$_REQUEST['edit-news']) {
      // Clean the data of dirty injections
      $cat_id = clean(@$_REQUEST['cat_id']); // Category ID
      $subject = clean(@$_REQUEST['subject']); // Subject
      $body = clean(@$_REQUEST['body']); // Body text
      $allow_comments = @$_REQUEST['allow_comments'] == true; // Are comments for this post allowed
      // Process SQL query
      if ($allow_comments)
        sql_query("UPDATE {$db_prefix}news SET `poster_id` = '{$user['id']}', `cat_id` = '$cat_id', `poster_name` = '{$user['name']}', `subject` = '$subject', `body` = '$body', `modify_time` = '".time()."', `allow_comments` = '$allow_comments' WHERE `news_id` = '$nid'");
      else {
        // Delete all comments as well
        sql_query("UPDATE {$db_prefix}news SET `poster_id` = '{$user['id']}', `cat_id` = '$cat_id', `poster_name` = '{$user['name']}', `subject` = '$subject', `body` = '$body', `modify_time` = '".time()."', `allow_comments` = '$allow_comments', `num_comments` = '0' WHERE `news_id` = '$nid'");
        sql_query("DELETE FROM {$db_prefix}news_comments WHERE `nid` = '$nid'");
      }
      // Redirect the page to the main manage news page
      redirect('index.php?action=admin;sa=news;ssa=manage');
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
    
    // Get the news post data
    $settings['page']['news'] = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}news WHERE `news_id` = '$nid'"));
    
    // Load the theme
    $settings['page']['title'] = $l['managenews_edit_title'];
    loadTheme('ManageNews','EditNews');
  }
  else {
    // The current page number
    $page = @$_REQUEST['pg'];
    
    // The first news article number of this page
    $start = $page * $settings['num_news_items'];
    
    // Setup category SQL
    if ($cat = (int)@$_REQUEST['cat'])
      $cat = "WHERE `cat_id` = '$cat'";
    else
      $cat = "";
    // Get the news posts
    $result = sql_query("
      SELECT
        *, `mem`.`display_name` AS `username`, IFNULL(`mem`.`display_name`, `mem`.`username`) AS `username`
      FROM {$db_prefix}news AS `n`
        LEFT JOIN {$db_prefix}members AS `mem` ON `mem`.`id` = `n`.`poster_id`
        LEFT JOIN {$db_prefix}news_categories AS `cat` ON `cat`.`cat_id` = `n`.`cat_id`
      $cat
      ORDER BY `n`.`post_time` DESC
      LIMIT $start, {$settings['num_news_items']}");
    $news = array();
    // Is there even any news? :O
    if (mysql_num_rows($result)) {
      while ($row = mysql_fetch_assoc($result)) {
        $news[] = array(
          'id' => $row['news_id'],
          'poster_id' => $row['poster_id'],
          'poster_name' => $row['username'],
          'subject' => $row['subject'],
          'cat_id' => $row['cat_id'],
          'cat_name' => $row['cat_name'],
          'body' => stripslashes($row['body']),
          'user_id' => $row['id'],
          'username' => $row['username'],
          'post_date' => formattime($row['post_time'],2),
          'num_views' => $row['num_views'],
          'num_comments' => (int)$row['num_comments'],
          'allow_comments' => $row['allow_comments']
        );
      }
      // Total amount of news articles
      $news_count = sql_query("SELECT * FROM {$db_prefix}news $cat");
      $total_news = 0;
      while (mysql_fetch_assoc($news_count)) {
        $total_news += 1;
      }
      // The current page number
      $settings['page']['page'] = $page;
      // The last page number
      $settings['page']['page_last'] = $total_news / $settings['num_news_items'];
      
      // The current category
      $settings['page']['cat'] = @$_REQUEST['cat'];
      
      // Load news categories
      $result = sql_query("SELECT * FROM {$db_prefix}news_categories");
      while ($row = mysql_fetch_array($result))
        $settings['page']['categories'][] = $row;
      
      // Load it up :D (the theme thingy)
      $settings['page']['title'] = $l['managenews_manage_title'];
      $settings['news'] = $news;
      unset($news);
      loadTheme('ManageNews','ShowNews');
    }
    else {
      // No news? :O
      
      // The previous page number
      $settings['page']['previous_page'] = $page - 1;
      // The current page number
      $settings['page']['current_page'] = $page;
      // The next page number
      $settings['page']['next_page'] = $page + 1;
      // Total amount of news articles
      $news_count = sql_query("SELECT * FROM {$db_prefix}news $cat");
      $settings['page']['total_news'] = 0;
      while (mysql_fetch_assoc($news_count)) {
        $settings['page']['total_news'] += 1;
      }
      
      // The current category
      $settings['page']['cat'] = @$_REQUEST['cat'];
      
      // Load news categories
      $result = sql_query("SELECT * FROM {$db_prefix}news_categories");
      while ($row = mysql_fetch_array($result))
        $settings['page']['categories'][] = $row;
      
      // Load the them
      $settings['page']['title'] = $l['news_nonews_title'];
      loadTheme('ManageNews','Nonews');
    }
  }
}
?>