<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           News.template.php

if(!defined('Snow'))
  die("Hacking Attempt..");

function Main() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // Load the categories listbox element
  loadCategories();
  
  echo '
  <h1>', $l['news_header'], '</h1>';
  
  if ($cat = $settings['page']['cat'])
    $cat = ';cat='.$cat;
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=news" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
      <br />
      ';
  
  pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=news'.$cat);
  
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
      echo '<p>'.str_replace('%num%','<a href="'.$cmsurl.'index.php?action=news;id='.$news['id'].'">'.$news['num_comments'].'</a>',$l['news_comments']).'</p>
    ';
    $i += 1;
  }
  
  pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=news'.$cat);
  
  // Show categories
  echo '<form action="'.$cmsurl.'index.php?action=news" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
     '.$settings['page']['categories'].'
      </p></form>
      ';
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
global $cmsurl, $db_prefix, $l, $settings, $user, $theme_url;
  
  echo '
  <h1>', $l['news_header'], '</h1>';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
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
    <p>
      <b>
        '.str_replace('%subject%',$comments[$i]['subject'],
          str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$comments[$i]['user_id'].'">'.$comments[$i]['username'].'</a>',
          str_replace('%date%',$comments[$i]['post_date'],$l['news_comment_heading']))).'
      </b>';
    if (can('manage_comments_edit'))
      echo '<a href="'.$cmsurl.'index.php?action=news;id='.$news['id'].';edit='.$comments[$i]['id'].'">
        <img src="'.$theme_url.'/'.$settings['theme'].'/images/modify.png" alt="'.$l['managenews_manage_edit'].'" width="15" height="15" />
      </a>';
    if (can('manage_comments_edit') && can('manage_comments_delete'))
      echo '
      ';
    if (can('manage_comments_delete'))
      echo '<a href="'.$cmsurl.'index.php?action=news;id='.$news['id'].';did='.$comments[$i]['id'].';sc='.$user['sc'].'">
        <img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['managenews_manage_delete'].'" width="15" height="15" />
      </a>';
    echo '
    </p>
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

function EditComment() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  echo '
  <h1>'.$l['news_editcomment_header'].'</h1>
  
  ';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  else
   echo '<p>'.$l['news_editcomment_desc'].'</p>';
  
  $comment = $settings['page']['comment'];
  
  echo '
  
  <form action="'.$cmsurl.'index.php?action=news;id='.$comment['nid'].';edit='.$comment['post_id'].'" method="post" style="display: inline">
  
  <p>
    <input type="hidden" name="edit-comment" value="true" />
  </p>
  
  <table>
  <tr><td>'.$l['news_editcomment_subject'].'</td><td><input name="subject" value="'.$comment['subject'].'" /></td></tr>
  </table>
  
  <p>
    <textarea name="body" cols="60" rows="8">'.$comment['body'].'</textarea>
  </p>
  
  <p style="display: inline"><input type="submit" value="'.$l['news_editcomment_submit'].'"></p>
  
  </form>
  
  <form action="'.$cmsurl.'index.php?action=news;id='.$comment['nid'].'" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="true" />
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
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