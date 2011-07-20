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
	<h1><img src="', theme()->url(), '/style/images/plugins_add-small.png" alt="" /> ', l('Add a new plugin'), '</h1>
	<p>', l('Plugins can be added to your site by entering the plugins globally unique identifier (the address at which the plugins package is downloaded) or by uploading the plugin package.'), '</p>';

		api()->context['form']->show('add_plugins_form');
?>