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
# News! News! Get your news here! :P
#
# void news_add();
#   - Used to add new news articles to your site, accessed
#     via index.php?action=admin;sa=news;area=add
#
# void news_manage();
#   - Allows you to manage news, such as editing and deleting
#     current news items. Accessed via index.php?action=admin;sa=news;area=manage
#
# void news_categories();
#   - Manages categories, deleting, editing and creation.
#     Accessed via index.php?action=admin;sa=news;area=categories
#
# void news_edit_ajax();
#   - Provides the AJAX interface to allow the updating of news articles,
#     its cool :D accessed via index.php?action=interface;sa=edit_news
#     Expects the POST data values of the same of adding and editing news
#     articles ;)
#

function news_add()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # You must have permission ;)
  error_screen(array('add_news', 'moderate_news'));

  language_load('admin_news');

  # We don't do a whole lot here except load categories :P
  $result = $db->query("
    SELECT
      cat_id, cat_name
    FROM {$db->prefix}news_categories
    ORDER BY cat_name",
    array());

  # Loop through them all... If any... :P
  $page['categories'] = array(
                          array(
                            'cat_id' => '0',
                            'cat_name' => $l['news_add_uncategorized'],
                          ),
    );
  $defined_categories = array('0');
  while($row = $db->fetch_assoc($result))
  {
    $page['categories'][] = $row;
    $defined_categories[] = $row['cat_id'];
  }
  
  # No errors yet
  $errors = array();
  
  # Processing?
  if(!empty($_POST['process']) && $_POST['process'] == 'news_add')
  {
    # Validate the category
    if(in_array(isset($_POST['category']) ? $_POST['category'] : 0, $categories))
      $errors[] = $l['news_add_error_category'];
    
    # If there are no errors
    if(!$errors)
    {
      # Insert the news post into the database
      $db->insert('insert', $db->prefix. 'news',
        array(
          'cat_id' => 'int', 'member_id' => 'int', 'subject' => 'string',
          'poster_time' => 'int', 'poster_name' => 'string', 'poster_email' => 'string',
          'body' => 'string', 'allow_comments' => 'int', 'is_viewable' => 'int',
        ),
        array(
          isset($_POST['category']) ? $_POST['category'] : 0, $user['id'],
          isset($_POST['subject']) ? $_POST['subject'] : 0, time_utc(), $user['name'], $user['email'],
          isset($_POST['body']) ? $_POST['body'] : 0, isset($_POST['comments']) && $_POST['comments'] ? 1 : 0,
          isset($_POST['viewable']) && $_POST['viewable'] ? 1 : 0
        ),
        array());
      
      # Update total news posts
      update_settings(array('total_news' => $settings['total_news'] + 1));
      
      # Update total news posts for category
      $db->query("
        UPDATE {$db->prefix}news_categories
          num_news = num_news + 1
        WHERE cat_id = %cat_id",
        array(
          'cat_id' => array('int',isset($_POST['category']) ? $_POST['category'] : 0),
        ));
      
      redirect('index.php?action=admin;sa=news;area=manage;added');
    }
  }
  
  # Our title...
  $page['title'] = $l['news_add_title'];

  # Our layout and we are done :)
  theme_load('admin_news', 'news_add_show');
}

