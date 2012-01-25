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
			<li><a href="', baseurl('index.php?action=admin&amp;sa=plugins_add'), '" title="', l('Add a new plugin'), '" class="first selected">', l('Add Plugin'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=plugins_manage'), '" title="', l('Manage plugins'), '">', l('Manage Plugins'), '</a></li>
			<li><a href="', baseurl('index.php?action=admin&amp;sa=plugins_settings'), '" title="', l('Manage plugin settings'), '">', l('Plugin Settings'), '</a></li>
		</ul>
		<div class="break">
		</div>
	</div>
	<h3><img src="', theme()->url(), '/style/images/plugins_add-small.png" alt="" /> ', l('Add a New Plugin'), '</h3>
	<p>', l('Plugins can be added to your site by entering the plugins globally unique identifier (the address at which the plugins package is downloaded) or by selecting a plugin package to upload.'), '</p>';

		api()->context['form']->render('add_plugins_form');
?>