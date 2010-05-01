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

$start_time = microtime(true);

# Magic quotes, what a joke!!!
if(function_exists('set_magic_quotes_runtime'))
  @set_magic_quotes_runtime(0);

# All time/date stuff should be considered UTC, makes life easier!
if(function_exists('date_default_timezone_set'))
  date_default_timezone_set('UTC');
else
  @ini_set('date.timezone', 'UTC');

# We are currently in SnowCMS :)
define('IN_SNOW', true, true);

# We want to see those errors...
error_reporting(E_STRICT | E_ALL);

# config.php not exist? A good sign SnowCMS isn't installed :P
if(!file_exists('config.php'))
{
  header('Location: setup.php');
  exit;
}

require(dirname(__FILE__). '/config.php');
require_once($core_dir. '/mitigate_globals.php');

# register_globals is horrible, just plain bad...
mitigate_globals();

# Now load up the database, very important you know!
require($core_dir. '/database.php');

load_database();

# Set the error handler as soon as possible!
require($core_dir. '/errors.php');

# Even though plugins can't overload this function, they can hook into it.
set_error_handler('errors_handler');

# Now that our database is loaded up, let's get the API started, very important you know?
require($core_dir. '/api.class.php');

# Call on load_api which is in api.class.php :)
load_api();

# Just a hook before anything else major is done.
$api->run_hooks('pre_start');

require($core_dir. '/time.php');
require($core_dir. '/validation.class.php');
require($core_dir. '/settings.class.php');

# Load up the validation and settings class :)
init_validation();
init_settings();

require($core_dir. '/func.php');

# Initialize the $func array.
init_func();

require($core_dir. '/compat.php');
require($core_dir. '/clean_request.php');

# We need to filter out some baaaad stuff, like any register_globals issues and other security things.
clean_request();

require($core_dir. '/session.php');

# Start up the session.
init_session();

require($core_dir. '/member.class.php');

# Now get that member stuff started up!
init_member();

# Include our l() function for translation :)
require($core_dir. '/l.php');

# Tasks tool, don't wanna forget that!
require($core_dir. '/tasks.class.php');

init_tasks();

require($core_dir. '/theme.class.php');

# Initialize the theme!!!
init_theme();

# Now there is some stuff that the system itself needs to take care of :)
require($core_dir. '/core.php');

init_core();

# Now it is time to check and see if an event is being requested.
if(!empty($_SERVER['QUERY_STRING']))
{
  # One is, but is it registered? Let's see!
  $event = $api->return_event($_SERVER['QUERY_STRING']);
}
else
{
  # We shall use the default event!
  $event = $api->return_event($settings->get('default_event', 'string', ''));
}

# So did we get an event, or not? If not, that's bad news!!!
if(!empty($event))
{
  # The event exists! Awesome!
  # Include the right file, if we need too.
  if(!empty($event['filename']) && !is_callable($event['callback']))
  {
    require_once($event['filename']);
  }

  # Now call on the callback, after all, that's what it's for!
  call_user_func($event['callback']);
}
else
{
  # There is an event request, but none to go with it, so add a noindex robots tag
  # just incase, we don't want anything to index this since it doesn't exist!
  $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));

  # Now show an UH OH! page.
  $theme->set_title(l('An error has occurred'));

  $theme->header();

  echo '
    <h1>', l('Request error'), '</h1>
    <p>', l('Sorry, but we could not find a way to properly execute your request.'), '</p>';

  $theme->footer();
}
?>