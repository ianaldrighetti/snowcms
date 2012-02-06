var overlays = [];
var windows = [];

function getWidth()
{
	if(typeof self.innerWidth != 'undefined')
	{
		return self.innerWidth;
	}
	else if (typeof document.documentElement.clientWidth != 'undefined')
	{
		return document.documentElement.clientWidth;
	}
	else if(typeof document.body.clientWidth != 'undefined')
	{
		return document.body.clientWidth;
	}
	else
	{
		return null;
	}
}

function getHeight()
{
	if(typeof self.innerHeight != 'undefined')
	{
		return self.innerHeight;
	}
	else if (typeof document.documentElement.clientHeight != 'undefined')
	{
		return document.documentElement.clientHeight;
	}
	else if(typeof document.body.clientHeight != 'undefined')
	{
		return document.body.clientHeight;
	}
	else
	{
		return null;
	}
}

function setOpacity(element, percent)
{
	if(percent > 100)
	{
		percent = 100;
	}
	else if(percent < 0)
	{
		percent = 0;
	}

	if(typeof element.style != 'undefined')
	{
		element.style.MozOpacity = '.' + percent.toString();
		element.style.filter = 'alpha(opacity=' + percent.toString() + ')';
		element.style.opacity = '.' + percent.toString();

		return percent;
	}
	else
	{
		return false;
	}
}

function createOverlay(opacity, color, zIndex)
{
	var element_id = overlays.length;

	// First create an element which will house the background overlay.
	var div = document.createElement('div');

	// Now all the CSS needed to cover the entire page.
	div.style.position = 'fixed';
	div.style.top = '0';
	div.style.right = '0';
	div.style.bottom = '0';
	div.style.left = '0';
	div.style.background = (color ? color : '#ffffff');
	div.id = 'overlay_' + element_id;

	if(zIndex)
	{
		div.style.zIndex = zIndex;
	}

	// Add the element to the page, otherwise it is quite pointless!
	document.body.appendChild(div);

	// For future reference.
	overlays[element_id] = div;

	// Now for the opacity.
	setOpacity(div, opacity);

	return element_id;
}

function removeOverlay(overlay_id)
{
	if(!overlay_id)
	{
		// Remove them all? Okay...
		for(var index in overlays)
		{
			removeOverlay(index);
		}

		return true;
	}
	else if(typeof overlays[overlay_id] == 'undefined')
	{
		return false;
	}
	else
	{
		document.body.removeChild(overlays[overlay_id]);
		delete overlays[overlay_id];

		return true;
	}
}

function createWindow(title, width, height, childNode, zIndex, closeCallback)
{
	// Set up a couple things we will be needing.
	var window_id = windows.length;
	var element = document.createElement('div');

	// Might as well do this now.
	windows[window_id] = element;

	// Now set up the element which will represent the window itself.
	element.style.width = width.toString() + 'px';
	element.style.height = height.toString() + 'px';
	element.style.position = 'fixed';

	// We need to do a bit of math to get where we want to place the window
	// itself... But it's not very hard.
	// Well, actually, a height isn't required... Just a width. So...
	if(!height)
	{
		element.style.top = '50px';
	}
	else
	{
		element.style.top = Math.ceil((getHeight() - height) / 2).toString() + 'px';
	}

	// But a width is!
	element.style.left = Math.ceil((getWidth() - width) / 2).toString() + 'px';

	// A couple other style things.
	element.style.background = '#FFFFFF';
	element.style.border = '1px solid #DEDEDE';
	element.style.borderRadius = '8px';

	if(zIndex)
	{
		element.style.zIndex = zIndex;
	}

	// Let's not forget to add it to the page.
	document.body.appendChild(element);

	// It is time to do the rest of the window, such as the title and the
	// button which will close the window itself.
	var titleElement = document.createElement('div');

	// Some styling never hurt anyone.
	titleElement.style.width = (width - 10).toString() + 'px';
	titleElement.style.background = '#DEDEDE';
	titleElement.style.borderRadius = '4px 4px 0 0';
	titleElement.style.padding = '5px';
	titleElement.style.fontWeight = 'bold';

	// Set the title.
	var titleTag = document.createElement('p');
	titleTag.id = 'window_' + window_id + '_title';
	titleTag.style.display = 'inline-block';
	titleTag.style.margin = '0';
	titleTag.style.cssFloat = 'left';
	titleTag.innerHTML = title;

	titleElement.appendChild(titleTag);

	// Gotta a couple other things we need to do, first.
	var closeTag = document.createElement('p');
	closeTag.style.display = 'inline-block';
	closeTag.style.margin = '0';
	closeTag.style.cssFloat = 'right';

	var closeElement = document.createElement('a');
	closeElement.innerHTML = '[X]';
	closeElement.href = 'javascript:void(0);';
	closeElement.onclick = (closeCallback ? closeCallback : function() { removeWindow(window_id); });

	closeTag.appendChild(closeElement);
	titleElement.appendChild(closeTag);

	// Just one more thing!
	var breakElement = document.createElement('div');
	breakElement.style.clear = 'both';

	titleElement.appendChild(breakElement);
	element.appendChild(titleElement);

	// Some child node you want added?
	if(childNode)
	{
		element.appendChild(childNode);
	}

	return window_id;
}

function removeWindow(window_id)
{
	if(typeof windows[window_id] != 'undefined')
	{
		document.body.removeChild(windows[window_id]);
		delete windows[window_id];

		return true;
	}
	else
	{
		return false;
	}
}

function menuStateChanged(group_id)
{
	if(s.id('group_' + group_id.toString()))
	{
		var element = s.id('group_' + group_id.toString());

		// If it is expanded, collapse it, and vice versa.
		element.className = element.className == 'expanded' ? 'collapsed' : 'expanded';

		s.ajaxCallback(baseurl + '/index.php?action=admin&sa=ajax', expandGroupDone, 'request=set&group_id=' + s.encode(group_id) + '&state=' + (element.className == 'collapsed' ? 0 : 1) + '&sid=' + s.encode(session_id));
	}
}

function expandGroupDone(response)
{
	// Nothing... (on purpose).
}