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

/*
	Class: Database

	This is the abstract class that SQL engines implement.
*/
abstract class Database
{
	// Variable: con
	// Holds the database connection resource.
	public $con = null;

	// Variable: prefix
	// Holds the table prefix, such as snow_
	public $prefix = null;

	// Variable: type
	// The type of database, for example: MySQL, SQLite, PostgreSQL, etc.
	public $type = null;

	// Variable: case_sensitive
	// Is the database system case sensitive? Some are, some aren't. Be sure
	// that you set this to true if the database is case sensitive, or very
	//bad things could occur!!! (Set to true, just incase ;))
	public $case_sensitive = true;

	// Variable: num_queries
	// The number of queries executed so far, right now, none!
	public $num_queries = 0;

	// Variable: drop_if_exists
	// Does your database type support DROP TABLE IF EXISTS?
	public $drop_if_exists = false;

	// Variable: if_not_exists
	// How about CREATE TABLE IF NOT EXISTS?
	public $if_not_exists = false;

	// Variable: extended_inserts
	// Can the database handle extended inserts?
	public $extended_inserts = false;

	// Variable: debug
	// Is the database in a forced debug mode?
	public $debug = false;

	// Variable: result_class
	// The result class which query and insert return (global $db_result_class
	// in connect()).
	public $result_class = null;

	// Variable: debug_text
	// Holds all the debugging stuff which gets written to a file when the
	// object is destructed.
	protected $debug_text = '';

	/*
		Constructor: __construct

		This constructor simply takes the database result's class name and
		saves it to the result_class attribute.

		Parameters:
			string $db_result_class - The name of the class which handles the
																result of the databases query function.
	*/
	public function __construct($db_result_class = null)
	{
		$this->result_class = $db_result_class;

		// Is debugging enabled?
		if(defined('DBDEBUG') && DBDEBUG)
		{
			$this->debug = true;
		}
	}

	/*
		Method: connect

		Connects to the SQL database or server.

		Parameters:
			none

		Returns:
		 bool - Returns true if the connection was a success, false if
						connection failed.

		Note:
			To get the database information, simply global the variables, such
			as $db_host, $db_name, $db_user, $db_passwd, $db_prefix, etc.
	*/
	abstract public function connect();

	/*
		Method: close

		Closes the connection to the SQL database or server.

		Parameters:
			none

		Returns:
		 bool - Returns true if the connection was successfully closed, false
						otherwise.
	*/
	abstract public function close();

	/*
		Method: errno

		Returns the error number of which occurred from the last database
		method called, if any.

		Parameters:
			none

		Returns:
			int - Returns the last error number, 0 if none.
	*/
	abstract public function errno();

	/*
		Method: error

		Returns the error string from the last SELECT, UPDATE, INSERT, etc.
		query.

		Parameters:
			none

		Returns:
		 string - Returns the last error string, an empty string if no errors
							occurred.
	*/
	abstract public function error();

	/*
		Method: escape

		Makes a string safe to use in a query.

		Parameters:
			string $str - The string that needs sanitizing.
			bool $htmlspecialchars - Whether or not to first do htmlspecialchars
															 on the string.

		Returns:
		 string - Returns the sanitized string.
	*/
	abstract public function escape($str, $htmlspecialchars = false);

	/*
		Method: unescape

		The opposite of the escape method.

		Parameters:
			string $str - The string to unescape.
			bool $htmlspecialchars_decode - Whether or not to undo
																			htmlspecialchars after unescaping.

		Returns:
		 string - Returns the unescaped string.
	*/
	abstract public function unescape($str, $htmlspecialchars_decode = false);

	/*
		Method: version

		Returns a string containing the databases version (Like MySQL 5.0.11).

		Parameters:
			none

		Returns:
		 string - Returns the version of the databases version number.
	*/
	abstract public function version();

	/*
		Method: tables

		Returns an array containing all the tables in the database.

		Parameters:
			none

		Returns:
		 array - Returns an array containing all the tables in the database.
	*/
	abstract public function tables();

	/*
		Method: columns

		Returns an array containing all the columns in the specified table,
		and only the columns names, this is just like <Database::tables>,
		except for columns, of course.

		Parameters:
			string $table - The name of the table to fetch the column names from.

		Returns:
			array - Returns an array containing all the columns of the specified
							table, false if the table does not exist.
	*/
	abstract public function columns($table);

