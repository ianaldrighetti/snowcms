<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

    echo '
  <h1><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Manage themes'), '</h1>
  <p style="margin-bottom: 20px;">', l('Here you can set the sites theme and also install themes as well.'), '</p>';

    // Get a listing of all the themes :-).
    $themes = theme_list();

    // Now load the information of the current theme.
    $current_theme = theme_load(themedir. '/'. settings()->get('theme', 'string', 'default'));

    echo '
  <div style="float: left; width: 200px;">
    <img src="', themeurl, '/', settings()->get('theme', 'string', 'default'), '/image.png" alt="" title="', $current_theme['name'], '" />
  </div>
  <div style="float: right; width: 590px;">
    <h1 style="margin-top: 0px;">', l('Current theme: %s', $current_theme['name']), '</h1>
    <h3 style="margin-top: 0px;">', l('By %s', (!empty($current_theme['website']) ? '<a href="'. $current_theme['website']. '">' : ''). $current_theme['author']. (!empty($current_theme['website']) ? '</a>' : '')), '</h3>
    <p>', $current_theme['description'], '</p>
  </div>
  <div class="break">
  </div>
  <h1 style="margin-top: 20px;">', l('Available themes'), '</h1>
  <table class="theme_list">
    <tr>';

    // List all the themes ;-)
    $length = count($themes);
    for($i = 0; $i < $length; $i++)
    {
      $theme_info = theme_load($themes[$i]);

      if(($i + 1) % 3 == 1)
      {
        echo '
    </tr>
  </table>
  <table class="theme_list">
    <tr>';
      }

      // Check to see if there is an update available.
      $update_available = false;

      // There is a file containing the new version...
      if(file_exists($theme_info['path']. '/available-update') && version_compare(file_get_contents($theme_info['path']. '/available-update'), $theme_info['version'], '>'))
      {
        $update_available = file_get_contents($theme_info['path']. '/available-update');
      }

      echo '
      <td><a href="', baseurl, '/index.php?action=admin&amp;sa=themes&amp;set=', urlencode(basename($theme_info['path'])), '&amp;sid=', member()->session_id(), '" title="', l('Set as site theme'), '"', (basename($theme_info['path']) == settings()->get('theme', 'string', 'default') ? ' class="selected"' : ''), '><img src="', themeurl, '/', basename($theme_info['path']), '/image.png" alt="" title="', $theme_info['description'], '" /><br />', $theme_info['name'], ' </a><br /><a href="', baseurl, '/index.php?action=admin&amp;sa=themes&amp;delete=', urlencode(basename($theme_info['path'])), '&amp;sid=', member()->session_id(), '" title="', l('Delete %s', $theme_info['name']), '" onclick="', (settings()->get('theme', 'string', 'default') == basename($theme_info['path']) ? 'alert(\''. l('You cannot delete the current theme.'). '\'); return false;' : 'return confirm(\''. l('Are you sure you want to delete this theme?\r\nThis cannot be undone!'). '\');"'), '" class="button">', l('Delete'), '</a>', !empty($update_available) ? '<a href="'. baseurl. '/index.php?action=admin&amp;sa=themes&amp;update='. urlencode(basename($theme_info['path'])). '&amp;version='. urlencode($update_available). '&amp;sid='. member()->session_id(). '" title="'. l('Update theme to version %s', htmlchars($update_available)). '" class="button important">'. l('Update available'). '</a>' : '', '</td>';
    }

    echo '
    </tr>
  </table>

  <h1>', l('Install a theme'), '</h1>
  <p>', l('Below you can specify a file to upload or a URL at which to download a theme (zip, tar and tar.gz only).'), '</p>';

    api()->context['form']->show('install_theme_form');
?>