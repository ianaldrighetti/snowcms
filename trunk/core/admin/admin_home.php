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

if(!defined('IN_SNOW'))
{
  die('Nice try...');
}

// Title: Control Panel - Home

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
    global $func, $icons;

    api()->run_hooks('admin_home');

    // Can you even access the Admin Control Panel..?
    if(!member()->can('access_admin_cp'))
    {
      admin_access_denied();
    }

    // Do we need to fetch the news from the SnowCMS site..?
    $handled = false;
    api()->run_hooks('admin_fetch_news', array(&$handled, 'http://download.snowcms.com/news/v2.x-line/news.php'));

    // If you didn't handle it (even if the news didn't need fetching, still set it to true!!!), we will.
    // So either it is just plain time to check again, OR the news is empty, for some weird reason.
    if(empty($handled) && ((settings()->get('admin_news_fetched', 'int', 0) + settings()->get('admin_news_fetch_every', 'int', 43200)) < time_utc() || $func['strlen'](settings()->get('admin_news_cache', 'string', '')) == 0))
    {
      // This is a place for the HTTP class!
      $http = api()->load_class('HTTP');

      // Make an HTTP request for it.
      $fetched_news = $http->request(api()->apply_filters('admin_news_url', 'http://download.snowcms.com/news/v2.x-line/news.php'));

      // If the hashes are the same, no need to continue.
      if(settings()->get('admin_news_hash', 'string', '') != sha1($fetched_news))
      {
        // Save the new hash.
        settings()->set('admin_news_hash', sha1($fetched_news), 'string');

        // Time to parse the news. Super fun!
        $parsed = array();
        while($func['strlen']($fetched_news) > 0)
        {
          // Get the headers.
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

        // Save the parsed news, in a serialized array!
        settings()->set('admin_news_cache', serialize($parsed), 'string');
      }

      // Update the last time the news was fetched.
      settings()->set('admin_news_fetched', time_utc(), 'int');
    }

    // Load up the news.
    $current_news = @unserialize(settings()->get('admin_news_cache', 'string'));

    theme()->header();

    echo '
  <div id="sidebar">
    <h3 style="margin-top: 0px !important;">', l('Notifications'), '</h3>';

    // If you would like to add a notification, simply add a filter to admin_notifications and
    // add an array to the passed array containing a subject, title (optional), and/or href (optional).
    $notifications = api()->apply_filters('admin_notifications', array());

    if(is_array($notifications) && count($notifications))
    {
      foreach($notifications as $notification)
      {
        echo '
      <p class="notification">', (!empty($notification['href']) ? '<a href="'. $notification['href']. '"'. (!empty($notification['title']) ? ' title="'. $notification['title']. '"' : ''). '>' : (!empty($notification['title']) ? '<span title="'. $notification['title']. '">' : '')), $notification['subject'], (!empty($notification['href']) ? '</a>' : (!empty($notification['title']) ? '</span>' : '')), '</p>';
      }
    }
    else
    {
      echo '
    <p class="notification">', l('No notifications.'), '</p>';
    }

    echo '
    <h3>News from <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a>:</h3>';

    // Loop through them all, if any, anyways.
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

        // Time to show the actual icons.
        $length = count($icon);
        for($i = 0; $i < $length; $i++)
        {
          echo '
        <td><a href="', $icon[$i]['href'], '" title="', $icon[$i]['title'], '"><img src="', $icon[$i]['src'], '" alt="" title="', $icon[$i]['title'], '" /><br />', $icon[$i]['label'], '</a></td>';

          if(($i + 1) % 6 == 0 && isset($icon[$i + 1]))
          {
            echo '
      </tr>
    </table>
    <table class="icons">
      <tr>';
          }
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

    theme()->footer();
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
  theme()->set_current_area('system_about');

  theme()->set_title(l('About'));

  theme()->header();

  echo '
  <h1><img src="', theme()->url(), '/about-small.png" alt="" /> ', l('About SnowCMS'), '</h1>
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
    <li>', l('Admin Control Panel icons from the <a href="http://kde-look.org/content/show.php/Oxygen+Icons?content=74184" title="Oxygen Icon set" target="_blank">Oxygen Icon set</a>.'), '</li>
    <li>', l('Admin Control Panel inspired by the <a href="http://www.jaws-project.com/" title="Jaws Project" target="_blank">Jaws Project</a>.'), '</li>
  </ul>

  <h1 style="margin-top: 20px;"><img src="', theme()->url(), '/about-small.png" alt="" /> ', l('System information'), '</h1>
  <p><strong>Operating system:</strong> ', admin_get_os_information(), '</p>
  <p><strong>Server software:</strong> ', admin_get_software_information(), '</p>
  <p><strong>PHP version:</strong> ', PHP_VERSION, '</p>
  <p><strong>Database:</strong> ', db()->type, '</p>
  <p><strong>Database version:</strong> ', db()->version(), '</p>';

  theme()->footer();
}

if(!function_exists('admin_get_os_information'))
{
  /*
    Function: admin_get_os_information

    Returns a string containing the operating systems information, as much
    as possible.

    Parameters:
      none

    Returns:
      string - Returns a string containing the operating systems information.

    Note:
      This function is overloadable.

      This may not work as well on Linux as it does Windows, if anyone has
      improvements for getting all the possible Linux information, please do tell :-)
  */
  function admin_get_os_information()
  {
    // Check if we're dealing with Windows.
    if(substr(PHP_OS, 0, 3) == 'WIN')
    {
      // Chek if there's a version string and if so, save it in $ver.
      if($ver = @shell_exec('ver'))
      {
        // Get the version number from the version string.
        $version = explode('.', preg_replace('/^.*\[.*?([0-9.]+)\].*$/s', '$1', $ver));

        // Get the build number.
        $build = isset($version[2]) ? $version[2] : 0;

        // The version pack version number.
        $sp = isset($version[3]) ? $version[3] : $build;

        // The version and build number in the one string.
        $version_build = (isset($version[0]) ? (int)$version[0] : 0). '.'. (isset($version[1]) ? (int)$version[1] : 0). '.'. substr($build, 0, 1);

        // The version number, without the build number
        $version = (isset($version[0]) ? (int)$version[0] : 0). '.'. (isset($version[1]) ? (int)$version[1] : 0);

        // Get the possible Windows version names.
        $versions = array(
          '1.01' => 'Windows 1.01',
          '2.03' => 'Windows 2.03',
          '2.11' => 'Windows 2.11',
          '3.0' => 'Windows 3.0',
          '3.1' => 'Windows 3.1',
          '3.11' => 'Windows 3.11',
          '3.2' => 'Windows 3.2',
          '3.5' => 'Windows NT 3.5',
          '3.51' => 'Windows NT 3.51',
          '4.0' => 'Windows 95',
          '4.0.1' => 'Windows NT 4.0',
          '4.10' => 'Windows 98',
          '4.10.2' => 'Windows 98 SE',
          '4.90' => 'Windows ME',
          '5.0' => 'Windows 2000',
          '5.1' => 'Windows XP',
          '5.2' => 'Windows Server 2003',
          '5.2.4' => 'Windows Home Server',
          '6.0' => 'Windows Vista',
          '6.1' => 'Windows 7',
        );

        // Check for Vista SP1
        if($version == '6.0' && $sp == '6001')
        {
          $sp = ' SP1';
        }
        // Vista SP2
        elseif($version == '6.0' && $sp == '6002')
        {
          $sp = ' SP2';
        }
        // Check for XP SP1
        elseif(($version == '5.1' || $version == '5.2') && ($sp == '1089' || $sp == '1070'))
        {
          $sp = ' SP1';
        }
        // XP SP2
        elseif(($version == '5.1' || $version == '5.2') && $sp == '2180')
        {
          $sp = ' SP2';
        }
        // XP SP3
        elseif(($version == '5.1' || $version == '5.2') && $sp == '5512')
        {
          $sp = ' SP3';
        }
        // Okay, so no service pack.
        else
        {
          $sp = '';
        }

        // Might be determined by the build number.
        if(isset($versions[$version_build]))
        {
          return $versions[$version_build];
        }
        // Or better yet, the actual version.
        elseif(isset($versions[$version]))
        {
          // Might want to add 64-bit to it. Maybe.
          if($version == '5.2' && (isset($_ENV['PROCESSOR_ARCHITECTURE']) ? $_ENV['PROCESSOR_ARCHITECTURE'] : 'x86') != 'x86')
          {
            return 'Windows XP 64-bit'. $sp;
          }
          elseif($version == '6.0' && (isset($_ENV['PROCESSOR_ARCHITECTURE']) ? $_ENV['PROCESSOR_ARCHITECTURE'] : 'x86') != 'x86')
          {
            return 'Windows Vista 64-bit'. $sp;
          }
          elseif($version == '6.1' && (isset($_ENV['PROCESSOR_ARCHITECTURE']) ? $_ENV['PROCESSOR_ARCHITECTURE'] : 'x86') != 'x86')
          {
            return 'Windows 7 64-bit'. $sp;
          }
          else
          {
            // Just plain ol' 32-bit.
            return $versions[$version]. $sp;
          }
        }
        else
        {
          return 'Windows '. $version. $sp;
        }
      }
      // We can't determine the particular Windows version, so let's check if it's just a generic Windows NT.
      elseif(PHP_OS == 'WINNT')
      {
        return 'Windows NT';
      }
      // Okay, then it's just a generic Windows.
      else
      {
        return 'Windows';
      }
    }
    else
    {
      return PHP_OS;
    }
  }
}

if(!function_exists('admin_get_software_information'))
{
  /*
    Function: admin_get_software_information

    Returns the information about the server software (Like IIS or Apache).

    Parameters:
      none

    Returns:
      string - Returns a string containing the servers software information.

    Note:
      This function is overloadable.
  */
  function admin_get_software_information()
  {
    $software = $_SERVER['SERVER_SOFTWARE'];

    // Is it Apache?
    if(stripos($software, 'Apache') !== false)
    {
      // Try to get a version.
      if(strpos($software, '/') !== false)
      {
        list(, $version) = explode('/', $software, 2);
        list($version) = @explode(' ', $version, 2);

        $version = trim($version);
      }

      return 'Apache'. (!empty($version) ? ' v'. $version : '');
    }
    // Might be IIS
    elseif(stripos($software, 'IIS') !== false)
    {
      // Get the version of IIS, if available.
      if(strpos($software, '/') !== false)
      {
        list(, $version) = explode('/', $software);

        $version = trim($version);
      }

      return '<abbr title="Internet Information Services">IIS</abbr>'. (!empty($version) ? ' v'. $version : '');
    }
    else
    {
      // Don't know...
      return $software;
    }
  }
}
?>