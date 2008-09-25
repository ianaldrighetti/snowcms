<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//          redistribute it as your wish!
//
//                   Page.php file


if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Displays the page
function Page($main_page = false) {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // If Main Page is true, we need to show the Page ID Set to be shown at the Home, or else get ?page=
  if($main_page) 
    $PageID = $settings['homepage'];
  else
    $PageID = clean($_REQUEST['page']);
  // Get it from MySQL
  $result = sql_query("SELECT * FROM {$db_prefix}pages WHERE `page_id` = '{$PageID}'");
  // Does it exist or not?
  if (mysql_num_rows($result)) {
    while($row = mysql_fetch_assoc($result)) {
      $settings['page']['title'] = $row['title'];
	    $settings['page']['date'] = $row['modify_date'] ? formattime($row['modify_date']) : formattime($row['create_date']);
      $settings['page']['content'] = stripslashes($row['content']);
      $settings['page']['html'] = $row['html'];
    }
    // It does! Set content and page title, then load Page.template.php
    loadTheme('Page');
  }
  else {
    // Oh Noes! Page doesn't exist D: Load Error Title and Error Function in Page.template.php
    $settings['page']['title'] = $l['page_error_title'];
    loadTheme('Page','Error');
  }
}

// An Admin Function to Make/Manage Pages
function ManagePages() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // This variable will be set if redirection is required to stop IE showing an alert if refreshed
  if (@$_REQUEST['redirect'])
    redirect('index.php?action=admin;sa=pages');
  
  $page = (int)@$_GET['page'];
  
  // If a page is set then we should be editing a page, not listing them
  if ($page) {
    // Are they allowed to modify pages?
    if (can('manage_pages_modify_html') || can('manage_pages_modify_bbcode')) {
      // Are they modifing the homepage and if so, are they allowed to?
      if ($page != $settings['homepage'] || can('manage_pages_home'))
        EditPage();
      else {
      $_SESSION['error'] = $l['managepages_error_notallowed_homepage'];
      redirect('index.php?action=admin;sa=pages');
      }
    }
    else {
      $_SESSION['error'] = $l['managepages_error_notallowed_modify'];
      redirect('index.php?action=admin;sa=pages');
    }
  }
  elseif (can('manage_pages_modify_html') || can('manage_pages_modify_bbcode') || can('manage_pages_create') || can('manage_pages_delete') || can('manage_pages_home')) {
    $page = (int)@$_POST['page'];
    $settings['page']['make_page']['do'] = false;
    $settings['page']['update_page'] = 0;
    // Do we need to update a page?
    if (!empty($_REQUEST['update_page'])) {
      // Are they allowed to modify pages?
      if (can('manage_pages_modify_html') || can('manage_pages_modify_bbcode')) {
        if (!($page_title = clean($_REQUEST['page_title']))) {
          // If they remove the page title, they'll regret it later
          $_SESSION['error'] = $l['managepages_no_title'];
          redirect('index.php?action=admin;sa=pages;page='.clean_header($page));
        }
        $page_content = addslashes($_REQUEST['page_content']);
  	    if(isset($_REQUEST['page_show_info']))
	      	$page_show_info = $_REQUEST['page_show_info'];
	      else
		      $page_show_info = 0;
		    // Check if it is HTML or BBCode
		    $html = can('manage_pages_modify_html') && !can('manage_pages_modify_bbcode') // Can only modify HTML
		          ? 1
		          :(!can('manage_pages_modify_html') && can('manage_pages_modify_bbcode') // Can only modify BBCode
		          ? 0
		          : (int)@$_REQUEST['html']); // Can modify both, so check what they selected
		    // If it's BBCode, clean it
		    if (!$html)
		      $page_content = clean(stripslashes($page_content));
        // Update it
        $result = sql_query("UPDATE {$db_prefix}pages SET `title` = '$page_title', `content` = '$page_content', `html` = '$html' WHERE `page_id` = '$page'");
      }
      // They are not allowed to modify pages
      else
        $_SESSION['error'] = $l['managepages_error_notallowed_modify'];
      redirect('index.php?action=admin;sa=pages');
    }
    // Do they want to change which page is the homepage?
    if (@$_REQUEST['homepage']) {
      // Are they allowed to change the homepage?
      if (can('manage_pages_home')) {
        // Clean it to prevent dirty hacking
        $homepage = clean($_REQUEST['homepage']);
        // Check if that page exists
        if (mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}pages WHERE `page_id` = '$homepage'")))
          // Change the homepage
          sql_query("UPDATE {$db_prefix}settings SET `value` = '$homepage' WHERE `variable` = 'homepage'");
        else
          $_SESSION['error'] = $l['managepages_error_invalid_homepage'];
      }
      // They can't change the homepage
      else
        $_SESSION['error'] = $l['managepages_error_notallowed_homepage'];
      // Redirect them so that if they refresh it won't do it again
      redirect('index.php?action=admin;sa=pages');
    }
    // Do they want to delete a page?
    if (@$_REQUEST['did']) {
      // Are they allowed to delete pages?
      if (can('manage_pages_delete')) {
        // Is their session valid?
        if (ValidateSession(@$_REQUEST['sc'])) {
          // Are they trying to delete the homepage?
          if ($settings['homepage'] != $_REQUEST['did']) {
            // Clean anything that's used in an SQL query
            $did = clean($_REQUEST['did']);
            // Delete the page
            sql_query("DELETE FROM {$db_prefix}pages WHERE `page_id` = '$did'");
          }
          // They are trying to delete the homepage
          else
            $_SESSION['error'] = $l['managepages_error_delete_homepage'];
        }
        // Their session verification failed
        else
          $_SESSION['error'] = $l['managepages_error_invalid_session'];
      }
      // They are not allowed to delete pages
      else
        $_SESSION['error'] = $l['managepages_error_notallowed_delete'];
      // Get the sort query
      $s = clean_header(@$_REQUEST['s'] ? ';s='.$_REQUEST['s'] : '');
      // Get the page
      $pg = clean_header(@$_REQUEST['pg'] ? ';pg='.$_REQUEST['pg'] : '');
      // Redirect them so that if they refresh it won't do it again
      redirect('index.php?action=admin;sa=pages'.$s.$pg);
    }
    // Or are we supposed to create a page?
    if(!empty($_REQUEST['create_page'])) {
      // Are they allowed to create a page?
      if (can('manage_pages_create')) {
        $settings['page']['make_page']['do'] = true;  
        // Who is the Page Owner? (Their User ID)
        $page_owner = clean($user['id']);
        // We save their name, just incase their account is deleted
        $owner_name = clean($user['name']);
        // The Time stamp of when it was made
        $create_date = time();
        // Clean the page's title
		    if (!($title = clean($_REQUEST['page_title']))) {
		      // They didn't even enter a page title
		      $_SESSION['error'] = $l['managepages_no_title'];
		      redirect('index.php?action=admin;sa=pages');
		    }
		    // Set the page type
		    $html = can('manage_pages_modify_html') && !can('manage_pages_modify_bbcode') // Can only modify HTML
		          ? 1
		          :(!can('manage_pages_modify_html') && can('manage_pages_modify_bbcode') // Can only modify BBCode
		          ? 0
		          : $settings['page_type']); // Can modify both or neither, so just use the site default
        // Insert it
        if(!$result = sql_query("INSERT INTO {$db_prefix}pages (`page_owner`,`owner_name`,`create_date`,`title`,`html`)
                                 VALUES ('$page_owner','$owner_name','$create_date','$title','$html')")) {
          // Oh NOES! It failed!
          $_SESSION['error'] = str_replace('%title%',$title,$l['managepages_make_fail']);
        }
      }
      // They are not allowed to create pages
      else
        $_SESSION['error'] = $l['managepages_error_notallowed_create'];
      redirect('index.php?action=admin;sa=pages');
    }
    
    // What to sort by sort
    $sort = $settings['page']['sort'] = @$_REQUEST['s'];
    
    // Get the sort SQL
    switch ($sort) {
      case 'title':             $sort = 'p.title, p.create_date'; break;
      case 'title_desc':        $sort = 'p.title DESC, p.create_date'; break;
      case 'creator':           $sort = 'p.page_owner, p.create_date'; break;
      case 'creator_desc':      $sort = 'p.page_owner DESC, p.create_date'; break;
      case 'creationdate':      $sort = 'p.create_date'; break;
      case 'creationdate_desc': $sort = 'p.create_date DESC'; break;
      default:                  $sort = 'p.create_date';
    }
    
    // The current page number
    $page = $page_before = @$_REQUEST['pg'];
    // If the page number is lower then zero then make it zero
    if ($page < 0)
      $page = 0;
    // The total amount of pages, real pages, not from the pagination
    $settings['page']['total_pages'] = mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}pages"));
    // If page number is higher then maximum, lower it until it isn't
    while ($settings['num_pages'] * $page >= $settings['page']['total_pages'] && $page > 0)
      $page -= 1;
    // If the page changed, redirect
    if ($page != $page_before) {
      $page = clean_header($page ? ';pg='.$page : '');
      // Get the sort query
      $s = clean_header(@$_REQUEST['s'] ? ';s='.$_REQUEST['s'] : '');
      redirect('index.php?action=admin;sa=pages'.$s.$page);
    }
    
    // Get the first page on this page, confusing, no?
    $start = $page * $settings['num_pages'];
    
    // Get all the pages in the database so we can list them :)
    $result = sql_query("
       SELECT
         p.page_id, p.page_owner, p.owner_name, p.create_date, 
         p.modify_date, p.title, m.id, m.username, m.display_name
       FROM {$db_prefix}pages AS p
         LEFT JOIN {$db_prefix}members AS m ON m.id = p.page_owner
       ORDER BY $sort
       LIMIT $start, {$settings['num_pages']}");
      $pages = array();
      while($row = mysql_fetch_assoc($result)) {
        if(!$row['id']) {
          $page_owner = -1;
          $owner = $row['owner_name'];
        }
        elseif($row['display_name']!=null) {
          $page_owner = $row['page_owner'];
          $owner = $row['display_name'];
        }
        else
          $owner = $row['username'];
        $pages[] = array(
          'page' => $row['page_id'],
          'title' => $row['title'],
          'page_owner' => $page_owner,
          'owner' => $owner,
          'date' => $row['modify_date'] ? formattime($row['modify_date']) : formattime($row['create_date'])
        );          
      }
    
    // The current page number
    $settings['page']['page'] = (int)$page;
    // The last page number
    $settings['page']['page_last'] = ceil($settings['page']['total_pages'] / $settings['num_pages']);
    
    // Load the $pages array into $settings so we can pass it on
    $settings['page']['pages'] = $pages;
    // Lets make it simple, count how many pages their are
    $settings['page']['num_pages'] = count($pages);
    // Set page title, and load ManagePages template
    $settings['page']['title'] = $l['managepages_title'];
    loadTheme('ManagePages');
  }
  // They don't have permission, so redrect them to the main control panel
  else
    redirect('index.php?action=admin');
}

