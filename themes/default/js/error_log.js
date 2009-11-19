function invert_boxes(element_name)
{
  elements = document.getElementsByName(element_name);

  for(var i = 0; i < elements.length; i++)
    elements[i].checked = !elements[i].checked;
}