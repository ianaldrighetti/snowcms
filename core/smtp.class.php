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

# Title: SMTP

/*
  Class: SMTP

  Allows emails to be sent using the SMTP (Simple Mail Transfer Protocol),
  this is used if enabled and all the SMTP options are set properly. Please
  note that you should NOT directly use this class, but use the <Mail> class.
*/
class SMTP
{
  # Variable: con
  # Holds the resource of the connection to the SMTP server.
  private $con;

  # Variable: host
  # The SMTP server hostname.
  private $host;

  # Variable: port
  # The port of the SMTP server.
  private $port;

  # Variable: is_tls
  # Whether or not to use a TLS connection with the SMTP server.
  private $is_tls;

  # Variable: timeout
  # The amount of time, in seconds, the class should be given to make
  # a connection with the SMTP server.
  private $timeout;

  # Variable: headers
  # Contains additional headers to send with the email.
  private $headers;

  # Variable: is_html
  # Set to true if the message being sent should be marked as HTML.
  private $is_html;

  # Variable: priority
  # The priority of the email message (1 - 5, 1 being the most important).
  private $priority;

  # Variable: charset
  # The charset of the email message being sent.
  private $charset;

  # Variable: errors
  # An array containing any error messages which occurred while processing the request.
  private $errors;

  /*
    Constructor: __construct

    Parameters:
      string $host - The SMTP server hostname.
      int $port - The SMTP port to connect to.
      bool $is_tls - Whether or not to use a TLS connection with the server.
      int $timeout - The amount of time, in seconds, to allow the class to
                     attempt to connect to the SMTP server before giving up.
  */
  public function __construct($host = null, $port = 25, $is_tls = false, $timeout = 5)
  {
    $this->con = null;
    $this->host = null;
    $this->port = 25;
    $this->is_tls = false;
    $this->timeout = 5;
    $this->headers = array();
    $this->is_html = false;
    $this->priority = 3;
    $this->charset = 'utf-8';
    $this->errors = array();

    if(!empty($host))
      $this->connect($host, $port, $is_tls, $timeout);
  }

  /*
    Method: connect

    Parameters:
      string $host - The SMTP server hostname.
      int $port - The SMTP port to connect to.
      bool $is_tls - Whether or not to use a TLS connection with the server.
      int $timeout - The amount of time, in seconds, to allow the class to
                     attempt to connect to the SMTP server before giving up.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      TLS is not supported by all servers, you must have at least PHP 5.1.0
      in order for this to possibly work, even then, the remote server needs
      to support TLS as well.
  */
  public function connect($host, $port, $is_tls = false, $timeout = 5)
  {
    # Already connected to the server?
    if(!empty($this->con))
      return false;

    # Let's try connecting to the SMTP server, shall we?
    $this->con = fsockopen($host, $port, $errno, $errstr, $timeout);

    # Did it work?
    if(!empty($this->con))
    {
      # Turn stream blocking on (stalls reading from the server until we get something).
      stream_set_blocking($this->con, 1);
      stream_set_timeout($this->con, $timeout, 0);

      # Clear the buffer, we don't need anything the server spewed out.
      $this->get_response();

      # Did you want TLS? Let's attempt to start it.
      if(!empty($is_tls))
        $this->is_tls = $this->start_tls();

      # Save the other information.
      $this->host = $host;
      $this->port = $port;
      $this->timeout = $timeout;

      # HELLO MR. SERVER! CAN HAZ HELO?
      if(!$this->send_hello())
      {
        # I guess I can't :(
        $this->errors[] = l('Server refused HELO.');
        $this->close();
        return false;
      }

      return true;
    }
    else
    {
      $this->errors[] = l('The server "%s" refused connection.', htmlchars($host));
      return false;
    }
  }

  /*
    Method: get_response

    Retrieves the response from the SMTP server.

    Parameters:
      none

    Returns:
      string - Returns the response from the server, false if nothing.
  */
  private function get_response()
  {
    if(empty($this->con))
      return false;

    $response = '';
    while($data = fgets($this->con, 512))
    {
      $response .= $data;

      # Is that all?
      if(substr($data, 3, 1) == ' ')
        break;
    }

    return !empty($response) ? $response : false;
  }

