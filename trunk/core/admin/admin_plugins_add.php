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

# Title: Control Panel - Plugins - Add

if(!function_exists('admin_plugins_add'))
{
  /*
    Function: admin_plugins_add

    Handles the downloading and extracting of plugins.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_url;

    $api->run_hooks('admin_plugins_add');

    # Can you add plugins?
    if(!$member->can('add_plugins'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    $theme->set_current_area('plugins_add');

    $theme->set_title(l('Add plugin'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_add-small.png" alt="" /> ', l('Add a new plugin'), '</h1>
  <p>', l('Plugins can be added to your site by entering the plugins dependency name (the address at which the plugins package is downloaded). A plugin can also be uploaded to the plugin directory as well (in its unextracted form).'), '</p>';

    $theme->footer();
  }
}
?>
