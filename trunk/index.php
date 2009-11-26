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

# Magic quotes, what a joke!!!
if(function_exists('set_magic_quotes_runtime'))
  @set_magic_quotes_runtime(0);

mb_internal_encoding('UTF-8');

# These are some variables that should NOT be set yet!!!
foreach(array('db_class', 'db_result_class') as $variable)
  if(isset($GLOBALS[$variable]))
    unset($GLOBALS[$variable]);

$start_time = microtime();

# We are currently in SnowCMS :)
define('IN_SNOW', true, true);

# We want to see those errors...
error_reporting(E_STRICT | E_ALL);
# config.php not exist? A good sign SnowCMS isn't installed :P
if(!file_exists('config.php'))
{
  header('Location: setup.php');
  exit;
}

require(dirname(__FILE__). '/config.php');

# Now load up the database, very important you know!
require($core_dir. '/database.php');

load_database();

# Now that our database is loaded up, let's get the API started, very important you know?
require($core_dir. '/api.class.php');

# Call on load_api which is in api.class.php :)
load_api();