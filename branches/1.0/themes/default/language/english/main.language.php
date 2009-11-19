<?php
#
#  Main English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $base_url, $settings;

# Dates
$l['month_long_1'] = 'January';
$l['month_long_2'] = 'February';
$l['month_long_3'] = 'March';
$l['month_long_4'] = 'April';
$l['month_long_5'] = 'May';
$l['month_long_6'] = 'June';
$l['month_long_7'] = 'July';
$l['month_long_8'] = 'August';
$l['month_long_9'] = 'September';
$l['month_long_10'] = 'October';
$l['month_long_11'] = 'November';
$l['month_long_12'] = 'December';
$l['month_short_1'] = 'Jan';
$l['month_short_2'] = 'Feb';
$l['month_short_3'] = 'Mar';
$l['month_short_4'] = 'Apr';
$l['month_short_5'] = 'May';
$l['month_short_6'] = 'Jun';
$l['month_short_7'] = 'Jul';
$l['month_short_8'] = 'Aug';
$l['month_short_9'] = 'Sep';
$l['month_short_10'] = 'Oct';
$l['month_short_11'] = 'Nov';
$l['month_short_12'] = 'Dec';
$l['day_long_0'] = 'Sunday';
$l['day_long_1'] = 'Monday';
$l['day_long_2'] = 'Tuesday';
$l['day_long_3'] = 'Wednesday';
$l['day_long_4'] = 'Thursday';
$l['day_long_5'] = 'Friday';
$l['day_long_6'] = 'Saturday';
$l['day_short_0'] = 'Sun';
$l['day_short_1'] = 'Mon';
$l['day_short_2'] = 'Tue';
$l['day_short_3'] = 'Wed';
$l['day_short_4'] = 'Thu';
$l['day_short_5'] = 'Fri';
$l['day_short_6'] = 'Sat';
$l['th_1'] = '1st';
$l['th_2'] = '2nd';
$l['th_3'] = '3th';
$l['th_4'] = '4th';
$l['th_5'] = '5th';
$l['th_6'] = '6th';
$l['th_7'] = '7th';
$l['th_8'] = '8th';
$l['th_9'] = '9th';
$l['th_10'] = '10th';
$l['th_11'] = '11th';
$l['th_12'] = '12th';
$l['th_13'] = '13th';
$l['th_14'] = '14th';
$l['th_15'] = '15th';
$l['th_16'] = '16th';
$l['th_17'] = '17th';
$l['th_18'] = '18th';
$l['th_19'] = '19th';
$l['th_20'] = '20th';
$l['th_21'] = '21st';
$l['th_22'] = '22nd';
$l['th_23'] = '23rd';
$l['th_24'] = '24th';
$l['th_25'] = '25th';
$l['th_26'] = '26th';
$l['th_27'] = '27th';
$l['th_28'] = '28th';
$l['th_29'] = '29th';
$l['th_30'] = '30th';
$l['th_31'] = '31st';
$l['birthdate_daymonth'] = '%1$s of %2$s';

# Messengers
$l['icq_messenger'] = '%s\'s ICQ';
$l['icq'] = 'ICQ Messenger';

$l['aim_messenger'] = '%s\'s AIM';
$l['aim'] = 'AOL Instant Messenger';

$l['msn_messenger'] = '%s\'s MSN Messenger';
$l['msn'] = 'MSN Messenger';

$l['yim_messenger'] = '%s\'s YIM';
$l['yim'] = 'Yahoo! Instant Messenger';

# Guest defaults
$l['guest_name'] = 'Guest';
$l['guest_group'] = 'Guest';
$l['guest_name_plural'] = 'Guests';

# Error
$l['no_homepage'] = 'No home page setting set, or valid!';

# Pagination
$l['pagination_first'] = '&laquo;&laquo; First';
$l['pagination_prev'] = '&laquo;';
$l['pagination_next'] = '&raquo;';
$l['pagination_last'] = 'Last &raquo;&raquo;';

# Theme errors!
$l['no_theme_main_error'] = 'Could not load main.template.php!';
$l['theme_file_failed'] = 'The Theme file %s.template.php failed to be loaded. It was not found!';
$l['theme_function_failed'] = 'Failed to call on %1$s(); in the template %2$s.template.php';

# Powered By :)
$l['powered_by'] = 'Powered By';

# Page created with # queries XD
$l['page_created_with'] = 'Page created with %1$s queries in %2$s seconds';

# Sidebar stuff
$l['main_sidebar_login'] = 'Login';
$l['main_sidebar_username'] = 'Username:';
$l['main_sidebar_password'] = 'Password:';
$l['main_register'] = 'Register';
$l['main_sidebar_login_button'] = 'Login';
$l['main_sidebar_welcome'] = 'Welcome';
$l['hello'] = 'Hello';
$l['welcome_pms_0_0'] = 'You have <a href="'. $base_url. '/index.php?action=pm">%1$s message</a>, <a href="'. $base_url. '/index.php?action=pm">%2$s is new</a>.';
$l['welcome_pms_1_0'] = 'You have <a href="'. $base_url. '/index.php?action=pm">%1$s messages</a>, <a href="'. $base_url. '/index.php?action=pm">%2$s is new</a>.';
$l['welcome_pms_0_1'] = 'You have <a href="'. $base_url. '/index.php?action=pm">%1$s message</a>, <a href="'. $base_url. '/index.php?action=pm">%2$s are new</a>.';
$l['welcome_pms_1_1'] = 'You have <a href="'. $base_url. '/index.php?action=pm">%1$s messages</a>, <a href="'. $base_url. '/index.php?action=pm">%2$s are new</a>.';
$l['main_sidebar_logout'] = 'Logout';

# Maintenance mode!
$l['maintenance_title'] = 'Maintenance Mode - '. $settings['site_name'];

# Static menu items... Like the Admin CP
$l['menu_static_admin'] = 'Admin CP';
$l['menu_static_admin_title'] = 'Administration Control Panel';

# Misc.
$l['edit'] = 'Edit this message';
$l['delete'] = 'Delete this message';
$l['split'] = 'Split the topic';
$l['are_you_sure'] = 'Are you sure you want to do this? This cannot be undone!';
$l['raise_order'] = 'Raise order';
$l['lower_order'] = 'Lower order';
$l['save'] = 'Save';
$l['cancel'] = 'Cancel';
$l['setting_error'] = '%s is invalid.';
$l['profile_gender_male'] = 'Male';
$l['profile_gender_female'] = 'Female';
$l['profile_gender_unspecified'] = 'Unspecified';
$l['last_edited_by'] = 'Last edited by';
$l['on'] = 'on';
$l['reason'] = 'Reason:';
$l['new'] = 'New';
$l['quote_this'] = 'Quote this message';

# File size formats
$l['b'] = 'B';
$l['kb'] = 'KB';
$l['mb'] = 'MB';
$l['gb'] = 'GB';

$l['px'] = 'px';

# BBCode stuff
$l['quote'] = 'Quote:';
$l['quote_from'] = 'Quote from %s:';
$l['code'] = 'Code';

# Sortings
$l['asc'] = 'Ascending';
$l['desc'] = 'Descending';

# Today & Yesterday!
$l['today'] = 'Today';
$l['yesterday'] = 'Yesterday';

$l['at'] = 'at';

$l['gmt'] = 'GMT';

# Lists
$l['list_separator'] = ', ';
$l['list_separator_last'] = ' and ';

$l['dimensions_separator'] = 'x';

$l['percent'] = '%';
$l['ratio'] = '%1$s:%2$s';
?>