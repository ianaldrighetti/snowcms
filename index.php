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

# Magic quotes, what a joke!!!
if(function_exists('set_magic_quotes_runtime'))
  @set_magic_quotes_runtime(0);

# All times from time() should be UTC ;)
@ini_set('date.timezone', 'UTC');

$start_time = microtime(true);

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

# Now that our database is loaded up, let's get the API started, very important you know?
require($core_dir. '/api.class.php');

# Call on load_api which is in api.class.php :)
load_api();

# Just a hook before anything else major is done.
$api->run_hook('pre_start');

require($core_dir. '/settings.class.php');

# Load up the settings :)
init_settings();

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

require($core_dir. '/theme.class.php');

# Initialize the theme!!!
init_theme();

# Whether or not their is an action in the address, there is still some sort of
# action going on, whether you like it or not! If there is no action (or a valid one)
# in the address, we will spoof it...
if(empty($_GET['action']) || (!empty($_GET['action']) && !$api->action_registered($_GET['action'])))
{
  # Use the default action in the settings...
  $_GET['action'] = $settings->get('default_action');

  # Now if there is an action in the URL, that means it is invalid, and we
  # don't want spiders to index that crap, do we?
  if(!empty($_GET['action']))
    $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));
}

# Do the requested action, if it exists :P
if(!empty($_GET['action']) && $api->action_registered($_GET['action']))
{
  $action = $api->return_action($_GET['action']);

  if(!empty($action[1]))
    require_once($action[1]);

  $action[0]();
}
else
{
  # Uh oh, this isn't good at all...
}

# Initialize the current members session, if any, though...
echo 'Executed in ', round(microtime(true) - $start_time, 6), ' seconds with ', $db->num_queries, ' queries.';
?>