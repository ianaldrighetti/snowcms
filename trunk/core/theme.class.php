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

/*
  Class: Theme

  This is an abstract class that a theme extends, and implements header
  and footer, but also incorporates the information that is gathered
  and stored within the arrays contained inside this class definition.
*/
abstract class Theme
{
  # Variable: main_title
  # Contains the sites main title, this title is not independent per page
  # but dependent on the whole sites name itself.
  protected $main_title;

  # Variable: title
  # The title specific to the current page.
  protected $title;

  # Variable: url
  # The URL of the current themes base directory.
  protected $url;

  # Variable: links
  # An array containing all the <link> tags to be added inside the <head> of
  # the document. Each array inside links can contain charset, href, hreflang,
  # media, rel, rev, target, type, class, dir, id, lang, style, title and xml:lang.
  protected $links;

  # Variable: js_vars
  # An associative array (variable => value) containing JavaScript variables
  # to be defined before including JavaScript files, if any.
  protected $js_vars;

  # Variable: js_files
  # Contains JavaScript files to include in the documents <head> tag. Each array
  # inside can contain type, charset, defer and src.
  protected $js_files;

  # Variable: meta
  # An array containing meta data to include in the documents <head> tag. Each
  # array can contain content, http-equiv, name, scheme, dir, lang and xml:lang.
  protected $meta;

  /*
    Constructor: __construct

    Parameters:
      string $main_title - The value to set the main_title attribute to.
      string $url - The URL to the base directory of the current theme.
  */
  public function __construct($main_title = null, $url = null)
  {
    $this->title = null;
    $this->url = null;
    $this->links = array();
    $this->js_vars = array();
    $this->js_files = array();
    $this->meta = array();

    if(!empty($main_title))
      $this->set_main_title($main_title);

    if(!empty($url))
      $this->set_url($url);

    # Our first meta tag!
    $this->add_meta(array('http-equiv' => 'Content-Type', 'content' => 'text/html; charset=utf-8'));

    # Do any possible specific theme stuff :)
    $this->init();
  }

  /*
    Method: set_main_title

    Parameters:
      string $main_title - What to set the sites main title to.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function set_main_title($main_title)
  {
    if(!empty($main_title) && is_string($main_title))
    {
      $this->main_title = $main_title;
      return false;
    }
    else
      return false;
  }

  /*
    Method: set_title

    Parameters:
      string $title - What to set the pages title to.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function set_title($title)
  {
    if(!empty($title) && is_string($title))
    {
      $this->title = $title;
      return true;
    }
    else
      return false;
  }

  /*
    Method: set_url

    Parameters:
      string $url - The URL of the current themes base directory (No trailing /!)

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function set_url($url)
  {
    if(!empty($url) && is_string($url))
    {
      $this->url = $url;
      return true;
    }
    else
      return false;
  }

  /*
    Method: main_title

    Parameters:
      none

    Returns:
      string - Returns the current main title of the theme.
  */
  public function main_title()
  {
    return $this->main_title;
  }

  /*
    Method: title

    Parameters:
      none

    Returns:
      string - Returns the current title of the page.
  */
  public function title()
  {
    return $this->title;
  }

  /*
    Method: url

    Parameters:
      none

    Returns:
      string - Returns the URL of the theme.
  */
  public function url()
  {
    return $this->url;
  }

