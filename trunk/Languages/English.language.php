<?php
// SnowCMS Main.language.php file for English
// Version 0.2

// Language's name
$l['language_name'] = 'English';

// Main.template.php
$l['main_language_go'] = 'Go';
$l['main_sidebar_login'] = 'Login';
$l['main_sidebar_logout'] = 'Logout';
$l['main_sidebar_register'] = 'Register';
$l['main_sidebar_profile'] = 'Profile';
$l['main_sidebar_control_panel'] = 'Control Panel';
$l['main_powered_by'] = 'Powered by %snowcms%';
$l['main_theme_by'] = 'Theme by %whom%';
$l['main_error'] = 'Error';

// Login.template.php stuff
$l['login_title'] = 'Login';
$l['login_header'] = 'Login';
$l['login_details'] = 'Here you can login to your '.$settings['site_name'].' account. If you do not have an account you can register one <a href="'.$cmsurl.'index.php?action=register">here</a>';
$l['login_user'] = 'Username:';
$l['login_pass'] = 'Password:';
$l['login_button'] = 'Login!';
$l['login_error'] = 'Wrong Username or Password';
$l['login_length'] = 'Session Length:';
$l['login_hour'] = 'An Hour';
$l['login_day'] = 'A Day';
$l['login_week'] = 'A Week';
$l['login_month'] = 'A Month';
$l['login_forever'] = 'Forever';
// Sub stuff for logging out in this template...
$l['logout_error_title'] = 'Logout Error';
$l['logout_error_header'] = 'Error';
$l['logout_error_desc'] = 'Session Verification Failed! Try Clicking on the logout link again.';

// Register.template.php stuff
$l['register_title'] = 'Register';
$l['register_header'] = 'Register';
$l['register_details'] = 'You can register for an account on '.$settings['site_name'].' here';
$l['register_username'] = 'Username:';
$l['register_password'] = 'Password:';
$l['register_verify_password'] = 'Verify Password:';
$l['register_email'] = 'Email:';
$l['register_captcha'] = 'Enter the Text you see in the image';
$l['register_success'] = 'Thank you %username%! Your account has been created successfully and you may now <a href="'.$cmsurl.'index.php?action=login">login</a>';
$l['register_successbut1'] = 'Thanks for registering! The administrators require you to activate your account via email, please check your email you used on your account, and click the link to activate your account.</p>

<p>If you didn\'t receive your activation email, check your spam/bulk folder. Some email clients wrongly think our emails are spam. If you still can\'t find it, use the following form to resend your activation email.';
$l['register_resend'] = 'Resend';
$l['register_successbut2'] = 'Thanks for registering! The administrators require themselves to activate accounts; you will recieve an email once your account is activated.';
$l['register_failed'] = 'Registration failed!';
$l['register_tos'] = 'I have read, I understand and I accept the %site% %link%terms of service%/link%.';
// For Account Activation...
$l['activate_title'] = 'Activate your Account';
$l['activate_acode_no_match'] = 'Wrong Activation Code';
$l['activate_account_already_activated'] = 'That account is already activated';
$l['activate_no_such_user'] = 'That username doesn\'t exist';
$l['activate_header'] = 'Account Activation';
$l['activate_desc'] = 'Here you can activate your account by entering your username and activation code';
$l['activate_button'] = 'Activate Account';
$l['activate_account_activated'] = 'Your account has been successfully activated! You may now <a href="'. $cmsurl. 'index.php?action=login">login</a>.';

// Error Stuff for Register.template.php
$l['register_error_user_taken'] = 'That username is already in use.';
$l['register_error_username_to_short'] = 'The username must be 3 characters or longer.';
$l['register_error_passwords'] = 'Those passwords don\'t match!';
$l['register_error_password_to_short'] = 'Your password is too short!';
$l['register_error_invalid_email'] = 'That email address is invalid!';
$l['register_error_captcha'] = 'You failed the CAPTCHA test! Please try again.';
$l['register_error_tos'] = 'You must accept our TOS.';
$l['register_error_activation_email'] = 'There was an error sending your activation email. Please wait for an administrator to manually register your account.';
$l['register_error_unknown'] = 'An unknown error has disabled us from registering your account. Please try again later.';
$l['register_button'] = 'Register';

// Page.template.php
$l['page_error_title'] = $settings['site_name'].' - Error';
$l['page_error_header'] = 'Error';
$l['page_error_details'] = 'An error has occurred! The page you have requested does not exist!';

