<?php
// default/Login.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h3>'.$l['login_header'].'</h3>
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
          <td>'.$l['login_user'].'</td><td><input name="username" type="text" value="', @$_REQUEST['username'], '"/></td>
        </tr>
        <tr>
          <td>'.$l['login_pass'].'</td><td><input name="password" type="password" /></td>
        </tr>
        <tr>
          <td>', $l['login_length'], '</td><td><select name="login_length">
                                        <option value="3600">', $l['login_hour'], '</option>
                                        <option value="86400">', $l['login_day'], '</option>
                                        <option value="604800">', $l['login_week'], '</option>
                                        <option value="2592000">', $l['login_month'], '</option>
                                        <option value="31104000" selected="yes">', $l['login_forever'], '</option>
                                      </select>
                                  </td>
        </tr>
        <tr>
          <td colspan="2"><input name="login" type="submit" value="'.$l['login_button'].'"/></td>
        </tr>
      </table>
    </fieldset>
  </form>';
}

function LogoutError() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h3>', $l['logout_error_header'], '</h3>
  <p>', $l['logout_error_desc'], '</p>';
}
?>