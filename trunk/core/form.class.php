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

// The Form class kind of depends on the Input class, so if it doesn't
// exist, we'll have to fix that. Which is pretty easy...
if(!class_exists('Input'))
{
	require_once(coredir. '/input.class.php');
}

/*
	Class: Form

	This is a very useful tool which aids in the creation, and saving, of
	form information. It is recommended that you use this when creating,
	well, any type of form to allow plugins the ability to modify how a form
	operates.

	Originally the Form class was just one single component, but after SnowCMS
	2.0 alpha, the Form was overhauled and split up into two separate
	components, the other being the <Input> class which handles each input
	individually and separately from the entire form itself.

	Now input's can be created and then added to a form, or they can be
	created here.

	While this class can be used to display the Form in its entirety, it may
	also be used to generate bits and pieces of the form as well. More
	information on that later!
*/
class Form
{
	// Variable: forms
	// An array containing all the registered forms and their options.
	private $forms;

	// Variable: current
	// A string containing the name of the current form being used. This
	// allows a form name to be set which will be used when doing any form
	// operations such as adding another input. For more information on that,
	// see <Form::current>.
	private $current;

	/*
		Method: __construct

		Not much done here except initializing the attributes.

		Parameters:
			none
	*/
	public function __construct()
	{
		$this->forms = array();
		$this->current = null;
	}

	/*
		Method: current

		Sets or returns the current form to be used with any form operation if
		no form is specifically identified.

		Parameters:
			string $name - The name of the form to use by default when no other is
										 specified.

		Returns:
			mixed - Returns the name of the form that will be used if no other is
							specified if $name is left empty, but true if the default form
							was changed successfully and false if not (e.g. the form does
							not exist).

		Note:
			This affects such operations as <Form::add_input>,
			<Form::remove_input>, <Form::generate>, and any other which asks for
			a form identifier (other than methods directly correlating to forms
			themselves, such as <Form::add>, <Form::remove>, <Form::exists>, etc.)
			which makes things a bit simpler.
	*/
	public function current($name = null)
	{
		// Setting a new default form?
		if(!empty($name))
		{
			// The form needs to exist.
			if($this->exists($name))
			{
				$this->current = $name;

				return true;
			}
			else
			{
				// That form needs to be added first.
				return false;
			}
		}
		else
		{
			// Just return the current form name, then.
			return $this->exists($this->current) ? $this->current : null;
		}
	}

