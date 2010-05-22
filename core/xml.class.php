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
  Class: XML

  The XML class, is of course, used to read XML files, for reading things
  such as theme and plugin information files.
*/
class XML
{
  # Variable: parser
  # Holds the XML Parser resource.
  private $parser;

  /*
    Constructor: __construct

    Parameters:
      string $filename - The name of the XML file to load.
  */
  public function __construct($filename = null)
  {
    # Create an XML Parser resource.
    $this->parser = xml_parser_create();
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
    return xml_parser_set_option($this->parser, $option, $value);
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
    # We just load up the files contents and pass it to the load_string method.
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
    # Call on the parser to, of course, parse the data.
    $values = array();
    $indexes = array();
    if(xml_parse_into_struct($this->parser, $data, $values, $indexes) == 1)
    {
      # Let's make it pretty, shall we?
      $data = array();

      # But of course, we can only make it pretty if it is ugly.
      if(count($values) > 0)
      {
        foreach($values as $value)
        {
          if(strtolower($value['type']) == 'cdata')
          {
            # Useless crap.
            continue;
          }

          # Opening? Then add it to the opened tags!
          if(in_array(strtolower($value['type']), array('open', 'close')))
          {
            if($value['level'] == 1 && strtolower($value['type']) != 'close')
            {
              $data[] = array(
                          'tag' => strtolower($value['tag']),
                          'type' => strtolower($value['type']),
                          'level' => (int)$value['level'] - 1,
                          'value' => $value['value'],
                          'attributes' => $this->strtolower_array_keys(isset($value['attributes']) ? $value['attributes'] : array()),
                          'childNodes' => array(),
                        );
            }
            elseif(strtolower($value['type']) != 'close')
            {
              $data[] = array(
                          'tag' => strtolower($value['tag']),
                          'type' => strtolower($value['type']),
                          'level' => (int)$value['level'] - 1,
                          'value' => $value['value'],
                          'attributes' => $this->strtolower_array_keys(isset($value['attributes']) ? $value['attributes'] : array()),
                          'childNodes' => array(),
                        );
            }
          }
          else
          {
            $data[] = array(
                        'tag' => strtolower($value['tag']),
                        'type' => strtolower($value['type']),
                        'level' => (int)$value['level'] - 1,
                        'value' => $value['value'],
                        'attributes' => $this->strtolower_array_keys(isset($value['attributes']) ? $value['attributes'] : array()),
                      );
          }
        }
      }

      return $data;
    }
    else
    {
      # Sorry, didn't work!
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
    # Only if there is anything in the array, of course!
    if(count($array) > 0)
    {
      $tmp = array();

      foreach($array as $key => $value)
      {
        $tmp[strtolower($key)] = $value;
      }

      # Alright, done. :P Simple, no?
      return $tmp;
    }

    # You gave us an empty array, I'll give it back!
    return array();
  }

  /*
    Destructor: __destruct
  */
  public function __destruct()
  {
    # Free the XML Parser resource, no longer needed, as it appears.
    xml_parser_free($this->parser);
  }
}
?>