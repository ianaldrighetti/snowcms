<?php
// SnowCMS
// http://www.snowcms.com/

if(!defined("Snow"))
  die("Hacking Attempt...");

function ManageMenus() {
global $settings, $db_prefix;
  
  if (@$_REQUEST['link_name'])
    AddLink();
  
  $settings['page']['title'] = 'Manage Menus';
  $result = sql_query("SELECT * FROM {$db_prefix}menus");
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
  }
  
  $settings['menus']['menus'] = $menus;
  
  loadTheme("ManageMenus");
}

function AddLink() {
global $db_prefix;
  
  $link_name = clean($_REQUEST['link_name']);
  $link_url = clean(@$_REQUEST['link_url']);
  
  sql_query("INSERT INTO {$db_prefix}menus (link_name, href, target, menu) VALUES ('$link_name', '$link_url', '0', '1')");
}

?>