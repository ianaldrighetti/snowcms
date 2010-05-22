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

# Title: Control Panel - Plugins - Manage

if(!function_exists('admin_plugins_manage'))
{
  /*
    Function: admin_plugins_manage



    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_manage()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_url;

    $api->run_hooks('admin_plugins_manage');

    # Can you manage plugin settings?
    if(!$member->can('manage_plugins'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    $theme->set_current_area('plugins_manage');

    $theme->set_title(l('Manage plugins'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_manage-small.png" alt="" /> ', l('Manage plugins'), '</h1>
  <p>', l('Manage your current plugins.'), '</p>';

    $theme->footer();
  }
}
?>