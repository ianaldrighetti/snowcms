<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
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

	// Variable: data
	// An array containing the parsed XML data from the last XML file
	// successfully parsed by <XML::parse>.
	private $data;

	// Variable: data_count
	// Maintains the current size of the data array.
	private $data_count;

	// Variable: index
	// An array containing an index of the parsed XML data from the last XML
	// file successfully parsed by <XML::parse>.
	private $index;

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
		$this->data = null;
		$this->data_count = 0;
		$this->index = null;
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
			array &$index - An array which will be used to store an index
											containing the location of items within the parsed XML
											array.

		Returns:
			array - Returns an array containing the XML files parsed data,
							false if the parser failed to parse the file.
	*/
	public function parse($filename, &$index = array())
	{
		// We just load up the files contents and pass it to the load_string method.
		return $this->parse_string(file_get_contents($filename), $index);
	}

	/*
		Method: parse_string

		Parses the specified XML string.

		Parameters:
			string $data - The XML data to parse.
			array &$index - An array which will be used to store an index
											containing the location of items within the parsed XML
											array.

		Returns:
			array - Returns an array containing the XML strings parsed data,
							false if the parser failed to parse the data.
	*/
	public function parse_string($data, &$index)
	{
		// Clear a couple of things.
		$this->data = null;
		$this->data_count = 0;
		$this->index = null;

		// Call on the parser to, of course, parse the data.
		$values = array();
		$indexes = array();
		$index = array();
		if(xml_parse_into_struct($this->parser, $data, $values, $indexes) == 1)
		{
			// Let's make it pretty, shall we?
			$data = array();

			// But of course, we can only make it pretty if it is ugly.
			if(count($values) > 0)
			{
				$current = 0;
				foreach($values as $value)
				{
					if(strtolower($value['type']) == 'cdata')
					{
						// Useless crap.
						continue;
					}

					$data[$current++] = array(
																'tag' => strtolower($value['tag']),
																'type' => strtolower($value['type']),
																'level' => (int)$value['level'] - 1,
																'value' => isset($value['value']) ? $value['value'] : null,
																'attributes' => $this->strtolower_array_keys(isset($value['attributes']) ? $value['attributes'] : array()),
															);

					// Do we need to add anything to the index? We will only store
					// something in the index if this tag is opening or complete.
					if($data[$current - 1]['type'] == 'open' || $data[$current - 1]['type'] == 'complete')
					{
						// Do we need to initialize the thing?
						if(!isset($index[$data[$current - 1]['tag']]))
						{
							$index[$data[$current - 1]['tag']] = array();
						}

						// Now add the location of the item.
						$index[$data[$current - 1]['tag']][] = $current - 1;
					}
				}
			}

			// Reset!
			$this->reset();

			// Save the data and the index.
			$this->data = $data;
			$this->data_count = count($data);
			$this->index = $index;

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

	/*
		Method: value

		Returns the information about the specified tag from the array of the
		parsed XML file.

		Parameters:
			string $tag - The name of the tag to find.
			string $parent_tag - The name of the tag that $tag should be a child
													 of. In order for $tag to be considered a child of
													 the specified parent tag it must be directly
													 within the parent tag.
			int $offset - Where should the search for value conclude, 0 meaning
										the first instance of $tag will be returned, 1 means the
										first instance will not be returned, but the second one
										will, and so on.

		Returns:
			array - Returns an array containing the tags information (just as an
							index contains with the results returned by <XML::parse>), but
							if no tag was found false will be returned.

		Note:
			This method will only work if <XML::parse> successfully parsed the
			last file.
	*/
	public function value($tag, $parent_tag = null, $offset = 0)
	{
		// No information to search? How about the tag you are searching for?
		// That can't be left empty... The tag must also exist somewhere, and
		// the same goes for the parent tag, if specified. Finally, offset must
		// not be less than 0.
		if($this->data === null || empty($tag) || !isset($this->index[strtolower($tag)]) || (!empty($parent_tag) && !isset($this->index[strtolower($tag)])) || (int)$offset < 0)
		{
			// Sorry! :-/
			return false;
		}

		// Make sure the offset is an integer... Should be, anyways.
		// Also lower case the tag and parent tag.
		$offset = (int)$offset;
		$tag = strtolower($tag);
		$parent_tag = $parent_tag !== null ? strtolower($parent_tag) : null;

		// If you want the tag to be a child of another tag, we have to do a bit
		// more work... But if you don't have a parent tag specified, that makes
		// our life so much easier.
		if(empty($parent_tag))
		{
			// Make sure the offset is valid.
			if(!isset($this->index[$tag][$offset]))
			{
				// Nothing! Sorry.
				return false;
			}
			else
			{
				// Got it!
				return $this->data[$this->index[$tag][$offset]];
			}
		}

		// Let's get going!
		for($i = 0; $i < $this->data_count; $i++)
		{
			// Keep going until we find the parent tag.
			if($this->data[$i]['tag'] == $parent_tag)
			{
				// Another for loop. Yippe!
				for($j = $i + 1; $j < $this->data_count; $j++)
				{
					// Did we find the tag you are looking for?
					if($this->data[$j]['tag'] == $tag && $this->data[$j]['level'] == $this->data[$i]['level'] + 1)
					{
						// Gotta make sure we are at the right offset.
						if($offset == 0)
						{
							// Yup, we found it!
							return $this->data[$j];
						}
						else
						{
							// We almost found it, darn.
							$offset--;
						}
					}
					// At too low of a level? Must mean we are out of the parent tag.
					elseif($this->data[$j]['level'] == $this->data[$i]['level'])
					{
						break;
					}
				}

				// Set $i to $j.
				$i = $j;
			}
		}

		// Sorry, we found nothing. Weird! :-P
		return false;
	}

	/*
		Method: get_value

		Extracts the value from the specified tag information.

		Parameters:
			array $item - An array containing a tags information, which should
										contain an index called 'value'

		Returns:
			string - Returns the tags value, or if there is none false is
							 returned.
	*/
	public function get_value($item)
	{
		// Seems pointless, but hey, makes life easier!
		return isset($item['value']) ? $item['value'] : false;
	}
}
?>