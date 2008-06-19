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
  $result = mysql_query("SELECT * FROM {$db_prefix}pages WHERE `page_id` = '{$PageID}'");
  // Does it exist or not?
  if(mysql_num_rows($result)>0) {
    while($row = mysql_fetch_assoc($result)) {
      $settings['page']['title'] = $row['title'];
      $settings['page']['content'] = stripslashes(stripslashes($row['content']));
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
  $settings['page']['make_page']['do'] = false;
  $settings['page']['update_page'] = 0;
  // Do we need to update a page?
  if(!empty($_REQUEST['update_page'])) {
    // If so, get the info, and clean it :P
    $page_id = clean($_REQUEST['page_id']);
    $page_title = clean($_REQUEST['page_title']);
    $page_content = addslashes(mysql_real_escape_string($_REQUEST['page_content']));
    // Update it
    $result = mysql_query("UPDATE {$db_prefix}pages SET `title` = '{$page_title}', `content` = '{$page_content}' WHERE `page_id` = '{$page_id}'");
    if($result) {
      // It was successful!
      $settings['page']['update_page'] = 1;
    }
    else {
      // Wasn't Successful! Oh Noes!
      $settings['page']['update_page'] = 2;
    }
  }
  // Or are we supposed to create a page?
  if(!empty($_REQUEST['make_page'])) {
    $settings['page']['make_page']['do'] = true;  
    // Who is the Page Owner? (Ther User ID)
    $page_owner = clean($user['id']);
    // We save their name, just incase if their account is deleted
    $owner_name = clean($user['name']);
    // The Time stamp of when it was made
    $create_date = time();
    // Clean the page's title
    $title = clean($_REQUEST['page_title']);
    // Insert it
    $result = mysql_query("INSERT INTO {$db_prefix}pages (`page_owner`,`owner_name`,`create_date`,`title`) VALUES('{$page_owner}','{$owner_name}','{$create_date}','{$title}')");
    if($result) {
      // It was a Success! Wooot!
      $settings['page']['make_page']['status'] = true;
      $settings['page']['make_page']['title'] = $title;
      $settings['page']['make_page']['info'] = str_replace("%title%", $_REQUEST['page_title'], $l['adminpage_make_success']);
    }
    else {
      // Oh NOES! It failed!
      $settings['page']['make_page']['status'] = false;
      $settings['page']['make_page']['title'] = $title;
      $settings['page']['make_page']['info'] = str_replace("%title%", $_REQUEST['page_title'], $l['adminpage_make_fail']);
    }
  }
  // Get all the pages in the database so we can list them :)
  $result = mysql_query("SELECT * FROM {$db_prefix}pages ORDER BY `page_id` DESC");
    $pages = array();
    while($row = mysql_fetch_assoc($result)) {
      $pages[] = array(
        'page_id' => $row['page_id'],
        'page_owner' => $row['page_owner'],
        'owner' => @$settings['users'][$row['page_owner']] ? $settings['users'][$row['page_owner']] : $row['owner_name'],
        'date' => $row['modify_date'] ? formattime($row['modify_date']) : formattime($row['create_date']),
        'title' => $row['title']
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

function EditPage() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // Get the Page ID and clean it!
  $page_id = clean($_REQUEST['page_id']);
  if(!empty($page_id)) {
    // Get it!
    $result = mysql_query("SELECT * FROM {$db_prefix}pages WHERE `page_id` = '{$page_id}'");
    // Does it exist? o.O
    if(mysql_num_rows($result)>0) {
      while($row = mysql_fetch_assoc($result)) {
        $page = array(
          'page_id' => $row['page_id'],
          'title' => $row['title'],
          'content' => stripslashes(stripslashes($row['content']))
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
?>