// Online.template.php
$l['online_title'] = 'Who\'s Online';
$l['online_title_unknown'] = 'Unknown';
$l['online_user_guest'] = 'Guest';
$l['online_header'] = 'Who\'s Online';
$l['online_desc'] = 'Here you can view who is online, and what page they are viewing.';
$l['online_user'] = 'User';
$l['online_ip'] = 'IP';
$l['online_currently_viewing'] = 'Currently Viewing';
$l['online_time'] = 'Last Active';

// Admin.template.php
$l['admin_title'] = 'Control Panel';
$l['admin_snowcms_news'] = 'Latest news on SnowCMS';
$l['admin_options'] = 'Admin Options';
$l['admin_error_title'] = 'Error';
$l['admin_news_timeout'] = 'Unable to get the latest news from the <a href="http://www.snowcms.com">SnowCMS</a> site due to a server timeout. Refresh the page, or check out our site.';
$l['admin_current_version'] = 'Your SnowCMS Version:';
$l['admin_snowcms_current_version'] = 'Latest SnowCMS Version:';
$l['admin_version_unavailable'] = 'N/A';
$l['admin_error_header'] = 'Error';
$l['admin_error_reason'] = 'You don\'t have sufficient permission to access the Admin CP!';
$l['admin_menu_managepages'] = 'Manage Pages';
$l['admin_menu_managepages_desc'] = 'You can create, edit and delete pages in your database';
$l['admin_menu_basic-settings'] = 'Basic Settings';
$l['admin_menu_basic-settings_desc'] = 'Edit simple settings such as site name, slogan, time format, etc.';
$l['admin_menu_members'] = 'Manage Members';
$l['admin_menu_members_desc'] = 'Manage your members, such as changing their member group, anything in their profile';
$l['admin_menu_permissions'] = 'Group Permissions';
$l['admin_menu_permissions_desc'] = 'This is where you can choose what a member group can and cannot do on your site';
$l['admin_menu_menus'] = 'Manage Menus';
$l['admin_menu_menus_desc'] = 'You can edit the links on the sidebar and main menu here.';
$l['admin_menu_forum'] = 'Manage Forum';
$l['admin_menu_forum_desc'] = 'You can manage your forum setup here, such as categories and board management.';
$l['admin_menu_email'] = 'Mail Settings';
$l['admin_menu_email_desc'] = 'You can change mail settings here, like whether SMTP or Sendmail are used.';
$l['admin_menu_news'] = 'Manage News';
$l['admin_menu_news_desc'] = 'You can add, modify and delete news posts here.';
$l['admin_menu_tos'] = 'Terms of Service';
$l['admin_menu_tos_desc'] = 'You can turn on or off and modify your site\'s terms of service here.';

// Settings.template.php
$l['basicsettings_title'] = 'Basic Settings';
$l['basicsettings_header'] = 'Basic Settings';
$l['basicsettings_desc'] = 'Here are basic settings for your site, such as site name, slogan, time format, etc.';
$l['basicsettings_site_name'] = 'Site Name:';
$l['basicsettings_slogan'] = 'Slogan:';
$l['basicsettings_language'] = 'Default Language:';
$l['basicsettings_theme'] = 'Default Theme:';
$l['basicsettings_account_activation'] = 'Account activation:';
$l['basicsettings_value_no_activation'] = 'No activation';
$l['basicsettings_value_email_activation'] = 'Email activation';
$l['basicsettings_value_admin_activation'] = 'Admin activation';
$l['basicsettings_login_threshold'] = 'Login Threshold:';
$l['basicsettings_remember_time'] = 'Default Cookie Time:';
$l['basicsettings_timeformat'] = 'Time Format:';
$l['basicsettings_dateformat'] = 'Date Format:';
$l['basicsettings_update'] = 'Update Settings';
$l['basicsettings_num_news_items'] = 'News items per page:';
$l['basicsettings_num_search_results'] = 'Search results per page:';
$l['basicsettings_manage_members_per_page'] = 'Members per page:';

// Mail.template.php
$l['mailsettings_title'] = 'Email Settings';
$l['mailsettings_header'] = 'Email Settings';
$l['mailsettings_desc'] = 'You can change mail settings here, like whether SMTP or Sendmail are used.';
$l['mailsettings_smtp'] = 'SMTP';
$l['mailsettings_sendmail'] = 'Sendmail';
$l['mailsettings_smtp_host'] = 'SMTP Host:';
$l['mailsettings_smtp_port'] = 'SMTP Port:';
$l['mailsettings_smtp_user'] = 'SMTP User:';
$l['mailsettings_smtp_pass'] = 'SMTP Password:';
$l['mailsettings_smtp_pass_2'] = 'Verify Password:';
$l['mailsettings_from_email'] = 'From Email Address:';
$l['mailsettings_update'] = 'Update Settings';
$l['mailsettings_error'] = 'Failed to update mail settings.';
$l['mailsettings_error_verification'] = 'The verification password was wrong.';

