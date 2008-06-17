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
//              Online.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function Online() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  if(can('view_online')) {
    $online = array();
    $result = mysql_query("SELECT * FROM {$db_prefix}online ORDER BY `last_active` DESC");
      while($row = mysql_fetch_assoc($result)) {
        $type = explode(":", $row['page']);
        if($type[0]=='action') {
          if(isset($l[$type[1].'_title']))
            $page_title = $l[$type[1].'_title'];
          else
            $page_title = $l['online_title_unknown'];
        }
        elseif($type[0]=='page') {
          if(isset($settings['page_titles'][$type[1]]))
            $page_title = $settings['page_title'][$type[1]];
          else
            $page_title = $l['online_title_unknown'];
        }
        else
          $page_title = $l['online_title_unknown'];
        $online[] = array(
          'user_id' => $row['user_id'],
          'is_user' => $row['user_id'] ? true : false,
          'user' => $row['user_id'] ? $settings['users'][$row['user_id']] : $l['online_user_guest'],
          'ip' => can('view_online_special') ? $row['ip'] : false,
          'page' => $page_title,
          'time' => date("g:i:sA", $row['last_active'])
        );
      }
    $settings['page']['online'] = $online;
    $settings['page']['title'] = $l['online_title'];
    loadTheme("Online");
  }
  else {
  
  }
}