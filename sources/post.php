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
# Post.php handles the posting, editing, etc. of topics
# and messages in the forum.
#
# void post_make();
#
# void post_save();
#
# void post_topic();
#
# void posts_message();
#
# void post_token_create();
#
# void post_token_delete();
#
# void post_preview_ajax();
#
# void post_quote_ajax();
#

function post_make()
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Can your membergroup even view the forum?
  error_screen('view_forum');

  language_load('post');

  # We need a couple JavaScript things.
  $page['scripts'][] = $settings['theme_url']. '/js/editor.js';
  $page['js_vars']['loading_text'] = $l['loading'];
  $page['js_vars']['option_str'] = $l['option'];
  $page['js_vars']['num_options'] = (int)(!empty($_POST['options']) && count($_POST['options']) > 0 ? count($_POST['options']) : 5);

  # Making a topic or a reply?
  if(!empty($_GET['board']) || !empty($page['board_id']))
  {
    $board_id = !empty($_GET['board']) ? (int)$_GET['board'] : (int)$page['board_id'];

    # We are making a topic... :D
    # But lets make sure this board even exists!
    $result = $db->query("
      SELECT
        b.board_name
      FROM {$db->prefix}boards AS b
      WHERE ". strtr($user['find_in_set'], array('alias' => 'b')). " AND b.board_id = %board_id",
      array(
        'board_id' => array('int', $board_id),
      ));
    if($db->num_rows($result))
    {
      $board_exists = true;
      @list($board_name) = $db->fetch_row($result);
    }

    # Check to see if this user is a moderator, perhaps ;)
    if(!$user['is_admin'] && $user['is_logged'])
    {
      $result = $db->query("
        SELECT
          board_id, member_id
        FROM {$db->prefix}moderators
        WHERE board_id = %board_id AND member_id = %member_id
        LIMIT 1",
        array(
          'board_id' => array('int', $board_id),
          'member_id' => array('int', $user['id']),
        ));
      if($db->num_rows($result))
        $user['is_moderator'] = true;
    }

    if(!empty($board_exists) && ($user['is_moderator'] || can('post_topic', $board_id) || can('post_topic')))
    {
      # Yay! Set the title and what not...
      $page['title'] = $l['post_new_topic'];

      # Some changeable text parts.
      $page['header_text'] = $l['post_new_topic_header'];
      $page['subheader_text'] = sprintf($l['posting_in'], $board_name);

      # But first set whether or not the people can use the moderation options (Lock, sticky, etc.)...
      $page['show_moderation_options'] = $user['is_moderator'] || $user['is_admin'] || can('use_moderation_options') || can('use_moderation_options', $board_id);

      # A poll, maybe..?
      if(isset($_REQUEST['poll']) && ($user['is_moderator'] || can('post_poll', $board_id) || can('post_poll')))
        $page['poll'] = true;
      else
        $page['poll'] = false;

      # Not yet set..? Set it! (The poll view setting, that is)
      if(!isset($page['results']) || $page['results'] < 1 || $page['results'] > 3)
        $page['results'] = 1;

      # You will need this too!
      if(!isset($page['board_id']))
        $page['board_id'] = $board_id;

      # Oh yeah, and a posting token!
      $page['post_token'] = post_token_create();

      theme_load('post', 'post_make_show');
    }
    else
    {
      language_load('post');

      # Just an error saying the board/topic doesn't exist :P Hehe
      $page['title'] = $l['error_screen_title'];

      theme_load('post', 'post_make_show_invalid');
    }
  }
  elseif(!empty($_GET['topic']) || !empty($page['topic_id']))
  {
    $topic_id = !empty($_GET['topic']) ? (int)$_GET['topic'] : (int)$page['topic_id'];

    # Replying..!
    # If you can, that is...
    $result = $db->query("
      SELECT
        t.board_id, t.num_replies, t.is_locked, msg.msg_id, msg.subject
      FROM {$db->prefix}topics AS t
        LEFT JOIN {$db->prefix}messages AS msg ON msg.msg_id = t.first_msg_id
      WHERE t.topic_id = %topic_id
      LIMIT 1",
      array(
        'topic_id' => array('int', $topic_id),
      ));

    # The topic needs to exist in order for you to reply to it! Silly :P
    if($db->num_rows($result))
    {
      $topic_exists = true;
      @list($board_id, $num_replies, $is_locked, $msg_id, $subject) = $db->fetch_row($result);
    }

    # You could be a moderator :)
    # Check to see if this user is a moderator, perhaps ;)
    if(!$user['is_admin'] && $user['is_logged'])
    {
      $result = $db->query("
        SELECT
          board_id, member_id
        FROM {$db->prefix}moderators
        WHERE board_id = %board_id AND member_id = %member_id
        LIMIT 1",
        array(
          'board_id' => array('int', $board_id),
          'member_id' => array('int', $user['id']),
        ));
      if($db->num_rows($result))
        $user['is_moderator'] = true;
    }

    # So, can you?
    if(!empty($topic_exists) && ($user['is_moderator'] || ((can('post_reply') || can('post_reply', $board_id)) && empty($is_locked))))
    {
      # You can reply! Good for you! 
      $page['title'] = $l['post_reply'];

      # JavaScript needed :)
      $page['scripts'][] = $settings['theme_url']. '/js/post.js';

      # You quoting anything..?
      if(!empty($_REQUEST['quote']))
      {
        $quote_messages = array();
        # Is it an array perhaps..?
        if(is_array($_REQUEST['quote']))
        {
          # That is quite fine with me :)
          if(count($_REQUEST['quote']))
          {
            foreach($_REQUEST['quote'] as $msg_id)
              if((string)$msg_id === (string)(int)$msg_id)
                $quote_messages[] = (int)$msg_id;
          }
        }
        elseif((string)$_REQUEST['quote'] == (string)(int)$_REQUEST['quote'])
          $quote_messages[] = (int)$_REQUEST['quote'];

        # So anything get through our little check..?
        if(count($quote_messages))
        {
          # No need to do multiples!
          $quote_messages = array_unique($quote_messages);

          $result = $db->query("
            SELECT
              msg.msg_id, msg.board_id, msg.member_id, msg.poster_time, msg.poster_name,
              msg.body, mem.member_id, mem.loginName, mem.displayName, b.who_view
            FROM {$db->prefix}messages AS msg
              LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = msg.member_id
              INNER JOIN {$db->prefix}boards AS b ON (b.board_id = msg.board_id AND ". strtr($user['find_in_set'], array('alias' => 'b')). ")
            WHERE msg.msg_id IN(%quote_messages)
            LIMIT %total_quoted",
            array(
              'quote_messages' => array('int_array', $quote_messages),
              'total_quoted' => array('int', count($quote_messages)),
            ));

          if($db->num_rows($result))
          {
            # Already got a message..? We don't want to ruin it!
            if(!isset($page['post_message']))
              $page['post_message'] = '';

            while($row = $db->fetch_assoc($result))
            {
              $page['post_message'] .= '[quote author='. ($row['displayName'] ? $row['displayName'] : $row['poster_name']). ' msg='. $row['msg_id']. ' time='. $row['poster_time']. "]\r\n". $row['body']. "\r\n[/quote]\r\n";
            }
          }
        }
      }

      # Some interchangeable thingys...
      $page['header_text'] = $l['post_reply_header'];
      $page['subheader_text'] = sprintf($l['posting_reply_to'], $subject);

      # Can you moderate? :D
      $page['show_moderation_options'] = $user['is_moderator'] || $user['is_admin'] || can('use_moderation_options') || can('use_moderation_options', $board_id);

      # You will need this too!
      if(!isset($page['topic_id']))
        $page['topic_id'] = $topic_id;

      # Oh yeah, and a posting token!
      $page['post_token'] = post_token_create();

      # You shouldn't have to make a subject... But if you want too, thats okay!
      if(!isset($page['post_subject']))
        $page['post_subject'] = $l['re']. ' '. $subject;

      # We use this to alert you of any new posts!
      $page['num_replies'] = $num_replies;

      # But wait, the last 10 replies :)
      $result = $db->query("
        SELECT
          msg.msg_id, msg.member_id, msg.poster_time, msg.poster_name, msg.body,
          msg.parse_bbc, msg.parse_smileys, mem.member_id, mem.loginName, mem.displayName
        FROM {$db->prefix}messages AS msg
          LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = msg.member_id
        WHERE msg.topic_id = %topic_id
        ORDER BY msg.poster_time ASC
        LIMIT 10",
        array(
          'topic_id' => array('int', $topic_id),
        ));
      $page['last_posts'] = array();
      while($row = $db->fetch_assoc($result))
        $page['last_posts'][] = array(
          'id' => $row['msg_id'],
          'posted' => timeformat($row['poster_time']),
          'poster' => array(
                        'id' => $row['member_id'],
                        'name' => $row['displayName'] ? $row['displayName'] : $row['poster_name'],
                        'username' => $row['loginName'],
                      ),
          'body' => !empty($row['parse_bbc']) ? bbc($row['body'], !empty($row['parse_smileys']), 'message_id-'. $row['msg_id']) : (!empty($row['parse_smileys']) ? smileys($row['body']) : $row['body']),
        );

      # These go in reverse order :P
      $page['last_posts'] = array_reverse($page['last_posts']);

      theme_load('post', 'post_make_show');
    }
    else
    {
      # Nope :P
      language_load('post');

      # Just an error saying the board/topic doesn't exist :P Hehe
      $page['title'] = $l['error_screen_title'];

      theme_load('post', 'post_make_show_invalid');
    }
  }
  else
    # Uh oh!
    error_screen();
}

