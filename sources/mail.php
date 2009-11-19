<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# Does the mail thing...
#
# bool mail_send(string $to, string $subject, string $message[, bool $is_html = false[, int $word_wrap = 80[, int $priority = 3[, bool $override_queue = false]]]]);
#   string $to - The emails of which the message will be sent to, if more then one
#                email is needed, simply separate them with semi-colons
#   string $subject - The subject of the email
#   string $message - The message that will be sent to the emails given
#                     NOTE: When HTML is used and $is_html is true, the alternate body will have
#                           its tags stripped
#   bool $is_html - Whether or not the email is HTML, default is false
#   int $word_wrap - Wraps the message to the given number of characters, default is 80
#   int $priority - The priority of the email from 1 to 5, 5 being highest priority and 1 being the
#                   lowest. By default it is set to 3, which is medium. NOTE: This parameter is ignored
#                   if the Mail Queue is disabled in the Control Panel.
#   bool $override_queue - If you want the emb_send_mail(s) to be sent instantly without being added to the Mail
#                          Queue simply set this to true, however if it is false and if the Mail Queue is
#                          enabled the email will be sent once the emails have been emailed via the Queue.
#   returns bool - If true is returned then sending the email was successful, otherwise it failed,
#                  however if $override_queue is false it will return true as long as the emb_send_mail(s)
#                  were added to the Mail Queue, if enabled
#
# bool mail_queue_add($to, $subject, $message, $is_html = false, $word_wrap = 80, $priority = 3);
#   string $to - The email addresses that will eventually get the email, if you want to have more
#                then one email, separate by semicolons (;)
#   string $subject - The subject of the email
#   string $message - The actual email body, but if you want to use HTML be sure is_html is true
#   bool $is_html - Whether or not the message is HTML or just plain text
#   int $word_wrap - Wraps the message to the given number of characters, default is 80
#   int $priority - The priority of the email over another, 1 the least important 5 is the most
#   returns bool - Returns whether or not the email was successfully added. If multiple addresses
#                  are supplied it returns true if at least one is successfully added. :P
#   NOTE: You DO NOT need to use this function EVER! This function is automatically called upon when
#         you use the sendmb_send_mail() function, so DO NOT use this for really any reason, you can if you want
#         I suppose but you shouldn't have a need to ;)
#
# void mail_queue_send([int $num_to_send = 0]);
#   int $num_to_send - The number of emails to send, if set to 0 this will go by the settings
#                      but if you set this yourself the function will send as many emails as
#                      supplied, which overrides all settings
#   returns nothing - This function returns absolutely nothing.
#


function mail_send($to, $subject, $message, $is_html = false, $word_wrap = 80, $priority = 3, $override_queue = false)
{
  global $l, $settings, $source_dir;

  # So we need to add this to the mail queue..?
  # Only if its on and if the option isn't overridden
  if($settings['enable_mail_queue'] && !$override_queue)
    # Yup, add it ;)
    return mail_queue_add($to, $subject, $message, $is_html, $word_wrap, $priority);
  else
  {
    # No Mail Queue... perhaps not enabled, overridden
    # OR even being used by the Mail Queue to send emails XD!

    # So are we using PHP Mail or SMTP?
    if($settings['mail_type'] == 0)
    {
      # Using the mail function, the multibyte one though!
      # Word wrap at 70 characters per line.
      $message = wordwrap($message, 70);

      # New lines are separated with \n not \r\n ;)
      $message = str_replace("\r\n", "\n", $message);

      # A little funky fix for Windows.
      if(mb_substr(PHP_OS, 0, 3) == 'WIN')
        $message = str_replace("\n.", "\n..", $message);

      # To an array..?
      if(is_array($to))
        $to = '<'. implode('>, <', $to). '>';

      # Now send it :)
      $additional_headers = "From: \"{$settings['site_name']}\" <{$settings['site_email']}>\r\n";
      $additional_headers .= "To: $to\r\n";

      # But wait, is it HTML?
      if(!empty($is_html))
      {
        $additional_headers .= "MIME-Version: 1.0\r\n";
        $additional_headers .= "Content-type: text/html; charset=utf-8\r\n";
      }

      $mail_sent = mb_send_mail($to, $subject, $message, $additional_headers);

      # Can't be too specific. Lol...
      $mail_error = (!empty($mail_sent) ? 'Not an error' : 'Failed to send the email with mail(); Be sure everything is properly configured.');
    }
    else
    {
      # Its SMTP ;)
      require_once($source_dir. '/SMTP.class.php');

      $smtp = new SMTP($settings['smtp_host'], $settings['smtp_port'], !empty($settings['smtp_is_tls']), 5);

      # Authenticate...
      $smtp->auth($settings['smtp_user'], $settings['smtp_pass']);
      $smtp->mail_from($settings['site_email']);

      if(!empty($is_html))
      {
        $alt_message = strip_tags($message);
        $smtp->set_html();
      }

      # Now send the email... Let's hope this works..!
      $additional_headers['from'] = '"'. $settings['site_name']. '" <'. $settings['site_email']. '>';
      $mail_sent = $smtp->send($to, $subject, $message, !empty($alt_message) ? $alt_message : '', $additional_headers);

      # Just incase an error occurred :P
      $mail_error = implode(', ', $smtp->error(false));
    }

    # Now check and see if sending the email was a succes or not...
    if(empty($mail_sent))
    {
      # It wasn't a success ):
      # So log the error.
      snow_error(E_USER_ERROR, $mail_error);

      return false;
    }
    else
      # It was a success!
      return true;
  }
}