  /*
    Method: start_tls

    Attempts to start a TLS connection with the SMTP server.

    Parameters:
      none

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function start_tls()
  {
    if(empty($this->con) || !function_exists('stream_socket_enable_crypto'))
      return false;

    # Let's ask nicely, now.
    fwrite($this->con, "STARTTLS\r\n");

    $reply_code = (int)substr($this->get_response(), 0, 3);

    # If the server accepted our request, it would have returned 220.
    if($reply_code = 220)
      return stream_socket_enable_crypto($this->con, true, STREAM_CRYPTO_METHOD_TLS_SERVER);
    else
      return false;
  }

  /*
    Method: send_hello

    Sends EHLO to the server, but if EHLO is not supported by the
    server, HELO is sent.

    Parameters:
      none

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function send_hello()
  {
    if(empty($this->con))
      return false;

    # As stated, EHLO might not always work, but EHLO first still!
    fwrite($this->con, "EHLO {$this->host}\r\n");

    $reply_code = (int)substr($this->get_response(), 0, 3);

    # Did we get a 250? That means it worked! :D
    if($reply_code == 250)
      return true;
    else
    {
      # Now the simple ol' HELO!
      fwrite($this->con, "HELO {$this->host}\r\n");

      $reply_code = (int)substr($this->get_response(), 0, 3);

      # Now return whether or not we got a 250.
      return $reply_code == 250;
    }
  }

  /*
    Method: authenticate

    Authenticates with the SMTP server using the supplied credentials.

    Parameters:
      string $username - The SMTP username.
      string $password - The SMTP password.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function authenticate($username, $password)
  {
    if(empty($this->con))
      return false;

    # Give the server a heads up on what we are sending.
    fwrite($this->con, "AUTH LOGIN\r\n");

    # Do you accept, Mr. Server?
    $reply_code = (int)substr($this->get_response(), 0, 3);

    # 334 means it accepted our request! ;D
    if($reply_code == 334)
    {
      # Send your username now, base64 encoded.
      fwrite($this->con, base64_encode($username). "\r\n");

      $reply_code = (int)substr($this->get_response(), 0, 3);

      # A 334 response is what we want, which means the username exists.
      if($reply_code != 334)
      {
        $this->errors[] = l('The server "%s" did not accept the SMTP username.', htmlchars($this->host));
        return false;
      }

      # Still going? Then we can send the password :)
      fwrite($this->con, base64_encode($password). "\r\n");

      $reply_code = (int)substr($this->get_response(), 0, 3);

      # Was your password accepted?
      if($reply_code == 235)
        return true;
      else
      {
        $this->errors[] = l('The server "%s" did not accept the SMTP password.', htmlchars($this->host));
        return false;
      }
    }
    else
    {
      # Well, that didn't work.
      $this->errors[] = l('The server "%s" refused to authenticate.', htmlchars($this->host));
      return false;
    }
  }

  /*
    Method: set_from

    Sets the MAIL FROM header, which tells the SMTP server who
    the email is sent from.

    Parameters:
      string $from - The email address the mail message should be marked "from".

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      $from MUST be ONLY an email address, you cannot supply My Name <address@host.com>,
      as that is not how the MAIL FROM header is setup. However, if you so please, you
      can set the FROM header in that format using <SMTP::add_header>
  */
  public function set_from($from)
  {
    if(empty($this->con))
      return false;

    fwrite($this->con, "MAIL FROM: <$from>\r\n");

    # Did the server accept this address, or not?
    $reply_code = (int)substr($this->get_response(), 0, 3);

    if($reply_code == 250)
    {
      $this->add_header('FROM', '<'. $from. '>');
      return true;
    }
    else
    {
      $this->errors[] = l('The server "%s" did not accept the email "%s" as the MAIL FROM.', htmlchars($this->host), htmlchars($from));
      return false;
    }
  }

