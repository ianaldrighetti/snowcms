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
$mysql_user = 'nscnet_snowcms'; # Your MySQL Username
$mysql_passwd = '1@snowcms1245&'; # Your MySQL Password
$mysql_db = 'nscnet_snow'; # Your MySQL DB
$mysql_prefix = 'scms_'; # Prefix for your database

// Some SnowCMS Specific Settings
$source_dir = './Sources'; # Path to your Source directory without trailing /!
$theme_dir = './Themes'; # Path to your Themes directory without trailing /!
$language_dir = './Languages'; # Path to your Languages directory without trailing /!
$cmsurl = 'http://snowcms.northsalemcrew.net/cms/'; # URL to your SnowCMS Installation
$theme_url = 'http://snowcms.northsalemcrew.net/cms/Themes/'; # URL to your SnowCMS Themes folder

/* Don't touch the stuff below! */
$db_prefix = '`'.$mysql_db.'`.'.$mysql_prefix;
?>
