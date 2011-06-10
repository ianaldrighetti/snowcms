var element = null;

function check_plugin_status()
{
  s.ajaxCallback(base_url + '/index.php?action=admin&sa=ajax&id=plugins_add&step=1&sid=' + session_id, function(response)
    {
      var response = s.json(response);

      if(typeof response['error'] != 'undefined' && response['error'].length > 0)
      {
        s.id('plugin_status').innerHTML = '<p style="color: red; font-weight: bold;">' + response['error'] + '</p>';
      }
      else
      {
        s.id('plugin_status').style.border = response['message']['border'];
        s.id('plugin_status').style.background = response['message']['background'];
        s.id('plugin_status').innerHTML = response['message']['text'];

        if(response['message']['proceed'])
        {
          extract_plugin();
        }
        else
        {
          s.id('plugin_status').innerHTML += '<div style="text-align: center !important;"><a href="javascript:void(0);" onclick="prompt_extract_plugin();">' + l['proceed with install'] + '</a> | <a href="javascript:void(0);" onclick="cancel_install(this);">' + l['cancel install'] + '</a></div>';
        }
      }
    }, 'filename=' + s.encode(filename));

  var h3 = document.createElement('h3');
  h3.innerHTML = l['checking status'];

  var div = document.createElement('div');
  div.id = 'plugin_status';
  div.style.margin = '10px';
  div.style.padding = '5px';
  div.innerHTML = '<p style="font-style: italic;">' + l['please wait'] + '</p>';

  element.appendChild(h3);
  element.appendChild(div);
}

function extract_plugin()
{
  s.ajaxCallback(base_url + '/index.php?action=admin&sa=ajax&id=plugins_add&step=2&sid=' + session_id, function(response)
    {
      var response = s.json(response);

      if(typeof response['error'] != 'undefined' && response['error'].length > 0)
      {
        s.id('extract_status').innerHTML = '<p style="color: red; font-weight: bold;">' + response['error'] + '</p>';
      }
      else
      {
        s.id('extract_status').innerHTML = response['message'];
        finalize_install();
      }
    }, 'filename=' + s.encode(filename));

  var h3 = document.createElement('h3');
  h3.innerHTML = l['extracting plugin'];

  var div = document.createElement('div');
  div.id = 'extract_status';
  div.innerHTML = '<p style="font-style: italic;">' + l['please wait'] + '</p>';

  element.appendChild(h3);
  element.appendChild(div);
}

function prompt_extract_plugin()
{
  if(confirm(l['are you sure']))
  {
    extract_plugin();
  }
}

function cancel_install(element)
{
  element.innerHTML = l['canceling'];

  s.ajaxCallback(base_url + '/index.php?action=admin&sa=ajax&id=plugins_add&step=cancel&sid=' + session_id, function(response)
    {
      location.href = base_url + '/index.php?action=admin';
    }, 'filename=' + s.encode(filename));
}

function finalize_install()
{
  s.ajaxCallback(base_url + '/index.php?action=admin&sa=ajax&id=plugins_add&step=3&sid=' + session_id, function(response)
    {
      var response = s.json(response);

      if(typeof response['error'] != 'undefined' && response['error'].length > 0)
      {
        s.id('finalize_install').innerHTML = '<p style="color: red; font-weight: bold;">' + response['error'] + '</p>';
      }
      else
      {
        s.id('finalize_install').innerHTML = response['message'];
      }
    }, 'filename=' + s.encode(filename));

  var h3 = document.createElement('h3');
  h3.innerHTML = l['finalize install'];

  var div = document.createElement('div');
  div.id = 'finalize_install';
  div.innerHTML = '<p style="font-style: italic;">' + l['please wait'] + '</p>';

  element.appendChild(h3);
  element.appendChild(div);
}

s.onload(function()
  {
    element = s.id('plugin_progress');
    check_plugin_status();
  });