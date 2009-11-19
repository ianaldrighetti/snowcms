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
# Management of permissions is done here.
#
# void permissions_membergroups();
#
# void permissions_edit();
#

function permissions_membergroups()
{
  global $db, $settings, $page, $l;
  
  # Is a member group selected?
  if(!empty($_GET['group']))
  {
    permissions_edit();
  }
  else
  {
    # Load the language
    language_load('admin_permissions');
    
    # Get the member groups
    $result = $db->query("
      SELECT
        group_id, group_name, group_name_plural, group_color, min_posts, members, allowed_pm_size
      FROM {$db->prefix}membergroups
      WHERE group_id != 1",
      array());
    
    # Add the guests group
    $page['groups'][] = array(
      'group_id' => -1,
      'group_name' => $l['guest_name'],
      'group_name_plural' => $l['guest_name_plural'],
      'group_color' => '',
      'min_posts' => -1,
      'members' => -1,
      'allowed_pm_size' => 0,
    );
    
    # Add the other members groups
    while($row = $db->fetch_assoc($result))
      $page['groups'][] = $row;
    
    # Load the theme
    $page['title'] = $l['permissions_membergroups_title'];
    
    theme_load('admin_permissions', 'permissions_membergroups_show');
  }
}

function permissions_edit()
{
  global $theme_url, $db, $settings, $page, $l;
  
  # Load the language
  language_load('admin_permissions');
  
  # Get the selected member group
  $group_id = $_GET['group'];
  
  # Load the member group's data
  $result = $db->query("
    SELECT
      group_name, allowed_pm_size
    FROM {$db->prefix}membergroups
    WHERE group_id = %group_id",
    array(
      'group_id' => array('int', $group_id),
    ));
  
  # Load the member group's data into $group and check for validity
  if((!($group = $db->fetch_assoc($result)) || $group_id == 1) && $group_id != -1)
  {
    # Invalid member group, load the theme telling them that
    $page['title'] = $l['permissions_edit_invalid_title'];
    theme_load('admin_permissions', 'permissions_edit_show_invalid');
  }
  else
  {
    # Get all of the recognised permissions.
    # 
    # Arrays contain categories of permissions. If the value of a permission
    # in this array is true, it will appear on the page. So the structure is:
    #
    # category => array(
    #   permission_1 = is_viewable,
    #   permission_2 = is_viewable,
    #   permission_3 = is_viewable,
    # )
    #
    $perms = array(
      'basic' => array(
              'view_profiles' => true,
              'view_memberlist' => true,
              'view_stats' => true,
              'news_comment' => true,
              'download_comment' => true,
              'edit_news_comment' => true,
              'edit_download_comment' => true,
              'download_downloads' => true,
              'view_pms' => true,
              'compose_pms' => true,
              'report_pms' => true,
              'edit_profile' => true,
              'edit_username' => true,
              'edit_display_name' => true,
              'edit_date_registered' => true,
              'edit_post_count' => true,
              'edit_membergroup' => true,
              'edit_email' => true,
              'edit_avatar' => true,
              'edit_signature' => true,
              'edit_profile_text' => true,
              'upload_avatars' => true,
            ),
      'forum' => array(
              'view_forum' => $settings['forum_enabled'],
              'post_topic' => $settings['forum_enabled'],
              'post_reply' => $settings['forum_enabled'],
              'post_poll' => $settings['forum_enabled'],
              'edit_post' => $settings['forum_enabled'],
              'edit_poll' => $settings['forum_enabled'],
              'delete_post' => $settings['forum_enabled'],
              'delete_topic' => $settings['forum_enabled'],
              'remove_poll' => $settings['forum_enabled'],
              'view_results' => $settings['forum_enabled'],
              'cast_vote' => $settings['forum_enabled'],
            ),
      'mod' => array(
              'view_ips' => true,
              'moderate_pms' => true,
              'ban_member' => true,
              'unban_member' => true,
              'suspend_member' => true,
              'unsuspend_member' => true,
              'add_poll_any' => $settings['forum_enabled'],
              'edit_profile_any' => true,
              'edit_username_any' => true,
              'edit_display_name_any' => true,
              'edit_date_registered_any' => true,
              'edit_post_count_any' => true,
              'edit_membergroup_any' => true,
              'edit_email_any' => true,
              'edit_avatar_any' => true,
              'edit_signature_any' => true,
              'edit_profile_text_any' => true,
              'edit_post_any' => $settings['forum_enabled'],
              'edit_news_comment_any' => true,
              'edit_download_comment_any' => true,
              'edit_poll_any' => $settings['forum_enabled'],
              'delete_post_any' => $settings['forum_enabled'],
              'delete_news_comment_any' => true,
              'delete_download_comment_any' => true,
              'delete_topic_any' => $settings['forum_enabled'],
              'remove_poll_any' => $settings['forum_enabled'],
            ),
      'admin' => array(
              'view_admin_panel' => true,
              'manage_settings' => true,
              'manage_mail' => true,
              'manage_registration' => true,
              'manage_themes' => true,
              'manage_emoticons' => true,
              'manage_news' => true,
              'manage_downloads' => true,
              'manage_menus' => true,
              'manage_pages_snowtext' => true,
              'manage_pages_html' => true,
              'manage_pages_bbcode' => true,
              'manage_members' => true,
              'register_members' => true,
              'manage_membergroups' => true,
              'manage_forum' => $settings['forum_enabled'],
              'manage_permissions' => true,
              'maintain_database' => true,
              'maintain_forum' => $settings['forum_enabled'],
              'maintain_tasks' => true,
              'view_error_log' => true,
            ),
      'mods' => array(
              'install_mods' => true,
              'uninstall_mods' => true,
              'manage_mods' => true,
            ),
    );
    
    $categories = $perms;
    
    # Saving permissions?
    if(isset($_POST['process']))
    {
      $perms_insert = array();
      $perms_delete = array();
      
      # Go through each category
      foreach($categories as $category => $perms)
      {
        # Go through each permission
        foreach($perms as $perm => $visible)
        {
          # Add the permission only if is checked.
          if(!empty($_POST[$perm]) && $visible)
          {
            $perms_insert[] = $perm;
            $perms_delete[] = $perm;
          }
          elseif($visible)
          {
            $perms_delete[] = $perm;
          }
        }
      }
      
      # Delete all the visible permissions, the checked ones will be inserted again
      # Create the where clause of the SQL
      $perms_delete = implode(',', $perms_delete);
      # Delete them
      $db->query("
        DELETE FROM {$db->prefix}permissions
        WHERE group_id = %group_id AND FIND_IN_SET(what, '$perms_delete')",
        array(
          'group_id' => array('int', $group_id),
        ));
      
      # Get the checked permissins in an array suitable for $db->insert()
      foreach($perms_insert as $key => $perm)
      {
        $perms_insert[$key] = array($group_id, $perm, 1);
      }
      
      # Insert the checked permissions
      $db->insert('insert', $db->prefix. 'permissions',
        array(
          'group_id' => 'int', 'what' => 'string', 'can' => 'int',
        ),
        $perms_insert,
        array());
      
      $db->query("
        UPDATE {$db->prefix}membergroups
          SET allowed_pm_size = %allowed_pm_size
        WHERE group_id = %group_id",
        array(
          'group_id' => array('int', $group_id),
          'allowed_pm_size' => array('int', isset($_POST['allowed_pm_size']) ? $_POST['allowed_pm_size'] : 0),
        ));
      
      # Redirect
      redirect('index.php?action=admin;sa=permissions;area=groups;saved');
    }
    
    # Get the permissions for the selected member group from the database
    $result = $db->query("
      SELECT
        what, can
      FROM {$db->prefix}permissions
      WHERE group_id = %group_id",
      array(
        'group_id' => array('int', $group_id),
      ));
    
    $perms_can = array();
    while($row = $db->fetch_assoc($result))
    {
      $perms_can[$row['what']] = $row['can'];
    }
    
    $totals = array();
    $selected = array();
    # Go through each category
    foreach($categories as $category => $perms)
    {
      $totals[$category] = 0;
      $selected[$category] = 0;
      # Go through each permission
      foreach($perms as $perm => $visible)
      {
        # If the permission is invisible, unset it
        if(!$visible)
        {
          unset($categories[$category][$perm]);
        }
        else
        {
          # The permission is visible, okay then add whether it is already set or not
          $totals[$category] += 1;
          if($categories[$category][$perm] = !empty($perms_can[$perm]))
          {
            $selected[$category] += 1;
          }
        }
      }
      
      # If this category has no visible permissions, unset it
      if(!$categories[$category])
      {
        unset($categories[$category]);
      }
    }
    
    # Put the variables into the page array
    $page['perms'] = $categories;
    $page['perms_totals'] = $totals;
    $page['perms_selected'] = $selected;
    $page['group'] = $group;
    $page['group']['allowed_pm_size'] = $group['allowed_pm_size'] ? $group['allowed_pm_size'] : '';
    
    # Add the JavaScript
    $page['scripts'][] = $theme_url. '/default/js/admin_permissions.js';
    
    # Load the theme
    $page['title'] = sprintf($l['permissions_edit_title'], $group['group_name']);
    
    theme_load('admin_permissions', 'permissions_edit_show');
  }
}
?>