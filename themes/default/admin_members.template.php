<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Settings Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function members_manage_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  $sort_asc = ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_asc.png" alt="Sorted Ascending" />';
  $sort_desc = ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_desc.png" alt="Sorted Descending" />';
  
  $sort_id = $page['sort'] == 'id' ? $sort_asc : ($page['sort'] == 'id;desc' ? $sort_desc : '');
  $sort_username = $page['sort'] == 'username' ? $sort_asc : ($page['sort'] == 'username;desc' ? $sort_desc : '');
  $sort_email = $page['sort'] == 'email' ? $sort_asc : ($page['sort'] == 'email;desc' ? $sort_desc : '');
  $sort_ip = $page['sort'] == 'ip' ? $sort_asc : ($page['sort'] == 'ip;desc' ? $sort_desc : '');
  $sort_posts = $page['sort'] == 'posts' ? $sort_asc : ($page['sort'] == 'posts;desc' ? $sort_desc : '');
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['admin_members_list_header'], '</h1>
      <p>', $l['admin_members_list_desc'], '</p>
      <br />
      <p>'. $page['pagination']. '</p>
      <br />
      <table class="htable">
        <tr>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=members;area=list;sort=id'. ($page['sort'] == 'id' ? ';desc' : ''). '">', $l['admin_members_list_id'], '</a>'. $sort_id. '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=members;area=list;sort=username'. ($page['sort'] == 'username' ? ';desc' : ''). '">', $l['admin_members_list_username'], '</a>'. $sort_username. '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=members;area=list;sort=email'. ($page['sort'] == 'email' ? ';desc' : ''). '">', $l['admin_members_list_email'], '</a>'. $sort_email. '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=members;area=list;sort=ip'. ($page['sort'] == 'ip' ? ';desc' : ''). '">', $l['admin_members_list_ip'], '</a>'. $sort_ip. '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=members;area=list;sort=posts'. ($page['sort'] == 'posts' ? ';desc' : ''). '">', $l['admin_members_list_posts'], '</a>'. $sort_posts. '</th>
        </tr>';
  
  # Echo the members
  foreach($page['members'] as $member)
    echo '
        <tr>
          <td>'. numberformat($member['member_id']). '</td>
          <td><a href="'. $base_url. '/index.php?action=profile;u='. $member['member_id']. '">'. $member['displayName']. '</a></td>
          <td><a href="mailto:'. $member['email']. '">'. $member['email']. '</a></td>
          <td>'. $member['last_ip']. '</td>
          <td>'. numberformat($member['num_posts']). '</td>
        </tr>';
  
  # Echo the footer stuff
  echo '
      </table>
      <br />
      <p>'. $page['pagination']. '</p>';
}

function members_register_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title and etc.
  echo '
      <h1>', $l['admin_members_register_header'], '</h1>
      <p>', $l['admin_members_register_desc'], '</p>
      ';
  
  # Tell them if the member was registered successfully
  if($page['registration_success'])
    echo '
      <div class="generic_success">
        <p>', $l['admin_members_register_success'], '</p>
      </div>
      ';
  # Display an error if applicable
  elseif($page['errors']) {
    echo '
      <div class="generic_error">';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>
      ';
  }
  
  # Echo the top of the form
  echo '
      <fieldset>
        <form action="'. $base_url. '/index.php?action=admin;sa=members;area=register;process" method="post">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">
            <tr>
              <td style="width: 16px;"></td>
              <td>'. $l['admin_members_register_username']. '</td>
              <td style="width: 50%;"><input type="text" name="username" value="'. $page['username']. '" /></td>
            </tr>
            <tr>
              <td style="width: 16px;"></td>
              <td>'. $l['admin_members_register_password']. '</td>
              <td><input type="password" name="passwrd" /></td>
            </tr>
            <tr>
              <td style="width: 16px;"></td>
              <td>'. $l['admin_members_register_vpassword']. '</td>
              <td><input type="password" name="vPasswrd" /></td>
            </tr>
            <tr>
              <td style="width: 16px;"></td>
              <td>'. $l['admin_members_register_membergroup']. '</td>
              <td>
                <select name="membergroup">';
      
      # Echo the member groups in a list box
      foreach($page['membergroups'] as $membergroup)
        echo '
                  <option value="'. $membergroup['group_id']. '"'. ($page['membergroup'] == $membergroup['group_id'] ? ' selected="selected"' : ''). '>'. $membergroup['group_name']. '</option>';
      
      # Echo footer stuff
      echo '
                </select>
              </td>
            </tr>
            <tr>
              <td style="width: 16px;"></td>
              <td>'. $l['admin_members_register_email']. '</td>
              <td><input type="text" name="email" value="'. $page['email']. '" /></td>
            </tr>
          </table>
          
          <p><br /></p>
          
          <p style="text-align: center;"><input type="submit" value="Register" /></p>
        </form>
      </fieldset>';
}

function members_options_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the header and title, etc.
  echo '
      <h1>', $l['admin_members_registration_header'], '</h1>
      <p>', $l['admin_members_registration_desc'], '</p>

      <fieldset>
        <form action="', $page['submit_url'], '" method="post">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">';
  
  # Echo all the settings
  foreach($page['settings'] as $setting)
  {
    echo '
            <tr class="setting_container">
              <td style="width: 16px;">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=adminhelp;var='. $setting['safe_name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td style="vertical-align: top;"><label for="', $setting['safe_name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '<br /><span class="small subtext">'. $setting['subtext']. '</span>' : '', '</td>
              <td style="width: 50%;">', $setting['input'], '</td>
            </tr>';
  }
  
  # Echo the footer and stuff
  echo '
            <tr>
              <td colspan="3" style="text-align: center; vertical-align: middle;"><input type="submit" name="save" value="Save" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}
?>