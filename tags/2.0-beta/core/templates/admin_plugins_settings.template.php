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
	<h3><img src="', theme()->url(), '/style/images/plugins_settings-small.png" alt="" /> ', l('Plugin Settings'), '</h3>
	<p>', l('Any settings plugins have to configure may be managed here.'), '</p>';

		// Are there even any settings?
		// Of course there is one field, which is the form token...
		if(count(api()->context['form']->inputs()) > 1)
		{
			// Yup, there are!
			api()->context['form']->render('admin_plugins_settings_form');
		}
		else
		{
			// Nope, there is not.
			echo '
	<p style="margin-top: 10px; font-weight: bold; text-align: center;">', l('There are currently no plugin settings.'), '</p>';
		}
?>