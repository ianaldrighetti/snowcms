<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
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
	Class: HTTP

	The HTTP class allows you to access remote files on the Internet.
	Makes it easier on you, and it also makes it better for end users
	otherwise some plugins may only support curl or fsockopen, but with
	this, it can use either.
*/
class HTTP
{
	// Variable: referer
	// The value of the HTTP Referer header, if none set, the header is not
	// sent.
	private $referer;

	// Variable: allow_redirect
	// Whether or not you want to be redirected when a Location header is
	// retrieved.
	private $allow_redirect;

	// Variable: max_redirects
	// Specifies the maximum amount of times the Location header will be
	// allowed to redirect  your request, if allow_redirect is true.
	private $max_redirects;

	// Variable: include_header
	// Whether or not you want to have the header included with the body of
	// the retrieved file.
	private $include_header;

	// Variable: post_data
	// An array (key => value) containing POST data you want sent on every
	// remote request. However, you can also set POST data at the time of
	// making the request, this attribute is for POST data you want sent for
	// every remote request.
	private $post_data;

	// Variable: http_version
	// The HTTP version to use in requests, either 1.0 or 1.1.
	private $http_version;

	// Variable: port
	// The port to connect through. If none specified 80 is used. If you set
	// is_ssl to true then the port is automatically changed to 443.
	private $port;

	// Variable: timeout
	// The maximum number of seconds that the downloading can take. If not
	// specified, 5 seconds is assumed.
	private $timeout;

	// Variable: user_agent
	// The user agent to set the User-Agent HTTP header to.
	private $user_agent;

	// Variable: info
	// An array containing the information from the last request made by the
	// HTTP class (HTTP status code, effective URL and content type).
	private $info;

	/*
		Constructor: __construct

		Parameters:
			int $port - The port to use when requesting a URL.
			array $post_data - The POST data to send upon each URL request.
			string $user_agent - The user agent to use in the HTTP headers.
			int $http_version - The HTTP version to use in requests, either 1.0
													or 1.1.
	*/
	public function __construct($port = null, $post_data = null, $user_agent = null, $http_version = null)
	{
		$this->set_referer(null);
		$this->set_allow_redirect(true);
		$this->set_max_redirects(5);
		$this->set_include_header(false);
		$this->set_post_data(empty($post_data) || !is_array($post_data) ? array() : $post_data);
		$this->set_http_version(empty($http_version) || (string)$http_version != (string)(float)$http_version ? 1.1 : $http_version);
		$this->set_port(empty($port) || (string)$port != (string)(int)$port ? 80 : $port);
		$this->set_timeout(5);
		$this->set_user_agent(empty($user_agent) || !is_string($user_agent) ? 'SnowCMS ' .(settings()->get('show_version', 'bool', false) ? 'v'. settings()->get('version', 'string') : ''). '/PHP'. (settings()->get('show_version', 'bool', false) ? 'v'. PHP_VERSION : '') : $user_agent);
		$this->info = false;
	}

	/*
		Method: set_referer

		Sets the HTTP Referer header.

		Parameters:
			string $referer - The HTTP referer to send in the remote request.
												Defaults to null.

		Returns:
			void - Nothing is returned by this method.
	*/
	public function set_referer($referer = null)
	{
		$this->referer = (string)$referer;
	}

	/*
		Method: referer

		Returns the currently set HTTP Referer.

		Parameters:
			none

		Returns:
			string - Returns the currently set HTTP Referer.
	*/
	public function referer()
	{
		return $this->referer;
	}

	/*
		Method: set_allow_redirect

		Whether or not to allow redirects (HTTP Location header) at all.

		Parameters:
			bool $allow_redirect - Whether or not to allow the remote file you
														 are requesting redirect you. Defaults to true.

		Returns:
			void - Nothing is returned by this method.
	*/
	public function set_allow_redirect($allow_redirect = true)
	{
		$this->allow_redirect = !empty($allow_redirect);
	}

