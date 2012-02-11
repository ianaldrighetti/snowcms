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

admin_section_menu('members', 'members_add');

echo '
	<h3><img src="', theme()->url(), '/style/images/members_add-small.png" alt="" /> ', l('Add a New Member'), '</h3>
	<p>', l('If registration is enabled, guests on your site can create their own account, but if registration is not open someone will have to create an account for them.'), '</p>';

api()->context['form']->render('admin_members_add_form');
?>