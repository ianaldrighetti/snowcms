<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}
echo '
		<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Installing Theme'), '</h3>
		<p>', l('Please wait while the theme is being installed.'), '</p>

		<p class="bold">', l('Extracting Theme'), '</p>
		<p', !empty(api()->context['extract_is_error']) ? ' class="red"' : '', '>', api()->context['extract_message'], '</p>';

if(!empty(api()->context['status_message']))
{
	echo '
		<p class="bold">', l('Checking Theme Status'), '</p>
		<div class="', api()->context['status_class'], '">
			<p>', api()->context['status_message'], '</p>
		</div>';

	// Did installation proceed?
	if(!empty(api()->context['proceed']))
	{
		echo '
		<h3>', l('Installation Complete'), '</h3>
		<p>', l('The theme was successfully installed.'), '</p>';
	}
	else
	{
		echo '
		<form action="', baseurl, '/index.php" method="get" onsubmit="return confirm(\'', l('Do you really want to install this theme?\r\nThis should only be done if you trust the source of this theme package.'), '\');" class="right">
			<input type="submit" value="', l('Proceed anyways'), ' &raquo;" />
			<input type="hidden" name="action" value="admin" />
			<input type="hidden" name="sa" value="themes" />
			<input type="hidden" name="install" value="', api()->context['install_filename'], '" />
			<input type="hidden" name="sid" value="', member()->session_id(), '" />
			<input type="hidden" name="proceed" value="true" />
		</form>';
	}
}

echo '
		<p class="right"><a href="', baseurl, '/index.php?action=admin&amp;sa=themes">Back to theme management &raquo;</a></p>';
?>