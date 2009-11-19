<?php
#
#   Settings English file for SnowCMS
#    Created by the SnowCMS Dev Team
#          www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Member groups permissions stuff
$l['permissions_membergroups_title'] = 'Member Group Permissions - '. $settings['site_name'];
$l['permissions_membergroups_header'] = 'Member Group Permissions';
$l['permissions_membergroups_desc'] = 'Managing member group permissions.';
$l['permissions_membergroups_name'] = 'Name';
$l['permissions_membergroups_members'] = 'Total Members';
$l['permissions_membergroups_allowed_pm_size'] = 'Allowed PM Size';
$l['permissions_membergroups_unlimited'] = 'Unlimited';

# Editing member group permissions stuff
$l['permissions_edit_title'] = 'Editing %s Permissions - '. $settings['site_name'];
$l['permissions_edit_header'] = 'Editing %s Permissions';
$l['permissions_edit_desc'] = 'Editing the %s group\'s permissions.';
$l['permissions_edit_allowed_pm_size'] = 'Allowed PM space';
$l['permissions_edit_allowed_pm_size_desc'] = 'Leave blank for unlimited.';
$l['permissions_edit_submit'] = 'Save Changes';

# Invalid member group stuff
$l['permissions_edit_invalid_title'] = 'Invalid Member Group - '. $settings['site_name'];
$l['permissions_edit_invalid_header'] = 'Invalid Member Group';
$l['permissions_edit_invalid_desc'] = 'You have entered an invalid member group ID.';

