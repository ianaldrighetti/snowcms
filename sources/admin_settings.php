<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# Everything about settings and such is in here! :D
#
# void settings_core();
#   - All the core settings for your SnowCMS setup are manageable
#     in here :D
#
# void settings_theme();
#

function settings_core()
{
  global $base_url, $theme_url, $db, $l, $page, $settings, $source_dir, $user;

  # You must be able to do this :|
  error_screen('manage_settings');

  # Speak my language :P
  language_load('admin_settings');

  # Here is an array of all our settings... and the options
  # for each setting... from the type to maybe even a function to
  # verify they are valid ;)
  # A note... for the text next to the variable, add a language
  # variable to the settings.language.php file in this format:
  # setting_VARIABLE, and for subtext, setting_sub_VARIABLE

  # We need an array with caching systems that are usable
  # on your server :)
  $cache_systems = array('file' => 'File based');

  # eAccelerator? :D!
  if(function_exists('eaccelerator_put'))
    $cache_systems['eaccelerator'] = 'eAccelerator';

  # APC? (Alternative PHP Cache)
  if(function_exists('apc_store'))
    $cache_systems['apc'] = 'APC';
  
  # CAPTCHA strengths
  # Disabled
  $captcha_strengths[1] = $l['setting_value_disabled'];
  if(function_exists('imagecreate'))
  {
    # System font
    $captcha_strengths[2] = $l['setting_value_system_font'];
    
    # TrueType font
    if(function_exists('imagefttext'))
    {
      $captcha_strengths = array_merge($captcha_strengths, array(
        3 => $l['setting_value_weak'],
        4 => $l['setting_value_medium'],
        5 => $l['setting_value_strong'],
      ));
    }
  }
  
  # Core settings definition list
  $core_settings = array(
    array(
      'variable' => 'site_name',
      'type' => 'text',
      'max' => 50,
      'min' => 1,
      'subtext' => true,
    ),
    array(
      'variable' => 'site_slogan',
      'type' => 'text',
      'max' => 200,
      'subtext' => true,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'gz_compressed',
      'type' => 'checkbox',
      'subtext' => true,
      'disabled' => !function_exists('ob_gzhandler'),
    ),
    array(
      'variable' => 'cache_enabled',
      'type' => 'checkbox',
      'subtext' => true,
    ),
    array(
      'variable' => 'cache_type',
      'type' => 'select',
      'subtext' => true,
      'options' => $cache_systems,
    ),
    array(
      'variable' => 'show_query_count',
      'type' => 'checkbox',
      'subtext' => true,
    ),
    array(
      'variable' => 'homepage',
      'type' => 'select',
      'subtext' => true,
      'options' => array(
                     $l['setting_homepage_0'],
                     $l['setting_homepage_1'],
                     $l['setting_homepage_2'],
                   ),
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'captcha_strength',
      'type' => 'select',
      'tags' => array('captcha'),
      'subtext' => true,
      'options' => $captcha_strengths,
    ),
    array(
      'variable' => 'captcha_chars',
      'type' => 'int',
      'max' => 8,
      'min' => 3,
      'subtext' => true,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'uploaded_avatars_enabled',
      'type' => 'checkbox',
      'subtext' => true,
    ),
    array(
      'variable' => 'avatar_filetypes',
      'type' => 'checkbox_multi',
      'subtext' => true,
      'options' => array(
                     'bmp' => $l['setting_bmp'],
                     'gif' => $l['setting_gif'],
                     'png' => $l['setting_png'],
                     'jpg' => $l['setting_jpg'],
                     'tif' => $l['setting_tif'],
                   ),
    ),
    array(
      'variable' => 'avatar_filesize',
      'type' => 'int',
      'postfix' => ' '. $l['kb'],
      'subtext' => true,
    ),
    array(
      'variable' => 'avatar_size',
      'type' => 'dimensions',
      'postfix' => $l['px'],
      'subtext' => true,
    ),
    array(
      'variable' => 'avatar_resize',
      'type' => 'checkbox',
      'subtext' => true,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'database_sessions',
      'type' => 'checkbox',
      'subtext' => true,
      'popup' => false,
    ),
  );

  #
  # Here is some more information on adding a variable to be edited...
  # variable - The variable name in the settings table (Required)
  # alias - The name for the field. Defaults to same as variable.
  # label - The label to appear in the form. Defaults to $l['setting_'. $variable]
  #         where $variable is the parameter above.
  # type - The type... in other words, the way we will validate the users input,
  #        valid options are text, text-html, textarea, textarea-html, int, double, date,
  #        time, password, function, function-password, checkbox, and select (Required)
  #        NOTE: If you use text as a type, things like < > ' " & etc. will be encoded
  #              with htmlspecialchars, if you want to allow HTML in the input, do text-html
  #        Another NOTE: When function is the text, the input type is text!!!
  # tags - An array of tags that can be checked by the theme file.
  # subtext - Whether or not to show subtext (Optional, default false)
  # popup - If you have a help popup with extra information, set this to true.
  #         In the adminhelp.language.php file, be sure to create a variable
  #         $l['popup_VARNAME']
  # length - An array with the max length and/or the minimum length of whatever
  #          it may be. Ex: array('max' => 30, 'min' => 2), With that the input could
  #          be a maximum of 30 and a minimum of 2.
  #          NOTE: When the type is int or a double, the max and min are totally different.
  #                If max were 30, then the highest the number could be is 30. ;)
  # truncate - Only works when max length is set! Set this to true if you want the text/number
  #            to be truncated to the max length...
  #            Example:
  #              - Type is text, value is Hello! and max length is 1 and truncate was true, the
  #                value would be changed to H
  #              - Type is int/double, value is 100, and max length is 50 and truncate was true,
  #                the value would be changed to 50
  # options - An array of options. Only required if type is select or checkbox_multi, the options
  #           should be setup like this:
  #           array('Option 1', 'Option 2', 'Option 3')
  #           If option 1 were selected, the value saved to the DB would be 0, if Option 2 were selected, 1 and so on.
  # function - A function which the system will pass on the input of the user. This is ONLY required
  #            if type is function or function-password!
  #            Only one parameter and it is passed by reference because the return should be
  #            a bool! True as in its OK, we did what we needed to and its valid :D Or false
  #            if it is totally not ok and don't save it! :) Ex:
  #            create_function('&$input', '
  #              if($input > 30)
  #                $input = 30;
  #              elseif($input < 2)
  #                return false;
  #              return true');
  #            Maybe not a great example, but you get the idea ;)
  # no-save - When set to true, data is not saved. Default is false.
  # disabled - Just incase you want the option to be displayed but not editable, set this
  #            to true. Default is false.
  # show - If you don't want the option to be shown at all, set this to false. Default is true.
  #

  $values = $settings;
  
  # No errors yet
  $page['errors'] = array();
  
  # Saving...?
  if(isset($_GET['save']))
  {
    # Use our cool settings_save() function :D!
    $saved_settings = settings_save($core_settings, 'update_settings');
    
    # If there were no errors.
    if(!count($page['errors']))
    {
      # Redirect.
      redirect('index.php?action=admin;sa=settings;area=core;saved');
    }
    else
    {
      # Get entered values.
      foreach($saved_settings as $setting)
        $values[$setting['name']] = $setting['value'];
    }
  }

  # Get ready to display our settings...
  $page['settings'] = settings_prepare($core_settings, $values);
  
  # JavaScripts
  $page['scripts'][] = $theme_url. '/default/js/captcha_strength.js';
  $page['onload'] = 'onload();';
  
  # The submit url.
  $page['submit_url'] = $base_url. '/index.php?action=admin;sa=settings;area=core;save';

  # Setup the page title and load the theme...
  $page['title'] = $l['admin_settings_title'];

  theme_load('admin_settings', 'settings_core_show');
}

