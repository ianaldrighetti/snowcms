<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# This includes some more core things SnowCMS might need, but not as much
# so they are put here, just to keep CoreCMS.php tidy :)
#
# void cache_save(string $key, mixed $value[, int $ttl = 120);
#   string $key - The associated key with which the data will be saved
#   mixed $data - The data to be stored... Any kind of data can be stored
#                 whether it be an int, double, array, and so on, except
#                 if NULL is supplied, and if an item has been cached with
#                 the key supplied, it will be deleted!
#   int $ttl - How long this data should be considered "fresh" otherwise
#              when you try to get it and if it is expired its thrown out.
#              Default is 120... seconds :P
#   NOTE: Currently supported cache systems are APC and eAccelerator, hope
#         to add more :) also file based caching works as well :D
#
# mixed cache_get(string $key);
#   string $key - The key you used to store the data... which was associated
#                 with it.
#   returns mixed - The type returned really depends upon the type stored.
#                   However, if NULL is returned, that means the cache $key
#                   does not exist.
#
# void cache_remove([string $key = false]);
#   !!!
#
# string pagination_create(string $tpl_url, int &$start, int $num_items[, int $per_page = 10]);
#   string $tpl_url - The beginning URL which will have the paginated number attached to
#                       so if you enter like http://you.com/index.php?act=something it would 
#                       add stuff to the URL like ;start=1 
#   int &$start - The starting point, in other words, put in the page that is currently set
#                 NOTE: This will also edit the variable (makes an alias) and it will change
#                 the variable to be put into a LIMIT query...
#   int $num_items - The maximum number of items... So like the maximum number of messages, posts, etc.
#   int $per_page - The number of items that will be shown per page, 10 is default
#   returns string - Returns the pagination. Oh yeah! :P
#
# string rand_string([int $length = 6]);
#   int $length - The length of the random string that you want to be created.
#   returns string - Returns the random string generated.
#
# bool update_settings(array $update_settings);
#   array $update_settings - The settings to be udpated in the settings table
#                           and you can have multiple settings updated, the format
#                           would look like this:
#                           array('SettingName' => 'SettingValue', 'AnotherSetting' => 'AnotherValue')
#                           NOTE: If the setting doesn't exist it will be created!
#   returns bool - TRUE is issued if it was a success, otherwise FALSE.
#
# void update_settings_close();
#   !!!
#
# string web_fetch(string $url[, string $post_data = ''[, int $num_redirects = 0]]);
#   string $url - The url to go to and fetch the data... such as http://www.snowcms.com/
#   string $post_data - The POST data to send while accessing the page, should look like this:
#                       var=something&anotherVar=somethingElse
#   int $num_redirects - You should never set this... this is used by the function itself
#                        for recursive calls when the Location: parameter is found in the header.
#   returns string - Returns the string of data... If it failed, false or null will be issued.
#   NOTE: This function can use cURL or fsockopen(), however, cURL is the first to be checked
#         as it is the SnowCMS developers preference... ;) (Personally, I think fsockopen is very
#         very hard to use, its slow too... It took me days to get the fsockopen side to work and
#         mere seconds for cURL, lol.)
#
# string return_avatar(string $avatar[, int $member_id = 0]);
#   string $avatar - The string of avatar information.
#                    (This is usually obtained through the members table column avatar)
#   int $member_id - The ID of the member of who you are displaying the avatar from.
#
#   returns string - Returns the URL to the avatar. Whether it be remote or local.
#
# array settings_prepare(array $edit_settings[, array $data = $settings]);
#   array $edit_settings - The settings you want to be displayed
#                          and of course editable by the user.
#   array $data - An array with the data for the all fields stored in it.
#   returns array - Returns an array of all the prepared necessities
#                   for displaying the settings ^^
#
# void settings_save(array $save_settings[, string $update_function = 'update_settings']);
#   array $save_settings - The settings you want to be saved to the
#                          database. This function is made to be used
#                          along side prepareSettings!
#   string $update_function - The function that handles the updating in
#                             the database of the settings. It will be
#                             passed an array which keys are variable/field
#                             names and values are variable/field values.
#   returns void - Nothing is returned by this function.
#
# array settings_field(array $field, string $l_prefix);
#
# string get_os();
#   returns the operating system running SnowCMS.
#
# float timezone_get(int $timezone, int $dst);
#   int $timezone - The ID of the timezone
#   int $dst - Daylight savings time settings.
#                0 - Off
#                1 - On
#                2 - Auto detect
#   - Returns the time difference worked out from its attributes.
#
# float time_utc();
#   - Returns the time in UTC.
#
# void redirect([string $uri = null[, bool $in_bound = true]]);
#   string $uri - The relative url to redirect to, $base_url is automatically added before $uri
#   bool $in_bound - If you want to go elsewhere besides this SnowCMS site, set it to false
#                    and you can give it the full url in $uri's place.
#   returns nothing
#
# string censor_text(string $str);
#   !!! Not done yet...
#
# array create_list(array $list[, bool $and = true]);
#   - Create a textual list from the array of items using language files.
#   array $list - An array of strings as the items in the list.
#   returns the textual list.
#   bool $and - Whether or not to finish with 'and' or another language equivalent.
#
# string email_hide(string $email);
#   - Modifies the email to be hidden from spambots. e.g.
#     webmaster@example.com becomes webmast..@example.com
#   string $email - The email address to hide
#   returns string - The hidden email address
#
# string numberformat(mixed $number[, int $decimals = null[, bool $thousands = true]]);
#
# string timeformat([int $timestamp = 0[, int $what = 0[, float $timezone = $user['timezone'][, bool $no_today_yesterday = false]]]]);
#   int $timestamp - The 10 integer timestamp gotten from the time_utc(); function,
#                    a note that if this is left blank the current time will be used.
#   int $what - What format to use:
#                 0 - Time and date
#                 1 - Date only
#                 2 - Time only
#   float $timezone - The timezone to use as a float (e.g. 10.5 = GMT+10:30)
#   bool $no_today_yesterday - Whether or not to output whether it happened Today + timeformat or
#                              Yesterday + timeformat. If it did happen today or yesterday, of course.
#   returns string - This will return a readable and formatted time string depending
#                    upon what the user has set
#
# string calculate_time(string $format, int $time[, bool $dates = true[, bool $times = true[, bool $timezones = true]]]);
#
# string entities(string $str[, string $quote_style = ENT_QUOTES]);
#
# string nbsp(string $str);
#
# bool check_time(int $year, int $month, int $day[, int $hour[, int $minute[, int $second]]]);
#
# void is_user_moderator(int $board_id);
#

function cache_save($key, $value, $ttl = 120)
{
  global $cache_dir, $settings;

  # First off, is caching enabled..?
  if(empty($settings['cache_enabled']))
    # Nope, return false.
    return false;

  # Lets make the key all funky and long like...
  $key = sha1($key. $settings['scmsVersion']). 'SCMS';

  if($settings['cache_type'] == 'eaccelerator' && function_exists('eaccelerator_put'))
  {
    # eAccelerator eh? Good choice :P
    
    # I guess eAccelerator doesn't remove expired stuff itself >.>
    eaccelerator_gc();
    
    # Deleting..?
    if($value === null)
      @eaccelerator_rm($key);
    else
      eaccelerator_put($key, serialize($value), $ttl);

    # It was done.
    return true;
  }
  elseif($settings['cache_type'] == 'apc' && function_exists('apc_store'))
  {
    # Using APC? www.php.net/apc :P
    if($value === null)
      # Not an array, so i take it delete?
      apc_delete($key);
    else
      apc_store($key, serialize($value), $ttl);

    # It was stored :)
    return true;
  }
  elseif($settings['cache_type'] == 'file')
  {
    # Just good ol' files :)
    # So deleting..?
    if($value === null && is_writable($cache_dir. '/'. $key. '_cache.php'))
    {
      @unlink($cache_dir. '/'. $key. '_cache.php');
      return true;
    }
    else
    {
      # Lets try to make the file and write to it.
      $fp = @fopen($cache_dir. '/'. $key. '_cache.php', 'w+');

      # Success :?
      if($fp)
      {
        # Lock it, I call it! :D
        @flock($fp, LOCK_EX);

        # So write it to the file...
        fwrite($fp, '<?php if(!defined(\'InSnow\')) { die; } else { $kill = '. (time_utc() + $ttl). '; $value = '. var_export($value, true). '; } ?>');

        # Ok, you can have it back...
        @flock($fp, LOCK_UN);
        @fclose($fp);

        # We did it!
        return true;
      }
    }
  }

  # Nothing was done? o-O
  return false;
}

