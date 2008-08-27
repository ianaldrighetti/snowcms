<?php
// Forum.template.php by SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Topic() {
global $cmsurl, $settings, $l, $user, $theme_url;
echo '
<form action="', $cmsurl, 'forum.php?action=post2;board=', $settings['board'], '" method="post"  class="write">
<table id="post" border="0px">
  <tr cellspacing="0px" cellpadding="0px">
    <td class="title" colspan="2">', $l['post_newtopic'], '</td>
  </tr>
  <tr align="center">
    <td>', $l['topic_subject'], '</td><td><input name="subject" type="text" size="80" value="', $settings['subject'], '"/>
  </tr>
  <tr align="center" valign="middle">
    <td colspan="2">
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[b]\',\'[/b]\');"><b>Bold</b></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[i]\',\'[/i]\');"><i>Italics</i></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[u]\',\'[/u]\');"><u>Underline</u></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[s]\',\'[/s]\');"><del>Strikethrough</del></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[img]\',\'[/img]\');">Image</a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[url]\',\'[/url]\');">http://Link</a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[tt]\',\'[/tt]\');"><tt>Teletype</tt></a>
	  </td>	
  </tr>
  <tr align="center" valign="middle">
  	<td colspan="2">  
	    
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :) \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/smile.png" alt="smile" title="Smile">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :( \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/sad.png" alt="sad" title="Sad">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :[ \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/angry.png" alt="angry" title="Angry">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :D \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/grin.png" alt="grin" title="Grin">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :O \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/shock.png" alt="shock" title="Shock">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' ;) \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/wink.png" alt="wink" title="Wink">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :P \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/tongue.png" alt="tongue" title="Mr. Yucky-Poo">
	    </a>
	    
	  </td>
  </tr>
  <tr align="center">
    <td colspan="2"><textarea id="body" name="body" rows="12" cols="60" onclick="if(document.selection){this.selection = document.selection.createRange()}" onkeyup="if(document.selection){this.selection = document.selection.createRange()}" onchange="if(document.selection){this.selection = document.selection.createRange().duplicate()}" onfocus="if(document.selection){this.selection = document.selection.createRange().duplicate()}">', $settings['body'], '</textarea></td>
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
      <td>', canforum('post_sticky', $settings['board']) ? $l['topic_sticky'].' <input name="sticky" '. $settings['sticky']. ' type="checkbox" value="1"/>' : '', '</td><td>', canforum('lock_topic', $settings['board']) ? $l['topic_lock'].' <input name="locked" '. $settings['locked']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>';
  }
echo '
  <tr align="center">
    <td colspan="2"><input name="make_topic" type="submit" value="', $l['topic_topic_button'], '"/></td>
  </tr>
</table>
</form>
<script type="text/javascript">
  document.getElementById(\'body\').focus();
</script>';
}

function Reply() {
global $cmsurl, $settings, $l, $user, $theme_url;
echo '
<form action="', $cmsurl, 'forum.php?action=post2;topic=', $settings['topic'], '" method="post" class="write">
<table id="post" border="0px">
  <tr cellspacing="0px" cellpadding="0px">
    <td class="title" colspan="2">', $l['post_postreply'], '</td>
  </tr>
  <tr align="center">
    <td>', $l['topic_subject'], '</td><td><input name="subject" type="text" size="80" value="', $settings['subject'], '"/>
  </tr>
 <tr align="center" valign="middle">
    <td colspan="2">
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[b]\',\'[/b]\');"><b>Bold</b></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[i]\',\'[/i]\');"><i>Italics</i></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[u]\',\'[/u]\');"><u>Underline</u></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[s]\',\'[/s]\');"><del>Strikethrough</del></a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[img]\',\'[/img]\');">Image</a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[url]\',\'[/url]\');">http://Link</a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[tt]\',\'[/tt]\');"><tt>Teletype</tt></a>
	  </td>	
  </tr>
  <tr align="center" valign="middle">
  	<td colspan="2">  
	    
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :) \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/smile.png" alt="smile" title="Smile">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :( \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/sad.png" alt="sad" title="Sad">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :[ \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/angry.png" alt="angry" title="Angry">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :D \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/grin.png" alt="grin" title="Grin">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :O \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/shock.png" alt="shock" title="Shock">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' ;) \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/wink.png" alt="wink" title="Wink">
	    </a>
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' :P \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/tongue.png" alt="tongue" title="Mr. Yucky-Poo">
	    </a>
	    
	  </td>
  </tr>
  <tr align="center">
    <td colspan="2"><textarea name="body" id="body" rows="12" cols="60"onclick="if(document.selection){this.selection = document.selection.createRange()}" onkeyup="if(document.selection){this.selection = document.selection.createRange()}" onchange="if(document.selection){this.selection = document.selection.createRange().duplicate()}" onfocus="if(document.selection){this.selection = document.selection.createRange().duplicate()}">', $settings['body'], '</textarea></td>
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
      <td>', canforum('post_sticky', $settings['board']) ? $l['topic_sticky'].' <input name="sticky" '. $settings['sticky']. ' type="checkbox" value="1"/>' : '', '</td><td>', canforum('lock_topic', $settings['board']) ? $l['topic_lock'].' <input name="locked" '. $settings['locked']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>';
  }
echo '
  <tr align="center">
    <td colspan="2"><input name="post_reply" type="submit" value="', $l['topic_post_button'] ,'"/></td>
  </tr>
</table>
</form>
<script type="text/javascript">
  document.getElementById(\'body\').focus();
</script>';
}
?>