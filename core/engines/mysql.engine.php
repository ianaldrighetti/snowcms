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

class MySQL extends Database
{
	public function connect()
	{
		// Persistent connection or not?
		if(!defined('dbpersist') || !dbpersist)
		{
			$this->con = @mysql_connect(dbhost, dbuser, dbpass);
		}
		else
		{
			$this->con = @mysql_pconnect(dbhost, dbuser, dbpass);
		}

		// Fail to connect?
		if(empty($this->con))
		{
			// Show the message.
			$this->log_error(1, true);
			return false;
		}

		// Select the database now ;)
		$select_db = @mysql_select_db(dbname, $this->con);

		// Failed to select the database..? That isn't good!
		if(empty($select_db))
		{
			$this->log_error(2, true);
		}

		// Sweet, everything seems to be in order so far, set a couple other things others
		// may need to use at a later time.
		$this->prefix = tblprefix;
		$this->type = 'MySQL';
		$this->case_sensitive = false;
		$this->drop_if_exists = true;
		$this->if_not_exists = true;
		$this->extended_inserts = true;

		// Alright, we are done here.
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
		global $func;

		return @mysql_real_escape_string(!empty($htmlspecialchars) ? $func['htmlspecialchars']($str) : $str, $this->con);
	}

	public function unescape($str, $htmlspecialchars_decode = false)
	{
		global $func;

		return !empty($htmlspecialchars_decode) ? $func['htmlspecialchars_decode'](stripslashes($str)) : stripslashes($str);
	}

	public function version()
	{
		if(empty($this->con))
		{
			return false;
		}

		// The VERSION() function will give us what we need :-)
		$result = $this->query('
			SELECT VERSION()');

		// Fetch it and return it!
		list($version) = $result->fetch_row();

		return $version;
	}

	public function tables()
	{
		if(empty($this->con))
		{
			return false;
		}

		// Get a listing of all the tables, simpler than SQLite...
		$result = $db->query('
			SHOW TABLES', array(), null, 'show_tables');

		// Load'em up, otherwise, what's the point, right..?
		$tables = array();
		while($row = $result->fetch_row())
		{
			$tables[] = $row[0];
		}

		return $tables;
	}

	public function columns($table)
	{
		// Check to see if the table exists, if it does not, we cannot get the columns :P
		if(empty($this->con) || !in_array($table, $this->tables()))
		{
			return false;
		}

		// Simple query, really.
		$result = $db->query('
			SHOW COLUMNS IN {raw:table}',
			array(
				'table' => $table,
			), null, 'show_columns');

		// Now get ready to load'em up.
		$columns = array();
		while($row = $result->fetch_row())
		{
			$columns[] = $row[0];
		}

		return $columns;
	}

	public function query($db_query, $db_vars = array(), $hook_name = null, $db_compat = null, $file = null, $line = 0)
	{
		// We can't make a query if we aren't connected.
		if(empty($this->con))
		{
			return false;
		}

		// Something for all queries before they are parsed.
		$return = null;
		api()->run_hooks('pre_parse_query', array(&$db_query, &$db_vars, &$hook_name, &$db_compat, &$file, &$line, &$return));

		if($return !== null)
		{
			return $return;
		}

		// Just incase, for some odd reason :P
		if(!empty($hook_name))
		{
			$return = null;
			api()->run_hooks($hook_name, array(&$db_query, &$db_vars, &$hook_name, &$db_compat, &$file, &$line, &$return));

			if($return !== null)
			{
				return $return;
			}
		}

		// debug set?
		if(isset($db_vars['debug']))
		{
			$prev_debug = $this->debug;

			$this->debug = !empty($db_vars['debug']);
			unset($db_vars['debug']);
		}

		/*

			In other databases such as SQLite, PostgreSQL, SQL Server, etc. anything that isn't
			MySQL at this time would do any query fixing to make it parse right upon execution.

		*/

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

		// Any possible variables that may need replacing? (Don't do this if it is an insert, or things could get ugly,
		// or don't do it if you said no_parse ;-))
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

				// Let's figure out which variables are defined.
				$defined = array();
				foreach($db_vars as $var_name => $value)
				{
					$defined[] = $var_name;
				}

				// No need to parse the same variables multiple times, is there?
				$matches[0] = array_unique($matches[0]);

				foreach($matches[0] as $variable)
				{
					list($datatype, $variable_name) = explode(':', substr($variable, 1, strlen($variable) - 2));

					// Let's just be safe, shall we?
					$datatype = trim($datatype);
					$variable_name = trim($variable_name);

					// Has it been defined or not?
					if(!in_array($variable_name, $defined))
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
				if(count($replacements) > 0)
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

		// Woo!!! QUERY THAT DATABASE!
		$query_start = microtime(true);
		$query_result = mysql_query(trim($db_query), $this->con);
		$query_took = round(microtime(true) - $query_start, 5);

		// That is one more query!
		$this->num_queries++;

		// Let's not call on it multiple times, mmk?
		$mysql_errno = $this->errno();
		$mysql_error = $this->error();

		// Debug this query, perhaps?
		if(!empty($this->debug))
		{
			$this->debug_text .= "Query:\r\n$db_query\r\nFile: $file\r\nLine: $line\r\nExecuted in $query_took seconds.\r\nError: ". (empty($query_result) ? '['. $mysql_errno. '] '. $mysql_error : 'None'). "\r\n\r\n";
			$this->debug = isset($prev_debug) ? $prev_debug : $this->debug;
		}

		// An error occur?
		if(empty($query_result))
		{
			$this->log_error('['. $mysql_errno. '] '. $mysql_error, true, $file, $line);
		}

		// Put it in a MySQLResult Object ;)
		$result = new $this->result_class($query_result, mysql_affected_rows($this->con), $db_compat == 'insert' ? mysql_insert_id($this->con) : 0, $mysql_errno, $mysql_error, $this->num_queries - 1);

		api()->run_hooks('post_query_exec', array(&$result, $db_query, $query_result, $this->result_class, $db_compat, $hook_name, $query_took, $mysql_errno, $mysql_error));

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
		// No sanitization here, actually.
		return '`'. $value. '`';
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

		// Something for all inserts.
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
			'ignore' => 'INSERT IGNORE',
			'replace' => 'REPLACE',
		);

		// Construct the query, MySQL suports extended inserts! Hip hip! HURRAY!
		$db_query = $inserts[$type]. ' INTO `'. $tbl_name. '` (`'. implode('`, `', $column_names). '`) VALUES'. implode(', ', $rows);

		// Let query handle it XD! (passes insert in db compat to let you know
		// if you don't have to do anything at all, which you shouldn't!!!
		return $this->query($db_query, array(), null, 'insert');
	}
}

$db_class = 'MySQL';
?>