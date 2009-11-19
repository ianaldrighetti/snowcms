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
# Pages... All your yummy content :P
#
# void pages_add();
#
# void pages_manage();
#
# void pages_list();
#
# void pages_edit_ajax();
#

function pages_add()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $theme_url, $user;

  # Can you access this? ._.
  error_screen(array('add_pages', 'moderate_pages'));
  
  # Set default (Empty) page data
  $page['page'] = array(
        'title' => '',
        'content' => '',
        'type' => 2,
        'is_viewable' => 1,
        'who_view' => array('all'),
      );
  
  # Title isn't declared as empty yet
  $page['title_empty'] = false;
  
  # Processing the adding of a page?
  if(isset($_POST['add_page']))
  {
    if(!empty($_POST['page_title']))
    {
      # Get the time now, because the following time_utc() gettings will need to have the EXACT same time
      $now = time_utc();
      
      # Add the page...
      $db->insert('insert', $db->prefix. 'pages',
        array(
          'member_id' => 'int', 'member_name' => 'string', 'modified_member_id' => 'int',
          'modified_name' => 'string', 'created_time' => 'int', 'modified_time' => 'int',
          'page_title' => 'string', 'content' => 'text', 'type' => 'int',
          'is_viewable' => 'int', 'who_view' => 'string',
        ),
        array(
          $user['id'], $user['name'], $user['id'], $user['name'], $now, $now,
          entities($_POST['page_title']),
          isset($_POST['type']) ? $_POST['content'] : entities($_POST['content']),
          isset($_POST['type']) && $_POST['type'] >= 0 && $_POST['type'] <= 2 ? $_POST['type'] : 2, !empty($_POST['is_viewable']) ? 1 : 0,
          # Filter out unchecked values and implode the keys of the remaining groups
          implode(',',array_keys(array_filter($_POST['groups']))),
        ),
        array());
      
      # Update total pages
      update_settings(array('total_pages' => $settings['total_pages'] + 1));
      
      # Redirect
      redirect('index.php?action=admin;sa=pages;area=manage;created');
    }
    # No page title?
    else
    {
      $page['title_empty'] = true;
      
      # Get the data that was entered, to be displayed in the form
      $page['page'] = array(
        'title' => isset($_POST['page_title']) ? entities($_POST['page_title']) : '',
        'content' => isset($_POST['content']) ? entities($_POST['content']) : '',
        'type' => isset($_POST['type']) && $_POST['type'] >= 0 && $_POST['type'] <= 2 ? $_POST['type'] : 2,
        'is_viewable' => isset($_POST['is_viewable']) ? (int)$_POST['is_viewable'] : 1,
        'who_view' => isset($_POST['page_title']) ? explode(',', implode(',', array_keys(array_filter($_POST['groups'])))) : array('all'),
      );
    }
  }
  
  # Okay, page language please.
  language_load('admin_pages');

  # We need a list of member groups that way
  # you can choose which can and cannot access it ;)
  $result = $db->query("
    SELECT
      group_id, group_name_plural AS group_name, min_posts
    FROM {$db->prefix}membergroups
    WHERE group_id != 1",
    array());

  # Lets load them up...
  # Add guests, as they aren't really a group in the database.
  $page['groups'] = array(
    array(
      'id' => -1,
      'name' => $l['guest_name_plural'],
      'post_group' => false,
    ),
  );
  while($row = $db->fetch_assoc($result))
    # Add it to the array.
    $page['groups'][] = array(
      'id' => $row['group_id'],
      'name' => $row['group_name'],
      'post_group' => $row['min_posts'] > -1 ? true : false,
    );

  # Not much more to do.
  $page['title'] = $l['admin_pages_create_title'];

  $page['scripts'][] = $theme_url. '/default/js/page_editor.js';

  theme_load('admin_pages', 'pages_add_show');
}

