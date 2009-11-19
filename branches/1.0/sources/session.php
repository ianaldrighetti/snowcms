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
# Handles sessions which are stored in the database :) If enabled, of course!
#
# void session_set_handler();
#   - Sets the session handler if enabled, and also starts the session.
#
# bool session_handler_open();
#   !!!
#
# bool session_handler_close();
#   !!!
#
# string session_handler_read();
#   !!!
#
# bool session_handler_write();
#   !!!
#
# bool session_handler_destroy();
#   !!!
#
# bool session_handler_gc();
#   !!!
#

function session_set_handler()
{
  global $db, $settings;

  # Database sessions?
  if(!empty($settings['database_sessions']))
    session_set_save_handler('session_handler_open', 'session_handler_close', 'session_handler_read',
                             'session_handler_write', 'session_handler_destroy', 'session_handler_gc');

  # Start the session :)
  session_start();
}

function session_handler_open($save_path, $session_name)
{
  # Nothing to do here but say it worked :)
  return true;
}

function session_handler_close()
{
  # Nothing here either ._.
  return true;
}

function session_handler_read($session_id)
{
  global $db;

  # Attempt to retrieve it from the database.
  $result = $db->query("
    SELECT
      data
    FROM {$db->prefix}sessions
    WHERE session_id = %session_id
    LIMIT 1",
    array(
      'session_id' => array('string', $session_id),
    ));

  # Get it, whether it exists or not =P
  @list($session_data) = $db->fetch_row($result);

  return (string)$session_data;
}

function session_handler_write($session_id, $session_data)
{
  global $db;

  # Just incase... :P
  if(!isset($db) || !is_object($db))
    return false;

  # Replace it in the database. Then we are done...
  return $db->insert('replace', $db->prefix. 'sessions',
    array(
      'session_id' => 'string', 'data' => 'text', 'saved' => 'int',
    ),
    array(
      $session_id, $session_data, time_utc(),
    ), array('session_id')) ? strlen($session_data) : false;
}

function session_handler_destroy($session_id)
{
  global $db;

  # DESTROY! DESTROY!
  return $db->query("
    DELETE FROM {$db->prefix}sessions
    WHERE session_id = %session_id
    LIMIT 1",
    array(
      'session_id' => array('string', $session_id),
    ));
}

function session_handler_gc($max_lifetime)
{
  global $db;

  # Delete old ones...
  $db->query("
    DELETE FROM {$db->prefix}sessions
    WHERE saved < %timeout",
    array(
      'timeout' => array('int', time_utc() + $max_lifetime),
    ));

  # Whatever :P
  return true;
}
?>