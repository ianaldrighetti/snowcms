function new_autofill(textbox, fillbox, url, post, type){
  var list=[]
  var last_query = -1;
  if(type == "multi"){
    get_value = function(){
      var valuesplit = textbox.value.split(",");
      return valuesplit[valuesplit.length-1];
    }
    set_textbox = function(textvalue){
      var index = textbox.value.lastIndexOf(",");
	    textbox.value = textbox.value.substr(0,index) + (index==-1?"":",") + textvalue + ",";
    }
    exclude = function(test){
      var value = get_value();
      return test.toLowerCase().indexOf(value.toLowerCase()) != 0 || test == value || _.index(test,textbox.value.split(",")) != -1
    }
  }else{
    get_value = function(){
      return textbox.value;
    }
    set_textbox = function(textvalue){
	    textbox.value = textvalue;
    }
    exclude = function(test){
      var value = get_value();
      return test.toLowerCase().indexOf(value.toLowerCase()) != 0 || test == value;
    }
  }
  function update_list(){
    var value = get_value()
    fillbox.innerHTML = ""
    for(var i = 0; i < list.length; i++){
      if(!exclude(list[i])){
        fillbox.style.display = "block"
        var li = _.d.createElement("li");
        li.className = "autofillitem"
	      li.innerHTML = "<a href='javascript:void(0)'>"+list[i].replace(value, "<b>"+value+"</b>")+"</a>"
	      li.textvalue = list[i]
	      li.onmousedown = function(){
	        set_textbox(this.textvalue)
	        setTimeout(function(){textbox.focus()},100);
	      }
        fillbox.appendChild(li)
      }
    }
  }
  function update_box(){
    var pos = _.pos(textbox)
    fillbox.style.left = pos.l + "px"
    fillbox.style.top = pos.t + pos.h + "px"
    fillbox.style.width = pos.w + "px"
    fillbox.innerHTML = ""
    fillbox.style.display = "none"
  }
  function check_list(){
    var value = get_value()
    
    if(value != ""){
      if(value.indexOf(last_query) == 0){
        update_list()
        last_query = value;
      }else{
        //setTimeout(function(){
        _.ajax(_.M(url, {string: value}), function(data){
          list = _.json(data,true)
          update_list()
          last_query = value;
        },post?_.M(post, {string: value}):null)
        //}, 500)
      }
    }
  }
  _.E(textbox, "blur", function(){
    setTimeout(function(){
      fillbox.innerHTML = ""
      fillbox.style.display = "none"
    },100)
  })
  _.E(textbox, "focus", function(){
    update_box()
    check_list()
  })
  _.E(textbox, "keyup", function(){
    update_box()
    check_list()
  })
}
