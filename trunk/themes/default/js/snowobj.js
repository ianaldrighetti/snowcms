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

  this.ajax = function(request_url, post_data)
  {
    var xmlObject = this.xmlRequest();

    // Do the open thing...
    xmlObject.open(post_data ? 'POST' : 'GET', request_url, false);
    xmlObject.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlObject.send(post_data ? post_data : '');

    return xmlObject.responseText;
  };

  this.ajaxCallback = function(request_url, callback, post_data)
  {
    var xmlObject = this.xmlRequest();

    // Set our function :P
    xmlObject.onreadystatechange = function()
    {
      // Lets check how its going :P
      if(xmlObject.readyState == 4)
      {
        callback(xmlObject.responseText);
      }
      else
        return false;
    };

    // Give it the URL and stuff...
    xmlObject.open(post_data ? 'POST' : 'GET', request_url, true);
    xmlObject.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlObject.send(post_data ? post_data : '');
  };

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

  this.json_encode = function(obj){
    //simple partial JSON encoder implementation
    //http://gist.github.com/gists/240659 stolen from me

    if(window.JSON && JSON.stringify) return JSON.stringify(obj);
    var enc = arguments.callee; //for purposes of recursion

    if(typeof obj == "boolean" || typeof obj == "number"){
        return obj+'' //should work...
    }else if(typeof obj == "string"){
      //a large portion of this is stolen from Douglas Crockford's json2.js
      return '"'+
            obj.replace(
              /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g
            , function (a) {
              return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
            })
            +'"'; //note that this isn't quite as purtyful as the usualness
    }else if(this.is_array(obj)){
      for(var i = 0; i < obj.length; i++){
        obj[i] = enc(obj[i]); //encode every sub-thingy on top
      }
      return "["+obj.join(",")+"]";
    }else{
      var pairs = []; //pairs will be stored here
      for(var k in obj){ //loop through thingys
        pairs.push(enc(k)+":"+enc(obj[k])); //key: value
      }
      return "{"+pairs.join(",")+"}" //wrap in the braces
    }
  }

  this.json = function(data, serialize)
  {
    if(serialize)
    {
      return this.json_encode(data)
    }
    else
      return eval('(' + data + ')');
  }

  this.onload = function(callback)
  {
    if(typeof window.onload != 'function')
      window.onload = callback;
    else
    {
      var prevCallback = window.onload;

      window.onload = function()
      {
        if(prevCallback)
          prevCallback();

        callback();
      };
    }
  };

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
    var cookieExpires = null;
    if(days)
    {
      cookieExpires = new Date();
      cookieExpires.setTime(cookieExpires.getTime() + (days * 86400000))
    }

    this.d.cookie = cookie_name + '=' + this.encode(value) + (!days ? '' : ('; expires=' + cookieExpires.toUTCString()));
  };

  this.trim = function(str)
  {
    return str.replace(/^\s+|\s+$/g, '');
  };

  this.truncate = function(element, maxlength)
  {
    if(typeof element.value != 'undefined')
    {
      if(element.value.length > maxlength)
        element.value = element.value.substring(0, maxlength);

      return true;
    }
    else
      return false;
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