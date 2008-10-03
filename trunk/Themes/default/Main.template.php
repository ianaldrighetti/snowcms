<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           Main.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

// If you want to change the layout of the site, you can edit this file, and it will change the layout of your site
// However, if you want to change your forums layout, look at Forum.template.php 
function theme_header() {
global $l, $cmsurl, $theme_url, $settings, $user;
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>'.@$settings['page']['title'].' - '.$settings['site_name'].'</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="'.$theme_url.'/'.$settings['theme'].'/style.css" type="text/css" media="screen" />
  <!--[if lte IE 6]><link rel="stylesheet" href="'.$theme_url.'/'.$settings['theme'].'/iefix.css" type="text/css" media="screen" /><![endif]-->
  <!--[if lte IE 7]><style type="text/css">#content {padding-left: 6px !important;}</style><![endif]-->
</head>

<body>
<div class="container">
  <div class="header">
    <a class="headerlink" href="'.$cmsurl.'" title="'.$settings['site_name'].'"><img class="headerimg" src="'.$theme_url.'/'.$settings['theme'].'/images/title.png" alt="'.$settings['site_name'].'" /></a>
  </div>
  <div class="sidebar">
  <ul>';
  // Show the Side Menu
  theme_menu('side');
  echo '
  </ul>
  ';
  languageOption();
  echo '</div>
  <div class="content">
  ';
}

// Call on by either theme_menu('main'); or theme_menu('side')
function theme_menu($which) {
global $l, $cmsurl, $settings, $user;
  // Are there even any links? Lol.
  if(count($settings['menu'][$which])>0) {
    foreach($settings['menu'][$which] as $link) {
      echo '<li><a href="'.$link['href'].'" '.$link['target'].'>'.$link['name'].'</a></li>';
    }
  }
}

// The footer of your site!
// Please do NOT remove the powered by link, it helps support SnowCMS
// by getting more users, if you do remove it, we can and will deny
// you of support for your SnowCMS installation. Thanks!
function theme_footer() {
global $l, $cmsurl, $theme_url, $settings, $user;
echo '
    <div style="clear: both"></div>
  </div>
  <div class="footer">
    <p>'.str_replace('%snowcms%','<a href="http://www.snowcms.com/" onClick="window.open(this.href); return false;">SnowCMS '.$settings['version'].'</a>',$l['main_powered_by']).' | '.str_replace('%whom%','<a href="http://snowcms.googlecode.com/" onclick="window.open(this.href); return false;">The SnowCMS Team</a>',$l['main_theme_by']).'</p>
  </div>
</div>
</body>
</html>';
}

function languageOption() {
global $user, $settings, $l, $db_prefix, $language_dir, $cmsurl, $cookie_prefix;
  
  // Check how many languages there are
  $total_languages = 0;
  foreach (scandir($language_dir) as $language)
    if (substr($language,0,1) != '.')
      $total_languages += 1;
  
  // Only show the change language form if there are at least two
  if ($total_languages > 1) {
    // Get the current language
    $current_language = clean($user['language'] ? $user['language'] : (@$_COOKIE[$cookie_prefix.'change-language'] ? @$_COOKIE[$cookie_prefix.'change-language'] : $settings['language']));
    
    echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="text-align: center"><p>
    <select name="change-language">
    ';
    
    foreach (scandir($language_dir) as $language)
      if (substr($language,0,1) != '.') {
        $l_temp = $l;
        include $language_dir.'/'.$language;
        if ($current_language == $language)
          echo '<option value="'.strrev(substr(strrev($language),strlen(strstr($language,'.language.php')),strlen($language))).'" selected="selected">'.$l['language_name'].'</option>
      ';
        else
          echo '<option value="'.strrev(substr(strrev($language),strlen(strstr($language,'.language.php')),strlen($language))).'">'.$l['language_name'].'</option>
      ';
        $l = $l_temp;
      }
    
    echo '</select>
    <input type="submit" value="'.$l['main_language_go'].'" />
    </p></form>
  ';
  }
}
?>
