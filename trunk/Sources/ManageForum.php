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
//           ManageForum.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Displays the page
function ManageForum() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  // :O We need another sub action kind of thing don't we? .-.
  // Lets call it Forum Action, or fa for short :-]
    $fa = array(
      'boards' => array('ManageForum.php','ManageBoards'),
      'categories' => array('ManageForum.php','ManageCats'),
    );
    if(is_array(@$fa[$_REQUEST['fa']])) {
      require_once($source_dir.'/'.$fa[$_REQUEST['fa']][0]);
        $fa[$_REQUEST['fa']][1]();
    }
    else {
      // Its not :( We don't want an error, so show the ForumHome();
      ForumHome();
    }    
}
function ForumHome() {
global $cmsurl, $db_prefix, $l, $settings, $user;
}
?>