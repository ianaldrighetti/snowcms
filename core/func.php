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

# Title: Variable functions

/*
  Function: init_func

  init_func initializes the the $func array which contains variable
  functions for handling strings to allow better language support.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function init_func()
{
  global $api, $func;

  $func = array();

  $api->run_hook('post_init_func', array(&$func));
}
?>