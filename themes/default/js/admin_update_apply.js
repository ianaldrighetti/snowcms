var element = null;
function download_update()
{
  s.ajaxCallback(base_url + '/index.php?action=admin&sa=update&apply=' + version + '&step=1', function(response)
    {
      var response = s.json(response);

      if(typeof response['error'] != 'undefined' && response['error'].length > 0)
      {
        s.id('download_status').innerHTML = '<p style="color: red; font-weight: bold;">' + response['error'] + '</p>';
      }
      else
      {
        s.id('download_status').innerHTML = '<p style="color: green;">' + response['message'] + '</p>';

        extract_update();
      }
    });

  var h3 = document.createElement('h3');
  h3.innerHTML = l['downloading'];

  var div = document.createElement('div');
  div.id = 'download_status';
  div.innerHTML = '<p style="font-style: italic;">' + l['please wait'] + '</p>';

  element.appendChild(h3);
  element.appendChild(div);
}

function extract_update()
{
  s.ajaxCallback(base_url + '/index.php?action=admin&sa=update&apply=' + version + '&step=2', function(response)
    {
      var response = s.json(response);

      if(response['error'].length > 0)
      {
        s.id('extract_status').innerHTML = '<p style="color: red; font-weight: bold;">' + response['error'] + '</p>';
      }
      else
      {
        s.id('extract_status').innerHTML = '<p style="color: green;">' + response['message'] + '</p>';

        copy_update();
      }
    });

  var h3 = document.createElement('h3');
  h3.innerHTML = l['extracting'];

  var div = document.createElement('div');
  div.id = 'extract_status';
  div.innerHTML = '<p style="font-style: italic;">' + l['please wait'] + '</p>';

  element.appendChild(h3);
  element.appendChild(div);
}

function copy_update()
{
  s.ajaxCallback(base_url + '/index.php?action=admin&sa=update&apply=' + version + '&step=3', function(response)
    {
      var response = s.json(response);

      if(response['error'].length > 0)
      {
        s.id('copy_status').innerHTML = '<p style="color: red; font-weight: bold;">' + response['error'] + '</p>';
      }
      else
      {
        var files = response['message'];

        for(var i = 0; i < files.length; i++)
        {
          copy_file(files[i]);
        }

        s.id('copy_status').innerHTML = '<p style="color: green;">' + l['copy success'] + '</p>';

        execute_update();
      }
    });

  var h3 = document.createElement('h3');
  h3.innerHTML = l['copying'];

  var div = document.createElement('div');
  div.id = 'copy_status';
  div.innerHTML = '<p style="font-style: italic;">' + l['please wait'] + '</p>';

  element.appendChild(h3);
  element.appendChild(div);
}

function copy_file(filename)
{
  s.id('copy_status').innerHTML = '<p><span style="font-weight: bold;">' + l['currently copying'] + '</span> ' + filename + '</p>';
  s.ajax(base_url + '/index.php?action=admin&sa=update&apply=' + version + '&step=4', 'filename=' + encodeURIComponent(filename));
}

function execute_update()
{
  s.ajaxCallback(base_url + '/index.php?action=admin&sa=update&apply=' + version + '&step=5', function(response)
    {
      var response = s.json(response);

      if(response['error'].length > 0)
      {
        s.id('execute_status').innerHTML = '<p style="color: red; font-weight: bold;">' + response['error'] + '</p>';
      }
      else
      {
        s.id('execute_status').innerHTML = '<p>' + response['message'] + '</p>';
      }
    });

  var h3 = document.createElement('h3');
  h3.innerHTML = l['executing'];

  var div = document.createElement('div');
  div.id = 'execute_status';
  div.innerHTML = '<p style="font-style: italic;">' + l['please wait'] + '</p>';

  element.appendChild(h3);
  element.appendChild(div);
}

if(start_update == -1)
  s.onload(function()
    {
      element = s.id('update_progress');
      download_update();
    });