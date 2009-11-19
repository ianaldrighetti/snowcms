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
# Handle showing a member's profile, editing profile settings, etc.
# 
# void profile_switch();
#   - Decide which function should handle the current action, based on GET data.
#
# void profile_account();
#  - Shows account settings and lets the member modify them.
#
# void profile_settings();
#  - Shows profile settings and lets the member modify them.
#
# void profile_preferences();
#  - Shows view preferences and lets the member modify them.
#
# void profile_member();
#   - Show a member's profile.
#
# void profile_ip();
#   - List a member's IP address history.
#
# string profile_validate_email(array $email);
#   - Returns whether the email address is valid or not.
#
# string profile_password_verify(array $password);
#   - Returns whether new password passed verification.
#
# string profile_password_validate(array $password);
#   - Returns whether password was correct.
#
# string profile_avatar_validate(array $avatar);
#
# void profile_update(array $settings);
#   - Updates member settings in the database.
#
# string profile_birthdate_convert();
#

function profile_switch()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;
  
  # Check out what we should be doing
  if(!empty($_GET['sa']) && $_GET['sa'] == 'account')
  {
    # Modifying account settings
    profile_account();
  }
  elseif(!empty($_GET['sa']) && $_GET['sa'] == 'settings')
  {
    # Modifying profile settings
    profile_settings();
  }
  elseif(!empty($_GET['sa']) && $_GET['sa'] == 'preferences')
  {
    # Modifying view preferences
    profile_preferences();
  }
  elseif(!empty($_GET['sa']) && $_GET['sa'] == 'ip')
  {
    # Viewing IP addresses
    profile_ip();
  }
  else
  {
    # Displaying a profile
    profile_member();
  }
}

