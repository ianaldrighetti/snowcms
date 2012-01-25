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

echo '
	<div class="section-tabs">
		<ul>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_add'), '" title="', l('Add a new member'), '" class="first">', l('Add Member'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_manage'), '" title="', l('Manage existing members'), '">', l('Manage Members'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_settings'), '" title="', l('Manage member registration and account settings'), '">', l('Member Settings'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_permissions'), '" title="', l('Manage member group permissions'), '" class="selected">', l('Manage Permissions'), '</a></li>
		</ul>
		<div class="break">
		</div>
	</div>
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