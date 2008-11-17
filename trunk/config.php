<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                  config.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

// Your MySQL information
$mysql_host = 'localhost'; # Your MySQL Host, doubt you will change this
$mysql_user = 'root'; # Your MySQL Username
$mysql_passwd = ''; # Your MySQL Password
$mysql_db = 'snowcms'; # Your MySQL DB
$mysql_prefix = ''; # Prefix for your database

// Misc
$cookie_prefix = ''; # Prefix for cookies

// Some SnowCMS specific settings
$source_dir = 'C:\\Users\\Myles\\XAMPP\\htdocs\\scms/Sources'; # Path to your Source directory without trailing /
$theme_dir = 'C:\\Users\\Myles\\XAMPP\\htdocs\\scms/Themes'; # Path to your Themes directory without trailing /
$language_dir = 'C:\\Users\\Myles\\XAMPP\\htdocs\\scms/Languages'; # Path to your Languages directory without trailing /
$cmsurl = 'http://localhost/scms/'; # URL to your SnowCMS Installation
$theme_url = 'http://localhost/scms/Themes'; # URL to your SnowCMS Themes folder

// Don't touch the stuff below
$db_prefix = '`'.$mysql_db.'`.'.$mysql_prefix;
$scms_installed = true;
?>