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
# The forum administration happens here.
#
# void forum_category_add();
#   - Add new categories here...
#
# void forum_board_add();
#   - Add new categories here...
#
# void forum_manage();
#   - Allows administrators (or whoever is allowed) to administer their
#     forum by moving, renaming, deleting, editing, boards and categories.
#
# void forum_category_edit_ajax();
#   - Allows the editing of categories through an AJAX interface. Accessed
#     through index.php?action=interface;sa=editCategory
#
# void forum_board_edit_ajax();
#   - Allows the editing of boards through an AJAX interface. Accessed
#     through index.php?action=interface;sa=editBoard
#

function forum_category_add()
{
  global $base_url, $db, $l, $page, $theme_url, $user;

  # You must be able to access this...
  error_screen('add_category');

  language_load('admin_forum');

  # Get all the categories ;)
  $result = $db->query("
    SELECT
      cat_id, cat_name
    FROM {$db->prefix}categories
    ORDER BY cat_order ASC",
    array());

  $page['categories'] = array();
  while($row = $db->fetch_assoc($result))
    $page['categories'][$row['cat_id']] = $row['cat_name'];

  # This is quite simple, seeing as we don't do much :P
  $page['title'] = $l['add_category_title'];

  theme_load('admin_forum', 'forum_category_add_show');
}

function forum_board_add()
{
  global $base_url, $db, $l, $page, $theme_url, $user;

  # Can you add a board..?
  error_screen('add_board');
  
  # Load the language
  language_load('admin_forum');
  
  # We need categories... That's where boards go :P
  $result = $db->query("
    SELECT
      cat_id, cat_name
    FROM {$db->prefix}categories
    ORDER BY cat_order ASC",
    array());

  $page['categories'] = array();
  while($row = $db->fetch_assoc($result))
  {
    $page['categories'][$row['cat_id']] = array(
      'id' => $row['cat_id'],
      'name' => $row['cat_name'],
      'boards' => array(),
    );
  }

  # But you can also add them after a board you see..?
  $result = $db->query("
    SELECT
      cat_id, board_id, board_name
    FROM {$db->prefix}boards
    WHERE child_of = 0
    ORDER BY board_order ASC",
    array());

  while($row = $db->fetch_assoc($result))
  {
    if(isset($page['categories'][$row['cat_id']]))
    {
      $page['categories'][$row['cat_id']]['boards'][] = array(
        'id' => $row['board_id'],
        'name' => '- '. $row['board_name'],
      );
    }
  }

  # We need a group list! Exclude admins though. They can do anything :P
  $result = $db->query("
    SELECT
      group_id, group_name_plural AS group_name, min_posts
    FROM {$db->prefix}membergroups
    WHERE group_id != 1",
    array());

  # The array containing the groups. Add guests ;)
  $page['groups'] = array(
    array(
      'id' => -1,
      'name' => $l['guest_name_plural'],
      'post_group' => false,
      'checked' => true,
    ),
  );
  
  # And the rest of the groups
  while($row = $db->fetch_assoc($result))
  {
    $page['groups'][] = array(
      'id' => $row['group_id'],
      'name' => $row['group_name'],
      'post_group' => $row['min_posts'] > -1,
      'checked' => true,
    );
  }

  # Now just load up the theme... I think we are done here 8D
  $page['title'] = $l['add_board_title'];
  
  theme_load('admin_forum', 'forum_board_add_show');
}

