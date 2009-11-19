<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Settings Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function menus_add_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['admin_menus_add_header'], '</h1>
      <p>', $l['admin_menus_add_desc'], '</p>';
  
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
           <tr><td class="separator" colspan="3"></td></tr>';
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

function menus_manage_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['admin_menus_manage_header'], '</h1>
      <p>', $l['admin_menus_manage_desc'], '</p>
      
      <br />
      
      <table class="htable">';
  
  # Echo the menus
  foreach($page['menus'] as $menu_id => $menu)
  {
    echo '
      <tr><td colspan="4" style="text-align: left; font-weight: bold;">', sprintf($l['admin_menus_manage_menu'], numberformat($menu_id)), '</td></tr>';
    
    # Echo the links, if there are any
    if($menu)
    {
      echo '
        <tr>
          <th>', $l['admin_menus_manage_name'], '</th>
          <th>', $l['admin_menus_manage_url'], '</th>
          <th>', $l['admin_menus_manage_window'], '</th>
          <th>', $l['admin_menus_manage_follow'], '</th>
          <td></td>
        </tr>';
      
      foreach($menu as $link)
      {
        echo '
            <tr>
              <td id="link_name_', $link['link_id'], '"><span onclick="editLink(', $link['link_id'], ', ', $link['link_order'], ');">'. $link['link_name']. '</span></td>
              <td id="link_href_', $link['link_id'], '"><span onclick="editLink(', $link['link_id'], ', ', $link['link_order'], ');">'. $link['link_href']. '</span></td>
              <td id="link_target_', $link['link_id'], '"><span onclick="editLink(', $link['link_id'], ', ', $link['link_order'], ');">'. ($link['link_target'] ? $l['admin_menus_manage_window_new'] : $l['admin_menus_manage_window_same']). '</span></td>
              <td id="link_follow_', $link['link_id'], '"><span onclick="editLink(', $link['link_id'], ', ', $link['link_order'], ');">'. ($link['link_follow'] ? $l['admin_menus_manage_follow_yes'] : $l['admin_menus_manage_follow_no']). '</span></td>
              <td id="link_options_', $link['link_id'], '">
                <a href="javascript:void(0);" onclick="editLink(', $link['link_id'], ', ', $link['link_order'], ');"><img src="'. $theme_url. '/'. $settings['theme']. '/images/edit.png" alt="', $l['admin_menus_manage_edit'], '" title="', $l['admin_menus_manage_edit'], '" /></a>
                <a href="', $base_url, '/index.php?action=admin;sa=menus;area=manage;del=', $link['link_id'], '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/delete.png" alt="', $l['admin_menus_manage_delete'], '" title="', $l['admin_menus_manage_delete'], '" /></a>
                <a href="', $base_url, '/index.php?action=admin;sa=menus;area=manage;raise=', $link['link_id'], '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/order_raise.png" alt="', $l['admin_menus_manage_raise'], '" title="', $l['admin_menus_manage_raise'], '" /></a>
                <a href="', $base_url, '/index.php?action=admin;sa=menus;area=manage;lower=', $link['link_id'], '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/order_lower.png" alt="', $l['admin_menus_manage_lower'], '" title="', $l['admin_menus_manage_lower'], '" /></a>
              </td>
            </tr>';
      }
    }
    else
    {
      # No links? Let them know
      echo '<tr><td colspan="4" style="text-align: left; font-style: italic;">', $l['admin_menus_manage_no_links'], '</td></th>';
    }
  }
  
  # Echo the footer stuff
  echo '
    </table>';
}
?>