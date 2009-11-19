<?php
#
# Admin Help English file for SnowCMS
#   Created by the SnowCMS Dev Team
#         www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $user;

# Admin popup stuff
$l['popup_admin_title'] = 'Help';
$l['popup_admin_close'] = 'Close window';
$l['popup_admin_invalid'] = 'Invalid help.';

# Admin time format
$l['popup_admin_timedateformat_title'] = 'Time and Date Format Help';
$l['popup_admin_timedateformat_desc'] = 'Here are some variables you can use for your date and time format:

<tt class="medium">YYYY</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full year (e.g. '. calculate_time('YYYY', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">YY</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short year (e.g. '. calculate_time('YY', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MMMM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full textual month (e.g. '. calculate_time('MMMM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MMM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short textual month (e.g. '. calculate_time('MMM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit month (e.g. '. calculate_time('MM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">M</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit month (e.g. '. calculate_time('M', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DDDD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full textual day (e.g. '. calculate_time('DDDD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DDD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short textual day (e.g. '. calculate_time('DDD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit day (e.g. '. calculate_time('DD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">D</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit day (e.g. '. calculate_time('D', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Dt</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit day (e.g. '. calculate_time('Dt', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">HH</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit 24-hours (e.g. '. calculate_time('HH', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">H</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit 24-hours (e.g. '. calculate_time('H', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">hh</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit 12-hours (e.g. '. calculate_time('hh', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">h</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit 12-hours (e.g. '. calculate_time('h', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">mm</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Minutes (e.g. '. calculate_time('mm', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">ss</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Seconds (e.g. '. calculate_time('ss', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">P</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Uppercase AM/PM (e.g. '. calculate_time('P', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">p</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Lowercase am/pm (e.g. '. calculate_time('p', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Z</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Timezone offset (e.g. '. calculate_time('Z', time_utc() + $user['timezone'] * 60). ')

Place a backslash (<tt class="medium">\</tt>) before a character to get the literal text.

e.g. <tt class="medium">DD\D</tt> might output '. calculate_time('DD\D', time_utc() + $user['timezone'] * 60);
$l['popup_admin_dateformat_title'] = 'Date Format Help';
$l['popup_admin_dateformat_desc'] = 'Here are some variables you can use for your date format:

<tt class="medium">YYYY</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full year (e.g. '. calculate_time('YYYY', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">YY</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short year (e.g. '. calculate_time('YY', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MMMM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full textual month (e.g. '. calculate_time('MMMM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MMM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short textual month (e.g. '. calculate_time('MMM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit month (e.g. '. calculate_time('MM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">M</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit month (e.g. '. calculate_time('M', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DDDD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full textual day (e.g. '. calculate_time('DDDD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DDD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short textual day (e.g. '. calculate_time('DDD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit day (e.g. '. calculate_time('DD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">D</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit day (e.g. '. calculate_time('D', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Dt</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit day (e.g. '. calculate_time('Dt', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Z</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Timezone offset (e.g. '. calculate_time('Z', time_utc() + $user['timezone'] * 60). ')

Place a backslash (<tt class="medium">\</tt>) before a character to get the literal text.

e.g. <tt class="medium">DD\D</tt> might output '. calculate_time('DD\D', time_utc() + $user['timezone'] * 60);
$l['popup_admin_timeformat_title'] = 'Time Format Help';
$l['popup_admin_timeformat_desc'] = 'Here are some variables you can use for your time format:

<tt class="medium">HH</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit 24-hours (e.g. '. calculate_time('HH', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">H</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit 24-hours (e.g. '. calculate_time('H', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">hh</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit 12-hours (e.g. '. calculate_time('hh', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">h</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit 12-hours (e.g. '. calculate_time('h', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">mm</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Minutes (e.g. '. calculate_time('mm', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">ss</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Seconds (e.g. '. calculate_time('ss', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">P</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Uppercase AM/PM (e.g. '. calculate_time('P', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">p</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Lowercase am/pm (e.g. '. calculate_time('p', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Z</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Timezone offset (e.g. '. calculate_time('Z', time_utc() + $user['timezone'] * 60). ')

Place a backslash (<tt class="medium">\</tt>) before a character to get the literal text.

e.g. <tt class="medium">HH\H</tt> might output '. calculate_time('HH\H', time_utc() + $user['timezone'] * 60);

# General popup stuff
$l['popup_title'] = 'Help';
$l['popup_close'] = 'Close window';
$l['popup_invalid'] = 'Invalid help.';

# General time format
$l['popup_timedateformat_title'] = 'Time and Date Format Help';
$l['popup_timedateformat_desc'] = 'Here are some variables you can use for your date and time format:

<tt class="medium">YYYY</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full year (e.g. '. calculate_time('YYYY', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">YY</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short year (e.g. '. calculate_time('YY', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MMMM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full textual month (e.g. '. calculate_time('MMMM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MMM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short textual month (e.g. '. calculate_time('MMM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit month (e.g. '. calculate_time('MM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">M</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit month (e.g. '. calculate_time('M', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DDDD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full textual day (e.g. '. calculate_time('DDDD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DDD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short textual day (e.g. '. calculate_time('DDD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit day (e.g. '. calculate_time('DD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">D</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit day (e.g. '. calculate_time('D', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Dt</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit day (e.g. '. calculate_time('Dt', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">HH</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit 24-hours (e.g. '. calculate_time('HH', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">H</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit 24-hours (e.g. '. calculate_time('H', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">hh</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit 12-hours (e.g. '. calculate_time('hh', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">h</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit 12-hours (e.g. '. calculate_time('h', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">mm</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Minutes (e.g. '. calculate_time('mm', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">ss</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Seconds (e.g. '. calculate_time('ss', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">P</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Uppercase AM/PM (e.g. '. calculate_time('P', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">p</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Lowercase am/pm (e.g. '. calculate_time('p', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Z</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Timezone offset (e.g. '. calculate_time('Z', time_utc() + $user['timezone'] * 60). ')

Place a backslash (<tt class="medium">\</tt>) before a character to get the literal text.

e.g. <tt class="medium">DD\D</tt> might output '. calculate_time('DD\D', time_utc() + $user['timezone'] * 60);
$l['popup_dateformat_title'] = 'Date Format Help';
$l['popup_dateformat_desc'] = 'Here are some variables you can use for your date format:

<tt class="medium">YYYY</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full year (e.g. '. calculate_time('YYYY', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">YY</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short year (e.g. '. calculate_time('YY', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MMMM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full textual month (e.g. '. calculate_time('MMMM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MMM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short textual month (e.g. '. calculate_time('MMM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">MM</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit month (e.g. '. calculate_time('MM', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">M</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit month (e.g. '. calculate_time('M', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DDDD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Full textual day (e.g. '. calculate_time('DDDD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DDD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Short textual day (e.g. '. calculate_time('DDD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">DD</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit day (e.g. '. calculate_time('DD', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">D</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit day (e.g. '. calculate_time('D', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Dt</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit day (e.g. '. calculate_time('Dt', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Z</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Timezone offset (e.g. '. calculate_time('Z', time_utc() + $user['timezone'] * 60). ')

Place a backslash (<tt class="medium">\</tt>) before a character to get the literal text.

e.g. <tt class="medium">DD\D</tt> might output '. calculate_time('DD\D', time_utc() + $user['timezone'] * 60);
$l['popup_timeformat_title'] = 'Time Format Help';
$l['popup_timeformat_desc'] = 'Here are some variables you can use for your time format:

<tt class="medium">HH</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit 24-hours (e.g. '. calculate_time('HH', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">H</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit 24-hours (e.g. '. calculate_time('H', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">hh</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Double digit 12-hours (e.g. '. calculate_time('hh', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">h</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Single digit 12-hours (e.g. '. calculate_time('h', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">mm</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Minutes (e.g. '. calculate_time('mm', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">ss</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Seconds (e.g. '. calculate_time('ss', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">P</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Uppercase AM/PM (e.g. '. calculate_time('P', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">p</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Lowercase am/pm (e.g. '. calculate_time('p', time_utc() + $user['timezone'] * 60). ')
<tt class="medium">Z</tt>&nbsp;&nbsp;-&nbsp;&nbsp;Timezone offset (e.g. '. calculate_time('Z', time_utc() + $user['timezone'] * 60). ')

Place a backslash (<tt class="medium">\</tt>) before a character to get the literal text.

e.g. <tt class="medium">HH\H</tt> might output '. calculate_time('HH\H', time_utc() + $user['timezone'] * 60);
?>