<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
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

session_start();

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
define('IN_SNOW', true, true);

// We want to see those errors...
error_reporting(E_STRICT | E_ALL);

// Remove magic quotes, if it is on...
if((function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == 1) || @ini_get('magic_quotes_sybase'))
{
  $_COOKIE = remove_magic($_COOKIE);
  $_GET = remove_magic($_GET);
  $_POST = remove_magic($_POST);
}

function remove_magic($array, $depth = 5)
{
  // Nothing in the array? No need!
  if(count($array) == 0)
  {
    return array();
  }
  // Exceeded our maximum depth? Just return the array, untouched.
  elseif($depth <= 0)
  {
    return $array;
  }

  foreach($array as $key => $value)
  {
    // Gotta remember that the key needs to have magic quote crud removed
    // as well!
    if(!is_array($value))
    {
      $array[stripslashes($key)] = stripslashes($value);
    }
    else
    {
      $array[stripslashes($key)] = remove_magic($value, $depth - 1);
    }
  }

  return $array;
}

// Alright, let's do this thing!
call_user_func(setup_return_func());

/*
	Function: setup_return_func

	Returns the function that ought to be called...

	Parameters:
		none

	Returns:
		string
*/
function setup_return_func()
{
	// We need to check system requirements. If you aren't running PHP 5.0,
	// have the XML Parser extension enabled, or MySQL, then you can't run
	// SnowCMS... Sorry! Oh, and this directory needs to be writeable.
	if(!is_writable(dirname(__FILE__)) || !version_compare(phpversion(), '5.0.0', '>=') || !function_exists('xml_parser_create') || !function_exists('mysql_connect'))
	{
		return 'setup_requirement_fail';
	}

	// What step do you claim you are on?
	$step = !empty($_GET['step']) && (int)$_GET['step'] > 0 && (int)$_GET['step'] < 4 ? (int)$_GET['step'] : 1;

	// Which are you really on?
	if(empty($_SESSION['step']) || $_SESSION['step'] != $step)
	{
		$step = 1;
	}

	// Just a simple session identifier.
	if(empty($_SESSION['id']))
	{
		$_SESSION['id'] = sha1(mt_rand(1, 9999) + str_shuffle('My favorite color is blue... Really. No joke.'));
	}

	// This is easy enough.
	return 'setup_step_'. $step;
}

/*
	Function: setup_requirement_fail

	Parameters:
		none

	Returns:
		void
*/
function setup_requirement_fail()
{
	template_header();

	echo '
			<h1>System Requirements Failure</h1>
			<p>Sorry, but it appears that your server does not meet the requirements in order to run SnowCMS. Please read the following for information as to why your server failed this test.</p>
			<br />
			<p>Checking if directory is writable... ', (is_writable(dirname(__FILE__)) ? '<span class="green bold">Writable</span>' : '<span class="red bold">Not Writable</span>'), '</p>
			<p>Checking for at least PHP version 5... ', (version_compare(phpversion(), '5.0.0', '>=') ? '<span class="green bold">OK</span>' : '<span class="red bold">FAIL</span>'), ' (running v', phpversion(), ')</p>
			<p>Checking for <a href="http://www.php.net/manual/en/book.xml.php" target="_blank">XML Parser</a> extension... ', (function_exists('xml_parser_create') ? '<span class="green bold">Enabled</span>' : '<span class="red bold">Not Enabled</span>'), '</p>
			<p>Checking for at MySQL extension... ', (function_exists('mysql_connect') ? '<span class="green bold">Enabled</span>' : '<span class="red bold">Not Enabled</span>'), '</p>
			<br />
			<p>Once these issues are resolved, simply refresh this page to check again.</p>';

	template_footer();
}

