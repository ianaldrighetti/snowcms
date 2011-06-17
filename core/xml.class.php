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
  Class: XML

  The XML class, is of course, used to read XML files, for reading things
  such as theme and plugin information files.
*/
class XML
{
  // Variable: parser
  // Holds the XML Parser resource.
  private $parser;

  // Variable: options
  // Holds all the set option, that way a new parser resource can
  // automatically be created without disturbing the set options.
  private $options;

  /*
    Constructor: __construct

    Parameters:
      string $filename - The name of the XML file to load.
  */
  public function __construct($filename = null)
  {
    // Create an XML Parser resource.
    $this->parser = xml_parser_create();
    $this->options = array();
  }

  /*
    Method: get_option

    Returns the current value of the specified XML Parser option.

    Parameters:
      int $option - The option to return.

    Returns:
      mixed - Returns the value of the option.

    Note:
      See <http://www.php.net/xml_parser_get_option> for more information.
  */
  public function get_option($option)
  {
    return xml_parser_get_option($this->parser, $option);
  }

  /*
    Method: set_option

    Sets the specified XML Parser option to the supplied value.

    Parameters:
      int $option - The option to set.
      mixed $value - The new value of the option.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      See <http://www.php.net/xml_parser_set_option> for more information.
  */
  public function set_option($option, $value)
  {
    if(xml_parser_set_option($this->parser, $option, $value))
    {
      // It was a valid option, so save it!
      $this->options[$option] = $value;
      return true;
    }
    else
    {
      return false;
    }
  }

  /*
    Method: parse

    Parses the specified XML file.

    Parameters:
      string $filename - The XML file to parse.

    Returns:
      array - Returns an array containing the XML files parsed data,
              false if the parser failed to parse the file.
  */
  public function parse($filename)
  {
    // We just load up the files contents and pass it to the load_string method.
    return $this->parse_string(file_get_contents($filename));
  }

  /*
    Method: parse_string

    Parses the specified XML string.

    Parameters:
      string $data - The XML data to parse.

    Returns:
      array - Returns an array containing the XML strings parsed data,
              false if the parser failed to parse the data.
  */
  public function parse_string($data)
  {
    // Call on the parser to, of course, parse the data.
    $values = array();
    $indexes = array();
    if(xml_parse_into_struct($this->parser, $data, $values, $indexes) == 1)
    {
      // Let's make it pretty, shall we?
      $data = array();

      // But of course, we can only make it pretty if it is ugly.
      if(count($values) > 0)
      {
        foreach($values as $value)
        {
          if(strtolower($value['type']) == 'cdata')
          {
            // Useless crap.
            continue;
          }

          $data[] = array(
                      'tag' => strtolower($value['tag']),
                      'type' => strtolower($value['type']),
                      'level' => (int)$value['level'] - 1,
                      'value' => isset($value['value']) ? $value['value'] : null,
                      'attributes' => $this->strtolower_array_keys(isset($value['attributes']) ? $value['attributes'] : array()),
                    );
        }
      }

      // Reset!
      $this->reset();

      return $data;
    }
    else
    {
      // Sorry, didn't work!
      $this->reset();
      return false;
    }
  }

  /*
    Method: strtolower_array_keys

    Lowers the case of the keys in the supplied array.

    Parameters:
      array $array - The array to lowercase the keys of.

    Returns:
      array - Returns an array, with all the keys lowercased.
  */
  private function strtolower_array_keys($array)
  {
    // Only if there is anything in the array, of course!
    if(count($array) > 0)
    {
      $tmp = array();

      foreach($array as $key => $value)
      {
        $tmp[strtolower($key)] = $value;
      }

      // Alright, done. :P Simple, no?
      return $tmp;
    }

    // You gave us an empty array, I'll give it back!
    return array();
  }

  /*
    Method: reset

    Closes and then makes a new XML parser.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.

    Note:
      This method is automatically called by the <XML::parse_string> method.
  */
  private function reset()
  {
    $this->__destruct();
    $this->parser = xml_parser_create();

    // Custom options? We need to reapply them.
    if(count($this->options) > 0)
    {
      foreach($this->options as $option => $value)
      {
        $this->set_option($option, $value);
      }
    }
  }

  /*
    Destructor: __destruct
  */
  public function __destruct()
  {
    // Free the XML Parser resource, no longer needed, as it appears.
    xml_parser_free($this->parser);
  }
}
?>