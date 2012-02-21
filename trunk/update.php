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

/*
	Title: SnowCMS System Updater

	This file handles everything related to updating the system. This file is
	not accessed directly by a user, but AJAX requests are made to complete
	certain steps of the update process. In order for the step to be completed
	the user must supply a valid update key, which is contained within the
	update-key.php file in the base directory of the system.
*/

// Magic quotes, what a joke!!!
if(function_exists('set_magic_quotes_runtime'))
{
	@set_magic_quotes_runtime(0);
}

// All time/date stuff should be considered UTC, makes life easier!
if(function_exists('date_default_timezone_set'))
{
	date_default_timezone_set('UTC');
}
else
{
	@ini_set('date.timezone', 'UTC');
}

// We are currently in SnowCMS :)
define('INSNOW', true, true);

// We want to see those errors...
error_reporting(E_STRICT | E_ALL);

// Remove magic quotes, if it is on...
if((function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == 1) || @ini_get('magic_quotes_sybase'))
{
	$_COOKIE = remove_magic($_COOKIE);
	$_GET = remove_magic($_GET);
	$_POST = remove_magic($_POST);
	$_REQUEST = remove_magic($_REQUEST);
}

// We will need to load a few things, but not everything.
if(file_exists('config.php'))
{
	require(dirname(__FILE__). '/config.php');
}
else
{
	die('No config.php file found.');
}

// We need the database, at least some point in time. We won't connect until
// we need to (which will be done whenever db() is called for the first
// time).
require(coredir. '/database.php');

require(coredir. '/compat.php');

// We definitely need the API.
require(coredir. '/api.class.php');

api();

// Along with a few other things.
require(coredir. '/typecast.class.php');
require(coredir. '/settings.class.php');

// Load up the settings with the Settings class.
settings();

require(coredir. '/func.php');

// Initialize the $func array.
init_func();

require(coredir. '/clean_request.php');

// Everything we need is up and running. Let's get started!
call_user_func(update_func());

/*
	Function: update_func

	Returns a string containing the function which should be called according
	to the current request being made.

	Parameters:
		none

	Returns:
		string - The name of the function to execute.
*/
function update_func()
{
	// We should see about loading the update key.
	if(is_file(basedir. '/update-key.php'))
	{
		$fp = fopen(basedir. '/update-key.php', 'r');

		// Seek passed some stuff.
		fseek($fp, 13);

		// Now we can get the key.
		$update_key = fread($fp, filesize(basedir. '/update-key.php') - 13);

		fclose($fp);
	}

	// Make sure the key they are specifying matches the real one.
	if(empty($update_key) || empty($_REQUEST['update_key']) || strlen($update_key) < 10 || $update_key != $_REQUEST['update_key'])
	{
		// That's no good!
		echo json_encode(array(
											 'error_message' => 'Invalid update key supplied.',
										 ));

		exit;
	}

	$actions = array(
							 'checkcompat' => 'update_check_compat',
							 'download' => 'update_download',
							 'cancel' => 'update_cancel',
							 'verify' => 'update_verify',
							 'extract' => 'update_extract',
							 'copy' => 'update_copy',
							 'apply' => 'update_apply',
						 );

	if(empty($_GET['action']) || !isset($actions[$_GET['action']]))
	{
		echo json_encode(array(
											 'error_message' => 'No action to execute specified.',
										 ));

		exit;
	}

	// Return the right function so we can move along!
	return $actions[$_GET['action']];
}

