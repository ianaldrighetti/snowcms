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

define('InSnow', true);
define('InSnowForum', true);

# All Errors!
error_reporting(E_ALL);

# We have started :0
$started_time = microtime();

# We need the SnowCMS configuration file xD
if(is_readable(dirname(__FILE__). '/config.php'))
  require_once(dirname(__FILE__). '/config.php');
else
  exit('<html><p style="font-family: Verdana, sans-serif; font-size: x-small;"><strong>System Error:</strong> config.php missing. If this error persists, contact the administrator.</p></html>');

# We need some important things here :)
require_once($source_dir. '/corecms.php');
require_once($source_dir. '/corefunctions.php');
require_once($source_dir. '/compat.php');
require_once($source_dir. '/session.php');
require_once($source_dir. '/language.php');
require_once($source_dir. '/errors.php');
require_once($source_dir. '/theme.php');
require_once($source_dir. '/permissions.php');
require_once($source_dir. '/security.php');
require_once($source_dir. '/bbcode.php');

# Clean up the query (the ?something=something) things...
clean_query();

# Is SnowCMS installed..?
if(empty($snowcms_installed))
  header('Location: http://'. $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']). '/install.php');

# Let's try to be UTF-8 compatible, shall we?
mb_internal_encoding('UTF-8');

# Just a variable for later use...
$page = array();

# Connect to the database.
db_connect();

# Set our custom error handler :)
set_error_handler('errors_handle');

# Our shutdown function :P
register_shutdown_function('snow_close');

# Settings, get your settings here!
load_settings();

# We need to start the session.
session_set_handler();

# Start the output buffer
handle_gzip();

# Lets load up the main language :)
language_load('main');

# Start up the user info ;)
init_member();

# Load the permissions now... With forum ones ;D
permissions_load(true);

# Load the menu ;)
load_menu();

# Load the theme data from the INI file
theme_data();

# Log you as online...
log_online();

# Need to run a task..? Make your users work >:D
need_task();

# Maintenance Mode?
security_mmode();

# So what are we doing..?
if(!empty($_GET['topic']) && empty($_GET['action']))
{
  # Loading a topic.
  require_once($source_dir. '/topic.php');
  topic_load();
}
elseif(!empty($_GET['board']) && empty($_GET['action']))
{
  # A board... :0
  require_once($source_dir. '/forum.php');
  forum_board();
}
elseif(!empty($_GET['msg']) && empty($_GET['action']))
{
  # Redirecting to a specific post in a topic.
  require_once($source_dir. '/topic.php');
  topic_redirect();
}
else
{
  # Hmm... We must be doing an action?
  # Here is the array of actions :) to add an action
  # it is quote simple, using the following syntax:
  # 'ACTION' => array('FILE','FUNCTION'),
  $actions = array(
    'edit' => array('edit-post.php', 'post_edit'),
    'edit2' => array('edit-post.php', 'post_edit_save'),
    'post' => array('post.php', 'post_make'),
    'post2' => array('post.php', 'post_save'),
    'recent' => array('recent.php', 'recent_posts'),
    'unread' => array('recent.php', 'recent_unread'),
    'unreadreplies' => array('recent.php', 'recent_unread_replies'),
  );

  # Lets see... Is action not empty and does the action
  # actually exist in the actions array..?
  if(!empty($_GET['action']) && is_array($actions[$_GET['action']]))
  {
    # We want to call on an action :)
    # Get the file
    require_once($source_dir. '/'. $actions[$_GET['action']][0]);

    # call on the function
    $actions[$_GET['action']][1]();
  }
  else
  {
    # Well well... Nothing huh? Or at least what you are trying
    # to do just doesn't exist :P
    # So we want to do the "home"
    require_once($source_dir. '/forum.php');
    forum_index();
  }
}
?>