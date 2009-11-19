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
# Forum.php handles loading the forum index and the specific boards :)
#
# void forum_index();
#   - This function has no parameters and returns nothing... All it does
#     is load the board index of the forum
#
# void forum_board();
#   - When ?board= is in the url it will load that specific board.
#
# string forum_topic_icon(array $row);
#   array $row - Should contain columns from the topic, such as you_posted, num_replies,
#                poll_id, and so on.
#
#   returns string - This will return an icon name that goes with what was recieved.
#

function forum_index()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # Are they allowed to view the forum in general?
  error_screen('view_forum');

  # The forum language would be nice...
  language_load('forum');

  # Okay, we need boards.php which has the function
  # we need to have to load the boards.
  require_once($source_dir. '/boards.php');

  # Now load them with this awesome function
  $page['categories'] = boards_load();

  # Recent posts, perhaps?
  if(!empty($settings['forum_recent_posts']) && $settings['forum_recent_posts'] > 0)
  {
    $result = $db->query("
      SELECT
        msg.msg_id, msg.topic_id, msg.board_id, msg.member_id, msg.subject, msg.poster_time, msg.poster_name,
        b.board_id, b.who_view, b.board_name, tl.topic_id, tl.member_id AS is_new, IFNULL(tl.member_id, 0) AS is_new,
        mem.member_id, mem.displayName
      FROM {$db->prefix}messages AS msg
        LEFT JOIN {$db->prefix}boards AS b ON b.board_id = msg.board_id
        LEFT JOIN {$db->prefix}topic_logs AS tl ON tl.topic_id = msg.topic_id
        LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = msg.member_id
      WHERE ". strtr($user['find_in_set'], array('alias' => 'b')). "
      ORDER BY msg.msg_id DESC
      LIMIT %limit",
      array(
        'limit' => array('int', $settings['forum_recent_posts']),
      ));

    $page['recent_posts'] = array();
    while($row = $db->fetch_assoc($result))
    {
      $page['recent_posts'][] = array(
        'id' => $row['msg_id'],
        'topic' => $row['topic_id'],
        'board' => array(
                     'id' => $row['board_id'],
                     'name' => $row['board_name'],
                     'href' => $base_url. '/forum.php?board='. $row['board_id'],
                     'link' => '<a href="'. $base_url. '/forum.php?board='. $row['board_id']. '" title="'. $row['board_name']. '">'. $row['board_name']. '</a>',
                   ),
        'subject' => $row['subject'],
        'href' => $base_url. '/forum.php?msg='. $row['msg_id'],
        'link' => '<a href="'. $base_url. '/forum.php?msg='. $row['msg_id']. '" title="'. $row['subject']. '">'. $row['subject']. '</a>',
        'time' => $row['poster_time'],
        'date' => timeformat($row['poster_time']),
        'is_new' => empty($row['is_new']),
        'poster' => array(
                      'id' => $row['member_id'] ? $row['member_id'] : 0,
                      'name' => $row['member_id'] ? $row['displayName'] : $row['poster_name'],
                      'href' => $row['member_id'] ? $base_url. '/index.php?action=profile;u='. $row['member_id'] : false,
                      'link' => $row['member_id'] ? '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. '">'. $row['displayName']. '</a>' : $row['poster_name'],
                    ),
      );
    }
  }

  # Load some statistics :)
  $page['stats'] = array(
    'topics' => numberformat($settings['total_topics']),
    'posts' => numberformat($settings['total_posts']),
    'members' => numberformat($settings['total_members']),
  );

  # Set the page title and that about does it.
  $page['title'] = $l['forum_title'];

  theme_load('forum', 'forum_index_show');
}

