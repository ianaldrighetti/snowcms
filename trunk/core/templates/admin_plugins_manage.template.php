<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<h1><img src="', theme()->url(), '/style/images/plugins_manage-small.png" alt="" /> ', l('Manage plugins'), '</h1>
	<p>', l('Manage your current plugins.'), '</p>';

		api()->context['table']->show('manage_plugins_table');
?>