function post_save()
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Can't view the forum? Then you can't even post!
  error_screen('view_forum');

  language_load('post');

  # Errors! Get your errors here! :P
  $page['post_errors'] = array();

  # We could have our first error :P
  if(empty($_POST['posting_token']) || !post_token_exists($_POST['posting_token']))
    $page['post_errors'][] = $l['invalid_post_token'];

  # Posting a topic or in a topic?
  if(!empty($_REQUEST['board']))
  {
    $board_id = (int)$_REQUEST['board'];

    # In order to post in this board, it needs to exist.
    $result = $db->query("
      SELECT
        b.board_name
      FROM {$db->prefix}boards AS b
      WHERE ". strtr($user['find_in_set'], array('alias' => 'b')). " AND b.board_id = %board_id",
      array(
        'board_id' => array('int', $board_id),
      ));
    if($db->num_rows($result))
    {
      $board_exists = true;
      @list($board_name) = $db->fetch_row($result);
    }

    # Check to see if this user is a moderator, perhaps ;)
    if(!$user['is_admin'] && $user['is_logged'])
    {
      $result = $db->query("
        SELECT
          board_id, member_id
        FROM {$db->prefix}moderators
        WHERE board_id = %board_id AND member_id = %member_id
        LIMIT 1",
        array(
          'board_id' => array('int', $board_id),
          'member_id' => array('int', $user['id']),
        ));
      if($db->num_rows($result))
        $user['is_moderator'] = true;
    }

    # So, did everything fall into place or not..?
    if(!empty($board_exists) && ($user['is_moderator'] || can('post_topic', $board_id) || can('post_topic')))
    {
      # Our options array for making the topic and stuffs!
      $options = array();

      # Let's check the poll stuff first! If you can post polls, that is ;)
      if(isset($_POST['poll']) && ($user['is_moderator'] || can('post_poll', $board_id) || can('post_poll')))
      {
        $options['poll'] = array();

        # We need to have a not empty poll question :P
        $options['poll']['question'] = !empty($_POST['question']) ? $_POST['question'] : '';

        # We need options for the poll...
        $options['poll']['options'] = !empty($_POST['options']) && count($_POST['options']) ? $_POST['options'] : array();

        # Votes per user..?
        $options['poll']['votes_allowed'] = !empty($_POST['votes_per_user']) ? (int)$_POST['votes_per_user'] : 1;

        # When does the poll expire..?
        $options['poll']['expires'] = !empty($_POST['poll_expires']) ? (int)$_POST['poll_expires'] : 0;

        # Can the users revoke their choices?
        $options['poll']['allow_change'] = !empty($_POST['allow_change']);

        # How about who can view the results..?
        $options['poll']['result_access'] = !empty($_POST['results_access']) ? (int)$_POST['results_access'] : 1;
      }

      # Check the regular stuffs now! Like the subject XD.
      $options['subject'] = !empty($_POST['subject']) ? $_POST['subject'] : '';

      # The actual post...
      $options['message'] = !empty($_POST['post']) ? $_POST['post'] : '';

      # Now just some settings. Like do you want to parse BBC?
      $options['parse_bbc'] = empty($_POST['no_bbc']);

      # Hmmm, smileys?
      $options['parse_smileys'] = empty($_POST['no_smileys']);

      # These can only be done by those who are allowed to ;)
      if($user['is_moderator'] || $user['is_admin'] || can('use_moderation_options') || can('use_moderation_options', $_GET['board']))
      {
        $options['change_moderation_options'] = true;
        $options['is_sticky'] = !empty($_POST['sticky']);
        $options['is_locked'] = !empty($_POST['lock']);
        $options['msg_locked'] = !empty($_POST['lock_message']);
      }
      else
      {
        # Nope and nope :P
        $options['change_moderation_options'] = false;
        $options['is_sticky'] = false;
        $options['is_locked'] = false;
        $options['msg_locked'] = false;
      }

      # So, create it! If you aren't previewing..!
      if(empty($_POST['preview']))
        $topic_id = post_topic($board_id, $options['subject'], $options['message'], $options, $page['post_errors']);

      # I think we are done... So, any errors?
      if(count($page['post_errors']) || !empty($_POST['preview']))
      {
        # Uh oh! Set up some stuff first.
        if(!empty($options['poll']))
        {
          $page['post_question'] = htmlspecialchars($options['poll']['question'], ENT_QUOTES, 'UTF-8');
          $page['votes_per_user'] = $options['poll']['votes_allowed'];
          $page['poll_expires'] = $options['poll']['expires'];
          $page['allow_change'] = $options['poll']['allow_change'];
          $page['results'] = $options['poll']['result_access'];
        }

        $page['post_subject'] = htmlspecialchars($options['subject'], ENT_QUOTES, 'UTF-8');
        $page['post_message'] = htmlspecialchars($options['message'], ENT_QUOTES, 'UTF-8');

        # Additional settings you set.
        $page['return'] = !empty($_POST['return']);
        $page['no_bbc'] = !$options['parse_bbc'];
        $page['no_smileys'] = !$options['parse_smileys'];
        $page['sticky'] = $options['is_sticky'];
        $page['lock'] = $options['is_locked'];
        $page['board_id'] = $board_id;

        # Delete the old posting token. If any... (That could have been the error)
        if(!empty($_POST['posting_token']))
          post_token_delete($_POST['posting_token']);

        # Displaying preview..?
        $page['display_preview'] = !empty($_POST['preview']);

        # Prepare a few things :) Maybe.
        if($page['display_preview'])
        {
          $page['preview_subject'] = htmlspecialchars($options['subject'], ENT_QUOTES);
          $page['preview_body'] = bbc(htmlspecialchars($options['message'], ENT_QUOTES));
        }

        # Now display the errors and everything.
        post_make();

        exit;
      }
      else
      {
        # Well, you did it! You posted that topic! :)

        # We don't need that token anymore...
        post_token_delete($_POST['posting_token']);

        # Update total topics and posts
        update_settings(array(
          'total_topics' => '++',
          'total_posts' => '++',
        ));

        # So, to the board or the topic..?
        if(!empty($_POST['return']))
          redirect('forum.php?topic='. $topic_id);
        else
          # To the board, and away!!!
          redirect('forum.php?board='. $board_id);
      }
    }
    else
    {
      language_load('post');

      # Just an error saying the board/topic doesn't exist :P Hehe
      $page['title'] = $l['error_screen_title'];

      theme_load('post', 'post_make_show_invalid');
    }
  }
  elseif(!empty($_REQUEST['topic']))
  {
    # Get our topic id XD.
    $topic_id = (int)$_REQUEST['topic'];

    # This topic needs to exist if you want to reply to it! (If you can, of course!)
    $result = $db->query("
      SELECT
        t.board_id, t.num_replies, t.is_locked, msg.msg_id, msg.subject
      FROM {$db->prefix}topics AS t
        LEFT JOIN {$db->prefix}messages AS msg ON msg.msg_id = t.first_msg_id
      WHERE t.topic_id = %topic_id
      LIMIT 1",
      array(
        'topic_id' => array('int', $topic_id),
      ));

    # The topic needs to exist in order for you to reply to it! Silly :P
    if($db->num_rows($result))
    {
      $topic_exists = true;
      @list($board_id, $num_replies, $is_locked, $msg_id, $subject) = $db->fetch_row($result);
    }

    # You could be a moderator :)
    # Check to see if this user is a moderator, perhaps ;)
    if(!$user['is_admin'] && $user['is_logged'])
    {
      $result = $db->query("
        SELECT
          board_id, member_id
        FROM {$db->prefix}moderators
        WHERE board_id = %board_id AND member_id = %member_id
        LIMIT 1",
        array(
          'board_id' => array('int', $board_id),
          'member_id' => array('int', $user['id']),
        ));
      if($db->num_rows($result))
        $user['is_moderator'] = true;
    }

    # Do you have the proper permissions..?
    if(!empty($topic_exists) && ($user['is_moderator'] || ((can('post_reply') || can('post_reply', $board_id)) && empty($is_locked))))
    {
      # No poll crap this time :) This is simple(r)!

      # Any more replies..?
      if(isset($_POST['num_replies']) && (string)$_POST['num_replies'] === (string)(int)$_POST['num_replies'] && $num_replies > (int)$_POST['num_replies'])
        $page['post_errors'][] = sprintf($l['new_posts_please_revise'], $num_replies - (int)$_POST['num_replies']);

      # Check the regular stuffs now! Like the subject XD.
      $options['subject'] = !empty($_POST['subject']) ? $_POST['subject'] : '';

      # It's actually okay to have an empty subject XD.
      if(empty($options['subject']) || trim($options['subject']) == '')
        $options['subject'] = $l['re']. ' '. htmlspecialchars_decode($subject, ENT_QUOTES);

      # The actual post...
      $options['message'] = !empty($_POST['post']) ? $_POST['post'] : '';

      # Now just some settings. Like do you want to parse BBC?
      $options['parse_bbc'] = empty($_POST['no_bbc']);

      # Hmmm, smileys?
      $options['parse_smileys'] = empty($_POST['no_smileys']);

      # These can only be done by those who are allowed to ;)
      if($user['is_moderator'] || $user['is_admin'] || can('use_moderation_options') || can('use_moderation_options', $_GET['board']))
      {
        $options['change_moderation_options'] = true;
        $options['is_sticky'] = !empty($_POST['sticky']);
        $options['is_locked'] = !empty($_POST['lock']);
        $options['msg_locked'] = !empty($_POST['lock_message']);
      }
      else
      {
        # Nope and nope :P
        $options['change_moderation_options'] = false;
        $options['is_sticky'] = false;
        $options['is_locked'] = false;
        $options['msg_locked'] = false;
      }

      # You'll need this!
      $options['board_id'] = $board_id;

      # Previewing? Don't make it yet...
      if(empty($_POST['preview']))
        $msg_id = post_message_create($topic_id, $options['subject'], $options['message'], $options, $page['post_errors']);

      if(count($page['post_errors']) || !empty($_POST['preview']))
      {
        # Uh oh!
        $page['post_subject'] = htmlspecialchars($options['subject'], ENT_QUOTES, 'UTF-8');
        $page['post_message'] = htmlspecialchars($options['message'], ENT_QUOTES, 'UTF-8');

        # Additional settings you set.
        $page['return'] = !empty($_POST['return']);
        $page['no_bbc'] = !$options['parse_bbc'];
        $page['no_smileys'] = !$options['parse_smileys'];
        $page['sticky'] = $options['is_sticky'];
        $page['lock'] = $options['is_locked'];
        $page['topic_id'] = $topic_id;

        # Delete the old posting token. If any... (That could have been the error)
        if(!empty($_POST['posting_token']))
          post_token_delete($_POST['posting_token']);

        # Displaying preview..?
        $page['display_preview'] = !empty($_POST['preview']);

        # Prepare a few things :) Maybe.
        if($page['display_preview'])
        {
          $page['preview_subject'] = htmlspecialchars($options['subject'], ENT_QUOTES);
          $page['preview_body'] = bbc(htmlspecialchars($options['message'], ENT_QUOTES));
        }

        # Now display the errors and everything.
        post_make();

        # Now stop!
        exit;
      }
      else
      {
        # It was a success! :)!

        # We don't need that token anymore...
        post_token_delete($_POST['posting_token']);
        
        # Update total posts
        update_settings(array(
          'total_posts' => '++',
        ));
        
        # So, to the board or the message..?
        if(!empty($_POST['return']))
          redirect('forum.php?msg='. $msg_id);
        else
          # To the board, and away!!!
          redirect('forum.php?board='. $board_id);
      }
    }
    else
    {
      language_load('post');

      # Just an error saying the board/topic doesn't exist :P Hehe
      $page['title'] = $l['error_screen_title'];

      theme_load('post', 'post_make_show_invalid');
    }
  }
  else
    # Nothing that I know of...
    error_screen();
}

