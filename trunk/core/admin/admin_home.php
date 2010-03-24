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
    global $api, $func, $settings, $theme;

    $api->run_hooks('admin_home');

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
            list($key, $value) = explode(':', $header);

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

    $theme->set_title(l('Administration Center'));

    $theme->header();

    echo '
      <h1>', l('Administration Center'), '</h1>

      <div id="left">
        Hello :-)
      </div>
      <div id="right">
        Hiya! :D
      </div>';

    $theme->footer();
  }
}
?>