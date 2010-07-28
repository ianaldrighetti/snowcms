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
{
  @set_magic_quotes_runtime(0);
}

# All time/date stuff should be considered UTC, makes life easier!
if(function_exists('date_default_timezone_set'))
{
  date_default_timezone_set('UTC');
}
else
{
  @ini_set('date.timezone', 'UTC');
}

# We are currently in SnowCMS :)
define('IN_SNOW', true, true);

# We want to see those errors...
error_reporting(E_STRICT | E_ALL);

# Remove magic quotes, if it is on...
if((function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == 1) || @ini_get('magic_quotes_sybase'))
{
  $_COOKIE = remove_magic($_COOKIE);
  $_GET = remove_magic($_GET);
  $_POST = remove_magic($_POST);
}

function remove_magic($array, $depth = 5)
{
  # Nothing in the array? No need!
  if(count($array) == 0)
  {
    return array();
  }
  # Exceeded our maximum depth? Just return the array, untouched.
  elseif($depth <= 0)
  {
    return $array;
  }

  foreach($array as $key => $value)
  {
    # Gotta remember that the key needs to have magic quote crud removed
    # as well!
    if(!is_array($value))
    {
      $array[stripslashes($key)] = stripslashes($value);
    }
    else
    {
      $array[stripslashes($key)] = remove_magic($value, $depth - 1);
    }
  }

  return $array;
}
?>