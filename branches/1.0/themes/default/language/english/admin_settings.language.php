<?php
#
#   Settings English file for SnowCMS
#    Created by the SnowCMS Dev Team
#          www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $db, $settings;

# Core settings vars.
$l['admin_settings_title'] = 'Core Settings - '. $settings['site_name'];
$l['admin_settings_header'] = 'Core Settings';
$l['admin_settings_desc'] = 'These core settings can affect the way your system works and operates, from title, compression, caching, and others.';
$l['settings_core_submit'] = 'Save';
$l['admin_cache_disabled'] = 'Disabled';

# Titles for settings...
$l['setting_site_name'] = 'Site name';
$l['setting_site_name_subtext'] = 'The name of your website, displayed in the title of most pages.';
$l['setting_site_slogan'] = 'Site slogan';
$l['setting_site_slogan_subtext'] = 'The slogan of your website, displayed near your site name or logo.';
$l['setting_gz_compressed'] = 'Enable GZip compressed output';
$l['setting_gz_compressed_subtext'] = 'Enabling this can help save bandwidth, however its not supported on all servers.';
$l['setting_cache_enabled'] = 'Enable caching';
$l['setting_cache_enabled_subtext'] = 'If this is enabled, it can help improve your sites load performace.';
$l['setting_cache_type'] = 'Caching system';
$l['setting_cache_type_subtext'] = 'Choose which caching system you want to use. Not all are supported by all servers.';
$l['setting_show_query_count'] = 'Show time and queries taken';
$l['setting_show_query_count_subtext'] = 'If checked, will display the total time and queries taken to generate the page.';
$l['setting_homepage'] = 'Homepage';
$l['setting_homepage_subtext'] = 'How or what should be displayed on the homepage.';
$l['setting_homepage_0'] = 'Page only';
$l['setting_homepage_1'] = 'Page and news';
$l['setting_homepage_2'] = 'News only';
$l['setting_captcha_strength'] = 'CAPTCHA strength';
$l['setting_captcha_strength_subtext'] = 'Not all CAPTCHA settings are available for all servers.';
$l['setting_captcha_chars'] = 'CAPTCHA characters';
$l['setting_captcha_chars_subtext'] = 'Number of characters on CAPTCHA. 3-8 characters.';
$l['setting_uploaded_avatars_enabled'] = 'Enable uploaded avatars';
$l['setting_uploaded_avatars_enabled_subtext'] = 'Whether or not members should be allowed to upload avatars.';
$l['setting_avatar_filetypes'] = 'Allowed avatar file types';
$l['setting_avatar_filetypes_subtext'] = 'The file types allowed for uploaded avatars.';
$l['setting_bmp'] = 'Bitmaps';
$l['setting_gif'] = 'GIFs';
$l['setting_png'] = 'PNGs';
$l['setting_jpg'] = 'JPEGs';
$l['setting_tif'] = 'TIFFs';
$l['setting_avatar_filesize'] = 'Maximum avatar filesize';
$l['setting_avatar_filesize_subtext'] = 'The maximum filesize of uploaded avatars in kilobytes.';
$l['setting_avatar_size'] = 'Maximum avatar size';
$l['setting_avatar_size_subtext'] = 'The maximum image size of uploaded avatars in pixels.';
$l['setting_avatar_resize'] = 'Resize large avatars';
$l['setting_avatar_resize_subtext'] = 'If enabled, avatars that are too large should be resized accordingly. TIFF images cannot be resized.';
$l['setting_value_disabled'] = 'Disabled';
$l['setting_value_system_font'] = 'System Font';
$l['setting_value_weak'] = 'Weak';
$l['setting_value_medium'] = 'Medium';
$l['setting_value_strong'] = 'Strong';
$l['setting_database_sessions'] = 'Enable database stored sessions';
$l['setting_database_sessions_subtext'] = 'If checked, session data will be stored in your database and not in files. ('. (!empty($db->save_sessions) ? 'Recommended' : 'Not recommended'). ')';

