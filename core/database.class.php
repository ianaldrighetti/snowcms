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

# The abstract class used by SQL engines.
abstract class Database
{
  # Holds the database connection resource.
  public $con = null;

  # Holds the table prefix, such as snow_
  public $prefix = null;

  # The type of database, for example: MySQL, SQLite, PostgreSQL, etc.
  public $type = null;

  # Is the database system case sensitive? Some are, some aren't. Be sure
  # that you set this to TRUE if the database is case sensitive, or very bad
  # things could occur!!! (Set to true, just incase ;))
  public $case_sensitive = true;

  # The number of queries executed so far, right now, none!
  public $num_queries = 0;

  # Does your database type support DROP TABLE IF EXISTS?
  public $drop_if_exists = false;

  # How about CREATE TABLE IF NOT EXISTS?
  public $if_not_exists = false;

  # Can the database handle extended inserts?
  public $extended_inserts = false;

  # Is the database in a forced debug mode?
  public $debug = false;

  # The result class which query and insert return (global $db_result_class in connect()).
  public $result_class = null;

  # Holds all the debugging stuff which gets written to a file when the object is destructed.
  protected $debug_text = '';

  /*

    This constructor simply takes the database result's class name and saves it to the result_class attribute.

  */
  public function __construct($db_result_class = null)
  {
    $this->result_class = $db_result_class;
  }

  /*

    Connects to the SQL database or server.

    @method public bool connect();
    returns bool - Returns TRUE if the connection was a success, FALSE if connection failed.

    NOTE: To get the database information, simply global the variables, such as $db_host, $db_name, $db_user, $db_passwd, $db_prefix, etc.

  */
  abstract public function connect();

  /*

    Closes the connection to the SQL database or server.

    @method public bool close();
    returns bool - Returns TRUE if the connection was successfully closed, FALSE otherwise.

  */
  abstract public function close();

  /*

    Returns the error number of which occurred from the last database method called, if any.

    @method public int errno();
    returns int - Returns the last error number, 0 if none.

  */
  abstract public function errno();

  /*

    Returns the error string from the last SELECT, UPDATE, INSERT, etc. query.

    @method public string error();
    returns string - Returns the last error string, an empty string if no errors occurred.

  */
  abstract public function error();

  /*

    Makes a string safe to use in a query.

    @method public string escape(string $str[, bool $htmlspecialchars = false]);
      string $str - The string that needs sanitizing.
      bool $htmlspecialchars - Whether or not to first do htmlspecialchars on the string.
    returns string - Returns the sanitized string.

  */
  abstract public function escape($str, $htmlspecialchars = false);

  /*

    The opposite of the escape method.

    @method public string unescape(string $str[, bool $htmlspecialchars_decode = false]);
      string $str - The string to unescape.
      bool $htmlspecialchars_decode - Whether or not to undo htmlspecialchars after unescaping.
    returns string - Returns the unescaped string.

  */
  abstract public function unescape($str, $htmlspecialchars_decode = false);

  /*

    Returns a string containing the databases version (Like MySQL 5.0.11).

    @method public string version();
    returns string - Returns the version of the databases version number.

  */
  abstract public function version();

  /*
    Returns an array containing all the tables in the database.

    @method public array tables();
    returns array - Returns an array containing all the tables in the database.

  */
  abstract public function tables();

  /*

    Queries the database, however, it isn't a simple [mysql|sqlite|...]_query as this method
    changes the query for any compatibility issues. ONLY SELECT, UPDATE and DELETE queries should be
    used with this method, check out the insert method for doing INSERT's and REPLACE's

    @method public {Database_Result} query(string $db_query[, array $db_vars = array()[ string $hook_name = null[, string $db_compat = null[ string $file = null[, int $line = 0]]]]]);
      string $db_query - The database query you want to execute.
      array $db_vars - The variable values to replace in the query.
      string $hook_name - The name of hook to run BEFORE anything else is done. The run_hook method
                          is to have $db_query, $db_vars and $db_compat passed as parameters.
      string $db_compat - A string which can be null or a string giving the database class a heads up
                          on any possible compatibility issues.
      string $file - The file query was called on, LEAVE THIS BLANK! This is for use by the insert method!
      int $line - The line the query was called on, LEAVE THIS BLANK! This is for use by the insert method as well!
    returns {Database_Result} - Returns an object with methods such as fetch_assoc, num_rows, etc.

  */
  abstract public function query($db_query, $db_vars = array(), $hook_name = null, $db_compat = null, $file = null, $line = 0);

