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

# Title: Control Panel - Home

if(!function_exists('admin_home'))
{
  /*
    Function: admin_home

    Displays the Administration Center's home page.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_home()
  {
    global $api, $base_url, $func, $icons, $member, $settings, $theme;

    $api->run_hooks('admin_home');

    # Can you even access the Admin Control Panel..?
    if(!$member->can('access_admin_cp'))
      admin_access_denied();

    # Do we need to fetch the news from the SnowCMS site..?
    $handled = false;
    $api->run_hooks('admin_fetch_news', array(&$handled, 'http://download.snowcms.com/news/v2.x-line/news.php'));

    # If you didn't handle it (even if the news didn't need fetching, still set it to true!!!), we will.
    # So either it is just plain time to check again, OR the news is empty, for some weird reason.
    if(empty($handled) && (($settings->get('admin_news_fetched', 'int', 0) + $settings->get('admin_news_fetch_every', 'int', 43200)) < time_utc() || $func['strlen']($settings->get('admin_news_cache', 'string', '')) == 0))
    {
      # This is a place for the HTTP class!
      $http = $api->load_class('HTTP');

      # Make an HTTP request for it.
      $fetched_news = $http->request($api->apply_filters('admin_news_url', 'http://download.snowcms.com/news/v2.x-line/news.php'));

      # If the hashes are the same, no need to continue.
      if($settings->get('admin_news_hash', 'string', '') != sha1($fetched_news))
      {
        # Save the new hash.
        $settings->set('admin_news_hash', sha1($fetched_news), 'string');

        # Time to parse the news. Super fun!
        $parsed = array();
        while($func['strlen']($fetched_news) > 0)
        {
          # Get the headers.
          list($headers, $fetched_news) = explode("\n\n", $fetched_news, 2);

          $headers = explode("\n", $headers);
          $tmp = array();
          foreach($headers as $header)
          {
            list($key, $value) = explode(':', $header, 2);

            $tmp[strtolower(trim($key))] = trim($value);
          }
          $headers = $tmp;

          $parsed[] = array(
                        'subject' => $headers['subject'],
                        'href' => isset($headers['url']) ? $headers['url'] : false,
                        'date' => $headers['date'],
                        'content' => $func['substr']($fetched_news, 0, $headers['content-length']),
                      );

          $fetched_news = ltrim($func['substr']($fetched_news, $headers['content-length'], $func['strlen']($fetched_news)));
        }

        # Save the parsed news, in a serialized array!
        $settings->set('admin_news_cache', serialize($parsed), 'string');
      }

      # Update the last time the news was fetched.
      $settings->set('admin_news_fetched', time_utc(), 'int');
    }

    # Load up the news.
    $current_news = @unserialize($settings->get('admin_news_cache', 'string'));

    $theme->header();

    echo '
  <div id="sidebar">
    <h3 style="margin-top: 0px !important;">', l('Notifications'), '</h3>';

    # If you would like to add a notification, simply add a filter to admin_notifications and
    # add an array to the passed array containing a subject, title (optional), and/or href (optional).
    $notifications = $api->apply_filters('admin_notifications', array());

    if(is_array($notifications) && count($notifications))
    {
      foreach($notifications as $notification)
        echo '
      <p class="notification">', (!empty($notification['href']) ? '<a href="'. $notification['href']. '"'. (!empty($notification['title']) ? ' title="'. $notification['title']. '"' : ''). '>' : (!empty($notification['title']) ? '<span title="'. $notification['title']. '">' : '')), $notification['subject'], (!empty($notification['href']) ? '</a>' : (!empty($notification['title']) ? '</span>' : '')), '</p>';
    }
    else
    {
      echo '
    <p class="notification">', l('No notifications.'), '</p>';
    }

    echo '
    <h3>News from <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a>:</h3>';

    # Loop through them all, if any, anyways.
    if(count($current_news) > 0)
    {
      foreach($current_news as $news)
      {
        echo '
      <p class="news_subject" title="', $news['date'], '">', (!empty($news['href']) ? '<a href="'. $news['href']. '" target="_blank">' : ''), $news['subject'], (!empty($news['href']) ? '</a>' : ''), '</p>
      <p class="news_content">', $news['content'], '</p>';
      }
    }
    else
    {
      echo '
    <p>', l('No news to display.'), '</p>';
    }

    echo '
  </div>
  <div id="main">';

    if(is_array($icons) && count($icons) > 0)
    {
      $first = true;
      foreach($icons as $header => $icon)
      {
        echo '
    <h1', (!empty($first) ? ' style="margin-top: 0px !important;"' : ''), '>', $header, '</h1>
    <table class="icons">
      <tr>';

        # Time to show the actual icons.
        $length = count($icon);
        for($i = 0; $i < $length; $i++)
        {
          echo '
        <td><a href="', $icon[$i]['href'], '" title="', $icon[$i]['title'], '"><img src="', $icon[$i]['src'], '" alt="" title="', $icon[$i]['title'], '" /><br />', $icon[$i]['label'], '</a></td>';

          if(($i + 1) % 6 == 0 && isset($icon[$i + 1]))
            echo '
      </tr>
    </table>
    <table class="icons">
      <tr>';
        }

        echo '
      </tr>
    </table>';

        $first = false;
      }
    }
    else
    {
      echo '
    <h1 style="margin-top: 0px !important;">', l('Error'), '</h1>
    <p>', l('Sorry, but it appears that the icons have been malformed.'), '</p>';
    }

    echo '
  </div>
  <div class="break">
  </div>';

    $theme->footer();
  }
}


/*
  Function: admin_about

  Displays the about page for SnowCMS in the control panel.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function admin_about()
{
  global $api, $base_url, $theme;

  $theme->set_current_area('system_about');

  $theme->set_title(l('About'));

  $theme->header();

  echo '
  <h1><img src="', $theme->url(), '/about-small.png" alt="" /> ', l('About SnowCMS'), '</h1>
  <p>', l('SnowCMS is a light, powerful and free content management system, otherwise known as a CMS. It has a powerful plugin system allowing you to have minor changes made to your site, or large features such as a forum, blog, or both! By default SnowCMS only has a member management and plugin system, meaning you can have your site with as few or as many features as you want, and nothing more. SnowCMS is written in the popular language <abbr title="PHP: Hypertext Preprocessor">PHP</abbr> and uses MySQL or SQLite for storage.'), '</p>
  <br />
  <p>', l('SnowCMS is released under the <a href="http://www.gnu.org/licenses/quick-guide-gplv3.html" title="GNU General Public License v3">GPL v3</a> license, meaning you are free to use, modify and redistribute SnowCMS if you so please. While you do have those freedoms, please keep in mind that a lot of work was put into SnowCMS by the <a href="http://www.snowcms.com/">SnowCMS Developer Team</a>, but also no warranty is provided by this software, nor are we or anyone else responsible for anything that may occur while using this system.'), '</p>

  <h3>', l('Developers'), '</h3>
  <p>', l('The following people are currently, or have been previously, major contributors to the <a href="http://www.snowcms.com/" title="SnowCMS">SnowCMS</a> project, we thank them for all their help!'), '</p>
  <ul>
    <li>Ian Aldrighetti (aldo) - ', l('Lead Developer of SnowCMS v0.7, 1.0 and 2.0'), '</li>
  </ul>

  <h3>', l('Credits'), '</h3>
  <p>', l('There are a few places where SnowCMS used the works of others, and this section is dedicated to their credit!'), '</p>
  <ul>
    <li>', l('Admin Control Panel icons from the <a href="http://tango.freedesktop.org/" title="Tango Desktop Project" target="_blank">Tango Desktop Project</a>.'), '</li>
    <li>', l('Admin Control Panel inspired by the <a href="http://www.jaws-project.com/" title="Jaws Project" target="_blank">Jaws Project</a>.'), '</li>
  </ul>';

  $theme->footer();
}
?>