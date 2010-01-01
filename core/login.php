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

if(!function_exists('login_view'))
{
  /*
    Function: login_view

    Simply displays the login form.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function login_view()
  {
    global $api, $base_url, $member, $theme, $theme_url;

    $api->run_hook('login_view');

    # Are you already logged in? If you are, you don't need this!
    if($member->is_logged())
    {
      header('Location: /');
      exit;
    }

    # Just a bit more security... :)
    $form = $api->load_class('Form');
    $form_token = $form->add('login_form');

    $theme->set_title(l('Login'));
    $theme->add_js_file(array('src' => $theme_url. '/default/js/secure_form.js'));

    $theme->header();

    echo '
      <h1>', l('Login to your account'), '</h1>
      <p>', l('Here you can login to your account, if you do not have an account, you can <a href="%s">register one</a>.', $base_url. '/index.php?action=register'), '</p>
      <form id="login_form" name="login_form" action="', $base_url, '/index.php?action=login2" method="post" class="login_form" onsubmit="secure_form(\'login_form\');">
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
                  <option value="0"', isset($_REQUEST['session_length']) && $_REQUEST['session_length'] == 0 ? ' selected="selected"' : '', '>', l('This session'), '</option>
                  <option value="3600"', isset($_REQUEST['session_length']) && $_REQUEST['session_length'] == 3600 ? ' selected="selected"' : '', '>', l('An hour'), '</option>
                  <option value="86400"', isset($_REQUEST['session_length']) && $_REQUEST['session_length'] == 86400 ? ' selected="selected"' : '', '>', l('A day'), '</option>
                  <option value="604800"', isset($_REQUEST['session_length']) && $_REQUEST['session_length'] == 604800 ? ' selected="selected"' : '', '>', l('A week'), '</option>
                  <option value="2419200"', isset($_REQUEST['session_length']) && $_REQUEST['session_length'] == 2419200 ? ' selected="selected"' : '', '>', l('A month'), '</option>
                  <option value="31536000"', isset($_REQUEST['session_length']) && $_REQUEST['session_length'] == 31536000 ? ' selected="selected"' : '', '>', l('A year'), '</option>
                  <option value="-1"', (isset($_REQUEST['session_length']) && $_REQUEST['session_length'] == -1) || !isset($_REQUEST['session_length']) ? ' selected="selected"' : '', '>', l('Forever'), '</option>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center !important;"><a href="', $base_url, '/index.php?action=reminder">', l('Lost your password?'), '</a></td>
            </tr>
          </table>
        </fieldset>
        <input type="hidden" name="form_token" value="', $form_token, '" />
      </form>';

    $theme->footer();
  }
}

if(!function_exists('login_process'))
{
  /*

  */
  function login_process()
  {
    global $api, $db, $member;

    $api->run_hook('login_process');

    # Are you logged in? You Silly Pants you!
    if($member->is_logged())
    {
      header('Location: /');
      exit;
    }


  }
}
?>