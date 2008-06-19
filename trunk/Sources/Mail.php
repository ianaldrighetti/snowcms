<?php
//                 SnowCMS
//           By aldo and soren121
//  Founded by soren121 & co-founded by aldo
//    http://snowcms.northsalemcrew.net
//
// SnowCMS is released under the GPL v3 License
// Which means you are free to edit it and then
//       redistribute it as your wish!
// 
//              Mail.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

function SendMail($to, $subject, $msg) {
global $cmsurl, $l, $settings, $user;
  // This function is simple, it is used to send emails, through fsockopen() or mail()
  // First lets see if they want to send email with fsockopen
  if($settings['mail_with_fsockopen']) {
    $smtp_server = fsockopen($settings['smtp_server'], $settings['smtp_port'], $errno, $errstr, 30);
    if(!$server_smtp)
    {
	    // Oh noes! It didn't work D:!
    }
    else {
      fwrite($smtp_server, "AUTH LOGIN");
      fwrite($smtp_server, base64_encode($settings['smtp_email']));
      fwrite($smtp_server, base64_encode($settings['smtp_pass']));
      fwrite($smtp_server, "MAIL FROM:<".$settings['smtp_email'].">\r\n");
      fwrite($smtp_server, "RCPT TO:<".$to.">\r\n");
      fwrite($smtp_server, "DATA\r\n");
      fwrite($smtp_server, "Received: from mydomain.com by hisdomain.com ; ".formattime()."\r\n");
      fwrite($smtp_server, "Date: ".formattime()."\r\n");
      fwrite($smtp_server, "From: {$settings['site_name']} <{$settings['smtp_email']}>\r\n");
      fwrite($smtp_server, "Subject: {$subject}\r\n");
      fwrite($smtp_server, "To: {$to}\r\n");
      fwrite($smtp_server, $msg);
      fwrite($smtp_server, ".\r\nQUIT\r\n");
    }
  }
  else {
    // this means we can send with the mail() function! Weeeee!
  }
}
?>