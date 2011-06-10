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

if(!defined('IN_SNOW'))
{
  die('Nice try...');
}

# Title: Compatibility functions

# Don't have JSON enabled?
if(!function_exists('json_encode'))
{
  require_once($core_dir. '/compat/json.php');
}

# Windows doesn't seem to have mime_content_type, at least on certain
# setups, it is somewhat important :P
if(!function_exists('mime_content_type'))
{
  require_once($core_dir. '/compat/mime_content_type.php');
}

# The following are a bit simpler ;-) or just plain don't exist on
# any version of PHP...

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

  # Is it an associative array?
  if($key === null)
  {
    # Maybe we can just plop it at the end..?
    if($index >= $length)
    {
      $array[] = $item;
    }
    else
    {
      $new_array = array();

      for($i = 0; $i < $length; $i++)
      {
        # The right index to insert item at?
        if($i == $index)
          $new_array[] = $item;

        $new_array[] = $array[$i];
      }

      $array = $new_array;
    }
  }
  else
  {
    # Can't have two of the same indexes, sorry!
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
      # Interesting... :P
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

# Some constants that aren't defined until PHP 5.3.0.
if(!defined('E_DEPRECATED'))
{
  # So for a bit of compatibility, let's define them ;)
  define('E_DEPRECATED', 8192);
  define('E_USER_DEPRECATED', 16384);
}

/*
  Function: recursive_unlink

  Deletes everything in the specified directory, including the
  directory itself.

  Parameters:
    string $path

  Returns:
    void - Nothing is returned by this function.
*/
function recursive_unlink($path)
{
  # Does the directory not exist? Then we cannot delete it!
  if(!file_exists($path))
  {
    return false;
  }
  # Is it a file? Just delete it!
  elseif(is_file($path))
  {
    return unlink($path);
  }
  # Nope, it is a directory.
  else
  {
    # So get all the files and what not.
    $files = scandir($path);

    if(count($files) > 0)
    {
      foreach($files as $file)
      {
        # Skip . and ..
        if($file == '.' || $file == '..')
        {
          continue;
        }

        # Is it a directory? Recursion!
        if(is_dir($path. '/'. $file))
        {
          recursive_unlink($path. '/'. $file);
        }
        # Just a file, so delete it :-)
        else
        {
          unlink($path. '/'. $file);
        }
      }
    }

    # Now to delete the directory itself!
    return rmdir($path);
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
  # Disallowed characters ;-)
  $remove = array('/', '\\', ':', '*', '?', '<', '>', '|', '"');

  $str = '';
  $length = strlen($filename);
  for($i = 0; $i < $length; $i++)
  {
    # Is it allowed?
    if(in_array($filename[$i], $remove))
    {
      # Nope!
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
  # It's not an array, so no...
  if(!is_array($array))
  {
    return false;
  }
  # Nothing? Technically we will consider that a flat array :P
  elseif(count($array) == 0)
  {
    return true;
  }

  foreach($array as $key => $value)
  {
    if((string)$key != (string)(int)$key)
    {
      # We found one that is not a numerical index, therefore, not flat!
      return false;
    }
  }

  return true;
}
?>