<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# Controls everything Administrative!
#
# void admin_switch();
#   - The core of the Administration system and it branches out through
#     the $admin_actions array.
#
# void admin_home();
#
# mixed admin_verify([bool $return = false]);
#   - For security, every so often, or when need be, you can be prompted
#     for your password just to be double sure, you are you :D
#   bool $return - For use by maybe AJAX interface, you can set this
#                  to true, and it will return whether or not they need
#                  to verify themselves or not. If left at false, the
#                  screen will be shown :)
#   returns mixed - If $return is left at false, nothing is returned,
#                   otherwise a bool is returned (True for they are verified
#                   false if they are not verified)
#
# void admin_help();
#
# void admin_verify_ajax();
#   - Provides an interface for users to verify themselves as an Administrator,
#     or at least that they have permission to access the ACP through an AJAXy
#     way ;) (index.php?action=interface;sa=adminVerify)
#

function admin_switch()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # Can you admin this?
  error_screen('view_admin_panel');

  # Default admin language.
  language_load('admin');

  # Just a note... I know language support isn't built in yet...
  # I know ._.
  # Its just its in planning right now so I go with the flow :P

  # Do you need verifying? Either you haven't yet, or your WRONG!
  admin_verify();

  # Just go on... admin_verify() will fend for itself :) and us too! XD.
  # Just like other arrays, but a bit different...
  $admin_actions = array(
    'main' => array(
                'name' => $l['admin_menu_main'],
                'title' => '',
                'viewable' => true,
                'default' => 'index',
                'areas' => array(
                             'index' => array(
                                          'name' => $l['admin_menu_index'],
                                          'title' => $l['admin_menu_index_title'],
                                          'viewable' => true,
                                          'file' => 'admin.php',
                                          'function' => 'admin_home',
                                        ),
                             'modcenter' => array(
                                              'name' => $l['admin_menu_modifications'],
                                              'title' => $l['admin_menu_modifications_title'],
                                              'viewable' => can('install_mods') || can('uninstall_mods') || can('manage_mods'),
                                              'file' => 'admin_mod.php',
                                              'function' => 'mod_switch',
                                            ),
                           ),
              ),
    'settings' => array(
                'name' => $l['admin_menu_settings'],
                'title' => '',
                'viewable' => can('manage_main') || can('manage_mail') || can('manage_registration') || can('manage_themes') || can('manage_emoticons'),
                'default' => 'core',
                'areas' => array(
                             'core' => array(
                                         'name' => $l['admin_menu_core_settings'],
                                         'title' => $l['admin_menu_core_settings_title'],
                                         'viewable' => can('manage_settings'),
                                         'file' => 'admin_settings.php',
                                         'function' => 'settings_core',
                                       ),
                             'forum' => array(
                                          'name' => $l['admin_menu_forum_settings'],
                                          'title' => $l['admin_menu_forum_settings_title'],
                                          'viewable' => !empty($settings['forum_enabled']),
                                          'file' => 'admin_settings.php',
                                          'function' => 'settings_forum',
                                        ),
                             'mail' => array(
                                         'name' => $l['admin_menu_mail_settings'],
                                         'title' => $l['admin_menu_mail_settings_title'],
                                         'viewable' => can('manage_mail'),
                                         'file' => 'admin_settings.php',
                                         'function' => 'settings_mail',
                                       ),
                            'registration' => array(
                                                'name' => $l['admin_menu_members_registration'],
                                                'title' => $l['admin_menu_members_registration_title'],
                                                'viewable' => can('manage_registration'),
                                                'file' => 'admin_members.php',
                                                'function' => 'members_options',
                                              ),
                             'themes' => array(
                                           'name' => $l['admin_menu_theme_settings'],
                                           'title' => $l['admin_menu_theme_settings_title'],
                                           'viewable' => can('manage_themes'),
                                           'file' => 'admin_settings.php',
                                           'function' => 'settings_theme',
                                         ),
                             'emoticon' => array(
                                           'name' => $l['admin_menu_emoticons'],
                                           'title' => $l['admin_menu_emoticons_title'],
                                           'viewable' => can('manage_emoticons'),
                                           'file' => 'admin_settings.php',
                                           'function' => 'settings_emoticons',
                                         ),
                           ),
              ),
    'news' => array(
                'name' => $l['admin_menu_news'],
                'title' => '',
                'viewable' => can('manage_news'),
                'default' => 'manage',
                'areas' => array(
                             'add' => array(
                                        'name' => $l['admin_menu_news_add'],
                                        'title' => $l['admin_menu_news_add_title'],
                                        'viewable' => true,
                                        'file' => 'admin_news.php',
                                        'function' => 'news_add',
                                      ),
                             'manage' => array(
                                           'name' => $l['admin_menu_news_manage'],
                                           'title' => $l['admin_menu_news_manage_title'],
                                           'viewable' => true,
                                           'file' => 'admin_news.php',
                                           'function' => 'news_manage',
                                         ),
                             'categories' => array(
                                               'name' => $l['admin_menu_news_categories'],
                                               'title' => $l['admin_menu_news_categories_title'],
                                               'viewable' => true,
                                               'file' => 'admin_news.php',
                                               'function' => 'news_categories',
                                             ),
                           ),
              ),
    'menus' => array(
                'name' => $l['admin_menu_menus'],
                'title' => '',
                'viewable' => can('manage_menus'),
                'default' => 'manage',
                'areas' => array(
                             'add' => array(
                                        'name' => $l['admin_menu_menus_add'],
                                        'title' => $l['admin_menu_menus_add_title'],
                                        'viewable' => true,
                                        'file' => 'admin_menus.php',
                                        'function' => 'menus_add',
                                      ),
                             'manage' => array(
                                           'name' => $l['admin_menu_menus_manage'],
                                           'title' => $l['admin_menu_menus_manage_title'],
                                           'viewable' => true,
                                           'file' => 'admin_menus.php',
                                           'function' => 'menus_manage',
                                         ),
                           ),
              ),
    'downloads' => array(
                     'name' => $l['admin_menu_downloads'],
                     'title' => '',
                     'viewable' => !empty($settings['downloads_enabled']) && can('manage_downloads'),
                     'default' => 'settings',
                     'areas' => array(
                                  'settings' => array(
                                                  'name' => $l['admin_menu_downloads_settings'],
                                                  'title' => $l['admin_menu_downloads_settings_title'],
                                                  'viewable' => true,
                                                  'file' => 'admin_downloads.php',
                                                  'function' => 'downloads_settings',
                                                ),
                                  'list' => array(
                                              'name' => $l['admin_menu_downloads_view'],
                                              'title' => $l['admin_menu_downloads_view_title'],
                                              'viewable' => true,
                                              'file' => 'admin_downloads.php',
                                              'function' => 'downloads_manage',
                                            ),
                                  'maintain' => array(
                                                  'name' => $l['admin_menu_downloads_maintenance'],
                                                  'title' => $l['admin_menu_downloads_maintenance_title'],
                                                  'viewable' => true,
                                                  'file' => 'admin_downloads.php',
                                                  'function' => 'downloads_maintain',
                                                ),
                                ),
                 ),
    'pages' => array(
                'name' => $l['admin_menu_pages'],
                'title' => '',
                'viewable' => can('manage_pages_snowtext') || can('manage_pages_html') || can('manage_pages_bbcode'),
                'default' => 'manage',
                'areas' => array(
                             'create' => array(
                                           'name' => $l['admin_menu_pages_create'],
                                           'title' => $l['admin_menu_pages_create_title'],
                                           'viewable' => true,
                                           'file' => 'admin_pages.php',
                                           'function' => 'pages_add',
                                         ),
                             'manage' => array(
                                           'name' => $l['admin_menu_pages_manage'],
                                           'title' => $l['admin_menu_pages_manage_title'],
                                           'viewable' => true,
                                           'file' => 'admin_pages.php',
                                           'function' => 'pages_manage',
                                         ),
                           ),
               ),
    'members' => array(
                   'name' => $l['admin_menu_members'],
                'title' => '',
                   'viewable' => can('manage_members') || can('register_members') || can('manage_membergroups'),
                   'default' => 'list',
                   'areas' => array(
                                'list' => array(
                                            'name' => $l['admin_menu_members_list'],
                                            'title' => $l['admin_menu_members_list_title'],
                                            'viewable' => can('manage_members'),
                                            'file' => 'admin_members.php',
                                            'function' => 'members_manage',
                                          ),
                                'register' => array(
                                            'name' => $l['admin_menu_members_register'],
                                            'title' => $l['admin_menu_members_register_title'],
                                            'viewable' => can('register_members'),
                                            'file' => 'admin_members.php',
                                            'function' => 'members_register',
                                          ),
                                'addgroup' => array(
                                                'name' => $l['admin_menu_members_group_add'],
                                                'title' => $l['admin_menu_members_group_add_title'],
                                                'viewable' => can('manage_membergroups'),
                                                'file' => 'admin_membergroups.php',
                                                'function' => 'membergroups_add',
                                              ),
                                'membergroups' => array(
                                                    'name' => $l['admin_menu_members_group_manage'],
                                                    'title' => $l['admin_menu_members_group_manage_title'],
                                                    'viewable' => can('manage_membergroups'),
                                                    'file' => 'admin_membergroups.php',
                                                    'function' => 'membergroups_manage',
                                                  ),
                              ),
                 ),
    'forum' => array(
                 'name' => $l['admin_menu_forum'],
                'title' => '',
                 'viewable' => !empty($settings['forum_enabled']) && can('manage_forum'),
                 'default' => 'boards',
                 'areas' => array(
                              'addboard' => array(
                                              'name' => $l['admin_menu_forum_add_board'],
                                              'title' => $l['admin_menu_forum_add_board_title'],
                                              'viewable' => true,
                                              'file' => 'admin_forum.php',
                                              'function' => 'forum_board_add',
                                            ),
                              'boards' => array(
                                            'name' => $l['admin_menu_forum_manage_boards'],
                                            'title' => $l['admin_menu_forum_manage_boards_title'],
                                            'viewable' => true,
                                            'file' => 'admin_forum.php',
                                            'function' => 'forum_manage',
                                          ),
                              'addcategory' => array(
                                                 'name' => $l['admin_menu_forum_add_category'],
                                                 'title' => $l['admin_menu_forum_add_category_title'],
                                                 'viewable' => true,
                                                 'file' => 'admin_forum.php',
                                                 'function' => 'forum_category_add',
                                               ),
                            ),
               ),
    'permissions' => array(
                       'name' => $l['admin_menu_permissions'],
                'title' => '',
                       'viewable' => can('manage_permissions'),
                       'default' => 'groups',
                       'areas' => array(
                                    'groups' => array(
                                                  'name' => $l['admin_menu_permissions_membergroup'],
                                                  'title' => $l['admin_menu_permissions_membergroup_title'],
                                                  'viewable' => true,
                                                  'file' => 'admin_permissions.php',
                                                  'function' => 'permissions_membergroups',
                                                ),
                                    'forum' => array(
                                                 'name' => $l['admin_menu_permissions_forum'],
                                                 'title' => $l['admin_menu_permissions_forum_title'],
                                                 'viewable' => !empty($settings['forum_enabled']),
                                                 'file' => 'admin_permissions.php',
                                                 'function' => 'permissions_forum',
                                               ),
                                  ),
                     ),
    'maintenance' => array(
                       'name' => $l['admin_menu_maintenance'],
                'title' => '',
                       'viewable' => can('maintain_database') || can('maintain_forum') || can('maintain_tasks') || can('maintain_error_log'),
                       'default' => 'database',
                       'areas' => array(
                                    'database' => array(
                                                    'name' => $l['admin_menu_maintenance_database'],
                                                    'title' => $l['admin_menu_maintenance_database_title'],
                                                    'viewable' => can('maintain_database'),
                                                    'file' => 'admin_maintain.php',
                                                    'function' => 'maintain_database',
                                                  ),
                                    'forum' => array(
                                                 'name' => $l['admin_menu_maintenance_forum'],
                                                 'title' => $l['admin_menu_maintenance_forum_title'],
                                                 'viewable' => !empty($settings['forum_enabled']) && can('maintain_forum'),
                                                 'file' => 'admin_maintain.php',
                                                 'function' => 'maintain_forum',
                                               ),
                                    'tasks' => array(
                                                 'name' => $l['admin_menu_maintenance_tasks'],
                                                 'title' => $l['admin_menu_maintenance_tasks_title'],
                                                 'viewable' => !empty($settings['enable_tasks']) && can('maintain_tasks'),
                                                 'file' => 'admin_maintain.php',
                                                 'function' => 'maintain_tasks',
                                               ),
                                    'error_log' => array(
                                                 'name' => $l['admin_menu_maintenance_error_log'],
                                                 'title' => $l['admin_menu_maintenance_error_log_title'],
                                                 'viewable' => can('maintain_error_log'),
                                                 'file' => 'admin_maintain.php',
                                                 'function' => 'maintain_errors',
                                               ),
                                  ),
                     ),
  );

  # Build the cool Admin Menu!
  $page['show_adminMenu'] = true;

  # We need the admin actions...
  $page['adminActions'] = $admin_actions;

  # So what do you want? >_>
  $sa = !empty($_GET['sa']) ? $_GET['sa'] : 'main';

  # Area? None set? Default! :D
  $area = !empty($_GET['area']) ? $_GET['area'] : (isset($admin_actions[$sa]['default']) ? $admin_actions[$sa]['default'] : '');

  # For the Admin menu... That way we know where we are :)
  $page['sa'] = $sa;
  $page['area'] = $area;

  # So does this sub action exist? Can you access it?
  if(isset($admin_actions[$sa]) && $admin_actions[$sa]['viewable'] && isset($admin_actions[$sa]['areas'][$area]) && $admin_actions[$sa]['areas'][$area]['viewable'])
  {
    # So lets get the file, and call on the function, and we are done here.
    require_once($source_dir. '/'. $admin_actions[$sa]['areas'][$area]['file']);

    $admin_actions[$sa]['areas'][$area]['function']();
  }
  else
    # Sorry, you can't access this!
    error_screen();
}

