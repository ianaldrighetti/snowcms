<?php
// default/Settings.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user;
  
}

function Basic() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['basicsettings_header'].'</h1>
  
  <p>'.$l['basicsettings_desc'].'</p>';
  echo '
  <form action="" method="post">
    <table>';
  foreach($settings['page']['settings'] as $setting => $info) {
    if($info['type']=='text') {
      $field = '<input name="'.$setting.'" type="text" value="'.$settings[$setting].'"/>';
    }
    elseif ($info['type'] == 'select') {
      $field = "\n".'          <select name="'.$setting.'">'."\n";
      $i = 0;
      while ($i < count($info['values'])) {
       if ($settings[$setting] != $info['values'][$i+1])
         $field .= '            <option value="'.$info['values'][$i+1].'">'.$info['values'][$i].'</option>'."\n";
       else
         $field .= '            <option value="'.$info['values'][$i+1].'" selected="selected">'.$info['values'][$i].'</option>'."\n";
       $i += 2;
      }
      $field .= '          </select>'."\n".'        ';
    }
    echo '
      <tr>
        <td>'.$l['basicsettings_'.$setting].'</td><td>'.$field.'</td>
      </tr>';
  }
  echo '
      <tr>
        <td>&nbsp;</td><td><input name="update" type="submit" value="'.$l['basicsettings_update'].'"/></td>
      </tr>
    </table>
  </form>';
}

function ManageMailSettings() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['mailsettings_header'].'</h1>
  ';
  
  if (@$_SESSION['error'])
	  echo '<p>'.$_SESSION['error'].'</p>
	';
  
  echo '<p>'.$l['mailsettings_desc'].'</p>
  
  <form action="" method="post">
  
  <script type="text/javascript">
  function smtp_clicked() {
    document.getElementById(\'smtp_host\').disabled = false;
    document.getElementById(\'smtp_port\').disabled = false;
    document.getElementById(\'smtp_user\').disabled = false;
    document.getElementById(\'smtp_pass\').disabled = false;
    document.getElementById(\'smtp_pass_2\').disabled = false;
  }
  
  function sendmail_clicked() {
    document.getElementById(\'smtp_host\').disabled = true;
    document.getElementById(\'smtp_port\').disabled = true;
    document.getElementById(\'smtp_user\').disabled = true;
    document.getElementById(\'smtp_pass\').disabled = true;
    document.getElementById(\'smtp_pass_2\').disabled = true;
  }
  </script>
  
  <p><input type="hidden" name="change_settings" value="true" /></p>
  
  <table width="100%">
  <tr>
    ';
  if ($settings['mail_with_fsockopen'])
    echo '<td width="50%"><input type="radio" name="mail_with_fsockopen" id="smtp" value="1" checked="checked" onclick="smtp_clicked()" /> <label for="smtp">'.$l['mailsettings_smtp'].'</label></td>
    <td><input type="radio" name="mail_with_fsockopen" id="sendmail" value="0" onclick="sendmail_clicked()" /> <label for="sendmail">'.$l['mailsettings_sendmail'].'</label></td>
  ';
  else
    echo '<td width="50%"><input type="radio" name="mail_with_fsockopen" id="smtp" value="1" onclick="smtp_clicked()" /> <label for="smtp">'.$l['mailsettings_smtp'].'</label></td>
    <td><input type="radio" name="mail_with_fsockopen" id="sendmail" value="0" checked="checked" onclick="sendmail_clicked()" onfocus="sendmail_clicked()" /> <label for="sendmail">'.$l['mailsettings_sendmail'].'</label>
    <script type="text/javascript">
    document.getElementById(\'sendmail\').focus();
    </script>
    </td>
  ';
  echo '</tr>
  </table>
  
  <table width="100%">
    <tr><td>'.$l['mailsettings_smtp_host'].'</td><td><input name="smtp_host" id="smtp_host" value="'.$settings['smtp_host'].'" /></td></tr>
    <tr><td>'.$l['mailsettings_smtp_port'].'</td><td><input name="smtp_port" id="smtp_port" value="'.$settings['smtp_port'].'" /></td></tr>
    <tr><td>'.$l['mailsettings_smtp_user'].'</td><td><input name="smtp_user" id="smtp_user" value="'.$settings['smtp_user'].'" /></td></tr>
    <tr><td>'.$l['mailsettings_smtp_pass'].'</td><td><input type="password" name="smtp_pass" id="smtp_pass" /></td></tr>
    <tr><td>'.$l['mailsettings_smtp_pass_2'].'</td><td><input type="password" name="smtp_pass_2" id="smtp_pass_2" /></td></tr>
    <tr><td>'.$l['mailsettings_from_email'].'</td><td><input name="from_email" value="'.$settings['from_email'].'" /></td></tr>
  </table>
  
  <p><input name="update" type="submit" value="'.$l['mailsettings_update'].'"/></p>
  
  </form>';
}
?>