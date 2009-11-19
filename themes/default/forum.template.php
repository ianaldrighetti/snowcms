<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Errors Layout template, February 1, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function forum_index_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['forum_header'], '</h1>
      <br />
      
      <table class="forumindex" width="100%" cellpadding="4px" cellspacing="0px">';
  if(count($page['categories']))
  {
    foreach($page['categories'] as $category)
    {
      echo '
        <a name="c', $category['id'], '"></a>
        <tr class="category_header">
          <th colspan="4"><a href="', $category['href'], '">', $category['name'], '</a></th>
        </tr>';

      foreach($category['boards'] as $board)
      {
        echo '
        <tr class="board">
          <td width="5%" align="center" valign="middle"><img src="', $user['images_url'], '/', $board['is_new'] ? 'board_new.png' : 'board_old.png', '" alt="', $board['is_new'] ? $l['forum_board_new'] : $l['forum_board_old'], '" title="', $board['is_new'] ? $l['forum_board_new'] : $l['forum_board_old'], '"/></td>
          <td align="left" valign="middle" width="50%"><a href="', $board['href'], '">', $board['name'], '</a><br />
              ', $board['description'], '
          </td>
          <td align="center" valign="middle" width="10%">', numberformat($board['num']['posts']), ' ', $l['posts'], ' ', $l['in'], '<br />
                          ', numberformat($board['num']['topics']), ' ', $l['topics'], '
          </td>
          <td align="left" valign="middle" width="35%">', !empty($board['last_post']['msg']['id']) ? $l['last_post_by']. ' <a href="'. $board['last_post']['member']['href']. '">'. $board['last_post']['member']['name']. '</a><br />'. $l['in']. ' <a href="'. $board['last_post']['msg']['href']. '" title="'. $l['posted_at']. ' '. $board['last_post']['msg']['time']. '">'. $board['last_post']['msg']['subject']. '</a>' : $l['forum_board_no_posts'], '</td>
        </tr>';
      }
    }
  }
  else
    echo '
        <tr class="generic_error">
          <td colspan="2" class="center">', $l['forum_no_boards'], '</td>
        </tr>';
  echo '
      </table>

      <table class="forumstats" width="100%" cellpadding="4px" cellspacing="0px">
        <tr>
          <th colspan="2">', $settings['site_name'], ' - Forum information</th>
        </tr>';

  # Show recent posts..?
  if(!empty($settings['forum_recent_posts']))
  {
    echo '
        <tr>
          <th class="sub" colspan="2">Recent posts</th>
        </tr>
        <tr>
          <td class="center" valign="middle" width="5%"><a href="'. $base_url. '/forum.php?action=recent" title="View more recent posts"><img src="'. $user['images_url']. '/topic/topic.png" alt="" title="View more recent posts" /></a></td>
          <td>';

    if(count($page['recent_posts']))
    {
      echo '
            <table class="center recentposts" width="100%" cellpadding="4px" cellspacing="0px">';

      foreach($page['recent_posts'] as $post)
      {
        echo '
              <tr>
                <td class="right" width="25%">[', $post['board']['link'], ']</td>
                <td class="left" width="50%">', $post['link'], !empty($post['is_new']) ? ' <img src="'. $user['images_url']. '/topic/new.png" alt="" title="'. $l['new']. '" />' : '', ' ', $l['by'], ' ', $post['poster']['link'], '</td>
                <td class="right" width="25%">', $post['date'], '</td>
              </tr>';
      }

      echo '
            </table>';
    }
    else
      echo '<span class="center">No recent posts to display.</span>';

    echo '
          </td>
        </tr>';
  }

  echo '
        <tr>
          <th class="sub" colspan="2">Statistics</th>
        </tr>
        <tr>
          <td class="center" valign="middle" width="5%"><a href="'. $base_url. '/index.php?action=stats" title="More statistics"><img src="'. $user['images_url']. '/information.png" alt="" title="More statistics" /></a></td>
          <td>
            <table class="center" width="100%" cellspacing="4px" cellpadding="0px">
              <tr>
                <td>Total topics: ', $page['stats']['topics'], '</td><td>Total posts: ', $page['stats']['posts'], '</td><td>Total members: ', $page['stats']['members'], '</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>';
}

function forum_board_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $page['board']['name'], '</h1>

      <table width="100%" class="board_tree" cellpadding="5px" cellspacing="0px">
        <tr>
          <td colspan="2"><a href="', $base_url, '/forum.php">', $l['forum_header'], '</a> &gt; <a href="', $base_url, '/forum.php?board=', $page['board']['id'], '">', $page['board']['name'], '</a></td>
        </tr>
      </table>
      <table width="100%" class="board_options" cellpadding="6px" cellspacing="0px">
        <tr>
          <td align="left">', $page['index'], '</td><td align="right">', forum_board_menu(), '</td>
        </tr>
      </table>
      <table width="100%" class="topic_listing" cellpadding="4px" cellspacing="0px">
        <tr>
          <th width="5%">&nbsp;</th>
          <th width="40%"><a href="', $page['sort_urls']['subject'], '">Subject ', $page['sort_icon']['subject'], '</a></th>
          <th class="center" width="10%"><a href="', $page['sort_urls']['replies'], '">Replies ', $page['sort_icon']['replies'], '</a></th>
          <th class="center" width="10%"><a href="', $page['sort_urls']['views'], '">Views ', $page['sort_icon']['views'], '</a></th>
          <th width="25%"><a href="', $page['sort_urls']['last_post'], '">Last post ', $page['sort_icon']['last_post'], '</a></th>
        </tr>';

  # List all the topics... If any ;)
  if(count($page['topics']))
    foreach($page['topics'] as $topic)
    {
      echo '
        <tr>
          <td><img src="', $user['images_url'], '/topic/', $topic['icon'], '" alt="" title="', !empty($topic['you_posted']) ? $l['you_posted'] : '', '" /></td>
          <td><a href="', $topic['href'], '">', $topic['subject'], '</a>', !empty($topic['is_new']) ? ' <a href="'. $topic['last_post']['href']. '" title="'. $l['new']. '"><img src="'. $settings['images_url']. '/topic/new.png" alt="'. $l['new']. '" title="'. $l['new']. '" /></a>' : '', '<br /><span class="small">Posted by ', $topic['poster']['link'], '</span></td>
          <td class="center">', numberformat($topic['num']['replies']), '</td>
          <td class="center">', numberformat($topic['num']['views']), '</td>
          <td><a href="', $topic['last_post']['href'], '" title="', $l['last_post'], '"><img src="', $settings['images_url'], '/topic/last_post.png" alt="" title="', $l['last_post'], '" style="float: right;" /></a> <span class="small">', $topic['last_post']['time'], '<br />', $l['by'], ' ', $topic['last_post']['poster']['link'], '</span></td>
        </tr>';
    }
  else
    echo '
        <tr>
          <td colspan="5" align="center">No topics</td>
        </tr>';

  echo '
      </table>';

}

function forum_board_menu()
{
  global $base_url, $l, $page;

  $menu = array();
  if(!empty($page['can']['post_topic']))
    $menu[] = '<a href="'. $base_url. '/forum.php?action=post;board='. $page['board']['id']. '" title="'. $l['forum_post_topic_title']. '">'. $l['forum_post_topic']. '</a>';
  if(!empty($page['can']['post_poll']))
    $menu[] = '<a href="'. $base_url. '/forum.php?action=post;board='. $page['board']['id']. ';poll" title="'. $l['forum_post_poll_title']. '">'. $l['forum_post_poll']. '</a>';

  return implode(' | ', $menu);
}
?>