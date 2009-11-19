<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No direct access please ^^
if(!defined('InSnow'))
  die(header('HTTP/1.1 404 Not Found'));

#
# config.php holds the configuration of your SnowCMS installation
# from your database settings to file paths needed.
#

# Your database settings
$db_type = ''; # Your database type

# File paths (No trailing slash)
$base_dir = '.'; # The base path to your SnowCMS installation
$source_dir = './sources'; # Path to the sources Folder
$theme_dir = './themes'; # Path to the themes Folder
$download_dir = './downloads'; # The path to the downloads directory which contains downloads for your download center
$avatar_dir = './_avatars'; # The path to the Aaatars directory which has avatars that users, use :P If you enable it
$emoticon_dir = '/emoticons'; # The path to the emoticons directory which has emoticon packs and emoticons
$cache_dir = './cache'; # The path to where cached files will go unless you are using another caching system. no trailing slash.

# We need some URLs too
$base_url = ''; # The base url to your SnowCMS install. No trailing slash.
$theme_url = 'themes'; # The base url to your SnowCMS themes folder. No trailing slash.
$emoticon_url = 'emoticons'; # The base url to your SnowCMS emoticons folder. No trailing slash.

# Check if a couple things exist. All we really need is the
# base path, source directory and theme directory.
if(!file_exists($base_dir) && file_exists('./'))
  $base_dir = dirname(__FILE__);
if(!file_exists($source_dir) && file_exists('./sources'))
  $source_dir = dirname(__FILE__). '/sources';
if(!file_exists($theme_dir) && file_exists('./themes'))
  $theme_dir = dirname(__FILE__). '/themes';
if(!file_exists($cache_dir) && file_exists('./cache'))
  $cache_dir = dirname(__FILE__). '/cache';

$snowcms_installed = false;
?>