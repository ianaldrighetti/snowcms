<?php
#########################################################################
#                             SnowCMS v2.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#                  Released under the GNU GPL v3 License                #
#                     www.gnu.org/licenses/gpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#                SnowCMS v2.0 began in November 2009                    #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 2.0                         #
#########################################################################

if(!defined('IN_SNOW'))
  die;

# Title: Theme information

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
  global $api;

  # Doesn't exist? Then we can't load it!
  if(!file_exists($path) || !is_dir($path) || !file_exists($path. '/implemented_theme.class.php') || !file_exists($path. '/theme.xml'))
  {
    return false;
  }

  # We need the XML class to do this.
  $xml = $api->load_class('XML');

  # Parse the XML file now.
  $data = $xml->parse($path. '/theme.xml');

  if(count($data) > 0)
  {
    # Keep track of whether or not we are in the author tag.
    $in_author = false;

    # Keep track of the theme info.
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
      # Keep track of where we are.
      if($item['tag'] == 'author' && $item['type'] == 'open')
      {
        $in_author = true;
      }
      elseif($item['tag'] == 'author' && $item['type'] == 'close')
      {
        $in_author = false;
      }

      # Saving something?
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

    # No author? No name? No way!
    if(empty($theme_info['author']) || empty($theme_info['name']))
    {
      return false;
    }
  }
  else
  {
    # Woops, that's not right!
    return false;
  }

  # Add the path, just incase :P
  $theme_info['path'] = realpath($path);
  $theme_info['directory'] = $theme_info['path'];

  # Alright, here ya go.
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
  global $theme_dir;

  # Doesn't exist?!
  if(!file_exists($theme_dir) || !is_dir($theme_dir))
  {
    return false;
  }

  # Get all the directories.
  $ls = scandir($theme_dir);

  $list = array();
  foreach($ls as $path)
  {
    # Skip ., .. and .svn.
    if(in_array($path, array('.', '..', '.svn')))
    {
      continue;
    }

    # Only look in directories, they are themes if they have the
    # implemented_theme.class.php file.
    if(is_dir($theme_dir. '/'. $path) && file_exists($theme_dir. '/'. $path. '/implemented_theme.class.php') && file_exists($theme_dir. '/'. $path. '/theme.xml'))
    {
      $list[] = realpath($theme_dir. '/'. $path);
    }
  }

  # Whether or not there were any themes found, return the array.
  return $list;
}
?>