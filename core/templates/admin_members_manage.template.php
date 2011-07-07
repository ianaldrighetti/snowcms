<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

    echo '
  <h1><img src="', theme()->url(), '/style/images/members_manage-small.png" alt="" /> ', l('Manage Members'), '</h1>
  <p>', l('All existing members can be managed here, such as editing, deleting, approving, etc.'), '</p>';

    api()->context['table']->show('admin_members_manage_table');
?>