function cache_get($key)
{
  global $cache_dir, $settings;

  # We need these...
  if(empty($settings['cache_enabled']))
    return false;

  # We must make the key all funky like how we stored it or else
  # we really can't get it out can we? :P
  $key = sha1($key. $settings['scmsVersion']). 'SCMS';

  if($settings['cache_type'] == 'eaccelerator' && function_exists('eaccelerator_get'))
    $value = eaccelerator_get($key);
  elseif($settings['cache_type'] == 'apc' && function_exists('apc_fetch'))
    $value = apc_fetch($key);
  elseif($settings['cache_type'] == 'file')
  {
    # This is the biggest one XD
    # Does it exist?
    if(file_exists($cache_dir. '/'. $key. '_cache.php'))
    {
      # Get it.
      require($cache_dir. '/'. $key. '_cache.php');

      # Expired..?
      if($kill < time_utc() && is_writable($cache_dir) && is_writable($cache_dir. '/'. $key. '_cache.php'))
      {
        # Ya... kill it >:D
        $value = null;
        unlink($cache_dir. '/'. $key. '_cache.php');
      }
    }
    else
      # Set value as null
      $value = null;
  }
  
  # File based caching doesn't need to be unserialized :D
  $return = $settings['cache_type'] == 'file' ? $value : @unserialize($value);

  # Is it an array..?
  if($return !== null)
    return $return;
  else
    # FAIL!
    return null;
}

function cache_remove($key = false)
{
  global $cache_dir, $settings;

  # Not enabled..? Not set? Well, we can't go on ;)
  if(empty($settings['cache_enabled']))
    return false;

  # Make our key if it isn't false... If it is false,
  # leave it alone ;) It means we want to remove everything.
  if($key !== false)
    $key = sha1($key. $settings['scmsVersion']). 'SCMS';

  # eAccelerator? =D
  if($settings['cache_type'] == 'eaccelerator' && function_exists('eaccelerator_get'))
  {
    if($key !== false)
      # Simple... Remove it.
      eaccelerator_rm($key);
    else
    {
      # Want to remove everything? Fine with me, but just incase
      # you have other things cached from other systems, lets only
      # remove things from SnowCMS :D
      $keys = eaccelerator_list_keys();

      if(count($keys))
        foreach($keys as $key)
        {
          # The string must be at least 44 characters long, since
          # sha1 is 40 characters long and SCMS is, of course 4...
          if(mb_strlen($key) < 44)
            continue;

          # Make sure its got SCMS at the end.
          if(mb_substr($key, mb_strlen($key) - 4, mb_strlen($key)) == 'SCMS')
            # Yup, remove it!
            eaccelerator_rm($key);
        }
    }
  }
  # APC? ^___^ Enabled in PHP6 by default :D
  elseif($settings['cache_type'] == 'apc' && function_exists('apc_fetch'))
  {
    # Just removing one thing..? ^__^
    if($key !== false)
      # Easy!
      apc_delete($key);
    else
    {
      # Well... From my research, I can't get the cached keys...
      # If anyone does know, tell me XD. For now, we will empty
      # the whole user cache. :P
      apc_clear_cache('user');
    }
  }
  elseif($settings['cache_type'] == 'file')
  {
    # One file...
    if($key !== false)
      @unlink($cache_dir. '/'. $key. '_cache.php');
    else
    {
      # Everything in this directory is, or should be, from this
      # CMS, so we will remove everything, of course, except the
      # index.php file and the .htaccess file XD.
      $files = scandir($cache_dir);

      if(count($files))
      {
        foreach($files as $file)
        {
          # Its gotta be a file! And not .htaccess or index.php
          if(is_file($cache_dir. '/'. $file) && !in_array($file, array('.', '..', '.htaccess', 'index.php')))
            # Yay! Delete :)
            @unlink($cache_dir. '/'. $key. '_cache.php');
        }
      }
    }
  }
}

function pagination_create($tpl_url, &$start, $num_items, $per_page = 10)
{
  global $l;
  
  # So how many pages total..?
  $total_pages = ceil((int)($num_items == 0 ? 1 : $num_items) / (int)$per_page);

  # Make sure start is an integer... At least make it one.
  $start = (int)$start;

  # We can't have a page less then one,
  # or greater then total_pages ;)
  if($start < 1)
    $start = 1;
  elseif($start > $total_pages)
    $start = $total_pages;

  # So start... Make an array holding all our stuffs.
  $index = array();

  # So the << First :) Though we may not link it
  # if we are on the first page.
  $index[] = '<span class="pagination_first">'. ($start != 1 ? '<a href="'. $tpl_url. '">' : ''). $l['pagination_first']. ($start != 1 ? '</a>' : ''). '</span>';

  # Now the < which is the previous one... Don't link
  # it if thats where we are :P
  $index[] = '<span class="pagination_prev">'. ($start != 1 ? '<a href="'. (($start - 1) > 1 ? $tpl_url. ';page='. ($start - 1) : $tpl_url). '">' : ''). $l['pagination_prev']. ($start != 1 ? '</a>' : ''). '</span>';

  # So now the page numbers...
  if($total_pages < 6)
  {
    # Hmm... Less then 5 :P
    $page_start = 1;
    $page_end = $total_pages;
  }
  elseif($start - 2 < 1)
  {
    # We are gonna go from 1 to 5 ;)
    $page_start = 1;
    $page_end = 5;
  }
  elseif($start + 2 <= $total_pages)
  {
    # Somewhere in between...
    $page_start = $start - 2;
    $page_end = $start + 2;
  }
  else
  {
    # The end of the line...
    # Some weird buggy that needs fixing...
    $page_start = ($start == ($total_pages - 1) ? $start - 3 : $start - 4);
    $page_end = $total_pages;
  }

  # So now that we have our numbers, for loop :D
  for($page = $page_start; $page < ($page_end + 1); $page++)
  {
    # So add the page number... Also, don't link the page number
    # if thats where we are at ;) oh, ya and, don't add ;page=
    # to the end of our template url if its page one :)
    $index[] = '<span class="pagination_page'. ($page == $start ? ' pagination_current' : ''). '">'. ($page != $start ? '<a href="'. ($page != 1 ? $tpl_url. ';page='. $page : $tpl_url). '">' : ''). numberformat($page). ($page != $start ? '</a>' : ''). '</span>';
  }

  # Almost done :D!
  # So add the > which is the next one ;)
  # Don't link it if thats our current page...
  $index[] = '<span class="pagination_next">'. ($start < $total_pages ? '<a href="'. $tpl_url. ';page='. ($start + 1). '">' : ''). $l['pagination_next']. ($start < $total_pages ? '</a>' : ''). '</span>';

  # Now the Last >> Of course, don't link it if thats where we are.
  $index[] = '<span class="pagination_last">'. ($start < $total_pages ? '<a href="'. $tpl_url. ';page='. $total_pages. '">' : ''). $l['pagination_last']. ($start < $total_pages ? '</a>' : ''). '</span>';

  # And we are done with the hard stuffs, yay.
  # So before we implode the stuff, take away 1 from
  # start, then multiply it by per_page. What for? For LIMIT clauses :D
  $start = ($start - 1) * $per_page;

  # Return it imploded...
  return implode(' ', $index);
}

function rand_string($length = 6)
{
  # No 0 or less then that! ^(._.)^
  if($length < 1 || !is_int($length))
    return false;

  # Lets create a random string shall we?
  # So here are the characters to work with in an array :)
  $chars = 'cT9DIP8H!?3Liu*0#Ug1k-Rp^&KMj4ZQlbxszoOe$m52w_7y~r%ht6vVNGgnEfWSaYFCJqXA@dB';

  $randString = '';

  # Now begin to form the random string
  for($i = 0; $i < $length; $i++)
    $randString .= $chars[mt_rand(0, 74)];

  # Return it, and we are good :)
  return $randString;
}

