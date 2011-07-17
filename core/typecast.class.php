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

// Title: Typecast

/*
	Class: Typecast

	The Typecast class does just what you might think: typecast "stuff." Not
	only will the Typecast class do that, but it will also check the types of
	the supplied variable. This class has common built-in data types, but
	more can be added with the <Typecast::add_type> method.

	Here are the supported types, by default: string, bool, int, double, array
	and object.
*/
class Typecast
{
	// Variable: types
	// An array containing registered types.
	private $types;

	/*
		Constructor: __construct

		The constructor doesn't do a whole lot other then adding common types.

		Parameters:
			none

		Note:
			Plugins may hook into typecast_init, which is called before any
			default types are added.
	*/
	public function __construct()
	{
		$this->types = array();

		// Have at it!
		api()->run_hooks('typecast_construct');

		// Now we will add some default types. Just for the curious, these types
		// will not be added if a plugin has already added a type by the same
		// name... As the <Typecast::add_type> method will simply reject them.
		$this->add_type('string', 'typecast_string');
		$this->add_type('bool', 'typecast_bool');
		$this->add_type('int', 'typecast_int');
		$this->add_type('double', 'typecast_double');
		$this->add_type('array', 'typecast_array');
		$this->add_type('object', 'typecast_object');
	}

	/*
		Method: add_type

		Adds the specified type, along with a callback which will handle
		requests in regards to the registered type.

		Parameters:
			string $name - The name of the type.
			callback $callback - Any valid callback, which can be called by
													 <www.php.net/call_user_func>. See the notes for
													 more information.

		Returns:
			bool - Returns true if the type was added successfully, false if the
						 the type already exists, the name isn't valid (empty) or if the
						 callback is not valid.

		Note:
			A callback must accept the following parameters, the first will always
			be supplied, while the rest may not:

				mixed value - This parameter is the value which may being tested or
											typecasted. If no other parameters are supplied, then
											the callback is to return true if the supplied value
											is of the type of the registered callback, false if
											not.
				bool typecast - This parameter will be set to true if the Typecast
												class is requesting that the callback typecast the
												supplied value to the type that the callback
												handles. For example, if the callback handles
												strings and the type of the value is an array, then
												the callback needs to change the type of the value
												(an array) to a string... This would mean that the
												callback would return the array serialized. In the
												case that the callback cannot change the type
												properly (such as a string to an array, and the
												string cannot be unserialized), simply return false.
	*/
	public function add_type($name, $callback)
	{
		// Make sure that this type doesn't exist yet. Also make sure the
		// callback is actually callable.
		if($this->type_exists($name) || !is_callable($callback))
		{
			return false;
		}

		// Add it.
		$this->types[strtolower($name)] = $callback;

		// We always want string to be last... It is very "It's me!" happy,
		// seeing as it will see an integer and double as its type.
		if(isset($this->types['string']))
		{
			$tmp = $this->types['string'];
			unset($this->types['string']);
			$this->types['string'] = $tmp;
		}

		return true;
	}

	/*
		Method: type_exists

		Determines whether the specified type already has a registered handler.

		Parameters:
			string $name - The name of the type.

		Returns:
			bool - Returns true if the type already exists, false if not.
	*/
	public function type_exists($name)
	{
		return !empty($name) && isset($this->types[strtolower($name)]);
	}

	/*
		Method: remove_type

		Removes the specified type from the list of types which can be detected
		with the Typecast class.

		Parameters:
			string $name - The name of the type.

		Returns:
			bool - Returns true if the type was removed successfully, false if
						 the type is not registered.
	*/
	public function remove_type($name)
	{
		// Does the type exist?
		if(!$this->type_exists($name))
		{
			// This type does not exist, therefore we cannot remove it.
			return false;
		}

		unset($this->types[strtolower($name)]);

		return true;
	}

	/*
		Method: return_types

		Returns an array containing the types supported by the Typecast class.

		Parameters:
			none

		Returns:
			array
	*/
	public function return_types()
	{
		// Pretty straight forward.
		return array_keys($this->types);
	}

	/*
		Method: typeof

		Returns the type of the of the value supplied.

		Parameters:
			mixed $value - The value to determine the type of.

		Returns:
			string - Returns a string containing the name of the type, which could
							 be string, int, double, array, object, or any other types
							 that may have been added. If the type of the value supplied
							 cannot be determined, false is returned.
	*/
	public function typeof($value)
	{
		// Here we go!
		foreach($this->types as $name => $callback)
		{
			// Check to see if this $name is the type of $value.
			if(call_user_func($callback, $value, false))
			{
				// Yup, sure is.
				return $name;
			}
		}

		// Sorry, I have no idea what type that is.
		return false;
	}