/*
	Function: update_check_compat

	Checks the compatibility of all currently enabled plugins and themes to
	see if they will be compatible with the update version that is going to be
	installed.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function update_check_compat()
{
	// We will keep track of warnings here.
	$warnings = array(
								'plugins' => array(),
								'theme' => false,
							);

	// Fetch the version we're going to be updating too!
	$version = update_version_to(true);

	// Let's go ahead and get the list of plugins that are currently enabled.
	// The API class will have that information on hand.
	$check_updates = array();
	foreach(api()->return_plugins() as $plugin_guid)
	{
		// We will need to load up the plugins information.
		$plugin_info = plugin_load($plugin_guid, false);

		// Thankfully there isn't much checking we have to do ourselves!
		if(!empty($plugin_info['compatible_with']) && !is_compatible($plugin_info['compatible_with'], $version))
		{
			// Well, that's no good!
			$warnings['plugins'][basename($plugin_info['directory'])] = array(
																																		'name' => $plugin_info['name'],
																																		'current_version' => $plugin_info['version'],
																																		'update_version' => false,
																																	);
			$check_updates[] = $plugin_info['directory'];
		}
	}

	// Did we get any hits?
	if(count($check_updates) > 0)
	{
		// This file has the function we need to use.
		require_once(coredir. '/admin/admin_plugins_manage.php');

		$updates = admin_plugins_check_updates($check_updates);;

		// Did we find any updates?
		if($updates !== false && count($updates) > 0)
		{
			// We may need to turn this into an array if it isn't already.
			if(!is_array($updates))
			{
				$dirname = basename(array_pop($check_updates));
				$tmp = $updates;
				$updates = array(
										 $dirname => $tmp,
									 );
			}

			// We will want to mark that these plugins have an update available
			// within the control panel.
			$plugin_updates = settings()->get('plugin_updates', 'array', array());

			// Now we can add to the list of warnings.
			foreach($updates as $dirname => $update_version)
			{
				$warnings['plugins'][$dirname]['update_version'] = $update_version;
				$plugin_updates[$dirname] = $update_version;
			}

			settings()->set('plugin_updates', $plugin_updates);
		}
	}

	require_once(coredir. '/theme.php');

	// Now it is time to check the current theme...
	$theme_info = theme_load(themedir. '/'. settings()->get('theme', 'string'));

	// Make sure we got something, and that the current theme is not default,
	// as even if it isn't compatible with the update version, it will be
	// updated to be supported if needed.
	if($theme_info !== null && settings()->get('theme', 'string') !== 'default')
	{
		// Let's see if it is going to cause any issues.
		if(!empty($theme_info['compatible_with']) && !is_compatible($theme_info['compatible_with'], $version))
		{
			// We will definitely need to warn about this theme...
			$warnings['theme'] = array(
														 'name' => $theme_info['name'],
														 'current_version' => !empty($theme_info['version']) ? $theme_info['version'] : false,
														 'update_version' => false,
													 );

			// But we will only check for updates if this theme has specified a
			// current version, otherwise there is no way for us to know if there
			// is an update available (themes aren't required to specify versions
			// like plugins are).
			if(!empty($warnings['theme']['current_version']))
			{
				require_once(coredir. '/admin/admin_themes_manage.php');

				// This function will do all the work for us!
				$update_version = admin_themes_check_updates($theme_info['directory']);

				// Let's see, did we get a new version?
				if($update_version !== false)
				{
					$warnings['theme']['update_version'] = $update_version;

					// There is an update available, so mark it as such.
					$theme_updates = settings()->get('theme_updates', 'array', array());
					$theme_updates[dirname($theme_info['directory'])] = $update_version;
					settings()->set('theme_updates', $theme_updates);
				}
			}
		}
	}

	// Alrighty then... everything has been checked, and now we can output the
	// results. Well, almost. Let's remove the index names from the plugins
	// array first.
	$tmp = $warnings['plugins'];
	$warnings['plugins'] = array();
	foreach($tmp as $plugin)
	{
		$warnings['plugins'][] = $plugin;
	}

	echo json_encode($warnings);
	exit;
}

/*
	Function: update_version_to

	Returns the version that the system is in the process of updating to.

	Parameters:
		$bypass_file - Whether to save the result to a file.

	Returns:
		string - Returns a string containing the version the system is going to
						 update to.
*/
function update_version_to($bypass_file = false)
{
	static $update_version = null;

	// Do we need to fetch which version we're updating to?
	if(!is_file(basedir. '/update-to.php') || filemtime(basedir. '/update-to.php') + 900 < (time() - date('Z')))
	{
		require_once(coredir. '/admin/admin_update.php');

		$update_version = admin_latest_version();

		// We may not want to save it to a file (because creating the
		// update-to.php file will throw the site into maintenance mode).
		if($bypass_file === false)
		{
			// We will want to store it within a file.
			$fp = fopen(basedir. '/update-to.php', 'w');

			fwrite($fp, '<?php die; ?>'. $update_version);

			fclose($fp);
		}

		// There ya go!
		return $update_version;
	}
	elseif($update_version === null)
	{
		// No need to check again, we can get it from the file.
		$fp = fopen(basedir. '/update-to.php', 'r');

		// We will just move passed the die statement.
		fseek($fp, 13);

		$update_version = fread($fp, filesize(basedir. '/update-to.php') - 13);

		fclose($fp);

		// Now we're good to go!
		return $update_version;
	}
	else
	{
		// We don't need to fetch it or load it -- we already did.
		return $update_version;
	}
}

