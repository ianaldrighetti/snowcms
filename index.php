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

# All Errors!
error_reporting(E_ALL);

# We have started :0
$started_time = microtime();

# We need the SnowCMS configuration file xD
require_once(dirname(__FILE__). '/config.php');

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
require_once($source_dir. '/captcha.php');
require_once($source_dir. '/bbcode.php');
require_once($source_dir. '/snowtext.php');

# Clean up the query (the ?something=something) things...
clean_query();

# Is SnowCMS installed..?
if(empty($snowcms_installed))
  header('Location: http://'. $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']). '/install.php');

# Let's try to be UTF-8 compatible, shall we?
mb_internal_encoding('UTF-8');

# Set the content type and character set in the HTTP headers
header('Content-Type: text/html; charset=UTF-8');

# Just a variable for later use...
$page = array();

# Connect to the database.
db_connect();

# Settings, get your settings here!
load_settings();

# We need to start the session.
session_set_handler();

# Set our custom error handler :)
set_error_handler('errors_handle');

# Our shutdown function :P
register_shutdown_function('snow_close');

# Start the output buffer
handle_gzip();

# Start up the user info ;)
init_member();

# Lets load up the main language :)
language_load('main');

# Load the permissions now...
permissions_load();

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
if(!empty($_GET['page']) && empty($_GET['action']))
{
  # Loading a page? I think so!
  require_once($source_dir. '/page.php');
  page_view();
}
else
{
  # Hmm... We must be doing an action?
  # Here is the array of actions :) to add an action
  # it is quote simple, using the following syntax:
  # 'ACTION' => array('FILE','FUNCTION'),
  $actions = array(
    'activate' => array('register.php', 'register_activate'),
    'admin' => array('admin.php', 'admin_switch'),
    'adminhelp' => array('admin.php', 'admin_help'),
    'avatar' => array('avatar.php', 'avatar_display'),
    'captcha' => array('captcha.php', 'captcha_display'),
    'email' => array('email.php', 'email_compose'),
    'help' => array('page.php', 'page_help'),
    'interface' => array('interface.php', 'interface_switch'),
    'login' => array('login.php', 'login_view'),
    'login2' => array('login.php', 'login_process'),
    'logout' => array('login.php', 'login_logout'),
    'iplookup' => array('ip.php', 'ip_lookup'),
    'online' => array('online.php', 'online_display'),
    'profile' => array('profile.php', 'profile_switch'),
    'register' => array('register.php', 'register_view'),
    'register2' => array('register.php', 'register_process'),
    'resend' => array('register.php', 'register_resend'),
    'reminder' => array('login.php', 'login_reminder1'),
    'reminder2' => array('login.php', 'login_reminder2'),
    'memberlist' => array('memberlist.php', 'memberlist_display'),
    'news' => array('news.php', 'news_switch'),
    'pm' => array('personalmessages.php', 'personalmessages_switch'),
    'stats' => array('stats.php', 'stats_display'),
    'tasks' => array('tasks.php', 'tasks_run'),
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
    require_once($source_dir. '/page.php');
    page_home();
  }
}
?>