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
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_add'), '" title="', l('Add a new member'), '" class="first selected">', l('Add Member'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_manage'), '" title="', l('Manage existing members'), '">', l('Manage Members'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_settings'), '" title="', l('Manage member regtisration and account settings'), '">', l('Member Settings'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=members_permissions'), '" title="', l('Manage member group permissions'), '">', l('Manage Permissions'), '</a></li>
		</ul>
		<div class="break">
		</div>
	</div>
	<h3><img src="', theme()->url(), '/style/images/members_add-small.png" alt="" /> ', l('Add a New Member'), '</h3>
	<p>', l('If registration is enabled, guests on your site can create their own account, but if registration is not open someone will have to create an account for them.'), '</p>';

		api()->context['form']->render('admin_members_add_form');
?>