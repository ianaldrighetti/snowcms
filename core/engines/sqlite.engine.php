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

class SQLite extends Database
{
  public function connect()
  {
    // Make it persistent, or not?
    if(!defined('dbpersist') || !dbpersist)
    {
      $this->con = @sqlite_open(dbhost);
    }
    else
    {
      $this->con = @sqlite_popen(dbhost);
    }

    // Couldn't open the database?
    if(empty($this->con))
    {
      $this->log_error(1, true);
      return false;
    }

    // All done, set a couple things, though.
    $this->prefix = tblprefix;
    $this->type = 'SQLite v2';

    // SQLite is case sensitive.
    $this->case_sensitive = true;

    // It doesn't support DROP IF EXISTS or IF NOT EXISTS, nor extended inserts...
    $this->drop_if_exists = false;
    $this->if_not_exists = false;
    $this->extended_inserts = false;

    // Set short columns on (Otherwise when you do joins, the table alias
    // is prepended in the column name...)
    @sqlite_query('PRAGMA short_column_names = 1', $this->con);

    return true;
  }

  public function close()
  {
    // SQLite's closing function returns nothing...
    if(!empty($this->con))
    {
      @sqlite_close($this->con);
      return true;
    }
    else
    {
      return false;
    }
  }

  public function errno()
  {
    return @sqlite_last_error($this->con);
  }

  public function error()
  {
    return @sqlite_error_string($this->errno());
  }

  public function escape($str, $htmlspecialchars = false)
  {
    global $func;

    return sqlite_escape_string(!empty($htmlspecialchars) ? $func['htmlspecialchars']($str) : $str);
  }

  public function unescape($str, $htmlspecialchars_decode = false)
  {
    global $func;

    // Convert '' to '
    $str = str_replace('\'\'', '\'', $str);

    return !empty($htmlspecialchars_decode) ? $func['htmlspecialchars_decode']($str) : $str;
  }

  public function version()
  {
    if(empty($this->con))
    {
      return false;
    }

    // Easy enough...
    return sqlite_libversion();
  }

  public function tables()
  {
    if(empty($this->con))
    {
      return false;
    }

    // The master table contains a list of tables and indexes.
    $result = $db->query('
      SELECT
        tbl_name
      FROM sqlite_master WHERE type = \'table\'');

    // Load'em up!
    $tables = array();
    while($row = $result->fetch_row())
    {
      $tables[] = $row[0];
    }

    return $tables;
  }

  public function columns($table)
  {
    if(empty($this->con))
    {
      return false;
    }

    // !!! TODO
    return false;
  }

