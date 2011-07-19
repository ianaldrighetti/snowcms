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

// Title: Compatibility functions

// Don't have JSON enabled?
if(!function_exists('json_encode'))
{
	require_once(coredir. '/compat/json.php');
}

// Windows doesn't seem to have mime_content_type, at least on certain
// setups, it is somewhat important :P
if(!function_exists('mime_content_type'))
{
	require_once(coredir. '/compat/mime_content_type.php');
}

// Windows also doesn't have fnmatch if you're using a PHP version lower
// than 5.3.0.
if(!function_exists('fnmatch'))
{
	function fnmatch($pattern, $string)
	{
		return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.', '\[' => '[', '\]' => ']'))."$#i", $string);
	}

}

// The following are a bit simpler ;-) or just plain don't exist on
// any version of PHP...

/*
	Function: array_insert

	Inserts an item at the specified index.

	Parameters:
		array $array - The array to insert the item into.
		mixed $item - The item to insert.
		int $position - The position at which to insert item.
		string $key

	Returns:
		array - Returns the new array with item inserted at the specified
						position.

	Note:
		If you are inserting the item into an associative array, you must
		specify the $key parameter, which is the key of the inserted item!
*/
function array_insert($array, $item, $index, $key = null)
{
	$index = (int)$index;
	$length = count($array);

	// Is it an associative array?
	if($key === null)
	{
		// Maybe we can just plop it at the end..?
		if($index >= $length)
		{
			$array[] = $item;
		}
		else
		{
			$new_array = array();

			for($i = 0; $i < $length; $i++)
			{
				// The right index to insert item at?
				if($i == $index)
					$new_array[] = $item;

				$new_array[] = $array[$i];
			}

			$array = $new_array;
		}
	}
	else
	{
		// Can't have two of the same indexes, sorry!
		if(isset($array[$key]))
		{
			return false;
		}
		elseif($index >= $length)
		{
			$array[$key] = $item;
		}
		else
		{
			// Interesting... :P
			$new_array = array();
			$current = 0;

			foreach($array as $akey => $avalue)
			{
				if($current == $index)
					$new_array[$key] = $item;

				$new_array[$akey] = $avalue;
			}

			$array = $new_array;
		}
	}

	return $array;
}

// Some constants that aren't defined until PHP 5.3.0.
if(!defined('E_DEPRECATED'))
{
	// So for a bit of compatibility, let's define them ;)
	define('E_DEPRECATED', 8192);
	define('E_USER_DEPRECATED', 16384);
}

/*
	Function: recursive_unlink

	Deletes everything in the specified directory, including the
	directory itself.

	Parameters:
		string $directory - The name of the directory to delete, along with the
												contents that reside within it.

	Returns:
		bool - Returns true if the contents of the directory were removed, along
					 with the directory itself, false will be returned in the case
					 that either certain files/directories couldn't be removed, or the
					 directory itself.
*/
function recursive_unlink($directory)
{
	// Does the directory not exist? Then we cannot delete it!
	if(!file_exists($directory))
	{
		return false;
	}
	// Is it a file? Just delete it!
	elseif(is_file($directory))
	{
		return unlink($directory);
	}
	// Nope, it is a directory.
	else
	{
		// So get all the files and what not.
		$files = scandir($directory);

		if(count($files) > 0)
		{
			foreach($files as $file)
			{
				// Skip . and ..
				if($file == '.' || $file == '..')
				{
					continue;
				}

				// Is it a directory? Recursion!
				if(is_dir($directory. '/'. $file))
				{
					recursive_unlink($directory. '/'. $file);
				}
				// Just a file, so delete it :-)
				else
				{
					unlink($directory. '/'. $file);
				}
			}
		}

		// Now to delete the directory itself!
		return rmdir($directory);
	}
}

/*
	Function: sanitize_filename

	Removes characters from the supplied string that would not be allowed
	in a traditional file name, such as slashes and so on.

	Parameters:
		string $filename - The name (and only the name!) of the file.

	Returns:
		string - Returns the sanitized name.
*/
function sanitize_filename($filename)
{
	// Disallowed characters ;-)
	$remove = array('/', '\\', ':', '*', '?', '<', '>', '|', '"');

	$str = '';
	$length = strlen($filename);
	for($i = 0; $i < $length; $i++)
	{
		// Is it allowed?
		if(in_array($filename[$i], $remove))
		{
			// Nope!
			continue;
		}

		$str .= $filename[$i];
	}

	return $str;
}

