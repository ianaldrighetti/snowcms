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

admin_section_menu('themes', 'install');

echo '
	<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Install a Theme'), '</h3>
	<p>', l('A theme can be installed two different ways, either by uploading a from your computer (such as a zip) or you can enter the URL where the theme can be downloaded from the Internet.'), '</p>';

api()->context['form']->render('install_theme_form');
?>