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
  function timeformat($timestamp = 0, $format = 'datetime', $today_yesterday = true)
  {
    global $api, $member, $settings;

    $return = null;
    $api->run_hooks('timeformat', array(&$return, $timestamp, $format, $today_yesterday));

    # Did the hooks do anything?
    if(!empty($return))
    {
      return $return;
    }

    # Is the format acceptable?
    $format = strtolower($format);
    if(!in_array($format, array('datetime', 'date', 'time')))
    {
      return false;
    }

    # No timestamp specified? We will use the current time then!
    if(empty($timestamp))
    {
      $timestamp = time_utc();
    }

    # Want to change the time, perhaps? Timezone, maybe? :P
    $timestamp = $api->apply_filters('timeformat_timestamp', $timestamp);

    # Do you want that fancy Today at or Yesterday at stuff? : )
    if(!empty($today_yesterday))
    {
      # We need to get the current time.
      $cur_time_time = $api->apply_filters('timeformat_timestamp', time_utc());

      # Get useful information.
      $cur_time = getdate($cur_time_time);
      $supplied = getdate($timestamp);

      # Is it today?
      $is_today = $supplied['yday'] == $cur_time_time['yday'] && $supplied['year'] == $cur_time_time['year'];

      # How about yesterday?
      $is_yesterday = ($supplied['yday'] == $cur_time['yday'] - 1 && $supplied['year'] == $cur_time_time['year']) || ($cur_time['yday'] == 0 && $supplied['year'] == $cur_time_time['year'] - 1 && $supplied['mday'] == 31 && $supplied['mon'] == 12);

      # So was it today or yesterday?
      if($is_today || $is_yesterday)
      {
        # For the date format, we just return Today or Yesterday ;)
        return '<strong>'. ($is_today ? l('Today') : l('Yesterday')). '</strong>'. ($format != 'date' ? ' '. l('at'). ' '. strftime($settings->get('time_format', 'string', '%I:%M:%S %p'), $timestamp) : '');
      }
    }

    # Nothing special, huh?
    return strftime($settings->get($format. '_format', 'string'), $timestamp);
  }
}
?>