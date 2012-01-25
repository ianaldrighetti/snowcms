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

			string update_url - The URL where updates for the theme can be
													retrieved from.

			string directory - The directory where the theme is located.*

			string compatible_with - A string containing a comma separated list of
															 versions (SnowCMS versions, that is) the
															 theme is compatible with.

			bool is_compatible - This index will contain true if the theme is
													 compatible with the currently running version of
													 SnowCMS, false if not. However if no versions
													 were specified in compatible_with, it will
													 contain null.

		* (asterisk) Indicates this index will never be null.
*/
function theme_load($path)
{
	// Doesn't exist? Then we can't load it!
	if(!is_dir($path) || !is_file($path. '/header.template.php') || !is_file($path. '/footer.template.php') || !is_file($path. '/theme.xml'))
	{
		return false;
	}

	// Everything else will be handled by theme_get_info.
	return theme_get_info($path. '/theme.xml');
}

/*
	Function: theme_get_info

	Parses the specified theme.xml file into an array containing the
	information about a theme.

	Parameters:
		string $filename - The name of the theme XML file.

	Returns:
		array - See notes on <theme_load> for more information.

	Note:
		This function is used to only parse a theme.xml file, but it does not
		validate whether or not a directory is a theme like <theme_load> does,
		but <theme_load> does use this function.

		If the required information (such as author and theme name) is not found
		false will be returned.
*/
function theme_get_info($filename)
{
	// The file needs to exist, of course!
	if(!file_exists($filename) || !is_file($filename))
	{
		return false;
	}

	// We need the XML class to do this.
	$xml = api()->load_class('XML');

	// Parse the XML file now.
	$data = $xml->parse($filename);

	// So, did it work?
	if($data === false)
	{
		// No, it did not.
		return false;
	}

	// Save the theme information from the parsed XML file.
	$theme_info = array(
									'author' => htmlchars($xml->get_value($xml->value('name', 'author'))),
									'website' => htmlchars($xml->get_value($xml->value('website', 'author'))),
									'email' => htmlchars($xml->get_value($xml->value('email', 'author'))),
									'name' => htmlchars($xml->get_value($xml->value('name', 'theme-info'))),
									'description' => htmlchars($xml->get_value($xml->value('description', 'theme-info'))),
									'version' => htmlchars($xml->get_value($xml->value('version', 'theme-info'))),
									'update_url' => htmlchars($xml->get_value($xml->value('update-url', 'theme-info'))),
									'compatible_with' => htmlchars($xml->get_value($xml->value('compatible-with', 'theme-info'))),
									'is_compatible' => null,
								);

	// No need to fetch the data again.
	if(!empty($theme_info['compatible_with']))
	{
		$theme_info['is_compatible'] = is_compatible($theme_info['compatible_with']);
	}

	// No author? No name? No way!
	if(empty($theme_info['author']) || empty($theme_info['name']))
	{
		return false;
	}

	// Just a couple more things... Is the email address valid?
	if(!is_email($theme_info['email']))
	{
		// Forget it, then.
		$theme_info['email'] = null;
	}

	// Same goes for the website URL.
	if(!is_url($theme_info['website']))
	{
		$theme_info['website'] = null;
	}

	// Oh, and the update URL.
	if(!is_url((strtolower(substr($theme_info['update_url'], 0, 7)) != 'http://' && strtolower(substr($theme_info['update_url'], 0, 8)) != 'https://' ? 'http://' : ''). $theme_info['update_url']))
	{
		$theme_info['update_url'] = null;
	}

	// Add the path, just incase :P
	$theme_info['path'] = realpath(dirname($filename));
	$theme_info['directory'] = $theme_info['path'];

	// Alright, we will put together the information array.
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
	if(!defined('themedir') || !file_exists(themedir) || !is_dir(themedir))
	{
		return false;
	}

	// Get all the directories.
	$ls = scandir(themedir);

	$list = array();
	foreach($ls as $directory)
	{
		// Skip ., .. and .svn.
		if(in_array($directory, array('.', '..', '.svn')))
		{
			continue;
		}

		// Only look in directories, they are themes if they have the
		// implemented_theme.class.php file.
		if(is_dir(themedir. '/'. $directory) && file_exists(themedir. '/'. $directory. '/header.template.php') && file_exists(themedir. '/'. $directory. '/footer.template.php') && file_exists(themedir. '/'. $directory. '/theme.xml'))
		{
			$list[] = realpath(themedir. '/'. $directory);
		}
	}

	// Whether or not there were any themes found, return the array.
	return $list;
}

/*
	Function: theme_package_valid

	Checks to see whether or not the specified file contains a valid theme.

	Parameters:
		string $filename - The name of the file to check.

	Returns:
		bool - Returns true if the file contains a valid theme, false if not.

	Note:
		This function uses the <Extraction> class in order to check whether or
		not the following files exist within a compressed file:
		header.template.php, footer.template.php and theme.xml.
*/
function theme_package_valid($filename)
{
	$extraction = api()->load_class('Extraction');

	// Get the list of files.
	$file_list = $extraction->files($filename);

	// Make sure there was anything in there.
	if(count($file_list) > 0)
	{
		// Make sure the files we require exist.
		$found = 0;
		foreach($file_list as $file)
		{
			if(in_array($file['name'], array('header.template.php', 'footer.template.php', 'theme.xml')))
			{
				$found++;
			}
		}

		if($found == 3)
		{
			// They exist, but is the theme.xml file valid?
			$tmp_filename = tempnam(dirname(__FILE__), 'theme_');
			if(!empty($tmp_filename) && $extraction->read($filename, 'theme.xml', $tmp_filename))
			{
				$theme_info = theme_get_info($tmp_filename);

				// We no longer need the temporary file.
				unlink($tmp_filename);

				// The theme information array shouldn't be false.
				return $theme_info !== false;
			}

			unlink($tmp_filename);
		}
	}

	return false;
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
	// ]]></script>';
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
	echo api()->apply_filters('theme_sub_title', settings()->get('site_sub_title', 'string', l('Light, simple, free. It&#039;s all you need, and just that.')));
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

/*
	Function: baseurl

	Returns the properly formatted URL for the specified location.

	Parameters:
		string $uri - The URI to append to the base URL of the site. This
									parameter is optional.

	Returns:
		string - The properly formatted URL for the specified location.

	Note:
		There is a filter called baseurl which is passed "baseurl. '/'. $uri"
		which can be used to modify the URL -- of course.
*/
function baseurl($uri = null)
{
	return api()->apply_filters('baseurl', baseurl. '/'. ($uri !== null && is_string($uri) ? $uri : ''));
}
?>