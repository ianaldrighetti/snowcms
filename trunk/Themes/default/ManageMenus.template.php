<?php
// default/ManagePages.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user, $cmsurl;
  
  $menus = $settings['menus'];
  
  echo '
  <h1>'.$settings['page']['title'].'</h1>
    <form action="'.$cmsurl.'index.php?action=admin;sa=menus" method="post" style="display: inline">
    
    <p><input type="hidden" name="change_menus" value="true" /></p>
    
    <table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_name'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_url'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_new_window'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_menu'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_order'].'</th>
        <th></th>
      </tr>';
    foreach($menus['menus'] as $menu) {
      if ($menu['target'])
        $new_window = ' checked="checked"';
      else
        $new_window = "";
      if ($menu['menu'] == 2 || $menu['menu'] == 3)
        $sidebar = ' checked="checked"';
      else
        $sidebar = "";
      if ($menu['menu'] == 1 || $menu['menu'] == 3)
        $mainmenu = ' checked="checked"';
      else
        $mainmenu = "";
      
      echo '
      <tr>
        <td><input name="link_name_'.$menu['id'].'" value="'.$menu['name'].'" size="13" /></td>
        <td><input name="link_url_'.$menu['id'].'" value="'.$menu['url'].'" /></td>
        <td><input type="checkbox" name="link_new_window_'.$menu['id'].'"'.$new_window.' /></td>
        <td><input type="checkbox" name="link_main_'.$menu['id'].'"'.$mainmenu.' /><input type="checkbox" name="link_sidebar_'.$menu['id'].'"'.$sidebar.' /></td>
        <td><input name="link_order_'.$menu['id'].'" value="'.$menu['order'].'" size="1" style="text-align: center" /></td>
        <td><a href="'.$cmsurl.'index.php?action=admin;sa=menus;did='.$menu['id'].'">'.$l['managemenus_delete'].'</a></td>
      </tr>';
      
      // Get the highest order ready for the new row
      $new_order = 0;
      if ($menu['order'] > $new_order)
        $new_order = $menu['order'] + 1;
    }
    echo '
      <tr>
        <td><input name="new_link_name" value="" size="13" /></td>
        <td><input name="new_link_url" value="" /></td>
        <td><input type="checkbox" name="new_link_new_window" /></td>
        <td><input type="checkbox" name="new_link_main" /><input type="checkbox" name="new_link_sidebar" /></td>
        <td><input name="new_link_order" value="'.$new_order.'" size="1" style="text-align: center" /></td>
        <td></td>
      </tr>
    </table>
  
  <p><input type="submit" value="Save Changes" /></p>
  
  </form>';
}

function NoMenus() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.$settings['page']['title'].'</h1>
    <form action="'.$cmsurl.'index.php?action=admin;sa=menus" method="post" style="display: inline">
    
    <p><input type="hidden" name="change_menus" value="true" /></p>
    
    <table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_name'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_url'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_new_window'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_menu'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managemenus_order'].'</th>
        <th></th>
      </tr>
      <tr>
        <td><input name="new_link_name" value="" size="13" /></td>
        <td><input name="new_link_url" value="" /></td>
        <td><input type="checkbox" name="new_link_new_window" /></td>
        <td><input type="checkbox" name="new_link_main" /><input type="checkbox" name="new_link_sidebar" /></td>
        <td><input name="new_link_order" value="1" size="1" style="text-align: center" /></td>
        <td></td>
      </tr>
    </table>
  
  <p><input type="submit" value="Save Changes" /></p>
  
  </form>
  ';
}
?>