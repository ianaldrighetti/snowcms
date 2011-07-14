<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

			echo '
	<h1><img src="', theme()->url(), '/style/images/error_log-small.png" alt="" /> ', l('Viewing Error #%s', api()->context['error_id']), '</h1>
	<p>', l('You are currently viewing error #%s. <a href="%s" title="Back to error log">Back to error log</a>.', api()->context['error_id'], baseurl. '/index.php?action=admin&amp;sa=error_log'), '</p>';

			// Output all the information.
			echo '
	<p style="margin-top: 10px;"><span class="bold">', l('Time:'), '</span> ', timeformat(api()->context['error']['error_time']), '</p>
	<p><span class="bold">', l('Type:'), '</span> ', isset(api()->context['error_const'][api()->context['error']['error_type']]) ? api()->context['error_const'][api()->context['error']['error_type']][1] : l('Unknown'), ' ', (api()->context['error']['error_type'] != api()->context['error_const'][api()->context['error']['error_type']][0] ? '('. api()->context['error_const'][api()->context['error']['error_type']][0]. ')' : ''), '</p>';

			// Was this a guest..?
			if(api()->context['error']['member_id'] == 0)
			{
				echo '
	<p><span class="bold">', l('Member:'), '</span> ', l('Guest (IP: %s)', api()->context['error']['member_ip']), '</p>';
			}
			else
			{
				// Load up their information, if they exist.
				$members = api()->load_class('Members');
				$members->load(api()->context['error']['member_id']);
				$member = $members->get(api()->context['error']['member_id']);

				// Do they?
				if($member === false)
				{
					echo '
		<p><span class="bold">', l('Member:'), '</span> ', l('%s (No longer exists, IP: %s)', api()->context['error']['member_name'], api()->context['error']['member_ip']), '</p>';
				}
				else
				{
					echo '
		<p><span class="bold">', l('Member:'), '</span> ', l('<a href="%s/index.php?action=profile&amp;id=%s" target="_blank">%s</a> (IP: %s)', baseurl, $member['id'], $member['name'], api()->context['error']['member_ip']), '</p>';
				}
			}

			// Now for the actual message, file and line.
			echo '
		<p><span class="bold">', l('Message:'), '</span></p>
		<p style="margin-left: 10px;">', api()->context['error']['error_message'], '</p>
		<p><span class="bold">', l('File:'), '</span> ', api()->context['error']['error_file'], ' (Line: ', api()->context['error']['error_line'], ')</p>
		<p><span class="bold">', l('URL:'), '</span> <a href="', htmlchars(api()->context['error']['error_url']), '" target="_blank">', htmlchars(api()->context['error']['error_url']), '</a></p>';
?>