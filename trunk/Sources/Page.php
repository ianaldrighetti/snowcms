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
  // If Main Page is true, we need to show the Page ID Set to be shown at the Home
  if($main_page) 
    $PageID = $settings['main_page_id'];
  else
    $PageID = clean($_REQUEST['page']);
  $result = mysql_query("SELECT * FROM {$db_prefix}pages WHERE `page_id` = '{$PageID}'");
  if(mysql_num_rows($result)>0) {
    while($row = mysql_fetch_assoc($result)) {
      $settings['page']['title'] = $row['title'];
      $settings['page']['content'] = stripslashes(stripslashes($row['content']));
    }
    loadTheme('Page');
  }
  else {
    // Oh Noes! Page doesn't exist D:
    $settings['page']['title'] = $l['page_error_title'];
    loadTheme('Page','Error');
  }
}

// An Admin Function to Make/Manage Pages
function ManagePages() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  $settings['page']['make_page'] = false;
  if(!empty($_REQUEST['make_page'])) {
    $settings['page']['make_page'] = true;  
    $page_owner = clean($user['id']);
    $owner_name = clean($user['name']);
    $create_date = time();
    $title = clean($_REQUEST['page_title']);
    $result = mysql_query("INSERT INTO {$db_prefix}pages (`page_owner`,`owner_name`,`create_date`,`title`) VALUES('{$page_owner}','{$owner_name}','{$create_date}','{$title}')");
    if($result) {
      $settings['page']['make_page']['status'] = true;
      $settings['page']['make_page']['title'] = $title;
      $settings['page']['make_page']['info'] = $l['adminpage_make_success'];
    }
    else {
      $settings['page']['make_page']['status'] = true;
      $settings['page']['make_page']['title'] = $title;
      $settings['page']['make_page']['info'] = $l['adminpage_make_fail'];
    }
  }
  $settings['page']['title'] = $l['adminpage_make_title'];
  loadTheme('ManagePages');
}
?>