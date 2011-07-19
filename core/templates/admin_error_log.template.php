<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<h3><img src="', theme()->url(), '/style/images/error_log-small.png" alt="" /> ', l('Error Log'), '</h3>
	<p>', l('The error log contains all errors generated from the website. When error logging by the system is enabled, users browsing the website will not be able to see these errors, which can lead to security issues.'), '</p>';

		api()->context['table']->show('error_log');
?>