function settings_forum()
{
  global $base_url, $theme_url, $db, $l, $page, $settings, $source_dir, $user;

  # You must be able to do this :|
  error_screen('manage_settings');

  # Speak my language :P
  language_load('admin_settings');

  $forum_settings = array(
    array(
      'variable' => 'forum_recent_posts',
      'type' => 'int',
      'subtext' => true,
    ),
  );

  $values = $settings;

  $page['errors'] = array();

  if(isset($_GET['save']))
  {
    # Use our cool settings_save() function :D!
    settings_save($forum_settings);
    
    # If there were no errors
    if(!count($page['errors']))
    {
      # Redirect...
      redirect('index.php?action=admin;sa=settings;area=forum;saved');
    }
    else
    {
      # Get entered values
      foreach($forum_settings as $setting)
        $values[$setting['variable']] = $_POST[isset($setting['alias']) ? $setting['alias'] : (isset($setting['variable']) ? $setting['variable'] : '')];
    }
  }

  $page['settings'] = settings_prepare($forum_settings, $values);

  # The submit url.
  $page['submit_url'] = $base_url. '/index.php?action=admin;sa=settings;area=forum;save';

  # Setup the page title and load the theme...
  $page['title'] = $l['admin_forum_settings_title'];

  $page['settings_header'] = $l['admin_forum_settings_header'];
  $page['settings_desc'] = $l['admin_forum_settings_desc'];

  theme_load('admin_settings', 'settings_core_show');
}

