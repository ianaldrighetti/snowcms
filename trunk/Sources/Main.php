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
//              Main.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

function Home() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  // So uh, What should we do? 1 = Show a Page, 2 = Show News
  if($settings['main_page']==1) {
    // Include the Page.php Source File
    require_once($source_dir.'/Page.php');
    // Call on Page(true); the true means load the Page that is set to be the Homes Default Page
    Page(true);
  }
  else {
    // Get the News.php Source File, and show the News
    require_once($source_dir.'/News.php');
    News();
  }
}
?>