	/*
		Method: allow_redirect

		This will return whether or not the HTTP instance at hand is set to
		redirect upon receiving a HTTP Location header.

		Parameters:
			none

		Returns:
			bool - Returns true if Location headers will be followed, false if not.
	*/
	public function allow_redirect()
	{
		return $this->allow_redirect;
	}

	/*
		Method: set_max_redirects

		Sets the maximum number of times the Location header will be followed
		when making a request.

		Parameters:
			int $max_redirects - The maximum number of times to allow your
													 request to be redirected. Defaults to 5.

		Returns:
			void - Nothing is returned by this method.
	*/
	public function set_max_redirects($max_redirects = 5)
	{
		$this->max_redirects = max((int)$max_redirects, 0);
	}

	/*
		Method: max_redirects

		Returns the maximum number of times the HTTP instance will follow
		a Location header.

		Parameters:
			none

		Returns:
			int - Returns the maximum number of times the Location header will
						be followed.
	*/
	public function max_redirects()
	{
		return $this->max_redirects;
	}

	/*
		Method: set_include_header

		Whether or not to include the header when returning or saving the
		data received from the request.

		Parameters:
			bool $include_header - Whether or not to have the header data along
														 with the content retrieved from the request.
														 Defaults to false.

		Returns:
			void - Nothing is returned by this method.
	*/
	public function set_include_header($include_header = false)
	{
		$this->include_header = !empty($include_header);
	}

	/*
		Method: include_header

		Returns whether or not the HTTP headers will be included when the data
		is returned or saved to a file.

		Parameters:
			none

		Returns:
			bool - Returns true if the header will be included, false if not.
	*/
	public function include_header()
	{
		return $this->include_header;
	}

	/*
		Method: set_post_data

		Sets an array which will be sent as POST data to the address when
		making a request via the <HTTP::request> method.

		Parameters:
			array $post_data - An associative array (array('key' => 'value', [...]))
												 containing the post data to send upon every
												 remote request.

		Returns:
			void - Nothing is returned by this method.

		Note:
			You can set the post data for the specific request you are making
			via the method <HTTP::request>.
	*/
	public function set_post_data($post_data)
	{
		if(is_array($post_data))
		{
			$this->post_data = $post_data;
		}
	}

	/*
		Method: post_data

		Returns the currently set POST data, which will be sent to the URL
		upon making a request. This can be set when making a specific request
		via the <HTTP::request> method.

		Parameters:
			none

		Returns:
			array - Returns an array containing the POST data.
	*/
	public function post_data()
	{
		return $this->post_data;
	}

	/*
		Method: set_http_version

		The HTTP version to use when making a request via <HTTP::request>.

		Parameters:
			float $http_version - The version of HTTP to use when requesting the
														remote file. This can either be 1.0 or 1.1.

		Returns:
			void - Nothing is returned by this method.
	*/
	public function set_http_version($http_version)
	{
		$this->http_version = $http_version == 1 || $http_version == 1.1 ? $http_version : 1.1;
	}

	/*
		Method: http_version

		Returns the currently set HTTP version to use upon making a request.

		Parameters:
			none

		Returns:
			float - Returns the currently set HTTP version to use upon making a
							request.
	*/
	public function http_version()
	{
		return $this->http_version;
	}

	/*
		Method: set_port

		Sets the port number to connect to when making a request via the
		<HTTP::request> method.

		Parameters:
			int $port - The port number to send the request to. Such as 80
									(HTTP), 443 (HTTPS) or others.

		Returns:
			void - Nothing is returned by this method.

		Note:
			The port defaults to 80. If you are going to attempt to make an SSL
			connection ensure that the server supports it via <HTTP::ssl_supported>.
	*/
	public function set_port($port)
	{
		if((string)$port == (string)(int)$port && $port > 0)
		{
			$this->port = (int)$port;
		}
	}

