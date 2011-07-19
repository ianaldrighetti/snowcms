<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

echo '
	<h3><img src="', theme()->url(), '/style/images/error_log-small.png" alt="" /> ', l('Viewing Error #%s', api()->context['error_id']), '</h3>

	<table width="100%" id="error_log_view">
		<tr>
			<td><span class="bold">', l('Date generated'), '</span></td>
			<td><span class="bold">', l('Type of error'), '</span></td>
			<td><span class="bold">', l('Generated by'), '</span></td>
		</tr>
		<tr>
			<td>', api()->context['error']['time'], '</td>
			<td>', api()->context['error']['type'], (api()->context['error']['const'] ? ' ('. api()->context['error']['const']. ')' : ''), '</td>
			<td>', api()->context['error']['generated_by'], '</td>
		</tr>
		<tr>
			<td colspan="3"><span class="bold">', l('File'), '</span></td>
		</tr>
		<tr>
			<td style="padding: 5px;" colspan="3">', api()->context['error']['file'], (!empty(api()->context['error']['line']) ? ' <span class="bold">'. l('on line'). '</span> '. api()->context['error']['line'] : ''), '</td>
		</tr>
		<tr>
			<td colspan="3"><span class="bold">', l('URL'), '</span></td>
		</tr>
		<tr>
			<td style="padding: 5px;" colspan="3">', api()->context['error']['url'], '</td>
		</tr>
		<tr>
			<td colspan="3"><span class="bold">', l('Error Message'), '</span></td>
		</tr>
		<tr>
			<td style="padding: 5px;" colspan="3">', api()->context['error']['message'], '</td>
		</tr>
	</table>

	<p class="right"><a href="', baseurl, '/index.php?action=admin&amp;sa=error_log">', l('Back to error log'), ' &raquo;</a></p>';
?>