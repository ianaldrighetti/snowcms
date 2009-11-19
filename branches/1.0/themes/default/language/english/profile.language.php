<?php
#
# Forum English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Showing profile stuff
$l['profile_title'] = 'Profile of %s - '. $settings['site_name'];
$l['profile_avatar'] = '%s\'s avatar';
$l['profile_profile'] = 'Profile';
$l['profile_account'] = 'Account Settings';
$l['profile_settings'] = 'Profile Settings';
$l['profile_preferences'] = 'View Preferences';
$l['profile_time_online_second'] = '%s second';
$l['profile_time_online_minute'] = '%s minute';
$l['profile_time_online_hour'] = '%s hour';
$l['profile_time_online_day'] = '%s day';
$l['profile_time_online_week'] = '%s week';
$l['profile_time_online_seconds'] = '%s seconds';
$l['profile_time_online_minutes'] = '%s minutes';
$l['profile_time_online_hours'] = '%s hours';
$l['profile_time_online_days'] = '%s days';
$l['profile_time_online_weeks'] = '%s weeks';
$l['profile_date_registered'] = 'Registered:';
$l['profile_last_visit'] = 'Last visit:';
$l['profile_posts'] = 'Posts:';
$l['profile_time_online'] = 'Time online:';
$l['profile_email'] = 'Email:';
$l['profile_email_send'] = 'Send %s an email';
$l['profile_pm'] = 'Message:';
$l['profile_send_pm'] = 'Send %s a PM';
$l['profile_msn'] = 'MSN:';
$l['profile_aim'] = 'AIM:';
$l['profile_yim'] = 'YIM:';
$l['profile_gtalk'] = 'GTalk:';
$l['profile_icq'] = 'ICQ:';
$l['profile_age'] = 'Age:';
$l['profile_happy_birthday'] = 'Happy Birthday!';
$l['profile_birthdate'] = 'Birthdate:';
$l['profile_gender'] = 'Gender:';
$l['profile_location'] = 'Location:';
$l['profile_timezone'] = 'Timezone:';
$l['profile_custom_title'] = 'Custom title:';
$l['profile_membergroup'] = 'Member group:';
$l['profile_post_group'] = 'Post group:';
$l['profile_ip'] = 'IP address:';
$l['profile_local_time'] = 'Local time:';
$l['profile_website'] = 'Website:';

# IP address list stuff
$l['profile_ip_title'] = 'IP History of %s - '. $settings['site_name'];
$l['profile_ip_header'] = 'IP History of %s';
$l['profile_ip_desc'] = 'Viewing the IP address history of %s.';
$l['profile_ip_ip'] = 'IP Address';
$l['profile_ip_first_time'] = 'First Used';
$l['profile_ip_last_time'] = 'Last Used';

# Empty IP address list stuff
$l['profile_ip_none_title'] = 'IP History of %s - '. $settings['site_name'];
$l['profile_ip_none_header'] = 'IP History of %s';
$l['profile_ip_none_desc'] = 'No IP addresses have been used by %s before.';

