<?php
#
# Admin English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings, $user;

# Verification vars.
$l['admin_verify_title'] = 'Verify Yourself - '. $settings['site_name'];
$l['admin_verify_header'] = 'Verify Yourself';
$l['admin_verify_message'] = 'In order to continue, you must enter your account password to verify yourself.';
$l['admin_verify_button'] = 'Verify';
$l['admin_verify_failed'] = 'Password incorrect.';

# Admin menu vars.
$l['admin_menu_main'] = 'Main';
$l['admin_menu_index'] = 'Admin Center';
$l['admin_menu_index_title'] = 'Administration Center';
$l['admin_menu_modifications'] = 'Mod Center';
$l['admin_menu_modifications_title'] = 'Manage your modifications';
$l['admin_menu_settings'] = 'Settings';
$l['admin_menu_core_settings'] = 'Core settings';
$l['admin_menu_core_settings_title'] = 'Configure core settings';
$l['admin_menu_forum_settings'] = 'Forum settings';
$l['admin_menu_forum_settings_title'] = 'Configure your forum';
$l['admin_menu_mail_settings'] = 'Mail settings';
$l['admin_menu_mail_settings_title'] = 'Configure mail settings';
$l['admin_menu_theme_settings'] = 'Theme settings';
$l['admin_menu_theme_settings_title'] = 'Add and manage themes';
$l['admin_menu_emoticons'] = 'Emoticons';
$l['admin_menu_emoticons_title'] = 'Manage emoticons and emoticon packs.';
$l['admin_menu_news'] = 'News';
$l['admin_menu_news_add'] = 'Add News';
$l['admin_menu_news_add_title'] = 'Add News Articles';
$l['admin_menu_news_manage'] = 'Manage News';
$l['admin_menu_news_manage_title'] = 'Manage existing News Articles';
$l['admin_menu_news_categories'] = 'Manage Categories';
$l['admin_menu_news_categories_title'] = 'Manage News Categories';
$l['admin_menu_menus'] = 'Menus';
$l['admin_menu_menus_add'] = 'Add Link';
$l['admin_menu_menus_add_title'] = 'Add new link';
$l['admin_menu_menus_manage'] = 'Manage Menus';
$l['admin_menu_menus_manage_title'] = 'Manage existing links';
$l['admin_menu_downloads'] = 'Downloads';
$l['admin_menu_downloads_settings'] = 'Settings';
$l['admin_menu_downloads_settings_title'] = 'Downloads Settings';
$l['admin_menu_downloads_view'] = 'View Downloads';
$l['admin_menu_downloads_view_title'] = 'View current downloads';
$l['admin_menu_downloads_maintenance'] = 'Maintain';
$l['admin_menu_downloads_maintenance_title'] = 'Downloads Maintenance';
$l['admin_menu_pages'] = 'Pages';
$l['admin_menu_pages_create'] = 'Create a Page';
$l['admin_menu_pages_create_title'] = 'Create a new page';
$l['admin_menu_pages_manage'] = 'Manage Pages';
$l['admin_menu_pages_manage_title'] = 'Manage existing Pages';
$l['admin_menu_members'] = 'Members';
$l['admin_menu_members_list'] = 'View Members';
$l['admin_menu_members_list_title'] = 'View all your members';
$l['admin_menu_members_register'] = 'Register New Member';
$l['admin_menu_members_register_title'] = 'Register a new member';
$l['admin_menu_members_registration'] = 'Registration';
$l['admin_menu_members_registration_title'] = 'Registration Settings';
$l['admin_menu_members_group_add'] = 'Add Member Group';
$l['admin_menu_members_group_add_title'] = 'Add a new member group';
$l['admin_menu_members_group_manage'] = 'Manage Membergroups';
$l['admin_menu_members_group_manage_title'] = 'Manage existing membergroups';
$l['admin_menu_forum'] = 'Forum';
$l['admin_menu_forum_add_board'] = 'Add Board';
$l['admin_menu_forum_add_board_title'] = 'Create a new board';
$l['admin_menu_forum_manage_boards'] = 'Manage Boards';
$l['admin_menu_forum_manage_boards_title'] = 'Manage existing boards';
$l['admin_menu_forum_add_category'] = 'Add Category';
$l['admin_menu_forum_add_category_title'] = 'Create a new category';
$l['admin_menu_permissions'] = 'Permissions';
$l['admin_menu_permissions_membergroup'] = 'Member Group Permissions';
$l['admin_menu_permissions_membergroup_title'] = 'Manage member group permissions';
$l['admin_menu_permissions_forum'] = 'Forum Permissions';
$l['admin_menu_permissions_forum_title'] = 'Manage forum permissions';
$l['admin_menu_maintenance'] = 'Maintenance';
$l['admin_menu_maintenance_database'] = 'Database';
$l['admin_menu_maintenance_database_title'] = 'Backup and optimize your database';
$l['admin_menu_maintenance_forum'] = 'Forum';
$l['admin_menu_maintenance_forum_title'] = 'Forum tools and Maintenance';
$l['admin_menu_maintenance_tasks'] = 'Tasks';
$l['admin_menu_maintenance_tasks_title'] = 'Add and manage automated tasks';
$l['admin_menu_maintenance_error_log'] = 'Error Log';
$l['admin_menu_maintenance_error_log_title'] = 'View Error Logs';