/*
	Function: is_flat_array

	Returns whether or not the array is a flat array. What it means is that
	if the array is not an associative array (string indexes), then it is
	considered a "flat" array (numerical indexes only).

	Parameters:
		array $array - The array to check.

	Returns:
		bool - Returns true if the array is a flat array, false if not.
*/
function is_flat_array($array)
{
	// It's not an array, so no...
	if(!is_array($array))
	{
		return false;
	}
	// Nothing? Technically we will consider that a flat array :P
	elseif(count($array) == 0)
	{
		return true;
	}

	foreach($array as $key => $value)
	{
		if((string)$key != (string)(int)$key)
		{
			// We found one that is not a numerical index, therefore, not flat!
			return false;
		}
	}

	return true;
}

/*
	Function: format_filesize

	Returns a string containing a nicer representation of the specified file
	size, instead of just in bytes as <www.php.net/filesize> provides. There
	are two ways to use this function, one would be to provide the name of the
	file, or to provide the size of the file itself.

	Parameters:
		string $filename - The name of the file to format the file size of,
											 unless $is_filesize is set to true, in which case
											 this parameter is now the file size.
		bool $is_filesize - This should be set to true if you do not want to
												provide the file name, but the size of a file (or
												something else, such as a string). Defaults to
												false.
		int $precision - The number of decimals to round to on the right side of
										 the period. Defaults to 2.

	Returns:
		string - Returns a string containing the formatted file size, however
						 false will be returned in the case that the file does not
						 exist or if the file size supplied is not valid (< 0).

	Note:
		Please note that if a directory is specified, the function will
		calculate the entire size of the directories contents, including all
		nested directories as well.

		Credit for the formatting of the file size:
			<www.php.net/manual/en/function.filesize.php#100097>
*/
function format_filesize($filename, $is_filesize = false, $precision = 2)
{
	// Not a file size? Then we shall fetch it!
	if(empty($is_filesize))
	{
		// Make sure the file, or directory, exists.
		if(!file_exists($filename))
		{
			// We can't calculate the size of nothing, well, we could, but we
			// won't!
			return false;
		}
		// Maybe it is a directory?
		elseif(is_dir($filename))
		{
			// Recursively calculate the directory size.
			$filename = recursive_filesize($filename);
		}
		else
		{
			// Must be a file.
			$filename = filesize($filename);
		}
	}

	// Let's just make sure...
	if((string)$filename != (string)(int)$filename || $filename < 0)
	{
		return false;
	}

	// Alright, here we go!!
	$units = array('B', 'KB', 'MB', 'GB', 'TB');
	for($i = 0; $i < 4 && $filename >= 1024; $i++)
	{
		$filename = $filename / 1024;
	}

	return round($filename, (int)$precision > 0 ? $precision : 0). ' '. $units[$i];
}

/*
	Function: recursive_filesize

	Calculates the size of a directory's files, along with all nested
	directories.

	Parameters:
		string $directory - The directory to calculate the size of.

	Returns:
		int - Returns the size of the specified directory, but false if the
					directory does not exist.
*/
function recursive_filesize($directory)
{
	// Does the directory not exist? Then we cannot calculate it's size!
	if(!file_exists($directory))
	{
		return false;
	}
	// Is it a file? Just get the size, then.
	elseif(is_file($directory))
	{
		return filesize($directory);
	}
	// Nope, it is a directory.
	else
	{
		// So get all the files and what not.
		$files = scandir($directory);
		$filesize = 0;

		if(count($files) > 0)
		{
			foreach($files as $file)
			{
				// Skip . and ..
				if($file == '.' || $file == '..')
				{
					continue;
				}

				// Is it a directory? Recursion!
				if(is_dir($directory. '/'. $file))
				{
					$filesize += recursive_filesize($directory. '/'. $file);
				}
				// Just a file, so add it's size.
				else
				{
					$filesize += filesize($directory. '/'. $file);
				}
			}
		}

		// Return the size of all the files.
		return $filesize;
	}
}

