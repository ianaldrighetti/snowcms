<?php
// default/Admin.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <div style="overflow: auto; width: 503px; height: 100px;">
    '.$settings['page']['news'].'
  </div>
  <table>
    <tr>
      <td>'.$l['admin_current_version'].'</td>
      <td>v'.$settings['version'].'</td>
    </tr>
    <tr>
      <td>'.$l['admin_snowcms_current_version'].'</td>
      <td>v'.$settings['latest_version'].'</td>
    </tr>
  </table>
  <div class="acp_left">
    <p class="main"><a href="'.$cmsurl.'index.php?action=admin&sa=managepages" title="'.$l['admin_menu_managepages'].'">'.$l['admin_menu_managepages'].'</a></p>
    <p class="desc">'.$l['admin_menu_managepages_desc'].'</p>
  </div>
  <div class="acp_right">
    <p class="main"><a href="'.$cmsurl.'index.php?action=admin&sa=basic-settings" title="'.$l['admin_menu_basic-settings'].'">'.$l['admin_menu_basic-settings'].'</a></p>
    <p class="desc">'.$l['admin_menu_basic-settings_desc'].'</p>  
  </div>
    <div class="acp_left">
    <p class="main"><a href="'.$cmsurl.'index.php?action=admin&sa=members" title="'.$l['admin_menu_members'].'">'.$l['admin_menu_members'].'</a></p>
    <p class="desc">'.$l['admin_menu_members_desc'].'</p>
  </div>
  <div class="acp_right">
    <p class="main">Main text</p>
    <p class="desc">Description</p>  
  </div>
  ';
}

function Error() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['admin_error_header'].'</h1>
  <p>'.$l['admin_error_reason'].'</p>';
}