# Admin icons
$l['admin_icons_modifications_name'] = 'Modifications';
$l['admin_icons_modifications_title'] = 'Manage Modifications';
$l['admin_icons_modifications_desc'] = 'Download, apply and manage modifications.';
$l['admin_icons_settings_name'] = 'Core Settings';
$l['admin_icons_settings_title'] = 'Configure Core Settings';
$l['admin_icons_settings_desc'] = 'Configure core settings for your site.';
$l['admin_icons_news_name'] = 'News Center';
$l['admin_icons_news_title'] = 'Manage News Article';
$l['admin_icons_news_desc'] = 'Manage all of the news articles.';
$l['admin_icons_downloads_name'] = 'Downloads Center';
$l['admin_icons_downloads_title'] = 'Downloads Configuration';
$l['admin_icons_downloads_desc'] = 'Configure your downloads center.';
$l['admin_icons_pages_name'] = 'Manage Pages';
$l['admin_icons_pages_title'] = 'Manage Pages';
$l['admin_icons_pages_desc'] = 'Manage all of your existing pages.';
$l['admin_icons_members_name'] = 'Manage Members';
$l['admin_icons_members_title'] = 'Manage Members';
$l['admin_icons_members_desc'] = 'View a member list and manage them by activating or moderating.';
$l['admin_icons_forum_name'] = 'Manage Forum';
$l['admin_icons_forum_title'] = 'Manage Forum Boards';
$l['admin_icons_forum_desc'] = 'Manage existing forum boards.';
$l['admin_icons_database_name'] = 'Database Maintenance';
$l['admin_icons_database_title'] = 'Run Database Maintenance';
$l['admin_icons_database_desc'] = 'Run essential maintenance on your database such as optimizing and creating backups.';

# Admin home!
$l['admin_home_title'] = 'Administration Center -'. $settings['site_name'];
$l['admin_header'] = 'Welcome, '. $user['name']. '!';
$l['admin_desc'] = 'Welcome to your Administration Center! Here you can manage various settings, options and features of your SnowCMS Powered website, depending upon your permissions. If you are having problems or need help, you can always seek some help with us at <a href="http://www.snowcms.com/" target="_blank">SnowCMS.com</a>.';
$l['admin_snowcms_news'] = 'SnowCMS News';
$l['admin_unable_to_retrieve_news'] = 'Sorry, but the news couldn\'t be retrieved from the SnowCMS site at the time of retrieval.';
$l['admin_system_info'] = 'System Information';
$l['admin_software_version'] = 'Your Version:';
$l['admin_latest_version'] = 'Latest Version:';
$l['admin_php_version'] = 'PHP Version:';
$l['admin_db_version'] = '%s Version:';
$l['admin_operating_system'] = 'Operating System:';
$l['admin_upgrade_message'] = 'Alert! It appears you are running an older version of SnowCMS. It is recommended you download the latest from <a href="http://www.snowcms.com/" target="_blank">SnowCMS.com</a> and upgrade ASAP!';
?>