	/*
		Method: port

		Returns the currently set port, which will be connected to upon making
		a request to the specified address via <HTTP::request>.

		Parameters:
			none

		Returns:
			int - Returns the currently set port.
	*/
	public function port()
	{
		return $this->port;
	}

	/*
		Method: set_timeout

		Sets the maximum amount of time (in seconds) that the request will
		wait before giving up on a connection.

		Parameters:
			int $timeout - In seconds, how long the request can take before
										 timing out.

		Returns:
			void - Nothing is returned by this method.
	*/
	public function set_timeout($timeout)
	{
		if((string)$timeout == (string)(int)$timeout && $timeout > 0)
		{
			$this->timeout = (int)$timeout;
		}
	}

	/*
		Method: timeout

		Returns the currently set timeout limit.

		Parameters:
			none

		Returns:
			int - Returns the currently set timeout limit, in seconds.
	*/
	public function timeout()
	{
		return $this->timeout;
	}

	/*
		Method: set_user_agent

		Sets the User-Agent header for any requests made.

		Parameters:
			string $user_agent - The user agent to send in the request.

		Returns:
			void - Nothing is returned by this method.
	*/
	public function set_user_agent($user_agent)
	{
		if(is_string($user_agent))
		{
			$this->user_agent = $user_agent;
		}
	}

	/*
		Method: user_agent

		Returns the currently set user agent to use in a request.

		Parameters:
			none

		Returns:
			string - Returns the user agent currently set.
	*/
	public function user_agent()
	{
		return $this->user_agent;
	}

