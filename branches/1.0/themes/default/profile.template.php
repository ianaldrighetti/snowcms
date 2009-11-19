<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#      Page Layout template, January 16, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function profile_member_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Avatar displaying
  if($page['member']['avatar'])
  {
    echo '
       <div class="profile-avatar"><img src="', $page['member']['avatar'], '" alt="', sprintf($l['profile_avatar'], $page['member']['username']), '" /></div>';
  }
  
  echo '
       <h1>', $page['member']['username'], '</h1>
       <p>', $page['member']['group']['name'], '</p>';
  
  if($page['member']['group']['stars']['amount'])
    echo '
       ';
  for($i = 0; $i < $page['member']['group']['stars']['amount']; $i += 1)
    echo '<img src="', $theme_url, '/', $settings['theme'], '/images/', $page['member']['group']['stars']['image'], '" alt="', $page['member']['group']['name'], '" />';
  
  # Viewing their own profile?
  if($user['id'] == $page['member']['id'])
  {
    echo '
       <table>
         <tr>
           <td><a href="', $base_url, '/index.php?action=profile">', $l['profile_profile'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=account">', $l['profile_account'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=settings">', $l['profile_settings'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=preferences">', $l['profile_preferences'], '</a></td>
         </tr>
       </table>';
  }
  # Moderator viewing someone else's profile?
  elseif(can('moderate_members'))
  {
    echo '
       <table>
         <tr>
           <td><a href="', $base_url, '/index.php?action=profile;u=', $page['member']['id'], '">', $l['profile_profile'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=account;u=', $page['member']['id'], '">', $l['profile_account'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=settings;u=', $page['member']['id'], '">', $l['profile_settings'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=preferences;u=', $page['member']['id'], '">', $l['profile_preferences'], '</a></td>
         </tr>
       </table>';
  }
  # Member viewing someone else's profile
  else
  {
    echo '
         <br /><br />';
  }
  
  echo '
       <div style="clear: both;"></div>
       <table class="vtable" style="width: 50%; float: left;">
         <colgroup span="1" style="width: 115px;"></colgroup>
         <tr><th>', $l['profile_date_registered'], '</th><td>', $page['member']['reg_time'], '</td></tr>
         <tr><th>', $l['profile_last_visit'], '</th><td>', $page['member']['last_online'], '</td></tr>
         <tr><th>', $l['profile_posts'], '</th><td>', numberformat($page['member']['posts']), '</td></tr>
         <tr><th>', $l['profile_time_online'], '</th><td>', $page['member']['time_online'], '</td></tr>
         <tr><td colspan="2"><br /></td></tr>
         <tr><th>', $l['profile_email'], '</th><td>', $page['member']['email'], '</td></tr>
         <tr><th>', $l['profile_pm'], '</th><td><a href="', $base_url, '/index.php?action=pm;sa=compose;to=', $page['member']['id'], '">', sprintf($l['profile_send_pm'], $page['member']['username']), '</a></td></tr>
         <tr><th>', $l['profile_msn'], '</th><td>', $page['member']['msn'], '</td></tr>
         <tr><th>', $l['profile_aim'], '</th><td>', $page['member']['aim'], '</td></tr>
         <tr><th>', $l['profile_yim'], '</th><td>', $page['member']['yim'], '</td></tr>
         <tr><th>', $l['profile_gtalk'], '</th><td>', $page['member']['gtalk'], '</td></tr>
         <tr><th>', $l['profile_icq'], '</th><td>', $page['member']['icq'], '</td></tr>
       </table>
       <table class="vtable" style="width: 49%;">
         <colgroup span="1" style="width: 130px;"></colgroup>
         <tr><th>', $l['profile_age'], '</th><td>', $page['member']['age'], $page['member']['birthday'] ? ' <img src="'. $settings['images_url']. '/birthday.png" alt="'. $l['profile_happy_birthday']. '" title="'. $l['profile_happy_birthday']. '" />' : '', '</td></tr>
         <tr><th>', $l['profile_birthdate'], '</th><td>', $page['member']['birthdate'] ? $page['member']['birthdate'] : '', '</td></tr>
         <tr><th>', $l['profile_gender'], '</th><td>', ($page['member']['gender'] == 2 ? $l['profile_gender_male'] : ($page['member']['gender'] == 1 ? $l['profile_gender_female'] : '')), '</td></tr>
         <tr><th>', $l['profile_location'], '</th><td>', $page['member']['location'], '</td></tr>
         <tr><th>', $l['profile_timezone'], '</th><td>', $l['gmt'], $page['member']['timezone'], '</td></tr>
         <tr><th>', $l['profile_custom_title'], '</th><td>', $page['member']['custom_title'], '</td></tr>
         <tr><th>', $l['profile_membergroup'], '</th><td>', $page['member']['membergroup']['name'], '</td></tr>
         <tr><th>', $l['profile_post_group'], '</th><td>', $page['member']['post_group']['name'], '</td></tr>
         <tr><th>', $l['profile_local_time'], '</th><td>', $page['member']['local_time'], '</td></tr>';
  
  if(can('view_ips'))
  echo '
         <tr><th>', $l['profile_ip'], '</th><td><a href="', $base_url, '/index.php?action=iplookup;ip=', $page['member']['ip'], '">', $page['member']['ip'], '</a> <span style="font-size: smaller; vertical-align: top;">[<a href="', $base_url, '/index.php?action=profile;sa=ip', ($user['id'] != $page['member']['id'] ? ';u='. $page['member']['id'] : ''), '">All</a>]</span></td></tr>';
  
  echo '
         <tr><th>', $l['profile_website'], '</th><td>', $page['member']['site_url'] ? '<a href="'. $page['member']['site_url']. '">'. $page['member']['site_name']. '</a>' : '', '</td></tr>
       </table>';
  
  if($page['member']['profile_text'])
    echo '
       <div style="clear: both;">
       <br />
       ', $page['member']['profile_text'], '
       </div>';
}

function profile_member_show_invalid()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', $l['profile_invalid_header'], '</h1>
       <div class="generic_error">
         <p>', $l['profile_invalid_desc'], '</p>
       </div>
       ';
}

