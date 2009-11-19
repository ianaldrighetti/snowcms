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
#

function post_edit()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # If you can't view the forum, you certainly can't edit.
  error_screen('view_forum');

  language_load('edit_post');
  language_load('post');

  # Session check, you know, for security :P
  security_sc('request');

  # Some post editing stuff...
  $page['scripts'][] = $settings['default_theme_url']. '/js/editor.js';
  $page['scripts'][] = $settings['default_theme_url']. '/js/post.js';
  $page['js_vars']['loading_text'] = $l['loading'];
  $page['js_vars']['option_str'] = $l['option'];
  $page['js_vars']['num_options'] = (int)(!empty($_POST['options']) && count($_POST['options']) > 0 ? count($_POST['options']) : 5);

  # Now, we need to see that this message exists, otherwise, you can't edit it.
  $result = $db->query("
    SELECT
      msg.msg_id, msg.topic_id, msg.board_id, msg.member_id, msg.modified_member_id,
      msg.modified_name, msg.modified_time, msg.modified_reason, msg.subject, msg.body, msg.parse_bbc,
      msg.parse_smileys, msg.is_locked AS msg_locked, mem.member_id, mem.loginName, mem.displayName,
      mem2.member_id AS modified_id, mem2.loginName AS modified_loginName,
      mem2.displayName AS modified_displayName, t.topic_id, t.first_msg_id, msg2.msg_id AS first_id,
      msg2.subject AS topic_subject, t.is_locked, t.is_sticky, t.poll_id
    FROM {$db->prefix}messages AS msg
      LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = msg.member_id
      LEFT JOIN {$db->prefix}members AS mem2 ON mem2.member_id = msg.modified_member_id
      LEFT JOIN {$db->prefix}topics AS t ON t.topic_id = msg.topic_id
      LEFT JOIN {$db->prefix}messages AS msg2 ON msg2.msg_id = t.first_msg_id
    WHERE msg.msg_id = %msg_id
    LIMIT 1",
    array(
      'msg_id' => array('int', !empty($_GET['msg']) ? $_GET['msg'] : 0),
    ));

  # Did we get something..?
  if($db->num_rows($result))
  {
    $row = $db->fetch_assoc($result);

    $page['msg_id'] = $row['msg_id'];

    # You could be a moderator. Maybe.
    if($user['is_logged'] && !$user['is_admin'])
    {
      $result = $db->query("
        SELECT
          board_id, member_id
        FROM {$db->prefix}moderators
        WHERE board_id = %board_id AND member_id = %member_id
        LIMIT 1",
        array(
          'board_id' => array('int', $row['board_id']),
          'member_id' => array('int', $user['id']),
        ));

      if($db->num_rows($result))
        $user['is_moderator'] = true;
    }

    # So, can you edit this or not..?
    if(can('edit_any_post') || can('edit_any_post', $row['board_id']) || ((can('edit_own_post') || can('edit_own_post', $row['board_id'])) && $row['member_id'] == $user['id'] && empty($row['msg_locked']) && empty($row['is_locked'])) || $user['is_moderator'])
    {
      # Some stuff for the editor is in the post language file...
      language_load('post');

      $page['title'] = $l['edit_post_title'];

      # Do you have powers?!?!
      $page['show_moderation_options'] = $user['is_moderator'] || $user['is_admin'] || can('use_moderation_options') || can('use_moderation_options', $row['board_id']);

      $page['editing_post'] = true;

      # Setup some things, like the subject, message, etc.
      # Oooo, is this the first message..? And has a poll! :|
      if($row['msg_id'] == $row['first_msg_id'] && !empty($row['poll_id']) && ($user['is_moderator'] || can('edit_any_poll') || can('edit_any_poll', $row['board_id']) || ((can('edit_own_poll') || can('edit_own_poll', $row['board_id'])) && $row['member_id'] == $user['id'])))
      {
        # Gotta get out a couple things.
        $result = $db->query("
          SELECT
            poll_id, question, closed, allowed_votes, expires, allow_change, result_access
          FROM {$db->prefix}topic_polls
          WHERE poll_id = %poll_id
          LIMIT 1",
          array(
            'poll_id' => array('int', $row['poll_id']),
          ));

        # So, do we..?
        if($db->num_rows($result))
        {
          # Do a little switch-a-roo
          $tmp = $row;
          $row = $db->fetch_assoc($result);

          $page['poll'] = true;
          if(!isset($page['post_question']))
            $page['post_question'] = $row['question'];
          if(!isset($page['votes_per_user']))
            $page['votes_per_user'] = (int)$row['allowed_votes'];

          # -1 means don't change it :P
          if(!isset($page['poll_expires']))
            $page['poll_expires'] = -1;
          if(!isset($page['allow_change']))
            $page['allow_change'] = !empty($row['allow_change']);
          if(!isset($page['results']))
            $page['results'] = (int)$row['result_access'];

          if(!isset($_POST['options']))
          {
            # We need to get out all those options...
            $result = $db->query("
              SELECT
                option_id AS id, value
              FROM {$db->prefix}poll_options
              WHERE poll_id = %poll_id",
              array(
                'poll_id' => array('int', $row['poll_id']),
              ));

            # We are going to emulate POST['options'] XD
            $_POST['options'] = array();
            while($option = $db->fetch_assoc($result))
              $_POST['options'][$option['id']] = $option['value'];
            $row = $tmp;
          }
        }
      }

      # Now things like the subject and body...
      # Just maybe... As long as it isn't yet set...
      if(!isset($page['post_subject']))
        $page['post_subject'] = $row['subject'];
      if(!isset($page['post_message']))
        $page['post_message'] = $row['body'];
      if(!isset($page['no_bbc']))
        $page['no_bbc'] = empty($row['parse_bbc']);
      if(!isset($page['no_smileys']))
        $page['no_smileys'] = empty($row['parse_smileys']);
      if(!isset($page['sticky']))
        $page['sticky'] = !empty($row['is_sticky']);
      if(!isset($page['lock']))
        $page['lock'] = !empty($row['is_locked']);
      if(!isset($page['lock_message']))
        $page['lock_message'] = !empty($row['msg_locked']);

      # Who edited it last..?
      if(!empty($row['modified_time']))
        $page['last_edit'] = '<strong>'. $l['last_edited_by']. '</strong> '. (!empty($row['modified_displayName']) ? '<a href="'. $base_url. '/index.php?action=profile;u='. $row['modified_id']. '" target="_blank">'. $row['modified_displayName']. '</a>' : $row['modified_name']). ' '. $l['on']. ' '. timeformat($row['modified_time']). (!empty($row['modified_reason']) ? '<br /><strong>'. $l['reason']. '</strong> '. censor_text($row['modified_reason']) : '');

      # A posting token :P
      require_once($source_dir. '/post.php');
      $page['post_token'] = post_token_create();

      theme_load('post', 'post_make_show');
    }
    else
    {
      $page['title'] = $l['edit_post_permission_denied'];

      theme_load('edit_post', 'edit_post_permission_denied');
    }
  }
  else
  {
    $page['title'] = $l['edit_post_doesnt_exist'];

    theme_load('edit_post', 'edit_post_does_exist');
  }
}

