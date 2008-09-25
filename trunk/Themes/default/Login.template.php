<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           Login.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

// Shows the login form, enter your username, password, and choose your desired session length =D
function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$settings['page']['title'].'</h1>
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

// Logout error Occurred, usually if not always because your Session ID was not right/valid
function LogoutError() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>', $l['logout_error_header'], '</h1>
  <p>', $l['logout_error_desc'], '</p>';
}
?>