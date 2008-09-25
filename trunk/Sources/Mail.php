<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//          redistribute it as your wish!
//
//                   Mail.php file


if(!defined("Snow"))
  die("Hacking Attempt...");

function SendMail($to, $subject, $msg, $is_html = false) {
global $cmsurl, $l, $settings, $source_dir, $user;
  // This function is simple, it is used to send emails, through fsockopen() or mail()
  // First lets see if they want to send email with fsockopen
  if($settings['mail_with_fsockopen']) {
    require($source_dir."/PHPMailer.php");

    $mail = new PHPMailer();
    
    // We are sending as SMTP, set the host, SMTP user and password
    $mail->IsSMTP();
    $mail->PluginDir($source_dir);
    $mail->Host = $settings['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp_user'];
    $mail->Password = $settings['smtp_pass'];

    // Set from, from name, and to who
    $mail->From = $settings['from_email'];
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
      $info['msg'] = str_replace('%error%', $mail->ErrorInfo, $l['mail_smtp_fail']);
      $info['error'] = true;
      return $info;
    }
    else {
      $info['msg'] = $l['mail_smtp_success'];
      $info['error'] = false;
      return $info;
    }
  }
  else {
    // Set the headers, such as where from, and Reply too
    $header = "From: ".$settings['from_email']."\r\nReply-To: ".$settings['from_email']."\r\n";
    // Send the email
    @$mail = mail($to, $subject, $msg, $header);
    // Was it successful or not?
    if(!$mail) {
      $info['msg'] = $l['mail_mail_fail'];
      $info['error'] = true;
      return $info;
    }
    else {
      $info['msg'] = $l['mail_mail_success'];
      $info['error'] = false;
      return $info;
    }
  }
}
?>