function admin_update_request(response)
{
  var response = s.json(response);

  if(typeof response['error'] != 'undefined' && response['error'].length > 0)
  {
    alert(response['error']);
  }
  else
  {
    s.id('your_version').style.color = response['needs_update'] ? 'red' : 'green';
    s.id('latest_version').style.fontStyle = 'normal';
    s.id('latest_version').innerHTML = response['version'];
    s.id('latest_version').title = '';

    var h1 = document.createElement('h1');
    h1.innerHTML = response['header'];

    var text = document.createElement('p');
    text.innerHTML = response['text'];

    s.id('response').innerHTML = '';
    s.id('response').appendChild(h1);
    s.id('response').appendChild(text);

    if(response['needs_update'])
    {
      var step = document.createElement('p');
      step.id = 'current_step';
      step.style.marginTop = '10px';
      step.innerHTML = '<a href="javascript:void(0);" onclick="admin_update_step_1();">Download update!</a>';

      s.id('response').appendChild(step);
    }
  }
}

function admin_update_step_1()
{
  alert(s.ajax(base_url + '/index.php?action=admin&sa=ajax&id=update&step=1'));
}

s.onload(function() { s.ajaxCallback(base_url + '/index.php?action=admin&sa=ajax&id=update', admin_update_request, {'check_type': 'version'}); });