function post_topic($board_id, $subject, $message, $options = array(), &$errors = array())
{
  global $base_url, $db, $l, $page, $settings, $user;

  # No stopping now!!!
  ignore_user_abort(true);

  # We may need this. Just incase ;)
  language_load('post');

  $board_id = (int)$board_id;

  # Please remember that this function is, well, dumb! What I mean is this function
  # in no way checks permissions. It creates the topic with EVERYTHING you give this
  # function regardless of the users permissions!

  # Do you have a poll that needs validation..?
  # You know, if you can post a poll ;)
  if(isset($options['poll']))
  {
    # The question cannot be empty...
    if(empty($options['poll']['question']) || mb_strlen($options['poll']['question']) < 1)
    {
      $errors[] = $l['question_empty'];
      $page['question_error'] = true;
    }
    else
      $options['poll']['question'] = htmlspecialchars($options['poll']['question'], ENT_QUOTES, 'UTF-8');

    # Option checking time!
    if(empty($options['poll']['options']) || count($options['poll']['options']) < 1)
      $errors[] = $l['options_empty'];
    elseif(count($options['poll']['options']) > 256)
      $errors[] = $l['options_to_many'];
    else
    {
      # Well, sanitize them. Make sure they aren't empty :P
      $tmp = array();

      foreach($options['poll']['options'] as $option)
      {
        $option = trim($option);
        if(!empty($option))
          $tmp[] = htmlspecialchars($option, ENT_QUOTES, 'UTF-8');
      }

      # Empty now?
      if(empty($tmp) || count($tmp) < 1)
        $errors[] = $l['options_empty'];
      else
        $options['poll']['options'] = $tmp;
    }

    # How many may they choose?
    if(empty($options['poll']['votes_allowed']) || $options['poll']['votes_allowed'] < 1)
      # What's the point of this poll..?
      $errors[] = $l['invalid_votes_per_user'];
    elseif($options['poll']['votes_allowed'] > count($options['poll']['options']))
      # They can't choose TOO many!
      $errors[] = $l['to_many_votes_per_user'];
    else
      $options['poll']['votes_allowed'] = (int)$options['poll']['votes_allowed'];

    # Expiration?
    if(!isset($options['poll']['expires']) || $options['poll']['expires'] < 0)
      $errors[] = $l['invalid_poll_expires'];
    else
      $options['poll']['expires'] = (int)$options['poll']['expires'];

    # Allow them to change their vote..?
    $options['poll']['allow_change'] = !empty($options['poll']['allow_change']);

    # How about who can view the results...
    if(empty($options['poll']['result_access']) || $options['poll']['result_access'] < 1 || $options['poll']['result_access'] > 3)
      $errors[] = $l['invalid_results_access'];
    else
      $options['poll']['result_access'] = (int)$options['poll']['result_access'];
  }

  # You can't have these empty. We won't sanitize them, but we will check!
  if(empty($subject) || mb_strlen($subject) < 1)
  {
    $errors[] = $l['subject_empty'];
    $page['subject_error'] = true;
  }

  if(empty($message) || mb_strlen($message) < 1)
  {
    $errors[] = $l['message_empty'];
    $page['post_error'] = true;
  }

  # A couple others need to be set. No matter what!
  $options['parse_bbc'] = !empty($options['parse_bbc']);
  $options['parse_smileys'] = !empty($options['parse_smileys']);
  $options['is_sticky'] = !empty($options['is_sticky']);
  $options['is_locked'] = !empty($options['is_locked']);

  # Woo! FINALLY! Database time. Maybe :P
  if(!count($errors))
  {
    # Make the new topic. We just need to kinda reserve the space :P
    $db->insert('insert', $db->prefix. 'topics',
      array(
        'is_sticky' => 'int', 'is_locked' => 'int', 'board_id' => 'int',
      ),
      array(
        $options['is_sticky'] ? 1 : 0, $options['is_locked'] ? 1 : 0, $board_id,
      ),
      array());
    
    # Get the last inserted id, which is our topic id! XD.
    $topic_id = $db->last_id($db->prefix. 'topics');

    # You got a poll we ought to deal with..?
    if(isset($options['poll']))
    {
      # Make that room as well ._.
      $db->insert('insert', $db->prefix. 'topic_polls',
        array(
          'question' => 'string-255', 'allowed_votes' => 'int', 'expires' => 'int',
          'allow_change' => 'int', 'member_id' => 'int', 'poster_name' => 'string',
        ),
        array(
          $options['poll']['question'], $options['poll']['votes_allowed'], ($options['poll']['expires'] > 0 ? (time_utc() + $options['poll']['expires'] * 86400) : 0),
          $options['poll']['allow_change'] ? 1 : 0, $user['id'], $user['name'],
        ),
        array());

      # So what's our poll's id?
      $poll_id = $db->last_id($db->prefix. 'topic_polls');

      # Yup, time to insert all those options!
      $option_insert = array();
      foreach($options['poll']['options'] as $option)
        $option_insert[] = array($poll_id, $option);

      # Oh yeah! XD!
      $db->insert('insert', $db->prefix. 'topic_poll_options',
        array(
          'poll_id' => 'int', 'value' => 'string-255',
        ),
        $option_insert,
        array());
    }

    # Time to create the message! Another function call, phew!
    # Oh wait, you'll need this!
    $options['board_id'] = $board_id;
    $msg_id = post_message_create($topic_id, $subject, $message, $options);

    # Okay... Now we may do the last thing! Set number of replies to 0
    # on your way too, because post_message_create(); incremented it when it
    # doesn't need to be for creating a topic XD!
    $db->query("
      UPDATE {$db->prefix}topics
      SET poll_id = %poll_id, first_msg_id = %msg_id,
      starter_member_id = %member_id, starter_member_name = %poster_name,
      num_replies = 0
      WHERE topic_id = %topic_id
      LIMIT 1",
      array(
        'poll_id' => array('int', !empty($poll_id) ? (int)$poll_id : 0),
        'msg_id' => array('int', $msg_id),
        'member_id' => array('int', $user['id']),
        'poster_name' => array('string', $user['name']),
        'topic_id' => array('int', $topic_id),
      ));

    # Last post in the board was taken care of by post_message_create() as well
    # so we don't need to do that, except for this:
    $db->query("
      UPDATE {$db->prefix}boards
      SET num_topics = num_topics + 1
      WHERE board_id = %board_id
      LIMIT 1",
      array(
        'board_id' => array('int', $board_id),
      ));

    # Update total topics and posts
    update_settings(array(
      'total_topics' => $settings['total_topics'] + 1,
      'total_posts' => $settings['total_posts'] + 1,
    ));

    # Increment this users number of topics...
    if($user['is_logged'])
      $db->query("
        UPDATE {$db->prefix}members
        SET num_topics = num_topics + 1
        WHERE member_id = %member_id
        LIMIT 1",
        array(
          'member_id' => array('int', $user['id']),
        ));

    # It also took care of the fact that you have viewed this topic
    # already. So, what's left..?
    return $topic_id;
  }
  else
    # Otherwise, nothing, you failed! :'(
    return false;
}

