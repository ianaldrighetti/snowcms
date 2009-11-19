if(typeof online_timeout == 'undefined')
  online_timeout = 15;

// Turn it into JS time...
online_timeout = online_timeout * 1000 * 60;

var mouse_moved = false;
var last_keepalive = (new Date).getTime();

function session_set_timeout(time)
{
  if(!time)
    time = online_timeout;

  setTimeout('session_keepalive();', time);
}

function session_keepalive()
{
  // Mouse not moved..? Sorry!
  if(mouse_moved == false)
  {
    session_set_timeout();
    return false;
  }

  // So is it time? :/
  var diff = (new Date).getTime() - last_keepalive;

  // Give a bit of lee-way though...
  if(diff > online_timeout - 20000)
  {
    _.X(base_url + '/index.php?action=keepalive', function() {}, '');

    // Update some stuff...
    mouse_moved = false;
    last_keepalive = (new Date).getTime();
    session_set_timeout();
  }
  else
  {
    session_set_timeout(10000);
  }
}

// Let's start going...
session_set_timeout();

// Now set mouse_moved to true when you move your mouse :P
_.R(function()
{
  if(typeof document.body != 'undefined')
    document.body.onmousemove = function()
    {
      mouse_moved = true;
    };
  else
    setTimeout('document.body.onmousemove = function() { mouse_moved = true; };', 200);
});