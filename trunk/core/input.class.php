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

// Constant: I_NO_ERROR
define('I_NO_ERROR', null, true);

// Constant: I_CALLBACK_ERROR
define('I_CALLBACK_ERROR', 1, true);

// Constant: I_TYPE_ERROR
define('I_TYPE_ERROR', 2, true);

// Constant: I_FILE_ERROR
define('I_FILE_ERROR', 4, true);

// Constant: I_SHORT_ERROR
define('I_SHORT_ERROR', 8, true);

// Constant: I_LONG_ERROR
define('I_LONG_ERROR', 16, true);

/*
	Class: Input

	The Input class is used to create an instance of an HTML input tag, which
	can be used to then generate the tag itself, but multiple inputs can be
	combined by adding them to a <Form>, which then can also be displayed.

	The idea of the previous <Form> class being split up is so developers may
	lay out forms in a more flexible manner, while still having all the ease
	of use and security that was provided by the Form class.
*/
class Input
{
	// Variable: name
	// The name of the input field.
	private $name;

	// Variable: label
	// A label, or nice name, for this input field, used for notifying users
	// of any errors with their data they have entered.
	private $label;

	// Variable: type
	// The type of the input field. See <Input::type> for more information.
	private $type;

	// Variable: request_type
	// Where the value will come from ($_GET, $_POST, $_REQUEST).
	private $request_type;

	// Variable: length
	// An array containing length restrictions. See <Input::length> for more
	// information.
	private $length;

	// Variable: truncate
	// A boolean containing whether or not the value should be truncated if it
	// is out of the set length range.
	private $truncate;

	// Variable: options
	// An array containing options for certain types. See <Input::options> for
	// more information.
	private $options;

	// Variable: callback
	// A string containing the name of a callback to be called before any
	// validation is done by the Input class. See <Input::callback> for more
	// information.
	private $callback;

	// Variable: default_value
	// The default value of the input.
	private $default_value;

	// Variable: disabled
	// Whether or not the input field is disabled.
	private $disabled;

	// Variable: readonly
	// Whether or not the input field is read-only.
	private $readonly;

	// Variable: rows
	// The number of rows in the field. Only valid for certain types. See
	// <Input::rows> for more information.
	private $rows;

	// Variable: columns
	// The number of columns in the textarea. Only valid for certain types.
	// See <Input::columns> for more information.
	private $columns;

	// Variable: valid
	// A boolean containing whether or not the value set by the user is
	// valid or not.
	private $valid;

	// Variable: value
	// The sanitized/validated value specified by the user.
	private $value;

	// Variable: error
	// The error caused by user input. This attribute is different than
	// $errors, which contains errors caused by the options of the input field
	// itself.
	private $error;

	// Variable: error_type
	// Contains the error code caused by user input.
	private $error_type;

	// Variable: errors
	// An array containing any errors with the input field.
	private $errors;

	/*
		Constructor: __construct

		Sets up all the default values for the attributes, along with setting
		any other user specified value.

		Parameters:
			string $name - The name of the input field.
			string $label - The label, or nice name, for the input field.
			string $type - The type of the input field. See <Input::type> for a
										 list of valid types.
			string $request_type - Where the variables value is expected to come
														 from. See <Input::request_type> for more
														 information.
			array $length - An array containing length constraints. See
											<Input::length> for more information.
			bool $truncate - Whether or not the value should be truncated if it is
											 outside of the specified length constraints. See
											 <Input::truncate> for more information.
			array $options - An array containing options for the input field. See
											 <Input::options> for more information.
			callback $callback - A valid callback which will be called before any
													 internal validation is done by the Input class.
													 See <Input::callback> for more information.
		  mixed $default_value - The default value of the input if no other
														 value is specified. See <Input::default_value>
														 for more information.
			bool $disabled - Whether or not the input field is disabled.
			bool $readonly - Whether or not the input field is read-only.
			int $rows - The number of rows in the field. See <Input::rows> for
									more information.
			int $columns - The number of columns in the textarea. See
										 <Input::columns> for more information.

		Note:
			If you would rather use a method which can return whether or not your
			settings did not contain any errors, please use <Input::set>.
	*/
	public function __construct($name = null, $label = null, $type = null, $request_type = null, $length = null, $truncate = false, $options = array(), $callback = null, $default_value = null, $disabled = false, $readonly = false, $rows = null, $columns = null)
	{
		// Set everything to blanks and what not.
		$this->name = null;
		$this->label = null;
		$this->type = null;
		$this->request_type = 'post';
		$this->length = array(
											'min' => null,
											'max' => null,
										);
		$this->truncate = false;
		$this->options = array();
		$this->callback = null;
		$this->default_value = null;
		$this->disabled = false;
		$this->readonly = false;
		$this->rows = null;
		$this->columns = null;
		$this->valid = false;
		$this->value = null;
		$this->error = null;
		$this->error_type = null;
		$this->valid = null;
		$this->errors = array();

		// Let's see, did you want to set anything?
		if(!empty($name))
		{
			$this->set($name, $label, $type, $request_type, $length, $truncate, $options, $callback, $default_value, $disabled, $readonly, $rows, $columns);
		}
	}

	/*
		Method: name

		Sets or returns the currently set name of the input field.

		Parameters:
			string $name - The name of the input field.

		Returns:
			mixed - Returns the current name of the input field if $name is left
							null, otherwise returns true if the name was set successfully,
							false if the name is invalid.

		Note:
			Please note that there is no validation as to whether this input
			fields name is unique within the context of the page.
	*/
	public function name($name = null)
	{
		// Are you setting the name?
		if($name !== null)
		{
			// Looks like you are. But is there anything to this name?
			if(strlen($name) > 0)
			{
				// Seems okay to me.
				$this->name = $name;

				return true;
			}
			else
			{
				// A name needs to contain something.
				return false;
			}
		}
		else
		{
			// We shall return the current name, then.
			return $this->name;
		}
	}

