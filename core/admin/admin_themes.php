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

// Title: Control Panel - Themes

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

			api()->context['filename'] = $filename;

			// The new Extraction class is mighty handy!
			$extraction = api()->load_class('Extraction');

			// We need to make sure the uploaded package is a valid theme.
			if(!($is_valid = theme_package_valid($filename)) || !$extraction->is_supported($filename))
			{
				// Sorry, it's not a valid theme.
				api()->context['validate_message'] = !$is_valid ? l('The file you have requested to install is not a valid theme.') : l('The file you have requested to install could not be extracted because it is an unsupported file type.');
				api()->context['validate_is_error'] = true;
			}
			else
			{
				api()->context['validate_message'] = l('The theme package was successfully validated. Proceeding...');

				// Let's get out the theme.xml file, we will need that for a couple
				// of things.
				$tmp_filename = tempnam(dirname(__FILE__), 'theme_');

				if(!$extraction->read($filename, 'theme.xml', $tmp_filename))
				{
					api()->context['status_message'] = l('The theme.xml file failed to be extracted from the theme package.');
					api()->context['status_class'] = 'red';
					api()->context['proceed'] = false;
				}
				else
				{
					// Now load the themes information.
					$theme_info = theme_get_info($tmp_filename);

					// Alright, we will now check whether or not the theme is
					// approved by SnowCMS.
					$status = plugin_check_status($filename, $reason);

					// Get the status message, and the color that the message should be.
					// But first, include a file.
					require_once(coredir. '/admin/admin_plugins_add.php');

					// Okay, now get the response!
					$response = admin_plugins_get_message($status, $theme_info['name'], $reason, true);

					// Is it okay? Can we continue without prompting?
					$install_proceed = isset($_GET['proceed']) || $status == 'approved';
					api()->run_hooks('plugin_install_proceed', array(&$install_proceed, $status, 'theme'));

					api()->context['status_message'] = $response['message'];
					api()->context['status_class'] = $response['div-class'];
					api()->context['proceed'] = $install_proceed;

					// Shall we proceed?
					if(!empty($install_proceed))
					{
						// We are almost there... Really! We are! We just need to check
						// the themes compatibility.
						api()->context['is_compatible'] = $theme_info['is_compatible'];
						api()->context['compatible_is_error'] = false;

						// We will continue if it is compatible, if no compatibility was
						// supplied or if you choose to ignore the warning.
						if($theme_info['is_compatible'] === true || $theme_info['is_compatible'] === null || (isset($_GET['compat']) && $_GET['compat'] == 'ignore'))
						{
							if($theme_info['is_compatible'] !== false)
							{
								api()->context['compatible_message'] = l('The theme &quot;%s&quot; is compatible with your version of SnowCMS. Proceeding...', $theme_info['name']);
							}
							else
							{
								api()->context['compatible_message'] = l('The theme &quot;%s&quot; is not compatible with your version of SnowCMS. Proceeding with installation anyways...', $theme_info['name']);
							}

							// Now just one last thing, extracting!
							// Let's get a nice name for the themes directory, how about
							// the name of the theme itself?
							$name = sanitize_filename($theme_info['name']);

							// But you may already have a theme with the same name, so...
							if(file_exists(themedir. '/'. $name))
							{
								$count = 1;
								while(file_exists(themedir. '/'. $name. ' ('. $count. ')'))
								{
									// Keep going.
									$count++;
								}

								// Looks like we found a suitable match!
								$name .= ' ('. $count. ')';
							}

							// Now attempt to extract the package. Well, first try to make
							// the directory.
							if(!file_exists(themedir. '/'. $name) && !@mkdir(themedir. '/'. $name, 0755, true))
							{
								api()->context['extract_message'] = l('Please make sure the theme directory is writable and try installing the theme again.');
								api()->context['extract_is_error'] = true;

								unlink($tmp_filename);
							}
							// Ok, now try to extract it.
							elseif($extraction->extract($filename, themedir. '/'. $name))
							{
								api()->context['extract_message'] = l('The theme was successfully extracted. Proceeding...');

								// Now if the theme is valid, which is should be since we
								// checked before, we can complete the installation.
								if(theme_load(themedir. '/'. $name) !== false)
								{
									// Execute install.php, if there is one.
									if(file_exists(themedir. '/'. $name. '/install.php'))
									{
										// We will just include it.
										require(themedir. '/'. $name. '/install.php');

										// Now delete it.
										unlink(themedir. '/'. $name. '/install.php');
									}

									api()->context['completed'] = true;

									// Delete the stuff we no longer need.
									unlink($filename);
									unlink($tmp_filename);
								}
								else
								{
									api()->context['completed'] = false;

									unlink($tmp_filename);
									unlink($filename);
									recursive_unlink(themedir. '/'. $name);
								}
							}
							else
							{
								// Hmm, something went wrong.
								api()->context['extract_message'] = l('The theme package could not be extracted for an unknown reason.');
								api()->context['extract_is_error'] = true;

								unlink($tmp_filename);
								recursive_unlink(themedir. '/'. $name);
							}
						}
						else
						{
							// Shucks! The theme author says it isn't compatible with your
							// current version of SnowCMS. Do you want to continue?
							api()->context['compatible_is_error'] = true;
							api()->context['install'] = htmlchars($_GET['install']);
							api()->context['compatible_message'] = l('The theme &quot;%s&quot; is not compatible with your version of SnowCMS. You may continue with the installation anyways, if you choose.', $theme_info['name']);

							unlink($tmp_filename);
						}
					}
					else
					{
						// Uh oh!
						// It was not safe, but if you still want to continue installing
						// it, be my guest! Just be sure you know what you're getting
						// yourself into, please!
						unlink($tmp_filename);

						api()->context['install'] = htmlchars($_GET['install']);
					}
				}
			}

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
			$update_version = admin_themes_check_updates(themedir. '/'. $update_theme);

			// Let's make our lives a bit easier...
			$update_dir = themedir. '/'. $update_theme;

			// So, how did that go?
			if($update_version === false || compare_versions($theme_info['version'], $update_version, '>=') || $theme_info['update_url'] === false)
			{
				// No update needed!
				theme()->set_title(l('No Update Available'));

				api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/manage_themes-small.png" alt="" /> '. l('No Update Available');
				api()->context['error_message'] = l('There is no update available for the theme &quot;%s.&quot; <a href="%s">Back to theme management &raquo;</a>', htmlchars($theme_info['name']), baseurl. '/index.php?action=admin&amp;sa=themes&amp;section=manage');

				theme()->render('error');
			}
			else
			{
				// There is an update for this theme! This process is almost the
				// same as installing a theme, only we have to download the update
				// ourselves from the update location before we can begin.
				// So let's get to it!
				$http = api()->load_class('HTTP');

				// We need to make sure the directory is writable.
				if(!is_writable($update_dir))
				{
					api()->context['download_message'] = l('Please make sure the theme directory is writable and try updating the theme again.');
					api()->context['download_is_error'] = true;
				}
				elseif(!$http->request($theme_info['update_url'], array('download' => 1, 'version' => $update_version), 0, $update_dir. '/theme-update.tmp'))
				{
					// That didn't go so well.
					api()->context['download_message'] = l('Failed to download the theme update file. Please make sure that either <a href="http://www.php.net/fsockopen" target="_blank">fsockopen</a> or <a href="http://www.php.net/curl" target="_blank">cURL</a> is enabled. This error could also be caused by being unable to contact the theme&#039;s update server.');
					api()->context['download_is_error'] = true;
				}
				// Did we download it? Sweet!
				else
				{
					api()->context['download_message'] = l('The theme update was successfully downloaded. Proceeding...');

					// This will come in handy.
					$filename = $update_dir. '/theme-update.tmp';

					// The new Extraction class is mighty handy!
					$extraction = api()->load_class('Extraction');

					// We need to make sure the uploaded package is a valid theme.
					if(!($is_valid = theme_package_valid($filename)) || !$extraction->is_supported($filename))
					{
						// Sorry, it's not a valid theme.
						api()->context['validate_message'] = !$is_valid ? l('The theme update package downloaded is not a valid theme.') : l('The theme update package downloaded could not be extracted because it is an unsupported file type.');
						api()->context['validate_is_error'] = true;

						unlink($filename);
					}
					else
					{
						api()->context['validate_message'] = l('The theme update package was successfully validated. Proceeding...');

						// Let's get out the theme.xml file, we will need that for a couple
						// of things.
						$tmp_filename = tempnam(dirname(__FILE__), 'theme_');

						if(!$extraction->read($filename, 'theme.xml', $tmp_filename))
						{
							api()->context['status_message'] = l('The theme.xml file failed to be extracted from the theme update package.');
							api()->context['status_class'] = 'red';
							api()->context['proceed'] = false;

							unlink($tmp_filename);
							unlink($filename);
						}
						else
						{
							// Now load the themes information.
							$updated_theme_info = theme_get_info($tmp_filename);

							// Alright, we will now check whether or not the theme is
							// approved by SnowCMS.
							$status = plugin_check_status($filename, $reason);

							// Get the status message, and the color that the message should be.
							// But first, include a file.
							require_once(coredir. '/admin/admin_plugins_add.php');

							// Okay, now get the response!
							$response = admin_plugins_get_message($status, $updated_theme_info['name'], $reason, true);

							// Is it okay? Can we continue without prompting?
							$install_proceed = isset($_GET['proceed']) || $status == 'approved';
							api()->run_hooks('plugin_install_proceed', array(&$install_proceed, $status, 'theme'));

							api()->context['status_message'] = $response['message'];
							api()->context['status_class'] = $response['div-class'];
							api()->context['proceed'] = $install_proceed;

							// Shall we proceed?
							if(!empty($install_proceed))
							{
								// We are almost there... Really! We are! We just need to check
								// the themes compatibility.
								api()->context['is_compatible'] = $updated_theme_info['is_compatible'];
								api()->context['compatible_is_error'] = false;

								// We will continue if it is compatible, if no compatibility was
								// supplied or if you choose to ignore the warning.
								if($updated_theme_info['is_compatible'] === true || $updated_theme_info['is_compatible'] === null || (isset($_GET['compat']) && $_GET['compat'] == 'ignore'))
								{
									if($updated_theme_info['is_compatible'] !== false)
									{
										api()->context['compatible_message'] = l('The theme update is compatible with your version of SnowCMS. Proceeding...', $updated_theme_info['name']);
									}
									else
									{
										api()->context['compatible_message'] = l('The theme update is not compatible with your version of SnowCMS. Proceeding with update anyways...', $updated_theme_info['name']);
									}

									// Now attempt to extract the package. Well, first try to
									// extract the update to a temporary location.
									if(!file_exists($update_dir. '/theme-update/') && !@mkdir($update_dir. '/theme-update/', 0755, true))
									{
										api()->context['extract_message'] = l('Please make sure the theme&#039;s directory is writable and try updating the theme again.');
										api()->context['extract_is_error'] = true;

										unlink($tmp_filename);
										unlink($filename);
									}
									// Ok, now try to extract it.
									elseif($extraction->extract($filename, $update_dir. '/theme-update/'))
									{
										api()->context['extract_message'] = l('The theme update was successfully extracted. Proceeding...');

										// Now if the theme is valid, which is should be since we
										// checked before, we can complete the installation.
										if(theme_load($update_dir. '/theme-update/') !== false && copydir($update_dir. '/theme-update/', $update_dir))
										{
											// We are done with this!
											recursive_unlink($update_dir. '/theme-update/');

											// Execute install.php, if there is one.
											if(file_exists($update_dir. '/install.php'))
											{
												// Set a variable just in case.
												$updating_from = $theme_info['version'];

												// We will just include it.
												require($update_dir. '/install.php');

												// Now delete it.
												unlink($update_dir. '/install.php');
											}

											api()->context['completed'] = true;

											// Woopsie! Forgot to mark this theme as up-to-date.
											$theme_updates = settings()->get('theme_updates', 'array', array());

											// We can do that by deleting the entry, like so:
											unset($theme_updates[basename($theme_info['path'])]);

											// Then putting it back.
											settings()->get('theme_updates', $theme_updates);

											// Delete the stuff we no longer need.
											unlink($filename);
											unlink($tmp_filename);
										}
										else
										{
											api()->context['completed'] = false;

											unlink($tmp_filename);
											unlink($filename);
											recursive_unlink($update_dir. '/theme-update/');
										}
									}
									else
									{
										// Hmm, something went wrong.
										api()->context['extract_message'] = l('The theme update package could not be extracted for an unknown reason.');
										api()->context['extract_is_error'] = true;

										unlink($tmp_filename);
										unlink($filename);
										recursive_unlink($update_dir. '/theme-update/');
									}
								}
								else
								{
									// Shucks! The theme author says it isn't compatible with your
									// current version of SnowCMS. Do you want to continue?
									api()->context['compatible_is_error'] = true;
									api()->context['update'] = htmlchars(basename($theme_info['path']));
									api()->context['compatible_message'] = l('The theme &quot;%s&quot; is not compatible with your version of SnowCMS. You may continue with the update anyways, if you choose.', $updated_theme_info['name']);

									unlink($tmp_filename);
									unlink($filename);
								}
							}
							else
							{
								// Uh oh!
								// It was not safe, but if you still want to continue installing
								// it, be my guest! Just be sure you know what you're getting
								// yourself into, please!
								unlink($tmp_filename);
								unlink($filename);

								api()->context['update'] = htmlchars(basename($theme_info['path']));
							}
						}
					}
				}

				theme()->set_title(l('Updating Theme'));

				api()->context['theme_name'] = $theme_info['name'];
				api()->context['theme_version'] = $theme_info['version'];
				api()->context['update_version'] = $update_version;

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

				// We will use the supplied update URL to query for any available
				// updates. This of course requires both the update URL (of course)
				// and a current version to be supplied.
				$request = $http->request('http://'. $theme_info['update_url'], array('updatecheck' => 1, 'version' => $theme_info['version']));

				// Did we get an answer?
				if(empty($request))
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