  /*
    Method: add_link

    Parameters:
      array $link - An associative array (attribute => value) containing all
                    the attributes of an link tag.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function add_link($link)
  {
    # Is href not set, link not an array, that link already exist?!?
    if(count($link) == 0 || empty($link['href']) || !is_array($link) || $this->link_exists($link['href']) || count($link) > 15)
      return false;

    # Make sure you don't have more attributes than allowed...
    $allowed_attributes = array('charset', 'href', 'hreflang', 'media', 'rel', 'rev', 'target', 'type', 'class', 'dir', 'id', 'lang', 'style', 'title', 'xml:lang');
    foreach($link as $attr => $value)
      if(!in_array($attr, $allowed_attributes))
        return false;

    $this->links[] = $link;
    return true;
  }

  /*
    Method: link_exists

    Parameters:
      string $link_href - The href of the link to check.

    Returns:
      bool - Returns TRUE if the link containing that href already exists,
             FALSE if not.
  */
  public function link_exists($link_href)
  {
    if(empty($link_href) || !is_string($link_href) || count($this->links) == 0)
      return false;

    foreach($this->links as $key => $link)
      if($link['href'] == $link_href)
        return true;

    return false;
  }

  /*
    Method: remove_link

    Parameters:
      string $link_href - The href of the link to remove.

    Returns:
      bool - Returns TRUE if the link was removed, FALSE if not.
  */
  public function remove_link($link_href)
  {
    if(empty($link_href) || !is_string($link_href) || count($this->links) == 0 || !$this->link_exists($link_href))
      return false;

    foreach($this->links as $key => $link)
      if($link['href'] == $link_href)
      {
        unset($this->links[$key]);
        return true;
      }

    # This should never happen o.O
    return false;
  }

  /*
    Method: add_js_var

    Parameters:
      string $variable - The name of the JavaScript variable to define.
      mixed $value - The value of the variable. Can either be a string, int or float.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function add_js_var($variable, $value)
  {
    if(empty($variable) || $this->js_var_exists($variable))
      return false;

    $this->js_vars[$variable] = json_encode($value);
    return true;
  }

  /*
    Method: js_var_exists

    Parameters:
      string $variable - The name of the JavaScript variable to check.

    Returns:
      bool - Returns TRUE if the variable exists, FALSE if not.
  */
  public function js_var_exists($variable)
  {
    return !empty($variable) && is_string($variable) && isset($this->js_vars[$variable]);
  }

  /*
    Method: remove_js_var

    Parameters:
      string $variable - The name of the JavaScript variable to remove.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function remove_js_var($variable)
  {
    if(empty($variable) || !is_string($variable) || !$this->js_var_exists($variable))
      return false;

    unset($this->js_vars[$variable]);
    return true;
  }

  /*
    Method: add_js_file

    Parameters:
      array $script - Contains the attributes of the script tag.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function add_js_file($script)
  {
    if(empty($script) || !is_array($script) || empty($script['src']) || $this->js_file_exists($script['src']))
      return false;

    # No type? Add it ourselves :P
    if(empty($script['type']))
      $script['type'] = 'text/javascript';

    if(empty($script['language']))
      $script['language'] = 'JavaScript';

    # Let's make sure you aren't throwing some random crap in...
    $allowed_attributes = array('type', 'charset', 'defer', 'src', 'language');
    foreach($script as $attr => $value)
      if(!in_array($attr, $allowed_attributes))
        return false;

    $this->js_files[] = $script;
    return true;
  }

  /*
    Method: js_file_exists

    Parameters:
      string $script_src - The script source (The URL of the JS file).

    Returns:
      bool - Returns TRUE if the js file exists, FALSE if not.
  */
  public function js_file_exists($script_src)
  {
    if(empty($script_src) || !is_string($script_src) || count($this->js_files) == 0)
      return false;

    foreach($this->js_files as $js)
      if($js['src'] == $script_src)
        return true;

    return false;
  }

