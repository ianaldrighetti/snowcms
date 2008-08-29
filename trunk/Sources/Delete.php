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
//              Admin.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

function Delete() {
global $db_prefix;
  
  $mid = clean($_REQUEST['msg']);
  $topic = clean($_REQUEST['topic']);
  sql_query("DELETE FROM {$db_prefix}messages WHERE `mid` = '$mid'");
  
  redirect('forum.php?topic='.$topic);
}
?>