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

// Title: Control Panel Section Menus

/*
	Function: admin_section_menu

	Displays a section menu for the specified section within the control
	panel.

	Parameters:
		string $section_id - The section to display the links from.
		string $current_id - The current location within the specified section.
		bool $second_level - Whether a second level of menus will be displayed
												 under this one -- defaults to false.

	Returns:
		void - Nothing is returned by this function.
*/
function admin_section_menu($section_id, $current_id = null, $second_level = false)
{
	// Which menu are we displaying?
	if($section_id == 'members')
	{
		$menu = array(
							array(
								'id' => 'members_add',
								'href' => baseurl('index.php?action=admin&amp;sa=members_add'),
								'title' => l('Add a new member'),
								'content' => l('Add Member'),
								'show' => member()->can('add_new_member') || member()->can('manage_members'),
							),
							array(
								'id' => 'members_manage',
								'href' => baseurl('index.php?action=admin&amp;sa=members_manage'),
								'title' => l('Manage existing members'),
								'content' => l('Manage Members'),
								'show' => member()->can('manage_members'),
							),
							array(
								'id' => 'members_settings',
								'href' => baseurl('index.php?action=admin&amp;sa=members_settings'),
								'title' => l('Manage member registration and account settings'),
								'content' => l('Member Settings'),
								'show' => member()->can('manage_member_settings'),
							),
							array(
								'id' => 'members_permissions',
								'href' => baseurl('index.php?action=admin&amp;sa=members_permissions'),
								'title' => l('Manage member group permissions'),
								'content' => l('Manage Permissions'),
								'show' => member()->can('manage_permissions'),
							),
						);
	}
	elseif($section_id == 'plugins')
	{
		$menu = array(
							array(
								'id' => 'plugins_add',
								'href' => baseurl('index.php?action=admin&amp;sa=plugins_add'),
								'title' => l('Add a new plugin'),
								'content' => l('Add Plugin'),
								'show' => member()->can('add_plugins'),
							),
							array(
								'id' => 'plugins_manage',
								'href' => baseurl('index.php?action=admin&amp;sa=plugins_manage'),
								'title' => l('Manage plugins'),
								'content' => l('Manage Plugins'),
								'show' => member()->can('manage_plugins'),
							),
							array(
								'id' => 'plugins_settings',
								'href' => baseurl('index.php?action=admin&amp;sa=plugins_settings'),
								'title' => l('Manage plugin settings'),
								'content' => l('Plugin Settings'),
								'show' => member()->can('manage_plugin_settings'),
							),
						);
	}
	elseif($section_id == 'settings')
	{
		// In case you are wondering, this menu is created within the
		// admin_settings.php file.
		$menu = isset(api()->context['section_menu']) ? api()->context['section_menu'] : array();
	}
	elseif($section_id == 'themes')
	{
		$menu = array(
							array(
								'id' => 'manage',
								'href' => baseurl('index.php?action=admin&amp;sa=themes&amp;section=manage'),
								'title' => l('Manage currently installed themes'),
								'content' => l('Manage Themes'),
								'show' => member()->can('select_theme') || member()->can('manage_themes'),
							),
							array(
								'id' => 'install',
								'href' => baseurl('index.php?action=admin&amp;sa=themes&amp;section=install'),
								'title' => l('Install a new theme from a file or from the Internet'),
								'content' => l('Install Theme'),
								'show' => member()->can('manage_themes'),
							),
							array(
								'id' => 'widgets',
								'href' => baseurl('index.php?action=admin&amp;sa=themes&amp;section=widgets'),
								'title' => l('Select and manage widgets displayed within the current theme'),
								'content' => l('Manage Widgets'),
								'show' => member()->can('manage_widgets') || member()->can('manage_themes'),
							),
						);
	}

	// Maybe a plugin would like to modify the menu.
	api()->run_hooks('admin_section_menu', array($section_id, $current_id, &$menu));

	echo '
	<div class="section-tabs', $second_level === true ? ' section-tabs-no-radius' : '', '">
		<ul>';

	// Time to display those links...
	foreach($menu as $item)
	{
		// If we aren't supposed to show it -- we won't. We also won't show the
		// link unless we have a URL and text to be linked.
		if(empty($item['show']) || empty($item['href']) || empty($item['content']))
		{
			continue;
		}

		// We may have a CSS class or two to add.
		$classes = array();
		if(!isset($not_first))
		{
			$classes[] = 'first';
		}

		// This item may be the selected one.
		if(!empty($current_id) && !empty($item['id']) && $current_id == $item['id'])
		{
			$classes[] = 'selected';
		}

		echo '
			<li><a href="', $item['href'], '"', !empty($item['title']) ? ' title="'. htmlchars($item['title']). '"' : '', !empty($item['onclick']) ? ' onclick="'. $item['onclick']. '"' : '', count($classes) > 0 ? ' class="'. implode(' ', $classes). '"' : '', '>', htmlchars($item['content']), '</a></li>';

		// We're not on the first item anymore!
		$not_first = true;
	}

	echo '
		</ul>
		<div class="break">
		</div>
	</div>';
}
?>