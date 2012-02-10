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
		<h3><img src="', theme()->url(), '/style/images/members_permissions-small.png" alt="" /> ', l('Managing %s Permissions', api()->context['group_name']), '</h3>
		<p>', l('Select the permissions you would like to assign to the member group below. <em>Deny</em> means the permission will be denied for the member even if another group they are assigned to allows the permission. <em>Disallow</em> means the permission is not allowed by members of the group, unless another group they are assigned to allows that permission.'), '</p>';

if(!empty(api()->context['message']))
{
	echo '
		<div class="message-box">
			<p>', api()->context['message'], '</p>
		</div>';
}

echo '
		', api()->context['form']->open(api()->context['group_id']. '_permissions');

// Since we aren't using the built-in method to render the form, we will
// have to do it ourself, but that's the price of having a custom layout.
foreach(api()->context['permissions'] as $permission)
{
	echo '
		<div class="permission-table">
			<p class="permission-header">', htmlchars($permission['label']), (!empty($permission['subtext']) ? ' <span class="permission-subtext">'. htmlchars($permission['subtext']). '</span>' : ''), '</p>';

	foreach($permission['permissions'] as $perm)
	{
		echo '
			<div class="float-left">
				<p class="permission-label">', htmlchars($perm['label']), '</p>', !empty($perm['subtext']) ? '
				<p class="permission-label-subtext">'. htmlchars($perm['subtext']). '</p>' : '', '
			</div>
			<div class="float-right">
				<p>', api()->context['form']->input($perm['id'])->generate(), '</p>
			</div>
			<div class="break">
			</div>';
	}

	echo '
		</div>';
}

echo '
			<p class="center"><input type="submit" name="', api()->context['group_id'], '_permissions" value="', l('Update'), '" /></p>
		', api()->context['form']->close(api()->context['group_id']. '_permissions');
?>