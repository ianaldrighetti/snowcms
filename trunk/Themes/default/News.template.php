<?php
// ManageForum.template.php by the SnowCMS Team
if(!defined('Snow'))
  die("Hacking Attempt..");

function Main() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // Load the categories listbox element
  loadCategories();
  
  echo '
  <h1>', $l['news_header'], '</h1>';
  
  $prev_page = $settings['page']['previous_page'];
  $page = $settings['page']['current_page'];
  $next_page = $settings['page']['next_page'];
  $total_news = $settings['page']['total_news'];
  if ($cat = $settings['page']['cat'])
    $cat = ';cat='.$cat;
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=news" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
      ';
  
  // Show the pervious page link if it is at least page two
  if ($prev_page > 0)
     echo '<table width="100%">
      <tr><td><a href="'.$cmsurl.'index.php?action=news'.$cat.';pg='.$prev_page.'">'.$l['news_previous_page'].'</a></td>
      ';
  // Show the previous page link if it is page one
  elseif ($prev_page == 0)
    echo '<table width="100%">
      <tr><td><a href="'.$cmsurl.'index.php?action=news'.$cat.'">'.$l['news_previous_page'].'</a></td>
      ';
  // Don't show the previous page link, because it is the first page
  else
    echo '<table width="100%">
      <tr><td></td>
      ';
  // Show the next page link
  if (@($total_news / $settings['num_news_items']) > $next_page)
    echo '<td style="text-align: right"><a href="'.$cmsurl.'index.php?action=news'.$cat.';pg='.$next_page.'">'.$l['news_next_page'].'</a></td></tr>
      </table>
      ';
  // Don't show the next page link, because it is the last page
  else
    echo '<td style="text-align: right"></td></tr>
      </table>
      ';
  
  // Show news on this page
  $i = 0;
  while ($i < count($settings['news'])) {
    $news = $settings['news'][$i];
    echo '
    <p><b>'.str_replace('%subject%','<a href="'.$cmsurl.'index.php?action=news;id='.$news['id'].'">'.$news['subject'].'</a>',
         str_replace('%category%','<a href="'.$cmsurl.'index.php?action=news;cat='.$news['cat_id'].'">'.$news['cat_name'].'</a>',
         str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$news['user_id'].'">'.$news['username'].'</a>',
         str_replace('%date%',$news['post_date'],$l['news_heading'])))).'</b></p>
    <p>'.bbc($news['body']).'</p>
    ';
    if ($news['allow_comments'])
      echo '<p>'.str_replace('%num%','<a href="'.$cmsurl.'index.php?action=news;id='.$news['id'].'">'.$news['numComments'].'</a>',$l['news_comments']).'</p>
    ';
    $i += 1;
  }
  
  // Show the pervious page link if it is at least page two
  if ($prev_page > 0)
     echo '<table width="100%">
      <tr><td><a href="'.$cmsurl.'index.php?action=news'.$cat.';pg='.$prev_page.'">'.$l['news_previous_page'].'</a></td>
      ';
  // Show the previous page link if it is page one
  elseif ($prev_page == 0)
    echo '<table width="100%">
      <tr><td><a href="'.$cmsurl.'index.php?action=news'.$cat.'">'.$l['news_previous_page'].'</a></td>
      ';
  // Don't show the previous page link, because it is the first page
  else
    echo '<table width="100%">
      <tr><td></td>
      ';
  // Show the next page link
  if (@($total_news / $settings['num_news_items']) > $next_page)
    echo '<td style="text-align: right"><a href="'.$cmsurl.'index.php?action=news'.$cat.';pg='.$next_page.'">'.$l['news_next_page'].'</a></td></tr>
      </table>
      ';
  // Don't show the next page link, because it is the last page
  else
    echo '<td style="text-align: right"></td></tr>
      </table>
      ';
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=news" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
      ';
}

function Manage() {
global $l, $cmsurl;
  
  echo '
  <h1>'.$l['news_manage_header'].'</h1>
  
  <p>'.$l['news_manage_desc'].'</p>
  
  <div class="acp_left">
    <p class="main"><a href="'. $cmsurl. 'index.php?action=admin;sa=news;ssa=add" title="'. $l['news_manage_add']. '">'. $l['news_manage_add']. '</a></p>
    <p class="desc">'. $l['news_manage_add_desc']. '</p>
  </div>
  <div class="acp_right">
    <p class="main"><a href="'. $cmsurl. 'index.php?action=admin;sa=news;ssa=categories" title="'. $l['news_manage_categories']. '">'. $l['news_manage_categories']. '</a></p>
    <p class="desc">'. $l['news_manage_categories_desc']. '</p>
  </div>
  ';
  
}

function Add() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.$l['news_add_header'].'</h1>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=news;ssa=add" method="post">
  
  <p><input type="hidden" name="add-news" value="true" /></p>
  
  <table>
  <tr><td>'.$l['news_add_category'].'</td><td><select name="cat_id">
    ';
  
  $categories = $settings['page']['categories'];
  foreach ($categories as $value) {
    echo '<option value="'.$value['id'].'">'.$value['name'].'</option>
    ';
  }
  
  echo '</select></td></tr>
  <tr><td>'.$l['news_add_subject'].'</td><td><input name="subject" /></td></tr>
  </table>
  
  <p><textarea name="body" cols="70" rows="12"></textarea></p>
  
  <p><input type="checkbox" name="allow_comments" id="allow_comments" checked="checked" /> <label for="allow_comments">'.$l['news_add_allow_comments'].'</label></p>
  
  <p><input type="submit" value="'.$l['news_add_submit'].'"></p>
  
  </form>
  
  ';
}

