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
			{
				return false;
			}
		};

		// Give it the URL and stuff...
		xmlObject.open(post_data ? 'POST' : 'GET', request_url, true);
		xmlObject.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlObject.send(post_data ? post_data : '');
	};

	this.checkallnone = function(element)
	{
		// Get all the checkboxes...
		var checkboxes = element.parentNode.parentNode.parentNode.getElementsByTagName('input');

		for(var i = 0; i < checkboxes.length; i++)
		{
			if(checkboxes[i].name == 'selected[]')
			{
				checkboxes[i].checked = element.checked;
			}
		}
	}

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

					if(this.decode(this.trim(temp).substr(0, this.trim(temp).indexOf('='))) == cookie_name)
					{
						return this.decode(temp.substr(temp.indexOf('=') + 1, temp.length));
					}
				}

				// Nothing huh?
				return false;
			}
			else
			{
				// Only one cookie? Its gotta be this or nothing!!!
				if(cookies.substr(0, cookie_name.length) == cookie_name)
				{
					return this.decode(cookies.substr(cookie_name.length + 1, cookies.length));
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
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

	this.in_array = function(needle, haystack)
	{
		for(index in haystack)
		{
			// Did we find it?
			if(haystack[index] == needle)
			{
				// We sure did!
				return true;
			}
		}

		// We did not :/
		return false;
	};

	this.is_array = function(checkObject)
	{
		// Thanks to: http://ajaxian.com/archives/isarray-why-is-it-so-bloody-hard-to-get-right
		return Object.prototype.toString.call(checkObject) === '[object Array]';
	};

	this.json_encode = function(obj)
	{
		// Simple partial JSON encoder implementation
		// http://gist.github.com/gists/240659 stolen from me

		if(window.JSON && JSON.stringify)
		{
			return JSON.stringify(obj);
		}

		var enc = arguments.callee; //for purposes of recursion

		if(typeof obj == 'boolean' || typeof obj == 'number')
		{
				return obj.toString();
		}
		else if(typeof obj == 'string')
		{
			// A large portion of this is used from Douglas Crockford's json2.js
			return '"'+ obj.replace(/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
															function(a)
															{
																return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
															}) + '"';
		}
		else if(this.is_array(obj))
		{
			for(var i = 0; i < obj.length; i++)
			{
				obj[i] = enc(obj[i]);
			}

			return '[' + obj.join(',') + ']';
		}
		else
		{
			var pairs = [];
			for(var k in obj)
			{
				pairs.push(enc(k) + ':' + enc(obj[k]));
			}

			return '{' + pairs.join(',') + '}';
		}
	}

	this.json = function(data, serialize)
	{
		if(serialize)
		{
			return this.json_encode(data)
		}
		else
		{
			return eval('(' + data + ')');
		}
	}

	this.onload = function(callback)
	{
		if(typeof window.onload != 'function')
		{
			window.onload = callback;
		}
		else
		{
			var prevCallback = window.onload;

			window.onload = function()
			{
				if(prevCallback)
				{
					prevCallback();
				}

				callback();
			};
		}
	};

	this.screenheight = function()
	{
		if(!this.is_ie)
		{
			return window.innerHeight;
		}
		else if(typeof document.documentElement.clientHeight != 'undefined')
		{
			return document.documentElement.clientHeight;
		}
		else
		{
			return document.getElementsByTagName('body')[0].clientHeight;
		}
	};

	this.screenwidth = function()
	{
		if(!this.is_ie)
		{
			return window.innerWidth;
		}
		else if (typeof document.documentElement.clientWidth != 'undefined')
		{
			return document.documentElement.clientWidth;
		}
		else
		{
			return document.getElementsByTagName('body')[0].clientWidth;
		}
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

	this.submitform = function(form_name, form, fields)
	{
		if(!s.id(form_name))
		{
			// The form doesn't appear to exist :/
			return true;
		}

		s.id(form_name + '_errors').innerHTML = '';
		s.id(form_name + '_message').innerHTML = '';

		// Time to send the form data.
		var data = [];
		for(var i = 0; i < fields.length; i++)
		{
			// A checkbox, perhaps?
			if(form[fields[i]].type == 'checkbox')
			{
				data[data.length] = fields[i] + '=' + (form[fields[i]].checked ? 1 : 0);
			}
			else if(form[fields[i]].type == 'select-multiple')
			{
				for(var j = 0; j < form[fields[i]].options.length; j++)
				{
					if(form[fields[i]].options[j].selected)
					{
						data[data.length] = fields[i] + '[]=' + this.encode(form[fields[i]].options[j].value);
					}
				}
			}
			else if(form[fields[i]].type == 'file')
			{
				// Sorry, we don't do files! (Returning true will make the form submit.
				return true;
			}
			else
			{
				data[data.length] = fields[i] + '=' + this.encode(form[fields[i]].value);
			}
		}

		data[data.length] = form_name + '=ajax';

		// Disable the save button.
		var saveText = form[form_name].value;
		form[form_name].disabled = 'disabled';
		form[form_name].value = form_saving;

		s.ajaxCallback((form.action.indexOf('?') > -1 ? form.action + '&ajax=1' : form.action + '?ajax=1') + '&time=' + (new Date).getTime(), function(response)
			{
				var response = s.json(response);

				// Errors? Perhaps.
				if(response['errors'].length > 0)
				{
					var div = document.createElement('div');
					div.className = 'errors';

					for(var i = 0; i < response['errors'].length; i++)
					{
						var p = document.createElement('p');
						p.innerHTML = response['errors'][i];

						div.appendChild(p);
					}

					s.id(form_name + '_errors').appendChild(div);
				}

				// You could be displaying a message too, maybe.
				if(response['message'].length > 0)
				{
					var div = document.createElement('div');
					div.className = 'message';
					div.innerHTML = response['message'];

					s.id(form_name + '_message').appendChild(div);
				}

				// Update to the new values ;-)
				for(index in response['values'])
				{
					if(typeof form[index].type != 'undefined')
					{
						// What's your type? ;-)
						if(form[index].type == 'text' || form[index].type == 'password' || form[index].type == 'hidden' || form[index].type == 'textarea')
						{
							// Simple, that's what!
							form[index].value = response['values'][index]['value'];
						}
						else if(form[index].type == 'checkbox')
						{
							// Checkboxes are pretty simple too.
							form[index].checked = response['values'][index]['value'] == 1 ? 'checked' : '';
						}
						else if(form[index].type == 'select-one')
						{
							for(var i = 0; i < form[index].options.length; i++)
							{
								// To select, or not to select? That is the question!
								if(form[index].options[i].value == response['values'][index]['value'])
								{
									form[index].options[i].selected = 'selected';
								}
							}
						}
						else if(form[index].type == 'select-multiple')
						{
							var selected = response['values'][index].value.toString().split(',');

							for(var i = 0; i < form[index].options.length; i++)
							{
								form[index].options[i].selected = '';

								for(var j = 0; j < selected.length; j++)
								{
									if(form[index].options[i].value == selected[j])
									{
										form[index].options[i].selected = 'selected';
									}
								}
							}
						}
						else
						{
							alert(form[index].type);
						}
					}
					else
					{
						alert(index);
					}
				}

				form[form_name].value = saveText;
				form[form_name].disabled = '';
			}, data.join('&'));
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
			{
				element.value = element.value.substring(0, maxlength);
			}

			return true;
		}
		else
		{
			return false;
		}
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