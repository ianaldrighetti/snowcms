<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<h3><img src="', theme()->url(), '/style/images/members_add-small.png" alt="" /> ', l('Add a new member'), '</h3>
	<p>', l('If registration is enabled, guests on your site can create their own account, but if registration is not open someone will have to create an account for them.'), '</p>';

		api()->context['form']->render('admin_members_add_form');
?>