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

    # Use cookies, mmm...
    @ini_set('session.use_cookies', 1);

    # And use ONLY cookies! Otherwise people can do that ?PHPSESSID attack crap...
    @ini_set('session.use_only_cookies', 1);

    # Maybe you have something to add?
    $api->run_hooks('sessions');

    # Now start the session.
    session_start();
  }
}
?>
