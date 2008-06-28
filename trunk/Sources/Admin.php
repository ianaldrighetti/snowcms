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
/*
  // Function Admin()
  ---------------------------------
  This function is almost like the index.php file, it lets us call un sub admin actions
  Which allows you to adminisrate your site :P
  ---------------------------------
  
  // Function AdminHome()
  ---------------------------------
  The AdminHome() function is the what else, the ACP Home,
  This gets the latest news and version of SnowCMS
  ---------------------------------  
*/  
  
function Admin() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  if(can('admin')) {
    if(!empty($_REQUEST['sa'])) {
      $sa = array(
        'basic-settings' => array('Settings.php','BasicSettings'),
        'editpage' => array('Page.php','EditPage'),
        'members' => array('Members.php','ManageMembers'),
        'managepages' => array('Page.php','ManagePages'),
        'permissions' => array('Permissions.php','GroupPermissions')        
      );
      // Is the sa= in the $sa array? If so do it :D
      if(is_array(@$sa[$_REQUEST['sa']])) {
        require_once($source_dir.'/'.$sa[$_REQUEST['sa']][0]);
          $sa[$_REQUEST['sa']][1]();
      }
      else {
        // Its not :( We don't want an error, so show the AdminHome();
        AdminHome();
      }
    }
    else {
      // If sa is just plain not set, load the Admin Home
      AdminHome();
    }
  }
  else {
    // Go away! We not want you here! >:(
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}

function AdminHome() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  // With cURL (If the curl_init function exists) get the latest version of SnowCMS and Latest News
  $settings['page']['news'] = null;
  if(function_exists('curl_init')) {
    $curl = curl_init('http://news.snowcms.com/latest.txt');
      curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_VERBOSE, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
	  $settings['latest_version'] = curl_exec($curl);
	  // Close the cURL Connection
	  curl_close($curl);    
    $curl = curl_init('http://news.snowcms.com/v0.x-line/news.txt');
      curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_VERBOSE, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
	  $settings['page']['news'] = curl_exec($curl);
	  // Close it again :]
	  curl_close($curl);
	  if(empty($settings['page']['news']))
	    // Oh noes! We tried, but couldn't get it :'(
	    $settings['page']['news'] = $l['admin_cant_get_news_2'];
	}
	else {
	  // We didn't try because you don't have cURL as far as we can tell :(
	  $settings['page']['news'] = $l['admin_cant_get_news_1'];
	}
	// Set the Title, and then call on the Admin.template.php template
  $settings['page']['title'] = $l['admin_title'];
  loadTheme('Admin');
}
?>