function profile_ip_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', sprintf($l['profile_ip_header'], $page['member']['username']), '</h1>
       <p>', sprintf($l['profile_ip_desc'], '<a href="'. $base_url. '/index.php?action=profile'. ($user['id'] != $page['member']['id'] ? ';u='. $page['member']['id'] : ''). '">'. $page['member']['username']. '</a>'), '</p>
       <br />
       <table class="htable">
         <tr><th>', $l['profile_ip_ip'], '</th><th>', $l['profile_ip_first_time'], '</th><th>', $l['profile_ip_last_time'], '</th></tr>';
  
  foreach($page['ips'] as $ip)
    echo '
         <tr><td><a href="', $base_url, '/index.php?action=iplookup;ip=', $ip['ip'], '">', $ip['ip'], '</a></td><td>', timeformat($ip['first_time']), '</td><td>', timeformat($ip['last_time']), '</td></tr>';
  
  echo '
       </table>';
}

function profile_ip_show_none()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', sprintf($l['profile_ip_none_header'], $page['member']['username']), '</h1>
       <p>', sprintf($l['profile_ip_none_desc'], '<a href="'. $base_url. '/index.php?action=profile'. ($user['id'] != $page['member']['id'] ? ';u='. $page['member']['id'] : ''). '">'. $page['member']['username']. '</a>'), '</p>';
}

