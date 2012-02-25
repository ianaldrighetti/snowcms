/*
	Class: Widgets
*/
function Widgets()
{
}

// Variable: areas
Widgets.prototype.areas = {};

// Variable: moving
Widgets.prototype.moving = null;

// Function: AddArea
Widgets.prototype.AddArea = function(areaId, currentWidgets)
{
	this.areas[areaId] = currentWidgets;
};

// Function: Save
Widgets.prototype.Save = function(element, widgetClass, widgetId)
{
	return false;
};

// Function: Delete
Widgets.prototype.Delete = function(element, widgetId)
{
	if(confirm('Do you really want to delete this widget?'))
	{
		$.ajax({
			'type': 'POST',
			'cache': false,
			'data': 'request_type=ajax&delete=true&widget_id=' + encodeURIComponent(widgetId) + '&sid=' + session_id,
			'url': baseurl + '/index.php?action=admin&sa=themes&section=widgets',
			'dataType': 'JSON',
			'success': function(result, status, xhr)
				{
					if(result['error'])
					{
						alert(result['error']);
					}
					else
					{
						// It has been removed from the list of widgets, so now we will
						// remove it from the page, along with the box that allows you
						// to move another widget under it.
						$('#widget_id-' + result['widgetId']).remove();
						$('#moveHere_' + result['widgetId']).remove();
					}
				},
			});
	}
};

// Function: Expand
Widgets.prototype.Expand = function(img, widgetId)
{
	if($('#options_' + widgetId).css('display') == 'block')
	{
		$('#options_' + widgetId).parent().removeClass('widget-no-radius');
		$('#options_' + widgetId).parent().addClass('widget-radius');
		$('#options_' + widgetId).css('display', 'none');
		$(img).attr('title', widget_expand);
		$(img).attr('src', themeurl + '/style/images/expand-double-arrow.png');
		$(img).mouseover(function()
			{
				this.src = themeurl + '/style/images/expand-double-arrow.png';
			});
		$(img).mouseout(function()
			{
				this.src = themeurl + '/style/images/expand-arrow.png';
			});
	}
	else
	{
		$('#options_' + widgetId).parent().addClass('widget-radius');
		$('#options_' + widgetId).parent().removeClass('widget-no-radius');
		$('#options_' + widgetId).css('display', 'block');
		$(img).attr('title', widget_collapse);
		$(img).attr('src', themeurl + '/style/images/collapse-double-arrow.png');
		$(img).mouseover(function()
			{
				this.src = themeurl + '/style/images/collapse-double-arrow.png';
			});
		$(img).mouseout(function()
			{
				this.src = themeurl + '/style/images/collapse-arrow.png';
			});
	}
};

// Function: StartMove
Widgets.prototype.StartMove = function(element, widgetId)
{
	alert('not implemented');
};

// Function: StartPlace
Widgets.prototype.StartPlace = function(element, widgetClass)
{
	// If we're moving something else, we should stop that.
	if(this.moving != null)
	{
		if(typeof this.moving == 'string')
		{
			this.StopPlace();
		}
		else
		{
			this.StopMove();
		}
	}

	// Show all the locations where the widget can be added.
	$('.move-selected-widget').css('display', 'block');
	$('p', '.move-selected-widget').html(widgets_moveHere);

	// We're moving this widget, now -- well, adding it.
	this.moving = widgetClass;
};

// Function: Move
Widgets.prototype.MoveHere = function(element, areaId, after)
{
	// We don't need to show the places that the widget can be moved anymore.
	$('.move-selected-widget').css('display', 'block');

	// Well, except the place it is being moved -- we will show a little
	// message.
	$('p', element).text(widgets_pleaseWait);

	// If this is a widget that is being added for the first time, then we
	// have to do something different than if we're moving an already set up
	// widget.
	if(typeof this.moving == 'string')
	{
		$.ajax({
			'type': 'POST',
			'cache': false,
			'data': 'request_type=ajax&move=false&widget_class=' + this.moving + '&area_id=' + encodeURIComponent(areaId) + '&after=' + encodeURIComponent(after) + '&sid=' + session_id,
			'url': baseurl + '/index.php?action=admin&sa=themes&section=widgets',
			'dataType': 'JSON',
			'success': function(result, status, xhr)
				{
					if(result['error'])
					{
						Widgets.StopPlace();

						alert(result['error']);
					}
					else
					{
						Widgets.StopPlace();

						$('#moveHere_' + result['after']).after('<div class="widget-container" id="widget_id-' + result['widget_info']['id'] + '">' +
																											'<p class="widget-name" title="' + widget_moveThis + '" onclick="Widgets.StartMove(this, ' + result['widget_info']['id'] + ');">' + result['widget_info']['title'] + '</p>' +
																											'<p class="widget-expand"><img src="' + themeurl + '/style/images/collapse-arrow.png" alt="" title="' + widget_collapse + '" onclick="Widgets.Expand(this, ' + result['widget_info']['id'] + ');" onmouseover="this.src = \'' + themeurl + '/style/images/collapse-double-arrow.png\';" onmouseout="this.src = \'' + themeurl + '/style/images/collapse-arrow.png\';" /></p>' +
																											'<div class="break">' +
																											'</div>' +
																											'<div id="options_' + result['widget_info']['id'] + '" class="widget-options" style="display: block;">' +
																											result['widget_info']['form'] +
																											'</div>' +
																										'</div>' +
																										'<div id="moveHere_' + result['widget_info']['id'] + '" class="move-selected-widget" onclick="Widgets.MoveHere(this, \'' + result['widget_area'] + '\', ' + result['widget_info']['id'] + ');">' +
																											'<p>' + widgets_moveHere + '</p>' +
																										'</div>');
					}
				},
		});
	}
};

// Function: StopPlace
Widgets.prototype.StopPlace = function()
{
	if(this.moving != null && typeof this.moving == 'string')
	{
		$('.move-selected-widget').css('display', 'none');
		this.moving = null;
	}
	else
	{
		alert('not implemented');
	}
};

// Function: StopMove
Widgets.prototype.StopMove = function()
{
	alert('not yet implemented');
};

var Widgets = new Widgets();