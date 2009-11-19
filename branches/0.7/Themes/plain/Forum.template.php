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
  <script type="text/javascript">
  /*vX Ajax Function. (C) Antimatter15 2008*/
  function vX(u,f,p){var x=(window.ActiveXObject)?new ActiveXObject("Microsoft.XMLHTTP"):new XMLHttpRequest();
  x.open(p?"POST":"GET",u,true);if(p) x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  x.onreadystatechange=function(){if(x.readyState==4&&x.status==200) f(x.responseText)};x.send(p)}
  /*QuickEdit*/
  function quickEdit(tid, mid){
    alert("if this actually worked, you\'d be editing a post now. (tid:"+tid+", mid: "+mid+")");
    vX("index.php", function(e){
      alert("But just to show how awesome Antimatter15\'s vX Ajax library is, index.php is "+e.length+" characters long.")
      alert("Seriously");
      alert("Now go get angry at me. Or soemthing.");
      vX("forum.php?action=post2;topic="+tid, function(e){
        alert("Now go refresh the page and see how awesome vX really is");
        alert("If you\'re a dev (Myles or Aldo), this needs 3 things to work.\\n 1) it needs to have a way to get the BBCode source of a post. \\n 2) it needs a way to edit (duh, i think this is already implemented).");
        
        if(confirm("Do you really really wanna edit?")){
          var content = prompt("Put in what crap you wan to edit this post into")
          if(content){
            vX("forum.php?action=post2;topic="+tid, function(e){
              alert("You Win!");
              alert("Now, Myles/Aldo, you also need a way to grab the updated HTML output as well as the BBCode.");
            },"edit="+mid+"&body="+content)
          }else{
            alert("looser, you need to put crap in it!")
          }
        }else{
          alert("You=Lose");
        }
        
      }, "body=Antimatter15 has an awesome vX AjaX Library.")
    })
  }
  </script>
</head>
<body>
<div style="width: 1000px; background-color: white; border: solid #CCCCCC 1px; margin: auto">
  <div style="text-align: center; margin-top: 10px">
    <img src="'.$theme_url.'/'.$settings['theme'].'/images/site_logo.png" />
  </div>
<div style="width: 200px; float: left">
  <ul>
';
theme_menu('main');
echo '
  </ul>
</div>
<div style="width: 200px; float: right">
  <ul>
';
theme_menu('side');
echo '
  </ul>
</div>
<div style="width: 580px; margin-left: 210px">
  ';
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
echo '
  </div>
  <p style="text-align: center">'.str_replace('%snowcms%','<a href="http://www.snowcms.com/" onClick="window.open(this.href); return false;">SnowCMS '.$settings['version'].'</a>',$l['main_powered_by']).' | '.str_replace('%whom%','<a href="http://www.snowcms.com/" onclick="window.open(this.href); return false;">The SnowCMS Team</a>',$l['main_theme_by']).'</p>
</div>
<div style="clear: both"></div>
</body>
</html>';
}
?>