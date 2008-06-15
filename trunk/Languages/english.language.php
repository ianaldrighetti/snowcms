<?php
// SnowCMS Main.language.php File for English
// Version 0.2

// Login.template.php stuff
$l['login_title'] = 'Login';
$l['login_header'] = 'Login';
$l['login_details'] = 'Here you can login to your '.$settings['site_name'].' account. If you do not have an account you can register one <a href="'.$cmsurl.'index.php?action=register">here</a>';
$l['login_user'] = 'Username:';
$l['login_pass'] = 'Password:';
$l['login_remember_me'] = 'Remember my username';
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
?>