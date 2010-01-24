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

# Title: Registration Handler

if(!function_exists('register_view'))
{
  /*
    Function: register_view

    Displays the registration form, that is if registration is enabled.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function register_view()
  {
    global $api, $base_url, $member, $settings, $theme;

    $api->run_hook('register_view');

    # Are you logged in? You don't need to register an account because you obviously have one!
    if($member->is_logged())
    {
      header('Location: '. $base_url);
      exit;
    }

    # Let's get that form going!
    $form = $api->load_class('Form');
    $form->add('registration_form', 'register_member', $base_url. '/index.php?action=register2');

    # Is registration enabled?
    if(!$settings->get('registration_enabled', 'bool'))
    {
      $theme->set_title(l('Registration disabled'));
      $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));

      $api->run_hook('registration_disabled');

      $theme->header();

      echo '
      <h1>', l('Registration disabled'), '</h1>
      <p>', l('We apologize for the inconvience, but registration is currently not open to the public. Please check back at a later time.'), '</p>';

      $theme->footer();
      exit;
    }

    $theme->set_title(l('Register'));

    $theme->header();

    echo '
    <h1>', l('Register an account'), '</h1>
    <p>', l('Here you can register an account on %s and get access to certain features that only registered members are allowed to use.', $settings->get('site_name')), '</p>';

    $theme->footer();
  }
}
?>