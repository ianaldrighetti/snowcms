<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

if(!defined('INSNOW'))
{
  die('Nice try...');
}

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
  // Variable: title
  private $title;

  // Variable: link
  private $link;

  // Variable: description
  private $description;

  // Variable: language
  private $language;

  // Variable: pubDate
  private $pubDate;

  // Variable: lastBuildDate
  private $lastBuildDate;

  // Variable: docs
  private $docs;

  // Variable: managingEditor
  private $managingEditor;

  // Variable: copyright
  private $copyright;

  // Variable: category
  private $category;

  // Variable: ttl
  private $ttl;

  // Variable: items
  private $items;

  /*
    Constructor: __construct

    Initializes all of the RSS classes attributes.
  */
  public function __construct()
  {
    // clear will set everything to empty and what not.
    $this->clear();
  }

  /*
    Method: clear

    Clears all the current RSS information.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.
  */
  public function clear()
  {
    // Just empty everything, EVERYTHING!
    $this->title = null;
    $this->link = null;
    $this->description = null;
    $this->language = null;
    $this->pubDate = null;
    $this->lastBuildDate = null;
    $this->docs = null;
    $this->managingEditor = null;
    $this->copyright = null;
    $this->category = array(
                        'value' => null,
                        'domain' => null,
                      );
    $this->ttl = null;
    $this->items = array();
  }

  /*
    Method: set_title

    Sets the title of the RSS feed.

    Parameters:
      string $title - The title of the RSS feed.

    Returns:
      void - Nothing is returned by this method.

    Note:
      This is a required attribute of the RSS feed, if it is not
      supplied, the feed cannot be displayed.
  */
  public function set_title($title)
  {
    $this->title = $title;
  }

  /*
    Method: set_link

    The URL to the website of where the RSS feed comes from,
    like if the RSS feed is of the forum, the link should be
    to the forum.

    Parameters:
      string $link - The URL of the page.

    Returns:
      void - Nothing is returned by this method.

    Note:
      This is a required attribute of the RSS feed, if it is not
      supplied, the feed cannot be displayed.
  */
  public function set_link($link)
  {
    $this->link = $link;
  }

  /*
    Method: set_description

    The description of the RSS feed.

    Parameters:
      string $description - The description of the RSS feed.

    Returns:
      void - Nothing is returned by this method.

    Note:
      This is a required attribute of the RSS feed, if it is not
      supplied, the feed cannot be displayed.
  */
  public function set_description($description)
  {
    $this->description = $description;
  }

  /*
    Method: set_language

    The language of the RSS feed, such as en-US or just en.

    Parameters:
      string $language - The language code, such as en-GB or en-US.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_language($language)
  {
    $this->language = $language;
  }

  /*
    Method: set_pubdate

    Sets the publication date of the RSS feed.

    Parameters:
      int $timestamp - The timestamp of the publication date of the
                       RSS feed being generated.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      The publication date is the date (well, timestamp) at which the latest
      item of the RSS feed was added. For example, if a newspaper were to have
      an RSS feed, which are usually published every 24 hours, the publication
      date would change to the date of the latest release of the newspaper.
  */
  public function set_pubdate($timestamp)
  {
    // Not an integer? Sorry.
    if((string)$timestamp == (string)(int)$timestamp)
    {
      $this->pubDate = (int)$timestamp;
      return true;
    }
    else
    {
      return false;
    }
  }

  /*
    Method: set_lastbuilddate

    The last build date of the RSS feed.

    Parameters:
      int $timestamp - The timestamp at which the RSS feed was last built.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_lastbuilddate($timestamp)
  {
    // Not an integer? Sorry.
    if((string)$timestamp == (string)(int)$timestamp)
    {
      $this->lastBuildDate = (int)$timestamp;
      return true;
    }
    else
    {
      return false;
    }
  }

  /*
    Method: set_docs

    The URL to the documents of the RSS feed being displayed.

    Parameters:
      string $link - The URL to the documents of the RSS feed.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_docs($link)
  {
    $this->docs = $link;
  }

  /*
    Method: set_managingeditor

    Sets the email for the person who is managing the content of the
    RSS feed being displayed.

    Parameters:
      string $email - The email of the content manager.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_managingeditor($email)
  {
    $this->managingEditor = $email;
  }

  /*
    Method: set_category

    Sets the category of the entire RSS feed, the attribute of the
    category tag, domain, can be specified as well.

    Parameters:
      string $category - The categor(y|ies) separated by forward slashes.
      string $domain - The location of this category (optional).

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_category($category, $domain = null)
  {
    $this->category['value'] = $category;
    $this->category['domain'] = $domain;
  }

  /*
    Method: set_ttl

    Sets the <ttl> tag of the RSS feed, in minutes, whcih specifies how long
    the feed may be cached until it will be renewed from the source.

    Parameters:
      int $ttl - The ttl, in minutes.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_ttl($ttl)
  {
    if((string)$ttl == (string)(int)$ttl)
    {
      $this->ttl = (int)$ttl;
    }
    else
    {
      return false;
    }
  }

  /*
    Method: title

    Returns the currently set title of the RSS feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the RSS feeds title.
  */
  public function title()
  {
    return $this->title;
  }

  /*
    Method: link

    Returns the currently set link (URL of where the RSS content is
    coming from) of the RSS feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the RSS feeds link.
  */
  public function link()
  {
    return $this->link;
  }

  /*
    Method: description

    Returns the currently set description of the RSS feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the RSS feeds description.
  */
  public function description()
  {
    return $this->description;
  }

  /*
    Method: language

    Returns the currently set language code of the RSS feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the RSS feeds language code.
  */
  public function language()
  {
    return $this->language;
  }

  /*
    Method: pubdate

    Returns the currently set published date of the RSS feed.

    Parameters:
      bool $parse - Whether or not to parse the currently set timestamp
                    into a string with the date formatted according to
                    RFC 2822, defaults to false.

    Returns:
      mixed - Returns a string containing the parsed date, if parse is set to
              true, if false, then an integer will be returned. However, if no
              published date was set, null will be returned.
  */
  public function pubdate($parse = false)
  {
    return is_int($this->pubDate) ? (!empty($parse) ? date('r', $this->pubDate) : $this->pubDate) : null;
  }

  /*
    Method: lastbuilddate

    Returns the currently set last build date date of the RSS feed.

    Parameters:
      bool $parse - Whether or not to parse the currently set timestamp
                    into a string with the date formatted according to
                    RFC 2822, defaults to false.

    Returns:
      mixed - Returns a string containing the parsed date, if parse is set to
              true, if false, then an integer will be returned. However, if no
              last build date was set, null will be returned.
  */
  public function lastbuilddate($parse = false)
  {
    return is_int($this->lastBuildDate) ? date('r', $this->lastBuildDate) : null;
  }

  /*
    Method: docs

    Returns the currently set docs URL (the page which contains the documents
    of how the feed is formatted) of the RSS feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the RSS feeds docs URL.
  */
  public function docs()
  {
    return $this->docs;
  }

  /*
    Method: managingeditor

    Returns the currently set managing editor email address of the RSS feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the RSS feeds managing editor
               email address.
  */
  public function managingeditor()
  {
    return $this->managingEditor;
  }

  /*
    Method: copyright

    Returns the currently set copyright of the RSS feed.

    Parameters:
      none

    Returns:
      string - Returns a string containing the RSS feeds copyright.
  */
  public function copyright()
  {
    return $this->copyright;
  }

  /*
    Method: category

    Returns the current set category information of the RSS feed, both
    the category name(s) and the domain, if set.

    Parameters:
      none

    Returns:
      array - Returns an array containing the category name(s) (the value index)
              and domain (domain index), if set.
  */
  public function category()
  {
    return $this->category;
  }

  /*
    Method: ttl

    Returns the currently set ttl (Time To Live) of the RSS feed, in minutes.

    Parameters:
      none

    Returns:
      int - Returns an integer containing the RSS feeds ttl.
  */
  public function ttl()
  {
    return $this->ttl;
  }

  /*
    Method: add_item

    Adds an item to the RSS feed either at the top, or the bottom.

    Parameters:
      array $item - An array containing the item to add to the feed.
      bool $prepend - Whether or not to prepend (add to front) item to
                      the array, or not. Defaults to false.

    Returns:
      bool - Returns true if the item was added successfully, false if the
             supplied item didn't have all the required elements as specified
             in the notes.

    Note:
      The following options are supported for item:
        string title - The title of the individual item.

        string link - The URL at which the item can be found.

        string description - The description of what the item contains.

        string author - The authors email address of the individual item, which
                        can be only the email address or the email address followed
                        by their name in paranthesis.

        array category - An array containing the category name (value index)
                         of where item is part of and the URL of where the
                         the category can be found (domain index).

        string comments - The URL at which comments can be made on the item.

        array enclosure - An array containing the information about the media
                          object attached to the element. You are required to
                          specify the URL at which the media can be found (url
                          index), the length which says how big the media is in
                          bytes (length index) and the type of the media, a MIME
                          type (type index).

        string guid - A string which uniquely identifies item, such as the URL
                      of where item can be found.

        int pubdate - A timestamp containing when the item was published.

        array source - The source channel (another RSS feed) of where item came
                       from. You are required to supply a URL (url index), which
                       is the original URL of the items RSS feed, but a name
                       (value index) can be specified as well.

      You are required to supply at least a title or a description.
  */
  public function add_item($item, $prepend = false)
  {
    // No title or description?
    if(empty($item['title']) && empty($item['description']))
    {
      // Sorry, but gotta have one.
      return false;
    }

    // Only a couple of things need to be checked...
    if(isset($item['category']) && empty($item['category']['value']))
    {
      // Gotta have a value for the category, if you specified it!
      return false;
    }

    if(isset($item['enclosure']) && (empty($item['enclosure']['url']) || empty($item['enclosure']['length']) || empty($item['enclosure']['type'])))
    {
      // You must have all the attributes for the enclosure.
      return false;
    }

    if(isset($item['source']) && (empty($item['source']['url'])))
    {
      // A URL for the source is a must!
      return false;
    }

    // So, do you want to prepend the item?
    if(!empty($prepend))
    {
      // Simple enough, really.
      array_unshift($this->items, $item);
    }
    else
    {
      // Nope, append. Simple too.
      $this->items[] = $item;
    }

    // Done!
    return false;
  }

  /*
    Method: remove_item

    Removes the specified item from the RSS feed.

    Parameters:
      int $index - The index to remove from the feed.

    Returns:
      bool - Returns true if the item was deleted, false
             if the item does not exist.
  */
  public function remove_item($index)
  {
    // Make sure the item exists... ;-)
    if(!isset($this->items[$index]))
    {
      // Nope, it does not.
      return false;
    }

    // Removing the first item?
    if($index == 0)
    {
      array_shift($this->items);
    }
    elseif($index == (count($this->items) - 1))
    {
      // It can be just deleted.
      unset($this->items[$index]);
    }
    else
    {
      // We need to completely rebuild it, otherwise indexing would
      // become completely screwed up.
      $items = array();

      $length = count($this->items);
      for($i = 0; $i < $length; $i++)
      {
        // Is this the one to delete?
        if($i == $index)
        {
          // Yup, so skip it.
          continue;
        }

        // It's good, add it.
        $items[] = $this->items[$i];
      }

      // Copy it.
      $this->items = $items;
    }

    // Done!
    return true;
  }

  /*
    Method: return_item

    Returns the specified item in the RSS feed.

    Parameters:
      int $index - The index of the RSS feed item, if left to null,
                   all items will be returned.

    Returns:
      array - Returns an array containing the individual item, if index is
              null, all items will be returned, or if the specified item
              does not exist, false is returned.
  */
  public function return_item($index = null)
  {
    return $index === null ? $this->items : (isset($this->items[$index]) ? $this->items[$index] : false);
  }

  /*
    Method: num_items

    Returns the current number of items in the RSS feed.

    Parameters:
      none

    Returns:
      int - Returns the number of items in the RSS feed.
  */
  public function num_items()
  {
    return count($this->items);
  }

  /*
    Method: generate

    Generates an RSS feed according to all the current information
    about the RSS feed. The feed will be displayed unless a file pointer
    is specified, in which case the feed will be written to the specified
    file stream.

    Parameters:
      resource $fp - The stream to write the RSS output to, instead of through
                     echo.

    Returns:
      bool - Returns true if the RSS feed was generated successfully, false if
             all the information which was required was not supplied, or if the
             method could not obtain a lock on the supplied file pointer.
  */
  public function generate($fp = null)
  {
    // You must have a title, link and description!
    if(empty($this->title) || empty($this->link) || empty($this->description))
    {
      // Sorry... Can't do it without it.
      return false;
    }
    // Supplied a stream for us to write to?
    elseif(!empty($fp) && !flock($fp, LOCK_EX))
    {
      // It would be nice if we could have an exclusive lock...
      return false;
    }

    api()->run_hooks('rss_generate', array($this, &$fp));

    // We may need to output headers!
    if(empty($fp))
    {
      if(ob_get_length() > 0)
      {
        ob_clean();
      }

      // Well, one ;-) The content type.
      header('Content-Type: application/rss+xml; charset=utf-8');
    }

    // Make things a tad easier.
    $crlf = "\r\n";

    // Let's start shall we?
    $buffer = '<?xml version="1.0" encoding="UTF-8"?>'. $crlf.
              '<rss version="2.0">'. $crlf.
              '  <channel>'. $crlf.
              '    <title>'. htmlchars($this->title). '</title>'. $crlf.
              '    <link>'. htmlchars($this->link). '</link>'. $crlf.
              '    <description>'. htmlchars($this->description). '</description>'. $crlf
              '    <generator>SnowCMS (http://www.snowcms.com/)</generator>'. $crlf;

    // Well, we have part of it... Let's output!
    if(!empty($fp))
    {
      fwrite($fp, $buffer);
    }
    else
    {
      echo $buffer;
      flush();
    }

    // And empty the buffer data.
    $buffer = '';

    // Did you specify a language?
    if(!empty($this->language))
    {
      $buffer .= '    <language>'. htmlchars($this->language). '</language>'. $crlf;
    }

    // Published date? Would be mighty nice :-)
    if(!empty($this->pubDate) || $this->pubDate === 0)
    {
      $buffer .= '    <pubDate>'. date('r', $this->pubDate). '</pubDate>'. $crlf;
    }

    // Last time it was built?
    if(!empty($this->lastBuildDate) || $this->lastBuildDate === 0)
    {
      $buffer .= '    <lastBuildDate>'. date('r', $this->lastBuildDate). '</lastBuildDate>'. $crlf;
    }

    // A docs URL? Weird.
    if(!empty($this->docs))
    {
      $buffer .= '    <docs>'. htmlchars($this->docs). '</docs>'. $crlf;
    }

    // The managing editors name/email?
    if(!empty($this->managingEditor))
    {
      $buffer .= '    <managingEditor>'. htmlchars($this->managingEditor). '</managingEditor>'. $crlf;
    }

    // Do you like to copyright?
    if(!empty($this->copyright))
    {
      $buffer .= '    <copyright>'. htmlchars($this->copyright). '</copyright>'. $crlf;
    }

    // Category information..?
    if(!empty($this->category['value']))
    {
      $buffer .= '    <category'. (!empty($this->category['domain']) ? ' domain="'. htmlchars($this->category['domain']). '"' : ''). '>'. htmlchars($this->category['value']). '</category>'. $crlf;
    }

    // Did you decide how long you want it to live? :P
    if(!empty($this->ttl) || $this->ttl === 0)
    {
      $buffer .= '    <ttl>'. (int)$this->ttl. '</ttl>'. $crlf;
    }

    // Once again, we got a good chunk done :-) So output the buffer to the right place.
    if(!empty($fp))
    {
      fwrite($fp, $buffer);
    }
    else
    {
      echo $buffer;
      flush();
    }

    // Empty it, and move on to items!
    $buffer = '';

    // That is, if there are any at all.
    if(count($this->items) > 0)
    {
      foreach($this->items as $item)
      {
        // Start the item out.
        $buffer = '    <item>'. $crlf;

        // You aren't required to have a title, but if you don't have the
        // title, then you have a description ;-)
        if(!empty($item['title']))
        {
          $buffer .= '      <title>'. htmlchars($item['title']). '</title>'. $crlf;
        }

        if(!empty($item['link']))
        {
          // Links are always good in an RSS feed... Lol.
          $buffer .= '      <link>'. htmlchars($item['link']). '</link>'. $crlf;
        }

        if(!empty($item['description']))
        {
          // Mmm... Descriptiveness :-P.
          $buffer .= '      <description>'. htmlchars($item['description']). '</description>'. $crlf;
        }

        if(!empty($item['author']))
        {
          // Someone had to make it...
          $buffer .= '      <author>'. htmlchars($item['author']). '</author>'. $crlf;
        }

        if(!empty($item['category']['value']))
        {
          $buffer .= '      <category'. (!empty($item['domain']) ? ' domain="'. htmlchars($item['domain']). '"' : ''). '>'. htmlchars($item['category']['value']). '</category>'. $crlf;
        }

        if(!empty($item['comments']))
        {
          // I would like to comment on your item. Please?
          $buffer .= '      <comments>'. htmlchars($item['comments']). '</comments>'. $crlf;
        }

        if(!empty($item['enclosure']['url']))
        {
          // Well aren't you Mr. Fancy Pants with your media in items!!!
          $buffer .= '      <enclosure url="'. htmlchars($item['enclosure']['url']). '" length="'. (int)$item['enclosure']['length']. '" type="'. htmlchars($item['enclosure']['type']). '" />'. $crlf;
        }

        if(!empty($item['guid']))
        {
          // Everything is unique! Well, unless you stole someone elses content. Tisk tisk!
          $buffer .= '      <guid>'. htmlchars($item['guid']). '</guid>'. $crlf;
        }

        if(isset($item['pubdate']))
        {
          // It had to be published at some point in time.
          $buffer .= '      <pubDate>'. date('r', (int)$item['pubdate']). '</pubDate>'. $crlf;
        }

        if(!empty($item['source']['url']))
        {
          // People have sources, you know?
          $buffer .= '      <source url="'. htmlchars($item['source']['url']). '">'. (!empty($item['source']['value']) ? htmlchars($item['source']['value']) : ''). '</source>'. $crlf;
        }

        // And... CLOSE TAG!
        $buffer .= '    </item>'. $crlf;

        // Output that sucker!!!
        if(!empty($fp))
        {
          fwrite($fp, $buffer);
        }
        else
        {
          echo $buffer;
          flush();
        }
      }
    }

    // Almost done!
    $buffer = '  </channel>'. "\r\n".
              '</rss>';

    if(!empty($fp))
    {
      fwrite($fp, $buffer);

      // And unlock it.
      flock($fp, LOCK_UN);
    }
    else
    {
      echo $buffer;
      flush();
    }

    // Woo! DONE!
    return true;
  }
}
?>