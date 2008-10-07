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
  <script type="text/javascript">
  /*vX Ajax Function. (C) Antimatter15 2008*/
  function vX(u,f,p){var x=(window.ActiveXObject)?new ActiveXObject("Microsoft.XMLHTTP"):new XMLHttpRequest();
  x.open(p?"POST":"GET",u,true);if(p) x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  x.onreadystatechange=function(){if(x.readyState==4&&x.status==200) f(x.responseText)};x.send(p)}
  /*QuickEdit*/
  function quickEdit(tid, mid){
    vX("forum.php?bbcode="+mid, function(e){
      var el = document.getElementsByName("pcmid"+mid)[0];
      var bak = el.innerHTML;
      el.innerHTML = "<input type=\\"hidden\\" value=\\""+tid+";"+mid+"\\"><textarea name=\\"editor\\" style=\\"width: 100%; height: 200px\\">"+e+"</textarea><br><input type=\\"button\\" onClick=\\"quickEdit_save(this.parentNode)\\" value=\\"Save\\"><input type=\\"button\\" value=\\"Cancel\\" onClick=\\"quickEdit_cancel(this.parentNode)\\"><textarea name=\\"backup\\" style=\\"display: none\\">"+bak+"</textarea>";
    })
  }
  
  function quickEdit_cancel(cnt){
    cnt.innerHTML = cnt.getElementsByTagName("textarea")[1].value
  }
  
  function quickEdit_save(cnt){
    var tmid = cnt.getElementsByTagName("input")[0].value.split(";");
    
    vX("forum.php?action=post2;topic="+tmid[0], function(e){
      vX("forum.php?html="+tmid[1], function(x){
        cnt.innerHTML = x;
      });
    },"edit="+tmid[1]+"&body="+cnt.getElementsByTagName("textarea")[0].value)
  }
  </script>
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
global $l, $cmsurl, $theme_url, $settings, $user;
echo link_tree().'
  </div>
  <div class="footer">
    <p>'.str_replace('%snowcms%','<a href="http://www.snowcms.com/" onClick="window.open(this.href); return false;">SnowCMS '.$settings['version'].'</a>',$l['main_powered_by']).' | '.str_replace('%whom%','<a href="http://www.snowcms.com/" onclick="window.open(this.href); return false;">The SnowCMS Team</a>',$l['main_theme_by']).'</p>
  </div>
</div>
</body>
</html>';
}
?>