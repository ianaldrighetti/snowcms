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

function MailSetup() {
global $cmsurl, $settings, $l, $user;
}
?>