function profile_member()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;
  
  # Get the member.
  $member_id = !empty($_GET['u']) ? (int)$_GET['u'] : $user['id'];
  
  # We can't show you a guest's profile
  if(!$member_id)
  {
    error_screen();
  }
  
  # Load the language
  language_load('profile');
  
  # Get the member from the database
  $result = $db->query("
    SELECT
      mem.member_id, mem.displayName, mem.custom_title, grp.group_id,
      grp.group_name, grp.group_color, grp.stars, grp2.group_id AS post_group_id,
      grp2.group_name AS post_group_name, grp2.group_color AS post_group_color,
      grp2.stars AS post_group_stars, mem.reg_time, mem.last_online, mem.num_posts,
      mem.time_online, mem.show_email, mem.email, mem.receive_email, mem.msn,
      mem.aim, mem.yim, mem.gtalk, mem.icq, mem.birthdate, mem.avatar, mem.gender,
      mem.location, mem.timezone, mem.dst, mem.site_name, mem.site_url,
      mem.last_ip, mem.signature, mem.profile_text
    FROM {$db->prefix}members AS mem
    LEFT JOIN {$db->prefix}membergroups AS grp ON grp.group_id = mem.group_id
    LEFT JOIN {$db->prefix}membergroups AS grp2 ON grp2.group_id = mem.post_group_id
    WHERE mem.member_id = %member_id
    LIMIT 1",
    array(
      'member_id' => array('int', $member_id),
    ));
  
  # Check if the member exists
  if(!$db->num_rows($result))
  {
    # An invalid member? Let's let them know
    $page['title'] = $l['profile_invalid_title'];
    theme_load('profile', 'profile_member_show_invalid');
  }
  else
  {
    # Okay, that member exists, let's continue
    $row = $db->fetch_assoc($result);
    
    # Calculate time online
    $weeks = floor($row['time_online'] / 60 / 60 / 24 / 7) * 60 * 60 * 24 * 7;
    $days = floor(($row['time_online'] - $weeks) / 60 / 60 / 24) * 60 * 60 * 24;
    $hours = floor(($row['time_online'] - $weeks - $days) / 60 / 60) * 60 * 60;
    $minutes = floor(($row['time_online'] - $weeks - $days - $hours) / 60) * 60;
    $seconds = floor($row['time_online'] - $weeks - $days - $hours - $minutes);
    
    # Divide the time blocks into their appropriate amounts
    $weeks /= 60 * 60 * 24 * 7;
    $days /= 60 * 60 * 24;
    $hours /= 60 * 60;
    $minutes /= 60;
    
    # Add text to time online
    $weeks = $weeks ? sprintf($l['profile_time_online_week'. ($weeks != 1 ? 's' : '')], numberformat($weeks)) : '';
    $days = $days || $weeks ? sprintf($l['profile_time_online_day'. ($days != 1 ? 's' : '')], numberformat($days)) : '';
    $hours = $hours || $days || $weeks ? sprintf($l['profile_time_online_hour'. ($hours != 1 ? 's' : '')], numberformat($hours)) : '';
    $minutes = $minutes || $hours || $days || $weeks ? sprintf($l['profile_time_online_minute'. ($minutes != 1 ? 's' : '')], numberformat($minutes)) : '';
    $seconds = $seconds || $minutes || $hours || $days || $weeks ? sprintf($l['profile_time_online_second'. ($seconds != 1 ? 's' : '')], numberformat($seconds)) : sprintf($l['profile_time_online_seconds'], 0);
    
    # Get member's timezone
    $timezone = timezone_get($row['timezone'], $row['dst']);
    
    # Format the member's data in an array
    $page['member'] = array(
      'id' => $row['member_id'],
      'username' => $row['displayName'],
      'custom_title' => $row['custom_title'] ? $row['custom_title'] : $row['post_group_name'],
      # Display group
      'group' => array(
                   'id' => $row['group_id'],
                   'name' => $row['group_name'],
                   'color' => $row['group_color'],
                   'stars' => array(
                                # $row['stars'] has the format of 'amount|image'
                                # So let's fix that :P
                                'amount' => mb_substr(mb_strstr(strrev($row['stars']),'|'), 1),
                                'image' => mb_substr(mb_strstr($row['stars'],'|'), 1),
                              ),
                 ),
      'membergroup' => array(
                   'id' => $row['group_id'],
                   'name' => $row['group_name'],
                   'color' => $row['group_color'],
                   'stars' => array(
                                # $row['stars'] has the format of 'amount|image'
                                # So let's fix that :P
                                'amount' => mb_substr(mb_strstr(strrev($row['stars']),'|'), 1),
                                'image' => mb_substr(mb_strstr($row['stars'],'|'), 1),
                              ),
                 ),
      'post_group' => array(
                   'id' => $row['post_group_id'],
                   'name' => $row['post_group_name'],
                   'color' => $row['post_group_color'],
                   'stars' => array(
                                # $row['post_group_stars'] has the format of 'amount|image'
                                # So let's fix that :P
                                'amount' => mb_substr(mb_strstr(strrev($row['post_group_stars']),'|'), 1),
                                'image' => mb_substr(mb_strstr($row['post_group_stars'],'|'), 1),
                              ),
                 ),
      'reg_time' => timeformat($row['reg_time']),
      'last_online' => timeformat($row['last_online']),
      'posts' => $row['num_posts'],
      'time_online' => create_list(array($weeks, $days, $hours, $minutes, $seconds), false),
      # Format the email as a link
      'email' => ($row['show_email'] || $user['id'] == $row['member_id'] || can('override_show_email')) && $user['is_logged']
                 # If they let people see their email
                 ? '<a href="'. $base_url. '/index.php?action=email;u='. $row['member_id']. '">'. $row['email']. '</a>'
                 # If they don't let people see their email, but let people send them emails
                 : ($row['receive_email'] || can('override_receive_email')
                   ? '<a href="'. $base_url. '/index.php?action=email;u='. $row['member_id']. '">'.
                      sprintf($l['profile_email_send'], $row['displayName']). '</a>'
                   # And if email address is completely hidden :P
                   : ''),
      'msn' => $row['msn'] ? (
                 !$user['is_logged']
                 ? '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. ';unhide">'. email_hide($row['msn']). '</a>'
                 : '<a href="http://spaces.live.com/profile.aspx?mem='. $row['msn']. '">'. $row['msn']. '</a>'
               ) : '',
      'aim' => $row['msn'] ? (
                 !$user['is_logged'] && mb_strpos($row['aim'], '@')
                 ? '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. ';unhide">'. email_hide($row['aim']). '</a>'
                 : '<a href="aim:goim?screenname='. $row['aim']. '">'. $row['aim']. '</a>'
               ) : '',
      'yim' => $row['yim'] ? '<a href="http://webmessenger.yahoo.com/?im='. $row['yim']. '">'. $row['yim']. '</a>' : '',
      'gtalk' => !$user['is_logged']
               ? '<a href="'. $base_url. '/index.php?action=profile;u='. $row['member_id']. ';unhide">'. email_hide($row['gtalk']). '</a>'
               : $row['gtalk'],
      'icq' => $row['icq'] ? '<a href="http://www.icq.com/whitepages/about_me.php?uin='. $row['icq']. '">'. $row['icq']. '</a>' : '',
      'age' => $row['birthdate'] ? profile_birthday_convert($row['birthdate'], 'age'). ' years old' : '',
      'birthday' => profile_birthday_convert($row['birthdate'], 'is_birthday'),
      'birthdate' => $row['birthdate'] ? profile_birthday_convert($row['birthdate'], 'birthdate') : '',
      'avatar' => return_avatar($row['avatar'], $row['member_id']),
      'gender' => $row['gender'],
      'location' => $row['location'],
      'timezone' => ($timezone >= 0 ? '+' : ''). floor($timezone). ':'. mb_substr(number_format(($timezone - floor($timezone)) * 0.6, 2), 2),
      'local_time' => timeformat(time_utc(), 0, $timezone),
      'site_name' => $row['site_name'] ? $row['site_name'] : preg_replace('/^(https?:\/\/)?([a-z-.]+).*?$/i', '$2', $row['site_url']),
      'site_url' => $row['site_url'],
      'ip' => $row['last_ip'],
      'signature' => bbc($row['signature']),
      'profile_text' => bbc($row['profile_text']),
    );
    
    # Set the title
    $page['title'] = sprintf($l['profile_title'], $page['member']['username']);
    
    # Load the theme
    theme_load('profile', 'profile_member_show');
  }
}