# Forum settings :D
$l['admin_forum_settings_title'] = 'Forum settings - '. $settings['site_name'];
$l['admin_forum_settings_header'] = 'Forum settings';
$l['admin_forum_settings_desc'] = 'Here you can configure settings to affect the operation of your forum system.';
$l['setting_forum_recent_posts'] = 'Display how many recent posts on the forum index?';
$l['setting_forum_recent_posts_subtext'] = 'Enter 0 to disable';

# Theme settings
$l['settings_theme_title'] = 'Theme Settings - '. $settings['site_name'];
$l['settings_theme_header'] = 'Theme Settings';
$l['settings_theme_desc'] = 'Modify the defaut theme settings and view preferences here.';
$l['settings_theme_submit'] = 'Save';
$l['settings_theme_saved'] = 'Theme settings saved successfully.';
$l['settings_theme_creator'] = 'Created by: %s';
$l['settings_theme_setting_theme'] = 'Theme';
$l['settings_theme_setting_timezone'] = 'Timezone';
$l['settings_theme_setting_dst'] = 'Daylight savings time';
$l['settings_theme_value_auto_detect'] = 'Auto Detect';
$l['settings_theme_value_off'] = 'Off';
$l['settings_theme_value_on'] = 'On';
$l['settings_theme_setting_format_datetime'] = 'Date and time format';
$l['settings_theme_setting_format_datetime_subtext'] = 'Select a default date and time format from the list or enter a custom one.';
$l['settings_theme_setting_format_date'] = 'Date format';
$l['settings_theme_setting_format_date_subtext'] = 'Select a default date format from the list or enter a custom one.';
$l['settings_theme_setting_format_time'] = 'Time format';
$l['settings_theme_setting_format_time_subtext'] = 'Select a default time format from the list or enter a custom one.';
$l['settings_theme_setting_preference_today_yesterday'] = 'Today and yesterday';
$l['settings_theme_setting_preference_today_yesterday_subtext'] = 'Should today and yesterday be displayed if applicable?';
$l['settings_theme_value_today_yesterday'] = 'Today and Yesterday';
$l['settings_theme_value_today'] = 'Today Only';
$l['settings_theme_value_disabled'] = 'Disabled';
$l['settings_theme_setting_preference_quick_reply'] = 'Enable quick reply';
$l['settings_theme_setting_preference_avatars'] = 'Show avatars';
$l['settings_theme_setting_preference_signatures'] = 'Show signatures';
$l['settings_theme_setting_preference_post_images'] = 'Show images in posts';
$l['settings_theme_setting_preference_emoticons'] = 'Show emoticons';
$l['settings_theme_setting_preference_return_topic'] = 'Return to topic after posting';
$l['settings_theme_setting_preference_pm_display'] = 'Personal messages display mode';
$l['settings_theme_value_list'] = 'List';
$l['settings_theme_value_threaded'] = 'Threaded';
$l['settings_theme_setting_preference_recently_online'] = 'Show recently online users';
$l['settings_theme_setting_preference_thousands_separator'] = 'Thousands separator';
$l['settings_theme_setting_preference_thousands_separator_subtext'] = 'Enter your prefered symbol for separating thousands.';
$l['settings_theme_setting_preference_decimal_point'] = 'Decimal point';
$l['settings_theme_setting_preference_decimal_point_subtext'] = 'Enter your prefered symbol for decimal points.';
$l['settings_theme_setting_per_page_topics'] = 'Topics per page';
$l['settings_theme_setting_per_page_posts'] = 'Posts per page';
$l['settings_theme_setting_per_page_news'] = 'News per page';
$l['settings_theme_setting_per_page_downloads'] = 'Downloads per page';
$l['settings_theme_setting_per_page_comments'] = 'Comments per page';
$l['settings_theme_setting_per_page_members'] = 'Members per page';
?>