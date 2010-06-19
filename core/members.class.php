<?php
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

if(!defined('IN_SNOW'))
  die;

/*
  Class: Members

  This is a class which allows the system and plugins to load various information
  about either an individual or multiple accounts. Anything relating to the obtaining
  or modification of member information should be done via the Members class as the
  current site may not be using SnowCMS's built in member database (You know, like
  someone is using a bridge ;))
*/
class Members
{
  # Variable: loaded
  # An array containing members which have already been loaded once (they don't need to be twice ;))
  private $loaded;

  /*
    Constructor: __construct

    Parameters:
      none
  */
  public function __construct()
  {
    $this->loaded = array();
  }

  /*
    Method: load

    This method loads the specified members into an array, which can be retrieved
    via <Members::get>

    Parameters:
      mixed $members - Either an array or single interger containing the member ID(s)
                       of which members you want to load the information of.

    Returns:
      bool - Returns TRUE if the information was successfully loaded, FALSE on failure.

  */
  public function load($members)
  {
    global $api, $db;

    # Not an array? Easy fix!
    if(!is_array($members))
    {
      $members = array((int)$members);
    }
    else
    {
      if(count($members) > 0)
      {
        foreach($members as $key => $member_id)
        {
          if((int)$member_id > 0)
          {
            $members[$key] = (int)$member_id;
          }
          else
          {
            unset($members[$key]);
          }
        }

        $members = array_unique($members);
      }
    }

    # Alright, so do you want to do this yourself? ;) If you do do this yourself,
    # set the handled parameter to a bool, otherwise, if it is null, this method
    # will just do it itself!!! :P
    $handled = null;
    $api->run_hooks('load_members', array(&$handled, $members));

    if($handled === null && count($members) > 0)
    {
      # Make sure this member isn't already loaded, otherwise it is a waste of resources.
      foreach($members as $member_id)
      {
        if(isset($this->loaded[$member_id]))
        {
          unset($members[$member_id]);
        }
      }

      if(count($members))
      {
        $result = $db->query('
          SELECT
            *
          FROM {db->prefix}members
          WHERE member_id IN({int_array:members})
          LIMIT {int:member_count}',
          array(
            'members' => $members,
            'member_count' => count($members),
          ), 'load_members_query');

        if($result->num_rows() > 0)
        {
          while($row = $result->fetch_assoc())
          {
            $member = array(
                        'id' => $row['member_id'],
                        'name' => $row['display_name'],
                        'username' => $row['member_name'],
                        'password' => $row['member_pass'],
                        'hash' => $row['member_hash'],
                        'email' => $row['member_email'],
                        'groups' => explode(',', $row['member_groups']),
                        'member_groups' => explode(',', $row['member_groups']),
                        'registered' => $row['member_registered'],
                        'ip' => $row['member_ip'],
                        'is_activated' => !empty($row['member_activated']),
                        'acode' => $row['member_acode'],
                        'data' => array(),
                      );

            # Got something to add?
            $api->run_hooks('members_load_array', array(&$member, $row));

            $this->loaded[$row['member_id']] = $member;
          }

          $result = $db->query('
            SELECT
              member_id, variable, value
            FROM {db->prefix}member_data
            WHERE member_id IN({int_array:members})',
            array(
              'members' => $members,
            ), 'load_members_data_query');

          if($result->num_rows() > 0)
            while($row = $result->fetch_assoc())
              $this->loaded[$row['member_id']]['data'][$row['variable']] = $row['value'];
        }
      }

      $handled = true;
    }
    elseif($handled === null)
    {
      $handled = false;
    }

    return !empty($handled);
  }

