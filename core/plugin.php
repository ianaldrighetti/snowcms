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

// Title: Plugin information

/*
	Function: plugin_load

	Loads a plugins information from the plugin.ini file. This should be
	used instead of manually loading a plugins information through
	parse_ini_file. This is because the format or location of the plugin
	information could change, and by using this, no major changes should
	have to occur when a schema change occurs.

	Parameters:
		string $plugin_id - The plugins identifier, this could be the path
												to the root directory of the plugin (if is_path
												is true), or the guid of the plugin.
		bool $is_path - If the plugin_id is a path, set this to true, if it
										is a depedency name, this should be false.

	Returns:
		array - Returns an array containing the plugins information, false
						if the plugin was not found.

	Note:
		Here are the following indexes in the array returned:

			string guid - The plugins globally unique identifier.*

			string name - The name of the plugin.*

			string author - The author of the plugin.*

			string version - The version of the plugin.*

			string description - The description of the plugin.

			string website - The authors website.

			string directory - The root directory of the plugin.*

			string compatible_with - A string containing a comma separated list of
															 versions (SnowCMS versions, that is) the
															 plugin is compatible with.

			bool is_compatible - This index will contain true if the plugin is
													 compatible with the currently running version of
													 SnowCMS, false if not. However if no versions
													 were specified in compatible_with, it will
													 contain null.

		* (asterisk) Indicates this index will never be null.
*/
function plugin_load($plugin_id, $is_path = true)
{
	// Is it a path? Make sure it exists...
	if(empty($plugin_id) || (!empty($is_path) && (!is_dir($plugin_id) || !is_file($plugin_id. '/plugin.php') || !is_file($plugin_id. '/plugin.xml'))))
	{
		return false;
	}
	// A guid? That's fine, but we need the path.
	elseif($is_path === false)
	{
		// Get all the plugins, and attempt to interpret the guid into an actual
		// path ;)
		$list = plugin_list();

		// No plugins? Then it definitely doesn't exist.
		if(count($list) > 0)
		{
			foreach($list as $path)
			{
				// Load the plugins informaion with <plugin_load> and check to see
				// if the guid matches :-).
				$plugin = plugin_load($path);

				if($plugin['guid'] == $plugin_id)
				{
					// Found it! Just return it's information now.
					return $plugin;
				}
			}
		}

		// Still running? Then we didn't find it!
		return false;
	}

	// The plugin_get_info function will do the rest of the work.
	return plugin_get_info($plugin_id. '/plugin.xml');
}

/*
	Function: plugin_get_info

	Parses the specified plugin.xml file into an array containing the
	information about a plugin.

	Parameters:
		string $filename - The name of the plugin XML file.

	Returns:
		array - See notes on <plugin_load> for more information.

	Note:
		This function is used to only parse a plugin.xml file so it does not
		validate whether or not a directory is a plugin like <plugin_load> does,
		but <plugin_load> does use this function.

		If the required information (such as author, version, guid, plugin name
		and so on) is not found false will be returned.
*/
function plugin_get_info($filename)
{
	if(!is_file($filename))
	{
		return false;
	}

	// Load up the XML parsing class.
	$xml = api()->load_class('XML');

	$data = $xml->parse($filename);

	// Make sure nothing went wrong when parsing the XML file.
	if($data === false)
	{
		return false;
	}

	$plugin_info = array(
									 'guid' => htmlspecialchars($xml->get_value($xml->value('guid', 'plugin-info')), ENT_QUOTES, 'UTF-8'),
									 'author' => htmlspecialchars($xml->get_value($xml->value('name', 'author')), ENT_QUOTES, 'UTF-8'),
									 'website' => htmlspecialchars($xml->get_value($xml->value('website', 'author')), ENT_QUOTES, 'UTF-8'),
									 'email' => htmlspecialchars($xml->get_value($xml->value('email', 'author')), ENT_QUOTES, 'UTF-8'),
									 'name' => htmlspecialchars($xml->get_value($xml->value('name', 'plugin-info')), ENT_QUOTES, 'UTF-8'),
									 'description' => htmlspecialchars($xml->get_value($xml->value('description', 'plugin-info')), ENT_QUOTES, 'UTF-8'),
									 'version' => htmlspecialchars($xml->get_value($xml->value('version', 'plugin-info')), ENT_QUOTES, 'UTF-8'),
									 'compatible_with' => htmlspecialchars($xml->get_value($xml->value('compatible-with', 'plugin-info')), ENT_QUOTES, 'UTF-8'),
									 'is_compatible' => null,
								 );

	// Check to see if the plugin is compatible.
	if(!empty($plugin_info['compatible_with']))
	{
		$plugin_info['is_compatible'] = is_compatible($plugin_info['compatible_with']);
	}

	// No author? No name? No way!
	if(empty($plugin_info['author']) || empty($plugin_info['name']) || empty($plugin_info['guid']) || empty($plugin_info['version']))
	{
		return false;
	}

	// Make sure the email address is valid.
	if(function_exists('is_email') && !is_email($plugin_info['email']))
	{
		$plugin_info['email'] = null;
	}

	// Same goes for the website URL.
	if(function_exists('is_url') && !is_url($plugin_info['website']))
	{
		$plugin_info['website'] = null;
	}

	// Oh, and the GUID (which is basically a URL).
	if(function_exists('is_url') && !is_url((strtolower(substr($plugin_info['guid'], 0, 7)) != 'http://' && strtolower(substr($plugin_info['guid'], 0, 8)) != 'https://' ? 'http://' : ''). $plugin_info['guid']))
	{
		// The GUID is REQUIRED.
		return false;
	}
	// Why don't we chop off the http://?
	elseif(strtolower(substr($plugin_info['guid'], 0, 7)) == 'http://')
	{
		// It is assumed, after all.
		$plugin_info['guid'] = substr($plugin_info['guid'], 7);
	}

	// Add the path, just incase :P
	$plugin_info['path'] = realpath(dirname($filename));
	$plugin_info['directory'] = $plugin_info['path'];

	// Now return the information.
	return $plugin_info;
}