function admin_home()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # The Administrative home! Yay.
  $page['title'] = $l['admin_home_title'];
  
  # The Admin News :)
  $page['snowcms_news'] = !empty($settings['current_news']) ? '<div class="admincp_news">'. preg_replace('/{{(\d+)}}/e', 'timeformat(\'$1\')', strip_tags($settings['current_news'], '<span><p><a><strong><b><i><em><br>')). '</div>' : '<p class="center">'. $l['admin_unable_to_retrieve_news']. '</p>';

  # The current SnowCMS version, check if its old...
  $page['scmsVersion'] = 'v'. $settings['scmsVersion'];
  $page['current_version'] = !empty($settings['current_version']) ? $settings['current_version'] : '??';

  # Maybe show an alert..?
  $page['upgrade_needed'] = (!version_compare($page['current_version'], $settings['scmsVersion'], '<=') && $page['current_version'] != '??') ? true : false;

  # Add a v infront, if its not ??
  if($page['current_version'] != '??')
    $page['current_version'] = 'v'. $page['current_version'];

  # System information :)
  $page['php_version'] = PHP_VERSION;

  # OS, WINNT Looks Ugly, so turn it into Windows :)
  $page['operating_system'] = get_os();

  # Database name :P
  $page['db_type'] = $db->type;

  # Database Version
  $page['db_version'] = $db->version();

  # Build the array of icons :)
  $page['icons'] = array();

  # Not all are added, but some are.. :P
  if(can('manage_mods'))
    $page['icons'][] = array(
                         'name' => $l['admin_icons_modifications_name'],
                         'title' => $l['admin_icons_modifications_title'],
                         'desc' => $l['admin_icons_modifications_desc'],
                         'href' => $base_url. '/index.php?action=admin;sa=main;area=modcenter',
                         'image' => 'admin-modifications.png',
                       );

  if(can('manage_settings'))
    $page['icons'][] = array(
                         'name' => $l['admin_icons_settings_name'],
                         'title' => $l['admin_icons_settings_title'],
                         'desc' => $l['admin_icons_settings_desc'],
                         'href' => $base_url. '/index.php?action=admin;sa=settings;area=core',
                         'image' => 'admin-settings.png',
                       );

  if(can('manage_news'))
    $page['icons'][] = array(
                         'name' => $l['admin_icons_news_name'],
                         'title' => $l['admin_icons_news_title'],
                         'desc' => $l['admin_icons_news_desc'],
                         'href' => $base_url. '/index.php?action=admin;sa=news;area=manage',
                         'image' => 'admin-news.png',
                       );

  if(!empty($settings['downloads_enabled']) && can('manage_downloads'))
    $page['icons'][] = array(
                         'name' => $l['admin_icons_downloads_name'],
                         'title' => $l['admin_icons_downloads_title'],
                         'desc' => $l['admin_icons_downloads_desc'],
                         'href' => $base_url. '/index.php?action=admin;sa=downloads;area=settings',
                         'image' => 'admin-downloads.png',
                       );

  if(can('manage_pages_snowtext') || can('manage_pages_html') || can('manage_pages_bbcode'))
    $page['icons'][] = array(
                         'name' => $l['admin_icons_pages_name'],
                         'title' => $l['admin_icons_pages_title'],
                         'desc' => $l['admin_icons_pages_desc'],
                         'href' => $base_url. '/index.php?action=admin;sa=pages;area=manage',
                         'image' => 'admin-pages.png',
                       );

  if(can('manage_members'))
    $page['icons'][] = array(
                         'name' => $l['admin_icons_members_name'],
                         'title' => $l['admin_icons_members_title'],
                         'desc' => $l['admin_icons_members_desc'],
                         'href' => $base_url. '/index.php?action=admin;sa=members;area=list',
                         'image' => 'admin-members.png',
                       );

  if(!empty($settings['forum_enabled']) && can('manage_forum'))
    $page['icons'][] = array(
                         'name' => $l['admin_icons_forum_name'],
                         'title' => $l['admin_icons_forum_title'],
                         'desc' => $l['admin_icons_forum_desc'],
                         'href' => $base_url. '/index.php?action=admin;sa=forum;area=boards',
                         'image' => 'admin-forum.png',
                       );

  if(can('maintain_database'))
    $page['icons'][] = array(
                         'name' => $l['admin_icons_database_name'],
                         'title' => $l['admin_icons_database_title'],
                         'desc' => $l['admin_icons_database_desc'],
                         'href' => $base_url. '/index.php?action=admin;sa=maintenance;area=database',
                         'image' => 'admin-maintenance.png',
                       );

  # Load the admin home template ;)
  theme_load('admin', 'admin_home_show');
}

