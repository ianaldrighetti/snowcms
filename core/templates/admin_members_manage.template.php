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
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_manage'), '" title="', l('Manage existing members'), '" class="selected">', l('Manage Members'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_settings'), '" title="', l('Manage member registration and account settings'), '">', l('Member Settings'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_permissions'), '" title="', l('Manage member group permissions'), '">', l('Manage Permissions'), '</a></li>
		</ul>
		<div class="break">
		</div>
	</div>
	<h3><img src="', theme()->url(), '/style/images/members_manage-small.png" alt="" /> ', l('Manage Members'), '</h3>
	<p>', l('All existing members can be managed here, such as editing, deleting, and approving accounts.'), '</p>';

		api()->context['table']->show('admin_members_manage_table');
?>