<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
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

// Title: Theme information

/*
	Function: theme_load

	Loads the specified themes information. Just as with <plugin_load>, this
	should be used incase the schema or file of the theme information were to
	change, very little modifications would need to be made, if at all, for
	anything using this function.

	Parameters:
		string $path - The root directory of the theme to load the information of.

	Returns:
		array - Returns the themes information in an array, false if the theme
						does not exist.

	Note:
		The following indexes are returned:

			string author - The name of the themes author.*

			string website - The website of the author.

			string email - The email address of the author.

			string name - The name of the theme.*

			string description - Description of the theme.

			string version - The themes current version.

			string update_url - The URL where updates for the theme can be retrieved from.

			string directory - The directory where the theme is located.*

		* (asterisk) Indicates this index will never be null.
*/
function theme_load($path)
{
	// Doesn't exist? Then we can't load it!
	if(!file_exists($path) || !is_dir($path) || !file_exists($path. '/header.template.php') || !file_exists($path. '/footer.template.php') || !file_exists($path. '/theme.xml'))
	{
		return false;
	}

	// We need the XML class to do this.
	$xml = api()->load_class('XML');

	// Parse the XML file now.
	$data = $xml->parse($path. '/theme.xml');

	if(count($data) > 0)
	{
		// Keep track of whether or not we are in the author tag.
		$in_author = false;

		// Keep track of the theme info.
		$theme_info = array(
										'author' => null,
										'website' => null,
										'email' => null,
										'name' => null,
										'description' => null,
										'version' => null,
										'update_url' => null,
									);
		foreach($data as $item)
		{
			// Keep track of where we are.
			if($item['tag'] == 'author' && $item['type'] == 'open')
			{
				$in_author = true;
			}
			elseif($item['tag'] == 'author' && $item['type'] == 'close')
			{
				$in_author = false;
			}

			// Saving something?
			if($item['tag'] == 'name' && $in_author)
			{
				$theme_info['author'] = $item['value'];
			}
			elseif($item['tag'] == 'update-url')
			{
				$theme_info['update_url'] = $item['value'];
			}
			elseif(array_key_exists($item['tag'], $theme_info) && $item['type'] != 'close')
			{
				$theme_info[$item['tag']] = $item['value'];
			}
		}

		// No author? No name? No way!
		if(empty($theme_info['author']) || empty($theme_info['name']))
		{
			return false;
		}
	}
	else
	{
		// Woops, that's not right!
		return false;
	}

	// Add the path, just incase :P
	$theme_info['path'] = realpath($path);
	$theme_info['directory'] = $theme_info['path'];

	// Alright, here ya go.
	return $theme_info;
}

/*
	Function: theme_list

	Lists all the current available themes in the theme directory.

	Parameters:
		none

	Returns:
		array - Returns an array containing all the paths to available
						themes, false if the theme directory does not exist.

	Note:
		In order for a theme to be detected, the folder must contain a theme.ini
		and implemented_theme.class.php file.
*/
function theme_list()
{
	// Doesn't exist?!
	if(!file_exists(themedir) || !is_dir(themedir))
	{
		return false;
	}

	// Get all the directories.
	$ls = scandir(themedir);

	$list = array();
	foreach($ls as $path)
	{
		// Skip ., .. and .svn.
		if(in_array($path, array('.', '..', '.svn')))
		{
			continue;
		}

		// Only look in directories, they are themes if they have the
		// implemented_theme.class.php file.
		if(is_dir(themedir. '/'. $path) && file_exists(themedir. '/'. $path. '/header.template.php') && file_exists(themedir. '/'. $path. '/footer.template.php') && file_exists(themedir. '/'. $path. '/theme.xml'))
		{
			$list[] = realpath(themedir. '/'. $path);
		}
	}

	// Whether or not there were any themes found, return the array.
	return $list;
}

/*
	Function: theme_title

	Outputs the current title of the web page. This function is meant to be
	used when creating a theme.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function, as the title is output with
					 an echo.
*/
function theme_title()
{
	echo api()->apply_filters('theme_title', (strlen(theme()->title()) > 0 ? htmlchars(theme()->title()). ' - ' : ''). (strlen(theme()->main_title()) > 0 ? theme()->main_title() : ''));
}

