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

  }

  public function header()
  {
    global $api, $base_url, $settings;

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
    body
    {
      margin: 0px !important;
      font-family: Verdana, sans-serif, Arial;
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
  </style>
  <script type="text/javascript">
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
    }
  </script>';

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
</head>
<body>
<div id="header">
  <h1>', $settings->get('site_name', 'string'), ' ', l('<a href="%s" class="goto_site" title="Go to site">&laquo; Go to site &raquo;</a>', $base_url), '</h1>
  <h3>', l('Administration Center'), '</h3>
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
      <p class="header first last"><a href="javascript:void(0);" onclick="open_list(\'list_1_1\');">', l('Control Center'), '</a></p>
      <ul id="list_1_1" style="border-bottom: 0px;">
        <li><a href="', $base_url, '/index.php?action=admin">', l('Home'), '</a></li>
        <li><a href="', $base_url, '/index.php?action=admin&amp;sa=update" title="', l('Check for any updates for your system'), '">', l('Update'), '</a></li>
      </ul>
    </div>
    <div class="category">
      <p class="header first"><a href="javascript:void(0);" onclick="open_list(\'list_2_1\');">Header #1</p>
      <ul id="list_2_1" style="display: none;">
        <li><a href="#">Link #1</a></li>
        <li><a href="#">Link #2</a></li>
        <li><a href="#">Link #3</a></li>
      </ul>
      <p class="header"><a href="javascript:void(0);" onclick="open_list(\'list_2_2\');">Header #2</p>
      <ul id="list_2_2" style="display: none;">
        <li><a href="#">Link #1</a></li>
        <li><a href="#">Link #2</a></li>
        <li><a href="#">Link #3</a></li>
      </ul>
      <p class="header last"><a href="javascript:void(0);" onclick="open_list(\'list_2_3\', true);">Header #3</p>
      <ul id="list_2_3" style="display: none; border-bottom: 0px;">
        <li><a href="#">Link #1</a></li>
        <li><a href="#">Link #2</a></li>
        <li><a href="#">Link #3</a></li>
      </ul>
    </div>
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