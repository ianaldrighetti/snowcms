<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

			echo '
		<h1><img src="', theme()->url(), '/style/images/members_permissions-small.png" alt="" /> ', l('Managing %s permissions', api()->return_group($group_id)), '</h1>
		<p>', l('Changes to member groups permissions can be applied here. If deny is selected, no matter what other groups the member may be in, the permission will be denied. If disallow is selected and another one of the member groups they are in allows the permission, the disallow will be overridden. <a href="%s" title="Back to Manage Permissions">Back to Manage Permissions</a>.', baseurl. '/index.php?action=admin&amp;sa=members_permissions'), '</p>';

			api()->context['form']->show(api()->context['group_id']. '_permissions');
?>