function update_settings($update_settings, $entities = false)
{
  global $db, $page, $settings;

  # Lets see... Did you even give us any..?
  if(count($update_settings))
  {
    # Okay good :)
    # Now we only actually update the stuff in the database once per page load...
    # But we will update the $settings array on this page right now :P
    $tmp_settings = array();

    if(!isset($page['update_settings']))
      $page['update_settings'] = array();

    foreach($update_settings as $key => $value)
    {
      # You might want to auto increment it. We will, only if its an integer!
      if(($value === '++' || $value === '--') && (!isset($settings[$key]) || (string)$settings[$key] == (string)(int)$settings[$key]))
      {
        if(!isset($settings[$key]))
          # Not set, but meh.
          $tmp_settings[$key] = $value == '++' ? 1 : -1;
        else
          $tmp_settings[$key] = $value == '++' ? (int)$settings[$key] + 1 : (int)$settings[$key] - 1;
      }
      else
        # Just set it, thats all... Maybe entities(), if set... :)
        $tmp_settings[$key] = $entities ? entities($value) : $value;
    }

    # Merge a couple things :P
    $settings = array_merge($settings, $tmp_settings);
    $page['update_settings'] = array_merge($page['update_settings'], $tmp_settings);

    # We did it. Well, maybe not yet :P
    return true;
  }

  return false;
}

function update_settings_close()
{
  global $db, $page;

  # Any settings that need updating?
  if(isset($page['update_settings']) && count($page['update_settings']))
  {
    # Oh yeah! There is :D
    $update_settings = array();
    foreach($page['update_settings'] as $key => $value)
      $update_settings[] = array($key, $value);

    # Do the database thing :P
    $db->insert('replace', $db->prefix. 'settings',
      array(
        'variable' => 'string-255', 'value' => 'text',
      ),
      $update_settings,
      array('variable'));
  }
}

function web_fetch($url, $post_data = '', $num_redirects = 0)
{
  # Parse the url.
  $parsed = parse_url($url);

  # So, cURL or the old fsockopen?
  if(function_exists('curl_init'))
  {
    # Phew! cURL is SO much better.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP/SnowCMS');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

    # Hmm, maybe a custom port? Could be HTTPS (443) or FTP (21).
    if($parsed['scheme'] == 'https')
      curl_setopt($ch, CURLOPT_PORT, 443);
    elseif($parsed['scheme'] == 'ftp')
      curl_setopt($ch, CURLOPT_PORT, 21);

    # Post data?
    if(!empty($post_data))
    {
      # Post data, yup yup.
      curl_setopt($ch, CURLOPT_POST, true);

      # Now, make the data into an array.
      $data = @explode('&', $post_data);
      $post_data = array();
      foreach($data as $value)
      {
        $value = @explode('=', $value);
        $post_data[$value[0]] = !empty($value[1]) ? $value[1] : '';
      }

      # Now send the data...
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }

    # And we are done, execute and return!
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }
  else
  {
    $fp = fsockopen(($parsed['scheme'] == 'https' ? 'ssl://' : ''). $parsed['host'], $parsed['scheme'] == 'https' ? 443 : 80, $errno, $errstr, 5);

    if(empty($fp))
      return false;
    elseif($num_redirects > 5)
      return null;

    # What are you requesting?
    $request_path = (!empty($parsed['path']) ? $parsed['path'] : '/'). (!empty($parsed['query']) ? '?'. $parsed['query'] : '');

    # No post data? Fine...
    if(empty($post_data))
    {
      $commands = "GET $request_path HTTP/1.1\r\n";
      $commands .= "Host: {$parsed['host']}\r\n";
      $commands .= "User-agent: PHP/SnowCMS\r\n";
      $commands .= "Connection: close\r\n\r\n";
    }
    else
    {
      $commands = "POST $request_path HTTP/1.1\r\n";
      $commands .= "Host: {$parsed['host']}\r\n";
      $comments .= "User-agent: PHP/SnowCMS\r\n";
      $commands .= "Content-type: application/x-www-form-urlencoded\r\n";
      $commands .= "Content-length: ". mb_strlen($post_data). "\r\n";
      $commands .= "Connection: close\r\n\r\n";
      $commands .= $post_data. "\r\n\r\n";
    }

    # Send all our request stuffs to the server.
    fwrite($fp, $commands);

    # Now start to get the data! :)
    $data = '';
    while(!feof($fp))
      $data .= fgets($fp, 4096);
    fclose($fp);

    # Let's read the headers...
    @list($raw_headers, $data) = explode("\r\n\r\n", $data, 2);

    # Analyze the status :)
    @list($http_status, $raw_headers) = explode("\r\n", $raw_headers, 2);

    # Load the headers into an easy to access array :D
    $headers = array();
    $raw_headers = explode("\r\n", $raw_headers);
    foreach($raw_headers as $header)
    {
      $header = trim($header);
      if(empty($header) || mb_strpos($header, ':') === false)
        continue;

      @list($name, $content) = explode(':', $header, 2);
      $headers[strtolower($name)] = trim($content);
    }

    # Need to redirect..?
    if(mb_strpos($http_status, '302') !== false || mb_strpos($http_status, '301') !== false || mb_strpos($http_status, '307') !== false)
      return !empty($headers['location']) ? web_fetch($headers['location'], $post_data, $num_redirects + 1) : false;

    # Okay, this is really weird... But if transfer-encoding is chunked, then
    # we need to do some extra stuff... Otherwise we can just return the content :)
    if(empty($headers['transfer-encoding']) || mb_strtolower($headers['transfer-encoding']) != 'chunked')
      return $data;

    # Get the hexidecimal do-dad...
    @list($hexdec, $data) = explode("\r\n", $data, 2);

    return substr($data, 0, hexdec($hexdec));
  }
}

function return_avatar($avatar, $member_id = 0)
{
  global $base_url;
  
  # Is it an avatar URL?
  if(preg_match('/^[a-z]+:\/\//i',$avatar))
    return $avatar;
  # Is it an uploaded avatar?
  elseif(preg_match('/^uploaded-(?:bmp|gif|png|jpg)$/i', $avatar))
  {
    $ext = preg_replace('/^uploaded-(bmp|gif|png|jpg)$/i', '$1', $avatar);
    return $base_url. '/index.php?action=avatar;u='. $member_id. ';ext=.'. $ext;
  }
  # Is it empty?
  elseif(empty($avatar))
    return null;
  # Okay, it must be from the collection then
  else
    return $base_url. '/index.php?action=avatar;collection='. $avatar;
}

