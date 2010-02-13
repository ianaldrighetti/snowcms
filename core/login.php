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

# Title: Login Handler

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
    global $api, $base_url, $func, $member, $theme, $theme_url;

    $api->run_hook('login_view');

    # Are you already logged in? If you are, you don't need this!
    if($member->is_logged())
    {
      header('Location: /');
      exit;
    }

    # Just a bit more security... CSRF security, that is!
    //$token = $api->load_class('Tokens');
    //$token->add('login_form');

    $theme->set_title(l('Log in'));
    $theme->add_js_file(array('src' => $theme_url. '/default/js/secure_form.js'));

    $theme->header();

    echo '
      <h1>', l('Log in to your account'), '</h1>
      <p>', l('Here you can login to your account, if you do not have an account, you can <a href="%s">register one</a>.', $base_url. '/index.php?action=register'), '</p>';

    # You can hook into this to display a message
    if($func['strlen']($api->apply_filter('login_message', '')) > 0)
    {
      echo '
      <div id="', $api->apply_filter('login_message_id', 'login_error'), '">
        ', $api->apply_filter('login_message', ''), '
      </div>';
    }

    # Generate that lovely login form.
    login_generate_form();
    $form = $api->load_class('Form');

    # We added the overall form, now we need to add
    $form->show('login_form');
    /*echo '
      <form id="login_form" name="login_form" action="', $api->apply_filter('login_action_url', $base_url. '/index.php?action=login2'), '" method="post" class="login_form" onsubmit="', $api->apply_filter('login_onsubmit', 'secure_form(\'login_form\');'), '">
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
              <td style="padding-left: 20px;">
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
        <input type="hidden" name="form_token" value="', $token->token('login_form'), '" />
      </form>';*/

    $theme->footer();
  }
}

