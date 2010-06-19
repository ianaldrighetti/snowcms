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

# Title: Account activation

if(!function_exists('activate_view'))
{
  /*
    Function: activate_view

    Handles the activation of members who registered an account but were
    required to activate their account via email.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function activate_view()
  {
    global $api, $base_url, $member, $settings, $theme;

    $api->run_hooks('activate_view');

    # Are you logged in? Then why would you need to activate another account?
    if($member->is_logged())
    {
      header('Location: '. $base_url);
      exit;
    }
    # What is the registration type? Is it actually email?
    elseif($settings->get('registration_type', 'int', 0) != 2)
    {
      $theme->set_title(l('An error has occurred'));

      $theme->header();

      echo '
      <h1>', l('An error has occurred'), '</h1>
      <p>', l('It appears that the current type of registration isn\'t email activation, so you cannot activate your account this way.'), '</p>';

      $theme->footer();
      exit;
    }

    # It should use a form in reality, but since this can be done through
    # a URL that wouldn't be the best solution. So we will hand make it,
    # in this case!
    if((!empty($_REQUEST['id']) || !empty($_REQUEST['name'])) && !empty($_REQUEST['code']) && $_REQUEST['code'] != 'admin_approval')
    {
      # We will be needing this. That's for sure :P
      $members = $api->load_class('Members');

      # Did you give is a name? We need to convert it to an ID.
      if(empty($_REQUEST['id']) && !empty($_REQUEST['name']))
      {
        $_REQUEST['id'] = (int)$members->name_to_id($_REQUEST['name']);
      }

      # Load up that member :)
      $members->load($_REQUEST['id']);
      $member_info = $members->get($_REQUEST['id']);

      if(!empty($member_info))
      {
        # Just because you got the right ID doesn't mean nothin' :P
        # Has this account already been activated?
        if($member_info['is_activated'] == 1)
        {
          $api->add_filter('activation_message', create_function('$value', '
                                                 global $base_url;

                                                 return l(\'It appears that the specified member is already activated. If this is your account, you can <a href="%s">login</a> now.\', $base_url. \'/index.php?action=login\');'));
          $api->run_hooks('activation_member_already_activated', array($member_info));

          $_REQUEST['name'] = $member_info['username'];
        }
        # Do the codes not match?
        elseif($member_info['acode'] != $_REQUEST['code'] || strlen($member_info['acode']) == 0)
        {
          $api->add_filter('activation_message', create_function('$value', '
                                                   return l(\'The supplied activation code is invalid.\');'));
          $api->run_hooks('activation_member_invalid_acode', array($member_info));

          $_REQUEST['name'] = $member_info['username'];
        }
        else
        {
          # Sweet! It's right ;D
          $members->update($_REQUEST['id'], array(
                                              'member_acode' => '',
                                              'member_activated' => 1,
                                            ));

          $api->add_filter('activation_message_id', create_function('$value', '
                                                      return \'activation_success\';'));
          $api->add_filter('activation_message', create_function('$value', '
                                                     global $base_url;

                                                     return l(\'Your account has been successfully activated. You may now proceed to <a href="%s">login</a>.\', $base_url. \'/index.php?action=login\');'));
          $api->run_hooks('activation_member_success', array($member_info));

          $_REQUEST['name'] = '';
          $_REQUEST['code'] = '';
        }
      }
      else
      {
        # It appears that member does not exist... Interesting.
        $api->add_filter('activation_message', create_function('$value', '
                                                 return l(\'It appears that the specified member does not exist. Please try again.\');'));
        $api->run_hooks('activation_member_nonexist');
      }
    }

    $theme->set_title('Activate your account');

    # No indexing if you have anything extra set ;)
    if(isset($_GET['id']) || isset($_GET['code']))
    {
      $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));
    }

    $theme->header();

    echo '
      <h1>', l('Activate your account'), '</h1>
      <p>', l('If your account has not yet been activated, you can enter your activation information here. If you have yet to receive your activation email, you can <a href="%s">request for it to be resent</a>.', $base_url. '/index.php?action=resend'), '</p>';

    if(strlen($api->apply_filters('activation_message', '')) > 0)
    {
      echo '
      <div id="', $api->apply_filters('activation_message_id', 'activation_error'), '">
        ', $api->apply_filters('activation_message', ''), '
      </div>';
    }

    echo '
      <form action="', $base_url, '/index.php?action=activate" method="post" id="activation_form">
        <fieldset>
          <table>
            <tr id="activation_form_name">
              <td class="td_left">', l('Username:'), '</td><td class="td_right"><input id="activation_form_name_input" type="text" name="name" value="', htmlchars(!empty($_REQUEST['name']) ? $_REQUEST['name'] : ''), '" /></td>
            </tr>
            <tr id="activation_form_code">
              <td class="td_left">', l('Activation code:'), '</td><td class="td_right"><input id="activation_form_code_input" type="text" name="code" value="', htmlchars(!empty($_REQUEST['code']) ? $_REQUEST['code'] : ''), '" /></td>
            </tr>
            <tr id="activation_form_submit">
              <td colspan="2" class="buttons"><input type="submit" value="', l('Activate account'), '" /></td>
            </tr>
          </table>
        </fieldset>
      </form>';

    $theme->footer();
  }
}
?>