function ShowCats() {
global $cmsurl, $db_prefix, $l, $settings, $user, $theme_url;
  echo '<h1>', $l['news_cats_header'], '</h1>
        <p>', $l['news_cats_desc'], '</p>
        <form action="', $cmsurl, 'index.php?action=admin;sa=news;ssa=categories" method="post">
          <table width="100%" id="mc">
            <tr>
              <th class="border" width="80%">', $l['news_cats_name'], '</th><th></th>
            </tr>';
          foreach($settings['cats'] as $cat) {
            echo '
            <tr>
              <td><input name="cat_name[', $cat['id'], ']" type="text" class="name" value="', $cat['name'], '"/></td>
              <td class="delete"><a href="', $cmsurl, 'index.php?action=admin;sa=news;ssa=categories;delete=', $cat['id'], ';sc=', $user['sc'], '" onClick="return confirm(\'', $l['news_cats_areyousure'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['news_cats_delete'].'" width="15" height="15" /></td>
            </tr>';
          }
          echo '
          </table>
          <p>
            <input type="hidden" name="update_cats" value="true" />
            <input type="submit" value="', $l['news_cats_update'], '" />
          </p>
        </form>
        <br />
        <form action="', $cmsurl, 'index.php?action=admin;sa=news;ssa=categories" method="post">
          <table id="add_cat">
            <tr>
              <th style="border-style: solid; border-width: 1px">'.$l['news_cats_add_name'].'</th>
              <th></th>
            </tr>
            <tr>
              <td style="text-align: center"><input class="cat_name" name="cat_name" style="width: 90%" /></td>
              <td>
                <input type="hidden" name="add_cat" value="true" />
                <input type="submit" value="'.$l['news_cats_add_submit'].'"/>
              </td>
            </tr>
          </table>
        </form>';
}

function Single() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  echo '
  <h1>', $l['news_header'], '</h1>';
  
  // Show news
  $news = $settings['news'];
  echo '
    <p><b>'.str_replace('%subject%','<a href="'.$cmsurl.'index.php?action=news;id='.$news['id'].'">'.$news['subject'].'</a>',
         str_replace('%category%','<a href="'.$cmsurl.'index.php?action=news;cat='.$news['cat_id'].'">'.$news['cat_name'].'</a>',
         str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$news['user_id'].'">'.$news['username'].'</a>',
         str_replace('%date%',$news['post_date'],$l['news_heading'])))).'</b></p>
    <p>'.bbc($news['body']).'</p>
    ';
}

function SingleComments() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  echo '
  <h1>', $l['news_header'], '</h1>';
  
  // Show news
  $news = $settings['news'];
  echo '
    <p><b>'.str_replace('%subject%','<a href="'.$cmsurl.'index.php?action=news;id='.$news['id'].'">'.$news['subject'].'</a>',
         str_replace('%category%','<a href="'.$cmsurl.'index.php?action=news;cat='.$news['cat_id'].'">'.$news['cat_name'].'</a>',
         str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$news['user_id'].'">'.$news['username'].'</a>',
         str_replace('%date%',$news['post_date'],$l['news_heading'])))).'</b></p>
    <p>'.bbc($news['body']).'</p>
    <hr />
    ';
  
  // Show comments
  $comments = $settings['comments'];
  $i = 0;
  while ($i < count($comments)) {
    echo '
    <p><b>'.str_replace('%subject%',$comments[$i]['subject'],
         str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$comments[$i]['user_id'].'">'.$comments[$i]['username'].'</a>',
         str_replace('%date%',$comments[$i]['post_date'],$l['news_comment_heading']))).'</b></p>
    <p>'.bbc($comments[$i]['body']).'</p>
    <hr />
    ';
    $i += 1;
  }
  
  // Add comment form
  if (can('make_comment'))
    echo '
  <form action="'.$cmsurl.'index.php?action=news;id='.$news['id'].'" method="post">
  
  <p>
  <input type="hidden" name="add-comment" value="true" />
  <input type="hidden" name="nid" value="'.$news['id'].'" />
  </p>
  
  <table>
  <tr><td>'.$l['news_comment_subject'].'</td><td><input name="subject" value="Re: '.$news['subject'].'" /></td></tr>
  </table>
  
  <p><textarea name="body" cols="60" rows="8"></textarea></p>
  
  <p><input type="submit" value="'.$l['news_comment_submit'].'"></p>
  
  </form>
  ';
}

function NoNews() {
global $l, $settings, $cmsurl;
  
  // Load the categories listbox element
  loadCategories();
  
  echo '
  <h1>', $l['news_nonews_header'], '</h1>';
  
  $prev_page = $settings['page']['previous_page'];
  $page = $settings['page']['current_page'];
  $next_page = $settings['page']['next_page'];
  $total_news = $settings['page']['total_news'];
  if ($cat = $settings['page']['cat'])
    $cat = ';cat='.$cat;
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=news" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
      ';
  
  echo '<p style="clear: both">'.$l['news_nonews_desc'].'</p>';
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=news" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
      ';
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
?>