function news_manage()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  error_screen(array('manage_news', 'moderate_news'));

  # Load the language :P
  language_load('admin_news');
  
  # Success message
  if(isset($_GET['added']))
    $page['success'] = $l['news_manage_success_added'];
  else
    $page['success'] = '';
  
  # We do a couple things here :D
  # Editing an article? OK.
  if(!empty($_GET['id']))
  {
    # Lets be sure this exists, shall we?
    $result = $db->query("
      SELECT
        news_id, cat_id, subject, body, allow_comments, is_viewable
      FROM {$db->prefix}news
      WHERE news_id = %news_id
      LIMIT 1",
      array(
        'news_id' => array('int', $_GET['id']),
      ));

    # If there is a row, it exists, of course :P
    if($db->num_rows($result))
    {
      # So lets get ready with everything you need.
      $row = $db->fetch_assoc($result);

      $page['news'] = array(
        'id' => $row['news_id'],
        'cat_id' => $row['cat_id'],
        'subject' => $row['subject'],
        'body' => $row['body'],
        'allow_comments' => !empty($row['allow_comments']),
        'is_viewable' => !empty($row['is_viewable']),
      );

      # Now that we have the news information, we now need
      # the categories for the news... of course :)
      $result = $db->query("
        SELECT
          cat_id, cat_name
        FROM {$db->prefix}news_categories
        ORDER BY cat_name ASC",
        array());

      # Loop through, and mark which one is selected.
      $page['categories'] = array();
      while($row = $db->fetch_assoc($result))
        $page['categories'][] = array(
          'id' => $row['cat_id'],
          'name' => $row['cat_name'],
          'selected' => $row['cat_id'] == $page['news']['cat_id'] ? true : false,
        );

      # Set the title and load the theme.
      $page['title'] = $l['news_edit_title'];

      theme_load('admin_news', 'news_manage_show_edit');
    }
    else
    {
      # Doesn't exist, silly pants :P
      $page['title'] = $l['news_article_doesnt_exist_title'];

      # Load the layout which tells you so.
      theme_load('admin_news', 'news_manage_show_invalid');
    }
  }
  # Or like adding a new news article :P
  elseif(!empty($_POST['add_news']))
  {
    # We are adding.
    $page['adding_news'] = true;

    # So we need to check if the category exists ;)
    $result = $db->query("
      SELECT
        cat_id
      FROM {$db->prefix}news_categories
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_id' => array('int', !empty($_POST['cat_id']) ? $_POST['cat_id'] : 0),
      ));

    # So does it exist..?
    if($db->num_rows($result))
    {
      # It sure does, so add it...
      $db->insert('insert', $db->prefix. 'news',
        array(
          'cat_id' => 'int', 'member_id' => 'int', 'subject' => 'string-255',
          'poster_time' => 'int', 'poster_name' => 'string', 'poster_email' => 'string',
          'body' => 'text', 'allow_comments' => 'int',
        ),
        array(
          $_POST['cat_id'], $user['id'], htmlspecialchars($_POST['subject'], ENT_QUOTES),
          time_utc(), $user['name'], $user['email'],
          htmlspecialchars($_POST['body'], ENT_QUOTES), !empty($_POST['allow_comments']) ? 1 : 0,
        ),
        array());

      # So was it made?
      if($db->affected_rows())
      {
        # So it was made :)
        # But do you want to continue editing?
        if(!empty($_POST['continue_editing']))
          redirect('index.php?action=admin;sa=news;area=manage;id='. $db->last_id());

        # Make a couple things :)
        $page['created_news'] = true;
        $page['news_id'] = $db->last_id();
        $page['news_name'] = htmlspecialchars($_POST['subject'], ENT_QUOTES);
      }
      else
        # Failed to create the news article :(
        $page['failed_to_create'] = true;
    }
    else
      # Nope, it doesn't.
      $page['cat_nonexistent'] = true;
  }
  # Editing..?
  elseif(!empty($_POST['edit_news']))
  {
    # We are editing.
    $page['editing_news'] = true;

    # So we need to check if the category exists ;)
    $cat_result = $db->query("
      SELECT
        cat_id
      FROM {$db->prefix}news_categories
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_id' => array('int', !empty($_POST['cat_id']) ? $_POST['cat_id'] : 0),
      ));

    # And the news article we want to edit!!!
    $news_result = $db->query("
      SELECT
        news_id
      FROM {$db->prefix}news
      WHERE news_id = %news_id
      LIMIT 1",
      array(
        'news_id' => array('int', !empty($_POST['news_id']) ? $_POST['news_id'] : 0),
      ));

    # So does it exist..? (Both? :P)
    if($db->num_rows($cat_result) && $db->num_rows($news_result))
    {
      # It sure does, so update the news!
      $db->query("
        UPDATE {$db->prefix}news
        SET
          cat_id = %cat_id, modified_member_id = %member_id,
          modified_name = %modified_name, modified_time = %cur_time,
          subject = %subject, body = %body, allow_comments = %allow_comments,
          is_viewable = %is_viewable
        WHERE news_id = %news_id",
        array(
          'cat_id' => array('int', $_POST['cat_id']),
          'member_id' => array('int', $user['id']),
          'modified_name' => array('string', $user['name']),
          'cur_time' => array('int', time_utc()),
          'subject' => array('string', htmlspecialchars($_POST['subject'], ENT_QUOTES)),
          'body' => array('text', htmlspecialchars($_POST['body'], ENT_QUOTES)),
          'allow_comments' => array('int', !empty($_POST['allow_comments']) ? 1 : 0),
          'is_viewable' => array('int', !empty($_POST['is_viewable']) ? 1 : 0),
          'news_id' => array('int', $_POST['news_id']),
        ));

      # So was it updated?
      if($db->affected_rows())
      {
        # Make a couple things :)
        $page['edited_news'] = true;
        $page['news_id'] = (int)$_POST['news_id'];
        $page['news_name'] = htmlspecialchars($_POST['subject'], ENT_QUOTES);
      }
      else
        # Failed to edit the news article :(
        $page['failed_to_edit'] = true;
    }
    elseif(!$db->num_rows($cat_result))
      # Nope, it doesn't.
      $page['cat_nonexistent'] = true;
    else
      # The news doesn't exist D:
      $page['news_nonexistent'] = true;
  }
  elseif(!empty($_POST['delete_news']))
  {
    # Deleting a news article? D: How could you?!?!
    $page['deleting_news'] = true;

    # Make sure the news article exists, we cannot delete something
    # that doesn't exist, can we?
    $result = $db->query("
      SELECT
        news_id
      FROM {$db->prefix}news
      WHERE news_id = %news_id
      LIMIT 1",
      array(
        'news_id' => array('int', !empty($_POST['news_id']) ? $_POST['news_id'] : 0),
      ));

    # Was it found?
    if($db->num_rows($result))
    {
      # It does exist, so delete it!
      $db->query("
        DELETE FROM {$db->prefix}news
        WHERE news_id = %news_id
        LIMIT 1",
        array(
          'news_id' => array('int', $_POST['news_id']),
        ));

      # So did we delete it successfully?
      if($db->affected_rows())
        # It sure was! Gone FOREVER >:D
        $page['news_deleted'] = true;
      else
        $page['news_delete_failed'] = true;
    }
    else
      # Didn't exist! D:!
      $page['news_nonexistent'] = true;
  }
  
  # Get all the news articles :) But of course, pagination :D
  $result = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}news",
    array());
  @list($num_news) = $db->fetch_row($result);

  # Create the pagination...
  $page['pagination'] = pagination_create($base_url. '/index.php?action=admin;sa=news;area=manage', $_REQUEST['page'], $num_news, $user['per_page']['news']);
  
  # Define the sort options
  $sort_options = array(
    'subject' => 'n.subject',
    'category' => 'nc.cat_name',
    'creator' => 'mem.displayName',
    'time' => 'n.poster_time',
    'comments' => 'n.num_comments',
    'views' => 'n.num_views',
  );
  
  # Get the appropriate field to sort by
  $page['sort'] = !empty($_GET['sort']) && in_array($_GET['sort'], array_keys($sort_options)) ? $_GET['sort'] : 'time';
  
  # Check whether it's ascending or decending
  $page['sort_asc'] = !isset($_GET['desc']);
  
  # And an SQL version
  $sort = $sort_options[$page['sort']]. ($page['sort_asc'] ? '' : ' DESC');
  
  # Get the news articles from the database
  $result = $db->query("
    SELECT
      n.news_id, n.cat_id, n.member_id, n.modified_member_id, n.modified_name,
      n.modified_time, n.subject, n.poster_time, n.poster_name, n.poster_email,
      n.num_comments, n.num_views, n.allow_comments, n.is_viewable, nc.cat_id,
      nc.cat_name, mem.member_id, mem.displayName, mem2.member_id AS modified_id,
      mem2.displayName as modified_displayName
    FROM {$db->prefix}news AS n
      LEFT JOIN {$db->prefix}news_categories AS nc ON nc.cat_id = n.cat_id
      LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = n.member_id
      LEFT JOIN {$db->prefix}members AS mem2 ON mem2.member_id = n.modified_member_id
    ORDER BY $sort
    LIMIT %start, %news_per_page",
    array(
      'start' => array('int', $_REQUEST['page']),
      'news_per_page' => array('int', $settings['news_per_page']),
    ));

  # Lets get them loaded up!
  $page['news'] = array();
  while($row = $db->fetch_assoc($result))
    $page['news'][] = $row;

  # We need to show the theme ;)
  $page['title'] = $l['admin_news_manage_title'];

  theme_load('admin_news', 'news_manage_show');
}