# Account settings
$l['profile_account_title'] = 'Account Settings - '. $settings['site_name'];
$l['profile_account_header'] = 'Account Settings';
$l['profile_account_desc'] = 'Modify %s\'s account settings here.';
$l['profile_account_your_desc'] = 'Modify your account settings here.';
$l['profile_account_saved'] = 'Account settings saved successfully';
$l['profile_account_setting_username'] = 'Username';
$l['profile_account_setting_displayName'] = 'Display name';
$l['profile_account_setting_reg_time'] = 'Date registered';
$l['profile_account_setting_sub_reg_time'] = 'Format: yyyy-mm-dd hh:mm:ss (24-hour), e.g. '. date('Y-m-d H:i:s');
$l['profile_account_setting_num_posts'] = 'Posts';
$l['profile_account_setting_language'] = 'Language';
$l['profile_account_setting_group_id'] = 'Member group';
$l['profile_account_setting_email'] = 'Email';
$l['profile_account_setting_email_error'] = 'Your email address is invalid or already in use by another member.';
$l['profile_account_setting_receive_email'] = 'Allow other members to send you emails?';
$l['profile_account_setting_sub_receive_email'] = 'Your email address won\'t be revealed.';
$l['profile_account_setting_show_email'] = 'Allow other members to see your email address?';
$l['profile_account_setting_sub_show_email'] = 'With spambot CAPTCHA protection.';
$l['profile_account_setting_passwrd'] = 'New password';
$l['profile_account_setting_sub_passwrd'] = '(Optional)';
$l['profile_account_setting_vpasswrd'] = 'Verify password';
$l['profile_account_setting_vpasswrd_error'] = 'Your verified password is incorrect.';
$l['profile_account_setting_cpasswrd'] = 'Current password';
$l['profile_account_setting_cpasswrd_error'] = 'Your current password is incorrect.';
$l['profile_account_setting_ypasswrd'] = 'Your password';
$l['profile_account_setting_sub_cpasswrd'] = 'For security purposes.';
$l['profile_account_setting_error_cpasswrd'] = 'Your current password was incorrect.';

# Profile settings stuff
$l['profile_settings_title'] = 'Profile Settings - '. $settings['site_name'];
$l['profile_settings_header'] = 'Profile Settings';
$l['profile_settings_desc'] = 'Modify %s\'s profile settings here.';
$l['profile_settings_your_desc'] = 'Modify your profile settings here.';
$l['profile_settings_saved'] = 'Profile settings saved successfully';

# Profile settings avatar stuff
$l['profile_settings_avatar_collection'] = 'Avatar from collection';
$l['profile_settings_avatar_url'] = 'Avatar from URL';
$l['profile_settings_avatar_upload'] = 'Uploaded avatar';
$l['profile_settings_avatar_none'] = '(None)';
$l['profile_settings_avatar_preview'] = 'Avatar Preview';

# Profil settings file types
$l['profile_settings_avatar_bmp'] = 'bitmaps';
$l['profile_settings_avatar_gif'] = 'GIFs';
$l['profile_settings_avatar_png'] = 'PNGs';
$l['profile_settings_avatar_jpg'] = 'JPEGs';
$l['profile_settings_avatar_tif'] = 'TIFFs';

# Profile settings errors
$l['profile_settings_error_unknown'] = 'An unknown error occured while uploading your avatar.';
$l['profile_settings_error_type'] = 'Your avatar is of an invalid file type. Only %s accepted.';
$l['profile_settings_error_filesize'] = 'Your avatar is above the maximum allowed file size. Maximum allowed file size is %s.';
$l['profile_settings_error_imagesize'] = 'Your avatar\'s dimensions are too large. Maximum allowed size is %sx%s pixels.';

# Profile settings
$l['setting_custom_title'] = 'Custom title';
$l['setting_birthdate'] = 'Birthdate';
$l['setting_sub_birthdate'] = 'Format: yyyy-mm-dd, e.g. '. date('Y-m-d');
$l['setting_location'] = 'Location';
$l['setting_gender'] = 'Gender';
$l['setting_value_unspecified'] = 'Unspecified';
$l['setting_value_male'] = 'Male';
$l['setting_value_female'] = 'Female';
$l['setting_msn'] = 'MSN';
$l['setting_sub_msn'] = 'Your MSN messenger email address.';
$l['setting_aim'] = 'AIM';
$l['setting_sub_aim'] = 'Your AOL Instance Messenger nickname.';
$l['setting_yim'] = 'YIM';
$l['setting_sub_yim'] = 'Your Yahoo! Instance Messenger nickname.';
$l['setting_gtalk'] = 'GTalk';
$l['setting_sub_gtalk'] = 'Your GTalk email address.';
$l['setting_icq'] = 'ICQ';
$l['setting_sub_icq'] = 'Your ICQ number.';
$l['setting_site_name'] = 'Website name';
$l['setting_site_url'] = 'Website URL';
$l['setting_signature'] = 'Signature';
$l['setting_sub_signature'] = 'Your signature is displayed below all your posts on the forum. BBCode and emoticons allowed.';
$l['setting_profile_text'] = 'Profile text';
$l['setting_sub_profile_text'] = 'Your profile text is displayed on your profile. BBCode and emoticons allowed.';