function forum_manage()
{
  global $base_url, $db, $l, $page, $theme_url, $user;

  # Are you even allowed to access this?
  error_screen('manage_forum');

  # Our Language, please :)
  language_load('admin_forum');

  # Deleting a category?
  if(!empty($_GET['del']) && isset($_GET['cat']))
  {
    # Make sure you didn't click a link elsewhere! :X
    checkSession('get');

    # Lets delete that category.
    $db->query("
      DELETE FROM {$db->prefix}categories
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_id' => array('int', $_GET['del']),
      ));

    # Any deleted?
    if($db->affected_rows())
      $page['errors'][] = $l['category_deleted'];
    else
      $page['errors'][] = $l['error_delete_category'];
  }
  # Maybe a board?
  elseif(!empty($_GET['del']) && isset($_GET['board']))
  {
    # You shouldn't be clicking links willy nilly :P!
    checkSession('get');

    # Now delete the board. Simple as that.
    $db->query("
      DELETE FROM {$db->prefix}boards
      WHERE board_id = %board_id
      LIMIT 1",
      array(
        'board_id' => array('int', $_GET['del']),
      ));

    if($db->affected_rows())
      $page['errors'][] = $l['board_deleted'];
    else
      $page['errors'][] = $l['error_delete_board'];
  }
  elseif((!empty($_GET['raise']) || !empty($_GET['lower'])) && isset($_GET['cat']))
  {
    # Raising or lowering category...

    # Just check...
    checkSession('get');

    # So which..?
    $raise = isset($_GET['raise']) ? 1 : -1;
    $cat_id = !empty($_GET['raise']) ? $_GET['raise'] : $_GET['lower'];

    # Get the total number of categories... For later use ;)
    $result = $db->query("
      SELECT
        COUNT(*)
      FROM {$db->prefix}categories",
      array());
    @list($num_cats) = $db->fetch_row($result);

    # Now we need the categories current rank.
    $result = $db->query("
      SELECT
        cat_order
      FROM {$db->prefix}categories
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_id' => array('int', $cat_id),
      ));
    @list($cat_order) = $db->fetch_row($result);

    # You can't lower it anymore below 1! Or above the total amount of categories!
    if(($raise == 1 && $cat_order < 2) || ($raise == -1 && ($cat_order + 1) > $num_cats))
      redirect('index.php?action=admin;sa=forum;area=boards');

    # Move the category out of the way that we will be moving too!
    $db->query("
      UPDATE {$db->prefix}categories
      SET cat_order = cat_order + %raise
      WHERE cat_order = %cat_order",
      array(
        'raise' => array('int', $raise),
        'cat_order' => array('int', $cat_order - $raise),
      ));

    # Now change the order of the category
    $db->query("
      UPDATE {$db->prefix}categories
      SET cat_order = %cat_order
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_order' => array('int', $cat_order - $raise),
        'cat_id' => array('int', $cat_id),
      ));

    # Okay, we're outta here! :D
    redirect('index.php?action=admin;sa=forum;area=boards');
  }
  elseif((!empty($_GET['raise']) || !empty($_GET['lower'])) && isset($_GET['board']))
  {
    # Lowering or raising a board...
    # Hehe, copy and paste FTW! ^^

    # Wouldn't hurt to much, but still...
    checkSession('get');

    # So which..?
    $raise = isset($_GET['raise']) ? 1 : -1;
    $board_id = !empty($_GET['raise']) ? $_GET['raise'] : $_GET['lower'];

    # Now we need the boards current rank, and category ;)
    $result = $db->query("
      SELECT
        board_order, cat_id, child_of
      FROM {$db->prefix}boards
      WHERE board_id = %board_id
      LIMIT 1",
      array(
        'board_id' => array('int', $board_id),
      ));
    @list($board_order, $cat_id, $child_of) = $db->fetch_row($result);

    # Get the total number of boards in the category... For later use ;)
    $result = $db->query("
      SELECT
        COUNT(*)
      FROM {$db->prefix}boards
      WHERE cat_id = %cat_id AND child_of = %child_of",
      array(
        'cat_id' => array('int', $cat_id),
        'child_of' => array('int', $child_of),
      ));
    @list($num_boards) = $db->fetch_row($result);

    # You can't lower it anymore below 1! Or above the total amount of boards in the category!
    if(($raise == 1 && $board_order < 2) || ($raise == -1 && ($board_order + 1) > $num_boards))
      redirect('index.php?action=admin;sa=forum;area=boards');

    # Move the board out of the way that we will be moving too!
    $db->query("
      UPDATE {$db->prefix}boards
      SET board_order = board_order + %raise
      WHERE board_order = %board_order AND child_of = %child_of",
      array(
        'raise' => array('int', $raise),
        'board_order' => array('int', $board_order - $raise),
        'child_of' => array('int', $child_of),
      ));

    # Now change the order of the board
    $db->query("
      UPDATE {$db->prefix}boards
      SET board_order = %board_order
      WHERE board_id = %board_id
      LIMIT 1",
      array(
        'board_order' => array('int', $board_order - $raise),
        'board_id' => array('int', $board_id),
      ));

    # Okay, we're outta here! :D
    redirect('index.php?action=admin;sa=forum;area=boards');
  }
  # Adding a category..? =D!
  elseif(!empty($_POST['add_category']) && can('add_category'))
  {
    # We can't have an empty category name.
    if(!empty($_POST['category_name']))
    {
      # Sanitize HTML stuffs...
      $cat_name = htmlspecialchars($_POST['category_name'], ENT_QUOTES, 'UTF-8');

      # Get its position...
      $position = !empty($_POST['category_position']) ? (int)$_POST['category_position'] : 0;

      # Is it collapsible?
      $is_collapsible = !empty($_POST['category_collapsible']);

      # So lets move the boards around that way this one can just pop in ;)
      # Check to see if the board exists...
      $result = $db->query("
        SELECT
          cat_order
        FROM {$db->prefix}categories
        WHERE cat_id = %cat_id
        LIMIT 1",
        array(
          'cat_id' => array('int', $position),
        ));

      # Anything?
      if($db->num_rows($result))
      {
        $row = $db->fetch_assoc($result);

        # Now we need to increment everything 1 above where we want to add after. Got it..?
        $db->query("
          UPDATE {$db->prefix}categories
          SET cat_order = cat_order + 1
          WHERE cat_order > %cat_order",
          array(
            'cat_order' => array('int', $row['cat_order']),
          ));

        $position = $row['cat_order'] + 1;
      }
      else
      {
        # No board, so its going to be first! ^__^
        # This will be simple... All we need to do is increment all positions by 1.
        $db->query("
          UPDATE {$db->prefix}categories
          SET cat_order = cat_order + 1",
          array());

        $position = 1;
      }

      # So now add the category at the position given.
      $db->insert('insert', $db->prefix. 'categories',
        array(
          'cat_order' => 'int', 'cat_name' => 'string-255', 'is_collapsible' => 'int',
        ),
        array(
          $position, $cat_name, $is_collapsible ? 1 : 0,
        ),
        array());

      if(!$db->affected_rows())
        $page['errors'][] = $l['add_category_couldnt_add'];
    }
    else
      $page['errors'][] = $l['add_category_no_name'];
  }
  # Adding a board..? =D!
  elseif(!empty($_POST['add_board']) && can('add_board'))
  {
    # We can't have an empty board name ;)
    if(!empty($_POST['board_name']))
    {
      # Make it so HTML won't parse :P
      $board_name = htmlspecialchars($_POST['board_name'], ENT_QUOTES, 'UTF-8');

      # Same for board description... But we will parse BBCode
      $board_desc = htmlspecialchars($_POST['board_desc'], ENT_QUOTES, 'UTF-8');

      # Our position. We will work more with this a bit later ;)
      $position = $_POST['board_position'];

      # The allowed groups. :)
      $allowed_groups = array();
      if(isset($_POST['groups']) && count($_POST['groups']))
        foreach($_POST['groups'] as $group_id => $is_allowed)
          if($is_allowed == 1)
            $allowed_groups[] = trim($group_id);

      # Implode them separated by a ,
      $allowed_groups = implode(',', $allowed_groups);

      # Board moderators... We will mess with these later too, if need be.
      $board_moderators = !empty($_POST['board_moderators']) ? $_POST['board_moderators'] : '';

      # Lets see about where this board ought to go.
      if(mb_substr($position, 0, 1) == 'c')
      {
        # In a category..? Ok... It will go in the first place.
        # Get the id of the category.
        $cat_id = (int)mb_substr($position, 1, mb_strlen($position));

        # So increment all the boards order up 1...
        $db->query("
          UPDATE {$db->prefix}boards
          SET board_order = board_order + 1
          WHERE cat_id = %cat_id
          LIMIT 1",
          array(
            'cat_id' => array('int', $cat_id),
          ));

        # The place it will go. 1! 8D
        $position = 1;
      }
      else
      {
        # Hmm, a board... It will go after this.
        $board_id = (int)mb_substr($position, 1, mb_strlen($position));

        # Hmm... What category is this board in..?
        $result = $db->query("
          SELECT
            cat_id, board_order
          FROM {$db->prefix}boards
          WHERE board_id = %board_id
          LIMIT 1",
          array(
            'board_id' => array('int', $board_id),
          ));

        # Does it exist?
        if($db->num_rows($result))
        {
          @list($cat_id, $board_order) = $db->fetch_row($result);

          # Increment all the board orders by 1 after this boards order...
          $db->query("
            UPDATE {$db->prefix}boards
            SET board_order = board_order + 1
            WHERE cat_id = %cat_id AND board_order > %board_order",
            array(
              'cat_id' => array('int', $cat_id),
              'board_order' => array('int', $board_order),
            ));

          # Now our new spot! :) All clean and empty.
          $position = $board_order + 1;
        }
        else
        {
          # Uh oh! It doesn't! 0-0
          # Its unallocated now. Sorry.
          $position = 1;
          $cat_id = 0;
        }
      }

      # Yay! Now we can actually add the board. What fun! ^_^
      # Note to self... Child boards not yet supported :P
      $db->insert('insert', $db->prefix. 'boards',
        array(
          'cat_id' => 'int', 'board_order' => 'int', 'child_of' => 'int',
          'who_view' => 'string-255', 'board_name' => 'string', 'board_desc' => 'string',
        ),
        array(
          $cat_id, $position, 0,
          $allowed_groups, $board_name, $board_desc
        ),
        array());

      # Was it a success..?
      if($db->affected_rows())
      {
        # We aren't quite done yet... We might be though...
        # Any moderators? Otherwise, yay!
        if(!empty($board_moderators))
        {
          # Get the board id...
          $board_id = $db->last_id($db->prefix. 'boards');

          # Now we need to look for these members ;)
          # Lets get going, shall we?
          $board_moderators = explode(',', $board_moderators);

          $moderators = array();
          foreach($board_moderators as $member_name)
            # If the database is case-sensitive, lower everything XD
            $moderators[] = trim($db->case_sensitive ? mb_strtolower($member_name) : $member_name);

          # Start to search for them.
          $result = $db->query("
            SELECT
              member_id
            FROM {$db->prefix}members
            WHERE ". ($db->case_sensitive ? 'LOWER(loginName)' : 'loginName'). " IN(%moderators)",
            array(
              'moderators' => array('string_array', $moderators),
            ));

          # Were any found..?
          if($db->num_rows($result))
          {
            # Get them!
            $rows = array();
            while($row = $db->fetch_assoc($result))
              $rows[] = array($board_id, $row['member_id']);

            # Now simply insert them ;)
            $db->insert('ignore', $db->prefix. 'moderators',
              array(
                'board_id' => 'int', 'member_id' => 'int',
              ),
              $rows,
              array('board_id','member_id'));
          }
        }
      }
      else
        $page['errors'][] = $l['add_board_couldnt_add'];
    }
    else
      $page['errors'][] = $l['add_board_no_name'];
  }

  # Load up all our categories...
  $result = $db->query("
    SELECT
      cat_id, cat_order, cat_name, is_collapsible
    FROM {$db->prefix}categories
    ORDER BY cat_order ASC",
    array());

  $page['categories'] = array();
  while($row = $db->fetch_assoc($result))
    $page['categories'][$row['cat_id']] = array(
      'id' => $row['cat_id'],
      'name' => $row['cat_name'],
      'order' => $row['cat_order'],
      'collapsible' => !empty($row['is_collapsible']),
      'link' => array(
                  'delete' => $base_url. '/index.php?action=admin;sa=forum;area=boards;del='. $row['cat_id']. ';cat;sc='. $user['sc'],
                  'raise' => $base_url. '/index.php?action=admin;sa=forum;area=boards;raise='. $row['cat_id']. ';cat;sc='. $user['sc'],
                  'lower' => $base_url. '/index.php?action=admin;sa=forum;area=boards;lower='. $row['cat_id']. ';cat;sc='. $user['sc'],
                ),
      'boards' => array(),
    );

  # Now all our boards... They will go inside the categories
  # array, maybe, if they are allocated that is ;)
  $page['unallocated'] = array();

  $result = $db->query("
    SELECT
      board_id, cat_id, board_order, child_of, board_name
    FROM {$db->prefix}boards
    ORDER BY board_order ASC",
    array());
  while($row = $db->fetch_assoc($result))
  {
    # For now, build the board!
    $board = array(
      'id' => $row['board_id'],
      'name' => $row['board_name'],
      'child_of' => $row['child_of'],
      'order' => $row['board_order'],
      'cat_id' => $row['cat_id'],
      'link' => array(
                  'edit' => $base_url. '/index.php?action=admin;sa=forum;area=boards;edit='. $row['board_id']. ';board',
                  'delete' => $base_url. '/index.php?action=admin;sa=forum;area=boards;del='. $row['board_id']. ';board;sc='. $user['sc'],
                  'raise' => $base_url. '/index.php?action=admin;sa=forum;area=boards;raise='. $row['board_id']. ';board;sc='. $user['sc'],
                  'lower' => $base_url. '/index.php?action=admin;sa=forum;area=boards;lower='. $row['board_id']. ';board;sc='. $user['sc'],
                ),
      'children' => array(),
    );

    # So is this board allocated..?
    if(!empty($page['categories'][$board['cat_id']]) && $board['child_of'] == 0)
    {
      # Add the board...
      $page['categories'][$board['cat_id']]['boards'][$board['id']] = $board;
    }
    else
    {
      # Oh noes! It isn't! Oh well, you can fix it ;)
      $page['unallocated'][] = $board;
    }
  }
  
  # Ohhh! Check the unallocated ones, they could be children :P
  foreach($page['unallocated'] as $key => $board)
  {
    # Not a children..? Don't bother... Or the child is an orphan? :o
    if($board['child_of'] == 0 || ($board['child_of'] > 0 && empty($page['categories'][$board['cat_id']]['boards'][$board['child_of']])))
      continue;

    # Allocate it, then delete it.
    $page['categories'][$board['cat_id']]['boards'][$board['child_of']]['children'][] = $board;
    unset($page['unallocated'][$key]);
  }

  # Okay! I think we got it! ;) So show the layout...
  $page['title'] = $l['manageboards_title'];
  
  if($page['categories'])
  {
    # Don't forget, we need this...
    $page['scripts'][] = $theme_url. '/default/js/edit_category.js';
    $page['js_vars']['save_text'] = $l['save'];
    $page['js_vars']['cancel_text'] = $l['cancel'];
    $page['js_vars']['allow_collapse'] = $l['manageboards_ajax_check_allow_collapse'];

    theme_load('admin_forum', 'forum_manage_show');
  }
  else
  {
    # No boards or categories
    theme_load('admin_forum', 'forum_manage_show_empty');
  }
}

