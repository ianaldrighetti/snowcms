<?php
#
# Admin English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Add link stuff
$l['admin_menus_add_title'] = 'Add Link - '. $settings['site_name'];
$l['admin_menus_add_header'] = 'Add Link';
$l['admin_menus_add_desc'] = 'Add a link to your menus here.';
$l['admin_menus_add_menu'] = 'Menu %s';
$l['setting_link_name'] = 'Name';
$l['setting_link_href'] = 'URL';
$l['setting_link_target'] = 'Open in new window';
$l['setting_link_follow'] = 'Allow search engines to follow';
$l['setting_sub_link_follow'] = 'Uncheck for advertisements or paid links.';
$l['setting_link_order'] = 'Place after link';
$l['setting_sub_link_order'] = 'Or select a menu to place it at the start of that menu.';
$l['setting_label_groups'] = 'Which groups can access this link?';

# Manage menus stuff
$l['admin_menus_manage_title'] = 'Manage Menus - '. $settings['site_name'];
$l['admin_menus_manage_header'] = 'Manage Menus';
$l['admin_menus_manage_desc'] = 'Manage your links and menus.';
$l['admin_menus_manage_menu'] = 'Menu %s';
$l['admin_menus_manage_name'] = 'Name';
$l['admin_menus_manage_url'] = 'URL';
$l['admin_menus_manage_window'] = 'Window';
$l['admin_menus_manage_window_same'] = 'Same';
$l['admin_menus_manage_window_new'] = 'New';
$l['admin_menus_manage_follow'] = 'Follow';
$l['admin_menus_manage_follow_yes'] = 'Yes';
$l['admin_menus_manage_follow_no'] = 'No';
$l['admin_menus_manage_edit'] = 'Edit';
$l['admin_menus_manage_delete'] = 'Delete';
$l['admin_menus_manage_raise'] = 'Raise Order';
$l['admin_menus_manage_lower'] = 'Lower Order';
$l['admin_menus_manage_no_links'] = 'No links';
?>