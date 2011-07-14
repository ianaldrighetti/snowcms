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

if(!function_exists('json_encode'))
{
	// Bitmasks for the JSON functions.
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
		// Is it an array or object?
		if(is_array($value))
		{
			if(!__json_flat_array($value) || is_object($value) || ($options & JSON_FORCE_OBJECT))
			{
				$values = array();
				foreach($value as $key => $val)
				{
					$values[] = '"'. __json_sanitize($key, $options). '":'. json_encode($val, $options);
				}

				return '{'. implode(',', $values). '}';
			}
			else
			{
				$values = array();
				foreach($value as $val)
				{
					$values[] = json_encode($val, $options);
				}

				return '['. implode(',', $values). ']';
			}
		}
		// How about a bool?
		elseif(is_bool($value))
		{
			return $value ? 'true' : 'false';
		}
		// A number, perhaps?
		elseif(is_numeric($value))
		{
			return $value;
		}
		// A string?
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
		{
			if(!is_int($key))
			{
				return false;
			}
		}

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
		// These are things which need to be replaced.
		$value = strtr($value, array(
														 "\b" => "\\b",
														 "\t" => "\\t",
														 "\n" => "\\n",
														 "\f" => "\\f",
														 "\r" => "\\r",
														 '"' => '\"',
														 '\\' => '\\\\',
													 ));

		// Anything special?
		if($options & JSON_HEX_TAG)
		{
			$value = strtr($value, array('<' => '\u003C', '>' => '\u003E'));
		}

		if($options & JSON_HEX_AMP)
		{
			$value = strtr($value, array('&' => '\u0026'));
		}

		if($options & JSON_HEX_APOS)
		{
			$value = strtr($value, array('\'' => '\u0027'));
		}

		if($options & JSON_HEX_QUOT)
		{
			$value = strtr($value, array('"' => '\u0022'));
		}

		return $value;
	}
}
?>