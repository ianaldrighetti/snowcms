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

# Title: Load Database

if(!defined('IN_SNOW'))
  die;

/*
  Function: load_database

  Loads the proper SQL engine class into the $db variable.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function load_database()
{
  global $core_dir, $db, $db_type;

  # Does the right stuff exist? It needs to for this system to run!
  if(file_exists($core_dir. '/engines/'. strtolower($db_type). '.engine.php') && file_exists($core_dir. '/engines/'. strtolower($db_type). '_result.engine.php'))
  {
    # Awesome, they're there! So we can get going now :)
    require_once($core_dir. '/database_result.class.php');
    require_once($core_dir. '/engines/'. strtolower($db_type). '_result.engine.php');
    require_once($core_dir. '/database.class.php');
    require_once($core_dir. '/engines/'. strtolower($db_type). '.engine.php');

    # Well, you should have specified the name of your class and result class, did you?
    if(!empty($db_class) && !empty($db_result_class) && class_exists($db_class) && class_exists($db_result_class))
    {
      $db = new $db_class($db_result_class);

      # Attempt to connect to the database.
      $db->connect();
    }
    else
      die(!empty($db_class) || !empty($db_result_class) ? '$db_class or $db_result_class was not specified in the SQL engine files!' : 'The classes specified in $db_class or $db_result_class were not found!');
  }
  else
    die('Invalid database type supplied in config.php');
}
?>
