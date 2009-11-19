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

class SQLite
{
  # Holds our connection handle...
  private $db_con = null;
  # The database prefix... use $db->prefix in your queries.
  public $prefix = null;
  # The SQL Name...
  public $type = 'SQLite';
  # Is it case sensitive? Sadly, yes.
  public $case_sensitive = true;
  # Number of queries executed.
  public $num_queries = 0;
  # SQLite only requires VACUUM once... We don't want to kill it!
  public $optimize_once = true;
  # Is the drop table functionality present?
  public $table_drop = false;
  # Is the creating tables only if they don't already exist functionlity included?
  public $table_ignore = false;
  # Are exteneded inserts allowed?
  public $extended_inserts = false;
  # Force SQL Debugging..?
  private $sql_debug = false;
  # Save session to the database? (Well, is it recommended... It is NOT on SQLite!!! BAD!!!)
  public $save_sessions = false;

  #
  # bool connect();
  #
  public function connect()
  {
    # We don't need everything with SQLite ;)
    global $db_name, $db_persistent, $tbl_prefix;
    
    # If there isn't a database, error time
    if(!file_exists($db_name))
      return false;
    
    # Persistent or not?
    if(!empty($db_persistent))
      $this->db_con = @sqlite_popen($db_name, 0666, $con_message);
    else
      $this->db_con = @sqlite_open($db_name, 0666, $con_message);

    # So did we connect or not?
    if(!$this->db_con)
    {
      # Something went wrong :(
      $this->db_con = null;
      return false;
    }

    # Nothing has gone wrong... yet. Set the prefix
    # which is just the table prefix...
    $this->prefix = $tbl_prefix;

    # We need to make a few SQLite functions...
    sqlite_create_function($this->db_con, 'find_in_set', 'sqlite_udf_find_in_set', 2);
    sqlite_create_function($this->db_con, 'unix_timestamp', 'sqlite_udf_unix_timestamp', 0);
    sqlite_create_function($this->db_con, 'sha1', 'sqlite_udf_sha1', 1);
    sqlite_create_function($this->db_con, 'md5', 'sqlite_udf_md5', 1);

    # For some reason, aliases stay in column names ._.
    @sqlite_query('PRAGMA short_column_names = 1', $this->db_con);

    # Thats about it...
    return true;
  }

  #
  # bool close();
  #
  public function close()
  {
    # Yeah... you need a connection.
    if(!empty($this->db_con))
    {
      @sqlite_close($this->db_con);
      # The sqlite_close function returns nothing...
      return true;
    }
    else
      return false;
  }

  #
  # bool data_seek(resource $db_result, int $row_num);
  #
  public function data_seek($db_result, $row_num = 0)
  {
    # Move the pointer.
    return @sqlite_seek($db_result, (int)$row_num);
  }

  #
  # int errno();
  #
  public function errno()
  {
    # Return the last error number...
    if($this->db_con)
      return sqlite_last_error($this->db_con);
    else
      return null;
  }

  #
  # string error();
  #
  public function error()
  {
    global $db_name;

    # If there isn't a database, we'll make a custom error
    if(!file_exists($db_name))
    {
      return 'Unknown database \''. $db_name. '\'';
    }
    else
    {
      # Get and return the last error.
      return sqlite_error_string($this->errno());
    }
  }

  #
  # string escape(string $str[, bool $htmlspecialchars = false]);
  #
  public function escape($str, $htmlspecialchars = false)
  {
    # htmlspecialchars?
    if(!empty($htmlspecialchars))
      $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');

    # Hmm, lets escape it... Using SQLite's special one :P
    return @sqlite_escape_string($str);
  }

