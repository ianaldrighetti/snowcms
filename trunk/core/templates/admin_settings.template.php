<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
	<h3><img src="', theme()->url(), '/style/images/settings-small.png" alt="" /> ', l('System Settings'), '</h3>
	<p>', l('All settings that deal with the core components of the SnowCMS system can be managed here. These settings include the name of website, email settings and other advanced settings.'), '</p>

	<div class="section-tabs">
		<ul>
			<li><a href="', baseurl, '/index.php?action=admin&amp;sa=settings&amp;type=basic" title="', l('Manage basic settings'), '" class="first', (api()->context['form_type'] == 'basic' ? ' selected' : ''), '">Basic Settings</a></li>
			<li><a href="', baseurl, '/index.php?action=admin&amp;sa=settings&amp;type=date" title="', l('Manage date and time settings'), '"', (api()->context['form_type'] == 'date' ? ' class="selected"' : ''), '>Date/Time Settings</a></li>
			<li><a href="', baseurl, '/index.php?action=admin&amp;sa=settings&amp;type=mail" title="', l('Manage email settings'), '"', (api()->context['form_type'] == 'mail' ? ' class="selected"' : ''), '>Email Settings</a></li>
			<li><a href="', baseurl, '/index.php?action=admin&amp;sa=settings&amp;type=other" title="', l('Manage miscellaneous settings'), '"', (api()->context['form_type'] == 'other' ? ' class="selected"' : ''), '>Miscellaneous Settings</a></li>
		</ul>
		<div class="break">
		</div>
	</div>';

		api()->context['form']->render(api()->context['form_type']. '_settings_form');
?>