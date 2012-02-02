var notificationWindowId;
var notificationOverlayId;

function openNotificationWindow()
{
	// Let's create our overlay first.
	notificationOverlayId = createOverlay(70, false, 1);
	overlays[notificationOverlayId].onclick = closeNotificationWindow;

	// Now our window.
	notificationWindowId = createWindow(notificationTitle, 650, false, fetchNotifications(), 2, closeNotificationWindow);
}

function fetchNotifications()
{
	var element = document.createElement('div');
	element.id = 'notification-box';

	if(notifications.length > 0)
	{
		for(index in notifications)
		{
			var p = document.createElement('p');
			p.className = notifications[index]['attr_class'];
			p.innerHTML = notifications[index]['message'];

			element.appendChild(p);
		}
	}
	else
	{
		element.innerHTML = '<p style="text-align: center;">No Notifications!</p>';
	}

	return element;
}

function closeNotificationWindow()
{
	removeOverlay(notificationOverlayId);
	removeWindow(notificationWindowId);
}