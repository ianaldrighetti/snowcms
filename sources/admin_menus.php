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
# Managing the menu
#
# void menus_add();
#   - Handle the adding of links.
#
# void menus_manage();
#   - Handle the list of menus.
#
# void menus_process();
#   - Handle the saving process of adding a link.
#
# void menus_edit_ajax();
#   - Handle AJAX requests for editing links.
#

function menus_add()
{
  global $base_url, $db, $page, $user, $source_dir, $l;
  
  # Get the language
  language_load('admin_menus');
  
  # Get the menu links from the database
  $result = $db->query("
    SELECT
      link_id, link_name, link_menu
    FROM {$db->prefix}menus
    ORDER BY link_order",
    array());
  
  $previous_menu = 0;
  
  # Format the menu links in a 2D array
  while($row = $db->fetch_assoc($result))
  {
    # If we have reached a new menu, add the menu in the list
    if($row['link_menu'] > $previous_menu)
    {
      $links['menu-'. $row['link_menu']] = sprintf($l['admin_menus_add_menu'], numberformat($row['link_menu']));
    }
    
    # Add the link
    $links['link-'. $row['link_id']] = '- '. $row['link_name'];
    
    # Update the menu
    $previous_menu = $row['link_menu'];
  }
  
  # Add any extra unused menus until menu 5 is reached
  for($i = $previous_menu + 1; $i <= 5; $i += 1)
    $links['menu-'. $i] = sprintf($l['admin_menus_add_menu'], numberformat($i));
  
  # Get all our settings in a nice long array
  $add_link_fields = array(
    array(
      'variable' => 'link_name',
      'type' => 'text',
    ),
    array(
      'variable' => 'link_href',
      'type' => 'text',
    ),
    array(
      'variable' => 'link_target',
      'type' => 'checkbox',
    ),
    array(
      'variable' => 'link_follow',
      'type' => 'checkbox',
      'subtext' => true,
    ),
    array(
      'variable' => 'link_order',
      'type' => 'select',
      'options' => $links,
      'subtext' => true,
    ),
    array(
      'type' => 'label',
      'label' => '<br /><b>'. $l['setting_label_groups']. '</b>',
    ),
    array(
     'variable' => 'group_-1',
     'label' => $l['guest_name_plural'],
     'type' => 'checkbox',
   ),
  );
  
  # Set the field defaults
  $field_defaults = array(
    'link_follow' => true,
    'group_-1' => true,
  );
  
  # Get the member groups
  $result = $db->query("
    SELECT
      group_id, group_name_plural AS group_name
    FROM {$db->prefix}membergroups",
    array());
  
  # Add the member groups to the end of the field list
  while($row = $db->fetch_assoc($result))
  {
    # Not for administrators, they can always access everything :P
    if($row['group_id'] != 1)
    {
      $add_link_fields[] = array(
                             'variable' => 'group_'. $row['group_id'],
                             'label' => $row['group_name'],
                             'type' => 'checkbox',
                           );
      
      # Make it selected by default :P
      $field_defaults['group_'. $row['group_id']] = true;
    }
  }
  
  # Saving?
  if(isset($_GET['save']))
  {
    # Use our cool settings_save() function
    settings_save($add_link_fields, 'menus_process');
    
    # Redirect
    redirect('index.php?action=admin;sa=menus;area=manage');
  }
  
  # Get ready to display our settings
  $page['settings'] = settings_prepare($add_link_fields, $field_defaults);
  
  # Whether or not settings have just been saved
  $page['saved'] = isset($_GET['saved']);
  
  # The submit URL
  $page['submit_url'] = $base_url. '/index.php?action=admin;sa=menus;area=add;save';
  
  # Set the title
  $page['title'] = $l['admin_menus_add_title'];
  
  # Load the theme
  theme_load('admin_menus', 'menus_add_show');
}