  /*
    Method: add_header

    Allows the ability to add additional headers to be sent along with the
    email message.

    Parameters:
      string $header - The headers name.
      string $value - The value of the header.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      You may also pass an array to $header and $value, just as you can with
      the <http://www.php.net/str_replace> function.

      Example:
        $smtp->add_header(array('FROM', 'X-PRIORITY'), array('Me <my@address>', 5));
  */
  public function add_header($header, $value)
  {
    if(empty($this->con))
      return false;

    if(is_array($header) && is_array($value))
    {
      # Not the same amount in both? BAD!
      if(count($header) != count($value) || count($header) == 0)
        return false;

      foreach($header as $key => $val)
        # Use this method to add it :P
        $this->add_header($val, $value[$key]);

      return true;
    }
    else
    {
      $this->headers[strtoupper($header)] = $value;
      return true;
    }
  }

  /*
    Method: send

    Sends the email using SMTP along with all the specified additions
    such as headers.

    Parameters:
      mixed $to - This can be either an array of addresses, a string containing
                  one address or a string containing multiple addresses separated
                  by commas.
      string $subject - The subject of the email message.
      string $message - The email message to send.
      string $alt_message - If HTML is set to true, this can be set so if the
                            receivers email client has HTML disabled or not supported.
                            You can turn on HTML via <SMTP::set_html>.

    Returns:
      bool - Returns true if the message was sent without issue, false otherwise.

    Note:
      Just because this method returned true does not mean all receipients (if any)
      actually received the email message.

      If HTML is set to true and no alternative message is supplied, the $message
      parameter will be sent as the alternative message, just all the HTML stripped
      using strip_tags.

      The connection to the remote server is closed if the email message is sent successfully.
  */
  public function send($to, $subject, $message, $alt_message = null)
  {
    if(empty($this->con))
      return false;

    # Is the to parameter an array? That's fine, we will change that.
    if(is_array($to))
      $to = implode(', ', $to);

    # We need to send RCPT TO headers before we go any further to tell the SMTP
    # server where the email message is headed to. So that includes all TO, CC
    # and also BCC that we need to do this for.
    $rcpts = $to;
    if(!empty($this->headers['TO']))
      $rcpts .= ', '. $this->headers['TO'];

    if(!empty($this->headers['CC']))
      $rcpts .= ', '. $this->headers['CC'];

    if(!empty($this->headers['BCC']))
      $rcpts .= ', '. $this->headers['BCC'];

    # Remove anything like <, >, and names.
    # Credit: http://www.php.net/manual/en/function.mail.php#89169
    $rcpts = explode(',', preg_replace('~([\w\s]+)<([\S@._-]*)>~', '$2', $rcpts));

    # Uh oh O.o
    if(count($rcpts) == 0)
      return false;

    # Now send all the RCPT TO headers.
    foreach($rcpts as $rcpt)
    {
      $rcpt = trim($rcpt);

      # Empty? Can't send it to nowhere!
      if(empty($rcpt))
        continue;

      fwrite($this->con, "RCPT TO: <$rcpt>\r\n");

      # Did it work? A 250 or 251 reply will be returned.
      $reply_code = (int)substr($this->get_response(), 0, 3);
      if($reply_code != 250 || $reply_code != 251)
        $this->errors[] = l('The email <%s> was not accepted by the server "%s"', htmlchars($rcpt), htmlchars($this->host));
    }

    # Now the DATA command, where afterwards we will send extra headers and the message! :)
    fwrite($this->con, "DATA\r\n");
    $reply_code = (int)substr($this->get_response(), 0, 3);

    if($reply_code != 354)
    {
      $this->errors[] = l('The server "%s" did not accept the DATA command.', htmlchars($this->host));
      return false;
    }

    # However, before we send the headers, first add a couple.
    if(empty($this->headers['SUBJECT']))
      $this->headers['SUBJECT'] = $subject;

    # No TO header? We shall do it ourselves, then!
    if(empty($this->headers['TO']))
      $this->headers['TO'] = $to;
    elseif(!empty($to))
      # There is already a TO header, but we have more to add.
      $this->headers['TO'] .= ', '. $to;

    # A date, perhaps.
    if(empty($this->headers['DATE']))
      $this->headers['DATE'] = date('r');

    # !!! Not done yet ;)
  }
}
?>