function settings_prepare($fields, $data, $l_prefix = '')
{
  global $base_url, $db, $l, $page, $settings, $user;
  
  # Go through each field one at a time.
  foreach($fields as $key => $field)
  {
    # Sort out the field's data.
    $field = settings_field($field, $l_prefix);
    
    # Skip this one if 'show' is false.
    if(!$field['show'])
    {
      # Get rid of this one.
      unset($fields[$key]);
      
      # And continue with the next.
      continue;
    }
    
    # Get the disabled HTML
    $disabled = $field['disabled'] ? ' disabled="disabled"' : '';
    
    # Get the title HTML
    $title = $field['title'] ? ' title="'. $field['title']. '"' : '';
    
    # If it's a listbox with an 'other' option, we need to add our own JavaScript
    if($field['type'] == 'select' && $field['other'])
    {
      if(!empty($field['events']['change']))
        $field['events']['change'] = 'select_other(this.id); '. $field['events']['change'];
      else
        $field['events']['change'] = 'select_other(this.id);';
      if(!empty($field['events']['keyup']))
        $field['events']['keyup'] = 'select_other(this.id); '. $field['events']['change'];
      else
        $field['events']['keyup'] = 'select_other(this.id);';
    }
    
    # Get the JavaScript events HTML
    $events = '';
    foreach($field['events'] as $event => $javascript)
    {
      $events .= ' on'. $event. '="'. entities($javascript). '"';
    }
    
    # Get the default $value
    $field['value'] = isset($data[$field['variable']]) ? $data[$field['variable']] : null;
    
    # Check for any additional formatting or validation to the default value
    switch($field['type'])
    {
      # Integers need to be typecasted
      case 'int':
        $field['value'] = (string)(int)$field['value'] == $field['value'] ? $field['value'] : '';
        break;
      # Double floats need to be typecasted
      case 'double':
        $field['value'] = (string)(double)$field['value'] == $field['value'] ? $field['value'] : '';
        break;
      # Times need to be formated as YYYY-MM-DD HH:mm:ss.
      case 'time':
        $field['value'] = (string)(int)$field['value'] == $field['value'] ? calculate_time('YYYY-MM-DD HH:mm:ss', $field['value']) : entities($field['value']);
        break;
      # Checkboxes are typecasted as booleans
      case 'checkbox':
        $field['value'] = (bool)$field['value'];
        break;
      # Listboxes need to be validated with their options
      case 'select':
        # If 'other' is specified any value is allowed. Otherwise we have to check it against the allowed options array
        if($field['other'])
          $field['value'] = entities($field['value']);
        else
          $field['value'] = in_array($field['value'], array_keys($field['options'])) ? $field['value'] : null;
        break;
      # Multiple checkboxes and listboxes that can have multiple values selected also need to be validated with their options
      case 'checkbox_multi': case 'select_multi':
        $field['value'] = explode(',', $field['value']);
        break;
      # Dates need to be split into an array
      case 'date':
        $field['value'] = explode('-', $field['value']);
        $field['value'] = array(
          # This little substr() trick pads small numbers with zeros, e.g. 5 -> 05
          'year' => !empty($field['value'][0]) ? substr('000'. (int)$field['value'][0], -4) : '',
          'month' => !empty($field['value'][1]) ? substr('0'. (int)$field['value'][1], -2) : '',
          'day' => !empty($field['value'][2]) ? substr('0'. (int)$field['value'][2], -2) : '',
        );
        break;
      # Dimensions need to be split into an array
      case 'dimensions':
        $field['value'] = explode('x', $field['value']);
        $field['value'] = array(
          'x' => !empty($field['value'][0]) ? (int)$field['value'][0] : '',
          'y' => !empty($field['value'][1]) ? (int)$field['value'][1] : '',
        );
        break;
      # We'll just encode HTML entities for all other types
      default:
        $field['value'] = $field['value'] ? entities($field['value']) : '';
    }
    
    # Get the $input HTML, dependent on $type
    switch($field['type'])
    {
      # Text, integers, double floats and times all use textboxes
      case 'text': case 'int': case 'double': case 'time':
        $field['input'] = '<input type="text" name="'. $field['name']. '" id="'. $field['name']. '" class="'. $field['type']. $field['class']. '" value="'. $field['value']. '"'. $disabled. $title. $events. ' />';
        break;
      # Passwords use password textboxes
      case 'password':
        $field['input'] = '<input type="password" name="'. $field['name']. '" id="'. $field['name']. '" class="password'. $field['class']. '"'. $disabled. $title. $events. ' />';
        break;
      # Textareas use textareas
      case 'textarea':
        $field['input'] = '<textarea name="'. $field['name']. '" id="'. $field['name']. '" class="textarea'. $field['class']. '"'. $disabled. $title. $events. ' cols="50" rows="6">'. $field['value']. '</textarea>';
        break;
      # Checkboxes use checkboxes
      case 'checkbox':
        $field['input'] = '<input type="checkbox" name="'. $field['name']. '" id="'. $field['name']. '" class="checkbox'. $field['class']. '" value="1"'. ($field['value'] ? ' checked="checked"' : ''). $disabled. $title. $events. ' />';
        break;
      # Multiple checkboxes use, well, multiple checkboxes
      case 'checkbox_multi':
        # No values yet
        $field['input'] = '';
        
        # Go through each checkbox, one at a time
        foreach($field['options'] as $value => $label)
        {
          # Check if this option is the default one
          if(in_array($value, $field['value']))
            $field['input'] .= ' <input type="checkbox" name="'. $field['name']. '_'. entities($value). '" id="'. $field['name']. '_'. entities($value). '" class="checkbox_multi'. $field['class']. '" value="1" checked="checked" /> <label for="'. $field['name']. '_'. entities($value). '">'. $label. '</label>';
          else
            $field['input'] .= ' <input type="checkbox" name="'. $field['name']. '_'. entities($value). '" id="'. $field['name']. '_'. entities($value). '" class="checkbox_multi'. $field['class']. '" value="1" /> <label for="'. $field['name']. '_'. entities($value). '">'. $label. '</label>';
        }
        
        # Remove the first space
        $field['input'] = substr($field['input'], 1);
        break;
      # Listboxes use <select>
      case 'select':
        # Add the start of the <select>
        $field['input'] = '<select name="'. $field['name']. '" id="'. $field['name']. '" class="select'. $field['class']. '"'. $disabled. $title. $events. '>';
        
        # Add the options
        foreach($field['options'] as $value => $label)
        {
          # Check if this option is the default one
          if($field['value'] == $value)
            $field['input'] .= '<option value="'. entities($value). '" selected="selected">'. $label. '</option>';
          else
            $field['input'] .= '<option value="'. entities($value). '">'. $label. '</option>';
        }
        
        # If the 'other' argument is given, add an 'other' option
        if($field['other'])
        {
          # If the defualt value isn't in the array, select the 'other' option by default
          if(!array_key_exists($field['value'], $field['options']))
            $field['input'] .= '<option value="-other-" selected="selected">Other</option>';
          else
            $field['input'] .= '<option value="-other-">Other</option>';
        }
        
        # Add the end of the <select>
        $field['input'] .= '</select>';
        
        # Add an 'other' option, if specified
        if($field['other'])
        {
          # If the value is in the options, we need to use JavaScript to
          # enter the default value in the other textbox. This is to help
          # handle the process for people with JavaScript disabled.
          if(in_array($field['value'], array_keys($field['options'])))
            $field['input'] .= '<br /><input type="text" name="'. $field['name']. '_other" id="'. $field['name']. '_other" class="select_other'. $field['class']. '"'. $disabled. $title. $events. ' /><script type="text/javascript">document.getElementById(\''. $field['name']. '_other\').value = \''. $field['value']. '\';</script>';
          else
            $field['input'] .= '<br /><input type="text" name="'. $field['name']. '_other" id="'. $field['name']. '_other" class="select_other'. $field['class']. '" value="'. $field['value']. '"'. $disabled. $title. $events. ' />';
        }
        break;
      # Listboxes that support multiple values also use <select>
      case 'select_multi':
        # Add the start of the <select>
        $field['input'] = '<select name="'. $field['name']. '" id="'. $field['name']. '" class="select_multi'. $field['class']. '" multiple="multiple"'. $disabled. $title. $events. '>';
        
        # Add the options
        foreach($field['options'] as $value => $label)
        {
          # Check if this option is the default one
          if(in_array($value, $field['value']))
            $field['input'] .= '<option value="'. entities($value). '" selected="selected">'. $label. '</option>';
          else
            $field['input'] .= '<option value="'. entities($value). '">'. $label. '</option>';
        }
        
        # Add the end of the <select>
        $field['input'] .= '</select>';
        break;
      # Dates use three textboxes in the format yyyy-mm-dd
      case 'date':
        # Year, doesn't have _year in id attribute so that clicking the label will give it focus
        $field['input'] = '<input type="text" name="'. $field['name']. '[]" id="'. $field['name']. '" class="date year'. $field['class']. '" value="'. ((int)$field['value']['year'] ? $field['value']['year'] : ''). '"'. $disabled. $title. $events. ' />-'.
        # Month
                          '<input type="text" name="'. $field['name']. '[]" id="'. $field['name']. '_month" class="date month'. $field['class']. '" value="'. ((int)$field['value']['month'] ? $field['value']['month'] : ''). '"'. $disabled. $title. $events. ' />-'.
        # Day
                          '<input type="text" name="'. $field['name']. '[]" id="'. $field['name']. '_day" class="date day'. $field['class']. '" value="'. ((int)$field['value']['day'] ? $field['value']['day'] : ''). '"'. $disabled. $title. $events. ' />';
        break;
      # Dimensions use two textboxes with an x in the middle (e.g. 100x100)
      case 'dimensions':
        # X, doesn't have _x in id attribute so that clicking the label will give it focus
        $field['input'] = '<input type="text" name="'. $field['name']. '[]" id="'. $field['name']. '" class="dimensions x'. $field['class']. '" value="'. $field['value']['x']. '"'. $disabled. $title. $events. ' />'. $l['dimensions_separator'].
        # Y
                          '<input type="text" name="'. $field['name']. '[]" id="'. $field['name']. '_y" class="dimensions y'. $field['class']. '" value="'. $field['value']['y']. '"'. $disabled. $title. $events. ' />';
        break;
      # Separators don't have any input, because they are handled by the theme
      default:
        $field['input'] = '';
    }
    
    # Update the changes.
    $fields[$key] = $field;
  }
  
  return $fields;
}

