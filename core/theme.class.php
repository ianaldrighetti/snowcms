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
  */
  public function __construct($main_title = null)
  {
    if(!empty($main_title))
      $this->set_main_title($main_title);

    $this->title = null;
    $this->links = array();
    $this->js_vars = array();
    $this->js_files = array();
    $this->meta = array();
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
    $allowed_attributes = array('charset', ' href', ' hreflang', ' media', ' rel', ' rev', ' target', ' type', ' class', ' dir', ' id', ' lang', ' style', ' title', ' xml:lang')
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
    if(empty($link_href) || !is_string($link_href) || count($this->links) == 0)
      return false;

    foreach($this->links as $key => $link)
      if($link['href'] == $link_href)
      {
        unset($this->links[$key]);
        return true;
      }

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
    if(empty($variable) || !is_string($variable) || (!is_string($value) && !is_int($value) && !is_float($value)) || $this->js_var_exists($variable))
      return false;

    $this->js_vars[$variable] = $value;
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
}
?>