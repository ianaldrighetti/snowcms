<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<h1><img src="', theme()->url(), '/style/images/settings-small.png" alt="" /> ', l('System Settings'), '</h1>
	<p>', l('Manage some basic, though core, system settings including your sites name, email address, and so forth.'), '</p>';

		api()->context['form']->show('admin_settings_form');
?>