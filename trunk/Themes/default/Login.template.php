<?php
// default/Login.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['login_header'].'</h1>
  <p>'.$l['login_details'].'</p>
  <form action="'.$cmsurl.'index.php?action=login2" method="post">
    <fieldset>
      <table>';
      if(!empty($settings['page']['error'])) 
      echo '
        <tr>
          <td colspan="2"><p class="login_error">'.$settings['page']['error'].'</p>
        </tr>';
      echo '
        <tr>
          <td>'.$l['login_user'].'</td><td><input name="username" type="text" value="'.@$_REQUEST['username'].'"/></td>
        </tr>
        <tr>
          <td>'.$l['login_pass'].'</td><td><input name="password" type="password" /></td>
        </tr>
        <tr>
          <td colspan="2"><input name="remember_me" type="checkbox" checked="checked" value="1"/> '.$l['login_remember_me'].'</td>
        </tr>
        <tr>
          <td colspan="2"><input name="login" type="submit" value="'.$l['login_button'].'"/></td>
        </tr>
      </table>
    </fieldset>
  </form>';
}
?>