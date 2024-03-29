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
		<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Installing Theme'), '</h3>
		<p>', l('Please wait while the theme is being installed.'), '</p>

		<p class="bold">', l('Validating Theme'), '</p>
		<p', empty(api()->context['validated']) ? ' class="red"' : '', '>', api()->context['validate_message'], '</p>';

if(!empty(api()->context['validated']))
{
	echo '
		<p class="bold">', l('Checking Theme Status'), '</p>
		<div class="', api()->context['status_class'], '">
			<p>', api()->context['status_message'], '</p>
		</div>';

	// Did installation proceed?
	if(!empty(api()->context['status_proceed']))
	{
		// Yes, but what about compatibility?
		echo '
		<p class="bold">', l('Checking Compatibility'), '</p>
		<p', empty(api()->context['is_compatible']) ? ' class="red"' : '', '>', api()->context['compatibility_message'], '</p>';

		if(!empty(api()->context['is_compatible']))
		{
			echo '
		<p class="bold">', l('Extracting Theme'), '</p>
		<p', empty(api()->context['extracted']) ? ' class="red"' : '', '>', api()->context['extract_message'], '</p>';

			if(!empty(api()->context['completed']))
			{
				// We're done! Awesome!
				echo '
		<p class="bold">', l('Installation Complete'), '</p>
		<p>', api()->context['complete_message'], '</p>';
			}
			elseif(!empty(api()->context['extracted']))
			{
				echo '
		<p class="bold">', l('Installation Failed'), '</p>
		<p>', api()->context['complete_message'], '</p>';
			}
		}
		else
		{
			// You may continue with the installation anyways, if you want.
			echo '
		<form action="', baseurl('index.php'), '" method="get" onsubmit="return confirm(\'', l('Do you really want to install this theme which isn\\\'t compatible with your version of SnowCMS?'), '\');" class="right">
			<input type="submit" value="', l('Proceed anyways'), ' &raquo;" />
			<input type="hidden" name="action" value="admin" />
			<input type="hidden" name="sa" value="themes" />
			<input type="hidden" name="install" value="', api()->context['install'], '" />
			<input type="hidden" name="sid" value="', member()->session_id(), '" />
			<input type="hidden" name="status" value="ignore" />
			<input type="hidden" name="compat" value="ignore" />
		</form>';
		}
	}
	else
	{
		echo '
		<form action="', baseurl('index.php'), '" method="get" onsubmit="return confirm(\'', l('Do you really want to install this theme?\r\nThis should only be done if you trust the source of this theme package.'), '\');" class="right">
			<input type="submit" value="', l('Proceed anyways'), ' &raquo;" />
			<input type="hidden" name="action" value="admin" />
			<input type="hidden" name="sa" value="themes" />
			<input type="hidden" name="install" value="', api()->context['install'], '" />
			<input type="hidden" name="sid" value="', member()->session_id(), '" />
			<input type="hidden" name="status" value="ignore" />
		</form>';
	}
}

echo '
		<p class="right"><a href="', baseurl('index.php?action=admin&amp;sa=themes&amp;section=install'), '">', l('Back to theme management'), ' &raquo;</a></p>';
?>