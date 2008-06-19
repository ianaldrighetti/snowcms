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

function SendMail($to, $subject, $msg, $is_html = false) {
global $cmsurl, $l, $settings, $user;
  // This function is simple, it is used to send emails, through fsockopen() or mail()
  // First lets see if they want to send email with fsockopen
  if($settings['mail_with_fsockopen']) {
    require($source_dir."/PHPMailer.php");

    $mail = new PHPMailer();
    
    // We are sending as SMTP, set the host, SMTP user and password
    $mail->IsSMTP();
    $mail->Host = $settings['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp_user'];
    $mail->Password = $settings['smtp_pass'];

    // Set from, from name, and to who
    $mail->From = $settings['smtp_from'];
    $mail->FromName = $settings['site_name'];
    $mail->AddAddress($to);

    // Set the word wrap, and is this HTML or not?
    $mail->WordWrap = 50;
    $mail->IsHTML($is_html);
    
    // Set Subject and Message
    $mail->Subject = $subject;
    $mail->Body = $msg;
    
    // Was it successful or not?
    if(!$mail->Send()) {
      return str_replace('%error%', $mail->ErrorInfo, $l['mail_smtp_fail']);
    }
    else {
      return $l['mail_smtp_success'];
    }
  }
  else {
    // Set the headers, such as where from, and Reply too
    $headers = "From: {$settings['webmaster_email']}\r\nReply-To: {$settings['webmaster_email']}".
    // Send the email
    $mail = mail($to, $subject, $msg, $headers);
    // Was it successful or not?
    if(!$mail) {
      return $l['mail_mail_fail'];
    }
    else {
      return $l['mail_mail_success'];
    }
  }
}
?>