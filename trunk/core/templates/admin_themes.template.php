<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

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
		<a name="', api()->context['current_theme']['anchor'], '"></a>
		<p class="bold no-margin">', api()->context['current_theme']['name'], ' v', api()->context['current_theme']['version'], ' ', l('by'), ' ', api()->context['current_theme']['author'], '</p>
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
					<a name="', $theme['anchor'], '"></a>
					<a href="', $theme['select_href'], '" title="', l('Select &quot;%s&quot; as the website theme', $theme['name']), '"><img src="', $theme['image_url'], '" alt="" title="" /></a>
					<p class="bold">', $theme['name'], ' v', $theme['version'], ' ', l('by'), ' ', $theme['author'], '</p>
					<p class="italic small">', $theme['description'], '</p>
					<p class="center"><a href="', $theme['select_href'], '" title="', l('Select &quot;%s&quot; as the website theme', $theme['name']), '"', (!$theme['is_compatible'] ? ' onclick="return confirm(\''. l('This theme isn\\\'t compatible with your version of SnowCMS, would you like to activate it anyways?'). '\');"' : ''), '>', l('Select'), '</a> | <a href="', $theme['delete_href'], '" title="', l('Delete &quot;%s&quot;', $theme['name']), '" onclick="return confirm(\'', l('Do you really want to delete the theme &quot;%s&quot;?\r\nThis can not be undone!', $theme['name']), '\');">', l('Delete'), '</a>', (!empty($theme['update_available']) ? ' | <a href="'. $theme['update_href']. '" title="'. l('Update &quot;%s&quot; from v%s to v%s', $theme['name'], $theme['version'], $theme['update_version']). '" class="bold red">'. l('Update'). '</a>' : ''), '</p>
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
				<td><p class="bold center">', l('No other themes installed. Would you like to <a href="%s">install a new theme</a>?', baseurl. '/index.php?action=admin&amp;sa=themes&amp;section=install'), '</p></td>';
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