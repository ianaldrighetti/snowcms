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
			<h1>', l('Registration Successful'), '</h1>
			<p>', l('Thank you for registering %s.', api()->context['member_info']['name']), ' ', (api()->context['member_info']['is_activated'] ? l('You may now proceed to <a href="%s">log in to your account</a>.', baseurl. '/index.php?action=login') : (settings()->get('registration_type', 'int', 1) == 2 ? l('The site requires an administrator to activate new accounts. You will receive an email once your account has been activated.') : (settings()->get('registration_type', 'int', 1) == 3 ? l('The site requires you to activate your account via email, so check your email (%s) for your activation link.', api()->context['member_info']['email']) : api()->apply_filters('registration_message_other', '')))), '</p>';
?>