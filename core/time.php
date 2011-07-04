<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

if(!defined('INSNOW'))
{
  die('Nice try...');
}

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
    $return = null;
    api()->run_hooks('timeformat', array(&$return, &$timestamp, &$format, &$today_yesterday));

    // Did the hooks do anything?
    if(!empty($return))
    {
      return $return;
    }

    // Is the format acceptable?
    $format = strtolower($format);
    if(!in_array($format, array('datetime', 'date', 'time')))
    {
      return false;
    }

    // No timestamp specified? We will use the current time then!
    if(empty($timestamp))
    {
      $timestamp = time_utc();
    }

    // Want to change the time, perhaps? Timezone, maybe? :P
    $timestamp = api()->apply_filters('timeformat_timestamp', $timestamp);

    // Do you want that fancy Today at or Yesterday at stuff? : )
    if(!empty($today_yesterday))
    {
      // We need to get the current time.
      $cur_time = api()->apply_filters('timeformat_timestamp', time_utc());

      // Get useful information.
      $cur_time = getdate($cur_time);
      $supplied = getdate($timestamp);

      // Is it today?
      $is_today = $supplied['yday'] == $cur_time['yday'] && $supplied['year'] == $cur_time['year'];

      // How about yesterday?
      $is_yesterday = !$is_today && ($supplied['yday'] == $cur_time['yday'] - 1 && $supplied['year'] == $cur_time['year']) || ($cur_time['yday'] == 0 && $supplied['year'] == $cur_time['year'] - 1 && $supplied['mday'] == 31 && $supplied['mon'] == 12);

      // Was it within a few hours ago?
      if($is_today && abs($supplied[0] - $cur_time[0]) <= 10800)
      {
				return time_diff($supplied[0], $cur_time[0]);
      }
      if($is_today || $is_yesterday)
      {
        // For the date format, we just return Today or Yesterday ;)
        return '<strong>'. ($is_today ? l('Today') : l('Yesterday')). '</strong>'. ($format != 'date' ? ' '. l('at'). ' '. strftime(settings()->get('time_format', 'string', '%I:%M:%S %p'), $timestamp) : '');
      }
    }

    // Nothing special, huh?
    return strftime(settings()->get($format. '_format', 'string'), $timestamp);
  }
}

/*
	Function: time_diff

	Calculates the difference between two timestamps, in English, that is.

	Parameters:
		int $timestamp - The timestamp for which you are calculating a ifference
										 from $from.
		int $from - The base timestamp (if not supplied, the current timestamp
								will be used).

	Returns:
		string - Returns the difference between the supplied timestamps.

	Note:
		This function is implemented in English, but if you are creating a
		translation plugin, you can easily hook into time_diff_translate which
		will pass three things: hours, minutes, seconds.

		Please note that this will only return the difference in hours, minutes,
		and seconds, but not days, weeks, months, etc.
*/
function time_diff($timestamp, $from = null)
{
	if($from === null)
	{
		$from = api()->apply_filters('timeformat_timestamp', time_utc());
	}

	$difference = abs($timestamp - $from);

	// First, the hours!
	$hours = floor($difference / (double)3600);

	// Take out all the hours.
	$difference = $difference % 3600;

	// Minutes!
	$minutes = floor($difference / (double)60);

	// Now take out all the minutes.
	$difference = $difference % 60;

	// And seconds...
	$seconds = $difference;

	// Hmmm, so do you want to format this?
	$return = null;
	api()->run_hooks('time_diff_translate', array(&$return, &$hours, &$minutes, &$seconds));

	if(empty($return))
	{
		$return = array();

		// Any hours?
		if($hours > 0)
		{
			$return[] = $hours. ' hour'. ($hours == 1 ? '' : 's');
		}

		// Minutes?
		if($minutes > 0)
		{
			$return[] = $minutes. ' minute'. ($minutes == 1 ? '' : 's');
		}

		// Seconds..?
		if($seconds > 0)
		{
			$return[] = $seconds. ' second'. ($seconds == 1 ? '' : 's');
		}

		// Well, is the difference nothing?
		if(count($return) == 0)
		{
			return 'Just now';
		}
		else
		{
			// Gotta do a couple things, maybe.
			if(count($return) > 1)
			{
				$last_item = $return[count($return) - 1];
				unset($return[count($return) - 1]);

				return implode(', ', $return). ' and '. $last_item. ' ago';
			}
			else
			{
				return $return[0]. ' ago';
			}
		}
	}
	else
	{
		return $return;
	}
}
?>