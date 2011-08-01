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
		<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Updating Theme &quot;%s&quot;', api()->context['theme_name']), '</h3>
		<p>', l('Please wait while the theme is being updated from v%s to v%s.', api()->context['theme_version'], api()->context['update_version']), '</p>

		<p class="bold">', l('Checking for Updates'), '</p>
		<p', empty(api()->context['update_found']) ? ' class="red"' : '', '>', api()->context['update_message'], '</p>';

if(!empty(api()->context['update_found']))
{
	echo '
		<p class="bold">', l('Downloading Update'), '</p>
		<p', empty(api()->context['downloaded']) ? ' class="red"' : '', '>', api()->context['download_message'], '</p>';

	if(!empty(api()->context['downloaded']))
	{
		echo '
			<p class="bold">', l('Validating Theme'), '</p>
			<p', empty(api()->context['validated']) ? ' class="red"' : '', '>', api()->context['validate_message'], '</p>';

		if(!empty(api()->context['validated']))
		{
			echo '
				<p class="bold">', l('Checking Theme Status'), '</p>
				<div class="', api()->context['status_class'], '">
					<p>', api()->context['status_message'], '</p>
				</div>';

			// Did the update process continue?
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
				<p class="bold">', l('Update Complete'), '</p>
				<p>', api()->context['complete_message'], '</p>';
					}
					elseif(!empty(api()->context['extracted']))
					{
						echo '
				<p class="bold">', l('Update Failed'), '</p>
				<p>', api()->context['complete_message'], '</p>';
					}
				}
				else
				{
					// You may continue with the installation anyways, if you want.
					echo '
				<form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Do you really want to update to a version of this theme which isn\\\'t compatible with your version of SnowCMS?'), '\');" class="right">
					<input type="submit" value="', l('Proceed anyways'), ' &raquo;" />
					<input type="hidden" name="action" value="admin" />
					<input type="hidden" name="sa" value="themes" />
					<input type="hidden" name="update" value="', api()->context['update'], '" />
					<input type="hidden" name="sid" value="', member()->session_id(), '" />
					<input type="hidden" name="status" value="ignore" />
					<input type="hidden" name="compat" value="ignore" />
				</form>';
				}
			}
			else
			{
				echo '
				<form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Do you really want to update this theme?\r\nThis should only be done if you trust the source of this theme.'), '\');" class="right">
					<input type="submit" value="', l('Proceed anyways'), ' &raquo;" />
					<input type="hidden" name="action" value="admin" />
					<input type="hidden" name="sa" value="themes" />
					<input type="hidden" name="update" value="', api()->context['update'], '" />
					<input type="hidden" name="sid" value="', member()->session_id(), '" />
					<input type="hidden" name="status" value="ignore" />
				</form>';
			}
		}
	}
}

echo '
		<p class="right"><a href="', baseurl, '/index.php?action=admin&amp;sa=themes">', l('Back to theme management'), ' &raquo;</a></p>';
?>