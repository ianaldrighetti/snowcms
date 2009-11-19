<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#         Main Template, January 15, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function header_template()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>', !empty($page['title']) ? $page['title'] : $settings['site_name'], '</title>
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css" media="all" />
  <script type="text/javascript" src="', $settings['default_theme_url'], '/js/libsnow.js"></script>
  <script type="text/javascript">
    var base_url = "', $base_url, '";
    var theme_url = "', $theme_url, '";
    var current_theme = "', $settings['theme'], '";';

  # Extra Javascript variables..?
  if(isset($page['js_vars']) && count($page['js_vars']))
    foreach($page['js_vars'] as $varName => $varValue)
      echo '
    var ', $varName, ' = ', $varValue, ';';

  echo '
  </script>
  <script type="text/javascript" src="', $settings['default_theme_url'], '/js/hashpassword.js"></script>';
  
  # We need to run a task..? Shweet!
  if(!empty($page['run_task']))
    echo '
  <script type="text/javascript" src="', $settings['default_theme_url'], '/js/task.js"></script>';
  
  # Other scripts people want to be loaded?
  if(isset($page['scripts']) && count($page['scripts']))
    foreach($page['scripts'] as $script_src)
      echo '
  <script type="text/javascript" src="', $script_src, '"></script>';
  
  # Meta tags..?
  if(!empty($page['meta_description']))
    echo '
  <meta name="description" content="', $page['meta_description'], '" />';
  if(!empty($page['meta_keywords']))
    echo '
  <meta name="keywords" content="', $page['meta_keywords'], '" />';
  
  # No index meta tag?
  if(!empty($page['no_index']))
    echo '
  <meta name="robots" content="noindex" />';
  
  # Others... like custom added :P!
  if(isset($page['meta']) && count($page['meta']))
    foreach($page['meta'] as $name => $content)
      echo '
  <meta name="', $name, '" content="', $content, '" />';
  
  # Hmm... now <link> tags perhaps..? Indeed Watson :)
  if(isset($page['link']) && count($page['link']))
    foreach($page['link'] as $link)
      echo '
  <link rel="', $link['rel'], '"', (!empty($link['type']) ? ' type="'. $link['type']. '"' : ''), ' href="', $link['href'], '"', (!empty($link['title']) ? ' title="'. $link['title']. '"' : ''), '', (!empty($link['target']) ? ' target="_blank"' : ''), '', (!empty($link['media']) ? ' media="'. $link['media']. '"' : ''), ' />';
  
  echo '
</head>
';

if(isset($page['onload']))
  echo '<body onload="', $page['onload'], '">';
else
  echo '<body>';

echo '
<div id="container">
  <div class="top_fade">
  </div>
  <div class="welcome">';

if(!$user['is_logged'])
{
  echo '
    <h3>', $l['main_sidebar_login'], '</h3>
    <fieldset class="login">
      <form action="', $base_url, '/index.php?action=login2" method="post" onSubmit="hashPassword(this.form);">
        <table>
          <tr>
            <td><label for="username">', $l['main_sidebar_username'], '</label></td>
            <td><input name="username" type="text" id="username" value="" class="text" tabindex="1" /></td>
            <td><span class="register">[<a href="', $base_url, '/index.php?action=register">', $l['main_register'], '</a>]</span>
          </tr>
          <tr>
            <td><label for="passwrd">', $l['main_sidebar_password'], '</label></td>
            <td><input name="passwrd" type="password" id="passwrd" value="" class="text" tabindex="2" /></td>
            <td><input type="submit" tabindex="3" value="', $l['main_sidebar_login_button'], '" class="button" /></td>
          </tr>
        </table>
        <input type="hidden" name="hashed_passwrd" id="hashed_passwrd" value="" />
        <input type="hidden" name="expires" value="-1" />
      </form>
    </fieldset>';
}
else
{
  if(can('view_pms'))
  {
    echo '
    <h3>', $l['main_sidebar_welcome'], '</h3>
    <p>', $l['hello'], ' <a href="', $base_url, '/index.php?action=profile">', $user['name'], '</a>! [<a href="', $base_url, '/index.php?action=logout;sc=', $user['sc'], '" class="logout">', $l['main_sidebar_logout'], '</a>]</p>
    <p>', nbsp(sprintf($l['welcome_pms_'. (int)($user['total_pms'] != 1). '_'. (int)($user['unread_pms'] != 1)], numberformat($user['total_pms']), numberformat($user['unread_pms']))), '</p>';
  }
  else
  {
    echo '
    <br />
    <h3>', $l['main_sidebar_welcome'], '</h3>
    <p>', $l['hello'], ' <a href="', $base_url, '/index.php?action=profile">', $user['name'], '</a>! [<a href="', $base_url, '/index.php?action=logout;sc=', $user['sc'], '" class="logout">', $l['main_sidebar_logout'], '</a>]</p>';
  }
}