if(!function_exists('login_generate_form'))
{
  /*
    Function: login_generate_form

    Generates the login form.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function login_generate_form()
  {
    global $api, $base_url;
    static $generated = false;

    # Don't generate the form twice.
    if(!empty($generated))
      return;

    # Add the core part first.
    $form = $api->load_class('Form');
    $form->add('login_form', array(
                               'callback' => 'login_process',
                               'action' => $api->apply_filter('login_action_url', $base_url. '/index.php?action=login2'),
                               'method' => 'post',
                               'submit' => l('Login'),
                             ));

    # Now the rest of the stuff.
    $form->add_field('login_form', 'member_name', array(
                                                    'type' => 'string',
                                                    'label' => l('Username:'),
                                                    'function' => create_function('&$value, $form_name, &$error', '
                                                                    global $api, $db;

                                                                    if(empty($value))
                                                                    {echo \'uh oh\';
                                                                      $api->run_hook(\'login_process_empty_username\');

                                                                      $error = l(\'Please enter your username.\');
                                                                      return false;
                                                                    }

                                                                    return true;'),
                                                    'value' => !empty($_POST['member_name']) ? $_POST['member_name'] : '',
                                                  ));

    $form->add_field('login_form', 'member_pass', array(
                                                    'type' => 'password',
                                                    'label' => l('Password:'),
                                                    'function' => create_function('&$value, $form_name, &$error', '
                                                                    global $api, $db;

                                                                    if(empty($value) && empty($_POST[\'secured_password\']))
                                                                    {
                                                                      $api->run_hook(\'login_process_empty_password\');

                                                                      $error = l(\'Please enter your password.\');
                                                                      return false;
                                                                    }

                                                                    return true;'),
                                                  ));

    $form->add_field('login_form', 'session_length', array(
                                                       'type' => 'select',
                                                       'label' => l('Stay logged in for'),
                                                       'options' => array(
                                                                      0 => l('This session'),
                                                                      3600 => l('An hour'),
                                                                      86400 => l('A day'),
                                                                      604800 => l('A week'),
                                                                      2419200 => l('A month'),
                                                                      31536000 => l('A year'),
                                                                      -1 => l('Forever'),
                                                                    ),
                                                       'value' => !empty($_REQUEST['session_length']) ? (int)$_REQUEST['session_length'] : -1,
                                                     ));

    # It has been generated, so don't generate it again!
    $generated = true;
  }
}

if(!function_exists('login_view2'))
{
  /*
    Function: login_view2

    Handles the submission of the login form.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function login_view2()
  {
    global $api;

    # Generate the login form :)
    login_generate_form();
    $form = $api->load_class('Form');

    # Process the form, and we are good to go!
    # Unless it failed, of course.
    if(!$form->process('login_form'))
      # Let login_view() handle the displaying of errors.
      login_view();
  }
}

if(!function_exists('login_process'))
{
  /*
    Function: login_process

    Processes the data submitted by the login form.
    This is the callback for the login form.

    Parameters:
      array $login - An array containing login information.
      array &$errors - An array containing any errors which occurred
                       while processing the login data.

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function login_process($login, &$errors = array())
  {
    global $api, $base_url, $cookie_name, $db, $func, $theme, $member;

    $api->run_hook('login_process');

    # Are you logged in? You Silly Pants you!
    if($member->is_logged())
    {
      header('Location: '. $base_url);
      exit;
    }

    # We'll be needing this.
    $token = $api->load_class('Tokens');

    # No indexing robots!
    $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));

    # Check that form token, if it isn't valid, then throw an error...
    if(empty($_POST['form_token']) || !$token->is_valid('login_form', $_POST['form_token']))
    {
      # Well, it was wrong, so say so.
      $api->add_filter('login_message', create_function('$value', '
        return l(\'Your security key is invalid. Please resubmit the form.\');'));

      # Do what you want to do if you have anything to do.
      $api->run_hook('login_process_token_invalid');

      # Show the login form, and we are done here.
      login_view();
      exit;
    }
    # Nothing supplied?
    elseif(empty($_POST['username']) || (empty($_POST['password']) && empty($_POST['secured_password'])))
    {
      # Please enter a username/Please enter a password :P
      $api->add_filter('login_message', create_function('$value', '
        return l(empty($_POST[\'username\']) ? \'Please enter a username.\' : \'Please enter a password.\');'));

      $api->run_hook('login_process_empty_fields');

      login_view();
      exit;
    }

    # So you got the stuff, but is it the right stuff? Let's see!
    $result = $db->query('
      SELECT
        member_id, member_pass, member_hash, member_activated
      FROM {db->prefix}members
      WHERE '. ($db->case_sensitive ? 'LOWER(member_name) = LOWER({string:member_name})' : 'member_name = {string:member_name}'). '
      LIMIT 1',
      array(
        'member_name' => $_POST['username'],
      ), 'login_process_query');

    # Did we get anything? If we got 0 rows, this member doesn't even exist!
    if($result->num_rows() == 0)
    {
      # Wrong username or password :P
      $api->add_filter('login_message', create_function('$value', '
        return l(\'Invalid username or password supplied.\');'));

      $api->run_hook('login_process_member_nonexist');

      login_view();
      exit;
    }

    # Now let's check that password!
    $row = $result->fetch_assoc();

    # No success as of yet.
    $login_success = false;

    # Is the secured_password field not empty? Cool, :)
    if(!empty($_POST['secured_password']) && !empty($_SESSION['last_guest_rand_str']) && $func['strlen']($_POST['secured_password']) == 40 && $_POST['secured_password'] == sha1($row['member_pass']. $_SESSION['last_guest_rand_str']))
      $login_success = true;
    # Maybe it is just plain text, pssssh!
    elseif(sha1($_POST['password']) == $row['member_pass'])
      $login_success = true;
    # You want to check something?
    else
      $api->run_hook('login_process_check_custom', array(&$login_success));

    # Failed to login? Sucks to be you.
    if(empty($login_success))
    {
      $api->add_filter('login_message', create_function('$value', '
        return l(\'Invalid username or password supplied.\');'));

      $api->run_hook('login_process_wrong_password');

      login_view();
      exit;
    }

    # Remove form data from your guest session, just taking up space.
    $token->clear();

    # So how long did you want to be remembered?
    # Forever?
    if(isset($_POST['session_length']) && $_POST['session_length'] == -1)
      $cookie_expires = time_utc() + 315360000;
    # A more specific time?
    elseif(!empty($_POST['session_length']) && (string)$_POST['session_length'] == (string)(int)$_POST['session_length'])
      $cookies_expires = time_utc() + (int)$_POST['session_length'];
    # Just until you close your browser?
    else
      $cookie_expires = 0;

    # Set the cookie, a chocolate chip cookie :) No one likes oatmeal cookies, they are nasty...
    # Oh yeah, and give the password a touch of salt, :D Take that HTTP sniffers!
    setcookie($cookie_name, $row['member_id']. '|'. sha1($row['member_pass']. $row['member_hash']), $cookie_expires);

    $_SESSION['member_id'] = (int)$row['member_id'];
    $_SESSION['member_pass'] = sha1($row['member_pass']. $row['member_hash']);

    $api->run_hook('login_success', array($row));

    # Redirect to check cookie, so we can do just that :P
    header('Location: '. $base_url. '/index.php?action=checkcookie&id='. $row['member_id']);
    exit;
  }
}
?>