  public function query($db_query, $db_vars = array(), $hook_name = null, $db_compat = null, $file = null, $line = 0)
  {
    if(empty($this->con))
    {
      return false;
    }

    $return = null;
    api()->run_hooks('pre_parse_query', array(&$db_query, &$db_vars, &$hook_name, &$db_compat, &$file, &$line, &$return));

    if($return !== null)
    {
      return $return;
    }

    // Just incase, you want to change something.
    if(!empty($hook_name))
    {
      $return = null;
      api()->run_hooks($hook_name, array(&$db_query, &$db_vars, &$hook_name, &$db_compat, &$file, &$line, &$return));

      if($return !== null)
      {
        return $return;
      }
    }

    // Debugging?
    if(isset($db_vars['debug']))
    {
      $prev_debug = $this->debug;

      $this->debug = !empty($db_vars['debug']);
      unset($db_vars['debug']);
    }

    // Figure out the command we are using, so we can be a tad more efficient.
    $command = strtoupper(trim(substr(trim($db_query), 0, strpos(trim($db_query), ' '))));

    // Now for some SQLite changes..! FUN!
    // SQLite does have support for UPDATE IGNORE, but it's UPDATE OR IGNORE...
    if($command == 'UPDATE' && stripos($db_query, 'UPDATE IGNORE') !== false)
    {
      $db_query = str_ireplace('UPDATE IGNORE', 'UPDATE OR IGNORE', $db_query);
    }

    // SQLite is naughty, and doesn't support LIMIT's in UPDATE and DELETE queries...
    if(($command == 'UPDATE' || $command == 'DELETE') && (preg_match('~UPDATE(?:.*?)SET~is', $db_query) == 1 || stripos($db_query, 'DELETE FROM') !== false) && preg_match('~(?:LIMIT\s+(?:\d+|%.*)(?:\s*,\s*(?:\d+|%.*))?)~i', $db_query, $matches) == 1)
    {
      $db_query = str_replace($matches[0], ' ', $db_query);
    }

    // ASC or DESC flag in a GROUP BY? Nope.
    if($command == 'SELECT' && preg_match('~GROUP BY (.*?) (?:ASC|DESC)~', $db_query, $matches))
    {
      // So just remove the ASC or DESC...
      $db_query = str_replace($matches[0], str_replace(array('ASC', 'DESC'), '', $matches[0]), $db_query);
    }

    // TRUNCATE? Nope. It's just a DELETE FROM.
    if($command == 'TRUNCATE')
    {
      // It could be TRUNCATE TABLE though too, so check that.
      $db_query = str_ireplace(stripos($db_query, 'TRUNCATE TABLE') !== false ? 'TRUNCATE TABLE' : 'TRUNCATE', 'DELETE FROM', $db_query);
    }

    // Any random function call? It's RANDOM() in SQLite.
    if(preg_match('~RAND\((?:.*?)\)~', $db_query, $matches))
    {
      $db_query = str_replace($matches[0], 'RAND()', $db_query);
    }

    // Let's use debug_backtrace() to find where this was called and what not ;)
    // Only if file and line aren't set already ;)
    if(empty($file) || empty($line))
    {
      $backtrace = debug_backtrace();
      $file = realpath($backtrace[0]['file']);
      $line = (int)$backtrace[0]['line'];
    }

    // Replace {db->prefix} and {db_prefix} with $this->prefix... :P
    $db_query = strtr($db_query, array('{db->prefix}' => $this->prefix, '{db_prefix}' => $this->prefix));

    // Any possible variables that may need replacing? (Don't do this if it is an insert, or things could get ugly)
    if(strpos($db_query, '{') !== false && ($db_compat != 'insert' || $db_compat == 'no_parse'))
    {
      // Find all the variables.
      preg_match_all('~{[\w-]+:\w+}~', $db_query, $matches);

      if(count($matches[0]))
      {
        // Holds all our soon-to-be replaced variables.
        $replacements = array();

        // Holds onto any undefined variables, you never know ;)
        $undefined = array();

        // No need to parse the same variables multiple times, is there?
        $matches[0] = array_unique($matches[0]);

        foreach($matches[0] as $variable)
        {
          list($datatype, $variable_name) = explode(':', substr($variable, 1, strlen($variable) - 2));

          // Let's just be safe, shall we?
          $datatype = trim($datatype);
          $variable_name = trim($variable_name);

          // Has it been defined or not?
          if(!isset($db_vars[$variable_name]))
          {
            $undefined[] = $variable_name;
            continue;
          }

          // Sanitize that value to how it should be!!!
          $replacements[$variable] = $this->var_sanitize($variable_name, $datatype, $db_vars[$variable_name], $file, $line);
        }

        // Did we get any undefined variables? :/
        if(count($undefined) > 0)
        {
          $this->log_error('Undefined database variables <em>'. implode('</em>, <em>', $undefined). '</em>', true, $file, $line);
        }

        // Maybe replace the variables in the query?
        if(count($replacements))
        {
          $db_query = strtr($db_query, $replacements);
        }
      }
    }

    // For every query...
    $return = null;
    api()->run_hooks('pre_query_exec', array(&$db_query, &$db_vars, &$db_compat, &$hook_name, &$return));

    if(!empty($return))
    {
      return $return;
    }

    // Now run that query!
    $query_start = microtime(true);
    $query_result = sqlite_query($db_query, $this->con, SQLITE_BOTH, $query_error);
    $query_took = round(microtime(true) - $query_start, 5);

    $this->num_queries++;

    // Any errors? SQLite query errors won't be returned by errno() or error()...
    $sqlite_errno = 0;
    $sqlite_error = $query_error;

    // Debug this query?
    if(!empty($this->debug))
    {
      $this->debug_text .= "Query:\r\n$db_query\r\nFile: $file\r\nLine: $line\r\nExecuted in $query_took seconds.\r\nError: ". (empty($query_result) ? $sqlite_error : 'None'). "\r\n\r\n";
      $this->debug = isset($prev_debug) ? $prev_debug : $this->debug;
    }

    // Did an error occur?
    if(empty($query_result))
    {
      $this->log_error($sqlite_error, true, $file, $line);
    }

    // We shall return the result in an SQLiteResult Object.
    $result = new $this->result_class($query_result, sqlite_changes($this->con), $db_compat == 'insert' ? sqlite_last_insert_rowid($this->con) : 0, $sqlite_errno, $sqlite_error, $this->num_queries - 1);

    // Maybe you want to cache it or something..?
    api()->run_hooks('post_query_exec', array(&$result, $db_query, $query_result, $this->result_class, $db_compat, $hook_name, $query_took, $sqlite_errno, $sqlite_error));

    return $result;
  }