function post_edit_save()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  error_screen('view_forum');

  language_load('edit_post');
  language_load('post');

  # Yeah, check before you post :P
  # Could be GET or POST, surprise me! XD.
  security_sc('request');

  # No errors yet...
  $page['post_errors'] = array();

  # We could have our first error :P
  require_once($source_dir. '/post.php');
  if(empty($_POST['posting_token']) || !post_token_exists($_POST['posting_token']))
    $page['post_errors'][] = $l['invalid_post_token'];

  # The message id is the most important thing!
  if(!empty($_REQUEST['msg']))
  {
    $msg_id = (int)$_REQUEST['msg'];

    # Make sure this post actually exists.
    $result = $db->query("
      SELECT
        msg.msg_id, msg.topic_id, msg.board_id, msg.member_id, msg.is_locked AS msg_locked,
        t.topic_id, t.is_locked, t.first_msg_id, t.poll_id
      FROM {$db->prefix}messages AS msg
        LEFT JOIN {$db->prefix}topics AS t ON t.topic_id = msg.topic_id
      WHERE msg.msg_id = %msg_id
      LIMIT 1",
      array(
        'msg_id' => array('int', $msg_id),
      ));

    # Did we get something..?
    if($db->num_rows($result))
    {
      $row = $db->fetch_assoc($result);

      $page['msg_id'] = $row['msg_id'];

      # You could be a moderator. Maybe.
      if($user['is_logged'] && !$user['is_admin'])
      {
        $result = $db->query("
          SELECT
            board_id, member_id
          FROM {$db->prefix}moderators
          WHERE board_id = %board_id AND member_id = %member_id
          LIMIT 1",
          array(
            'board_id' => array('int', $row['board_id']),
            'member_id' => array('int', $user['id']),
          ));

        if($db->num_rows($result))
          $user['is_moderator'] = true;
      }

      # Now, can you edit this..?
      if(can('edit_any_post') || can('edit_any_post', $row['board_id']) || ((can('edit_own_post') || can('edit_own_post', $row['board_id'])) && $row['member_id'] == $user['id'] && empty($row['msg_locked']) && empty($row['is_locked'])) || $user['is_moderator'])
      {
        # All our options go here :)
        $options = array();

        # Maybe a poll..?
        if($row['msg_id'] == $row['first_msg_id'] && !empty($row['poll_id']) && ($user['is_moderator'] || can('edit_any_poll') || can('edit_any_poll', $row['board_id']) || ((can('edit_own_poll') || can('edit_own_poll', $row['board_id'])) && $row['member_id'] == $user['id'])))
        {
          $options['poll'] = array();

          # We need to have a not empty poll question :P
          $options['poll']['question'] = !empty($_POST['question']) ? $_POST['question'] : '';

          # We need options for the poll...
          $options['poll']['options'] = !empty($_POST['options']) && count($_POST['options']) ? $_POST['options'] : array();

          # Votes per user..?
          $options['poll']['votes_allowed'] = !empty($_POST['votes_per_user']) ? (int)$_POST['votes_per_user'] : 1;

          # When does the poll expire..? (Remember! -1 means leave it alone!)
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

        # Update the post...
        if(empty($_POST['preview']))
          post_edit_update($msg_id, $options['subject'], $options['message'], $options, $page['post_errors']);

        # Any errors occur? Or maybe your previewing?
        if(count($page['post_errors']) || !empty($_POST['preview']))
        {
        
        }
        else
        {
          # You did it! Yay!
        }
      }
      else
      {
        $page['title'] = $l['edit_post_permission_denied'];

        theme_load('edit_post', 'edit_post_permission_denied');
      }
    }
    else
    {
      $page['title'] = $l['edit_post_doesnt_exist'];

      theme_load('edit_post', 'edit_post_does_exist');
    }
  }
  else
    error_screen();
}

function post_edit_update($msg_id, $subject, $message, $options  = array(), &$errors = array())
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Stopping could be bad :P So don't...
  ignore_user_abort(true);

  $msg_id = (int)$msg_id;

  # Remember!!! Like all the other post creating functions and what not, this function
  # is totally 'stupid' and does not pay attention to permissions AT ALL! So whatever
  # is given, no matter the users permissions, will be done!!!

  # A poll being updated? Check it.
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
    if(!isset($options['poll']['expires']) || $options['poll']['expires'] < -1)
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
  $options['msg_locked'] = !empty($options['msg_locked']);

  # Any errors? Let's do this thing!
  if(!count($errors))
  {
    return true;
  }
  else
    return false;
}

function post_edit_ajax()
{
  global $base_url, $db, $l, $page, $settings, $user;

  language_load('edit_post');

  # Can't even view the forum..? Take this!
  if(!can('view_forum'))
  {
    echo json_encode(array('error' => $l['edit_post_access_denied']));
    exit;
  }
}
?>