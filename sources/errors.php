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

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# SnowCMS now logs errors through the PHP function
# set_error_handler() and this is what handles it :D
#
# void errors_handle(string $err_type, string $errstr, string $errfile[, int $errline]);
#   int $err_type - The error number, or the type of error, like E_USER_ERROR
#                or other things like that, in integer form :) but it can also be a string
#   string $errstr - The actual error
#   string $errfile - The file that the error occurred in
#   string $errline - The line that the error occurred, optional
#

function errors_handle($error_type, $errstr, $errfile, $errline = 0)
{
  global $db, $settings, $user;

  # First off do they want to log errors..?
  if(!empty($settings['log_errors']) && mb_substr($errfile, mb_strlen($errfile) - 10, mb_strlen($errfile)) != 'errors.php')
  {
    # Get the user IP
    if(!isset($user['ip']))
      $user_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    
    # Insert it... thats about it.
    return $db->insert('insert', $db->prefix. 'error_log',
      array(
        'error_time' => 'int', 'member_id' => 'int', 'member_name' => 'string-80',
        'ip' => 'string-16', 'error_url' => 'string', 'error' => 'text',
        'error_type' => 'string', 'file' => 'string', 'line' => 'int'
      ),
      array(
        time_utc(), isset($user['id']) ? $user['id'] : 0, isset($user['name']) ? $user['name'] : '',
        isset($user['ip']) ? $user['ip'] : $user_ip, 'http://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'], $errstr,
        $error_type, $errfile, $errline
      ),
      array());
  }

  return false;
}
?>