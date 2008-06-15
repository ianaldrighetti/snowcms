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
?>