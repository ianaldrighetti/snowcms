<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Settings Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function membergroups_add_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['admin_membergroups_add_header'], '</h1>
      <p>', $l['admin_membergroups_add_desc'], '</p>
      ';
  
  echo '
       <fieldset>
        <form action="', $page['submit_url'], '" method="post">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">';

  # Loop through all the settings
  foreach($page['settings'] as $setting)
  {
    # Only if it isn't a separator
    if($setting['type'] != 'separator')
    {
      echo '
            <tr class="setting_container">
              <td width="16">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=adminhelp;var='. $setting['safe_name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td valign="top"><label for="', $setting['safe_name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '<br /><span class="small subtext">'. $setting['subtext']. '</span>' : '', '</td>
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

function membergroups_manage_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['admin_membergroups_header'], '</h1>
      <p>', $l['admin_membergroups_desc'], '</p>
      
      <br />
      ';
  
  if($page['error'])
    echo '
      <div class="generic_error">
        <p>', $page['error'], '</p>
      </div>
      
      <br />
      ';
  
  echo '
      <table class="htable">
        <tr>
          <th>', $l['admin_membergroups_name_plural'], '</th>
          <th>', $l['admin_membergroups_name_singular'], '</th>
          <th>', $l['admin_membergroups_color'], '</th>
          <th>', $l['admin_membergroups_min_posts'], '</th>
          <th>', $l['admin_membergroups_stars'], '</th>
          <td></td>
        </tr>';
  
  foreach($page['membergroups'] as $group)
  {
    echo '
            <tr>
              <td id="group_name_plural_', $group['id'], '"><span onclick="editMembergroup(', $group['id'], ', ', $group['order'], ');">'. $group['name_plural']. '</span></td>
              <td id="group_name_singular_', $group['id'], '"><span onclick="editMembergroup(', $group['id'], ', ', $group['order'], ');">'. $group['name_singular']. '</span></td>
              <td id="group_color_', $group['id'], '"><div class="membergroup_color" onclick="editMembergroup(', $group['id'], ', ', $group['order'], ');" style="background: ', $group['color'] ? $group['color'] : 'transparent', ';"></div></td>
              <td id="group_min_posts_', $group['id'], '"><span onclick="editMembergroup(', $group['id'], ', ', $group['order'], ');">', $group['min_posts'] != -1 ? numberformat($group['min_posts']) : '', '</span></td>
              <td id="group_stars_', $group['id'], '"><span onclick="editMembergroup(', $group['id'], ', ', $group['order'], ');">';
    
    for($i = 0; $i < $group['stars']['amount']; $i += 1)
      echo '<img src="', $theme_url, '/', $settings['theme'], '/images/', $group['stars']['image'], '" alt="*" />';
    
    echo '</span></td>
              <td id="group_options_', $group['id'], '">
                <a href="javascript:void(0);" onclick="editMembergroup(', $group['id'], ', ', $group['order'], ');"><img src="'. $theme_url. '/'. $settings['theme']. '/images/edit.png" alt="', $l['admin_membergroups_edit'], '" title="', $l['admin_membergroups_edit'], '" /></a>
                <a href="', $base_url, '/index.php?action=admin;sa=members;area=membergroups;del=', $group['id'], '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/delete.png" alt="', $l['admin_membergroups_delete'], '" title="', $l['admin_membergroups_delete'], '" /></a>
              </td>
            </tr>';
  }
  
  # Echo the footer stuff
  echo '
    </table>';
}
?>