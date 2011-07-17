<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

echo '
				<div id="snowcms-news">
					<h3>', l('News from <a href="%s" target="_blank">SnowCMS</a>', 'http://www.snowcms.com/'), '</h3>
					<div id="news-content">';

// List all the news, if there is any.
if(count(api()->context['current_news']) > 0)
{
	foreach(api()->context['current_news'] as $news)
	{
		echo '
				<p class="news-header">', (!empty($news['href']) ? '<a href="'. $news['href']. '" target="_blank">' : ''), $news['subject'], (!empty($news['href']) ? '</a>' : ''), ' <span class="news-date">', l('on'), ' ', timeformat($news['timestamp']), '</span></p>
				<p class="news-item">', $news['message'], '</p>';
	}
}
else
{
	echo '
			<p class="center">', l('No news to display. This could be caused by not being able to contact the update server.'), '</p>';
}

echo '
					</div>
				</div>';

// Do we need to show all the icons?
if(is_array($GLOBALS['icons']) && count($GLOBALS['icons']) > 0)
{
	foreach($GLOBALS['icons'] as $label => $icon)
	{
		echo '
			<h3>', $label, '</h3>
			<table class="icon-table">
				<tr>';

		// Time to show the actual icons.
		$length = count($icon);
		for($i = 0; $i < $length; $i++)
		{
			echo '
					<td><a href="', $icon[$i]['href'], '" title="', $icon[$i]['title'], '"><img src="', $icon[$i]['src'], '" alt="" title="', $icon[$i]['title'], '" /><br /><span class="label">', $icon[$i]['label'], '</span></a></td>';

			if(($i + 1) % 7 == 0 && isset($icon[$i + 1]))
			{
				echo '
				</tr>
			</table>
			<table class="icon-table">
				<tr>';
			}
		}

		echo '
				</tr>
			</table>';
	}
}
else
{
	echo '
			<h1 style="margin-top: 0px !important;">', l('Error'), '</h1>
			<p>', l('Sorry, but it appears that the icons have been malformed.'), '</p>';
}
?>