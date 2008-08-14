<?php
// Forum.template.php by SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Topic() {
global $cmsurl, $settings, $l, $user;
$l['post_newtopic'] = 'Start new topic';
echo '
<form action="', $cmsurl, 'forum.php?action=post&board=', $settings['board'], '" method="post">
<table id="post" border="0px">
  <tr cellspacing="0px" cellpadding="0px">
    <td class="title" colspan="2">', $l['post_newtopic'], '</td>
  </tr>
  <tr align="center">
    <td>Subject:</td><td><input name="subject" type="text" size="80" value="', $settings['subject'], '"/>
  </tr>
  <tr align="center">
    <td colspan="2"><textarea name="body" rows="12" cols="60">', $settings['body'], '</textarea></td>
  </tr>';
  if(canforum('post_sticky', $_REQUEST['board']) || canforum('lock_topic', $_REQUEST['board'])) {
    echo '
    <tr align="center">
      <td>', canforum('post_sticky', $_REQUEST['board']) ? 'Make topic Sticky <input name="sticky" type="checkbox" value="1"', $settings['sticky'] ? ' checked="checked"' : '', '/>' : '', '</td><td>', canforum('lock_topic', $_REQUEST['board']) ? 'Lock topic <input name="lock" type="checkbox" type="checkbox" value="1"', $settings['locked'] ? ' checked="checked"' : '', '/>' : '', '</td>
    </tr>';
  }
echo '
  <tr align="center">
    <td colspan="2"><input name="make_topic" type="submit" value="Post Topic"/></td>
  </tr>
</table>
</form>';
}
?>