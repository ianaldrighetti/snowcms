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
# Page loading is (or should be) pointed in this direction
# 
# void page_view([bool $show_home = false[, bool $show_news = false]]);
#   bool $show_home - Set this to true and it will ignore ?page and
#                     load the page from the settings array, default
#                     is false
#   bool $show_news - Set this to true to display news below the page
#                     content.
#   - page_view() handles of course loading pages from the pages table
#     it gets the page id from the url (unless otherwised specificed)
#     and gets the page but of course checks permissions and whether
#     or not the page is viewable :)
#
# void page_home();
#   - Through settings you have, um, set, it determines
#     what you want to do for the home page. Whether it
#     be show a specific page or news
#
# void page_help();
#

function page_view($show_home = false, $show_news = false)
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # Loading home page or not?
  if(!empty($show_home))
    $page_id = (int)$settings['default_page'];
  else
    $page_id = (int)$_GET['page'];
  
  language_load('page');

  # Start our array, for later use.
  $page['page'] = array();
  
  # Check the cache, it might be there...
  if(($cache = cache_get('page_id-'. $page_id)) != null)
  {
    # Okay, its cached, so save it to our array of usefulness :D
    $page['page'] = $cache;
  }
  else
  {
    # I guess its not in the cache, or we don't want caching on :P
    $result = $db->query("
      SELECT
        p.page_id, p.member_id AS creator_id, p.member_name, p.modified_member_id, p.modified_name,
        p.created_time, p.modified_time, p.page_title, p.content, p.type, p.is_viewable, p.num_views, 
        p.who_view, mem.member_id, mem.displayName, mem2.member_id AS modifier_id, mem2.displayName AS modifier_name
      FROM {$db->prefix}pages AS p
        LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = p.member_id
        LEFT JOIN {$db->prefix}members AS mem2 ON mem2.member_id = p.modified_member_id
      WHERE p.page_id = %page_id
      LIMIT 1",
      array(
        'page_id' => array('int', $page_id)
      ));

    # So did we find anything? ^^
    if($db->num_rows($result))
    {
      # First let's get the row
      $row = $db->fetch_assoc($result);
      # Now lets build the array. We haven't check if you can view it,
      # but we will later ;)
      $page['page'] = array(
        'id' => $row['page_id'],
        'title' => $row['page_title'],
        'creator' => array(
                       'id' => $row['creator_id'],
                       'name' => !empty($row['displayName']) ? $row['displayName'] : $row['member_name'],
                     ),
        'modifier' => array(
                        'id' => $row['modifier_id'],
                        'name' => !empty($row['modifier_name']) ? $row['modifier_name'] : $row['modified_name'],
                      ),
        'created_time' => timeformat($row['created_time']),
        'modified_time' => !empty($row['modified_time']) ? timeformat($row['modified_time']) : false,
        'content' => $row['type'] == 2 ? snowtext(
                                           $row['content'],
                                           $row['page_title'],
                                           $row['num_views'],
                                           array(
                                             'id' => $row['creator_id'],
                                             'name' => !empty($row['displayName']) ? $row['displayName'] : $row['member_name'],
                                           ),
                                           $row['created_time'],
                                           timeformat($row['modified_time'])
                                         )
                     : ($row['type'] ? $row['content']
                     : bbc($row['content'], true, 'page_content_id-'. $page_id)),
        'type' => !empty($row['type']),
        'is_viewable' => !empty($row['is_viewable']),
        'num_views' => $row['num_views'],
        'who_view' => $row['who_view'],
      );

      # So cache it? Whether or not, do it. Lol.
      cache_save('page_id-'. $page_id, $page['page'], 120);
    }
  }

  # Add something... ;) Can't cache it!
  if(count($page['page']))
    $page['page']['can'] = array(
      'edit' => (can('moderate_pages') || $user['is_admin'] || (can('edit_own_pages') && $row['creator_id'] == $user['id']) || can('edit_pages')) ? true : false,
      'delete' => (can('moderate_pages') || $user['is_admin'] || (can('delete_own_pages') && $row['creator_id'] == $user['id']) || can('delete_pages')) ? true : false,
    );

  # So can you access this, or does it exist..?
  if(!empty($page['page']['who_view']) && (((in_array($user['group']['id'], explode(',', $page['page']['who_view'])) || in_array($user['post_group']['id'], explode(',', $page['page']['who_view']))) && $page['page']['is_viewable']) || can('moderate_pages')))
  {
    # Woo! They can access it :D
    # So set the page title.
    $page['title'] = $page['page']['title']. ' - '. $settings['site_name'];

    # Create the meta descriptions and such?
    if($settings['create_meta'])
    {
      # We need this :P
      require_once($source_dir. '/metadata.php');
      $page['meta']['description'] = metadata_description($page['page']['content']);
      $page['meta']['keywords'] = metadata_keywords($page['page']['content'], false, 'page_keywords_id-'. $page['page']['id']);
    }

    # Increment the number of views?
    if(!isset($_SESSION['last_page_id']) || $_SESSION['last_page_id'] != $page_id)
    {
      # ya, we need to, do it!
      $db->query("
        UPDATE {$db->prefix}pages
        SET num_views = num_views + 1
        WHERE page_id = %page_id
        LIMIT 1",
        array(
          'page_id' => array('int', $page_id),
        ));

      # Set the new last page viewed.
      $_SESSION['last_page_id'] = $page_id;
    }
    
    # Are we suppose to be displaying the news too?
    if($show_news)
    {
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
        LIMIT %news_per_page",
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
      
      # Load the theme
      theme_load('pages', 'pages_view_show_news');
    }
    # Okay, so no news, just the page
    else
    {
      # Can't think of anything else :D
      theme_load('pages', 'pages_view_show');
    }
  }
  else
  {
    # :| Nothing, or at least you can't view it :P
    $page['title'] = $l['page_error_title'];

    # Don't index this robot :P
    $page['no_index'] = true;

    # Now load the layout ;)
    theme_load('pages', 'pages_view_show_error');
  }
}

function page_home()
{
  global $l, $page, $settings, $source_dir, $user;

  # So what..?
  # 0 = A Page
  # 1 = News
  # Nothing else assigned, but you can add more :P
  if($settings['homepage'] == 0)
    # Yes, override Page ID to be the home page ;)
    page_view(true);
  elseif($settings['homepage'] == 1)
    # Show the homepage and add the news
    page_view(true, true);
  elseif($settings['homepage'] == 2)
  {
    # Show the news
    require_once($source_dir. '/news.php');
    news_list();
  }
  else
    # Nothing? O_O
    die($l['no_homepage']);
}

function page_help()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $theme_url, $user;

  # Load the right language file...
  language_load('help');

  # Variable..?
  $var = !empty($_GET['var']) ? (string)'_'. $_GET['var'] : '';

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>', !empty($l['popup'. $var. '_title']) ? $l['popup'. $var. '_title'] : $l['popup_title'], '</title>
	<link rel="stylesheet" type="text/css" href="', $theme_url, '/', $settings['theme'], '/style.css" />
</head>
<body class="help_bg">
<div class="help_text">
  ', strtr(!empty($l['popup'. $var. '_desc']) ? $l['popup'. $var. '_desc'] : $l['popup_invalid'], array("\n" => '<br />')), '
  <br /><br />
  <p style="text-align: center;"><a href="javascript:window.close();">', $l['popup_close'], '</a></p>
</div>
</body>
</html>';
}
?>