function settings_theme()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $theme_dir, $avatar_dir, $user;
  
  # Logged in users only
  if(!$user['is_logged'])
    error_screen();
  
  # Get the language
  language_load('admin_settings');
  
  # No errors yet
  $page['errors'] = array();
  
  # Get the themes
  $themes = array();
  $page['theme'] = array(
    'name' => '',
    'desc' => '',
    'creator' => '',
    'website' => '',
    'thumb' => '',
  );
  foreach(theme_list() as $key => $ini)
  {
    $themes[$key] = isset($ini['name']) ? $ini['name'] : '';
    
    # If it's the selected theme, record some information about it
    if($key == $settings['theme'])
    {
      $page['theme'] = array(
        'name' => isset($ini['name']) ? $ini['name'] : '',
        'desc' => isset($ini['desciption']) ? $ini['desciption'] : '',
        'creator' => isset($ini['creator']) ? $ini['creator'] : '',
        'website' => isset($ini['website']) ? $ini['website'] : '',
        'thumb' => isset($ini['thumb']) ? $ini['thumb'] : '',
      );
    }
  }
  
  # Get the timezones
  $timezones = array(
    'GMT-12:00 - International Date Line West',
    'GMT-11:00 - Midway Island, Samoa',
    'GMT-10:00 - Hawaii',
    'GMT-09:00 - Alaska',
    'GMT-08:00 - Pacific Time (US & Canada)',
    'GMT-08:00 - Tijuana, Baja California',
    'GMT-07:00 - Arizona',
    'GMT-07:00 - Chihuahua, La Paz, Mazatlan',
    'GMT-07:00 - Mountain Time (US & Canada)',
    'GMT-06:00 - Central America',
    'GMT-06:00 - Central Time (US & Canada)',
    'GMT-06:00 - Guadalajara, Mexico City',
    'GMT-06:00 - Monterrey',
    'GMT-06:00 - Saskatchewan',
    'GMT-05:00 - Bogota, Lima, Quito, Rio Branco',
    'GMT-05:00 - Eastern Time (US & Canada)',
    'GMT-05:00 - Indiana (East)',
    'GMT-04:30 - Caracas',
    'GMT-04:00 - Atlantic Time (Canada)',
    'GMT-04:00 - La Paz',
    'GMT-04:00 - Manaus',
    'GMT-04:00 - Santiago',
    'GMT-03:30 - Newfoundland',
    'GMT-03:00 - Brasilia',
    'GMT-03:00 - Buenos Aires',
    'GMT-03:00 - Georgetown',
    'GMT-03:00 - Greenland',
    'GMT-03:00 - Montevideo',
    'GMT-02:00 - Mid-Atlantic',
    'GMT-01:00 - Azores',
    'GMT-01:00 - Cape Verde Is.',
    'GMT+00:00 - Casablanca',
    'GMT+00:00 - Dublin, Edinburgh, Lisbon, London',
    'GMT+00:00 - Monrovia, Reykjavik',
    'GMT+01:00 - Amsterdam, Berlin, Bern',
    'GMT+01:00 - Rome, Stockholm, Vienna',
    'GMT+01:00 - Belgrade, Bratislava, Budapest',
    'GMT+01:00 - Ljubljana, Prague',
    'GMT+01:00 - Brussels, Copenhagen',
    'GMT+01:00 - Madrid, Paris',
    'GMT+01:00 - Sarajevo, Skopje, Warsaw, Zagreb',
    'GMT+01:00 - West Central Africa',
    'GMT+02:00 - Amman',
    'GMT+02:00 - Athens, Bucharest, Istanbul',
    'GMT+02:00 - Beirut',
    'GMT+02:00 - Cairo',
    'GMT+02:00 - Harare, Pretoria',
    'GMT+02:00 - Helsinki, Kyiv, Riga',
    'GMT+02:00 - Sofia, Tallinn, Vilnius',
    'GMT+02:00 - Jerusalem',
    'GMT+02:00 - Minsk',
    'GMT+02:00 - Windhoek',
    'GMT+03:00 - Baghdad',
    'GMT+03:00 - Kuwait, Riyadh',
    'GMT+03:00 - Moscow, St. Petersburg',
    'GMT+03:00 - Volgograd',
    'GMT+03:00 - Nairobi',
    'GMT+03:00 - Tbilisi',
    'GMT+03:30 - Tehran',
    'GMT+04:00 - Abu Dhabi, Muscat',
    'GMT+04:00 - Baku',
    'GMT+04:00 - Yerevan',
    'GMT+04:30 - Kabul',
    'GMT+05:00 - Ekaterinburg',
    'GMT+05:00 - Islamabad, Karachi',
    'GMT+05:00 - Tashkent',
    'GMT+05:30 - Chennai, Kolkata',
    'GMT+05:30 - Mumbai, New Delhi',
    'GMT+05:30 - Sri Jayawardenepura',
    'GMT+05:45 - Kathmandu',
    'GMT+06:00 - Almaty, Novosibirsk',
    'GMT+06:00 - Astana, Dhaka',
    'GMT+06:30 - Yangon (Rangoon)',
    'GMT+07:00 - Bangkok, Hanoi, Jakarta',
    'GMT+07:00 - Krasnoyarsk',
    'GMT+08:00 - Beijing, Chongqing',
    'GMT+08:00 - Hong Kong, Urumqi',
    'GMT+08:00 - Irkutsk, Ulaan Bataar',
    'GMT+08:00 - Kuala Lumpur, Singapore',
    'GMT+08:00 - Perth',
    'GMT+08:00 - Taipei',
    'GMT+09:00 - Osaka, Sapporo, Tokyo',
    'GMT+09:00 - Seoul',
    'GMT+09:00 - Yakutsk',
    'GMT+09:30 - Adelaide',
    'GMT+09:30 - Darwin',
    'GMT+10:00 - Brisbane',
    'GMT+10:00 - Canberra, Melbourne, Sydney',
    'GMT+10:00 - Guam, Port Moresby',
    'GMT+10:00 - Hobart',
    'GMT+10:00 - Vladivostok',
    'GMT+11:00 - Magadan, Solomon Is.',
    'GMT+11:00 - New Caledonia',
    'GMT+12:00 - Auckland, Wellington',
    'GMT+12:00 - Fiji, Kamchatka, Marshall Is.',
    'GMT+13:00 - Nuku\'alofa',
  );
  
  # Get per page options
  $per_page_options = array(
    10 => numberformat(10),
    20 => numberformat(20),
    30 => numberformat(30),
    40 => numberformat(40),
    50 => numberformat(50),
  );
  
  # Get all our settings in a nice long array
  $theme_settings = array(
    array(
      'variable' => 'theme',
      'type' => 'select',
      'options' => $themes,
      'tags' => array(
                  'theme',
                ),
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'timezone',
      'type' => 'select',
      'options' => $timezones,
    ),
    array(
      'variable' => 'dst',
      'type' => 'select',
      'options' => array(
                     '2' => $l['settings_theme_value_auto_detect'],
                     '0' => $l['settings_theme_value_off'],
                     '1' => $l['settings_theme_value_on'],
                   ),
    ),
    array(
      'variable' => 'format_datetime',
      'type' => 'select',
      'max' => 75,
      'min' => 2,
      'subtext' => true,
      'options' => array(
                     'MMMM D, YYYY, h:mm:ss P' => calculate_time('MMMM D, YYYY, h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     'D MMMM YYYY, h:mm:ss P' => calculate_time('D MMMM YYYY, h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     'MM/DD/YYYY, h:mm:ss P' => calculate_time('MM/DD/YYYY, h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     'DD/MM/YYYY, h:mm:ss P' => calculate_time('DD/MM/YYYY, h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     'YYYY-MM-DD HH:mm:ss' => calculate_time('YYYY-MM-DD HH:mm:ss', time_utc() + $user['timezone'] * 3600),
                   ),
      'other' => true,
      'popup' => true,
    ),
    array(
      'variable' => 'format_date',
      'type' => 'select',
      'max' => 75,
      'min' => 2,
      'subtext' => true,
      'options' => array(
                     'MMMM D, YYYY' => calculate_time('MMMM D, YYYY', time_utc() + $user['timezone'] * 3600),
                     'D MMMM YYYY' => calculate_time('D MMMM YYYY', time_utc() + $user['timezone'] * 3600),
                     'MM/DD/YYYY' => calculate_time('MM/DD/YYYY', time_utc() + $user['timezone'] * 3600),
                     'DD/MM/YYYY' => calculate_time('DD/MM/YYYY', time_utc() + $user['timezone'] * 3600),
                     'YYYY-MM-DD' => calculate_time('YYYY-MM-DD', time_utc() + $user['timezone'] * 3600),
                   ),
      'other' => true,
      'popup' => true,
    ),
    array(
      'variable' => 'format_time',
      'type' => 'select',
      'max' => 75,
      'min' => 2,
      'subtext' => true,
      'options' => array(
                     'h:mm:ss P' => calculate_time('h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     'h:mm:ss p' => calculate_time('h:mm:ss p', time_utc() + $user['timezone'] * 3600),
                     'HH:mm:ss' => calculate_time('HH:mm:ss', time_utc() + $user['timezone'] * 3600),
                   ),
      'other' => true,
      'popup' => true,
    ),
    array(
      'variable' => 'preference_today_yesterday',
      'type' => 'select',
      'subtext' => true,
      'options' => array(
                     2 => $l['settings_theme_value_today_yesterday'],
                     1 => $l['settings_theme_value_today'],
                     0 => $l['settings_theme_value_disabled'],
                   ),
      'popup' => false,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'preference_quick_reply',
      'type' => 'checkbox',
    ),
    array(
      'variable' => 'preference_avatars',
      'type' => 'checkbox',
    ),
    array(
      'variable' => 'preference_signatures',
      'type' => 'checkbox',
    ),
    array(
      'variable' => 'preference_post_images',
      'type' => 'checkbox',
    ),
    array(
      'variable' => 'preference_emoticons',
      'type' => 'checkbox',
    ),
    array(
      'variable' => 'preference_return_topic',
      'type' => 'checkbox',
    ),
    array(
      'variable' => 'preference_pm_display',
      'type' => 'select',
      'options' => array(
                     $l['settings_theme_value_list'],
                     $l['settings_theme_value_threaded'],
                   ),
    ),
    array(
      'variable' => 'preference_recently_online',
      'type' => 'checkbox',
    ),
    array(
      'variable' => 'preference_thousands_separator',
      'type' => 'text',
      'max' => 1,
      'subtext' => true,
    ),
    array(
      'variable' => 'preference_decimal_point',
      'type' => 'text',
      'min' => 1,
      'max' => 1,
      'subtext' => true,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'per_page_topics',
      'type' => 'select',
      'options' => $per_page_options,
    ),
    array(
      'variable' => 'per_page_posts',
      'type' => 'select',
      'options' => $per_page_options,
    ),
    array(
      'variable' => 'per_page_news',
      'type' => 'select',
      'options' => $per_page_options,
    ),
    array(
      'variable' => 'per_page_downloads',
      'type' => 'select',
      'options' => $per_page_options,
    ),
    array(
      'variable' => 'per_page_comments',
      'type' => 'select',
      'options' => $per_page_options,
    ),
    array(
      'variable' => 'per_page_members',
      'type' => 'select',
      'options' => $per_page_options,
    ),
  );
  
  $values = $settings;
  
  # Saving?
  if(isset($_POST['process']))
  {
    # Use the cool settings_save() function
    $saved_settings = settings_save($theme_settings, 'update_settings');
    
    # If there were no errors.
    if(!count($page['errors']))
    {
      # Redirect.
    redirect('index.php?action=admin;sa=settings;area=themes');
    }
    else
    {
      # Get entered values.
      foreach($saved_settings as $setting)
        $values[$setting['name']] = $setting['value'];
    }
  }
  
  # Get ready to display our settings
  $page['settings'] = settings_prepare($theme_settings, $values, 'settings_theme_');
  
  # Whether or not settings have just been saved
  $page['saved'] = isset($_GET['saved']);
  
  # Add the JavaScript
  $page['scripts'][] = $user['theme_url']. '/default/js/admin_settings.js';
  
  # The submit URL
  $page['submit_url'] = $base_url. '/index.php?action=profile;sa=preferences;save';
  
  # Title and theme
  $page['title'] = $l['settings_theme_title'];
  
  theme_load('admin_settings', 'settings_theme_show');
}

function settings_theme_ajax()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $theme_dir, $avatar_dir, $user;
  
  # Get the themes
  $themes = array();
  foreach(theme_list() as $key => $ini)
    $themes[$key] = $ini['name'];
}
?>