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
  Class: Atom

  In order to allow plugins and others to quickly create Atom feeds without
  too much hassle, the Atom class allows you to pass data to the class and
  then generate an Atom feed. Also see <RSS>.

  The RSS class is made according to the information found here:
  <http://www.atomenabled.org/developers/syndication/>
*/
class Atom
{
  # Variable: id
  # Identifies the feed using a unique ID, such as a URL to your site.
  private $id;

  # Variable: title
  # The title of the Atom feed.
  private $title;

  # Variable: updated
  # The last time the feed was modified significantly.
  private $updated;

  # Variable: authors
  # An array containing sub arrays with an author name, email address
  # and URI (URL to their home page, like their profile).
  private $authors;

  # Variable: links
  # An array containing links, such as a link to the Atom feed itself.
  private $links;

  # Variable: categories
  # An array containing categories of the Atom feed.
  private $categories;

  # Variable: contributors
  # An array containing contributors to the feed.
  private $contributors;

  # Variable: icon
  # An small image which provides "iconic visual identification" for the feed.
  private $icon;

  # Variable: logo
  # Same as the above, but only larger.
  private $logo;

  # Variable: rights
  # Contains information about the rights of the feed, like copyrights.
  private $rights;

  # Variable: subtitle
  # A subtitle for the Atom feed.
  private $subtitle;

  # Variable: entries
  # An array containing entries for the Atom feed.
  private $entries;

  /*
    Constructor: __construct
  */
  public function __construct()
  {
    # This will set everything to null and empty.
    $this->clear();
  }

  /*
    Method: clear

    Clears all the feed information.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.
  */
  public function clear()
  {
    $this->id = null;
    $this->title = null;
    $this->updated = null;
    $this->authors = array();
    $this->links = array();
    $this->categories = array();
    $this->contributors = array();
    $this->icon = null;
    $this->logo = null;
    $this->rights = null;
    $this->subtitle = null;
    $this->entries = array();
  }

  /*
    Method: set_id

    Sets the id (universally unique identifier, such as a URL to the home page)
    of the feed.

    Parameters:
      string $id - The id to set for the feed.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_id($id)
  {
    $this->id = $id;
  }

  /*
    Method: id

    Returns the currently set id for the feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the currently set id of the feed.
  */
  public function id()
  {
    return $this->id;
  }

  /*
    Method: set_title

    Sets the title of the feed.

    Parameters:
      string $title - The title to set for the feed.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_title($title)
  {
    $this->title = $title;
  }

  /*
    Method: title

    Returns the currently set title for the feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the currently set title of the feed.
  */
  public function title()
  {
    return $this->title;
  }

  /*
    Method: set_updated

    Sets the timestamp for when the last time the feed was last
    significantly updated.

    Parameters:
      int $timestamp - The timestamp of when the feed was last
                       significantly updated.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_updated($timestamp)
  {
    if((string)$timestamp == (string)(int)$timestamp)
    {
      $this->updated = (int)$timestamp;
      return true;
    }
    else
    {
      return false;
    }
  }

  /*
    Method: updated

    Returns the currently set timestamp of when the feed was last
    significantly updated.

    Parameters:
      none

    Returns:
      int - Returns an int containing a timestamp which is the last
            time the feed was last significantly updated.
  */
  public function updated()
  {
    return $this->updated;
  }

  /*
    Method: add_author

    Adds an author to the feed.

    Parameters:
      string $name - The name of the author.
      string $email - The email address of the author.
      string $uri - The URI of the authors home page, such as a link to
                    their profile.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      If entries do not have an author, then you must have an author for the
      entire feed itself.
  */
  public function add_author($name, $email = null, $uri = null)
  {
    # You must have a name...
    if(empty($name))
    {
      return false;
    }
    else
    {
      $this->authors[] = array(
                           'name' => $name,
                           'email' => $email,
                           'uri' => $uri,
                         );

      return true;
    }
  }

  /*
    Method: authors

    Returns an array containing all the currently added authors.

    Parameters:
      none

    Returns:
      array - Returns an array containing all the currently added authors.
  */
  public function authors()
  {
    return $this->authors;
  }

  /*
    Method: remove_author

    Removes the author at the specified index.

    Parameters:
      int $index - The index of the author to remove.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function remove_author($index)
  {
    if((string)$index != (string)(int)$index || $index < 0 || $index >= count($this->authors))
    {
      return false;
    }

    if($index == 0)
    {
      # Pop it off the top.
      array_shift($this->authors);
    }
    elseif($index == (count($this->authors) - 1))
    {
      # Take it off the end.
      unset($this->authors[$index]);
    }
    else
    {
      # Somewhere inbetween it appears.
      $authors = array();
      $length = count($this->authors);

      for($i = 0; $i < $length; $i++)
      {
        # Is this what we are deleting?
        if($index == $i)
        {
          # Yup, so skip it.
          continue;
        }

        # Keeping it!
        $authors[] = $this->authors[$i];
      }

      $this->authors = $authors;
    }

    return true;
  }

  /*
    Method: add_link

    Adds a link to the feed.

    Parameters:
      string $href - The href attribute of the link (required).
      string $rel - The relationship type of the link (defaults to alternate).
      string $type - The media type of the resource.
      string $title - Human readable information about the link.
      string $hreflang - The language of the referenced resource.
      string $length - The length of the resource, in bytes.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      Here are some valid options for the $rel parameter:
        alternate - An alternate representation of the feed, like a URL to the
                    place of where the feed entries come from or another feed (like RSS).
        enclosure - A related resource which is large in size, like an audio or
                    video recording.
        related - A document related to the entry or feed.
        self - A URL to the feed itself (recommended by the specification).
        via - The source of information used in the entry.
  */
  public function add_link($href, $rel = 'alternate', $type = null, $title = null, $hreflang = null, $length = null)
  {

  }
}
?>