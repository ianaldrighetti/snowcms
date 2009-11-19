<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#      Page Layout template, January 16, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die(header('HTTP/1.1 404 Page Not Found'));

function register_view_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['register_header'], '</h1>';

  # Any registration errors..?
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

  # Now show the form
  echo '
      <fieldset>
        <form action="', $base_url, '/index.php?action=register2" id="registerform" method="post">
          <table class="registrationform">
            <colgroup span="1"></colgroup>
            <colgroup span="1" style="width: 1%;"></colgroup>
            <tr>
              <td class="right">', $l['label_username'], ':</td><td><input onkeyup="autoCheckUsername(this)" name="username" id="regUsername" type="text" class="text" value="', $page['username'], '"/></td>
              <td><a href="javascript:void(0);" onclick="checkUsername(\'regUsername\');" title="Check if username is available"><img src="', $settings['images_url'], '/check_user.png" id="check_user" alt="" title="Check if username is available" width="16" height="16" /></a></td>
            </tr>
            <tr>
              <td class="right" style="width: 160px;">', $l['label_password'], ':</td><td><input name="passwrd" onkeyup="check_passwords(this.form);" id="password" type="password" class="text" /></td>
              <td rowspan="2" style="width: 150px;">
                <div id="password_strength_text" class="password_strength_plain_text">&nbsp;</div>
                <div id="password_strength_bar" class="password_strength_background" style="display: none;"></div>
              </td>
            </tr>
            <tr>
              <td class="right">', $l['label_password_verify'], ':</td><td><input name="vPasswrd" onkeyup="check_passwords(this.form);" id="vPassword" type="password" class="text" /></td>
              <td></td>
            </tr>
            <tr>
              <td class="right">', $l['label_email'], ':</td><td><input name="email" onkeyup="checkEmail(this);" type="text" class="text" value="', $page['email'], '"/></td>
              <td></td>
            </tr>';
  
  # CAPTCHA
  if($settings['captcha_strength'])
  {
    echo '
            <tr>
              <td class="right">', $l['label_captcha'], ':</td><td><input name="captcha" type="text" class="text" value="', !empty($page['captcha']) ? $page['captcha'] : '', '"/></td>
              <td><a href="javascript:void(0);" onclick="newCaptcha();"><img src="', $settings['images_url'], '/new_captcha.png" alt="" title="Generate new CAPTCHA" width="16" height="16" /></a></td>
            </tr>
            <tr>
              <td class="center" colspan="4"><img id="captcha-image" src="', $base_url, '/index.php?action=captcha" alt="CAPTCHA" width="', $theme['captcha_width'], '" height="', $theme['captcha_height'], '" /></td>
            </tr>';
  }
  else
  {
    echo '
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>';
  }
  
  echo '
            <tr>
              <td colspan="4"><div id="agreement">', $page['agreement'], '</div></td>
            </tr>
            <tr>
              <td class="center" colspan="4"><input name="accepted_agreement" id="accepted_agreement" onclick="agreementCheck(this.form);" type="checkbox" value="1"', $page['accepted_agreement'] ? ' checked="checked"' : '', '/><label for="accepted_agreement"> I Agree</label></td>
            </tr>
            <tr>
              <td class="center" colspan="4"><input name="submit_registration" id="submit_registration" type="submit" value="Register"', $page['accepted_agreement'] ? '' : ' disabled="disabled"', '/></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}

function register_view_show_disabled()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['register_disabled_header'], '</h1>
      <p>', $l['register_disabled_desc'], '</p>';
}

function register_process_show_welcome()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['register_welcome_header'], '</h1>
      <p>', sprintf($l['register_welcome_desc'], $page['username']), '</p>';
}

function regiter_process_show_sent()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['register_emailsent_header'], '</h1>
      <p>', sprintf($l['register_emailsent_desc'], $page['username'], $page['email']), '</p>';
}

function register_process_show_admin()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['register_approval_header'], '</h1>
      <p>', sprintf($l['register_approval_desc'], $page['username'], $page['email']), '</p>';
}

function register_process_show_flood()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display flood control message
}

function register_activate_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display email activation form
}

function register_activate_show_success()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display email activation success message
}

function register_resend_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display resend email activation form
}

function register_resend_show_disabled()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display resend email activation disabled message
}
?>