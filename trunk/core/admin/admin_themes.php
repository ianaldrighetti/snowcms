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

// Title: Add and Manage Themes

if(!function_exists('admin_themes'))
{
	/*
		Function: admin_themes

		Provides an interface for the selecting and uploading/downloading of themes.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_themes()
	{
		api()->run_hooks('admin_themes');

		// Can you view the error log? Don't try and be sneaky now!
		if(!member()->can('manage_themes'))
		{
			// Get out of here!!!
			admin_access_denied();
		}

		$theme_sections = api()->apply_filters('admin_theme_sections', array(
																																		 'manage' => array(
																																									 l('Manage Themes'),
																																									 l('Manage currently installed themes'),
																																								 ),
																																		 'install' => array(
																																										l('Install Themes'),
																																										l('Install a new theme from a file or from the Internet'),
																																									),
																																	 ));

		// Which section are you accessing?
		$theme_section = isset($_GET['section']) && isset($theme_sections[$_GET['section']]) ? $_GET['section'] : 'manage';
		$GLOBALS['_GET']['section'] = $theme_section;

		// Now to generate a section menu, to make stuff easier and simpler in
		// the template.
		api()->context['section_menu'] = array();
		$is_first = true;
		foreach($theme_sections as $section_id => $section_info)
		{
			api()->context['section_menu'][] = array(
																					 'href' => baseurl. '/index.php?action=admin&amp;sa=themes&amp;section='. $section_id,
																					 'title' => $section_info[1],
																					 'is_first' => $is_first,
																					 'is_selected' => $section_id == $theme_section,
																					 'text' => $section_info[0],
																				 );

			$is_first = false;
		}

		// Time for a Form, awesomeness!!!
		admin_themes_generate_form();
		$form = api()->load_class('Form');

		// You should only be able to set or delete a theme if you are in the
		// right section of the control panel. The same goes for installing a
		// new theem.
		if($theme_section == 'manage' && (!empty($_GET['set']) || !empty($_GET['delete'])) && verify_request('get'))
		{
			if(!empty($_GET['set']))
			{
				// Pretty simple to change the current theme ;-)
				$new_theme = basename($_GET['set']);

				// Check to see if the theme exists.
				if(file_exists(themedir. '/'. $new_theme) && theme_load(themedir. '/'. $new_theme) !== false)
				{
					// Simple enough, set the theme.
					settings()->set('theme', $new_theme, 'string');
				}
			}
			elseif(!empty($_GET['delete']))
			{
				// Deleting, are we?
				$delete_theme = basename($_GET['delete']);

				// Make sure it isn't the current theme.
				if(settings()->get('theme', 'string', 'default') != $delete_theme && theme_load(themedir. '/'. $delete_theme) !== false)
				{
					// It's not, so we can delete it.
					// Which is simply a recursive delete.
					recursive_unlink(themedir. '/'. $delete_theme);
				}
			}

			// Let's get you out of here now :-)
			redirect(baseurl. '/index.php?action=admin&sa=themes');
		}
		// So are you wanting to install a new theme?
		elseif($theme_section == 'install' && isset($_POST['install_theme_form']))
		{
			// Process that form!
			$form->process('install_theme_form');
		}

		// We may need to generate and save a few things, depending upon the
		// section we are in.
		if($theme_section == 'manage')
		{
			api()->context['themes'] = theme_list();

			// The theme_updates contains a serialized array containing all
			// available updates for themes. The keys are the themes update URL's
			// and the value is the version available.
			$theme_updates = settings()->get('theme_updates', 'array', array());
			$current = 1;
			api()->context['theme_list'] = array();
			foreach(theme_list() as $theme_id)
			{
				$theme_info = theme_load($theme_id);

				// Build an array containing the themes information.
				$theme = array(
									 'anchor' => 't'. sha1(basename($theme_info['directory'])),
									 'name' => $theme_info['name'],
									 'description' => $theme_info['description'],
									 'author' => (!empty($theme_info['website']) ? '<a href="'. $theme_info['website']. '" target="_blank">' : ''). $theme_info['author']. (!empty($theme_info['website']) ? '</a>' : ''),
									 'version' => $theme_info['version'],
									 'select_href' => baseurl. '/index.php?action=admin&amp;sa=themes&amp;set='. urlencode(basename($theme_info['path'])). '&amp;sid='. member()->session_id(),
									 'delete_href' => baseurl. '/index.php?action=admin&amp;sa=themes&amp;delete='. urlencode(basename($theme_info['path'])). '&amp;sid='. member()->session_id(),
									 'image_url' => themeurl. '/'. basename($theme_info['path']). '/image.png',
									 'update_available' => isset($theme_updates[basename($theme_info['path'])]) && compare_versions($theme_updates[basename($theme_info['path'])], $theme_info['version'], '>'),
									 'update_version' => false,
									 'update_href' => false,
									 'new_row' => $current % 3 == 0,
									 'is_last' => false,
								 );

				// Didn't set the update URL because this will make it a bit easier.
				if($theme['update_available'])
				{
					$theme['update_href'] = baseurl. '/index.php?action=admin&amp;sa=themes&amp;update='. urlencode(basename($theme_info['path'])). '&amp;sid='. member()->session_id();
					$theme['update_version'] = $theme_updates[basename($theme_info['path'])];
				}

				// If this is the current theme, we will take this out and use it
				// for something else.
				if(basename($theme_info['path']) == settings()->get('theme', 'string', 'default'))
				{
					api()->context['current_theme'] = $theme;

					continue;
				}
				elseif(empty($theme['name']))
				{
					// No name? Then we will ignore this.
					continue;
				}
				else
				{
					// Not the current theme, so we will display it.
					api()->context['theme_list'][] = $theme;
				}

				$current++;
			}

			// If a new row was requested on the last item, we will mark it as
			// false, as we don't need any more rows if there are no more themes.
			// Got it? Good :-) Well, that is if there are any themes.
			if(count(api()->context['theme_list']) > 0)
			{
				api()->context['theme_list'][count(api()->context['theme_list']) - 1]['new_row'] = false;
				api()->context['theme_list'][count(api()->context['theme_list']) - 1]['is_last'] = true;
			}

			// How wide should the table be?
			api()->context['table_width'] = count(api()->context['theme_list']) == 1 ? '33%' : (count(api()->context['theme_list']) == 2 ? '66%' : '100%');
		}

		admin_current_area('manage_themes');

		theme()->set_title(l('Manage Themes'));

		api()->context['form'] = $form;
		api()->context['section'] = $theme_section;

		theme()->render('admin_themes');
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
																			 'action' => baseurl. '/index.php?action=admin&amp;sa=themes&amp;section=install',
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
		redirect(baseurl. '/index.php?action=admin&sa=themes&install='. urlencode(basename($filename)). '&sid='. member()->session_id());
	}
}

if(!function_exists('admin_themes_install'))
{
	/*
		Function: admin_themes_install

		Handles the installation of new themes.

		Parameters:
			none

		Returns:
			void

		Notes:
			This function is overloadable.
	*/
	function admin_themes_install()
	{
		// Can you do this? If not, get out of here!
		if(!member()->can('manage_themes'))
		{
			admin_access_denied();
		}

		admin_current_area('manage_themes');

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

			theme()->render('admin_themes_install');
		}
	}
}

