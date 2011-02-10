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

/*
  Title: Core actions

  Function: init_core

  Registers actions which are default "features", such as logging in/out,
  registration, and other such operations. Plus a couple other things ;)

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.

  Note:
    All the actions registered in this function can be overloaded, simply
    by registering the actions before init_core is called, but also, all
    the functions which are used are overloadable as well.
*/
function init_core()
{
  global $api, $core_dir;

  # We have a couple default actions of our own :) (Remember, you can
  # register these actions before they are registered here! :) But also
  #all these functions are overloadable, so simply define them before
  # this too!!!).
  $api->add_event('action=activate', 'activate_view', $core_dir. '/activate.php');
  $api->add_event('action=admin', 'admin_home', $core_dir. '/admin/admin_home.php');
  $api->add_event('action=admin&sa=about', 'admin_about', $core_dir. '/admin/admin_home.php');
  $api->add_event('action=admin&sa=ajax&id=plugins_update', 'admin_plugins_update_ajax', $core_dir. '/admin/admin_plugins_manage.php');
  $api->add_event('action=admin&sa=ajax&id=update', 'admin_update_ajax', $core_dir. '/admin/admin_update.php');
  $api->add_event('action=admin&sa=update&apply=*', 'admin_update_apply', $core_dir. '/admin/admin_update.php');
  $api->add_event('action=admin&sa=error_log', 'admin_error_log', $core_dir. '/admin/admin_error_log.php');
  $api->add_event('action=admin&sa=error_log&id=*', 'admin_error_log_view', $core_dir. '/admin/admin_error_log.php');
  $api->add_event('action=admin&sa=members_add', 'admin_members_add', $core_dir. '/admin/admin_members_add.php');
  $api->add_event('action=admin&sa=members_manage', 'admin_members_manage', $core_dir. '/admin/admin_members_manage.php');
  $api->add_event('action=admin&sa=members_manage&id=*', 'admin_members_manage_edit', $core_dir. '/admin/admin_members_manage.php');
  $api->add_event('action=admin&sa=members_permissions', 'admin_members_manage_permissions', $core_dir. '/admin/admin_members_permissions.php');
  $api->add_event('action=admin&sa=members_permissions&grp=*', 'admin_members_manage_group_permissions', $core_dir. '/admin/admin_members_permissions.php');
  $api->add_event('action=admin&sa=members_settings', 'admin_members_settings', $core_dir. '/admin/admin_members_settings.php');
  $api->add_event('action=admin&sa=plugins_add', 'admin_plugins_add', $core_dir. '/admin/admin_plugins_add.php');
  $api->add_event('action=admin&sa=plugins_add&install=*', 'admin_plugins_install', $core_dir. '/admin/admin_plugins_add.php');
  $api->add_event('action=admin&sa=plugins_manage', 'admin_plugins_manage', $core_dir. '/admin/admin_plugins_manage.php');
  $api->add_event('action=admin&sa=plugins_manage&update=*', 'admin_plugins_update', $core_dir. '/admin/admin_plugins_manage.php');
  $api->add_event('action=admin&sa=plugins_settings', 'admin_plugins_settings', $core_dir. '/admin/admin_plugins_settings.php');
  $api->add_event('action=admin&sa=settings', 'admin_settings', $core_dir. '/admin/admin_settings.php');
  $api->add_event('action=admin&sa=themes', 'admin_themes', $core_dir. '/admin/admin_themes.php');
  $api->add_event('action=admin&sa=update', 'admin_update', $core_dir. '/admin/admin_update.php');
  $api->add_event('action=checkcookie', 'checkcookie_verify', $core_dir. '/checkcookie.php');
  $api->add_event('action=login', 'login_view', $core_dir. '/login.php');
  $api->add_event('action=login2', 'login_view2', $core_dir. '/login.php');
  $api->add_event('action=logout', 'logout_process', $core_dir. '/logout.php');
  $api->add_event('action=profile', 'profile_view', $core_dir. '/profile.php');
  $api->add_event('action=profile&id=*', 'profile_view', $core_dir. '/profile.php');
  $api->add_event('action=register', 'register_view', $core_dir. '/register.php');
  $api->add_event('action=register2', 'register_process', $core_dir. '/register.php');
  $api->add_event('action=resend', 'resend_view', $core_dir. '/resend.php');
  $api->add_event('action=resource', 'api_handle_resource');
  $api->add_event('action=reminder', 'reminder_view', $core_dir. '/reminder.php');
  $api->add_event('action=reminder2', 'reminder_view2', $core_dir. '/reminder.php');
  $api->add_event('action=popup', 'core_popup');

  # Stop output buffering which was started in the <load_api> function.
  ob_end_clean();

  # Start output buffering.
  ob_start($api->apply_filters('output_callback', null));

  # Check to see if admin_prepend needs to be called.
  reset($_GET);
  if(key($_GET) == 'action' && current($_GET) == 'admin')
  {
    require_once($core_dir. '/admin.php');

    admin_prepend();
  }
}

if(!function_exists('core_popup'))
{
  /*
    Function: core_popup

    Displays the popup dialog content of the specified popup.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      To make a popup, simply apply a filter to popup_{ID_HERE}, for
      example if the popup identifier is timeformat, apply a filter to
      popup_timeformat. Be sure that if the popup information should only
      be available to a certain member group, check using the
      <Member::is_a> method before applying the filter.
  */
  function core_popup()
  {
    global $api, $func;

    if(empty($_GET['id']))
    {
      die(l('No popup identifier supplied.'));
    }

    # Collect the popup information.
    $popup = $api->apply_filters('popup_'. $_GET['id'], array('title' => '', 'content'));

    # We need title and content for the popup.
    if(empty($popup['title']) || empty($popup['content']))
    {
      die(l('Invalid popup identifier'));
    }

    # Now simply output the popup.
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>', $func['htmlspecialchars']($popup['title']), '</title>
  <style type="text/css">
    ', $api->apply_filters('core_popup_css', 'body { background: #FFFFFF; font-family: Tahoma, Arial, sans-serif; font-size: 90%; }
h1 { font-size: 115%; color: #3465A7; margin-top: 15px; }'), '
  </style>
</head>
<body>
  <h1>', $func['htmlspecialchars']($popup['title']), '</h1>
  ', $popup['content'], '
</body>
</html>';
  }
}
?>