function profile_ip()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;
  
  # Get the member.
  $member_id = !empty($_GET['u']) ? (int)$_GET['u'] : $user['id'];
  
  # We can't show you a guest's IP addresses
  if(!$member_id)
    error_screen();
  else
    error_screen('view_ips');
  
  # Load the language
  language_load('profile');
  
  # Get the member from the database
  $result = $db->query("
    SELECT
      member_id AS id, displayName AS username, last_online
    FROM {$db->prefix}members
    WHERE member_id = %member_id
    LIMIT 1",
    array(
      'member_id' => array('int', $member_id),
    ));
  
  # Check if the member exists
  if(!$db->num_rows($result))
  {
    # An invalid member? Let's let them know
    $page['title'] = $l['profile_invalid_title'];
    theme_load('profile', 'profile_member_show_invalid');
  }
  else
  {
    # Okay, the member exists, let's continue
    $page['member'] = $db->fetch_assoc($result);
    
    # Get the member's IP addresses from the database
    $result = $db->query("
      SELECT
        ip, first_time, last_time
      FROM {$db->prefix}ip_logs
      WHERE member_id = %member_id
      LIMIT 1",
      array(
        'member_id' => array('int', $member_id),
      ));
    
    # Check if there were any IPs
    if(!$db->num_rows($result))
    {
      # No IP addresses? Let's load the theme and tell them
      $page['title'] = sprintf($l['profile_ip_none_title'], $page['member']['username']);
      theme_load('profile', 'profile_ip_show_none');
    }
    else
    {
      # Put the IPs in an array
      $page['ips'] = array();
      while($row = $db->fetch_assoc($result))
        $page['ips'][] = $row;
      
      # The most recently used IP should actually have the member's last online time
      $page['ips'][0]['last_time'] = $page['member']['last_online'];
      
      # Set the title
      $page['title'] = sprintf($l['profile_ip_title'], $page['member']['username']);
      
      # Load the theme
      theme_load('profile', 'profile_ip_show');
    }
  }
}

function profile_account()
{
  global $base_url, $source_dir, $db, $l, $page, $settings, $source_dir, $theme_dir, $user;
  
  # Logged in users only
  if(!$user['is_logged'])
    error_screen();
  
  # Get the member.
  $member_id = !empty($_GET['u']) ? (int)$_GET['u'] : $user['id'];
  
  # Check if the member is the one logged in.
  $me = $user['id'] == $member_id;
  
  # If they are editing someone else's profile, we need to screen it.
  if(!$me)
    error_screen('edit_profile_any');
  # Okay, well are they allowed to edit their own profile.
  else
    error_screen('edit_profile');
  
  # Get the language
  language_load('profile');
  
  # No errors yet
  $page['errors'] = array();
  
  # Get the member groups
  $result = $db->query("
    SELECT
      group_id, group_name
    FROM {$db->prefix}membergroups",
    array());
  
  # Format member groups in a 2D array
  while($row = $db->fetch_assoc($result))
    $membergroups[$row['group_id']] = $row['group_name'];
  
  # Get the language folders in the default theme
  if(is_readable($theme_dir. '/default/language'))
    foreach(scandir($theme_dir. '/default/language') as $language)
      $lfolders[] = 'default/language/'. $language;
  
  # Get the languages folders in the selected theme, if it is not the default
  if($settings['theme'] != 'default' && is_readable($theme_dir. '/'. $settings['theme']. '/language'))
  {
    foreach(scandir($theme_dir. '/'. $settings['theme']. '/language') as $language)
      $lfolders[] = $settings['theme']. '/language/'. $language;
  }
  
  # Remove anything that has a period (e.g. index.php as well as . and ..)
  foreach($lfolders as $id => $lfolder)
    if(preg_match('/\./',$lfolder))
      unset($lfolders[$id]);
  
  # Convert the language folders into a language array
  foreach($lfolders as $lfolder)
  {
    # Get the language ID (Folder name without path)
    $lang = preg_replace('/.*language\/(.*)/','$1',$lfolder);
    
    # Get the language's name from name.ini
    if(file_exists($theme_dir. '/'. $lfolder. '/name.ini'))
    {
      $languages[$lang] = preg_replace('/name=(.*)/', '$1', file_get_contents($theme_dir. '/'. $lfolder. '/name.ini'));
    }
    elseif(!isset($languages[$lang]))
    {
      # name.ini is missing, so fallback on the language's ID, unless this language name
      # has already been set (By the default theme's language of the same ID)
      $languages[$lang] = $lang;
    }
  }
  
  # Get all our settings in a nice long array
  $account_settings = array(
    array(
      'variable' => 'loginName',
      'alias' => 'username',
      'name' => 'username',
      'type' => 'text',
      'length' => array(
                    'max' => 80,
                    'min' => 3,
                  ),
      'show' => $me ? can('edit_username') : can('edit_username_any'),
    ),
    array(
      'variable' => 'displayName',
      'type' => 'text',
      'length' => array(
                    'max' => 80,
                    'min' => 3,
                  ),
      'show' => $me ? can('edit_display)name') : can('edit_display_name_any'),
    ),
    array(
      'variable' => 'reg_time',
      'type' => 'time',
      'subtext' => true,
      'show' => $me ? can('edit_date_registered') : can('edit_date_registered_any'),
    ),
    array(
      'variable' => 'num_posts',
      'type' => 'int',
      'show' => $me ? can('edit_post_count') : can('edit_post_count_any'),
    ),
    array(
      'variable' => 'language',
      'type' => 'select',
      'options' => $languages,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'group_id',
      'type' => 'select',
      'show' => $me ? can('edit_membergroup') : can('edit_membergroup_any'),
      'options' => $membergroups,
    ),
    array(
      'type' => 'separator',
      'show' => $me ? can('edit_membergroup') : can('edit_membergroup_any'),
    ),
    array(
      'variable' => 'email',
      'type' => 'text',
      'function' => 'profile_validate_email',
    ),
    array(
      'variable' => 'receive_email',
      'type' => 'checkbox',
      'subtext' => true,
    ),
    array(
      'variable' => 'show_email',
      'type' => 'checkbox',
      'subtext' => true,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'passwrd',
      'type' => 'password',
      'valdiate' => 'profile_password_verify',
      'subtext' => true,
    ),
    array(
      'variable' => 'vpasswrd',
      'type' => 'password',
      'save' => false,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'cpasswrd',
      'label' => $user['id'] == ($member_id) ? $l['profile_account_setting_cpasswrd'] : $l['profile_account_setting_ypasswrd'],
      'type' => 'password',
      'validate' => 'profile_password_validate',
      'save' => false,
      'subtext' => true,
    ),
  );
  
  # Get the member's account settings
  $result = $db->query("
    SELECT
      loginName, displayName, reg_time, num_posts, language,
      group_id, email, receive_email, show_email
    FROM {$db->prefix}members
    WHERE member_id = %member_id
    LIMIT 1",
    array(
      'member_id' => array('int', $member_id),
    ));
  $values = $db->fetch_assoc($result);
  
  # Saving?
  if(isset($_POST['process']))
  {
    # Use our cool settings_save() function
    $saved_settings = settings_save($account_settings, 'profile_update', 'profile_account_');

    # If there were no errors
    if(!$page['errors'])
    {
      # Redirect
      if($user['id'] == ($member_id))
        redirect('index.php?action=profile;sa=account;saved');
      else
        redirect('index.php?action=profile;sa=account;u='. ($member_id). ';saved');
    }
    else
    {
      # Get entered values
      foreach($saved_settings as $setting)
        $values[$setting['name']] = $setting['value'];
    }
  }
  
  # Get ready to display our settings
  $page['settings'] = settings_prepare($account_settings, $values, 'profile_account_');
  
  # Whether or not settings have just been saved
  $page['saved'] = isset($_GET['saved']);
  
  # Member's ID and display name
  $page['member_id'] = $member_id;
  $page['member_name'] = $values['displayName'];
  
  # The submit URL
  if($user['id'] == ($member_id))
  {
    $page['submit_url'] = $base_url. '/index.php?action=profile;sa=account;save';
  }
  else
  {
    $page['submit_url'] = $base_url. '/index.php?action=profile;sa=account;u='. ($member_id). ';save';
  }
  
  # Title and theme
  $page['title'] = $l['profile_account_title'];
  
  theme_load('profile', 'profile_account_show');
}

