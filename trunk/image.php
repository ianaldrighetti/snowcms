<?php
session_start();
error_reporting(E_ALL);
define("Snow", true);
require_once('config.php');
// include captcha class
require_once($source_dir.'/Captcha.php');
/* 
  This is the CAPTCHA Image for registering
  This file may disappear one day D: but of
  course CAPTCHA will still be in SnowCMS
  but in a different way :P
*/

// define fonts
$aFonts = array($source_dir.'/fonts/VeraBd.ttf', $source_dir.'/fonts/VeraIt.ttf', $source_dir.'/fonts/Vera.ttf');
$captcha = new PhpCaptcha($aFonts, 200, 60);
$captcha->UseColour(false);
$captcha->SetNumChars(6);
$captcha->Create();
?>