  #
  # string unescape(string $str[, bool $decode_htmlspecialchars = false]);
  #
  public function unescape($str, $decode_htmlspecialchars = false)
  {
    # Decode this first... Though SQLite unescapes automatically :P
    $str = strtr($str, array('\'\'' => '\''));

    # Decode htmlspecialchars? Not recommended to do this
    # if you want to stop XSS :P
    if(!empty($decode_htmlspecialchars))
      $str = htmlspecialchars_decode($str, ENT_QUOTES);

    # Of course we must return it!
    return $str;
  }

  #
  # array fetch_array(resource $db_result);
  #
  public function fetch_array($db_result)
  {
    # Return the array, both numerical and associative.
    return @sqlite_fetch_array($db_result);
  }

  #
  # array fetch_assoc(resource $db_result);
  #
  public function fetch_assoc($db_result)
  {
    # SQLite only has 1 fetching function, sort of :P
    return @sqlite_fetch_array($db_result, SQLITE_ASSOC);
  }

  #
  # array fetch_row(resource $db_result);
  #
  public function fetch_row($db_result)
  {
    # Numerical only!
    return @sqlite_fetch_array($db_result, SQLITE_NUM);
  }

  #
  # bool free_result(resource $db_result);
  #
  public function free_result($db_result)
  {
    # SQLite doesn't have one...
    return true;
  }

  #
  # string version();
  #
  public function version()
  {
    # What version are you Oh SQLite?
    return @sqlite_libversion();
  }

  #
  # int last_id(string $tbl_name = null);
  #
  public function last_id($tbl_name = null)
  {
    # Yes, Mr. Silly Pants we need a connection.
    if(!empty($this->db_con))
    {
      # Just to let you know, we don't need tbl_name
      # Its just for other SQL languages.
      return @sqlite_last_insert_rowid($this->db_con);
    }
    else
      return false;
  }

  #
  # array list_tables();
  #
  public function list_tables()
  {
    # Yes, a connection is needed as well.
    if(!empty($this->db_con))
    {
      # Select the tables from the SQLite Master table.
      $request = $this->query('SELECT tbl_name FROM sqlite_master WHERE type = \'table\'', array());
      # Our tables array, of course.
      $db_tables = array();
      while($row = $this->fetch_row($request))
        # Add the table to the array...
        $db_tables[] = $row[0];

      # Return the tables :)
      return $db_tables;
    }
    else
      return false;
  }

  #
  # string backup_table_structure(string $table);
  #   string $table - A table's name to backup.
  #   bool $onexists - If 'drop', drops the table if it exists,
  #                    if 'ignore', doesn't drop or recreate it.
  #                    But SQLite doesn't support it, so we just
  #                    ignore it.
  #   returns string - Returns SQL that could be run to recreate
  #                    the table's structure, for backing up
  #                    purposes.
  #
  public function backup_table_structure($table, $onexists = false)
  {
    global $db_name;
    
    static $tables = null;
    
    if(is_null($tables))
    {
      # Get the SQL for the table's structure
      $result = $this->query('SELECT sql FROM sqlite_master');
      
      while($row = $this->fetch_row($result))
      {
        $tables[mb_substr($row[0], 14, mb_strpos(mb_substr($row[0], 14), '\''))] = $row[0];
      }
    }
    
    return isset($tables[$table]) ? $tables[$table]. ';' : '';
  }

  #
  # array backup_table_data(string $table[, $extended_inserts = false]);
  #   string $table - A table's name to backup.
  #   bool $extended_inserts - Whether or not to use extended
  #                            inserts. But SQLite doesn't
  #                            support them, so we ignore this
  #                            argument.
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
    $result = $this->query('SELECT COUNT(*) FROM %table', array('table' => array('raw', $table)));
    list($num_rows) = $this->fetch_row($result);
    
