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

# Title: Clean Request
# This cleans the requests in magical ways I can't comprehend


# This is a pluggable function, so make sure someone hasn't defined it already...
if(!function_exists('clean_request'))
{
  /*
    Function: clean_request
    I don't know what this does so ask aldo
  */
  function clean_request()
  {
    global $_COOKIE, $_GET, $_POST, $_REQUEST;

    # POST data goes first, then $_GET ;)
    $_REQUEST = array_merge($_POST, $_GET);

    # Remove magic quotes, if it is on...
    if((function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == 1) || @ini_get('magic_quotes_sybase'))
    {
      $_COOKIE = remove_magic($_COOKIE);
      $_GET = remove_magic($_GET);
      $_POST = remove_magic($_POST);
      $_REQUEST = remove_magic($_REQUEST);
    }

    # Don't even try it, okay?
    if(isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS']))
      die('Hacking attempt...');
  }
  /*
    Function: remove_magic
    I don't know what this does so ask aldo
    
    Parameters:
      array $array - Ask aldo
      int $depth - Ask aldo
  */
  function remove_magic($array, $depth = 0)
  {
    if(count($array) == 0)
      return array();
    elseif($depth > 5)
      return $array;

    foreach($array as $key => $value)
    {
      if(!is_array($value))
        $array[stripslashes($key)] = stripslashes($value);
      else
        $array[stripslashes($key)] = remove_magic($value, $depth + 1);
    }

    return $array;
  }
}
?>