  /*

    Sanitizes the variable in a query using the correct methods.

    @method protected mixed var_sanitize(string $var_name, string $datatype, mixed $value, $file, $line);
      string $var_name - The name of the variable in the query.
      string $datatype - The datatype of the variable.
      mixed $value - Contains the value of the variable.
      string $file - The file that query/insert was called in.
      int $line - The line that query/insert was called on.
    returns mixed - Returns the correctly sanitized value.

    NOTE: SnowCMS currently supports the following datatypes:
            float - a number such as 1, 1.0, etc.
            float_array - an array containing floats, when all numbers inside are properly sanitized, implode with a comma.
            int - a integer.
            int_array - an array containing integers, implode using commas.
            raw - a string which will be put into the query, with nothing done to it.
            string - a string.
            string_array - an array containing strings, implode using commas.
            text - an alias of string.
            text_array - an alias of string_array.

    More information about databasing can be found at http://code.google.com/p/snowcms/wiki/Databasing

  */
  abstract protected function var_sanitize($var_name, $datatype, $value, $file, $line);

  /*

    All the following methods are helper methods of var_sanitize. They all get passed
    the variable name, value, file name and line number. These helper methods are expected
    to properly sanitize the value according to the variables datatype. If the value given
    is not able to be sanitized properly, you must call on the database method log_error
    fatally.

  */
  abstract protected function sanitize_float($var_name, $value, $file, $line);
  abstract protected function sanitize_float_array($var_name, $value, $file, $line);
  abstract protected function sanitize_int($var_name, $value, $file, $line);
  abstract protected function sanitize_int_array($var_name, $value, $file, $line);
  abstract protected function sanitize_string($var_name, $value, $file, $line);
  abstract protected function sanitize_string_array($var_name, $value, $file, $line);

  private function sanitize_raw($var_name, $value, $file, $line)
  {
    return $value;
  }

  /*

    Inserts or replaces data in the database. You can insert/replace multiple rows
    by having arrays inside the data array.

    @method public {Database_Result} insert(string $type, string $tbl_name, array $columns, array $data[, array $keys = array()]);
      string $type - The type of insert you want to perform, INSERT, IGNORE or REPLACE supported.
      string $tbl_name - The table name that the data will be inserted into.
      array $columns - An array containg the columns that will have data inserted into.
      array $data - The actual data to be inserted.
      array $keys - Some database types (Like PostgreSQL) do not support REPLACE, in order to fix that you must supply the column
                    names which are the primary/unique keys that way an UPDATE query can be attempted, if that fails, the data is inserted.
      string $hook_name - The hook to run (using run_hook in $api) BEFORE the anything is done, run_hook is to pass $type, $tbl_name, $columns,
                          $data and $keys as parameters.
    returns {Database_Result} - An object containing methods such as affected_rows(), etc.

    More information about databasing can be found at http://code.google.com/p/snowcms/wiki/Databasing

    NOTE: It is recommended for running the query through the database that you use the query method, simply pass the method the
          file and line the insert method was called on to query, also, set the db_compat parameter in query to insert, return
          query's result.

  */
  abstract public function insert($type, $tbl_name, $columns, $data, $keys = array(), $hook_name = null);

  /*

    Logs a database error into the SnowCMS error log, if the error is fatal, show a plain ol'
    ugly error page stating an error occurred.

    @method protected void log_error(string $error_message[, bool $is_fatal = false[, string $file = null[, int $line = 0]]]);
      string $error_message - The error message to log.
      bool $is_fatal - Whether or not the error that occurred means that SnowCMS can no longer continue running.
      string $file - The file that the method was called in that created the error.
      int $line - The line that the method was called on that created the error.
    returns void - Nothing is returned by this method.

  */
  public function log_error($error_message, $is_fatal = false, $file = null, $line = 0)
  {

    # !!! Error needs to be logged somewhere ;) If it can be, that is.
    # !!! Once member system is implemented, only show the file and line
    #     to administrators.

    # Fatal error..?
    if(!empty($is_fatal) && ($error_message === 1 || $error_message === 2))
    {
      die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex" />
	<title>Database connection issues</title>
</head>
<body>
  <h1>Database connection issues</h1>
  <p>Sorry for the inconvience, but SnowCMS could not connect to the database at this time. '. ($error_message == 1 ? 'This could be caused by the MySQL server being overloaded, down, or the supplied MySQL credentials are wrong.' : 'This was caused by having insufficient rights for the database.'). ' If this continues, please contact the server administrator if at all possible.</p>
</body>
</html>');
    }
    elseif(!empty($is_fatal))
    {
      die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex" />
	<title>Fatal database error</title>
</head>
<body>
  <h1>Fatal database error</h1>
  <p><strong>Database error</strong>: '. $error_message. ' '. ((!empty($file)) ? '<br /><strong>File</strong>: '. $file. '<br /><strong>Line</strong>: '. $line : 'If this continues, please contact the server administrator if at all possible.'). '</p>
</body>
</html>');
    }
  }

  /*

    The destructor writes all the debugging text to a file, if any, upon this object being destructed.

  */
  public function __destruct()
  {
    global $base_dir;

    $this->debug_text = trim($this->debug_text);

    if(!empty($this->debug_text))
    {
      $fp = fopen($base_dir. '/db_debug.sql', 'a');
      fwrite($fp, $this->debug_text);
      @fclose($fp);
    }
  }
}
?>