function EditPage() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('manage_pages_modify_html') || can('manage_pages_modify_bbcode')) {  
    // Get the Page ID and clean it!
    $page = (int)addslashes(mysql_real_escape_string(@$_REQUEST['page']));
    if(!empty($page)) {
      // Get it!
      $result = sql_query("SELECT * FROM {$db_prefix}pages WHERE `page_id` = '{$page}'");
      // Does it exist? o.O
      if ($row = mysql_fetch_assoc($result)) {
        $page = array(
          'page' => $row['page_id'],
          'title' => $row['title'],
          'content' => clean(stripslashes($row['content'])),
          'html' => $row['html']
        );
        // Load $page (the pages info) into $settings, clean() the content with clean() so it won't parse any HTML Entities like &copy; as what you would see (c)
        // Set the title, and load the ManagePages template with the Editor function
        $settings['page']['edit_page'] = $page;
        $settings['page']['title'] = str_replace("%title%", $page['title'], $l['managepages_edit_title']);
        $settings['page']['all-pages'] = sql_query("SELECT * FROM {$db_prefix}pages");
        loadTheme('ManagePages','Editor');
      }
      else {
        // The Page doesn't exist! Load up the Error :P
        $settings['page']['title'] = $l['managepages_no_page_title'];
        loadTheme('ManagePages','NoPage');
      }
    }
    else {
      // $page is empty, load the error...
      $settings['page']['title'] = $l['managepages_no_page_title'];
      loadTheme('ManagePages','NoPage');
    }
  }
  // They don't have permission, so redrect them to page management
  else
    redirect('index.php?action=admin;sa=pages');
}
?>