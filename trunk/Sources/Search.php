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
//                Post.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function fSearch() {
global $l, $settings;
  
  $settings['page']['title'] = 'Search';
  LoadForum('Search');
}
?>