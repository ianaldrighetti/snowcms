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

//
// config.php holds all your database information and paths.
//

// Database settings:
define('DBTYPE', '', true); // Your database type, an example would be mysql, sqlite or postgresql
define('DBHOST', '', true); // The location of your database, could be localhost or a path (for SQLite)
define('DBUSER', '', true); // The user that has access to your database, though not all database systems have this.
define('DBPASS', '', true); // The password to your database user.
define('DBNAME', '', true); // The name of the database.
define('DBPERSIST', false, true); // Whether or not to have a persistent connection to the database.
define('DBDEBUG', false, true); // Enable database debugging? (Outputs queries into a file ;))
define('TBLPREFIX', 'snow_', true); // The prefix of the tables, allows multiple installs on the same database.

// The location of your root directory of your SnowCMS installation.
define('BASEDIR', defined('__DIR__') ? __DIR__ : dirname(__FILE__), true);

// Some other useful paths...
define('COREDIR', basedir. '/core', true);
define('THEMEDIR', basedir. '/themes', true);
define('PLUGINDIR', basedir. '/plugins', true);
define('UPLOADDIR', basedir. '/uploads', true);

// The address of where your SnowCMS install is accessible (No trailing /!)
define('BASEURL', '', true);
define('THEMEURL', baseurl. '/themes', true);
define('PLUGINURL', baseurl. '/plugins', true);

// What do you want to be the name of the cookie?
define('COOKIENAME', 'SCMS643', true);
?>