// ManagePages.template.php
$l['managepages_title'] = 'Manage Pages';
$l['managepages_header'] = 'Manage Pages';
$l['managepages_createpage'] = 'Create Page';
$l['managepages_pagetitle'] = 'Page Title:';
$l['managepages_header'] = 'Manage Pages';
$l['managepages_desc'] = 'You can manage and create pages here.';
$l['managepages_no_title'] = 'You didn\'t enter a title for the page.';
$l['managepages_make_success'] = 'The page %title% was successfully created!';
$l['managepages_make_fail'] = 'Failed to create the page %title%.';
$l['managepages_pagetitle'] = 'Page Title';
$l['managepages_pageowner'] = 'Page Owner';
$l['managepages_datemade'] = 'Created On';
$l['managepages_no_pages'] = 'Their are currently no pages in your database';
$l['managepages_delete'] = 'Delete';
$l['managepages_change_homepage'] = 'Change Homepage';
$l['managepages_edit_title'] = 'Editing %title%';
$l['managepages_edit_header'] = 'Editing %title%';
$l['managepages_no_page_title'] = 'That page doesn\'t exist!';
$l['managepages_no_page_header'] = 'That page doesn\'t exist!';
$l['managepages_no_page_desc'] = 'The page you have requested to edit does not exist.';
$l['managepages_edit_desc'] = 'You are currently editing a page; you can edit the content and the title of the page, and you use can use HTML in the content of the page.';
$l['managepages_editpage_title'] = 'Page Title:';
$l['managepages_editpage_content'] = 'Page Content:';
$l['managepages_editpage_button'] = 'Update Page';
$l['managepages_editpage_insert_link'] = 'Insert Link';
$l['managepages_update_failed'] = 'Page update failed!';
$l['managepages_update_success'] = 'Page updated successfully!';
$l['managepages_editpage_show_info'] = "Show extra page info";
$l['managepages_error_change_homepage'] = "Changing the homepage failed.";

// Some Email stuff, for Email Activation, etc.
$l['mail_smtp_fail'] = 'Sending email failed! Error: %error%';
$l['mail_smtp_success'] = 'Email sent successfully!';
$l['mail_mail_fail'] = 'Sending email failed!';
$l['mail_mail_success'] = 'Email sent successfully!';
$l['email_register_subject'] = 'Activate your account at '.$settings['site_name'].'.';
$l['email_register_tpl'] = "Hello %username%!\r\n Someone has requested an account at {$settings['site_name']} If you didn't request an account at this site, ignore this email.\r\nIf you did request this, click on the link below to activate your account\r\n%alink%\r\n \r\nRegards,\r\nThe {$settings['site_name']} Team";

// List pages
$l['listpage_header'] = 'Page List';
$l['listepage_desc'] = 'This page shows all the pages that was writen in the CMS.';

// Forum.template.php
$l['forum_title'] = $settings['site_name'].' - Index';
$l['forum_error_title'] = $settings['site_name'].' - Error';
$l['forum_error_header'] = 'Error';
$l['forum_error_message'] = 'Sorry, but you aren\'t allowed to view the '.$settings['site_name'].' board, if you have not yet tried, maybe <a href="'.$cmsurl.'index.php?action=register">registering</a> and <a href="'.$cmsurl.'index.php?action=login">logging</a> will allow you to view the forum';
$l['forum_posts_in'] = 'posts in';
$l['forum_topics'] = 'topics';
$l['forum_board_new'] = 'New posts';
$l['forum_board_old'] = 'No new posts';
$l['forum_no_posts'] = 'No Posts';
$l['forum_topic_own_new'] = 'New replies';
$l['forum_topic_new'] = 'New replies';
$l['forum_topic_own_old'] = 'No new replies';
$l['forum_topic_old'] = 'No new replies';
$l['forum_error_cantviewb_message'] = 'Sorry, you are not allowed to view or access this board';
$l['forum_error_noboard_message'] = 'The Board ID you have requested does not exist!';
$l['topic_lastby'] = 'Last Post by';
$l['topic_in'] = 'in';