	/*
		Method: add

		Adds a new form which can then be used to create forms, of course!

		Parameters:
			string $name - The name of the form to create, which will be used as
										 the identifier when adding or any other form specific
										 related operation later on. Please note form names are
										 case insensitive.
			array $options - An array containing options about the form itself.
											 See the notes for more information.

		Returns:
			bool - Returns true if the form was added successfully, false on
						 failure.

		Note:
			The following is a list of indices which are supported by $options:

				string accept-charset - Specifies the supported character sets for
																the data contained within the form.

				string action - The URL where the form should submit the data to.

				callback callback - A callback which will be passed all the form
														information if the form data is valid.

				bool csrf-token - Whether or not a CSRF protection token to the
													form, see below for more information. Defaults to
													true.

				mixed enctype - Specifies how the form data will be encoded when
												sent. If a file field is added, the enctype will
												automatically be changed to multipart/form-data.

				string id - The unique HTML id for the form tag. This will be used
										to assign all added input's id's as well. Defaults to
										the form's name.

				string method - The way the form will be submitted, which is either
												post or get. Defaults to post. Also, all input's
												will be changed to the specified submission method.

				string submit - The text of the submit button. This may be
												irrelevant if the form is not rendered via the
												<Form::render> method.

			The only option required is to have a valid callback, which can be any
			callback supported by <www.php.net/call_user_func>.

			The callback is expected to accept two parameters. The first being an
			array which will contain the data from all the input's that exist
			within the form (formatted as such:
			array({input name} => mixed value)). The second parameter is a
			reference parameter which contains an array, which is to be used if
			any more errors occurred with the data received. This is useful if
			there is any extra validation that needs to be done. The callback also
			needs to return true if no errors occurred and false if there are
			errors which need to be displayed.

			PLEASE NOTE: The Form class automatically adds an input of its own,
			which is called {form name}_token, which is used for CSRF (Cross Site
			Request Forgery) protection. If <Form::open> and <Form::close> are
			called when displaying the form manually (e.g. without the use of
			<Form::render>) then this input will automatically be displayed on the
			web page as well. However, if they are not used, then the input must
			be displayed manually by using the <Form::generate> method with the
			input name of {form name}_token, otherwise the form will always be
			invalid and will show an error indicating that the form token was
			invalid. This automatic protection feature may be disabled by setting
			the csrf-token option to false.
	*/
	public function add($name, $options)
	{
		// Does this form exist already?
		if($this->exists($name) || empty($options['callback']) || !is_callable($options['callback']))
		{
			return false;
		}

		$name = strtolower($name);

		// Now setup the initial information about the form.
		$this->forms[$name] = array(
														'accept-charset' => null,
														'action' => null,
														'callback' => null,
														'enctype' => null,
														'errors' => array(),
														'inputs' => array(),
														'hooked' => false,
														'id' => $name,
														'method' => 'post',
														'submit' => l('Submit'),
													);

		// We will simply edit the form, and if the edit method returns false,
		// that means one of your options was incorrect.
		if(!$this->edit($name, $options))
		{
			// I guess something went wrong, so we will delete the form, then.
			$this->remove($name);

			return false;
		}

		// Do you want us to automatically add a token input which will be used
		// in the battle against CSRF attacks?!
		if(!isset($options['csrf-token']) || !empty($options['csrf-token']))
		{
			// Load up the Tokens class.
			$token = api()->load_class('Tokens');

			// We only want to recreate the token if it doesn't exist.
			if(!$token->exists($name. '_token'))
			{
				$token->add($name. '_token');
			}

			// Add the input.
			$this->add_input(array(
												 'name' => $name. '_token',
												 'label' => l('Token'),
												 'type' => 'hidden',
												 'request_type' => $this->forms[$name]['method'],
												 'callback' => create_function('$name, $value, &$error', '

																				 // Load up the Tokens class.
																				 $token = api()->load_class(\'Tokens\');

																				 // So, is this token valid?
																				 if($token->is_valid($name, $value))
																				 {
																					 // Everything is hunky dory!
																					 return true;
																				 }
																				 else
																				 {
																					 // Sorry, but we have reason to
																					 // believe you didn\'t submit
																					 // this form!!
																					 $error = l(\'Your security key is invalid. Please try resubmitting the form.\');

																					 return false;
																				 }'),
												 'default_value' => $token->token($name),
											 ), $name);
		}

		// Everything checks out. Now get to adding those inputs!
		return true;
	}

	/*
		Method: edit

		Allows the forms options to be edited.

		Parameters:
			string $name - The name of the form to be edited.
			array $options - An array containing the new options to be applied to
											 the specified form.

		Returns:
			bool - Returns true if the forms options were successfully edited,
						 false on failure (e.g. the form doesn't exist).

		Note:
			For options which can be edited, see <Form::add>, but there is one
			exception: you can not disable CSRF protection or enable it if it
			wasn't initially.

			However, you may easily disable CSRF protection by removing the input
			with a name of {form name}_token with <Form::remove_input>.
	*/
	public function edit($name, $options)
	{
		// We can't edit what isn't there!!
		if(!$this->exists($name))
		{
			return false;
		}

		// Lower-case it! STAT!
		$name = strtolower($name);

		// Changing the accepted character sets? Alright.
		if(isset($options['accept-charset']))
		{
			$this->forms[$name]['accept-charset'] = $options['accept-charset'];
		}

		// How about the callback? This we need to make sure is actually valid.
		if(isset($options['callback']) && is_callable($options['callback']))
		{
			$this->forms[$name]['callback'] = $options['callback'];
		}
		// Well, apparently the callback you supplied is very callable.
		elseif(isset($options['callback']))
		{
			return false;
		}

		// Changing the encoding type of the form?
		if(isset($options['enctype']))
		{
			$this->forms[$name]['enctype'] = $options['enctype'];
		}

		// How about an HTML id?
		if(isset($options['id']))
		{
			$this->forms[$name]['id'] = $options['id'];
		}

		// The way the form is submitted?
		if(isset($options['method']) && in_array(strtolower($options['method']), array('get', 'post')))
		{
			$this->forms[$name]['method'] = strtolower($options['method']);
		}
		elseif(isset($options['method']))
		{
			return false;
		}

		// Last, but not least, the text on the submit button... Maybe?
		if(isset($options['submit']))
		{
			$this->forms[$name]['submit'] = $options['submit'];
		}

		return true;
	}

	/*
		Method: exists

		Determines whether or not the specified form exists.

		Parameters:
			string $name - The name of the form to check for.

		Returns:
			bool - Returns true if the form exists, false if not.
	*/
	public function exists($name)
	{
		return !empty($name) && isset($this->forms[strtolower($name)]);
	}

	/*
		Method: remove

		Removes the specified form.

		Parameters:
			string $name - The name of the form to remove.

		Returns:
			bool - Returns true if the form was removed successfully, false if
						 not (e.g. the form does not exist).
	*/
	public function remove($name)
	{
		// Can't delete what's not there!
		if(!$this->exists($name))
		{
			return false;
		}

		// Just delete it. Not much to it.
		unset($this->forms[strtolower($name)]);

		return true;
	}

	/*
		Method: return_form

		Returns the specified form and all of its information.

		Parameters:
			string $name - The name of the form to return. If this parameter is
										 left empty then all forms will be returned.

		Returns:
			array - Returns an array containing the forms information, however if
							the specified form does not exist, false will be returned.
	*/
	public function return_form($name = null)
	{
		return !empty($name) ? ($this->exists($name) ? $this->forms[strtolower($name)] : false) : $this->forms;
	}

	/*
		Method: add_input

		Adds an input to the specified form.

		Parameters:
			mixed $input - This can either be an instance of an Input class, or
										 an array containing all the information required to
										 set up an instance of Input, as described by
										 <Input::set>.
			string $form_name - The name of the form to add this input to.
			int $position - The position of where the input should be inserted,
											starting at 0 (0 means the input will be placed before
											all others). If this is left empty, the input will,
											unsurprisingly, be added to the end.

		Returns:
			bool - Returns true if the input was added successfully, false if not
						 (which could mean that the form does not exist, or that the
						 instance of input was not valid, or that the information
						 supplied to create an instance of the input class was not
						 valid, or an input with the same name already exists).

		Note:
			If the instance of Input (whether it was directly supplied as an Input
			or created with the supplied options) is not valid according to the
			<Input::validate> method, then the input will not be added.

			Keep in mind, you do not have to specify the $form_name parameter if
			one was set with <Input::current>.
	*/
	public function add_input($input, $form_name = null, $position = null)
	{
		// Did you supply a form name? No? Well, let's get one.
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// Does it exist?
		if(!$this->exists($form_name))
		{
			return false;
		}

		// Now, is this an array or an Input?
		if(is_array($input))
		{
			// We shall attempt to instantiate an Input based upon your options,
			// then.
			$input = new Input($input);
		}
		// Could it be an Input?
		elseif(!is_object($input) || !($input instanceof Input))
		{
			// Nope, it is not.
			return false;
		}

		// The Input must validate, otherwise we won't use it.
		if(!$input->validate())
		{
			return false;
		}

		// Alright, so do you want this Input to go in a specific place?
		if(!isset($position) || $position === null || count($this->forms[$name]['inputs']) <= $position)
		{
			// Nope, you obviously don't care, so we will plop it in at the end!
			$this->forms[$name]['inputs'][strtolower($input->name())] = $input;
		}
		else
		{
			// We shall use the function array_insert, which resides within the
			// compat.php file.
			$this->forms[$name]['inputs'] = array_insert($this->forms[$name], $input, $position, $input->name());
		}

		// Did you add an input which is the type of a file? Then we will want
		// to change the encoding type of the form, otherwise the file upload
		// won't work.
		if($input->type() == 'file')
		{
			$this->forms[$name]['enctype'] = 'multipart/form-data';
		}

		// Alright. Everything seems to check out.
		return true;
	}

	/*
		Method: input_exists

		Checks to see if the specified input exists within the form.

		Parameters:
			string $name - The name of the input to check for.
			string $form_name - The name of the form to check.

		Returns:
			bool - Returns true if the input exists within the specified form,
						 false if not.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function input_exists($name, $form_name = null)
	{
		// We may need to get the form name.
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// Pretty simple.
		return $this->exists($form_name) && isset($this->forms[$form_name]['inputs'][strtolower($name)]);
	}

	/*
		Method: input

		Returns the specified input, which can then be used to manipulate it.

		Parameters:
			string $name - The name of the input to return.
			string $form_name - The form which contains the input.

		Returns:
			object - Returns the Input object specified with $name.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function input($name, $form_name = null)
	{
		// We may need to fetch the form name.
		$form_name = !empty($form_name) ? $form_name : $this->current();

		// Does the input exist?
		if(!$this->input_exists($name, $form_name))
		{
			// No, no it does not!!
			return false;
		}

		// Return the input. Easy.
		return $this->forms[strtolower($form_name)]['inputs'][strtolower($name)];
	}

	/*
		Method: inputs

		Returns an array containing all the inputs that exist within the
		specified form.

		Parameters:
			string $form_name - The name of the form to retrieve the inputs from.

		Returns:
			array - Returns an array containing all the inputs which exist within
							the form, but false if the form does not exist.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function inputs($form_name = null)
	{
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// Does the form even exist? It's gotta!
		if(!$this->exists($form_name))
		{
			return false;
		}

		return $this->forms[$form_name]['inputs'];
	}

	/*
		Methods left:
			open - returns a string containing the whole <form ...> tag.
			close - returns a string containing the whole ... </form> tag, along
							with the CSRF protection tag, if there is one.
			generate - Calls on the Input's generate method
			render - Renders the entire form, in a table-like format
			process - Processes the submitted form
			errors - Returns an array containing all errors generated by the forms
							 submission. Can also display them too.
}
?>