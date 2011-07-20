<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<h3><img src="', theme()->url(), '/style/images/members_manage-small.png" alt="" /> ', l('Manage Members'), '</h3>
	<p>', l('All existing members can be managed here, such as editing, deleting, and approving accounts.'), '</p>';

		api()->context['table']->show('admin_members_manage_table');
?>