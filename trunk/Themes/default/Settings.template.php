<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//         Settings.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $l;
  
  echo '
  <h1>'.$l['settings_title'].'</h1>
  
  <p>'.$l['settings_desc'].'</p>
  ';
  
  SettingOptions();
  
  echo '
  <p style="clear: both"><br /></p>
  
  <form action="" method="post">
    <p>
      <input type="hidden" name="redirect" value="admin" />
      <input type="submit" value="'.$l['main_back'].'" />
    </p>
  </form>
  ';
}

function Basic() {
global $cmsurl, $settings, $l, $user;
  
  echo '
  <h1>'.$l['settings_basic_header'].'</h1>
  ';
  
  if(@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  echo '
  <p>'.$l['settings_basic_desc'].'</p>';
  echo '
  <form action="" method="post" style="display: inline">
    <table>';
  foreach($settings['page']['settings'] as $setting => $info) {
    if($info['type'] == 'text') {
      $field = '<input name="'.$setting.'" type="text" value="'.$settings[$setting].'" />';
    }
    elseif($info['type'] == 'select') {
      $field = "\n".'          <select name="'.$setting.'">'."\n";
      $i = 0;
      while($i < count($info['values'])) {
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
        <td>'.$l['settings_basic_'.$setting].':</td><td>'.$field.'</td>
      </tr>';
  }
  echo '
    </table>
    <br />
    <p style="display: inline"><input name="update" type="submit" value="'.$l['settings_basic_submit'].'" /></p>
  </form>
  
  <form action="" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="settings" />
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
  </form>';
}

function ManageMailSettings() {
global $cmsurl, $settings, $l, $user;
  
  echo '
  <h1>'.$l['settings_mail_header'].'</h1>
  
  ';
  
  if(@$_SESSION['error'])
	  echo '<p>'.$_SESSION['error'].'</p>';
	else
	  echo '<p>'.$l['settings_mail_desc'].'</p>';
  
  echo '
  
  <form action="" method="post" style="display: inline">
  
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
  if($settings['mail_with_fsockopen'])
    echo '<td width="50%"><input type="radio" name="mail_with_fsockopen" id="smtp" value="1" checked="checked" onclick="smtp_clicked()" /> <label for="smtp">'.$l['settings_mail_smtp'].'</label></td>
    <td><input type="radio" name="mail_with_fsockopen" id="sendmail" value="0" onclick="sendmail_clicked()" /> <label for="sendmail">'.$l['settings_mail_sendmail'].'</label></td>
  ';
  else
    echo '<td width="50%"><input type="radio" name="mail_with_fsockopen" id="smtp" value="1" onclick="smtp_clicked()" /> <label for="smtp">'.$l['settings_mail_smtp'].'</label></td>
    <td><input type="radio" name="mail_with_fsockopen" id="sendmail" value="0" checked="checked" onclick="sendmail_clicked()" onfocus="sendmail_clicked()" /> <label for="sendmail">'.$l['settings_mail_sendmail'].'</label>
    <script type="text/javascript">
    document.getElementById(\'sendmail\').focus();
    </script>
    </td>
  ';
  echo '</tr>
  </table>
  
  <table width="100%">
    <tr><td>'.$l['settings_mail_smtp_host'].'</td><td><input name="smtp_host" id="smtp_host" value="'.$settings['smtp_host'].'" /></td></tr>
    <tr><td>'.$l['settings_mail_smtp_port'].'</td><td><input name="smtp_port" id="smtp_port" value="'.$settings['smtp_port'].'" /></td></tr>
    <tr><td>'.$l['settings_mail_smtp_user'].'</td><td><input name="smtp_user" id="smtp_user" value="'.$settings['smtp_user'].'" /></td></tr>
    <tr><td>'.$l['settings_mail_smtp_pass'].'</td><td><input type="password" name="smtp_pass" id="smtp_pass" /></td></tr>
    <tr><td>'.$l['settings_mail_smtp_pass_2'].'</td><td><input type="password" name="smtp_pass_2" id="smtp_pass_2" /></td></tr>
    <tr><td>'.$l['settings_mail_from_email'].'</td><td><input name="from_email" value="'.$settings['from_email'].'" /></td></tr>
  </table>
  
  <br />
  
  <p style="display: inline">
    <input name="update" type="submit" value="'.$l['settings_mail_submit'].'" />
  </p>
  
  </form>
  
  <form action="" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="settings" />
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
  </form>';
}

function FieldLengths() {
global $cmsurl, $settings, $l, $user;
  
  echo '
  <h1>'.$l['settings_lengths_header'].'</h1>
  
  ';
  
  if(@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  else
    echo '<p>'.$l['settings_lengths_desc'].'</p>';
  
  echo '
  
  <form action="" method="post" style="display: inline">
    <table>';
  foreach ($settings['page']['settings'] as $setting) {
    echo '
      <tr>
        <td>'.$l['settings_lengths_'.$setting].':</td>
        <td>'.
         '<input name="'.$setting.'_short" value="'.$settings[$setting.'_short'].'" /> - '.
         '<input name="'.$setting.'_long" value="'.$settings[$setting.'_long'].'" />'.
       '</td>
      </tr>';
  }
  echo '
    </table>
    <br />
    <p style="display: inline"><input type="submit" name="update" value="'.$l['settings_lengths_submit'].'" /></p>
  </form>
  
  <form action="" method="post" style="display: inline">
    <p style="display: inline">
      <input type="hidden" name="redirect" value="settings" />
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
  </form>';
}

function SettingOptions() {
global $l, $settings, $cmsurl;
  
  $options = $settings['page']['options'];
  
  $odd = true;
  foreach ($options as $option) {
    echo '
  <div class="acp_'.($odd ? 'left' : 'right').'">
    <p class="main"><a href="'.$cmsurl.'index.php?action=admin;sa=settings;ssa='.$option.'" title="'.$l['settings_menu_'.$option].'">'.$l['settings_menu_'.$option].'</a></p>
    <p class="desc">'.$l['settings_menu_'.$option.'_desc'].'</p>
  </div>
  ';
    $odd = !$odd;
  }
}
?>