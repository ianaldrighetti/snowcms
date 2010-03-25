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
    global $base_url, $theme_url;

    $this->add_js_file(array('src' => $theme_url. '/default/js/snowobj.js'));
    $this->add_js_var('base_url', $base_url);
  }

  public function header()
  {
    global $api, $base_url, $cookie_name, $member, $settings;

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <style type="text/css">
    *
    {
      margin: 0px;
    }

    img
    {
      border: none;
    }

    body
    {
      background: #FFFFFF;
      font-family: Tahoma, Arial, sans-serif;
      font-size: 90%;
    }

    input[type="text"], input[type="password"], textarea
    {
      font-family: Tahoma, Arial, sans-serif;
      border: 1px solid #AAAAAA;
      padding: 2px;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
    }

    #header
    {
      width: 100%;
      height: 70px;
      background: #3465A7;
    }

    #header #container
    {
      width: 800px;
      margin: 0px auto;
    }

    #header #container h1
    {
      float: left;
      padding-top: 20px;
      color: #FFFFFF;
      font-weight: normal;
      font-size: 150%;
    }

    #header #container #member_info
    {
      float: right;
      width: 200px;
      margin-top: 10px;
      padding: 8px;
      color: #FFFFFF;
      background: #729FCF;
    }

    #header #container #member_info a
    {
      color: #FFFFFF;
      text-decoration: none;
    }

    #header #container #member_info a:hover
    {
      text-decoration: underline;
    }

    #header #container #member_info .links
    {
      font-size: 90%;
    }

    #content
    {
      width: 800px;
      margin: 20px auto 10px auto;
      padding-bottom: 10px;
      border-bottom: 1px solid #DDDDDD;
      font-size: 90%;
    }

    #content h1
    {
      font-size: 125%;
      color: #3465A7;
      margin-top: 15px;
    }

    #content h3
    {
      font-size: 110%;
      color: #AAAAAA;
      margin-top: 15px;
    }

    #content ul
    {
      margin: 5px auto;
      padding-left: 20px;
    }

    #content #sidebar
    {
      float: left;
      width: 180px;
    }

    #content #sidebar h3
    {
      margin-top: 10px;
      font-size: 105%;
      color: #A9A9A9;
      border-bottom: 1px solid #DDDDDD;
      padding-bottom: 3px;
    }

    #content #sidebar h3 a
    {
      color: #A3A3A3;
      text-decoration: none;
    }

    #content #sidebar .news_subject
    {
      font-weight: bold;
      font-size: 90%;
    }

    #content #sidebar .news_content
    {
      font-size: 90%;
      margin-bottom: 5px;
    }

    #content #sidebar .notification
    {
      font-size: 90%;
    }

    #content #main
    {
      float: right;
      width: 590px;
    }

    #content #main .icons a
    {
      display: block;
      width: 80px;
      height: 80px;
      padding: 5px;
      text-align: center;
      font-size: 70%;
      text-decoration: none;
      border: 1px solid #FFFFFF;
      color: #000000;
    }

    #content #main .icons a:hover
    {
      -moz-border-radius: 5px;
      -webkit-border-radius: 5px;
      border-radius: 5px;
      border: 1px solid #C9C9C9;
      background: #EDEDED;
    }

    #footer
    {
      width: 800px;
      margin: 0px auto;
      color: #888A85;
      font-size: 75%;
    }

    #footer a
    {
      color: #3465A7;
      text-decoration: none;
    }

    #footer a:hover
    {
      text-decoration: underline;
    }

    .break
    {
      clear: both;
    }

    .form fieldset
    {
      border: none;
    }

    .form .td_left
    {
      width: 75%;
      text-align: left !important;
      padding: 5px 0px;
    }

    .form .td_right
    {
      width: 25%;
      text-align: center !important;
      padding: 5px 0px;
    }

    .form .label
    {
      font-weight: bold;
      font-size: 110%;
    }

    .form .subtext
    {
      font-size: 85%;
    }

    .form .buttons
    {
      text-align: center !important;
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

  // ]]></script>
</head>
<body>
<div id="header">
  <div id="container">
    <h1>', $settings->get('site_name', 'string'), ': ', l('Control Panel'), '</h1>

    <div id="member_info">
      <p>', l('Hello, <a href="%s" title="View your profile">%s</a>.', $base_url. '/index.php?action=profile', $member->display_name()), '</p>
      <p class="links">', l('<a href="%s" title="Go to the Control Panel Home">Control Panel</a> | <a href="%s" title="Log out of your account">Log out</a>', $base_url. '/index.php?action=admin', $base_url. '/index.php?action=logout&amp;sc='. $member->session_id()), '</p>
    </div>
    <div class="break">
    </div>
  </div>
</div>
<div id="content">', $api->apply_filters('admin_theme_pre_content', '');
  }

  public function footer()
  {
    global $api, $db, $settings, $start_time;

    echo $api->apply_filters('admin_theme_post_content', ''), '
</div>
<div id="footer">
  <p>', l('Powered by <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a> v%s.', $settings->get('version', 'string')), '</p>
  <p>', l('Page created in %s seconds with %u queries.', round(microtime(true) - $start_time, 3), $db->num_queries), '</p>
</div>
</body>
</html>';
  }
}
?>