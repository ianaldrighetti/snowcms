function textarea(element_id)
{
	this.el = s.id(element_id);
	this._el[this.el.id] = this;

	var tmp = this;
	this.el.onchange = this.el.onkeyup = this.el.onclick = this.el.onselect = function()
		{
			tmp._updatePosition()
		};
}

textarea.prototype.el = null;
textarea.prototype.caret = null;
textarea.prototype._el = {};

textarea.prototype.get = function(element_id)
{
	if(typeof textarea._el[element_id] != 'undefined')
	{
		return textarea._el[element_id];
	}
	else
	{
		return new textarea(element_id);
	}
}

textarea.prototype._updatePosition = function()
{
	if(typeof this.el.createTextRange != 'undefined')
	{
		this.caret = document.selection.createRange().duplicate();
	}
}

textarea.prototype.replaceSelection = function(text)
{
	if(this.caret && this.el.createTextRange)
	{
		this.caret.text = this.caret.text.charAt(this.caret.text.length - 1) == ' ' ? text + ' ' : text;
		this.caret.select();
	}
	else if(typeof this.el.selectionStart == 'number')
	{
		var begin = this.el.value.substr(0, this.el.selectionStart);
		var end = this.el.value.substr(this.el.selectionEnd)
		var scroll = this.el.scrollTop;
		this.el.value = begin + text + end;

		if(this.el.setSelectionRange)
		{
			this.el.focus();
			this.el.setSelectionRange(begin.length + text.length, begin.length + text.length);
		}

		this.el.scrollTop = scroll;
	}
	else
	{
		this.el.value += text;
		this.el.focus(this.el.value.length -1);
	}
}

textarea.prototype.surroundSelection = function(before, after)
{
	if(this.caret && this.el.createTextRange)
	{
		var tmp_len = this.caret.text.length;
		this.caret.text = this.caret.text.charAt(tmp_len - 1) == ' ' ? before + this.caret.text + after + ' ': before + this.caret.text + after;
		if(tmp_len == 0)
		{
			this.caret.moveStart("character", -after.length);
			this.caret.moveEnd("character", -after.length);
			this.caret.select();
		}
		else
		{
			this.el.focus(this.caret);
		}
	}
	else if(typeof this.el.selectionStart == 'number')
	{
		var begin = this.el.value.substr(0, this.el.selectionStart);
		var end = this.el.value.substr(this.el.selectionEnd)
		var selection = this.el.value.substr(this.el.selectionStart, this.el.selectionEnd - this.el.selectionStart);
		var scroll = this.el.scrollTop;
		var newpos = this.el.selectionStart;
		this.el.value = begin + before + selection + after + end;

		if(this.el.setSelectionRange)
		{
			if(selection.length == 0)
			{
				this.el.setSelectionRange(newpos + before.length, newpos + before.length);
			}
			else
			{
				this.el.setSelectionRange(newpos, newpos + before.length + selection.length + after.length);
			}

			this.el.focus();
		}

		this.el.scrollTop = scroll;
	}
	else
	{
		this.el.value += before + after;
		this.el.focus(this.el.value.length - 1);
	}
}