function settings_save($fields, $save_function = 'update_settings', $l_prefix = '')
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;
  
  # If $page['errors'] isn't defined yet, define it.
  $page['errors'] = isset($page['errors']) ? (array)$page['errors'] : array();
  
  # This is the actual array errors will go in, and at the end of the script, they'll be moved in $page['errors'].
  $errors = array();
  
  # Go through each field one at a time.
  foreach($fields as $key => $field)
  {
    # Sort out the field's data.
    $field = settings_field($field, $l_prefix);
    
    # Skip this one if 'show' is false. And we don't need separators either.
    if(!$field['show'] || $field['type'] == 'separator')
    {
      # Get rid of this one.
      unset($fields[$key]);
      
      # And continue with the next.
      continue;
    }
    
    # Get the field's value.
    $field['value'] = isset($_POST[$field['name']]) ? $_POST[$field['name']] : '';
    
    # Validate with a function, if applicable.
    if($field['validate'])
    {
      # Yeah, it returns an error (if applicable), if it wants to change the value it will have to parse by reference.
      if($error = $field['validate']($field))
        $errors[] = $error;
    }
    # No validate function? Then let's validate with more conventional means.
    else
    {
      switch($field['type'])
      {
        # Text boxes, passwords and textareas need their HTML entities encoded.
        case 'text': case 'password': case 'textarea':
          # Check if the text is too long (and should be truncated).
          if(!is_null($field['max']) && mb_strlen($field['value']) > $field['max'] && $field['truncate'])
            $field['value'] = mb_substr($field['value'], 0, $field['max']);
          # Check if the text is too long.
          elseif(!is_null($field['max']) && mb_strlen($field['value']) > $field['max'])
            $errors[] = $field['errors']['max'];
          # Check if the text is too short.
          elseif(!is_null($field['min']) && mb_strlen($field['value']) < $field['min'])
            $errors[] = $field['errors']['min'];
          # If HTML isn't suppose to be preserved, encode it with entities.
          if(!$field['html'])
            $field['value'] = entities($field['value']);
          break;
        # Integers need to be typecasted.
        case 'int':
          # Typecast it to an integer.
          $field['value'] = (int)$field['value'];
          # Check if the integer is too long (and if 'truncate' is set, decrease it).
          if(!is_null($field['max']) && $field['value'] > $field['max'] && $field['truncate'])
            $field['value'] = $field['max'];
          # Check if the integer is too long.
          elseif(!is_null($field['max']) && $field['value'] > $field['max'])
            $errors[] = $field['errors']['max'];
          # Check if the integer is too short (and if 'truncate' is set, increase it).
          elseif(!is_null($field['min']) && $field['value'] < $field['min'] && $field['truncate'])
            $field['value'] = $field['min'];
          # Check if the integer is too short.
          elseif(!is_null($field['min']) && $field['value'] < $field['min'])
            $errors[] = $field['errors']['min'];
          break;
        # Double floats need to be typecasted.
        case 'double':
          # Typecast it to a double float.
          $field['value'] = (double)$field['value'];
          # Check if the float is too long (and if 'truncate' is set, decrease it).
          if(!is_null($field['max']) && $field['value'] > $field['max'] && $field['truncate'])
            $field['value'] = $field['max'];
          # Check if the float is too long.
          elseif(!is_null($field['max']) && $field['value'] > $field['max'])
            $errors[] = $field['errors']['max'];
          # Check if the float is too short (and if 'truncate' is set, increase it).
          elseif(!is_null($field['min']) && $field['value'] < $field['min'] && $field['truncate'])
            $field['value'] = $field['min'];
          # Check if the float is too short.
          elseif(!is_null($field['min']) && $field['value'] < $field['min'])
            $errors[] = $field['errors']['min'];
          break;
        # Times need to be interpreted and changed to integers.
        case 'time':
          # Check if the date is valid.
          if(preg_match('/^\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{2}:\d{2}$/', $field['value']))
          {
            # Get the parts of the date.
            $date = preg_split('/[- :]/', $field['value']);
            # Validate the date and time
            if(check_time($date[0], $date[1], $date[2], $date[3], $date[4], $date[5]))
              $field['value'] = strtotime($field['value']);
            # Invalid date, so set it to zero.
            else
              $field['value'] = 0;
          }
          # Invalid date, so set it to zero.
          else
            $field['value'] = 0;
          # Check if the time is too new (and if 'truncate' is set, decrease it).
          if(!is_null($field['max']) && $field['value'] > $field['max'] && $field['truncate'])
            $field['value'] = $field['max'];
          # Check if the time is too new.
          elseif(!is_null($field['max']) && $field['value'] > $field['max'])
            $errors[] = $field['errors']['max'];
          # Check if the time is too old (and if 'truncate' is set, increase it).
          elseif(!is_null($field['min']) && $field['value'] < $field['min'] && $field['truncate'])
            $field['value'] = $field['min'];
          # Check if the time is too old.
          elseif(!is_null($field['min']) && $field['value'] < $field['min'])
            $errors[] = $field['errors']['min'];
          break;
        # Checkboxes are typecasted as booleans.
        case 'checkbox':
          $field['value'] = (bool)$field['value'];
          break;
        # Listboxes need to be validated with their options.
        case 'select':
          # If 'other' is specified and it is selected, use the other textbox for its value instead.
          if($field['other'] && $field['value'] == '-other-')
          {
            # Set the value to the 'other' field's value.
            $field['value'] = $_POST[$field['name']. '_other'];
            # Check if the text is too long (and should be truncated).
            if(!is_null($field['max']) && mb_strlen($field['value']) > $field['max'] && $field['truncate'])
              $field['value'] = mb_substr($field['value'], 0, $field['max']);
            # Check if the text is too long.
            elseif(!is_null($field['max']) && mb_strlen($field['value']) > $field['max'])
              $errors[] = $field['errors']['max'];
            # Check if the text is too short.
            elseif(!is_null($field['min']) && mb_strlen($field['value']) < $field['min'])
              $errors[] = $field['errors']['min'];
            # If HTML isn't suppose to be preserved, encode it with entities.
            if(!$field['html'])
              $field['value'] = entities($field['value']);
          }
          else
            $field['value'] = in_array($field['value'], array_keys($field['options'])) ? $field['value'] : '';
          break;
        # Dates need to be imploded (because they're arrays).
        case 'date':
          # Get the parts of the date.
          $year = isset($field['value'][0]) ? (int)$field['value'][0] : 0;
          $month = isset($field['value'][1]) ? (int)$field['value'][1] : 0;
          $day = isset($field['value'][2]) ? (int)$field['value'][2] : 0;
          # Is it valid (and a complete date)?
          if(check_time($year, $month, $day))
            $field['value'] = substr('000'. $year, -4). '-'. substr('0'. $month, -2). '-'. substr('0'. $day, -2);
          # Okay, so it isn't complete, are we allowed to have a partial date?
          elseif(!$field['complete'])
          {
            # Is it just a year?
            if($year && !$month && !$day)
              $field['value'] = substr('000'. $year, -4). '-00-00';
            # A month and day then?
            elseif(!$year && $month && $day)
              $field['value'] = '0000-'. substr('0'. $month, -2). '-'. substr('0'. $day, -2);
             else
               $field['value'] = '';
           }
           else
             $field['value'] = '';
          break;
        # Dimensions need to be imploded (because they're arrays).
        case 'dimensions':
          if(count($field['value']) == 2)
            $field['value'] = (int)$field['value'][0]. 'x'. (int)$field['value'][1];
          else
            $field['value'] = '';
          break;
        # If the type isn't recognised, dump it (for security reasons).
        default:
          unset($fields[$key]);
          # And continue with the next.
          continue;
      }
    }
    
    # Update the changes.
    $fields[$key] = $field;
  }
  
  # Validate the value with a 'match' regex, if applicable.
  if($field['match'] && !preg_match($field['match'], $field['value']))
    $errors[] = $field['errors']['match'];
  
  # Check if there are any errors and add them to the error array.
  if($errors)
    $page['errors'] += $errors;
  # No errors, so save.
  else
  {
    # The fields to save, with keys as variables and values as, well, values. To be filled later.
    $fields_save = array();
    
    # Go through each field, one at time (yes, again).
    foreach($fields as $field)
    {
      # Run the callback (if applicable).
      if($field['callback'])
        $field['callback']($field);
      
      # Add to another array, if it's suppose to be saved.
      if($field['save'])
        $fields_save[$field['variable']] = $field['value'];
    }
    
    # Save the variables.
    if($fields_save)
      $save_function($fields_save);
  }
  
  return $fields;
}

