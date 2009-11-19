<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Settings Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function settings_core_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  # Show our title XD.
  echo '
      <h1>', !empty($page['settings_header']) ? $page['settings_header'] : $l['admin_settings_header'], '</h1>
      <p>', !empty($page['settings_desc']) ? $page['settings_desc'] : $l['admin_settings_desc'], '</p>';
  
  # Show any errors
  if($page['errors'])
  {
    echo '
      <div class="generic_error">';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  echo '
      <fieldset>
        <form action="', $page['submit_url'], '" method="post">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">';

  # Loop through all our settings...
  foreach($page['settings'] as $setting)
  {
    # CAPTCHA.
    if(in_array('captcha', $setting['tags']))
    {
      echo '
            <tr class="setting_container">
              <td width="16">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=adminhelp;var='. $setting['name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td valign="top"><label for="', $setting['name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '<br /><span class="small subtext">'. $setting['subtext']. '</span>' : '', '</td>
              <td width="50%">
                <img id="captcha_preview" src="', $base_url, '/index.php?action=captcha;width=200;height=96;fontsize=28" alt="" width="200" height="96" style="float: right;" />
                ', $setting['input'], $setting['postfix'], '
              </td>
            </tr>';
    }
    # Only if it isn't a separator.
    elseif($setting['type'] != 'separator')
    {
      echo '
            <tr class="setting_container">
              <td width="16">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=adminhelp;var='. $setting['name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td valign="top"><label for="', $setting['name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '<br /><span class="small subtext">'. $setting['subtext']. '</span>' : '', '</td>
              <td width="50%">', $setting['input'], $setting['postfix'], '</td>
            </tr>';
    }
    else
    {
      echo '
           <tr><td class="separator" colspan="4"></td></tr>';
    }
  }

  echo '
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" value="', $l['settings_core_submit'], '" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}

function settings_theme_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  # Show our title XD.
  echo '
      <h1>', $l['settings_theme_header'], '</h1>
      <p>', $l['settings_theme_desc'], '</p>';
  
  # Show any errors
  if($page['errors'])
  {
    echo '
      <div class="generic_error">';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  echo '
      <fieldset>
        <form action="" method="post">
          <input type="hidden" name="process" vaue="true" />
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">';

  # Loop through all our settings...
  foreach($page['settings'] as $setting)
  {
    # Check if it's the theme changer.
    if(in_array('theme', $setting['tags']))
    {
      echo '
            <tr class="setting_container">
              <td width="16">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=adminhelp;var='. $setting['name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td valign="top">
                <label for="', $setting['name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '
                <br />
                <div class="small subtext">'. $setting['subtext']. '</span>' : '', '
              </td>
              <td width="25%">
                ', $setting['input'], $setting['postfix'], '
              </td>
              <td width="25%" rowspan="2">
                <img id="theme_preview" class="theme_preview" src="', $settings['theme_url'], '/', $page['theme']['thumb'], '" alt="Theme preview" />
              </td>
            </tr>
            <tr>
              <td></td>
              <td colspan="2" style="vertical-align: top;">
                <div id="theme_name" class="theme_name">', $page['theme']['name'], '</div>
                <div id="theme_desc" class="theme_desc">', $page['theme']['desc'], '</div>
                <br />
                <div id="theme_creator" class="theme_creator">', sprintf($l['settings_theme_creator'], '<a href="'. $page['theme']['website']. '">'. $page['theme']['creator']. '</a>'), '<div>
              </td>
            </tr>
           <tr><td class="theme_separator" colspan="3"></td></tr>';
    }
    # Only if it isn't a separator.
    elseif($setting['type'] != 'separator')
    {
      echo '
            <tr class="setting_container">
              <td width="16">', !empty($setting['popup']) ? '<a href="javascript:void(0);" onclick="popupWindow(\'index.php?action=adminhelp;var='. $setting['name']. '\', 325, 150, true);"><img src="'. $settings['images_url']. '/information.png" alt="?" title="?" /></a>' : '', '</td>
              <td valign="top"><label for="', $setting['name'], '">', $setting['label'], '</label>', !empty($setting['subtext']) ? '<br /><span class="small subtext">'. $setting['subtext']. '</span>' : '', '</td>
              <td width="50%" colspan="2">', $setting['input'], $setting['postfix'], '</td>
            </tr>';
    }
    else
    {
      # Okay, then it's a separator, so display a... well, separator.
      echo '
           <tr><td class="separator" colspan="4"></td></tr>';
    }
  }

  echo '
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" name="save" value="', $l['settings_theme_submit'], '" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}
?>