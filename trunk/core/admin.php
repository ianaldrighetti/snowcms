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

# Title: Admin switch

if(!function_exists('admin_switch'))
{
  /*
    Function: admin_switch

    This is the core of the Admin CP, it routes all requests to their
    proper location.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_switch()
  {
    global $api, $member, $theme;

    # First things first, are you even allowed to view the Admin CP?
    # Plugins can add more groups through the admin_allowed_groups filter.
    $allowed_groups = $api->apply_filters('admin_allowed_groups', array('administrator'));

    if(count($allowed_groups) > 0)
      foreach($allowed_groups as $group)
        if($member->is_a($group))
        {
          $allowed = true;
          break;
        }

    if(empty($allowed))
    {
      $theme->set_title(l('Access denied'));
      $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));

      $theme->header();

      echo '
      <h1>', l('Access denied'), '</h1>
      <p>', l('Sorry, but you are not allowed to access the page you have requested.'), '</p>';

      $theme->footer();
      exit;
    }

    # We may require you to enter a password, for security reasons!
    admin_prompt_password();
  }
}

if(!function_exists('admin_prompt_required'))
{
  /*
    Function: admin_prompt_required

    Checks to see if the user needs to verify their session with
    their account password.

    Parameters:
      none

    Returns:
      bool - Returns true if the user needs to supply their password
             in order to continue, false if not.

    Note:
      This function is overloadable.
  */
  function admin_prompt_required()
  {
    # !!! TODO
    return false;
  }
}

if(!function_exists('admin_prompt_password'))
{
  /*
    Function: admin_prompt_password

    Unlike <admin_prompt_required>, this function actually prompts
    for the password itself. A form is shown where the user can enter
    their password, or, a parameter can be passed containing their
    password (plain text, SHA-1'd or secured) for use by AJAX means.
    Hint hint ;)

    Parameters:
      string $password - The users plain text, SHA-1'd or secured password,
                         if left blank, the form is displayed.

    Returns:
      void - Nothing is returned by this function.
  */
  function admin_prompt_password($password = null)
  {

  }
}
?>