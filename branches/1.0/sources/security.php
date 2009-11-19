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
# Security.php does a couple things like defends against brute force hopefully :)
#
# bool seucrity_flood(string $type[, int $check_num = 2]);
#   string $type - The type of flooding to check, such as login, register, posting
#                  and so on, but this just depends as this can be used by any part
#                  of the CMS and depends whether you have modifications that use it :D
#   int $check_num - The number of times they must have attempted whatever action before
#                    being considered flooding. Only counts actions that have yet to expire.
#   returns bool - Returns true if they are "flooding" from the type given or false
#
# void security_log(string $type[, int $ttl = 15]);
#   string $type - The type to save to the database, such as login, register or as
#                  noted in security_flood(), anything really, use this if you want mod
#                  maker people :P
#   int $ttl - The Time to Live. If you enter 10 and the user tries to access whatever
#              it is again is doing security_log(); within 10 seconds and you check the
#              type with security_flood(); they will be considered to be flooding.
#   returns nothing
#
# void security_mmode();
#   - When maintenance_mode is set to 1 in settings this will stop the page from
#     loading and show a screen that maintenance mode is currently enabled.
#   - A note that ?action=login and ?action=login2 will still be accessible even
#     when maintenance mode is enabled which allows people to attempt to login if
#     they are an administrator :P
#
# void security_sc([string $method = 'get']);
#   string $method - The method in which to check, get for URL (sc must be in the URL!),
#                    post for POST data (sc too!), or request which is URL or POST data.
#   returns nothing - Nothing is returned. If verification failed, an error screen will
#                     be displayed.
#

function security_flood($type, $check_num = 2)
{
  global $base_url, $db, $l, $source_dir, $settings, $user;

  # This returns whether or not they are flooding, according to you :P
  # Remove expired ones? Just maybe.
  if(mt_rand(1, 4) == 2)
    $db->query("
      DELETE FROM {$db->prefix}flood_control
      WHERE ttl < %cur_time",
      array(
        'cur_time' => array('int', time_utc())
      ));

  # Make the identifier... if you are a guest
  # then its your IP, otherwise its your member id
  $identifier = $user['is_logged'] ? $user['id'] : 'ip'. $user['ip'];

  # Now do what this is supposed to :)
  $result = $db->query("
    SELECT
      fc.type, fc.identifier, fc.ttl
    FROM {$db->prefix}flood_control AS fc
    WHERE ". ($db->case_sensitive ? 'LOWER(fc.type) = LOWER(%type)' : 'fc.type = %type'). " AND fc.identifier = %identifier AND fc.ttl > %ttl",
    array(
      'type' => array('string', $type),
      'identifier' => array('string', $identifier),
      'ttl' => array('int', time_utc())
    ));

  # Any rows? "flooding" otherwise nope :P
  return $db->num_rows($result) >= $check_num;
}

function security_log($type, $ttl = 15)
{
  global $base_url, $db, $l, $source_dir, $settings, $user;

  # Log the action :)
  # If ttl is 0, don't bother!
  if((int)$ttl > 0)
  {
    # Insert it :)
    # Once we make the identifier ;)
    $identifier = $user['is_logged'] ? $user['id'] : 'ip'. $user['ip'];
    $db->insert('insert', $db->prefix. 'flood_control',
      array(
        'type' => 'string', 'identifier' => 'string', 'ttl' => 'int'
      ),
      array(
        $type, $identifier, time_utc() + $ttl
      ),
      array('type','identifier'));
  }
}

function security_mmode()
{
  global $base_url, $db, $l, $source_dir, $settings, $user;

  # Not an administrator..? 
  # BUT login and login2 may still be accessed :)
  if(!$user['is_admin'] && $settings['maintenance_mode'] && (isset($_REQUEST['action']) && (!in_array($_REQUEST['action'], array('login', 'login2'))))) {
    # Ok... Clean the headers and load the theme :P

    $page['title'] = $l['maintenance_title'];
    theme_load('security', 'security_mmode_show');

    exit;
  }

  # Otherwise don't do anything :P
}

function security_sc($method = 'get')
{
  global $user, $l, $page;

  # This might be a simple function, but makes life easier! And more secure!

  # Just incase ;)
  $method = mb_strtolower($method);

  # So how we checkin'?
  if($method == 'get')
    $is_match = !empty($_GET['sc']) && $_GET['sc'] == $user['sc'];
  elseif($method == 'post')
    $is_match = !empty($_POST['sc']) && $_POST['sc'] == $user['sc'];
  else
    $is_match = !empty($_REQUEST['sc']) && $_REQUEST['sc'] == $user['sc'];

  # Is it not a match? Otherwise I don't really care. ;)
  if(empty($is_match))
  {
    # Sorry, something went wrong!
    language_load('errors');

    $page['title'] = $l['error_screen_title'];

    theme_load('errors', 'errors_session');

    # Stop!
    exit;
  }
}
?>