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
	Title: Clean Request

	Simply cleans the GLOBALS array in PHP of unwanted variables, such as
	old and deprecated variables (such as HTTP_GET_VARS), but it also
	removes what PHP's magic quotes
	<http://www.php.net/manual/en/security.magicquotes.php> did to variables
	like $_GET, $_POST, $_REQUEST, etc.
*/

if(!function_exists('clean_request'))
{
	/*
		Function: clean_request

		Removes any affects magic quotes has on $_COOKIE, $_GET, $_POST or
		$_REQUEST. It also removes the $_COOKIE variable from $_REQUEST.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function clean_request()
	{
		global $_COOKIE, $_GET, $_POST, $_REQUEST;

		// Remove magic quotes, if it is on...
		if((function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == 1) || @ini_get('magic_quotes_sybase'))
		{
			$_COOKIE = remove_magic($_COOKIE);
			$_GET = remove_magic($_GET);
			$_POST = remove_magic($_POST);
		}

		// $_REQUEST should only contain $_POST and $_GET, no cookies!
		$_REQUEST = array_merge($_POST, $_GET);
	}

	/*
		Function: remove_magic

		Strips slashes with stripslashes, recursively to the specified depth.

		Parameters:
			array $array - The array to remove the effects of magic quotes from.
			int $depth - How deep you want this function to go inside embedded
									 arrays.

		Returns:
			array - An array is returned which has had the effects of magic
							quotes undone.
	*/
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
}

/*
	Function: redirect

	Redirects the browser to the specified URL.

	Parameters:
		string $location - The new location redirect the browser to.
		mixed $status - The type of redirect to issue, such as 301 (Moved
										Permanently) or 307 (Temporary). If you don't want to
										remember either, you can supply permanent[ly] or
										temporary. This defaults to a temporary move.

	Returns:
		void - Nothing is returned by this function.

	Note:
		If you are wondering what the difference between a temporary and a
		permanent redirect are, and cannot deduce what it means through their
		names, I suppose I should tell you! A temporary redirect ensures that a
		browser will not cache the redirection when the same page is requested
		at a later time, while a permanent redirect can cause certain browsers
		(I know Chrome does, not sure about others) to cache the redirect until
		the cache is cleared, which can be bad if you don't want the browser to
		assume anything. Did that help? Bet not.
*/
function redirect($location = null, $status = 307)
{
	// Simply clear all headers, and redirect.
	if(ob_get_length() > 0)
	{
		// Well, if there are any.
		@ob_clean();
	}

	if(empty($location))
	{
		$location = baseurl;
	}

	// What type of redirect? Temporary, or permanent?
	if((int)$status == 307 || strtolower($status) == 'temporary')
	{
		// We may need to make a minor modification. Some browsers like to show
		// an annoying 'This web page is being redirect to another location'
		// blah  blah blah if there is POST data involved. This can be fixed
		// pretty  easily.
		if(count($_POST) > 0)
		{
			header('HTTP/1.1 303 See Other');
		}
		else
		{
			header('HTTP/1.1 307 Temporary Redirect');
		}
	}
	else
	{
		header('HTTP/1.0 301 Moved Permanently');
	}

	// Don't cache this! Geez. (This is done because browsers still send the
	// POST data when doing a 307 redirect, but then with a 301 redirect they
	// cache the resulting redirect)
	header('Cache-Control: no-cache');

	// Now redirect to the location of your desire!
	header('Location: '. $location);

	// Execution, HALT!
	exit;
}
?>