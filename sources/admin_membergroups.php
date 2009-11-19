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
# Managing the member groups.
#
# void membergroups_add();
#   - Handle the adding of member groups.
#
# void membergroups_manage();
#   - Handle the list of member groups.
#
# void membergroups_process();
#   - Handle the saving process of adding a member group.
#
# void membergroups_edit_ajax();
#   - Handle AJAX requests for editing member groups.
#

function membergroups_add()
{
  global $base_url, $db, $page, $user, $source_dir, $l;

}

function membergroups_manage()
{
  global $base_url, $theme_url, $db, $page, $settings, $l;
  
  # Get the language
  language_load('admin_membergroups');
  
  # Are we deleting one of the member groups?
  if(!empty($_GET['del']))
  {
    # Make sure they aren't the admin or default group
    if($_GET['del'] != 1 && $_GET['del'] != $settings['registration_group'])
    {
      # Move all members in this member group to the default member group
      $db->query("
        UPDATE {$db->prefix}members
        SET group_id = %registration_group
        WHERE group_id = %group_id
        LIMIT 1",
        array(
          'group_id' => array('int', $_GET['del']),
          'registration_group' => array('int', $settings['registration_group']),
        ));
      
      # Delete the member group from the database
      $db->query("
        DELETE FROM {$db->prefix}membergroups
        WHERE group_id = %group_id
        LIMIT 1",
        array(
          'group_id' => array('int', $_GET['del']),
        ));
      
      # Redirect
      redirect('index.php?action=admin;sa=members;area=membergroups');
    }
    # Trying to delete admin group?
    elseif($_GET['del'] == 1)
    {
      $page['error'] = $l['admin_membergroups_error_delete_admin'];
    }
    # Then they must be trying to delete the registration group
    else
    {
      $page['error'] = $l['admin_membergroups_error_delete_registration'];
    }
  }

  # Get the member groups from the database
  $result = $db->query("
    SELECT
      group_id  AS id, group_name AS name_singular, group_name_plural AS name_plural, group_color AS color, min_posts, stars
    FROM {$db->prefix}membergroups
    ORDER BY group_name",
    array());
  
  # JavaScripts
  $page['scripts'][] = $theme_url. '/default/js/edit_membergroups.js';
  $page['js_vars']['save_text'] = $l['save'];
  $page['js_vars']['cancel_text'] = $l['cancel'];
  $page['js_vars']['total_groups'] = $db->num_rows($result);
  
  # Format the member groups in an array
  $page['membergroups'] = array();
  while($row = $db->fetch_assoc($result))
  {
    $row['stars'] = array(
      'amount' => mb_substr($row['stars'], 0, mb_strpos($row['stars'], '|')),
      'image' => mb_substr($row['stars'], mb_strpos($row['stars'], '|') + 1),
    );
    $row['order'] = $row['id'];
    $page['membergroups'][] = $row;
  }
  
  # Set the title
  $page['title'] = $l['admin_membergroups_title'];
  
  # Load the theme
  theme_load('admin_membergroups', 'membergroups_manage_show');
}

function membergroups_process($data)
{
  global $db;

}

function membergroups_edit_ajax()
{
  global $db, $l, $settings, $user;
  
  language_load('admin_membergroups');

  # Can you do this..?
  if(!can('manage_membergroups'))
  {
    echo json_encode(array('error' => $l['membergroups_ajax_not_allowed']));
    exit;
  }

  # Our output array.
  $output = array('error' => '');

  # Lets see, saving..?
  if(isset($_GET['save']))
  {
    # Get the link name.
    $group_name_plural = !empty($_POST['group_name_plural']) ? $_POST['group_name_plural'] : '';
    $group_name_singular = !empty($_POST['group_name_singular']) ? $_POST['group_name_singular'] : '';
    $group_color = !empty($_POST['group_color']) ? $_POST['group_color'] : '';
    $min_posts = !empty($_POST['min_posts']) ? $_POST['min_posts'] : '';

    # Make sure its not totally empty...
    if(mb_strlen($group_name_plural) && mb_strlen($group_name_singular))
    {
      # HTML special!
      $group_name_plural = htmlspecialchars($group_name_plural, ENT_QUOTES, 'UTF-8');
      $group_name_singular = htmlspecialchars($group_name_singular, ENT_QUOTES, 'UTF-8');
      $group_color = htmlspecialchars($group_color, ENT_QUOTES, 'UTF-8');
      $min_posts = (int)$min_posts;

      # Update it!
      $db->query("
        UPDATE {$db->prefix}membergroups
        SET
          group_name_plural = %group_name_plural, group_name = %group_name, group_color = %group_color,
          min_posts = %min_posts
        WHERE group_id = %group_id
        LIMIT 1",
        array(
          'group_id' => array('int', !empty($_POST['group_id']) ? $_POST['group_id'] : 0),
          'group_name_plural' => array('string', $group_name_plural),
          'group_name' => array('string', $group_name_singular),
          'group_color' => array('string', $group_color),
          'min_posts' => array('int', $min_posts),
        ));

      # Give them the info
      $output['group_name_plural'] = $group_name_plural;
      $output['group_name_singular'] = $group_name_singular;
      $output['group_color'] = $group_color;
      $output['min_posts'] = $min_posts;
    }
    else
      $output['error'] = $l['membergroups_ajax_board_name_error'];
  }
  else
  {
    # Nope, get the link info, the name at least and ID...
    $result = $db->query("
      SELECT
        group_id, group_name_plural, group_name, group_color, min_posts, stars
      FROM {$db->prefix}membergroups
      WHERE group_id = %group_id
      LIMIT 1",
      array(
        'group_id' => array('int', !empty($_POST['group_id']) ? $_POST['group_id'] : 0),
      ));

    # Does it exist?
    if($db->num_rows($result))
    {
      # Yeah, it exists.
      @list($id, $name_plural, $name_singular, $color, $min_posts, $stars) = $db->fetch_row($result);
      $output['group_id'] = $id;
      $output['group_name_plural'] = $name_plural;
      $output['group_name_singular'] = $name_singular;
      $output['group_color'] = $color;
      $output['group_min_posts'] = $min_posts;
      $output['stars'] = $stars;
    }
    else
      $output['error'] = $l['membergroups_ajax_board_not_found'];
  }

  # Output our JSON array and we got it.
  echo json_encode($output);
}
?>