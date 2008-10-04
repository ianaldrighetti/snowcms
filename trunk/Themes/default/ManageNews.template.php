<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           News.template.php

if(!defined('Snow'))
  die("Hacking Attempt..");

function Main() {
global $l, $cmsurl;
  
  echo '
  <h1>'.$l['managenews_header'].'</h1>
  
  <p>'.$l['managenews_desc'].'</p>
  ';
  
  NewsOptions();
  
  echo '
  
  <p style="clear: both">
    <br />
  </p>
  
  <form action="'.$cmsurl.'index.php?action=admin" method="post">
    <p>
      <input type="hidden" name="redirect" value="admin" />
      <input type="submit" value="'.$l['main_back'].'" />
    </p>
  </form>
  ';
}

function AddNews() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.$l['managenews_add_header'].'</h1>
  ';
  
  if (@$_SESSION['error'])
	  echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
	else
    echo '<p>', $l['managenews_add_desc'], '</p>';
  
  echo
  '
  <form action="'.$cmsurl.'index.php?action=admin;sa=news;ssa=add" method="post" style="display: inline">
  
  <p><input type="hidden" name="add-news" value="true" /></p>
  
  <table>
  <tr><td>'.$l['managenews_add_category'].':</td><td><select name="cat_id">
    ';
  
  $categories = $settings['page']['categories'];
  foreach ($categories as $value) {
    echo '<option value="'.$value['id'].'">'.$value['name'].'</option>
    ';
  }
  
  echo '</select></td></tr>
  <tr><td>'.$l['managenews_add_subject'].':</td><td><input name="subject" /></td></tr>
  </table>
  
  <p><textarea name="body" cols="70" rows="12"></textarea></p>
  
  <p><input type="checkbox" name="allow_comments" id="allow_comments" checked="checked" /> <label for="allow_comments">'.$l['managenews_add_allowcomments'].'</label></p>
  
  <p style="display: inline"><input type="submit" value="'.$l['managenews_add_submit'].'"></p>
  
  </form>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=news" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect1" value="true" />
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
  </form>';
}

function EditNews() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.$l['managenews_edit_header'].'</h1>
  ';
  
  if (@$_SESSION['error'])
	  echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
	else
    echo '<p>', $l['managenews_edit_desc'], '</p>';
  
  $news = $settings['page']['news'];
  
  echo
  '
  <form action="'.$cmsurl.'index.php?action=admin;sa=news;ssa=manage;id='.$news['news_id'].'" method="post" style="display: inline">
  
  <p><input type="hidden" name="edit-news" value="true" /></p>
  
  <table>
  <tr><td>'.$l['managenews_edit_category'].':</td><td><select name="cat_id">
    ';
  
  $categories = $settings['page']['categories'];
  foreach ($categories as $value) {
    echo '<option value="'.$value['id'].'">'.$value['name'].'</option>
    ';
  }
  
  echo '</select></td></tr>
  <tr><td>'.$l['managenews_edit_subject'].':</td><td><input name="subject" value="'.$news['subject'].'" /></td></tr>
  </table>
  
  <p>
    <textarea name="body" cols="70" rows="12">'.$news['body'].'</textarea>
  </p>
  
  <p><input type="checkbox" name="allow_comments" id="allow_comments"'.($news['allow_comments'] ? ' checked="checked"' : '').' /> <label for="allow_comments">'.$l['managenews_edit_allowcomments'].'</label></p>
  
  <p style="display: inline"><input type="submit" value="'.$l['managenews_edit_submit'].'"></p>
  
  </form>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=news;ssa=manage" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect1" value="true" />
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
  </form>
  
  ';
}

function ShowCats() {
global $cmsurl, $db_prefix, $l, $settings, $user, $theme_url;
  
  echo '
  <h1>', $l['managenews_cats_header'], '</h1>
  
  <p>', $l['managenews_cats_desc'], '</p>
  
  <form action="', $cmsurl, 'index.php?action=admin;sa=news;ssa=categories" method="post">
    <table width="100%" id="mc">
      <tr>
        <th class="border" width="80%">', $l['managenews_cats_name'], '</th><th></th>
      </tr>';
    foreach($settings['cats'] as $cat) {
      echo '
      <tr>
        <td><input name="cat_name[', $cat['id'], ']" type="text" class="name" value="', $cat['name'], '"/></td>
        <td class="delete"><a href="', $cmsurl, 'index.php?action=admin;sa=news;ssa=categories;delete=', $cat['id'], ';sc=', $user['sc'], '" onClick="return confirm(\'', $l['managenews_cats_areyousure'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['managenews_cats_delete'].'" width="15" height="15" /></td>
      </tr>';
    }
    echo '
    </table>
    <p>
      <input type="hidden" name="update_cats" value="true" />
      <input type="submit" value="', $l['managenews_cats_update'], '" />
    </p>
  </form>
  <br />
  <form action="', $cmsurl, 'index.php?action=admin;sa=news;ssa=categories" method="post">
    <table id="add_cat">
      <tr>
        <th style="border-style: solid; border-width: 1px">'.$l['managenews_cats_add_name'].'</th>
        <th></th>
      </tr>
      <tr>
        <td style="text-align: center"><input class="cat_name" name="cat_name" style="width: 90%" /></td>
        <td>
          <input type="hidden" name="add_cat" value="true" />
          <input type="submit" value="'.$l['managenews_cats_add_submit'].'"/>
        </td>
      </tr>
    </table>
  </form>';
}