/*
	Function: setup_step_1

	Displays and processes everything that occurs within the first step of
	the installation process.

	Parameters:
		none

	Returns:
		void
*/
function setup_step_1()
{
	if(!empty($_POST['proc_step_1']))
	{
		$error_msg = array();

		$db_host = !empty($_POST['db_host']) ? $_POST['db_host'] : '';
		$db_user = !empty($_POST['db_user']) ? $_POST['db_user'] : '';
		$db_pass = !empty($_POST['db_pass']) ? $_POST['db_pass'] : '';
		$db_name = !empty($_POST['db_name']) ? $_POST['db_name'] : '';
		$tbl_prefix = !empty($_POST['tbl_prefix']) ? $_POST['tbl_prefix'] : '';

		// Make sure the session id's match.
		if(empty($_POST['session_id']) || $_POST['session_id'] != $_SESSION['id'])
		{
			$error_msg[] = 'Session verification failed. Please try again.';
		}

		if(count($error_msg) == 0)
		{
			$connection = @mysql_connect($db_host, $db_user, $db_pass);

			// So, did it work?
			if(!empty($connection))
			{
				$selected_db = @mysql_select_db($db_name, $connection);

				// Were we able to select the database?
				if(!empty($selected_db))
				{
					// Sweet! Now to save the configuration file. Hopefully...
					// Shouldn't be a problem, seeing as the directory *ought* to be
					// writable if you are on step #1.

					if(generate_config($db_host, $db_user, $db_pass, $db_name, $tbl_prefix, $error_msg))
					{
						// You're on to step 2!
						$_SESSION['step'] = 2;

						// Let's redirect you there...
						header('HTTP/1.1 Temporary Redirect');
						header('Location: setup.php?step=2');

						exit;
					}
				}
				else
				{
					// No, maybe you didn't assign the right permissions :-/.
					$error_msg[] = 'MySQL Error: ['. mysql_errno(). '] '. mysql_error();
				}
			}
			else
			{
				// Shoot, it didn't!
				$error_msg[] = 'MySQL Error: ['. mysql_errno(). '] '. mysql_error();
			}
		}
	}

	template_header(1);

	echo '
			<h1>Let&#039;s Get Started!</h1>
			<p>Simply enter your MySQL credentials below, then click <em>Proceed</em>.</p>';

	if(!empty($error_msg) && count($error_msg) > 0)
	{
		echo '
			<div class="error-message">';

		foreach($error_msg as $e_message)
		{
			echo '
				<p>', $e_message, '</p>';
		}

		echo '
			</div>';
	}

	echo '
			<form action="setup.php" method="post">
				<table width="60%" style="margin: auto;">
					<tr>
						<td class="label" valign="middle">MySQL host:</td>
						<td><input type="text" name="db_host" value="', isset($_POST['db_host']) ? htmlspecialchars($_POST['db_host'], ENT_QUOTES) : 'localhost', '" /></td>
					</tr>
					<tr>
						<td class="label" valign="middle">MySQL user:</td>
						<td><input type="text" name="db_user" value="', !empty($_POST['db_user']) ? htmlspecialchars($_POST['db_user'], ENT_QUOTES) : '', '" /></td>
					</tr>
					<tr>
						<td class="label" valign="middle">MySQL password:</td>
						<td><input type="password" name="db_pass" value="" /></td>
					</tr>
					<tr>
						<td class="label" valign="middle">MySQL database:</td>
						<td><input type="text" name="db_name" value="', !empty($_POST['db_name']) ? htmlspecialchars($_POST['db_name'], ENT_QUOTES) : '', '" /></td>
					</tr>
					<tr>
						<td class="label" valign="middle">Table prefix:</td>
						<td><input type="text" name="tbl_prefix" value="', !empty($_POST['tbl_prefix']) ? htmlspecialchars($_POST['tbl_prefix'], ENT_QUOTES) : 'snow_', '" /></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: right; padding: 5px 0;"><input type="submit" name="proc_step_1" value="Proceed &raquo;" /></td>
					</tr>
				</table>
				<input name="session_id" type="hidden" value="', $_SESSION['id'], '" />
			</form>';

	template_footer();
}

