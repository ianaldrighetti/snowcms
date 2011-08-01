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
	<h3><img src="', theme()->url(), '/style/images/update-small.png" alt="" /> ', l('Check for Updates'), '</h3>
	<p>', l('Just as with computers, it is a good idea to keep SnowCMS up to date because these updates can fix security issues, bugs, enhancements and new features.'), '</p>

	<p>Your version: <span class="', api()->context['update_available'] ? 'red bold' : 'green', '">', api()->context['current_version'], '</span></p>
	<p>Latest version: ', (api()->context['latest_version'] === false ? '<span class="red">'. l('Could not connect to update server. Please check again later.'). '</span>' : '<span class="green">'. api()->context['latest_version']. '</span>'), '</p>

	<h1 style="font-size: 14px;">', api()->context['latest_info']['header'], '</h1>
	<p>', api()->context['update_available'] ? l(api()->context['latest_info']['message']) : api()->context['latest_info']['message'], '</p>
	<p class="right">', api()->context['update_available'] ? '<a href="'. baseurl. '/index.php?action=admin&amp;sa=update&amp;apply='. api()->context['latest_version']. '&amp;sid='. member()->session_id(). '" title="'. l('Apply update v%s', api()->context['latest_version']). '">'. l('Apply Update'). '</a> | ' : '', ' <a href="', baseurl, '/index.php?action=admin&amp;sa=update&amp;check" title="', l('Check for Updates'), '">', l('Check for Updates'), '</a></p>';
?>