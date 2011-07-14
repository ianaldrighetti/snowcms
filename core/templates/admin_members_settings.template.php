<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<h1><img src="', theme()->url(), '/style/images/members_settings-small.png" alt="" /> ', l('Member settings'), '</h1>
	<p>', l('Member settings can be managed here, which includes setting the registration mode, or disabling it all together.'), '</p>';

		api()->context['form']->show('admin_members_settings_form');
?>