// Links for Forum.template.php
$l['forum_link_home'] = 'Home';
$l['forum_link_forumindex'] = 'Forum Index';
$l['forum_link_search'] = 'Search';
$l['forum_link_admin'] = 'Admin';
$l['forum_link_profile'] = 'Profile';
$l['forum_link_members'] = 'Members';
$l['forum_link_pm'] = 'Personal Messages';
$l['forum_link_register'] = 'Register';
$l['forum_link_login'] = 'Login';

// Topic.template.php
$l['topic_pages'] = 'Pages:';
$l['topic_on'] = 'on';
$l['topic_firstpage'] = 'First Page';
$l['topic_lastpage'] = 'Last Page';
$l['topic_newtopic'] = 'New Topic';
$l['topic_reply'] = 'Post Reply';
$l['forum_postreply'] = 'Post Reply';
$l['post_newtopic'] = 'Start new topic';
$l['topic_topic_button'] = 'Post Topic';
$l['topic_post_button'] = 'Post Reply';
$l['topic_sticky'] = 'Make topic Sticky';
$l['topic_lock'] = 'Lock topic';
$l['topic_subject'] = 'Subject:';
$l['post_postreply'] = 'Post Reply';
$l['topic_editpost'] = 'Edit Post';
$l['topic_quote'] = 'Reply with Quote';
$l['topic_deletemsg'] = 'Delete Message';
$l['topic_split'] = 'Split this Message';
$l['topic_delconfirm'] = 'Are you sure you want to delete this message? \\n This cannot be undone!';
$l['topic_posts'] = 'Posts:';
$l['topic_status'] = 'Status:';

// Profile.template.php
$l['profile_title'] = 'Profile';
$l['profile_header'] = 'Profile of %user%';
$l['profile_own_header'] = 'Your Profile';
$l['profile_notallowed_title'] = 'Profile';
$l['profile_notallowed_header'] = 'Profile';
$l['profile_notallowed_desc'] = 'You do not have permission to view this member\'s profile.';
$l['profile_notallowed_desc_loggedout'] = 'Sorry but you may only view this profile if you are <a href="'.$cmsurl.'index.php?action=login">logged in</a>. If you have not yet done so, why not <a href="'.$cmsurl.'index.php?action=register">register</a> or <a href="'.$cmsurl.'index.php?action=login">login</a>.';
$l['profile_noprofile_title'] = 'Invalid Profile';
$l['profile_noprofile_header'] = 'Invalid Profile';
$l['profile_noprofile_desc'] = 'This is an invalid profile.';
$l['profile_profile_of'] = 'Profile of %user%';
$l['profile_online'] = 'Online';
$l['profile_offline'] = 'Offline';
$l['profile_moderate'] = 'Moderate';
$l['profile_edit_link'] = 'Change Settings';
$l['profile_edit_title'] = 'Change Settings';
$l['profile_edit_header'] = 'Change Settings';
$l['profile_edit_display_name'] = 'Display Name';
$l['profile_edit_email'] = 'Email Address';
$l['profile_edit_signature'] = 'Signature';
$l['profile_edit_profile_text'] = 'Profile Text';
$l['profile_edit_password_old'] = 'Old Password';
$l['profile_edit_password_new'] = 'New Password';
$l['profile_edit_password_verify'] = 'Verify Password';
$l['profile_edit_change'] = 'Change Details';
$l['profile_edit_cancel'] = 'Cancel';

