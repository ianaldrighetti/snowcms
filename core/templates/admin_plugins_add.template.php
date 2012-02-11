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

admin_section_menu('plugins', 'plugins_add');

echo '
	<h3><img src="', theme()->url(), '/style/images/plugins_add-small.png" alt="" /> ', l('Add a New Plugin'), '</h3>
	<p>', l('Plugins can be added to your site by entering the plugins globally unique identifier (the address at which the plugins package is downloaded) or by selecting a plugin package to upload.'), '</p>';

api()->context['form']->render('add_plugins_form');
?>