s.onload(function()
{
	var image = new Image();
	image.src = base_url + '/index.php?action=tasks&time=' + (new Date).getTime();
});