  /*
    Method: get

    After the member(s) information has been loaded via <Members::get> the data
    can be retrieved through this method.

    Parameters:
      mixed $members - Either an array or integer containing the member you want to get
                       the loaded information of.

    Returns:
      array - If you set the members parameter as an array, you will retrieve an array
              containing nested arrays, the index of the subarrays being the members id.
              If a single integer is supplied, then a single array will be returned.
              However, if the member id supplied has not yet been loaded, the value will be false.
  */
  public function get($members)
  {
    global $api;

    if(!is_array($members))
    {
      $members = (int)$members;
      $member_data = -1;

      # You want to do the loading? Set the member_data variable to something other
      # than -1, otherwise, this method will get the data itself. Set member_data to
      # null if that member has not been loaded.
      $api->run_hooks('members_get', array(&$member_data, $members));

      if($member_data == -1 && isset($this->loaded[$members]))
      {
        $member_data = $this->loaded[$members];
      }
      elseif($member_data == -1)
      {
        $member_data = null;
      }

      # Wanna touch it?
      $api->run_hooks('post_members_get', array(&$member_data, $members));

      return $member_data === null ? false : $member_data;
    }
    else
    {
      # Load all those members ;)
      $member_data = array();
      foreach($members as $member_id)
      {
        $member_data[$member_id] = $this->get($member_id);
      }

      # Simple, no?
      return $member_data;
    }
  }

  /*
    Method: add

    Creates a member with the supplied information.

    Parameters:
      string $member_name - The login name of the member you want to create.
      string $member_pass - The unhashed password the member uses to login with.
      string $member_email - The email of the member.
      array $options - An optional array containing extra information (such as their
                       hash, display name, ip, data (member_data table), if none of
                       these are supplied, the system automatically creates this info.)

    Returns:
      int - Returns an integer if the member was successfully created (which is the new
            new members ID) or FALSE on failure.
  */
  public function add($member_name, $member_pass, $member_email, $options = array())
  {
    global $api, $db, $func, $member;

    # Allows a plugin to handle the creation of members themselves ;)
    # Set the handled parameter to an integer or FALSE, otherwise the
    # system will handle the creation of the member.
    $handled = null;
    $api->run_hooks('members_add', array(&$handled, $member_name, $member_pass, $member_email, $options));

    if($handled === null)
    {
      $member_name = trim($member_name);

      # Now make sure that the member name and email are allowed, we don't want them to be
      # in use already, as that would be pretty bad :P
      if(!$this->name_allowed($member_name) || !$this->email_allowed($member_email) || !$this->password_allowed($member_name, $member_pass))
      {
        return false;
      }

      # How about a hash? (This hash will likely get changed eventually, but :P)
      if(!empty($options['member_hash']) && (strlen($options['member_hash']) == 0 || strlen($options['member_hash']) > 16))
      {
        return false;
      }
      elseif(empty($options['member_hash']))
      {
        $options['member_hash'] = $this->rand_str(16);
      }

      # Have you set a display name? Gotta check that!
      if(!empty($options['display_name']) && !$this->name_allowed($options['display_name']))
      {
        return false;
      }
      elseif(empty($options['display_name']))
      {
        # We will just make your login name your display name too...
        $options['display_name'] = $member_name;
      }

      # No member groups assigned? Member it is! (If the member is not an administrator, they
      # must have at least the member group assigned to them)
      if(isset($options['member_groups']) && is_array($options['member_groups']) && !in_array('administrator', $options['member_groups']) && !in_array('member', $options['member_groups']))
      {
        return false;
      }
      elseif(!isset($options['member_groups']) || !is_array($options['member_groups']))
      {
        $options['member_groups'] = array('member');
      }

      # Registration time can be manually set, must be greater than 0 though :P
      if(isset($options['member_registered']) && $options['member_registered'] <= 0)
      {
        return false;
      }
      elseif(!isset($options['member_registered']))
      {
        $options['member_registered'] = time_utc();
      }

      # An IP?
      if(empty($options['member_ip']))
      {
        $options['member_ip'] = $member->ip();
      }

      # Is the member activated?
      $options['member_activated'] = !empty($options['member_activated']) ? 1 : 0;

      # If the member is not activated, then we will generate an activation code...
      $options['member_acode'] = empty($options['member_activated']) && empty($options['member_acode']) ? sha1($this->rand_str(mt_rand(30, 40))) : (!empty($options['member_acode']) ? $options['member_acode'] : '');

      # Alright! Now insert that member!!!
      $result = $db->insert('insert', '{db->prefix}members',
                  array(
                    'member_name' => 'string', 'member_pass' => 'string', 'member_hash' => 'string',
                    'display_name' => 'string', 'member_email' => 'string', 'member_groups' => 'string',
                    'member_registered' => 'int', 'member_ip' => 'string', 'member_activated' => 'int',
                    'member_acode' => 'string',
                  ),
                  array(
                    htmlchars($member_name), sha1($func['strtolower'](htmlchars($member_name)). $member_pass), $options['member_hash'],
                    htmlchars($options['display_name']), htmlchars($member_email), implode(',', $options['member_groups']),
                    $options['member_registered'], $options['member_ip'], $options['member_activated'],
                    $options['member_acode'],
                  ), array(), 'members_add_query');

      $handled = $result->success() ? $result->insert_id() : false;

      # Maybe there is some default data that needs insertion?
      if(!empty($handled))
      {
        $data = array();

        # If you want to add data, do:
        # $data[] = array(varible, value)
        $api->run_hooks('members_add_default_data', array(&$data));

        # Anything?
        if(count($data) > 0)
        {
          foreach($data as $key => $value)
          {
            $data[$key] = array($handled, $value[0], $value[1]);
          }

          $db->insert('replace', '{db->prefix}member_data',
            array(
              'member_id' => 'int', 'variable' => 'string-255', 'value' => 'string',
            ),
            $data, array('member_id'), 'members_add_default_data_query');
        }
      }
    }

    return (string)(int)$handled == (string)$handled ? (int)$handled : false;
  }