function settings_field($field, $l_prefix = '')
{
  global $l;
  
  # Define the allowed types
  $types = array(
    'text',
    'password',
    'int',
    'double',
    'time',
    'textarea',
    'checkbox',
    'checkbox_multi',
    'select',
    'select_multi',
    'date',
    'dimensions',
    'separator',
  );
  
  # Set the default variable
  $field['variable'] = isset($field['variable']) ? (string)$field['variable'] : '';
  
  # Validate the type
  if(empty($field['type']) || !in_array($field['type'], $types))
    $field['type'] = '';
  
  # Default $name to $variable
  $field['name'] = isset($field['name']) ? (string)$field['name'] : $field['variable'];
  
  # Default $label to $l[$l_prefix. 'setting_'. $name]
  $field['label'] = isset($field['label']) ? (string)$field['label'] : (isset($l[$l_prefix. 'setting_'. $field['name']]) ? $l[$l_prefix. 'setting_'. $field['name']] : '');
  
  # Default $subtext to nothing so it doesn't error
  if(empty($field['subtext']))
    $field['subtext'] = '';
  # If $subtext is true set $subtext to $l[$l_prefix. 'setting_'. $name. '_subtext']
  elseif($field['subtext'] === true)
    $field['subtext'] = isset($l[$l_prefix. 'setting_'. $field['name']. '_subtext']) ? $l[$l_prefix. 'setting_'. $field['name']. '_subtext'] : '';
  else
    $field['subtext'] = (string)$field['subtext'];
  
  # Default $title to nothing so it doesn't error
  if(empty($field['title']))
    $field['title'] = '';
  # If $title is true set $subtext to $l[$l_prefix. 'setting_'. $name. '_title']
  elseif($field['label'] === true)
    $field['title'] = isset($l[$l_prefix. 'setting_'. $field['name']. '_title']) ? $l[$l_prefix. 'setting_'. $field['name']. '_title'] : '';
  else
    $field['title'] = (string)$field['title'];
  
  # Make sure these don't error when called later.
  $field['class'] = isset($field['class']) ? ' '. $field['class'] : '';
  $field['postfix'] = isset($field['postfix']) ? (string)$field['postfix'] : '';
  $field['tags'] = isset($field['tags']) ? (array)$field['tags'] : array();
  $field['popup'] = isset($field['popup']) ? (bool)$field['popup'] : false;
  $field['max'] = isset($field['max']) ? (int)$field['max'] : null;
  $field['min'] = isset($field['min']) ? (int)$field['min'] : null;
  $field['complete'] = isset($field['complete']) ? (bool)$field['complete'] : true;
  $field['truncate'] = isset($field['truncate']) ? (bool)$field['truncate'] : false;
  $field['html'] = isset($field['html']) ? (bool)$field['html'] : false;
  $field['other'] = isset($field['other']) ? (bool)$field['other'] : false;
  $field['options'] = isset($field['options']) ? (array)$field['options'] : array();
  $field['validate'] = isset($field['validate']) ? (string)$field['validate'] : '';
  $field['callback'] = isset($field['callback']) ? (string)$field['callback'] : '';
  $field['match'] = isset($field['match']) ? (string)$field['match'] : '';
  $field['save'] = isset($field['save']) ? (bool)$field['save'] : true;
  $field['disabled'] = isset($field['disabled']) ? (bool)$field['disabled'] : false;
  $field['show'] = isset($field['show']) ? (bool)$field['show'] : true;
  $field['events'] = isset($field['events']) ? (array)$field['events'] : array();
  
  # Sort out some error messages.
  $field['errors']['match'] = isset($field['match_error']) ? (string)$field['match_error'] : (isset($l[$l_prefix. 'setting_'. $field['name']. '_error_match']) ? $l[$l_prefix. 'setting_'. $field['name']. '_error_match'] : '');
  $field['errors']['max'] = isset($field['match_error']) ? (string)$field['match_error'] : (isset($l[$l_prefix. 'setting_'. $field['name']. '_error_max']) ? $l[$l_prefix. 'setting_'. $field['name']. '_error_max'] : '');
  $field['errors']['min'] = isset($field['match_error']) ? (string)$field['match_error'] : (isset($l[$l_prefix. 'setting_'. $field['name']. '_error_min']) ? $l[$l_prefix. 'setting_'. $field['name']. '_error_min'] : '');
  
  # Return the modified field.
  return $field;
}

function get_os()
{
  # Check if we're dealing with Windows.
  if(strtolower(substr(PHP_OS, 0, 3)) == 'win')
  {
    # Chek if there's a version string and if so, save it in $ver.
    if($ver = @shell_exec('ver'))
    {
      # Get the version number from the version string.
      $version = explode('.', preg_replace('/^.*\[.*?([0-9.]+)\].*$/s', '$1', $ver));
      
      # Get the build number.
      $build = isset($version[2]) ? $version[2] : 0;
      
      # The version pack version number.
      $sp = isset($version[3]) ? $version[3] : $build;
      
      # The version and build number in the one string.
      $version_build = (isset($version[0]) ? (int)$version[0] : 0). '.'. (isset($version[1]) ? (int)$version[1] : 0). '.'. substr($build, 0, 1);
      
      # The version number, without the build number
      $version = (isset($version[0]) ? (int)$version[0] : 0). '.'. (isset($version[1]) ? (int)$version[1] : 0);
      
      # Get the possible Windows version names.
      $versions = array(
        '1.01' => 'Windows 1.01',
        '2.03' => 'Windows 2.03',
        '2.11' => 'Windows 2.11',
        '3.0' => 'Windows 3.0',
        '3.1' => 'Windows 3.1',
        '3.11' => 'Windows 3.11',
        '3.2' => 'Windows 3.2',
        '3.5' => 'Windows NT 3.5',
        '3.51' => 'Windows NT 3.51',
        '4.0' => 'Windows 95',
        '4.0.1' => 'Windows NT 4.0',
        '4.10' => 'Windows 98',
        '4.10.2' => 'Windows 98 SE',
        '4.90' => 'Windows ME',
        '5.0' => 'Windows 2000',
        '5.1' => 'Windows XP',
        '5.2' => 'Windows Server 2003',
        '5.2.4' => 'Windows Home Server',
        '6.0' => 'Windows Vista',
        '6.1' => 'Windows 7',
      );
      
      # Check for Vista SP1
      if($version == '6.0' && $sp == '6001')
        $sp = ' SP1';
      # Vista SP2
      elseif($version == '6.0' && $sp == '6002')
        $sp = ' SP2';
      # Check for XP SP1
      elseif(($version == '5.1' || $version == '5.2') && ($sp == '1089' || $sp == '1070'))
        $sp = ' SP1';
      # XP SP2
      elseif(($version == '5.1' || $version == '5.2') && $sp == '2180')
        $sp = ' SP2';
      # XP SP3
      elseif(($version == '5.1' || $version == '5.2') && $sp == '5512')
        $sp = ' SP3';
      # Okay, so no service pack.
      else
        $sp = '';
      
      if(isset($versions[$version_build]))
        return $versions[$version_build];
      elseif(isset($versions[$version]))
      {
        if($version == '5.2' && (isset($_ENV['PROCESSOR_ARCHITECTURE']) ? $_ENV['PROCESSOR_ARCHITECTURE'] : 'x86') != 'x86')
          return 'Windows XP 64-bit'. $sp;
        elseif($version == '6.0' && (isset($_ENV['PROCESSOR_ARCHITECTURE']) ? $_ENV['PROCESSOR_ARCHITECTURE'] : 'x86') != 'x86')
          return 'Windows Vista 64-bit'. $sp;
        else
          return $versions[$version]. $sp;
      }
      else
        return 'Windows '. $version. $sp;
    }
    # We can't determine the particular Windows version, so let's check if it's just a generic Windows NT.
    elseif(strtolower(PHP_OS) == 'winnt')
      return 'Windows NT';
    # Okay, then it's just a generic Windows.
    else
      return 'Windows';
  }
  else
    return PHP_OS;
}