function profile_settings()
{
  global $base_url, $theme_url, $db, $l, $page, $settings, $source_dir, $theme_dir, $avatar_dir, $user;
  
  # Logged in users only
  if(!$user['is_logged'])
    error_screen();
  
  # Get the member.
  $member_id = !empty($_GET['u']) ? (int)$_GET['u'] : $user['id'];
  
  # Check if the member is the one logged in.
  $me = $user['id'] == $member_id;
  
  # If they are editing someone else's profile, we need to screen it.
  if(!$me)
    error_screen('edit_profile_any');
  # Okay, well are they allowed to edit their own profile.
  else
    error_screen('edit_profile');
  
  # Get the language
  language_load('profile');
  
  # No errors yet
  $page['errors'] = array();
  
  # Get all our settings in a nice long array
  $profile_settings = array(
    array(
      'variable' => 'avatar',
      'type' => 'text',
      'tags' => array(
                  'avatar',
                ),
      'validate' => 'profile_avatar_validate',
      'save' => true,
      # Non-standard attribute.
      'member_id' => $member_id,
    ),
    array(
      'variable' => 'custom_title',
      'type' => 'text',
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'birthdate',
      'type' => 'date',
      'complete' => false,
      'subtext' => true,
    ),
    array(
      'variable' => 'location',
      'type' => 'text',
    ),
    array(
      'variable' => 'gender',
      'type' => 'select',
      'options' => array(
                     '0' => $l['setting_value_unspecified'],
                     '2' => $l['setting_value_male'],
                     '1' => $l['setting_value_female'],
                   ),
      'subtext' => true,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'msn',
      'type' => 'text',
      'subtext' => true,
    ),
    array(
      'variable' => 'aim',
      'type' => 'text',
      'subtext' => true,
    ),
    array(
      'variable' => 'yim',
      'type' => 'text',
      'subtext' => true,
    ),
    array(
      'variable' => 'gtalk',
      'type' => 'text',
      'subtext' => true,
    ),
    array(
      'variable' => 'icq',
      'type' => 'text',
      'subtext' => true,
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'site_name',
      'type' => 'text',
    ),
    array(
      'variable' => 'site_url',
      'type' => 'text',
    ),
    array(
      'type' => 'separator',
    ),
    array(
      'variable' => 'signature',
      'type' => 'textarea',
      'subtext' => true,
    ),
    array(
      'variable' => 'profile_text',
      'type' => 'textarea',
      'subtext' => true,
    ),
  );
  
  # Get the member's account settings
  $result = $db->query("
    SELECT
      displayName, custom_title, avatar, birthdate, location, gender, msn, aim, yim,
      gtalk, icq, site_name, site_url, signature, profile_text
    FROM {$db->prefix}members
    WHERE member_id = %member_id
    LIMIT 1",
    array(
      'member_id' => array('int', $member_id),
    ));
  $values = $db->fetch_assoc($result);
  
  # If the site URL isn't set, default to http://
  $values['site_url'] = $values['site_url'] ? $values['site_url'] : 'http://';
  
  # Saving?
  if(isset($_POST['process']))
  {
    # Use our cool settings_save() function
    $saved_settings = settings_save($profile_settings, 'profile_update');
    
    # Check for errors
    if(!$page['errors'])
    {
      # Redirect
      redirect('index.php?action=profile;sa=settings;saved');
    }
    # Errors? Then get the default values for the form.
    else
    {
      foreach($saved_settings as $field)
        $values[$field['name']] = $field['value'];
    }
  }
  
  # Get ready to display our settings
  $page['settings'] = settings_prepare($profile_settings, $values);
  
  # Whether or not settings have just been saved
  $page['saved'] = isset($_GET['saved']);
  
  # Member's ID and display name
  $page['member_id'] = $member_id;
  $page['member_name'] = $values['displayName'];
  
  # Sort out the avatar
  $avatar = $values['avatar'];
  
  # Is it an avatar URL?
  if(preg_match('/^[a-z]+:\/\//i',$avatar))
  {
    $page['avatar_image'] = $avatar;
    $page['avatar_radio'] = 'url';
    $page['avatar_collection'] = '';
    $page['avatar_url'] = $avatar;
    $page['avatar_visible'] = true;
    $page['js_vars']['upload'] = 'false';
  }
  # Is it an uploaded avatar?
  elseif(preg_match('/^uploaded-(?:bmp|gif|png|jpg|tif)$/i', $avatar))
  {
    $ext = preg_replace('/^uploaded-(bmp|gif|png|jpg|tif)$/i', '$1', $avatar);
    $page['avatar_image'] = $base_url. '/index.php?action=avatar;u='. $page['member_id']. ';ext=.'. $ext;
    $page['avatar_radio'] = 'upload';
    $page['avatar_collection'] = '';
    $page['avatar_url'] = 'http://';
    $page['avatar_visible'] = true;
    $page['js_vars']['upload'] = 'true';
  }
  # Is it empty?
  elseif(!$avatar)
  {
    $page['avatar_image'] = '';
    $page['avatar_radio'] = 'collection';
    $page['avatar_collection'] = 'none';
    $page['avatar_url'] = 'http://';
    $page['avatar_visible'] = false;
    $page['js_vars']['upload'] = 'false';
  }
  # Okay, it must be from the collection then
  else
  {
    $page['avatar_image'] = $base_url. '/index.php?action=avatar;collection='. $avatar;
    $page['avatar_radio'] = 'collection';
    $page['avatar_collection'] = $avatar;
    $page['avatar_url'] = 'http://';
    $page['avatar_visible'] = true;
    $page['js_vars']['upload'] = 'false';
  }
  
  # Get avatars in collection
  $page['collection'] = array();
  foreach(scandir($avatar_dir. '/collection') as $avatar)
    # Ignore everything that starts with an extension (Including . and ..), and ignore PHP files too.
    if(mb_substr($avatar, 0, 1) != '.' && mb_substr($avatar, -4) != '.php')
      $page['collection'][] = $avatar;
  
  # Set the appropriate JavaScript to load.
  $page['js_vars']['avatar_image'] = $page['avatar_image'];
  $page['scripts'][] = $theme_url. '/default/js/profile.js';
  
  # The submit URL
  $page['submit_url'] = $base_url. '/index.php?action=profile;sa=settings;save';
  
  # Title and theme
  $page['title'] = $l['profile_settings_title'];
  
  theme_load('profile', 'profile_settings_show');
}

