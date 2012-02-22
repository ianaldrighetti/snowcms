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
	Class: Tokens

	The Tokens class allows tokens to be registered with the system which
	can then be used to validate whether or not a user submitted a form, or
	other type of request. This class was created in the hopes that developers
	would use this tool to prevent CSRF, or Cross-Site Request Forgery. This
	tool is automatically utilized by the <Form> class, unless a form's
	options explicitly indicates that no CSRF token should be used.
*/
class Tokens
{
	/*
		Constructor: __construct

		This method may create the CSRF tokens array within the $_SESSION array
		if it does not exist.

		Parameters:
			none
	*/
	public function __construct()
	{
		// Do we need to generate the array within their session data that will
		// hold all the tokens?
		if(!isset($_SESSION['csrf_tokens']) || !is_array($_SESSION['csrf_tokens']))
		{
			$_SESSION['csrf_tokens'] = array();
		}
	}

	/*
		Method: add

		Associates the specified token string with the given name.

		Parameters:
			string $name - The name of the token.
			string $token - The token string to be associated with the name given,
											this should of course be as random as possible. This
											parameter may be left blank, in which case a random
											token string will be generated with a length of 64 to
											128 characters (inclusive).

		Returns:
			string - Returns the token string which was associated with the given
							 name.
	*/
	public function add($name, $token = null)
	{
		// Empty token? That's alright, we can fix that :)
		if(empty($token))
		{
			$members = api()->load_class('Members');

			// Super random, please!
			$token = $members->rand_str(mt_rand(64, 128));
		}

		$_SESSION['csrf_tokens'][$name] = array(
																				'token' => $token,
																				'registered' => time_utc(),
																			);

		// We will return the token, just in case they wanted one automatically
		// assigned.
		return $token;
	}

	/*
		Method: exists

		Checks whether there is a token associated with the specified name.

		Parameters:
			string $name - The name of the token.

		Returns:
			bool - Returns true if the token exists, false if not.
	*/
	public function exists($name)
	{
		return isset($_SESSION['csrf_tokens'][$name]);
	}

	/*
		Method: is_valid

		Checks to see if the supplied token matches the one with the token name.

		Parameters:
			string $name - The name of the token.
			string $token - The token to check the validity of.
			int $max_age - The maximum age of the token, in seconds. Defaults to
										 86400 seconds (1 day). See the notes below for more
										 information.

		Returns:
			bool - Returns true if the token is valid, false if it is not.

		Note:
			If the token is not valid, depending upon the scenario, don't get rid
			of the information (such as a page editing, forum post, etc.) ust say
			that the form token was incorrect and have them resubmit the data,
			if it is theirs of course...

			The system stores these tokens within the users session data, and
			SnowCMS sets the default cookie length for the PHP session ID to 5
			days, so just keep that in mind when setting $max_age.
	*/
	public function is_valid($name, $token, $max_age = 86400)
	{
		return !$this->is_expired($name, $max_age) && $this->token($name) == $token;
	}

	/*
		Method: is_expired

		Checks to see if the token is expired according to the supplied maximum
		age.

		Parameters:
			string $name - The name of the token.
			int $max_age - The maximum age that the token can be in order to be
										 considered valid. Defaults to 86400 seconds, or 1 day.

		Returns:
			bool - Returns true if the token is expired, false if not.
	*/
	public function is_expired($name, $max_age = 86400)
	{
		return !$this->exists($name) || ($_SESSION['csrf_tokens'][$name]['registered'] + $max_age) < time_utc();
	}

	/*
		Method: token

		Returns the token associated with the specified name.

		Parameters:
			string $name - The name of the token.

		Returns:
			string - Returns the token string of the identified token name, but
							 will return false if no token by the supplied name exists.
	*/
	public function token($name)
	{
		return $this->exists($name) ? $_SESSION['csrf_tokens'][$name]['token'] : false;
	}

	/*
		Method: delete

		Deletes the specified token.

		Parameters:
			string $name - The name of the token.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function delete($name)
	{
		if($this->exists($name))
		{
			unset($_SESSION['csrf_tokens'][$name]);

			return true;
		}
		else
		{
			return false;
		}
	}

	/*
		Method: clear

		Deletes all CSRF tokens within the current session.

		Parameters:
			none

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function clear()
	{
		// Just reset the CSRF tokens array.
		$_SESSION['csrf_tokens'] = array();

		return true;
	}
}
?>