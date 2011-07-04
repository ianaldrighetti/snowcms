<?php
if(!defined('INSNOW'))
{
  die('Nice try...');
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php theme_title(); ?></title>
	<?php theme_head(); ?>
</head>
<body>
  <div id="wrapper">
    <div id="header">
      <h2><?php theme_site_name(); ?></h2>
      <p><?php theme_sub_title(); ?></p>
      <div id="menu-outer">
        <div id="menu-inner">
          <ul id="menu">
<?php theme_menu(); ?>
          </ul>
        </div>
      </div>
    </div>
    <div id="content">