function menus_manage()
{
  global $base_url, $theme_url, $db, $page, $l;
  
  # Get the language
  language_load('admin_menus');
  
  # Are we deleting one of the links?
  if(!empty($_GET['del']))
  {
    # Get this link's order
    $result = $db->query("
      SELECT
        link_order
      FROM {$db->prefix}menus
      WHERE link_id = %link_id
      LIMIT 1",
      array(
        'link_id' => array('int', $_GET['del']),
      ));
    @list($link_order) = $db->fetch_row($result);
    
    # Lower the order of all links with a higher order than this one
    $db->query("
      UPDATE {$db->prefix}menus
      SET
        link_order = link_order - 1
      WHERE link_order >= %link_order",
      array(
        'link_order' => array('int', $link_order),
      ));
    
    # Delete the link from the database
    $db->query("
      DELETE FROM {$db->prefix}menus
      WHERE link_id = %link_id
      LIMIT 1",
      array(
        'link_id' => array('int', $_GET['del']),
      ));
    
    # Update the last time the menus were updated, for caching calculations
    update_settings(array('menus_last_updated' => time_utc()));
    
    # Redirect
    redirect('index.php?action=admin;sa=menus;area=manage');
  }
  
  # Are we changing the order of one of the links?
  if(!empty($_GET['raise']) || !empty($_GET['lower']))
  {
    # Are we raising or lowering?
    $raise = isset($_GET['raise']) ? 1 : -1;
    $link_id = isset($_GET['raise']) ? $_GET['raise'] : $_GET['lower'];

    # Get this link's current order and menu
    $result = $db->query("
      SELECT
        link_order, link_menu
      FROM {$db->prefix}menus
      WHERE link_id = %link_id
      LIMIT 1",
      array(
        'link_id' => array('int', $link_id),
      ));
    @list($link_order, $link_menu) = $db->fetch_row($result);
    
    # Get the menu of link this one will take the position of
    $result = $db->query("
      SELECT
        link_menu
      FROM {$db->prefix}menus
      WHERE link_order = %link_order
      LIMIT 1",
      array(
        'link_order' => array('int', $link_order - $raise),
      ));
    @list($new_menu) = $db->fetch_row($result);
    
    # If this link would be changing menu, we need to not change order
    if($link_menu == $new_menu)
    {
      # Not changing menu? Okay let's change order
      # Change the order of the link this one will take the position of
      $db->query("
        UPDATE {$db->prefix}menus
        SET
          link_order = link_order + $raise
        WHERE link_order = %link_order",
        array(
          'link_order' => array('int', $link_order - $raise),
        ));

      # Change the order of our link and its menu too
      $db->query("
        UPDATE {$db->prefix}menus
        SET
          link_order = %link_order
        WHERE link_id = %link_id
        LIMIT 1",
        array(
          'link_order' => array('int', $link_order - $raise),
          'link_id' => array('int', $link_id),
        ));
    }
    elseif(($raise == -1 || $link_menu > 1) && ($raise == 1 || $link_menu < 5))
    {
      # Changing menu? Let's change it then
      # Change the menu of our link and its menu too
      $db->query("
        UPDATE {$db->prefix}menus
        SET
          link_menu = %link_menu
        WHERE link_id = %link_id
        LIMIT 1",
        array(
          'link_menu' => array('int', $link_menu - $raise),
          'link_id' => array('int', $link_id),
        ));
    }
    
    # Update the last time the menus were updated, for caching calculations
    update_settings(array('menus_last_updated' => time_utc()));
    
    # Redirect
    redirect('index.php?action=admin;sa=menus;area=manage');
  }

  # Get the menu links from the database
  $result = $db->query("
    SELECT
      link_id, link_name, link_href, link_menu, link_order, link_target, link_follow, who_view
    FROM {$db->prefix}menus
    ORDER BY link_order",
    array());
  
  # JavaScripts
  $page['scripts'][] = $theme_url. '/default/js/edit_menus.js';
  $page['js_vars']['save_text'] = $l['save'];
  $page['js_vars']['cancel_text'] = $l['cancel'];
  $page['js_vars']['same_text'] = $l['admin_menus_manage_window_same'];
  $page['js_vars']['new_text'] = $l['admin_menus_manage_window_new'];
  $page['js_vars']['yes_text'] = $l['admin_menus_manage_follow_yes'];
  $page['js_vars']['no_text'] = $l['admin_menus_manage_follow_no'];
  $page['js_vars']['total_links'] = $db->num_rows($result);
  
  $previous_menu = 0;
  
  $page['menus'] = array();
  # Format the menu links in a 2D array
  while($row = $db->fetch_assoc($result))
  {
    # If we skipped a menu (That has no links), we'll have to fill it in
    if($row['link_menu'] > $previous_menu + 1)
    {
      # Set each skipped menu to an empty array
      for($i = $previous_menu + 1; $i < $row['link_menu']; $i += 1)
        $page['menus'][$i] = array();
    }
    
    # Add the link
    $page['menus'][$row['link_menu']][] = $row;
    
    # Update the menu
    $previous_menu = $row['link_menu'];
  }
  
  # Set the title
  $page['title'] = $l['admin_menus_manage_title'];
  
  # Load the theme
  theme_load('admin_menus', 'menus_manage_show');
}

