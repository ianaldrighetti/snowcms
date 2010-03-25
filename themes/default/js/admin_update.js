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

    var h1 = document.createElement('h1');
    h1.innerHTML = response['header'];

    var text = document.createElement('p');
    text.innerHTML = response['text'];

    s.id('response').innerHTML = '';
    s.id('response').appendChild(h1);
    s.id('response').appendChild(text);
  }
}

s.onload(function() { s.ajaxCallback(base_url + '/index.php?action=admin&sa=ajax&id=update', admin_update_request, {'check_type': 'version'}); });