    # Go through 500 rows at a time
    $offset = -1;
    while(++$offset < $num_rows / 500)
    {
      # Get the SQL for the table's data
      $result = $this->query('SELECT * FROM %table LIMIT '. ($offset * 500). ', 500', array('table' => array('raw', $table)));
      
      # Get the total amount of columns
      $num_columns = sqlite_num_fields($result);
      
      # Get the columns
      $columns = array();
      for($i = 0; $i < $num_columns; $i++)
        $columns[] = '\''. sqlite_field_name($result, $i). '\'';
      
      # No exetended inserts, so let's get each row, one at a time
      $rows = array();
      while($row = $this->fetch_row($result))
      {
        # Escape the fields
        foreach($row as $key => $field)
          $row_escaped[$key] = sqlite_escape_string($field);
        
        # Add the row's SQL to the $rows array
        $rows[] = 'INSERT INTO \''. $table. '\' ('. implode(', ', $columns). ') VALUES (\''. implode('\', \'', $row_escaped). '\');';
      }
      
      # Join the rows together
      $output .= implode("\r\n", $rows). "\r\n";
    }
    
    # Remove the last newline and return the output
    return mb_substr($output, 0, -2);
  }

  #
  # int num_rows(resource $db_result);
  #
  public function num_rows($db_result)
  {
    # Just return it... Easy squeezy lemon peezy :P
    return @sqlite_num_rows($db_result);
  }

  #
  # int affected_rows();
  #
  public function affected_rows()
  {
    # Yes... you guessed it.
    if(!empty($this->db_con))
      # return... ^(._.)^
      return sqlite_changes($this->db_con);
    else
      return false;
  }

  #
  # resource query(string $db_query, array $dbVars[, string $file = ''[, int $line = 0]]);
  #
  public function query($db_query, $dbVars, $file = '', $line = 0)
  {
    # Oh yeah, we sure do need a connection!
    if(!empty($this->db_con))
    {
      # Just trim it up, just 'coz.
      $db_query = trim($db_query);

      # SQLite does have an UPDATE IGNORE, but it is UPDATE OR IGNORE :P
      if(stripos($db_query, 'UPDATE IGNORE') !== false)
        $db_query = str_ireplace('UPDATE IGNORE', 'UPDATE OR IGNORE', $db_query);

      # SQLite is naughty and doesn't like LIMIT's in UPDATE queries.
      # We'll just show SQLite won't we! ^_^ Same with DELETE's
      if((preg_match('~UPDATE(?:.*?)SET~is', $db_query) == 1 || mb_stripos($db_query, 'DELETE FROM') === 0) && preg_match('~(?:LIMIT\s+(?:\d+|%.*)(?:\s*,\s*(?:\d+|%.*))?)~i', $db_query, $matches) == 1)
        $db_query = str_replace($matches[0], ' ', $db_query);

      # We might need to do some stuff... For SQLite Compatibility.
      if(preg_match('~GROUP BY (.*?) (?:ASC|DESC)~', $db_query, $matches))
      {
        # SQLite does not support having ASC or DESC in GROUP BY's ._.
        $db_query = str_replace($matches[0], str_replace(array('ASC','DESC'), '', $matches[0]), $db_query);
      }
      elseif(mb_stripos($db_query, 'TRUNCATE') !== false)
      {
        # SQLite has no support for TRUNCATE, so DELETE FROM 
        # will have to do the job...
        $db_query = str_ireplace('TRUNCATE', 'DELETE FROM', $db_query);
      }

      # RAND() needs to turn into RANDOM()
      if(preg_match('~RAND\((?:.*?)\)~', $db_query, $matches))
      {
        # We choose not to get the number between RAND() (If any) since
        # SQLite doesn't allow you to seed the random.
        $db_query = str_replace($matches[0], 'RANDOM()', $db_query); 
      }

      # Can we see if there are any variables..?
      if(mb_strpos($db_query, '%') !== false)
      {
        # Hmm, just possibly. But lets check, shall we?
        preg_match_all('~%\w+~', $db_query, $matches);

        # If we got any, we got some :P
        if(count($matches[0]))
        {
          # Okay, now we need to replace them.
          foreach($matches[0] as $dbVar)
          {
            # Call on a useful (private) method which does the work :)
            $db_query = str_replace($dbVar, $this->varReplace(mb_substr($dbVar, 1, mb_strlen($dbVar)), $dbVars, $file, $line), $db_query);
          }
        }
      }

      # So are you ready?!?! I AM! :D IM PUMPED! Lol.
      $time_started = microtime(true);
      $request = @sqlite_query($db_query, $this->db_con, SQLITE_BOTH, $query_error);
      $time_took = round(microtime(true) - $time_started, 5);
      
      # Thats one more query.
      $this->num_queries++;

      # So was it a success..?
      if(!$request)
        # Nope, it was not...
        $this->db_log_error('SQLite Error: '. $query_error, $file, $line);

      # Debug?
      if(!empty($dbVars['debug']) || !empty($this->sql_debug))
      {
        $fp = @fopen(dirname(__FILE__). '/../../debug.sql', 'a');
        if($fp)
        {
          flock($fp, LOCK_EX);
          fwrite($fp, "Query:\r\n$db_query\r\nFile: $file\r\nLine: $line\r\nExcution time: $time_took\r\nError (If any): $query_error\r\n\r\n");
          flock($fp, LOCK_UN);
          @fclose($fp);
        }
      }

      # Return the result, whether it failed or not.
      return $request;
    }
    else
      return false;
  }

  #
  # string varReplace(string $dbVar, array $dbVars[, string $file = null[, int $line = 0]]);
  #
  private function varReplace($dbVar, $dbVars, $file = null, $line = 0, $override = false, $var_name = null)
  {
    # First off, is this variable defined..?
    if(!isset($dbVars[$dbVar]) && !$override)
      # Oh noes! Its fatal too!
      $this->db_log_error('Undefined database variable '. $dbVar, $file, $line, true);
    # Is it like, valid?
    elseif(isset($dbVars[$dbVar]) && count($dbVars[$dbVar]) != 2 && !$override)
      # Fatal as well.
      $this->db_log_error('Invalid database variable declaration ('. $dbVar. '), not enough parameters.', $file, $line, true);

    # Nothing went wrong, its peachie! Lol.
    # So lets see, what type of variable is this?
    $var_type = mb_strtolower($override ? $dbVar : $dbVars[$dbVar][0]);
    # Hold on, could be a string with a length ;)
    if(mb_substr($var_type, 0, 6) == 'string' && mb_strpos($var_type, '-') !== false)
    {
      # Get the maximum length!
      $max_length = mb_substr($var_type, 7, mb_strlen($var_type));
      # Now set the data type to string.
      $var_type = 'string';
    }

    # We need the variable value :)
    $var_value = $override ? $dbVars : $dbVars[$dbVar][1];

    # In override? Set the right var name...
    if($override)
      $dbVar = $var_name;

    # Just because you gave us a data type doesn't mean its valid :P
    if(in_array($var_type, array('string','text','int','float','raw','int_array','string_array')))
    {
      # String..? Ok, or text :P
      if($var_type == 'string' || $var_type == 'text')
      {
        # Sweet, its a string! Maximum length..?
        if(isset($max_length))
          $var_value = mb_substr($var_value, 0, $max_length);

        # Escape and return... not much to it.
        return '\''. $this->escape($var_value, false). '\'';
      }
      elseif($var_type == 'int')
      {
        # So lets see... Is this right?
        if(!is_numeric($var_value) || (string)$var_value !== (string)(int)$var_value)
          # Nope... its not fatal, but lets just flag them :)
          $this->db_log_error('Wrong data type, integer expected ('. $dbVar. ')', $file, $line);

        # Return the value, all squeky clean :)
        return (string)(int)$var_value;
      }
      elseif($var_type == 'float')
      {
        # So lets see... is it a float...
        if(!is_numeric($var_value) || (string)$var_value !== (string)(float)$var_value)
          # No... not fatal though...
          $this->db_log_error('Wrong data type, float expected ('. $dbVar. ')', $file, $line);

        # Return the value clean and what not.
        return (string)(float)$var_value;
      }
      elseif($var_type == 'raw')
      {
        # Just raw..? Straight into the query..? All right...
        return $var_value;
      }
      elseif($var_type == 'int_array')
      {
        # Is this an array..?
        if(!is_array($var_value))
          # Fatal!
          $this->db_log_error('Wrong data type, array expected ('. $dbVar. ')', $file, $line, true);

        # So we need an array to hold the clean values.
        $values = array();

        # Anything though? o.O
        if(count($var_value))
          foreach($var_value as $value)
          {
            # It has to be an integer!
            if(!is_numeric($value) || (string)$value !== (string)(int)$value)
              $this->db_log_error('Wrong data type, integer expected ('. $dbVar. ')', $file, $line);

            # So sanitize and add it to the array.
            $values[] = (string)(int)$value;
          }

        # Implode the values and return.
        return implode(', ', $values);
      }
      elseif($var_type == 'string_array')
      {
        # It has to be an array!
        if(!is_array($var_value))
          # Fatal D:
          $this->db_log_error('Wrong data type, array expected ('. $dbVar. ')', $file, $line, true);

        # We must hold the cleaned up values...
        $values = array();

        # But of course, only if anything is in the array.
        if(count($var_value))
          foreach($var_value as $value)
          {
            # Just add it to the array all clean and what not :P
            $values[] = $this->escape($value, false);
          }

        # Okie dokie... return it imploded...
        return '\''. implode('\', \'', $values). '\'';
      }
    }
    else
      # Oh noes! Unknown data type :X
      $this->db_log_error('Unknown data type '. $var_type, $file, $line, true);
  }

  #
  # bool insert(string $type, string $tbl_name, array $columns, array $data, array $keys[, string $file = null[, int $line = 0]]);
  #
  public function insert($type, $tbl_name, $columns, $data, $keys, $file = null, $line = 0)
  {
    # A connection is needed Mr. Silly Pants :)
    if(!empty($this->db_con))
    {
      # Lower it :)
      $type = mb_strtolower($type);

      # Is the type valid..?
      if(!in_array($type, array('insert','replace','ignore')))
        # Yes its fatal! We don't know what to do! D:
        $this->db_log_error('Unknown insertion type '. $type, $file, $line, true);

      # Still going? Nothing must have gone wrong... yet. ^(._.)^

      # Check to see if this is a multiple insert :D
      if(!is_array($data[array_rand($data)]))
        # Shove it into an array...
        $data = array($data);

      # Get all the column names...
      $colNames = array_keys($columns);

      # Get the data types...
      $colTypes = array();
      foreach($columns as $colType)
        $colTypes[] = $colType;

      # The number of columns, useful... :)
      $num_columns = count($colNames);

      # So lets start making the values.
      $row_values = array();
      foreach($data as $rowNum => $row)
      {
        # Lets check to see if this row has as many columns...
        if($num_columns != count($row))
          # Nope... fatal error.
          $this->db_log_error('Column count doesn\'t match value count at row '. ($rowNum + 1), $file, $line, true);

        # Cool, if we are still going, thats good :P lets sanitize...
        $values = array();
        foreach($row as $index => $value)
          $values[] = $this->varReplace($colTypes[$index], $value, $file, $line, true, $colNames[$index]);

        # Now implode and add to the row values array... which we will use later.
        $row_values[] = '('. implode(', ', $values). ')';
      }

      # So lets start to build the query...
      # Though SQLite doesn't support extended inserts
      # :( so we need to do individual ones...
      # So hold whether its a success... (1 Failure == All Failures)
      $query_failed = false;
      foreach($row_values as $row_value)
      {
        # Make it...
        $db_query = ($type == 'replace' ? 'REPLACE' : ($type == 'insert' ? 'INSERT' : 'INSERT OR IGNORE')). ' INTO '. $tbl_name. ' (\''. implode('\',\'', $colNames). '\') VALUES'. $row_value;

        # So we have the query built, we can execute it ;)
        $request = @sqlite_query($db_query, $this->db_con, SQLITE_BOTH, $query_error);

        # One more query!
        $this->num_queries++;

        # So was it a success..?
        if(!$request)
        {
          # Nope, it was not...
          $query_failed = true;
          $this->db_log_error('SQLite Error: '. $query_error, $file, $line);
        }

        # Debug?
        if(!empty($dbVars['debug']) || !empty($this->sql_debug))
        {
          $fp = @fopen(dirname(__FILE__). '/../../debug.sql', 'a');
          if($fp)
          {
            flock($fp, LOCK_EX);
            fwrite($fp, "Query:\r\n$db_query\r\nFile: $file\r\nLine: $line\r\nExcution time: $time_took\r\nError (If any): ". $this->error(). "\r\n\r\n");
            flock($fp, LOCK_UN);
            @fclose($fp);
          }
        }
      }

      # Return whether or not if the query was a success!
      return !$query_failed;
    }
    else
      return false;
  }

  #
  # void db_log_error(string $error[, string $file = null[, int $line = 0[, bool $fatal = false]]]);
  #
  private function db_log_error($error, $file = null, $line = 0, $fatal = false)
  {
    global $source_dir, $user;

    # Does our error logging function not exist? Get it!
    if(!function_exists('errors_handle'))
      require_once($source_dir. '/errors.php');

    # Log the error, and that is about it...
    errors_handle('database', $error, $file, $line);

    # But wait, is it fatal..?
    if($fatal && $user['is_admin'])
      # If they are an admin, we can be flat out :P
      die('<p style="font-size: 12px; font-family: Verdana;"><strong>Fatal Database Error</strong>: '. $error);
    elseif($fatal)
      die('<p style="font-size: 12px; font-family: Verdana;"><strong>Fatal Database Error</strong>: If this error continues, please contact the Administrator.');
  }

  #
  # bool optimize_table(mixed $tbl_name);
  #
  public function optimize_table($tbl_name)
  {
    static $optimized = false;

    # The tbl_name var is just a dummy... You can't optimize tables
    # individually in SQLite :P

    # So have we optimized already? This is a fix so the database
    # doesn't get hammered with optimize requests, because its not
    # really light on resources because it makes a temporary database,
    # copies it over, then puts it back into the original one removing
    # unnecessary space.
    if(!$optimized)
    {
      # So VACUUM!
      $this->query('VACUUM', array());

      # So we have done this, so don't do it again :P
      $optimized = true;
    }

    # It was a success?
    return true;
  }
}

#
# sqlite_udf_find_in_set();
#   - Since SQLite doesn't have this function, we must
#     emulate it ourselves with PHP ;)
#
function sqlite_udf_find_in_set($find, $list)
{
  # Explode the list at @
  $list = explode(',', $list);

  # So is it in the list?
  foreach($list as $item)
    if(trim($item) == $find)
      return true;

  # Sorry, nothing!
  return false;
}

#
# sqlite_udf_unix_timestamp();
#   - SQLite also doesn't have UNIX_TIMESTAMP()...
#     though its simple to emulate :P
#
function sqlite_udf_unix_timestamp()
{
  # Lol... see?
  return time_utc();
}

#
# sqlite_udf_sha1();
#   - SQLite doesn't have a built in SHA1 function...
#
function sqlite_udf_sha1($str)
{
  return sha1($str);
}

#
# sqlite_udf_md5();
#   - Same goes for MD5...
#
function sqlite_udf_md5($str)
{
  return md5($str);
}

# The database class name...
$db_class = 'SQLite';
?>