<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# Feed classes are defined here.
#
# RSS2 Class, it does RSS things in RSS 2.0 spec XD.
#
# Atom1 class, less used but roughly as powerful as RSS.
#

class RSS2
{
  private $feed_title = null;
  private $feed_link = null;
  private $feed_desc = null;
  private $items = array();

  /**
   * void setTitle(string $feed_title);
   *   - Sets the title of the feed.
  **/
  public function set_title($feed_title)
  {
    $this->feed_title = $feed_title;
  }

  /**
   * void setLink(string $feed_link);
   *  - Sets the source url of the feed.
  **/
  public function set_link($feed_link)
  {
    $this->feed_link = $feed_link;
  }

  /**
   * void setDesc(string $feed_desc);
   *  - Sets the description of the feed.
  **/
  public function set_desc($feed_desc)
  {
    $this->feed_desc = $feed_desc;
  }

  /**
   * void addItem(array $item);
   *  - Adds an item to the RSS items. Acceptable
   *    RSS elements are:
   *    title - The title of the item. *
   *    link - The URL to the original source of the item. *
   *    description - An excerpt from the content of the item. *
   *    author - The email address of the user who created the
   *             source of the item. (Note: Will only be displayed
   *             to logged in users!)
   *    category - The cateogory of the item. For example, on a forum
   *               it would be the board name ;)
   *    comments - A url to where to comment on the item.
   *    guid - A URL or string which uniquely identifies the item. *
   *    pubDate - A unix timestamp (See www.php.net/time) on which
   *              the item was originally created. *
   *    * = Required.
  **/
  public function add_item($item)
  {
    # Add the item ;) If it is an array!
    if(!is_array($item))
      trigger_error('The item given is not an array', E_USER_NOTICE);

    $this->items[] = array(
      'title' => $item['title'],
      'link' => $item['link'],
      'description' => $item['description'],
      'author' => !empty($item['author']) ? $item['author'] : false,
      'category' => !empty($item['category']) ? $item['category'] : false,
      'comments' => !empty($item['comments']) ? $item['comments'] : false,
      'guid' => $item['guid'],
      'pubDate' => (int)$item['pubDate'],
    );
  }

  /**
   * void show();
   *   - Displays the RSS feed with all the items
   *     and information gathered through the other
   *     methods such as addItem and so on...
  **/
  public function show()
  {
    global $settings, $user;

    # Headers sent..?
    if(headers_sent())
      # Yeah, remove them! :P
      @ob_clean();

    # Its RSS/XML ;)
    header('Content-Type: application/rss+xml');

    # Output the XML declaration.
    # Oh, and the start of the RSS stuffs.
    echo '<?xml version="1.0"?>
<rss version="2.0">
  <channel>
    <title>', $this->feed_title, '</title>
    <link>', $this->feed_link, '</link>
    <description>', $this->feed_desc, '</description>
    <pubDate>', gmdate('D, d M Y h:i:s T'), '</pubDate>
    <lastBuildDate>', gmdate('D, d M Y h:i:s T'), '</lastBuildDate>
    <generator>SnowCMS v', $settings['scmsVersion'], '</generator>';

    # Now the items...
    if(count($this->items))
    {
      foreach($this->items as $item)
      {
        # Output the item and all its content goodness...
        echo '
    <item>
      <title>', $item['title'], '</title>
      <link>', $item['link'], '</link>
      <description>', $item['description'], '</description>', !empty($item['author']) && $user['is_logged'] ? '
      <author>'. $item['author']. '</author>' : '', !empty($item['category']) ? '
      <category>'. $item['category']. '</category>' : '', !empty($item['comments']) ? '
      <comments>'. $item['comments']. '</comments>' : '', '
      <guid>', $item['guid'], '</guid>
      <pubDate>', gmdate('D, d M Y h:i:s T', $item['pubDate']), '</pubDate>
    </item>';
      }
    }

    # Close the channel and rss tag.
    echo '
  </channel>
</rss>';
  }
}

class Atom1
{
  private $feed_title = null;
  private $feed_subtitle = null;
  private $feed_link = null;
  private $entries = array();

  /**
   * void setTitle(string $feed_title);
   *   - Sets the title of the feed.
  **/
  public function set_title($feed_title)
  {
    $this->feed_title = $feed_title;
  }

  /**
   * void setDesc(string $feed_subtitle);
   *  - Sets the description of the feed.
  **/
  public function set_subtitle($feed_subtitle)
  {
    $this->feed_subtitle = $feed_subtitle;
  }

  /**
   * void setLink(string $feed_link);
   *  - Sets the source url of the feed.
  **/
  public function set_link($feed_link)
  {
    $this->feed_link = $feed_link;
  }

  /**
   * void addEntry(array $entry);
   *  - Adds an entry to the Atom feed. Acceptable
   *    Atom feed elements are:
   *    title - The title of the item. *
   *    link - The URL to the original source of the item. *
   *    description - An excerpt from the content of the item. *
   *    author - The email address of the user who created the
   *             source of the item. (Note: Will only be displayed
   *             to logged in users!)
   *    category - The cateogory of the item. For example, on a forum
   *               it would be the board name ;)
   *    comments - A url to where to comment on the item.
   *    guid - A URL or string which uniquely identifies the item. *
   *    pubDate - A unix timestamp (See www.php.net/time) on which
   *              the item was originally created. *
   *    * = Required.
  **/
  public function add_entry($entry)
  {
    # Add the entry ;) If it is an array!
    if(!is_array($item))
      trigger_error('The entry given is not an array', E_USER_NOTICE);

    $this->entries[] = array(
      'title' => $item['title'],
      'link' => $item['link'],
      'summary' => $item['summary'],
      'author' => !empty($item['author']) ? $item['author'] : false,
      'category' => !empty($item['category']) ? $item['category'] : false,
      'id' => $item['id'],
      'updated' => (int)$item['updated'],
    );
  }

  /**
   * void show();
   *   - Displays the Atom feed with all the entries
   *     and information gathered through the other
   *     methods such as addItem and so on...
  **/
  public function show()
  {
    global $settings, $user;

    # Headers sent..?
    if(headers_sent())
      # Yeah, remove them! :P
      @ob_clean();

    # Its Atom/XML ;)
    header('Content-Type: application/atom+xml');

    # Output the XML declaration.
    # Oh, and the start of the Atom feed
    echo '<?xml version="1.0"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>', $this->feed_title, '</title>
  <subtitle>', $this->feed_subtitle, '</subtitle>
  <link href="', $this->feed_link, '" />
  <updated>', gmdate('D, d M Y h:i:s T'), '</updated>
  <generator>SnowCMS v', $settings['scmsVersion'], '</generator>';

    # Now the items...
    if(count($this->items))
    {
      foreach($this->items as $item)
      {
        # Output the item and all its content goodness...
        echo '
  <entry>
    <title>', $item['title'], '</title>
    <link href="', $item['link'], '" />
    <author>'. $item['author']. '</author>' : '', !empty($item['category']) ? '
    <category>'. $item['category']. '</category>' : '', !empty($item['comments']) ? '
    <id>', $item['guid'], '</id>
    <updated>', gmdate('D, d M Y h:i:s T', $item['updated']), '</updated>
    <summary>', $item['summary'], '</summary>', !empty($item['author']) && $user['is_logged'] ? '
  </entry>';
      }
    }

    # Close the channel and feed tag
    echo '
</feed>';
  }
}
?>