/*
	Function: generate_config

	Generates the config.php file with the supplied information.

	Parameters:
		string $db_host
		string $db_user
		string $db_pass
		string $db_name
		string $tbl_prefix
		array &$error_msg

	Returns:
		bool
*/
function generate_config($db_host, $db_user, $db_pass, $db_name, $tbl_prefix, &$error_msg)
{
	$bytes = file_put_contents(dirname(__FILE__). '/config.php',
	'<?php
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

# No direct access!!!
if(!defined(\'IN_SNOW\'))
  die;

#
# config.php holds all your database information and paths.
#

# Database settings:
$db_type = \'mysql\'; # Your database type, an example would be mysql, sqlite or postgresql
$db_host = \''. addcslashes($db_host, '\''). '\'; # The location of your database, could be localhost or a path (for SQLite)
$db_user = \''. addcslashes($db_user, '\''). '\'; # The user that has access to your database, though not all database systems have this.
$db_pass = \''. addcslashes($db_pass, '\''). '\'; # The password to your database user.
$db_name = \''. addcslashes($db_name, '\''). '\'; # The name of the database.
$db_persist = false; # Whether or not to have a persistent connection to the database.
$db_debug = false; # Enable database debugging? (Outputs queries into a file ;))
$tbl_prefix = \''. addcslashes($tbl_prefix, '\''). '\'; # The prefix of the tables, allows multiple installs on the same database.

# The location of your root directory of your SnowCMS installation.
$base_dir = defined(\'__DIR__\') ? __DIR__ : dirname(__FILE__);

# Some other useful paths...
$core_dir = $base_dir. \'/core\';
$theme_dir = $base_dir. \'/themes\';
$plugin_dir = $base_dir. \'/plugins\';
$upload_dir = $base_dir. \'/uploads\';

# The address of where your SnowCMS install is accessible (No trailing /!)
$base_url = \'http://'. $_SERVER['HTTP_HOST']. dirname($_SERVER['REQUEST_URI']). '\';
$theme_url = $base_url. \'/themes\';
$plugin_url = $base_url. \'/plugins\';

# What do you want to be the name of the cookie?
$cookie_name = \'SCMS'. mt_rand(100, 999). '\';
?>');

	// Do we need to add an error message?
	if((int)$bytes == 0)
	{
		$error_msg[] = 'Failed to create the configuration file. Make sure the directory is writable.';
	}

	return (int)$bytes > 0;
}

/*
	Function: setup_step_2

	Displays and processes everything which needs to occur during step #2.

	Parameters:
		none

	Returns:
		void
*/
function setup_step_2()
{
	if(!empty($_POST['proc_step_2']))
	{
		$error_msg = array();

		$member_name = !empty($_POST['member_name']) ? trim($_POST['member_name']) : '';
		$member_pass = !empty($_POST['member_pass']) ? $_POST['member_pass'] : '';
		$verify_pass = !empty($_POST['verify_pass']) ? $_POST['verify_pass'] : '';
		$member_email = !empty($_POST['member_email']) ? $_POST['member_email'] : '';

		// Make sure they pass session verification.
		if(empty($_POST['session_id']) || $_POST['session_id'] != $_SESSION['id'])
		{
			$error_msg[] = 'Session verification failed. Please try again.';
		}

		// No empty username!
		if(strlen($member_name) == 0)
		{
			$error_msg[] = 'Please enter a username.';
		}

		// At least a 6 character long password, please.
		if(strlen($member_pass) < 6)
		{
			$error_msg[] = 'Please use a password that is more than 6 characters long.';
		}
		elseif($verify_pass != $member_pass)
		{
			$error_msg[] = 'Your passwords do not match.';
		}

		// We won't validate it, but enter SOMETHING!
		if(empty($member_email))
		{
			$error_msg[] = 'Please enter an email address.';
		}

		if(count($error_msg) == 0)
		{
			// Finalize the installation process :-)
			if(setup_finalize($member_name, $member_pass, $member_email, $error_msg))
			{
				// Awesome, you're done!
				$_SESSION['step'] = 3;

				header('HTTP/1.1 Temporary Redirect');
				header('Location: setup.php?step=3');

				exit;
			}
		}
	}

	template_header(2);

	echo '
			<h1>Create an Account</h1>
			<p>Alrighty, let&#039;s get you an administrative account created. We&#039;re just about done!</p>';

	if(!empty($error_msg) && count($error_msg) > 0)
	{
		echo '
			<div class="error-message">';

		foreach($error_msg as $e_message)
		{
			echo '
				<p>', $e_message, '</p>';
		}

		echo '
			</div>';
	}

	echo '
			<form action="setup.php?step=2" method="post">
				<table width="50%" style="margin: auto;">
					<tr>
						<td class="label">Username:</td>
						<td><input type="text" name="member_name" value="', !empty($_POST['member_name']) ? htmlspecialchars($_POST['member_name'], ENT_QUOTES) : '', '" /></td>
					</tr>
					<tr>
						<td class="label">Password:</td>
						<td><input type="password" name="member_pass" value="" /></td>
					</tr>
					<tr>
						<td class="label">Password: <sub>Just to be sure...</sub></td>
						<td><input type="password" name="verify_pass" value="" /></td>
					</tr>
					<tr>
						<td class="label">Email:</td>
						<td><input type="text" name="member_email" value="', !empty($_POST['member_email']) ? htmlspecialchars($_POST['member_email'], ENT_QUOTES) : '', '" /></td>
					<tr>
						<td colspan="2" style="text-align: right; padding: 5px 0;"><input type="submit" name="proc_step_2" value="Proceed &raquo;" /></td>
					</tr>
				</table>
				<input type="hidden" name="session_id" value="', $_SESSION['id'], '" />
			</form>';

	template_footer();
}

/*
	Function: setup_finalize

	Parameters:
		string $member_name
		string $member_pass
		array &$error_msg

	Returns:
		bool
*/
function setup_finalize($member_name, $member_pass, $member_email, &$error_msg)
{
	// Get the configuration file. We will need that!
	if(!file_exists(dirname(__FILE__). '/config.php'))
	{
		$error_msg[] = 'Could not find the configuration file. Please restart the installation process.';

		return false;
	}

	require_once(dirname(__FILE__). '/config.php');

	$connection = @mysql_connect($db_host, $db_user, $db_pass);

	// This shouldn't happen, but hey, you never know!
	if(empty($connection))
	{
		$error_msg[] = 'MySQL Error: ['. mysql_errno(). '] '. mysql_error();

		return false;
	}

	$selected_db = @mysql_select_db($db_name, $connection);

	// This shouldn't happen either, but whatever.
	if(empty($selected_db))
	{
		$error_msg[] = 'MySQL Error: ['. mysql_errno(). '] '. mysql_error();

		return false;
	}

	// Get the setup SQL... If we can.
	if(!file_exists(dirname(__FILE__). '/setup-mysql.sql'))
	{
		$error_msg[] = 'Could not find setup-mysql.sql, which is required for installation.';

		return false;
	}

	$lines = explode("\r\n", file_get_contents(dirname(__FILE__). '/setup-mysql.sql'));

	$commands = array();
	foreach($lines as $line)
	{
		if(substr(ltrim($line), 0, 1) == '#' || substr(ltrim($line), 0, 2) == '--')
		{
			// Skip this, it's a comment.
			continue;
		}

		$commands[] = $line;
	}

	$commands = explode(';', str_replace('{db->prefix}', $tbl_prefix, implode('', $commands)));

	// Start a transaction.
	mysql_query('SET autocommit = 0');
	mysql_query('START TRANSACTION');

	foreach($commands as $command)
	{
		if(strlen(trim($command)) == 0)
		{
			continue;
		}

		// If anything bad happens, we will quit.
		if(!mysql_query($command))
		{
			// Oh noes! Something bad happened!
			$error_msg[] = 'MySQL Error: ['. mysql_errno(). '] '. mysql_error();

			// Rollback! Quick!
			mysql_query('ROLLBACK');

			// Quit!
			return false;
		}
	}

	// Seems to be all good.
	mysql_query('COMMIT');

	// Let's create your account now, shall we?
	mysql_query('
		INSERT INTO `'. $tbl_prefix. 'members`
		(`member_name`, `member_pass`, `member_hash`, `member_email`, `display_name`, `member_groups`, `member_registered`, `member_activated`)
		VALUES(\''. mysql_real_escape_string(htmlspecialchars($member_name, ENT_QUOTES)). '\', \''. mysql_real_escape_string(sha1(strtolower($member_name). $member_pass)). '\', \''. mysql_real_escape_string(rand_str(16)). '\', \''. mysql_real_escape_string(htmlspecialchars($member_email, ENT_QUOTES)). '\', \''. mysql_real_escape_string(htmlspecialchars($member_name, ENT_QUOTES)). '\', \'administrator\', \''. time(). '\', 1)');

	return true;
}

/*
	Function: rand_str

	Generates a random string, randomly... Of course!

	Parameters:
		int $length

	Returns:
		void

	Note:
		Well, it's as random as a random number can be... Well, what I could
		think of anyway. :-P
*/
function rand_str($length = 0)
{
	if(empty($length) || $length < 1)
	{
		$length = mt_rand(1, 100);
	}

	$chars = array(
						 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
						 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
						 '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '~', '!', '@', '#', '$', '%', '^', '*', '-', '_', '+', '=', '?',
					 );

	$str = '';
	for($i = 0; $i < $length; $i++)
	{
		$str .= $chars[array_rand($chars)];
	}

	return $str;
}

/*
	Function: setup_step_3

	Finishes the installation process by, well, deleting stuff!

	Parameters:
		none

	Returns:
		void
*/
function setup_step_3()
{
	// Delete some stuff...
	unlink(__FILE__);
	unlink(dirname(__FILE__). '/setup-mysql.sql');
	unlink(dirname(__FILE__). '/setup-sqlite.sql');

	require_once(dirname(__FILE__). '/config.php');

	template_header(3);

	echo '
			<h1>You&#039;re Ready to Go!</h1>
			<p>You&#039;re <a href="', $base_url, '">new site is all set</a> and ready to go!</p>

			<p>Thanks again for choosing SnowCMS for your content management needs. If you need any help, check out <a href="http://www.snowcms.com/" target="_blank">www.snowcms.com</a>.</p>';

	template_footer();
}

/*
	Function: template_header

	Parameters:
		int $step

	Returns:
		void
*/
function template_header($step = 0)
{
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex" />
	<title>SnowCMS Installer</title>
	<style style="text/css">
		body
		{
			font-family: Verdana, Tahoma, Arial, sans-serif;
			font-size: 13px;
			background: #F5F5F5;
		}

		#container
		{
			width: 600px;
			margin: auto;
			background: white;
			border: 1px solid #CCCCCC;
		}

		#current-step
		{
			padding: 0 10px;
			border-bottom: 1px solid #CCCCCC;
		}

		#current-step .header
		{
			font-size: 32px;
			font-family: Georgia;
			color: #CCCCCC;
		}

		#current-step .current
		{
			color: black !important;
		}

		#current-step sub
		{
			font-family: Verdana, Tahoma, Arial, sans-serif !important;
			font-size: 13px !important;
		}

		#content
		{
			padding: 0 10px;
		}

		#content h1
		{
			font-family: Georgia;
			font-size: 24px;
			font-weight: normal;
		}

		#content form .label
		{
			font-size: 15px;
		}

		#content form input[type="text"], #content form input[type="password"]
		{
			font-family: Verdana, Tahoma, Arial, sans-serif;
			font-size: 15px;
			padding: 3px;
		}

		#content form input[type="submit"]
		{
			font-family: Verdana, Tahoma, Arial, sans-serif;
		}

		#footer
		{
			border-top: 1px solid #CCCCCC;
			padding: 10px;
			text-align: center;
		}

		#footer p
		{
			margin: 0;
			padding: 0;
		}

		.green
		{
			color: green !important;
		}

		.red
		{
			color: red !important;
		}

		.bold
		{
			font-weight: bold !important;
		}

		.error-message
		{
			margin: 10px 0;
			padding: 10px 0 5px 0;
			background: #FFCCCC;
			border: 1px solid #B22222;
			text-align: center;
		}
	</style>
</head>
<body>
	<div id="container">
		<div id="current-step">
			<p><span class="header', $step == 1 ? ' current' : '', '">Step 1</span> <sub>Configure</sub> <span class="header current">&gt;</span> <span class="header', $step == 2 ? ' current' : '', '">Step 2</span> <sub>Create Account</sub> <span class="header current">&gt;</span> <span class="header', $step == 3 ? ' current' : '', '">Finished</span> <sub>Enjoy!</sub></p>
		</div>
		<div id="content">';
}

/*
	Function: template_footer

	Parameters:
		none

	Returns:
		void
*/
function template_footer()
{
	echo '
		</div>
		<div id="footer">
			<p>Thank you for choosing <a href="http://www.snowcms.com/" target="_blank">SnowCMS</a> for your content management solution!</p>
		</div>
	</div>
</body>
</html>';
}
?>