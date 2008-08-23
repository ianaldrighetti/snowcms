<?php
// default/Main.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function theme_header() {
global $l, $cmsurl, $theme_url, $settings, $user;
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>'.$settings['site_name'].' - '.$settings['page']['title'].'</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.13/soren121" />
	<link rel="stylesheet" href="'.$theme_url.'/'.$settings['theme'].'/style.css" type="text/css" media="screen" />
	<!--[if lte IE 6]><link rel="stylesheet" href="'.$theme_url.'/'.$settings['theme'].'/iefix.css" type="text/css" media="screen" /><![endif]-->
	<!--[if lte IE 7]><style type="text/css">#content {padding-left: 6px !important;}</style><![endif]-->
</head>

<body>
<div id="container">
	<div id="header">
		<div id="headerimg">
			<a class="headerlink" href="'.$cmsurl.'" title="'.$settings['site_name'].'"><img class="headerimg" src="'.$theme_url.'/'.$settings['theme'].'/images/title.png" alt="'.$settings['site_name'].'" /></a>
		</div>
	</div>
	<div id="sidebar">
	<ul>';
	  theme_menu('side');
	echo '
	</ul>
	';
	languageOption();
	echo '</div>
	<div id="content">
	';
}

// Call on by either theme_menu('main'); or theme_menu('side')
function theme_menu($which) {
global $cmsurl, $settings, $user;
  if(count($settings['menu'][$which])>0) {
    foreach($settings['menu'][$which] as $link) {
      echo '<li><a href="'.$link['href'].'" '.$link['target'].'>'.$link['name'].'</a></li>';
    }
  }
  if($which=='side') {
    if(!$user['is_logged'])
      echo '<li><a href="'.$cmsurl.'index.php?action=login">Login</a></li>
            <li><a href="'.$cmsurl.'index.php?action=register">Register</a></li>';
    else
      echo '<li><a href="'.$cmsurl.'index.php?action=profile">Profile</a></li>
            <li><a href="'.$cmsurl.'index.php?action=logout;sc=', $user['sc'], '">Logout</a></li>';      
    if(can('admin'))
      echo '<li><a href="'.$cmsurl.'index.php?action=admin">Admin CP</a></li>';
  }  
}
function theme_footer() {
global $cmsurl, $theme_url, $settings, $user;
echo '
	</div>
	<div id="footer">
	  <p>Powered by <a href="http://www.snowcms.com/" onClick="window.open(this.href); return false;">SnowCMS</a> '.$settings['version'].' | Theme by <a href="http://www.sourceforge.net/projects/snowcms" onclick="window.open(this.href); return false;">the SnowCMS team</a></p>
	</div>
</div>
</body>
</html>';
}

function languageOption() {
global $user, $settings, $l, $db_prefix;
  
  $current_language = clean($user['language'] ? $user['language'] : (@$_COOKIE['language'] ? @$_COOKIE['language'] : $settings['language']));
  $result = sql_query("SELECT * FROM {$db_prefix}languages");
  if (mysql_num_rows($result) > 1) {
    while ($row = mysql_fetch_assoc($result))
      $languages[$row['lang_id']] = $row['lang_name'];
    
    
	  echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="text-align: center"><p>
	  <select name="change-language">
	  ';
	  
    foreach ($languages as $id => $name) {
      if ($current_language == $id)
	      echo ' <option value="'.$id.'" selected="selected">'.$name.'</option>
	  ';
	    else
	      echo ' <option value="'.$id.'">'.$name.'</option>
	  ';
	  }
  	echo ' <option value="5">Spanish</option>
	  ';
  	echo '</select>
	  <input type="submit" value="'.$l['main_language_go'].'" />
	  </p></form>
	';
	}
}
?>
