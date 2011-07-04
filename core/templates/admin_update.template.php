<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

    echo '
  <h1><img src="', theme()->url(), '/style/images/update-small.png" alt="" /> ', l('Check for updates'), '</h1>
  <p>', l('Just as with computers, it is a good idea to ensure that your system is up to date to make sure that you are not vulnerable to any security issues, or just to fix any bugs in the system.'), '</p>
  <br />
  <p>Your version: <span class="', api()->context['is_update_required'] ? 'red bold' : 'green', '">', settings()->get('version', 'string'), '</span></p>
  <p>Latest version: ', (api()->context['latest_version'] === false ? '<span class="red">'. l('Could not connect to update server. Please check again later.'). '</span>' : api()->context['latest_version']), '</p>

  <h1 style="font-size: 14px;">', l(api()->context['latest_info']['header']), '</h1>
  <p>', l(api()->context['latest_info']['text']), '</p>
  <br />
  <p>', api()->context['is_update_required'] ? '<a href="'. baseurl. '/index.php?action=admin&amp;sa=update&amp;apply='. api()->context['latest_version']. '&amp;sid='. member()->session_id(). '" title="'. l('Apply update'). '">'. l('Apply update'). '</a> | ' : '', ' <a href="', baseurl, '/index.php?action=admin&amp;sa=update&amp;check" title="', l('Check for updates'), '">', l('Check for updates'), '</a></p>';
?>