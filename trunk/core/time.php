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
  Title: Time functions

  Function: time_utc

  Returns the current timestamp in UTC.

  Parameters:
    none

  Returns:
    int - Returns the current timestamp in UTC.

  Note:
    Use this function instead of the <http://www.php.net/time> function
    otherwise when using <timeformat>, the actual time will not be properly
    displayed.
*/
function time_utc()
{
  return time() - date('Z');
}

/*
  Function: timeformat

  Returns a human readable time/date.

  Parameters:
    int $timestamp - A UTC timestamp.
    string $format - Either datetime to include both the date and time,
                     date for only date or time for only time.
    double $timezone - If set, this timezone offset will be used, and not
                       the current members.
    bool $today_yesterday - Set to true if you want the time for be formatted
                            like Today at {TIME} or Yesterday at {TIME}, false
                            if no matter what, to just display a date.

  Returns:
    string - Returns the human readable time/date.

  Note:
    This function is overloadable.
*/
if(!function_exists('timeformat'))
{
  function timeformat($timestamp = 0, $format = 'datetime', $timezone = null, $today_yesterday = true)
  {
    global $api, $member, $settings;

    # No timestamp specified? We will use the current time then!
    if(empty($timestamp))
      $timestamp = time_utc();

    # No timezone set? We will change that ;)
    if(empty($timezone))
      $timezone = $member->is_logged() ? $member->data('timezone', 'double', 0) : $settings->get('timezone', 'double', 0);

    $timestamp += $timezone * 3600;

    # !!!
  }
}
?>