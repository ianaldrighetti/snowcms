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
  Class: RSS

  In order to allow plugins and others to quickly create RSS feeds without
  too much hassle, the RSS class allows you to pass data to the class and
  then generate an RSS feed. Also see <Atom>.

  The RSS class is made according to the information found here:
  <http://cyber.law.harvard.edu/rss/rss.html>
*/
class RSS
{
  # Variable: title
  private $title;

  # Variable: link
  private $link;

  # Variable: description
  private $description;

  # Variable: language
  private $language;

  # Variable: pubDate
  private $pubDate;

  # Variable: lastBuildDate
  private $lastBuildDate;

  # Variable: docs
  private $docs;

  # Variable: managingEditor
  private $managingEditor;

  # Variable: items
  private $items;

  /*
    Constructor: __construct

    Initializes all of the RSS classes attributes.
  */
  public function __construct()
  {
    $this->title = null;
    $this->link = null;
    $this->description = null;
    $this->language = null;
    $this->pubDate = null;
    $this->lastBuildDate = null;
    $this->docs = null;
    $this->managingEditor = null;
    $this->items = array();
  }

  /*
    Method: set_title

    Sets the title of the RSS feed.

    Parameters:
      string $title - The title of the RSS feed.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_title($title)
  {
    $this->title = $title;
  }
}
?>