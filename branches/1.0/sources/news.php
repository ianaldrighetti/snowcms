<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
#
# void news_switch();
#
# void news_list();
#
# void news_post();
#

function news_switch()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;
  
  # Find out what we're doing
  if(!empty($_GET['id']))
  {
    # Display a news post
    news_post();
  }
  else
  {
    # Display the list of news
    news_list();
  }
}

function news_list()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;
  
  # Load the language :P
  language_load('news');
  
  # Get all the news articles :) But of course, pagination :D
  $result = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}news",
    array());
  @list($num_news) = $db->fetch_row($result);

  # Create the pagination...
  $page['pagination'] = pagination_create($base_url. '/index.php?action=news', $_GET['page'], $num_news, $user['per_page']['news']);
  
  # Get the news articles from the database
  $result = $db->query("
    SELECT
      n.news_id, n.cat_id, n.member_id, n.modified_member_id, n.modified_name,
      n.modified_time, n.subject, n.poster_time, n.poster_name, n.poster_email,
      n.body, n.num_comments, n.num_views, n.allow_comments, n.is_viewable, nc.cat_id,
      nc.cat_name, mem.member_id, mem.displayName, mem2.member_id AS modified_id,
      mem2.displayName as modified_displayName
    FROM {$db->prefix}news AS n
      LEFT JOIN {$db->prefix}news_categories AS nc ON nc.cat_id = n.cat_id
      LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = n.member_id
      LEFT JOIN {$db->prefix}members AS mem2 ON mem2.member_id = n.modified_member_id
    ORDER BY n.poster_time DESC
    LIMIT %start, %news_per_page",
    array(
      'start' => array('int', !empty($_REQUEST['page']) ? $_REQUEST['page'] : 0),
      'news_per_page' => array('int', $user['per_page']['news']),
    ));

  # Lets get them loaded up!
  $page['news'] = array();
  while($row = $db->fetch_assoc($result))
  {
    # Add the news post
    $page['news'][] = $row;
    
    # Add the BBCode to the news post
    $page['news'][count($page['news']) - 1]['body'] = bbc($page['news'][count($page['news']) - 1]['body']);
  }
  
  # Check if there was any news
  if($page['news'])
  {
    # We need to show the theme ;)
    $page['title'] = $l['news_list_title'];
    
    theme_load('news', 'news_list_show');
  }
  else
  {
    # No news, but we still need to load the theme, just differently
    $page['title'] = $l['news_list_none_title'];
    
    theme_load('news', 'news_list_show_none');
  }
}

function news_post()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;
  
  # Load the language
  language_load('news');
  
  # Get the news article from the database
  $result = $db->query("
    SELECT
      n.news_id, n.cat_id, n.member_id, n.modified_member_id, n.modified_name,
      n.modified_time, n.subject, n.poster_time, n.poster_name, n.poster_email,
      n.body, n.num_comments, n.num_views, n.allow_comments, n.is_viewable, nc.cat_id,
      nc.cat_name, mem.member_id, mem.displayName, mem2.member_id AS modified_id,
      mem2.displayName as modified_displayName
    FROM {$db->prefix}news AS n
      LEFT JOIN {$db->prefix}news_categories AS nc ON nc.cat_id = n.cat_id
      LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = n.member_id
      LEFT JOIN {$db->prefix}members AS mem2 ON mem2.member_id = n.modified_member_id
    WHERE n.news_id = %news_id",
    array(
      'news_id' => array('int', $_GET['id']),
    ));

  # Lets get them loaded up!
  $page['news'] = $db->fetch_assoc($result);
  
  # Add the BBCode to the news post
  $page['news']['body'] = bbc($page['news']['body']);
  
  # Load the title and theme
  $page['title'] = $page['news']['subject'];

  theme_load('news', 'news_post_show');
}
?>