<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No direct access please :)
if(!defined('InSnow'))
  die;

class MySQL
{
  # Database resource contained in con
  private $con = null;

  # The table prefix :) Use {$db->prefix} in your queries!
  public $prefix = null;

  # The type of database..!
  public $type = 'MySQL';

  # Case sensitive or not? MySQL isn't, others are.
  public $case_sensitive = false;

  # Number of queries executed so far, which is none!
  public $num_queries = 0;

  # Does the database support DROP TABLE IF EXISTS?
  public $table_drop = true;

  # How about CREATE IF NOT EXISTS? Can it do that?
  public $table_ignore = true;

  # Are extended inserts supported too?
  public $extended_inserts = true;

  # How about debugging? You forcing it?
  private $debug = false;

  # Save sessions in the database? (Well, do you recommend it on this
  # database type, because like SQLite it isn't recommended :P)
  public $save_sessions = true;

  # These are all database functions the snow_error funciton should look
  # out for so it can be sure to get the proper file and line it was called in.
  public $functions = array(
    'mysql_affected_rows', 'mysql_data_seek', 'mysql_errno', 'mysql_error',
    'mysql_fetch_array', 'mysql_fetch_assoc', 'mysql_fetch_row',
    'mysql_free_result', 'mysql_insert_id', 'mysql_num_rows',
    'mysql_real_escape_string',
  );

