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
      $members = array((int)$members);
    else
    {
      if(count($members) > 0)
      {
        foreach($members as $key => $member_id)
        {
          if((int)$member_id > 0)
            $members[$key] = (int)$member_id;
          else
            unset($members[$key]);
        }

        $members = array_unique($members);
      }
    }

    # Alright, so do you want to do this yourself? ;) If you do do this yourself,
    # set the handled parameter to a bool, otherwise, if it is null, this method
    # will just do it itself!!! :P
    $handled = null;
    $api->run_hook('load_members', array(&$handled, $members));

    if($handled === null && count($members) > 0)
    {
      # Make sure this member isn't already loaded, otherwise it is a waste of resources.
      foreach($members as $member_id)
        if(isset($this->loaded[$member_id]))
          unset($members[$member_id]);

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
                        'member_groups' => explode(',', $row['member_groups']),
                        'registered' => $row['member_registered'],
                        'ip' => $row['member_ip'],
                        'is_activated' => !empty($row['member_activated']),
                        'acode' => $row['member_acode'],
                        'data' => array(),
                      );

            # Got something to add?
            $api->run_hook('load_members_array', array(&$member, $row));

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
              $this->loaded[$row['member_id']][$row['variable']] = $row['value'];
        }
      }

      $handled = true;
    }
    elseif($handled === null)
      $handled = true;

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
      $api->run_hook('members_get', array(&$member_data, $members));

      if($member_data == -1 && isset($this->loaded[$members]))
        $member_data = $this->loaded[$members];
      elseif($member_data == -1)
        $member_data = null;

      return $member_data === null ? false : $member_data;
    }
    else
    {
      # Load all those members ;)
      $member_data = array();
      foreach($members as $member_id)
        $member_data[$member_id] = $this->get($member_id);

      # Simple, no?
      return $member_data;
    }
  }

  /*
    Method: create

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
  public function create($member_name, $member_pass, $member_email, $options = array())
  {
    global $api, $db, $member;

    # Allows a plugin to handle the creation of members themselves ;)
    # Set the handled parameter to an integer or FALSE, otherwise the
    # system will handle the creation of the member.
    $handled = null;
    $api->run_hook('members_create', array(&$handled, $member_name, $member_pass, $member_email, $options));

    if($handled === null)
    {
      $member_name = trim($member_name);

      # Now make sure that the member name and email are allowed, we don't want them to be
      # in use already, as that would be pretty bad :P
      if(!$this->name_allowed($member_name) || !$this->email_allowed($member_email))
        return false;

      # How about a hash? (This hash will likely get changed eventually, but :P)
      if(!empty($options['member_hash']) && (mb_strlen($options['member_hash']) == 0 || mb_strlen($options['member_hash']) > 16))
        return false;
      elseif(empty($options['member_hash']))
        $options['member_hash'] = $this->generate_hash(mt_rand(8, 16));

      # Have you set a display name? Gotta check that!
      if(!empty($options['display_name']) && !$this->name_allowed($options['display_name']))
        return false;
      elseif(empty($options['display_name']))
        # We will just make your login name your display name too...
        $options['display_name'] = $member_name;

      # No member groups assigned? Member it is! (If the member is not an administrator, they
      # must have at least the member group assigned to them)
      if(isset($options['member_groups']) && is_array($options['member_groups']) && !in_array('administrator', $options['member_groups']) && !in_array('member', $options['member_groups']))
        return false;
      elseif(!isset($options['member_groups']) || !is_array($options['member_groups']))
        $options['member_groups'] = array('member');

      # Registration time can be manually set, must be greater than 0 though :P
      if(isset($options['member_registered']) && $options['member_registered'] > 0)
        return false;
      elseif(!isset($options['member_registered']))
        $options['member_registered'] = time();

      # An IP?
      if(empty($options['member_ip']))
        $options['member_ip'] = $member->ip();

      # Is the member activated?
      $options['member_activated'] = !empty($options['member_activated']);

      # If the member is not activated, then we will generate an activation code...
      $options['member_acode'] = empty($options['member_activated']) && empty($options['member_acode']) ? sha1($this->generate_hash(mt_rand(30, 40))) : (!empty($options['member_acode']) ? $options['member_acode'] : '');
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
    global $api, $db, $settings;

    # You know what to do, set it to a bool if you handle it ;)
    $handled = null;
    $api->run_hook('members_name_allowed', array(&$handled, $member_name));

    if($handled === null)
    {
      # Make sure the name isn't too long, or too short!
      if(mb_strlen($member_name) < 3 || mb_strlen($member_name) > 80)
        return false;

      # Lower it!!! (And htmlspecialchars it as well :P)
      $member_name = mb_strtolower(htmlchars($member_name));

      # First check to see if it is a reserved name...
      $reserved_names = explode("\n", mb_strtolower($settings->get('reserved_names')));

      if(count($reserved_names))
      {
        foreach($reserved_names as $reserved_name)
        {
          $reserved_name = trim($reserved_name);

          # Any wildcards?
          if(mb_strpos($member_name, '*') !== false)
          {
            if(preg_match('~^'. str_replace('*', '(?:.*?)?', $reserved_name). '$~i', $member_name))
              return false;
          }
          elseif($member_name == $reserved_name)
            return false;
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
        return false;
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

  */
  public function email_allowed($member_email, $member_id = 0)
  {
    global $api, $db, $settings;

    # You know what to do, set it to a bool if you handle it ;)
    $handled = null;
    $api->run_hook('members_email_allowed', array(&$handled, $member_email));

    if($handled === null)
    {
      # Check the email with regex!
      if(!preg_match('~^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$~i', $member_email))
        return false;

      # Now check disallowed emails...
      $disallowed_emails = explode("\n", mb_strtolower($settings->get('disallowed_emails')));

      if(count($disallowed_emails))
      {
        foreach($disallowed_emails as $disallowed_email)
        {
          $disallowed_email = trim($disallowed_email);

          if($member_email == $disallowed_email)
            return false;
        }
      }

      # Maybe the domain is disallowed?
      $disallowed_domains = explode("\n", mb_strtolower($settings->get('disallowed_emails')));

      list(, $email_domain) = explode('@', $member_email);

      if(count($disallowed_domain))
      {
        foreach($disallowed_domains as $disallowed_domain)
        {
          $disallowed_domain = trim($disallowed_domain);

          # Got any wildcards?
          if(mb_strpos($disallowed_domain, '*') !== false)
          {
            if(preg_match('~^'. str_replace('*', '(?:.*?)?', $disallowed_domain). '$~i', $email_domain))
              return false;
          }
          elseif($email_domain == $disallowed_domain)
            return false;
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
        return false;
    }

    return $handled === null ? true : !empty($handled);
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
    global $api, $db;

    # You should get this idea by now :P
    $authenticated = null;
    $api->run_hook('members_authenticate', array(&$authenticated, $member_name, $member_pass, $pass_hash));

    if($authenticated === null)
    {
      $member_name = htmlchars(trim($member_name));

      # Password not hashed..? That's fine, I'll do it myself, then. :P
      if(empty($pass_hash))
      {
        $member_pass = sha1(strtolower($member_name). $member_pass);
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
          return true;
        else
          return false;
      }
      else
        # We got nothing!
        return false;
    }

    return !empty($authenticated);
  }

  /*
    Method: generate_hash

    Generates a random as long as the supplied length.

    Parameters:
      int $length - The length of the random string you want to create.
                    If no length is supplied, a random length between
                    1 and 100 is used.

    Returns:
      string - Returns the randomly (pseudo-random, of course, because we all know,
               computers can't really make true random stuff ;)) generated string.
  */
  public function generate_hash($length = 0)
  {
    if(empty($length) || $length < 1)
      $length = mt_rand(1, 100);

    $chars = array(
               'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
               'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
               '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '~', '!', '@', '#', '$', '%', '^', '*', '-', '_', '+', '=', '?',
             );

    $str = '';
    for($i = 0; $i < 75; $i++)
      $str .= $chars[mt_rand(0, 74)];

    return $str;
  }
}
?>