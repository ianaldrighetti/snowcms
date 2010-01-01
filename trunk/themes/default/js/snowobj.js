var user_agent = navigator.userAgent;

function SnowObj()
{
  this.is_opera = user_agent.indexOf('Opera') != -1;
  this.is_ie = user_agent.indexOf('MSIE') != -1 && !this.is_opera;
  this.is_trident = this.is_ie;
  this.is_ff = user_agent.indexOf('Firefox') != -1;
  this.is_gecko = this.is_ff;
  this.is_safari = (user_agent.indexOf('Safari') != -1 || user_agent.indexOf('AppleWebKit') != -1) && user_agent.indexOf('Chrome') == -1;
  this.is_chrome = user_agent.indexOf('Chrome') != -1;
  this.is_webkit = this.is_safari || this.is_chrome || user_agent.indexOf('Konqueror') != -1;

  this.cookie = function(cookie_name)
  {
    var cookies = this.d.cookie;

    // Anything cookie wise even set..?
    if(cookies.length > 0)
    {
      // Any ;? Means multiple cookies!
      if(cookies.indexOf(';') > -1)
      {
        var cookies = cookies.split(';');

        for(cookie in cookies)
        {
          // Trim it up..!
          var temp = this.trim(cookies[cookie]);

          if(decodeURIComponent(this.trim(temp).substr(0, this.trim(temp).indexOf('='))) == cookie_name)
            return decodeURIComponent(temp.substr(temp.indexOf('=') + 1, temp.length));
        }

        // Nothing huh?
        return false;
      }
      else
      {
        // Only one cookie? Its gotta be this or nothing!!!
        if(cookies.substr(0, cookie_name.length) == cookie_name)
          return decodeURIComponent(cookies.substr(cookie_name.length + 1, cookies.length));
        else
          return false;
      }
    }
    else
      return false;
  };

  this.d = document;

  this.decode = function(str)
  {
    return decodeURIComponent(str);
  };

  this.encode = function(str)
  {
    return encodeURIComponent(str);
  };

  this.id = function(element_id)
  {
    return document.getElementById(element_id);
  };

  this.is_array = function(checkObject)
  {
    // Thanks to: http://ajaxian.com/archives/isarray-why-is-it-so-bloody-hard-to-get-right
    return Object.prototype.toString.call(checkObject) === '[object Array]' ? true : false;
  };

  this.json = function(data, serialize)
  {
    if(serialize)
    {
      // Number..? Its just that ;)
      if(typeof data == 'number')
        return data;
      else if(typeof data == 'string')
        return '"' + data + '"';
      else if(typeof data == 'object')
      {
        var serializedString = '';
        var values = new Array();

        // idk how to do this!!!
        if(this.is_array(data))
        {

        }
        else
        {
        }

        return '{' + serializedString + '}';
      }
    }
    else
      return eval('(' + data + ')');
  }

  this.screenheight = function()
  {
    if(!this.is_ie)
      return window.innerHeight;
    else if(typeof document.documentElement.clientHeight != 'undefined')
      return document.documentElement.clientHeight;
    else
      return document.getElementsByTagName('body')[0].clientHeight;
  };

  this.screenwidth = function()
  {
    if(!this.is_ie)
      return window.innerWidth;
    else if (typeof document.documentElement.clientWidth != 'undefined')
      return document.documentElement.clientWidth;
    else
      return document.getElementsByTagName('body')[0].clientWidth;
  };

  this.setcookie = function(cookie_name, value, days)
  {
    if(days)
      var cookieExpires = (new Date()).setTime((new Date()).getTime() + (days * 86400000));

    this.d.cookie = cookie_name + '=' + this.encode(value) + (!days ? '' : ('; expires=' + cookieExpires.toGMTString()));
  };

  this.trim = function(str)
  {
    return str.replace(/^\s+|\s+$/g, '');
  };

  this.xmlRequest = function()
  {
    var xmlRequest;

    try
    {
      // Firefox, Opera, Safari, Chrome and IE8 >=
      xmlRequest = new XMLHttpRequest();
    }
    catch(e)
    {
      try
      {
        xmlRequest = new ActiveXObject('Microsoft.XMLHTTP');
      }
      catch(e)
      {
        return false;
      }
    }

    return xmlRequest;
  };
}
s = new SnowObj();