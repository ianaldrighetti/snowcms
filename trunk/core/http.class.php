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
  Class: HTTP
  The HTTP class allows you to access remote files on the Internet.
  Makes it easier on you, and it also makes it better for end users
  otherwise some plugins may only support curl or fsockopen, but with
  this, it can use either.

  At download params:
    POST data, resume from (in bytes), timeout, url (duh), output file
*/
class HTTP
{
  # Variable: referer
  # The value of the HTTP Referer header, if none set, the header is not sent.
  private $referer;

  # Variable: allow_redirect
  # Whether or not you want to be redirected when a Location header is retrieved.
  private $allow_redirect;

  # Variable: max_redirects
  # Specifies the maximum amount of times the Location header will be allowed to redirect
  # your request, if allow_redirect is TRUE.
  private $max_redirects;

  # Variable: include_header
  # Whether or not you want to have the header included with the body of the retrieved file.
  private $include_header;

  # Variable: post_data
  # An array (key => value) containing POST data you want sent on every remote request.
  # However, you can also set POST data at the time of making the request, this attribute
  # is for POST data you want sent for every remote request.
  private $post_data;

  # Variable: http_version
  # The HTTP version to use in requests, either 1.0 or 1.1
  private $http_version;

  # Variable: port
  # The port to connect through. If none specified 80 is used. If you set is_ssl to true
  # then the port is automatically changed to 443.
  private $port;

  # Variable: timeout
  # The maximum number of seconds that the downloading can take. If not specified, 5 seconds
  # is assumed.
  private $timeout;

  # Variable: user_agent
  # The user agent to set the User-Agent HTTP header to.
  private $user_agent;

  /*
    Constructor: __construct([int port = null[, array $post_data = null[, string $user_agent = null[, int $http_version = null]]]]);

    Parameters:
      int $port - The port to use when requesting a URL.
      array $post_data - The POST data to send upon each URL request.
      string $user_agent - The user agent to use in the HTTP headers.
      int $http_version - The HTTP version to use in requests, either 1.0 or 1.1.
  */
  public function __construct($port = null, $post_data = null, $user_agent = null, $http_version = null)
  {
    global $settings;

    $this->set_referer(null);
    $this->set_allow_redirect(true);
    $this->set_max_redirects(5);
    $this->set_include_header(false);
    $this->set_post_data(empty($post_data) || !is_array($post_data) ? array() : $post_data);
    $this->set_http_version(empty($http_version) || (string)$http_version != (string)(float)$http_version ? 1.1 : $http_version);
    $this->set_port(empty($port) || (string)$port != (string)(int)$port ? 80 : $port);
    $this->set_timeout(5);
    $this->set_user_agent(empty($user_agent) || !is_string($user_agent) ? 'SnowCMS ' .((bool)$settings->get('show_version') ? 'v'. $settings->get('version') : ''). '/PHP'. ((bool)$settings->get('show_version') ? 'v'. PHP_VERSION : '') : $user_agent);
  }

  /*
    Method: set_referer

    Parameters:
      string referer - The HTTP referer to send in the remote request. Defaults to NULL.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_referer($referer = null)
  {
    $this->referer = (string)$referer;
  }

  /*
    Method: set_allow_redirect

    Parameters:
      bool $allow_redirect - Whether or not to allow the remote file you are requesting redirect you.
                             Defaults to TRUE.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_allow_redirect($allow_redirect = true)
  {
    $this->allow_redirect = !empty($allow_redirect);
  }

  /*
    Method: set_max_redirects

    Parameters:
      int $max_redirects - The maximum number of times to allow your request to be redirected. Defaults to 5.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_max_redirects($max_redirects = 5)
  {
    $this->max_redirects = max((int)$max_redirects, 0);
  }

  /*
    Method: set_include_header

    Parameters:
      bool $include_header - Whether or not to have the header data along with the content retrieved from the request.
                             Defaults to FALSE.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_include_header($include_header = false)
  {
    $this->include_header = !empty($include_header);
  }

  /*
    Method: set_post_data

    Parameters:
      array $post_data - An associative array (array('key' => 'value', [...])) containing the post data to send
                         upon every remote request.

    Returns:
      void - Nothing is returned by this method.

    NOTE: You can set the post data for the specific request you are making via the method
  */
}
?>