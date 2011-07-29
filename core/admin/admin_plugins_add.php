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

// Title: Control Panel - Plugins - Add

if(!function_exists('admin_plugins_add'))
{
	/*
		Function: admin_plugins_add

		Handles the downloading and extracting of plugins.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_add()
	{
		api()->run_hooks('admin_plugins_add');

		// Can you add plugins?
		if(!member()->can('add_plugins'))
		{
			// That's what I thought!
			admin_access_denied();
		}

		admin_plugins_add_generate_form();
		$form = api()->load_class('Form');

		if(!empty($_POST['add_plugins_form']))
		{
			$form->process('add_plugins_form');
		}

		admin_current_area('plugins_add');

		theme()->set_title(l('Add Plugin'));

		api()->context['form'] = $form;

		theme()->render('admin_plugins_add');
	}
}

if(!function_exists('admin_plugins_add_generate_form'))
{
	/*
		Function: admin_plugins_add_generate_form

		Generates the form which allows you to upload or download a plugin.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_add_generate_form()
	{
		$form = api()->load_class('Form');

		// Let's get to making our form, shall we?
		$form->add('add_plugins_form', array(
																		 'action' => baseurl. '/index.php?action=admin&amp;sa=plugins_add',
																		 'callback' => 'admin_plugins_add_handle',
																		 'method' => 'post',
																		 'submit' => l('Add plugin'),
																	 ));

		$form->current('add_plugins_form');

		// Do you want to upload the plugin?
		$form->add_input(array(
											 'name' => 'plugin_file',
											 'type' => 'file',
											 'label' => l('From a file'),
											 'subtext' => l('Select the plugin file you want to install.'),
											 'required' => false,
										 ));

		// A URL? Sure!
		$form->add_input(array(
											 'name' => 'plugin_url',
											 'type' => 'string',
											 'label' => l('From a URL'),
											 'subtext' => l('Enter the URL of the plugin you want to download and install.'),
											 'default_value' => 'http://',
										 ));
	}
}

if(!function_exists('admin_plugins_add_handle'))
{
	/*
		Function: admin_plugins_add_handle

		Handles the form data submitted through the add plugins form.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns false on failure, the user gets redirected to
						 {baseurl}/index.php?action=admin&sa=plugins_add&install={filename}
						 where the status of the plugin is checked and then installed.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_add_handle($data, &$errors = array())
	{
		// Where should this plugin go..?
		$filename = plugindir. '/'. uniqid('plugin_');
		while(file_exists($filename))
		{
			$filename = plugindir. '/'. uniqid('plugin_');
		}

		// We wanted to make sure the directory didn't exist yet.
		$filename .= '.tmp';

		// Uploading a file, are we?
		if(!empty($data['plugin_file']['tmp_name']))
		{
			// Simply try to move the file now.
			if(!move_uploaded_file($data['plugin_file']['tmp_name'], $filename))
			{
				// Woops, didn't work!
				$errors[] = l('Plugin upload failed.');

				return false;
			}
		}
		// You want us to download it? I can do that.
		elseif(!empty($data['plugin_url']) && strtolower($data['plugin_url']) != 'http://')
		{
			// The HTTP class can do all this, awesomely, of course!
			$http = api()->load_class('HTTP');

			if(!$http->request($data['plugin_url'], array(), 0, $filename))
			{
				// Sorry, but looks like it didn't work!!!
				$errors[] = l('Failed to download the plugin from &quot;%s&quot;', htmlchars($data['plugin_url']));

				return false;
			}
		}
		else
		{
			$errors[] = l('No file or URL specified.');

			return false;
		}

		// If it worked, we get redirected!
		redirect(baseurl. '/index.php?action=admin&sa=plugins_add&install='. urlencode(basename($filename)). '&sid='. member()->session_id());
	}
}

if(!function_exists('admin_plugins_install'))
{
	/*
		Function: admin_plugins_install

		Handles the actual installing of the plugin, after things
		such as the plugins status is checked on SnowCMS.com

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_plugins_install()
	{
		api()->run_hooks('admin_plugins_install');

		// Can you add plugins?
		if(!member()->can('add_plugins'))
		{
			// That's what I thought!
			admin_access_denied();
		}

		admin_current_area('plugins_add');

		// Check the session id.
		verify_request('get');

		// Which file are you installing as a plugin?
		$filename = realpath(plugindir. '/'. basename($_GET['install']));
		$extension = explode('.', $filename);

		// Make sure the file exists, that it is a file, that it is within the
		// plugin directory, and that the extension is valid.
		if(empty($filename) || !is_file($filename) || substr($filename, 0, strlen(realpath(plugindir))) != realpath(plugindir) || count($extension) < 2 || $extension[count($extension) - 1] != 'tmp')
		{
			// Must not be valid, from what we can tell.
			theme()->set_title(l('An Error Occurred'));

			api()->context['error_title'] = '<img src="'. theme()->url(). '/style/images/plugins_add-small.png" alt="" /> '. l('Plugin Installation Error');
			api()->context['error_message'] = l('Sorry, but the supplied plugin file either does not exist or is not a valid file.');

			theme()->render('error');
		}
		else
		{
			// Time to get to installation!
			theme()->set_title(l('Installing Plugin'));

			api()->context['filename'] = $filename;

			// The new Extraction class is mighty handy!
			$extraction = api()->load_class('Extraction');

			// We need to make sure the uploaded package is a valid plugin.
			if(!($is_valid = plugin_package_valid($filename)) || !$extraction->is_supported($filename))
			{
				// Sorry, it's not a valid theme.
				api()->context['validate_message'] = !$is_valid ? l('The file you have requested to install is not a valid plugin.') : l('The file you have requested to install could not be extracted because it is an unsupported file type.');
				api()->context['validate_is_error'] = true;
			}
			else
			{
				api()->context['validate_message'] = l('The plugin package was successfully validated. Proceeding...');

				// Let's get out the plugin.xml file, we will need that for a couple
				// of things.
				$tmp_filename = tempnam(dirname(__FILE__), 'plugin_');

				if(!$extraction->read($filename, 'plugin.xml', $tmp_filename))
				{
					api()->context['status_message'] = l('The plugin.xml file failed to be extracted from the plugin package.');
					api()->context['status_class'] = 'red';
					api()->context['proceed'] = false;
				}
				else
				{
					// Now load the plugin information.
					$plugin_info = plugin_get_info($tmp_filename);

					// Alright, we will now check whether or not the plugin is
					// approved by SnowCMS.
					$status = plugin_check_status($filename, $reason);

					// Okay, now get the response!
					$response = admin_plugins_get_message($status, $plugin_info['name'], $reason, false);

					// Is it okay? Can we continue without prompting?
					$install_proceed = isset($_GET['proceed']) || $status == 'approved';
					api()->run_hooks('plugin_install_proceed', array(&$install_proceed, $status, 'plugin'));

					api()->context['status_message'] = $response['message'];
					api()->context['status_class'] = $response['div-class'];
					api()->context['proceed'] = $install_proceed;

					// Shall we proceed?
					if(!empty($install_proceed))
					{
						// We are almost there... Really! We are! We just need to check
						// the themes compatibility.
						api()->context['is_compatible'] = $plugin_info['is_compatible'];
						api()->context['compatible_is_error'] = false;

						// We will continue if it is compatible, if no compatibility was
						// supplied or if you choose to ignore the warning.
						if($plugin_info['is_compatible'] === true || $plugin_info['is_compatible'] === null || (isset($_GET['compat']) && $_GET['compat'] == 'ignore'))
						{
							if($plugin_info['is_compatible'] !== false)
							{
								api()->context['compatible_message'] = l('The plugin &quot;%s&quot; is compatible with your version of SnowCMS. Proceeding...', $plugin_info['name']);
							}
							else
							{
								api()->context['compatible_message'] = l('The plugin &quot;%s&quot; is not compatible with your version of SnowCMS. Proceeding with installation anyways...', $plugin_info['name']);
							}

							// Now just one last thing, extracting!
							// Let's get a nice name for the plugins directory, how about
							// the name of the theme itself?
							$name = sanitize_filename($plugin_info['name']);

							// But you may already have a plugin with the same name, so...
							if(file_exists(plugindir. '/'. $name))
							{
								$count = 1;
								while(file_exists(plugindir. '/'. $name. ' ('. $count. ')'))
								{
									// Keep going.
									$count++;
								}

								// Looks like we found a suitable match!
								$name .= ' ('. $count. ')';
							}

							// Now attempt to extract the package. Well, first try to make
							// the directory.
							if(!file_exists(plugindir. '/'. $name) && !@mkdir(plugindir. '/'. $name, 0755, true))
							{
								api()->context['extract_message'] = l('Please make sure the plugin directory is writable and try installing the plugin again.');
								api()->context['extract_is_error'] = true;

								unlink($tmp_filename);
							}
							// Ok, now try to extract it.
							elseif($extraction->extract($filename, plugindir. '/'. $name))
							{
								api()->context['extract_message'] = l('The plugin was successfully extracted. Proceeding...');

								// Now if the plugin is valid, which is should be since we
								// checked before, we can complete the installation.
								if(plugin_load(plugindir. '/'. $name) !== false)
								{
									// Execute install.php, if there is one.
									if(file_exists(plugindir. '/'. $name. '/install.php'))
									{
										// We will just include it.
										require(plugindir. '/'. $name. '/install.php');

										// Now delete it.
										unlink(plugindir. '/'. $name. '/install.php');
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
									recursive_unlink(plugindir. '/'. $name);
								}
							}
							else
							{
								// Hmm, something went wrong.
								api()->context['extract_message'] = l('The plugin package could not be extracted for an unknown reason.');
								api()->context['extract_is_error'] = true;

								unlink($tmp_filename);
								recursive_unlink(plugindir. '/'. $name);
							}
						}
						else
						{
							// Shucks! The theme author says it isn't compatible with your
							// current version of SnowCMS. Do you want to continue?
							api()->context['compatible_is_error'] = true;
							api()->context['install'] = htmlchars($_GET['install']);
							api()->context['compatible_message'] = l('The plugin &quot;%s&quot; is not compatible with your version of SnowCMS. You may continue with the installation anyways, if you choose.', $plugin_info['name']);

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

			theme()->render('admin_plugins_install');
		}
	}
}

if(!function_exists('admin_plugins_get_message'))
{
	/*
		Function: admin_plugins_get_message

		Parameters:
			string $status
			string $plugin_name
			string $reason
			bool $is_theme

		Returns:
			array
	*/
	function admin_plugins_get_message($status, $plugin_name, $reason = null, $is_theme = false)
	{
		$response = array(
									'color' => '',
									'message' => '',
									'div-class' => '',
								);

		// Is the package approved?
		if($status == 'approved')
		{
			$response['color'] = 'green';
			$response['message'] =  l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" has been reviewed and approved by the SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database.<br />Proceeding...', $plugin_name);
			$response['div-class'] = 'message-box';
		}
		// Disapproved?
		elseif($status == 'disapproved')
		{
			$response['color'] = '#DB2929';
			$response['message'] = l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" has been reviewed and disapproved by the SnowCMS Dev Team.<br />Reason: %s<br />Proceed at your own risk.', $plugin_name, !empty($reason) ? l($reason) : l('None given.'));
			$response['div-class'] = 'error-message';
		}
		// Deprecated? Pending..?
		elseif($status == 'deprecated' || $status == 'pending')
		{
			$response['color'] = '#1874CD';
			$response['message'] = ($status == 'deprecated' ? l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" is deprecated and a newer version is available at the <a href="http://'. (empty($is_theme) ? 'plugins' : 'themes'). '.snowcms.com/" target="_blank" title="SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database">SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database</a> site.<br />Proceed at your own risk.', $plugin_name) : l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" is currently under review by the SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database, so no definitive status can be given.<br />Proceed at your own risk.', $plugin_name));
			$response['div-class'] = 'alert-message';
		}
		elseif(in_array($status, array('unknown', 'malicious', 'insecure')))
		{
			if($status == 'unknown')
			{
				$response['message'] = l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" is unknown to the <a href="http://'. (empty($is_theme) ? 'plugins' : 'themes'). '.snowcms.com/" target="_blank" title="SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database">SnowCMS '. (empty($is_theme) ? 'Plugin' : 'Theme'). ' Database</a> site.<br />Proceed at your own risk.', $plugin_name);
			}
			elseif($status == 'malicious')
			{
				$response['message'] = l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" has been identified as malicious and it is not recommended you continue.<br />Reason: %s<br />Proceed at your own risk.', $plugin_name, !empty($reason) ? l($reason) : l('None given.'));
			}
			elseif($status == 'insecure')
			{
				$response['message'] = l('The '. (empty($is_theme) ? 'plugin' : 'theme'). ' "%s" has known security issues, it is recommended that you not continue.<br />Reason: %s<br />Proceed at your own risk.', $plugin_name, !empty($reason) ? l($reason) : l('None given.'));
			}

			$response['color'] = '#DB2929';
			$response['div-class'] = 'error-message';
		}
		else
		{
			api()->run_hooks('admin_plugins_handle_status', array(&$response, $plugin_name, &$reason, !empty($is_theme)));
		}

		return $response;
	}
}
?>