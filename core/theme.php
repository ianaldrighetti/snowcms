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
      string name - The name of the theme.
      string author - The of the theme.
      string version - The version of the theme.
      string update_url - The URL of where to download theme updates.
      string description - The description of the theme.
*/
function theme_load($path)
{
  # Doesn't exist? Then we can't load it!
  if(!file_exists($path) || !is_dir($path) || !file_exists($path. '/implemented_theme.class.php') || !file_exists($path. '/theme.ini'))
  {
    return false;
  }

  # Load the information from the theme.ini file.
  $ini = parse_ini_file($path. '/theme.ini');

  return array(
            'name' => $ini['theme']['name'],
            'author' => $ini['theme']['author'],
            'version' => $ini['theme']['version'],
            'update_url' => $ini['theme']['update url'],
            'description' => $ini['theme']['description'],
            'path' => realpath($path),
          );
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
    if(is_dir($theme_dir. '/'. $path) && file_exists($theme_dir. '/'. $path. '/implemented_theme.class.php') && file_exists($theme_dir. '/'. $path. '/theme.ini'))
    {
      $list[] = realpath($theme_dir. '/'. $path);
    }
  }

  # Whether or not there were any themes found, return the array.
  return $list;
}
?>