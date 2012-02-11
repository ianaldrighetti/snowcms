<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

if(!defined('INSNOW'))
{
	die('Nice try...');
}

admin_section_menu('members', 'members_permissions');

echo '
	<h3><img src="', theme()->url(), '/style/images/members_permissions-small.png" alt="" /> ', l('Manage Permissions'), '</h3>
	<p>', l('Each member group can have their own unique permissions assigned to them, which then users of the group inherit.'), '</p>

	<div class="table">
		<table width="100%" cellspacing="0" cellpadding="4px">
			<tr class="columns">
				<th width="50%">Name</th>
				<th>Denied</th>
				<th>Disallowed</th>
				<th>Allowed</th>
				<th>Members</th>
			</tr>';

foreach(api()->context['group_list'] as $group)
{
	echo '
			<tr class="tr_', ($group['position'] % 2), '">
				<td>', (!empty($group['href']) ? '<a href="'. $group['href']. '" title="'. l('Manage permissions for the group &quot;%s&quot;', $group['name']). '">' : '<span title="'. l('This group&#039;s permissions cannot be modified'). '">'), $group['name'], (!empty($group['href']) ? '</a>' : '</span>'), '</td>
				<td class="center">', $group['assigned']['deny'], '</td>
				<td class="center">', $group['assigned']['disallow'], '</td>
				<td class="center">', $group['assigned']['allow'], '</td>
				<td class="center">', $group['members'], '</td>
			</tr>';
}

echo '
		</table>
	</div>';
?>