function timezone_get($timezone, $dst = true)
{
  # First, we need an array of the timezones without DST
  $timezones = array(
    -12, -11, -10, -9, -8, -8, -7, -7, -7, -6, -6, -6, -6, -6, -5, -5,
    -5, -4.5, -4, -4, -4, -4, -3.5, -3, -3, -3, -3, -3, -2, -1, -1, 0,
    0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3,
    3, 3, 3, 3, 3.5, 4, 4, 4, 4.5, 5, 5, 5, 5.5, 5.5, 5.5, 5.75, 6, 6,
    6.5, 7, 7, 8, 8, 8, 8, 8, 8, 9, 9, 9, 9.5, 9.5, 10, 10, 10, 10,
    10, 11, 11, 12, 12, 13,
  );
  
  # If DST isn't on auto, add it to the timezone offset and return
  if($dst != 2)
    return $timezones[$timezone] + ($dst % 2);
  
  # Next, an array of DST settings
  #
  #   0 - No DST
  #   1 - Starts last Sunday of March, ends last Sunday of October
  #   2 - Starts first Sunday of October, ends third Sunday of March
  #   3 - Erratic, so let's just say no DST, sorry :P
  #   4 - Starts first Sunday of October, ends first Sunday of April
  #   5 - Starts third Sunday of October, ends third Sunday of February
  #   6 - Starts second Sunday of March, ends first Sunday of November
  #   7 - Varies, so let's just say no DST, sorry again :P
  #   8 - Starts second Sunday of March, ends first Sunday of October
  #   9 - Starts last Sunday of March, ends last Sunday of October
  #
  # Don't you just love DST?
  $dst = array(
    0, 0, 0, 6, 6, 9, 0, 9, 9, 6, 6, 9, 9, 0, 0, 6, 6, 0, 0 /*Canada/Atlantic*/,
    0, 0, 7, 6, 0 /*Brazil/West*/, 3, 0, 1, 0 /*America/Montevideo*/, 0, 1, 0, 0,
    1, 0, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 0 /*Asia/Beirut*/, 0, 0, 1, 1,
    0 /*Asia/Jerusalem*/, 1, 0 /*Africa/Windhoek*/, 0, 0, 1, 1, 0, 0,
    0 /*Asia/Tehran*/, 0, 1, 1, 0, 1, 0, 0, 0, 0, 0 /*Asia/Calcutta*/, 0, 0, 0,
    0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 4, 0, 0, 4, 0, 4, 1, 1, 1, 0, 1, 0,
  );
  
  # Get month
  $month = (int)strftime('%m', time_utc() + $timezones[$timezone] * 60);
  # Get weekday (0 = Sunday, 6 = Saturday)
  $weekday = (int)strftime('%w', time_utc() + $timezones[$timezone] * 60);
  # Get day of month
  $dayofmonth = (int)strftime('%d', time_utc() + $timezones[$timezone] * 60);
  # Get last Sunday, divided by seven (a.k.a. number of Sundays this month)
  $num_sundays = floor(($dayofmonth - $weekday) / 7);
  
  # Now we're going to have to check the DST
  switch($dst[$timezone])
  {
    case 1: case 9:
      # DST starts last Sunday of March and ends last Sunday of October
      # Check if month is a month that's always not DST
      if(in_array($month, array(1, 2, 11, 12)))
        return $timezones[$timezone];
      # Check if month is a month that's always DST
      elseif(in_array($month, array(4, 5, 6, 7, 8, 9)))
        return $timezones[$timezone] + 1;
      # Check if month is March (When DST starts)
      elseif($month == 3)
      {
        # Check if it's at least the last Sunday of the month
        if($num_sundays + 7 > 31)
          return $timezones[$timezone] + 1;
        else
          return $timezones[$timezone];
      }
      # Then we must be in October (When DST ends)
      else
      {
        # Check if it's at least the last Sunday of the month
        if($num_sundays + 7 > 31)
          return $timezones[$timezone];
        else
          return $timezones[$timezone] + 1;
      }
      break;
    case 2:
      # DST starts first Sunday of October and ends third Sunday of March
      # Check if month is a month that's always not DST
      if(in_array($month, array(4, 5, 6, 7, 8, 9, 10)))
        return $timezones[$timezone];
      # Check if month is a month that's always DST
      elseif(in_array($month, array(1, 2, 12)))
        return $timezones[$timezone] + 1;
      # Check if month is October (When DST starts)
      elseif($month == 3)
      {
        # Check if it's at least the second Sunday
        if($num_sundays >= 1)
          return $timezones[$timezone] + 1;
        else
          return $timezones[$timezone];
      }
      # Then we must be in March (When DST ends)
      else
      {
        # Check if it's at least the first Sunday
        if($num_sundays >= 3)
          return $timezones[$timezone];
        else
          return $timezones[$timezone] + 1;
      }
      break;
    case 4:
      # DST starts first Sunday of October and ends first Sunday of April
      # Check if month is a month that's always not DST
      if(in_array($month, array(5, 6, 7, 8, 9, 10)))
        return $timezones[$timezone];
      # Check if month is a month that's always DST
      elseif(in_array($month, array(1, 2, 3, 12)))
        return $timezones[$timezone] + 1;
      # Check if month is October (When DST starts)
      elseif($month == 3)
      {
        # Check if it's at least the second Sunday
        if($num_sundays >= 1)
          return $timezones[$timezone] + 1;
        else
          return $timezones[$timezone];
      }
      # Then we must be in April (When DST ends)
      else
      {
        # Check if it's at least the first Sunday
        if($num_sundays >= 1)
          return $timezones[$timezone];
        else
          return $timezones[$timezone] + 1;
      }
      break;
    case 5:
      # DST starts third Sunday of October, ends third Sunday of February
      # Check if month is a month that's always not DST
      if(in_array($month, array(3, 4, 5, 6, 7, 8, 9, 10)))
        return $timezones[$timezone];
      # Check if month is a month that's always DST
      elseif(in_array($month, array(1, 12)))
        return $timezones[$timezone] + 1;
      # Check if month is October (When DST starts)
      elseif($month == 3)
      {
        # Check if it's at least the second Sunday
        if($num_sundays >= 3)
          return $timezones[$timezone] + 1;
        else
          return $timezones[$timezone];
      }
      # Then we must be in February (When DST ends)
      else
      {
        # Check if it's at least the first Sunday
        if($num_sundays >= 3)
          return $timezones[$timezone];
        else
          return $timezones[$timezone] + 1;
      }
      break;
    case 6:
      # DST starts second Sunday of March and ends first Sunday of November
      # Check if month is a month that's always not DST
      if(in_array($month, array(1, 2, 12)))
        return $timezones[$timezone];
      # Check if month is a month that's always DST
      elseif(in_array($month, array(4, 5, 6, 7, 8, 9, 10)))
        return $timezones[$timezone] + 1;
      # Check if month is March (When DST starts)
      elseif($month == 3)
      {
        # Check if it's at least the second Sunday
        if($num_sundays >= 2)
          return $timezones[$timezone] + 1;
        else
          return $timezones[$timezone];
      }
      # Then we must be in November (When DST ends)
      else
      {
        # Check if it's at least the first Sunday
        if($num_sundays >= 1)
          return $timezones[$timezone];
        else
          return $timezones[$timezone] + 1;
      }
      break;
    case 8:
      # DST starts second Sunday of March and ends first Sunday of October
      # Check if month is a month that's always not DST
      if(in_array($month, array(1, 2, 11, 12)))
        return $timezones[$timezone];
      # Check if month is a month that's always DST
      elseif(in_array($month, array(4, 5, 6, 7, 8, 9)))
        return $timezones[$timezone] + 1;
      # Check if month is March (When DST starts)
      elseif($month == 3)
      {
        # Check if it's at least the second Sunday
        if($num_sundays >= 2)
          return $timezones[$timezone] + 1;
        else
          return $timezones[$timezone];
      }
      # Then we must be in October (When DST ends)
      else
      {
        # Check if it's at least the first Sunday
        if($num_sundays >= 1)
          return $timezones[$timezone];
        else
          return $timezones[$timezone] + 1;
      }
      break;
    default:
      # No DST
      return $timezones[$timezone];
  }
}

