function increaseEditor(editor_id, increment)
{
  handle = document.getElementById(editor_id);

  // Get the current height... and remove the px.
  cur_height = parseInt(handle.style.height);

  // Height blank..? We will assume its something else :P
  if(handle.style.height == '')
  {
    // JS override?
    if(typeof default_editor_height != 'undefined')
      cur_height = default_editor_height;
    else
      cur_height = 260;
  }

  // Increase it :)
  var newHeight = (cur_height + increment);
  if(newHeight > 0)
    handle.style.height = newHeight + 'px';
}

function decreaseEditor(editor_id, decrement)
{
  increaseEditor(editor_id, -decrement);
}