function admin_verify($return = false)
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # This is whether or not we are actually processing the verification. Right now, let's give it a no.
  $verifying = false;
  
  # You verifying?
  if(isset($_POST['verify']) && $user['is_logged'] && (!empty($_POST['hashed_passwrd']) || !empty($_POST['passwrd'])) && can('view_admin_panel'))
  {
    # Mark that we're verifying.
    $verifying = true;

    # Lets get the password :D
    # So we should check if it is hashed...
    if(empty($_POST['hashed_passwrd']))
      # Nope I guess :P
      $password = sha1($_POST['passwrd']);
    else
      # It is hashed already, or at least we think... with the secret key XD
      $password = $_POST['hashed_passwrd'];

    # So lets check :)
    $result = $db->query("
      SELECT
        member_id, passwrd
      FROM {$db->prefix}members
      WHERE member_id = %member_id
      LIMIT 1",
    array(
      'member_id' => array('int', $user['id'])
    ));
    $row = $db->fetch_assoc($result);

    # So is it right?
    if(!empty($_SESSION['old_loginHash']) && $password == sha1($row['passwrd']. $_SESSION['old_loginHash']))
      $verified = true;
    elseif($password == $row['passwrd'])
      $verified = true;
    else
      $verified = false;
    
    # Very well Sir, I guess you are who you say you are.
    if($verified)
    {
      # So lets make something random :)
      $adminSc = sha1(filemtime($source_dir. '/corecms.php'). microtime(true). mt_rand(). rand_string(6));

      # So set it to their session and account :)
      $_SESSION['adminSc'] = $adminSc;

      # This is just for temporary reasons :)
      $user['adminSc'] = $adminSc;
      
      # Save!
      $db->query("
        UPDATE {$db->prefix}members
        SET adminSc = %adminSc
        WHERE member_id = %member_id
        LIMIT 1",
        array(
          'member_id' => array('int', $user['id']),
          'adminSc' => array('string-40', $adminSc),
        ));

      $_SESSION['last_admin_verify'] = time();
    }
  }

  # So do you need verification..?
  if(empty($_SESSION['adminSc']) || $user['adminSc'] != $_SESSION['adminSc'] || !isset($_SESSION['last_admin_verify']) || ((int)$_SESSION['last_admin_verify'] + 1800) < time())
  {
    # Wait... Returning?
    if($return)
      return false;

    # Not returning, so go on.
    # Load the language.
    language_load('Admin');

    # Set the page title.
    $page['title'] = $l['admin_verify_title'];

    # No indexy! Shouldn't be here anyways though.
    $page['no_index'] = true;

    # Login failed? That way we can give them a message, just incase.
    $page['login_failed'] = $verifying;
    
    # Load the theme and we are done!
    theme_load('admin', 'admin_verify_show');

    # And exit!
    exit;
  }
  elseif($return)
    return true;
}