echo '
  </div>
  <div class="logo">
    <h1><a href="', $base_url, '">', $settings['site_name'], '</a></h1>
    <h3>', $settings['site_slogan'] ? $settings['site_slogan'] : '&nbsp;', '</h3>
  </div>
  <div class="top_menu">';


    # Menu items?
    if(isset($settings['menu']['1']) && count($settings['menu']['1']))
    {
          echo '
    <ul>';
      foreach($settings['menu']['1'] as $link)
        echo '
      <li><a href="', $link['href'], '"', (!empty($link['target']) ? ' target="_blank"' : ''), '', (!empty($link['follow']) ? '' : ' rel="nofollow"'), '>', $link['name'], '</a></li>';
      echo '
    </ul>';
    }
      
        echo '
    <div class="break">
    </div>
  </div>';
      
  if(!empty($page['show_adminMenu']))
  {
    echo '
  <div class="top_menu admin_menu">';  

      buildAdminMenu();

    echo '
    <div class="break"></div>
  </div>';

  }

  echo '
  <div class="main_body">
    <div class="content">';
}

function footer_template()
{
  global $base_url, $db, $l, $page, $settings, $theme, $theme_url, $user;

  echo '    
    </div>
    <div class="bottom_menu">';


      # Menu items?
      if(isset($settings['menu']['2']) && count($settings['menu']['2']))
      {
            echo '
      <ul>';
        foreach($settings['menu']['2'] as $link)
          echo '
        <li><a href="', $link['href'], '"', (!empty($link['target']) ? ' target="_blank"' : ''), '', (!empty($link['follow']) ? '' : ' rel="nofollow"'), '>', $link['name'], '</a></li>';
        # Add the admin control panel link if they can administrate the site
        if(can('view_admin_panel'))
          echo '
        <li><a href="'. $base_url. '/index.php?action=admin" title="'. $l['menu_static_admin_title']. '">'. $l['menu_static_admin']. '</a></li>';
        echo '
      </ul>';
      }
        
          echo '
    </div>
  </div>
  <div class="footer">
    <p>', $l['powered_by'], ' <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS v', $settings['scmsVersion'], '</a>', !empty($settings['show_query_count']) ? ' | '. sprintf($l['page_created_with'], numberformat(!empty($db->num_queries) ? (int)$db->num_queries : 0), numberformat($page['created_in'])) : '', '</p>
  </div>
  <br />
</div>
</body>
</html>';
}

function buildAdminMenu()
{
  global $base_url, $page, $theme_url;

  # We need the admin actions...
  if(empty($page['adminActions']))
    return;

  # Lets build up the Administrative Menu.
  # We need a couple JavaScript things.
  echo '
    <script type="text/javascript" src="', $theme_url, '/default/js/menu.js"></script>
    <script type="text/javascript">
      _.ready(function() {';
  foreach($page['adminActions'] as $sa => $action)
    echo '
        new_dropdown(_.G("menu_', $sa, '"), _.G("handle_', $sa, '"));';
  echo '
    });
    </script>
    <ul>';

  # Loop through all the main menu items.
  foreach($page['adminActions'] as $sa => $action)
  {
    # Its gotta be viewable.
    if($action['viewable'])
    {
      echo '
      <li><a href="', $base_url, '/index.php?action=admin;sa=', $sa, ';area=', $action['default'], '" title="', $action['title'], '"', !empty($page['sa']) && $page['sa'] == $sa ? ' class="admin_menu_selected"' : '', ' id="handle_', $sa, '">', $action['name'], '</a>
        <ul id="menu_', $sa, '" class="dd_menu">';

      foreach($action['areas'] as $area => $info)
        if($info['viewable'])
          echo '<li><a href="', $base_url, '/index.php?action=admin;sa=', $sa, ';area=', $area, '" title="', $info['title'], '"', !empty($page['sa']) && $page['sa'] == $sa && !empty($page['area']) && $page['area'] == $area ? ' class="admin_menu_selected"' : '', '>', $info['name'], '</a></li>';
      
      echo '</ul>
      </li>';
    }
  }
  echo '
    </ul>';
}
?>