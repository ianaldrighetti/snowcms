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

/*
	Function: load_database

	Loads the proper SQL engine class into the $db variable.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.

	Note:
		This function should *not* be called directly! If a Database instance
		has yet to be instantiated, the calling of the <db> function will then
		call this function.
*/
function load_database()
{
	// Does the right stuff exist? It needs to for this system to run!
	if(file_exists(coredir. '/engines/'. strtolower(dbtype). '.engine.php') && file_exists(coredir. '/engines/'. strtolower(dbtype). '_result.engine.php'))
	{
		// Awesome, they're there! So we can get going now :)
		require_once(coredir. '/database_result.class.php');
		require_once(coredir. '/engines/'. strtolower(dbtype). '_result.engine.php');
		require_once(coredir. '/database.class.php');
		require_once(coredir. '/engines/'. strtolower(dbtype). '.engine.php');

		// Well, you should have specified the name of your class and result class, did you?
		if(!empty($db_class) && !empty($db_result_class) && class_exists($db_class) && class_exists($db_result_class))
		{
			$GLOBALS['db'] = new $db_class($db_result_class);

			// Attempt to connect to the database.
			$GLOBALS['db']->connect();
		}
		else
		{
			die(!empty($db_class) || !empty($db_result_class) ? '$db_class or $db_result_class was not specified in the SQL engine files!' : 'The classes specified in $db_class or $db_result_class were not found!');
		}
	}
	else
	{
		die('Invalid database type supplied in config.php');
	}
}

/*
	Function: db

	Returns the current Database instance.

	Parameters:
		none

	Returns:
		object

	Note:
		If no Database instance exists, then this function will call
		<load_database> to do so.
*/
function db()
{
	// No Database instance yet?
	if(!isset($GLOBALS['db']))
	{
		// This'll do it for us.
		load_database();
	}

	return $GLOBALS['db'];
}
?>