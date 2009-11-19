window.onload = function(){
  if(_.G('post'))
    ta = new textarea('post');
}
function textarea(id)
{
  this.el = _.G(id);
  textarea._el[this.el.id] = this;
  var tmp = this;
  this.el.onchange = this.el.onkeyup = this.el.onclick = this.el.onselect = function(){tmp._updatePosition()};
}

textarea._el = {};

textarea.get = function(id)
{
  if(textarea._el[id])
    return textarea._el[id]
  else
    return new textarea(id);
}

textarea.prototype._updatePosition = function()
{
    if(typeof this.el.createTextRange != "undefined")
        this.caret = document.selection.createRange().duplicate();
}

textarea.prototype.replaceSelection = function(text){
    if(this.caret && this.el.createTextRange){
        this.caret.text = this.caret.text.charAt(this.caret.text.length - 1) == ' ' ? text + ' ' : text;
        this.caret.select();
    }else if(this.el.selectionStart){
        var begin = this.el.value.substr(0, this.el.selectionStart);
        var end = this.el.value.substr(this.el.selectionEnd)
        var scroll = this.el.scrollTop;
        this.el.value = begin + text + end;
        if(this.el.setSelectionRange){
            this.el.focus();
            this.el.setSelectionRange(begin.length + text.length, begin.length + text.length);
        }
        this.el.scrollTop = scroll;
    }else{
        this.el.value += text;
        this.el.focus(this.el.value.length -1);
    }
}

textarea.prototype.surroundSelection = function(before, after){
    if(this.caret && this.el.createTextRange){
        var tmp_len = this.caret.text.length;
        this.caret.text = this.caret.text.charAt(tmp_len - 1) == ' ' ? before + this.caret.text + after + ' ': before + this.caret.text + after;
        if(tmp_len == 0){
			    this.caret.moveStart("character", -after.length);
			    this.caret.moveEnd("character", -after.length);
			    this.caret.select();
        }else
          this.el.focus(this.caret);
    }else if(this.el.selectionStart){
        var begin = this.el.value.substr(0, this.el.selectionStart);
        var end = this.el.value.substr(this.el.selectionEnd)
        var selection = this.el.value.substr(this.el.selectionStart, this.el.selectionEnd - this.el.selectionStart);
        var scroll = this.el.scrollTop;
        var newpos = this.el.selectionStart;
        this.el.value = begin + before + selection + after + end;
        if(this.el.setSelectionRange){
          if(selection.length == 0)
            this.el.setSelectionRange(newpos + before.length, newpos + before.length);
          else
            this.el.setSelectionRange(newpos, newpos + before.length + selection.length + after.length);
          this.el.focus();
        }
        this.el.scrollTop = scroll;
    }else{
      this.el.value += before + after;
      this.el.focus(this.el.value.length - 1);
    }
        
}

function addPollOption()
{
  if(_.G('more_options') && num_options < 255)
  {
    var handle = _.G('more_options');

    // We are making one more option!
    num_options++;

    handle.innerHTML += '<br /><label for="option_' + (num_options - 1) + '">' + (option_str.replace(/%u/i, num_options)) + '</label> <input name="options[]" id="option_' + (num_options - 1) + '" type="text" value="" /> ';
  }
}

function expiration_changed(handle)
{
  if(_.G('results_after_expired') && _.G('results_anyone'))
  {
    // Did the value change at all..?
    if(typeof last_expiration == 'undefined' || last_expiration != handle['poll_expires'].value)
    {
      if(handle['poll_expires'].value > 0)
      {
        // You can choose this option now :P
        _.G('results_after_expired').disabled = false;
      }
      else
      {
        // Nope, you cannot!
        _.G('results_after_expired').disabled = true;

        if(_.G('results_after_expired').checked)
          _.G('results_anyone').checked = true;
      }
    }
  }
}

function preview_message(handle)
{
  if(_.G('post_preview'))
  {
    element = _.G('post_preview');

    // Loading...
    element.innerHTML = '<p class="italic center">' + loading_text + '</p>';

    // Got a poll..?
    has_poll = (_.G('question') ? true : false);

    // Ask the server to parse some things.
    _.X(base_url + '/index.php?action=interface;sa=post_preview', function(pData)
      {
        var data = _.S(pData, true);

        if(data['error'].length > 0)
          alert(data['error']);
        else
        {
          if(_.G('question') && data['question_error'])
            _.G('question').className = 'red_border';
          else if(_.G('question'))
            _.G('question').className = '';

          if(data['subject_error'])
            _.G('post_subject').className = 'red_border';
          else
            _.G('post_subject').className = '';

          if(data['post_error'])
            _.G('post').className = 'red_border';
          else
            _.G('post').className = 'post_editor';

          // Errors? D:
          _.G('post_errors').innerHTML = '';
          if(data['errors'].length > 0)
          {
            for(var i = 0; i < data['errors'].length; i++)
              _.G('post_errors').innerHTML += '<p class="error center">' + data['errors'][i] + '</p>';
          }

          element.innerHTML = '<div class="preview_header">' + data['subject'] + '</div><div class="preview_body">' + data['post'] + '</div>';

          // Un-hide the hide preview button? :P
          if(_.G('hide_preview_button') && _.G('hide_preview_button').style.display == 'none')
          {
            _.G('hide_preview_button').style.display = 'table-cell';
          }
        }
      }, (has_poll ? 'poll=true&question=' + encodeURIComponent(handle['question'].value) + '&' : '') + 'subject=' + encodeURIComponent(handle['post_subject'].value) + '&post=' + encodeURIComponent(handle['post'].value) + '&parse_bbc=' + (!handle['no_bbc'].checked ? 1 : 0) + '&parse_smileys=' + (!handle['no_smileys'].checked ? 1 : 0));
  }
}

function hide_preview()
{
  if(_.G('hide_preview_button') && _.G('hide_preview_button').style.display != 'none')
  {
    _.G('hide_preview_button').style.display = 'none';
    _.G('post_preview').innerHTML = '';
  }
}
