<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                  Admin.php file

if(!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

function Captcha() {
global $source_dir;
  // Amount of characters
  $chars = 6;
  // Width of CAPTCHA
  $width = 150;
  // Height of CAPTCHA
  $height = 60;
  // Possible font families
  $font = array($source_dir.'/fonts/Vera.ttf',$source_dir.'/fonts/VeraBd.ttf',$source_dir.'/fonts/VeraIt.ttf');
  // Average font size
  $fontsize = 18;
  // Salt used in hashing
  $salt = 'salt4me';
  // Possible characters
  $allowed_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  // Amount of lines behind text
  $lines = 25;
  // Furtherest rotation
  $rotate = 30;
  // Get the CAPTCHA code
  $code = '';
  for($i=0;$i<$chars;$i+=1)
    $code .= substr(str_shuffle($allowed_chars),0,1);
  // Save the CAPTCHA code in the session
  $_SESSION['captcha_'.sha1(sha1($salt))] = sha1(strtolower($code).sha1($salt));
  // Start creating the CAPTCHA image
  $image = imagecreate($width,$height);
  // Set the background colour
  $colour = imagecolorallocate($image,255,255,255);
  imagefill($image,0,0,$colour);
  // Draw random lines
  for($i=0;$i<$lines;$i+=1) {
    // Randomly get the shade
    $colour = mt_rand(200-$i*3,255-$i*3);
    // Create the colour based on the shade
    $colour = imagecolorallocate($image,$colour,$colour,$colour);
    // Draw the line
    imageline($image,mt_rand(0,$width/5),mt_rand(0,$height),mt_rand($width-$width/5,$width),mt_rand(0,$height),$colour);
  }
  // Get the distance between each of the characters
  $dist = ($width - 20) / $chars;
  // Draw each letter on the image
  for($i=0;$i<$chars;$i+=1) {
    // Randomly get the shade
    $colour = mt_rand(50,150);
    // Create the colour based on the shade
    $colour = imagecolorallocate($image,$colour,$colour,$colour);
    // Set a random size
    $size = mt_rand($fontsize-4,$fontsize+4);
    // Draw the letter
    imagefttext($image,$size,mt_rand(-$rotate,$rotate),15+$dist*$i,$height/2+$size/2,$colour,$font[mt_rand(0,count($font)-1)],$code[$i]);
  }
  // Empty output buffer
  ob_clean();
  // Send the content type
  header('Content-type: image/jpeg');
  // Send the image as a JPEG
  imagejpeg($image);
  // Unset the CAPTCHA code as a seccurity precaution
  unset($code);
  // Exit as a security precaution
  exit;
}
?>