	/*
		Method: ssl_supported

		Parameters:
			none

		Returns:
			bool - Returns true if PHP supports SSL, false if not.
	*/
	public function ssl_supported()
	{
		// OpenSSL available..?
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
							the request is returned, however, false on failure. If
							filename is not left empty, true will be returned if the
							data was saved to the file successfully, false on failure
							which could mean the request as a whole failed, or the file
							could not be written to.

		Note:
			If the protocol of $url is https, the port is automatically changed
			to 443. Only if the server supports SSL, otherwise the protocol is
			automatically changed to http.

			If $resume_from is set to 0 and $filename is set, if $filename
			already exists then its contents will be overwritten, however, if
			$resume_from is set to greater than 0, than the file is appended to.

			There is an array inside which plugins can hook into
			(called 'request_callbacks') and modify to add additional support to
			remote file accessing. Currently SnowCMS supports cURL and fsockopen
			as methods of remote requesting. cURL is preferred over fsockopen
			(as fsockopen tends to be much slower than cURL!), so even if
			fsockopen would work, cURL is still used.

			Failure is defined as a URL redirecting more than allowed or receiving
			an HTTP status code of greater than or equal to 400.
	*/
	public function request($url, $post_data = array(), $resume_from = 0, $filename = null)
	{
		global $func;

		// Just incase...
		$url = ltrim($url);

		// In the embedded arrays, each array is to contain two things,
		// a test index which is a callback that returns true or false.
		// That bool the test callback tells request whether or not the
		// remote request can be made via the callback index. The callback
		// index contains, obviously, a callback to a function which
		// makes the remote request. An array containing all the information
		// (such as url, post_data, etc. etc.) to use in the request is passed.
		// If filename isn't empty, an index in the passed array will contain
		// the pointer to the file which you will write to via fwrite (the index
		// being called 'fp'). The callback is to return false on failure, or
		// true if all data was written to the file successfully OR the string
		// containing the retrieved data if 'fp' was empty. Capeesh? :P
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

		// Well, want to add anything...?
		api()->run_hooks('request_callbacks', array(&$request_callbacks));

		// Find what will be handling our request :)
		if(!empty($request_callbacks))
		{
			foreach($request_callbacks as $request_callback)
			{
				if($request_callback['test']())
				{
					$callback = $request_callback['callback'];
					break;
				}
			}

			// Nothing found..? What a shame...
			if(empty($callback))
			{
				return false;
			}

			// You wanted this written to a file?
			if(!empty($filename))
			{
				$fp = fopen($filename, $resume_from > 0 ? 'ab' : 'wb');
			}

			// Well, we have gone as far as we have, no it is up to you ;)
			return $callback(array(
				'url' => ($func['substr']($func['strtolower']($url), 0, 8) == 'https://' && !$this->ssl_supported() ? 'http'. $func['substr']($func['strtolower']($url), 5, $func['strlen']($url)) : $url),
				'post_data' => array_merge($this->post_data, $post_data),
				'resume_from' => max((int)$resume_from, 0),
				'fp' => !empty($filename) ? $fp : null,
				'referer' => $this->referer,
				'allow_redirect' => !empty($this->allow_redirect),
				'max_redirects' => $this->max_redirects,
				'include_header' => !empty($this->include_header),
				'http_version' => $this->http_version,
				'port' => ($func['substr']($func['strtolower']($url), 0, 8) == 'https://' && $this->port == 80 && $this->ssl_supported() ? 443 : $this->port),
				'timeout' => $this->timeout,
				'user_agent' => $this->user_agent,
				'http' => $this,
			));
		}
		else
		{
			return false;
		}
	}

	/*
		Method: set_info

		This method is only to be used by the registered request callbacks, and
		should be called upon immediately upon completing a request issued by
		the HTTP class. This method is to be supplied an array containing
		information about the completed request. See the note for more
		information.

		Parameters:
			int $info - An array containing the information about the completed
									request.

		Returns:
			void - Nothing is returned by this method.

		Note:
			The array should contain the following information:

				int status_code - The HTTP status code received from the server the
													request was made to.

				string effective_url - The last effective URL of the request, such
															 as if the Location: header is supplied, the
															 function should make the request again to the
															 URL specified in Location: if the allow
															 redirect options permit.

				string content_type - The contents of the Content-Type: header.

				bool timed_out - A bool which indicates whether the request timed
												 out.
	*/
	public function set_info($info)
	{
		// Make sure the right options have been set.
		if(!array_key_exists('status_code', $info) || !array_key_exists('effective_url', $info) || !array_key_exists('content_type', $info) || !array_key_exists('timed_out', $info))
		{
			return;
		}

		$this->info = array(
										'status_code' => (int)$info['status_code'],
										'effective_url' => $info['effective_url'],
										'content_type' => $info['content_type'],
										'timed_out' => !empty($info['timed_out']),
									);
	}

	/*
		Method: status_code

		Returns the HTTP status code received from the last request.

		Parameters:
			none

		Returns:
			mixed - Returns an integer containing the HTTP status code if one was
							retrieved, and false if no previous request was made.
	*/
	public function status_code()
	{
		return is_array($this->info) ? $this->info['status_code'] : false;
	}

	/*
		Method: effective_url

		Returns the effective URL (the actual URL the request ended up
		retrieving) from the last request.

		Parameters:
			none

		Returns:
			mixed - Returns a string containing the last effective URL, and false
							if no previous request was made.
	*/
	public function effective_url()
	{
		return is_array($this->info) ? $this->info['effective_url'] : false;
	}

	/*
		Method: content_type

		Returns the content type received from the last request.

		Parameters:
			none

		Returns:
			mixed - Returns a string containing the content type retrieved from
							the last request, and false if no previous request was made.

		Note:
			Please note that the value returned could also contain a charset, such
			as: text/html; charset=utf-8. Also, this value may be null in the case
			that the URL retrieved did not specify a Content-Type header.
	*/
	public function content_type()
	{
		return is_array($this->info) ? $this->info['content_type'] : false;
	}

	/*
		Method: timed_out

		Returns whether the last request resulted in a timeout, whether it be
		because the remote server is down, or the functions timed out.

		Parameters:
			none

		Returns:
			mixed - Returns a bool indicating whether the last request timed out,
							however if no previous request was made, then null is
							returned.
	*/
	public function timed_out()
	{
		return is_array($this->info) ? $this->info['timed_out'] : null;
	}
}

if(!function_exists('http_curl_request'))
{
	/*
		Function: http_curl_request

		This function completes the specified remote request using the
		PHP extension cURL.

		Parameters:
			array $request - An array containing things such as the url,
											 post_data, among others. Check out <HTTP::request>
											 for more info.

		Returns:
			mixed - If the index in $request 'fp' is set, true will be returned
							if the file was successfully written to, false on failure.
							Otherwise, a string containing the retrieved data is returned,
							or false on failure.

		Note:
			This function is overloadable.
	*/
	function http_curl_request($request)
	{
		global $func;

		if(!is_array($request))
		{
			return false;
		}

		// Get an instance of cURL going!
		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $request['url']);

			// Any special place we are resuming from?
			curl_setopt($ch, CURLOPT_RESUME_FROM, $request['resume_from']);

			// Whether or not we are following Location headers. Luckily cURL
			// does this without needing us to do it, phew!
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $request['allow_redirect']);

