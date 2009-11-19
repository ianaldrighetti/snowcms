<?php
#
# Login English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $base_url, $settings;

$l['login_title'] = 'Login - '. $settings['site_name'];

# Login Page specifics
$l['login_header'] = 'Login';
$l['login_desc'] = 'Here you can login to your account. If you do not have an account you can register one <a href="'. $base_url. '/index.php?action=register">here</a>.';

# Form language variables
$l['login_username'] = 'Username:';
$l['login_password'] = 'Password:';
$l['login_session_length'] = 'Session Length:';
$l['login_1hour'] = '1 Hour';
$l['login_1day'] = '1 Day';
$l['login_1week'] = '1 Week';
$l['login_1month'] = '1 Month';
$l['login_forever'] = 'Forever';
$l['login_button'] = 'Login';
$l['login_forgot_password'] = 'Forgot your password?';

$l['error_login_empty_info'] = 'Please be sure your username and password are filled in.';
$l['error_login_wrong_username_or_password'] = 'Wrong Username or Password.';
$l['error_login_account_not_activated'] = 'It appears your account isn\'t activated yet. '. ($settings['account_activation'] == 1 ? '<a href="'. $base_url. '/index.php?action=resend">Didn\'t get your activation email?</a>' : 'An Administrator must approve your account first.');
$l['error_login_account_banned'] = 'Sorry, but your account has been banned.';
$l['error_login_account_suspended'] = 'Sorry, but your account is suspended, your suspension will expire on %s';

# Flooding is bad... BAD!
$l['login_flood_title'] = 'Flood Attempt Detected - '. $settings['site_name'];

# Password Reminder stuffs.
$l['login_reminder_title'] = 'Request a Password Reminder - '. $settings['site_name'];
$l['login_reminder_header'] = 'Request a Password Reminder';
$l['login_reminder_desc'] = 'Forgot your password for your account? This is the place to begin the recovery of your password. Since password\'s are hashed we cannot give you your original password however we can have your password reset for your account.';
$l['login_reminder_error'] = 'Sorry, but we couldn\'t find a user with that name or email address in our database.';
$l['login_reminder_success'] = 'You shall recieve an email shortly with details on how to reset your account\'s password.';
$l['login_reminder_usernameemail'] = 'Username/Email:';
$l['login_reminder_button'] = 'Request Reminder';

$l['login_reminder2_title'] = 'Process the Password Reminder - '. $settings['site_name'];
$l['login_reminder2_header'] = 'Process Reminder';
$l['login_reminder2_desc'] = 'After you have recieved the email containing your verification code you can set your new password here, if you haven\'t yet requested or didn\'t get the email, you can <a href="'. $base_url. '/index.php?action=reminder">try again</a> if you please.';

$l['error_reminder2_unknown'] = 'Sorry but that user either does not exist, the code is incorrect or a password reminder was not requested for the account';
$l['error_reminder2_passwords'] = 'The password is to short or they don\'t match';

$l['reminder_email_subject'] = 'Password Reminder Requested - '. $settings['site_name'];
$l['reminder_email_tpl'] = "Hello {\$MEMBER_NAME}!

It appears someone has requested a password reminder for your account at {$settings['site_name']}, if you actually requested this click the link below to proceed:
$base_url/index.php?action=reminder2;id={\$MEMBER_ID};code={\$CODE}

If you didn't request this password reminder, we apologize for this inconvience, and just ignore this email.

Regards,
The {$settings['site_name']} Team.
$base_url";

# Flooding? D:
$l['login_flood_warning'] = 'Login flood attempt';
$l['login_flood_desc'] = 'It appears you are possibly flooding the login system. For security reasons, you have been disabled from logging in temporarily. Please try again soon.';
?>