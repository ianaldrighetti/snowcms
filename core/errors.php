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

/*
  Title: Error Handler

  Function: errors_handler

  This is a function which is registered with <http://www.php.net/set_error_handler>,
  and called whenever there is an error which occurs.

  Parameters:
    mixed $error_type - The type of error which occurred, this could be something such
                        as E_USER_ERROR, E_USER_NOTICE, E_NOTICE or even a string like
                        database.
    string $error_message - The error message.
    string $error_file - The in which the error occurred.
    int $error_line - The line on which the error occurred in $error_file.
    array $error_context

  Returns:
    bool - Returns true on success, which tells PHP not to handle the error itself,
           and false on failure, which tells PHP to handle the error.

  Note:
    This function will not be called for fatal errors such as calling on functions
    which are undefined.

    While this function is not completely overloadable (since the error handler is
    set before plugins are ran), you can hook into error_handler and handle it yourself.
*/
function errors_handler($error_type, $error_message, $error_file = null, $error_line = 0, $error_context = array())
{
  global $api, $db, $member, $settings;

  # Sorry, we can only do this if the API class has been declared and instantiated.
  $handled = null;
  if(isset($api) && is_object($api))
    $api->run_hooks('error_handler', array(&$handled, $error_type, $error_message, $error_file, $error_line, $error_context));

  # Not handled? We will do it then! That is, if it's enabled.
  if($handled === null && isset($settings) && is_object($settings) && $settings->get('errors_log', 'bool'))
  {
    # In order to stop any possible recursion, don't log errors coming from this file.
    if(substr($error_file, strlen($error_file) - 10, strlen($error_file)) == 'errors.php')
      return false;

    # Just for the record, we need your IP!
    $member_id = (int)(isset($member) && is_object($member) ? $member->id() : 0);
    $member_name = isset($member) && is_object($member) ? $member->name() : '';
    $member_ip = isset($member) && is_object($member) ? $member->ip() : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);

    # Simply insert the error into the database now.
    $result = $db->insert('insert', '{db->prefix}error_log',
      array(
        'error_time' => 'int', 'member_id' => 'int', 'member_name' => 'string-80',
        'member_ip' => 'string', 'error_type' => 'string-40', 'error_message' => 'text',
        'error_file' => 'string', 'error_line' => 'int', 'error_url' => 'string',
      ),
      array(
        time_utc(), $member_id, $member_name,
        $member_ip, $error_type, $error_message,
        $error_file, $error_line, 'http://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'],
      ), array(), 'errors_handler_query');

    # If the query was a success, then PHP doesn't have to deal with it ;)
    $handled = $result->success();
  }

  return $handled;
}