// Permissions.template.php
$l['permissions_title'] = 'Manage Permissions';
$l['permissions_header'] = 'Group Permissions';
$l['permissions_desc'] = 'Here is where you can manage member group specific permissions, in other words, you can choose what people in a member group can and cannot do.</p>';
$l['permissions_change_groups'] = 'Save Changes';
$l['permissions_new_group'] = 'New Group';
$l['permissions_modify'] = 'Modify';
$l['permissions_delete'] = 'Delete';
$l['permissions_all'] = 'All';
$l['permissions_membergroup'] = 'Member Group';
$l['permissions_numusers'] = 'Users';
$l['permissions_permissions'] = 'Permissions';
$l['permissions_editperms_title'] = 'Edit Permissions';
$l['permissions_nogroup_desc'] = 'The member group you have requested does not exist!';
$l['permissions_edit_header'] = 'Edit Permissions';
$l['permissions_edit_desc'] = 'Here you can edit the permissions for this specific group, which allows you to choose what they can and cannot do.';
$l['permissions_edit_save'] = 'Save';
$l['permissions_perm_all'] = 'Allow them to do everything';
$l['permissions_perm_admin'] = 'Allow them to administrate the site';
$l['permissions_perm_manage_basic-settings'] = 'Allow them to edit basic settings';
$l['permissions_perm_manage_members'] = 'Allow them to manage members';
$l['permissions_perm_manage_menus'] = 'Allow them to manage menus';
$l['permissions_perm_manage_news'] = 'Allow them to manage news';
$l['permissions_perm_manage_pages'] = 'Allow them to manage pages';
$l['permissions_perm_manage_permissions'] = 'Allow them to manage permissions';
$l['permissions_perm_manage_forum_perms'] = 'Allow them to manage forum permissions';
$l['permissions_perm_moderate_username'] = 'Allow them to change members\' usernames.';
$l['permissions_perm_moderate_display_name'] = 'Allow them to change members\' display names.';
$l['permissions_perm_moderate_email'] = 'Allow them to change members\' email addresses.';
$l['permissions_perm_moderate_password'] = 'Allow them to change members\' passwords.';
$l['permissions_perm_moderate_group'] = 'Allow them to change members\' groups.';
$l['permissions_perm_moderate_signature'] = 'Allow them to change members\' signatures.';
$l['permissions_perm_moderate_profile'] = 'Allow them to change members\' profiles.';
$l['permissions_perm_moderate_activate'] = 'Allow them to activate members\' accounts.';
$l['permissions_perm_moderate_suspend'] = 'Allow them to suspend members.';
$l['permissions_perm_moderate_unsuspend'] = 'Allow them to unsuspend members.';
$l['permissions_perm_moderate_ban'] = 'Allow them to ban members.';
$l['permissions_perm_moderate_unban'] = 'Allow them to unban members.';
$l['permissions_perm_view_forum'] = 'Allow them to view the forum';
$l['permissions_perm_view_online'] = 'Allow them to view who is online';
$l['permissions_perm_view_profile'] = 'Allow them to view others profiles';
$l['permissions_perm_search'] = 'Allow them to search the forum';
$l['permissions_perm_manage_mail_settings'] = 'Allow them to edit mail settings';
$l['permissions_perm_manage_groups'] = 'Allow them to manage member groups';
$l['permissions_perm_manage_forum'] = 'Allow them to manage the Forum';
$l['permissions_error_change'] = 'Changing member groups failed.';
$l['permissions_error_new'] = 'Adding member group failed.';
$l['permissions_error_default_guest'] = 'Cannot make guest group default.';
$l['permissions_error_delete'] = 'Deleting member group failed.';
$l['permissions_error_delete_admin'] = 'Cannot delete administrative group.';
$l['permissions_error_delete_guest'] = 'Cannot delete guest group.';
$l['permissions_error_delete_default'] = 'Cannot delete default group.';
// Sub part for Board Permissions
$l['mf_perms_title'] = 'Manage Board Permissions';
$l['mf_perms_header'] = 'Board Permissions';
$l['mf_perms_desc'] = 'Choose a board below, and from there, you can choose a member group to edit permissions for that are allowed access to that board.';
$l['mf_perms_manage'] = 'Manage';
$l['mf_perms_nocats'] = 'Error! It appears you don\'t have any <a href="'. $cmsurl. 'index.php?action=admin;sa=forum;fa=categories">categories</a> made, please make some if you want to access this</p>';
$l['mf_bp_board_title'] = 'Choose Member Group';
$l['mf_bp_board_header'] = 'Choose Member Group';
$l['mf_bp_board_desc'] = 'Choose a member group below that you would like to edit board permissions for...';
$l['mf_bp_board_nogroups'] = 'It looks like no Member Groups have access to this board.';
$l['mf_gp_board_title'] = 'Edit Board Permissions';
$l['mf_gp_board_header'] = 'Edit Permissions';
$l['mf_gp_board_desc'] = 'Here you can edit the individual permissions for the %group% group in the %boardname% board';
$l['mf_gp_board_button'] = 'Update Permissions';
$l['forumperms_delete_any'] = 'Allow them to delete any topic/post';
$l['forumperms_delete_own'] = 'Allow them to delete their own topics/posts';
$l['forumperms_lock_topic'] = 'Allow them to lock topics';
$l['forumperms_move_any'] = 'Allow them to move any topic';
$l['forumperms_edit_any'] = 'Allow them to edit any post';
$l['forumperms_edit_own'] = 'Allow them to edit their own posts';
$l['forumperms_post_new'] = 'Allow them to post new topics';
$l['forumperms_post_reply'] = 'Allow them to reply to topics';
$l['forumperms_sticky_topic'] = 'Allow them to sticky topics';
$l['forumperms_split_topic'] = 'Allow them to split topics/posts';
 
