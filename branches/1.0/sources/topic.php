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
# void topic_load();
#
# void topic_redirect();
#

function topic_load()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # You allowed to view the forum..?
  error_screen('view_forum');

  # Speak my language! Or Perish! :D!
  language_load('forum');

  # We will need these...
  $page['scripts'][] = $base_url. '/Themes/default/js/editor.js';
  $page['scripts'][] = $base_url. '/Themes/default/js/post.js';

  # Lets load up the topic. If you can see it!
  $result = $db->query("
    SELECT
      t.topic_id, t.is_sticky, t.is_locked, t.board_id, t.poll_id, t.first_msg_id, t.num_replies, t.num_views,
      b.board_id, b.cat_id, b.board_name, b.who_view, c.cat_id, c.cat_name, msg.msg_id, msg.subject, msg.body, 
      msg.member_id, p.poll_id, p.question, p.closed, p.allowed_votes, p.expires, p.allow_change, p.voters, p.result_access
    FROM {$db->prefix}topics AS t
      LEFT JOIN {$db->prefix}messages AS msg ON t.first_msg_id = msg.msg_id
      LEFT JOIN {$db->prefix}boards AS b ON t.board_id = b.board_id
      LEFT JOIN {$db->prefix}categories AS c ON b.cat_id = c.cat_id
      LEFT JOIN {$db->prefix}topic_polls AS p ON t.poll_id = p.poll_id
    WHERE ". strtr($user['find_in_set'], array('alias' => 'b')). " AND t.topic_id = %topic_id
    LIMIT 1",
    array(
      'topic_id' => array('int', $_GET['topic']),
    ));

  # No rows..?
  if(!$db->num_rows($result))
    error_screen();
  else
  {
    # You must be able to see this :P
    $row = $db->fetch_assoc($result);

    # Build a topic array, containing some useful information ;)
    $page['topic'] = array(
      'id' => $row['topic_id'],
      'msg_id' => $row['first_msg_id'],
      'subject' => $row['subject'],
      'is_sticky' => !empty($row['is_sticky']),
      'is_locked' => !empty($row['is_locked']),
      'poll' => array(
                  'id' => $row['poll_id'],
                  'question' => $row['question'],
                  'closed' => !empty($row['closed']) || (time_utc() < $row['expires'] && $row['expires'] > 0),
                  'allowed_votes' => $row['allowed_votes'],
                  'allow_change' => !empty($row['allow_change']),
                  'voters' => $row['voters'],
                  'result_access' => !empty($row['result_access']),
                  'options' => array(),
                  'votes_casted' => array(),
                ),
      'board' => array(
                   'id' => $row['board_id'],
                   'name' => $row['board_name'],
                 ),
      'category' => array(
                      'id' => $row['cat_id'],
                      'name' => $row['cat_name'],
                    ),
      'num' => array(
                 'replies' => $row['num_replies'],
                 'views' => $row['num_views'],
               ),
      'who_view' => $row['who_view'],
      'starter' => $row['member_id'],
    );

    # You could be a board moderator too! =D Super Powers! ^__^
    # But of course you can only be a moderator if you are logged in...
    if($user['is_logged'])
    {
      # Also, mark as read... :)
      $db->insert('replace', $db->prefix. 'topic_logs',
        array(
          'topic_id' => 'int', 'member_id' => 'int',
        ),
        array(
          $page['topic']['id'], $user['id'],
        ),
        array('topic_id', 'member_id'));

      # Administrators have all powers anyways...
      if(!$user['is_admin'])
      {
        $result = $db->query("
          SELECT
            board_id, member_id
          FROM {$db->prefix}moderators
          WHERE board_id = %board_id AND member_id = %member_id
          LIMIT 1",
          array(
            'board_id' => array('int', $page['topic']['board']['id']),
            'member_id' => array('int', $user['id']),
          ));

        # Any rows? If so, your in luck! =D!
        if($db->num_rows($result))
          $user['is_moderator'] = true;
      }
    }
    elseif($user['is_guest'])
      $_SESSION['topic_viewed'][$page['topic']['id']] = $page['topic']['num']['replies'];

    # A poll..?
    if(!empty($page['topic']['poll']['id']))
    {
      # Yup, a poll! :p
      $page['is_poll'] = true;

      # Load up them options!
      $result = $db->query("
        SELECT
          option_id, poll_id, value, votes
        FROM {$db->prefix}topic_poll_options
        WHERE poll_id = %poll_id",
        array(
          'poll_id' => array('int', $page['topic']['poll']['id']),
        ));

      # Lets get those options! =D
      while($row = $db->fetch_assoc($result))
        $page['topic']['poll']['options'][] = array(
                                                'id' => $row['option_id'],
                                                'value' => $row['value'],
                                                'votes' => $row['votes'],
                                              );

      # Now a couple boolean variables... Like if you can vote and what not.
      $page['can_view_poll'] = $user['is_moderator'] || can('view_results') || can('view_results', $page['topic']['board']['id']);
      $page['can_vote'] = ($user['is_moderator'] || can('cast_vote') || can('cast_vote', $page['topic']['board']['id'])) && empty($page['topic']['poll']['closed']);

      # So you think you can vote? Let's see what Mr. Database has to say...
      if($page['can_vote'] && $user['is_logged'])
      {
        # Have you cast a vote for this poll? Then you cannot vote again!
        # Unless you revoke your votes, of course, if allowed. :P
        $result = $db->query("
          SELECT
            poll_id, member_id, option_id
          FROM {$db->prefix}topic_poll_logs
          WHERE poll_id = %poll_id AND member_id = %member_id",
          array(
            'poll_id' => array('int', $page['topic']['poll']['id']),
            'member_id' => array('int', $user['id']),
          ));

        if($db->num_rows($result))
        {
          # What have we got here? Your votes! That's what!
          $page['can_vote'] = false;

          # Now an array contain the options you voted for :P
          while($row = $db->fetch_assoc($result))
            $page['topic']['poll']['votes_casted'][] = $row['option_id'];
        }
      }

      # Display the results..? :P
      $page['display_results'] = (isset($_GET['displayResults']) && ($page['topic']['poll']['result_access'] == 1 || $page['topic']['poll']['result_access'] == 2 && !$page['can_vote'])) || !empty($page['topic']['poll']['closed']) || (!$page['can_vote'] && $page['topic']['poll']['result_access'] == 1);
      
    }
    else
      $page['is_poll'] = false;

    # Shall we do a couple other things first..?
    # Like increment the number of views this topic has!
    # Of course, don't do this each time you refresh. Lol.
    if(!isset($_SESSION['last_topic_id']) || $_SESSION['last_topic_id'] != $page['topic']['id'])
    {
      # Okay, increment away!
      $db->query("
        UPDATE {$db->prefix}topics
        SET num_views = num_views + 1
        WHERE topic_id = %topic_id
        LIMIT 1",
        array(
          'topic_id' => array('int', $page['topic']['id']),
        ));

      # Now set this as the last topic viewed!
      $_SESSION['last_topic_id'] = $page['topic']['id'];

      # You just added one more... So add it here too.
      $page['topic']['num']['views']++;
    }

    # Where are we starting..?
    $start = !empty($_GET['page']) ? (int)$_GET['page'] : 1;

    # Our current page. For later use, of course!
    $cur_page = $start;

    # Don't index this if we are on page 1 =| It shouldn't have ;page=1 in it!
    if(!empty($_GET['page']) && $start == 1)
      $page['no_index'] = true;

    # Create our pagination. The nation of pages, lol...
    $page['index'] = pagination_create($base_url. '/forum.php?topic='. $page['topic']['id'], $start, $page['topic']['num']['replies'] + 1, $settings['posts_per_page']);

    # Now get out all the posts...
    $result = $db->query("
      SELECT
        msg.msg_id, msg.topic_id, msg.board_id, msg.member_id, msg.modified_member_id,
        msg.modified_name, msg.modified_reason, msg.modified_time, msg.subject, msg.poster_time,
        msg.poster_name, msg.poster_email, msg.poster_ip, msg.body, msg.parse_bbc,
        msg.parse_smileys, msg.is_locked
      FROM {$db->prefix}messages AS msg
      WHERE msg.topic_id = %topic_id
      ORDER BY msg.msg_id ASC
      LIMIT %start,%per_page",
      array(
        'topic_id' => array('int', $page['topic']['id']),
        'start' => array('int', $start),
        'per_page' => array('int', $user['per_page']['posts']),
      ));

    # Now we need to get ALL the members information to load it with
    # our handy dandy members_load function!!! :D
    $members = array();
    $modified_members = array();
    while($row = $db->fetch_assoc($result))
    {
      $members[] = $row['member_id'];

      if(!empty($row['modified_member_id']))
        $modified_members[] = $row['modified_member_id'];
    }

    # Move the pointer back to square one...
    $db->data_seek($result, 0);

    # Load them up!
    require_once($source_dir. '/members.php');

    # See how easy this is?!
    members_load($members, 'extended');

    # Why two different ones..? Well, because we don't need anymore than basic
    # stuff for the person who modified your post, silly :P
    if(count($modified_members))
      members_load($modified_members, 'basic');

    # No sense on keep these around...
    unset($members, $modified_members);

    # Now the resource will be needed later...
    $page['messages_result'] = $result;
    unset($result);

    # Use this callback to get all the stuff :)
    $page['message_callback'] = 'topic_load_messages';

    # Load the topic now ;)
    $page['title'] = $page['topic']['subject']. ' - '. $settings['site_name'];

    # Can you reply?
    $page['can_reply'] = ($user['is_moderator'] || ((can('post_reply') || can('post_reply', $page['topic']['board']['id'])) && empty($page['topic']['is_locked'])));

    # Show moderation tools perhaps..?
    $page['show_moderation_tools'] = $user['is_moderator'];

    # Show quick reply..?
    $page['show_quick_reply'] = ($user['is_moderator'] || ((can('post_reply') || can('post_reply', $page['topic']['board']['id'])) && empty($page['topic']['is_locked']))) && !empty($user['visible']['quick_reply']);

    # Create a posting token... Only if the quick reply is being shown.
    if($page['show_quick_reply'])
    {
      require_once($source_dir. '/post.php');

      $page['post_token'] = post_token_create();
    }

    # Who's viewing this topic? Perhaps?
    if(!empty($user['visible']['recently_online']))
    {
      require_once($source_dir. '/online.php');
      $page['online'] = online_get(array('topic' => $_GET['topic']), true, 1);
      $page['online']['total_members'] = numberformat($page['online']['total_members']);
      $page['online']['total_guests'] = numberformat($page['online']['total_guests']);
    }

    theme_load('topic', 'topic_load_show');
  }
}

