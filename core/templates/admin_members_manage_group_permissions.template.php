<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

			echo '
		<h3><img src="', theme()->url(), '/style/images/members_permissions-small.png" alt="" /> ', l('Managing %s Permissions', api()->context['group_name']), '</h3>
		<p>', l('Select the permissions you would like to assign to the member group below. <em>Deny</em> means the permission will be denied for the member even if another group they are assigned to allows the permission. <em>Disallow</em> means the permission is not allowed by members of the group, unless another group they are assigned to allows that permission. <a href="%s" title="Back to Manage Permissions">Back to permissions management &raquo;</a>', baseurl. '/index.php?action=admin&amp;sa=members_permissions'), '</p>';

			api()->context['form']->render(api()->context['group_id']. '_permissions');
?>