function profile_preferences()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $theme_dir, $avatar_dir, $user;
  
  # Logged in users only
  if(!$user['is_logged'])
    error_screen();
  
  # Get the member.
  $member_id = !empty($_GET['u']) ? (int)$_GET['u'] : $user['id'];
  
  # Check if the member is the one logged in.
  $me = $user['id'] == $member_id;
  
  # If they are editing someone else's profile, we need to screen it.
  if(!$me)
    error_screen('edit_profile_any');
  # Okay, well are they allowed to edit their own profile.
  else
    error_screen('edit_profile');
  
  # Get the language
  language_load('profile');
  
  # No errors yet
  $page['errors'] = array();
  
  # Get the themes
  $themes = array();
  foreach(theme_list() as $key => $ini)
  {
    $themes[$key] = $ini['name'];
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
  $view_preferences = array(
    array(
      'variable' => 'theme',
      'type' => 'select',
      'options' => $themes,
      'show' => $settings['change_theme'],
    ),
    array(
      'type' => 'separator',
      'show' => $settings['change_theme'],
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
                     2 => $l['profile_preferences_value_auto_detect'],
                     0 => $l['profile_preferences_value_off'],
                     1 => $l['profile_preferences_value_on'],
                   ),
    ),
    array(
      'variable' => 'format_datetime',
      'type' => 'select',
      'max' => 75,
      'min' => 2,
      'subtext' => true,
      # Values are input backwards so that the default time and date format will override the others, if applicable
      'options' => array_reverse(array(
                     'YYYY-MM-DD H:mm:ss' => calculate_time('YYYY-MM-DD H:mm:ss', time_utc() + $user['timezone'] * 3600),
                     'DD/MM/YYYY, h:mm:ss P' => calculate_time('DD/MM/YYYY, h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     'MM/DD/YYYY, h:mm:ss P' => calculate_time('MM/DD/YYYY, h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     'D MMMM YYYY, h:mm:ss P' => calculate_time('D MMMM YYYY, h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     'MMMM D, YYYY, h:mm:ss P' => calculate_time('MMMM D, YYYY, h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     $settings['format_datetime'] => calculate_time($settings['format_datetime'], time_utc() + $user['timezone'] * 3600). ' (Default)',
                   )),
      'other' => true,
      'popup' => true,
    ),
    array(
      'variable' => 'format_date',
      'type' => 'select',
      'max' => 75,
      'min' => 2,
      'subtext' => true,
      # Values are input backwards so that the default date format will override the others, if applicable
      'options' => array_reverse(array(
                     'YYYY-MM-DD' => calculate_time('YYYY-MM-DD', time_utc() + $user['timezone'] * 3600),
                     'DD/MM/YYYY' => calculate_time('DD/MM/YYYY', time_utc() + $user['timezone'] * 3600),
                     'MM/DD/YYYY' => calculate_time('MM/DD/YYYY', time_utc() + $user['timezone'] * 3600),
                     'D MMMM YYYY' => calculate_time('D MMMM YYYY', time_utc() + $user['timezone'] * 3600),
                     'MMMM D, YYYY' => calculate_time('MMMM D, YYYY', time_utc() + $user['timezone'] * 3600),
                     $settings['format_date'] => calculate_time($settings['format_date'], time_utc() + $user['timezone'] * 3600). ' (Default)',
                   )),
      'other' => true,
      'popup' => true,
    ),
    array(
      'variable' => 'format_time',
      'type' => 'select',
      'max' => 75,
      'min' => 2,
      'subtext' => true,
      # Values are input backwards so that the default time format will override the others, if applicable
      'options' => array_reverse(array(
                     'HH:mm:ss' => calculate_time('HH:mm:ss', time_utc() + $user['timezone'] * 3600),
                     'h:mm:ss p' => calculate_time('h:mm:ss p', time_utc() + $user['timezone'] * 3600),
                     'h:mm:ss P' => calculate_time('h:mm:ss P', time_utc() + $user['timezone'] * 3600),
                     $settings['format_time'] => calculate_time($settings['format_time'], time_utc() + $user['timezone'] * 3600). ' (Default)',
                   )),
      'other' => true,
      'popup' => true,
    ),
    array(
      'variable' => 'preference_today_yesterday',
      'type' => 'select',
      'subtext' => true,
      'options' => array(
                     2 => $l['profile_preferences_value_today_yesterday'],
                     1 => $l['profile_preferences_value_today'],
                     0 => $l['profile_preferences_value_disabled'],
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
                     $l['profile_preferences_value_list'],
                     $l['profile_preferences_value_threaded'],
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
  
  # Saving?
  if(isset($_POST['process']))
  {
    # Use our cool settings_save() function
    settings_save($view_preferences, 'profile_update');
    
    # Redirect
    redirect('index.php?action=profile;sa=preferences;saved');
  }
  
  # Get the member's account settings
  $result = $db->query("
    SELECT
      displayName, theme, timezone, dst, format_datetime, format_date, format_time, preference_quick_reply, preference_avatars,
      preference_signatures, preference_post_images, preference_emoticons, preference_return_topic, preference_pm_display, preference_recently_online,
      preference_thousands_separator, preference_decimal_point, preference_today_yesterday, per_page_topics, per_page_posts, per_page_news, per_page_downloads,
      per_page_comments, per_page_members
    FROM {$db->prefix}members
    WHERE member_id = %member_id
    LIMIT 1",
    array(
      'member_id' => array('int', $member_id),
    ));
  $member = $db->fetch_assoc($result);
  
  # Get ready to display our settings
  $page['settings'] = settings_prepare($view_preferences, $member, 'profile_preferences_');
  
  # Whether or not settings have just been saved
  $page['saved'] = isset($_GET['saved']);
  
  # Member's ID and display name
  $page['member_id'] = $member_id;
  $page['member_name'] = $member['displayName'];
  
  # The submit URL
  $page['submit_url'] = $base_url. '/index.php?action=profile;sa=preferences;save';
  
  # Title and theme
  $page['title'] = $l['profile_preferences_title'];
  
  theme_load('profile', 'profile_preferences_show');
}

function profile_validate_email($email)
{
  global $source_dir, $db, $user, $l;
  
  # register.php contains register_validate_mail(), required to validate the email address
  require_once($source_dir. '/register.php');
  
  # Check if the email address is valid
  if(!register_validate_email($email['value']))
  {
    # register_validate_mail() says it is invalid if the email address
    # is already in use, even if by this member, so let's make sure the
    # email address isn't actually being used by this member
    $result = $db->query("
      SELECT
        email
      FROM {$db->prefix}members
      WHERE member_id = %member_id
      LIMIT 1",
      array(
        'member_id' => array('int', $member_id),
      ));
    list($current_email) = $db->fetch_row($result);
    
    if($email['value'] != $current_email)
      return $l['profile_account_setting_email_error'];
    else
      return '';
  }
  else
    return '';
}

function profile_password_verify($password)
{
  global $l;
  
  # Verify the password
  if($password['value'] != (isset($_POST['vpasswrd']) ? $_POST['vpasswrd'] : ''))
    return $l['profile_account_setting_vpasswrd_error'];
  else
    return '';
}

function profile_password_validate($password)
{
  global $user, $l;
  
  # Check that they entered the correct password
  if(sha1($password['value']) != $user['password'])
    return $l['profile_account_setting_cpasswrd_error'];
  else
    return '';
}

function profile_avatar_validate(&$avatar)
{
  global $base_url, $theme_url, $db, $l, $page, $settings, $source_dir, $theme_dir, $avatar_dir, $user;
  
  # No upload error yet
  $upload_error = '';
  
  # Get the member ID.
  $member_id = $avatar['member_id'];
  
  # Time to save the avatar!
  # Get the data.
  $avatar_radio = isset($_POST['avatar']) ? $_POST['avatar'] : '';
  $avatar_collection = isset($_POST['collection']) ? $_POST['collection'] : '';
  # Add http:// is the avatar URL doesn't use HTTP or HTTPS.
  if(!empty($_POST['url']))
    $avatar_url = mb_substr($_POST['url'], 0, 7) == 'http://' || mb_substr($_POST['url'], 0, 7) == 'https://' ? $_POST['url'] : 'http://'. $_POST['url'];
  else
    $avatar_url = '';
  
  # Collection or URL?
  if($avatar_radio == 'collection' || $avatar_radio == 'url')
  {
    $avatar['value'] = $avatar_radio == 'collection' ? $avatar_collection : $avatar_url;
    return '';
  }
  # Uploading then
  else
  {
    # Get the allowed file types
    $file_types = explode(',', $settings['avatar_filetypes']);
    
    # Array of MIME types to extensions ready to be filled
    $types = array();
    # Array of MIME types to primary MIME types ready to be filled
    $mime_primaries = array();
    
    # Check if bitmaps are allowed
    if(in_array('bmp', $file_types))
      $types[IMAGETYPE_BMP] = 'bmp';
    
    # Check if GIFs are allowed
    if(in_array('gif', $file_types))
      $types[IMAGETYPE_GIF] = 'gif';
    
    # Check if PNGs are allowed
    if(in_array('png', $file_types))
      $types[IMAGETYPE_PNG] = 'png';
    
    # Check if JPEGs are allowed
    if(in_array('jpg', $file_types))
      $types[IMAGETYPE_JPEG] = 'jpg';
    
    # Check if TIFFs are allowed
    if(in_array('tif', $file_types))
    {
      $types[IMAGETYPE_TIFF_II] = 'tif';
      $types[IMAGETYPE_TIFF_MM] = 'tif';
    }
    
    # Get the first extension, we'll use this as the default temporary extension
    list(, $default_extension) = each($types);
    
    # Make sure there weren't any random errors
    if($_FILES['file']['error'])
      $upload_error = $l['profile_settings_error_unknown'];
    # Make sure the file isn't too large
    elseif($_FILES['file']['size'] >= $settings['avatar_filesize'] * 1000)
      $upload_error = $l['profile_settings_error_filesize'];
    
    # Get the maximum allowed width and height for avatars.
    list($width, $height) = explode('x', $settings['avatar_size']);
    
    # Only continue the uploading process if there were no errors yet
    if(!$upload_error)
    {
      # Get where we want to save the file (Minus the file extension)
      $target = $avatar_dir. '/avatar-'. ($member_id);
      
      # Move the file from its temporary location to where it will reside long term
      if(move_uploaded_file($_FILES['file']['tmp_name'], $target. '.tmp.'. $default_extension))
      {
        # Get image's data with the GD library
        $image = getimagesize($target. '.tmp.'. $default_extension);
        
        # Check that the image is valid
        if(!in_array($image[2], array_keys($types)))
        {
          # Get the language representations of the types
          $l_types = array();
          foreach($types as $type)
            $l_types[] = $l['profile_settings_avatar_'. $type];
          
          # Invalid image!
          $upload_error = sprintf($l['profile_settings_error_type'], create_list($l_types));
          # Delete the avatar
          unlink($target. '.tmp.'. $default_extension);
        }
        # Make sure the image size isn't too large
        elseif($image[0] > $width || $image[1] > $height)
        {
          # Image is too large!
          $upload_error = sprintf($l['profile_settings_error_imagesize'], $width, $height);
          # Delete the avatar
          unlink($target. '.tmp.'. $default_extension);
        }
        else
        {
          # Still no errors, yay!
          # Delete any old avatars from this member
          foreach($types as $extension)
            if(is_writable($target. '.'. $extension))
              unlink($target. '.'. $extension);
          
          # Rename the image to the approriate file extension
          rename($target. '.tmp.'. $default_extension, $target. '.'. $types[$image[2]]);
          
          $avaar['value'] = 'uploaded-'. $types[$image[2]];
        }
      }
      # Moving failed? Error!
      else
        $upload_error = $l['profile_settings_error_unknown'];
      
      # If there was an error, we need to update the database to
      # reflect the fact that the old avatar is now deleted
      if($upload_error)
      {
        $avatar['value'] = '';
        return $upload_error;
      }
    }
  }
  
  return '';
}

function profile_update($fields)
{
  global $db, $user, $source_dir;
  
  # Get the member.
  $member_id = !empty($_GET['u']) ? (int)$_GET['u'] : $user['id'];
  
  # Let's see... Did you even give us any..?
  if(count($fields))
  {
    # Okay good :)
    # This array holds the variable and value, which we implode
    $new_settings = array();

    # Keep track...
    $i = 0;

    # Hold our values :D
    $values = array();

    # Loop through and set them all up :)
    foreach($fields as $setting => $value)
    {
      # If it's the password, hash it
      if($setting == 'passwrd')
      {
        # If the password field was left blank, we'll just skip this
        if($value)
        {
          # Not blank? Get on with it then
          $value = sha1($value);
          
          # Add the password into the array
          $new_settings[] = "$setting = %value_". mb_substr('00'. $i, -3);
          
          # login.php contains login_cookie()
          require_once($source_dir. '/login.php');
          
          # We need to update the cookies with the new password, to keep them logged in
          login_cookie($user['id'], $value);
          
          # And update the session oassword too
          $_SESSION['passwrd'] = $value;
        }
      }
      # If it's the site URL, we'll need to sanitize it
      elseif($setting == 'site_url')
      {
        # If the value is just http://, then it shouldn't be anything at all
        if(!$value || $value == 'http://')
          $value = '';
        # If the protocol isn't HTTP or HTTPS, we'll add http://
        elseif(!preg_match('/https?:\/\//is', $value))
          $value = 'http://'. $value;
        
        # Add the site URL into the array
        $new_settings[] = "$setting = %value_". mb_substr('00'. $i, -3);
      }
      else
      {
        # Add the setting into the array
        $new_settings[] = "$setting = %value_". mb_substr('00'. $i, -3);
      }

      $values['value_'. mb_substr('00'. $i, -3)] = array('string', $value);
      
      # Update counter
      $i++;
    }

    # Put all the settings toGet ther
    $fields = implode(', ', $new_settings);

    # One more thing!
    $values['member_id'] = array('int', $member_id);
    
    # Now update/insert them :)
    $db->query("
      UPDATE {$db->prefix}members
      SET $fields
      WHERE member_id = %member_id
      LIMIT 1",
      $values);
  }
}

function profile_birthday_convert($date, $what = 'age')
{
  global $l;
  
  # How old you are and outputting your birthdate oh, and if it's your birthday!... That is all :P
  $what = mb_strtolower($what);
  if(!in_array($what, array('age', 'birthdate', 'is_birthday')) || empty($date))
    return false;

  # :) Wootness for list!
  @list($year, $month, $day) = @explode('-', $date);

  # They must be integers! Silly.
  $year = (int)$year;
  $month = (int)$month;
  $day = (int)$day;

  # No BS either...
  if($year > date('Y') || ($year == date('Y') && $month > date('n')) || ($year == date('Y') && $month == date('n') && $day > date('j')))
    return false;

  if($what == 'age')
  {
    # So you want an age but you have no year..? Pffft. I don't read minds!
    if($year == 0)
      return false;
    # No specific day..? Fine. We will just assume you were born on New Years! Aren't you special..?
    elseif($day == 0 && $month == 0)
      return date('Y') - $year;
    elseif($year == date('Y'))
      # Dang! Your one smart baby! O_O.
      return 0;

    # Pad with zeros, perhaps?
    if($year < 1000)
    {
      $new_year = '';
      for($i = 0; $i < (4 - strlen((string)$year)); $i++)
        $new_year .= '0';
      $new_year .= (string)$year;
      $year = $new_year;
    }

    # No specific day but a month..? What is WRONG with you people!?!
    if($day == 0)
      $day = 1;

    # Pad..?
    if($day < 10)
      $day = (string)('0'. $day);

    # If you are giving us full details, we need it to be true...
    if(!checkdate((int)$month, (int)$day, (int)$year))
      return false;

    # Month needs love too!
    if($month < 10)
      $month = (string)('0'. $month);

    # Now the hard part... WHY?
    # A little funky... Yes, but heh.
    $is_leap = create_function('$year', '
      return checkdate(2, 29, (int)$year);');

    # Your birth year, was it leap..?
    if($is_leap($year))
      $days = 366;
    else
      $days = 365;

    # How many days did you not actually live in your birth year?
    $then = @getdate(strtotime($year. '-'. $month. '-'. $day));

    # Subtract those days... Minus 1 because you lived on THAT day! (You thought I wouldn't think of that, didn't you? :P)
    $days -= $then['yday'] - 1;

    # Right now though you are (CURRENT_YEAR - YEAR_BORN) - 1 years old...
    $age = (date('Y') - (int)$year) - 1;

    # Now the current year information... :)
    $now = @getdate(strtotime(date('Y-m-d')));

    # Days that have gone by..?
    $days += $now['yday'] - 1;

    # So are you +1 of age?
    if(($is_leap(date('Y')) && $days >= 366) || (!$is_leap(date('Y')) && $days >= 365))
      $age++;

    # The thinking! It's OVER! YES!
    return $age;
  }
  elseif($what == 'birthdate')
  {
    # Pad with zeros, perhaps?
    if($year < 1000)
    {
      $new_year = '';
      for($i = 0; $i < (4 - strlen((string)$year)); $i++)
        $new_year .= '0';
      $new_year .= (string)$year;
      $year = $new_year;
    }

    # Just a year..? Ugh.
    if(($day == 0  && $month == 0) || $month == 0)
    {
      # Just the year, sorry.
      return calculate_time('YYYY', strtotime($year. '-01-01'));
    }

    # Make sure you aren't lying! :P
    if((!empty($day) && (int)$year != 0) || $month > 12 || $month < 0)
      if(!checkdate((int)$month, (int)$day, (int)$year))
        return false;

    if($year == '0000')
    {
      $year = null;

      # Make sure your day isn't, well, stupid :P (We will use a static year... A leap one)
      if($day > @cal_days_in_month(CAL_GREGORIAN, $month, 1992))
        return false;
    }
    
    # Now we can begin to form our date...
    if($year)
      return timeformat(strtotime($year. '-'. $month. '-'. $day), 1);
    else
    {
      $time = getdate(strtotime('1992-'. $month. '-'. $day));
      return sprintf($l['birthdate_daymonth'], $l['th_'. $time['mday']], $l['month_long_'. $time['mon']]);
    }
  }
  else
    # So, are you a year older? :)!
    return $day == date('s') && $month == date('n');
}
?>