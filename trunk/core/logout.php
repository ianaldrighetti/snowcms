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

# Title: Logout Handler

if(!function_exists('logout_process'))
{
  /*
    Function: logout_process

    Logs you out of your account, as long as your session id is supplied.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function logout_process()
  {
    global $api, $base_url, $cookie_name, $member, $theme;

    # Not even logged in? Then you can't log out!
    if($member->is_guest())
    {
      header('Location: '. $base_url);
      exit;
    }

    # Check that session identifier, make sure it is yours.
    if(empty($_GET['sc']) || $_GET['sc'] != $member->session_id())
    {
      $api->run_hooks('logout_failed');

      $theme->set_title(l('An error has occurred'));
      $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));

      $theme->header();

      echo '
      <h1>', l('Logging out failed'), '</h1>
      <p>', l('Sorry, but the supplied session identifier was invalid, so your request to be logged out failed. Please try again.'), '</p>';

      $theme->footer();
      exit;
    }

    # Remove the cookie and session information.
    setcookie($cookie_name, '', time_utc() - 604800);
    unset($_SESSION['member_id'], $_SESSION['member_pass']);

    $api->run_hooks('logout_success');

    # Let's go home...
    header('Location: '. $base_url);
    exit;
  }
}
?>