  /*
    Method: remove_js_file

    Parameters:
      string $script_src - The script source (The URL of the JS file).

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function remove_js_file($script_src)
  {
    if(empty($script_src) || !is_string($script_src) || count($this->js_files) == 0 || !$this->js_file_exists($script_src))
      return false;

    foreach($this->js_files as $key => $js)
      if($js['src'] == $script_src)
      {
        unset($this->js_files[$key]);
        return true;
      }

    # This shouldn't happen o.O
    return false;
  }

  /*
    Method: add_meta

    Parameters:
      array $meta - An array containing the meta tags information.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function add_meta($meta)
  {
    if(empty($meta) || !is_array($meta) || count($meta) == 0 || ((isset($meta['name']) || isset($meta['http-equiv'])) && $this->meta_exists(isset($meta['name']) ? $meta['name'] : $meta['http-equiv'])))
      return false;
    elseif(!empty($meta['name']) && !empty($meta['http-equiv']))
      return false;


    $allowed_attributes = array('content', 'http-equiv', 'name', 'scheme', 'dir', 'lang', 'xml:lang');
    foreach($meta as $attr => $value)
      if(!in_array($attr, $allowed_attributes))
        return false;

    $this->meta[] = $meta;
    return true;
  }

  /*
    Method: meta_exists

    Parameters:
      string $name - The name of the tag (or http-equiv).

    Returns:
      bool - Returns TRUE if the meta tag exists, FALSE if not.
  */
  public function meta_exists($name)
  {
    if(empty($name) || !is_string($name) || count($this->meta) == 0)
      return false;

    foreach($this->meta as $meta)
      if((isset($meta['name']) && $meta['name'] == $name) || (isset($meta['http-equiv']) && $meta['http-equiv'] == $name))
        return true;

    return false;
  }

  /*
    Method: remove_meta

    Parameters:
      string $name - The name of the tag (or http-equiv).

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function remove_meta($name)
  {
    if(!$this->meta_exists($name))
      return false;

    foreach($this->meta as $key => $meta)
      if((isset($meta['name']) && $meta['name'] == $name) || (isset($meta['http-equiv']) && $meta['http-equiv'] == $name))
      {
        unset($this->meta[$key]);
        return true;
      }

    # Should never happen :/
    return false;
  }

  /*
    Method: init

    This is a protected method, which can be overloaded by the Implemented_Theme
    class, which can be used to add any required CSS, JavaScript, or what-have-you
    without hardcoding it :)

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.
  */
  protected function init()
  {

  }

  /*
    Method: header

    This is a method which the Implemented_Theme class must implement!
    The method outputs the themes header HTML.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.
  */
  abstract public function header();

  /*
    Method: footer

    This is a method which the Implemented_Theme class must implement!
    The method outputs the themes footer HTML.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.
  */
  abstract public function footer();

  /*
    Method: generate_tag

    Parameters:
      string $name - The name of the HTML tag, like meta, link, etc.
      array $attributes - Attributes of the HTML tag.
      bool $self_closing - Whether or not the tag is self closing.

    Returns:
      string - Returns the formed tag.
  */
  protected function generate_tag($name, $attributes, $self_closing = true)
  {
    if(empty($name))
      return false;

    $tag = '<'. $name;

    if(is_array($attributes) && count($attributes) > 0)
    {
      $attr = array();

      foreach($attributes as $name => $value)
      {
        if(!empty($value))
          $attr[] = $name. '="'. addcslashes($value, '"'). '"';
      }

      $tag .= ' '. implode(' ', $attr);
    }

      return ($tag. (!empty($self_closing) ? ' />' : '>'));
  }
}

/*
  Function: init_theme

  Creates the $theme object.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function init_theme()
{
  global $api, $base_url, $settings, $theme, $theme_dir, $theme_url;

  require_once($theme_dir. '/'. $settings->get('theme'). '/implemented_theme.class.php');
  $theme = $api->load_class('Implemented_Theme', array($settings->get('site_name'), $theme_url. '/'. $settings->get('theme')));

  $theme->add_js_file(array('src' => $theme_url. '/default/js/snowobj.js'));
  $theme->add_js_var('base_url', $base_url);
  $theme->add_js_var('theme_url', $theme_url);

  # You can hook into here to add all your theme stuffs (<link>'s, js vars, js files, etc).
  $api->run_hooks('post_init_theme');
}
?>