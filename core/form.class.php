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
  Class: Form

  This is a very useful tool which aids in the creation, and saving, of
  form information. It is recommended that you use this when creating,
  well, any type of form to allow plugins the ability to add, remove and
  modify the form.
*/
class Form
{
  # Variable: forms
  # Holds the registered form information.
  private $forms;

  /*
    Method: __construct
  */
  public function __construct()
  {
    $this->forms = array();
  }

  /*
    Method: add

    Registers a new form.

    Parameters:
      string $form_name - The name of the form to create.
      callback $form_callback - The callback which will be passed the values
                                submitted by the form. The values will be passed
                                after they have all be checked and properly handled.
      string $form_action - The URL of the forms action, once
                            it is submitted. If nothing is
                            supplied, the current URL will be
                            used instead.
      string $form_method - How the browser should send the form
                            either POST or GET.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function add($form_name, $form_callback, $form_action = null, $form_method = 'post')
  {
    global $api;

    # Form already registered by this name..? Is it not callable?
    if($this->form_registered($form_name) || !is_callable($form_callback))
      return false;

    # Only get or post ;)
    $form_method = strtolower($form_method);
    if(!in_array($form_method, array('get', 'post')))
      return false;

    $this->forms[$form_name] = array(
                                 'callback' => $form_callback,
                                 'action' => $form_action,
                                 'method' => $form_method,
                                 'fields' => array(),
                                 'errors' => array(),
                               );

    $token = $api->load_class('Tokens');
    $token->add($form_name);

    # Our first field, a token!
    $this->add_field($form_name, 'form_token', array(
                                                 'type' => 'hidden',
                                                 'value' => $token->token($form_name),
                                                 'function' => create_function('$value, $form_name, &$error', '
                                                   global $api;

                                                   $token = $api->load_class(\'Tokens\');

                                                   if($token->is_valid($form_name, $value))
                                                    return true;
                                                  else
                                                  {
                                                    $error = l(\'Your security key is invalid. Please resubmit the form.\');
                                                    return false;
                                                  }'),
                                                 'save' => false,
                                               ));

    return true;
  }

  /*
    Method: remove

    Removes the specified form.

    Parameters:
      string $form_name - The name of the form.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function remove($form_name)
  {
    if(!$this->form_registered($form_name))
      return false;

    unset($this->forms[$form_name]);
    return true;
  }

  /*
    Method: form_registered

    Checks to see if the specified form name is in use.

    Parameters:
      string $form_name - The name of the form.

    Returns:
      bool - Returns true if the form is registered, false if not.
  */
  public function form_registered($form_name)
  {
    return isset($this->forms[$form_name]);
  }

  /*
    Method: edit

    Allows you to edit the specified form information.

    Parameters:
      string $form_name - The name of the form.
      array $options - An array containing the new values.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      Allowed options:
        callback - The callback of the form.
        action - The URL to submit the form to.
        method - Either GET or POST.
  */
  public function edit($form_name, $options)
  {
    # Can't edit something that doesn't exist, now can we?
    if(!$this->form_registered($form_name))
      return false;

    # Editing the callback? Make sure it is callable.
    if(isset($options['callback']) && is_callable($options['callback']))
      $this->forms[$form_name]['callback'] = $options['callback'];
    elseif(isset($options['callback']))
      return false;

    # The action?
    if(isset($options['action']))
      $this->forms[$form_name]['action'] = $options['action'];

    # How about the method of transporation? ;) Only get or post.
    if(isset($options['method']) && in_array(strtolower($options['method']), array('get', 'post')))
      $this->forms[$form_name]['method'] = strtolower($options['method']);
    elseif(isset($options['method']))
      return false;

    # If nothing caused false to be returned elsewhere, it worked!
    return true;
  }

