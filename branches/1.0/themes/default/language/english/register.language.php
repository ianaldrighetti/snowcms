<?php
#
# Registration English file for SnowCMS
#    Created by the SnowCMS Dev Team
#          www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $base_url, $settings;

# Language vars for Registration Enabled :P
$l['register_title'] = 'Register - '. $settings['site_name'];

# Registration form/page
$l['register_header'] = 'Register';

# Field labels
$l['label_username'] = 'Username';
$l['label_password'] = 'Password';
$l['label_password_verify'] = 'Verify Password';
$l['label_email'] = 'Email';
$l['label_captcha'] = 'CAPTCHA';

# Registering errors
$l['error_username_empty'] = 'Please fill in a username';
$l['error_username_length'] = 'Your username must be between 3 and 80 characters';
$l['error_username_taken'] = 'Sorry but that username is in use or is not allowed';
$l['error_password_empty'] = 'Please fill in a password';
$l['error_password_length'] = 'Your password must be at least 4 characters';
$l['error_passwords_verify'] = 'Your passwords don\'t match!';
$l['error_email_empty'] = 'Please fill in an email address.';
$l['error_email_disallowed'] = 'Sorry, that email address is either already in use or it is disallowed.';
$l['error_email_invalid'] = 'That email address is invalid.';
$l['error_captcha_empty'] = 'Enter the letters you see in the image';
$l['error_captcha_invalid'] = 'You entered the wrong characters in the CAPTCHA';
$l['error_didnt_accept_agreement'] = 'Please be sure to read and accept the agreement';
$l['error_agreement_missing'] = 'ERROR: Could not find agreement.txt';

# Welcome screen :P
$l['register_welcome_header'] = 'Welcome';
$l['register_welcome_desc'] = 'Thank you for registering %s! Your account has been created and you don\'t need to do anything more, you may now <a href="'. $base_url. '/index.php?action=login">login to your account</a>.';

# Email sent screen ;)
$l['register_emailsent_header'] = 'Activation Email Sent';
$l['register_emailsent_desc'] = 'Thank you for registering %s! An email has been sent to %s, so in order to activate your account you must click the link in the email recieved from '. $settings['site_name']. ', once you do you will be able to login to your account.';

# Awaiting approval
$l['register_approval_header'] = 'Awaiting Approval';
$l['register_approval_desc'] = 'Thank you for registering %s! It is required for an administrator to approve your account before you can login, once an Administrator approves your account you will recieve an email at %s.';

# Registration disabled
$l['register_disabled_header'] = 'Registration Disabled';
$l['register_disabled_desc'] = 'Sorry, but registration has been disabled, maybe come back later?';

# Language vars for Registration Disabled
$l['register_disabled_title'] = 'Registration Disabled - '. $settings['site_name'];

# Flooding is bad :P
$l['register_flood_title'] = 'Flood Attempt Detected - '. $settings['site_name'];

# Activation page
$l['register_activation_title'] = 'Account Activation - '. $settings['site_name'];

# Resend email activation
$l['register_resend_error_title'] = 'An Error has Occurred - '. $settings['site_name'];

$l['register_resend_title'] = 'Resend Email Activation - '. $settings['site_name'];

# Registration email template ;)
$l['register_email_subject'] = 'Welcome to '. $settings['site_name'];
$l['register_email_activation_tpl'] = "You have successfully registered an account with {$settings['site_name']}, {\$LOGIN_NAME}!

However in order to login to your account you must activate it, which you can do so by clicking the below link:
{$base_url}/index.php?action=activate;id={\$MEMBER_ID};code={\$ACODE}

If you didn't request this account, we apologize and please just ignore this email.

Regards,
The {$settings['site_name']} Team.
{$base_url}";
?>