	/*
		Method: label

		Sets or returns the currently set label of the input field. This label
		is used when notifying a user of problems with the data they have
		entered into this specific input field, so be sure the label is unique
		and properly identifies the field that the issue may have came from.

		Parameters:
			string $label - The label for the input field.

		Returns:
			mixed - Returns the current label if $label is left empty, but true on
							success when the label is being set, and false on failure.
	*/
	public function label($label = null)
	{
		if($label !== null)
		{
			// The label cannot be empty.
			if(strlen($label) > 0)
			{
				$this->label = $label;

				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			// Just return the current label.
			return $this->label;
		}
	}

	/*
		Method: type

		Sets or returns the currently set type of the input field.

		Parameters:
			string $type - The type of the field to set.

		Returns:
			mixed - Returns a string containing the type of the field if $type is
							left null, but true if the type was successfully set, and
							false if the type is not supported.

		Note:
			The following types are supported:

				hidden - A hidden input field, which can contain any value that is a
								 string or number.
				int - An integer.
				double - A double.
				string - A string, however it is passed through <htmlchars>.
				string-html - A string, similar to the above type without being
											passed through <htmlchars>.
				textarea - The same as the string type (and will be passed through
									 <htmlchars>), except this input field would be displayed
									 as a textarea tag.
				textarea-html - Just as the above type, but will not be passed
												through <htmlchars>.
				password - A password field.
				checkbox - A checkbox field.
				checkbox-multi - A list of multiple checkboxes.
				select - An drop-down list using the <select> tag.
				select-multi - An options list, but multiple values may be selected
											 at once.
				radio - A list of radio buttons.
				file - A file upload field.
				callback - This means that the Input class will do no validation
									 itself, and all validation will be handled by the
									 specified callback. See <Input:: callback> for more
									 information.

		This is how the types will be returned via <Input::value>:

			hidden - As is (see string).
			int - As is.
			double - As is.
			string - As is with HTML tags encoded.
			string-html - As is.
			textarea - See string.
			textarea-html - See string-html.
			password - As is.
			checkbox - 0 for unchecked, 1 for checked.
			checkbox-multi - See select, with the exception that the return value
											 would be an array containing all checked boxes.
			select - The index of the selected option value. For example, if
							 options was array('This setting', 'Another setting'): if
							 "Another setting" was chosen, 1 would be returned as that is
							 the index of the value in the array, however you may do
							 'another' => 'Another setting' in the options array, and
							 "another" would be the returned value.
			select-multi - See select, except each selected option will be
										 contained within an array.
			radio - The select options key will be passed.
			file - The array from $_FILES will be passed.
			callback - Whatever value the callback says is valid.
	*/
	public function type($type = null)
	{
		// Setting the type?
		if($type !== null)
		{
			// Looks like you are!
			// But you can't set just any type.
			if(in_array(strtolower($type), array('hidden', 'int', 'double', 'string', 'string-html',
																						'textarea', 'textarea-html', 'password', 'checkbox',
																						'checkbox-multi', 'select', 'select-multi', 'radio',
																						'file', 'callback')))
			{
				if($this->type != strlen($type))
				{
					$this->changed();
				}

				$this->type = strtolower($type);

				return true;
			}
			else
			{
				// I have no idea what you are talking about!
				return false;
			}
		}
		else
		{
			// Just return the type.
			return $this->type;
		}
	}

	/*
		Method: request_type

		Sets or returns the currently set location of where the value of the
		input field will be retrieved.

		Parameters:
			string $request_type - Must be either get (for $_GET), post (for
														 $_POST), or request (for $_REQUEST).

		Returns:
			mixed - Returns the current request type if $request_type is left
							empty, but true if the new request type was set successfully,
							and false if not.

		Note:
			If this option is never set, it will default to post.
	*/
	public function request_type($request_type = null)
	{
		if($request_type !== null)
		{
			if(in_array(strtolower($request_type), array('get', 'post', 'request')))
			{
				if($this->request_type != strtolower($request_type))
				{
					$this->changed();
				}

				$this->request_type = strtolower($request_type);

				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $this->request_type;
		}
	}

	/*
		Method: length

		Sets or returns the currently set length constraints.

		Parameters:
			array $length - An array containing at least one of two indices: min
											and max. See notes for more details.

		Returns:
			mixed - Returns an array containing the currently set length
							constraints, but true if the constraint is set successfully,
							false if the constraint information is invalid.

		Note:
			The length constraints have different meanings depending upon the
			<Input::type> currently set.

			For example, if the type is a number (int, double) and a min of 6 is
			set, and a max of 982 is set, and the user specifies a a number any
			larger than the max would be considered invalid. The same would be
			true if the user specified anything lower than set minimum of 6.

			However, if the type is a string (string, string-html, textarea,
			textarea-html, hidden or password), then these set constraints refer
			to the length of the string itself (strlen), so if a min of 5 was set
			and a max of 100 was set, then the input must be at least 5 characters
			long and no more than 100 characters to be considered valid.

			A select-multi, along with checkbox-multi, may have minimums and
			maximums set as well. By setting a minimum, this tells the input how
			many options a user must select at the very least, and a maximum tells
			the field how many options they may select at the most. Please see
			<Input::truncate> for more information about maximums with such types.

			This option may also apply to the file type, where min and max refer
			to the size of the file size, in bytes. The <Input::truncate> option
			has absolutely no effect with file types.

			Both indices (min, max) do not have to be set, as you can set just a
			min or just a max if you please.

			Please keep in mind that the behavior of length constraints is changed
			if <Input::truncate> is set to true (false by default).

			Also, if you would like to clear either the min or max value, set
			them to false, setting them to null will not do anything!
	*/
	public function length($length = null)
	{
		if($length !== null && is_array($length))
		{
			// We don't want to set anything until we are sure they are both valid
			// if there are two, that is.
			$tmp = array(
							 'min' => null,
							 'max' => null,
						 );

			// Do you have a min index set?
			// Also make sure that the min index is actually a number, or false.
			if(isset($length['min']) && ($length['min'] === false || (string)$length['min'] == (string)(int)$length['min']))
			{
				$tmp['min'] = $length['min'] !== false ? (int)$length['min'] : null;
			}
			elseif(isset($length['min']))
			{
				// Well, seems as though you specified something invalid.
				return false;
			}

			// Now do the same for the max index.
			if(isset($length['max']) && ($length['max'] === false || (string)$length['max'] == (string)(int)$length['max']))
			{
				$tmp['max'] = $length['max'] !== false ? (int)$length['max'] : null;
			}
			elseif(isset($length['max']))
			{
				// INVALID!
				return false;
			}

			// Make sure your minimum isn't larger than your maximum...
			if($tmp['min'] !== null && $tmp['max'] !== null && $tmp['min'] > $tmp['max'])
			{
				// Yeah, that's no good.
				return false;
			}

			if($tmp['min'] !== $this->length['min'] || $tmp['max'] !== $this->length['max'])
			{
				$this->changed();
			}

			// Seems like there were no issues...
			if($tmp['min'] !== null)
			{
				$this->length['min'] = $tmp['min'];
			}

			if($tmp['max'] !== null)
			{
				$this->length['max'] = $tmp['max'];
			}

			return true;
		}
		else
		{
			// Looks like you want to take a look at what's set right now.
			return $this->length;
		}
	}

	/*
		Method: truncate

		Sets or returns whether the truncation behavior is enabled.

		Parameters:
			bool $truncate - Whether or not to truncate a value if it isn't within
											 the specified length constraints. (<Input::length>)

		Returns:
			bool - Returns a bool indicating whether or not the truncation
						 behavior is enabled (even if $truncate isn't left empty, this
						 will return the set value after it is updated).

		Note:
			This option has no effect unless a <Input::length> constraint is put
			in place.

			Just as with the length constraints, this option will have different
			effects depending upon the current type of the field.

			If the type is a number (int, double), with a min of 5 set and a max
			of 145 set, and the user specifies a value of 200, the value WOULD NOT
			be considered invalid. The value would be changed to the maximum
			allowed number, in this case 145. This also means if the value
			specified is under 5, the value would be changed to 5.

			Now, if the type is a string (string, string-html, textarea,
			textarea-html, hidden or password), the only effect this option will
			have is if the string is too long. So if the max is set to 255, and
			the string is any longer than 255 characters, it would be truncated
			to only be 255 characters in length. However, if the value is below
			a set minimum, the value would still be considered invalid.

			Please take notice that the length of the string could still exceed
			the set maximum if the input is of type string, textarea or hidden,
			as the length of the string is checked BEFORE being ran through
			<htmlchars>.

			Field types of select-multi and checkbox-multi are also affected by
			this option. If a maximum of 3 is set (meaning the user may only
			select up to 3 options) and truncate set to false (which is the
			default behavior), if the user selects more than the 3 options allowed
			the user will be notified that they are not allowed to select that
			many options. However, if truncate is set to true, then the user
			would not be notified of any such issues, and the selected options
			above and beyond the first 3 will simply be removed.
	*/
	public function truncate($truncate = null)
	{
		// Setting a new value? Alrighty.
		if($truncate !== null)
		{
			if($this->truncate != !empty($truncate))
			{
				$this->changed();
			}

			$this->truncate = !empty($truncate);
		}

		// We will return the set value, no matter what!
		return $this->truncate;
	}

	/*
		Method: options

		Sets or returns the currently set options which are used with types such
		as select, select-multi, radio, etc.

		Parameters:
			array $options - An array of options.

		Returns:
			mixed - Returns an array containing the current options if $options is
							left empty, but true if the new options were set successfully,
							false if the array of options is invalid.

		Note:
			The $options array must be formatted in one of two ways.

			The array can either be set up as such: array('option 1', 'option 2',
			 'option 3'), in which case the values returned would be the index of
			the option (starting from 0, naturally). But the options array may
			also have string indices, like: array('opt-1' => 'option 1', 'opt-2'
			 => 'option 2'), which would mean the values returned would be opt-1
			or opt-2.

			Of course, you can not have a value (which is the label for that
			option) which is not a string or number.

			These option values are passed through <htmlchars>.
	*/
	public function options($options = null)
	{
		// Setting some new options?
		if($options !== null && is_array($options))
		{
			// We know it is an array, but are all the options strings?
			// or numbers, of course!
			foreach($options as $index => $value)
			{
				// Let's see!
				if(!is_string($value))
				{
					// Nope, invalid! Sorry!
					return false;
				}
			}

			if($this->options != $options)
			{
				$this->changed();
			}

			// Seems like it's okay to me.
			$this->options = $options;

			return true;
		}
		else
		{
			return $this->options;
		}
	}

	/*
		Method: callback

		Sets or returns the currently set callback which is called before any
		validation is done by the Input class itself.

		Parameters:
			callback $callback - Any valid callback format, such as a string
													 containing the functions name, an array
													 containing the instance of an object and the
													 method to be called, such as:
													 array($myObj, 'myMethod').

		Returns:
			mixed - Returns the current callback if $callback is left empty, but
							true if the new callback was successfully set, and false if
							the supplied callback isn't actually, um, callable.

		Note:
			To verify whether or not this callback is valid, it is checked with
			the is_callable function (<www.php.net/is_callable>).

			A callback must accept three parameters (if the input's type is not a
			callback), the first being the name of the field, the second being the
			value specified by the user, and a third, which must be a reference
			parameter, which will contain an error message if there is any error.
			Also, this callback must return a bool, true if the value passed is
			valid, false if not.

			If the input's type is a callback then the callback must accept four
			parameters, the first three being those described above, but the last
			being a flag, which will be set to true if the input is requesting
			that the callback return the HTML for the input tag that this
			callback will generate.

			Take note that no callback is required, unless the specified
			<Input::type> is callback.
	*/
	public function callback($callback = null)
	{
		if($callback !== null)
		{
			// Just need to make sure it is actually callable.
			if(is_callable($callback))
			{
				if($this->callback != $callback)
				{
					$this->changed();
				}

				$this->callback = $callback;

				return true;
			}
			else
			{
				// Nope, it is not callable!
				return false;
			}
		}
		else
		{
			return $this->callback;
		}
	}

	/*
		Method: default_value

		Sets or returns the currently set default value for the input field.

		Parameters:
			mixed $default_value - The default value of the field, see the notes
														 for more information.

		Returns:
			mixed - Returns the default value if $default_value is left empty, but
							true if the default value was set successfully, false if not.

		Note:
			There are different types of default values which can be accepted,
			depending upon the current <Input::type> set.

			Input fields with a type of int or double must, of course, be of that
			type. Same goes for string fields as well.

			If the input field has a type of select or radio, then the default
			value must be that of the options index (which could be numerical or
			a string).

			If the type is select-multi or checkbox-multi, then the default value
			must be an array containing the indices of the options selected or
			checked.

			If the type is checkbox, then the default value must be either a 1 or
			a 0, where 1 means it is checked, and 0 means it is not checked.

			There is very little validation done in this method, this is because
			the type may not yet be set! Therefore the method cannot determine
			whether or not the default value is actually valid. Not only that,
			but if it did do validation here, the type could be changed then
			allowing invalid default values. Because of this, all validation as to
			whether or not the input field is set up properly is done within the
			<Input::validate> method.

			Take note that even default values are checked against length
			constraints specified with <Input::length>! This will not occur until
			the input field is validated, though.

	*/
	public function default_value($default_value = null)
	{
		// Setting a new default value?
		if($default_value !== null)
		{
			// Looks like it.
			// There are two types we are expecting, one of which is a string.
			if(is_string($default_value))
			{
				// Not much validation can be done, yet.
				$this->default_value = $default_value;
			}
			// Could it be an array?
			elseif(is_array($default_value))
			{
				// We only want the values themselves.
				$values = array();

				foreach($default_value as $value)
				{
					// Which can only be a string.
					if(!is_string($value))
					{
						return false;
					}

					$values[] = $value;
				}

				// Seems to be alright, for now.
				$this->default_value = $values;
			}
			else
			{
				// Well, that's not right!
				return false;
			}

			if($this->default_value != $default_value)
			{
				$this->changed();
			}

			return true;
		}
		else
		{
			return $this->default_value;
		}
	}

	/*
		Method: disabled

		Sets or returns whether the input field is disabled.

		Parameters:
			bool $disabled - Whether or not the input field is to be disabled.

		Returns:
			bool - Returns true if the input field is disabled, false if not. The
						 currently set value will always be returned, whether or not the
						 input fields disabled status is being changed or not.

		Note:
			If the field is disabled, then the value which will be returned (and
			displayed if done so with <Input::generate>) will always be the set
			<Input::default_value>.
	*/
	public function disabled($disabled = null)
	{
		// Changing the input fields disabled status?
		if($disabled !== null)
		{
			if($this->disabled != !empty($disabled))
			{
				$this->changed();
			}

			$this->disabled = !empty($disabled);
		}

		return $this->disabled;
	}

	/*
		Method: readonly

		Sets or returns whether the input field is read-only.

		Parameters:
			bool $readonly - Whether or not the input field is to be read-only.

		Returns:
			bool - Returns true if the input field is read-only, false if not. The
						 currently set value will always be returned, whether or not the
						 input fields read-only status is being changed or not.

		Note:
			If the field is read-only, then the value which will be returned (and
			displayed if done so with <Input::generate>) will always be the set
			<Input::default_value>.
	*/
	public function readonly($readonly = null)
	{
		// Changing something?
		if($readonly !== null)
		{
			if($this->readonly != !empty($readonly))
			{
				$this->changed();
			}

			$this->readonly = !empty($readonly);
		}

		return $this->readonly;
	}

	/*
		Method: rows

		Sets or returns the currently set number of rows to display with certain
		types.

		Parameters:
			int $rows - The number of rows to display. See the notes for more
									information.

		Returns:
			mixed - Returns the current amount of rows to display if $rows is left
							empty, but true if the new value is set successfully, false if
							not.

		Note:
			This option is only valid for types of textarea, textarea-html and
			select-multi.
	*/
	public function rows($rows = null)
	{
		if($rows !== null)
		{
			// Make sure the number isn't too small.
			if($rows > 0)
			{
				$this->rows = (int)$rows;

				return true;
			}
			else
			{
				// Yup, you can't have 0 rows, either!
				return false;
			}
		}
		else
		{
			return $this->rows;
		}
	}

	/*
		Method: columns

		Sets or returns the currently set number of columns to display with
		textareas.

		Parameters:
			int $columns - The number of columns to display on a textarea.

		Returns:
			mixed - Returns the current amount of columns to display if $columns
							is left empty, but true if the new value is set successfully,
							false if not.

		Note:
			This option is only valid for types of textarea and textarea-html.
	*/
	public function columns($columns = null)
	{
		if($columns !== null)
		{
			if($columns > 0)
			{
				$this->columns = (int)$columns;

				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $this->columns;
		}
	}

	/*
		Method: set

		This method can be used to set all options for the input field with just
		one call.

		Parameters:
			string $name - See <Input::name>.
			string $label - See <Input::label>.
			string $type - See <Input::type>.
			string $request_type - See <Input::request_type>.
			array $length - See <Input::length>.
			bool $truncate - See <Input::truncate>.
			array $options - See <Input::options>.
			callback $callback - See <Input::callback>.
			mixed $default_value - See <Input::default_value>.
			bool $disabled - See <Input::disabled>.
			bool $readonly - See <Input::readonly>.
			int $rows - See <Input::rows>.
			int $columns - See <Input::columns>.

		Returns:
			bool - Returns true if all the options set would allow this instance
						 of Input to work without issue, false if there are conflicting
						 or missing options.

		Note:
			This method will return the results of <Input::validate> after all
			options are set. If this returns false, you may check out the method
			<Input::errors>.

			If any error occurs, the previous state of this instance will be
			restored, as if this method were never called.
	*/
	public function set($name, $label, $type, $request_type, $length, $truncate, $options, $callback, $default_value, $disabled, $readonly, $rows, $columns)
	{
		// We may need to revert back later.
		$prev_options = array(
											'name' => $this->name,
											'label' => $this->label,
											'type' => $this->type,
											'request_type' => $this->request_type,
											'length' => $this->length,
											'truncate' => $this->truncate,
											'options' => $this->options,
											'callback' => $this->callback,
											'default_value' => $this->default_value,
											'disabled' => $this->disabled,
											'readonly' => $this->readonly,
											'rows' => $this->rows,
											'columns' => $this->columns,
										);

		// Set the name, type, length, etc. etc.
		if(!$this->name($name))
		{
			// We require a name, so...
			$this->revert($prev_options);
			$this->errors[] = l('The name of the field was left empty.');

			return false;
		}

		// The label is very important, so we need that to be okay.
		if(!$this->label($label))
		{
			$this->revert($prev_options);
			$this->errors[] = l('The label of the field was left empty.');

			return false;
		}

		// The type must be valid as well.
		if(!$this->type($type))
		{
			$this->revert($prev_options);
			$this->errors[] = l('The supplied type of "%s" is invalid.', htmlchars($type));

			return false;
		}

		// No biggy.
		$this->request_type($request_type);

		// Length isn't required...
		$this->length($length);

		// Neither is truncate.
		$this->truncate($truncate);

		// Just make sure options are okay if specified.
		if(is_array($options) && !$this->options($options))
		{
			$this->revert($prev_options);
			$this->errors[] = l('The option supplied are invalid.');

			return false;
		}

		// Make sure the callback is okay, if specified.
		if(!empty($callback) && !$this->callback($callback))
		{
			$this->revert($prev_options);
			$this->errors[] = l('The callback supplied is not valid.');

			return false;
		}

		// How about a default value?
		if($default_value !== null && !$this->default_value($default_value))
		{
			$this->revert($prev_options);
			$this->errors[] = l('The default value supplied is not valid.');

			return false;
		}

		// The rest of these are not required.
		$this->disabled($disabled);
		$this->readonly($readonly);
		$this->rows($rows);
		$this->columns($columns);

		// Well, we did part of the job here... Now to do the rest in validate.
		if($this->validate())
		{
			// It's all good!!!
			return true;
		}
		else
		{
			// You did something wrong. I don't know what, but you did it wrong!!!
			$this->revert($prev_options);

			return false;
		}
	}

	/*
		Method: validate

		This method will validate all the currently set options for the input
		field, to make sure that there are no conflicting options or missing
		options that are only required under certain circumstances.

		Parameters:
			none

		Returns:
			bool - Returns true if all the current options for the input field are
						 valid, and would allow the input field to operate as it should.

		Note:
			If this method returns false, you should check out <Input::errors> to
			see what all the trouble is about.
	*/
	public function validate()
	{
		if(count($this->errors) > 0)
		{
			return false;
		}

		// We need options if the type is select, select-multi, checkbox-multi
		// or radio.
		if(in_array($this->type, array('select', 'select-multi', 'checkbox-multi', 'radio')))
		{
			// No options, at all?
			if(!is_array($this->options) || count($this->options) == 0)
			{
				$this->errors[] = l('Type &quot;%s&quot; requires options to be specified.', htmlchars($this->type));

				return false;
			}

			// Need to make sure the default value is actually an option.
			if(($this->type == 'select' || $this->type == 'radio') && (!is_string($this->default_value) || !in_array($this->default_value, $this->options)))
			{
				// Just chuck it out, then.
				$this->default_value = null;
			}
			elseif($this->type == 'select-multi' || $this->type == 'checkbox-multi')
			{
				// Is the default value not an array? That's easy to fix.
				if(!is_array($this->default_value))
				{
					$this->default_value = array($this->default_value);
				}

				// Go through all the options, and make sure they are available.
				foreach($this->default_value as $index => $value)
				{
					// Just a simple check.
					if(!in_array($value, $this->options))
					{
						// Wasn't found, so remove it.
						unset($this->default_value[$index]);
					}
				}

				// Just set the default value to null if there is nothing in the
				// array now.
				if(count($this->default_value) == 0)
				{
					$this->default_value = 0;
				}
			}
		}

		// Is this input of type callback? Well then, we need a callback!
		if($this->type == 'callback' && !is_callable($this->callback))
		{
			$this->errors[] = l('A callback is required for input types of callback.');

			return false;
		}

		// Wow, that was easier than I thought! Really!
		return true;
	}

	/*
		Method: revert

		Reverts all options back to the previous state.

		Parameters:
			array $prev_options - An array containing the options to restore to.

		Returns:
			void - Nothing is returned by this method.

		Note:
			This method is private.
	*/
	private function revert($prev_options)
	{
		// Just set them all back.
		$this->name = $prev_options['name'];
		$this->label = $prev_options['label'];
		$this->type = $prev_options['type'];
		$this->request_type = $prev_options['request_type'];
		$this->length = $prev_options['length'];
		$this->truncate = $prev_options['truncate'];
		$this->options = $prev_options['options'];
		$this->callback = $prev_options['callback'];
		$this->default_value = $prev_options['default_value'];
		$this->disabled = $prev_options['disabled'];
		$this->readonly = $prev_options['readonly'];
		$this->rows = $prev_options['rows'];
		$this->columns = $prev_options['columns'];
	}

	/*
		Method: value

		Returns the value of the input field, with regards to all the options
		set, along with any user input.

		Parameters:
			none

		Returns:
			mixed - Returns the current value of the input field.

		Note:
			All validation of the value for the input field is contained within
			this method.

			Please note that this method calls upon the <Input::validate> method
			to make sure everything is in "tip-top shape." If validate returns
			false, so will this method!

			Also, false will be returned in the event that the users input is
			invalid. To make sure that this isn't a real value that is NOT invalid
			be sure to call on <Input::valid>, which will return true if the value
			is valid, and false if not.
	*/
	public function value()
	{
		global $func;

		// Do we have a value, if we do, then we don't need to go through this
		// again.
		if(!empty($this->valid))
		{
			return $this->value;
		}

		// Make sure all options are in order.
		if(!$this->validate())
		{
			$this->valid = false;

			// Uh, no... They are not. Something is wrong.
			return false;
		}

		// Reset a couple things.
		$this->valid = false;
		$this->error = null;
		$this->error_type = null;
		$this->value = null;

		// Get the value of this field, unless it is either not found, or if
		// the field is disabled or read-only, in which case the default value
		// will be used.
		$value = $this->request_type == 'post' ? (isset($_POST[$this->name]) ? $_POST[$this->name] : null) : ($this->request_type == 'get' ? (isset($_GET[$this->name]) ? $_GET[$this->name] : null) : (isset($_REQUEST[$this->name]) ? $_REQUEST[$this->name] : null));

		// I just wanted to make life a bit easier with what I did above, so:
		if(!isset($value) || $this->disabled || $this->readonly)
		{
			// Default value it is, then!
			$value = $this->default_value;
		}

		// Here we go!!!
		if(!empty($this->callback))
		{
			// We may need this.
			$error_message = null;

			// Now call that callback.
			if(!call_user_func_array($this->callback, array($this->name, &$value, &$error_message)))
			{
				// Uh oh!
				$this->error = $error_message;
				$this->error_type = I_CALLBACK_ERROR;

				return false;
			}
		}

		// It's time for validation!
		// First off, strings!
		if(in_array($this->type, array('hidden', 'string', 'string-html', 'textarea',
																		'textarea-html', 'password')))
		{
			// Make sure it is a string...
			if(!typecast()->is_a('string', $value))
			{
				$this->error = l('The field &quot;%s&quot; must be a string.', htmlchars($this->label));
				$this->error_type = I_TYPE_ERROR;

				return false;
			}

			// Still going? Then let's see if the field's data needs encoding.
			if(in_array($this->type, array('hidden', 'string', 'textarea')))
			{
				$value = htmlchars($value);
			}
		}
		// Some sort of number?
		elseif($this->type == 'int' || $this->type == 'double')
		{
			// We can do the same thing as we did with string types.
			if(!typecast()->is_a($this->type, $value))
			{
				$this->error = l('The field &quot;%s&quot; must be '. ($this->type == 'int' ? ' an integer' : 'a number'). '.', htmlchars($this->label));
				$this->error_type = I_TYPE_ERROR;

				return false;
			}
		}
		// Checkboxes are easy!
		elseif($this->type == 'checkbox')
		{
			// All we care about is whether they were checked or not.
			$value = !empty($value);
		}
		// Now for select's, whether it be a single one or multiple.
		elseif($this->type == 'select' || $this->type == 'select-multi')
		{
			$is_multiple = $this->type == 'select-multi';

			// We will need a couple temporary arrays, one of which will hold the
			// select options, and another will be the options themselves!
			$selected = array();
			$options = array_keys($this->options);

			if($is_multiple && is_array($value) && count($value) > 0)
			{
				foreach($value as $option_id)
				{
					// Let's make sure that the option they choose is one that
					// actually exists.
					if(in_array($option_id, $options))
					{
						// Everything seems fine here.
						$selected[] = $option_id;
					}
				}
			}
			else
			{
				// Make sure they selected a valid option.
				if(!in_array($value, $options))
				{
					$this->error = l('Please select an option for the field &quot;%s&quot;.', htmlchars($this->label));
					$this->error_type = I_TYPE_ERROR;

					return false;
				}
			}

			// Everything is fine, so save those options selected.
			return $value;
		}
		// Multiple checkboxes? Crazy!
		elseif($this->type == 'checkbox-multi')
		{
			// Need a couple arrays as well.
			$checked = array();
			$options = array_keys($this->options);

			if(is_array($value) && count($value) > 0)
			{
				foreach($value as $option_id => $is_checked)
				{
					// Did they check a valid option?
					if($is_checked == 1 && in_array($option_id, $options))
					{
						// Seems like they did.
						$checked[] = $option_id;
					}
				}
			}

			$value = $checked;
		}
		// Does anyone even listen to radio anymore? Oh, right -- it's not that
		// kind of radio! My bad.
		elseif($this->type == 'radio')
		{
			// Make sure the option they selected is okay.
			if(!in_array($value, array_keys($this->options)))
			{
				$this->error = l('Please select an option for the field &quot;%s&quot;.', htmlchars($this->label));
				$this->error_type = I_TYPE_ERROR;

				return false;
			}
		}
		// And finally, a file!
		elseif($this->type == 'file')
		{
			// Make sure the file is valid.
			if(isset($_FILES[$this->name]) && is_uploaded_file($_FILES[$this->name]['tmp_name']))
			{
				$value = $_FILES[$name];
			}
			else
			{
				// Was not an uploaded file. Hmmm...
				$this->error = l('The file uploaded for the field "%s" was not a valid.', htmlchars($this->label));
				$this->error_type = I_FILE_ERROR;

				return false;
			}
		}

		// Now it is time to check if this input does not meet the length
		// constraints defined.
		if(isset($this->length['min']) || isset($this->length['max']))
		{
			// Yeah, one of them is defined, but let's check out the field's type
			// first.
			if(in_array($this->type, array('hidden', 'string', 'string-html', 'textarea',
																			'textarea-html', 'password')))
			{
				// Is the value shorter than allowed?
				if(isset($this->length['min']) && $func['strlen']($value) < $this->length['min'])
				{
					$this->error = l('The field &quot;%s&quot; must be at least %d character'. ($this->length['min'] == 1 ? '' : 's'). ' in length.', htmlchars($this->label), $this->length['min']);
					$this->error_type = I_SHORT_ERROR;

					return false;
				}
				// Maybe longer? But should we truncate it, instead of an error?
				elseif(isset($this->length['max']) && empty($this->truncate) && $func['strlen']($value) > $this->length['max'])
				{
					$this->error = l('The field &quot;%s&quot; can not be longer than %d character'. ($this->length['max'] == 1 ? '' : 's'). ' in length.', htmlchars($this->label), $this->length['max']);
					$this->error_type = I_LONG_ERROR;

					return false;
				}
				elseif(isset($this->length['max']) && !empty($this->truncate) && $func['strlen']($value) > $this->length['max'])
				{
					// Just chop off anything that is out of the maximum range.
					$value = $func['substr']($value, 0, $this->length['max']);
				}
			}
			// Maybe it is a number of some sort.
			elseif($this->type == 'int' || $this->type == 'double')
			{
				if(isset($this->length['min']) && $value < $this->length['min'])
				{
					$this->error = l('The field &quot;%s&quot; must be no smaller than %'. ($this->type == 'int' ? 'd' : 'f'). '.', htmlchars($this->label), $this->length['min']);
					$this->error_type = I_SHORT_ERROR;

					return false;
				}
				elseif(isset($this->length['max']) && empty($this->truncate) && $value > $this->length['max'])
				{
					$this->error = l('The field &quot;%s&quot; must be no larger than %'. ($this->type == 'int' ? 'd' : 'f'). '.', htmlchars($this->label), $this->length['max']);
					$this->error_type = I_LONG_ERROR;

					return false;
				}
				elseif(isset($this->length['max']) && !empty($this->truncate) && $value > $this->length['max'])
				{
					$value = $this->type == 'int' ? (int)$this->length['max'] : (double)$this->length['max'];
				}
			}
			// Some multi select field?
			elseif($this->type == 'checkbox-multi' || $this->type == 'select-multi')
			{
				// Is it required users choose at least X options?
				if(isset($this->length['min']) && count($value) < $this->length['min'])
				{
					$this->error = l('The field &quot;%s&quot; requires that at least %d option'. ($this->length['min'] == 1 ? '' : 's'). ' be selected.', htmlchars($this->label), $this->length['min']);
					$this->error_type = I_SHORT_ERROR;

					return false;
				}
				elseif(isset($this->length['max']) && empty($this->truncate) && count($value) > $this->length['max'])
				{
					$this->error = l('The field &quot;%s&quot; requires that no more than %d option'. ($this->length['max'] == 1 ? '' : 's'). ' be selected.', htmlchars($this->label), $this->length['max']);
					$this->error_type = I_LONG_ERROR;

					return false;
				}
				elseif(isset($this->length['max']) && !empty($this->truncate) && count($value) > $this->length['max'])
				{
					// Chop off some options.
					list($value) = array_chunk($value, $this->length['max']);
				}
			}
			// A file, perhaps?
			elseif($this->type == 'file')
			{
				// Is there a minimum supplied? With files, this refers to the size
				// of the file.
				if(isset($this->length['min']) && filesize($value['tmp_name']) < $this->length['min'])
				{
					$this->error = l('The file uploaded for the field &quot;%s&quot; must be at least %s in size.', htmlchars($this->label), format_filesize($this->length['min'], true));
					$this->error_type = I_SHORT_ERROR;

					return false;
				}
				// Is the file's size too big? We don't do any truncation type thing
				// with files... Probably isn't a very good idea, anyways.
				elseif(isset($this->length['max']) && filesize($value['tmp_name']) > $this->length['max'])
				{
					$this->error = l('The file uploaded for the field &quot;%s&quot; must be no larger than %s in size.', htmlchars($this->label), format_filesize($this->length['max'], true));
					$this->error_type = I_LONG_ERROR;

					return true;
				}
			}
		}

		// Looks like we are done!
		// So it appears to be valid, and be sure to cache the value too!
		$this->valid = true;
		$this->value = $value;

		// Now you can have it!
		return $this->value;
	}

	/*
		Method: changed

		This is a private method, which is called when any options for the Input
		are modified, in which case the current value cached is considered to be
		invalid.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.
	*/
	private function changed()
	{
		// Reset the value information.
		$this->errors = array();
		$this->error = null;
		$this->error_type = null;
		$this->valid = null;
		$this->value = null;
	}

	/*
		Method: valid

		Returns a bool to indicate whether or not the value returned by the
		<Input::value> method is the actual value intended to be used. The
		reason for this is in case a callback returns false as a valid value.

		Parameters:
			none

		Returns:
			bool - Returns true if the value retrieved from <Input::value> is
						 valid, false if not.

		Note:
			See <Input::errors>, <Input::error> and <Input::error_type> for more
			information about possible causes for a value not being valid.
	*/
	public function valid()
	{
		// We may need to call on the value method to determine whether or not
		// the value is valid.
		if($this->valid === null)
		{
			$this->value();
		}

		return $this->valid;
	}

	/*
		Method: errors

		This method will return any validation errors which are caused due to
		misconfigurations with the input's options.

		Parameters:
			bool $only_last - Whether or not to only return the last error caused
												by misconfiguration. Defaults to true.

		Returns:
			mixed - Returns a string containing the last misconfiguration error if
							$only_last is true, but an array containing all errors if
							false. In the case that there aren't any errors, false will be
							returned.
	*/
	public function errors($only_last = true)
	{
		// First off, are there even any errors?
		if(count($this->errors) == 0)
		{
			// Nope.
			return false;
		}
		elseif(!empty($only_last))
		{
			return $this->errors[count($this->errors) - 1];
		}
		else
		{
			return $this->errors;
		}
	}

	/*
		Method: error

		Returns a user caused error due to invalid input.

		Parameters:
			none

		Returns:
			string - Returns a string containing the error generated by attempting
							 to validate the input from the user according to the input
							 options supplied. False is returned if no error has been
							 generated.

		Note:
			You may also obtain the error which occurred by getting the error code
			from <Input::error_type>.
	*/
	public function error()
	{
		// Return the error, if there is one.
		return !empty($this->error) ? $this->error : false;
	}

	/*
		Method: error_type

		Returns the error code which represents the type of error caused by user
		input. See the notes for more information on the types of error codes.

		Parameters:
			none

		Returns:
			int - Returns the error code, see notes for more information.

		Note:
			These are the error codes which are returned (they are constants, but
			their numerical value is also given):

			- I_NO_ERROR (null): No error caused by user input.
			- I_CALLBACK_ERROR (1): The error was thrown by a custom callback.
			- I_TYPE_ERROR (2): A type mismatch in the case of numerical and
				string types, along with radio and select types in the case that no
				option was selected (or a valid one).
			- I_FILE_ERROR (4): Can only occur with a file type, and this means
				that the file was either not uploaded, or some other error occurred
				with uploading the file to the server. More information can be
				retrieved from the $_FILES[INPUT_NAME]['error'] variable.
			- I_SHORT_ERROR (8): Caused by input being too short or too small. If
				the type is a string (string, string-html, textarea, etc.) this
				means the input did not meet the minimum length requirement in
				string size, if the type is a number (int, double) then the number
				entered is too small, if the type is a select-multi or
				checkbox-multi then too few options were selected, and if the type
				is a file then the file is too small.
			- I_LONG_ERROR (16): Similar to that of the I_SHORT_ERROR constant,
				only in this case, the input (string, number, file, options) is too
				large.

		You can use the constants to check which error occurred, you can also
		use the actual numbers as well, but this could cause issue if their
		values are ever changed.
	*/
	public function error_type()
	{
		return $this->error_type;
	}

	/*
		Method: generate

		Generates the HTML for the input tag, according to all the specified
		options currently set.

		Parameters:
			string $element_id - A string containing the HTML element's id
													 attribute value, if none is supplied, it will
													 default to the input's name.
			string $css_class - A string (or array) containing CSS classes to
												  assign to the HTML element.

		Returns:
			string - Returns a string containing the generated input, however if
							 <Input::validate> determines that the current options are
							 invalid, false will be returned.
	*/
	public function generate($element_id = null, $css_class = null)
	{
		// We can't generate this if the options are invalid.
		if(!$this->validate())
		{
			return false;
		}

		// No element ID? We will use this, then.
		if(empty($element_id))
		{
			$element_id = $this->name;
		}

		// Just make sure the ID is not going to screw anything up.
		$element_id = $element_id;

		// Any CSS classes? Maybe you gave us an array of them?
		if(is_array($css_class))
		{
			$css_class = implode(' ', $css_class);
		}

		// Get the value for the field, could be the one from the value method,
		// or somewhere else, if the value isn't valid.
		if($this->valid())
		{
			$value = $this->value();

			if(in_array($this->type, array('string-html', 'textarea-html', 'password')))
			{
				// These need to be made safe before being put into any tag.
				$value = htmlchars($value);
			}
		}
		// File types have no value that should be filled in.
		elseif($this->type != 'file')
		{
			// Get the value from where ever it is supposed to come from.
			$value = $this->request_type == 'post' ? (isset($_POST[$this->name]) ? $_POST[$this->name] : $this->default_value) : ($this->request_type == 'get' ? (isset($_GET[$this->name]) ? $_GET[$this->name] : $this->default_value) : (isset($_REQUEST[$this->name]) ? $_REQUEST[$this->name] : $this->default_value));

			// We may need to do something a little special if we are dealing with
			// a checkbox-multi type.
			if($this->type == 'checkbox-multi')
			{
				if(is_array($value))
				{
					$tmp = array();

					// We want to get the options checked.
					foreach($value as $option_id => $checked)
					{
						// Only add the option if it was checked.
						if($checked == 1)
						{
							$tmp[] = $option_id;
						}
					}

					$value = $tmp;
				}
				else
				{
					$value = array();
				}
			}
			// String types need to be made safe.
			elseif(in_array($this->type, array('hidden', 'string', 'string-html', 'textarea',
																					'textarea-html', 'password', 'int', 'double')))
			{
				$value = htmlchars($value);
			}
		}

		// Now, let's get started! Is it hidden, a string, password, or number?
		if(in_array($this->type, array('hidden', 'string', 'string-html',
																		'password', 'int', 'double')))
		{
			return '<input name="'. $this->name. '" id="'. $element_id. '"'. (!empty($css_class) ? ' class="'. $css_class. '"' : ''). ' type="'. (in_array($this->type, array('hidden', 'password')) ? $this->type : 'text'). '" value="'. $value. '"'. ($this->type != 'int' && $this->type != 'double' && isset($this->length['max']) ? ' maxlength="'. $this->length['max']. '"' : ''). (!empty($this->disabled) ? ' disabled="disabled"' : ''). (!empty($this->readonly) ? ' readonly="readonly"' : ''). ' />';
		}
		// How about a big ol' textarea?
		elseif($this->type == 'textarea' || $this->type == 'textarea-html')
		{
			return '<textarea name="'. $this->name. '" id="'. $element_id. '"'. (!empty($css_class) ? ' class="'. $css_class. '"' : ''). (isset($this->length['max']) ? ' onkeyup="if(this.value.length > '. $this->length['max']. ') { this.value = this.value.substr(0, '. $this->length['max']. '); }"' : ''). (isset($this->rows) ? ' rows="'. $this->rows. '"' : ''). (isset($this->columns) ? ' cols="'. $this->columns. '"' : ''). (!empty($this->disabled) ? ' disabled="disabled"' : ''). (!empty($this->readonly) ? ' readonly="readonly"' : ''). '>'. $value. '</textarea>';
		}
		// Checkbox?
		elseif($this->type == 'checkbox')
		{
			return '<input name="'. $this->name. '" id="'. $element_id. '"'. (!empty($css_class) ? ' class="'. $css_class. '"' : ''). ' type="checkbox" value="1"'. (!empty($value) ? ' checked="checked"' : ''). (!empty($this->disabled) ? ' disabled="disabled"' : ''). (!empty($this->readonly) ? ' readonly="readonly"' : ''). ' />';
		}
		elseif($this->type == 'checkbox-multi')
		{
			// Multiple checkboxes... Sweet.
			$checkboxes = array();

			foreach($this->options as $index => $label)
			{
				$checkboxes[] = '<label><input name="'. $this->name. '['. htmlchars($index). ']" id="'. $element_id. '"'. (!empty($css_class) ? ' class="'. $css_class. '"' : ''). ' type="checkbox" value="1"'. (in_array($index, $value) ? ' checked="checked"' : ''). (!empty($this->disabled) ? ' disabled="disabled"' : ''). (!empty($this->readonly) ? ' readonly="readonly"' : ''). ' /> '. htmlchars($label). '</label>';
			}

			return implode('<br />', $checkboxes);
		}
		// Some sort of select?
		elseif($this->type == 'select' || $this->type == 'select-multi')
		{
			$select = array(
									'<select name="'. $this->name. ($this->type == 'select-multi' ? '[]' : ''). '" id="'. $element_id. '"'. (!empty($css_class) ? ' class="'. $css_class. '"' : ''). ($this->type == 'select-multi' ? ' multiple="multiple"' : ''). ($this->type == 'select-multi' && $this->rows > 0 ? ' size="'. $this->rows. '"' : ''). (!empty($this->disabled) ? ' disabled="disabled"' : ''). '>',
								);

			// Now to add all the options!
			foreach($this->options as $index => $label)
			{
				$select[] = '<option value="'. htmlchars($index). '"'. ((!is_array($value) && $value == $index) || (is_array($value) && in_array($index, $value)) ? ' selected="selected"' : ''). '>'. htmlchars($label). '</option>';
			}

			$select[] = '</select>';

			// Put them all together!
			return implode("\r\n", $select);
		}
		// Radio buttons?
		elseif($this->type == 'radio')
		{
			$radios = array();

			foreach($this->options as $index => $label)
			{
				$radios[] = '<label><input name="'. $this->name. '" id="'. $element_id. '"'. (!empty($css_class) ? ' class="'. $css_class. '"' : ''). ' type="radio" value="'. htmlchars($index). '"'. (!empty($value) && $value == $index ? ' checked="checked"' : ''). (!empty($this->disabled) ? ' disabled="disabled"' : ''). (!empty($this->readonly) ? ' readonly="readonly"' : ''). ' /> '. $label. '</label>';
			}

			return implode('<br />', $radios);
		}
		elseif($this->type == 'file')
		{
			return '<input name="'. $this->name. '" id="'. $element_id. '"'. (!empty($css_class) ? ' class="'. $css_class. '"' : ''). ' type="file" value="'. $value. '"'. (!empty($this->disabled) ? ' disabled="disabled"' : ''). (!empty($this->readonly) ? ' readonly="readonly"' : ''). ' />';
		}
		elseif($this->type == 'callback')
		{
			// We have no idea how to handle this, but the callback should!
			return call_user_func($this->callback, false, false, false, true);
		}
	}
}
?>