// ManageMembers.template.php
$l['managemembers_title'] = 'Manage Members';
$l['managemembers_header'] = 'Member List';
$l['managemembers_showing'] = 'Showing members %from% to %to%.';
$l['managemembers_showing_one'] = 'Showing member %number%.';
$l['managemembers_showing_none'] = 'No members match this filter.';
$l['managemembers_next_page'] = 'Next Page';
$l['managemembers_previous_page'] = 'Previous Page';
$l['managemembers_id'] = 'ID';
$l['managemembers_username'] = 'Username';
$l['managemembers_group'] = 'Group';
$l['managemembers_join_date'] = 'Join Date';
$l['managemembers_filter_button'] = 'Filter';
$l['managemembers_filter_everyone'] = 'Everyone';
$l['managemembers_filter_active'] = 'Active';
$l['managemembers_filter_activated'] = 'Activated';
$l['managemembers_filter_unactivated'] = 'Unactivated';
$l['managemembers_filter_suspended'] = 'Suspended';
$l['managemembers_filter_banned'] = 'Banned';
$l['managemembers_moderate_button'] = 'Moderate';
$l['managemembers_moderate_title'] = 'Moderate %name%';
$l['managemembers_moderate_header'] = 'Moderate %name%';
$l['managemembers_moderate_id'] = 'ID';
$l['managemembers_moderate_username'] = 'Username';
$l['managemembers_moderate_display_name'] = 'Display Name';
$l['managemembers_moderate_email'] = 'Email Address';
$l['managemembers_moderate_group'] = 'Member Group';
$l['managemembers_moderate_posts'] = 'Total Posts';
$l['managemembers_moderate_registration_date'] = 'Registration Date';
$l['managemembers_moderate_last_login'] = 'Last Login Date';
$l['managemembers_moderate_never'] = 'Never';
$l['managemembers_moderate_suspended_until'] = 'Suspended Until';
$l['managemembers_moderate_registration_ip'] = 'Registration IP';
$l['managemembers_moderate_last_ip'] = 'Last Used IP';
$l['managemembers_moderate_signature'] = 'Signature';
$l['managemembers_moderate_profile_text'] = 'Profile Text';
$l['managemembers_moderate_change'] = 'Change Details';
$l['managemembers_moderate_profile'] = 'View Profile';
$l['managemembers_moderate_activate'] = 'Activate Account';
$l['managemembers_moderate_suspend'] = '%button% for %input% hour(s)';
$l['managemembers_moderate_suspend_button'] = 'Suspend';
$l['managemembers_moderate_unsuspend_button'] = 'Remove Suspension';
$l['managemembers_moderate_renew_suspension'] = '%renew% for %input% hour(s)';
$l['managemembers_moderate_renew_remove_suspension'] = '%renew% for %input% hour(s) or %remove%';
$l['managemembers_moderate_renew_suspension_button'] = 'Renew Suspension';
$l['managemembers_moderate_ban'] = 'Ban Permanently';
$l['managemembers_moderate_unban'] = 'Remove Ban';
$l['managemembers_error_username_none'] = 'You didn\'t enter a username.';
$l['managemembers_error_username_already_used'] = 'That username is already in use.';
$l['managemembers_error_display_name_already_used'] = 'That display name is already in use.';
$l['managemembers_error_email_none'] = 'You didn\'t enter an email address.';
$l['managemembers_error_email_invalid'] = 'That email address is invalid.';
$l['managemembers_error_password_too_short'] = 'That password is too short.';
$l['managemembers_error_password_failed_verification'] = 'The verification password didn\'t match.';
$l['managemembers_error_group_invalid'] = 'That group is invalid.';
$l['managemembers_error_activate'] = 'Failed to activate member\'s account.';
$l['managemembers_error_suspension'] = 'Failed to suspend member.';
$l['managemembers_error_unsuspend'] = 'Failed to unsuspend member.';
$l['managemembers_error_ban'] = 'Failed to ban member.';
$l['managemembers_error_unban'] = 'Failed to unban member.';

