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

/*
  Class: PHP_Mail

  Allows emails to be sent using the mail function built into PHP, this is
  used if the mail function is allowed and enabled. Please note that you
  should NOT directly use this class, but use the <Mail> class.
*/
class PHP_Mail
{
  // Variable: options
  // Contains all the options for sending the email.
  private $options;

  /*
    Constructor: __construct
  */
  public function __construct()
  {
    $this->options = array(
                       'headers' => array(),
                       'is_html' => false,
                       'priority' => 3,
                       'charset' => 'utf-8',
                     );
  }

  /*
    Method: connect

    When using the mail() function, you don't connect to anything, but just
    for good measure, this method is here are filler ;)

    Parameters:
      string $host
      int $port
      bool $is_tls
      int $timeout

    Returns:
      bool - Returns true.
  */
  public function connect($host, $port, $is_tls = false, $timeout = 5)
  {
    return true;
  }

  /*
    Method: authenticate

    Just as above, this is just here as filler.

    Parameters:
      string $username
      string $password

    Returns:
      bool - Returns true.
  */
  public function authenticate($username, $password)
  {
    return true;
  }

  /*
    Method: set_from

    Sets the FROM header.

    Parameters:
      string $from - The email address the mail message should be marked "from".

    Returns:
      bool - Returns true.
  */
  public function set_from($from)
  {
    $this->add_header('FROM', $from);
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
        $php_mail->add_header(array('FROM', 'X-PRIORITY'), array('Me <my@address>', 5));
  */
  public function add_header($header, $value)
  {
    if(is_array($header) && is_array($value))
    {
      // Not the same amount of headers and values? Tisk tisk!
      if(count($header) != count($value) || count($header) == 0)
        return false;

      foreach($header as $key => $val)
        // :D
        $this->add_header($val, $value[$key]);
    }
    else
    {
      $this->options['headers'][strtoupper($header)] = $value;
      return true;
    }
  }

  /*
    Method: send

    Sends the email using the mail() function along with all the specified additions
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
    global $func;

    $handled = null;
    api()->run_hooks('php_mail_send_pre', array(&$handled, $to, $subject, $message, $alt_message, $this->options));

    if($handled !== null)
      return !empty($handled);

    // Set some headers...
    if(empty($this->options['headers']['DATE']))
      $this->options['headers']['DATE'] = date('r');

    if(empty($this->options['headers']['CONTENT-TYPE']))
      $this->options['headers']['CONTENT-TYPE'] = (!empty($this->options['is_html']) ? 'text/html' : 'text/plain'). ';charset='. $this->charset;

    if(empty($this->options['headers']['MIME-VERSION']))
      $this->options['headers']['MIME-VERSION'] = '1.0';

    // Any priority?
    if(empty($this->options['headers']['X-PRIORITY']) && isset($this->options['priority']))
      $this->options['headers']['X-PRIORITY'] = $this->options['priority'];

    if(empty($this->headers['X-MS-PRIORITY']) && isset($this->options['priority']))
    {
      if($this->options['priority'] == 1)
        $priority = 'Highest';
      elseif($this->options['priority'] == 2)
        $priority = 'High';
      elseif($this->options['priority'] == 3)
        $priority = 'normal';
      elseif($this->options['priority'] == 4)
        $priority = 'belownormal';
      else
        $priority = 'low';

      $this->options['headers']['X-MS-PRIORITY'] = $priority;
    }

    // Set the X-Mailer, just 'cause we are cool like that!
    $this->options['headers']['X-MAILER'] = 'PHP/'. version(). ' using SnowCMS';

    // Gotta change the Content-Type?
    if($this->options['is_html'])
    {
      // It's a message with multiple parts! (HTML and alternative!)
      $boundary = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_'), 0, 40);
      $this->headers['CONTENT-TYPE'] = 'multipart/alternative; boundary="'. $boundary. '"';

      // No alternative message? That isn't a good idea!
      if(empty($alt_message))
      {
        $alt_message = api()->apply_filters('php_mail_alt_message_create', $message);

        if($alt_message == $message)
          $alt_message = strip_tags($alt_message);
      }
    }

    // Format all them headers right.
    $headers = array();
    foreach($this->headers as $header => $value)
      $headers[] = $header. ': '. $value;

    // Implode! Implode! :0
    $headers = wordwrap(api()->apply_filters('php_mail_headers', implode("\r\n", $headers)), 70);

    // Messages end with a \n not \r\n... Weird.
    $message = wordwrap(str_replace("\r\n", "\n", $message), 70);

    // Just a little Windows fix.
    if(substr(PHP_OS, 0, 3) == 'WIN')
      $message = str_replace("\n.", "\n..", $message);

    // Here we go! Finally!
    if(isset($boundary))
    {
      // Oops, I lied :P
      $alt_message = wordwrap(str_replace("\r\n", "\n", $alt_message), 70);

      if(substr(PHP_OS, 0, 3) == 'WIN')
        $alt_message = str_replace("\n.", "\n..", $alt_message);

      // Put it all together now!
      $body = "--{$boundary}\r\nContent-Type: text/plain; charset={$this->options['charset']}\r\n\r\n{$alt_message}\r\n\r\n";
      $body .= "\r\n--{$boundary}\r\nContent-Type: text/html; charset={$this->options['charset']}\r\n\r\n{$message}\r\n\r\n";
      $body .= "--{$boundary}--\r\n.\r\n";

      $message = api()->apply_filters('php_mail_multipart_body', $body);
    }
    else
      $message = api()->apply_filters('php_mail_message_body', $message);

    $mail = api()->apply_filters('php_mail_function', isset($func['mail']) ? $func['mail'] : 'mail');

    // Now it is time to send it with the appropriate function.
    $sent = $mail($to, $subject, $message, $headers, settings()->get('mail_additional_parameters', 'string', ''));

    $this->close();
    return !empty($sent);
  }

  /*
    Method: close

    Deletes all options and what not.

    Parameters:
      bool $quit - Just for good measure.

    Returns:
      bool - Returns true.
  */
  public function close($quit = false)
  {
    $this->options = array(
                       'headers' => array(),
                       'is_html' => false,
                       'priority' => 3,
                       'charset' => 'utf-8',
                     );

    return true;
  }

  /*
    Method: set_html

    Sets the message to allow HTML.

    Parameters:
      bool $is_html - Whether or not to allow HTML.

    Returns:
      bool - Returns true.
  */
  public function set_html($is_html = true)
  {
    $this->options['is_html'] = !empty($is_html);
    return true;
  }

  /*
    Method: set_priority

    Sets the X-PRIORITY and X-MS-PRIORITY header.

    Parameters:
      int $priority - A number 1 through 5, 1 being the most important
                      and 5 being the least important.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_priority($priority = 3)
  {
    if((string)$priority != (string)(int)$priority || $priority > 5 || $priority < 1)
      return false;

    $this->priority = (int)$priority;
    return true;
  }

  /*
    Method: set_charset

    Sets the character set of the message.

    Parameters:
      string $charset

    Returns:
      bool - Returns true.
  */
  public function set_charset($charset = 'utf-8')
  {
    $this->charset = $charset;
    return true;
  }

  /*
    Method: error

    Just filler.

    Parameters:
      bool $last_error

    Returns:
      bool - Returns false.
  */
  public function error($last_error = true)
  {
    return false;
  }
}
?>