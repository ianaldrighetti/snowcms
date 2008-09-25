<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//          redistribute it as your wish!
//
//                  config.php file

// We can't let you access it directly :)
if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Your MySQL Information
$mysql_host = 'localhost'; # Your MySQL Host, doubt you will change this
$mysql_user = ''; # Your MySQL Username
$mysql_passwd = ''; # Your MySQL Password
$mysql_db = ''; # Your MySQL DB
$mysql_prefix = 'scms_'; # Prefix for your database

// Misc
$cookie_prefix = 'scms_'; # Prefix for cookies

// Some SnowCMS Specific Settings
$source_dir = ''; # Path to your Source directory without trailing /!
$theme_dir = ''; # Path to your Themes directory without trailing /!
$language_dir = ''; # Path to your Languages directory without trailing /!
$cmsurl = ''; # URL to your SnowCMS Installation
$theme_url = ''; # URL to your SnowCMS Themes folder

/* Don't touch the stuff below! */
$db_prefix = '`'.$mysql_db.'`.'.$mysql_prefix;
$scms_installed = false;
?>