  /*
    Method: name_allowed

    Checks to see if the specified member name is allowed, or already taken.

    Parameters:
      string $member_name - The member name to check.
      int $member_id - If there is a certain member which you want to exclude
                       from your search, give it here ;) Default is 0, which
                       is to search all members.

    Returns:
      bool - Returns TRUE if the name is allowed, FALSE if not.
  */
  public function name_allowed($member_name, $member_id = 0)
  {
    global $api, $db, $func, $settings;

    # You know what to do, set it to a bool if you handle it ;)
    $handled = null;
    $api->run_hooks('members_name_allowed', array(&$handled, $member_name));

    if($handled === null)
    {
      # Make sure the name isn't too long, or too short!
      if($func['strlen']($member_name) < $settings->get('members_min_name_length', 'int', 3) || $func['strlen']($member_name) > $settings->get('members_max_name_length', 'int', 80))
      {
        return false;
      }

      # Lower it!!! (And htmlspecialchars it as well :P)
      $member_name = $func['strtolower'](htmlchars($member_name));

      # First check to see if it is a reserved name...
      $reserved_names = explode("\n", $func['strtolower']($settings->get('reserved_names', 'string')));

      if(count($reserved_names))
      {
        foreach($reserved_names as $reserved_name)
        {
          $reserved_name = trim($reserved_name);

          # Any wildcards?
          if($func['strpos']($reserved_name, '*') !== false)
          {
            if(preg_match('~^'. str_replace('*', '(?:.*?)?', $reserved_name). '$~i', $member_name))
            {
              return false;
            }
          }
          elseif($member_name == $reserved_name)
          {
            return false;
          }
        }
      }

      # Now search the database...
      $result = $db->query('
        SELECT
          member_id
        FROM {db->prefix}members
        WHERE '. ($db->case_sensitive ? '(LOWER(member_name) = {string:member_name} OR LOWER(display_name) = {string:member_name})' : '(member_name = {string:member_name} OR display_name = {string:member_name})'). ' AND member_id != {int:member_id}
        LIMIT 1',
        array(
          'member_name' => $member_name,
          'member_id' => $member_id,
        ), 'members_name_allowed_query');

      # We find any matches?
      if($result->num_rows() > 0)
      {
        return false;
      }
    }

    # Are we still going? Then return the handled value, unless it wasn't modified,
    # in which case, that means we handled it and we didn't find the name!
    return $handled === null ? true : !empty($handled);
  }