function admin_help()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $theme_url, $user;

  error_screen('view_admin_panel');

  # Load the right language file...
  language_load('help');

  # Variable..?
  $var = !empty($_GET['var']) ? (string)'_'. $_GET['var'] : '';

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>', !empty($l['popup_admin'. $var. '_title']) ? $l['popup_admin'. $var. '_title'] : $l['popup_admin_title'], '</title>
	<link rel="stylesheet" type="text/css" href="', $theme_url, '/', $settings['theme'], '/style.css" />
</head>
<body class="help_bg">
<div class="help_text">
  ', strtr(!empty($l['popup_admin'. $var. '_desc']) ? $l['popup_admin'. $var. '_desc'] : $l['popup_admin_invalid'], array("\n" => '<br />')), '
  <br /><br />
  <p style="text-align: center;"><a href="javascript:window.close();">', $l['popup_admin_close'], '</a></p>
</div>
</body>
</html>';
}

function admin_verify_ajax()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # First off, lets make sure you are permitted to access the ACP.
  if(!can('view_admin_panel'))
  {
    echo json_encode(array('error' => $l['ajax_access_denied']));
    exit;
  }

  # Our oh so useful output array.
  $output = array('error' => '');

  # Password supplied? ._.
  if(!empty($_REQUEST['passwrd']))
  {
    # So far, you aren't verified :P
    $verified = false;

    # Lets get the password :D
    # So we should check if it is hashed...
    if(empty($_REQUEST['hashed_passwrd']))
      # Nope I guess :P
      $password = sha1($_REQUEST['passwrd']);
    else
      # It is hashed already, or at least we think... with the secret key XD
      $password = $_REQUEST['hashed_passwrd'];

    # So lets check :)
    $result = $db->query("
      SELECT
        mem.member_id, mem.passwrd
      FROM {$db->prefix}members AS mem
      WHERE mem.member_id = %member_id
      LIMIT 1",
    array(
      'member_id' => array('int', $user['id'])
    ));
    $row = $db->fetch_assoc($result);

    # So is it right?
    if($password == sha1($row['passwrd']. $_SESSION['old_loginHash']) && !empty($_SESSION['old_loginHash']))
      $verified = true;
    elseif($password == $row['passwrd'])
      $verified = true;

    if($verified)
    {
      # It was right! Congrats!
      # So lets make something random :)
      $adminSc = sha1(filemtime($source_dir. '/corecms.php'). microtime(true). mt_rand(). rand_string(6));

      # Save it to the user.
      $db->query("
        UPDATE {$db->prefix}members
        SET adminSc = %adminSc
        WHERE member_id = %member_id
        LIMIT 1",
      array(
        'member_id' => array('int', $user['id']),
        'adminSc' => array('string-40', $adminSc)
      ));

      # So you are verified...
      $output['verified'] = true;
    }
    else
      # Wrong Password! ._.
      $output['error'] = $l['ajax_password_incorrect'];
  }
  else
    # Uh...
    $output['error'] = $l['ajax_no_password_supplied'];

  # Output and we are done.
  echo json_encode($output);
}
?>