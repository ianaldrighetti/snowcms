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
# Handle the displaying of topics.
#
# void recent_posts();
#

function recent_posts()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # You allowed to view the forum..?
  error_screen('view_forum');

  # Speak my language! Or Perish! :D!
  language_load('forum');

  # Where are we starting..?
  $start = !empty($_GET['page']) ? (int)$_GET['page'] : 1;

  # Don't index this if we are on page 1 =| It shouldn't have ;page=1 in it!
  if(!empty($_GET['page']) && $start == 1)
    $page['no_index'] = true;

  # Create our pagination. The nation of pages, lol... (We only want 10 pages of posts at the most!)
  $page['index'] = pagination_create($base_url. '/forum.php?action=recentposts', $start, min($settings['total_posts'], $settings['posts_per_page'] * 10), $settings['posts_per_page']);

  # Get the posts we want to list :)
  $result = $db->query("
    SELECT
      msg.msg_id, msg.topic_id, msg.board_id, msg.member_id, msg.modified_member_id,
      msg.modified_name, msg.modified_reason, msg.modified_time, msg.subject, msg.poster_time,
      msg.poster_name, msg.poster_email, msg.poster_ip, msg.body, msg.parse_bbc,
      msg.parse_smileys, msg.is_locked
    FROM {$db->prefix}messages AS msg
    ORDER BY msg.msg_id DESC
    LIMIT %start,%per_page",
    array(
      'topic_id' => array('int', $page['topic']['id']),
      'start' => array('int', $start),
      'per_page' => array('int', $user['per_page']['posts']),
    ));

  # Get members loaded...
  $members = array();
  $modified_members = array();
  while($row = $db->fetch_assoc($result))
  {
    $members[] = $row['member_id'];

    if(!empty($row['modified_member_id']))
      $modified_members[] = $row['modified_member_id'];
  }

  # Move that pointer back! NOW!
  $db->data_seek($result, 0);

  # Load lots of information about the members who posted it...
  if(!function_exists('members_load'))
    require_once($source_dir. '/members.php');
  members_load($members, 'extended');

  # And a little bit about those who modified the post :P
  if(count($modified_members))
    members_load($modified_members, 'basic');

  # Let's not remake something that is already made! :P
  $page['messages_result'] = $result;
  unset($result);

  # Get the message loader...
  require_once($source_dir. '/topic.php');
  $page['message_callback'] = 'topic_load_messages';

  # Load the page now ;)
  $page['title'] = $l['recent_posts_title'];

  theme_load('recent', 'recent_posts_show');
}
?>