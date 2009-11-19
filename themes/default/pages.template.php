<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#      Page Layout template, January 16, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function pages_view_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the page stuff
  echo '
       <h1>', $page['page']['title'], '</h1>
       <div>
         ', $page['page']['content'], '
       </div>';
}

function pages_view_show_news()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the page stuff
  echo '
       <h1>', $page['page']['title'], '</h1>
       <div>
       ', $page['page']['content'], '
       </div>';
  
  if($page['news'])
    echo '
       <hr class="home_news_separator" />';
  
  # Echo the news
  foreach($page['news'] as $news)
  {
    echo '
      <div class="news home">
        <h2><a href="', $base_url, '/index.php?action=news;id=', $news['news_id'], '">', $news['subject'], '</a></h2>
        <div class="time_created">', timeformat($news['poster_time']), '</div>';
    
    if($news['cat_id'])
      echo '
        <div class="category">', $l['page_view_news_category'], ' <a href="', $base_url, '/index.php?action=news;cat=', $news['cat_id'], '">', $news['cat_name'], '</a></div>';
    
    echo '
        <div class="body">
          ', $news['body'], '
        </div>
        <div class="creator">', $l['page_view_news_created_by'], ' <a href="', $base_url, '/index.php?action=profile;u=', $news['member_id'], '">', $news['displayName'], '</a></a></div>
      </div>
      <br /><br />';
  }
}

function pages_view_show_error()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['page_error_header'], '</h1>
      <p>', $l['page_error_desc'], '</p>';
}
?>