			// The maximum number of times the request can be redirected.
			curl_setopt($ch, CURLOPT_MAXREDIRS, $request['max_redirects']);

			// Want the header or not?
			curl_setopt($ch, CURLOPT_HEADER, $request['include_header']);

			// Which version of HTTP?
			curl_setopt($ch, CURLOPT_HTTP_VERSION, $request['http_version'] == 1 ? CURL_HTTP_VERSION_1_0 : CURL_HTTP_VERSION_1_1);

			// The port? Preferably 80 :P
			curl_setopt($ch, CURLOPT_PORT, $request['port']);

			// How long should we wait?
			curl_setopt($ch, CURLOPT_TIMEOUT, $request['timeout']);

			// We want cURL to return the data to us, otherwise upon calling
			// curl_exec, it is just automatically displayed to the user.
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// Any post data..?
		if(!empty($request['post_data']) && count($request['post_data']) > 0)
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request['post_data']));
		}

		// Any referer set?
		if(!empty($request['referer']))
		{
			curl_setopt($ch, CURLOPT_REFERER, $request['referer']);
		}

		// How about a user agent?
		if(!empty($request['user_agent']))
		{
			curl_setopt($ch, CURLOPT_USERAGENT, $request['user_agent']);
		}

		// Execute the cURL session...
		$data = curl_exec($ch);

		// Get our information!
		$info = curl_getinfo($ch);

		// We will want to give the HTTP class its information.
		$request['http']->set_info(array(
																 'status_code' => $info['http_code'],
																 'effective_url' => $info['url'],
																 'content_type' => $info['content_type'],
																 'timed_out' => $info['total_time'] >= $request['timeout'],
															));

		// Now that we gave the HTTP instance the information, we should check
		// to make sure the request was successful. This could mean that we got
		// a file not found or other server error, or maybe the request timed
		// out according to the timeout option we were supplied.
		if($info['http_code'] >= 400 || $info['total_time'] >= $request['timeout'])
		{
			// Anything over 400 is not a good sign!
			return false;
		}

		// Get the error number... Just incase...
		$curl_errno = curl_errno($ch);
		curl_close($ch);

		// Did we get error #33? We can fix that :P
		if($curl_errno == 33)
		{
			$new_request = $request;
			$new_request['resume_from'] = 0;
			$new_request['fp'] = null;

			// Make the request again, with everything originally
			// except the resume from and fp...
			$data = http_curl_request($new_request);

			// Cheating..? Sure, but don't tell anyone xD
			$data = $func['substr']($data, $request['resume_from'], $func['strlen']($data));
		}

		// Did you want this written to a file?
		if(!empty($request['fp']))
		{
			flock($request['fp'], LOCK_EX);
			fwrite($request['fp'], $data);
			flock($request['fp'], LOCK_UN);
			fclose($request['fp']);

			return $data !== false;
		}
		else
		{
			// You just wanted the data, so here you go...
			return $data;
		}
	}
}