/*
	Function: compare_versions

	Compares two version numbers. This is practically the same as the built-in
	<www.php.net/version_compare> function, but with a slight addition. This
	function should be used instead of version_compare. See the notes for more
	information.

	Parameters:
		string $version1 - First version number.
		string $version2 - Second version number.
		string $operator - Just as with the built-in version_compare function,
											 you can specify a comparison operator, such as: <,
											 lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>,  or ne.

	Returns:
		mixed - Returns -1 if $version1 is lower than $version2, 0 if equal, and
						1 if $version1 is greater than $version 2, that is if no
						$operator is specified. If an operator is supplied true will be
						returned if the relationship is that of the specified operator,
						false if not.

	Note:
		As stated, this is very similar to that of the built-in version_compare
		function, however there is a weird "thing" the built-in function does.

		For example, if $version1 is 1.0 and $version2 is 1.0.0, version_compare
		will return -1, i.e. 1.0 is less than 1.0.0. This is of course not
		something that should happen.

		This function fixes that by adding .0's to the end of the shortest
		version (the one with less .'s, and as many as required) before
		supplying the parameters to the real version_compare.

		Please note that such things as dev, beta, rc, etc. will be removed
		before adding any .0's to the end of the version numbers ONLY IF they
		are separated by a space or a dash (-)!
*/
function compare_versions($version1, $version2, $operator = null)
{
	// Count the number of .'s in each version.
	$version1_dots = substr_count($version1, '.');
	$version2_dots = substr_count($version2, '.');

	// Now, which one has less?
	if($version1_dots > $version2_dots)
	{
		// Looks like version2 needs some more.
		// But maybe there is -beta or something?
		if(strpos($version2, ' ') !== false)
		{
			list($version2, $after) = explode(' ', $version2, 2);
		}
		elseif(strpos($version2, '-') !== false)
		{
			list($version2, $after) = explode('-', $version2, 2);
		}

		// Add as many .0's as we need! Also add back $after, if we need to.
		$version2 .= str_repeat('.0', $version1_dots - $version2_dots). (isset($after) ? $after : '');
	}
	elseif($version2_dots > $version1_dots)
	{
		// Do the same thing, but for the other version.
		// Looks like version2 needs some more.
		// But maybe there is -beta or something?
		if(strpos($version1, ' ') !== false)
		{
			list($version1, $after) = explode(' ', $version1, 2);
		}
		elseif(strpos($version1, '-') !== false)
		{
			list($version1, $after) = explode('-', $version1, 2);
		}

		// Add as many .0's as we need! Also add back $after, if we need to.
		$version1 .= str_repeat('.0', $version2_dots - $version1_dots). (isset($after) ? $after : '');
	}

	// Alright, now we can call version_compare.
	return $operator !== null ? version_compare($version1, $version2, $operator) : version_compare($version1, $version2);
}

/*
	Function: is_compatible

	Checks to see if this version of SnowCMS is compatible with
	whatever you're checking, be it plugin, theme, etc.

	Parameters:
		string $versions - The string of versions that this thing is
								compatible with. Should be comma
								delimited.

	Returns:
		bool - True if this version of SnowCMS is listed, false if it isn't.
*/
function is_compatible($versions)
{
	// Did you really give us a string?
	if(!is_string($versions))
	{
		// You lied!
		return false;
	}
	
	// Good, you gave us a string! Let's manipulate it now.
	$versions = str_replace(' ', '', $versions);
	$versions = explode(',', $versions);
	
	// That's done, now we can check out all those version numbers.
	foreach($versions as $index => $version)
	{
		// Does this version number have a wildcard?
		if(strpos($version, '*'))
		{
			// Check the wildcard to see if this version of Snow matches it.
			if(fnmatch($version, settings()->get('version', 'string')))
			{
				// It's your lucky day!
				return true;
			}
		}
		
		// No, it doesn't. That simplifies things for us!
		else
		{
			if($version == settings()->get('version', 'string'))
			{
				// Good, it's compatible!
				return true;
			}
		}
	}
	
	// Aww, this...thing we're testing isn't compatible :(
	return false;
}

?>