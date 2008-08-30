<?php
// Forum.template.php by SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');

// The main part, the forum header which has the main menu, the meta data, and more
function forum_header() {
global $cmsurl, $theme_url, $l, $settings, $user;
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>'.$settings['page']['title'].'</title>
  <link rel="stylesheet" href="'.$theme_url.'/'.$settings['theme'].'/forum.css" type="text/css" media="screen" />
  <script type="text/javascript" src="'. $theme_url. '/default/scripts/bbcode_mini.js"></script>
</head>
<body>
  <div id="header">
  </div>
  <div id="menu">
    <ul>
      <li><a href="'.$cmsurl.'index.php">'.$l['forum_link_home'].'</a></li>
      <li><a href="'.$cmsurl.'forum.php">'.$l['forum_link_forumindex'].'</a></li>
      <li><a href="'.$cmsurl.'forum.php?action=search">'.$l['forum_link_search'].'</a></li>';
    if(can('admin'))
      echo '<li><a href="'.$cmsurl.'index.php?action=admin">'.$l['forum_link_admin'].'</a></li>';
    if($user['is_logged']) 
      echo '<li><a href="'.$cmsurl.'index.php?action=profile">'.$l['forum_link_profile'].'</a></li>';
    if(can('view_mlist')) 
      echo '<li><a href="'.$cmsurl.'index.php?action=members">'.$l['forum_link_members'].'</a></li>';
    if($user['is_logged'])
      echo '<li><a href="">'.$l['forum_link_pm'].'</a></li>';
    if(!$user['is_logged'])
      echo '
      <li><a href="'.$cmsurl.'index.php?action=register">'.$l['forum_link_register'].'</a></li>
      <li><a href="'.$cmsurl.'index.php?action=login">'.$l['forum_link_login'].'</a></li>';
    echo '
    </ul>
  </div>'. link_tree();
}

// This constructs the link tree that is gotten from the linktree array in $settings
function link_tree() {
global $settings;
  $tree = array();
  foreach($settings['linktree'] as $link) {
    $tree[] = '<a href="'. $link['href']. '">'. $link['name']. '</a>';
  }
  return '<p class="link_tree">'. implode(" > ", $tree). '</p>';
}

// The forum footer function, you can change the copyright here, though we will have a setting to do that soon
function forum_footer() {
global $cmsurl, $theme_url, $settings, $user;
echo link_tree(). '
  <div id="foot">
    <p>Powered by <a href="http://www.snowcms.com/">SnowCMS</a> | Copyright &copy; 2008 Your Site</p>
  </div>
</body>
</html>';
}
?>