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
  
  ';
  
  AdminOptions();
}

function NocURL() {
global $cmsurl, $settings, $l, $user, $theme_url;
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
  <iframe style="overflow: auto; width: 475px; height: 100px;" src="'.$settings['page']['news_url'].'?stylesheet='.$theme_url.'/'.$settings['theme'].'/news.css"></iframe>
	
  <h2>'.$l['admin_options'].'</h2>
  
  ';
  
  AdminOptions();
}

function AdminOptions() {
global $l, $cmsurl;
  
  $options = array();
  if (can('manage_pages'))
    $options[] = 'pages';
  if (can('manage_basic-settings'))
    $options[] = 'basic-settings';
  $options[] = 'members';
  $options[] = 'permissions';
  $options[] = 'menus';
  $options[] = 'forum';
  $options[] = 'email';
  $options[] = 'news';
  if (can('manage_tos'))
    $options[] = 'tos';
  if (can('ban_ips') || can('unban_ips'))
    $options[] = 'ips';
  
  $odd = true;
  foreach ($options as $option) {
    echo '
  <div class="acp_'.($odd ? 'left' : 'right').'">
    <p class="main"><a href="'.$cmsurl.'index.php?action=admin;sa='.$option.'" title="'.$l['admin_menu_'.$option].'">'.$l['admin_menu_'.$option].'</a></p>
    <p class="desc">'.$l['admin_menu_'.$option.'_desc'].'</p>
  </div>
  ';
    $odd = !$odd;
  }
}

// Now, you can show this by doing loadTheme('Admin','Error'); which tells them they can't access the ACP :P
function Error() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['admin_error_header'].'</h1>
  <p>'.$l['admin_error_reason'].'</p>';
}
?>