if(!function_exists('http_fsockopen_request'))
{
	/*
		Function: http_fsockopen_request

		This function completes the specified remote request using fsockopen.L.

		Parameters:
			array $request - An array containing things such as the url,
											 post_data, among others. Check out <HTTP::request>
											 for more info.
			int $num_redirects - This holds the number of times that the
													 function has followed the HTTP Location header.
													 This is used by the function itself! So no
													 touchy, please :)

		Returns:
			mixed - If the index in $request 'fp' is set, true will be returned
							if the file was successfully written to, false on failure.
							Otherwise, a string containing the retrieved data is returned,
							or false on failure.

		Note:
			This function is overloadable.
	*/
	function http_fsockopen_request($request, $num_redirects = 0)
	{
		global $func;

		if(!is_array($request))
		{
			return false;
		}
		elseif(!empty($request['allow_redirect']) && $num_redirects > $request['max_redirects'])
		{
			// We can't keep being redirected, as we have a set option that tells
			// us how many times will will continue to follow. So we will still
			// set the request information, but the developer can use this
			// information to see that they were being redirected too often.
			$request['http']->set_info(array(
																	 'status_code' => isset($request['status_code']) ? $request['status_code'] : 301,
																	 'effective_url' => $request['url'],
																	 'content_type' => null,
																	 'timed_out' => false,
																));

			return false;
		}

		// Parse the URL... We need to for fsockopen.
		$parsed = parse_url($request['url']);

		$fp = fsockopen(($parsed['scheme'] == 'https' ? 'ssl://' : ''). $parsed['host'], $request['port'], $errno, $errstr, $request['timeout']);

		// Couldn't connect...?
		if(empty($fp))
		{
			$request['http']->set_info(array(
																	 'status_code' => null,
																	 'effective_url' => $request['url'],
																	 'content_type' => null,
																	 'timed_out' => true,
																));

			return false;
		}

		// Let's set a timeout, maybe.
		if($request['timeout'] > 0)
		{
			stream_set_timeout($fp, $request['timeout']);
		}

		// Make our request path, used in our request, of course!
		$request_path = (!empty($parsed['path']) ? $parsed['path'] : '/'). (!empty($parsed['query']) ? '?'. $parsed['query'] : '');

		// No post data? Then GET!
		if(empty($post_data))
		{
			$commands = "GET $request_path HTTP/". ($request['http_version'] == 1 ? '1.0' : '1.1'). "\r\n";
			$commands .= "Host: {$parsed['host']}\r\n";

			if(!empty($request['resume_from']) && $request['resume_from'] > 0)
			{
				$commands .= "Range: {$request['resume_from']}-\r\n";
			}

			if(!empty($request['referer']))
			{
				$commands .= "Referer: {$request['referer']}\r\n";
			}

			if(!empty($request['user_agent']))
			{
				$commands .= "User-Agent: {$request['user_agent']}\r\n";
			}

			$commands .= "Connection: close\r\n\r\n";
		}
		else
		{
			// Turn the array into a string.
			$post_data = http_build_query($request['post_data']);

			$commands = "POST $request_path HTTP/". ($request['http_version'] == 1 ? '1.0' : '1.1'). "\r\n";
			$commands .= "Host: {$parsed['host']}\r\n";

			if(!empty($request['resume_from']) && $request['resume_from'] > 0)
			{
				$commands .= "Range: {$request['resume_from']}-\r\n";
			}

			if(!empty($request['referer']))
			{
				$commands .= "Referer: {$request['referer']}\r\n";
			}

			if(!empty($request['user_agent']))
			{
				$commands .= "User-Agent: {$request['user_agent']}\r\n";
			}

			$commands .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$commands .= "Content-Length: ". strlen($post_data). "\r\n";
			$commands .= "Connection: close\r\n\r\n";
			$commands .= $post_data. "\r\n\r\n";
		}

		// Send those commands to the server.
		fwrite($fp, $commands);

		// Now we can start to get the data.
		$data = '';
		while(!feof($fp))
		{
			$data .= fgets($fp, 4096);
		}

		$info = stream_get_meta_data($fp);
		fclose($fp);

		// Make sure it didn't time out.
		if(!empty($info['timed_out']))
		{
			$request['http']->set_info(array(
																	 'status_code' => null,
																	 'effective_url' => $request['url'],
																	 'content_type' => null,
																	 'timed_out' => true,
																));

			return false;
		}

		// Get the headers and data separated.
		list($full_raw_headers, $data) = explode("\r\n\r\n", $data, 2);

		// Get the status.
		list($http_status, $raw_headers) = explode("\r\n", $full_raw_headers, 2);

		// Let's get the status code all alone.
		if(strpos($http_status, ' ') !== false)
		{
			$parts = explode(' ', $http_status);

			// The status code will be the second part.
			$status_code = (int)$parts[1];
		}
		else
		{
			$status_code = false;
		}

		// Make sure we didn't get any error.
		if($status_code === false || $status_code >= 400)
		{
			$request['http']->set_info(array(
																	 'status_code' => $status_code,
																	 'effective_url' => $request['url'],
																	 'content_type' => null,
																	 'timed_out' => false,
																));

			return false;
		}

		// Now read the headers into an easy to read array... :D
		$headers = array();
		$raw_headers = explode("\r\n", $raw_headers);
		if(count($raw_headers) > 0)
		{
			foreach($raw_headers as $header)
			{
				$header = trim($header);
				if(empty($header) || $func['strpos']($header, ':') === false)
				{
					continue;
				}

				list($name, $content) = explode(':', $header, 2);
				$headers[$func['strtolower']($name)] = trim($content);
			}
		}

		// So do we need to redirect, perhaps?
		if(in_array($status_code, array(301, 302, 303, 307), true))
		{
			// Yeah, but are we allowed to do so?
			if(!empty($request['allow_redirect']))
			{
				return !empty($headers['location']) ? http_fsockopen_request(array_merge($request, array('url' => $headers['location'], 'status_code' => $status_code)), $num_redirects + 1) : false;
			}
			else
			{
				// No, we aren't. So that's no good.
				$request['http']->set_info(array(
																		 'status_code' => $status_code,
																		 'effective_url' => $request['url'],
																		 'content_type' => !empty($headers['content-type']) ? $headers['content-type'] : null,
																		 'timed_out' => false,
																	));

				return false;
			}
		}

		// Okay, well, if the transfer-encoding header is not set, then we can
		// just stop here, if not, we need to do a little bit extra.
		if(!empty($headers['transfer-encoding']) && $func['strtolower']($headers['transfer-encoding']) == 'chunked')
		{
			list($hex, $data) = explode("\r\n", $data, 2);

			$new_data = '';
			while($hex != '0')
			{
				$new_data .= $func['substr']($data, 0, hexdec($hex));
				$data = ltrim($func['substr']($data, hexdec($hex), $func['strlen']($data)));
				list($hex, $data) = explode("\r\n", $data, 2);
			}

			$data = $new_data;
		}

		$data = $request['include_header'] ? implode("\r\n\r\n", array($full_raw_headers, $data)) : $data;

		// Everything seems to have been okay, so we can set our information in
		// the HTTP class now.
		$request['http']->set_info(array(
																 'status_code' => $status_code,
																 'effective_url' => $request['url'],
																 'content_type' => !empty($headers['content-type']) ? $headers['content-type'] : null,
																 'timed_out' => false,
															));

		// Did you want this written to a file?
		if(!empty($request['fp']))
		{
			flock($request['fp'], LOCK_EX);
			fwrite($request['fp'], $data);
			flock($request['fp'], LOCK_UN);
			fclose($request['fp']);

			return $data !== false;
		}
		else
		{
			return $data;
		}
	}
}
?>