  /*
    Method: add_field

    Adds a field to the specified form.

    Parameters:
      string $form_name - The name of the form.
      string $name - The name of the input/textarea.
      array $options - Options about how the input should be formed and handled.
                       Look at the note below for more information.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      Here are acceptable indices, and their expected values, for the $options parameter.
      
        column - The name of the column in the database for which this value will be used.
                 If nothing is supplied, the name of the input/textarea will be used.
                 
        type - The accepted types of a field are as follows (required):
                 hidden - A hidden input, ooooo! I wonder what you want to hide?
                 int - An integer value.
                 double - A double value.
                 string - A string value (input type="text")
                 string-html - Same as above, but HTML tags are not sanitized with htmlchars.
                 text - A string value, however, it is a textarea.
                 text-html - Same as above, but HTML tags are not sanitized with htmlchars.
                 password - A password field.
                 checkbox - A checkbox field.
                 select - An options list (<select>), you are then supposed to supply
                          the options values.
                 select-multi - An options list, but multiple values can be selected.
                 function - This means the system will do no checking by itself, and
                            all will be handled by the supplied function callback.
                 custom(-{type}) - Allows you to set a custom HTML value for the value index,
                                   you are expected to form the input/textarea tag yourself,
                                   however, you have to append -{TYPE} to the end of custom
                                   which tells the system what kind of value to expect. If
                                   that is not appended, you are required to supply a function
                                   which handles the data before it is entered into the database.
                                   Also note that the value will be handled as a callback.
                                   
        label - The label of the input (the text previous to the input/textarea), be sure
                to run it through the l function! If nothing is supplied the column name
                is used instead.
                
        subtext - A description which is put below the label. (Optional)
        
        popup - Supply true if there is a popup (which should contain a more comprehensive
                set of information) that can be displayed. Apply a filter to help_{$column}.
                Defaults to false.
                
        length - An array of length restrictions. Ex: array('min' => 10, 'max' => 100). If
                 that was supplied the string could be a minimum length of 10 and a maximum
                 length of 100, if the string was not, an error would be shown. However, if
                 its type was an int/double the value could be, at minimum of 10 and a max
                 of 100, otherwise an error would be thrown. If no minimum is supplied, no
                 minimum will be expected (0), if no maximum is supplied, the length will
                 be unlimited. This option can only be used with the types: int, double,
                 and string.
                 
        truncate - This goes along the length index. If you set this to true, and a max
                   length is specified, then the value will be truncated according to the
                   maximum length. If it is a string (value: Hello), and a max of 2, the
                   value will be truncated at a length of 2 (He). However, if it is an
                   int/double (value: 50) and the maximuym is 25, the value will be 25.
                   Defaults to false.
                   
        options - An array of options, such as: array('Option 1', 'Option 2', 'Option 3')
                  or array('yes' => 'Yes', 'no' => 'No'), the index being the value in the
                  database, and the value being the value displayed in the options list.
                  
        function - A function callback, which is required if the type if function but optional
                   if it is anything else. This function will be called before (if any) any
                   system checking is done. Three parameter will be supplied, which is the value
                   (make sure you make it a reference parameter that way you can modify it), the
                   forms name and lastly, a reference parameter which contains the error, if any.
                   Your function is expected to return true if the value is okay (or that you
                   made it okay) and false if it is invalid. If you return nothing, then it
                   will see it as false.
                   
        value - The current value of the field. For numeric and string fields, the value is
                used as is, however, with checkboxes: 0 unchecked, 1 checked, select: selected="selected"
                put on the selected option, select-multi: same as previous one, except possibly
                on multiple options. Defaults to nothing.
                
        disabled - true if the field is disabled (value cannot be changed by the user) and false
                   if it is enabled. Defaults to false.
                   
        show - Set this to false if you do not want this field to be displayed, true if you want
               it to be shown. Defaults to true.
               
        save - Whether or not to include the field when being passed to the saving function.
               Defaults to true. This is useful for password verification, but also this is
               used internally for XSRF protection (fyi ;)).

      Just so you know, this is how each type will be saved to the database:
        hidden - As is.
        
        int - As is.
        
        double - As is.
        
        string - As is with HTML tags encoded.
        
        string-html - As is.
        
        text - As is with HTML tags encoded.
        
        text-html - As is.
        
        password - As is.
        
        checkbox - 0 for unchecked, 1 for checked.
        
        select - The index of the option value. For example:
                   options = array('This setting', 'Another setting')
                 If "Another setting" was chosen, 1 would be stored in the database
                 as that is its index, however, you can do 'another' => 'Another setting'
                 and "another" would be stored in the database.
                 
        select-multi - As is above, except each selected option will be comma delimited.
  */
  public function add_field($form_name, $name, $options = array())
  {
    # The form not registered? Is this field name already specified?
    if(!$this->form_registered($form_name) || $this->field_registered($form_name, $name))
      return false;

    # Validate that puppy!
    $field = $this->validate_field($name, $options);

    # Did you do something you shouldn't have? Tisk tisk!
    if($field === false)
      return false;

    # Add it.
    $this->forms[$form_name]['fields'][$name] = $field;

    return true;
  }