function forum_category_edit_ajax()
{
  global $db, $l, $settings, $user;

  language_load('admin_forum');

  # Can you do this..?
  if(!can('manage_forum') || empty($settings['forum_enabled']))
  {
    echo json_encode(array('error' => $l['manageboards_ajax_not_allowed']));
    exit;
  }

  # Our output array.
  $output = array('error' => '');

  # Lets see, saving..?
  if(isset($_GET['save']))
  {
    # Get the category name.
    $cat_name = !empty($_POST['cat_name']) ? $_POST['cat_name'] : '';

    # Make sure its not totally empty...
    if(mb_strlen($cat_name) > 0)
    {
      # Html special!
      $cat_name = htmlspecialchars($cat_name, ENT_QUOTES, 'UTF-8');

      # Update it!
      $db->query("
        UPDATE {$db->prefix}categories
        SET cat_name = %cat_name, is_collapsible = %can_collapse
        WHERE cat_id = %cat_id
        LIMIT 1",
        array(
          'cat_id' => array('int', !empty($_POST['cat_id']) ? $_POST['cat_id'] : 0),
          'cat_name' => array('string', $cat_name),
          'can_collapse' => array('int', !empty($_POST['is_collapsible']) ? 1 : 0),
        ));

      # Anything happen?
      if($db->affected_rows())
        $output['cat_name'] = $cat_name;
      else
      {
        # Error! Oh noes!
        $output['error'] = $l['manageboards_ajax_cat_not_updated'];

        # It could be that you didn't change the name at all .-.
        $result = $db->query("
          SELECT
            cat_name
          FROM {$db->prefix}categories
          WHERE cat_id = %cat_id
          LIMIT 1",
          array(
            'cat_id' => array('int', !empty($_POST['cat_id']) ? $_POST['cat_id'] : 0),
          ));

        if($db->num_rows($result))
        {
          @list($cur_name) = $db->fetch_row($result);

          # If they are the same, remove the error.
          if($cur_name == $cat_name)
          {
            # Unset the error, and set the name ;) Your fine!
            $output['error'] = '';
            $output['cat_name'] = $cat_name;
          }
        }
      }
    }
    else
      $output['error'] = $l['manageboards_ajax_cat_name_error'];
  }
  else
  {
    # Nope, get the category info, the name at least and ID...
    $result = $db->query("
      SELECT
        cat_id, cat_name, is_collapsible
      FROM {$db->prefix}categories
      WHERE cat_id = %cat_id
      LIMIT 1",
      array(
        'cat_id' => array('int', !empty($_POST['cat_id']) ? $_POST['cat_id'] : 0),
      ));

    # Does it exist?
    if($db->num_rows($result))
    {
      # Yeah, it exists.
      @list($cat_id, $cat_name, $is_collapsible) = $db->fetch_row($result);
      $output['cat_id'] = $cat_id;
      $output['cat_name'] = htmlspecialchars_decode($cat_name, ENT_QUOTES);
      $output['is_collapsible'] = $is_collapsible ? true : false;
    }
    else
      $output['error'] = $l['manageboards_ajax_category_not_found'];
  }

  # Output our JSON array and we got it.
  echo json_encode($output);
  exit;
}