  #
  # Connects to the database server. No parameters are expected
  # but it obtains the variables $db_host, $db_user, etc. to connect.
  #
  # bool connect();
  #
  # returns bool - TRUE on success, FALSE on failure.
  #
  public function connect()
  {
    global $db_host, $db_user, $db_pass, $db_name, $db_persistent, $tbl_prefix;

    # No persistent connection to the database?
    if(empty($db_persistent))
      $this->con = @mysql_connect($db_host, $db_user, $db_pass);
    else
      $this->con = @mysql_pconnect($db_host, $db_user, $db_pass);

    if(empty($this->con))
    {
      # Couldn't connect. Interesting.
      $this->db_log_error(1, true);
      return false;
    }
    elseif(!@mysql_select_db($db_name))
    {
      # Couldn't select the database? :0
      $this->db_log_error(2, true);
      return false;
    }

    # Sweet! Its all working swell!!!
    $this->prefix = $tbl_prefix;

    # Now set the time zone to UTC and a couple other UTF-8 things.
    mysql_query("SET time_zone = '+00:00', character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $this->con);

    return true;
  }

  #
  # Closes the connection to the MySQL server.
  #
  # bool close();
  #
  # returns bool - TRUE if the connection was successfully
  #                closed FALSE on failure, and NULL if no
  #                connection was present.
  #
  public function close()
  {
    if(!empty($this->con))
      return @mysql_close($this->con);
    else
      return null;
  }

  #
  # Moves the internal pointer to the supplied row number.
  #
  # bool data_seek(resource $db_result[, int $row_num = 0]);
  #
  # returns bool - TRUE on success, FALSE on failure.
  #
  # NOTE: $row_num starts at 0!
  #
  public function data_seek($db_result, $row_num = 0)
  {
    # Seek it!
    return mysql_data_seek($db_result, $row_num);
  }

  #
  # Returns the last error number from the most recently
  # called MySQL function.
  #
  # int errno();
  #
  # returns int - 0 is returned if no errors have occurred.
  #
  public function errno()
  {
    return mysql_errno(!empty($this->con) ? $this->con : null);
  }

  #
  # Returns the last error string from a SELECT, UPDATE,
  # INSERT, etc.
  #
  # string error();
  #
  # returns string - Returns the error string or null if none occurred.
  #
  public function error()
  {
    return mysql_error(!empty($this->con) ? $this->con : null);
  }

  #
  # Escapes a string and returns it so it can be safely included in a
  # query. If you choose to, it will return it with < > ' " and & changed
  # to their html entity counterpart. Which stops possible XSS, and HTML
  # at all :P
  #
  # string escape(string $str[, bool $htmlspecialchars = false]);
  #
  # returns string - Returns the sanitized string.
  #
  public function escape($str, $htmlspecialchars = false)
  {
    if(empty($this->con))
      return false;

    # Easy as pie :P
    return mysql_real_escape_string(!empty($htmlspecialchars) ? htmlspecialchars($str, ENT_QUOTES, 'UTF-8') : $str, $this->con);
  }

  #
  # Unescapes a string that has been escaped using escape()
  #
  # string unescape(string $str[, bool $decode_htmlspecialchars = false]);
  #
  # returns string - Returns the unsanitized string.
  #
  # NOTE: When taking a string that has been sanitized with escape() out
  #       of the database, it is automatically unescaped by the MySQL server.
  #       (But of course, htmlspecialchars is not ;)) This method is actually
  #       almost useless, but you never know!!!
  #
  public function unescape($str, $decode_htmlspecialchars = false)
  {
    # No connection needed, take that!
    $str = stripslashes($str);

    # Undo the change or not?
    return !empty($decode_htmlspecialchars) ? htmlspecialchars_decode($str, ENT_QUOTES) : $str;
  }

  #
  # array fetch_array(resource $db_result);
  #
  public function fetch_array($db_result)
  {
    return mysql_fetch_array($db_result);
  }

  #
  # array fetch_assoc(resource $db_result);
  #
  public function fetch_assoc($db_result)
  {
    return mysql_fetch_assoc($db_result);
  }

  #
  # array fetch_row(resource $db_result);
  #
  public function fetch_row($db_result)
  {
    return mysql_fetch_row($db_result);
  }

  #
  # bool free_result(resource $db_result);
  #
  public function free_result($db_result)
  {
    return mysql_free_result($db_result);
  }

  #
  # string version();
  #
  public function version()
  {
    static $version = null;

    if(empty($this->con))
      return false;

    # I could be wrong, but I don't think the MySQL version
    # will change in one page load :P
    if(empty($version))
    {
      $result = $this->query("
        SELECT VERSION()",
        array());
      list($version) = $this->fetch_row($result);
    }

    return $version;
  }

  #
  # int last_id([string $tbl_name = null[, string $field_name = null]]);
  #
  # NOTE: MySQL doesn't need any of those parameters, others do!!!
  #
  public function last_id($tbl_name = null, $field_name = null)
  {
    if(empty($this->con))
      return false;

    return mysql_insert_id($this->con);
  }

  #
  # array list_tables();
  #
  public function list_tables()
  {
    global $db_name;

    if(empty($this->con))
      return false;

    # Get them tables!
    $result = $this->query("
      SHOW TABLES FROM `%db_name`",
      array(
        'db_name' => array('raw', $db_name),
      ));

    # Load'em up!
    $tables = array();
    while($row = $this->fetch_row($result))
      $tables[] = $row[0];

    return $tables;
  }

  #
  # string backup_table_structure(string $tbl_name[, mixed $on_exists = false]);
  #
  public function backup_table_structure($tbl_name, $on_exists = false)
  {
    global $db_name;

    if(empty($this->con))
      return false;

    $result = $this->query("
      SHOW CREATE TABLE %tbl_name",
      array(
        'tbl_name' => array('raw', $tbl_name),
      ));

    @list(, $create_table) = $this->fetch_row($result);

    if(empty($create_table))
      return null;

    if($on_exists == 'drop')
      $create_table = "DROP TABLE IF EXISTS `$tbl_name`;\r\n$create_table;";
    elseif($on_exists == 'ignore')
      $create_table = 'CREATE TABLE IF NOT EXISTS'. mb_substr($create_table, 12). ';';
    else
      $create_table .= ';';

    # Give it to them! :P
    return $create_table;
  }

  #
  # array backup_table_data(string $table[, $extended_inserts = false]);
  #   string $table - A table's name to backup.
  #   bool $extended_inserts - Whether or not to use extended
  #                            inserts, if this engine didn't
  #                            support them, we would ignore
  #                            this argument, but MySQL does
  #                            support them.
  #   int $num_extended - The amount of rows to extend together
  #                       in the one extended insert before
  #                       breaking into another INSERT query.
  #   returns string - Returns SQL that could be run to recreate
  #                    the table's data, for backing up
  #                    purposes.
  #
  public function backup_table_data($table, $extended_inserts = false, $num_extended = 10)
  {
    global $db_name;
    
    # No output yet
    $output = '';
    
    # Get the amount of rows
    $result = $this->query('SELECT COUNT(*) FROM `%db_name`.%table', array('db_name' => array('raw', $db_name), 'table' => array('raw', $table)));
    list($num_rows) = $this->fetch_row($result);
    
    # Go through 500 rows at a time
    $offset = -1;
    while(++$offset < $num_rows / 500)
    {
      # Get the SQL for the table's data
      $result = $this->query('SELECT * FROM `%db_name`.%table LIMIT '. ($offset * 500). ', 500', array('db_name' => array('raw', $db_name), 'table' => array('raw', $table)));
      
      # Get the total amount of columns
      $num_columns = mysql_num_fields($result);
      
      # Get the columns
      $columns = array();
      for($i = 0; $i < $num_columns; $i++)
        $columns[] = '`'. mysql_field_name($result, $i). '`';
      
      # Let's see, are we not using extended inserts?
      if(!$extended_inserts)
      {
        # No exetended inserts, so let's get each row, one at a time
        $rows = array();
        while($row = $this->fetch_row($result))
        {
          # Escape the fields
          foreach($row as $key => $field)
            $row_escaped[$key] = $this->escape($field);
          
          # Add the row's SQL to the $rows array
          $rows[] = 'INSERT INTO `'. $db_name. '`.`'. $table. '` ('. implode(', ', $columns). ') VALUES (\''. implode('\', \'', $row_escaped). '\');';
        }
        
        # Join the rows together
        $output .= implode("\r\n", $rows). "\r\n";
      }
      else
      {
        # Extended inserts, eh? Keep going until an array is empty
        $row = true;
        while($row)
        {
          # Keep going until we run out of rows or have gotten to the maximum rows per INSERT (a.k.a. $num_extended)
          $i = 0;
          $rows = array();
          while($i++ < $num_extended && ($row = $this->fetch_row($result)))
          {
            # Escape the fields
            foreach($row as $key => $field)
              $row_escaped[$key] = $this->escape($field);
            
            # Join the fields together
            $rows[] = implode('\', \'', $row_escaped);
          }
          
          # If there was at least one row, join them together and add the SQL
          if($rows)
          {
            $output .= 'INSERT INTO `'. $db_name. '`.`'. $table. '` ('. implode(', ', $columns). ') VALUES'. "\r\n". '(\''. implode('\'),'. "\r\n". '(\'', $rows). '\');'. "\r\n";
          }
        }
      }
    }
    
    # Remove the last newline and return the output
    return mb_substr($output, 0, -2);
  }

  #
  # int num_rows(resource $db_result);
  #
  public function num_rows($db_result)
  {
    # I feel like such a tool!
    return mysql_num_rows($db_result);
  }

  #
  # int affected_rows();
  #
  public function affected_rows()
  {
    if(empty($this->con))
      return false;

    return mysql_affected_rows($this->con);
  }

  #
  # Queries the database, however it isn't a simple mysql_query
  # because it needs to allow SQL compatibility on other SQL
  # versions. Yes, all queries to the database should be MySQL
  # compatible and all SQL versions need to convert MySQL only
  # things to native (or remove it) support ;)
  #
  # resource query(string $db_query[, array $db_vars = array()]);
  #
  # returns resource - Returns a resource to be used with things like
  #                    fetch_assoc, fetch_array, etc.
  #
  # NOTE: Do not, I repeat DO NOT use this method for INSERT or REPLACE
  #       queries, that is what the method insert is for!
  #
  public function query($db_query, $db_vars = array(), $file = null, $line = 0)
  {
    global $base_dir;

    if(empty($this->con))
      return false;

    # Remove excess whitespace and what not.
    $db_query = trim($db_query);

$thing = strpos($db_query, 'el.error_id') !== false;


    /*
      In other database types (Like SQLite, PostgreSQL, SQL Server, anything not MySQL :P)
      this is where you would (should) do any touch ups on the query.

      Not that if you want (and you should :P) you can use a flag in $db_vars
      which contains anything special in the query, called compat. Such as
      $db_vars['compat'] could contain rand, in which case, the query contains
      RAND(), because in SQLite, it uses RANDOM() not RAND() and you would look
      for that instead of on EVERY query.
    */

    # So is compat set? Throw it out!!! MySQL doesn't need it :P
    if(isset($db_vars['compat']))
      unset($db_vars['compat']);

    # Same with debug, the MySQL class does use it, but for debugging
    # like ALL types should :P
    if(!empty($db_vars['debug']))
    {
      $debug = true;
      unset($db_vars['debug']);
    }

    # Get the file and line query was called :)
    $backtrace = debug_backtrace();
    $file = realpath($backtrace[0]['file']);
    $line = $backtrace[0]['line'];
    unset($backtrace);

    # If % is found, it could be a variable that needs parsing...
    if(mb_strpos($db_query, '%') !== false)
    {
      # Try to find them.
      preg_match_all('~%\w+~', $db_query, $matches);

      # Anything matched though?
      if(count($matches[0]))
      {
        # No need to do many replacements at once!!!
        $replacements = array();

        # Any undefined? Could be!
        $undefined = array();

        # Why do it more than once? You silly goose!
        $matches[0] = array_unique($matches[0]);

        foreach($matches[0] as $variable)
        {
          # Remove the %
          $variable_name = mb_substr($variable, 1, mb_strlen($variable));

          # Does it even exist?
          if(!isset($db_vars[$variable_name]))
          {
            $undefined[] = $variable_name;
            continue;
          }

          # Now we will need to get the right and sanitized value :P!!!
          $replacements[$variable] = $this->var_sanitize($variable_name, $db_vars[$variable_name][0], $db_vars[$variable_name][1], $file, $line);
        }

        # Don't replace anything if something couldn't be replaced!
        if(count($undefined))
          $this->db_log_error('Undefined database variables <em>'. implode('</em>, <em>', $undefined). '</em>.', true, $file, $line);

        # Now we can replace the variables!
        # If any though o.O
        if(count($replacements))
          $db_query = strtr($db_query, $replacements);
      }
    }

    # Wooo!!! QUERY THAT DATABASE!
    $time_started = microtime(true);
    $db_result = @mysql_query($db_query, $this->con);
    $time_took = round(microtime(true) - $time_started, 5);

    # One more query done.
    $this->num_queries++;

    # An error occur?
    if(empty($db_result))
    {
      # This way we don't get it twice ;)
      $query_error = $this->errno(). ': '. $this->error();

      $this->db_log_error($query_error, false, $file, $line);
    }

    # Want to debug everything, or this query?
    if(!empty($debug) || !empty($this->debug))
    {
      # Open the file, or create it :)
      $fp = fopen($base_dir. '/db_debug.sql', 'a');

      if($fp)
      {
        flock($fp, LOCK_EX);
        fwrite($fp, "Query:\r\n$db_query\r\nFile: $file\r\nLine: $line\r\nExecuted in $time_took seconds\r\nError: ". (!empty($query_error) ? $query_error : 'None'). "\r\n\r\n");
        flock($fp, LOCK_UN);
        fclose($fp);
      }
    }

    return $db_result;
  }

  #
  # mixed var_sanitize(string $var_name, string $datatype, mixed $value);
  #
  private function var_sanitize($var_name, $datatype, $value, $file = null, $line = 0)
  {
    $datatype = mb_strtolower($datatype);

    # Is it string? Might have a length.
    if(mb_substr($datatype, 0, 6) == 'string' && mb_strpos($datatype, '-') !== false)
    {
      @list(, $length) = explode('-', $datatype);
      $datatype = 'string';
      $value = substr($value, 0, (int)$length);
    }

    $datatypes = array(
      'float' => 'sanitize_float',
      'float_array' => 'sanitize_float_array',
      'int' => 'sanitize_int',
      'int_array' => 'sanitize_int_array',
      'raw' => 'sanitize_raw',
      'string' => 'sanitize_string',
      'string_array' => 'sanitize_string_array',
      'text' => 'sanitize_string',
      'text_array' => 'sanitize_string_array',
    );

    # Data type defined..?
    if(!isset($datatypes[$datatype]))
      $this->db_log_error('Undefined data type <strong>'. mb_strtoupper($datatype). '</strong>.', true, $file, $line);

    # Return the sanitized value!!! If at all possible, of course.
    return $this->$datatypes[$datatype]($value, $var_name, $file, $line);
  }

  #
  # !!!
  #
  private function sanitize_float($value, $var_name, $file = null, $line = 0)
  {
    # Gotta be a float!!!
    if((string)$value !== (string)(float)$value)
      $this->db_log_error('Wrong data type, float expected ('. $var_name. ')', false, $file, $line);

    return (string)(float)$value;
  }

  private function sanitize_float_array($value, $var_name, $file = null, $line = 0)
  {
    # Not an array? Well then! It can't be an array of floats, can it?!?
    if(!is_array($value))
      $this->db_log_error('Wrong data type, array expected ('. $var_name. ')', false, $file, $line);

    $new_value = array();
    if(count($value))
      foreach($value as $v)
        $new_value[] = $this->sanitize_float($v, $var_name, $file, $line);

    return implode(', ', $new_value);
  }

  private function sanitize_int($value, $var_name, $file = null, $line = 0)
  {
    # Mmmm, inty!
    if((string)$value !== (string)(int)$value)
      $this->db_log_error('Wrong data type, integer expected ('. $var_name. ')', false, $file, $line);

    return (string)(int)$value;
  }

  private function sanitize_int_array($value, $var_name, $file = null, $line = 0)
  {
    if(!is_array($value))
      $this->db_log_error('Wrong data type, array expected ('. $var_name. ')', false, $file, $line);

    $new_value = array();
    if(count($value))
      foreach($value as $v)
        $new_value[] = $this->sanitize_int($v, $var_name, $file, $line);

    return implode(', ', $new_value);
  }

  private function sanitize_raw($value, $var_name, $file = null, $line = 0)
  {
    return $value;
  }

  private function sanitize_string($value, $var_name, $file = null, $line = 0)
  {
    # Check if its a string? Pffft. :P
    return '\''. $this->escape($value). '\'';
  }

  private function sanitize_string_array($value, $var_name, $file = null, $line = 0)
  {
    if(!is_array($value))
      $this->db_log_error('Wrong data type, array expected ('. $var_name. ')', false, $file, $line);

    $new_value = array();
    if(count($value))
      foreach($value as $v)
        $new_value[] = $this->sanitize_string($v, $var_name, $file, $line);

    return implode(', ', $new_value);
  }

  #
  # Inserts or replaces data in the database. You can insert/replace multiple rows
  # by having arrays inside the data array.
  #
  # bool insert(string $insert_type, string $tbl_name, array $columns, array $data, array $keys);
  #
  # returns bool - TRUE if all were inserted/replaced successfully, FALSE on failure.
  #
  public function insert($insert_type, $tbl_name, $columns, $data, $keys, $file = null, $line = 0)
  {
    global $base_dir;

    if(empty($this->con))
      return false;

    $insert_type = mb_strtolower($insert_type);

    # We can only support the types we defined, silly! :P
    if(!in_array($insert_type, array('insert', 'replace', 'ignore')))
      $this->db_log_error('Unknown insert type '. $insert_type, true, $file, $line);

    # You inserting multiple things to the database..?
    if(!is_array($data[array_rand($data)]))
      # No, but we will make it one! >:D
      $data = array($data);

    # Number of columns? XD!
    $num_columns = count($columns);

    # Get the column names, used later.
    $column_names = array_keys($columns);

    # One more thing though, WHO CALLED ME?
    $backtrace = debug_backtrace();
    $file = realpath($backtrace[0]['file']);
    $line = $backtrace[0]['line'];
    unset($backtrace);

    # Start to get it all prepared!!!
    $rows = array();
    foreach($data as $row_index => $row)
    {
      # Oh noes, not enough dataz?!?
      if($num_columns != count($row))
        $this->db_log_error('Number of columns defined doesn\'t match row index #'. $row_index. '.', true, $file, $line);

      # Now this rows values :D!!!
      $values = array();
      foreach($row as $index => $value)
        $values[] = $this->var_sanitize($column_names[$index], $columns[$column_names[$index]], $value);

      # Now add it to all our rows :D
      $rows[] = '('. implode(', ', $values). ')';
    }

    # Just a little somethin' somethin'
    $inserts = array(
      'ignore' => 'INSERT IGNORE',
      'insert' => 'INSERT',
      'replace' => 'REPLACE',
    );

    # Construct the query. MySQL supports extended inserts! Hip hip! HURRAY!
    $db_query = $inserts[$insert_type]. ' INTO '. $tbl_name. ' (`'. implode('`, `', $column_names). '`) VALUES'. implode(', ', $rows);

    # Execute that query!!!
    $time_started = microtime(true);
    $db_result = @mysql_query($db_query, $this->con);
    $time_took = round(microtime(true) - $time_started, 5);

    # That's another query, yup yup!
    $this->num_queries++;

    # Uh oh! Did something go wrong? :0
    if(empty($db_result))
    {
      $query_error = $this->errno(). ': '. $this->error();
      $this->db_log_error($query_error, false, $file, $line);
    }

    # Debugging? Yessir!
    # Want to debug everything, or this query?
    if(!empty($debug) || !empty($this->debug))
    {
      # Open the file, or create it :)
      $fp = fopen($base_dir. '/db_debug.sql', 'a');

      if($fp)
      {
        flock($fp, LOCK_EX);
        fwrite($fp, "Query:\r\n$db_query\r\nFile: $file\r\nLine: $line\r\nExecuted in $time_took seconds\r\nError: ". (!empty($query_error) ? $query_error : 'None'). "\r\n\r\n");
        flock($fp, LOCK_UN);
        fclose($fp);
      }
    }

    # Success or not?
    return !empty($db_result);
  }

  #
  # !!!
  #
  private function db_log_error($error_message, $is_fatal = false, $file = null, $line = 0)
  {
    global $source_dir, $user;
    
    echo $error_message. ' in <strong>'. $file. '</strong> on line <strong>'. $line. '</strong><br />';
    
    if(!function_exists('errors_handle'))
      require_once($source_dir. '/errors.php');

    # Only log the error if we are connected... :P DUH!
    if(!empty($this->con) && ($error_message !== 1 || $error_message !== 2) && function_exists('errors_handle'))
      errors_handle('database', $error_message, $file, $line);

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
  <p>Sorry for the inconvience, but SnowCMS could not connect to the database. '. ($error_message == 1 ? 'This could be caused by the MySQL server being overloaded or down, but also the MySQL connection credentials could be wrong.' : 'This was caused by having insufficient rights for the database.'). ' If this continues, please contact the server administrator if at all possible.</p>
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
  <p><strong>Database error</strong>: '. $error_message. ' '. ((!empty($user['is_admin']) && !empty($file)) ? '<br /><strong>File</strong>: '. $file. '<br /><strong>Line</strong>: '. $line : 'If this continues, please contact the server administrator if at all possible.'). '</p>
</body>
</html>');
    }
  }

  #
  # bool optimize_table(string $tbl_name);
  #
  public function optimize_table($tbl_name)
  {
    global $db_name;

    # So all or not..?
    if($tbl_name === true)
    {
      # So get the tables.
      $tables = $this->list_tables();

      # Loop through and optimize ;)
      foreach($tables as $table)
        $this->optimize_table($table);

      # Just say it worked :P
      return true;
    }
    else
    {
      # Optimize the table, and thats about it.
      return $this->query("
        OPTIMIZE TABLE `%db_name`.`%tbl_name`",
        array(
          'db_name' => array('raw', $db_name),
          'tbl_name' => array('raw', $tbl_name)
        ));
    }
  }
}

# The database class contained within!
$db_class = 'MySQL';
?>