function time_utc()
{
  # Surpress a notice
  if(function_exists('date_default_timezone_set'))
    date_default_timezone_set('UTC');
  
  # Return the current timestamp in UTC
  return time() - date('Z');
}

function redirect($uri = null, $permanent = false, $in_bound = true)
{
  global $base_url;

  # Remove all headers :P
  @ob_clean();
  
  # Is this suppose to be a 301 Moved Permanently?
  if($permanent)
  {
    header('HTTP/1.1 301 Moved Permanently');
  }
  
  # Send the header.
  # But wait, is it in bound? :P
  if(!empty($in_bound) || empty($uri))
    header('Location: '. $base_url. '/'. (!empty($uri) ? $uri : ''));
  else
    header('Location: '. $uri);
  exit;
}

function censor_text($str)
{
  # !!! Needs implementing!!!
  return $str;
}

function create_list($list, $and = true)
{
  global $l;
  
  # Remove empty items and order properly
  $list = array_values(array_filter($list));
  
  # Add the first list item
  $str = isset($list[0]) ? $list[0] : '';
  
  # Only continue to add more items to the list if there is at least one more
  if(isset($list[1]))
  {
    # Add each other list item
    $list_length = count($list);
    for($i = 1; $i < $list_length - 1; $i++)
      $str .= $l['list_separator']. $list[$i];
    
    # Add the last list item
    $str .= ($and ? $l['list_separator_last'] : $l['list_separator']). $list[$i];
  }
  
  # Return the list
  return $str;
}

function email_hide($email)
{
  # Check the length of the username part of the email address
  switch(mb_strpos($email, '@'))
  {
    # Only one character? Just add two dots
    case 1: return preg_replace('/^(.*?)@(.*)$/', '$1..@$2', $email); break;
    # Only two characters? Remove one and add two dots
    case 2: return preg_replace('/^(.*?).{1}@(.*)$/', '$1..@$2', $email); break;
    # Three or more characters? Remove two and replace them with dots
    default: return preg_replace('/^(.*?).{2}@(.*)$/', '$1..@$2', $email);
  }
}

function numberformat($number, $decimals = null, $thousands = true)
{
  global $user;
  
  # Get the decimal point and thousands separators, ready to be replaced in.
  $replacements = array(
    '.' => $user['preference']['decimal_point'],
    # If the thousands separator is a space, make it a non-breaking one (0xa0).
    ',' => $user['preference']['thousands_separator'] != ' ' ? $user['preference']['thousands_separator'] : "\xa0",
  );
  
  # If the amount of decimals is null, calculate the amount in the number and use that instead.
  if(is_null($decimals))
    $decimals = mb_strlen(mb_substr($number, mb_strpos($number. '.', '.') + 1));
  
  # Use numberformat() and then replace the parts in, for UTF-8 support.
  return strtr(number_format($number, $decimals, '.', $thousands ? ',' : ''), $replacements);
}

function timeformat($timestamp = 0, $what = 0, $timezone = null, $no_today_yesterday = false)
{
  global $l, $user;

  # Current timestamp?
  if(empty($timestamp))
    $timestamp = time_utc();

  # Change with timezone
  if(is_null($timezone))
    $timestamp += $user['timezone'] * 3600;
  else
    $timestamp += $timezone * 3600;
  
  # Show today/yesterday..?
  if(empty($no_today_yesterday) && $user['preference']['today_yesterday'] > 0)
  {
    # Still today..?
    $cur_time = time_utc();

    # Timezone needs to be considered :P
    if(is_null($timezone))
      $cur_time += $user['timezone'] * 3600;
    else
      $cur_time += $timezone * 3600;

    $supplied = @getdate($timestamp);
    $cur = @getdate($cur_time);

    # So, is it today..?
    if($supplied['yday'] == $cur['yday'] && $supplied['year'] == $cur['year'])
      return '<strong>'. $l['today']. '</strong> '. $l['at']. ' '. calculate_time($user['format_time'], $timestamp);
    # How about yesterday..? If enabled, of course... (A little more complicated... You know, if it is January 1st...)
    elseif($user['preference']['today_yesterday'] > 1 && ($supplied['yday'] == $cur['yday'] - 1 || $cur['yday'] == 0 && $supplied['year'] == $cur['year'] - 1 && $supplied['mday'] == 31 && $supplied['mon'] == 12))
      return '<strong>'. $l['yesterday']. '</strong> '. $l['at']. ' '. calculate_time($user['format_time'], $timestamp);
  }
  
  # This is easy XD
  return calculate_time($user[!$what ? 'format_datetime' : ($what == 1 ? 'format_date' : 'format_time')], $timestamp);
}

function calculate_time($format, $time, $dates = true, $times = true, $timezones = true)
{
  global $l, $user;
  
  # Get an array of date information about the Unix Timestamp
  $time = getdate($time);
  
  # No replacements yet.
  $replacements = array();
  
  # Should we add dates to the replacments?
  if($dates)
  {
    $replacements += array(
      'YYYY' => $time['year'],
      'YY' => mb_substr($time['year'], -2),
      'MMMM' => $l['month_long_'. $time['mon']],
      'MMM' => $l['month_short_'. $time['mon']],
      'MM' => mb_substr('0'. $time['mon'], -2),
      'M' => $time['mon'],
      'DDDD' => $l['day_long_'. $time['wday']],
      'DDD' => $l['day_short_'. $time['wday']],
      'DD' => mb_substr('0'. $time['mday'], -2),
      'Dt' => $l['th_'. $time['mday']],
      'D' => $time['mday'],
    );
  }
  
  # Should we add times?
  if($times)
  {
    $replacements += array(
      'HH' => mb_substr('0'. $time['hours'], -2),
      'H' => $time['hours'],
      'hh' => mb_substr('0'. (($time['hours'] + 11) % 12 + 1), -2),
      'h' => ($time['hours'] + 11) % 12 + 1,
      'mm' => mb_substr('0'. $time['minutes'], -2),
      'ss' => mb_substr('0'. $time['seconds'], -2),
      'P' => $time['hours'] >= 12 ? 'PM' : 'AM',
      'p' => $time['hours'] >= 12 ? 'pm' : 'am',
    );
  }
  
  # How about timezones?
  if($timezones)
  {
    $replacements += array(
      'Z' => ($user['timezone'] >= 0 ? '+' : ''). floor($user['timezone']). ':'. mb_substr(number_format(($user['timezone'] - floor($user['timezone'])) * 0.6, 2), 2),
    );
  }
  
  # Get numeric HTML entities for HTML special characters
  $entities = array(
    '&' => '&#38;',
    '"' =>  '&#34;',
    '\'' =>  '&#39;',
    '<' =>  '&#60;',
    '>' =>  '&#62;',
  );
  
  # Replace HTML entities with numeric ones
  $format = strtr(html_entity_decode($format), $entities);
  
  # If there are any escape sequences, parse them
  if(substr_count($format, '\\'))
    $format = preg_replace('/\\\\(.)/se', '\'&#\'. ord(\'$1\'). \';\'', $format);
  
  # Replace in the dates and return
  return strtr($format, $replacements);
}

function entities($str, $quote_style = ENT_QUOTES)
{
  # Return the string with special HTML characters changed to HTML entities
  return htmlspecialchars($str, $quote_style, 'UTF-8');
}

function nbsp($str)
{
  return substr(preg_replace('/>(.*? .*?)</e', '\'>\'. str_replace(\' \', \'&nbsp;\', \'$1\'). \'<\'', '>'. $str. '<'), 1, -1);
}

function check_time($year, $month, $day, $hour = null, $minute = null, $second = null)
{
  return checkdate($month, $day, $year);
}

function is_user_moderator($board_id)
{
  global $db, $user;
  static $checked = false;

  # So let's see if you are a moderator or not of the board supplied.
  # That is if you are not a guest or admin. Admins can do ANYTHING anyways.
  if($user['is_logged'] && !$user['is_admin'] && empty($checked))
  {
    $result = $db->query("
      SELECT
        member_id, board_id
      FROM {$db->prefix}moderators
      WHERE member_id = %member_id AND board_id = %board_id
      LIMIT 1",
      array(
        'member_id' => array('int', $user['id']),
        'board_id' => array('int', $board_id),
      ));

    # Anything found? Then you are a board moderator! Congratulations :D
    $user['is_moderator'] = $db->num_rows($result) ? true : false;

    # Being checked once is enough :P!
    $checked = true;
  }
}
?>