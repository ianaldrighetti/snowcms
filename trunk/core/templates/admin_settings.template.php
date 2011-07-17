<?php
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
	<h3><img src="', theme()->url(), '/style/images/settings-small.png" alt="" /> ', api()->context['settings_title'], '</h3>
	<p>', api()->context['settings_description'], '</p>';


		api()->context['form']->render(api()->context['form_type']. '_settings_form');
?>