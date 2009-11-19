<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Settings Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function news_list_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <div class="pagination top">', $page['pagination'], '</div>
      <br />';
  
  # Echo the news
  foreach($page['news'] as $news)
  {
    echo '
      <div class="news">
        <h1><a href="', $base_url, '/index.php?action=news;id=', $news['news_id'], '">', $news['subject'], '</a></h1>
        <div class="time_created">', timeformat($news['poster_time']), '</div>';
    
    if($news['cat_id'])
      echo '
        <div class="category">', $l['news_list_category'], ' <a href="', $base_url, '/index.php?action=news;cat=', $news['cat_id'], '">', $news['cat_name'], '</a></div>';
    
    echo '
        <div class="body">
          ', $news['body'], '
        </div>
        <div class="creator">', $l['news_list_created_by'], ' <a href="', $base_url, '/index.php?action=profile;u=', $news['member_id'], '">', $news['displayName'], '</a></a></div>
      </div>
      <br /><br />';
  }
  
  # Echo the footer stuff
  echo '
      <div class="pagination bottom">', $page['pagination'], '</div>';
}

function news_list_show_none()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['news_list_none_header'], '</h1>
      <p>', $l['news_list_none_desc'], '</p>';
}

function news_post_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $page['news']['subject'], '</h1>
      <div class="news_time_created">', timeformat($page['news']['poster_time']), '</div>';
    
    if($news['cat_id'])
      echo '
        <div class="category">', $l['news_list_category'], ' <a href="', $base_url, '/index.php?action=news;cat=', $news['cat_id'], '">', $news['cat_name'], '</a></div>';
    
    echo '
      <div class="news_body">
        ', $page['news']['body'], '
      </div>
      <div class="news_creator">', $l['news_post_created_by'], ' <a href="', $base_url, '/index.php?action=profile;u=', $page['news']['member_id'], '">', $page['news']['displayName'], '</a></a></div>
      <br />
      <div class="pagination bottom">', $page['pagination'], '</div>';
}
?>