<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<div class="section-tabs">
		<ul>';

		foreach(api()->context['section_menu'] as $item)
		{
			echo '
			<li><a href="', $item['href'], '" title="', $item['title'], '"', ($item['is_first'] || $item['is_selected'] ? ' class="'. ($item['is_first'] ? 'first' : ''). ($item['is_selected'] ? ($item['is_first'] ? ' ' : ''). 'selected' : ''). '"' : ''), '>', $item['text'], '</a></li>';
		}

	echo '
		</ul>
		<div class="break">
		</div>
	</div>';

	// Are you viewing the installer or the page to choose your theme?
	if(api()->context['section'] == 'install')
	{
		echo '
	<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Install a Theme'), '</h3>
	<p>', l('A theme can be installed two different ways, either by uploading a from your computer (such as a zip) or you can enter the URL where the theme can be downloaded from the Internet.'), '</p>';

		api()->context['form']->render('install_theme_form');
	}
	elseif(api()->context['section'] == 'manage')
	{
		echo '
	<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Current Theme'), '</h3>
	<div style="float: left; width: 200px;">
		<img src="', themeurl, '/', settings()->get('theme', 'string', 'default'), '/image.png" alt="" title="', api()->context['current_theme']['name'], '" />
	</div>
	<div style="float: left; margin-left: 10px;">
		<p class="bold no-margin">', api()->context['current_theme']['name'], ' ', l('by'), ' ', api()->context['current_theme']['author'], '</p>
		<p>', api()->context['current_theme']['description'], '</p>', !empty(api()->context['current_theme']['update_available']) ? '
		<p><a href="'. api()->context['current_theme']['update_href']. '" title="'. l('Update &quot;%s&quot; from v%s to v%s', api()->context['current_theme']['name'], api()->context['current_theme']['version'], api()->context['current_theme']['update_version']). '" class="red bold">'. l('Update'). '</a></p>' : '', '
	</div>
	<div class="break">
	</div>
	<h3>', l('Available Themes'), '</h3>
	<table class="theme_list" width="', api()->context['table_width'], '">
		<tr>';

		if(count(api()->context['theme_list']) > 0)
		{
			// Generate a table containing all the themes and what not.
			foreach(api()->context['theme_list'] as $theme)
			{
				echo '
				<td valign="top" width="33%">
					<a href="', $theme['select_href'], '" title="', l('Select &quot;%s&quot; as the website theme', $theme['name']), '"><img src="', $theme['image_url'], '" alt="" title="" /></a>
					<p class="bold">', $theme['name'], ' ', l('by'), ' ', $theme['author'], '</p>
					<p class="italic small">', $theme['description'], '</p>
					<p class="center"><a href="', $theme['select_href'], '" title="', l('Select &quot;%s&quot; as the website theme', $theme['name']), '">', l('Select'), '</a> | <a href="', $theme['delete_href'], '" title="', l('Delete &quot;%s&quot;', $theme['name']), '" onclick="return confirm(\'', l('Do you really want to delete the theme &quot;%s&quot;?\r\nThis can not be undone!', $theme['name']), '\');">', l('Delete'), '</a>', (!empty($theme['update_available']) ? ' | <a href="'. $theme['update_href']. '" title="'. l('Update &quot;%s&quot; from v%s to v%s', $theme['name'], $theme['version'], $theme['update_version']). '" class="bold red">'. l('Update'). '</a>' : ''), '</p>
				</td>';

				if($theme['new_row'])
				{
					echo '
			</tr>
		</table>
		<table class="theme_list" width="', api()->context['table_width'], '">
			<tr>';
				}
			}
		}
		else
		{
			echo '
				<td><p class="bold center">', l('No other themes installed.'), '</p></td>';
		}

		echo '
		</tr>
	</table>';
	}
	else
	{
		// You probably have something else in mind.
		api()->run_hooks('admin_theme_display', array(api()->context['section']));
	}
?>