function pages_manage()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $theme_url, $user;

  # Can you do this?
  error_screen(array('manage_pages', 'moderate_pages'));

  # Language please...
  language_load('admin_pages');
  
  # We might be editing a page?
  if(!empty($_GET['id']))
  {
    # Title isn't declared as empty yet
    $page['title_empty'] = false;
    
    # We sure do! :)
    $result = $db->query("
      SELECT
        page_id, member_id, modified_time, page_title,
        content, type, is_viewable, who_view
      FROM {$db->prefix}pages
      WHERE page_id = %page_id
      LIMIT 1",
      array(
        'page_id' => array('int', $_GET['id']),
      ));

    # So does it even exist?
    if($db->num_rows($result))
    {
      $row = $db->fetch_assoc($result);

      # Just because it exists doesn't mean you can access it ;)
      if(can('moderate_pages') || can('edit_pages') || (can('edit_own_pages') && $row['member_id'] == $user['id']))
      {
        # Okay. You can edit it,
        
        # Are we saving changes?
        if(isset($_POST['edit_page']))
        {
          # The page title can't be empty
          if(!empty($_POST['page_title']))
          {
            # Get the time now, because the following time_utc() gettings will need to have the EXACT same time
            $now = time_utc();

            # Update the page in the database
            $db->query("
              UPDATE {$db->prefix}pages
              SET
                member_id = %member_id, member_name = %member_name, modified_member_id = %modified_member_id,
                modified_name = %modified_name, created_time = %created_time, modified_time = %modified_time,
                page_title = %page_title, content = %content, type = %type, is_viewable = %is_viewable,
                who_view = %who_view
              WHERE page_id = %page_id
              LIMIT 1",
              array(
                'member_id' => array('int', $user['id']), 'member_name' => array('text', $user['name']),
                'modified_member_id' => array('int', $user['id']), 'modified_name' => array('text', $user['name']),
                'created_time' => array('int', $now), 'modified_time' => array('int', $now),
                'page_title' => array('text', entities($_POST['page_title'])),
                'content' => array('text', isset($_POST['type']) ? $_POST['content'] : entities($_POST['content'])),
                'type' => array('int', isset($_POST['type']) && $_POST['type'] >= 0 && $_POST['type'] <= 2 ? $_POST['type'] : 2),
                'is_viewable' => array('int', !empty($_POST['is_viewable']) ? 1 : 0),
                # Filter out unchecked values and implode the keys of the remaining groups
                'who_view' => array('text', implode(',', array_keys(array_filter($_POST['groups'])))),
                'page_id' => array('int', $row['page_id']),
              ),
              array());

            # Clear this page's cache :P
            cache_remove('page_id-'. $row['page_id']);

            # Redirect
            redirect('index.php?action=admin;sa=pages;area=manage;edited');
          }
          else
            $page['title_empty'] = true;
        }
        
        # So we're not saving changes? Then lets load up some stuff!
        $page['page'] = array(
          'id' => $row['page_id'],
          'creator_id' => $row['member_id'],
          'modified_time' => $row['modified_time'],
          'display_warning' => $row['modified_time'] + 300 > time_utc() ? true : false,
          'title' => isset($_POST['page_title']) ? entities($_POST['page_title']) : $row['page_title'],
          'content' => isset($_POST['content']) ? entities($_POST['content']) : ($row['type'] ? entities($row['content']) : $row['content']),
          'type' => isset($_POST['type']) && $_POST['type'] >= 0 && $_POST['type'] <= 2 ? $_POST['type'] : $row['type'],
          'is_viewable' => isset($_POST['is_viewable']) ? (int)$_POST['is_viewable'] : !empty($row['is_viewable']),
          'who_view' => isset($_POST['page_title']) ? explode(',', implode(',', array_keys(array_filter($_POST['groups'])))) : explode(',', $row['who_view']),
        );
        
        # So load a list of member groups.
        $result = $db->query("
          SELECT
            group_id, group_name_plural AS group_name, min_posts
          FROM {$db->prefix}membergroups
          WHERE group_id != 1",
          array());

        # Lets load them up...
        # Add guests, as they aren't really a group in the database.
        $page['groups'] = array(
          array(
            'id' => -1,
            'name' => $l['guest_name_plural'],
            'post_group' => false,
          ),
        );
        while($row = $db->fetch_assoc($result))
          # Add it to the array.
          $page['groups'][] = array(
            'id' => $row['group_id'],
            'name' => $row['group_name'],
            'post_group' => $row['min_posts'] > -1 ? true : false,
            'selected' => in_array($row['group_id'], $page['page']['who_view']),
          );

        # Now that all that is done, we may show the editor :)
        $page['title'] = $l['admin_pages_edit_title'];

        # Our little helper :)
        $page['scripts'][] = $theme_url. '/default/js/page_editor.js';

        theme_load('admin_pages', 'pages_manage_show_edit');
      }
      else
        # You can't edit this! ._.
        error_screen();
    }
    else
    {
      # That doesn't exist silly!
      $page['title'] = $l['page_edit_not_exist'];

      theme_load('admin_pages', 'pages_manageshow_invalid');
    }
  }
  else
  {
    # Show the page list
    pages_list();
  }
}

