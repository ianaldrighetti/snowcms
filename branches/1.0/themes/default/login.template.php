<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Login Layout template, January 16, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function login_view_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['login_header'], '</h1>
      <p>', $l['login_desc'], '</p>';

  # Any errors perhaps..?
  if(isset($page['errors']) && count($page['errors']))
  {
    echo '
      <div class="generic_error">';

    foreach($page['errors'] as $error)
      echo '
      <p>', $error, '</p>';
    
    echo '
      </div>';
  }

  echo '
      <fieldset>
        <form action="', $base_url, '/index.php?action=login2" method="post" onClick="hashPassword(this.form);"
          <table class="loginform">
            <tr>
              <td style="text-align: right;">', $l['login_username'], '</td><td><input name="username" type="text" class="text" value="', $page['username'], '" /></td>
            </tr>
            <tr>
              <td style="text-align: right;">', $l['login_password'], '</td><td><input name="passwrd" type="password" class="text" value="" /></td>
            </tr>
            <tr>
              <td style="text-align: right;">', $l['login_session_length'], '</td>
              <td><select name="expires">
                    <option value="3600">', $l['login_1hour'], '</option>
                    <option value="86400">', $l['login_1day'], '</option>
                    <option value="604800">', $l['login_1week'], '</option>
                    <option value="18748800">', $l['login_1month'], '</option>
                    <option value="-1" selected="selected">', $l['login_forever'], '</option>
                  </select>
              </td>
            </tr>
            <tr align="center">
              <td colspan="2"><input type="submit" value="', $l['login_button'], '" /></td>
            </tr>
            <tr style="text-align: center;">
              <td colspan="2"><a href="', $base_url, '/index.php?action=reminder">', $l['login_forgot_password'], '</a></td>
            </tr>
          </table>
          <input name="hashed_passwrd" type="hidden" id="hashed_passwrd" value="" />
        </form>
      </fieldset>';
}

function login_process_show_flood()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['login_flood_warning'], '</h1>
      <p>', $l['login_flood_desc'], '</p>';
}

function login_reminder_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['login_reminder_header'], '</h1>
      <p>', $l['login_reminder_desc'], '</p>';
  
  # An error maybe..?
  if(isset($page['errors']) && $page['errors'])
    echo '
      <div class="generic_error">
        <p>', $l['login_reminder_error'], '</p>
      </div>';
  # Or better yet, a success.
  elseif(isset($page['success']) && $page['success'])
    echo '
      <div class="generic_success">
        <p>', $l['login_reminder_success'], '</p>
      </div>';
  
  echo '
      <fieldset>
        <form action="', $base_url, '/index.php?action=reminder" method="post">
          <table class="reminderform">
            <tr>
              <td>', $l['login_reminder_usernameemail'], '</td><td><input name="email" type="text" value="" /></td>
            </tr>
            <tr>
              <td colspan="2"><input type="submit" value="', $l['login_reminder_button'], '" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}

function login_reminder_show_flood()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['login_flood_warning'], '</h1>
      <p>', $l['login_flood_desc'], '</p>';
}

function login_reminder2_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['login_reminder2_header'], '</h1>
      <p>', $l['login_reminder2_desc'], '</p>';
  
  # Any errors?
  if(isset($page['errors']) && count($page['errors']))
  {
    echo '
      <div class="generic_error">';
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    echo '
      </div>';
  }
  
  # The form...
  echo '
      <fieldset>
        <form action="', $base_url, '/index.php?action=reminder2" method="post">
          <table class="reminderform">
            <tr>
              <td>Username</td><td><input name="username" type="text" value="', $page['username'], '" /></td>
            </tr>
            <tr>
              <td>Verification Code</td><td><input name="code" type="text" value="', $page['code'], '" /></td>
            </tr>
            <tr>
              <td>New Password</td><td><input name="newPasswrd" type="password" value="" /></td>
            </tr>
            <tr>
              <td>Verify Password</td><td><input name="vNewPasswrd" type="password" value="" /></td>
            </tr>
            <tr>
              <td colspan="2"><input name="proc_reset" type="submit" value="Reset Password"/></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}
?>