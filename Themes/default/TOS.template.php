<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//            TOS.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $l, $settings;
  
  echo '
  <h1>'.str_replace('%site%',$settings['site_name'],$l['tos_title']).'</h1>
  
  '.$settings['page']['body'];
}

function Manage() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.$l['tos_header'].'</h1>
  
  <p>'.$l['tos_desc'].'</p>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=tos" method="post">
    <p>
      ';
  if (!$settings['enable_tos'])
    echo '<input type="hidden" name="enable_tos" value="true" />
          <input type="submit" value="'.$l['tos_enable'].'" />';
  else
    echo '<input type="hidden" name="disable_tos" value="true" />
          <input type="submit" value="'.$l['tos_disable'].'" />';
  echo '
    </p>
  </form>
  <form action="'.$cmsurl.'index.php?action=admin;sa=tos" method="post">
    <p>
      ';
  
  ShowLanguages();
  
  echo '
    </p>
  </form>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=pages" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="true" />
      <input type="submit" value="'.$l['tos_cancel'].'" />
    </p>
  </form>
  ';
}

function Change() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.str_replace('%lang%',$settings['page']['language'],$l['tos_change_header']).'</h1>
  
  <p>'.str_replace('%lang%',$settings['page']['language'],$l['tos_change_desc']).'</p>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=tos" method="post">
    <p>
      ';
  if (!$settings['enable_tos'])
    echo '<input type="hidden" name="enable_tos" value="true" />
          <input type="submit" value="'.$l['tos_enable'].'" />';
  else
    echo '<input type="hidden" name="disable_tos" value="true" />
          <input type="submit" value="'.$l['tos_disable'].'" />';
  echo '
    </p>
  </form>
  <form action="'.$cmsurl.'index.php?action=admin;sa=tos" method="post">
    <p>
      <select name="l">
        ';
  foreach ($settings['page']['languages'] as $value => $name) {
    echo '<option value="'.$value.'">'.$name.'</option>
        ';
  }
  echo '
      </select>
      <input type="submit" value="'.$l['tos_change'].'" />
    </p>
  </form>
  <form action="'.$cmsurl.'index.php?action=admin;sa=tos;l='.$settings['page']['language'].'" method="post">
    <p>
      <input type="hidden" name="tos_lang" value="'.$settings['page']['language'].'" />
      ';
  if ($settings['page']['language'])
    echo '<textarea name="body" cols="70" rows="16">'.$settings['page']['tos'].'</textarea>
    ';
  echo '</p>
    <p>
      <input type="submit" value="'.$l['tos_change_submit'].'" />
    </p>
  </form>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=pages" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="true" />
      <input type="submit" value="'.$l['tos_cancel'].'" />
    </p>
  </form>
  ';
}

function OneLanguage() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.str_replace('%lang%',$settings['page']['language'],$l['tos_onelang_header']).'</h1>
  
  <p>'.str_replace('%lang%',$settings['page']['language'],$l['tos_onelang_desc']).'</p>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=tos" method="post">
    <p>
      ';
  if (!$settings['enable_tos'])
    echo '<input type="hidden" name="enable_tos" value="true" />
          <input type="submit" value="'.$l['tos_enable'].'" />';
  else
    echo '<input type="hidden" name="disable_tos" value="true" />
          <input type="submit" value="'.$l['tos_disable'].'" />';
  echo '
    </p>
  </form>
  <form action="'.$cmsurl.'index.php?action=admin;sa=tos" method="post">
    <p>
      <input type="hidden" name="tos_lang" value="'.$settings['page']['language'].'" />
      ';
  if ($settings['page']['language'])
    echo '<textarea name="body" cols="70" rows="16">'.$settings['page']['tos'].'</textarea>
    ';
  echo '</p>
    <p>
      <input type="submit" value="'.$l['tos_onelanguage_submit'].'" />
    </p>
  </form>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=pages" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="true" />
      <input type="submit" value="'.$l['tos_cancel'].'" />
    </p>
  </form>
  ';
}

function ShowLanguages() {
global $user, $settings, $l, $db_prefix, $language_dir, $cmsurl, $cookie_prefix;
  
  // Check how many languages there are
  $total_languages = 0;
  foreach (scandir($language_dir) as $language)
    if (substr($language,0,1) != '.')
      $total_languages += 1;
  
  $current_language = $settings['page']['language'];
  
  echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="text-align: center"><p>
  <select name="l">
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
?>