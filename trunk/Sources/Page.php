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
//                Page.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Displays the page
function Page($main_page = false) {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // If Main Page is true, we need to show the Page ID Set to be shown at the Home, or else get ?page=
  if($main_page) 
    $PageID = $settings['main_page_id'];
  else
    $PageID = clean($_REQUEST['page']);
  // Get it from MySQL
  $result = sql_query("SELECT * FROM {$db_prefix}pages WHERE `page_id` = '{$PageID}'");
  // Does it exist or not?
  if(mysql_num_rows($result)>0) {
    while($row = mysql_fetch_assoc($result)) {
      $settings['page']['title'] = $row['title'];
	    $settings['page']['date'] = $row['modify_date'] ? formattime($row['modify_date']) : formattime($row['create_date']);
      $settings['page']['content'] = stripslashes($row['content']);
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
  if(can('manage_pages')) { 
    $settings['page']['make_page']['do'] = false;
    $settings['page']['update_page'] = 0;
    // Do we need to update a page?
    if(!empty($_REQUEST['update_page'])) {
      // If so, get the info, and clean it :P
      $page_id = clean($_REQUEST['page_id']);
      $page_title = clean($_REQUEST['page_title']);
      $page_content = addslashes($_REQUEST['page_content']);
	  if(isset($_REQUEST['page_show_info']))
	  	$page_show_info = $_REQUEST['page_show_info'];
	  else
		  $page_show_info = 0;
      // Update it
      $result = sql_query("UPDATE {$db_prefix}pages SET `title` = '{$page_title}', `content` = '{$page_content}' WHERE `page_id` = '{$page_id}'");
      if($result) {
        // It was successful!
        $settings['page']['update_page'] = 1;
      }
      else {
        // Wasn't Successful! Oh Noes!
        $settings['page']['update_page'] = 2;
      }
    }
    if (@$_REQUEST['did']) {
      $did = clean($_REQUEST['did']);
      sql_query("DELETE FROM {$db_prefix}pages WHERE `page_id` = '$did'") or die(mysql_error());
    }
    // Or are we supposed to create a page?
    if(!empty($_REQUEST['make_page'])) {
      $settings['page']['make_page']['do'] = true;  
      // Who is the Page Owner? (Their User ID)
      $page_owner = clean($user['id']);
      // We save their name, just incase their account is deleted
      $owner_name = clean($user['name']);
      // The Time stamp of when it was made
      $create_date = time();
      // Clean the page's title
      $title = clean($_REQUEST['page_title']);
      // Insert it
      $result = sql_query("INSERT INTO1 {$db_prefix}pages (`page_owner`,`owner_name`,`create_date`,`title`) VALUES('{$page_owner}','{$owner_name}','{$create_date}','{$title}')");
      if(!$result) {
        // Oh NOES! It failed!
        $_SESSION['error'] = str_replace('%title%',$title,$l['adminpage_make_fail']);
      }
      redirect('index.php?action=admin;sa=managepages');
    }
    // Get all the pages in the database so we can list them :)
    $result = sql_query("
       SELECT
         p.page_id, p.page_owner, p.owner_name, p.create_date, 
         p.modify_date, p.title, m.id, m.username, m.display_name
       FROM {$db_prefix}pages AS p
         LEFT JOIN {$db_prefix}members AS m ON m.id = p.page_owner
       ORDER BY p.page_id DESC");
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
          'page_id' => $row['page_id'],
          'title' => $row['title'],
          'page_owner' => $page_owner,
          'owner' => $owner,
          'date' => $row['modify_date'] ? formattime($row['modify_date']) : formattime($row['create_date'])
        );          
      }
    // Load the $pages array into $settings so we can pass it on
    $settings['page']['pages'] = $pages;
    // Lets make it simple, count how many pages their are
    $settings['page']['num_pages'] = count($pages);
    // Set page title, and load ManagePages template
    $settings['page']['title'] = $l['adminpage_make_title'];
    loadTheme('ManagePages');
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}

function EditPage() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('manage_pages')) {  
    // Get the Page ID and clean it!
    $page_id = (int)addslashes(mysql_real_escape_string($_REQUEST['page_id']));
    if(!empty($page_id)) {
      // Get it!
      $result = sql_query("SELECT * FROM {$db_prefix}pages WHERE `page_id` = '{$page_id}'");
      // Does it exist? o.O
      if(mysql_num_rows($result)>0) {
        while($row = mysql_fetch_assoc($result)) {
          $page = array(
            'page_id' => $row['page_id'],
            'title' => $row['title'],
            'content' => stripslashes($row['content'])
          );
          // Load $page (the pages info) into $settings, clean() the content with clean() so it won't parse any HTML Entities like &copy; as what you would see (c)
          // Set the title, and load the ManagePages template with the Editor function
          $settings['page']['edit_page'] = $page;
          $settings['page']['edit_page']['content'] = clean($settings['page']['edit_page']['content']);
          $settings['page']['title'] = str_replace("%title%", $page['title'], $l['managepages_edit_title']);
          loadTheme('ManagePages','Editor');
        }
      }
      else {
        // The Page doesn't exist! Load up the Error :P
        $settings['page']['title'] = $l['managepages_no_page_title'];
        loadTheme('ManagePages','NoPage');
      }
    }
    else {
      // $page_id is empty, load the error...
      $settings['page']['title'] = $l['managepages_no_page_title'];
      loadTheme('ManagePages','NoPage');
    }
  }
  else {
    // They cant manage pages, why should they be able to edit them? xD
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}
?>