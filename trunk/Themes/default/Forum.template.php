<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           Forum.template.php

if(!defined('Snow')) 
  die('Hacking Attempt...');

// The main part, the forum header which has the main menu, the meta data, and more
function forum_header() {
global $cmsurl, $theme_url, $l, $settings, $user;
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>'.@$settings['page']['title'].' - '.$settings['site_name'].'</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="'.$theme_url.'/'.$settings['theme'].'/style.css" type="text/css" media="screen" />
  <!--[if lte IE 6]><link rel="stylesheet" href="'.$theme_url.'/'.$settings['theme'].'/iefix.css" type="text/css" media="screen" /><![endif]-->
  <!--[if lte IE 7]><style type="text/css">#content {padding-left: 6px !important;}</style><![endif]-->
  <script type="text/javascript" src="'.$theme_url.'/'.$settings['theme'].'/scripts/vX.js"></script>
  <script type="text/javascript" src="'.$theme_url.'/'.$settings['theme'].'/scripts/forum_mini.js"></script>
</head>

<body>
<div class="container">
  <div class="sidebar">
  <a href="'.$cmsurl.'" title="'.$settings['site_name'].'">
    <img class="site_logo" src="'.$theme_url.'/'.$settings['theme'].'/images/site_logo.png" alt="'.$settings['site_name'].'" />
  </a>
  <ul>';
      // Show the Side Menu
      theme_menu('side');
    echo '
    </ul>
  ';
  //languageOption();
  echo '</div>
  <div class="header-right"></div>
  <div class="content">
  '.link_tree();
}

// Call on by either theme_menu('main') or theme_menu('side')
function theme_menu($which) {
global $l, $cmsurl, $settings, $user;
  
  // Are there even any links? Lol.
  if (count($settings['menu'][$which])>0) {
    foreach ($settings['menu'][$which] as $link) {
      echo '<li><a href="'.$link['href'].'" '.$link['target'].'>'.$link['name'].'</a></li>';
    }
  }
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
global $l, $cmsurl, $theme_url, $settings, $user, $num_queries;
echo link_tree().'
  </div>
  <div class="footer">
      ';
languageOption();
echo '
    <p>'.str_replace('%snowcms%','<a href="http://www.snowcms.com/" onClick="window.open(this.href); return false;">SnowCMS '.$settings['version'].'</a>',$l['main_powered_by']).' | '.str_replace('%whom%','<a href="http://www.snowcms.com/" onclick="window.open(this.href); return false;">The SnowCMS Team</a>',$l['main_theme_by']).' | ', str_replace('%queries%', $num_queries, $l['page_made']), '</p>
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
    
    echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="language-option"><p>
    <select name="change-language">
    ';
    
    foreach (scandir($language_dir) as $language)
      if (substr($language,0,1) != '.') {
        $l_temp = $l;
        include $language_dir.'/'.$language;
        if ($current_language.'.language.php' == $language)
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