// MemberList.template.php
$l['memberlist_title'] = 'Member List';
$l['memberlist_header'] = 'Member List';
$l['memberlist_showing'] = 'Showing members %from% to %to%.';
$l['memberlist_showing_one'] = 'Showing member %number%.';
$l['memberlist_showing_none'] = 'No members match this filter.';
$l['memberlist_next_page'] = 'Next Page';
$l['memberlist_previous_page'] = 'Previous Page';
$l['memberlist_id'] = 'ID';
$l['memberlist_username'] = 'Username';
$l['memberlist_group'] = 'Group';
$l['memberlist_join_date'] = 'Join Date';
$l['memberlist_filter_button'] = 'Filter';
$l['memberlist_filter_everyone'] = 'Everyone';
$l['memberlist_filter_active'] = 'Active';
$l['memberlist_filter_activated'] = 'Activated';
$l['memberlist_filter_unactivated'] = 'Unactivated';
$l['memberlist_filter_suspended'] = 'Suspended';
$l['memberlist_filter_banned'] = 'Banned';

// ManageMenus.template.php
$l['managemenus_title'] = 'Manage Menus';
$l['managemenus_header'] = 'Manage Menus';
$l['managemenus_name'] = 'Name';
$l['managemenus_url'] = 'URL';
$l['managemenus_new_window'] = 'New Window';
$l['managemenus_menu'] = 'Menu';
$l['managemenus_order'] = 'Order';
$l['managemenus_delete'] = 'Delete';
$l['managemenus_save_changes'] = 'Save Changes';

// ManageForum.template.php
$l['manageforum_title'] = 'Manage Forum';
$l['manageforum_header'] = 'Manage Forum';
$l['manageforum_desc'] = 'Click on one of the links below to manage your <a href="'. $cmsurl. 'forum.php">forum</a>';
// Links
$l['mf_link_cats'] = 'Manage Categories';
$l['mf_link_cats_desc'] = 'Create, Edit, and Delete forum categories';
$l['mf_link_boards'] = 'Manage Boards';
$l['mf_link_boards_desc'] = 'You can create, edit and delete your boards in your forum here';
$l['managecats_title'] = 'Manage Categories';
$l['mf_link_perms'] = 'Forum Permissions';
$l['mf_link_perms_desc'] = 'Here is where you can edit permissions for each board and each member group';
// Manage Categories
$l['mf_new_category'] = 'New Category';
$l['managecats_add_header'] = 'Add Category';
$l['managecats_addbutton'] = 'Add Category';
$l['managecats_header'] = 'Manage Categories';
$l['managecats_desc'] = 'Here you can edit, delete and create categories';
$l['mc_tr_cn'] = 'Category Name';
$l['mc_tr_order'] = 'Order';
$l['managecats_update'] = 'Update';
$l['managecats_are_you_sure'] = 'Are you sure you want to delete this Category?';
$l['managecats_catname'] = 'Category Name:';
$l['managecats_order'] = 'Order:';
// Manage Boards
$l['manageboards_title'] = 'Manage Boards';
$l['manageboards_header'] = 'Manage Boards';
$l['manageboards_desc'] = 'You can edit, add, and delete boards in your forum, be sure that you have <a href="'. $cmsurl. 'index.php?action=admin;sa=forum;fa=categories">categories</a> created already';
$l['manageboards_no_cats'] = 'No categories have been found! You <em>must</em> have categories to add boards, you can make them <a href="'. $cmsurl. 'index.php?action=admin;sa=forum;fa=categories">here</a>';
$l['manageboards_are_you_sure_del'] = 'Are you SURE you want to delete this board? \nIt can\\\'t be undone!';
$l['manageboards_edit'] = 'Edit';
$l['manageboards_delete'] = 'Delete';
$l['manageboards_modify'] = 'Modify';
// Add Boards
$l['manageboards_add_title'] = 'Add Board';
$l['manageboards_add_header'] = 'Add Board';
$l['manageboards_add_category'] = 'Category';
$l['manageboards_add_boardname'] = 'Board Name';
$l['manageboards_add_boarddesc'] = 'Board Description';
$l['manageboards_add_whoview'] = 'Allowed Groups';
$l['manageboards_add_button'] = 'Add Board';
$l['manageboards_add_guests'] = 'Guests';
$l['manageboards_add_update'] = 'Update Boards';
// Edit Boards
$l['manageboards_edit_title'] = 'Edit Board';
$l['manageboards_edit_header'] = 'Edit Board';
$l['manageboards_edit_button'] = 'Update Board';

