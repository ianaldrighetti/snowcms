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
//            Members.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function ManageMembers() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('manage_members')) {
    // So they can, yippe for you! :P
    // Are they just viewing the list, or managing a member, or something else perhaps?
    if((empty($_REQUEST['u'])) && (empty($_REQUEST['ssa']))) {
      // K, just load the list of members
      loadMlist();
    }
    elseif((!empty($_REQUEST['u'])) && (empty($_REQUEST['ssa']))) {
      // :o They are moderating/viewing someones profile
      loadProf();
    }
    else {
      // A Super Sub Action :D!
      if($_REQUEST['ssa']=='ua') {
        // Okay, list all unactivated accounts...
        loadUA();
      }
    }
  }
  else {
    // You can't Manage Members silly!
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}