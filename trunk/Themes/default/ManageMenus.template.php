<?php
// default/ManagePages.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user, $cmsurl;
  
  $menus = $settings['menus'];
  
  echo '
  <h1>Manage Menus</h1>
  ';
  if($menus['total']) {
    echo '
    <form action="'.$cmsurl.'index.php?action=admin&sa=menus" method="post" style="display: inline">
    
    <p><input type="hidden" name="change_menus" value="true" /></p>
    
    <table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px">Name</th>
        <th style="border-style: solid; border-width: 1px">Link</th>
        <th style="border-style: solid; border-width: 1px">New Window</th>
        <th style="border-style: solid; border-width: 1px">Sidebar</th>
        <th style="border-style: solid; border-width: 1px">Order</th>
        <th></th>
      </tr>';
    foreach($menus['menus'] as $menu) {
      if ($menu['target'])
        $new_window = ' checked="checked"';
      else
        $new_window = "";
      if ($menu['menu'])
        $sidebar = ' checked="checked"';
      else
        $sidebar = "";
      
      echo '
      <tr>
        <td><input name="" value="'.$menu['name'].'" size="13" /></td><td><input name="" value="'.$menu['url'].'" /></td><td><input type="checkbox" name=""'.$new_window.' /></td><td><input type="checkbox" name=""'.$sidebar.' /></td><td><input name="" value="'.$menu['order'].'" size="1" style="text-align: center" /></td><td><a href="">Delete</a></td>
      </tr>';
    }
    echo '
    </table>
  
  <p><input type="submit" value="Save Changes" /></p>
  
  </form>
  
  <br />
  
  <form action="'.$cmsurl.'index.php?action=admin&sa=menus" method="post">
    <table>
      <tr>
        <td>Name:</td><td><input name="link_name" value="" /></td>
      </tr><tr>
        <td>URL:</td><td><input name="link_url" value="" /></td>
      </tr><tr>
        <td>&nbsp;</td><td><input name="make_link" type="submit" value="Create Link"/></td>
      </tr>
    </table>
  </form>';
  }
  else {
    echo '<p>There are no menus.</p>';
  }
}
?>