function post_message_create($topic_id, $subject, $message, $options = array(), &$errors = array())
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Might have been done by post_topic(), but still!
  ignore_user_abort(true);

  # Just for errors, but you never make those, do you..?
  language_load('post');

  $topic_id = (int)$topic_id;

  # These can't be empty! :P
  if(empty($subject) || mb_strlen($subject) < 1)
  {
    $errors[] = $l['subject_empty'];
    $page['subject_error'] = true;
  }
  else
    $subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');

  if(empty($message) || mb_strlen($message) < 1)
  {
    $errors[] = $l['message_empty'];
    $page['post_error'] = true;
  }
  else
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

  # These empty..? That's okay.
  $options['parse_bbc'] = !empty($options['parse_bbc']);
  $options['parse_smileys'] = !empty($options['parse_smileys']);
  $options['is_sticky'] = !empty($options['is_sticky']);
  $options['is_locked'] = !empty($options['is_locked']);
  $options['msg_locked'] = !empty($options['msg_locked']);

  if(!count($errors))
  {
    # Simple, I suppose.
    $columns = array(
                'topic_id' => 'int', 'board_id' => 'int', 'member_id' => 'int',
                'subject' => 'string-255', 'poster_time' => 'int', 'poster_name' => 'string',
                'poster_email' => 'string', 'poster_ip' => 'string', 'body' => 'text',
                'parse_bbc' => 'int', 'parse_smileys' => 'int',
              );
    $values = array(
                $topic_id, $options['board_id'], $user['id'],
                $subject, time_utc(), $user['name'],
                $user['email'], $user['ip'], $message,
                $options['parse_bbc'] ? 1 : 0, $options['parse_smileys'] ? 1 : 0,
              );

    # Maybe lock the message?
    if(!empty($options['change_moderation_options']))
    {
      $columns['is_locked'] = 'int';
      $values[] = $options['msg_locked'] ? 1 : 0;
    }

    $db->insert('insert', $db->prefix. 'messages',
      $columns,
      $values,
      array());

    # Get the last id...
    $msg_id = $db->last_id($db->prefix. 'messages');

    # You thought we were done? Ha!
    # Update the topic to reflect the last message id.
    $db->query("
      UPDATE {$db->prefix}topics
      SET last_msg_id = %msg_id, num_replies = num_replies + 1,
      last_member_id = %member_id, last_member_name = %poster_name
      WHERE topic_id = %topic_id
      LIMIT 1",
      array(
        'msg_id' => array('int', $msg_id),
        'member_id' => array('int', $user['id']),
        'poster_name' => array('string', $user['name']),
        'topic_id' => array('int', $topic_id),
      ));

    # Oh, and the boards last post ._.
    $db->query("
      UPDATE {$db->prefix}boards
      SET num_posts = num_posts + 1, last_msg_id = %msg_id,
      last_member_id = %member_id, last_member_name = %poster_name
      WHERE board_id = %board_id
      LIMIT 1",
      array(
        'msg_id' => array('int', $msg_id),
        'member_id' => array('int', $user['id']),
        'poster_name' => array('string', $user['name']),
        'board_id' => array('int', $options['board_id']),
      ));

    # Your last message in this topic, that way we know! :P
    if($user['is_logged'])
      $db->insert('replace', $db->prefix. 'message_logs',
        array(
          'member_id' => 'int', 'topic_id' => 'int', 'msg_id' => 'int',
        ),
        array(
          $user['id'], $topic_id, $msg_id,
        ),
        array('member_id', 'topic_id'));

    # No one has read this topic now! Muahaha! XD
    $db->query("
      DELETE FROM {$db->prefix}topic_logs
      WHERE topic_id = %topic_id",
      array(
        'topic_id' => array('int', $topic_id),
      ));

    # Oh wait, except you :P If you are logged in
    if($user['is_logged'])
      $db->insert('insert', $db->prefix. 'topic_logs',
        array(
          'topic_id' => 'int', 'member_id' => 'int',
        ),
        array(
          $topic_id, $user['id'],
        ),
        array());

    # One more post! ^.^
    if($user['is_logged'])
    {
      $db->query("
        UPDATE {$db->prefix}members
        SET num_posts = num_posts + 1
        WHERE member_id = %member_id
        LIMIT 1",
        array(
          'member_id' => array('int', $user['id']),
        ));

      # To avoid any possible mishaps ;)
      $user['posts']++;

      # New post group..? :)
      $result = $db->query("
        SELECT
          group_id
        FROM {$db->prefix}membergroups
        WHERE min_posts > -1 AND min_posts <= %num_posts
        ORDER BY min_posts DESC
        LIMIT 1",
        array(
          'num_posts' => array('int', $user['posts']),
        ));

      # Only if we found anything, of course XD!!!
      if($db->num_rows($result))
      {
        list($post_group_id) = $db->fetch_row($result);

        # Update it...
        $db->query("
          UPDATE {$db->prefix}members
          SET post_group_id = %post_group_id
          WHERE member_id = %member_id
          LIMIT 1",
          array(
            'post_group_id' => array('int', $post_group_id),
            'member_id' => array('int', $user['id']),
          ));
      }
    }

    # Now does the moderation options need changing..?
    if(!empty($options['change_moderation_options']))
    {
      $db->query("
        UPDATE {$db->prefix}topics
        SET is_locked = %is_locked, is_sticky = %is_sticky
        WHERE topic_id = %topic_id
        LIMIT 1",
        array(
          'is_locked' => array('int', $options['is_locked'] ? 1 : 0),
          'is_sticky' => array('int', $options['is_sticky'] ? 1 : 0),
          'topic_id' => array('int', $topic_id),
        ));
    }

    # Phew! DONE!
    return $msg_id;
  }
  else
    return false;
}