/*
	Function: plugin_list

	Finds and returns an array containing plugins in the plugin directory.

	Parameters:
		bool $force_reload - Whether to forcibly reload the currently installed
												 plugins by rescanning the plugin directory.

	Returns:
		array - Returns an array containing all the current plugin paths, false
						if the plugin directory does not exist.

	Note:
		In order for a plugin to be detected, the plugins directory must contain
		a plugin.xml and plugin.php file.
*/
function plugin_list($force_reload = false)
{
	static $list_cache = null;

	// Does the plugin directory not exist for some strange reason?
	if(!defined('plugindir') || !file_exists(plugindir) || !is_dir(plugindir))
	{
		return false;
	}
	// No point on loading this over and over again if we don't need to.
	elseif(empty($force_reload) && $list_cache !== null)
	{
		return $list_cache;
	}

	// Scan the plugins directory.
	$ls = scandir(plugindir);

	$list = array();
	foreach($ls as $file)
	{
		// Skip the ., .. and .svn folders.
		if(in_array($file, array('.', '..', '.svn')))
		{
			continue;
		}

		// Only look in directories, of course! Then check and see if
		// plugin.php and plugin.ini exists.
		if(is_dir(plugindir. '/'. $file) && file_exists(plugindir. '/'. $file. '/plugin.php') && file_exists(plugindir. '/'. $file. '/plugin.xml'))
		{
			// Yup, it was a valid (or most likely valid :-P) plugin.
			$list[] = realpath(plugindir. '/'. $file);
		}
	}

	// Store that in our 'cache.'
	$list_cache = $list;

	// Return the list, whether or not there are any.
	return $list;
}

/*
	Function: plugin_package_valid

	Checks to see whether or not the specified file contains a valid plugin.

	Parameters:
		string $filename - The name of the file to check.

	Returns:
		bool - Returns true if the file contains a valid plugin, false if not.

	Note:
		This function uses the <Extraction> class in order to check whether or
		not the following files exist within a compressed file: plugin.php and
		plugin.xml.
*/
function plugin_package_valid($filename)
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
			if(in_array($file['name'], array('plugin.php', 'plugin.xml')))
			{
				$found++;
			}
		}

		if($found == 2)
		{
			// They exist, but is the plugin.xml file valid?
			$tmp_filename = tempnam(dirname(__FILE__), 'plugin_');
			if(!empty($tmp_filename) && $extraction->read($filename, 'plugin.xml', $tmp_filename))
			{
				$plugin_info = plugin_get_info($tmp_filename);

				// We no longer need the temporary file.
				unlink($tmp_filename);

				// The plugin information array shouldn't be false.
				return $plugin_info !== false;
			}

			unlink($tmp_filename);
		}
	}

	return false;
}
?>