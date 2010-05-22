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

# Title: Profile

if(!function_exists('profile_view'))
{
  /*
    Function: profile_view

    Handles the viewing of members profiles, but also editing of their
    profiles as well.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function profile_view()
  {
    global $api, $member, $settings;

    $api->run_hooks('profile_view');

    # Are you viewing someone elses profile? If it's yours, ignore it.
    if(isset($_GET['id']) && $_GET['id'] != $member->id())
    {
      # Can you even view other profiles?
      if(!$member->can('view_other_profile'))
      {
        # Nope, you cannot.
        member_access_denied(null, l('Sorry, but you do not have permission to view other members profiles.'));
      }
      else
      {
        # Does the member even exist..? If not, then we will show a denied page.
        $members = $api->load_class('Members');
        $members->load($_GET['id']);

        $member_info = $members->get($_GET['id']);

        # So did it load?
        if($member_info == false)
        {
          # No it did not. So the member doesn't exist :-(
          member_access_denied(l('Member doesn\'t exist'), l('Sorry, but the member you are requesting does not exist.'));
        }
      }
    }
  }
}
?>