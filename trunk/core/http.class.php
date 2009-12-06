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
    Constructor: __construct

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

    Note: You can set the post data for the specific request you are making via the method
  */
  public function set_post_data($post_data)
  {
    if(is_array($post_data))
      $this->post_data = $post_data;
  }

  /*
    Method: set_http_version

    Parameters:
      float $http_version - The version of HTTP to use when requesting the remote file.
                            Can either be 1.0 or 1.1

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_http_version($http_version)
  {
    $this->http_version = $http_version == 1 || $http_version == 1.1 ? $http_version : 1.1;
  }

  /*
    Method: set_port

    Parameters:
      int $port - The port number to send the request to. Such as 80 (HTTP), 443 (HTTPS)
                  or others.

    Returns:
      void - Nothing is returned by this method.

    Note:
      The port defaults to 80. If you are going to attempt to make an SSL connection
      ensure that the server supports it via <HTTP::ssl_supported>.
  */
  public function set_port($port)
  {
    if((string)$port == (string)(int)$port && $port > 0)
      $this->port = (int)$port;
  }

  /*
    Method: set_timeout

    Parameters:
      int $timeout - In seconds, how long the request can take before timing out.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_timeout($timeout)
  {
    if((string)$timeout == (string)(int)$timeout && $timeout > 0)
      $this->timeout = (int)$timeout;
  }

  /*
    Method: set_user_agent

    Parameters:
      string $user_agent - The user agent to send in the request.

    Returns:
      void - Nothing is returned by this method.
  */
  public function set_user_agent($user_agent)
  {
    if(is_string($user_agent))
      $this->user_agent = $user_agent;
  }

  /*
    Method: ssl_supported

    Parameters:
      none

    Returns:
      bool - Returns TRUE if PHP supports SSL, FALSE if not.
  */
  public function ssl_supported()
  {
    # OpenSSL available..?
    return function_exists('openssl_seal');
  }

  /*
    Method: request

    Parameters:
      string $url - The URL to request.
      array $post_data - An associative array which contains POST data to
                         send when the request is made. If the post_data
                         attribute contains anything, this parameter will
                         have that data merged with this one.
      int $resume_from - Where to start the download from, in bytes.
      string $filename - If this is specified, this method will store the
                         data retrieved in that specified file.

    Returns:
      mixed - If filename is left empty, a string containing the data from
              the request is returned, however, FALSE on failure. If filename
              is not left empty, TRUE will be returned if the data was saved
              to the file successfully, FALSE on failure which could mean
              the request as a whole failed, or the file could not be written to.

    Note:
      If the protocol of $url is https, the port is automatically changed to 443.
      Only if the server supports SSL, otherwise the protocol is automatically
      changed to http.
      If $resume_from is set to 0 and $filename is set, if $filename already exists
      then its contents will be overwritten, however, if $resume_from is set to
      greater than 0, than the file is appended to.
      There is an array inside which plugins can hook into (called 'request_callbacks')
      and modify to add additional support to remote file accessing. Currently
      SnowCMS supports cURL and fsockopen as methods of remote requesting. The order
      in which each callback is in is the order in which SnowCMS tests to see if
      the current PHP setup supports it. So if cURL and fsockopen are supported, cURL
      is still used.
  */
  public function request($url, $post_data = array(), $resume_from = 0, $filename = null)
  {
    global $api;

    # Just incase...
    $url = ltrim($url);

    # In the embedded arrays, each array is to contain two things,
    # a test index which is a callback that returns TRUE or FALSE.
    # That bool the test callback tells request whether or not the
    # remote request can be made via the callback index. The callback
    # index contains, obviously, a callback to a function which
    # makes the remote request. An array containing all the information
    # (such as url, post_data, etc. etc.) to use in the request is passed.
    # If filename isn't empty, an index in the passed array will contain
    # the pointer to the file which you will write to via fwrite (the index
    # being called 'fp'). The callback is to return FALSE on failure, or
    # TRUE if all data was written to the file successfully OR the string
    # containing the retrieved data if 'fp' was empty. Capeesh? :P
    $request_callbacks = array(
      array(
        'test' => create_function('', '
                    return function_exists(\'curl_exec\');'),
        'callback' => 'http_curl_request',
      ),
      array(
        'test' => create_function('', '
                    return function_exists(\'fsockopen\');'),
        'callback' => 'http_fsockopen_request',
      ),
    );

    # Well, want to add anything...?
    $api->run_hook('request_callbacks', array(&$request_callbacks));

    # Find what will be handling our request :)
    if(!empty($request_callbacks))
    {
      foreach($request_callbacks as $request_callback)
        if($request_callback['test']())
        {
          $callback = $request_callback['callback'];
          break;
        }

      # Nothing found..? What a shame...
      if(empty($callback))
        return false;

      # You wanted this written to a file?
      if(!empty($filename))
        $fp = @fopen($filename, $resume_from > 0 ? 'ab' : 'wb');

      # Well, we have gone as far as we have, no it is up to you ;)
      return $callback(array(
        'url' => substr(mb_strtolower($url), 0, 8) == 'https://' && !$this->ssl_supported() ? 'http'. substr(mb_strtolower($url), 5, mb_strlen($url)) : $url,
        'post_data' => array_merge($this->post_data, $post_data),
        'resume_from' => max((int)$resume_from, 0),
        'fp' => !empty($fp) ? $fp : null,
        'referer' => $this->referer,
        'allow_redirect' => !empty($this->allow_redirect),
        'max_redirects' => $this->max_redirects,
        'include_header' => !empty($this->include_header),
        'http_version' => $this->http_version,
        'port' => substr(mb_strtolower($url), 0, 8) == 'https://' && $this->port == 80 && $this->ssl_supported() ? 443 : $this->port,
        'timeout' => $this->timeout,
        'user_agent' => $this->user_agent,
      ));
    }
    else
      return false;
  }
}

if(!function_exists('http_curl_request'))
{
  /*
    Function: http_curl_request

    This function completes the specified remote request using the
    PHP extension cURL.

    Parameters:
      array $request - An array containing things such as the url, post_data,
                       among others. Check out <HTTP::request> for more info.

    Returns:
      mixed - If the index in $request 'fp' is set, TRUE will be returned
              if the file was successfully written to, FALSE on failure.
              Otherwise, a string containing the retrieved data is returned,
              or FALSE on failure.
  */
  function http_curl_request($request)
  {
    $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $request['url']);
      curl_setopt($ch, CURLOPT_RESUME_FROM, $request['resume_from']);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $request['allow_redirect']);
      curl_setopt($ch, CURLOPT_MAXREDIRS, $request['max_redirects']);
      curl_setopt($ch, CURLOPT_HEADER, $request['include_header']);
      curl_setopt($ch, CURLOPT_HTTP_VERSION, $request['http_version'] == 1 ? CURL_HTTP_VERSION_1_0 : CURL_HTTP_VERSION_1_1);
      curl_setopt($ch, CURLOPT_PORT, $request['port']);
      curl_setopt($ch, CURLOPT_TIMEOUT, $request['timeout']);

    # Any post data..?
    if(!empty($request['post_data']) && count($request['post_data']) > 0)
    {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request['post_data']);
    }

    # Any referer set?
    if(!empty($request['referer']))
      curl_setopt($ch, CURLOPT_REFERER, $request['referer']);

    # How about a user agent?
    if(!empty($request['user_agent']))
      curl_setopt($ch, CURLOPT_USERAGENT, $request['user_agent']);

    # Execute the cURL session...
    $data = curl_exec($ch);
    curl_close($ch);

    # Did you want this written to a file?
    if(!empty($request['fp']))
    {
      flock($fp, LOCK_EX);
      fwrite($fp, $data);
      flock($fp, LOCK_UN);
      fclose($fp);

      return $data !== false;
    }
    else
      # You just wanted the data, so here you go...
      return $data;
  }
}
?>