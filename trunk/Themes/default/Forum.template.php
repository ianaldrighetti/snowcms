<?php
// Forum.template.php by SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function forum_header() {
global $cmsurl, $theme_url, $settings, $user;
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>'.$settings['page']['title'].'</title>
  <link rel="stylesheet" href="'.$theme_url.'/'.$settings['theme'].'/forum.css" type="text/css" media="screen" />
</head>
<body>
  <div id="header">
  </div>
  <div id="menu">
    <ul>
      <li><a href="">Home</a></li>
      <li><a href="">Forum Index</a></li>
      <li><a href="">Search</a></li>
      <li><a href="">Admin</a></li>
      <li><a href="">Profile</a></li>
      <li><a href="">Members</a></li>
      <li><a href="">Personal Messages</a></li>
    </ul>
  </div>';
}

function forum_footer() {
global $cmsurl, $theme_url, $settings, $user;
echo '
  <div id="foot">
    <p>Powered by <a href="http://www.snowcms.com/">SnowCMS</a> | Copyright &copy; 2008 Your Site</p>
  </div>
</body>
</html>';
}
?>