function news_categories()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # So are you allowed to be here?
  error_screen(array('manage_news_categories', 'moderate_news'));

  # Load the language.
  language_load('admin_news');
  
  # Success and error messages
  if(isset($_GET['added']))
    $page['success'] = $l['admin_news_manage_categories_add_success'];
  elseif(isset($_GET['deleted']))
    $page['success'] = $l['admin_news_manage_categories_delete_success'];
  
  $page['errors'] = '';
  
  # Default values
  $page['category_name'] = '';
  
  # Check to see if you are up to something.
  if(!empty($_POST['process']) && $_POST['process'] == 'category-add')
  {
    # But you can't have an empty category name. ._.
    if(!empty($_POST['category_name']))
    {
      # Maximum 100 characters
      if(mb_strlen($_POST['category_name']) <= 100)
      {
        # Not empty, good enough for me!
        $db->insert('insert', $db->prefix. 'news_categories',
          array(
            'cat_name' => 'string-255',
          ),
          array(
            $_POST['category_name'],
          ),
          array());

        # Was it created?
        if($db->affected_rows())
        {
          # Success! Not let's redirect
          redirect('/index.php?action=admin;sa=news;area=categories;added');
        }
        else
        {
          $page['error'] = $l['admin_news_manage_categories_add_error_unknown'];
          $page['category_name'] = htmlspecialchars($_POST['category_name']);
        }
      }
      else
      {
        $page['error'] = $l['admin_news_manage_categories_add_error_name_long'];
        $page['category_name'] = htmlspecialchars($_POST['category_name']);
      }
    }
    else
      $page['error'] = $l['admin_news_manage_categories_add_error_name_none'];
  }
  elseif(!empty($_POST['edit_category']))
  {
    # Editing a category? Aye Aye Captain!
    $page['editing_category'] = true;

    # Does this category even exist?
    $result = $db->query("
      SELECT
        cat_id
      FROM {$db->prefix}news_categories
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_id' => array('int', $_POST['edit_category']),
      ));

    # The category name cannot be empty :P nor can
    # the category be nonexistent.
    if(!empty($_POST['cat_name']) && $db->num_rows($result))
    {
      # Okay, all seems to be in order, update the category name.
      $db->query("
        UPDATE {$db->prefix}news_categories
        SET
          cat_name = %cat_name
        WHERE cat_id = %cat_id
        LIMIT 1",
        array(
          'cat_name' => array('string-255', $_POST['cat_name']),
          'cat_id' => array('int', $_POST['edit_category']),
        ));

      # Did it update right?
      if($db->affected_rows())
      {
        # It was a success!
        $page['edit_success'] = true;
        $page['cat_name'] = htmlspecialchars($_POST['cat_name'], ENT_QUOTES);
        $page['cat_id'] = (int)$_POST['edit_category'];
      }
      else
        $page['edit_failed'] = true;
    }
    elseif(empty($_POST['cat_name']))
      # Empty category name...
      $page['cat_name_empty'] = true;
    else
      # That category can't be updated if it doesn't exist!
      $page['cat_not_exist'] = true;
  }
  # So you want to delete a category? Fine, be that way!
  elseif(!empty($_GET['del']))
  {
    # Make sure we can delete it, its gotta exist.
    $result = $db->query("
      SELECT
        cat_id
      FROM {$db->prefix}news_categories
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_id' => array('int', $_GET['del']),
      ));

    # If it exists, delete it.
    if($db->num_rows($result))
    {
      # DELETE >:D FOREVA!
      $db->query("
        DELETE FROM {$db->prefix}news_categories
        WHERE cat_id = %cat_id
        LIMIT 1",
        array(
          'cat_id' => array('int', $_GET['del']),
        ));

      # Did we actually delete it?
      if($db->affected_rows())
          redirect('/index.php?action=admin;sa=news;area=categories;deleted');
      else
        $page['error'] = $l['admin_news_manage_categories_delete_error_unknown'];
    }
    else
      # Deletion was a failure ):
      $page['error'] = $l['admin_news_manage_categories_delete_error_doesnt_exist'];
  }
  
  # Get all the news categories from our database
  $result = $db->query("
    SELECT
      cat_id, cat_name, num_news
    FROM
      {$db->prefix}news_categories
    ORDER BY cat_name
  ");
  
  $page['categories'] = array();
  while($row = mysql_fetch_assoc($result))
    $page['categories'][] = $row;
  
  # Show the theme :D
  $page['title'] = $l['admin_news_manage_categories_title'];

  theme_load('admin_news', 'news_categories_show');
}

