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
	Class: Member

	The Member class contains all the information about the current member,
	such as their member ID, name, display name, email, and so on. This class
	can be totally overloaded, just be sure to have all the same methods
	implemented as the one below ;) However, you can also hook into the
	constructor and load the information yourself.
*/
if(!class_exists('Member'))
{
	class Member
	{
		// Variable: id
		// Contains the members ID.
		private $id;

		// Variable: name
		// Contains the members login name
		private $name;

		// Variable: passwrd
		// Contains the members hashed password
		private $passwrd;

		// Variable: hash
		// A set of random characters (up to 16 characters) that the members
		// authentication cookie is salted with. Only changes whenever the
		// member changes their current password.
		private $hash;

		// Variable: display_name
		// Contains the members display name
		private $display_name;

		// Variable: email
		// Contains the members email address.
		private $email;

		// Variable: registered
		// Contains the unix timestamp of when the member registered
		// their account.
		private $registered;

		// Variable: ip
		// The members current IP address.
		private $ip;

		// Variable: groups
		// Contains an array of groups the member is assigned to. It the
		// members groups array will contain either administrator, member
		// or guest, but not more than one of those, however it can contain
		// other registered groups which are done via the <API>
		private $groups;

		// Variable: permissions
		// Contains the permissions the member has based on all the groups
		// they are in. However, if the member is an administrator, this
		// array will always be empty as they can do anything.
		private $permissions;

		// Variable: data
		// Contains an array of members data, such as options and other
		// various settings which are contained with the {db->prefix}member_data
		// table in the database.
		private $data;

		// Variable: session_id
		// The members current session ID. This should be used to verify that
		// it is the actual member completing such actions as commenting, deleting
		// and any other actions which should require some sort of verification.
		private $session_id;

		/*
			Constructor: __construct

			During the construction of this object, all the attributes of the
			object are set to default values or populated with member data if
			the current person browsing the site has a valid authentication cookie.

			This class can either be redeclared entirely, or a plugin can hook into
			the hook named 'post_login' to completely redefine any of Member's
			attributes. This is useful for bridging SnowCMS with other systems, but
			you could also use this tactic to implement other login systems such as
			OpenID, or anything else, for that matter.


			Parameters:
				none
		*/
		public function __construct()
		{
			global $func;

			// Just define them, for now.
			$member_id = 0;
			$passwrd = '';
			$this->ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

			// Get that cookie, mmm..!
			if(!empty($_COOKIE[api()->apply_filters('login_cookie_name', cookiename)]))
			{
				list($member_id, $passwrd) = @explode('|', $_COOKIE[api()->apply_filters('login_cookie_name', cookiename)]);

				$member_id = (string)$member_id == (string)(int)$member_id && (int)$member_id > 0 && $func['strlen']($passwrd) == 40 ? (int)$member_id : 0;
				$passwrd = $member_id > 0 && $func['strlen']($passwrd) == 40 ? $passwrd : '';

				api()->run_hooks('cookie_data', array(&$member_id, &$passwrd));

				// Only set this if the data was previously empty.
				if(empty($_SESSION['member_id']) || empty($_SESSION['member_pass']))
				{
					$_SESSION['member_id'] = $member_id;
					$_SESSION['member_pass'] = $passwrd;
				}
			}
			else
			{
				api()->run_hooks('cookie_empty', array(&$member_id, &$passwrd));
			}

			// Are you trying to steal someone else's session? Tisk tisk tisk! I just won't put up with that :P
			if(!empty($_SESSION['member_id']) && !empty($_SESSION['passwrd']) && ($_SESSION['member_id'] != $member_id || $_SESSION['member_pass'] != $passwrd))
			{
				// Nice try, but better luck next time...
				unset($member_id, $passwrd);
			}

			// So after ALL that, did your member id get set?
			if(isset($member_id) && $member_id > 0)
			{
				// Alright, let's see if what you have is right :P
				$result = db()->query('
					SELECT
						member_id AS id, member_name AS name, member_pass AS pass, member_hash AS hash, display_name,
						member_email AS email, member_groups AS groups, member_registered AS registered
					FROM {db->prefix}members
					WHERE member_id = {int:member_id} AND member_activated = 1
					LIMIT 1',
					array(
						'member_id' => $member_id,
					), 'login_query');

				// Did we find a member by that id?
				if($result->num_rows() > 0)
				{
					$member = $result->fetch_assoc();

					// Now one last check, then we will know if it is who you are claiming to be!
					if(!empty($member['hash']) && sha1($member['pass']. $member['hash']) == $passwrd)
					{
						// Now we can get some stuff done... ;)
						$this->id = $member['id'];
						$this->name = $member['name'];
						$this->passwrd = $member['pass'];
						$this->hash = $member['hash'];
						$this->display_name = $member['display_name'];
						$this->email = $member['email'];
						$this->registered = $member['registered'];
						$this->groups = @explode(',', $member['groups']);

						// Let's update their last active time, along with their current
						// IP address.
						db()->query('
							UPDATE {db->prefix}members
							SET member_last_active = {int:cur_time}, member_ip = {string:member_ip}
							WHERE member_id = {int:member_id}
							LIMIT 1',
							array(
								'cur_time' => time_utc(),
								'member_ip' => $this->ip,
								'member_id' => $this->id,
							), 'member_update_last_info');

						// Time to load their other data from the {db->prefix}member_data table :)
						$this->data = array();
						$result = db()->query('
							SELECT
								variable, value
							FROM {db->prefix}member_data
							WHERE member_id = {int:member_id}',
							array(
								'member_id' => $this->id,
							), 'member_data_query');

						if($result->num_rows() > 0)
						{
							while($row = $result->fetch_assoc())
							{
								$this->data[$row['variable']] = $row['value'];
							}
						}

						// Time to load up their permissions based on groups.
						// However, if they are an administrator, don't bother!
						if(!$this->is_admin())
						{
							$result = db()->query('
													SELECT
														group_id, permission, status
													FROM {db->prefix}permissions
													WHERE group_id IN({string_array:groups})',
													array(
														'groups' => $this->groups,
													), 'member_permission_query');

							// Get ready to load'em up!
							$this->permissions = array();

							// You can also explicitly deny permissions as well.
							$deny = array();
							while($row = $result->fetch_assoc())
							{
								// -1 means denied, no matter what!
								if($row['status'] == -1)
								{
									$deny[] = $row['permission'];
								}
								else
								{
									// If status is 1, give them the permission, however, if they can't
									// use the previous permission unless the permission isn't set yet.
									$this->permissions[$row['permission']] = $row['status'] == 1 ? true : (isset($this->permissions[$row['permission']]) ? !empty($this->permissions[$row['permission']]) : false);
								}
							}

							// Any denied permissions? As we need to apply those.
							if(count($deny) > 0)
							{
								foreach($deny as $permission)
								{
									$this->permissions[$permission] = false;
								}
							}
						}
					}
				}
			}

			// So, you aren't logged in, you are a guest ;)
			if(!$this->is_logged())
			{
				$this->groups = array('guest');
			}

			// Don't think I did a good enough job at logging in the member? FINE! :P
			$member = array();
			api()->run_hooks('post_login', array(&$member));

			if(count($member) > 0)
			{
				$this->id = isset($member['id']) ? $member['id'] : $this->id;
				$this->name = isset($member['name']) ? $member['name'] : $this->name;
				$this->passwrd = isset($member['pass']) ? $member['pass'] : $this->passwrd;
				$this->hash = isset($member['hash']) ? $member['hash'] : $this->hash;
				$this->display_name = isset($member['display_name']) ? $member['display_name'] : $this->display_name;
				$this->email = isset($member['email']) ? $member['email'] : $this->email;
				$this->registered = isset($member['registered']) ? $member['registered'] : $this->registered;
				$this->groups = isset($member['groups']) ? @explode(',', $member['groups']) : $this->groups;
				$this->permissions = isset($member['permissions']) ? $member['permissions'] : $this->permissions;
			}

			// The session id not set yet? or is it old..?
			if(empty($_SESSION['session_id']) || empty($_SESSION['session_assigned']) || ((int)$_SESSION['session_assigned'] + 86400) < time_utc())
			{
				$rand = mt_rand(1, 2);
				$_SESSION['session_id'] = sha1(($rand == 1 ? session_id() : ''). substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-_!@#$%^&*()[]{}:;,.\\|\'"'), 0, mt_rand(16, 32)). ($rand == 2 ? session_id() : ''));
				$_SESSION['session_assigned'] = time_utc();
			}

			$this->session_id = $_SESSION['session_id'];
		}

		/*
			Method: id

			Parameters:
				none

			Returns:
				int - Returns the members ID.
		*/
		public function id()
		{
			return $this->id;
		}

		/*
			Method: name

			Parameters:
				none

			Returns:
				string - Returns the members login name.
		*/
		public function name()
		{
			return $this->name;
		}

		/*
			Method: passwrd

			Parameters:
				none

			Returns:
				string - Returns the members hashed password.
		*/
		public function passwrd()
		{
			return $this->passwrd;
		}

		/*
			Method: hash

			Parameters:
				none

			Returns:
				string - A set of random characters that the members authentication
								cookie is salted with.
		*/
		public function hash()
		{
			return $this->hash;
		}

		/*
			Method: display_name

			Parameters:
				none

			Returns:
				string - Returns the display name of the member.
		*/
		public function display_name()
		{
			return $this->display_name;
		}

		/*
			Method: email

			Parameters:
				none

			Returns:
				string - Returns the members email address.
		*/
		public function email()
		{
			return $this->email;
		}

		/*
			Method: registered

			Parameters:
				none

			Returns:
				int - Returns the unix timestamp containing the time
							at which the member registered an account.
		*/
		public function registered()
		{
			return $this->registered;
		}

		/*
			Method: ip

			Parameters:
				none

			Returns:
				string - Returns the current users IP.
		*/
		public function ip()
		{
			return $this->ip;
		}

		/*
			Method: groups

			Parameters:
				none

			Returns:
				array - An array containing the groups the member is
								part of, which will contain either administrator,
								member, guest, but it can also contain others, but
								no more than one of the previously mentioned groups.
		*/
		public function groups()
		{
			return $this->groups;
		}

		/*
			Method: session_id

			Parameters:
				none

			Returns:
				string - A string containing the members current session_id.
		*/
		public function session_id()
		{
			return $this->session_id;
		}

		/*
			Method: is_a

			Allows you to see if the member is a specified group. You can pass a single group identifier,
			or an array of group identifiers. If you pass a group (array) of group identifiers, FALSE will
			be returned if the member isn't ALL of the specified groups.

			Parameters:
				mixed $what - An array of group identifiers, or a single group identifier.

			Returns:
			 bool - Returns TRUE if the member is all of the groups you specified, FALSE if not.
		*/
		public function is_a($what)
		{
			// Before we check anything, we will check to see if you are an administrator, in which case
			// you can do EVERYTHING! :)
			if(in_array('administrator', $this->groups))
			{
				return true;
			}

			// An array of groups?
			if(is_array($what))
			{
				if(count($what) == 0)
				{
					return false;
				}

				foreach($what as $group)
				{
					// When you have multiple groups, you must have
					if(!$this->is_a($group))
					{
						return false;
					}
				}

				// Nothing went wrong? Good, your all of what what is! ;)
				return true;
			}
			else
			{
				// Simple:
				return in_array(strtolower($what), $this->groups);
			}
		}

		/*
			Method: is_guest

			Parameters:
				none

			Returns:
				bool - Returns TRUE if the person isn't logged in, FALSE if not.
		*/
		public function is_guest()
		{
			return !$this->is_logged();
		}

		/*
			Method: is_logged

			Parameters:
				none

			Returns:
				bool - Returns TRUE if the member is logged in, FALSE if not.
		*/
		public function is_logged()
		{
			return $this->id > 0;
		}

		/*
			Method: is_admin

			Parameters:
				none

			Returns:
				bool - Returns TRUE if the member is an administrator, FALSE if not.
		*/
		public function is_admin()
		{
			return $this->is_a('administrator');
		}

		/*
			Method: can

			Returns whether or not the member can execute the specified permission.

			Parameters:
				mixed $permission - The permission(s) to check. If an array of permissions
														are supplied, if even one permission is denied, then
														false will be returned. In order for an array of
														permissions to return true is if the member has permission
														to do all the supplied.

			Returns:
				bool - Returns true if the member has the permission, false if not.
		*/
		public function can($permission)
		{
			// Hold on! Are you an administrator? If so, you can do ANYTHING!
			if($this->is_admin())
			{
				return true;
			}

			// An array of permissions?
			if(is_array($permission))
			{
				// Make sure there are any to check.
				if(count($permission))
				{
					foreach($permission as $p)
					{
						// Can you?
						if(!$this->can($permission))
						{
							return false;
						}
					}

					// If we are still going, that means all permissions were allowed.
					return true;
				}
				else
				{
					// Sure, you can do nothing.
					return true;
				}
			}
			else
			{
				// A single, lonesome permission! So sad :-(
				return !empty($this->permissions[$permission]);
			}
		}

		/*
			Method: data

			An accessor for the members settings, options, whatever they set
			specifically to their account.

			Parameters:
				string $variable - The name of the setting.
				string $type - The data type to have the setting value
											 returned as.
				mixed $default - If the requested setting variable is not set
												 then this value will be returned, as is.

			Returns:
				mixed - Returns the value of the setting, NULL if the setting
								 was not found.

			Note:
				The data types supported vary depending upon plugins. Plugins can
				add more data types by hooking into typecast_construct, with more
				information available at <Typecast::add_type>.
		*/
		public function data($variable, $type = null, $default = null)
		{
			// Everything can be done with a single line. Yippe...
			return typecast()->to(!empty($type) ? $type : 'string', isset($this->data[$variable]) ? $this->data[$variable] : $default);
		}
	}
}

if(!function_exists('load_member'))
{
	/*
		Function: load_member

		Loads the Member class, if $member has not been set yet.

		Paramters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.

			As of SnowCMS 2.0 alpha 2, this function was renamed from init_member
			to load_member.

			This function should *not* be called directly, because calling the
			<member> function will call this function if necessary.
	*/
	function load_member()
	{
		api()->run_hooks('init_member');

		// Make sure no plugin created some form of a member object.
		if(!isset($GLOBALS['member']))
		{
			$GLOBALS['member'] = api()->load_class('Member');
		}
		api()->run_hooks('post_init_member');
	}
}

/*
	Function: member

	Returns the current instance of Member.

	Parameters:
		none

	Returns:
		object
*/
function member()
{
	if(!isset($GLOBALS['member']))
	{
		// Looks like there is no Member instance yet, let's make one!
		load_member();
	}

	return $GLOBALS['member'];
}
?>