function ShowNews() {
global $cmsurl, $db_prefix, $l, $settings, $user, $theme_url;
  
  // Load the categories listbox element
  loadCategories();
  
  echo '
  <h1>', $l['managenews_manage_header'], '</h1>';
  
  if (@$_SESSION['error'])
	  echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
	else
    echo '<p>', $l['managenews_manage_desc'], '</p>';
  
  if ($cat = $settings['page']['cat'])
    $cat = ';cat='.$cat;
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=admin;sa=news;ssa=manage" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
      <br />
      ';
  
  pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=news;ssa=manage'.$cat);
  
  // Show news on this page
  $i = 0;
  while ($i < count($settings['news'])) {
    $news = $settings['news'][$i];
    echo '
    <p>
      <b>
        '.str_replace('%subject%','<a href="'.$cmsurl.'index.php?action=admin;sa=news;ssa=manage;id='.$news['id'].'">'.$news['subject'].'</a>',
          str_replace('%category%','<span style="font-weight: normal">'.$news['cat_name'].'</span>',
          str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$news['user_id'].'">'.$news['username'].'</a>',
          str_replace('%date%',$news['post_date'],$l['news_heading'])))).'
      </b>
      <a href="'.$cmsurl.'index.php?action=admin;sa=news;ssa=manage;id='.$news['id'].'">
        <img src="'.$theme_url.'/'.$settings['theme'].'/images/modify.png" alt="'.$l['managenews_manage_edit'].'" width="15" height="15" />
      </a>
      <a href="'.$cmsurl.'index.php?action=admin;sa=news;ssa=manage;did='.$news['id'].';sc='.$user['sc'].'">
        <img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['managenews_manage_delete'].'" width="15" height="15" />
      </a>
    </p>
    <p>'.bbc($news['body']).'</p>
    ';
    if ($news['allow_comments'])
      echo '<p style="font-size: 90%"><i>'.str_replace('%num%','<b>'.$news['num_comments'].'</b>',$l['news_comments']).'</i></p>
    ';
    $i += 1;
  }
  
  pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=news;ssa=manage'.$cat);
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=news" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
  
  <p style="clear: both">
    <br />
  </p>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=news" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect1" value="true" />
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
  </form>';
}

function NoNews() {
global $l, $settings, $cmsurl;
  
  // Load the categories listbox element
  loadCategories();
  
  echo '
  <h1>', $l['news_nonews_header'], '</h1>
  
  <p>', $l['managenews_manage_desc'], '</p>
  
  ';
  
  $prev_page = $settings['page']['previous_page'];
  $page = $settings['page']['current_page'];
  $next_page = $settings['page']['next_page'];
  $total_news = $settings['page']['total_news'];
  if ($cat = $settings['page']['cat'])
    $cat = ';cat='.$cat;
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=admin;sa=news;ssa=manage" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
      ';
  
  echo '<p style="clear: both">'.$l['news_nonews_desc'].'</p>';
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=news" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
  
  <p style="clear: both">
    <br />
  </p>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=news" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect1" value="true" />
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
  </form>';
}

function DoesntExist() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>', $l['news_doesntexist_header'], '</h1>
  <p style="clear: both">'.$l['news_doesntexist_desc'].'</p>';
}

function loadCategories() {
global $l, $settings;
  
  // Get categories HTML
  $categories = '
    <input type="hidden" name="action" value="news" />
    <select name="cat">
    ';
 if (!@$_REQUEST['cat'])
   $categories .= '<option value="all" selected="selected">All</option>
    ';
 else
   $categories .= '<option value="all">All</option>
    ';
  
  foreach ($settings['page']['categories'] as $cat) {
    if (@$_REQUEST['cat'] == $cat['cat_id'])
      $categories .= '<option value="'.$cat['cat_id'].'" selected="selected">'.$cat['cat_name'].'</option>'."\n";
    else
      $categories .= '<option value="'.$cat['cat_id'].'">'.$cat['cat_name'].'</option>'."\n";
  }
  $categories .= '</select>';
  
  $categories .= '
          <input type="submit" value="'.$l['news_category_change'].'" />
          ';
  
  $settings['page']['categories'] = $categories;
}

function NewsOptions() {
global $l, $settings, $cmsurl;
  
  $options = $settings['page']['options'];
  
  $odd = true;
  foreach ($options as $option) {
    echo '
  <div class="acp_'.($odd ? 'left' : 'right').'">
    <p class="main"><a href="'.$cmsurl.'index.php?action=admin;sa=news;ssa='.$option.'" title="'.$l['managenews_menu_'.$option].'">'.$l['managenews_menu_'.$option].'</a></p>
    <p class="desc">'.$l['managenews_menu_'.$option.'_desc'].'</p>
  </div>
  ';
    $odd = !$odd;
  }
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