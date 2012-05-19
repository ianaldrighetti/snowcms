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

admin_section_menu('themes', 'widgets');

echo '
	<h3><img src="', theme()->url(), '/style/images/manage_themes-small.png" alt="" /> ', l('Manage Widgets'), '</h3>
	<p>Widgets allow plugins to add additional functionality in a theme, such as displaying some sort of dynamic or static content. If the theme supports widgets, just select the widgets you wish to add and choose where they should be displayed, along with changing any configuration options for the widget.</p>';

// Does the current theme support widgets?
if(api()->context['widget_support'])
{
	// We need to list all the widgets that are available for use.
	echo '
	<div class="widget-area available-list">
		<h3>', l('Available Widgets'), '</h3>
		<div class="widget-list">';

	foreach(api()->context['widgets'] as $widget_class => $widget)
	{
		echo '
			<div class="widget-info">
				<div class="widget-label" title="', l('Place this widget'), '" onclick="Widgets.StartPlace(this, \'', urlencode($widget_class), '\');">
					<p>', $widget['name'], '</p>
				</div>
				<p>', $widget['description'], '</p>
			</div>';
	}

	echo '
			<div class="break">
			</div>
		</div>
	</div>';

	// Now for a list of all the areas the theme supports widgets in.
	foreach(api()->context['widget_areas'] as $widget_area => $area_info)
	{
		echo '
	<div class="widget-area sidebar-list">
		<h3>', $area_info['label'], '</h3>
		<div class="widget-list" id="', $widget_area, '">
			<div id="moveHere_', $widget_area, '_top" class="move-selected-widget" onclick="Widgets.MoveHere(this, \'', $widget_area, '\', \'top\');">
				<p>&laquo; ', l('Add Widget Here'), ' &raquo;</p>
			</div>';

		foreach($area_info['widgets'] as $widget)
		{
			echo '
			<div class="widget-container" id="widget_id-', $widget['id'], '">
				<p class="widget-name" title="', l('Move this widget'), '" onclick="Widgets.StartMove(this, ', $widget['id'], ');">', $widget['title'], '</p>
				<p class="widget-expand"><img src="', theme()->url(), '/style/images/expand-arrow.png" alt="" title="', l('Expand this widget'), '" onclick="Widgets.Expand(this, ', $widget['id'], ');" onmouseover="this.src = \'', theme()->url(), '/style/images/expand-double-arrow.png\';" onmouseout="this.src = \'', theme()->url(), '/style/images/expand-arrow.png\';" /></p>
				<div class="break">
				</div>
				<div id="options_', $widget['id'], '" class="widget-options">
					', $widget['form'], '
				</div>
			</div>
			<div id="moveHere_', $widget['id'], '" class="move-selected-widget" onclick="Widgets.MoveHere(this, \'', $widget_area, '\', ', $widget['id'], ');">
				<p>&laquo; ', l('Add Widget Here'), ' &raquo;</p>
			</div>';
		}

		echo '
		</div>
	</div>
	<script type="text/javascript">
		Widgets.AddArea(\'', $widget_area, '\', ', count($area_info['widgets']), ');
	</script>';
	}

	echo '
	<div class="break">
	</div>';
}
else
{
	echo '
	<div class="error-message">
		<p>', l('The current default theme does not support widgets.'), '</p>
	</div>';
}
?>