function news_edit_ajax()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;

  # Our array we will output in using JSON_encode...
  $output = array('error' => '');

  # News language...
  language_load('admin_news');

  # Wait, before we go on... can you do this?
  if(!can('manage_news') && !can('moderate_news'))
  {
    # Uhh, no!
    echo json_encode(array('error' => $l['ajax_access_denied']));
    exit;
  }
    

  # We need to be sure you are verified...
  if(!function_exists('adminVerify'))
    require_once($source_dir. '/Admin.php');

  # So are verified or not? Because you need to be ._.
  if(adminVerify(true))
  {
    # So you are verified are you?
    # Doesn't mean the news and the category exists XD.
    $cat_result = $db->query("
      SELECT
        cat_id
      FROM {$db->prefix}news_categories
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_id' => array('int', !empty($_POST['cat_id']) ? $_POST['cat_id'] : 0),
      ));

    $news_result = $db->query("
      SELECT
        news_id
      FROM {$db->prefix}news
      WHERE news_id = %news_id
      LIMIT 1",
      array(
        'news_id' => array('int', !empty($_POST['news_id']) ? $_POST['news_id'] : 0),
      ));

    # So see if they exist...
    if($db->num_rows($cat_result) && $db->num_rows($news_result))
    {
      # Both do exist, so I guess we can update it ;)
      $db->query("
        UPDATE {$db->prefix}news
        SET
          cat_id = %cat_id, modified_member_id = %member_id,
          modified_name = %modified_name, modified_time = %cur_time,
          subject = %subject, body = %body, allow_comments = %allow_comments,
          is_viewable = %is_viewable
        WHERE news_id = %news_id",
        array(
          'cat_id' => array('int', $_POST['cat_id']),
          'member_id' => array('int', $user['id']),
          'modified_name' => array('string', $user['name']),
          'cur_time' => array('int', time_utc()),
          'subject' => array('string', htmlspecialchars($_POST['subject'], ENT_QUOTES)),
          'body' => array('text', htmlspecialchars($_POST['body'], ENT_QUOTES)),
          'allow_comments' => array('int', !empty($_POST['allow_comments']) ? 1 : 0),
          'is_viewable' => array('int', !empty($_POST['is_viewable']) ? 1 : 0),
          'news_id' => array('int', $_POST['news_id']),
        ));

      # Then basically return if it was updated successfully :P
      if($db->affected_rows())
        $output['news_edited'] = true;
      else
        $output['news_edited'] = false;
    }
    elseif(!$db->num_rows($cat_result))
      # The category doesn't exist o.O
      $output['error'] = $l['news_category_doesnt_exist'];
    else
      # The news article doesn't exist D:
      $output['error'] = $l['news_article_doesnt_exist'];
  }
  else
    # You aren't verified!
    $output = array('error' => $l['news_need_admin_verification'], 'needs_verification' => true);

  # Output the result
  echo json_encode($output);
}
?>