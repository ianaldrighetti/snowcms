<?php
//                 SnowCMS
//           By aldo and soren121
//  Founded by soren121 & co-founded by aldo
//    http://snowcms.northsalemcrew.net
//
// SnowCMS is released under the GPL v3 License
// Which means you are free to edit it and then
//       redistribute it as your wish!
// 
//            config.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Your MySQL Information
$mysql_host = 'localhost'; # Your MySQL Host, doubt you will change this
$mysql_user = 'admin'; # Your MySQL Username
$mysql_passwd = '123456'; # Your MySQL Password
$mysql_db = 'snow'; # Your MySQL DB
$mysql_prefix = 'scms_'; # Prefix for your database

// Some SnowCMS Specific Settings
$source_dir = 'C:\\wamp\\www\\nSnow\\Sources'; # Path to your Source directory without trailing /!
$theme_dir = 'C:\\wamp\\www\\nSnow\\Themes'; # Path to your Themes directory without trailing /!
$language_dir = 'C:\\wamp\\www\\nSnow\\Languages'; # Path to your Languages directory without trailing /!
$cmsurl = 'http://localhost/nSnow/'; # URL to your SnowCMS Installation
$theme_url = 'http://localhost/nSnow/Themes/'; # URL to your SnowCMS Themes folder

/* Don't touch the stuff below! */
$db_prefix = '`'.$mysql_db.'`.'.$mysql_prefix;
$scms_installed = true;
?>