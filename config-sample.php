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

# No direct access!!!
if(!defined('IN_SNOW'))
  die;

#
# config.php holds all your database information and paths.
#

# Database settings:
$db_type = ''; # Your database type, an example would be mysql, sqlite or postgresql
$db_host = ''; # The location of your database, could be localhost or a path (for SQLite)
$db_user = ''; # The user that has access to your database, though not all database systems have this.
$db_pass = ''; # The password to your database user.
$db_name = ''; # The name of the database.
$db_persist = false; # Whether or not to have a persistent connection to the database.
$db_debug = false; # Enable database debugging? (Outputs queries into a file ;))
$tbl_prefix = 'snow_'; # The prefix of the tables, allows multiple installs on the same database.

# The location of your root directory of your SnowCMS installation.
$base_dir = defined('__DIR__') ? __DIR__ : dirname(__FILE__);

# Some other useful paths...
$core_dir = $base_dir. '/core';
$theme_dir = $base_dir. '/themes';
$plugin_dir = $base_dir. '/plugins';
$upload_dir = $base_dir. '/uploads';

# The address of where your SnowCMS install is accessible (No trailing /!)
$base_url = '';
$theme_url = $base_url. '/themes';
$plugin_url = $base_url. '/plugins';

# What do you want to be the name of the cookie?
$cookie_name = 'SCMS643';
?>