  /*
    Method: email_allowed

    Checks to see if the specified email is allowed (either the whole address itself,
    or the domain) and not already taken.

    Parameters:
      string $member_email - The email to check.
      int $member_id - If there is a certain member which you want to exclude
                       from your search, give it here ;) Default is 0, which
                       is to search all members.

    Returns:
      bool - Returns true if the email address is allowed, false if not.
  */
  public function email_allowed($member_email, $member_id = 0)
  {
    global $api, $db, $func, $settings;

    # You know what to do, set it to a bool if you handle it ;)
    $handled = null;
    $api->run_hooks('members_email_allowed', array(&$handled, $member_email));

    if($handled === null)
    {
      $member_email = $func['strtolower'](htmlchars($member_email));

      # Check the email with regex!
      if(!preg_match('~^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$~i', $member_email))
      {
        return false;
      }

      # Now check disallowed emails...
      $disallowed_emails = explode("\n", $func['strtolower']($settings->get('disallowed_emails', 'string')));

      if(count($disallowed_emails) > 0)
      {
        foreach($disallowed_emails as $disallowed_email)
        {
          $disallowed_email = trim($disallowed_email);

          # Any wildcards?
          if($func['strpos']($disallowed_email, '*') !== false)
          {
            if(preg_match('~^'. str_replace('*', '(?:.*?)?', $disallowed_email). '$~i', $member_email))
            {
              return false;
            }
          }
          elseif($member_email == $disallowed_email)
          {
            return false;
          }
        }
      }

      # Or maybe, just maybe, it is already in use...
      $result = $db->query('
        SELECT
          member_id
        FROM {db->prefix}members
        WHERE '. ($db->case_sensitive ? 'LOWER(member_email) = {string:member_email}' : 'member_email = {string:member_email}'). ' AND member_id != {int:member_id}
        LIMIT 1',
        array(
          'member_email' => $member_email,
          'member_id' => $member_id,
        ), 'members_email_allowed_query');

      if($result->num_rows() > 0)
      {
        return false;
      }
    }

    return $handled === null ? true : !empty($handled);
  }

  /*
    Method: password_allowed

    Checks to see if the supplied password is allowed.

    Parameters:
      string $member_name - The login name of the member you are
                            checking the password of.
      string $member_pass - The password to check.

    Returns:
      bool - Returns TRUE if the password is allowed, FALSE if not.

    Note:
      The supplied password parameter should NOT be hashed, it should
      be the password in its original (unhashed) form.
  */
  public function password_allowed($member_name, $member_pass)
  {
    global $api, $db, $func, $settings;

    $handled = null;
    $api->run_hooks('members_password_allowed', array(&$handled, $member_pass));

    if($handled === null)
    {
      # Just a low setting? So must have at least 3 characters...
      if($settings->get('password_security', 'int') == 1)
      {
        $handled = $func['strlen']($member_pass) >= 3;
      }
      # Must be at least 4 characters long and cannot contain their username ;)
      elseif($settings->get('password_security', 'int') == 2)
      {
        $handled = $func['strlen']($member_pass) >= 4 && $func['stripos']($member_pass, $member_name) === false;
      }
      # At least 5 characters in length and must contain at least 1 number.
      else
      {
        $handled = $func['strlen']($member_pass) >= 5 && $func['stripos']($member_pass, $member_name) === false && preg_match('~[0-9]+~', $member_pass);
      }
    }

    return !empty($handled);
  }

  /*
    Method: authenticate

    This method takes a login name and a password, and checks to see if the supplied
    credentials would actually allow the user to login... This could be use for various
    things, oh, say, someone commenting under a username, but they don't actually want
    to login to post under their account (you know, like they are in a hurry/in a public
    place), hint hint ;) or actually verifying the login of a member...

    Parameters:
      string $member_name - The name of the member to check the credentials of.
      string $member_pass - The password to attempt to login with.
      string $pass_hash - This can be either false, which means that the supplied password
                          is not hashed at all (in which case, this method hashes it), it can
                          also be true, which means that the password was hashed in this format:
                          SHA1(LOWER(members name) + members password)
                          or a string that was used to salt the supplied hashed password. That
                          means that the members password hashed in this format:
                          SHA1(SHA1(LOWER(members name) + members password) + password hash).

    Returns:
      bool - Returns TRUE if the credentials were correct, FALSE on failure.
  */
  public function authenticate($member_name, $member_pass, $pass_hash = false)
  {
    global $api, $db, $func;

    # You should get this idea by now :P
    $authenticated = null;
    $api->run_hooks('members_authenticate', array(&$authenticated, $member_name, $member_pass, $pass_hash));

    if($authenticated === null)
    {
      $member_name = htmlchars(trim($member_name));

      # Password not hashed..? That's fine, I'll do it myself, then. :P
      if(empty($pass_hash))
      {
        $member_pass = sha1($func['strtolower'](htmlchars($member_name)). $member_pass);
        $pass_hash = true;
      }

      # Alright, let's query that database!
      $result = $db->query('
        SELECT
          member_pass, member_hash
        FROM {db->prefix}members
        WHERE member_name = {string:member_name}
        LIMIT 1',
        array(
          'member_name' => $member_name,
        ), 'members_authenticate_query');

      # Did we get anything?
      if($result->num_rows() > 0)
      {
        # Sure, we may have gotten a result, but that doesn't mean their password is right :P
        $row = $result->fetch_assoc();

        # So let's check
        if($member_pass == $row['member_pass'] || (!empty($pass_hash) && $pass_hash !== true && $member_pass == sha1($row['member_pass']. $pass_hash)))
        {
          return true;
        }
        else
        {
          # Maybe you would like to check..?
          $authenticated = false;
          $api->run_hooks('members_authenticate_other', array(&$authenticated, $member_name, $member_pass, $pass_hash, $row));

          return !empty($authenticated);
        }
      }
      else
      {
        # We got nothing!
        return false;
      }
    }

    return !empty($authenticated);
  }