function topic_load_messages()
{
  global $base_url, $db, $l, $page, $settings, $user;

  # No messages result? Sorry. Can't do much!
  if(empty($page['messages_result']))
    return false;

  # Now fetch! Fetch like the wind! If anything exists though :P
  if(!($row = $db->fetch_assoc($page['messages_result'])))
    return false;

  # As much as I would like to not do much, I gotta! :P
  $message = array();

  # Format a couple things.
  $message['id'] = $row['msg_id'];
  $message['topic'] = $row['topic_id'];
  $message['board'] = $row['board_id'];
  $message['href'] = $base_url. '/forum.php?msg='. $message['id'];

  # The time is unformated, date is...
  $message['time'] = $row['poster_time'];
  $message['date'] = timeformat($row['poster_time']);

  # Censor the subject, you and your naughty words :P
  $message['subject'] = censor_text($row['subject']);

  # Censor the body before we parse BBCode. It might mess up links,
  # and images, but hey, it is probably the Administrators content to do so!
  $row['body'] = censor_text($row['body']);

  # Do you want BBCode parsed..?
  if(!empty($row['parse_bbc']))
    $message['body'] = bbc($row['body'], !empty($row['parse_smileys']), 'msg_id-'. $message['id']);
  # No BBC, but smileys perhaps?
  elseif(!empty($row['parse_smileys']))
    $message['body'] = smileys($row['body']);
  # Well, you are no fun!
  else
    $message['body'] = $row['body'];

  # Did you start this topic? :P
  $message['topic_starter'] = !empty($row['member_id']) && $row['member_id'] == $page['topic']['starter'];

  # Now the message posters information. If they are a member...
  if($member = members_info($row['member_id'], 'extended'))
  {
    # For the most part, all we need to do is this:
    $message['poster'] = $member;

    # However, change their IP to that of which this message was posted from.
    $message['poster']['ip'] = $row['poster_ip'];

    # Also, if your member group is 3 (Members, where they all go by default) switch the stars around!!!
    if($message['poster']['group']['id'] == 3 && !empty($message['poster']['post_group']['stars']))
    {
      $message['poster']['group']['stars'] = $message['poster']['post_group']['stars'];
      $message['poster']['group']['color'] = $message['poster']['post_group']['color'];
    }
  }
  else
    # A guest or a deleted member...
    $message['poster'] = array(
      'id' => 0,
      'name' => $row['poster_name'],
      'username' => $row['poster_name'],
      'email' => $row['poster_email'],
      'ip' => $row['poster_ip'],
    );

  # For now, it isn't modified ;)
  $message['modified'] = array('is' => false);

  # Now for the person who edited your post!!!
  if(!empty($row['modified_member_id']) && $member = members_info($row['modified_member_id'], 'basic'))
  {
    # We only need some stuff out of the members information.
    $message['modified'] = array(
      'is' => true,
      'id' => $member['id'],
      'name' => $member['name'],
      'username' => $member['username'],
      'href' => $member['href'],
      'link' => '<a href="'. $member['href']. '" target="_blank">'. $member['name']. '</a>',
      'reason' => censor_text($row['modified_reason']),
      'time' => $row['modified_time'],
      'date' => timeformat($row['modified_time']),
    );
  }
  elseif(!empty($row['modified_member_id']))
    # Well, they must have been deleted! ._.
    $message['modified'] = array(
      'is' => true,
      'id' => 0,
      'name' => $row['modified_name'],
      'username' => $row['modified_name'],
      'href' => false,
      'link' => $row['modified_name'],
      'reason' => censor_text($row['modified_reason']),
      'time' => $row['modified_time'],
      'date' => timeformat($row['modified_time']),
    );

  # Is this message locked..? :P
  $message['is_locked'] = !empty($row['is_locked']);

  # One last thing, what can you do?
  $message['can'] = array(
                     'quote' => $user['is_logged'],
                     'edit' => can('edit_any_post') || can('edit_any_post', $page['topic']['board']['id']) || ((can('edit_own_post') || can('edit_own_post', $page['topic']['board']['id'])) && $row['member_id'] == $user['id'] && empty($row['is_locked'])) || $user['is_moderator'],
                     'delete' => (can('delete_any_post') || can('delete_any_post', $page['topic']['board']['id']) || ((can('delete_own_post') || can('delete_own_post', $page['topic']['board']['id'])) && $row['member_id'] == $user['id']) || $user['is_moderator']) && $page['topic']['msg_id'] != $message['id'],
                     'split' => can('split_any_post') || can('split_any_post', $page['topic']['board']['id']) || $user['is_moderator'],
                     'view_ip' => can('view_ip') || can('view_ip', $page['topic']['board']['id']) || $row['member_id'] == $user['id'] || $user['is_moderator'],
                   );

  return $message;
}

