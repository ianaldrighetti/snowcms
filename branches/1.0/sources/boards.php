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
# array boards_load();
#   - This function has no parameters however it returns an array
#   - This array that is returned includes categories with information
#     and in that array is another array of boards in that category
#     NOTE: If no boards were found, the category will be removed.
#   - Be sure that ?board= is set in the url or else the board index
#     will be displayed
#

function boards_load()
{
  global $base_url, $db, $settings, $user;

  # What board..?
  $board_id = !empty($_GET['board']) ? (int)$_GET['board'] : 0;

  # Only categories if the board id is 0
  if($board_id === 0)
  {
    # Category array
    $categories = array();

    # This is the delete array which we will use to help
    # delete categories with no boards.
    $del = array();

    # Now get them out.
    $result = $db->query("
      SELECT
        c.cat_id, c.cat_order, c.cat_name
      FROM {$db->prefix}categories AS c
      ORDER BY c.cat_order ASC", array());

    # Loop through them all, if any
    while($row = $db->fetch_assoc($result))
    {
      # Save it, or what else use is it :)
      $categories[$row['cat_id']] = array(
        'id' => $row['cat_id'],
        'name' => $row['cat_name'],
        'href' => $base_url. '/forum.php#c'. $row['cat_id'],
        'order' => $row['cat_order'],
        'boards' => array()
      );

      # Now add it to the delete array
      $del[$row['cat_id']] = true;
    }
  }
  else
    # Just a boards array :)
    $boards = array();
  
  # Get the boards for use
  $result = $db->query("
    SELECT
      b.board_id, b.cat_id, b.board_order, b.child_of, b.who_view, b.board_name,
      b.board_desc, b.num_topics, b.num_posts, b.last_msg_id, b.last_member_id, b.last_member_name,
      msg.msg_id, msg.member_id AS poster_id, msg.subject, msg.poster_time, msg.modified_time AS poster_time,
      IFNULL(msg.modified_time, msg.poster_time) AS poster_time, msg.topic_id, t.topic_id,
      t.first_msg_id, msg2.msg_id, msg2.subject AS topic_subject, mem.member_id AS mem_id, mem.displayName, mem.loginName,
      bl.board_id AS is_new, bl.member_id AS bl_member, IFNULL(bl.board_id, 0) AS is_new
    FROM {$db->prefix}boards AS b
      LEFT JOIN {$db->prefix}messages AS msg ON msg.msg_id = b.last_msg_id
      LEFT JOIN {$db->prefix}topics AS t ON t.topic_id = msg.topic_id
      LEFT JOIN {$db->prefix}messages AS msg2 ON msg2.msg_id = t.first_msg_id
      LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = b.last_member_id
      LEFT JOIN {$db->prefix}board_logs AS bl ON (bl.board_id = b.board_id AND bl.member_id = %member_id)
    WHERE ". strtr($user['find_in_set'], array('alias' => 'b')). " AND b.child_of = %board_id",
    array(
      'member_id' => array('int', $user['id']),
      'board_id' => array('int', $board_id),
    ));

  # Now loop through the boards
  while($row = $db->fetch_assoc($result))
  {
    # Load it into a temporary array...
    $tmp = array(
      'id' => $row['board_id'],
      'name' => $row['board_name'],
      'description' => $row['board_desc'],
      'href' => $base_url. '/forum.php?board='. $row['board_id'],
      'order' => $row['board_order'],
      'is_new' => (empty($row['is_new']) && $user['is_logged']) || ($user['is_guest'] && ( !isset($_SESSION['board_viewed'][$row['board_id']]) || $_SESSION['board_viewed'][$row['board_id']] < $row['num_posts'])),
      'num' => array(
                 'topics' => $row['num_topics'],
                 'posts' => $row['num_posts'],
               ),
      'last_post' => array(
                       'topic' => array(
                                    'id' => $row['topic_id'],
                                    'subject' => $row['topic_subject'],
                                    'href' => $base_url. '/forum.php?topic='. $row['topic_id'],
                                  ),
                       'msg' => array(
                                  'id' => $row['last_msg_id'],
                                  'subject' => $row['subject'],
                                  'href' => $base_url. '/forum.php?msg='. $row['last_msg_id'],
                                  'time' => timeformat($row['poster_time']),
                                ),
                       'member' => array(
                                     'id' => $row['mem_id'],
                                     'name' => $row['mem_id'] ? $row['displayName'] : $row['last_member_name'],
                                     'username' => $row['mem_id'] ? $row['loginName'] : false,
                                     'href' => $row['mem_id'] ? $base_url. '/index.php?action=profile;u='. $row['mem_id'] : false,
                                   ),
                     ),
    );
    
    # So where are we loading it into?
    if($board_id === 0)
    {
      # Into a category, but only if it is allocated to a category
      if(!empty($categories[$row['cat_id']]) && $row['child_of'] == 0)
        $categories[$row['cat_id']]['boards'][] = $tmp;

      # Doesn't need to be deleted :)
      $del[$row['cat_id']] = false;
    }
    else
      # Just into the boards
      $boards[] = $tmp;
  }
  $db->free_result($result);
  
  # Need to delete the empty categories..?
  if($board_id === 0)
  {
    foreach($del as $cat_id => $needs_del)
      if($needs_del)
        unset($categories[$cat_id]);
    
    # Now return it.
    return $categories;
  }
  else
    # Just return the boards
    return $boards;
}
?>