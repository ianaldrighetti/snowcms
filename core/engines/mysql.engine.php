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

class MySQLDatabase extends Database
{
  public function connect()
  {
    global $db_host, $db_name, $db_pass, $db_persist, $db_result_class, $db_type, $db_user, $tbl_prefix;

    # Persistent connection or not?
    if(empty($db_persist))
      $this->con = @mysql_connect($db_host, $db_user, $db_pass);
    else
      $this->con = @mysql_pconnect($db_host, $db_user, $db_pass);

    # Fail to connect?
    if(empty($this->con))
    {
      $this->con = null;
      return false;
    }

    # Select the database now ;)
    $select_db = @mysql_select_db($db_name, $this->con);

    # Failed to select the database..? That isn't good!
    if(empty($select_db))
    {
      $this->con = null;
      return false;
    }

    # Sweet, everything seems to be in order so far, set a couple other things others
    # may need to use at a later time.
    $this->prefix = $tbl_prefix;
    $this->type = 'MySQL';
    $this->case_sensitive = false;
    $this->drop_if_exists = true;
    $this->if_not_exists = true;
    $this->extended_inserts = true;
    $this->result_class = $db_result_class;

    # Alright, we are done here.
    return true;
  }

  public function close()
  {
    return @mysql_close($this->con);
  }

  public function errno()
  {
    return @mysql_errno($this->con);
  }

  public function error()
  {
    return @mysql_error($this->con);
  }

  public function escape($str, $htmlspecialchars = false)
  {
    return @mysql_real_escape_string(!empty($htmlspecialchars) ? htmlspecialchars($str, ENT_QUOTES, 'UTF-8') : $str, $this->con);
  }

  public function unescape($str, $htmlspecialchars_decode = false)
  {
    return stripslashes(!empty($htmlspecialchars_decode) ? htmlspecialchars_decode($str, ENT_QUOTES) : $str);
  }

  public function version()
  {
    if(empty($this->con))
      return false;

    $result = $db->query('
      SELECT VERSION()');
    list($version) = $result->fetch_row();

    return $version;
  }

  public function tables()
  {
    if(empty($this->con))
      return false;

    $result = $db->query('
      SHOW TABLES');

    $tables = array();
    while($row = $result->fetch_row())
      $tables[] = $row[0];

    return $tables;
  }

  public function query($db_query, $db_vars = array(), $hook_name = null, $db_compat = null)
  {
    global $api;

    if(empty($this->con))
      return false;

    # Just incase, for some odd reason :P
    $this->run_hook($hook_name, &$db_query, &$db_vars, &$db_compat);
  }
}

$db_class = 'MySQLDatabase';
?>