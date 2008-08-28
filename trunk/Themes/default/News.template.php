<?php
// ManageForum.template.php by the SnowCMS Team
if(!defined('Snow'))
  die("Hacking Attempt..");

function Main() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  echo '
  <h1>', $l['news_header'], '</h1>';
  
  $i = 0;
  while ($i < count($settings['news'])) {
    $news = $settings['news'][$i];
    echo '
    <p><b>'.$news['subject'].'</b> by '.$news['username'].' @ '.$news['post_date'].'</p>
    <p>'.bbc($news['body']).'</p>
    ';
    if ($news['allow_comments'])
      echo '<p>'.str_replace('%num%','<a href="'.$cmsurl.'index.php?action=news;id='.$news['id'].'">'.$news['numComments'].'</a>',$l['news_comments']).'</p>
    ';
    $i += 1;
  }
}

function None() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  echo '
  <h1>', $l['news_nonews_header'], '</h1>
  <p>', $l['news_nonews_desc'], '</p>
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
  ';
  
}

function Add() {
global $l, $cmsurl;
  
  echo '
  <h1>'.$l['news_add_header'].'</h1>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=news;ssa=add" method="post">
  
  <p><input type="hidden" name="add-news" value="true" /></p>
  
  <table>
  <tr><td>'.$l['news_add_category'].'</td><td><select name="cat_id">
    ';
  
  $categories = $settings['page']['categories'];
  foreach ($categories as $value) {
    echo '<option value="'.$value[0].'">'.$value[1].'</option>
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

function Single() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  echo '
  <h1>', $l['news_header'], '</h1>';
  
  // Show news
  $news = $settings['news'];
  echo '
    <p>'.str_replace('%subject%',$news['subject'],
         str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$news['user_id'].'">'.$news['username'].'</a>',
         str_replace('%date%',$news['post_date'],$l['news_heading']))).'</p>
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
    <p>'.str_replace('%subject%',$news['subject'],
         str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$news['user_id'].'">'.$news['username'].'</a>',
         str_replace('%date%',$news['post_date'],$l['news_heading']))).'</p>
    <p>'.bbc($news['body']).'</p>
    <hr />
    ';
  
  // Show comments
  $comments = $settings['comments'];
  $i = 0;
  while ($i < count($comments)) {
    echo '
    <p>'.str_replace('%subject%',$comments[$i]['subject'],
         str_replace('%name%','<a href="'.$cmsurl.'index.php?action=profile;u='.$comments[$i]['user_id'].'">'.$comments[$i]['username'].'</a>',
         str_replace('%date%',$comments[$i]['post_date'],$l['news_comment_heading']))).'</p>
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
?>