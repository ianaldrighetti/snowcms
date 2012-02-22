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

// Title: Install Theme

if(!function_exists('admin_themes_install'))
{
	/*
		Function: admin_themes_install

		Provides an interface for installing a theme from a file uploaded
		through the web browser or by supplying a URL to a theme to download
		from somewhere on that thing we call the World Wide Web.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_themes_install()
	{
		api()->run_hooks('admin_themes_install');

		// The only way you can install a theme is if you have the
		// 'manage_themes' permission.
		if(!member()->can('manage_themes'))
		{
			admin_access_denied();
		}

		// Generate the form which will allow the user to upload a file or to
		// enter a URL of a downloadable theme.
		admin_themes_generate_form();
		$form = api()->load_class('Form');

		// Did the user submit the form?
		if(isset($_POST['install_theme_form']))
		{
			$form->process('install_theme_form');
		}

		// Do we need to clean up the theme directory? There could be some
		// temporary files that we don't need. But we will only do this if the
		// user hasn't assisted us within the last 24 hours.
		if(empty($_SESSION['themedir_cleaned']) || ((int)$_SESSION['themedir_cleaned'] + 86400) < time_utc())
		{
			foreach(scandir(themedir) as $filename)
			{
				// We will ignore anything that is a directory, any file not
				// ending with .tmp, or a file that isn't over a few hours old.
				if(is_dir(themedir. '/'. $filename) || substr($filename, -4, 4) != '.tmp' || (filemtime(themedir. '/'. $filename) + 10800) > time_utc())
				{
					continue;
				}

				// We will delete it, then.
				@unlink(themedir. '/'. $filename);
			}

			// Thanks for helping out ;-)
			$_SESSION['themedir_cleaned'] = time_utc();
		}

		// Get ready to display the form to allow upload or the entry of a URL.
		admin_current_area('install_manage_themes');

		theme()->set_title(l('Install a new Theme'));

		api()->context['form'] = $form;

		theme()->render('admin_themes_install');
	}
}

if(!function_exists('admin_themes_generate_form'))
{
	/*
		Function: admin_themes_generate_form

		Generates the form which allows themes to be installed.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_themes_generate_form()
	{
		$form = api()->load_class('Form');

		$form->add('install_theme_form', array(
																			 'action' => baseurl('index.php?action=admin&amp;sa=themes&amp;section=install'),
																			 'method' => 'post',
																			 'callback' => 'admin_themes_handle',
																			 'submit' => l('Install theme'),
																		 ));

		$form->current('install_theme_form');

		// There are a couple ways you can install a theme, either by uploading
		// a file directly or supplying a download address.
		$form->add_input(array(
											 'name' => 'theme_file',
											 'type' => 'file',
											 'label' => l('From a file:'),
											 'subtext' => l('Select the theme file you want to install as a theme.'),
											 'required' => false,
										 ));

		$form->add_input(array(
											 'name' => 'theme_url',
											 'type' => 'string',
											 'label' => l('From a URL:'),
											 'subtext' => l('Enter the URL of the theme you want to download and install.'),
											 'default_value' => 'http://',
										 ));
	}
}

if(!function_exists('admin_themes_handle'))
{
	/*
		Function: admin_themes_handle

		Handles the installation of the theme.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.
	*/
	function admin_themes_handle($data, &$errors = array())
	{
		// Generate a random name for the theme file, but we want to make sure
		// the directory doesn't exist, either.
		$filename = themedir. '/'. uniqid('theme_');
		while(file_exists($filename))
		{
			$filename = themedir. '/'. uniqid('theme_');
		}

		// Add .tmp to the end.
		$filename = $filename. '.tmp';

		// Downloading a theme, are we?
		if(!empty($data['theme_url']) && strtolower($data['theme_url']) != 'http://')
		{
			// We will need the HTTP class.
			$http = api()->load_class('HTTP');

			// Now attempt to download it.
			if(!$http->request($data['theme_url'], array(), 0, $filename))
			{
				// Sorry, but it appears that didn't work!
				$errors[] = l('Failed to download the theme from "%s"', htmlchars($data['theme_url']));
				return false;
			}

			// We want the name of the file...
			$name = basename($data['theme_url']);
		}
		// Did you want to upload a theme?
		elseif(!empty($data['theme_file']['tmp_name']))
		{
			// Now attempt to move the file.
			if(move_uploaded_file($data['theme_file']['tmp_name'], $filename))
			{
				// Keep the original file name...
				$name = $data['theme_file']['name'];
			}
			else
			{
				$errors[] = l('Failed to move the uploaded file.');
				return false;
			}
		}
		else
		{
			$errors[] = l('No file or URL specified.');
			return false;
		}

		// We will need to test the theme to make sure it is okay, not
		// deprecated and so on and so forth.
		redirect(baseurl('index.php?action=admin&sa=themes&install='. urlencode(basename($filename)). '&sid='. member()->session_id()));
	}
}

if(!function_exists('admin_themes_perform_install'))
{
	/*
		Function: admin_themes_perform_install

		Handles the installation of new themes.

		Parameters:
			none

		Returns:
			void

		Notes:
			This function is overloadable.
	*/
	function admin_themes_perform_install()
	{
		// Can you do this? If not, get out of here!
		if(!member()->can('manage_themes'))
		{
			admin_access_denied();
		}

		admin_current_area('install_manage_themes');

		// Check their session id supplied in the URL.
		verify_request('get');

		// Get the filename of the theme we are installing.
		$filename = realpath(themedir. '/'. basename($_GET['install']));
		$extension = explode('.', $filename);

		// Do some file checks, making sure it is in the right place and what
		// not. Don't want to let anyone do anything tricky, that's for sure.
		if(empty($filename) || !is_file($filename) || substr($filename, 0, strlen(realpath(themedir))) != realpath(themedir) || count($extension) < 2 || $extension[count($extension) - 1] != 'tmp')
		{
			theme()->set_title(l('An Error Occurred'));

			api()->context['error_title'] = l('Theme Installation Error');
			api()->context['error_message'] = l('Sorry, but the theme you are requesting to install does not exist or is not a valid installation package.');

			theme()->render('error');
		}
		else
		{
			// Install that theme! Maybe.
			theme()->set_title(l('Installing Theme'));

			// Thankfully the theme will be installed with the Component class.
			$component = api()->load_class('Component');

			$result = $component->install($filename, 'theme', array(
																													'ignore_status' => isset($_GET['status']) && $_GET['status'] == 'ignore',
																													'ignore_compatibility' => isset($_GET['compat']) && $_GET['compat'] == 'ignore',
																												));

			// Let's make this a bit easier.
			foreach($result as $index => $value)
			{
				api()->context[$index] = $value;
			}

			// If the installation was a success we can delete the theme.
			if(!empty($result['completed']))
			{
				unlink($filename);
			}

			api()->context['install'] = htmlchars($_GET['install']);

			theme()->render('admin_themes_perform_install');
		}
	}
}
?>