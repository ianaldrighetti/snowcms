<?php
#########################################################################
#                             SnowCMS v2.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#                  Released under the GNU GPL v3 License                #
#                     www.gnu.org/licenses/gpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#                SnowCMS v2.0 began in November 2009                    #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 2.0                         #
#########################################################################

if(!defined('IN_SNOW'))
  die;

function login_view()
{
  global $api, $base_url, $theme, $theme_url;

  $theme->set_title(l('Login'));
  $theme->add_js_file(array('src' => $theme_url. '/default/js/secure_form.js'));

  $theme->header();

  echo '
      <h1>', l('Login to your account'), '</h1>
      <p>', l('Here you can login to your account, if you do not have an account, you can <a href="%s">register one</a>.', $base_url. '/index.php?action=register'), '</p>
      <form action="', $base_url, '/index.php?action=login2" method="post" class="login_form" onsubmit="secure_form(this.form);">
        <fieldset>
          <table>
            <tr>
              <th colspan="2">', l('Username'), ':</th>
            </tr>
            <tr>
              <td colspan="2"><input type="text" name="username" value="', !empty($_REQUEST['username']) ? htmlchars($_REQUEST['username']) : '', '" /></td>
            </tr>
            <tr>
              <th colspan="2">', l('Password'), ':</th>
            </tr>
            <tr>
              <td colspan="2"><input type="password" name="password" value="" /></td>
            </tr>
            <tr>
              <td colspan="2"><input type="submit" name="submit" value="', l('Login'), '" /></td>
            </tr>
            <tr>
              <td>', l('Stay logged in for'), ':</td>
              <td style="text-align: right !important;">
                <select name="session_length">
                  <option value="0">', l('This session'), '</option>
                  <option value="3600">', l('An hour'), '</option>
                  <option value="86400">', l('A day'), '</option>
                  <option value="604800">', l('A week'), '</option>
                  <option value="2419200">', l('A month'), '</option>
                  <option value="31536000">', l('A year'), '</option>
                  <option value="-1" selected="selected">', l('Forever'), '</option>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center !important;"><a href="', $base_url, '/index.php?action=reminder">', l('Lost your password?'), '</a></td>
            </tr>
          </table>
        </fieldset>
      </form>';

  $theme->footer();
}
?>