<?php
#########################################################################
#                             SnowCMS v2.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#                  Released under the GNU GPL v3 License                #
#                     www.gnu.org/licenses/gpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#                SnowCMS v2.0 began in November 2009                    #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 2.0                         #
#########################################################################

if(!defined('IN_SNOW'))
  die;

class Admin_Theme extends Theme
{
  protected function init()
  {
    global $cookie_name, $theme_url;

    $this->add_js_file(array('src' => $theme_url. '/default/js/snowobj.js'));
    $this->add_js_var('cookie_name', $cookie_name);
  }

  public function header()
  {
    global $api, $base_url, $cookie_name, $member, $settings;

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <style type="text/css">
    body
    {
      margin: 0px !important;
      font-family: Verdana, sans-serif, Arial;
      font-size: 13px;
      background: #666666;
    }

    #header
    {
      width: 100%;
      background: #336699;
    }

    #header a
    {
      color: #E6E6E6;
      text-decoration: none;
    }

    #header a:hover
    {
      text-decoration: underline;
    }

    #header h1
    {
      margin: 0px;
      padding: 10px 0px 0px 20px;
      color: #F9F9F9;
      font-family: Georgia;
      font-size: 22px;
      font-weight: normal;
    }

    #header h1 .goto_site
    {
      font-size: 12px !important;
      font-style: italic;
    }

    #header h3
    {
      margin: 0px;
      padding: 0px 0px 5px 20px;
      font-family: Georgia;
      font-size: 14px;
      font-style: italic;
      font-weight: normal;
      color: #FFFFFF;
    }

    #header #left
    {
      float: left;
    }

    #header #right
    {
      float: right;
      padding: 10px 10px 0px 0px;
      font-size: 14px;
      color: #FFFFFF;
    }

    #header p
    {
      margin: 0px !important;
      padding: 0px !important;
    }

    #container
    {
      margin: 0px auto 0px auto;
      padding: 5px 5px 0px 5px;
      background: #E6E6E6;
    }

    #container #sidebar
    {
      float: left;
      width: 180px;
      padding-left: 5px;
    }

    #container #sidebar .category
    {
      margin: 0px 0px 6px 0px;
      background: #B6B6B6;
      border: 1px solid #A2A2A2;
      border-width: 1px 1px 0px 1px;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      -moz-border-radius-topleft: 10px;
      -moz-border-radius-topright: 10px;
    }

    #container #sidebar .header
    {
      display: block;
      margin: 0px !important;
      padding: 5px !important;
      font-size: 16px;
      font-family: Georgia, "Times New Roman", "Bitstream Charter", Times, serif;
      border-top: 1px solid #FFFFFF;
      border-bottom: 1px solid #A2A2A2;
    }

    #container #sidebar .header a
    {
      color: #FFFFFF;
      text-decoration: none;
    }

    #container #sidebar .header a:hover
    {
      text-decoration: underline;
    }

    #container #sidebar ul
    {
      margin: 0px !important;
      padding: 0px;
      list-style: none;
      background: #FFFFFF;
      border-bottom: 1px solid #A2A2A2;
    }

    #container ul li a
    {
      display: block;
      padding: 5px 0px 5px 5px;
      font-size: 14px;
      text-decoration: none;
      color: #000000;
    }

    #container ul li a:hover
    {
      background: #DDDDDD;
    }

    #container #sidebar .first
    {
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      -moz-border-radius-topleft: 10px;
      -moz-border-radius-topright: 10px;
    }

    #container #sidebar .last
    {
      border-bottom: 0px !important;
    }

    #container #content
    {
      float: left;
      padding-left: 20px;
    }

    #container #content h1
    {
      margin-top: 0px !important;
      font-family: Georgia, "Times New Roman", "Bitstream Charter", Times, serif;
      font-style: italic;
      font-size: 24px;
      font-weight: normal;
      color: #464646;
    }

    #container #content #left
    {
      float: left;
      width: 48%;
    }

    #container #content #right
    {
      float: right;
      width: 48%;
    }

    #footer
    {
      background: #666666;
      color: #999999;
      padding: 5px;
      text-align: center;
      font: italic 13px Georgia;
    }

    #footer a
    {
      color: #DDDDDD;
      text-decoration: none;
    }

    #footer a:hover
    {
      text-decoration: underline;
    }

    .attention
    {
      margin: 5px;
      padding: 5px;
      font-size: 14px;
      border: 2px solid red;
      background: #FFC6C6;
    }

    .break
    {
      clear: both;
    }
  </style>';

    # Any meta tags?
    if(count($this->meta))
      foreach($this->meta as $meta)
        echo '
  ', $this->generate_tag('meta', $meta);

    echo '
  <title>', $api->apply_filters('theme_title', (!empty($this->title) ? htmlchars($this->title). ' - ' : ''). (!empty($this->main_title) ? $this->main_title : '')), '</title>';

    # Links
    if(count($this->links))
      foreach($this->links as $link)
        echo '
  ', $this->generate_tag('link', $link);

    # JavaScript variables :D
    if(count($this->js_vars))
    {
      echo '
  <script type="text/javascript" language="JavaScript"><!-- // --><![CDATA[';

      foreach($this->js_vars as $variable => $value)
        echo '
    var ', $variable, ' = ', is_numeric($value) ? $value : '"'. addcslashes($value, '"'). '"', ';';

      echo '
  // ]]></script>';
    }

    # Now JavaScript files!
    if(count($this->js_files))
      foreach($this->js_files as $js_file)
        echo '
  <script', !empty($js_file['language']) ? ' language="'. $js_file['language']. '"' : '', !empty($js_file['type']) ? ' type="'. $js_file['type']. '"' : '', !empty($js_file['src']) ? ' src="'. $js_file['src']. '"' : '', !empty($js_file['defer']) ? ' defer="defer"' : '', !empty($js_file['charset']) ? ' charset="'. $js_file['charset']. '"' : '', '></script>';

    echo '
  <script type="text/javascript"><!-- // --><![CDATA[
    function open_list(element_id, is_last)
    {
      var element = document.getElementById(element_id);

      if(is_last && element.style.display != \'none\')
      {
        element.parentNode.style.borderBottomLeftRadius = \'0px\';
        element.parentNode.style.borderBottomRightRadius = \'0px\';
        element.parentNode.style.mozBorderRadiusBottomleft = \'0px\';
        element.parentNode.style.mozBorderRadiusBottomright = \'0px\';
      }
      else if(is_last)
      {
        element.parentNode.style.borderBottomLeftRadius = \'10px\';
        element.parentNode.style.borderBottomRightRadius = \'10px\';
        element.parentNode.style.mozBorderRadiusBottomleft = \'10px\';
        element.parentNode.style.mozBorderRadiusBottomright = \'10px\';
      }

      element.style.display = element.style.display == \'none\' ? \'block\' : \'none\';
      s.setcookie(cookie_name + \'_menus_\' + element_id, element.style.display == \'none\' ? 0 : 1, 365);
    }

    function slide_list(element_id, change)
    {
      var element = document.getElementById(element_id);
      var height = typeof element.style.height == \'undefined\' ? 0 : parseInt(element.style.height);

      if((height >= 100 && change > 0) || (height <= 0 && change < 0))
        return;

      element.style.height = (height + change) + \'px\';
      setTimeout(\'slide_list(\\\'\' + element_id + \'\\\', \' + change + \');\', 100);
    }
  // ]]></script>
</head>
<body>
<div id="header">
  <div id="left">
    <h1>', $settings->get('site_name', 'string'), ' ', l('<a href="%s" class="goto_site" title="Go to site">&laquo; Go to site &raquo;</a>', $base_url), '</h1>
    <h3>', l('Administration Center'), '</h3>
  </div>
  <div id="right">
    <p>', l('You are currently logged in as <a href="%s" title="View your profile" style="font-weight: bold;">%s</a>.', $base_url. '/index.php?action=profile', $member->display_name()), '</p>
    <p style="text-align: right;">', l('<a href="%s" title="Log out of your account">&laquo; Log out &raquo;</a>', $base_url. '/index.php?action=logout&amp;sc='. $member->session_id()), '</p>
  </div>
  <div class="break">
  </div>
</div>
<div id="container">';

    # Any notices, perhaps?
    if(strlen($api->apply_filters('admin_theme_notices', '')) > 0)
    {
      echo '
  <div class="attention">
    ', $api->apply_filters('admin_theme_notices', ''), '
  </div>';
    }

    echo '
  <div id="sidebar">
    <div class="category">
      <p class="header first"><a href="javascript:void(0);" onclick="open_list(\'list_1_1\');">', l('SnowCMS'), '</a></p>
      <ul id="list_1_1" style="display: ', (!isset($_COOKIE[$cookie_name. '_menus_list_1_1']) || !empty($_COOKIE[$cookie_name. '_menus_list_1_1']) ? 'block' : 'none'), ';">
        <li><a href="', $base_url, '/index.php?action=admin">', l('Home'), '</a></li>
        <li><a href="', $base_url, '/index.php?action=admin&amp;sa=update" title="', l('Check for any updates for your system'), '">', l('Update'), '</a></li>
      </ul>
      <p class="header"><a href="javascript:void(0);" onclick="open_list(\'list_1_2\');" title="', l('Member management'), '">', l('Members'), '</a></p>
      <ul id="list_1_2" style="display: ', (!isset($_COOKIE[$cookie_name. '_menus_list_1_2']) || !empty($_COOKIE[$cookie_name. '_menus_list_1_2']) ? 'block' : 'none'), ';">
        <li><a href="', $base_url, '/index.php?action=admin&amp;sa=member_add" title="', l('Add a new member'), '">', l('Add'), '</a></li>
        <li><a href="', $base_url, '/index.php?action=admin&amp;sa=member_manage" title="', l('Manage your existing members'), '">', l('Manage'), '</a></li>
        <li><a href="', $base_url, '/index.php?action=admin&amp;sa=member_settings" title="', l('Member settings'), '">', l('Settings'), '</a></li>
      </ul>
      <p class="header last"><a href="javascript:void(0);" onclick="open_list(\'list_1_3\');" title="', l('Plugin management'), '">', l('Plugins'), '</a></p>
      <ul id="list_1_3" style="display: ', (!isset($_COOKIE[$cookie_name. '_menus_list_1_3']) || !empty($_COOKIE[$cookie_name. '_menus_list_1_3']) ? 'block' : 'none'), ';">
        <li><a href="', $base_url, '/index.php?action=admin&amp;sa=plugin_install" title="', l('Add a new plugin'), '">', l('Add'), '</a></li>
        <li><a href="', $base_url, '/index.php?action=admin&amp;sa=plugin_manage" title="', l('Plugin management'), '">', l('Manage'), '</a></li>
      </ul>
    </div>';

    # Time to get all the links for the admin menu, organized into categories!
    $categories = array();

    $menu = $api->return_menu_items('action=admin');

    # Anything?
    if(!empty($menu) && count($menu) > 0)
    {
      foreach($menu as $link)
      {
        if(empty($link['extra']))
          $link['extra'] = l('Other');

        # Do we need to add the category?
        if(!empty($link['extra']) && !isset($categories[$link['extra']]))
          $categories[$link['extra']] = array();

        $index = $link['extra'];
        unset($link['extra']);
        $categories[$index][] = $link;
      }

      echo '
    <div class="category">';

      $current = 1;
      $last = count($categories);
      foreach($categories as $category => $links)
      {
        echo '
      <p class="header', ($current == 1 ? ' first' : ''), ($current == $last ? ' last' : ''), '"><a href="javascript:void(0);" onclick="open_list(\'list_2_', $current, '\');">', $category, '</a></p>
      <ul id="list_2_', $current, '" style="display: ', (!isset($_COOKIE[$cookie_name. '_menus_list_2_'. $current]) || !empty($_COOKIE[$cookie_name. '_menus_list_2_'. $current]) ? 'block' : 'none'), ';">';

        # Now display those links!
        foreach($links as $link)
        {
          # Remove the content index :P
          $content = $link['content'];
          unset($link['content']);

          echo '
        <li>', $this->generate_tag('a', $link, false), $content, '</a></li>';
        }

        echo '
      </ul>';

        $current++;
      }

      echo '
    </div>';
    }

    echo '
  </div>
  <div id="content">', $api->apply_filters('admin_theme_pre_content', '');
  }

  public function footer()
  {
    global $api, $db, $settings, $start_time;

    echo $api->apply_filters('admin_theme_post_content', ''), '
  </div>
  <div style="clear: both;">
  </div>
</div>
<div id="footer">
  <p>', l('Powered by <a href="http://www.snowcms.com/" target="_blank" title="Visit SnowCMS.com">SnowCMS</a> v%s.', $settings->get('version', 'string')), ' | ', l('Page created in %s seconds with %u queries.', round(microtime(true) - $start_time, 3), $db->num_queries), '</p>
</div>
</body>
</html>';
  }
}
?>