/*
	Function: update_download

	Downloads the update package to install.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function update_download()
{
	// Which version are we downloading?
	$version = update_version_to();

	// Let's construct the name of the file that we will download from the
	// SnowCMS update service. We would like to download a gzipped tarball,
	// but if the server doesn't support gzip, then we will have to do
	// without.
	$filename = $version. '.tar'. (function_exists('gzdeflate') ? '.gz' : '');

	// Not much more to do than to download it.
	$http = api()->load_class('HTTP');

	$response = $http->request(api()->apply_filters('admin_update_url', 'http://download.snowcms.com/updates/'. $filename), array(), 0, basedir. '/'. $filename);

	// Not much to say, either.
	echo json_encode($response);
	exit;
}

/*
	Function: update_cancel

	Cancels the current update process by removing any files created during
	the current process.

	Parameters:
		bool $silent - Whether the function should display true and exit after
									 this function is called.

	Returns:
		void - Nothing is returned by this function.
*/
function update_cancel($silent = false)
{
	// We definitely need to delete the update-key.php and update-to.php
	// files. But first we should see if there is any downloaded file that
	// needs to be removed.
	$version = update_version_to();

	if(file_exists(basedir. '/'. $version. '.tar'. (function_exists('gzdeflate') ? '.gz' : '')))
	{
		// We'll go ahead and remove that.
		@unlink(basedir. '/'. $version. '.tar'. (function_exists('gzdeflate') ? '.gz' : ''));
	}

	// Do we need to remove the temporary directory where we extracted
	// everything from the update package into?
	if(file_exists(coredir. '/~tmp/'))
	{
		recursive_unlink(coredir. '/~tmp/');
	}

	// Now for those other two.
	@unlink(basedir. '/update-key.php');
	@unlink(basedir. '/update-to.php');
	@unlink(basedir. '/package-info.dat');

	if(empty($silent))
	{
		echo json_encode(true);
		exit;
	}
}

