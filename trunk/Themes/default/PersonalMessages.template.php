<?php
// default/PersonalMessages.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Inbox() {
global $l, $settings, $user, $cmsurl, $theme_url;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_inbox_header'].'</h1>
  
  '.PMBar();
  
  if (@$_SESSION['error'])
	 echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  else
    echo '<p>'.$l['pm_inbox_desc'].'</p>';
  
  echo '
  
  <table width="100%" style="text-align: center">
    <tr>
      <th style="border-style: solid; border-width: 1px; width: 45%">'.$l['pm_inbox_subject'].'</th>
      <th style="border-style: solid; border-width: 1px; width: 15%">'.$l['pm_inbox_from'].'</th>
      <th style="border-style: solid; border-width: 1px; width: 40%">'.$l['pm_inbox_received'].'</th>
      <th></th>
    </tr>';
  
  foreach ($settings['page']['messages'] as $message) {
    echo '
    <tr>
      <td><a href="'.$cmsurl.'forum.php?action=pm;msg='.$message['id'].'">'.$message['subject'].'</a></td>
      <td><a href="'.$cmsurl.'index.php?action=profile;u='.$message['from_id'].'">'.$message['from'].'</a></td>
      <td>'.$message['time'].'</td>
      ';
    if (can('pm_delete'))
      echo '<td><a href="'.$cmsurl.'forum.php?action=pm;did='.$message['id'].';sc='.$user['sc'].'" onclick="return confirm(\'', $l['pm_inbox_delete_areyousure'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['pm_inbox_delete'].'" width="15" height="15" style="border: 0" /></a></td>';
    else
      echo '<td></td>';
    echo '
    </tr>
    ';
  }
  
  echo '</table>
  
  </td></tr></table>';
}

function InboxEmpty() {
global $l, $settings, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_inbox_empty_header'].'</h1>
  
  '.PMBar();
  
  if (@$_SESSION['error'])
	 echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  else
    echo '<p>'.$l['pm_inbox_empty_desc'].'</p>';
  
  echo '
  
  </td></tr></table>';
}

function Message() {
global $l, $settings, $cmsurl;
  
  $message = $settings['page']['message'];
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_message_header'].'</h1>
  
  '.PMBar();
  
  if (@$_SESSION['error'])
	 echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  else
    echo '<p>'.$l['pm_message_desc'].'</p>';
  
  echo '
  
  <table width="100%">
    <tr>
      <td style="border-style: solid; border-width: 1px">
      '.
      str_replace('%subject%','<b>'.$message['subject'].'</b>',
      str_replace('%from%','<a href="'.$cmsurl.'index.php?action=profile;u='.$message['from_id'].'">'.$message['from'].'</a>',
      str_replace('%time%','<b>'.$message['time'].'</b>',
      $l['pm_message_heading'])))
      .'
      </td>
    </tr>
    <tr>
      <td>
      '.$message['body'].'
      </td>
    </tr>
  </table>
  
  </td></tr></table>';
  
}