function post_token_create()
{
  global $settings, $user;

  if(!isset($_SESSION['posting_tokens']))
    $_SESSION['posting_tokens'] = array();

  # After you get so many tokens, we should delete some... Just maybe.
  post_token_purge();

  # Pretty simple. We are generating a random string with some extras :P
  $token_func = create_function('', '
    global $user;
    return sha1(mt_rand(999, 1999). (mt_rand(1, 2) == 1 ? $user[\'password\']. $user[\'sc\'] : $user[\'sc\']. $user[\'password\']). microtime(true));');

  # No duplicates!
  $token = $token_func();
  while(isset($_SESSION['posting_tokens'][$token]))
    $token = $token_func();

  # We got it! Add it to their session :)
  $_SESSION['posting_tokens'][$token] = time_utc();

  # Now you can have it XD.
  return $token;
}

function post_token_delete($del_token)
{
  global $settings, $user;

  # Delete some tokens if they are super old.
  post_token_purge(array($del_token));

  # Just delete the darn token :P Simple as that.
  # But no reason to go on a wild goose chase if
  # the token doesn't exist XD.
  if(!empty($_SESSION['posting_tokens']) && isset($_SESSION['posting_tokens'][$del_token]))
  {
    unset($_SESSION['posting_tokens'][$del_token]);

    # You get here? You shouldn't have! ._.
    return !isset($_SESSION['posting_tokens'][$del_token]);
  }
  else
    return false;
}

function post_token_exists($token)
{
  return isset($_SESSION['posting_tokens'][$token]);
}

function post_token_purge($exclude = array())
{
  global $settings;
  static $ran = false;

  # Only if we have over 10... And it hasn't been ran on this load too!
  if(count($_SESSION['posting_tokens']) > 10 && !$ran)
  {
    $time_add = $settings['online_timeout'] * 60;
    foreach($_SESSION['posting_tokens'] as $token => $time_added)
    {
      # Is it older than the online timeout? (Don't worry if your still posting, it will just require you to click post again XD).
      if(($time_added + $time_add) < time_utc() && !in_array($token, $exclude))
        unset($_SESSION['posting_tokens'][$token]);
    }

    # Don't run it twice on the same page load. Pointless really...
    $ran = true;
  }
}

function post_preview_ajax()
{
  global $l, $settings;

  language_load('post');

  # We don't care about permissions here. Nothing bad *should* happen :P
  if(!empty($settings['forum_enabled']))
  {
    # Our array that outputs errors and what not... Just to let them know
    # when they are previewing their message XD
    $output = array('has_poll' => !empty($_POST['poll']), 'error' => '', 'errors' => array(), 'subject' => '&nbsp;', 'post' => '');

    # Poll by any chance..?
    if(!empty($_POST['poll']) && (empty($_POST['question']) || mb_strlen($_POST['question']) < 1))
    {
      $output['question_error'] = true;
      $output['errors'][] = $l['question_empty'];
    }
    elseif(!empty($_POST['poll']))
      $output['question_error'] = false;

    # Any subject errors? It has to be, well, not empty. lol.
    if(empty($_POST['subject']) || mb_strlen($_POST['subject']) < 1)
    {
      $output['subject_error'] = true;
      $output['errors'][] = $l['subject_empty'];
    }
    else
    {
      $output['subject_error'] = false;

      $output['subject'] = htmlspecialchars($_POST['subject'], ENT_QUOTES, 'UTF-8');
    }

    # Message errors, perhaps?
    if(empty($_POST['post']) || mb_strlen($_POST['post']) < 1)
    {
      $output['post_error'] = true;
      $output['errors'][] = $l['message_empty'];
    }
    else
    {
      $output['post_error'] = false;

      # This will have to be done eventually. Just do it now.
      $_POST['post'] = htmlspecialchars($_POST['post'], ENT_QUOTES, 'UTF-8');

      # Want BBC..?
      if(!empty($_POST['parse_bbc']))
        # Parse the BBC, maybe smileys too :)
        $output['post'] = bbc($_POST['post'], !empty($_POST['parse_smileys']));
      # No BBC but smileys? o_O Okay then.
      elseif(empty($_POST['parse_bbc']) && !empty($_POST['parse_smileys']))
        $output['post'] = smileys($_POST['post']);
      # Nothing?
      else
        $output['post'] = $_POST['post'];
    }

    echo json_encode($output);
  }
  else
    echo json_encode(array('error' => $l['preview_error']));

  exit;
}

function post_quote_ajax()
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Only if the forum is enabled :)
  if(empty($settings['forum_enabled']))
    exit;

  # If you can't view it, you cannot quote it! XD.
  $result = $db->query("
    SELECT
      msg.msg_id, msg.poster_name, msg.poster_time,
      msg.body, mem.loginName, mem.displayName
    FROM {$db->prefix}messages AS msg
      LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = msg.member_id
      LEFT JOIN {$db->prefix}boards AS b ON b.board_id = msg.board_id
    WHERE msg.msg_id = %msg_id AND ". strtr($user['find_in_set'], array('alias' => 'b')). "
    LIMIT 1",
    array(
      'msg_id' => array('int', !empty($_REQUEST['msg']) ? (int)$_REQUEST['msg'] : 0),
    ));

  $output = array();

  if($db->num_rows($result))
  {
    $row = $db->fetch_assoc($result);

    $output['id'] = $row['msg_id'];
    $output['author'] = $row['displayName'] ? $row['displayName'] : $row['poster_name'];
    $output['time'] = $row['poster_time'];
    $output['body'] = $row['body'];
  }
  else
  {
    language_load('errors');

    $output['error'] = $l['error_ajax_quote'];
  }

  echo json_encode($output);
}
?>