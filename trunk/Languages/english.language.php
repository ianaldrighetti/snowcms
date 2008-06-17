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

// Error Stuff for Register.template.php
$l['register_error_user_taken'] = 'That Username is already in use';
$l['register_error_username_to_short'] = 'The Username must be 3 characters or longer';
$l['register_error_passwords'] = 'Those Passwords don\'t match';
$l['register_error_password_to_short'] = 'Password is to short';
$l['register_error_invalid_email'] = 'That Email is invalid';
$l['register_error_captcha'] = 'The CAPTCHA Test Failed';
$l['register_error_unknown'] = 'An Unknown Error has disabled us from Registering your Account. Info: '.mysql_error();
$l['register_button'] = 'Register';

// Page.template.php
$l['page_error_title'] = $settings['site_name'].' - Error';
$l['page_error_header'] = 'Error';
$l['page_error_details'] = 'An error has occurred! The Page you have requested does not exist!';

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
$l['admin_cant_get_news_1'] = 'We were unable to get the latest SnowCMS News from the <a href="http://www.snowcms.com">SnowCMS</a> site due to your server configuration does not have <a href="http://php.net/curl">cURL</a> setup. Please check out our site for the latest news &amp; updates.';
$l['admin_cant_get_news_2'] = 'Unable to get the latest news from <a href="http://www.snowcms.com">SnowCMS</a> due to a server timeout. Refresh the page, or check out our site';
$l['admin_current_version'] = 'Your SnowCMS Version:';
$l['admin_snowcms_current_version'] = 'Latest SnowCMS Version:';

// Settings.template.php
$l['basicsettings_title'] = 'Basic Settings';
?>