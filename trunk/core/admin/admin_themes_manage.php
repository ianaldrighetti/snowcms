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

// Title: Manage Themes

if(!function_exists('admin_themes_manage'))
{
	/*
		Function: admin_themes_manage

		Provides the interface for managing themes which are currently
		installed, such as allowing a theme to be selected, updated or removed.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_themes_manage()
	{
		api()->run_hooks('admin_themes_manage');

		// The user is allowed to access this section if they have the
		// select_theme or manage_themes permission.
		if(!member()->can('select_theme') && !member()->can('manage_themes'))
		{
			admin_access_denied();
		}

		// Do they want to change the current theme, or maybe delete one?
		if(!empty($_GET['set']) || !empty($_GET['delete']))
		{
			verify_request('get');

			if(!empty($_GET['set']))
			{
				// It's not rocket science to change the current theme ;-).
				$new_theme = basename($_GET['set']);

				// But first we will want to make sure that the theme exists.
				if(is_dir(themedir. '/'. $new_theme) && theme_load(themedir. '/'. $new_theme) !== false)
				{
					settings()->set('theme', $new_theme);
				}
			}
			else
			{
				// Not much harder to delete a theme, either.
				$delete_theme = basename($_GET['delete']);

				// We won't allow you to delete the current theme, otherwise things
				// could get a bit nasty!
				if(settings()->get('theme', 'string', 'default') != $default_theme && theme_load(themedir. '/'. $new_theme) !== false)
				{
					// We don't store any information about themes anywhere, so we
					// simply remove the files themselves to delete it.
					recursive_unlink(themedir. '/'. $delete_theme);
				}
			}

			redirect(baseurl('index.php?action=admin&sa=themes&section=manage'));
		}

		// Now we'll generate an array containing all the currently installed
		// themes, along with adding any information we need, such as whether
		// there is an update available for the theme.
		$theme_updates = settings()->get('theme_updates', 'array', array());
		$current = 1;
		api()->context['theme_list'] = array();
		foreach(theme_list() as $theme_id)
		{
			$theme_info = theme_load($theme_id);

			$theme = array(
								 'anchor' => 't'. sha1(basename($theme_info['directory'])),
								 'name' => $theme_info['name'],
								 'is_compatible' => $theme_info['is_compatible'] === true || $theme_info['is_compatible'] === null,
								 'description' => $theme_info['description'],
								 'author' => (!empty($theme_info['website']) ? '<a href="'. $theme_info['website']. '" target="_blank">' : ''). $theme_info['author']. (!empty($theme_info['website']) ? '</a>' : ''),
								 'version' => $theme_info['version'],
								 'select_href' => baseurl('index.php?action=admin&amp;sa=themes&amp;set='. urlencode(basename($theme_info['path'])). '&amp;sid='. member()->session_id()),
								 'delete_href' => baseurl('index.php?action=admin&amp;sa=themes&amp;delete='. urlencode(basename($theme_info['path'])). '&amp;sid='. member()->session_id()),
								 'image_url' => themeurl. '/'. basename($theme_info['path']). '/image.png',
								 'update_available' => isset($theme_updates[basename($theme_info['path'])]) && compare_versions($theme_updates[basename($theme_info['path'])], $theme_info['version'], '>'),
								 'update_version' => false,
								 'update_href' => false,
								 'new_row' => $current % 3 == 0,
								 'is_last' => false,
							 );

			// Does this theme have an update available?
			if($theme['update_available'])
			{
				$theme['update_href'] = baseurl('index.php?action=admin&sa=themes&update='. urlencode(basename($theme_info['path'])). '&amp;sid='. member()->session_id());
				$theme['update_version'] = $theme_updates[basename($theme_info['path'])];
			}

			// If this is the current theme, we won't add it to the list of themes
			// because it gets to be displayed all by itself.
			if(basename($theme_info['path']) == settings()->get('theme', 'string', 'default'))
			{
				api()->context['current_theme'] = $theme;
			}
			else
			{
				api()->context['theme_list'][] = $theme;

				$current++;
			}
		}

		// If a new row was requested on the last item, we will mark it as
		// false, as we don't need any more rows because it is the last theme in
		// the list anyways.
		if($current > 1)
		{
			api()->context['theme_list'][$current - 2]['new_row'] = false;
			api()->context['theme_list'][$current - 2]['is_last'] = true;
		}

		// Now, how wide does the table need to be?
		api()->context['table_width'] = $current == 2 ? '33%' : ($current == 3 ? '66%' : '100%');

		// We're all done with loading the currently installed themes, so now
		// just get ready to display everything.
		admin_current_area('manage_manage_themes');

		theme()->set_title(l('Manage Themes'));

		theme()->render('admin_themes_manage');
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
		if(!member()->can('select_theme') && !member()->can('manage_themes'))
		{
			admin_access_denied();
		}

		admin_current_area('manage_manage_themes');

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