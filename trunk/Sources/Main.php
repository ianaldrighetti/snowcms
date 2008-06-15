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
    require_once($source_dir.'/Page.php');
    Page(true);
  }
  else {
    require_once($source_dir.'/News.php');
    News();
  }
}
?>