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
# Interface.php should be used for ALL AJAX communications to the CMS
# all you do is add an "interface" name to the array in interfaceSwitch();
#
# void interface_switch();
#   - Accessed via ?action=interface
#   - This should be used for all AJAX communication to the SnowCMS site
#
# void register_validate_name_ajax();
#   - Called on by ?action=interface;sa=user_check
#   - Have the POST data field username filled and this will output true
#     or false depending upon whether or not the username is allowed or not
#   - This checks length, whether the username is taken and if the username
#     is banned or not
#

function interface_switch()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # Here you can add Interface actions to the below array with this
  # simple template:
  # 'action' => array('File the Function is in', 'Function to call on'),
  # Then you can access the action through AJAX or some kind of way by:
  # BASE_URL/index.php?action=interface;sa=ACTION_NAME
  # And you then have an interface to have AJAX communication with SnowCMS
  # Developer note... You can of course put the function in here, or in
  # your own file... whichever you prefer :)
  $interfaceActions = array(
    'adminVerify' => array('admin.php', 'admin_verify_ajax'),
    'ajax_quote' => array('post.php', 'post_quote_ajax'),
    'editBoard' => array('admin_forum.php', 'forum_board_edit_ajax'),
    'editCategory' => array('admin_forum.php', 'forum_category_edit_ajax'),
    'editMembergroups' => array('admin_membergroups.php', 'membergroups_edit_ajax'),
    'edit_menus' => array('admin_menus.php', 'menus_edit_ajax'),
    'edit_news' => array('admin_news.php', 'news_edit_ajax'),
    'post_preview' => array('post.php', 'post_preview_ajax'),
    'user_check' => array('interface.php', 'register_validate_name_ajax'),
    'user_suggest' => array('interface.php', 'interface_user_suggest'),
    'settings_theme' => array('admin_settings.php', 'settings_theme_ajax'),
  );

  # So check if the action exists ;)
  if(is_array($interfaceActions[$_REQUEST['sa']]))
  {
    # Get the file holding the function
    require_once($source_dir. '/'. $interfaceActions[$_REQUEST['sa']][0]);

    # Now call on the function and we are done here
    $interfaceActions[$_REQUEST['sa']][1]();
    exit;
  }
  else
    # Invalid option...
    die('Invalid sub action');
}

function register_validate_name_ajax()
{
  global $base_url, $db, $l, $page, $source_dir, $settings, $user;

  # Now lets check if the username is allowed :)
  $requested_name = !empty($_REQUEST['requested_name']) ? $_REQUEST['requested_name'] : '';

  # Lets see, lets check if the name is allowed, so we need register.php
  require_once($source_dir. '/register.php');

  # Use the nameAllowed function, which checks banned names and whether
  # the username is in use by someone else ;)
  # If its allowed, we echo 1, otherwise 0
  if(register_validate_name($requested_name))
    echo '1';
  else
    echo '0';
}

function interface_user_suggest()
{
  global $db, $user;

  if($user['is_guest'])
    exit;

  $search = !empty($_POST['search']) ? $_POST['search'] : '';

  if(($cache = cache_get('user_suggest-'. $search)) != null)
    $output = $cache;
  elseif(!empty($search))
  {
    $result = $db->query("
      SELECT
        displayName
      FROM {$db->prefix}members
      WHERE LOWER(displayName) LIKE LOWER(%search)
      ORDER BY displayName ASC
      LIMIT 20",
      array(
        'search' => array('string', $search. '%'),
      ));

    $output = array();
    while($row = $db->fetch_assoc($result))
      $output[] = $row['displayName'];

    cache_save('user_suggest-'. $search, $output, 30);
  }

  echo json_encode(!empty($output) ? $output : array());
  exit;
}
?>