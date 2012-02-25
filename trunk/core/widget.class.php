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
	Class: Widget

	The Widget class is a base (abstract) class which can be inherited for
	a plugin to create widgets that can display dynamic (or static) content
	within a theme.
*/
abstract class Widget
{
	// Variable: name
	// A string containing the name of the widget.
	private $name;

	// Variable: description
	// A string containing the description of the widget.
	private $description;

	/*
		Constructor: __construct

		Parameters:
			string $name - The name of the widget.
			string $description - A description of the widget.
	*/
	public function __construct($name, $description)
	{
		$this->name = null;
		$this->description = null;

		$this->name($name);
		$this->description($description);
	}

	/*
		Method: name

		Gets or sets the name of the widget.

		Parameters:
			string $name - The name of the widget.

		Returns:
			mixed - Returns a string containing the widgets name if the $name
							parameter is empty, otherwise true if the name was set
							successfully and false if not.
	*/
	final public function name($name = null)
	{
		if($name !== null)
		{
			// The name cannot be empty.
			if(strlen($name) > 0)
			{
				$this->name = htmlchars($name);

				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $this->name;
		}
	}

	/*
		Method: description

		Gets or sets the description of the widget.

		Parameters:
			string $description - The description of the widget.

		Returns:
			mixed - Returns a string containing the description of the widget if
							the $description parameter is empty, but true if the
							description was set successfully and false if not.
	*/
	final public function description($description = null)
	{
		if($description !== null)
		{
			if(strlen($description) > 0)
			{
				$this->description = htmlchars($description);

				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $this->description;
		}
	}

	/*
		Method: form

		This method returns a string containing the form for the options of the
		widget displayed within the control panel. This form should not contain
		the HTML form tag, as this is done automatically.

		Parameters:
			array $options - An array containing the currently set options for the
											 widget.

		Returns:
			string

		Note:
			This is an abstract method and must be implemented.

			It is highly recommended, although not required, that every widget
			have a title option (with the options name being, surprise! title)
			which can be edited by the user to modify the title/label of the
			widget. This is used within the widget manager for users to quickly
			identify each individual widget -- even if it is of the same widget
			type.
	*/
	abstract public function form($options);

	/*
		Method: save

		This method receives an array containing the options the user wants to
		set for the widget. This method will verify the options submitted by the
		user and return an array containing the options to be saved to the
		database.

		Parameters:
			array $options - An array containing the options specified by the
											 user.
			array &$errors - An array containing an errors regarding the users
											 specified options.

		Returns:
			mixed - Returns an array containing the options to be saved to the
							database, or false if any errors occurred while verifying the
							users specified options.

		Note:
			This is an abstract method and must be implemented.
	*/
	abstract public function save($options, &$errors = array());

	/*
		Method: default_options

		This method returns an array containing the default options for a widget
		which has been created.

		Parameters:
			none

		Returns:
			array - Returns an array containing the default options for the widget
							which may be nothing (though, as stated in <Widget::form> it
							is recommended that there is at least a title option).

		Note:
			This method does not have to be implemented, however, if it isn't then
			the default implementation will return an array containing an index
			'title' with the value of the name of the widget.
	*/
	public function default_options()
	{
		return array(
						 'title' => $this->name(),
					 );
	}

	/*
		Method: display

		This method will generate and display the contents of the widget
		according to the options specified by the theme and also the currently
		set options for the widget.

		Parameters:
			array $display_options - An array containing display options for the
															 widget, which are specified by the theme
															 itself. See the notes for more information.
			array $options - An array containing the options that were specified
											 by the user within the control panel for this widget.

		Returns:
			void - Nothing is to be returned by this method, as the widget is
						 expected to echo any necessary content.

		Note:
			This is an abstract method and must be implemented.

			The $display_options parameter will contain the following:

				string before - A string specifying any content that the theme wants
												the widget to display before any of the widgets own
												content.

				string after - A string specifying any content that the theme wants
											 the widget to display after any of the widgets own
											 content.

				string before_title - A string specifying any content that the theme
															wants the widget to display before the title
															of the widget.

				string after_title - A string specifying any content that the theme
														 wants the widget to display after the title of
														 the widget.

				string before_content - A string specifying any content that the
																theme wants the widget to display before any
																main content of the widget.

				string after_content - A string specifying any content that the
															 theme wants the widget to display after any
															 main content of the widget.

			Each of these indexes will always be set, though they may contain an
			empty string.
	*/
	abstract function display($display_options, $options);
}
?>