/*
	Function: theme_head

	Outputs the HTML which is displayed between the <head> tags in a theme.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function theme_head()
{
	api()->run_hooks('pre_theme_head');

	// Got any meta tags?
	if(count(theme()->return_meta()) > 0)
	{
		foreach(theme()->return_meta() as $meta)
		{
			echo '
	', theme()->generate_tag('meta', $meta);
		}
	}

	// Now for links... Whatever those may be.
	if(count(theme()->return_links()) > 0)
	{
		foreach(theme()->return_links() as $link)
		{
			echo '
	', theme()->generate_tag('link', $link);
		}
	}

	// JavaScript variables! Yippe!
	if(count(theme()->return_js_var()) > 0)
	{
		echo '
	<script type="text/javascript" language="JavaScript"><!-- // --><![CDATA[';

		foreach(theme()->return_js_var() as $variable => $value)
		{
			echo '
		var ', $variable, ' = ', json_encode($value), ';';
		}

		echo '
	</script>';
	}

	// Finally, any JavaScript files?
	if(count(theme()->return_js_files()) > 0)
	{
		foreach(theme()->return_js_files() as $js_file)
		{
				echo '
	<script', !empty($js_file['language']) ? ' language="'. $js_file['language']. '"' : '', !empty($js_file['type']) ? ' type="'. $js_file['type']. '"' : '', !empty($js_file['src']) ? ' src="'. $js_file['src']. '"' : '', !empty($js_file['defer']) ? ' defer="defer"' : '', !empty($js_file['charset']) ? ' charset="'. $js_file['charset']. '"' : '', '></script>';
		}
	}

	// Now you can add anything if you want.
	echo api()->apply_filters('theme_head', '');

	api()->run_hooks('post_theme_head');
}

/*
	Function: theme_site_name

	Outputs the name of the website.

	Parameters:
		none

	Returns:
		void
*/
function theme_site_name()
{
	echo api()->apply_filters('theme_site_name', settings()->get('site_name', 'string', l('SnowCms')));
}

/*
	Function: theme_sub_title

	Outputs the sub title of the website (slogan, if you will).

	Parameters:
		none

	Returns:
		void
*/
function theme_sub_title()
{
	echo api()->apply_filters('theme_sub_title', settings()->get('sub_title', 'string', l('Light, simple, free. It&#039;s all you need, and just that.')));
}

/*
	Function: theme_menu

	Outputs the desired menu.

	Parameters:
		string $menu - The name of the menu to display, defaults to "main."

	Returns:
		void

	Note:
		If a theme needs to change the before and after HTML surrounding the
		links they can apply filters to theme_menu_before and theme_menu_after.
*/
function theme_menu($menu = 'main')
{
	api()->run_hooks('pre_theme_menu', array(&$menu));

	// Load'em up! Maybe.
	$menu_items = api()->return_menu_items($menu);

	if(!empty($menu_items) && count($menu_items) > 0)
	{
		foreach($menu_items as $item)
		{
			// We won't give it the content or extra stuff, but we will *need* the
			// content stuff ourselves.
			$content = $item['content'];
			unset($item['content'], $item['extra']);

			echo '
	', api()->apply_filters('theme_menu_before', '<li>'), theme()->generate_tag('a', $item, false), $content, '</a>', api()->apply_filters('theme_menu_after', '</li>');
		}
	}

	api()->run_hooks('post_theme_menu', array(&$menu));
}

/*
	Function: theme_foot

	Outputs the information displayed in the sites footer area.

	Parameters:
		none

	Returns:
		void

	Note:
		There is a filter called theme_foot which can be used to modify the
		information displayed, except for the "Powered by SnowCMS" part.
*/
function theme_foot()
{
	echo api()->apply_filters('theme_foot', l('Page created in %s seconds with %u queries.', round(microtime(true) - starttime, 3), db()->num_queries)). (settings()->get('show_version', 'bool', true) ? ' | '. l('Powered by <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a> v%s', settings()->get('version', 'string')) : '');
}
?>