# View preferences
$l['profile_preferences_title'] = 'View Preferences - '. $settings['site_name'];
$l['profile_preferences_header'] = 'View Preferences';
$l['profile_preferences_desc'] = 'Modify %s\'s view preferences here.';
$l['profile_preferences_your_desc'] = 'Modify your view preferences here.';
$l['profile_preferences_saved'] = 'View preferences saved successfully.';
$l['profile_preferences_setting_theme'] = 'Theme';
$l['profile_preferences_setting_timezone'] = 'Timezone';
$l['profile_preferences_setting_dst'] = 'Daylight savings time';
$l['profile_preferences_value_auto_detect'] = 'Auto Detect';
$l['profile_preferences_value_off'] = 'Off';
$l['profile_preferences_value_on'] = 'On';
$l['profile_preferences_setting_format_datetime'] = 'Date and time format';
$l['profile_preferences_setting_format_datetime_subtext'] = 'Select a default date and tme format from the list or enter a custom one.';
$l['profile_preferences_setting_format_date'] = 'Date format';
$l['profile_preferences_setting_format_date_subtext'] = 'Select a default date format from the list or enter a custom one.';
$l['profile_preferences_setting_format_time'] = 'Time format';
$l['profile_preferences_setting_format_time_subtext'] = 'Select a default time format from the list or enter a custom one.';
$l['profile_preferences_setting_preference_today_yesterday'] = 'Today and yesterday';
$l['profile_preferences_setting_preference_today_yesterday_subtext'] = 'Should today and yesterday be displayed if applicable?';
$l['profile_preferences_value_today_yesterday'] = 'Today and Yesterday';
$l['profile_preferences_value_today'] = 'Today Only';
$l['profile_preferences_value_disabled'] = 'Disabled';
$l['profile_preferences_setting_preference_quick_reply'] = 'Enable quick reply';
$l['profile_preferences_setting_preference_avatars'] = 'Show avatars';
$l['profile_preferences_setting_preference_signatures'] = 'Show signatures';
$l['profile_preferences_setting_preference_post_images'] = 'Show images in posts';
$l['profile_preferences_setting_preference_emoticons'] = 'Show emoticons';
$l['profile_preferences_setting_preference_return_topic'] = 'Return to topic after posting';
$l['profile_preferences_setting_preference_pm_display'] = 'Personal messages display mode';
$l['profile_preferences_value_list'] = 'List';
$l['profile_preferences_value_threaded'] = 'Threaded';
$l['profile_preferences_setting_preference_recently_online'] = 'Show recently online users';
$l['profile_preferences_setting_preference_thousands_separator'] = 'Thousands separator';
$l['profile_preferences_setting_preference_thousands_separator_subtext'] = 'Enter your prefered symbol for separating thousands.';
$l['profile_preferences_setting_preference_decimal_point'] = 'Decimal point';
$l['profile_preferences_setting_preference_decimal_point_subtext'] = 'Enter your prefered symbol for decimal points.';
$l['profile_preferences_setting_per_page_topics'] = 'Topics per page';
$l['profile_preferences_setting_per_page_posts'] = 'Posts per page';
$l['profile_preferences_setting_per_page_news'] = 'News per page';
$l['profile_preferences_setting_per_page_downloads'] = 'Downloads per page';
$l['profile_preferences_setting_per_page_comments'] = 'Comments per page';
$l['profile_preferences_setting_per_page_members'] = 'Members per page';

# Invalid profile stuff
$l['profile_invalid_title'] = 'Invalid Profile - '. $settings['site_name'];
$l['profile_invalid_header'] = 'Invalid Profile';
$l['profile_invalid_desc'] = 'No member has that ID number.';
?>