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
# This file will create functions that SnowCMS might need, if your PHP
# version is sufficient for use, so of course, if any functions aren't
# in at least PHP4, you should add them here, and of course the if's
# and what nots to define them if need be.
#

# Hmm... PHP5 is required for file_get_contents and file_put_contents
if(!function_exists('file_get_contents'))
{
  #
  # A note that these functions only add the BASIC
  # functionality of file_get and file_put_contents
  # it does not add those include_paths and what nots
  #
  function file_get_contents($filename, $flags = 0, $context = null, $offset = -1, $maxlen = -1)
  {
    # Just get the files contents.
    $fp = @fopen($filename, 'rb');
    if($fp)
    {
      # The file did exist, so lets return it.
      @flock($fp, LOCK_SH);
      $contents = fread($fp, filesize($filename));
      @flock($fp, LOCK_UN);
      @fclose($fp);
      return $contents;
    }
    else
      # The file didn't exist...
      return false;
  }
  
  function file_put_contents($filename, $data, $flags = 0, $context = null)
  {
    # Save the data to the file.
    $fp = @fopen($filename, 'wb');
    if($fp) {
      # Worked... lets write it!
      @flock($fp, LOCK_EX);
      $written = fwrite($fp, $data);
      @flock($fp, LOCK_UN);
      @fclose($fp);
      return $written;
    }
    else
      # Didn't exist? O.o
      return false;
  }
}

# json_encode(); might be useful to SnowCMS Dev team and others
# just incase they want to use it for the ajax stuff, but its
# really only supported as recent as PHP 5.2 or so.
# So yours truly made his own :D
if(!function_exists('json_encode'))
{
  # Some constant whatever thingys... These aren't implemented,
  # but they are defined just incase ;)
  define('PHP_JSON_HEX_QUOT', 0);
  define('PHP_JSON_HEX_TAG', 0);
  define('PHP_JSON_HEX_AMP', 0);
  define('PHP_JSON_HEX_APOS', 0);

  function json_encode($value, $options = 0)
  {
    # Number..? Thats fine... its just that ;)
    if(is_numeric($value))
      return $value;
    # A string..?
    elseif(is_string($value))
      # Just incased in " ;)
      return '"'. __json_sanitize($value). '"';
    # An array..? :o this could be a biggy
    elseif(is_array($value))
    {
      # So we need to see if this is like a "flat" array,
      # as in, if the array is like: array('something','else')
      # it is "flat", or you could say, a keyless array
      if(__json_flat_array($value))
      {
        # Cool, cool, its flat... so now prepare it...
        $values = array();
        # Get the values...
        foreach($value as $val)
          # Recursion, sorta :P
          $values[] = json_encode($val);
        # Implode and return...
        return '['. implode($values, ','). ']';
      }
      else
      {
        # Now now, this is different, and this array has keys and values
        # So lets loop ;)
        $values = array();
        foreach($value as $key => $val) {
          # This is a bit different, but should be easy...
          $values[] = '"'. __json_sanitize($key). '":'. json_encode($val);
        }
        # Implode and return
        return '{'. implode($values, ','). '}';
      }
    }
    elseif(is_object($value))
      # Sorry, don't support Objects :P
      return false;
  }
  function __json_flat_array($array)
  {
    foreach($array as $key => $value) 
      if(!is_int($key)) 
        return false;

    return true;
  }
  function __json_sanitize($value)
  {
    # Sanitize \ and /
    $value = strtr($value, array('\\' => '\\\\', '/' => '\/'));
    # Now line breaks and what not...
    $value = strtr($value, array("\n" => '\n', "\r" => '\r', "\t" => '\t'));
    # Now escape ONLY " ;)
    $value = addcslashes($value, '"');

    return $value;
  }
}

# scandir is a useful function... but only in PHP5
if(!function_exists('scandir'))
{
  function scandir($directory, $options = 0, $context = null)
  {
    # Its gotta be a directory...
    if(is_dir($directory) && ($dir = @opendir($directory)) !== false)
    {
      $listing = array();
      # Loop and get them ;)
      while(($file = readdir($dir)) !== false)
        $listing[] = $file;

      # Maybe the listing is the other way around? :o
      if($option !== 0)
        $listing = array_reverse($listing);

      return $listing;
    }
    else
      return false;
  }
}

# stripos is PHP5... but we can fix that... (mb_stripos is PHP 5.2 >=)
if(!function_exists('stripos') || !function_exists('mb_stripos'))
{
  function mb_stripos($haystack, $needle, $offset = 0)
  {
    # Lol, we just use mb_strpos :P
    return mb_strpos(mb_strtolower($haystack), mb_strtolower($needle), $offset);
  }

  if(!function_exists('stripos'))
  {
    function stripos($haystack, $needle, $offset)
    {
      return mb_stripos($haystack, $needle, $Offset);
    }
  }
}

# http_build_query is useful in a couple places...
# Credit: http://www.php.net/http_build_query#90438
if(!function_exists('http_build_query'))
{ 
  function http_build_query($data, $prefix = '', $sep = '', $key = '')
  { 
    $ret = array(); 
    foreach((array)$data as $k => $v)
    { 
      if(is_int($k) && $prefix != null)
        $k = urlencode($prefix. $k); 

      if((!empty($key)) || ($key === 0))
        $k = $key. '['. urlencode($k). ']'; 

      if(is_array($v) || is_object($v))
        array_push($ret, http_build_query($v, '', $sep, $k)); 
      else
        array_push($ret, $k.'='. urlencode($v)); 
    } 

    if(empty($sep))
      $sep = ini_get('arg_separator.output'); 

    return implode($sep, $ret); 
  }
}
?>