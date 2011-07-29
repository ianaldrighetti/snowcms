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
			<h1>', l('Resend your activation email'), '</h1>
			<p>', l('If for some reason you didn\'t receive your activation email, you can request to have it resent by entering your username below.'), '</p>';

		if(strlen(api()->apply_filters('resend_message', '')) > 0)
		{
			echo '
			<div id="', api()->apply_filters('resend_message_id', 'resend_success'), '">
				', api()->apply_filters('resend_message', ''), '
			</div>';
		}

		api()->context['form']->render('resend_form');
?>