<?php
// Forum.template.php by SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Topic() {
global $cmsurl, $settings, $l, $user, $theme_url, $theme_dir;
echo '
<form action="', $cmsurl, 'forum.php?action=post2;board=', $settings['board'], '" method="post"  class="write">
<table id="post" border="0px">
  <tr cellspacing="0px" cellpadding="0px">
    <td class="title" colspan="3">', $l['post_newtopic'], '</td>
  </tr>
  <tr align="center">
    <td width="20%">', $l['topic_subject'], '</td>
    <td><input name="subject" type="text" size="78" value="', $settings['subject'], '" />
    <td width="20%"></td>
  </tr>
  <tr align="center" valign="middle">
    <td colspan="3">
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[b]\',\'[/b]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_bold.png" alt="'.$l['forum_post_bold'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[i]\',\'[/i]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_italic.png" alt="'.$l['forum_post_italic'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[u]\',\'[/u]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_underline.png" alt="'.$l['forum_post_underline'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[s]\',\'[/s]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_strikethrough.png" alt="'.$l['forum_post_strikethrough'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[img]\',\'[/img]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_image.png" alt="'.$l['forum_post_image'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[url]\',\'[/url]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_link.png" alt="'.$l['forum_post_link'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[code]\',\'[/code]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_code.png" alt="'.$l['forum_post_code'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[quote]\',\'[/quote]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_quote.png" alt="'.$l['forum_post_quote'].'" /></a>
	  </td>	
  </tr>
  <tr align="center" valign="middle">
  	<td colspan="3">  
	    ';
	
	include_once $theme_dir.'/'.$settings['theme'].'/emoticons/emoticons.php';
	
	foreach ($smileys as $key => $value) {
	  echo '
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' '.$key.' \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$value.'" alt="'.$key.'"></a>';
	}
	
	echo '
	  </td>
  </tr>
  <tr align="center">
    <td colspan="3"><textarea id="body" name="body" rows="12" cols="60" onclick="if(document.selection){this.selection = document.selection.createRange()}" onkeyup="if(document.selection){this.selection = document.selection.createRange()}" onchange="if(document.selection){this.selection = document.selection.createRange().duplicate()}" onfocus="if(document.selection){this.selection = document.selection.createRange().duplicate()}">', $settings['body'], '</textarea></td>
  </tr>';
  if(canforum('post_sticky', $settings['board']) || canforum('lock_topic', $settings['board'])) {
    if($settings['sticky'])
      $settings['sticky'] = 'checked="checked"';
    else
      $settings['sticky'] = '';
    if($settings['locked'])
      $settings['locked'] = 'checked="checked"';
    else
      $settings['locked'] = '';
    echo '
    <tr align="center">
      <td colspan="3">', canforum('post_sticky', $settings['board']) ? $l['topic_sticky'].' <input name="sticky" '. $settings['sticky']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>
    <tr align="center">
      <td colspan="3">', canforum('lock_topic', $settings['board']) ? $l['topic_lock'].' <input name="locked" '. $settings['locked']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>';
  }
echo '
  <tr align="center">
    <td colspan="3"><input name="make_topic" type="submit" value="', $l['topic_topic_button'], '"/></td>
  </tr>
</table>
</form>
<script type="text/javascript">
  document.getElementById(\'body\').focus();
</script>';
}

function Reply() {
global $cmsurl, $settings, $l, $user, $theme_url, $theme_dir;
echo '
<form action="', $cmsurl, 'forum.php?action=post2;topic=', $settings['topic'], '" method="post" class="write">
<p><input type="hidden" name="edit" value="'.$settings['edit'].'" /></p>
<table id="post" border="0px">
  <tr cellspacing="0px" cellpadding="0px">
    <td class="title" colspan="3">', $l['post_postreply'], '</td>
  </tr>
  <tr align="center">
    <td width="20%">', $l['topic_subject'], '</td>
    <td><input name="subject" type="text" size="78" value="', $settings['subject'], '" />
    <td width="20%"></td>
  </tr>
 <tr align="center" valign="middle">
    <td colspan="3">
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[b]\',\'[/b]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_bold.png" alt="'.$l['forum_post_bold'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[i]\',\'[/i]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_italic.png" alt="'.$l['forum_post_italic'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[u]\',\'[/u]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_underline.png" alt="'.$l['forum_post_underline'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[s]\',\'[/s]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_strikethrough.png" alt="'.$l['forum_post_strikethrough'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[img]\',\'[/img]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_image.png" alt="'.$l['forum_post_image'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[url]\',\'[/url]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_link.png" alt="'.$l['forum_post_link'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[code]\',\'[/code]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_code.png" alt="'.$l['forum_post_code'].'" /></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[quote]\',\'[/quote]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_quote.png" alt="'.$l['forum_post_quote'].'" /></a>
	  </td>	
  </tr>
  <tr align="center" valign="middle">
  	<td colspan="2">';
	
	include_once $theme_dir.'/'.$settings['theme'].'/emoticons/emoticons.php';
	
	foreach ($smileys as $key => $value) {
	  echo '
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' '.$key.' \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$value.'" alt="'.$key.'"></a>';
	}
	
	echo '</td>
  </tr>
  <tr align="center">
    <td colspan="3"><textarea name="body" id="body" rows="12" cols="60"onclick="if(document.selection){this.selection = document.selection.createRange()}" onkeyup="if(document.selection){this.selection = document.selection.createRange()}" onchange="if(document.selection){this.selection = document.selection.createRange().duplicate()}" onfocus="if(document.selection){this.selection = document.selection.createRange().duplicate()}">', $settings['body'], '</textarea></td>
  </tr>';
  if(canforum('post_sticky', $settings['board']) || canforum('lock_topic', $settings['board'])) {
    if($settings['sticky'])
      $settings['sticky'] = 'checked="checked"';
    else
      $settings['sticky'] = '';
    if($settings['locked'])
      $settings['locked'] = 'checked="checked"';
    else
      $settings['locked'] = '';
    echo '
    <tr align="center">
      <td colspan="3">', canforum('post_sticky', $settings['board']) ? $l['topic_sticky'].' <input name="sticky" '. $settings['sticky']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>
    <tr align="center">
      <td colspan="3">', canforum('lock_topic', $settings['board']) ? $l['topic_lock'].' <input name="locked" '. $settings['locked']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>';
  }
echo '
  <tr align="center">
    <td colspan="3"><input name="post_reply" type="submit" value="', $l['topic_post_button'] ,'"/></td>
  </tr>
</table>
</form>
<script type="text/javascript">
  document.getElementById(\'body\').focus();
</script>';
}

function CantPost() {

}
?>