	/*
		Method: query

		Queries the database, however, it isn't a simple
		[mysql|sqlite|...]_query as this method changes the query for any
		compatibility issues. ONLY SELECT, UPDATE and DELETE queries should be
		used with this method, check out the insert method for doing INSERT's
		and REPLACE's.

		Parameters:
			string $db_query - The database query you want to execute.
			array $db_vars - The variable values to replace in the query.
			string $hook_name - The name of hook to run BEFORE anything else is
													done. The run_hook method is to have $db_query,
													$db_vars and $db_compat passed as parameters.
			string $db_compat - A string which can be null or a string giving the
													database class a heads up on any possible
													compatibility issues.
			string $file - The file query was called on, LEAVE THIS BLANK! This
										 is for use by the insert method!
			int $line - The line the query was called on, LEAVE THIS BLANK! This
									is for use by the insert method as well!

		Returns:
		 object {Database_Result} - Returns an object with methods such as
																fetch_assoc, num_rows, etc.
	*/
	abstract public function query($db_query, $db_vars = array(), $hook_name = null, $db_compat = null, $file = null, $line = 0);

	/*
		Method: var_sanitize

		Sanitizes the variable in a query using the correct methods.

		Parameters:
			string $var_name - The name of the variable in the query.
			string $datatype - The datatype of the variable.
			mixed $value - Contains the value of the variable.
			string $file - The file that query/insert was called in.
			int $line - The line that query/insert was called on.

		Returns:
		 mixed - Returns the correctly sanitized value.

		Note:
			 SnowCMS currently supports the following datatypes:
						float - A number such as 1, 1.0, etc.

						float_array - An array containing floats, when all numbers
													inside are properly sanitized, it becomes
													comma delimited.

						array_float - An alias of float_array.

						int - An integer.

						int_array - An array containing integers, which once properly
												sanitized will become comma delimited.

						array_int - An alias of int_array.

						raw - A string which will be put into the query, with nothing
									done to it.

						string - A string which will be escaped using
										 <Database::escape>, which will automatically be
										 surrounded by single quotes.

						string_array - An array containing strings, and once sanitized,
													 will be comma delimited.

						array_string - An alias of string_array.

						text - An alias of string.

						text_array - An alias of string_array.

						array_text - An alias of string_array.

						identifier - An identifier, for example, giving an identifier
												 the value of myColName in MySQL would be turned
												 into `myColName`, because in MySQL backticks (`)
												 tell MySQL that anything between those quotes is
												 nothing special. For database systems such as
												 SQLite or PostgreSQL, it would be 'myColName',
												 as single quotes marks identifiers.

						identifier_array - An array containing identifiers, and once
															 santizied, will be comma delimited.

						array_identifier - An alias of identifier_array.

		More information about databasing can be found at
		<http://code.google.com/p/snowcms/wiki/Databasing>.
	*/
	abstract protected function var_sanitize($var_name, $datatype, $value, $file, $line);

