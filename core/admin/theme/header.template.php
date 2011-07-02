<?php
if(!defined('IN_SNOW'))
{
  die('Nice try...');
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <style type="text/css">
    *
    {
      margin: 0px;
    }

    img
    {
      border: none;
    }

    body
    {
      background: #FFFFFF;
      font-family: Tahoma, Arial, sans-serif;
      font-size: 90%;
    }

    input[type="text"], input[type="password"], textarea
    {
      font-family: Tahoma, Arial, sans-serif;
      border: 1px solid #AAAAAA;
      padding: 2px;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
    }

    textarea
    {
      font-size: 90%;
    }

    #header
    {
      width: 100%;
      height: 70px;
      background: #3465A7;
    }

    #header #container
    {
      width: 800px;
      margin: 0px auto;
    }

    #header #container #text
    {
      float: left;
      padding-top: 10px;
    }

    #header #container #text h1
    {
      color: #FFFFFF;
      font-weight: normal;
      font-size: 150%;
    }

    #header #container #text h1 .visit_site a
    {
      font-size: 12px;
      color: #DDDDDD;
      text-decoration: none;
    }

    #header #container #text h1 .visit_site a:hover
    {
      color: #FFFFFF;
    }

    #header #container #text h3
    {
      font-size: 90%;
      font-weight: normal;
      color: #FFFFFF;
    }

    #header #container #member_info
    {
      float: right;
      width: 200px;
      margin-top: 10px;
      padding: 8px;
      color: #FFFFFF;
      background: #729FCF;
    }

    #header #container #member_info a
    {
      color: #FFFFFF;
      text-decoration: none;
    }

    #header #container #member_info a:hover
    {
      text-decoration: underline;
    }

    #header #container #member_info .links
    {
      font-size: 90%;
    }

    #content
    {
      width: 800px;
      margin: 20px auto 10px auto;
      padding-bottom: 10px;
      border-bottom: 1px solid #DDDDDD;
      font-size: 90%;
    }

    #content h1
    {
      font-size: 125%;
      color: #3465A7;
      margin-top: 15px;
    }

    #content h3
    {
      font-size: 110%;
      color: #AAAAAA;
      margin-top: 15px;
    }

    #content ul
    {
      margin: 5px auto;
      padding-left: 20px;
    }

    #content #sidebar
    {
      float: left;
      width: 180px;
    }

    #content #sidebar h3
    {
      margin-top: 10px;
      font-size: 105%;
      color: #A9A9A9;
      border-bottom: 1px solid #DDDDDD;
      padding-bottom: 3px;
    }

    #content #sidebar h3 a
    {
      color: #A3A3A3;
      text-decoration: none;
    }

    #content #sidebar .news_subject
    {
      font-weight: bold;
      font-size: 90%;
    }

    #content #sidebar .news_content
    {
      font-size: 90%;
      margin-bottom: 5px;
    }

    #content #sidebar .notification
    {
      font-size: 90%;
    }

    #content #main
    {
      float: right;
      width: 590px;
    }

    #content .theme_list
    {
      text-align: center;
    }

    #content .theme_list a
    {
      display: block;
      width: 250px;
      height: 150px;
      padding: 5px;
      text-align: center;
      font-size: 90%;
      text-decoration: none;
      border: 1px solid #FFFFFF;
      color: #000000;
    }

    #content .theme_list a:hover, #content .theme_list .selected
    {
      -moz-border-radius: 5px;
      -webkit-border-radius: 5px;
      border-radius: 5px;
      border: 1px solid #C9C9C9;
      background: #EDEDED;
    }

    #content .theme_list .button
    {
      display: inline;
      border: 1px solid #FFFFFF;
      background: transparent;
      margin-top: 10px;
      margin-left: 5px;
    }

    #content .theme_list .important
    {
			-moz-border-radius: 5px;
			-webkit-border-radius: 5px;
			border-radius: 5px;
			border: 1px solid #CD0000;
			background: #FFC1C1;
    }

    #content .theme_list .important:hover
    {
			border-color: #CD0000;
			background: #FF6A6A;
    }

    #content #main .icons a
    {
      display: block;
      width: 80px;
      height: 80px;
      padding: 5px;
      text-align: center;
      font-size: 70%;
      text-decoration: none;
      border: 1px solid #FFFFFF;
      color: #000000;
    }

    #content .icons a:hover
    {
      -moz-border-radius: 5px;
      -webkit-border-radius: 5px;
      border-radius: 5px;
      border: 1px solid #C9C9C9 !important;
      background: #EDEDED;
    }

    #footer
    {
      width: 800px;
      margin: 0px auto;
      color: #888A85;
      font-size: 75%;
    }

    #footer a
    {
      color: #3465A7;
      text-decoration: none;
    }

    #footer a:hover
    {
      text-decoration: underline;
    }

    #version
    {
      float: left;
    }

    #jump_to
    {
      float: right;
    }

    .break
    {
      clear: both;
    }

    .form fieldset
    {
      border: none;
    }

    .form table
    {
      margin: auto;
      text-align: center !important;
    }

    .form .td_left
    {
      width: 75%;
      text-align: left !important;
      padding: 5px 0px;
    }

    .form .td_right
    {
      width: 25%;
      text-align: center !important;
      padding: 5px 0px;
    }

    .form .label
    {
      font-weight: bold;
      font-size: 110%;
    }

    .form .subtext
    {
      font-size: 85%;
    }

    .form .buttons
    {
      text-align: center !important;
    }

    .form .errors
    {
      margin: 15px 10px 15px 10px;
      padding: 5px;
      background: #FFCCCC;
      border: 2px solid #EE0000;
      text-align: center;
      color: #000000;
    }

    .form .message
    {
      margin: 15px 10px 15px 10px;
      padding: 5px;
      background: #A6D785;
      border: 2px solid #458B00;
      text-align: center;
      color: #000000;
    }

    .table
    {
      width: 100% !important;
      margin: 5px auto 5px auto;
    }

    .table .header
    {
      text-align: right !important;
    }

    .table th
    {
      text-align: left !important;
    }

    .table .columns
    {
      background: #3465A7;
      color: #FFFFFF;
    }

    .table .columns a
    {
      color: #FFFFFF;
      text-decoration: none;
    }

    .table .columns a:hover
    {
      text-decoration: underline;
    }

    .table .tr_0
    {
      background: #E6E8FA;
    }

    .table .tr_1
    {
      background: #F5F5F5;
    }

    .table .options
    {
      text-align: right !important;
    }

    .table .filters
    {
      text-align: right !important;
    }

    .pagination
    {
      font-size: 80%;
    }

    .bold
    {
      font-weight: bold;
    }

    .red
    {
      color: red !important;
    }

    .green
    {
      color: green !important;
    }
  </style>
  <title><?php theme_title(); ?></title>
  <?php theme_head(); ?>
</head>
<body>
<div id="header">
  <div id="container">
    <div id="text">
      <h1><?php echo settings()->get('site_name', 'string'); ?> <span class="visit_site"><a href="<?php echo baseurl; ?>" title="<?php echo l('Visit site'); ?>">&laquo; <?php echo l('Visit site'); ?> &raquo;</a></span></h1>
      <h3><?php echo l('Control Panel'); ?></h3>
    </div>

    <div id="member_info">
      <p><?php echo l('Hello, <a href="%s" title="View your profile">%s</a>.', baseurl. '/index.php?action=profile', member()->display_name()); ?></p>
      <p class="links"><?php echo l('<a href="%s" title="Go to the Control Panel Home">Control Panel</a> | <a href="%s" title="Log out of your account">Log out</a>', baseurl. '/index.php?action=admin', baseurl. '/index.php?action=logout&amp;sc='. member()->session_id()); ?></p>
    </div>
    <div class="break">
    </div>
  </div>
</div>
<div id="content">