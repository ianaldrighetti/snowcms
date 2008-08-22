<?php
// SnowCMS
// http://www.snowcms.com/

if(!defined("Snow"))
  die("Hacking Attempt...");

function ManageMenus() {
global $l, $settings, $db_prefix;
  
  // Change links' names details
  if (@$_REQUEST['change_menus']) {
    
    // Perform a bunch of steps to make the order numbers whole and not skip any
    // e.g. turn 1, 3, 3.5 and 4 into 1, 2, 3 and 4
    $order[] = 0;
    $total = 0;
    $max = -1;
    $min = -1;
    foreach ($_REQUEST as $key => $value) {
      if (substr($key,0,11) == 'link_order_') {
        $link_id = clean(substr($key,11,strlen($key)));
        $link_order = clean(@$_REQUEST['link_order_'.$link_id]);
        $link[$link_id] = $link_order;
        
        if ($max < $link_id)
          $max = $link_id + 1;
        if ($min > $link_id || $min == -1)
          $min = $link_id;
      }
    }
    
    if ($max == -1)
      $max = 0;
    
    if (@$_REQUEST['new_link_name'] || @$_REQUEST['new_link_url']) {
      $new_id = $max;
      $link[$new_id] = clean(@$_REQUEST['new_link_order']);
      $max += 1;
    }
    
    $b = 1;
    while ($b < $max - $min + 2) {
      $smallest_value = -1;
      $smallest_id = -1;
      $i = 0;
      while ($i <= $max) {
        if (($smallest_value > @$link[$i] || $smallest_value == -1) && @$link[$i]) {
          $smallest_value = $link[$i];
          $smallest_id = $i;
        }
        $i += 1;
      }
      
      $order[$smallest_id] = $b;
      $link[$smallest_id] = 0;
      $b += 1;
    }
    
    // Save changes to current links
    foreach ($_REQUEST as $key => $value)
      if (substr($key,0,10) == 'link_name_') {
        $link_id = clean(substr($key,10,strlen($key)));
        $link_name = clean($value);
        $link_url = clean(@$_REQUEST['link_url_'.$link_id]);
        $link_new_window = @$_REQUEST['link_new_window_'.$link_id] == true;
        $link_sidebar = @$_REQUEST['link_sidebar_'.$link_id] == true;
        $link_order = $order[$link_id];
        sql_query("
        UPDATE {$db_prefix}menus
        SET
          `link_name` = '$link_name',
          `href` = '$link_url',
          `target` = '$link_new_window',
          `menu` = '$link_sidebar',
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
      $link_sidebar = @$_REQUEST['new_link_sidebar'] == true;
      $link_order = $order[$new_id];
      sql_query("
      INSERT INTO {$db_prefix}menus
      (
        `link_name`,
        `href`,
        `target`,
        `menu`,
        `order`
      )
      VALUES (
        '$link_name',
        '$link_url',
        '$link_new_window',
        '$link_sidebar',
        '$link_order'
      )
      ");
    }
  }
  // Delete a link
  if (@$_REQUEST['did']) {
    $did = clean($_REQUEST['did']);
    sql_query("DELETE FROM {$db_prefix}menus WHERE `link_id` = '$did'");
    
    // Perform a bunch of steps to make the order numbers whole and not skip any
    // e.g. turn 1, 3, 3.5 and 4 into 1, 2, 3 and 4
    $result = sql_query("SELECT * FROM {$db_prefix}menus");
    if (mysql_num_rows($result)) {
    while ($row = mysql_fetch_assoc($result))
      $_REQUEST['link_order_'.$row['link_id']] = $row['order'];
    $order[] = 0;
    $total = 0;
    $max = -1;
    $min = -1;
    foreach ($_REQUEST as $key => $value) {
      if (substr($key,0,11) == 'link_order_') {
        $link_id = clean(substr($key,11,strlen($key)));
        $link_order = clean(@$_REQUEST['link_order_'.$link_id]);
        $link[$link_id] = $link_order;
        
        if ($max < $link_id)
          $max = $link_id + 1;
        if ($min > $link_id || $min == -1)
          $min = $link_id;
      }
    }
    
    $b = 1;
    while ($b < $max - $min + 2) {
      $smallest_value = -1;
      $smallest_id = -1;
      $i = 0;
      while ($i <= $max) {
        if (($smallest_value > @$link[$i] || $smallest_value == -1) && @$link[$i]) {
          $smallest_value = $link[$i];
          $smallest_id = $i;
        }
        $i += 1;
      }
      
      $order[$smallest_id] = $b;
      $link[$smallest_id] = 0;
      $b += 1;
    }
    
    foreach ($_REQUEST as $key => $value)
      if (substr($key,0,11) == 'link_order_') {
        $link_id = clean(substr($key,11,strlen($key)));
        $link_order = $order[$link_id];
        sql_query("
        UPDATE {$db_prefix}menus
        SET
          `order` = '$link_order'
        WHERE
          `link_id` = '$link_id'
        ");
      }
    }
    //////////
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
            'menu' => $row['menu']
          );
    }
    
    $settings['menus']['menus'] = $menus;
    loadTheme('ManageMenus');
  }
  else
    loadTheme('ManageMenus','NoMenus');
  
  
}

?>