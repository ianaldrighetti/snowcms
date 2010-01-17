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
    # Variable: id
    # Contains the members ID.
    private $id;

    # Variable: name
    # Contains the members login name
    private $name;

    # Variable: passwrd
    # Contains the members hashed password
    private $passwrd;

    # Variable: hash
    # A set of random characters (up to 16 characters) that the members
    # authentication cookie is salted with. Only changes whenever the
    # member changes their current password.
    private $hash;

    # Variable: display_name
    # Contains the members display name
    private $display_name;

    # Variable: email
    # Contains the members email address.
    private $email;

    # Variable: registered
    # Contains the unix timestamp of when the member registered
    # their account.
    private $registered;

    # Variable: ip
    # The members current IP address.
    private $ip;

    # Variable: groups
    # Contains an array of groups the member is assigned to. It the
    # members groups array will contain either administrator, member
    # or guest, but not more than one of those, however it can contain
    # other registered groups which are done via the <API>
    private $groups;

    # Variable: data
    # Contains an array of members data, such as options and other
    # various settings which are contained with the {db->prefix}member_data
    # table in the database.
    private $data;

    # Variable: session_id
    # The members current session ID. This should be used to verify that
    # it is the actual member completing such actions as commenting, deleting
    # and any other actions which should require some sort of verification.
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
      global $api, $cookie_name, $db;

      # Just define them, for now.
      $member_id = 0;
      $passwrd = '';
      $this->ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

      # Get that cookie, mmm..!
      if(!empty($_COOKIE[$cookie_name]))
      {
        list($member_id, $passwrd) = @explode('|', $_COOKIE[$cookie_name]);

        $member_id = (string)$member_id == (string)(int)$member_id && (int)$member_id > 0 && strlen($passwrd) == 40 ? (int)$member_id : 0;
        $passwrd = $member_id > 0 && strlen($passwrd) == 40 ? $passwrd : '';

        $api->run_hook('cookie_data', array(&$member_id, &$passwrd));

        # Only set this if the data was previously empty.
        if(empty($_SESSION['member_id']) || empty($_SESSION['member_pass']))
        {
          $_SESSION['member_id'] = $member_id;
          $_SESSION['member_pass'] = $passwrd;
        }
      }
      else
        $api->run_hook('cookie_empty', array(&$member_id, &$passwrd));

      # Are you trying to steal someone else's session? Tisk tisk tisk! I just won't put up with that :P
      if(!empty($_SESSION['member_id']) && !empty($_SESSION['passwrd']) && ($_SESSION['member_id'] != $member_id || $_SESSION['member_pass'] != $passwrd))
        # Nice try, but better luck next time...
        unset($member_id, $passwrd);

      # So after ALL that, did your member id get set?
      if(isset($member_id) && $member_id > 0)
      {
        # Alright, let's see if what you have is right :P
        $result = $db->query('
          SELECT
            member_id AS id, member_name AS name, member_pass AS pass, member_hash AS hash, display_name,
            member_email AS email, member_groups AS groups, member_registered AS registered
          FROM {db->prefix}members
          WHERE member_id = {int:member_id} AND member_activated > 0
          LIMIT 1',
          array(
            'member_id' => $member_id,
          ), 'login_query');

        # Did we find a member by that id?
        if($result->num_rows() > 0)
        {
          $member = $result->fetch_assoc();

          # Now one last check, then we will know if it is who you are claiming to be!
          if(!empty($member['hash']) && sha1($member['pass']. $member['hash']) == $passwrd)
          {
            # Now we can get some stuff done... ;)
            $this->id = $member['id'];
            $this->name = $member['name'];
            $this->passwrd = $member['pass'];
            $this->hash = $member['hash'];
            $this->display_name = $member['display_name'];
            $this->email = $member['email'];
            $this->registered = $member['registered'];
            $this->groups = @explode(',', $member['groups']);

            # Time to load their other data from the {db->prefix}member_data table :)
            $this->data = array();
            $result = $db->query('
              SELECT
                variable, value
              FROM {db->prefix}member_data
              WHERE member_id = {int:member_id}',
              array(
                'member_id' => $this->id,
              ), 'member_data_query');

            if($result->num_rows() > 0)
              while($row = $result->fetch_assoc())
                $this->data[$row['variable']] = $row['value'];
          }
        }
      }

      # So, you aren't logged in, you are a guest ;)
      if(!$this->is_logged())
        $this->groups = array('guest');

      # Don't think I did a good enough job at logging in the member? FINE! :P
      $member = array();
      $api->run_hook('post_login', array(&$member));

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
      }

      # The session id not set yet? or is it old..?
      if(empty($_SESSION['session_id']) || empty($_SESSION['session_assigned']) || ((int)$_SESSION['session_assigned'] + 86400) < time())
      {
        $rand = mt_rand(1, 2);
        $_SESSION['session_id'] = sha1(($rand == 1 ? session_id() : ''). substr(str_shuffle('abcdefghijklmnopqrstuvwxyz1234567890-_'), 0, mt_rand(16, 32)). ($rand == 2 ? session_id() : ''));
        $_SESSION['session_assigned'] = time();
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
      # Hold on there! If they are an administrator, they are EVERYTHING!!!
      if($this->is_a('administrator') && (is_string($what) && strtolower($what) != 'administrator'))
        return true;

      if(!is_array($what))
      {
        $what = strtolower($what);

        # Is it in our array?
        return in_array($what, $this->groups);
      }
      else
      {
        foreach($what as $w)
          if(!$this->is_a($w))
            return false;

        # No objections? Good.
        return true;
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
      Method: data

      Parameters:
        string $variable - The name of the data's variable.

      Returns:
        string - Returns the value of the variable, NULL if the variable is not set.
    */
    public function data($variable)
    {
      return isset($this->data[$variable]) ? $this->data[$variable] : null;
    }
  }
}

if(!function_exists('init_member'))
{
  /*
    Function: init_member

    Loads the Member class, if $member has not been set yet.

    Paramters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function init_member()
  {
    global $api, $member;

    $api->run_hook('init_member');

    # Have you not set $member yet? We will then.
    if(!isset($member))
      $member = $api->load_class('Member');

    # Are you not logged in? Then we need to go a little something :)
    if($member->is_guest())
      $api->add_hook('post_init_theme', 'member_guest_login_prep');
  }
}

/*
  Function: member_guest_login_prep

  If the current person browsing the site is a guest, then a random hash
  needs to be generated which is used for salting their password before
  they login, that is, if they do login.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function member_guest_login_prep()
{
  global $api, $theme;

  # The Members class has a random string generator :)
  $members = $api->load_class('Members');

  # Do we need to store the last random string?
  if(!empty($_SESSION['guest_rand_str']))
    $_SESSION['last_guest_rand_str'] = $_SESSION['guest_rand_str'];

  $_SESSION['guest_rand_str'] = $members->rand_str(mt_rand(20, 40));

  $theme->add_js_var('login_salt', $_SESSION['guest_rand_str']);
}
?>
