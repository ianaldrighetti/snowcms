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
  Function: plugin_load

  Loads a plugins information from the plugin.ini file. This should be
  used instead of manually loading a plugins information through
  parse_ini_file. This is because the format or location of the plugin
  information could change, and by using this, no major changes should
  have to occur when a schema change occurs.

  Parameters:
    string $plugin_id - The plugins identifier, this could be the path
                        to the root directory of the plugin (if is_path
                        is true), or the dependency name of the plugin.
    bool $is_path - If the plugin_id is a path, set this to true, if it
                    is a depedency name, this should be false.

  Returns:
    array - Returns an array containing the plugins information, false
            if the plugin was not found.

  Note:
    Here are the following indexes in the array returned:
      string dependency - The plugins dependency name.
      string name - The name of the plugin.
      string author - The author of the plugin.
      string version - The version of the plugin.
      array dependencies - An array containing all of the plugins dependencies.
      string description - The description of the plugin.
      string path - The root directory of the plugin.
*/
function plugin_load($plugin_id, $is_path = true)
{
  # Is it a path? Make sure it exists...
  if(empty($plugin_id) || (!empty($is_path) && (!file_exists($plugin_id) || !is_dir($plugin_id) || !file_exists($plugin_id. '/plugin.php') || !file_exists($plugin_id. '/plugin.ini'))))
  {
    return false;
  }
  # A dependency name? That's fine, but we need the path.
  elseif(empty($is_path))
  {
    # Get all the plugins, and attempt to interpret the depedency name into
    # an actual path ;)
    $list = plugin_list();

    # No plugins? Then it definitely doesn't exist.
    if(count($list) > 0)
    {
      foreach($list as $path)
      {
        # Load the plugins informaion with <plugin_load> and check to see
        # if the dependency name matches :-).
        $plugin = plugin_load($path);

        if($plugin['dependency'] == $plugin_id)
        {
          # Found it! Just return it's information now.
          return $plugin;
        }
      }
    }

    # Still running? Then we didn't find it!
    return false;
  }

  # Simple enough, load the plugin.ini file.
  $ini = parse_ini_file($plugin_dir. '/plugin.ini', true);

  # Now return the information.
  return array(
            'dependency' => $ini['plugin']['dependency name'],
            'name' => $ini['plugin']['plugin name'],
            'author' => $ini['plugin']['author'],
            'version' => $ini['plugin']['version'],
            'dependencies' => !empty($ini['plugin']['dependencies']) ? explode('|', $ini['plugin']['dependencies']) : false,
            'description' => $ini['plugin']['description'],
            'path' => realpath($plugin_id),
          );
}

/*
  Function: plugin_list

  Finds and returns an array containing plugins in the plugin directory.

  Parameters:
    none

  Returns:
    array - Returns an array containing all the current plugin paths, false
            if the plugin directory does not exist.

  Note:
    In order for a plugin to be detected, the plugins directory must contain
    a plugin.ini and plugin.php file.
*/
function plugin_list()
{
  global $plugin_dir;

  # Does the plugin directory not exist for some strange reason?
  if(!file_exists($plugin_dir) || !is_dir($plugin_dir))
  {
    return false;
  }

  # Scan the plugins directory.
  $ls = scandir($plugin_dir);

  $list = array();
  foreach($ls as $file)
  {
    # Skip the ., .. and .svn folders.
    if(in_array($file, array('.', '..', '.svn')))
    {
      continue;
    }

    # Only look in directories, of course! Then check and see if
    # plugin.php and plugin.ini exists.
    if(is_dir($plugin_dir. '/'. $file) && file_exists($plugin_dir. '/'. $file. '/plugin.php') && file_exists($plugin_dir. '/'. $file. '/plugin.ini'))
    {
      # Yup, it was a valid (or most likely valid :-P) plugin.
      $list[] = realpath($plugin_dir. '/'. $file);
    }
  }

  # Return the list, whether or not there are any.
  return $list;
}
?>