  protected function var_sanitize($var_name, $datatype, $value, $file, $line)
  {
    $datatype = strtolower($datatype);

    // Is it a string? It could have a length :)
    if(substr($datatype, 0, 6) == 'string' && strpos($datatype, '-') !== false)
    {
      list(, $length) = explode('-', $datatype);

      $datatype = 'string';
      $value = substr($value, 0, (int)$length);
    }

    $datatypes = array(
      'float' => 'sanitize_float',
      'float_array' => 'sanitize_float_array',
      'array_float' => 'sanitize_float_array',
      'identifier' => 'sanitize_identifier',
      'identifier_array' => 'sanitize_identifier_array',
      'array_identifier' => 'sanitize_identifier_array',
      'int' => 'sanitize_int',
      'int_array' => 'sanitize_int_array',
      'array_int' => 'sanitize_int_array',
      'raw' => 'sanitize_raw',
      'string' => 'sanitize_string',
      'string_array' => 'sanitize_string_array',
      'array_string' => 'sanitize_string_array',
      'text' => 'sanitize_string',
      'text_array' => 'sanitize_string_array',
      'array_text' => 'sanitize_string_array',
    );

    api()->run_hooks('database_types', array(&$datatypes));

    // Is the datatype defined?
    if(!isset($datatypes[$datatype]))
    {
      $this->log_error('Undefined data type <string>'. strtoupper($datatype). '</strong>.', true, $file, $line);
    }

    // Return the sanitized value...
    return is_callable(array($this, $datatypes[$datatype])) ? $this->$datatypes[$datatype]($var_name, $value, $file, $line) : $datatypes[$datatype]($var_name, $value, $file, $line);
  }

  protected function sanitize_float($var_name, $value, $file, $line)
  {
    // Make sure it is of the right type :)
    if((string)$value !== (string)(float)$value)
    {
      $this->log_error('Wrong data type, float expected ('. $var_name. ')', true, $file, $line);
    }

    return (string)(float)$value;
  }

  protected function sanitize_float_array($var_name, $value, $file, $line)
  {
    // Not an array? Well, it can't be an array of floats then can it?
    if(!is_array($value))
    {
      $this->log_error('Wrong data type, array expected ('. $var_name. ')', true, $file, $line);
    }

    $new_value = array();
    if(count($value))
    {
      foreach($value as $v)
      {
        $new_value[] = $this->sanitize_float($var_name, $v, $file, $line);
      }
    }

    return implode(', ', $new_value);
  }

  protected function sanitize_identifier($var_name, $value, $file, $line)
  {
    return '\''. $value. '\'';
  }

  protected function sanitize_identifier_array($var_name, $value, $file, $line)
  {
    if(!is_array($value))
    {
      $this->log_error('Wrong data type, array expected ('. $var_name. ')', true, $file, $line);
    }

    $new_value = array();
    if(count($value))
    {
      foreach($value as $v)
      {
        $new_value[] = $this->sanitize_identifier($var_name, $v, $file, $line);
      }
    }

    return implode(', ', $new_value);
  }

  protected function sanitize_int($var_name, $value, $file, $line)
  {
    // Mmmm, inty!
    if((string)$value !== (string)(int)$value)
    {
      $this->log_error('Wrong data type, integer expected ('. $var_name. ')', true, $file, $line);
    }

    return (string)(int)$value;
  }

