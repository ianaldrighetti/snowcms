<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#        Topic Layout template, May 17, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function recent_posts_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['recent_posts_header'], '</h1>

      <p>', $l['recent_posts_desc'], '</p>
      
      <br />';

  echo '
      <table width="100%" class="topic_options" cellpadding="6px" cellspacing="0px">
        <tr>
          <td align="left">', $page['index'], '</td><td align="right"></td>
        </tr>
      </table>
      <table width="100%" class="post_listing" cellpadding="2px" cellspacing="0px">';

  # Start spewing out those messages!
  while($message = $page['message_callback']())
  {
    echo '
        <tr>
          <th align="center" valign="middle" width="20%">&nbsp;</th>
          <th align="left" valign="middle" width="70%"><a name="msg', $message['id'], '"></a><a href="', $message['href'], '" id="subject_', $message['id'], '">', $message['subject'], '</a> - <span class="normal small">', $message['date'], '</span></th>
          <th align="right" valign="middle" width="10%">', recent_message_menu($message['id'], $message['topic'], $message['can']), '</th>
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
            <br />', $l['posts'], ': ', $message['poster']['num']['posts'], '
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
          <td align="left">', $page['index'], '</td><td align="right"></td>
        </tr>
      </table>';
}

function recent_message_menu($msg_id, $topic_id, $can)
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