function topic_redirect()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # First off, can you even view the forum?
  error_screen('view_forum');

  # Get the message ID...
  $msg_id = !empty($_GET['msg']) ? (int)$_GET['msg'] : 0;
  
  # Anything?
  if(!empty($msg_id))
  {
    # Yup, it is at least an id, but does it exist?
    $result = $db->query("
      SELECT
        msg_id, topic_id
      FROM {$db->prefix}messages
      WHERE msg_id = %msg_id",
      array(
        'msg_id' => array('int', $msg_id)
      ));

    # Does it even exist..?
    if($db->num_rows($result))
    {
      # We need the topic id.
      $msg = $db->fetch_assoc($result);

      # Now see if the topic exists, and whether or not
      # they are allowed to access the topic.
      $result = $db->query("
        SELECT
          t.topic_id, t.board_id, t.first_msg_id, t.num_replies,
          b.board_id, b.who_view
        FROM {$db->prefix}topics AS t
          LEFT JOIN {$db->prefix}boards AS b ON b.board_id = t.board_id
        WHERE ". strtr($user['find_in_set'], array('alias' => 'b')). " AND t.topic_id = %topic_id",
        array(
          'topic_id' => array('int', $msg['topic_id'])
        ));

      # Did it exist, or can they access it? :P
      if($db->num_rows($result))
      {
        $topic = $db->fetch_assoc($result);
        # Lets check something real quick like, is this msg id the
        # id of the first message of the topic?
        if($topic['first_msg_id'] == $msg_id)
          # Yup, redirect to the topics first page.
          redirect('forum.php?topic='. $topic['topic_id']);
        else
        {
          # Its something else...
          # Number of pages this topic has
          $numPages = ceil($topic['num_replies'] / $user['per_page']['posts']);

          # If there is only 1 page, no need to do anything complicated.
          if($numPages == 1)
            redirect('forum.php?topic='. $topic['topic_id']. '#msg'. $msg_id);
          else {
            # Something complicated :P
            # So lets see how many messages there are before (and including) this message.
            $result = $db->query("
              SELECT
                COUNT(*) as num_messages
              FROM {$db->prefix}messages
              WHERE topic_id = %topic_id AND msg_id <= %msg_id",
              array(
                'topic_id' => array('int', $topic['topic_id']),
                'msg_id' => array('int', $msg_id)
              ));
            $row = $db->fetch_assoc($result);

            # So lets get the page now :D
            $msgPage = ceil($row['num_messages'] / $settings['posts_per_page']);

            # Now redirect.
            redirect('forum.php?topic='. $topic['topic_id']. ';page='. $msgPage. '#msg'. $msg_id);
          }
        }
      }
      else
        # Nope...
        redirect('forum.php');
    }
    else
      # Just redirect...
      redirect('forum.php');
  }
  else
    # Doesn't exist...
    redirect('forum.php');
}
?>