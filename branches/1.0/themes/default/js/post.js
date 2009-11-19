function format_quote(data)
{
  return '[quote author=' + data['author'] + ' msg=' + data['id'] + ' time=' + data['time'] + ']\r\n' + data['body'] + '\r\n[/quote]';
}

function quick_quote_reply(msg_id)
{
  _.X(base_url + '/index.php?action=interface;sa=ajax_quote', function(pData)
    {
      var data = _.S(pData, true);

      if(typeof data['error'] != 'undefined')
        alert(data['error']);
      else
        ta.replaceSelection(format_quote(data));
    }, 'msg=' + encodeURIComponent(msg_id));
}

function quick_quote(msg_id)
{
  // Is the quick reply even showing..?
  if(_.G('quick_reply') && _.G('post') && typeof ta != 'undefined')
  {
    // Is it opened..?
    if(_.G('quick_reply').style.display != 'none')
    {
      // We are doing it the AJAX way!
      quick_quote_reply(msg_id);

      return false;
    }
    else
      return true;
  }
  else
    // It isn't here, so go load the page ._.
    return true;
}