  /*
    Method: rand_str

    Generates a random as long as the supplied length.

    Parameters:
      int $length - The length of the random string you want to create.
                    If no length is supplied, a random length between
                    1 and 100 is used.

    Returns:
      string - Returns the randomly (pseudo-random, of course, because we all know,
               computers can't really make true random stuff ;)) generated string.
  */
  public function rand_str($length = 0)
  {
    global $api;

    # If for some very strange, unknown reason you want to do a random string, be my guest!
    $handled = null;
    $str = '';
    $api->run_hooks('members_rand_str', array(&$handled, $length, &$str));

    if($handled === null)
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
    else
    {
      return $str;
    }
  }

  /*
    Method: update

    Updates the information of a supplied member ID.

    Parameters:
      int $member_id - The member ID to update.
      array $options - An array containing the information you want to update.

    Returns:
      bool - Returns TRUE if the member was successfully updated, FALSE if not.

    Note:
      For the $options parameter, these are acceptable indices to use:

        member_name - Their login name, however, if this is changed, their current password
                      MUST be supplied, otherwise, the update will fail (Password must
                      not be hashed yet), their member name must be supplied if their password
                      is changed.

        member_pass - The users new password.

        member_hash - A salt that the authentication cookie is hashed with.

        display_name - The members display name.

        member_email - The members email address.

        member_groups - An array containing the members groups.

        member_ip - The IP of the member.

        member_activated - The current status of the member. 0 means unactivated, 1 means
                           activated and 11 means that the member changed their email and
                           that the administrator has set the option to require the member
                           to verify their new email address before it is changed.

        member_acode - An activation code for activating or reactivating their account.

        data - An array formatted as so: variable => value, simple, no? If you want to delete
               a data variable for the specified member, set the value to false.

        admin_override - Set this to true if it is the administrator modifying the account, in
                         which case an activation code is generated (but it can be supplied) and
                         the members activation state changes to 11, and they are required to set
                         a new password.
  */
  public function update($member_id, $options)
  {
    global $api, $db, $func;

    if(count($options) == 0)
    {
      return false;
    }

    $handled = null;
    $api->run_hooks('members_update', array(&$handled, $member_id, $options));

    if($handled === null)
    {
      # Can't update a profile that doesn't exist, can we?
      $result = $db->query('
        SELECT
          member_id
        FROM {db->prefix}members
        WHERE member_id = {int:member_id}
        LIMIT 1',
        array(
          'member_id' => $member_id,
        ), 'members_update_profile_exists');

      if($result->num_rows() == 0)
      {
        return false;
      }
      elseif(isset($options['data']))
      {
        $member_data = $options['data'];
        unset($options['data']);
      }

      $allowed_columns = array(
        'member_name' => 'string-80',
        'member_pass' => 'string-40',
        'member_hash' => 'string-16',
        'display_name' => 'string-255',
        'member_email' => 'string-255',
        'member_groups' => 'string-255',
        'member_registered' => 'int',
        'member_ip' => 'string-150',
        'member_activated' => 'int',
        'member_acode' => 'string-40',
      );

      $api->run_hooks('members_update_allowed_columns', array(&$allowed_columns));

      $data = array();
      foreach($allowed_columns as $column => $type)
      {
        # Only add the data if the column exists...
        if(isset($options[$column]))
        {
          $data[$column] = $options[$column];
        }
      }

      # Let's let you check the data (just incase ;)) first...
      $api->run_hooks('members_update_check_data', array(&$handled, &$data));

      # If a hook didn't change handled, we can do our stuff :P
      if($handled !== false)
      {
        if(isset($data['member_groups']) && !is_array($data['member_groups']))
        {
          $data['member_groups'] = array($data['member_groups']);
        }

        # Make sure their name and password are OK, if supplied!
        if(isset($data['member_name']) && !$this->name_allowed($data['member_name'], $member_id))
        {
          # Must be taken!
          return false;
        }
        elseif(isset($data['member_pass']) && !$this->password_allowed(isset($data['member_name']) ? $data['member_name'] : '', $data['member_pass']))
        {
          return false;
        }
        elseif(isset($data['member_email']) && !$this->email_allowed($data['member_email'], $member_id))
        {
          return false;
        }
        elseif(isset($data['display_name']) && !$this->name_allowed($data['display_name'], $member_id))
        {
          return false;
        }
        elseif((isset($data['member_groups']) && !is_array($data['member_groups'])) || (isset($data['member_groups']) && (!in_array('member', $data['member_groups']) && !in_array('administrator', $data['member_groups']))))
        {
          return false;
        }

        # Remove any blank groups.
        if(isset($data['member_groups']) && count($data['member_groups']) > 0)
        {
          $member_groups = array();
          foreach($data['member_groups'] as $group_id)
          {
            if(strlen(trim($group_id)) > 0)
            {
              $member_groups[] = trim($group_id);
            }
          }

          # There, all done :-)
          $data['member_groups'] = $member_groups;
        }

        # Now we need to hash the password, maybe!
        if(!empty($options['member_name']) && !empty($options['member_pass']))
        {
          $data['member_pass'] = sha1($func['strtolower']($options['member_name']). $options['member_pass']);
        }
        elseif(empty($options['admin_override']) && ((!empty($options['member_name']) && empty($options['member_pass'])) || (empty($options['member_name']) && !empty($options['member_pass']))))
        {
          return false;
        }
        elseif(!empty($options['admin_override']))
        {
          $data['member_acode'] = empty($data['member_acode']) ? sha1($this->rand_str(mt_rand(30, 40))) : $data['member_acode'];
          $data['member_activated'] = 11;
        }

        # Can't be a bool, the database wants an integer! Easy fix, though!
        if(is_bool($data['member_activated']))
        {
          $data['member_activated'] = !empty($data['member_activated']) ? 1 : 0;
        }

        # Our data array could be empty, simply because they are updating member_data table information!
        if(!empty($data))
        {
          $db_vars = array(
            'member_id' => $member_id,
          );
          $values = array();
          foreach($data as $column => $value)
          {
            $values[] = $column. ' = {'. $allowed_columns[$column]. ':'. $column. '_value}';
            $db_vars[$column. '_value'] = $column == 'member_groups' ? implode(',', $value) : $value;
          }

          # Now update that data!
          $result = $db->query('
            UPDATE {db->prefix}members
            SET '. implode(', ', $values). '
            WHERE member_id = {int:member_id}
            LIMIT 1',
            $db_vars, 'members_update_query');

          $handled = $result->success();
        }

        if(!empty($member_data) && count($member_data) > 0 && ($handled === null || $handled))
        {
          $data = array();
          $delete = array();
          foreach($member_data as $variable => $value)
          {
            if($value !== false)
            {
              $data[] = array($member_id, $variable, $value);
            }
            else
            {
              $delete[] = $variable;
            }
          }

          if(count($data) > 0)
          {
            $result = $db->insert('replace', '{db->prefix}member_data',
                        array(
                          'member_id' => 'int', 'variable' => 'string-255', 'value' => 'string',
                        ),
                        $data,
                        array('member_id'), 'members_update_data_query');

            $handled = $result->success();
          }

          if(count($delete) > 0)
          {
            $result = $db->query('
              DELETE FROM {db->prefix}member_data
              WHERE member_id = {int:member_id} AND variable IN({string_array:variables})',
              array(
                'member_id' => $member_id,
                'variables' => $delete,
              ), 'members_update_delete_data_query');

            $handled = $result->success();
          }
        }

        # This member will need to be reloaded ;)
        unset($this->loaded[$member_id]);

        $api->run_hooks('members_update_force_refresh', array($member_id));
      }
    }

    return !empty($handled);
  }