function Outbox() {
global $l, $cmsurl, $settings, $user, $theme_url;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_outbox_header'].'</h1>
  
  '.PMBar();
  
  if (@$_SESSION['error'])
	 echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  else
    echo '<p>'.$l['pm_outbox_desc'].'</p>';
  
  echo '
  
  <table width="100%" style="text-align: center">
    <tr>
      <th style="border-style: solid; border-width: 1px; width: 45%">'.$l['pm_outbox_subject'].'</th>
      <th style="border-style: solid; border-width: 1px; width: 15%">'.$l['pm_outbox_to'].'</th>
      <th style="border-style: solid; border-width: 1px; width: 40%">'.$l['pm_outbox_sent'].'</th>
      <th></th>
    </tr>';
  
  foreach ($settings['page']['messages'] as $message) {
    echo '
    <tr>
      <td><a href="'.$cmsurl.'forum.php?action=pm;msg='.$message['id'].'">'.$message['subject'].'</a></td>
      <td><a href="'.$cmsurl.'index.php?action=profile;u='.$message['from_id'].'">'.$message['to'].'</a></td>
      <td>'.$message['time'].'</td>
      ';
    if (can('pm_delete'))
      echo '<td><a href="'.$cmsurl.'forum.php?action=pm;sa=outbox;did='.$message['id'].';sc='.$user['sc'].'" onclick="return confirm(\'', $l['pm_outbox_delete_areyousure'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['pm_outbox_delete'].'" width="15" height="15" style="border: 0" /></a></td>';
    else
      echo '<td></td>';
    echo '
    </tr>
    ';
  }
  
  echo '</table>
  
  </td></tr></table>';
}

function OutboxEmpty() {
  
}

function Compile() {
global $l, $settings, $cmsurl, $theme_dir, $theme_url;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_compile_header'].'</h1>
  
  '.PMBar();
  
  if (@$_SESSION['error'])
	 echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  else
    echo '<p>'.$l['pm_compile_desc'].'</p>';
  
  echo '
  
  <form action="'.$cmsurl.'forum.php?action=pm;sa=compile" method="post">
    <table id="post" border="0px">
      <tr cellspacing="0px" cellpadding="0px">
        <td class="title" colspan="3">'.$l['pm_compile_header'].'</td>
      </tr>
      <tr>
        <td width="20%" style="text-align: right; padding-right: 5px">'.$l['pm_compile_to'].':</td>
        <td><input name="to" value="'.$settings['page']['to'].'" />
        <td width="20%"></td>
      </tr>
      <tr>
        <td width="20%" style="text-align: right; padding-right: 5px">'.$l['pm_compile_subject'].':</td>
        <td style="text-align: center"><input name="subject" value="'.$settings['page']['subject'].'" size="78" />
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
        <td colspan="3"><textarea id="body" name="body" rows="12" cols="60" onclick="if(document.selection){this.selection = document.selection.createRange()}" onkeyup="if(document.selection){this.selection = document.selection.createRange()}" onchange="if(document.selection){this.selection = document.selection.createRange().duplicate()}" onfocus="if(document.selection){this.selection = document.selection.createRange().duplicate()}"></textarea></td>
      </tr>
      <tr align="center">
        <td colspan="3"><input name="make_topic" type="submit" value="'.$l['pm_compile_submit'].'"/></td>
      </tr>
    </table>
  </form>
  <script type="text/javascript">
    document.getElementById(\'body\').focus();
  </script>
  
  </td></tr></table>';
}

function NotAllowed() {
global $l, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_notallowed_header'].'</h1>
  
  '.PMBar().'
  
  <p>'.$l['pm_notallowed_desc'].'</p>
  
  </td></tr></table>';
}

function CompileNotAllowed() {
global $l, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_compile_notallowed_header'].'</h1>
  
  '.PMBar().'
  
  <p>'.$l['pm_compile_notallowed_desc'].'</p>
  
  </td></tr></table>';
}

function PMBar() {
global $l, $cmsurl;
  
  if (!can('pm_view'))
    return '';
  
  $return = '
  <div style="text-align: center">
    <form action="'.$cmsurl.'forum.php?action=pm" method="post" style="display: inline">
      <p style="display: inline">
        <input type="hidden" name="redirect" value="true" />
        <input type="submit" value="'.$l['pm_button_inbox'].'" />
      </p>
    </form>
    
    ';
  
  if (can('pm_compile'))
  $return .= '<form action="'.$cmsurl.'forum.php?action=pm;sa=compile" method="post" style="display: inline">
      <p style="display: inline">
        <input type="hidden" name="redirect" value="true" />
        <input type="submit" value="'.$l['pm_button_compile'].'" />
        </p>
    </form>';
  
  $return .= '
    
    <form action="'.$cmsurl.'forum.php?action=pm;sa=outbox" method="post" style="display: inline">
      <p style="display: inline">
        <input type="hidden" name="redirect" value="true" />
        <input type="submit" value="'.$l['pm_button_outbox'].'" />
        </p>
    </form>
  </div>
  ';
  
  return $return;
}
?>