// Post.template.php
$l['forum_startnew'] = 'Post New Topic';
$l['forum_post_bold'] = 'Bold';
$l['forum_post_italic'] = 'Italic';
$l['forum_post_underline'] = 'Underline';
$l['forum_post_strikethrough'] = 'Strikethrough';
$l['forum_post_image'] = 'Image';
$l['forum_post_link'] = 'Hyperlink';
$l['forum_post_code'] = 'Code';
$l['forum_post_quote'] = 'Quote';

// Search.template.php
$l['forum_search_title'] = 'Search';
$l['forum_search_results_title'] = 'Search';
$l['forum_search_noresults_title'] = 'Search';
$l['forum_search_notallowed_title'] = 'Search';
$l['forum_search_submit'] = 'Search';
$l['forum_search_noresults'] = 'Your search returned no results.';
$l['forum_search_notallowed'] = 'You\'re not allowed to search the forum.';

// Error.template.php
$l['themeerror_title'] = 'Theme Error!';
$l['themeerror_header'] = 'Theme Load Error';
$l['themeerror_msg'] = 'An error occurred while trying to load the template function %func%(); in the template %file%';

// News.template.php
$l['news_title'] = 'News';
$l['news_header'] = 'News';
$l['news_heading'] = '%subject% in %category% by %name% at %date%';
$l['news_comment_heading'] = '%subject% by %name% at %date%';
$l['news_comments'] = 'Comments: %num%';
$l['news_comment_subject'] = 'Subject:';
$l['news_comment_submit'] = 'Add Comment';
$l['news_category_change'] = 'Change';
$l['news_previous_page'] = 'Previous Page';
$l['news_next_page'] = 'Next Page';
$l['news_manage_title'] = 'Manage News';
$l['news_manage_header'] = 'Manage News';
$l['news_manage_desc'] = 'Here you can add, modify and delete news posts.';
$l['news_manage_add'] = 'Add News';
$l['news_manage_add_desc'] = 'You can add a news post here.';
$l['news_manage_categories'] = 'Manage Categories';
$l['news_manage_categories_desc'] = 'You can add, delete and modify news categories here.';
$l['news_add_title'] = 'Manage News';
$l['news_add_header'] = 'Add News';
$l['news_add_category'] = 'Category:';
$l['news_add_subject'] = 'Subject:';
$l['news_add_allow_comments'] = 'Allow Comments';
$l['news_add_submit'] = 'Add Post';
$l['news_cats_title'] = 'Manage News';
$l['news_cats_header'] = 'News Categories';
$l['news_cats_desc'] = 'Here you can add, delete and modify news categories.';
$l['news_cats_name'] = 'Category Name';
$l['news_cats_areyousure'] = 'Are you sure you want to delete this news category?';
$l['news_cats_delete'] = 'Delete';
$l['news_cats_update'] = 'Rename Categories';
$l['news_cats_add'] = 'New Category';
$l['news_cats_add_name'] = 'Category Name';
$l['news_cats_add_submit'] = 'Add Category';
$l['news_nonews_title'] = 'News';
$l['news_nonews_header'] = 'News';
$l['news_nonews_desc'] = 'There is no news in this category.';
$l['news_doesntexist_title'] = 'News';
$l['news_doesntexist_header'] = 'News';
$l['news_doesntexist_desc'] = 'There is no news article with this ID.';

// TOS.template.php
$l['tos_title'] = 'Terms of Service';
$l['tos_header'] = 'Terms of Service';
$l['tos_manage_title'] = 'Manage TOS';
$l['tos_manage_header'] = 'Manage Terms of Service';
$l['tos_manage_desc'] = 'Here you can modify your site\'s terms of service. You can also turn it on or off here.';
$l['tos_change_title'] = 'Manage TOS';
$l['tos_change_header'] = 'Change %lang% TOS';
$l['tos_change_desc'] = 'Here you can modify your site\'s terms of service. You can also turn it on or off here. HTML is allowed.';
$l['tos_onelang_title'] = 'Manage TOS';
$l['tos_onelang_header'] = 'Manage TOS';
$l['tos_onelang_desc'] = 'Here you can modify your site\'s terms of service. You can also turn it on or off here. HTML is allowed.';
$l['tos_enable'] = 'Enable TOS';
$l['tos_disable'] = 'Disable TOS';
$l['tos_change'] = 'Change TOS';
$l['tos_change_submit'] = 'Save Changes';
$l['tos_onelanguage_submit'] = 'Save Changes';
$l['tos_notos'] = 'There is no TOS written in this language.';
?>