<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#        Topic Layout template, May 17, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function topic_load_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $page['topic']['subject'], ' <span class="num_replies">', sprintf($l['read_times'], numberformat($page['topic']['num']['views'])), '</span></h1>

      <table width="100%" class="board_tree" cellpadding="5px" cellspacing="0px">
        <tr>
          <td colspan="2"><a href="', $base_url, '/forum.php">', $l['forum_header'], '</a> &gt; <a href="', $base_url, '/forum.php?board=', $page['topic']['board']['id'], '">', $page['topic']['board']['name'], '</a> &gt; <a href="', $base_url, '/forum.php?topic=', $page['topic']['id'], '">', $page['topic']['subject'], '</a></td>
        </tr>
      </table>
      <table width="100%" class="topic_options" cellpadding="6px" cellspacing="0px">
        <tr>
          <td align="left">', $page['index'], '</td><td align="right">', topic_menu(), '</td>
        </tr>
      </table>';

  # Any poll to display..? If you can see it too! Of course.
  if(!empty($page['is_poll']) && $page['can_view_poll'])
  {
    echo '
      <table width="100%" class="poll_display" cellpadding="2px" cellspacing="0px">
        <tr>
          <td>
            <strong>Question</strong>: ', $page['topic']['poll']['question'], $page['topic']['poll']['allowed_votes'] > 1 ? ' (You are allowed to choose '. $page['topic']['poll']['allowed_votes']. ' options)' : '';

    # You haven't voted yet..? And the poll isn't closed you say? And your a member?
    # Then VOTE already! :-P
    if(!empty($page['can_vote']) && empty($page['topic']['poll']['closed']) && $user['is_logged'] && empty($page['display_results']))
    {
      echo '
            <table width="100%" cellpadding="2px" cellspacing="0px">';

      foreach($page['topic']['poll']['options'] as $option)
      {
        echo '
              <tr>
                <td width="3%"><input name="vote[]" id="option_', $option['id'], '" type="', $page['topic']['poll']['allowed_votes'] == 1 ? 'radio' : 'checkbox', '" value="', $option['id'], '" /></td><td><label for="option_', $option['id'], '">', $option['value'], '</label></td>
              </tr>';
      }

      echo '
            </table>';
    }
    elseif(!empty($page['display_results']))
    {
      echo 'viewing results';
    }
    else
    {
      # Uh oh! You can't view the results. It isn't allowed, yet :P
      echo 'uh oh!';
    }

    echo '
          </td>
        </tr>
      </table>';
  }

  echo '
      <table class="post_listing">';

  # Start spewing out those messages!
  while($message = $page['message_callback']())
  {
    echo '
        <tr>
          <th align="center" valign="middle" width="20%">&nbsp;</th>
          <th align="left" valign="middle" width="70%"><a name="msg', $message['id'], '"></a><a href="', $message['href'], '" id="subject_', $message['id'], '">', $message['subject'], '</a> - <span class="normal small">', $message['date'], '</span></th>
          <th align="right" valign="middle" width="10%">', topic_message_menu($message['id'], $message['topic'], $message['can']), '</th>
          <th></th>
        </tr>
        <tr>
          <td align="center" valign="top">
            <a href="', $message['poster']['href'], '" class="bold" style="color: ', $message['poster']['group']['color'], ' !important;">', $message['poster']['name'], '</a><br />
            <span class="small">', $message['poster']['group']['name'], !empty($message['poster']['post_group']['name']) ? '<br />'. $message['poster']['post_group']['name'] : '', '</span><br />', $message['poster']['group']['stars'], '<br />';

    # Any avatar?
    if(!empty($message['poster']['avatar']) && $user['visible']['avatars'])
      echo '
            <img src="', $message['poster']['avatar'], '" alt="" title="" />';

    # Now the posters information... :)
    echo '
            <br />', $l['posts'], ': ', numberformat($message['poster']['num']['posts']), '
          </td>
          <td valign="top" colspan="2">
            <table width="100%" cellpadding="4px" cellspacing="0px">
              <tr>
                <td width="100%" style="padding-bottom: 20px;" colspan="3" id="post_', $message['id'], '">
                  ', $message['body'], '
                </td>
              </tr>
              <tr>
                <td class="italic small" align="left" width="80%">', !empty($message['modified']['is']) ? '&laquo; <strong>'. $l['last_edited_by']. '</strong> '. $message['modified']['link']. (!empty($message['modified']['reason']) ? ' '. $l['on']. ' '. $message['modified']['date']. ' <strong>'. $l['reason']. '</strong> '. $message['modified']['reason'] : ''). ' &raquo;' : '', '</td>
                <td align="right" width="20%">', !empty($message['can']['view_ip']) ? 'IP: '. $message['poster']['ip'] : '', '</td>
              </tr>';

    # Any signature?
    if(!empty($message['poster']['signature']) && $user['visible']['signatures'])
      echo '
              <tr>
                <td width="100%" class="signature" colspan="2">
                  ', $message['poster']['signature'], '
                </td>
              </tr>';

    echo '
            </table>
          </td>
        </tr>';
  }

  echo '
      </table>
      <table width="100%" class="topic_options" cellpadding="6px" cellspacing="0px">
        <tr>
          <td align="left">', $page['index'], '</td><td align="right">', topic_menu(), '</td>
        </tr>
      </table>';

  if(!empty($page['online']))
  {
    echo '
      <table width="100%" class="list_online" cellpadding="4px" cellspacing="0px">
        <tr>
          <th>', sprintf($l['topic_who_viewing'], $page['online']['total_members'], $page['online']['total_guests']), '</th>
        </tr>
        <tr>
          <td class="list">', implode(', ', $page['online']['members']), '</td>
        </tr>
      </table>';
  }

  if($page['show_quick_reply'])
  {
    echo '
      <div class="quick_reply" id="quick_reply">
	      <form action="', $base_url, '/forum.php?action=post2" method="post">
	        <table width="100%" class="center">
	          <tr>
	            <th>', $l['topic_quick_reply'], '</th>
	          </tr>
	          <tr>
	            <td><textarea name="post" class="quick_reply_box" id="post"></textarea></td>
	          </tr>
	          <tr>
	            <td><input name="submit_post" type="submit" value="', $l['post'], '" /> <input name="preview" type="submit" value="', $l['preview'], '" /></td>
	          </tr>
	        </table>
	        <input name="topic" type="hidden" value="', $page['topic']['id'], '" />
	        <input name="posting_token" type="hidden" value="', $page['post_token'], '" />
	      </form>
      </div>';
  }
}

