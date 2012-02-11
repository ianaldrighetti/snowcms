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

admin_section_menu('plugins', 'plugins_manage');

echo '
	<h3><img src="', theme()->url(), '/style/images/plugins_manage-small.png" alt="" /> ', l('Manage Plugins'), '</h3>';

// Do we need to show a message that the plugins they are trying to activate
// say they aren't compatible with this version of SnowCMS?
if(isset(api()->context['compat']) && count(api()->context['compat']) > 0)
{
	echo '
	<div class="error-message left" style="padding: 5px 10px !important; font-size: 13px;">
		<p>', l('The following plugins you are trying to activate are not compatible with SnowCMS. Would you still like to activate them anyways? Please note that activating such plugins may cause instability issues.'), '</p>
		<ul>';

	$dirnames = array();
	foreach(api()->context['compat'] as $plugin_info)
	{
		echo '
			<li>', $plugin_info['name'], '</li>';

		$dirnames[] = $plugin_info['dirname'];
	}

	echo '
		</ul>
		<form action="', baseurl('index.php?action=admin&amp;sa=plugins_manage&amp;ignore=true'), '" method="post">
			<p class="right"><input type="submit" value="', l('Activate &raquo;'), '" /></p>
			<input type="hidden" name="activate" value="', htmlchars(implode(',', $dirnames)), '" />
			<input type="hidden" name="sid" value="', member()->session_id(), '" />
			<input type="hidden" name="ignore" value="true" />
		</form>
	</div>';
}

api()->context['table']->show('manage_plugins_table');
?>