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

# A pluggable class, how classy? Hahaha, yeah, I know, bad pun.
if(!class_exists('Member'))
{
  class Member
  {
    private $id;
    private $name;
    private $passwrd;
    private $hash;
    private $display_name;
    private $email;
    private $registered;
    private $ip;
    private $groups;
    private $data;
    private $session_id;

    public function __construct()
    {
      global $api, $cookie_name, $db;

      # Just define them, for now.
      $member_id = 0;
      $passwrd = '';
      $this->ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

      # Get that cookie, mmm..!
      if(!empty($_COOKIE[$cookie_name]) && empty($_SESSION['member_id']) && empty($_SESSION['member_pass']))
      {
        list($member_id, $passwrd) = @explode('|', $_COOKIE[$cookie_name]);

        $member_id = (string)$member_id == (string)(int)$member_id && (int)$member_id > 0 && mb_strlen($passwrd) == 40 ? (int)$member_id : 0;
        $passwrd = $member_id > 0 && mb_strlen($passwrd) == 40 ? $passwrd : '';

        $api->run_hook('cookie_data', array(&$member_id, &$passwrd));

        $_SESSION['member_id'] = $member_id;
        $_SESSION['member_pass'] = $passwrd;
      }
      else
        $api->run_hook('cookie_empty', array(&$member_id, &$passwrd));

      # Are you trying to steal someone else's session? Tisk tisk tisk! I just won't put up with that :P
      if(!empty($_SESSION['member_id']) && !empty($_SESSION['passwrd']) && ($_SESSION['member_id'] != $member_id || $_SESSION['member_pass'] != $passwrd))
        # Nice try, but better luck next time...
        unset($member_id, $passwrd);

      # So after ALL that, did your member id get set?
      if($member_id > 0)
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
        $_SESSION['session_id'] = sha1(($rand == 1 ? session_id() : ''). substr(str_shuffle('abcdefghijklmnopqrstuvwxyz1234567890'), 0, mt_rand(16, 32)). ($rand == 2 ? session_id() : ''));
        $_SESSION['session_assigned'] = time();
      }

      $this->session_id = $_SESSION['session_id'];
    }

    /*

      Just a lot of accessors ;)

    */
    public function id()
    {
      return $this->id;
    }

    public function name()
    {
      return $this->name;
    }

    public function passwrd()
    {
      return $this->passwrd;
    }

    public function hash()
    {
      return $this->hash;
    }

    public function display_name()
    {
      return $this->display_name;
    }

    public function email()
    {
      return $this->email;
    }

    public function registered()
    {
      return $this->registered;
    }

    public function groups()
    {
      return $this->groups;
    }

    public function session_id()
    {
      return $this->session_id;
    }

    /*

      Allows you to see if the member is a specified group. You can pass a single group identifier,
      or an array of group identifiers. If you pass a group (array) of group identifiers, FALSE will
      be returned if the member isn't ALL of the specified groups.

      @method public bool is_a(mixed $what);
        mixed $what - An array of group identifiers, or a single group identifier.
      returns bool - Returns TRUE if the member is all of the groups you specified, FALSE if not.

    */
    public function is_a($what)
    {
      # Hold on there! If they are an administrator, they are EVERYTHING!!!
      if($this->is_a('administrator') && (is_string($what) && mb_strtolower($what) != 'administrator'))
        return true;

      if(!is_array($what))
      {
        $what = mb_strtolower($what);

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

      @method public bool is_guest();
      returns bool - Returns TRUE if the person isn't logged in, FALSE if not.

    */
    public function is_guest()
    {
      return !$this->is_logged();
    }

    /*

      @method public bool is_logged();
      returns bool - Returns TRUE if the member is logged in, FALSE if not.

    */
    public function is_logged()
    {
      return $this->id > 0;
    }

    /*

      @method public bool is_admin();
      returns bool - Returns TRUE if the member is an administrator, FALSE if not.

    */
    public function is_admin()
    {
      return $this->is_a('administrator');
    }

    /*

      @method public string data(string $variable);
        string $variable - The name of the data's variable.
      returns string - Returns the value of the variable, NULL if the variable is not set.

    */
    public function data($variable)
    {
      return isset($this->data[$variable]) ? $this->data[$variable] : null;
    }
  }
}

if(!function_exists('init_member'))
{
  #
  # Just gets the member authenticated and stuff XD.
  #
  function init_member()
  {
    global $api, $member;

    $api->run_hook('init_member');

    # Have you not set $member yet? We will then.
    if(!isset($member))
      $member = new Member();
  }
}
?>