  /*
    Method: delete

    Deletes the specified members.

    Parameters:
      mixed $members - This can either be an integer, or an array of integers.

    Returns:
      bool - Returns TRUE if the specified members were deleted, FALSE if not.

    Note:
      Be sure before executing this command that you verify their session id!
      Check out <Members.verify>
  */
  public function delete($members)
  {
    global $api, $db;

    $handled = null;
    $api->run_hooks('members_delete', array(&$handled, $members));

    if($handled === null)
    {
      # Not an array? We will fix that!!!
      if(!is_array($members))
      {
        $members = array($members);
      }

      # Yeah, we deleted nothing successfully! Ha!
      if(count($members) == 0)
      {
        return true;
      }

      # Now let's just make sure they are all plausible ids...
      foreach($members as $key => $member_id)
      {
        $member_id = (int)$member_id;

        if($member_id < 1)
        {
          unset($members[$key]);
        }
      }

      $members = array_unique($members);

      # Now delete those members!
      $result = $db->query('
        DELETE FROM {db->prefix}members
        WHERE member_id IN({int_array:members})
        LIMIT {int:member_count}',
        array(
          'members' => $members,
          'member_count' => count($members),
        ), 'members_delete_query');

      # Was it a success? We still have some more to do!
      if($result->success())
      {
        # Now delete their data in the member_data table.
        $result = $db->query('
          DELETE FROM {db->prefix}member_data
          WHERE member_id IN({int_array:members})',
          array(
            'members' => $members,
          ), 'members_delete_query_data');
      }

      $handled = $result->success();

      $api->run_hooks('post_members_delete', array($members));
    }

    return !empty($handled);
  }

