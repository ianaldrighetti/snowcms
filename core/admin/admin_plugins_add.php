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

// Title: Add Plugin

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

		// We may need to do a bit of cleanup in the plugin directory. There may
		// be some temporary files that don't need to be there anymore.
		if(empty($_SESSION['plugindir_cleaned']) || ((int)$_SESSION['plugindir_cleaned'] + 86400) < time_utc())
		{
			foreach(scandir(plugindir) as $filename)
			{
				// We don't want to delete any directories, files not ending with
				// .tmp, or a file that is newer than a few hours.
				if(is_dir(plugindir. '/'. $filename) || substr($filename, -4, 4) != '.tmp' || (filemtime(plugindir. '/'. $filename) + 10800) > time_utc())
				{
					continue;
				}

				@unlink(plugindir. '/'. $filename);
			}

			// Thanks for your help, but we won't have you do it again for awhile!
			$_SESSION['plugindir_cleaned'] = time_utc();
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

			// The Component class makes this a snap.
			$component = api()->load_class('Component');

			$result = $component->install($filename, 'plugin', array(
																													 'ignore_status' => isset($_GET['status']) && $_GET['status'] == 'ignore',
																													 'ignore_compatibility' => isset($_GET['compat']) && $_GET['compat'] == 'ignore',
																												 ));

			// Make our life even easier, please.
			foreach($result as $index => $value)
			{
				api()->context[$index] = $value;
			}

			// Should we delete that uploaded file?
			if(!empty($result['completed']))
			{
				unlink($filename);
			}

			// Set a couple of things.
			api()->context['install'] = htmlchars($_GET['install']);

			theme()->render('admin_plugins_install');
		}
	}
}
?>