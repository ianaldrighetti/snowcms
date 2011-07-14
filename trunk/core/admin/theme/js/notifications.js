var notificationWindowId;
var notificationOverlayId;

function openNotificationWindow()
{
	// Let's create our overlay first.
	notificationOverlayId = createOverlay(70, false, 1);

	// Now our window.
	notificationWindowId = createWindow(notificationTitle, 500, false, fetchNotifications(), 2, closeNotificationWindow);
}

function fetchNotifications()
{
	var element = document.createElement('div');
	element.innerHTML = '<p style="text-align: center;">No Notifications!</p>';

	return element;
}

function closeNotificationWindow()
{
	removeOverlay(notificationOverlayId);
	removeWindow(notificationWindowId);
}