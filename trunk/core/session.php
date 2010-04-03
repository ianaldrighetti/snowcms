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

if(!defined('IN_SNOW'))
  die;

# Title: Session

# Another pluggable function, sessions!!! Woo!
if(!function_exists('init_session'))
{
  /*
    Function: init_session

    Begins the session for the current, well, session of browsing.
    If session.auto_start is enabled, this function closes that session
    to open up another one as there are some possible modifications
    done to how sessions are handled.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This is a pluggable function.
  */
  function init_session()
  {
    global $api;

    # Are sessions set to automatically start upon load? Turn it off :P
    if(@ini_get('session.auto_start') == 1)
      session_write_close();

    # Custom session save path..? Make sure it is readable and writeable.
    if(strlen($settings->get('session.save_path', 'string', '')) > 0 && is_writeable($settings->get('session.save_path', 'string')) && is_readable($settings->get('session.save_path', 'string')))
      @ini_set('session.save_path', $settings->get('session.save_path', 'string'));

    # Use cookies, mmm...
    @ini_set('session.use_cookies', 1);

    # Increase the GC probability a bit.
    @ini_set('session.gc_divisor', $settings->get('session.gc_divisor', 'int', 0) > 0 ? $settings->get('session.gc_divisor', 'int') : 200);

    # Extend the lifetime of the sessions.
    @ini_set('session.gc_maxlifetime', $settings->get('session.gc_maxlifetime', 'int', 0) > 0 ? $settings->get('session.gc_maxlifetime', 'int') : 3600);

    # Along with the cookie itself.
    @ini_set('session.cookie_lifetime', time_utc() + 432000);

    # And use ONLY cookies! Otherwise people can do that ?PHPSESSID attack crap...
    @ini_set('session.use_only_cookies', 1);

    # Only allow the cookie to be accessed via HTTP, not something like JavaScript.
    # Though, not all browsers currently support it.
    @ini_set('session.cookie_httponly', 1);

    # Maybe you have something to add, or change?
    $api->run_hooks('init_session');

    # Now start the session.
    session_start();
  }
}
?>
