<?php
session_start();
error_reporting(E_ALL);
define("Snow", true);
require('config.php');

// include captcha class
require($source_dir.'/Captcha.php');
// define fonts
$aFonts = array($source_dir.'/fonts/VeraBd.ttf', $source_dir.'/fonts/VeraIt.ttf', $source_dir.'/fonts/Vera.ttf');
$captcha = new PhpCaptcha($aFonts, 200, 60);
$captcha->UseColour(false);
$captcha->SetNumChars(6);
$captcha->Create();
?>