  /*
    Method: name_to_id

    Converts a username to an ID.

    Parameters:
      mixed $name - The username to convert, this can also be an array
                    of usernames as well.

    Returns:
      mixed - Returns an integer if one username is supplied, an associative
              array containing the IDs (LOWER(name) => ID). The value of the
              name will be false (for arrays or single lookups) if the name
              was not found.

    Note:
      Please note that this looks up usernames, not display names!
  */
  public function name_to_id($name)
  {
    global $api, $db, $func;

    # You might want to do this if you have your own member setup ;)
    $handled = null;
    $api->run_hooks('member_name_to_id', array(&$handled, $name));

    if($handled === null)
    {
      # Is it a bird, a plane, an array?!
      if(!is_array($name))
      {
        # It's not an array, yet ;)
        $name = array($name);
      }

      # Nothing? Bad!
      if(count($name) == 0)
      {
        return false;
      }

      # Lowercase all the names.
      foreach($name as $key => $value)
      {
        $name[$key] = $func['strtolower']($value);
      }

      # Simple in reality...
      $result = $db->query('
        SELECT
          LOWER(member_name) AS name, member_id AS id
        FROM {db->prefix}members
        WHERE '. ($db->case_sensitive ? 'LOWER(member_name)' : 'member_name'). ' IN({string_array:names})',
        array(
          'names' => $name,
        ), 'member_name_to_id_query');

      # Now it gets different... We may just return the ID itself, no array.
      if(count($name) == 1)
      {
        if($result->num_rows() == 0)
        {
          return false;
        }

        list(, $member_id) = $result->fetch_row();

        return $member_id;
      }
      else
      {
        # Flip!!! :-)
        $names = array_flip($name);

        # For now, we will assume none were found.
        foreach($names as $key => $name)
        {
          $names[$key] = false;
        }

        while($row = $result->fetch_assoc())
        {
          $names[$row['name']] = $row['id'];
        }

        return $names;
      }
    }

    return $handled;
  }
}
?>