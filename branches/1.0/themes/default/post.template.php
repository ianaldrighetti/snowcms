<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#         Post Layout template, June 23, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function post_make_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $page['header_text'], '</h1>
      <p>', $page['subheader_text'], '</p>

      <div id="post_preview">';

  # We could be displaying a preview (For those no-js browsers .-.)
  if(!empty($page['display_preview']))
    echo '
        <div class="preview_header">', $page['preview_subject'], '</div>
        <div class="preview_body">
          ', $page['preview_body'], '
        </div>';

  echo '
      </div>
      <div id="post_errors">';

  # Any errors to display?
  if(!empty($page['post_errors']))
  {
    foreach($page['post_errors'] as $error)
      echo '
        <p class="center error">', $error, '</p>';
  }

  echo '
      </div>

      <form action="', $base_url, '/forum.php?action=', !empty($page['editing_post']) ? 'edit2' : 'post2', '" method="post">
        <fieldset>
          <table align="center" width="90%">';

  # Posting a poll..?
  if(!empty($page['poll']))
  {
    echo '
            <tr>
              <td align="right" width="15%" class="bold">', $l['question'], '</td>
              <td align="left" width="90%"><input name="question" id="question" type="text"', !empty($page['question_error']) ? ' class="red_border" ' : ' ', 'value="', !empty($page['post_question']) ? $page['post_question'] : '', '" size="80" /></td>
            </tr>
            <tr>
              <td align="right" valign="top" width="15%" class="bold">&nbsp;</td>
              <td align="left" width="90%">';

    # Some options already filled out?
    if(!empty($_POST['options']) && count($_POST['options']) > 0)
    {
      $i = 0;
      foreach($_POST['options'] as $option)
      {
        echo '
                ', ($i > 0) ? '<br />' : '', '<label for="option_', $i, '">', sprintf($l['option'], $i + 1), '</label> <input name="options[]" id="option_', $i, '" type="text" value="', htmlspecialchars($option, ENT_QUOTES, 'UTF-8'), '" />';
        $i++;
      }
    }
    else
      for($i = 0; $i < 5; $i++)
        echo '
                ', ($i > 0) ? '<br />' : '', '<label for="option_', $i, '">', sprintf($l['option'], $i + 1), '</label> <input name="options[]" id="option_', $i, '" type="text" value="" />';

    echo '
                <span id="more_options"></span> [<a href="javascript:void(0);" onclick="addPollOption();">', $l['add_option'], '</a>]
              </td>
            </tr>
            <tr>
              <td align="right" valign="middle" width="15%" class="bold">', $l['poll_settings'], '</td>
              <td align="left" width="90%">
                <table align="left">
                  <tr>
                    <td>', $l['votes_per_user'], '</td><td><input name="votes_per_user" type="text" value="', !empty($page['votes_per_user']) ? $page['votes_per_user'] : 1, '" size="2" /></td>
                  </tr>
                  <tr>
                    <td>', $l['poll_expires'], '<br /><span class="small">', $l['poll_expires_subtext'], '</span></td><td><input name="poll_expires" onchange="expiration_changed(this.form);" type="text" value="', !empty($page['poll_expires']) ? $page['poll_expires'] : 0, '" size="2" /></td>
                  </tr>
                  <tr>
                    <td>', $l['allow_vote_change'], '</td><td><input name="allow_change" type="checkbox" value="1" title="', $l['yes'], '"', !empty($page['allow_change']) ? ' checked="checked"' : '', ' /></td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <input name="results_access" id="results_anyone" type="radio" value="1"', !empty($page['results']) && $page['results'] == 1 ? ' checked="checked"' : '', ' /> <label for="results_anyone">', $l['results_anyone'], '</label><br />
                      <input name="results_access" id="results_after_vote" type="radio" value="2"', !empty($page['results']) && $page['results'] == 2 ? ' checked="checked"' : '', ' /> <label for="results_after_vote">', $l['results_after_vote'], '</label><br />
                      <input name="results_access" id="results_after_expired" type="radio" value="3"', !empty($page['results']) && $page['results'] == 3 ? ' checked="checked"' : (empty($page['poll_expires']) || $page['poll_expires'] < 1 ? ' disabled="disabled"' : ''), ' /> <label for="results_after_expired">', $l['results_after_expired'], '</label><br />
                    </td>
                  </tr>
                </table>
              </td>
            </tr>';
  }

  echo '
            <tr>
              <td align="right" width="15%" class="bold">', $l['subject'], '</td>
              <td align="left" width="90%"><input name="subject" id="post_subject" type="text"', !empty($page['subject_error']) ? ' class="red_border" ' : ' ', 'value="', !empty($page['post_subject']) ? $page['post_subject'] : '', '" size="80" /></td>
            </tr>
            <tr>
              <td align="center" colspan="2">
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[b]\', \'[/b]\');"><img src="', $settings['images_url'], '/post/bold.png" alt="" title="', $l['bold'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[i]\', \'[/i]\');"><img src="', $settings['images_url'], '/post/italic.png" alt="" title="', $l['italic'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[u]\', \'[/u]\');"><img src="', $settings['images_url'], '/post/underline.png" alt="" title="', $l['underline'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[s]\', \'[/s]\');"><img src="', $settings['images_url'], '/post/strikethrough.png" alt="" title="', $l['strikethrough'], '" /></a>
                &nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[pre]\', \'[/pre]\');"><img src="', $settings['images_url'], '/post/align_pre.png" alt="" title="', $l['align_pre'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[left]\', \'[/left]\');"><img src="', $settings['images_url'], '/post/align_left.png" alt="" title="', $l['align_left'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[center]\', \'[/center]\');"><img src="', $settings['images_url'], '/post/align_center.png" alt="" title="', $l['align_center'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[right]\', \'[/right]\');"><img src="', $settings['images_url'], '/post/align_right.png" alt="" title="', $l['align_right'], '" /></a>
                &nbsp;&nbsp;&nbsp;
                <select onChange="if(this.value != 0) { ta.surroundSelection(\'[font=\' + this.value + \']\', \'[/font]\'); this.options[0].selected = true; }">
                  <option value="0">', $l['select_font'], '</option>
                  <option value="Arial">Arial</option>
                </select>
                <select onChange="if(this.value != 0) { ta.surroundSelection(\'[size=\' + this.value + \']\', \'[/size]\'); this.options[0].selected = true; }">
                  <option value="0">', $l['select_size'], '</option>
                  <option value="8">8</option>
                  <option value="10">10</option>
                  <option value="12">12</option>
                  <option value="14">14</option>
                  <option value="16">16</option>
                  <option value="18">18</option>
                  <option value="24">24</option>
                  <option value="32">32</option>
                </select>
              </td>
            </tr>
            <tr>
              <td align="center" colspan="2">
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[img]\', \'[/img]\');"><img src="', $settings['images_url'], '/post/image.png" alt="" title="', $l['image'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[url=\', \'][/url]\');"><img src="', $settings['images_url'], '/post/link.png" alt="" title="', $l['link'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[email]\', \'[/email]\');"><img src="', $settings['images_url'], '/post/email.png" alt="" title="', $l['email'], '" /></a>
                &nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[sup]\', \'[/sup]\');"><img src="', $settings['images_url'], '/post/superscript.png" alt="" title="', $l['superscript'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[sub]\', \'[/sub]\');"><img src="', $settings['images_url'], '/post/subscript.png" alt="" title="', $l['subscript'], '" /></a>
                &nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[code]\', \'[/code]\');"><img src="', $settings['images_url'], '/post/code.png" alt="" title="', $l['code'], '" /></a>
                <a href="javascript:void(0);" onclick="ta.surroundSelection(\'[quote]\', \'[/quote]\');"><img src="', $settings['images_url'], '/post/quote.png" alt="" title="', $l['quote'], '" /></a>
              </td>
            </tr>
            <tr>
              <td align="center" colspan="2">
                <a name="message"></a>
                <textarea name="post" id="post" class="post_editor', !empty($page['post_error']) ? ' red_border' : '', '" style="width: 65%; height: 150px;">', !empty($page['post_message']) ? $page['post_message'] : '', '</textarea>
              </td>
            </tr>
            <tr>
              <td align="center" colspan="2">
                <input name="submit_post" type="submit" value="', empty($page['editing_post']) ? $l['post'] : $l['edit_post'], '" />
                <input name="preview" type="submit" value="', $l['preview'], '" onclick="preview_message(this.form); return false;" />
                <span id="hide_preview_button" style="display: none;"><input type="button" value="', $l['hide_preview'], '" onclick="hide_preview(this.form); return false;" /></span>
              </td>
            </tr>';

  # Last edit..? (If editing, of course)
  if(!empty($page['editing_post']) && !empty($page['last_edit']))
  {
    echo '
            <tr>
              <td align="left" colspan="2">', $page['last_edit'], '<br /><br /></td>
            </tr>';
  }

  echo '
            <tr>
              <td align="left" colspan="2">
                <p><a href="javascript:void(0);" onclick="_.toggle(_.G(\'additional_options\'));"><img src="', $settings['images_url'], '/post/additional_options.png" alt="" title="', $l['additional_options'], '" /> ', $l['additional_options'], '</a></p>
                <div id="additional_options" style="display: none;">
                  <table align="left" width="100%">
                    <tr>
                      <td><input name="return" id="return" type="checkbox" value="1"', !empty($page['return']) ? ' checked="checked"' : '', '/> <label for="return">', $l['return_to_message'], '</label></td>
                      <td><input name="no_bbc" id="no_bbc" type="checkbox" value="1"', !empty($page['no_bbc']) ? ' checked="checked"' : '', '/> <label for="no_bbc">', $l['dont_parse_bbc'], '</label></td>
                    </tr>
                    <tr>
                      <td', empty($page['show_moderation_options']) ? ' colspan="2" align="left"' : '', '><input name="no_smileys" id="no_smileys" type="checkbox" value="1"', !empty($page['no_smileys']) ? ' checked="checked"' : '', '/> <label for="no_smileys">', $l['dont_parse_smileys'], '</label></td>';

  if(!empty($page['show_moderation_options']))
    echo '
                      <td><input name="sticky" id="sticky" type="checkbox" value="1"', !empty($page['sticky']) ? ' checked="checked"' : '', '/> <label for="sticky">', $l['sticky_topic'], '</label></td>
                    </tr>
                    <tr>
                      <td', empty($page['editing_post']) ? ' colspan="2"' : '', '><input name="lock" id="lock" type="checkbox" value="1"', !empty($page['lock']) ? ' checked="checked"' : '', '/> <label for="lock">', $l['lock_topic'], '</label></td>', !empty($page['editing_post']) ? '<td><input name="lock_message" id="lock_message" type="checkbox" value="1"'. (!empty($page['lock_message']) ? ' checked="checked"' : ''). ' /> <label for="lock_message">'. $l['lock_message']. '</label></td>' : '';

  echo '
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
          </table>
        </fieldset>';

  # So, anything extra? :P Oh, say the board id?
  if(!empty($page['board_id']))
    echo '
      <input name="board" type="hidden" value="', $page['board_id'], '" />';
  elseif(!empty($page['topic_id']))
    echo '
      <input name="topic" type="hidden" value="', $page['topic_id'], '" />
      <input name="num_replies" type="hidden" value="', isset($page['num_replies']) ? $page['num_replies'] : '', '" />';
  else
    echo '
      <input name="msg" type="hidden" value="', $page['msg_id'], '" />';

  # Posting a poll?
  if(!empty($page['poll']))
    echo '
      <input name="poll" type="hidden" value="1" />';

  echo '
      <input name="posting_token" type="hidden" value="', $page['post_token'], '" />
      <input name="sc" type="hidden" value="', $user['sc'], '" />
      </form>';

  # Last 6 posts! :)
  if(!empty($page['last_posts']))
  {
    # Display those recent ones! XD.
    echo '
      <table align="center" class="last_posts" width="90%" cellpadding="0" cellspacing="0">
        <tr>
          <th>', sprintf($l['last_posts'], count($page['last_posts'])), '</th>
        </tr>';

    # Now show those posts! XD Not much to it though, yay.
    foreach($page['last_posts'] as $message)
    {
      echo '
        <tr>
          <td class="post_header"><div style="float: left;">', $l['posted_by'], ' ', $message['poster']['name'], ' ', $l['on'], ' ', $message['posted'], '</div><div style="float: right;"><a href="#message" onclick="quick_quote_reply(', $message['id'], ');" title="', $l['quote_this'], '">', $l['quote_this'], '</a></div><div style="clear: both;"></div></td>
        </tr>
        <tr>
          <td class="post">
            ', $message['body'], '
          </td>
        </tr>';
    }

    echo '
      </table>';
  }
}

function post_make_show_invalid()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display invalid topic message
}
?>