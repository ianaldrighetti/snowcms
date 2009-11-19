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
# Permissions.php does all the things the other functions are to afraid to
# do, deny! Lol...
#
# void permissions_load([bool $load_forum = false]);
#   bool $load_forum - Whether or not to also load
#                      permissions from the board_permissions
#                      table
#   - Permissions are loaded into the $user['permissions'] array
#
# bool can(string $permission[, int $board_id = 0]);
#   string $permission - The permission you want to see if
#                        they can do, like view_forum, moderate_pages
#                        and so on and so forth...
#   int $board_id - The board ID, only set this if you need to check
#                   a board specific permission... default is 0 which
#                   is none ;)
#   return bool - Returns true or false, true if they are allowed to do
#                 said permission, false if not.
#
# void error_screen([mixed $permission = false[, string $error_tpl = null[, int $board_id = 0]]]);
#   - A great security feature :P if the permission given is not allowed
#     an error screen saying your not allowed to do it will appear :P
#   mixed $permission - The permission you want to be checked if the user
#                       can do or not (string), however if you set it to false
#                       the error screen will be shown regardless. Or, if you
#                       want, it can be an array of permissions. Please note
#                       that in order to NOT have the error screen shown, the
#                       user must have permission on ALL those permissions in the
#                       array!
#   string $error_tpl - The custom error template to be shown, give the name
#                       of the template file that has the errorScreen() function
#                       in it to be called on, if you leave this blank the default
#                       will be shown.
#   int $board_id - The specific board ID of the permission to be checked
#                   Default is 0, which is none at all
#

function permissions_load($load_forum = false)
{
  global $db, $user;

  # Are you an admin? If so ignore this crap, because you can do EVERYTHING!
  if(!$user['is_admin'])
  {
    # Do we need to make an array? :P
    if(!isset($user['permissions']))
      $user['permissions'] = array();

    # Select them all ;)
    $result = $db->query("
      SELECT
        p.group_id, p.what, p.can
      FROM {$db->prefix}permissions AS p
      WHERE (p.group_id = %group_id OR p.group_id = %post_group) AND p.can = 1",
      array(
        'group_id' => array('int', $user['group']['id']),
        'post_group' => array('int', $user['post_group']['id'])
      ));

    # K, well... load them up...
    while($row = $db->fetch_assoc($result))
      $user['permissions'][$row['what']] = !empty($row['can']);

    # Loading board permissions perhaps?
    if($load_forum)
    {
      # Ok...
      $result = $db->query("
        SELECT
          p.board_id, p.group_id, p.what, p.can
        FROM {$db->prefix}board_permissions AS p
        WHERE (p.group_id = %group_id OR p.group_id = %post_group) AND p.can = 1",
        array(
          'group_id' => array('int', $user['group']['id']),
          'post_group' => array('int', $user['post_group']['id'])
        ));

      $user['permissions']['_board_permissions'] = array();
      while($row = $db->fetch_assoc($result))
      {
        # This is a bit different, since we have
        # specific boards, well... we improvise ^^
        $user['permissions']['_board_permissions'][$row['board_id']. '-'. $row['what']] = !empty($row['can']);
      }
    }
  }
}

function can($permission, $board_id = 0)
{
  global $user;

  if($user['is_admin'])
    return true;

  # So we looking for a regular permission or..?
  if(empty($board_id))
    return !empty($user['permissions'][$permission]);
  else
    # We are doing it via a board check :P
    return !empty($user['permissions']['_board_permissions'][$board_id. '-'. $permission]);
}

function error_screen($permission = false, $error_tpl = null, $board_id = 0)
{
  global $board_url, $db, $page, $l, $settings, $user;

  # Is it an array..?
  if(is_array($permission) && count($permission))
  {
    # Now lets check!
    foreach($permission as $perm)
    {
      # So can they..?
      if(!can($perm, (int)$board_id))
      {
        # They can't! Show the screen!
        $show_screen = true;
        break;
      }
    }
  }
  # Nope, not an array, just a single, lonesome permission...
  elseif(!can($permission, (int)$board_id) || $permission === false)
  {
    # Show the screen...
    $show_screen = true;
  }

  # Show the screen..?
  if(!empty($show_screen))
  {
    # Load the Error language
    language_load('errors');

    # Set the title
    $page['title'] = $l['error_screen_title'];

    # Don't index this
    $page['no_index'] = true;
    
    # Thats about it... Load the template.
    theme_load(empty($error_tpl) ? 'errors' : $error_tpl, 'errors_screen');
    
    exit;
  }
}
?>