<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                  Admin.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

/*
  // Function Admin()
  ---------------------------------
  This function is almost like the index.php file, it lets us call un sub admin actions
  Which allows you to adminisrate your site :P 
  So this is kinda like index.php, just a sub one :)
  ---------------------------------
  
  // Function AdminHome()
  ---------------------------------
  The AdminHome() function is the what else, the ACP Home,
  This gets the latest news and version of SnowCMS, or gives
  possible errors for why you couldn't get the news and such
  ---------------------------------  
*/  
  
function Admin() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  
  // Add link to the link tree
  addTree($l['admin_linktree'],'index.php?action=admin');
  
  // This variable will be set if redirection is required to stop IE showing an alert if refreshed
  if (@$_REQUEST['redirect'] == 'admin')
    redirect('index.php?action=admin');

  // Can they administrate the forum? :P
  if(can('admin')) {
    if(!empty($_REQUEST['sa'])) {
      $sa = array(
        'settings' => array('Settings.php','Settings'),
        'forum' => array('ManageForum.php','ManageForum'),
        'pages' => array('Page.php','ManagePages'),
        'members' => array('Members.php','ManageMembers'),
        'permissions' => array('Permissions.php','GroupPermissions'),
        'maintain' => array('Maintain.php','Maintain'),
        'menus' => array('Menus.php','ManageMenus'),
        'news' => array('News.php','ManageNews'),
        'tos' => array('TOS.php','ManageTOS'),
        'ips' => array('IPs.php','ManageIPs'),
        'pms' => array('PersonalMessages.php','ModeratePMs')
      );
      // Is the sa= in the $sa array? If so do it :D
      if(is_array(@$sa[$_REQUEST['sa']])) {
        require_once($source_dir.'/'.$sa[$_REQUEST['sa']][0]);
        $sa[$_REQUEST['sa']][1]();
      }
      else
        // Its not :( We don't want an error, so show the AdminHome();
        AdminHome();
    }
    else
      // If sa is just plain not set, load the Admin Home
      AdminHome();
  }
  else {
    // Go away! We not want you here! >:(
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}

function AdminHome() {
global $cmsurl, $db_prefix, $l, $settings, $source_dir, $user;
  
  // Get the control panel menu options
  // By adding it to the array if they are
  // allowed to do so...
  $options = array();
  if (can('manage_pages'))
    $options[] = 'pages';
  if (can('settings'))
    $options[] = 'settings';
  if (can('manage_members'))
    $options[] = 'members';
  if (can('manage_permissions'))
    $options[] = 'permissions';
  if (can('manage_menus'))
    $options[] = 'menus';
  if (can('manage_forum'))
    $options[] = 'forum';
  if (can('manage_news'))
    $options[] = 'news';
  if (can('manage_tos'))
    $options[] = 'tos';
  if (can('ban_ips') || can('unban_ips'))
    $options[] = 'ips';
  if (can('manage_pms'))
    $options[] = 'pms';
  if (can('maintain'))
    $options[] = 'maintain';
  
  // Save them so we can use them ;D
  $settings['page']['options'] = $options;
  
  // With cURL (If the curl_init function exists) get the latest version of SnowCMS and Latest News
  // Soon this will be turned into a function, and I hope to have a fsockopen option too :)
  $settings['page']['news'] = null;
  if(function_exists('curl_init')) {
    $curl = curl_init('http://news.snowcms.com/latest.txt');
      curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_VERBOSE, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
	  $settings['latest_version'] = 'v' . curl_exec($curl);
	  // Close the cURL Connection, we don't want it open ;)
	  curl_close($curl); 
	  // Now the news for SnowCMS ;)!   
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
	    $settings['page']['news'] = $l['admin_news_timeout'];
	  // Set the Title, and then call on the Admin.template.php template
    $settings['page']['title'] = $l['admin_title'];
    loadTheme('Admin');
	}
	else {
	  // We didn't try because you don't have cURL as far as we can tell :(
	  // Oh well... we will use a frame... o.O
	  $settings['latest_version'] = $l['admin_version_unavailable'];
	  $settings['page']['title'] = $l['admin_title'];
	  $settings['page']['news_url'] = 'http://news.snowcms.com/v0.x-line/news.txt';
    loadTheme('Admin','NocURL');
	}
}
?>