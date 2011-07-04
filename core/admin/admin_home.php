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

    api()->context['current_news'] = $current_news;

    theme()->render('admin_home');
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
  if(!member()->can('access_admin_cp'))
  {
    admin_access_denied();
  }

  admin_current_area('system_about');

  theme()->set_title(l('About'));

  theme()->render('admin_about');
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

				// Check for Windows 7 SP2
				if($version == '6.1' && $sp == '7601')
				{
					$sp = ' SP1';
				}
        // Check for Vista SP1
        elseif($version == '6.0' && $sp == '6001')
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