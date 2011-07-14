<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<h1><img src="', theme()->url(), '/style/images/members_add-small.png" alt="" /> ', l('Add a new member'), '</h1>
	<p>', l('If registration is enabled, guests on your site can create their own member, but if you need to create a new member, you can do so here.'), '</p>';

		api()->context['form']->show('admin_members_add_form');
?>