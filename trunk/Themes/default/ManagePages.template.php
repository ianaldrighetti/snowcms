<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//        ManagePages.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $values, $l, $user, $theme_url;
  
  $pg = $settings['page']['page'] ? ';pg='.$settings['page']['page'] : '';
  $s = $settings['page']['sort'] ? ';s='.$settings['page']['sort'] : '';
  
  echo '
  <h1>'.$l['managepages_header'].'</h1>
  ';
  
  if (@$_SESSION['error'])
	 echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
	
  echo '
  <p>'.$l['managepages_desc'].'</p>';
  
  if($settings['page']['make_page']['do']) {
    if($settings['page']['make_page']['status']) {
      echo '
      <div id="page_success">
        <p>'.$settings['page']['make_page']['info'].'</p>
      </div>';
    }
    else {
      echo '
      <div id="page_fail">
        <p>'.$settings['page']['make_page']['info'].'</p>
      </div>';    
    }
  }
  
  if (can('manage_pages_create'))
  echo '
  <form action="'.$cmsurl.'index.php?action=admin;sa=pages" method="post">
    <p><input type="hidden" name="create_page" value="true"></p>
    <p><label>'.$l['managepages_newpagetitle'].'</label> <input name="page_title" value="'.$values['page_title'].'" /> <input type="submit" value="'.$l['managepages_createpage'].'" /></p>
  </form>
  ';
  
  echo '
  <form action="'.$cmsurl.'index.php?action=admin;sa=pages" method="post">
  ';
  
  pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=pages'.$s);
  
  echo '<table width="100%" style="text-align: center">
    <tr>
      <th class="no-border" style="width: 5%"></th>
      <th style="border-style: solid; border-width: 1px; width: 37%"><a href="'.$cmsurl.'index.php?action=admin;sa=pages'.$pg.';s=title'.
        ($settings['page']['sort'] == 'title' ? '_desc' : '')
        .'">'.$l['managepages_pagetitle'].'</a></th>
      <th style="border-style: solid; border-width: 1px; width: 22%"><a href="'.$cmsurl.'index.php?action=admin;sa=pages'.$pg.';s=owner'.
        ($settings['page']['sort'] == 'owner' ? '_desc' : '')
        .'">'.$l['managepages_pageowner'].'</a></th>
      <th style="border-style: solid; border-width: 1px; width: 32%"><a href="'.$cmsurl.'index.php?action=admin;sa=pages'.$pg.';s=creationdate'.
        ($settings['page']['sort'] == 'creationdate' ? '_desc' : '')
        .'">'.$l['managepages_datemade'].'</a></th>
      <th class="no-border" style="width: 4%"></th>
    </tr>';
  foreach($settings['page']['pages'] as $page) {
    echo '
    <tr>';
    if (!can('manage_pages_home'))
      echo '<td></td>
    ';
    elseif ($settings['homepage'] == $page['page'])
      echo '<td><input type="radio" name="homepage" value="'.$page['page'].'" checked="checked"></td>
    ';
    else
      echo '<td><input type="radio" name="homepage" value="'.$page['page'].'"></td>
    ';
    
    if (can('manage_pages_modify_html') || can('manage_pages_modify_bbcode'))
      echo '<td><a href="'.$cmsurl.'index.php?action=admin;sa=pages;page='.$page['page'].'">'.$page['title'].'</a></td><td>';
    else
      echo '<td>'.$page['title'].'</td><td>';
    
    if ($page['page_owner'] != -1)
      echo '<a href="'.$cmsurl.'index.php?action=profile;u='.$page['page_owner'].'">'.$page['owner'].'</a>';
    else
      echo $page['owner'];
    
    echo '</td><td>'.$page['date'].'</td>';
    
    if (can('manage_pages_delete'))
      echo '<td><a href="'.$cmsurl.'index.php?action=admin;sa=pages'.$s.$pg.';did='.$page['page'].';sc='.$user['sc'].'" onclick="return confirm(\'', $l['managepages_delete_areyousure'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['managepages_delete'].'" width="15" height="15" style="border: 0" /></a></td>';
    else
      echo '<td></td>';
    
    echo '
    </tr>';
  }
  echo '
  </table>
  ';
  
  pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=pages'.$s);
  
  if (can('manage_pages_home'))
    echo '<p><input type="submit" value="'.$l['managepages_change_homepage'].'" /></p>
  
  </form>';
  
  echo '
  <form action="'.$cmsurl.'index.php?action=admin" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="admin" />
      <input type="submit" value="'.$l['managepages_cancel'].'" />
    </p>
  </form>
  ';
}