	/*
		Method: sanitize_float

		All the following methods are helper methods of var_sanitize. They all
		get passed the variable name, value, file name and line number. These
		helper methods are expected to properly sanitize the value according
		to the variables datatype. If the value given is not able to be
		sanitized properly, you must call on the database method log_error
		fatally.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	abstract protected function sanitize_float($var_name, $value, $file, $line);

	/*
		Method: sanitize_float_array

		All the following methods are helper methods of var_sanitize. They all
		get passed the variable name, value, file name and line number. These
		helper methods are expected to properly sanitize the value according to
		the variables datatype. If the value given is not able to be sanitized
		properly, you must call on the database method log_error fatally.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	abstract protected function sanitize_float_array($var_name, $value, $file, $line);

	/*
		Method: sanitize_identifier

		All the following methods are helper methods of var_sanitize. They all
		get passed the variable name, value, file name and line number. These
		helper methods are expected to properly sanitize the value according to
		the variables datatype. If the value given is not able to be sanitized
		properly, you must call on the database method log_error fatally.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	abstract protected function sanitize_identifier($var_name, $value, $file, $line);

	/*
		Method: sanitize_identifier_array

		All the following methods are helper methods of var_sanitize. They all
		get passed the variable name, value, file name and line number. These
		helper methods are expected to properly sanitize the value according to
		the variables datatype. If the value given is not able to be sanitized
		properly, you must call on the database method log_error fatally.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	abstract protected function sanitize_identifier_array($var_name, $value, $file, $line);

	/*
		Method: sanitize_int

		All the following methods are helper methods of var_sanitize. They all
		get passed the variable name, value, file name and line number. These
		helper methods are expected to properly sanitize the value according to
		the variables datatype. If the value given is not able to be sanitized
		properly, you must call on the database method log_error fatally.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	abstract protected function sanitize_int($var_name, $value, $file, $line);


	/*
		Method: sanitize_int_array

		All the following methods are helper methods of var_sanitize. They all
		get passed the variable name, value, file name and line number. These
		helper methods are expected to properly sanitize the value according to
		the variables datatype. If the value given is not able to be sanitized
		properly, you must call on the database method log_error fatally.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	abstract protected function sanitize_int_array($var_name, $value, $file, $line);


	/*
		Method: sanitize_string

		All the following methods are helper methods of var_sanitize. They all
		get passed the variable name, value, file name and line number. These
		helper methods are expected to properly sanitize the value according to
		the variables datatype. If the value given is not able to be sanitized
		properly, you must call on the database method log_error fatally.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	abstract protected function sanitize_string($var_name, $value, $file, $line);


	/*
		Method: sanitize_string_array

		All the following methods are helper methods of var_sanitize. They all
		get passed the variable name, value, file name and line number. These
		helper methods are expected to properly sanitize the value according to
		the variables datatype. If the value given is not able to be sanitized
		properly, you must call on the database method log_error fatally.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	abstract protected function sanitize_string_array($var_name, $value, $file, $line);


	/*
		Method: sanitize_raw

		This method sanitizes (haha!) a raw value. It just simply returns the
		supplied value.

		Parameters:
			string $var_name - The name of the variable being replaced, this is
												 used if an error happens to occur (wrong datatype).
			mixed $value - The value to have sanitized.
			string $file - The name of the file in which the query was originally
										 called in. Used if an error happens to occur.
			int $line - The line in the file that the query was originally called
									on. Used if an error happens to occur.

		Returns:
			string - The value to replace the variable with, properly sanitized
							 according to the type. The value should always be returned
							 as a string.
	*/
	private function sanitize_raw($var_name, $value, $file, $line)
	{
		return $value;
	}

	/*
		Method: insert

		Inserts or replaces data in the database. You can insert/replace
		multiple rows by having arrays inside the data array.

		Parameters:
			string $type - The type of insert you want to perform, INSERT, IGNORE
										 or REPLACE supported.
			string $tbl_name - The table name that the data will be inserted into.
			array $columns - An array containg the columns that will have data
											 inserted into.
			array $data - The actual data to be inserted.
			array $keys - Some database types (Like PostgreSQL) do not support
										REPLACE, in order to fix that you must supply the column
										names which are the primary/unique keys that way an
										UPDATE query can be attempted, if that fails, the data
										is inserted.
			string $hook_name - The hook to run (using run_hook in $api) BEFORE
													the anything is done, run_hook is to pass $type,
													$tbl_name, $columns, $data and $keys as
													parameters.

		Returns:
		 object {Database_Result} - An object containing methods such as
																affected_rows(), etc.

		More information about databasing can be found at
		<http://code.google.com/p/snowcms/wiki/Databasing>.

		Note:
			 It is recommended for running the query through the database that
			 you use the query method, simply pass the method the file and line
			 the insert method was called on to query, also, set the db_compat
			 parameter in query to insert, return query's result.
	*/
	abstract public function insert($type, $tbl_name, $columns, $data, $keys = array(), $hook_name = null);

	/*
		Method: log_error

		Logs a database error into the SnowCMS error log, if the error is
		fatal, show a plain ol' ugly error page stating an error occurred.

		Parameters:
			string $error_message - The error message to log.
			bool $is_fatal - Whether or not the error that occurred means that
											 SnowCMS can no longer continue running.
			string $file - The file that the method was called in that created
										 the error.
			int $line - The line that the method was called on that created the
									error.

		Returns:
		 void - Nothing is returned by this method.
	*/
	public function log_error($error_message, $is_fatal = false, $file = null, $line = 0)
	{
		// If the errors_handler function exists, use that, otherwise, nothing we can do!
		if(function_exists('errors_handler'))
		{
			errors_handler('database', $error_message, $file, $line);
		}

		// Fatal error..?
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
		Destructor: __destruct

		The destructor writes all the debugging text to a file, if any, upon
		this object being destructed.

		Parameters:
			none
	*/
	public function __destruct()
	{
		$this->debug_text = trim($this->debug_text);

		if(!empty($this->debug_text))
		{
			$fp = fopen(basedir. '/db_debug.sql', 'a');
			fwrite($fp, $this->debug_text);
			@fclose($fp);
		}
	}
}
?>