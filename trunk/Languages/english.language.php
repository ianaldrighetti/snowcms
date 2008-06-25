<?php
// SnowCMS Main.language.php File for English
// Version 0.2

// Login.template.php stuff
$l['login_title'] = 'Login';
$l['login_header'] = 'Login';
$l['login_details'] = 'Here you can login to your '.$settings['site_name'].' account. If you do not have an account you can register one <a href="'.$cmsurl.'index.php?action=register">here</a>';
$l['login_user'] = 'Username:';
$l['login_pass'] = 'Password:';
$l['login_remember_me'] = 'Remember Me';
$l['login_button'] = 'Login!';
$l['login_error'] = 'Wrong Username or Password';

// Register.template.php stuff
$l['register_title'] = 'Register';
$l['register_header'] = 'Register';
$l['register_details'] = 'You can register for an account on '.$settings['site_name'].' here';
$l['register_username'] = 'Username:';
$l['register_password'] = 'Password:';
$l['register_verify_password'] = 'Verify Password:';
$l['register_email'] = 'Email:';
$l['register_captcha'] = 'Enter the Text you see in the image';
$l['register_success'] = 'Thank you '.strtolower(@$_REQUEST['username']).'! Your account has been created successfully and you may now <a href="'.$cmsurl.'index.php?action=login">login</a>';
$l['register_successbut1'] = 'Thanks for registering! The administrators require you to activate your account via email, please check your email you used on your account, and click the link to activate your account';
$l['register_successbut2'] = 'Thanks for registering! The administrators require themselves to activate accounts; you will recieve an email once your account is activated.';
$l['register_failed'] = 'Registration failed!';

// Error Stuff for Register.template.php
$l['register_error_user_taken'] = 'That username is already in use.';
$l['register_error_username_to_short'] = 'The username must be 3 characters or longer.';
$l['register_error_passwords'] = 'Those passwords don\'t match!';
$l['register_error_password_to_short'] = 'Your password is too short!';
$l['register_error_invalid_email'] = 'That email address is invalid!';
$l['register_error_captcha'] = 'You failed the CAPTCHA test! Please try again.';
$l['register_error_unknown'] = 'An unknown error has disabled us from registering your account. Info: '.mysql_error();
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
$l['admin_title'] = 'Admin CP';
$l['admin_error_title'] = 'Error';
$l['admin_cant_get_news_1'] = 'We were unable to get the latest SnowCMS News from the <a href="http://www.snowcms.com">SnowCMS</a> site because your server configuration does not have <a href="http://php.net/curl">cURL</a> setup. Please check out our site for the latest news &amp; updates.';
$l['admin_cant_get_news_2'] = 'Unable to get the latest news from <a href="http://www.snowcms.com">SnowCMS</a> due to a server timeout. Refresh the page, or check out our site!';
$l['admin_current_version'] = 'Your SnowCMS Version:';
$l['admin_snowcms_current_version'] = 'Latest SnowCMS Version:';
$l['admin_error_header'] = 'Error';
$l['admin_error_reason'] = 'You don\'t have sufficient permission to access the Admin CP!';
$l['admin_menu_managepages'] = 'Manage Pages';
$l['admin_menu_managepages_desc'] = 'You can create, edit and delete pages in your database';
$l['admin_menu_basic-settings'] = 'Basic Settings';
$l['admin_menu_basic-settings_desc'] = 'Edit simple settings such as site name, slogan, time format, etc.';
$l['admin_menu_members'] = 'Manage Members';
$l['admin_menu_members_desc'] = 'Manage your members, such as changing their member group, anything in their profile';

// Settings.template.php
$l['basicsettings_title'] = 'Basic Settings';
$l['basicsettings_header'] = 'Basic Settings';
$l['basicsettings_desc'] = 'Here are basic settings for your site, such as site name, slogan, time format, etc.';
$l['basicsettings_site_name'] = 'Site Name:';
$l['basicsettings_slogan'] = 'Slogan:';
$l['basicsettings_login_threshold'] = 'Login Threshold:';
$l['basicsettings_remember_time'] = 'Default Cookie Time:';
$l['basicsettings_timeformat'] = 'Time Format:';
$l['basicsettings_update'] = 'Update Settings';

// ManagePages.template.php
$l['adminpage_make_title'] = 'Manage Pages';
$l['managepages_makepage'] = 'Create Page';
$l['managepages_pagetitle'] = 'Page Title:';
$l['managepages_header'] = 'Manage Pages';
$l['managepages_desc'] = 'You can manage and create pages here.';
$l['adminpage_make_success'] = 'The page %title% was successfully created!';
$l['adminpage_make_fail'] = 'Failed to create the page %title%';
$l['adminpages_title_td'] = 'Page Title';
$l['adminpages_pageowner'] = 'Page Owner';
$l['adminpages_datemade'] = 'Created on';
$l['adminpages_no_pages'] = 'Their are currently no pages in your database';
$l['managepages_edit_title'] = 'Editing page %title%';
$l['managepages_no_page_title'] = 'That page doesn\'t exist!';
$l['managepages_no_page_header'] = 'That page doesn\'t exist!';
$l['managepages_no_page_desc'] = 'The page you have requested to edit does not exist.';
$l['managepages_edit_header'] = 'Edit Page';
$l['managepages_edit_desc'] = 'You are currently editing a page; you can edit the content and the title of the page, and you use can use HTML in the content of the page.';
$l['managepages_editpage_title'] = 'Page Title:';
$l['managepages_editpage_content'] = 'Page Content:';
$l['managepages_editpage_button'] = 'Update Page';
$l['managepages_update_failed'] = 'Page update failed!';
$l['managepages_update_success'] = 'Page updated successfully!';
$l['managepages_editpage_show_info'] = "Show extra page info";

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
?>
