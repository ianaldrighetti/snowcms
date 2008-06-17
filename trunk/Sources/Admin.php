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
  
  
function Admin() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  if(can('admin')) {
    if(!empty($_REQUEST['sa'])) {
      $sa = array(
        'members' => array('Members.php','ManageMembers'),
        'basic-settings' => array('Settings.php','BasicSettings')
      );
      if(is_array(@$sa[$_REQUEST['members']])) {
        require_once($source_dir.'/'.$sa[$_REQUEST['sa']][0]);
          $sa[$_REQUEST['sa']][1]();
      }
      else {
        AdminHome();
      }
    }
    else {
      AdminHome();
    }
  }
  else {
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}

function AdminHome() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  // Now we need to get the latest SnowCMS News :)
  $settings['page']['news'] = null;
  if(function_exists('curl_init')) {
    $curl = curl_init('http://www.snowcms.com/news/news.txt');
      curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_VERBOSE, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
	  $settings['page']['news'] = curl_exec($curl);
	  curl_close($curl);
	  if(empty($settings['page']['news']))
	    $settings['page']['news'] = $l['admin_cant_get_news_2'];
	}
	else {
	  $settings['page']['news'] = $l['admin_cant_get_news_1'];
	}
  $settings['page']['title'] = $l['admin_title'];
  loadTheme('Admin');
}
