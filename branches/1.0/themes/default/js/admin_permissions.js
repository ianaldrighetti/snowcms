function toggle_visibility(perms)
{
  if(_.G(perms + '_change').innerHTML == '+')
  {
    _.G(perms + '_perms').style.display = 'block';
    _.G(perms + '_change').innerHTML = '-';
  }
  else
  {
    _.G(perms + '_perms').style.display = 'none';
    _.G(perms + '_change').innerHTML = '+';
  }
}

function toggle_checked(me, perms)
{
  if(me.checked)
  {
    _.G(perms + '_selected').innerHTML = Number(_.G(perms + '_selected').innerHTML) + 1;
  }
  else
  {
    _.G(perms + '_selected').innerHTML = Number(_.G(perms + '_selected').innerHTML) - 1;
  }
}