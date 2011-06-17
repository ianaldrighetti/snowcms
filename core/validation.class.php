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

if(!defined('IN_SNOW'))
{
  die('Nice try...');
}

/*
  Class: Validation

  Handles the validation of data, such as strings, integers, doubles,
  and so on and so forth. The Validation class is used by numerous other
  tools such as the <Form>, <Member>, and <Settings> classes, as well as
  the database layer. Plugins can add on additional types as well, which
  would then be usable in all other tools which use the Validation class.

  By default, the following types are implemented: bool, int, double, string.
*/
class Validation
{
  // Variable: types
  // An array containing the registered types.
  private $types;

  /*
    Constructor: __construct
  */
  public function __construct()
  {
    global $func;

    $this->types = array();

    // Add some default types.
    $this->add_type('bool', create_function('&$data, $min = null, $max = null, $truncate = false', '
                              if(is_bool($data) || $data == 0 || $data == 1)
                              {
                                $data = !empty($data);
                                return true;
                              }
                              else
                                return false;'));

    $this->add_type('int', create_function('&$data, $min = null, $max = null, $truncate = false', '
                             if((string)$data == (string)(int)$data)
                             {
                               $data = (int)$data;

                               if($min !== null && $data < $min)
                               {
                                 if(!empty($truncate))
                                   $data = (int)$min;
                                 else
                                   return false;
                               }
                               elseif($max !== null && $data > $max)
                               {
                                 if(!empty($truncate))
                                   $data = (int)$max;
                                 else
                                   return false;
                               }

                               return true;
                             }
                             else
                               return false;'));

    $this->add_type('double', create_function('&$data, $min = null, $max = null, $truncate = false', '
                                if((string)$data == (string)(double)$data)
                                {
                                  $data = (double)$data;

                                  if($min !== null && $data < $min)
                                  {
                                    if(!empty($truncate))
                                      $data = (double)$min;
                                    else
                                      return false;
                                  }
                                  elseif($max !== null && $data > $max)
                                  {
                                    if(!empty($truncate))
                                      $data = (double)$max;
                                    else
                                      return false;
                                  }

                                  return true;
                                }
                                else
                                  return false;'));

    $this->add_type('string', create_function('&$data, $min = null, $max = null, $truncate = false', '
                                global $func;

                                if(is_string($data) || is_numeric($data) || is_bool($data))
                                {
                                  $data = (string)(is_bool($data) ? (!empty($data) ? 1 : 0) : $data);

                                  if($min !== null && strlen($data) < $min)
                                  {
                                    return false;
                                  }
                                  elseif($max !== null && strlen($data) > $max)
                                  {
                                    if(!empty($truncate))
                                    {
                                      if(isset($func[\'substr\']))
                                        $data = $func[\'substr\']($data, 0, (int)$max);
                                      else
                                        $data = substr($data, 0, (int)$max);
                                    }
                                    else
                                      return false;
                                  }

                                  return true;
                                }
                                else
                                  return false;'));

    // Here is a wise place to add your types... :P
    api()->run_hooks('validation_construct', array(&$this));
  }

  /*
    Method: add_type

    Allows the addition of a validation type.

    Parameters:
      string $name - The name of the type.
      string $callback - The function which checks the validity of the data.
      string $filename - The location of the callback, if any.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      The callback is expected to accept the following parameters:
        &data - The data that needs to be checked of its validity.

        minimum length - The minimum length of data.

        maximum length - The maximum length of data.

        truncate - A bool indicating whether or not data should be truncated
                   or not if it is longer than maximum length.

      Please note that data is a reference parameter! So be not to return the
      properly type-casted data, as the return value is expected to be something
      different. Also, if minimum or maximum is null (be sure to use the === operator!)
      do not check the length which is supplied as null.

      Now, as for the return value, a bool is expected, true meaning the data was
      fine and successfully type-casted to the proper type, false if the data was
      invalid, and could not be type-casted. The rest is done by the class ;-).
  */
  public function add_type($name, $callback, $filename = null)
  {
    // Can't have an empty name or class name.
    if(empty($name) || empty($callback))
    {
      return false;
    }

    // Does the file exist? That is, unless the function isn't already defined.
    if(!is_callable($callback) && (empty($filename) || !file_exists($filename)))
    {
      return false;
    }
    elseif(!is_callable($callback) && file_exists($filename))
    {
      require_once($filename);

      // Function still not defined? That's no good :(
      if(!is_callable($callback))
      {
        return false;
      }
    }

    // Not callable? Not good!
    if(!is_callable($callback))
    {
      return false;
    }

    // Alright, save the callback, and we are good!
    $this->types[strtolower($name)] = $callback;

    return true;
  }

  /*
    Method: types

    Returns an array of the currently registered types.

    Parameters:
      none

    Returns:
      array - Returns an array containing the names of the currently added
              types.
  */
  public function types()
  {
    // Simple enough, right?
    return array_keys($this->types);
  }

  /*
    Method: data

    Checks the validity of the supplied data, and

    Parameters:
      mixed &$data - The data which is being validated, this is a reference
                     as the data will be type-casted to the correct type as
                     long as it can be successfully.
      string $type - The type of $data, such as string, int, double, or any
                     other type which has been added via <Validation::add_type>.
                     There is a special type called formatted as well, look below
                     at the notes for more information.
      mixed $format - Only needed when the supplied type is array. Look below
                      for more information.

    Returns:
      bool - Returns true if the data is all valid and of the type supplied,
             false if not. If false, $data will be set to null, otherwise,
             the data inside $data will be type-casted to the appropriate type.

    Note:
      As mentioned previously, there is also a type called formatted, which
      allows you to pass an array of data, the way the information is validated
      is through the use of the $format parameter. Here are some examples.

      If you pass an array of integers (or what you want to check), type formatted,
      and the format supplied is int, it will go through the indices and make sure
      each value is an integer. This works for any type of course.

      Another way is to supply $format as an associative array, the value of the
      specified key is the type that the key in the supplied $data should be.
      Here is an example:

      data(array(
             'member_id' => 1,
             'member_name' => 'some user',
             'member_email' => 'my@email.com',
           ), 'formatted',
           array(
             'member_id' => 'int',
             'member_name' => array('string', 3, 80, false),
             'member_email' => array('string', null, 255, false),
           ));

     The above shows the data you want validated (the first array), while the last
     is another array containing all the same indices but of course, with a string
     or array. You can either just set a string for the type, or pass an array
     structured as so: array(type, min length, max length, truncate?, callback).
     The min and max length can differ for, of course, different types, for strings
     it uses the length, while with numbers, it uses the actual value, so say you
     said 5 min and 10 max, which would mean that the supplied number can be
     between 5 and 10 (inclusive, though). As for "truncate?", that is a bool,
     which tells the type if the supplied value is longer than the max length, should
     the value be truncated instead of being marked as invalid. Callback is pretty
     straight forward, this callback is called before the type handler has at the
     value, so you can do any checking yourself, if you want. Of course, this callback
     should return a value, true if the data was fine, and false if it was not, meaning
     the value will be null, along with everything else.

     If you set an array with the type of formatted, the second index should contain
     an array (or just the type itself) which gives information about how to check
     the validity of the data.

     Please note that if the $data array has less or more indices than the $format
     array, $data is set to null instantly as it is clearly not valid, but it also,
     of course, makes sure even if there are the same number of indices, that they
     are the same.

     Another note, the $data array could have the order of its indices changed, unless
     that is of course the $data array has the indices in the same order as $format.

     Oh yes! Before I forget!!! If the type is not of formatted, you can still pass
     a value to the $format parameter, which contains an array as so:
     array(min length, max length, truncate?, callback)

     This applies to both, you can set min and max length to null, if you so prefer,
     and those checks will not be, erm, checked.
  */
  public function data(&$data, $type, $format = null)
  {
    global $func;

    // Is the type empty?
    if(empty($type))
    {
      // Can't check the data, so delete it!
      $data = null;

      return false;
    }

    // Case insensitivity please ;)
    $type = strtolower($type);

    // Is it of the formatted type?
    if($type == 'formatted')
    {
      // $data needs to be an array, otherwise, this is the wrong thing.
      if(!is_array($data))
      {
        $data = null;
        return false;
      }

      // Now real quick like! Is the format just a type or an array?
      if(!is_array($format) || (is_array($format) && isset($format[0]) && $format[0] != 'formatted'))
      {
        // Not much to do, is there?
        if(count($data) == 0)
        {
          return true;
        }

        if(is_array($format))
        {
          $format = (string)$format[0];
        }

        // Just a single type, so yeah.
        $new = array();
        foreach($data as $key => $value)
        {
          // Simply use the data method, hehe :)
          if($this->data($value, $format))
          {
            // It was valid, so just add it.
            $new[$key] = $value;
          }
          else
          {
            // Oh noes! It was bad.
            $data = null;

            return false;
          }
        }

        // All done? No issues? Great!
        $data = $new;
        return true;
      }
      else
      {
        // Quick check! If they both don't have the same amount of indices,
        // then something is already wrong ;)
        if(count($data) != count($format) || count($data) == 0)
        {
          $data = null;

          return false;
        }

        // Now it is time to check!
        $new = array();
        foreach($format as $key => $value)
        {
          // Is the key not in $data..?
          if(!isset($data[$key]))
          {
            $data = null;
            return false;
          }

          $valid = $this->data($data[$key], is_array($format[$key]) ? $value[0] : $value, is_array($format[$key]) ? array_slice($value, 1) : null);

          // Was the data valid?
          if(empty($valid))
          {
            $data = null;
            return false;
          }
          else
          {
            $new[$key] = $data[$key];
          }
        }

        // If we are still going, it worked!
        $data = $new;
        return true;
      }
    }
    elseif(isset($this->types[$type]))
    {
      // Any type of callback, maybe? :-(
      if(isset($format[3]) && is_callable($format[3]))
      {
        if(!$format[3]($data))
        {
          // Something must have went wrong, don't know what, though.
          $data = null;

          return false;
        }
      }

      // Now let the type handle the validity checking...
      if($this->types[$type]($data, isset($format[0]) && is_numeric($format[0]) ? (int)$format[0] : null, isset($format[1]) && is_numeric($format[1]) ? (int)$format[1] : null, isset($format[2]) && is_bool($format[2]) ? $format[2] : false))
      {
        // Alright, it was alright!!! :)
        return true;
      }
      else
      {
        // Uh oh, it was invalid!
        $data = null;

        return false;
      }
    }

    // Nope, the type wasn't defined, so bad!
    $data = null;
    return false;
  }
}

/*
  Function: init_validation

  Instantiates the Validation class for use of validating data.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function init_validation()
{
  global $validation;

  // You can hook into validation_construct to add types ;)
  $validation = api()->load_class('Validation');
}
?>