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

class Implemented_Theme extends Theme
{
  protected function init()
  {
    $this->add_link(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => $this->url. '/style/style.css'));
  }

  public function header()
  {
    global $api, $settings;

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>';

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
  <div id="wrapper">
    <div id="header">
      <h2>', $api->apply_filters('theme_site_name', $settings->get('site_name')), '</h2>
      <p>', $api->apply_filters('theme_sub_title', 'The <span style="text-decoration: line-through;">best</span> worst theme on the Internets'), '</p>
      <div id="menu-outer">
        <div id="menu-inner">
          <ul id="menu">
            <li>Home</li>
            <li>About</li>
          </ul>
        </div>
      </div>
    </div>
    <div id="content">', $api->apply_filters('theme_pre_content', '');
  }

  public function footer()
  {
    global $api, $db, $settings, $start_time;

    echo $api->apply_filters('theme_post_content', ''), '
    </div>
    <div id="sidebar">
      Right Column
    </div>
    <div id="footer">
      <p>', $api->apply_filters('theme_footer', l('Page created in %s seconds with %u queries.', round(microtime(true) - $start_time, 3), $db->num_queries). ' | '. l('Theme by %s.', ' <a href="http://www.sorenstudios.com/" target="_blank" title="Soren Studios">Soren Studios</a>'). ($settings->get('show_version', 'bool') ? ' | '. l('Powered by <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a> v%s', $settings->get('version')) : '')), '</p>
    </div>
    <div style="clear: both;">
    </div>
  </div>
</body>
</html>';
  }
}
?>