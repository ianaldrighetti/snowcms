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
  Class: Mail

  Another one of the numerous tools available in SnowCMS, the Mail class
  handles emails. It is quite simple to use, and internally uses either
  the mail() function or SMTP, but you don't have to worry about that!
*/
class Mail
{
  // Variable: handler
  // Contains the class which handles the sending of emails.
  private $handler;

  // Variable: options
  // Holds any options for email sending.
  private $options;

  /*
    Constructor: __construct
  */
  public function __construct()
  {
    api()->run_hooks('mail_construct');

    // SMTP or plain ol' mail()?
    if(strtolower(settings()->get('mail_handler', 'string')) == 'smtp')
    {
      if(!class_exists('SMTP'))
      {
        require_once(coredir. '/smtp.class.php');
      }

      $this->handler = new SMTP();
    }
    else
    {
      if(!class_exists('PHP_Mail'))
      {
        require_once(coredir. '/php_mail.class.php');
      }

      $this->handler = new PHP_Mail();
    }

    $this->options = array(
                       'headers' => array(),
                       'from' => settings()->get('site_email', 'string'),
                       'is_html' => false,
                       'priority' => 3,
                       'charset' => 'utf-8',
                     );
  }

  /*
    Method: set_from

    Sets the MAIL FROM header, which tells the server who
    the email is sent from.

    Parameters:
      string $from - The email address the mail message should be marked "from".

    Returns:
      void - Nothing is returned by this method.

    Note:
      $from MUST be ONLY an email address, you cannot supply My Name <address@host.com>,
      as that is not how the MAIL FROM header is setup. However, if you so please, you
      can set the FROM header in that format using <Mail::add_header>.
  */
  public function set_from($from)
  {
    $this->options['from'] = $from;
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
        $mail->add_header(array('FROM', 'X-PRIORITY'), array('Me <my@address>', 5));
  */
  public function add_header($header, $value)
  {
    if(is_array($header) && is_array($value))
    {
      // Not the same amount in both? That won't work.
      if(count($header) != count($value) || count($header) == 0)
      {
        return false;
      }

      foreach($header as $key => $val)
      {
        // Woo for easiness :)
        $this->add_header($val, $value[$key]);
      }

      return true;
    }
    else
    {
      $this->options['headers'][strtoupper($header)] = $value;
      return true;
    }
  }

  /*
    Method: send

    Sends the email along with all the specified additions, such as headers.

    Parameters:
      mixed $to - This can be either an array of addresses, a string containing
                  one address or a string containing multiple addresses separated
                  by commas.
      string $subject - The subject of the email message.
      string $message - The email message to send.
      string $alt_message - If HTML is set to true, this can be set so if the
                            receivers email client has HTML disabled or not supported.
                            You can turn on HTML via <Mail::set_html>.
      array $options - Any extra options, this is for use by plugins.

    Returns:
      bool - Returns true if the message was sent without issue, false otherwise.

    Note:
      Just because this method returned true does not mean all receipients (if any)
      actually received the email message.

      If HTML is set to true and no alternative message is supplied, the $message
      parameter will be sent as the alternative message, just all the HTML stripped
      using strip_tags.

  */
  public function send($to, $subject, $message, $alt_message = null, $options = array())
  {
    // Do you have anything you want to do? Like, oh, say a mail queue, that way many
    // emails can be sent without overloading the server or the remote server, and you
    // know, you could use the $options array to then set a value of override to true
    // and then you would ignore the email request and the email would be sent once the
    // email actually gets sent through an automated task (<Tasks>). But, you know, you
    // didn't hear it from me! :-)
    $handled = null;
    api()->run_hooks('mail_send', array(&$handled, $to, $subject, $message, $alt_message, $options, $this->handler, $this->options));

    if($handled === null)
    {
      // Using SMTP? We got a couple SPECIAL things we got to do.
      if(settings()->get('mail_handler', 'string') == 'smtp')
      {
        $connected = $this->handler->connect(settings()->get('smtp_host', 'string', 'localhost'), settings()->get('smtp_port', 'int', 25), settings()->get('smtp_is_tls', 'bool', false), settings()->get('smtp_timeout', 'int', 5));

        // Did we connect..?
        if(empty($connected))
        {
          return false;
        }

        // Authenticate!
        $auth = $this->handler->authenticate(settings()->get('smtp_user', 'string'), settings()->get('smtp_pass', 'string'));

        // Did we successfully authenticate?
        if(empty($auth))
        {
          return false;
        }
      }

      // Set the FROM address.
      $success = $this->handler->set_from(!empty($this->options['from']) ? $this->options['from'] : settings()->get('site_email', 'string'));

      if(empty($success))
      {
        return false;
			}

      // Should the message be HTML?
      $this->handler->set_html(!empty($this->options['is_html']));

      // Priority :)
      $this->handler->set_priority(!empty($this->options['priority']) ? $this->options['priority'] : 3);

      // Character set... Yippe?
      $this->handler->set_charset(!empty($this->options['charset']) ? $this->options['charset'] : 'utf-8');

      // Add any headers, that is, if there are any.
      if(isset($this->options['headers']) && count($this->options['headers']) > 0)
      {
        $this->handler->add_header(array_keys($this->options['headers']), array_values($this->options['headers']));
      }

      // Alrighty then! Send the message and we are DONE!
      $handled = $this->handler->send($to, $subject, $message, $alt_message);

      if(!empty($handled))
      {
        $this->close();
      }
    }

    return !empty($handled);
  }

  /*
    Method: close

    Resets all options

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.
  */
  public function close()
  {
    $this->options = array(
                       'headers' => array(),
                       'from' => settings()->get('site_email', 'string'),
                       'is_html' => false,
                       'priority' => 3,
                       'charset' => 'utf-8',
                     );
  }

  /*
    Method: set_html

    Sets the message to allow HTML or not.

    Parameters:
      bool $is_html - Whether or not to allow HTML.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_html($is_html = true)
  {
    $this->options['is_html'] = !empty($is_html);
  }

  /*
    Method: set_priority

    Sets the X-PRIORITY and X-MS-PRIORITY header.

    Parameters:
      int $priority - A number 1 through 5, 1 being the most important
                      and 5 being being the least important.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_priority($priority = 3)
  {
    if((string)$priority != (string)(int)$priority || $priority > 5 || $priority < 1)
    {
      return false;
    }

    $this->options['priority'] = (int)$priority;
    return true;
  }

  /*
    Method: set_charset

    Sets the character set of the message.

    Parameters:
      string $charset

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_charset($charset = 'utf-8')
  {
    if(empty($charset))
    {
      return false;
    }

    $this->options['charset'] = $charset;
    return true;
  }

  /*
    Method: error

    Allows access to the errors from the email handler.

    Parameters:
      bool $last_error - Whether or not to return the last message, and the
                         last message only. If false, all error messages are
                         returned, however, if there are none, false is returned.

    Returns:
      mixed - Returns the last error message (if $last_error is true), an array
              or false if there are no messages.
  */
  public function error($last_error = true)
  {
    return $this->handler->error($last_error);
  }
}
?>