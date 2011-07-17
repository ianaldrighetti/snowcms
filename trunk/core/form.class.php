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
			needs to return false if there were any more errors generated, but
			can return whatever it wants if the form was successfully processed.
			Excluding false, of course.

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

		// Enable CSRF by default if not set.
		if(!isset($options['csrf-token']))
		{
			$options['csrf-token'] = true;
		}

		// We will simply edit the form, and if the edit method returns false,
		// that means one of your options was incorrect.
		if(!$this->edit($name, $options))
		{
			// I guess something went wrong, so we will delete the form, then.
			$this->remove($name);

			return false;
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

		if(isset($options['action']))
		{
			$this->forms[$name]['action'] = $options['action'];
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

		// The way the form is submitted?
		if(isset($options['method']) && in_array(strtolower($options['method']), array('get', 'post')))
		{
			$this->forms[$name]['method'] = strtolower($options['method']);
		}
		elseif(isset($options['method']))
		{
			return false;
		}

		// Enabling or disabling CSRF protection?
		if(isset($options['csrf-token']))
		{
			// Are you enabling it? Is it already enabled..?
			if(!empty($options['csrf-token']) && !$this->input_exists($name. '_token'))
			{
				// Load up the Tokens class.
				$token = api()->load_class('Tokens');

				// We only want to recreate the token if it doesn't exist, or if it
				// is expired.
				if(!$token->exists($name. '_token') || $token->is_expired($name. '_token'))
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
																						 // But now that the token is
																						 // used, delete it.
																						 $token->delete($name. \'_token\');

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
													 'default_value' => $token->token($name. '_token'),
												 ), $name);
			}
			// Maybe disabling it?
			elseif(empty($options['csrf-token']) && $this->input_exists($name. '_token'))
			{
				// To disable it, we simply delete it!
				$this->remove_input($name. '_token', $name);
			}
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
			$input = new Input(isset($input['name']) ? $input['name'] : null, isset($input['label']) ? $input['label'] : null, isset($input['subtext']) ? $input['subtext'] : null,
													isset($input['type']) ? $input['type'] : null, isset($input['request_type']) ? $input['request_type'] : null, isset($input['length']) ? $input['length'] : null,
													isset($input['truncate']) ? $input['truncate'] : null, isset($input['options']) ? $input['options'] : null, isset($input['callback']) ? $input['callback'] : null,
													isset($input['default_value']) ? $input['default_value'] : null, isset($input['disabled']) ? $input['disabled'] : null, isset($input['readonly']) ? $input['readonly'] : null,
													isset($input['rows']) ? $input['rows'] : null, isset($input['columns']) ? $input['columns'] : null);
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
		if(!isset($position) || $position === null || count($this->forms[$form_name]['inputs']) <= $position)
		{
			// Nope, you obviously don't care, so we will plop it in at the end!
			$this->forms[$form_name]['inputs'][strtolower($input->name())] = $input;
		}
		else
		{
			// We shall use the function array_insert, which resides within the
			// compat.php file.
			$this->forms[$form_name]['inputs'] = array_insert($this->forms[$form_name], $input, $position, $input->name());
		}

		// Did you add an input which is the type of a file? Then we will want
		// to change the encoding type of the form, otherwise the file upload
		// won't work.
		if($input->type() == 'file')
		{
			$this->forms[$form_name]['enctype'] = 'multipart/form-data';
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
		Method: remove_input

		Removes the specified input from the form.

		Parameters:
			string $name - The name of the input to remove.
			string $form_name - The name of the form which contains the input.

		Returns:
			bool - Returns true if the input was removed successfully, false if
						 not.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function remove_input($name, $form_name = null)
	{
		// Get the name of the form?
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// The input must exist!
		if(!$this->input_exists($name, $form_name))
		{
			return false;
		}

		// Delete it!
		unset($this->forms[strtolower($form_name)]['inputs'][strtolower($name)]);

		return true;
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
		Method: open

		Returns the opening of the form, which would be the <form action="...
		tag. All options set for the form will be used to determine the action,
		method, etc. attributes of the tag.

		Parameters:
			string $form_name - The name of the form.

		Returns:
			string - A string containing the opening form tag.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.

			This method is only to be used if you do not want to have the form
			rendered automatically in a table-like format with <Input::render>.

			If you do use this method, you should also use <Form::close> as well.

			Please note that before anything is generated a hook called
			form_{form name} is ran. This hook may also be called in
			<Form::process> if it is called before <Form::open>, in which case the
			hook will not be ran again in this method.
	*/
	public function open($form_name = null)
	{
		// Get the form name, if one was supplied.
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// Make sure the form exists.
		if(!$this->exists($form_name))
		{
			return false;
		}

		// Run that hook, just in case! But only if it wasn't ran already, such
		// as before the form was processed.
		if(!$this->hooked($form_name))
		{
			api()->run_hooks('form_'. $form_name, array(&$this));

			$this->hooked($form_name, true);
		}

		// Now put together that <form> tag. We can use the Theme's generate
		// tag to make things easier!
		return theme()->generate_tag('form', array(
																						'accept-charset' => $this->forms[$form_name]['accept-charset'],
																						'action' => $this->forms[$form_name]['action'],
																						'class' => 'form',
																						'enctype' => $this->forms[$form_name]['enctype'],
																						'id' => !empty($this->forms[$form_name]['id']) ? $this->forms[$form_name]['id'] : $name,
																						'method' => $this->forms[$form_name]['method'],
																					), false). "\r\n". '<fieldset>';
	}

	/*
		Method: close

		Returns the closing of the form, which would be the ...</form> part. If
		there is a CSRF protection token ({form name}_token would be the input's
		name) it will be contained within the string returned as well.

		Parameters:
			string $form_name - The name of the form.

		Returns:
			string - A string containing the opening form tag.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function close($form_name = null)
	{
		// Get the form name, if one wasn't supplied.
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// Make sure the form exists.
		if(!$this->exists($form_name))
		{
			return false;
		}

		// We may need to include the CSRF token as well.
		return ($this->input_exists($form_name. '_token', $form_name) ? $this->generate($form_name. '_token', $form_name). "\r\n" : ''). '</form>';
	}

	/*
		Method: generate

		Generates the HTML for the input specified in the form.

		Parameters:
			string $name - The name of the input to generate.
			string $element_id - A string containing the HTML element's id. If
													 none is supplied, it will default to the input's
													 name.
			string $css_class - A string (or array) containing CSS classes to
													assign to the HTML element.
			string $form_name - The name of the form which contains the input.

		Returns:
			string - Returns a string containing the generated input, however, if
							 if <Input::validate> determines that the current options for
							 the <Input> are invalid, false will be returned.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function generate($name, $element_id = null, $css_class = null, $form_name = null)
	{
		// Get the form name, if one wasn't supplied.
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// Make sure the input exists.
		if(!$this->input_exists($name, $form_name))
		{
			return false;
		}

		// Just call on the generate method.
		return $this->input($name, $form_name)->generate($element_id, !empty($css_class) ? $css_class : 'form-input');
	}

	/*
		Method: render

		Renders the entire form, from beginning to end, in a table-like format,
		just as was done with the previous Form class (in SnowCMS 2.0 alpha).
		This is only recommended for such things as settings forms.

		Parameters:
			string $form_name - The name of the form to render.

		Returns:
			void - Nothing is returned by this method.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function render($form_name = null)
	{
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// Make sure the form exists.
		if(!$this->exists($form_name))
		{
			// No, it doesn't. Darn.
			return false;
		}

		// To make my life easier!
		$form_id = !empty($this->forms[$form_name]['id']) ? $this->forms[$form_name]['id'] : $form_name;

		// Here we go!
		echo '
			', $this->open($form_name), '
				<table>
					<tr>
						<td class="message_td" colspan="2" id="', $form_id, '_messages">';

		// Any messages to display?
		$messages = array();
		api()->run_hooks($form_name. '_messages', array(&$messages, $form_name));

		if(count($messages) > 0)
		{
			echo '
							<div class="message-box">';

			foreach($messages as $message)
			{
				echo '
								<p>', $message, '</p>';
			}

			echo '
							</div>';
		}

		echo '
						</td>
					</tr>
					<tr>
						<td class="errors_td" colspan="2" id="', $form_id, '_errors">';

		// Maybe there are some errors?
		$error_messages = array();
		api()->run_hooks($form_name. '_errors', array(&$error_messages, $form_name));

		if(count($error_messages) > 0)
		{
			echo '
							<div class="error-message">';

			foreach($error_messages as $error_message)
			{
				echo '
								<p>', $error_message, '</p>';
			}

			echo '
							</div>';
		}

		echo '
						</td>
					</tr>';

		// Now it is time to show all the inputs!
		foreach($this->forms[$form_name]['inputs'] as $input)
		{
			// Is this a hidden field..? Then we don't need any of the stuff
			// below!!
			if($input->type() == 'hidden')
			{
				echo $input->generate();

				// Move along.
				continue;
			}

			echo '
					<tr class="form-row">
						<td id="', $form_id, '_', $input->name(), '_left" class="td-left"><p class="label"><label for="', $input->name(), '">', $input->label(), '</label></p>', (strlen($input->subtext()) > 0 ? '<p class="subtext">'. $input->subtext(). '</p>' : ''), '</td>
						<td id="', $form_id, '_', $input->name(), '_right" class="td-right">', $input->generate($input->name(), 'form-input'), '</td>
					</tr>';
		}

		echo '
					<tr id="', $form_id, '_submit">
						<td class="buttons" colspan="2"><input type="submit" name="', $form_name, '" value="', htmlchars($this->forms[$form_name]['submit']), '" /></td>
					</tr>
				</table>
			</fieldset>
		</form>';
	}

	/*
		Method: process

		By calling this method, the entire form will be processed, which
		involves determining any errors caused by user input. If the form is
		processed successfully (which means there were no errors) then the
		callback assigned in <Form::add> will be called upon and passed all the
		data submitted by the user.

		Parameters:
			string $form_name - The name of the form to process.

		Returns:
			mixed - Returns false if the form was not processed successfully
							(which means the form has errors which need correcting by the
							user), however on success the value returned will be anything
							the callback returns when the form was processed without any
							issues.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function process($form_name = null)
	{
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// The form needs to exist in order to process it.
		if(!$this->exists($form_name))
		{
			return false;
		}

		// Run that hook?
		if(!$this->hooked($form_name))
		{
			api()->run_hooks('form_'. $form_name, array(&$this));

			$this->hooked($form_name, true);
		}

		// If you want to handle the form processing, be my guest!
		$errors = null;
		$handled = null;

		api()->run_hooks('form_process', array(&$handled, &$form_name, &$this->forms[$form_name], &$errors));

		if($handled !== null)
		{
			// Did any errors occur?
			if(is_array($errors))
			{
				$this->forms[$form_name]['errors'] = $errors;
			}

			return $handled;
		}
		else
		{
			// Guess we will have to handle everything ourselves.
			// Not a big deal, though.
			$errors = array();
			$data = array();
			$handled = false;

			foreach($this->forms[$form_name]['inputs'] as $input)
			{
				// Make sure the Input will work right.
				if(!$input->validate())
				{
					$errors[] = l('The input "%s" in the form "%s" is not configured correctly.', htmlchars($input->name()), htmlchars($form_name));
				}
				elseif(!$input->valid())
				{
					// Well, something went wrong with user input, but luckily the
					// Input class deals with that.
					$errors[] = $input->error();
				}
				else
				{
					// The data they entered was just fine.
					$data[strtolower($input->name())] = $input->value();
				}
			}

			// If no errors occurred, then we can call on the registered callback.
			if(count($errors) == 0)
			{
				$handled = call_user_func_array($this->forms[$form_name]['callback'], array($data, &$errors));
			}

			$this->forms[$form_name]['errors'] = $errors;
		}

		api()->add_hook($form_name. '_errors', create_function('&$errors, $form_name', '
																					 $form = api()->load_class(\'Form\');

																					 $errors = array_merge($errors, $form->errors($form_name));'));

		return $handled;
	}

	/*
		Method: errors

		Returns an array containing any errors generated when processing the
		specified form.

		Parameters:
			string $form_name - The name of the form to obtain the errors from.

		Returns:
			array - Returns the array containing any generated errors.

		Note:
			The $form_name parameter does not need to be supplied if one was set
			with the <Form::current> method.
	*/
	public function errors($form_name = null)
	{
		$form_name = strtolower(!empty($form_name) ? $form_name : $this->current());

		// We can't return errors if the form doesn't exist.
		if(!$this->exists($form_name))
		{
			return false;
		}

		return $this->forms[$form_name]['errors'];
	}

	/*
		Method: hooked

		Determines whether or not the form has already had hooks ran.

		Parameters:
			string $form_name - The name of the form.
			bool $hooks_ran - Set to true if the hooks have just been run.

		Returns:
			bool - Returns true if the form has already had the hooks run, false
						 if not.

		Note:
			This is a private method which cannot be used outside of the Form
			class.
	*/
	private function hooked($form_name, $hooks_ran = false)
	{
		static $hooked = array();

		if(!empty($hooks_ran))
		{
			$hooked[$form_name] = true;
		}

		return !empty($hooked[$form_name]);
	}
}
?>