function profile_account_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', $l['profile_account_header'], '</h1>
       <p>', $user['id'] == $page['member_id'] ? $l['profile_account_your_desc'] : sprintf($l['profile_account_desc'], '<b>'. $page['member_name']. '</b>'), '</p>
       ';
  
  # Viewing their own profile?
  if($user['id'] == $page['member_id'])
  {
    echo '
       <table>
         <tr>
           <td><a href="', $base_url, '/index.php?action=profile">', $l['profile_profile'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=account">', $l['profile_account'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=settings">', $l['profile_settings'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=preferences">', $l['profile_preferences'], '</a></td>
         </tr>
       </table>
       ';
  }
  # Moderator viewing someone else's profile?
  elseif(can('moderate_members'))
  {
    echo '
       <table>
         <tr>
           <td><a href="', $base_url, '/index.php?action=profile;u=', $page['member_id'], '">', $l['profile_profile'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=account;u=', $page['member_id'], '">', $l['profile_account'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=settings;u=', $page['member_id'], '">', $l['profile_settings'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=preferences;u=', $page['member_id'], '">', $l['profile_preferences'], '</a></td>
         </tr>
       </table>
       ';
  }
  
  if($page['saved'])
    echo '
       <div class="generic_success">
         <p>', $l['profile_account_saved'], '</p>
       </div>
       ';
  # Show any errors
  elseif($page['errors'])
  {
    echo '
      <div class="generic_error">
      ';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  echo '
       <fieldset>
        <form action="" method="post">
          <input type="hidden" name="process" value="true" />
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">';

  # Loop through all the settings
  foreach($page['settings'] as $setting)
  {
    # Only if it isn't a separator
    if($setting['type'] != 'separator')
    {
      echo '
            <tr class="setting_container">
              <td width="16">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=adminhelp;var='. $setting['name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td valign="top"><label for="', $setting['name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '<br /><span class="small subtext">'. $setting['subtext']. '</span>' : '', '</td>
              <td width="50%">', $setting['input'], '</td>
            </tr>';
    }
    else
    {
      # Okay, then it's a separator, so display a... well, separator
      echo '
           <tr><td class="separator" colspan="3"></td></tr>
           ';
    }
  }

  echo '
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" name="save" value="Save" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}

function profile_settings_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', $l['profile_settings_header'], '</h1>
       <p>', $user['id'] == $page['member_id'] ? $l['profile_settings_your_desc'] : sprintf($l['profile_settings_desc'], '<b>'. $page['member_name']. '</b>'), '</p>';
  
  # Viewing their own profile?
  if($user['id'] == $page['member_id'])
  {
    echo '
       <table>
         <tr>
           <td><a href="', $base_url, '/index.php?action=profile">', $l['profile_profile'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=account">', $l['profile_account'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=settings">', $l['profile_settings'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=preferences">', $l['profile_preferences'], '</a></td>
         </tr>
       </table>';
  }
  # Moderator viewing someone else's profile?
  elseif(can('moderate_members'))
  {
    echo '
       <table>
         <tr>
           <td><a href="', $base_url, '/index.php?action=profile;u=', $page['member_id'], '">', $l['profile_profile'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=account;u=', $page['member_id'], '">', $l['profile_account'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=settings;u=', $page['member_id'], '">', $l['profile_settings'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=preferences;u=', $page['member_id'], '">', $l['profile_preferences'], '</a></td>
         </tr>
       </table>';
  }
  
  if($page['saved'])
    echo '
       <div class="generic_success">
         <p>', $l['profile_settings_saved'], '</p>
       </div>';
  # Show any errors
  elseif($page['errors'])
  {
    echo '
      <div class="generic_error">';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  # Loop through all the settings
  foreach($page['settings'] as $setting)
  {
    # Avatar?
    if(in_array('avatar', $setting['tags']))
    {
      echo '
       <br />
       <img id="avatar_preview" src="', $page['avatar_image'], '" alt="', $l['profile_settings_avatar_preview'], '" title="', $l['profile_settings_avatar_preview'], '" style="float: right; padding-right: 10px;', !$page['avatar_visible'] ? ' visibility: hidden;' : '', '" />
       <form action="" method="post" enctype="multipart/form-data">
         <input type="hidden" name="process" value="true" />
         <div style="width: 527px; padding-left: 36px;">
           <div>
             <input type="radio" name="avatar" id="avatar_collection" value="collection"', ($page['avatar_radio'] == 'collection' ? ' checked="checked"' : ''), ' onfocus="collection_change();" />
             <label for="avatar_collection">
               ', $l['profile_settings_avatar_collection'], '
               <select name="collection" id="collection" size="8" style="float: right; width: 155px; font-size: x-small;" onclick="collection_click();" onchange="collection_change();" onkeyup="collection_change();">
                 <option value=""', ($page['avatar_collection'] == 'none' ? ' selected="selected"' : ''), '>', $l['profile_settings_avatar_none'], '</option>';
      
      foreach($page['collection'] as $avatar)
      {
        if($page['avatar_collection'] == $avatar)
          echo '
                   <option selected="selected">', $avatar, '</option>';
        else
          echo '
                   <option>', $avatar, '</option>';
      }
      
      echo '
               </select>
             </label>
           </div>
           <br />
           <div>
             <input type="radio" name="avatar" id="avatar-url" value="url"', ($page['avatar_radio'] == 'url' ? ' checked="checked"' : ''), ' onfocus="url_change();" />
             <label for="avatar-url">
               ', $l['profile_settings_avatar_url'], '
               <div style="padding-left: 20px;"><input type="text" name="url" id="url" value="', $page['avatar_url'], '" onclick="url_click();" onchange="url_change();" onkeyup="url_change();" /></div>
             </label>
           </div>
           <br />
           <div>
             <input type="radio" name="avatar" id="avatar_upload" value="upload"', ($page['avatar_radio'] == 'upload' ? ' checked="checked"' : ''), ' onfocus="upload_change();" />
             <label for="avatar_upload">
               ', $l['profile_settings_avatar_upload'], '
               <div style="padding-left: 20px;">
                 <input type="hidden" name="MAX_FILE_SIZE" value="', $settings['avatar_filesize'] * 1000, '" />
                 <input type="file" name="file" value="" onclick="upload_click();" />
               </div>
             </label>
           </div>
         </div>
        <fieldset style="clear: both;">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">';
    }
    # Only if it isn't a separator
    elseif($setting['type'] != 'separator')
      echo '
            <tr class="setting_container">
              <td width="16">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=adminhelp;var='. $setting['name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td valign="top"><label for="', $setting['name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '<br /><span class="small subtext">'. $setting['subtext']. '</span>' : '', '</td>
              <td width="50%">', $setting['input'], '</td>
            </tr>';
    # Okay, then it's a separator, so display a... well, separator
    else
      echo '
           <tr><td class="separator" colspan="3"></td></tr>';
  }

  echo '
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" name="save" value="Save" /></td>
            </tr>
          </table>
        </fieldset>
      </form>';
}

function profile_preferences_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', $l['profile_preferences_header'], '</h1>
       <p>', $user['id'] == $page['member_id'] ? $l['profile_preferences_your_desc'] : sprintf($l['profile_preferences_desc'], '<b>'. $page['member_name']. '</b>'), '</p>';
  
  # Viewing their own profile?
  if($user['id'] == $page['member_id'])
  {
    echo '
       <table>
         <tr>
           <td><a href="', $base_url, '/index.php?action=profile">', $l['profile_profile'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=account">', $l['profile_account'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=settings">', $l['profile_settings'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=preferences">', $l['profile_preferences'], '</a></td>
         </tr>
       </table>';
  }
  # Moderator viewing someone else's profile?
  elseif(can('moderate_members'))
  {
    echo '
       <table>
         <tr>
           <td><a href="', $base_url, '/index.php?action=profile;u=', $page['member_id'], '">', $l['profile_profile'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=account;u=', $page['member_id'], '">', $l['profile_account'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=settings;u=', $page['member_id'], '">', $l['profile_settings'], '</a></td>
           <td style="text-align: center; width: 20px;">-</td>
           <td><a href="', $base_url, '/index.php?action=profile;sa=preferences;u=', $page['member_id'], '">', $l['profile_preferences'], '</a></td>
         </tr>
       </table>';
  }
  
  if($page['saved'])
    echo '
       <div class="generic_success">
         <p>', $l['profile_preferences_saved'], '</p>
       </div>';
  # Show any errors
  elseif($page['errors'])
  {
    echo '
      <div class="generic_error">
      ';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  
  echo '
       <fieldset>
        <form action="" method="post">
          <input type="hidden" name="process" value="true" />
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">';

  # Loop through all the settings
  foreach($page['settings'] as $setting)
  {
    # Only if it isn't a separator
    if($setting['type'] != 'separator')
    {
      echo '
            <tr class="setting_container">
              <td width="16">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=help;var='. $setting['name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td valign="top"><label for="', $setting['name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '<br /><span class="small subtext">'. $setting['subtext']. '</span>' : '', '</td>
              <td width="50%">', $setting['input'], '</td>
            </tr>';
    }
    else
    {
      # Okay, then it's a separator, so display a... well, separator
      echo '
           <tr><td class="separator" colspan="3"></td></tr>
           ';
    }
  }

  echo '
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" name="save" value="Save" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}
?>