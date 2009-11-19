function popupWindow(popup_url, width, height, allow_scroll)
{
  window.open(popup_url, 'popup_window', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=' + (allow_scroll ? 'yes' : 'no') + ',resizeable=no,width=' + width + ',height=' + height);
}

function defined(name)
{
  return typeof window[name] == "undefined";
}

//http://www.thespanner.co.uk/2009/01/29/detecting-browsers-javascript-hacks/

is_ff = /a/[-1]=='a';
is_ie = !+"\v1";
is_saf = /a/.__proto__=='//';
is_op = /^function \(/.test([].sort);
is_chr = /source/.test((/a/.toString+''));

B=/a/[-1]=='a'?'FF':'\v'=='v'?'IE':/a/.__proto__=='//'?'Saf':/s/.test(/a/.toString)?'Chr':'Op'
