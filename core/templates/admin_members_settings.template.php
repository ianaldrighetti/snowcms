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
	<div class="section-tabs">
		<ul>';

		foreach(api()->context['section_menu'] as $item)
		{
			echo '
			<li><a href="', $item['href'], '" title="', $item['title'], '"', ($item['is_first'] || $item['is_selected'] ? ' class="'. ($item['is_first'] ? 'first' : ''). ($item['is_selected'] ? ($item['is_first'] ? ' ' : ''). 'selected' : ''). '"' : ''), '>', $item['text'], '</a></li>';
		}

		echo '
		</ul>
		<div class="break">
		</div>
	</div>
	<h3><img src="', theme()->url(), '/style/images/members_settings-small.png" alt="" /> ', api()->context['settings_title'], '</h3>
	<p>', api()->context['settings_description'], '</p>';


		api()->context['form']->render(api()->context['form_type']. '_settings_form');
?>