  /*
    Method: validate_field

    Validates the supplied field information, this is a helper method
    for <Form::add_field>, and also used for <Form::edit_field> as well.

    Parameters:
      string $name - The name of the field.
      array $options - An array of options.

    Returns:
      mixed - Returns an array on success, and false on failure.
  */
  private function validate_field($name, $options)
  {
    # Holds all of our stoof :)
    $field = array();

    # A column specified? Use that, otherwise, the supplied field name.
    $field['column'] = !empty($options['column']) ? $options['column'] : $name;

    # Here is an array containing all the recognized types.
    $allowed_types = array('int', 'double', 'string', 'string-html', 'text', 'text-html', 'password', 'checkbox', 'select', 'select-multi', 'function', 'custom');

    if(empty($options['type']))
      return false;

    # Before we validate the supplied, it might be custom!
    $options['type'] = strtolower($options['type']);
    $field['is_custom'] = $options['type'] == 'custom' || substr($options['type'], 0, 7) == 'custom-';

    if($field['is_custom'] && strlen($field['type']) > 7)
      $options['type'] = substr($options['type'], 7, strlen($options['type']) - 7);

    # So, is it valid?
    if(in_array($options['type'], $allowed_types))
      $field['type'] = $options['type'];
    else
      return false;

    # Label isn't required, but, c'mon, its a good idea ;)
    $field['label'] = isset($options['label']) ? $options['label'] : $field['column'];

    # Same goes for subtext.
    $field['subtext'] = isset($options['subtext']) ? $options['subtext'] : '';

    # How about a popup? More information never hurt anyone. I think.
    $field['popup'] = !empty($options['popup']);

    # A length isn't required either, so let's see.
    $field['length'] = array(
                         'min' => 0,
                         'max' => -1,
                       );

    if(!empty($options['length']['min']) && (string)$options['length']['min'] == (string)(int)$options['length']['min'])
      $field['length']['min'] = (int)$options['length']['min'];

    if(!empty($options['length']['max']) && (string)$options['length']['max'] == (string)(int)$options['length']['min'])
      $field['length']['max'] = (int)$options['length']['max'];

    # To truncate, or to not truncate, that is the question!
    $field['truncate'] = !empty($options['truncate']);

    # We only need options if your fields type is select or select-multi.
    if($field['type'] == 'select' || $field['type'] == 'select-multi')
    {
      # Nothing supplied?!
      if(!isset($options['options']) || !is_array($options['options']))
        return false;

      $field['options'] = $options['options'];
    }

    # A function, perhaps?
    $field['function'] = isset($options['function']) && is_callable($options['function']) ? $options['function'] : null;

    # Maybe a value?
    $field['value'] = isset($options['value']) ? $options['value'] : '';

    # Disabled?
    $field['disabled'] = !empty($options['disabled']);

    # Should we show/handle this field at all?
    $field['show'] = isset($options['show']) ? !empty($options['show']) : true;

    # Pass it to the callback?
    $field['save'] = isset($options['save']) ? !empty($options['save']) : true;

    # Woo! We are done!
    return $field;
  }

  /*
    Method: remove_field

    Removes the field from the specified form.

    Parameters:
      string $form_name - The name of the form.
      string $name - The name of the field.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function remove_field($form_name, $name)
  {
    if(!$this->field_registered($form_name, $name))
      return false;

    unset($this->forms[$form_name]['fields'][$name]);
    return true;
  }

  /*
    Method: field_registered

    Checks to see if the supplied field is registered on the
    specified form.

    Parameters:
      string $form_name - The name of the form.
      string $name - The name of the field.
  */
  public function field_registered($form_name, $name)
  {
    return isset($this->forms[$form_name]['fields'][$name]);
  }

  /*
    Method: edit_field

    Allows you to edit the field on the specified form.

    Parameters:
      string $form_name - The name of the form.
      string $name - The name of the field.
      array $options - An array containing all the options
                       you want to be added/changed in the fields
                       current setup.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function edit_field($form_name, $name, $options)
  {
    # The field not registered? Then you certainly can't edit what isn't there!
    if(!$this->field_registered($form_name, $name))
      return false;

    # Get the current options, merge the new ones and validate them. If validation
    # fails, we just won't actually update them :P
    $field = $this->validate_field($name, array_merge($this->forms[$form_name]['fields'][$name], $options));

    # Did YOU fail? :P
    if($field === false)
      return false;

    # So it worked, sweet.
    $this->forms[$form_name]['fields'][$name] = $field;

    return true;
  }

  /*
    Method: show

    Shows the specified form in HTML form.

    Parameters:
      string $form_name - The name of the form to display.

    Returns:
      void - Nothing is returned by this method.
  */
  public function show($form_name)
  {
    global $api;

    if(!$this->form_registered($form_name))
      echo l('The form "%s" does not exist.', htmlchars($form_name));

    # Before we display the form, let's let yalls have at it.
    # So you can add, remove and edit fields and such :)
    $api->run_hook($form_name);

    #
  }
}
?>
