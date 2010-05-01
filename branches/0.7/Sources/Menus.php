<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                  Menus.php file

if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

function ManageMenus() {
global $l, $settings, $db_prefix;
  
  // Add link to the link tree
  AddTree('Menus','index.php?action=admin;sa=menus');
  
  // Check if they are allowed to manage menus
  if (can('manage_menus')) {
    // Change links' names details
    if (@$_REQUEST['change_menus']) {
      // Save changes to current links
      foreach ($_REQUEST as $key => $value)
        if (substr($key,0,10) == 'link_name_') {
          $link_id = clean(substr($key, 10, strlen($key)));
          $link_name = clean($value);
          $link_url = clean(@$_REQUEST['link_url_'. $link_id]);
          $link_new_window = @$_REQUEST['link_new_window_'. $link_id] == true;
          $link_main = @$_REQUEST['link_main_'. $link_id] == true;
          $link_sidebar = (@$_REQUEST['link_sidebar_'. $link_id] == true) * 2;
          $link_menu = $link_main + $link_sidebar;
          $link_perm = (int)@$_REQUEST['link_perm_'. $link_id];
          $link_order = (int)@$_REQUEST['link_order_'. $link_id];
          sql_query("
          UPDATE {$db_prefix}menus
          SET
            `link_name` = '$link_name',
            `href` = '$link_url',
            `target` = '$link_new_window',
            `menu` = '$link_menu',
            `permission` = '$link_perm',
            `order` = '$link_order'
          WHERE
            `link_id` = '$link_id'
          ");
        }
      // Insert new link
      if (@$_REQUEST['new_link_name'] || @$_REQUEST['new_link_url']) {
        $link_name = clean($_REQUEST['new_link_name']);
        $link_url = clean($_REQUEST['new_link_url']);
        $link_new_window = @$_REQUEST['new_link_new_window'] == true;
        $link_main = @$_REQUEST['new_link_main'] == true;
        $link_sidebar = (@$_REQUEST['new_link_sidebar'] == true) * 2;
        $link_menu = (int)$link_main + $link_sidebar;
        $link_perm = (int)@$_REQUEST['new_link_perm'];
        $link_order = clean(@$_REQUEST['new_link_order']);
        // Now the Query for the new link :-D
        sql_query("
        INSERT INTO {$db_prefix}menus
        (
          `link_name`,
          `href`,
          `target`,
          `menu`,
          `permission`,
          `order`
        )
        VALUES (
          '$link_name',
          '$link_url',
          '$link_new_window',
          '$link_menu',
          '$link_perm',
          '$link_order'
        )
        ");
      }
      // Redirect back home
      redirect('index.php?action=admin;sa=menus');
    }
    // Delete a link
    if (!empty($_REQUEST['did'])) {
      // Get the link ID they want to delete
      $did = clean($_REQUEST['did']);
      sql_query("DELETE FROM {$db_prefix}menus WHERE `link_id` = '$did'");
      redirect('index.php?action=admin;sa=menus');
    }
    
    $settings['page']['title'] = $l['managemenus_title'];
    $result = sql_query("SELECT * FROM {$db_prefix}menus ORDER BY `order`");
    if ($settings['menus']['total'] = mysql_num_rows($result)) {
      while($row = mysql_fetch_assoc($result)) {
            $menus[] = array(
              'id' => $row['link_id'],
              'order' => $row['order'],
              'name' => $row['link_name'],
              'url' => $row['href'],
              'target' => $row['target'],
              'menu' => $row['menu'],
              'permission' => $row['permission']
            );
      }
      // We need to reload the menus, so they are up to date
      loadMenus();
      $settings['menus']['menus'] = $menus;
      loadTheme('ManageMenus');
    }
    else
      loadTheme('ManageMenus','NoMenus');
  }
  // They don't have permission, so redrect them to the main control panel
  else
    redirect('index.php?action=admin');
}
?>