  protected function sanitize_int_array($var_name, $value, $file, $line)
  {
    if(!is_array($value))
    {
      $this->log_error('Wrong data type, array expected ('. $var_name. ')', true, $file, $line);
    }

    $new_value = array();
    if(count($value))
    {
      foreach($value as $v)
      {
        $new_value[] = $this->sanitize_int($var_name, $v, $file, $line);
      }
    }

    return implode(', ', $new_value);
  }

  protected function sanitize_string($var_name, $value, $file, $line)
  {
    // No need to see if it is a string P:
    return '\''. $this->escape($value). '\'';
  }

  protected function sanitize_string_array($var_name, $value, $file, $line)
  {
    if(!is_array($value))
    {
      $this->log_error('Wrong data type, array expected ('. $var_name. ')', true, $file, $line);
    }

    $new_value = array();
    if(count($value))
    {
      foreach($value as $v)
      {
        $new_value[] = $this->sanitize_string($var_name, $v, $file, $line);
      }
    }

    return implode(', ', $new_value);
  }

  public function insert($type, $tbl_name, $columns, $data, $keys = array(), $hook_name = null)
  {
    if(empty($this->con))
    {
      return false;
    }

    api()->run_hooks('pre_insert_exec', array(&$type, &$tbl_name, &$columns, &$data, &$keys, &$hook_name));

    if(!empty($hook_name))
    {
      api()->run_hooks($hook_name, array(&$type, &$tbl_name, &$columns, &$data, &$keys, &$hook_name));
    }

    // Let's get where you called us from!
    $backtrace = debug_backtrace();
    $file = realpath($backtrace[0]['file']);
    $line = (int)$backtrace[0]['line'];
    unset($backtrace);

    $type = strtolower($type);

    // We only support insert, ignore and replace.
    if(!in_array($type, array('insert', 'ignore', 'replace')))
    {
      $this->log_error('Unknown insert type '. $type, true, $file, $line);
    }

    // Replace {db->prefix} and {db_prefix} with $this->prefix
    $tbl_name = strtr($tbl_name, array('{db->prefix}' => $this->prefix, '{db_prefix}' => $this->prefix));

    // Just an array, and not an array inside an array? We'll fix that...
    if(!isset($data[0]) || !is_array($data[0]))
    {
      $data = array($data);
    }

    // The number of columns :)
    $num_columns = count($columns);

    // Now get the column names, quite useful you know :)
    $column_names = array_keys($columns);

    // Now we can get all the rows ready :)
    $rows = array();
    foreach($data as $row_index => $row)
    {
      // Not enough data?
      if($num_columns != count($row))
      {
        $this->log_error('Number of columns doesn\'t match the number of supplied columns in row //'. ($row_index + 1), true, $file, $line);
      }

      // Save the values to an array, all sanitized and what not, of course!
      $values = array();
      foreach($row as $index => $value)
      {
        $values[] = $this->var_sanitize($column_names[$index], $columns[$column_names[$index]], $value, $file, $line);
      }

      // Add those values to our rows now :)
      $rows[] = '('. implode(', ', $values). ')';
    }

    $inserts = array(
      'insert' => 'INSERT',
      'ignore' => 'INSERT OR IGNORE',
      'replace' => 'INSERT OR REPLACE',
    );

    $affected_rows = 0;
    $insert_id = 0;
    $errno = 0;
    $error = '';
    $query_num = -1;

    // SQLite doesn't support extended inserts, so we have to play a little game ;)
    foreach($rows as $row)
    {
      $db_query = $inserts[$type]. ' INTO \'' .$tbl_name. '\' (\''. implode('\', \'', $column_names). '\') VALUES'. $row;

      $result = $this->query($db_query, array(), null, 'insert');
      $affected_rows += $result->affected_rows();
      $insert_id = $result->insert_id();
      $errno = $result->errno();
      $error = $result->error();
      $query_num = $result->query_num();
    }

    // Make a fake result object containing the combined stuff.
    return new $this->result_class(true, $affected_rows, $insert_id, $errno, $error, $query_num);
  }
}

$db_class = 'SQLite';
?>