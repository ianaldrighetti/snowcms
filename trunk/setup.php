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
?>