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
      array $options - An array of options containing information about the form.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      The following $options indices are allowed:
        accept-charset - Specifies the supported character sets, defaults to utf-8.

        action - The URL of where to send the form data to once submitted.

        ajax_submit - Whether or not (if the browser supports it) to submit the form
                      via AJAX, defaults to false.

        callback - The callback which is passed all the form information.

        enctype - Specifies how the form data will be encoded when being sent.
                  If a file field is added, the enctype is automatically changed
                  to multipart/form-data.

        id - The unique HTML id for the tag. Defaults to the form name.

        method - The way the form should be submitted, either POST or GET, defaults
                 to POST.

        submit - The text on the submit button.

      Once the form is processed using <Form::process> (might I add, successfully,
      so no errors from the data the user has submitted), an array containing the
      sanitized and/or handled data will be passed to the callback (array(column => value)).
      Also note that your callback is expected to return some value, other than
      false, unless something went wrong as well. You could return something such
      as an array of information, or just true :) But also, there is a second parameter
      which is a reference parameter to an array, to which you can add more errors to
      if there are any more errors which occurred while processing the form data.
  */
  public function add($form_name, $options)
  {
    global $api;

    # Form already registered by this name..? Is it not callable?
    if($this->form_exists($form_name) || !is_callable($options['callback']))
    {
      return false;
    }

    # We will use the edit method to add your options ;D
    $this->forms[$form_name] = array(
                                 'accept-charset' => 'utf-8',
                                 'action' => null,
                                 'ajax_submit' => false,
                                 'callback' => null,
                                 'enctype' => null,
                                 'errors' => array(),
                                 'fields' => array(),
                                 'hooked' => false,
                                 'id' => $form_name,
                                 'method' => 'post',
                                 'submit' => l('Submit'),
                               );

    # Told you! :D
    if(!$this->edit($form_name, $options))
    {
      # Hmm, it didn't work... Maybe you ought to fix that? :P
      unset($this->forms[$form_name]);
      return false;
    }

    $token = $api->load_class('Tokens');

    # Only recreate the token if it does not exist.
    if(!$token->exists($form_name))
    {
      $token->add($form_name);
    }

    # Our first field, a token!
    $this->add_field($form_name, $form_name. '_form_token', array(
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
    if(!$this->form_exists($form_name))
    {
      return false;
    }

    unset($this->forms[$form_name]);
    return true;
  }

  /*
    Method: form_exists

    Checks to see if the specified form name is in use.

    Parameters:
      string $form_name - The name of the form.

    Returns:
      bool - Returns true if the form is registered, false if not.
  */
  public function form_exists($form_name)
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
      For options to set, see <Form::add>.
  */
  public function edit($form_name, $options)
  {
    global $theme;

    # Can't edit something that doesn't exist, now can we?
    if(!$this->form_exists($form_name))
    {
      return false;
    }

    # Editing the charset? Simply check if it is set or not.
    if(isset($options['accept-charset']))
    {
      $this->forms[$form_name]['accept-charset'] = $options['accept-charset'];
    }

    # The action?
    if(isset($options['action']))
    {
      $this->forms[$form_name]['action'] = $options['action'];
    }

    # Want to submit it via AJAX?
    if(isset($options['ajax_submit']))
    {
      $this->forms[$form_name]['ajax_submit'] = !empty($options['ajax_submit']);

      if(!empty($options['ajax_submit']))
      {
        $theme->add_js_var('form_saving', l('Saving...'));
      }
    }

    # How about the callback? Make sure it is callable.
    if(isset($options['callback']) && is_callable($options['callback']))
    {
      $this->forms[$form_name]['callback'] = $options['callback'];
    }
    elseif(isset($options['callback']))
    {
      return false;
    }

    # The encoding type, maybe?
    if(isset($options['enctype']))
    {
      $this->forms[$form_name]['enctype'] = $options['enctype'];
    }

    # The HTML id? Good :)
    if(isset($options['id']))
    {
      $this->forms[$form_name]['id'] = $options['id'];
    }

    # How about the method of transporation? ;) Only get or post.
    if(isset($options['method']) && in_array(strtolower($options['method']), array('get', 'post')))
    {
      $this->forms[$form_name]['method'] = strtolower($options['method']);
    }
    elseif(isset($options['method']))
    {
      return false;
    }

    # The text on the submit button, perhaps?
    if(isset($options['submit']))
    {
      $this->forms[$form_name]['submit'] = $options['submit'];
    }

    # If nothing caused false to be returned elsewhere, it worked!
    return true;
  }

  /*
    Method: return_form

    Returns the specified form and all of its information.

    Parameters:
      string $form_name - The name of the form. Leave this set to null in
                          order to return all forms.

    Returns:
      array - Returns the forms information, false if the form doesn't exist.
  */
  public function return_form($form_name = null)
  {
    if(!empty($form_name) && !$this->form_exists($form_name))
    {
      return false;
    }

    return empty($form_name) ? $this->forms : $this->forms[$form_name];
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
      Here are acceptable indices, and their expected values, for the $options parameter:
        column - The name of the column in the database for which this value will be used.
                 If nothing is supplied, the name of the input/textarea will be used.

        type - The accepted types of a field are as follows (required):
                 - hidden - A hidden input, ooooo! I wonder what you want to hide?

                 - int - An integer value.

                 - double - A double value.

                 - string - A string value (input type="text")

                 - string-html - Same as above, but HTML tags are not sanitized with htmlchars.

                 - textarea - A string value, however, it is a textarea.

                 - textarea-html - Same as above, but HTML tags are not sanitized with htmlchars.

                 - password - A password field.

                 - checkbox - A checkbox field.

                 - checkbox-multi - A list of multiple checkboxes.

                 - select - An options list (<select>), you are then supposed to supply
                            the options values.

                 - select-multi - An options list, but multiple values can be selected.

                 - radio - A list of radio buttons.

                 - file - A file field.

                 - function - This means the system will do no checking by itself, and
                              all will be handled by the supplied function callback.

                 - custom(-{type}) - Allows you to set a custom HTML value for the value index,
                                     you are expected to form the input/textarea tag yourself,
                                     however, you have to append -{TYPE} to the end of custom
                                     which tells the system what kind of value to expect. If
                                     that is not appended, you are required to supply a function
                                     which handles the data before it is entered into the database.
                                     Also note that the value will be handled as a callback.

                 - full(-{type}) - Much like custom(-{type}), however, this one gives you full control
                                   of a <td> tag which has an attribute of colspan="2".

        label - The label of the input (the text previous to the input/textarea), be sure
                to run it through the l function! If nothing is supplied the column name
                is used instead.

        subtext - A description which is put below the label. (Optional)

        popup - Supply true if there is a popup (which should contain a more comprehensive
                set of information) that can be displayed. Apply a filter to popup_{$column}.
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

        function - A function callback, which is required if the type is function but optional
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
                on multiple options. Defaults to nothing. Please note that the value is automatically
                encoded by htmlchars.

        disabled - true if the field is disabled (value cannot be changed by the user) and false
                   if it is enabled. Defaults to false.

        show - Set this to false if you do not want this field to be displayed, true if you want
               it to be shown. Defaults to true.

        save - Whether or not to include the field when being passed to the saving function.
               Defaults to true. This is useful for password verification, but also this is
               used internally for XSRF protection (fyi ;)).

        rows - The number of rows in the textarea. Only valid for text, text-html and select-multi.
               When set for select-multi it sets the size attribute.

        cols - The number of columns in the textarea. Only valid for text and text-html.

        position - The position at which to place the field (0 -> [NUM FIELDS] - 1). Say you add one field
                   at position 0, and then another at position 0, the second field will be first, and the
                   first field added will be second. If not supplied, it will be added to the end.

      Just so you know, this is how each type will be saved to the database:
        hidden - As is (See string).

        int - As is.

        double - As is.

        string - As is with HTML tags encoded.

        string-html - As is.

        textarea - As is with HTML tags encoded.

        textarea-html - As is.

        password - As is.

        checkbox - 0 for unchecked, 1 for checked.

        checkbox-multi - See select.

        select - The index of the option value. For example:
                   options = array('This setting', 'Another setting')
                 If "Another setting" was chosen, 1 would be stored in the database
                 as that is its index, however, you can do 'another' => 'Another setting'
                 and "another" would be stored in the database.

        select-multi - As is above, except each selected option will be comma delimited.

        radio - The selected options key will be passed.

        file - The array from the $_FILES array will be passed.
  */
  public function add_field($form_name, $name, $options = array())
  {
    # The form not registered? Is this field name already specified?
    if(!$this->form_exists($form_name) || $this->field_exists($form_name, $name))
    {
      return false;
    }

    # Any position..?
    if(isset($options['position']))
    {
      $position = (string)$options['position'] == (string)(int)$options['position'] ? (int)$options['position'] : null;
      unset($options['position']);
    }

    # Validate that puppy!
    $field = $this->validate_field($name, $options);

    # Did you do something you shouldn't have? Tisk tisk!
    if($field === false)
    {
      return false;
    }

    # Add it. But maybe not so fast!
    if(!isset($position) || $position === null)
    {
      $this->forms[$form_name]['fields'][$name] = $field;
    }
    else
    {
      # Insert it at the right place ;D
      $this->forms[$form_name]['fields'] = array_insert($this->forms[$form_name]['fields'], $field, $position, $name);
    }

    # If the type of the field is a file, change the encoding type to the right one.
    if($field['type'] == 'file')
    {
      $this->forms[$form_name]['enctype'] = 'multipart/form-data';
    }

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
    $field = array(
      # The column where the data will be saved, possibly.
      'column' => !empty($options['column']) ? $options['column'] : $name,

      # Whether or not the field is disabled, readonly, should even be shown or saved.
      'disabled' => isset($options['disabled']) ? !empty($options['disabled']) : false,
      'readonly' => isset($options['readonly']) ? !empty($options['readonly']) : false,
      'show' => isset($options['show']) ? !empty($options['show']) : true,
      'save' => isset($options['save']) ? !empty($options['save']) : true,
      'size' => isset($options['size']) && (int)$options['size'] > 0 ? (int)$options['size'] : null,
      'type' => strtolower($options['type']),
      'is_custom' => false,
      'is_full' => false,
      'function' => isset($options['function']) ? $options['function'] : false,

      # The label and subtext of the field, which are good ideas :)
      'label' => isset($options['label']) ? $options['label'] : (!empty($options['column']) ? $options['column'] : $name),
      'subtext' => isset($options['subtext']) ? $options['subtext'] : '',

      # A little popup with more information, maybe?
      'popup' => !empty($options['popup']),
      'length' => array(
                    'min' => isset($options['length']['min']) && (int)$options['length']['min'] > -1 ? (int)$options['length']['min'] : null,
                    'max' => isset($options['length']['max']) && (int)$options['length']['max'] > 0 ? (int)$options['length']['max'] : null,
                  ),
      'truncate' => !empty($options['truncate']),
      'options' => isset($options['options']) && is_array($options['options']) ? $options['options'] : false,
      'value' => isset($options['value']) ? $options['value'] : null,
      'rows' => isset($options['rows']) && (int)$options['rows'] > 0 ? $options['rows'] : null,
      'cols' => isset($options['cols']) && (int)$options['cols'] > 0 ? $options['cols'] : null,

      # HTML id?
      'id' => isset($options['id']) ? $options['id'] : false,
    );

    # Now it is time to do some checking!
    # Here is an array containing all the recognized types.
    $allowed_types = array('hidden', 'int', 'double', 'string', 'string-html', 'textarea', 'textarea-html', 'password', 'checkbox', 'checkbox-multi', 'select', 'select-multi', 'radio', 'file', 'function', 'custom', 'full');

    # No type? No field!
    if(empty($field['type']))
    {
      return false;
    }

    # Before we validate the supplied type, check to see if it is full or custom...
    $field['is_full'] = $field['type'] == 'full' || substr($field['type'], 0, 5) == 'full-';
    $field['is_custom'] = $field['type'] == 'custom' || substr($field['type'], 0, 7) == 'custom-' || $field['is_full'];

    if($field['is_custom'] && !$field['is_full'] && strlen($field['type']) > 7)
    {
      $field['type'] = substr($field['type'], 7, strlen($field['type']) - 7);
    }
    elseif($field['is_full'] && strlen($field['type']) > 5)
    {
      $field['type'] = substr($field['type'], 5, strlen($field['type']) - 5);
    }

    # So, is it a valid type?
    if(!in_array($field['type'], $allowed_types))
    {
      return false;
    }

    # Is your minimum length larger than your maximum?
    if($field['length']['min'] !== null && $field['length']['max'] !== null && $field['length']['min'] > $field['length']['max'])
    {
      return false;
    }

    # We only need options if your fields type is checkbox-multi, select, select-multi or radio.
    if($field['type'] == 'select' || $field['type'] == 'select-multi')
    {
      # Nothing supplied (Well, no array at all, at least)?!
      if($field['options'] === false)
      {
        return false;
      }

      # Make it safe!
      if(count($field['options']))
      {
        foreach($field['options'] as $key => $value)
        {
          $field['options'][$key] = htmlchars($value);
        }
      }

      # Is the value set and is it not one that is available..?
      if(is_array($field['value']))
      {
        # A bit different for array values ;)
        if(count($field['value']))
        {
          foreach($field['value'] as $key)
          {
            # Is this specific option not existent?
            if(!isset($field['options'][$key]))
            {
              # Delete it!
              unset($field['value'][$key]);
            }
          }

          if(count($field['value']) == 0)
          {
            $field['value'] = null;
          }
        }
        else
        {
          $field['value'] = null;
        }
      }
      elseif($field['value'] !== null && !isset($field['options'][$field['value']]))
      {
        # We will just unset it then.
        $field['value'] = null;
      }
    }

    # Check the function, make sure it is callable if set, and if it is required or not.
    if(($field['function'] == false && $field['type'] == 'function') || ($field['function'] !== false && !is_callable($field['function'])))
      return false;

    # Do we need to encode the value?
    $field['value'] = isset($field['value']) ? ($field['is_custom'] && is_callable($field['value']) ? $field['value'] : (is_array($field['value']) ? $field['value'] : htmlchars($field['value']))) : '';

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
    if(!$this->field_exists($form_name, $name))
    {
      return false;
    }

    unset($this->forms[$form_name]['fields'][$name]);
    return true;
  }

  /*
    Method: field_exists

    Checks to see if the supplied field is registered on the
    specified form.

    Parameters:
      string $form_name - The name of the form.
      string $name - The name of the field.
  */
  public function field_exists($form_name, $name)
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
    if(!$this->field_exists($form_name, $name))
    {
      return false;
    }

    # Any position..?
    if(isset($options['position']))
    {
      $position = (string)$options['position'] == (string)(int)$options['position'] ? (int)$options['position'] : null;
      unset($options['position']);
    }

    # Get the current options, merge the new ones and validate them. If validation
    # fails, we just won't actually update them :P
    $field = $this->validate_field($name, array_merge($this->forms[$form_name]['fields'][$name], $options));

    # Did YOU fail? :P
    if($field === false)
    {
      return false;
    }

    # So it worked, sweet.
    $this->forms[$form_name]['fields'][$name] = $field;

    # So it worked, sweet. Edit the new field. But maybe not so fast!
    if(!isset($position) || $position === null)
    {
      $this->forms[$form_name]['fields'][$name] = $field;
    }
    else
    {
      # Delete the older one.
      unset($this->forms[$form_name]['fields'][$name]);

      # Insert it at the right place ;D
      $this->forms[$form_name]['fields'] = array_insert($this->forms[$form_name]['fields'], $field, $position, $name);
    }

    # If the type of the field is a file, change the encoding type to the right one.
    if($field['type'] == 'file')
      $this->forms[$form_name]['enctype'] = 'multipart/form-data';

    return true;
  }

  /*
    Method: return_field

    Returns the specified fields information.

    Parameters:
      string $form_name - The name of the form the field is in.
      string $name - The name of the field. Leave this null in order to have
                     all the fields returned.

    Returns:
      array - Returns the array containing the fields information, false if
              the specified field doesn't exist.
  */
  public function return_field($form_name, $name = null)
  {
    if(!$this->field_exists($form_name, $name) || (empty($name) && $this->form_exists($form_name)))
    {
      return false;
    }

    return empty($name) ? $this->forms[$form_name]['fields'] : $this->forms[$form_name]['fields'][$name];
  }

  /*
    Method: num_fields

    Returns the total number of fields in the specified form.

    Parameters:
      string $form_name - The name of the form.

    Returns:
      int - Returns the number of fields in the specified form.
  */
  public function num_fields($form_name)
  {
    # There certainly are none in a form which doesn't exist!
    if(!$this->form_exists($form_name))
    {
      return false;
    }

    # Count them! Simple!
    return count($this->forms[$form_name]['fields']);
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

    if(!$this->form_exists($form_name))
    {
      echo l('The form "%s" does not exist.', htmlchars($form_name));
      return;
    }

    # Before we display the form, let's let yalls have at it.
    # So you can add, remove and edit fields and such :)
    # But of course only run the hook here if <Form::process> has
    # yet to be called ;)
    if(empty($this->forms[$form_name]['hooked']))
    {
      $api->run_hooks($form_name);
      $this->forms[$form_name]['hooked'] = true;
    }

    # If you want to display the forms in your own special way, just hook into here :)
    $handled = null;
    $api->run_hooks('form_show', array($form_name, $this->forms[$form_name], &$handled));

    if(empty($handled))
    {
      # Make life just a little bit simple, shall we?
      $form = $this->forms[$form_name];

      if(empty($form['id']))
      {
        $form['id'] = $form_name;
      }

      echo '
      <form', (!empty($form['accept-charset']) ? ' accept-charset="'. $form['accept-charset']. '"' : ''), ' action="', $form['action'], '" class="form"', (!empty($form['enctype']) ? ' enctype="'. $form['enctype']. '"' : ''), ' id="', $form['id'], '" method="', $form['method'], '"', (!empty($form['ajax_submit']) ? ' onsubmit="s.submitform('. str_replace('"', '\'', json_encode($form_name)). ', this, '. str_replace('"', '\'', json_encode(array_keys($form['fields']))). '); return false;"' : ''), '>
        <fieldset>
          <table>
            <tr>
              <td class="message_td" colspan="2" id="', $form['id'], '_message">';

      # Any messages? Like a success message. Put it here! :P
      if(strlen($api->apply_filters($form_name. '_message', '')) > 0)
      {
        echo '
                <div class="message">
                  ', $api->apply_filters($form_name. '_message', ''), '
                </div>';
      }

        echo '
              </td>
            </tr>
            <tr>
              <td class="errors_td" colspan="2" id="', $form['id'], '_errors">';

      # Any errors? Those need displayin'!
      if(count($form['errors']) > 0)
      {
        echo '
                <div class="errors">';

        foreach($form['errors'] as $error)
        {
          echo '
                  <p>', $error, '</p>';
        }

        echo '
                </div>';
      }

        echo '
              </td>
            </tr>';

      # Show the fields, you know, the things you enter stuff into.
      if(count($form['fields']) > 0)
      {
        foreach($form['fields'] as $name => $field)
        {
          # Make this simple, show it!
          $this->show_field($form_name, $name, $field);
        }
      }

      echo '
            <tr id="', $form['id'], '_submit">
              <td class="buttons" colspan="2"><input type="submit" name="', $form_name, '" value="', $form['submit'], '" /></td>
            </tr>
          </table>
        </fieldset>
      </form>';
    }
  }

  /*
    Method: show_field

    Outputs a field according to the parameters supplied.

    Parameters:
      string $form_name - The name of the form the field is within.
      string $name - The name of the field.
      array $field - All the fields options.

    Returns:
      void - Nothing is returned by this method.
  */
  private function show_field($form_name, $name, $field)
  {
    global $api, $base_url, $theme_url;

    # Do you want to do this?
    $handled = null;
    $api->run_hooks('form_show_field', array(&$handled, $form_name, $name, $field));

    # Did someone else not handle it? Should it even be shown?
    if(empty($handled) && !empty($field['show']))
    {
      $form = $this->forms[$form_name];

      echo '
            <tr class="form_field" id="', $form['id'], '_', $name, '">';

      # Is the field hidden? Then showing something isn't very hidden, now is it? I didn't think so.
      if($field['type'] != 'hidden' && empty($field['is_full']))
      {
        echo '
              <td id="', $form['id'], '_', $name, '_left" class="td_left"><p class="label">', (!empty($field['popup']) ? '<a href="javascript:void(0);" onclick="formPopup = window.open(\''. $base_url. '/index.php?action=popup&amp;id=popup_'. $name. '\', \'formPopup\', \'location=no,menubar=no,status=no,titlebar=yes,height=300px,width=300px,directories=no\'); formPopup.focus();"><img src="'. $api->apply_filters('form_popup_image_url', $theme_url. '/default/style/images/admincp/about-small.png'). '" alt="" title="'. l('More information'). '" /></a> ' : ''). '<label for="', $form['id'], '_', $name, '_input">', $field['label'], '</label></p>', !empty($field['subtext']) ? '<p class="subtext">'. $field['subtext']. '</p>' : '', '</td>';
      }

      # Now here is the fun part! Actually displaying the fields.
      if(empty($field['is_custom']))
      {
        # No need to repeat this over and over again, is there?
        echo '
              <td id="', $form['id'], '_', $name, '_right" class="td_right">';

        # Strings, integers, doubles, passwords, etc.
        if(in_array($field['type'], array('int', 'double', 'string', 'string-html', 'password')))
        {
          echo '<input class="input_generic" id="', (!empty($field['id']) ? $field['id']. ' ' : ''), $form['id'], '_', $name, '_input" type="', ($field['type'] == 'password' ? 'password' : 'text'), '" name="', $name, '" value="', $field['value'], '"', ($field['length']['max'] > 0 ? ' maxlength="'. $field['length']['max']. '"' : ''), (!empty($field['disabled']) ? ' disabled="disabled"' : ''), (!empty($field['readonly']) ? ' readonly="readonly"' : ''), ' />';
        }
        # Text areas! Woo.
        elseif(substr($field['type'], 0, 8) == 'textarea')
        {
          echo '<textarea class="input_textarea" id="', (!empty($field['id']) ? $field['id']. ' ' : ''), $form['id'], '_', $name, '_input" name="', $name, '"', ($field['length']['max'] > 0 ? ' onkeyup="s.truncate(this, '. $field['length']['max']. ');"' : ''), ($field['rows'] > 0 ? ' rows="'. $field['rows']. '"' : ''), ($field['cols'] > 0 ? ' cols="'. $field['cols']. '"' : ''), ($field['length']['max'] > 0 ? ' maxlength="'. $field['length']['max']. '"' : ''), (!empty($field['disabled']) ? ' disabled="disabled"' : ''), (!empty($field['readonly']) ? ' readonly="readonly"' : ''), '>', $field['value'], '</textarea>';
        }
        elseif(substr($field['type'], 0, 8) == 'checkbox')
        {
          # Multiple, perhaps?
          if(substr($field['type'], -5, 5) == 'multi')
          {
            # Display them ALL!
            if(count($field['options']))
            {
              foreach($field['options'] as $key => $label)
              {
                echo '<label><input class="input_checkbox" id="', $form['id'], '_', $name, '_input" type="checkbox" name="', $name, '[', $key, ']" value="1"', (!empty($field['value']) && $field['value'] == $key ? ' checked="checked"' : ''), (!empty($field['disabled']) ? ' disabled="disabled"' : ''), (!empty($field['readonly']) ? ' readonly="readonly"' : ''), ' /> ', $label, '</label><br />';
              }
            }
          }
          else
          {
            # Nope, just a lonesome one! :(
            echo '<input class="input_checkbox" id="', $form['id'], '_', $name, '_input" type="checkbox" name="', $name, '" value="1"', (!empty($field['value']) ? ' checked="checked"' : ''), (!empty($field['disabled']) ? ' disabled="disabled"' : ''), (!empty($field['readonly']) ? ' readonly="readonly"' : ''), ' />';
          }
        }
        elseif(substr($field['type'], 0, 6) == 'select')
        {
          echo '<select class="input_select" id="', $form['id'], '_', $name, '_input" name="', $name, '"', ($field['type'] == 'select-multi' ? ' multiple="multiple"' : ''), ($field['type'] == 'select-multi' && $field['rows'] > 0 ? ' size="'. $field['rows']. '"' : ''), (!empty($field['disabled']) ? ' disabled="disabled"' : ''), '>';

          if(count($field['options']))
          {
            foreach($field['options'] as $key => $value)
            {
              echo '
                  <option value="', $key, '"', ((!is_array($field['value']) && $field['value'] == $key) || (is_array($field['value']) && in_array($key, $field['value'])) ? ' selected="selected"' : ''), '>', $value, '</option>';
            }
          }

          echo '</select>';
        }
        elseif($field['type'] == 'hidden')
        {
          echo '<input id="', $form['id'], '_', $name, '_input" type="hidden" name="', $name, '" value="', $field['value'], '"', (!empty($field['disabled']) ? ' disabled="disabled"' : ''), ' />';
        }
        elseif($field['type'] == 'radio')
        {
          # Display the list of radio buttons.
          if(count($field['options']))
          {
            foreach($field['options'] as $key => $label)
            {
              echo '<label><input class="input_radio" id="', $form['id'], '_', $name, '_input" type="radio" name="', $name, '" value="', $key, '"', (!empty($field['value']) && $field['value'] == $key ? ' checked="checked"' : ''), (!empty($field['disabled']) ? ' disabled="disabled"' : ''), (!empty($field['readonly']) ? ' readonly="readonly"' : ''), ' /> ', $label, '</label><br />';
            }
          }
        }
        elseif($field['type'] == 'file')
        {
          echo '<input class="input_file" id="', $form['id'], '_', $name, '_input" type="file" name="', $name, '" value="', $field['value'], '"', (!empty($field['disabled']) ? ' disabled="disabled"' : ''), (!empty($field['readonly']) ? ' readonly="readonly"' : ''), ' />';
        }

        echo '</td>';
      }
      elseif(empty($field['is_full']))
      {
        echo '
              <td id="', $form['id'], '_', $name, '_right" class="td_right">', (is_callable($field['value']) ? $field['value']() : $field['value']), '</td>';
      }
      else
      {
        echo '
              <td id="', $form['id'], '_', $name, '_right" class="full" colspan="2">', (is_callable($field['value']) ? $field['value']() : $field['value']), '</td>';
      }

      echo '
            </tr>';
    }
  }

  /*
    Method: process

    Actually processes and handles the submitting of the created forms.
    This method handles the sanitization and error handling of the data
    submitted by the user.

    Parameters:
      string $form_name - The name of the form.

    Returns:
      mixed - Returns false on failure, however, on success whatever the
              forms callback is set to return will be returned on success.
  */
  public function process($form_name)
  {
    global $api, $func;

    if(!$this->form_exists($form_name))
    {
      echo l('The form "%s" does not exist.', htmlchars($form_name));
      return;
    }

    # Run the hook, so you can make any modifications and what not to the form.
    # Note: This is the same exact hook in <Form::show> it is just that when the
    #       form is submitted, this method will be called before showing, so this
    #       just needs to be done ;)
    if(empty($this->forms[$form_name]['hooked']))
    {
      $api->run_hooks($form_name);
      $this->forms[$form_name]['hooked'] = true;
    }

    # Do you want the fun of handling this form? You be my guest!
    $errors = null;
    $handled = null;
    $api->run_hooks('form_process', array(&$handled, $form_name, $this->forms[$form_name], &$errors));

    if($handled !== null)
    {
      if(is_array($errors))
        $this->forms[$form_name]['errors'] = $errors;

      return !empty($handled);
    }
    else
    {
      # If the form wasn't actually submitted, then we couldn't process it right...
      if(empty($_POST[$form_name]) || count($_POST) == 0 || count($this->forms[$form_name]['fields']) == 0)
      {
        return false;
      }

      # Reset the errors array, just incase.
      $this->forms[$form_name]['errors'] = array();

      # We will need the validation class, that is for sure!
      $validation = $api->load_class('Validation');

      # Now this is the super fun part, processing everything!!!
      $processed = array();
      foreach($this->forms[$form_name]['fields'] as $name => $field)
      {
        # Field disabled? Not supposed to be shown? Then you can't supply any information about this.
        if(!empty($field['disabled']) || empty($field['show']))
        {
          continue;
        }

        # Is the POST field not even set? Well, we will set it then. To empty! ;)
        if(!isset($_POST[$name]))
        {
          $_POST[$name] = '';
        }

        # Any function to run, perhaps? Do so now.
        if(!empty($field['function']) && is_callable($field['function']))
        {
          $error = '';
          if(!$field['function']($_POST[$name], $form_name, $error))
          {
            # So something went wrong, did it?
            $this->forms[$form_name]['errors'][] = $error;

            # No need to continue, you said something was wrong!
            continue;
          }
        }

        # No passing this field to the forms callback? Then we're done!
        if(empty($field['save']))
        {
          continue;
        }

        # Now it is time to check the data types of the submitted form data, woo!!!
        # So, is it a string(-html), text(-html), password or a hidden field?
        if(in_array($field['type'], array('string', 'string-html', 'textarea', 'textarea-html', 'password', 'hidden')))
        {
          # Set as a string field, in reality, anything can be a string.
          if(!$validation->data($_POST[$name], 'string'))
          {
            $this->forms[$form_name]['errors'][] = l('The "%s" field must be a string.', htmlchars($this->forms[$form_name]['fields'][$name]['label']));
            continue;
          }

          # But does it need encoding?!
          if(in_array($field['type'], array('string', 'textarea', 'password', 'hidden')))
          {
            $_POST[$name] = htmlchars($_POST[$name]);
          }
        }
        # How about an integer or double?
        elseif($field['type'] == 'int' || $field['type'] == 'double')
        {
          # Temporarily type-cast the value to an integer, if it isn't the same, it isn't one.
          if(!$validation->data($_POST[$name], $field['type']))
          {
            $this->forms[$form_name]['errors'][] = l('The "%s" field must be an '. ($field['type'] == 'int' ? 'integer' : 'number'). '.', htmlchars($this->forms[$form_name]['fields'][$name]['label']));
            continue;
          }
        }
        # Could it be a checkbox?
        elseif($field['type'] == 'checkbox')
        {
          # Simple :)
          $_POST[$name] = !empty($_POST[$name]) ? 1 : 0;
        }
        # Select of some sort?
        elseif($field['type'] == 'select' || $field['type'] == 'select-multi')
        {
          $is_multi = $field['type'] == 'select-multi';

          $selected = array();
          $options = array_keys($field['options']);

          # Now to see which ones you selected, if any.
          if(is_array($_POST[$name]) && count($_POST[$name]) > 0)
          {
            foreach($_POST[$name] as $option_key)
            {
              # Is it even a valid option?
              if(in_array($option_key, $options))
              {
                $selected[] = $option_key;

                if(isset($field['length']['max']) && count($selected) >= $field['length']['max'])
                {
                  break;
                }
              }
            }
          }
          elseif(!$is_multi)
          {
            $selected[] = $_POST[$name];
          }

          # Join them all together, like one happy family! Of one ;D
          $_POST[$name] = implode(',', $selected);
        }
        elseif($field['type'] == 'checkbox-multi')
        {
          # The keys hold the values, in this case :)
          $checked = array();
          $options = array_keys($field['options']);

          if(is_array($_POST[$name]) && count($_POST[$name]) > 0)
          {
            foreach($_POST[$name] as $key => $dummy)
            {
              # Is it even a valid option?
              if(in_array($key, $options))
              {
                $checked[] = $key;

                if(isset($field['length']['max']) && count($checked) >= $field['length']['max'])
                {
                  break;
                }
              }
            }
          }

          $_POST[$name] = implode(',', $checked);
        }
        elseif($field['type'] == 'radio')
        {
          # Just make sure the option you selected is valid ;)
          if(!in_array($_POST[$name], array_keys($field['options'])))
          {
            $_POST[$name] = '';
          }
        }
        elseif($field['type'] == 'file')
        {
          # We will start with nope, its invalid ;)
          $_POST[$name] = false;

          # Is the right $_FILES index set?
          if(isset($_FILES[$name]))
          {
            # Make sure it is an actually uploaded file.
            if(is_uploaded_file($_FILES[$name]['tmp_name']))
            {
              # We'll set it in the post field P:
              $_POST[$name] = $_FILES[$name];
            }
          }
        }

        # Any length restrictions set?
        if((isset($field['length']['min']) || isset($field['length']['max'])) && ($field['type'] == 'int' || $field['type'] == 'double'))
        {
          if(isset($field['length']['min']) && $_POST[$name] < $field['length']['min'])
          {
            $this->forms[$form_name]['errors'][] = l('The field "%s" must be no smaller than %'. ($field['type'] == 'int' ? 'd' : 'f'). '.', $this->forms[$form_name]['fields'][$name]['label'], $field['length']['min']);
            continue;
          }
          elseif(isset($field['length']['max']) && ($truncate = ($_POST[$name] > $field['length']['max'])) && empty($field['truncate']))
          {
            $this->forms[$form_name]['errors'][] = l('The field "%s" must be no larger than %'. ($field['type'] == 'int' ? 'd' : 'f'). '.', $this->forms[$form_name]['fields'][$name]['label'], $field['length']['max']);
            continue;
          }

          if(!empty($truncate))
          {
            $_POST[$name] = $field['type'] == 'int' ? (int)$field['length']['max'] : (double)$field['length']['min'];
          }
        }
        elseif((isset($field['length']['min']) || isset($field['length']['max'])) && in_array($field['type'], array('string', 'string-html', 'textarea', 'textarea-html', 'password', 'hidden')))
        {
          if(isset($field['length']['min']) && $func['strlen']($_POST[$name]) < $field['length']['min'])
          {
            $this->forms[$form_name]['errors'][] = l('The field "%s" must be no less than %d characters.', $this->forms[$form_name]['fields'][$name]['label'], $field['length']['min']);
            continue;
          }
          elseif(isset($field['length']['max']) && $field['length']['max'] > -1 && ($truncate = ($func['strlen']($_POST[$name]) > $field['length']['max'])) && empty($field['truncate']))
          {
            $this->forms[$form_name]['errors'][] = l('The field "%s" must be no longer than %d characters.', $this->forms[$form_name]['fields'][$name]['label'], $field['length']['max']);
            continue;
          }

          # Truncation needed/wanted?
          if(!empty($truncate))
          {
            $_POST[$name] = $func['substr']($_POST[$name], 0, $field['length']['max']);
          }
        }

        # If we got here, then everything is good, so add the value :)
        $processed[$field['column']] = $_POST[$name];
      }

      # No errors? Then everything is good!
      if(count($this->forms[$form_name]['errors']) == 0)
      {
        # Give the callback the processed information so they can do whatever ;)
        # And return what it returned!!!
        $errors = array();
        $success = $this->forms[$form_name]['callback']($processed, $errors);

        # Did it fail?
        if($success === false)
        {
          # Any more errors? Add them.
          if(count($errors))
          {
            foreach($errors as $error)
            {
              $this->forms[$form_name]['errors'][] = $error;
            }
          }

          return false;
        }
        else
        {
          # We don't return just true, since the callback could return another value.
          return $success;
        }
      }
      else
      {
        # Form processing failed!!!
        return false;
      }
    }
  }

  /*
    Method: json_process

    This is almost identical to the <Form::process> method in every way,
    except this method returns a JSON encoded string containing information
    about the submission of the form. Check the notes for more information.

    Parameters:
      string $form_name - The name of the form.

    Returns:
      string - Returns a JSON-encoded string.

    Note:
      The JSON encoded string contains an array of the error messages which
      occurred while processing the form.
  */
  public function json_process($form_name)
  {
    global $api;

    # Even though process does this, it echo's the data, which we don't want.
    if(!$this->form_exists($form_name))
    {
      return json_encode(array(l('The form "%s" does not exist.', htmlchars($form_name))));
    }

    # Now process the form!
    $this->process($form_name);

    $response = array(
                  'errors' => $this->forms[$form_name]['errors'],
                  'message' => '',
                  'values' => array(),
                );

    # How about a message? :)
    $response['message'] = $api->apply_filters($form_name. '_message', '');

    # Add all the new values...
    foreach($this->forms[$form_name]['fields'] as $field_name => $field)
    {
      $response['values'][$field_name] = array('type' => $field['type'], 'value' => $field['value']);
    }

    # Now return the JSON encoded string containing any errors, if any, of course!
    return json_encode($response);
  }

  /*
    Method: run_hooks

    Runs any hooks for the specified form.

    Parameters:
      string $form_name - The name of the form.

    Returns:
      bool - Returns true if the hooks were ran, false if they were already
             ran or if the form doesn't exist.

    Note:
      Useful for using in conjunction with <Form::num_fields> when checking how
      many fields are there, even before the <Form::show> or <Form::process>
      is called.

      Yes, I bet you are thinking, "Why not just call on $api->run_hooks($form_name);
      ourselves?!?", which is a good question. You most certainly could, but you never
      know, in the future these hooks could pass more parameters, and you would need
      to then update your code, when you could have just used this :-P. Just leave it
      at, "It's not a good idea."
  */
  public function run_hooks($form_name)
  {
    global $api;

    # Doesn't exist? We won't run them! ;-)
    if(!$this->form_exists($form_name))
    {
      return false;
    }

    # Of course, we don't want to run the hook multiple times! That would be bad!
    if(empty($this->forms[$form_name]['hooked']))
    {
      $api->run_hooks($form_name);
      $this->forms[$form_name]['hooked'] = true;
      return true;
    }
    else
    {
      # Already ran them ;-)
      return false;
    }
  }
}
?>