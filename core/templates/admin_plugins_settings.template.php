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
	<h1><img src="', theme()->url(), '/style/images/plugins_settings-small.png" alt="" /> ', l('Manage plugin settings'), '</h1>
	<p>', l('Various plugin settings can be managed here.'), '</p>';

		// Gotta run those hooks, in order to know the actual number of fields...
		api()->context['form']->run_hooks('admin_plugins_settings_form');

		// Are there even any settings?
		// Of course there is one field, which is the form token...
		if(api()->context['form']->num_fields('admin_plugins_settings_form') > 1)
		{
			// Yup, there are!
			api()->context['form']->show('admin_plugins_settings_form');
		}
		else
		{
			// Nope, there is not.
			echo '
	<p style="margin-top: 10px; font-weight: bold; text-align: center;">', l('There are currently no plugin settings.'), '</p>';
		}
?>