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
//              Core.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function ManageGroups() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // This is where you manage member groups (not post groups, though right now we don't have post groups :P)
  if(can('manage_groups')) {
  
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}
?>