function menus_process($data)
{
  global $db;
  
  foreach($data as $key => $value)
    if(preg_match('/^group_(-1|[0-9]+)$/', $key))
    {
      $membergroups[preg_replace('/^group_(-1|[0-9]+)$/', '$1', $key)] = $value;
      unset($data[$key]);
    }
  
  $data['who_view'] = implode(',', array_keys(array_filter($membergroups)));
  
  $result = $db->query("
    SELECT
      link_order, link_menu
    FROM {$db->prefix}menus
    WHERE link_id = %link_id
    LIMIT 1",
    array(
      'link_id' => array('int', (int)preg_replace('/^link-([0-9]+)$/', '$1', $data['link_order'])),
    ));
  @list($data['link_order'], $data['link_menu']) = $db->fetch_row($result);
  $data['link_order'] += 1;
  
  $db->query("
    UPDATE {$db->prefix}menus
    SET
      link_order = link_order + 1
    WHERE link_order >= %link_order",
    array(
      'link_order' => array('int', $data['link_order']),
    ));
  
  $db->insert('insert', $db->prefix. 'menus',
    array(
      'link_name' => 'text', 'link_href' => 'text', 'link_target' => 'int',
      'link_menu' => 'int', 'link_follow' => 'int', 'link_order' => 'int',
      'who_view' => 'string-255',
    ),
    array(
      $data['link_name'], $data['link_href'], $data['link_target'],
      (int)$data['link_menu'], $data['link_follow'], $data['link_order'],
      $data['who_view'],
    ),
    array());
  
  # Update the last time the menus were updated, for caching calculations
  update_settings(array('menus_last_updated' => time_utc()));
}

function menus_edit_ajax()
{
  global $db, $l, $settings, $user;
  
  language_load('admin_menus');

  # Can you do this..?
  if(!can('manage_menus'))
  {
    echo json_encode(array('error' => $l['managemenus_ajax_not_allowed']));
    exit;
  }

  # Our output array.
  $output = array('error' => '');

  # Lets see, saving..?
  if(isset($_GET['save']))
  {
    # Get the link data
    $link_id = !empty($_POST['link_id']) ? $_POST['link_id'] : 0;
    $link_name = !empty($_POST['link_name']) ? $_POST['link_name'] : '';
    $link_href = !empty($_POST['link_href']) ? $_POST['link_href'] : '';
    $link_target = !empty($_POST['link_target']) ? $_POST['link_target'] : '';
    $link_follow = !empty($_POST['link_follow']) ? $_POST['link_follow'] : '';

    # Make sure its not totally empty...
    if(true)
    {
      # HTML special!
      $link_name = htmlspecialchars($link_name, ENT_QUOTES, 'UTF-8');
      $link_href = htmlspecialchars($link_href, ENT_QUOTES, 'UTF-8');

      # Update it!
      $db->query("
        UPDATE {$db->prefix}menus
        SET
          link_name = %link_name, link_href = %link_href, link_target = %link_target,
          link_follow = %link_follow
        WHERE link_id = %link_id
        LIMIT 1",
        array(
          'link_id' => array('int', $link_id),
          'link_name' => array('string', $link_name),
          'link_href' => array('string', $link_href),
          'link_target' => array('int', $link_target ? 1 : 0),
          'link_follow' => array('int', $link_follow ? 1 : 0),
        ));
      
      # Update the last time the menus were updated, for caching calculations
      update_settings(array('menus_last_updated' => time_utc()));
      
      # Give them the info
      $output['link_name'] = $link_name;
      $output['link_href'] = $link_href;
      $output['link_target'] = $link_target;
      $output['link_follow'] = $link_follow;
    }
    else
      $output['error'] = $l['managemenus_ajax_menu_name_error'];
  }
  else
  {
    # Nope, get the link info, the name at least and ID...
    $result = $db->query("
      SELECT
        link_id, link_name, link_href, link_target, link_follow
      FROM {$db->prefix}menus
      WHERE link_id = %link_id
      LIMIT 1",
      array(
        'link_id' => array('int', !empty($_POST['link_id']) ? $_POST['link_id'] : 0),
      ));

    # Does it exist?
    if($db->num_rows($result))
    {
      # Yeah, it exists.
      @list($link_id, $link_name, $link_href, $link_target, $link_follow) = $db->fetch_row($result);
      $output['link_id'] = $link_id;
      $output['link_name'] = $link_name;
      $output['link_href'] = $link_href;
      $output['link_target'] = $link_target;
      $output['link_follow'] = $link_follow;
    }
    else
      $output['error'] = $l['managemenus_ajax_menu_not_found'];
  }

  # Output our JSON array and we got it.
  echo json_encode($output);
}
?>