<?php
// default/Admin.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

// When loadTheme('Admin'); is called on, it will display this below function (Main()) and the theme_header(); before this, and theme_footer(); after
function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['admin_title'].'</h1>
  <table>
    <tr>
      <td>'.$l['admin_current_version'].'</td>
      <td>v'.$settings['version'].'</td>
    </tr>
    <tr>
      <td>'. $l['admin_snowcms_current_version']. '</td>
      <td>'. $settings['latest_version']. '</td>
    </tr>
  </table>
  
  <h2>'.$l['admin_snowcms_news'].'</h2>
  <div style="overflow: auto; width: 475px; height: 100px;">
    '. $settings['page']['news']. '
	</div>
	
  <h2>'.$l['admin_options'].'</h2>
  <div class="acp_left">
    <p class="main"><a href="'. $cmsurl. 'index.php?action=admin;sa=managepages" title="'. $l['admin_menu_managepages']. '">'. $l['admin_menu_managepages']. '</a></p>
    <p class="desc">'. $l['admin_menu_managepages_desc']. '</p>
  </div>
  <div class="acp_right">
    <p class="main"><a href="'. $cmsurl. 'index.php?action=admin;sa=basic-settings" title="'. $l['admin_menu_basic-settings']. '">'. $l['admin_menu_basic-settings']. '</a></p>
    <p class="desc">'. $l['admin_menu_basic-settings_desc']. '</p>  
  </div>
    <div class="acp_left">
    <p class="main"><a href="'. $cmsurl. 'index.php?action=admin;sa=members" title="'. $l['admin_menu_members']. '">'. $l['admin_menu_members']. '</a></p>
    <p class="desc">'. $l['admin_menu_members_desc']. '</p>
  </div>
  <div class="acp_right">
    <p class="main"><a href="'. $cmsurl. 'index.php?action=admin;sa=permissions" title="'. $l['admin_menu_permissions']. '">'. $l['admin_menu_permissions']. '</a></p>
    <p class="desc">'. $l['admin_menu_permissions_desc']. '</p>  
  </div>
  <div class="acp_left">
    <p class="main"><a href="'. $cmsurl. 'index.php?action=admin;sa=menus" title="'. $l['admin_menu_menus']. '">'. $l['admin_menu_menus']. '</a></p>
    <p class="desc">'. $l['admin_menu_menus_desc']. '</p>  
  </div>
  <div class="acp_right">
    <p class="main"><a href="'. $cmsurl. 'index.php?action=admin;sa=forum" title="'. $l['admin_menu_forum']. '">'. $l['admin_menu_forum']. '</a></p>
    <p class="desc">'. $l['admin_menu_forum_desc']. '</p>  
  </div>
  ';
}

// Now, you can show this by doing loadTheme('Admin','Error'); which tells them they can't access the ACP :P
function Error() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['admin_error_header'].'</h1>
  <p>'.$l['admin_error_reason'].'</p>';
}
?>