/*
	Function: update_verify

	This function will verify the contents of the downloaded update package
	by making sure that the checksum of the downloaded file matches the
	checksum retrieved from the SnowCMS update service and also makes sure
	that the package can be extracted.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function update_verify()
{
	$response = array(
								'verified' => false,
								'error_code' => 0,
							);
	$version = update_version_to();

	// We will need to reference this a few times.
	$filename = $version. '.tar'. (function_exists('gzdeflate') ? '.gz' : '');

	// Why don't we go ahead and see if we can download the checksum file.
	$http = api()->load_class('HTTP');
	$checksum = trim($http->request(api()->apply_filters('admin_update_checksum_url', 'http://download.snowcms.com/updates/'. $filename. '.chksum')));

	// Let's make sure the checksum is valid, it should be 40 characters long.
	if(empty($checksum) || strlen($checksum) != 40)
	{
		$response['error_code'] = 1;

		echo json_encode($response);
		exit;
	}

	// Now we will check the integrity of the downloaded package.
	if(sha1_file(basedir. '/'. $filename) != $checksum)
	{
		$response['error_code'] = 2;

		echo json_encode($response);
		exit;
	}

	// Just one last check and we will be good to go... We need to make sure
	// that the file we downloaded can be extracted.
	$extraction = api()->load_class('Extraction');

	// The is_supported method will do its best to determine whether the
	// update package can be extracted.
	if(!$extraction->is_supported(basedir. '/'. $filename))
	{
		$response['error_code'] = 3;

		echo json_encode($response);
		exit;
	}

	// It passed all the tests. So we're good to go!
	$response['verified'] = true;

	echo json_encode($response);
	exit;
}

/*
	Method: update_extract

	Extracts the update from its compressed package into a temporary location
	before the files are copied to their new location and any update commands
	are executed.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function update_extract()
{
	// Load up and set a few things.
	$version = update_version_to();
	$filename = $version. '.tar'. (function_exists('gzdeflate') ? '.gz' : '');
	$extraction = api()->load_class('Extraction');
	$response = array(
								'extracted' => false,
								'error_code' => 0,
							);

	// Make sure we can write to where we need to!
	if(!is_writable(basedir) || !is_writable(coredir) || !is_writable(coredir. '/admin') || !is_writable(themedir))
	{
		$response['error_code'] = 1;

		echo json_encode($response);
		exit;
	}

	// First we will need to create a file that we can write all the files to,
	// we do this as an intermediary step just in case there are some big
	// files which need to be extracted which could end up causing a timeout.
	$fp = fopen(basedir. '/package-info.dat', 'wb');

	flock($fp, LOCK_EX);

	// But we need to make sure we were able to do so.
	if(empty($fp))
	{
		$response['error_code'] = 2;

		echo json_encode($response);
		exit;
	}

	// Let's go ahead and extract the package.
	$files = $extraction->files(basedir. '/'. $filename);
	if($files === false)
	{
		$response['error_code'] = 3;

		echo json_encode($response);
		exit;
	}

	// Looks like we're good to go!
	// We will want to move through this all as quickly as possible, so we
	// will make a little (not exactly) index.
	fwrite($fp, pack('V', count($files)));

	// The start position won't be 0, not exactly. The first 4 bytes contains
	// an unsigned integer telling us how many files there are, then there are
	// {n} 32-bit unsigned integers.
	$position = (count($files) * 4) + 4;
	foreach($files as $file)
	{
		// Save the current position within the file.
		fwrite($fp, pack('V', $position));

		// The next file will start after this:
		// (as a note, there will be a prepended character [f or d] to indicate
		// whether the name is a file or directory, and before that there is a
		// 16-bit unsigned integer which tells us how long the name is)
		$position += strlen($file['name']) + 3;
	}

	// Now for the information itself.
	foreach($files as $file)
	{
		fwrite($fp, pack('v', strlen($file['name'])). ($file['is_dir'] ? 'd' : 'f'). $file['name']);
	}

	// Unlock the file, close it, and then we're done with this step.
	flock($fp, LOCK_UN);
	fclose($fp);

	$response['extracted'] = true;

	echo json_encode($response);
	exit;
}

/*
	Function: update_copy

	Actually extracts the files from the package to their new "homes." This
	function may be called repeatedly in order to prevent any sort of timing
	out of the PHP process.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function update_copy()
{
	$response = array(
								'finished' => false,
								'percent_finished' => 0,
								'offset' => 0,
								'value' => '',
							);

	// So, where are we starting?
	$offset = isset($_POST['start']) && (int)$_POST['start'] > 0 ? (int)$_POST['start'] : 0;

	// We will need the Extraction class.
	$extraction = api()->load_class('Extraction');

	$version = update_version_to();
	$package_name = $version. '.tar'. (function_exists('gzdeflate') ? '.gz' : '');

	// Then, before we can get going, we will need to open the file containing
	// the files within the package.
	$fp = fopen(basedir. '/package-info.dat', 'rb');
	flock($fp, LOCK_SH);

	$tmp = unpack('Vcount', fread($fp, 4));
	$file_count = $tmp['count'];

	// Let's make sure that we haven't finished everything, though.
	if($offset >= $file_count)
	{
		// We're done!
		$response['finished'] = true;
		$response['percent_finished'] = 100;
		$response['offset'] = $file_count - 1;

		echo json_encode($response);
		exit;
	}

	// How many do we have left?
	$left = $file_count - $offset;
	for($i = 0; $i < ($left > 5 ? 5 : $left); $i++)
	{
		// Move to the right place.
		fseek($fp, 4 + (($offset + $i) * 4));

		// Now read the 4 bytes which will tell us where we need to move next.
		$tmp = unpack('Vposition', fread($fp, 4));

		fseek($fp, $tmp['position']);

		// The first two bytes will tell us how long the file name is (not
		// including the single character we prepended to that).
		$tmp = unpack('vlength', fread($fp, 2));
		$length = $tmp['length'];

		// So, a directory or file?
		$is_dir = fread($fp, 1) == 'd';

		// Now for the files name.
		$filename = fread($fp, $length);

		// We can't extract an entire directory from the package, so we will
		// just create the directory, if need be.
		if($is_dir)
		{
			if(!is_dir(basedir. '/'. $filename))
			{
				@mkdir(basedir. '/'. $filename, 0775, true);
			}
		}
		// But we can extract the file itself.
		else
		{
			$extraction->read(basedir. '/'. $package_name, $filename, basedir. '/'. $filename);
		}
	}

	// Increment the offset accordingly.
	$offset += $left > 5 ? 5 : $left;

	// How far did we get?
	$response['percent_finished'] = round((($offset + 1) / (double)$file_count) * 100, 2);
	$response['offset'] = $offset;

	// I guess we should make one last check to make sure we haven't finished
	// yet. No need to have them make a request just to verify that.
	if($offset >= $file_count)
	{
		$response['finished'] = true;
		$response['percent_finished'] = 100;
		$response['offset'] = $file_count - 1;
	}

	echo json_encode($response);
	exit;
}

/*
	Function: update_apply

	This function will execute any PHP files which need to be ran in order to
	complete the update process. Once the proper files have been executed (if
	any), then the cleanup will begin by removing files only required during
	the update process. Also, the system will check for updates again -- just
	in case.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function update_apply()
{
	// Is there any state data?
	$GLOBALS['state'] = isset($_REQUEST['state']) ? $_REQUEST['state'] : null;

	// The files we will run (well, the file) should mark the following as
	// true if it is done.
	$GLOBALS['finished'] = false;

	// If you want, you can give a status as well.
	$GLOBALS['percent_finished'] = false;

	// The system-update.php file in the core directory will be what we want
	// to execute, if it exists.
	if(is_file(coredir. '/system-update.php'))
	{
		require(coredir. '/system-update.php');

		// You done?
		if(!empty($GLOBALS['finished']))
		{
			// Then we should remove this.
			@unlink(coredir. '/system-update.php');
		}
	}
	else
	{
		// Well, there is no file which can say we're done, so we will do that
		// ourselves.
		$GLOBALS['finished'] = true;
	}

	// Do we need to finish up?
	if(!empty($GLOBALS['finished']))
	{
		// The update_cancel function accomplishes what we want to do.
		update_cancel(true);

		// Now, check for updates!!!
		require_once(coredir. '/admin/admin_update.php');

		admin_update_check(true);
	}

	echo json_encode(array(
										 'finished' => $GLOBALS['finished'],
										 'percent_finished' => $GLOBALS['percent_finished'],
										 'state' => $GLOBALS['state'],
									 ));
	exit;

}

/*
	Function: time_utc
*/
function time_utc()
{
	return time() - date('Z');
}
?>