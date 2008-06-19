<?php
// default/Register.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['register_header'].'</h1>
  <p>'.$l['register_details'].'</p>
  <form action="'.$cmsurl.'index.php?action=register2" method="post">
    <fieldset>
      <table>';
      if(count($settings['page']['error'])>0) {
      echo '
      <tr>
        <td><p class="error">';  
        foreach($settings['page']['error'] as $error) {
          echo $error.'<br />';
        }
      echo '
        </p></td>
      </tr>';
      }
      echo '  
        <tr>
          <td>'.$l['register_username'].'</td><td><input name="username" type="text" value="'.@$_REQUEST['username'].'"/></td>
        </tr>
        <tr>
          <td>'.$l['register_password'].'</td><td><input name="password" type="password" /></td>
        </tr>
        <tr>
          <td>'.$l['register_verify_password'].'</td><td><input name="vpassword" type="password" /></td>
        </tr>
        <tr>
          <td>'.$l['register_email'].'</td><td><input name="email" type="text" value="'.@$_REQUEST['email'].'" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td><td colspan="2"><img src="'.$cmsurl.'image.php" alt="CAPTCHA" style="border: 1px solid #000;" /></td>
        </tr>
        <tr>
          <td>'.$l['register_captcha'].'</td><td><input name="captcha" type="text"/></td>
        </tr>
        <tr>
          <td colspan="2"><input name="register" type="submit" value="'.$l['register_button'].'"/></td>
        </tr>
      </table>
    </fieldset>
  </form>';
}
function Success() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['register_header'].'</h1>
  <p>'.$l['register_success'].'</p>';
}

function SuccessBut1() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['register_header'].'</h1>
  <p>'.$l['register_successbut1'].'</p>';
}

function SuccessBut2() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['register_header'].'</h1>
  <p>'.$l['register_successbut2'].'</p>';
}
?>