	/*
		Method: is_a

		Determines whether the type supplied matches that of the value also
		being supplied.

		Parameters:
			string $name - The name of the type to compare the actual type of
										 $value against.
			mixed $value - The value to test.

		Returns:
			bool - Returns true if the type supplied matches that of the value
						 supplied.
	*/
	public function is_a($name, $value)
	{
		// This is really easy.
		return $this->typeof($value) == strtolower($name);
	}

	/*
		Method: to

		Typecasts the specified value to the specified type.

		Parameters:
			string $name - The name of the type to typecast $value to.
			mixed $value - The value to typecast.

		Returns:
			mixed - Returns the new value which was converted to the specified
							type, or false on failure.

		Note:
			Please note that when converting values to such types as bool may,
			of course, return a value of false which does not mean that the value
			could not be properly converted. In such a case, that is the real
			value. However, other types such as a string, int, double, array, etc.
			will return false and actually mean it. Just thought I would mention
			that :-P.

			However, this method may also return false in the case that the type
			specified does not exist. In which case you may want to check with
			<Typecast::type_exists> first to make sure that the type actually
			exists, but such a check is likely not required with such types as
			string, double, int, bool, array and object, which are built-in.
	*/
	public function to($name, $value)
	{
		// Does this type exist?
		if(!$this->type_exists($name))
		{
			return false;
		}

		return call_user_func($this->types[$name], $value, true);
	}
}

/*
	Function: typecast_string

	The string type handler for the <Typecast> class.

	Parameters:
		mixed $value
		bool $typecast

	Returns:
		mixed
*/
function typecast_string($value, $typecast = false)
{
	// Typecasting?
	if(!empty($typecast))
	{
		// Is this something that can be serialized?
		if(is_array($value) || is_object($value))
		{
			// Simple enough.
			return serialize($value);
		}
		else
		{
			// Anything else can be made a string.
			return is_bool($value) ? ($value ? '1' : '0') : (string)$value;
		}
	}
	else
	{
		return $value == (string)$value;
	}
}

/*
	Function: typecast_bool

	The boolean type handler for the <Typecast> class.

	Parameters:
		mixed $value
		bool $typecast

	Returns:
		mixed
*/
function typecast_bool($value, $typecast = false)
{
	if(!empty($typecast))
	{
		// If it is empty, it is false, otherwise: true!
		return !empty($value);
	}
	else
	{
		return $value === false || $value === true;
	}
}

/*
	Function: typecast_int

	The integer type handler for the <Typecast> class.

	Parameters:
		mixed $value
		bool $typecast

	Returns:
		mixed
*/
function typecast_int($value, $typecast = false)
{
	if(!empty($typecast))
	{
		return is_array($value) || is_object($value) ? count($value) : (int)$value;
	}
	else
	{
		return (string)$value == (string)(int)$value;
	}
}

/*
	Function: typecast_double

	The double type handler for the <Typecast> class.

	Parameters:
		mixed $value
		bool $typecast

	Returns:
		mixed
*/
function typecast_double($value, $typecast = false)
{
	if(!empty($typecast))
	{
		return is_array($value) || is_object($value) ? (double)count($value) : (double)$value;
	}
	else
	{
		return (string)$value == (string)(double)$value || typecast_int($value, false);
	}
}

/*
	Function: typecast_array

	The array type handler for the <Typecast> class.

	Parameters:
		mixed $value
		bool $typecast

	Returns:
		mixed
*/
function typecast_array($value, $typecast = false)
{
	if(!empty($typecast))
	{
		// Try to unserialize it if it is a string, otherwise, maybe nothing.
		return is_string($value) && is_array(unserialize($value)) ? unserialize($value) : (is_array($value) || is_object($value) ? (array)$value : false);
	}
	else
	{
		return is_array($value);
	}
}

/*
	Function: typecast_object

	The object type handler for the <Typecast> class.

	Parameters:
		mixed $value
		bool $typecast

	Returns:
		mixed
*/
function typecast_object($value, $typecast = false)
{
	if(!empty($typecast))
	{
		return is_string($value) && is_object(unserialize($value)) ? unserialize($value) : (is_object($value) || is_array($value) ? (object)$value : false);
	}
	else
	{
		return is_object($value);
	}
}

/*
	Function: typecast

	Returns the instance of the <Typecast> class to be used throughout the
	system.

	Parameters:
		none

	Returns:
		object - Returns an instance of the <Typecast> class.
*/
function typecast()
{
	// Do we need to create an instance of the Typecast class?
	if(!isset($GLOBALS['typecast']) || !is_object($GLOBALS['typecast']))
	{
		// Yup, we sure do. Plugins may hook into typecast_construct to add
		// any more types, if they so please!
		$GLOBALS['typecast'] = new Typecast();
	}

	return $GLOBALS['typecast'];
}
?>