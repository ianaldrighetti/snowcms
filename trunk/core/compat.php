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

# Title: Compatibility functions

if(!function_exists('json_encode'))
{
  # Bitmasks for the JSON functions.
  define('JSON_HEX_TAG', 0x00001);
  define('JSON_HEX_AMP', 0x00010);
  define('JSON_HEX_APOS', 0x00100);
  define('JSON_HEX_QUOT', 0x01000);
  define('JSON_FORCE_OBJECT', 0x10000);

  /*
    Function: json_encode

    Parameters:
      mixed $value - The value to encode to a JSON representative value.
      int $options - Just there for compatibility reasons.

    Returns:
      string - Returns the JSON encoded string.
  */
  function json_encode($value, $options = 0)
  {
    # Is it an array or object?
    if(is_array($value))
    {
      if(!__json_flat_array($value) || is_object($value) || ($options & JSON_FORCE_OBJECT))
      {
        $values = array();
        foreach($value as $key => $val)
          $values[] = '"'. __json_sanitize($key, $options). '":'. json_encode($val, $options);

        return '{'. implode(',', $values). '}';
      }
      else
      {
        $values = array();
        foreach($value as $val)
          $values[] = json_encode($val, $options);

        return '['. implode(',', $values). ']';
      }
    }
    # How about a bool?
    elseif(is_bool($value))
    {
      return $value ? 'true' : 'false';
    }
    # A number, perhaps?
    elseif(is_numeric($value))
    {
      return $value;
    }
    # A string?
    elseif(is_string($value))
    {
      return '"'. __json_sanitize($value, $options). '"';
    }
  }

  /*
    Function: __json_flat_array

    Checks to see whether or not the array is associative
    or non-associative (flat). This is a helper function
    for <json_encode>.

    Parameters:
      array $array

    Returns:
      bool - Returns true is the array is non-associative, false
             if it is associative.
  */
  function __json_flat_array($array)
  {
    foreach($array as $key => $value)
      if(!is_int($key))
        return false;

    return true;
  }

  /*
    Function: __json_sanitize

    Sanitizes a string according to the JSON spec, but also the
    supplied options. This is a helper function for <json_encode>.

    Parameters:
      string $value
      int $options

    Returns:
      string - Returns the sanitized string.
  */
  function __json_sanitize($value, $options = 0)
  {
    # These are things which need to be replaced.
    $value = strtr($value, array(
                             "\b" => "\\b",
                             "\t" => "\\t",
                             "\n" => "\\n",
                             "\f" => "\\f",
                             "\r" => "\\r",
                             '"' => '\"',
                             '\\' => '\\\\',
                           ));

    # Anything special?
    if($options & JSON_HEX_TAG)
      $value = strtr($value, array('<' => '\u003C', '>' => '\u003E'));

    if($options & JSON_HEX_AMP)
      $value = strtr($value, array('&' => '\u0026'));

    if($options & JSON_HEX_APOS)
      $value = strtr($value, array('\'' => '\u0027'));

    if($options & JSON_HEX_QUOT)
      $value = strtr($value, array('"' => '\u0022'));

    return $value;
  }
}

/*
  Function: array_insert

  Inserts an item at the specified index.

  Parameters:
    array $array - The array to insert the item into.
    mixed $item - The item to insert.
    int $position - The position at which to insert item.
    string $key

  Returns:
    array - Returns the new array with item inserted at the specified position.

  Note:
    If you are inserting the item into an associative array, you must
    specify the $key parameter, which is the key of the inserted item!
*/
function array_insert($array, $item, $index, $key = null)
{
  $index = (int)$index;
  $length = count($array);

  # Is it an associative array?
  if($key === null)
  {
    # Maybe we can just plop it at the end..?
    if($index >= $length)
      $array[] = $item;
    else
    {
      $new_array = array();

      for($i = 0; $i < $length; $i++)
      {
        # The right index to insert item at?
        if($i == $index)
          $new_array[] = $item;

        $new_array[] = $array[$i];
      }

      $array = $new_array;
    }
  }
  else
  {
    # Can't have two of the same indexes, sorry!
    if(isset($array[$key]))
      return false;
    elseif($index >= $length)
      $array[$key] = $item;
    else
    {
      # Interesting... :P
      $new_array = array();
      $current = 0;

      foreach($array as $akey => $avalue)
      {
        if($current == $index)
          $new_array[$key] = $item;

        $new_array[$akey] = $avalue;
      }

      $array = $new_array;
    }
  }

  return $array;
}
?>