function forum_board()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # Gotta check if they can just overall view the forum here..
  error_screen('view_forum');

  # Forum Language
  language_load('forum');

  # Just get the board ID.
  $board_id = !empty($_GET['board']) ? (int)$_GET['board'] : 0;

  # Before we do anything special... like load child boards perhaps
  # lets see if they are allowed to even have access to this board
  # or if it exists XD
  $result = $db->query("
    SELECT
      b.board_id AS id, b.who_view, b.board_name AS name, b.board_desc AS description, b.num_posts
    FROM {$db->prefix}boards AS b
    WHERE ". strtr($user['find_in_set'], array('alias' => 'b')) . " AND b.board_id = %board_id
    LIMIT 1",
  array(
    'board_id' => array('int', $board_id),
  ));

  # If we have a row then we found it :)
  if($db->num_rows($result))
  {
    # So this board does exist now we can continue.
    $page['board'] = $db->fetch_assoc($result);

    # Lets set the title and description.
    $page['title'] = sprintf($l['forum_board_title'], $page['board']['name']);

    # Strip the tags just incase.
    $page['meta_description'] = mb_substr(strip_tags($page['board']['description']), 0, 255);

    # Now lets see about child boards
    require_once($source_dir. '/boards.php');
    $page['boards'] = boards_load();

    # Mark this board as read now, just incase :)
    if($user['is_logged'])
    {
      # Only if the user is logged in of course.
      $db->insert('replace', $db->prefix. 'board_logs',
        array(
          'board_id' => 'int', 'member_id' => 'int'
        ),
        array(
          $board_id, $user['id']
        ),
        array('board_id', 'member_id'));

      # Hmm, moderators? Admins have all powers, so don't check them!
      if(!$user['is_admin'])
      {
        # Lets see, are you one?
        $result = $db->query("
          SELECT
            board_id, member_id
          FROM {$db->prefix}moderators
          WHERE board_id = %board_id AND member_id = %member_id",
          array(
            'board_id' => array('int', $board_id),
            'member_id' => array('int', $user['id'])
          ));

        # Anything? You are a moderator then :D
        if($db->num_rows($result))
          $user['is_moderator'] = true;
      }
    }
    elseif($user['is_guest'])
      $_SESSION['board_viewed'][$board_id] = $page['board']['num_posts'];

    # Still some more stuff to do, like loading the topics!
    # But we also need to make the index thing :)
    # To make the pagination we need the number of topics.
    $result = $db->query("
      SELECT
        COUNT(*)
      FROM {$db->prefix}topics
      WHERE board_id = %board_id",
      array(
        'board_id' => array('int', $board_id)
      ));
    @list($num_topics) = $db->fetch_row($result);

    # Sorts perhaps..?
    # First the default...
    $topic_sort = 't.is_sticky DESC, last_post_time DESC';
    $page['sort'] = 'last_post';
    $page['sorting'] = 'desc';
    $page['sort_url'] = '';

    $sorts = array(
      'subject' => 'msg.subject',
      'replies' => 't.num_replies',
      'views' => 't.num_views',
      'last_post' => 'last_post_time',
    );

    # So are you sorting...?
    if(!empty($_GET['sort']) && isset($sorts[$_GET['sort']]))
    {
      # So good. The sort exists, lets do it XD!
      $topic_sort = $sorts[$_GET['sort']]. ' '. (isset($_GET['desc']) ? 'DESC' : 'ASC');

      $page['sort'] = htmlspecialchars($_GET['sort'], ENT_QUOTES, 'UTF-8');
      $page['sorting'] = isset($_GET['desc']) ? 'desc' : 'asc';

      $page['sort_url'] = ';sort='. $page['sort']. ($page['sorting'] == 'desc' ? ';desc' : '');
    }

    # We need an array with sorting things ;) Icons too!
    $page['sort_urls'] = array();
    $page['sort_icon'] = array();
    foreach($sorts as $sort => $colName)
    {
      # Current sort?
      if($page['sort'] == $sort)
      {
        # Add it... But check something ;)
        $page['sort_urls'][$sort] = $base_url. '/forum.php?board='. $page['board']['id']. ';sort='. $sort. (isset($_GET['desc']) ? '' : ';desc');
        $page['sort_icon'][$sort] = '<img src="'. $settings['images_url']. '/'. (isset($_GET['desc']) ? 'sort_desc.png' : 'sort_asc.png'). '" alt="" title="" />';
      }
      else
      {
        # Just a regular sort :D
        $page['sort_urls'][$sort] = $base_url. '/forum.php?board='. $page['board']['id']. ';sort='. $sort;
        $page['sort_icon'][$sort] = '';
      }
    }

    # Now make the pagination and get the right page number :)
    $page['index'] = pagination_create($base_url. '?board='. $board_id. $page['sort_url'], $start, $num_topics, $user['per_page']['topics']);

    # Now get them... BIG BOY! :D
    $result = $db->query("
      SELECT
        t.topic_id, t.is_sticky, t.is_locked, t.board_id, t.poll_id, t.first_msg_id,
        t.last_msg_id, t.starter_member_id, t.starter_member_name, t.last_member_id,
        t.last_member_name, t.num_replies, t.num_views, tl.topic_id AS is_new, tl.member_id AS tl_id,
        msg.msg_id, msg.subject, msg.poster_time, msg2.msg_id AS msg_id2,
        msg2.subject AS subject2, msg2.poster_time AS last_post_time, mem.member_id,
        mem.displayName AS starter_name, mem2.member_id AS member_id2, mem2.displayName AS last_name,
        IFNULL(msg2.poster_time, msg.poster_time) AS last_post_time, ml.topic_id AS you_posted
      FROM {$db->prefix}topics AS t
        LEFT JOIN {$db->prefix}messages AS msg ON msg.msg_id = t.first_msg_id
        LEFT JOIN {$db->prefix}messages AS msg2 ON msg2.msg_id = t.last_msg_id
        LEFT JOIN {$db->prefix}topic_logs AS tl ON (tl.topic_id = t.topic_id AND tl.member_id = %member_id)
        LEFT JOIN {$db->prefix}message_logs AS ml ON (ml.member_id = %member_id AND ml.topic_id = t.topic_id)
        LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = t.starter_member_id
        LEFT JOIN {$db->prefix}members AS mem2 ON mem2.member_id = t.last_member_id
      WHERE t.board_id = %board_id
      GROUP BY t.topic_id
      ORDER BY %sort
      LIMIT %start, %topics_per_page",
      array(
        'member_id' => array('int', $user['id']),
        'board_id' => array('int', $board_id),
        'sort' => array('raw', $topic_sort),
        'start' => array('int', $start),
        'topics_per_page' => array('int', $user['per_page']['topics']),
      ));

    # Now we need to loop through all the topics... and save them :)
    $page['topics'] = array();
    while($row = $db->fetch_assoc($result))
    {
      $page['topics'][] = array(
        'id' => $row['topic_id'],
        'msg_id' => $row['msg_id'],
        'icon' => forum_topic_icon($row),
        'subject' => $row['subject'],
        'time' => timeformat($row['poster_time']),
        'href' => $base_url. '/forum.php?topic='. $row['topic_id'],
        'poster' => array(
                      'id' => $row['member_id'] ? $row['member_id'] : false,
                      'name' => $row['starter_name'] ? $row['starter_name'] : $row['starter_member_name'],
                      'href' => $row['member_id'] ? $base_url. '/index.php?action=profile;u='. $row['member_id'] : false,
                      'link' => $row['member_id'] ? '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. '">'. $row['starter_name']. '</a>' : $row['starter_member_name'],
                    ),
        'last_post' => array(
                         'id' => $row['msg_id2'] ? $row['msg_id2'] : $row['msg_id'],
                         'subject' => $row['subject2'] ? $row['subject2'] : $row['subject'],
                         'time' => timeformat($row['last_post_time']),
                         'href' => $base_url. '/forum.php?msg='. ($row['msg_id2'] ? $row['msg_id2'] : $row['msg_id']),
                         'poster' => array(
                                       'id' => $row['member_id2'] ? $row['member_id2'] : ($row['member_id'] ? $row['member_id'] : false),
                                       'name' => $row['last_name'] ? $row['last_name'] : ($row['starter_name'] ? $row['starter_name'] :$row['last_member_name']),
                                       'href' => $row['member_id2'] ? $base_url. '/index.php?action=profile;u='. $row['member_id2'] : ($row['member_id'] ? $base_url. '/index.php?action=profile;u='. $row['member_id'] : false),
                                       'link' => $row['member_id2'] ? '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id2']. '">'. $row['last_name']. '</a>' : ($row['member_id'] ? '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. '">'. $row['starter_name']. '</a>' : $row['last_member_name']),
                                     ),
                       ),
        'num' => array(
                   'views' => $row['num_views'],
                   'replies' => $row['num_replies']
                 ),
        'is_new' => (empty($row['is_new']) && $user['is_logged']) || ($user['is_guest'] && (!isset($_SESSION['topic_viewed'][$row['topic_id']]) || (int)$_SESSION['topic_viewed'][$row['topic_id']] < $row['num_replies'])),
        'is_sticky' => !empty($row['is_sticky']),
        'is_locked' => !empty($row['is_locked']),
        'has_poll' => !empty($row['poll_id']),
        'you_posted' => !empty($row['you_posted']),
        'can' => array(
                   'move' => can('move_any_topic') || can('move_any_topic', $board_id) || ((can('move_own_topic') || can('move_own_topic', $board_id)) && $row['member_id'] == $user['id']) || $user['is_moderator'],
                   'delete' => can('delete_any_topic') || can('delete_any_topic', $board_id) || ((can('delete_own_topic') || can('delete_own_topic', $board_id)) && $row['member_id'] == $user['id']) || $user['is_moderator'],
                   'lock' => can('lock_any_topic') || can('lock_any_topic', $board_id) || ((can('lock_own_topic') || can('sticky_own_topic', $board_id)) && $row['member_id'] == $user['id']) || $user['is_moderator'],
                   'sticky' => can('sticky_any_topic') || can('lock_any_topic', $board_id) || ((can('sticky_own_topic') || can('sticky_own_topic', $board_id)) && $row['member_id'] == $user['id']) || $user['is_moderator'],
                   'merge' => can('merge_any_topic') || can('merge_any_topic', $board_id) || ((can('merge_own_topic') || can('merge_own_topic', $board_id)) && $row['member_id'] == $user['id']) || $user['is_moderator'],
                 ),
      );
    }

    # Now what can you do..?
    $page['can'] = array(
      'post_topic' => can('post_topic') || can('post_topic', $page['board']['id']) || $user['is_moderator'],
      'post_poll' => can('post_poll') || can('post_poll', $page['board']['id']) || $user['is_moderator'],
    );

    # Now load the theme.
    theme_load('forum', 'forum_board_show');
  }
  else
  {
    # Error XD!
    $page['title'] = $l['forum_error_title'];

    # No indexing ;)
    $page['no_index'] = true;

    # Now load the theme.
    theme_load('forum', 'forum_board_show_invalid');
  }
}

function forum_topic_icon($row)
{
  # Lets start out with the beginnings. If you posted in it, it will begin with my_
  $icon = !empty($row['you_posted']) ? 'my_' : '';

  # Is it a poll?
  if(!empty($row['poll_id']))
    $icon .= 'poll.png';
  # Locked and sticky?
  elseif(!empty($row['is_locked']) && !empty($row['is_sticky']))
    $icon .= 'locked_sticky.png';
  # Just locked?
  elseif(!empty($row['is_locked']))
    $icon .= 'locked.png';
  # Just a sticky?
  elseif(!empty($row['is_sticky']))
    $icon .= 'sticky.png';
  # Just a topic... :|
  else
    $icon .= 'topic.png';

  return $icon;
}
?>