<?php
// Forum.template.php by SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Topic() {
global $cmsurl, $settings, $l, $user;
echo '
<form action="', $cmsurl, 'forum.php?action=post2&board=', $settings['board'], '" method="post">
<table id="post" border="0px">
  <tr cellspacing="0px" cellpadding="0px">
    <td class="title" colspan="2">', $l['post_newtopic'], '</td>
  </tr>
  <tr align="center">
    <td>', $l['topic_subject'], '</td><td><input name="subject" type="text" size="80" value="', $settings['subject'], '"/>
  </tr>
  <tr align="center">
    <td colspan="2"><textarea name="body" rows="12" cols="60">', $settings['body'], '</textarea></td>
  </tr>';
  if(canforum('post_sticky', $_REQUEST['board']) || canforum('lock_topic', $settings['board'])) {
    echo '
    <tr align="center">
      <td>', canforum('post_sticky', $settings['board']) ? $l['topic_sticky'].' <input name="sticky" type="checkbox" value="1"'. $settings['sticky'] ? ' checked="checked"' : ''. '/>' : '', '</td><td>', canforum('lock_topic', $_REQUEST['board']) ? $l['topic_lock']. ' <input name="lock" type="checkbox" type="checkbox" value="1"'. $settings['locked'] ? ' checked="checked"' : ''. '/>' : '', '</td>
    </tr>';
  }
echo '
  <tr align="center">
    <td colspan="2"><input name="make_topic" type="submit" value="', $l['topic_topic_button'], '"/></td>
  </tr>
</table>
</form>';
}

function Reply() {
global $cmsurl, $settings, $l, $user;
echo '
<form action="', $cmsurl, 'forum.php?action=post2&board=', $settings['board'], '" method="post">
<table id="post" border="0px">
  <tr cellspacing="0px" cellpadding="0px">
    <td class="title" colspan="2">', $l['post_postreply'], '</td>
  </tr>
  <tr align="center">
    <td>', $l['topic_subject'], '</td><td><input name="subject" type="text" size="80" value="', $settings['subject'], '"/>
  </tr>
  <tr align="center">
    <td colspan="2"><textarea name="body" rows="12" cols="60">', $settings['body'], '</textarea></td>
  </tr>';
  if(canforum('post_sticky', $settings['board']) || canforum('lock_topic', $settings['board'])) {
    echo '
    <tr align="center">
      <td>', canforum('post_sticky', $settings['board']) ? $l['topic_sticky'].' <input name="sticky" type="checkbox" value="1"'. $settings['sticky'] ? ' checked="checked"' : ''. '/>' : '', '</td><td>', canforum('lock_topic', $settings['board']) ? $l['topic_lock']. ' <input name="lock" type="checkbox" type="checkbox" value="1"'. $settings['locked'] ? ' checked="checked"' : ''. '/>' : '', '</td>
    </tr>';
  }
echo '
  <tr align="center">
    <td colspan="2"><input name="make_topic" type="submit" value="', $l['topic_post_button'] ,'"/></td>
  </tr>
</table>
</form>';
}
?>