function topic_menu()
{
  global $base_url, $l, $page;

  $options = array();

  if(!empty($page['can_reply']))
    $options[] = '<a href="'. $base_url. '/forum.php?action=post;topic='. $page['topic']['id']. '">'. $l['post_reply']. '</a>';

  return implode(' | ', $options);
}

function topic_message_menu($msg_id, $topic_id, $can)
{
  global $base_url, $l, $page, $settings, $user;

  $icons = array();

  if(!empty($can['quote']))
    $icons[] = '<a href="'. $base_url. '/forum.php?action=post;topic='. $topic_id. ';quote='. $msg_id. '" onclick="return quick_quote('. $msg_id. ');" title="'. $l['quote_this']. '"><img src="'. $settings['images_url']. '/post/quote.png" alt="" title="'. $l['quote_this']. '" /></a>';
  if(!empty($can['edit']))
    $icons[] = '<a href="'. $base_url. '/forum.php?action=edit;msg='. $msg_id. ';sc='. $user['sc']. '" title="'. $l['edit']. '"><img src="'. $settings['images_url']. '/post/edit.png" alt="" title="'. $l['edit']. '" /></a>';
  if(!empty($can['delete']))
    $icons[] = '<a href="'. $base_url. '/forum.php?action=delete;msg='. $msg_id. ';sc='. $user['sc']. '" title="'. $l['delete']. '"><img src="'. $settings['images_url']. '/post/delete.png" alt="" title="'. $l['delete']. '" /></a>';
  if(!empty($can['split']))
    $icons[] = '<a href="'. $base_url. '/forum.php?action=split;msg='. $msg_id. ';sc='. $user['sc']. '" title="'. $l['split']. '"><img src="'. $settings['images_url']. '/post/split.png" alt="" title="'. $l['split']. '" /></a>';

  return implode(' ', $icons);
}
?>