# Editing member group permissions and categories
$l['permissions_edit_category_basic'] = 'Basic Permissions';
$l['permissions_edit_perm_view_profiles'] = 'View profiles';
$l['permissions_edit_perm_view_memberlist'] = 'View memberlist';
$l['permissions_edit_perm_view_stats'] = 'View stats';
$l['permissions_edit_perm_news_comment'] = 'Post news comments';
$l['permissions_edit_perm_download_comment'] = 'Post download comments';
$l['permissions_edit_perm_edit_news_comment'] = 'Edit own news comments';
$l['permissions_edit_perm_edit_download_comment'] = 'Edit own download comments';
$l['permissions_edit_perm_download_downloads'] = 'Download downloads';
$l['permissions_edit_perm_view_pms'] = 'View personal messages';
$l['permissions_edit_perm_compose_pms'] = 'Send personal messages';
$l['permissions_edit_perm_report_pms'] = 'Report personal messages';
$l['permissions_edit_perm_edit_profile'] = 'Edit own profile';
$l['permissions_edit_perm_edit_username'] = 'Edit own username';
$l['permissions_edit_perm_edit_display_name'] = 'Edit own display name';
$l['permissions_edit_perm_edit_date_registered'] = 'Edit own registration date';
$l['permissions_edit_perm_edit_post_count'] = 'Edit own post count';
$l['permissions_edit_perm_edit_membergroup'] = 'Edit own member group';
$l['permissions_edit_perm_edit_email'] = 'Edit own email address';
$l['permissions_edit_perm_edit_avatar'] = 'Edit own avatar';
$l['permissions_edit_perm_edit_signature'] = 'Edit own signature';
$l['permissions_edit_perm_edit_profile_text'] = 'Edit own profile text';
$l['permissions_edit_perm_upload_avatars'] = 'Upload avatars';
$l['permissions_edit_category_forum'] = 'Forum Permissions';
$l['permissions_edit_perm_view_forum'] = 'View forum';
$l['permissions_edit_perm_post_topic'] = 'Post new topics';
$l['permissions_edit_perm_post_reply'] = 'Post replies';
$l['permissions_edit_perm_post_poll'] = 'Post new poll topics';
$l['permissions_edit_perm_edit_post'] = 'Edit own posts';
$l['permissions_edit_perm_edit_poll'] = 'Edit own polls';
$l['permissions_edit_perm_delete_post'] = 'Delete own posts';
$l['permissions_edit_perm_delete_topic'] = 'Delete own topics';
$l['permissions_edit_perm_remove_poll'] = 'Remove polls';
$l['permissions_edit_perm_view_results'] = 'View poll results';
$l['permissions_edit_perm_cast_vote'] = 'Cast vote in a poll';
$l['permissions_edit_category_mod'] = 'Moderation Permissions';
$l['permissions_edit_perm_view_ips'] = 'View members\' IPs';
$l['permissions_edit_perm_moderate_pms'] = 'Moderate personal messages';
$l['permissions_edit_perm_ban_member'] = 'Ban members';
$l['permissions_edit_perm_unban_member'] = 'Unban members';
$l['permissions_edit_perm_suspend_member'] = 'Suspend members';
$l['permissions_edit_perm_unsuspend_member'] = 'Unsuspend members';
$l['permissions_edit_perm_add_poll_any'] = 'Add polls to any topic';
$l['permissions_edit_perm_edit_profile_any'] = 'Edit any member\'s profile';
$l['permissions_edit_perm_edit_username_any'] = 'Edit any member\'s username';
$l['permissions_edit_perm_edit_display_name_any'] = 'Edit any member\'s display name';
$l['permissions_edit_perm_edit_date_registered_any'] = 'Edit any member\'s registration date';
$l['permissions_edit_perm_edit_post_count_any'] = 'Edit any member\'s post count';
$l['permissions_edit_perm_edit_membergroup_any'] = 'Edit any member\'s member group';
$l['permissions_edit_perm_edit_email_any'] = 'Edit any member\'s email address';
$l['permissions_edit_perm_edit_avatar_any'] = 'Edit any member\'s avatar';
$l['permissions_edit_perm_edit_signature_any'] = 'Edit any member\'s signature';
$l['permissions_edit_perm_edit_profile_text_any'] = 'Edit any member\'s profile text';
$l['permissions_edit_perm_edit_post_any'] = 'Edit any forum posts';
$l['permissions_edit_perm_edit_news_comment_any'] = 'Edit any news comments';
$l['permissions_edit_perm_edit_download_comment_any'] = 'Edit any downloads comments';
$l['permissions_edit_perm_edit_poll_any'] = 'Edit any polls';
$l['permissions_edit_perm_delete_post_any'] = 'Delete any forum posts';
$l['permissions_edit_perm_delete_news_comment_any'] = 'Delete any news comments';
$l['permissions_edit_perm_delete_download_comment_any'] = 'Delete any download comments';
$l['permissions_edit_perm_delete_topic_any'] = 'Delete any topics';
$l['permissions_edit_perm_remove_poll_any'] = 'Remove polls from any topic';
$l['permissions_edit_category_admin'] = 'Administration Permissions';
$l['permissions_edit_perm_view_admin_panel'] = 'View admin panel';
$l['permissions_edit_perm_manage_settings'] = 'Manage main settings';
$l['permissions_edit_perm_manage_mail'] = 'Manage mail settings';
$l['permissions_edit_perm_manage_registration'] = 'Manage registration settings';
$l['permissions_edit_perm_manage_themes'] = 'Manage theme settings';
$l['permissions_edit_perm_manage_emoticons'] = 'Manage emoticons';
$l['permissions_edit_perm_manage_news'] = 'Manage news';
$l['permissions_edit_perm_manage_downloads'] = 'Manage downloads';
$l['permissions_edit_perm_manage_menus'] = 'Manage menus';
$l['permissions_edit_perm_manage_pages_snowtext'] = 'Manage SnowText pages';
$l['permissions_edit_perm_manage_pages_html'] = 'Manage HTML pages';
$l['permissions_edit_perm_manage_pages_bbcode'] = 'Manage BBCode pages';
$l['permissions_edit_perm_manage_members'] = 'Manage members';
$l['permissions_edit_perm_register_members'] = 'Register new members';
$l['permissions_edit_perm_manage_membergroups'] = 'Manage member groups';
$l['permissions_edit_perm_manage_forum'] = 'Manage forum';
$l['permissions_edit_perm_manage_permissions'] = 'Manage permissions';
$l['permissions_edit_perm_maintain_database'] = 'Maintain database';
$l['permissions_edit_perm_maintain_forum'] = 'Maintain forum';
$l['permissions_edit_perm_maintain_tasks'] = 'Maintain tasks';
$l['permissions_edit_perm_view_error_log'] = 'View error log';
$l['permissions_edit_category_mods'] = 'Mod Permissions';
$l['permissions_edit_perm_install_mods'] = 'Install mods';
$l['permissions_edit_perm_uninstall_mods'] = 'Uninstall mods';
$l['permissions_edit_perm_manage_mods'] = 'Manage mods';
?>