<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           Post.template.php

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Topic() {
global $cmsurl, $settings, $l, $values, $user, $theme_url, $theme_dir;
  echo '
  <script type="text/javascript" src="'.$theme_url.'/'.$settings['theme'].'/scripts/bbcode.js"></script>
  
  <h1>'.$l['post_topic_header'].'</h1>
  ';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  echo '
  <form action="', $cmsurl, 'forum.php?action=post2;board=', $settings['board'], '" method="post" class="write">
  <table id="post" border="0px">
    <tr align="center">
      <td width="20%">', $l['topic_subject'], '</td>
      <td><input name="subject" type="text" size="50" value="'.$values['subject'].'" />
      <td width="20%"></td>
    </tr>
    <tr align="center" valign="middle">
      <td colspan="3">
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[b]\',\'[/b]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_bold.png" alt="'.$l['bbcode_bold'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[i]\',\'[/i]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_italic.png" alt="'.$l['bbcode_italic'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[u]\',\'[/u]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_underline.png" alt="'.$l['bbcode_underline'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[s]\',\'[/s]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_strikethrough.png" alt="'.$l['bbcode_strikethrough'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[img]\',\'[/img]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_image.png" alt="'.$l['bbcode_image'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[url]\',\'[/url]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_link.png" alt="'.$l['bbcode_link'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[code]\',\'[/code]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_code.png" alt="'.$l['bbcode_code'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[quote]\',\'[/quote]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_quote.png" alt="'.$l['bbcode_quote'].'" /></a>
      </td>
    </tr>
    <tr align="center" valign="middle">
      <td colspan="3">  
        ';
	
	include_once $theme_dir.'/'.$settings['theme'].'/emoticons/emoticons.php';
	
	foreach ($smileys as $key => $value) {
	  echo '
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' '.$key.' \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$value.'" title="'.(($key==':P')?'Mr. Yucky-Poo':strtoupper(substr($value,0,1)).str_replace(".bmp","",str_replace(".png","",str_replace(".gif","",str_replace(".jpg","",str_replace(".jpeg","",substr($value,1,strlen($value)))))))).'" alt="'.$key.'"></a>';
	}
	
	echo '
      </td>
    </tr>
    <tr align="center">
      <td colspan="3"><textarea tabindex=2 id="body" name="body" rows="12" cols="60" onclick="if(document.selection){this.selection = document.selection.createRange()}" onkeyup="if(document.selection){this.selection = document.selection.createRange()}" onchange="if(document.selection){this.selection = document.selection.createRange().duplicate()}" onfocus="if(document.selection){this.selection = document.selection.createRange().duplicate()}">'.$values['body'].'</textarea></td>
    </tr>';
  if(canforum('sticky_topic', $settings['board']) || canforum('lock_topic', $settings['board'])) {
    if($values['sticky'])
      $settings['sticky'] = 'checked="checked"';
    else
      $settings['sticky'] = '';
    if($values['locked'])
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
      <td colspan="3"><input name="make_topic" tabindex=3 type="submit" value="'.$l['post_topic_submit'].'"/></td>
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
  <script type="text/javascript" src="'.$theme_url.'/'.$settings['theme'].'/scripts/bbcode.js"></script>
  
  <h1>'.$l['post_reply_header'].'</h1>
  ';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  echo '
  <form action="', $cmsurl, 'forum.php?action=post2;topic=', $settings['topic'], '" method="post" class="write">
  <p><input type="hidden" name="edit" value="'.$settings['edit'].'" /></p>
  <table id="post" border="0px">
    <tr align="center">
      <td width="20%">', $l['topic_subject'], '</td>
      <td><input name="subject" type="text" size="78" value="'.$values['subject'].'" />
      <td width="20%"></td>
    </tr>
   <tr align="center" valign="middle">
      <td colspan="3">
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[b]\',\'[/b]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_bold.png" alt="'.$l['bbcode_bold'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[i]\',\'[/i]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_italic.png" alt="'.$l['bbcode_italic'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[u]\',\'[/u]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_underline.png" alt="'.$l['bbcode_underline'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[s]\',\'[/s]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_strikethrough.png" alt="'.$l['bbcode_strikethrough'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[img]\',\'[/img]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_image.png" alt="'.$l['bbcode_image'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[url]\',\'[/url]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_link.png" alt="'.$l['bbcode_link'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[code]\',\'[/code]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_code.png" alt="'.$l['bbcode_code'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[quote]\',\'[/quote]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_quote.png" alt="'.$l['bbcode_quote'].'" /></a>
      </td>
    </tr>
    <tr align="center" valign="middle">
      <td colspan="3">';
	
	include_once $theme_dir.'/'.$settings['theme'].'/emoticons/emoticons.php';
	
	foreach ($smileys as $key => $value) {
	  echo '
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' '.$key.' \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$value.'" title="'.(($key==':P')?'Mr. Yucky-Poo':strtoupper(substr($value,0,1)).str_replace(".bmp","",str_replace(".png","",str_replace(".gif","",str_replace(".jpg","",str_replace(".jpeg","",substr($value,1,strlen($value)))))))).'" alt="'.$key.'"></a>';
	}
	
    echo '</td>
    </tr>
    <tr align="center">
      <td colspan="3"><textarea name="body" id="body" rows="12" cols="60"onclick="if(document.selection){this.selection = document.selection.createRange()}" onkeyup="if(document.selection){this.selection = document.selection.createRange()}" onchange="if(document.selection){this.selection = document.selection.createRange().duplicate()}" onfocus="if(document.selection){this.selection = document.selection.createRange().duplicate()}">'.$values['subject'].'</textarea></td>
    </tr>';
  if(canforum('sticky_topic', $settings['board']) || canforum('lock_topic', $settings['board'])) {
    if($values['sticky'])
      $settings['sticky'] = 'checked="checked"';
    else
      $settings['sticky'] = '';
    if($values['locked'])
      $settings['locked'] = 'checked="checked"';
    else
      $settings['locked'] = '';
    echo '
    <tr align="center">
      <td colspan="3">', canforum('sticky_topic', $settings['board']) ? $l['topic_sticky'].' <input name="sticky" '. $settings['sticky']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>
    <tr align="center">
      <td colspan="3">', canforum('lock_topic', $settings['board']) ? $l['topic_lock'].' <input name="locked" '. $settings['locked']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>';
  }
  echo '
    <tr align="center">
      <td colspan="3"><input name="post_reply" type="submit" value="'.$l['post_reply_submit'].'"/></td>
    </tr>
  </table>
  </form>
  <script type="text/javascript">
    document.getElementById(\'body\').focus();
  </script>';
}

function Edit() {
global $cmsurl, $settings, $l, $user, $theme_url, $theme_dir;
  echo '
  <script type="text/javascript" src="'.$theme_url.'/'.$settings['theme'].'/scripts/bbcode.js"></script>
  
  <h1>'.$l['post_edit_header'].'</h1>
  ';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  echo '
  <form action="', $cmsurl, 'forum.php?action=post2;topic=', $settings['topic'], '" method="post" class="write">
  <p><input type="hidden" name="edit" value="'.$settings['edit'].'" /></p>
  <table id="post" border="0px">
    <tr align="center">
      <td width="20%">', $l['topic_subject'], '</td>
      <td><input name="subject" type="text" size="78" value="'.$values['subject'].'" />
      <td width="20%"></td>
    </tr>
   <tr align="center" valign="middle">
      <td colspan="3">
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[b]\',\'[/b]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_bold.png" alt="'.$l['bbcode_bold'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[i]\',\'[/i]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_italic.png" alt="'.$l['bbcode_italic'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[u]\',\'[/u]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_underline.png" alt="'.$l['bbcode_underline'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[s]\',\'[/s]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_strikethrough.png" alt="'.$l['bbcode_strikethrough'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[img]\',\'[/img]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_image.png" alt="'.$l['bbcode_image'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[url]\',\'[/url]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_link.png" alt="'.$l['bbcode_link'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[code]\',\'[/code]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_code.png" alt="'.$l['bbcode_code'].'" /></a>
        <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\'[quote]\',\'[/quote]\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/bbc_quote.png" alt="'.$l['bbcode_quote'].'" /></a>
      </td>
    </tr>
    <tr align="center" valign="middle">
      <td colspan="3">';
	
	include_once $theme_dir.'/'.$settings['theme'].'/emoticons/emoticons.php';
	
	foreach ($smileys as $key => $value) {
	  echo '
	    <a href="javascript:void(0);" onClick="add_bbcode(\'body\',\' '.$key.' \',\'\');">
	    <img src="'.$theme_url.'/'.$settings['theme'].'/emoticons/'.$value.'" title="'.(($key==':P')?'Mr. Yucky-Poo':strtoupper(substr($value,0,1)).str_replace(".bmp","",str_replace(".png","",str_replace(".gif","",str_replace(".jpg","",str_replace(".jpeg","",substr($value,1,strlen($value)))))))).'" alt="'.$key.'"></a>';
	}
	
    echo '</td>
    </tr>
    <tr align="center">
      <td colspan="3"><textarea name="body" id="body" rows="12" cols="60"onclick="if(document.selection){this.selection = document.selection.createRange()}" onkeyup="if(document.selection){this.selection = document.selection.createRange()}" onchange="if(document.selection){this.selection = document.selection.createRange().duplicate()}" onfocus="if(document.selection){this.selection = document.selection.createRange().duplicate()}">'.$values['subject'].'</textarea></td>
    </tr>';
  if(canforum('sticky_topic', $settings['board']) || canforum('lock_topic', $settings['board'])) {
    if($values['sticky'])
      $settings['sticky'] = 'checked="checked"';
    else
      $settings['sticky'] = '';
    if($values['locked'])
      $settings['locked'] = 'checked="checked"';
    else
      $settings['locked'] = '';
    echo '
    <tr align="center">
      <td colspan="3">', canforum('sticky_topic', $settings['board']) ? $l['topic_sticky'].' <input name="sticky" '. $settings['sticky']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>
    <tr align="center">
      <td colspan="3">', canforum('lock_topic', $settings['board']) ? $l['topic_lock'].' <input name="locked" '. $settings['locked']. ' type="checkbox" value="1"/>' : '', '</td>
    </tr>';
  }
  echo '
    <tr align="center">
      <td colspan="3"><input name="post_reply" type="submit" value="'.$l['post_edit_submit'].'"/></td>
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