function forum_board_edit_ajax()
{
  global $db, $l, $settings, $user;

  language_load('admin_forum');

  # Can you do this..?
  if(!can('manage_forum') || empty($settings['forum_enabled']))
  {
    echo json_encode(array('error' => $l['manageboards_ajax_not_allowed']));
    exit;
  }

  # Our output array.
  $output = array('error' => '');

  # Lets see, saving..?
  if(isset($_GET['save']))
  {
    # Get the board name.
    $board_name = !empty($_POST['board_name']) ? $_POST['board_name'] : '';

    # Make sure its not totally empty...
    if(mb_strlen($board_name) > 0)
    {
      # Html special!
      $board_name = htmlspecialchars($board_name, ENT_QUOTES, 'UTF-8');

      # Update it!
      $db->query("
        UPDATE {$db->prefix}boards
        SET board_name = %board_name
        WHERE board_id = %board_id
        LIMIT 1",
        array(
          'board_id' => array('int', !empty($_POST['board_id']) ? $_POST['board_id'] : 0),
          'board_name' => array('string', $board_name),
        ));

      # Anything happen?
      if($db->affected_rows())
        $output['board_name'] = $board_name;
      else
      {
        $output['error'] = $l['manageboards_ajax_board_not_updated'];

        # What if you didn't change anything? That shouldn't really show the error :)
        $result = $db->query("
          SELECT
            board_name
          FROM {$db->prefix}boards
          WHERE board_id = %board_id
          LIMIT 1",
          array(
            'board_id' => array('int', !empty($_POST['board_id']) ? $_POST['board_id'] : 0),
          ));

        if($db->num_rows($result))
        {
          @list($cur_name) = $db->fetch_row($result);

          # Same name..? Thats not really an error, so don't show it.
          if($cur_name == $board_name)
          {
            $output['error'] = '';
            $output['board_name'] = $board_name;
          }
        }
      }
    }
    else
      $output['error'] = $l['manageboards_ajax_board_name_error'];
  }
  else
  {
    # Nope, get the board info, the name at least and ID...
    $result = $db->query("
      SELECT
        board_id, board_name
      FROM {$db->prefix}boards
      WHERE board_id = %board_id
      LIMIT 1",
      array(
        'board_id' => array('int', !empty($_POST['board_id']) ? $_POST['board_id'] : 0),
      ));

    # Does it exist?
    if($db->num_rows($result))
    {
      # Yeah, it exists.
      @list($board_id, $board_name) = $db->fetch_row($result);
      $output['board_id'] = $board_id;
      $output['board_name'] = htmlspecialchars_decode($board_name, ENT_QUOTES);
    }
    else
      $output['error'] = $l['manageboards_ajax_board_not_found'];
  }

  # Output our JSON array and we got it.
  echo json_encode($output);
  exit;
}
?>