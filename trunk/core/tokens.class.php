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
	tool is automatically utilized by the <Form> class, unless otherwise
	specified.
*/
class Tokens
{
	// Variable: tokens
	// An array containing information about all the tokens associated with
	// the current member or guest.
	private $tokens;

	/*
		Constructor: __construct

		Initializes the tokens attribute, such as loading all valid tokens for
		the current user, along with registering a shut down function which will
		save all tokens and possibly delete any expired tokens from the tokens
		table.

		Parameters:
			none
	*/
	public function __construct()
	{
		$this->tokens = array();

		// Let's load up all the tokens associated with the current user, only
		// if they are less than a week old, though.
		$result = db()->query('
			SELECT
				token_name, token, token_registered
			FROM {db->prefix}tokens
			WHERE session_id = {string:session_id} AND token_registered >= {int:timeout}',
			array(
				'session_id' => member()->is_logged() ? 'member_id-'. member()->id() : 'ip'. member()->ip(),
				'timeout' => time_utc() - 604800,
			), 'token_load_registered_query');

		// Did we find any?
		if($result->num_rows() > 0)
		{
			while($row = $result->fetch_assoc())
			{
				$this->tokens[$row['token_name']] = array(
																						'token' => $row['token'],
																						'registered' => $row['token_registered'],
																						'is_new' => false,
																						'deleted' => false,
																					);
			}
		}

		// Save the registered tokens right before exit...
		api()->add_hook('snow_exit', create_function('', '
			$token = api()->load_class(\'Tokens\');

			$token->save();

			// Maybe we should remove expired ones? But not every page load.
			// (Why 79? Because that\'s that Wolfram|Alpha answered to the query
			// \'random number between 1 and 100\' :P)
			if(mt_rand(1, 100) == 79)
			{
				db()->query(\'
					DELETE FROM {db->prefix}tokens
					WHERE token_registered < {int:timeout}\',
					array(
						\'timeout\' => time_utc() - 604800,
					), \'token_delete_expired\');
			}'));
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

		// Now add that token to the list!
		$this->tokens[$name] = array(
														'token' => $token,
														'registered' => time_utc(),
														'is_new' => true,
														'deleted' => false,
													);

		return $token;
	}

	/*
		Method: exists

		Checks whether the specified token is registered with the Token system.

		Parameters:
			string $name - The name of the token.

		Returns:
			bool - Returns true if the token exists, false if not.
	*/
	public function exists($name)
	{
		return isset($this->tokens[$name]) && !$this->tokens[$name]['deleted'];
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

			The system will only load tokens which are younger than one week, so
			if $max_age is larger than 604800, it won't make any difference.
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
		return !$this->exists($name) || ($this->tokens[$name]['registered'] + $max_age) < time_utc();
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
		return $this->exists($name) ? $this->tokens[$name]['token'] : false;
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
			$this->tokens[$name]['deleted'] = true;

			return true;
		}
		else
		{
			return false;
		}
	}

	/*
		Method: clear

		Marks all tokens for deletion.

		Parameters:
			string $session_id - The session ID of which all registered tokens
													 are to be removed from the tokens table, whether
													 or not they are expired. If this is left empty,
													 then all tokens from the current session will be
													 removed.

		Returns:
			bool - Returns TRUE on success, FALSE on failure.
	*/
	public function clear($session_id = null)
	{
		// Is it the current session? Just mark them for deletion.
		if(empty($session_id) || $session_id == (member()->is_logged() ? 'member_id-'. member()->id() : 'ip'. member()->ip()))
		{
			if(count($this->tokens))
			{
				foreach($this->tokens as $token_name => $form)
				{
					$this->delete($token_name);
				}
			}

			return true;
		}
		else
		{
			// It is a different session ID than the current, so do it RIGHT NOW! :P
			$result = db()->query('
				DELETE FROM {db->prefix}tokens
				WHERE session_id = {string:session_id}',
				array(
					'sessiond_id' => $session_id,
				), 'token_clear_query');

			return $result->success();
		}
	}

	/*
		Method: save

		Saves any new information about the tokens in the database, such as
		adding new tokens, updating current ones or deleting, well, deleted
		ones!

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.

		Note:
			You should not call on this method, as it will be done automatically.
	*/
	public function save()
	{
		if(count($this->tokens) > 0)
		{
			$deleted = array();
			$changed = array();
			foreach($this->tokens as $token_name => $form)
			{
				// Is it marked for deletion?
				if(!empty($form['deleted']))
				{
					$deleted[] = $token_name;
				}
				// Maybe it is updated/new?
				elseif(!empty($form['is_new']))
				{
					$changed[] = array(member()->is_logged() ? 'member_id-'. member()->id() : 'ip'. member()->ip(), $token_name, $form['token'], $form['registered']);
				}
			}

			// Any deleted?
			if(count($deleted) > 0)
			{
				db()->query('
					DELETE FROM {db->prefix}tokens
					WHERE token_name IN({string_array:deleted})',
					array(
						'deleted' => $deleted,
					), 'token_save_delete_query');
			}

			// So do any need adding, or deletion?
			if(count($changed) > 0)
			{
				db()->insert('replace', '{db->prefix}tokens',
					array(
						'session_id' => 'string', 'token_name' => 'string-100', 'token' => 'string-255',
						'token_registered' => 'int',
					),
					$changed,
					array('sessiond_id', 'token_name'), 'token_save_replace_query');
			}
		}
	}
}
?>