function pages_list()
{
  global $db, $page, $settings, $l, $base_url;
  
  # Nope, they're not doing this, yet
  $page['homepage_delete'] = false;
  
  # Are we deleting a page?
  if(isset($_GET['delete']))
  {
    # Check if they're trying to delete the homepage
    if($_GET['delete'] == 1)
    {
      # It's the homepage, we can't delete it
      # Mark an error
      $page['homepage_delete'] = true;
    }
    else
    {
      # Not the homepage, let's delete it
      $db->query("
        DELETE FROM {$db->prefix}pages
        WHERE page_id = %page_id",
        array(
          'page_id' => array('int', $_GET['delete']),
        ));
      
      # Update total pages
      update_settings(array('total_pages' => $settings['total_pages'] - 1));
      
      # Redirect them
      redirect('index.php?action=admin;sa=pages;area=manage');
    }
  }
  
  # Check for successful things
  $page['page_created'] = isset($_GET['created']);
  $page['page_edited'] = isset($_GET['edited']);
  
  # Define possible things to sort by
  $sorts = array(
      'id' => 'page_id',
      'title' => 'page_title',
      'created' => 'created_time',
      'modified' => 'modified_time',
      'views' => 'num_views',
    );
  
  # Get the sort URL stuff
  $page['sort'] = isset($_GET['sort']) && in_array($_GET['sort'],array_keys($sorts)) ? $_GET['sort'] : 'title';
  $page['sort'] .= isset($_GET['desc']) ? ';desc' : '';
  
  # Get the sort SQL stuff
  $sort_sql = isset($_GET['sort']) && in_array($_GET['sort'],array_keys($sorts)) ? $sorts[$_GET['sort']] : 'page_title';
  $sort_sql .= isset($_GET['desc']) ? ' DESC' : '';
  
  # Get the total pages
  $num_pages = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}pages",
    array());
  $num_pages = $db->fetch_assoc($num_pages);
  $num_pages = $num_pages['COUNT(*)'];
  
  # Deal with the pagination stuff
  $page_num = isset($_GET['page']) ? (int)$_GET['page'] : 0;
  $page['pagination'] = pagination_create($base_url. '/index.php?action=admin;sa=pages;area=manage;sort='. $page['sort'],$page_num,$num_pages);
  
  # Get the pages from the database
  $result = $db->query("
    SELECT
      page_id, page_title, created_time, modified_time, num_views
    FROM {$db->prefix}pages
    ORDER BY $sort_sql
    LIMIT $page_num, 10",
    array());
  
  # Format the pages in a 2D array
  while($row = $db->fetch_assoc($result))
    $page['pages'][] = $row;
  
  # Set the title
  $page['title'] = $l['admin_pages_list_title'];
  
  # Load the theme
  theme_load('admin_pages', 'pages_list_show');
}
?>