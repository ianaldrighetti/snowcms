<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<div class="section-tabs">
		<ul>
			<li><a href="', baseurl, '/index.php?action=admin&amp;sa=themes" class="first', !api()->context['viewing_installer'] ? ' selected' : '', '">', l('Manage Themes'), '</a></li>
			<li><a href="', baseurl, '/index.php?action=admin&amp;sa=themes&amp;do=install"', api()->context['viewing_installer'] ? ' class="selected"' : '', '>', l('Install Themes'), '</a></li>
		</ul>
		<div class="break">
		</div>
	</div>';

	// Are you viewing the installer or the page to choose your theme?
	if(api()->context['viewing_installer'])
	{
		echo '
	<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Install a Theme'), '</h3>
	<p>', l('A theme can be installed two different ways, either by uploading a from your computer (such as a zip) or you can enter the URL where the theme can be downloaded from the Internet.'), '</p>';

		api()->context['form']->render('install_theme_form');
	}
	else
	{
		echo '
	<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Current Theme'), '</h3>
	<div style="float: left; width: 200px;">
		<img src="', themeurl, '/', settings()->get('theme', 'string', 'default'), '/image.png" alt="" title="', api()->context['current_theme']['name'], '" />
	</div>
	<div style="float: left; margin-left: 10px;">
		<p class="bold no-margin">', l('%s by %s', api()->context['current_theme']['name'], (!empty(api()->context['current_theme']['website']) ? '<a href="'. api()->context['current_theme']['website']. '">' : ''). api()->context['current_theme']['author']. (!empty(api()->context['current_theme']['website']) ? '</a>' : '')), '</p>
		<p>', api()->context['current_theme']['description'], '</p>
	</div>
	<div class="break">
	</div>
	<h3>', l('Available Themes'), '</h3>
	<table class="theme_list">
		<tr>';

		// List all the themes ;-)
		$length = count(api()->context['themes']);
		for($i = 0; $i < $length; $i++)
		{
			$theme_info = theme_load(api()->context['themes'][$i]);

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
	</table>';
	}
?>