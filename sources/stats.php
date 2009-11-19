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
# Statistics are generated and shown here
#
# void stats_display();
#   - Shows statistics.
#
# int stats_gcd();
#   - Caculate greatest common divisor for the stats_lcm() function.
#
# int stats_lcm();
#   - Calculate lowest common multiple for ratios.
#

function stats_display()
{
  global $base_url, $db, $page, $settings, $user, $l;
  
  # Load the language
  language_load('stats');
  
  # First let's try the cache
  if(!empty($settings['cache_enabled']) && ($cache = cache_get('stats_'. $user['language']. '_'. $user['numberformat_hash'])) != null)
  {
    $page['stats'] = $cache['stats'];
    $page['top'] = $cache['top'];
    
    # Days online
    $days_online = floor((time_utc() - $settings['install_time']) / (60 * 60 * 24));
    
    # Page views doesn't need caching because we don't even need to call from the database
    $page['stats']['totals']['views'] = numberformat($settings['total_page_views']);
    $page['stats']['averages']['views'] = numberformat($days_online ? $settings['total_page_views'] / $days_online : 0, 2);
  }
  # No cache :/ well, we've got a lot to do
  else
  {
    # Member genders
    $result = $db->query("
      SELECT
        gender, COUNT(*)
      FROM {$db->prefix}members
      GROUP BY gender",
      array());
    # $gender[0] = Unspecified
    # $gender[1] = Female
    # $gender[2] = Male
    $gender = array(0, 0, 0);
    while($row = $db->fetch_assoc($result))
      $gender[$row['gender']] = $row['COUNT(*)'];
    
    # Latest member
    $result = $db->query("
      SELECT
        member_id, displayName
      FROM {$db->prefix}members
      ORDER BY reg_time DESC",
      array());
    @list($latest_member['id'], $latest_member['name']) = $db->fetch_row($result);
    
    # Latest page
    $result = $db->query("
      SELECT
        page_id, page_title
      FROM {$db->prefix}pages
      ORDER BY created_time DESC",
      array());
    @list($latest_page['id'], $latest_page['title']) = $db->fetch_row($result);
    
    # Latest forum topic
    $result = $db->query("
      SELECT
        tpc.topic_id, msg.subject
      FROM {$db->prefix}topics AS tpc
      LEFT JOIN {$db->prefix}messages AS msg ON tpc.topic_id = msg.topic_id
      ORDER BY msg.poster_time DESC",
      array());
    @list($latest_topic['id'], $latest_topic['subject']) = $db->fetch_row($result);
    
    # Latest forum post
    $result = $db->query("
      SELECT
        topic_id, subject
      FROM {$db->prefix}messages
      ORDER BY poster_time DESC",
      array());
    @list($latest_post['id'], $latest_post['subject']) = $db->fetch_row($result);
    
    # Latest news article
    $result = $db->query("
      SELECT
        news_id, subject
      FROM {$db->prefix}news",
      array());
    @list($latest_news['id'], $latest_news['subject']) = $db->fetch_row($result);
    
    # Latest news comment
    $result = $db->query("
      SELECT
        comment_id, subject
      FROM {$db->prefix}news_comments",
      array());
    @list($latest_news_comment['id'], $latest_news_comment['subject']) = $db->fetch_row($result);
    
    # Latest download
    $result = $db->query("
      SELECT
        download_id, subject
      FROM {$db->prefix}downloads",
      array());
    @list($latest_download['id'], $latest_download['subject']) = $db->fetch_row($result);
    
    # Latest download comment
    $result = $db->query("
      SELECT
        comment_id, subject
      FROM {$db->prefix}download_comments",
      array());
    @list($latest_download_comment['id'], $latest_download_comment['subject']) = $db->fetch_row($result);
    
    # Users online
    $result = $db->query("
      SELECT
        COUNT(*)
      FROM {$db->prefix}online",
      array());
    @list($users_online) = $db->fetch_row($result);
    
    # Empty right now, but will soon hopefully be filled up
    $page['top'] = array();
    
    # Top 10 pages
    $result = $db->query("
      SELECT
        page_id, page_title, num_views
      FROM {$db->prefix}pages
      WHERE num_views > 0
      ORDER BY num_views DESC
      LIMIT 10",
      array());
    while($row = $db->fetch_assoc($result))
    {
      # Get current key
      $key = isset($page['top']['pages']) && count($page['top']['pages']);
      $page['top']['pages'][$key]['left'] = '<a href="'. $base_url. '/index.php?page='. $row['page_id']. '">'. $row['page_title']. '</a>';
      $page['top']['pages'][$key]['right'] = numberformat($row['num_views']);
      # Calculate percentage with 'top page = 100%' as base
      $page['top']['pages'][$key]['percent'] = !$page['top']['pages'][0]['right'] ? 100 : $row['num_views'] / $page['top']['pages'][0]['right'] * 100;
    }
    
    # Top 10 members most online
    $result = $db->query("
      SELECT
        member_id, displayName, time_online
      FROM {$db->prefix}members
      WHERE time_online > 0
      ORDER BY time_online DESC
      LIMIT 10",
      array());
    while($row = $db->fetch_assoc($result))
    {
      # Get current key
      $key = isset($page['top']['most_online']) && count($page['top']['most_online']);
      $page['top']['most_online'][$key]['left'] = '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. '">'. $row['displayName']. '</a>';
      
      # Calculate time online
      $weeks = floor($row['time_online'] / 60 / 60 / 24 / 7) * 60 * 60 * 24 * 7;
      $days = floor(($row['time_online'] - $weeks) / 60 / 60 / 24) * 60 * 60 * 24;
      $hours = floor(($row['time_online'] - $weeks - $days) / 60 / 60) * 60 * 60;
      $minutes = floor(($row['time_online'] - $weeks - $days - $hours) / 60) * 60;
      
      # Divide the time blocks into their appropriate amounts
      $weeks /= 60 * 60 * 24 * 7;
      $days /= 60 * 60 * 24;
      $hours /= 60 * 60;
      $minutes /= 60;
      
      # Add text to time online
      $weeks_str = $weeks ? $weeks. sprintf($l['stats_time_online_week'], numberformat($weeks)). ' ' : '';
      $days_str = $days || $weeks ? $days. sprintf($l['stats_time_online_day'], numberformat($days)). ' ' : '';
      $hours_str = $hours || $days || $weeks ? $hours. sprintf($l['stats_time_online_hour'], numberformat($hours)). ' ' : '';
      $minutes_str = $minutes || $hours || $days || $weeks ? $minutes. sprintf($l['stats_time_online_minute'], numberformat($minutes)). ' ' : '';
      
      # Add time online
      $page['top']['most_online'][$key]['time_online'] = $row['time_online'];
      $page['top']['most_online'][$key]['right'] = $weeks_str. $days_str. $hours_str. $minutes_str;
      
      # Calculate percentage with 'most online member = 100%' as base
      $page['top']['most_online'][$key]['percent'] = !$page['top']['most_online'][0]['time_online'] ? 100 : $row['time_online'] / $page['top']['most_online'][0]['time_online'] * 100;
    }
    
    # Top 10 posters
    $result = $db->query("
      SELECT
        member_id, displayName, num_posts
      FROM {$db->prefix}members
      WHERE num_posts > 0
      ORDER BY num_posts DESC
      LIMIT 10",
      array());
    while($row = $db->fetch_assoc($result))
    {
      # Get current key
      $key = isset($page['top']['posters']) && count($page['top']['posters']);
      $page['top']['posters'][$key]['left'] = '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. '">'. $row['displayName']. '</a>';
      $page['top']['posters'][$key]['right'] = numberformat($row['num_posts']);
      # Calculate percentage with 'top poster = 100%' as base
      $page['top']['posters'][$key]['percent'] = !$page['top']['posters'][0]['right'] ? 100 : $row['num_posts'] / $page['top']['posters'][0]['right'] * 100;
    }
    
    # Top 10 topic starters
    $result = $db->query("
      SELECT
        member_id, displayName, num_topics
      FROM {$db->prefix}members
      WHERE num_topics > 0
      ORDER BY num_topics DESC
      LIMIT 10",
      array());
    while($row = $db->fetch_assoc($result))
    {
      # Get current key
      $key = isset($page['top']['topic_starters']) && count($page['top']['topic_starters']);
      $page['top']['topic_starters'][$key]['left'] = '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. '">'. $row['displayName']. '</a>';
      $page['top']['topic_starters'][$key]['right'] = numberformat($row['num_topics']);
      # Calculate percentage with 'top topic starter = 100%' as base
      $page['top']['topic_starters'][$key]['percent'] = !$page['top']['topic_starters'][0]['right'] ? 100 : $row['num_topics'] / $page['top']['topic_starters'][0]['right'] * 100;
    }
    
    # Top 10 topics (By replies)
    $result = $db->query("
      SELECT
        tpc.topic_id, msg.subject, tpc.num_replies
      FROM {$db->prefix}topics AS tpc
      LEFT JOIN {$db->prefix}messages AS msg ON tpc.topic_id = msg.topic_id
      WHERE tpc.num_replies > 0
      ORDER BY tpc.num_replies DESC
      LIMIT 10",
      array());
    while($row = $db->fetch_assoc($result))
    {
      # Get current key
      $key = isset($page['top']['topic_replies']) && count($page['top']['topics_replies']);
      $page['top']['topics_replies'][$key]['left'] = '<a href="'. $base_url. '/forum.php?topic='. $row['yopic_id']. '">'. $row['subject']. '</a>';
      $page['top']['topics_replies'][$key]['right'] = numberformat($row['num_replies']);
      # Calculate percentage with 'top topics = 100%' as base
      $page['top']['topics_replies'][$key]['percent'] = !$page['top']['topics_replies'][0]['right'] ? 100 : $row['num_replies'] / $page['top']['topics_replies'][0]['percent'] * 100;
    }
    
    # Top 10 topics (By views)
    $result = $db->query("
      SELECT
        tpc.topic_id, msg.subject, tpc.num_views
      FROM {$db->prefix}topics AS tpc
      LEFT JOIN {$db->prefix}messages AS msg ON tpc.topic_id = msg.topic_id
      WHERE tpc.num_views > 0
      ORDER BY tpc.num_views DESC
      LIMIT 10",
      array());
    while($row = $db->fetch_assoc($result))
    {
      # Get current key
      $key = isset($page['top']['topic_views']) && count($page['top']['topics_views']);
      $page['top']['topics_views'][$key]['left'] = '<a href="'. $base_url. '/forum.php?topic='. $row['topic_id']. '">'. $row['subject']. '</a>';
      $page['top']['topics_views'][$key]['right'] = numberformat($row['num_views']);
      # Calculate percentage with 'top topics = 100%' as base
      $page['top']['topics_views'][$key]['percent'] = !$page['top']['topics_views'][0]['right'] ? 100 : $row['num_views'] / $page['top']['topics_views'][0]['right'] * 100;
    }
    
    # Top 10 boards
    $result = $db->query("
      SELECT
        board_id, board_name, num_posts
      FROM {$db->prefix}boards
      WHERE num_posts > 0
      ORDER BY num_posts DESC
      LIMIT 10",
      array());
    while($row = $db->fetch_assoc($result))
    {
      # Get current key
      $key = isset($page['top']['boards']) && count($page['top']['boards']);
      $page['top']['boards'][$key]['left'] = '<a href="'. $base_url. '/forum.php?board='. $row['board_id']. '">'. $row['board_name']. '</a>';
      $page['top']['boards'][$key]['right'] = numberformat($row['num_posts']);
      # Calculate percentage with 'top boards = 100%' as base
      $page['top']['boards'][$key]['percent'] = !$page['top']['boards'][0]['right'] ? 100 : $row['num_posts'] / $page['top']['boards'][0]['right'] * 100;
    }
    
    # Days online
    $days_online = floor((time_utc() - $settings['install_time']) / (60 * 60 * 24));
    
    # Now let's put it all in $page['stats']
    $page['stats'] = array(
      'totals' => array(
                    'members' => numberformat($settings['total_members']),
                    'pages' => numberformat($settings['total_pages']),
                    'boards' => numberformat($settings['total_boards']),
                    'topics' => numberformat($settings['total_topics']),
                    'posts' => numberformat($settings['total_posts']),
                    'news' => numberformat($settings['total_news']),
                    'news_comments' => numberformat($settings['total_news_comments']),
                    'downloads' => numberformat($settings['total_downloads']),
                    'downloads_hits' => numberformat($settings['total_downloads_hits']),
                    'downloads_comments' => numberformat($settings['total_downloads_comments']),
                    # One is added to count for this page load, because it isn't normally counted until the theme is loaded
                    'views' => numberformat($settings['total_page_views'] + 1),
                  ),
      'averages' => array(
                    'members' => numberformat($days_online ? $settings['total_members'] / $days_online : 0, 2),
                    'pages' => numberformat($days_online ? $settings['total_pages'] / $days_online : 0, 2),
                    'topics' => numberformat($days_online ? $settings['total_topics'] / $days_online : 0, 2),
                    'posts' => numberformat($days_online ? $settings['total_posts'] / $days_online : 0, 2),
                    'posts_member' => numberformat($settings['total_members'] ? $settings['total_posts'] / $settings['total_members'] : 0, 2),
                    'posts_topic' => numberformat($settings['total_topics'] ? $settings['total_posts'] / $settings['total_topics'] : 0, 2),
                    'news' => numberformat($days_online ? $settings['total_news'] / $days_online : 0, 2),
                    'news_comments' => numberformat($days_online ? $settings['total_news_comments'] / $days_online : 0, 2),
                    'downloads' => numberformat($days_online ? $settings['total_downloads'] / $days_online : 0, 2),
                    'downloads_hits' => numberformat($days_online ? $settings['total_downloads_hits'] / $days_online : 0, 2),
                    'downloads_comments' => numberformat($days_online ? $settings['total_downloads_comments'] / $days_online : 0, 2),
                    'views' => numberformat($days_online ? $settings['total_page_views'] / $days_online : 0, 2),
                  ),
      'users' => array(
                    'now' => numberformat($users_online),
                    'today' => numberformat($settings['most_online_today']),
                    'ever' => numberformat($settings['most_online_ever']),
                    'posted' => numberformat($settings['total_members'] ? $settings['members_posted'] / $settings['total_members'] * 100 : 0). $l['percent'],
                    'commented' => numberformat($settings['total_members'] ? $settings['members_commented'] / $settings['total_members'] * 100 : 0). $l['percent'],
                    'gender_ratio' => sprintf($l['ratio'], numberformat(stats_lcm($gender[2], $gender[1])), numberformat(stats_lcm($gender[1], $gender[2]))),
                  ),
      'latest' => array(
                    'member' => '<a href="'. $base_url. '/index.php?action=profile;u='. $latest_member['id']. '">'. $latest_member['name']. '</a>',
                    'page' => '<a href="'. $base_url. '/index.php?page='. $latest_page['id']. '">'. $latest_page['title']. '</a>',
                    'topic' => '<a href="'. $base_url. '/forum.php?topic='. $latest_topic['id']. '">'. $latest_topic['subject']. '</a>',
                    'post' => '<a href="'. $base_url. '/forum.php?msg='. $latest_post['id']. '">'. $latest_post['subject']. '</a>',
                    'news' => '<a href="'. $base_url. '/index.php?news='. $latest_news['id']. '">'. $latest_news['subject']. '</a>',
                    'news_comment' => '<a href="'. $base_url. '/index.php?comment='. $latest_news_comment['id']. '">'. $latest_news_comment['subject']. '</a>',
                    'download' => '<a href="'. $base_url. '/index.php?download='. $latest_download['id']. '">'. $latest_download['subject']. '</a>',
                    'download_comment' => '<a href="'. $base_url. '/index.php?comment='. $latest_download_comment['id']. '">'. $latest_download_comment['subject']. '</a>',
                  ),
    );
    
    # Cache?
    if($settings['cache_enabled'])
      cache_save('stats_'. $user['numberformat_hash']. '_'. $user['numberformat_hash'], array(
                            'stats' => $page['stats'],
                            'top' => $page['top'],
                          ));
  }
  
  # Title time
  $page['title'] = $l['stats_title'];
  
  # And the theme
  theme_load('stats', 'stats_display_show');
}

function stats_gcd($a, $b)
{
  # Return the greatest common divisor
  return !$b ? $a : stats_gcd($b, $a % $b);
}

function stats_lcm($a, $b)
{
  # Return the lowest common multiple
  $c = stats_gcd($a, $b);
  return $c ? $a / $c : 0;
}
?>