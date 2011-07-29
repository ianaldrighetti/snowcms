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
		<h3><img src="', theme()->url(), '/style/images/plugins_add-small.png" alt="" /> ', l('Installing Plugin'), '</h3>
		<p>', l('Please wait while the plugin is being installed.'), '</p>

		<p class="bold">', l('Validating Plugin'), '</p>
		<p', !empty(api()->context['validate_is_error']) ? ' class="red"' : '', '>', api()->context['validate_message'], '</p>';

if(!empty(api()->context['status_message']))
{
	echo '
		<p class="bold">', l('Checking Plugin Status'), '</p>
		<div class="', api()->context['status_class'], '">
			<p>', api()->context['status_message'], '</p>
		</div>';

	// Did installation proceed?
	if(!empty(api()->context['proceed']))
	{
		// Yes, but what about compatibility?
		echo '
		<p class="bold">', l('Checking Compatibility'), '</p>
		<div', !empty(api()->context['compatible_is_error']) ? ' class="error-message"' : '', '>
			<p>', api()->context['compatible_message'], '</p>
		</div>';

		if(empty(api()->context['compatible_is_error']))
		{
			echo '
		<p class="bold">', l('Extracting Plugin'), '</p>
		<p', !empty(api()->context['extract_is_error']) ? ' class="red"' : '', '>', api()->context['extract_message'], '</p>';

			if(empty(api()->context['extract_is_error']) && !empty(api()->context['completed']))
			{
				// We're done! Awesome!
				echo '
		<p class="bold">', l('Installation Complete'), '</p>
		<p>', l('The plugin was successfully installed.'), '</p>';
			}
			elseif(empty(api()->context['extract_is_error']))
			{
				echo '
		<p class="bold">', l('Installation Failed'), '</p>
		<p>', l('The plugin was not successfully installed due to it not being a valid plugin.'), '</p>';
			}
		}
		else
		{
			// You may continue with the installation anyways, if you want.
			echo '
		<form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Do you really want to install this plugin which isn\\\'t compatible with your version of SnowCMS?'), '\');" class="right">
			<input type="submit" value="', l('Proceed anyways'), ' &raquo;" />
			<input type="hidden" name="action" value="admin" />
			<input type="hidden" name="sa" value="plugins_add" />
			<input type="hidden" name="install" value="', api()->context['install'], '" />
			<input type="hidden" name="sid" value="', member()->session_id(), '" />
			<input type="hidden" name="proceed" value="true" />
			<input type="hidden" name="compat" value="ignore" />
		</form>';
		}
	}
	else
	{
		echo '
		<form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Do you really want to install this plugin?\r\nThis should only be done if you trust the source of this plugin package.'), '\');" class="right">
			<input type="submit" value="', l('Proceed anyways'), ' &raquo;" />
			<input type="hidden" name="action" value="admin" />
			<input type="hidden" name="sa" value="plugins_add" />
			<input type="hidden" name="install" value="', api()->context['install'], '" />
			<input type="hidden" name="sid" value="', member()->session_id(), '" />
			<input type="hidden" name="proceed" value="true" />
		</form>';
	}
}

echo '
		<p class="right"><a href="', baseurl, '/index.php?action=admin&amp;sa=plugins_manage">', l('Back to plugin management'), ' &raquo;</a></p>';
?>