function mail_queue_add($to, $subject, $message, $is_html = false, $word_wrap = 80, $priority = 3)
{
  global $db, $settings;

  # Lets see, we need to add these items to the queue ;)
  # If the queue is enabled of course.
  if(!empty($settings['enable_mail_queue']))
  {
    # Multiple addresses..? We want individual rows :P
    if(mb_substr_count($to, ';')) {
      # Multiple... This will be easy =D
      $addresses = @explode(';', $to);

      # But sadly we can only do the success thing partially
      # so if just one is a success, this whole thing is considered
      # a success...
      $success = false;

      foreach($addresses as $address)
      {
        # Oh yeah B) Using the same function we are in! :D
        $address = trim($address);
        $added = false;

        if(!empty($address))
          $added = mail_queue_add($address, $subject, $message, $is_html, $word_wrap, $priority);
        if($added)
          # Change it to a success.
          $success = true;
      }

      # Now return whether it was a success.
      return $success;
    }
    else
    {
      # Just a single address...
      # So sanitize and insert the data.
      $to = $db->escape(trim($to));
      $subject = $db->escape($subject);
      $message = $db->escape($message);

      # Is it HTML?
      $is_html = !empty($is_html) ? 1 : 0;

      # The word wrap, but lets be sure that its not to big or to small ;)
      $word_wrap = (int)($word_wrap > 255 ? 255 : ($word_wrap < 15 ? 15 : $word_wrap));

      # Priority, only between 1 and 5!
      $priority = (int)($priority > 5 ? 5 : ($priority < 1 ? 1 : $priority));

      # Now add the email to the queue
      $added = $db->insert('insert', $db->prefix. 'mail_queue',
        array(
          'time_added' => 'int', 'to_address' => 'string', 'subject' => 'string',
          'message' => 'text', 'is_html' => 'int', 'priority' => 'int', 'word_wrap' => 'int',
        ),
        array(
          time_utc(), $to, $subject,
          $message, $is_html, $priority, $word_wrap,
        ),
        array());
      # Was it a success..?
      return (bool)$added;
    }
  }
}

function mail_queue_send($num_to_send = 0)
{
  global $db, $settings;

  # You can't stop me coppers!
  ignore_user_abort(true);

  # Only if the mail queue is enabled :)
  if(!empty($settings['enable_mail_queue']))
  {
    # So do you have a custom amount..?
    $num_to_send = (int)$num_to_send;
    if($num_to_send <= 0)
      # Nope...
      $num_to_send = (int)$settings['mail_queue_num_send'];

    # So lets get the number you wanted and then send them :)
    $result = $db->query("
      SELECT
        q.mail_id, q.time_added, q.to_address, q.subject, q.message,
        q.is_html, q.priority, q.word_wrap, q.attempted_times
      FROM {$db->prefix}mail_queue AS q
      ORDER BY q.priority, q.time_added DESC, q.attempted_times ASC
      LIMIT %num_to_send",
      array(
        'num_to_send' => array('int', $num_to_send),
      ));

    # An array to hold successful email sending
    $completed = array();

    # Failed ones need live too :(
    $failed = array();
    while($row = $db->fetch_assoc($result))
    {
      # Send it...
      if(sendmb_send_mail($row['to_address'], $row['subject'], $row['message'], $row['is_html'], $row['word_wrap'], $row['priority'], true))
        # Success! :)
        $completed[] = $row['mail_id'];
      else
        # Failed :|
        $failed[] = $row['mail_id'];
    }

    # So now we can remove those emails that were sent successfully, if they
    # failed, leave them, who knows, maybe later? =P
    if(count($completed))
    {
      # Ok, Delete them ;)
      $db->query("
        DELETE FROM {$db->prefix}mail_queue
        WHERE mail_id IN(%delete_rows)", 
        array(
          'delete_rows' => array('int_array', $completed),
        ));
    }

    # Failed ones..?
    if(count($failed))
    {
      # +1 to to attempted_times
      $db->query("
        UPDATE {$db->prefix}mail_queue
        SET attempted_times = attempted_times + 1
        WHERE mail_id IN($update_rows)",
        array(
          'update_rows' => array('int_array', $failed),
        ));
    }
  }
}
?>