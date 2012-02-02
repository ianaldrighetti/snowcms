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
	<h3><img src="', theme()->url(), '/style/images/update-small.png" alt="" /> ', l('Applying Update v%s', api()->context['version']), '</h3>
	<p>', l('Please wait while SnowCMS applies the system update. The website will be put into maintenance mode until the update process has completed.'), '</p>
	<p>', l('<strong>Warning:</strong> Do <em>not</em> close this window until the update process has completed, otherwise the update may not be properly applied resulting in possible damage to your website.'), '</p>

	<h3>', l('Update Progress'), '</h3>

	<div class="update-progress-bar-box" style="margin-top: 10px;">
		<div class="update-progress-bar-fill" id="update-overall" style="width: 0%;">
		</div>
		<div class="update-progress-message">
			<p id="update-overall-message">', l('Loading...'), '</p>
		</div>
		<div id="overall-underlay" class="update-progress-underlay" style="visibility: hidden;">
		</div>
	</div>
	<div class="update-progress-bar-box" id="update-step-container" style="margin-top: 15px; visibility: hidden;">
		<div class="update-progress-bar-fill" id="update-step" style="width: 0%;">
		</div>
		<div class="update-progress-message">
			<p id="update-step-message">0%</p>
		</div>
		<div class="update-progress-underlay" style="visibility: hidden;">
		</div>
	</div>
	<div id="update-box" style="display: none;">
	</div>';
?>