if(!function_exists('admin_themes_update'))
{
	/*
		Function: admin_themes_update

		When there is an update for a theme available, this function will handle
		the update process by downloading, extracting, and installing the new
		version of the theme.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_themes_update()
	{
		// Can you do this? If not, get out of here!
		if(!member()->can('manage_themes'))
		{
			admin_access_denied();
		}

		admin_current_area('manage_themes');

		// Check their session id supplied in the URL.
		verify_request('get');

		// Which theme are we updating?
		$update_theme = !empty($_GET['update']) ? htmlchars(basename($_GET['update'])) : false;
		$theme_info = theme_load(themedir. '/'. $update_theme);

		// Make sure this stuff given to us is valid. Though we only need to
		// make sure the theme given is actually a real theme.
		if(empty($update_theme) || $theme_info === false)
		{
			theme()->set_title(l('Theme Not Found'));

			api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/manage_themes-small.png" alt="" /> '. l('Theme Not Found');
			api()->context['error_message'] = l('Sorry, but the theme you are requesting to update does not exist. Please <a href="javascript:history.go(-1);">go back</a> and try again.');

			theme()->render('error');
		}
		else
		{
			// Get the latest version of this theme.
			// This function will handle that for us.
			$update_version = admin_themes_check_updates($theme_info['directory']);

			// So, how did that go?
			if($update_version === false || compare_versions($theme_info['version'], $update_version, '>=') || $theme_info['update_url'] === false)
			{
				// No update needed! But this may be a system error, as outdated
				// information may be indicating to the administrator that there is
				// an update, when there isn't.
				$theme_updates = settings()->get('theme_updates', 'array', array());
				if(!empty($theme_updates[basename($theme_info['directory'])]))
				{
					// Looks like the system does think there is an update available,
					// so maybe we should fix that...
					unset($theme_updates[basename($theme_info['directory'])]);

					settings()->set('theme_updates', $theme_updates);
				}

				theme()->set_title(l('No Update Available'));

				api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/manage_themes-small.png" alt="" /> '. l('No Update Available');
				api()->context['error_message'] = l('There is no update available for the theme &quot;%s.&quot; <a href="%s">Back to theme management &raquo;</a>', htmlchars($theme_info['name']), baseurl. '/index.php?action=admin&amp;sa=themes&amp;section=manage');

				theme()->render('error');
			}
			else
			{
				// The Component class makes this very easy :-)
				$component = api()->load_class('Component');

				$result = $component->update($theme_info['directory'], $update_version, 'theme', array(
																																													 'ignore_status' => isset($_GET['status']) && $_GET['status'] == 'ignore',
																																													 'ignore_compatibility' => isset($_GET['compat']) && $_GET['compat'] == 'ignore',
																																												 ));

				// Let's make this a bit easier.
				foreach($result as $index => $value)
				{
					api()->context[$index] = $value;
				}

				// Was the update a success? Awesome.
				if(!empty($result['completed']))
				{
					// But we need to remove the update notification, after we check
					// for any more updates, that is.
					$response = admin_themes_check_updates($theme_info['directory']);

					$theme_updates = settings()->get('theme_updates', 'array', array());

					// So, delete it or change it?
					if(empty($response))
					{
						// Delete it.
						unset($theme_updates[basename($theme_info['directory'])]);
					}
					else
					{
						// Looks like you have some more updates to do.
						$theme_updates[basename($theme_info['directory'])] = $response;
					}

					// Now save it.
					settings()->set('theme_updates', $theme_updates);
				}

				api()->context['update'] = htmlchars($_GET['update']);
				api()->context['theme_name'] = $theme_info['name'];
				api()->context['theme_version'] = $theme_info['version'];
				api()->context['update_version'] = $update_version;

				theme()->set_title(l('Updating Theme'));

				theme()->render('admin_themes_update');
			}
		}
	}
}

if(!function_exists('admin_themes_check_updates'))
{
	/*
		Function: admin_themes_check_updates

		Checks to see if the themes require any updating. If no specific theme
		directories are supplied, all themes will be checked for updates, if
		they support it.

		Parameters:
			array $themes - An array containing an array of directories which are
											at the root of the theme.

		Returns:
			mixed - Returns an array containing all the themes that have an update
							available, if multiple themes were supplied in the $themes
							parameter. If only one theme was supplied then the update
							version available will be returned (a string) or false if
							there are no updates available.

		Note:
			This function is overloadable.

			If the $themes parameter is empty, it is assumed that this function is
			being called upon by the SnowCMS task system and will check for
			updates for all existing themes, so long as it hasn't been done within
			the last hour.
	*/
	function admin_themes_check_updates($themes = array(), $force_check = false)
	{
		global $func;

		// We will save available updates to an array, which will then be saved
		// to the settings table.
		$theme_updates = array();

		// Not an array? We'll make it one!
		if(!is_array($themes))
		{
			$themes = array($themes);
		}

		// Did any theme directories get supplied?
		if(count($themes) == 0)
		{
			// Since you didn't supply any themes, we will get them on our own!
			// It is likely this is being called on by the task scheduling system,
			// so let's make sure we aren't doing this too often!
			if(settings()->get('last_theme_update_check', 'int', 0) + 3600 < time_utc())
			{
				// <theme_load> will get us the information we want.
				$theme_list = theme_list();

				foreach($theme_list as $theme_location)
				{
					$themes[] = $theme_location;
				}

				// We are checking now.
				settings()->set('last_theme_update_check', time_utc(), 'int');

				// We will need this to see if we should save the results.
				$system_update_check = true;
			}
		}

		// Now for the actual checking.
		if(count($themes) > 0)
		{
			// The HTTP class will do everything we want.
			$http = api()->load_class('HTTP');

			foreach($themes as $theme_location)
			{
				// Get the theme information.
				$theme_info = theme_load($theme_location);

				// Does the theme not exist, no update URL, no version?
				if($theme_info === false || empty($theme_info['update_url']) || empty($theme_info['version']))
				{
					continue;
				}

				// Set up the POST data we will be sending.
				$post_data = array('requesttype' => 'updatecheck', 'version' => $theme_info['version']);

				// Want to add some sort of update key or something?
				if($func['strlen'](api()->apply_filters(sha1($theme_info['directory']). '_updatekey', '')) > 0)
				{
					$post_data['updatekey'] = api()->apply_filters(sha1($theme_info['directory']). '_updatekey', '');
				}

				// We will use the supplied update URL to query for any available
				// updates. This of course requires both the update URL (of course)
				// and a current version to be supplied. See
				// http://code.google.com/p/snowcms/wiki/SUTP for more information.
				$request = $http->request('http://'. $theme_info['update_url'], $post_data);

				// Did we get an answer?
				if(empty($request) || trim(strtolower($request)) == 'UPTODATE')
				{
					// Nope, nothing. How rude!
					continue;
				}

				// If there is a new version, we will save it in a file.
				if(compare_versions($request, $theme_info['version'], '>'))
				{
					// Themes don't have a GUID like plugins do, the directory the
					// theme is located within will be used instead.
					$theme_updates[basename($theme_info['path'])] = $request;
				}
			}
		}

		// Now save the available updates array to the database. If we were
		// checking all themes, that is.
		if(!empty($system_update_check))
		{
			settings()->set('theme_updates', $theme_updates, 'string');
		}

		// Did you give us one theme to check for updates?
		if(count($themes) == 1)
		{
			// Alright, let's see if we have something to return.
			if(count($theme_updates) > 0)
			{
				// POP! Goes the weasel! =D
				return array_pop($theme_updates);
			}
			else
			{
				// Looks like there is no update available.
				return false;
			}
		}
		else
		{
			// Return the whole thing, then.
			return $theme_updates;
		}
	}
}
?>