function Editor() {
global $cmsurl, $settings, $l, $user, $theme_url;
  
  echo '
  <h1>'.str_replace('%title%',$settings['page']['edit_page']['title'],$l['managepages_edit_header']).'</h1>
  ';
  
  if (@$_SESSION['error'])
	 echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  echo '
  <p>'.$l['managepages_edit_desc'].'</p>
  
  <script type="text/javascript" src="'.$theme_url.'/'.$settings['theme'].'/scripts/bbcode.js"></script>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=pages" method="post" style="display: inline">
    <p>
      <input type="hidden" name="update_page" value="true" />
      <input type="hidden" name="page" value="'.$settings['page']['edit_page']['page'].'" />
    </p>
    <table>
      <tr>
        <td>'.$l['managepages_edit_pagetitle'].':</td><td><input name="page_title" value="'.$settings['page']['edit_page']['title'].'" /></td>
      </tr>
      <tr>
        <td colspan="2">
        <select name="links" id="links">
         ';
   
   while ($row = mysql_fetch_assoc($settings['page']['all-pages']))
     echo '<option value="'.$row['page'].'">'.$row['title'].'</option>
         ';
   
   echo '</select>
        <input type="button" value="'.$l['managepages_edit_insert_link'].'" onclick="add_bbcode(\'page_content\',\'<a href=\\\'index.php?page=\'+document.getElementById(\'links\').options[document.getElementById(\'links\').selectedIndex].value+\'\\\'>\',\'</a>\')" /></td>
      </tr>
      <tr>
        <td colspan="2"><textarea name="page_content" rows="16" cols="70" onclick="this.selection = document.selection.createRange()" onkeyup="this.selection = document.selection.createRange()" onchange="this.selection = document.selection.createRange().duplicate()" onfocus="this.selection = document.selection.createRange().duplicate()">'.$settings['page']['edit_page']['content'].'</textarea></td>
      </tr>
    </table>
    
    <p>
      ';
    if (can('manage_pages_modify_html') && !can('manage_pages_modify_bbcode')) {
      if (!$settings['page']['edit_page']['html'])
        echo $l['managepages_edit_bbcode_to_html'];
    }
    elseif (!can('manage_pages_modify_html') && can('manage_pages_modify_bbcode')) {
      if ($settings['page']['edit_page']['html'])
        echo $l['managepages_edit_html_to_bbcode'];
    }
    elseif ($settings['page']['edit_page']['html'])
      echo '<input type="radio" name="html" id="html" value="1" checked="checked" /> <label for="html">'.$l['managepages_edit_html'].'</label>
      <input type="radio" name="html" id="bbcode" value="0" /> <label for="bbcode">'.$l['managepages_edit_bbcode'].'</label>';
    else
      echo '<input type="radio" name="html" id="html" value="1" /> <label for="html">'.$l['managepages_edit_html'].'</label>
      <input type="radio" name="html" id="bbcode" value="0" checked="checked" /> <label for="bbcode">'.$l['managepages_edit_bbcode'].'</label>';
    echo '
    </p>
    
    <p style="display: inline">
      <input type="hidden" name="page" value="'.$settings['page']['edit_page']['page'].'" />
      <input type="submit" value="'.$l['managepages_edit_button'].'" />
    </p>
  </form>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=pages" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="true" />
      <input type="submit" value="'.$l['managepages_edit_cancel'].'" />
    </p>
  </form>
  
  <script type="text/javascript">
  document.getElementById(\'page_content\').focus();
  </script>';
}

function NoPage() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['managepages_no_page_header'].'</h1>
  
  <p>'.$l['managepages_no_page_desc'].'</p>';
}

function pagination($page, $last, $url) {
global $l, $cmsurl;
  
  echo '<p>';
  $i = $page < 2 ? 0 : $page - 2;
  if ($i > 1)
    echo '<a href="'.$cmsurl.$url.'">1</a> ... ';
  elseif ($i == 1)
    echo '<a href="'.$cmsurl.$url.'">1</a> ';
  while ($i < ($page + 3 < $last ? $page + 3 : $last)) {
    if ($i == $page)
      echo '<b>['.($i+1).']</b> ';
    elseif ($i)
      echo '<a href="'.$cmsurl.$url.';pg='.$i.'">'.($i+1).'</a> ';
    else
      echo '<a href="'.$cmsurl.$url.'">'.($i+1).'</a> ';
    $i += 1;
  }
  if ($i < $last - 1)
    echo '... <a href="'.$cmsurl.$url.';pg='.($last-1).'">'.$last.'</a>';
  elseif ($i == $last - 1)
    echo '<a href="'.$cmsurl.$url.';pg='.($last-1).'">'.$last.'</a>';
  echo '</p>';
}
?>