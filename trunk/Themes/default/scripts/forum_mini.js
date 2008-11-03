var qqtemp = "";

  /*QuickEdit*/
  function quickEdit(tid, mid){
    var el = _.G("pcmid"+mid);
    var bak = el.innerHTML;
    el.innerHTML = "<input type=\"hidden\" value=\""+tid+";"+mid+"\"><textarea disabled=\"true\" name=\"editor\" style=\"width: 99%; height: 200px\">Loading...</textarea><br><input type=\"button\" onClick=\"quickEdit_save(this.parentNode)\" value=\"Save\"><input type=\"button\" value=\"Cancel\" onClick=\"quickEdit_cancel(this.parentNode)\"><textarea name=\"backup\" style=\"display: none\">"+bak+"</textarea>";

    _.X("forum.php?bbcode="+mid, function(e){
      var s = _.S(e,true); //deserialize JSON
      el.getElementsByTagName("textarea")[0].disabled = false;
      el.getElementsByTagName("textarea")[0].value = unescape(s.bbcode.replace(/\+/g,"%20"));
    })
  }
  
  function quickEdit_cancel(cnt){
    cnt.innerHTML = cnt.getElementsByTagName("textarea")[1].value
  }
  
  function quickEdit_save(cnt){
    var tmid = cnt.getElementsByTagName("input")[0].value.split(";");

    _.X("forum.php?action=post2;topic="+tmid[0], function(e){
      _.X("forum.php?html="+tmid[1], function(x){
        var q = _.S(x,true); //deserialize JSON
        cnt.innerHTML = cnt.getElementsByTagName("textarea")[1].value;
        cnt.getElementsByTagName("div")[0].innerHTML = unescape(q.html.replace(/\+/g,"%20"));
      });
    },"edit="+tmid[1]+"&body="+encodeURIComponent(cnt.getElementsByTagName("textarea")[0].value).replace(/%20/g,"+"))
    
    cnt.getElementsByTagName("textarea")[0].value = "Saving..."
    cnt.getElementsByTagName("textarea")[0].disabled = true;
  }
  
  
  function quickQuote(tid, mid){
    window.location.href = "#quickreply";
    var qrt = _.G("quickreplyinput")
    qrt.disabled = true;
    qqtemp = qrt.value;
    qrt.value = "Loading...";
    try{
      qrt.focus();
    }catch(err){}
    
    _.X("forum.php?bbcode="+mid, function(e){
      var s = _.S(e,true); //deserialize JSON
      qrt.disabled = false;
      qrt.value = qqtemp+"[quote by=\""+s.poster_name+"\"]\n"+_.H(unescape(s.bbcode.replace(/\+/g," ")), true)+"\n[/quote]\n";
      try{
        qrt.focus();
      }catch(err){}
    })
  }