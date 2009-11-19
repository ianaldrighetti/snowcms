<?php
#
#   Settings English file for SnowCMS
#    Created by the SnowCMS Dev Team
#          www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Member list stuff
$l['admin_members_list_title'] = 'Member List - '. $settings['site_name'];
$l['admin_members_list_header'] = 'Member List';
$l['admin_members_list_desc'] = 'Check out all of the site\'s members and modify as you wish.';

# Member list table header stuff
$l['admin_members_list_id'] = 'ID';
$l['admin_members_list_username'] = 'Username';
$l['admin_members_list_email'] = 'Email Address';
$l['admin_members_list_ip'] = 'IP Address';
$l['admin_members_list_posts'] = 'Posts';

# Register member stuff
$l['admin_members_register_title'] = 'Register New Member - '. $settings['site_name'];
$l['admin_members_register_header'] = 'Register New Member';
$l['admin_members_register_desc'] = 'Register a new member.';
$l['admin_members_register_success'] = 'Member registered successfully.';

# Register member labels
$l['admin_members_register_username'] = 'Username';
$l['admin_members_register_password'] = 'Password';
$l['admin_members_register_vpassword'] = 'Verify Password';
$l['admin_members_register_membergroup'] = 'Member Group';
$l['admin_members_register_email'] = 'Email';
$l['admin_members_register_register'] = 'Register';

# Register member errors
$l['admin_members_register_error_username_empty'] = 'Please fill in a username';
$l['admin_members_register_error_username_length'] = 'Username must be between 3 and 80 characters';
$l['admin_members_register_error_username_taken'] = 'Sorry but that username is in use or is not allowed';
$l['admin_members_register_error_password_empty'] = 'Please fill in a password';
$l['admin_members_register_error_password_length'] = 'Password must be at least 4 characters';
$l['admin_members_register_error_passwords_verify'] = 'Those passwords don\'t match!';
$l['admin_members_register_error_email_empty'] = 'Please fill in an email address';
$l['admin_members_register_error_email_disallowed'] = 'Sorry, that email is either already in use or it is disallowed';
$l['admin_members_register_error_email_invalid'] = 'Email address is invalid';

# Registration options stuff
$l['admin_members_registration_title'] = 'Registration Options - '. $settings['site_name'];
$l['admin_members_registration_header'] = 'Registration Options';
$l['admin_members_registration_desc'] = 'Modify member registration options.';

# Registration options labels
$l['setting_registration_enabled'] = 'Enable Registration';
$l['setting_sub_registration_enabled'] = 'Enabling registration allows your visitors to register accounts.';
$l['setting_account_activation'] = 'Account Activation';
$l['setting_account_activation_none'] = 'Immediate';
$l['setting_account_activation_email'] = 'Email';
$l['setting_account_activation_approval'] = 'Administator approved';
$l['setting_sub_account_activation'] = 'Decides how member accounts are activated';
$l['setting_registration_group'] = 'Default Member Group';
$l['setting_sub_registration_group'] = 'The member group newly registered members are placed in.';
?>