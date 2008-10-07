  /*QuickEdit*/
  function quickEdit(tid, mid){
    var el = document.getElementById("pcmid"+mid);
    var bak = el.innerHTML;
    el.innerHTML = "<input type=\"hidden\" value=\""+tid+";"+mid+"\"><textarea disabled=\"true\" name=\"editor\" style=\"width: 99%; height: 200px\">Loading...</textarea><br><input type=\"button\" onClick=\"quickEdit_save(this.parentNode)\" value=\"Save\"><input type=\"button\" value=\"Cancel\" onClick=\"quickEdit_cancel(this.parentNode)\"><textarea name=\"backup\" style=\"display: none\">"+bak+"</textarea>";

    vX("forum.php?bbcode="+mid, function(e){
      el.getElementsByTagName("textarea")[0].disabled = false;
      el.getElementsByTagName("textarea")[0].value = e;
    })
  }
  
  function quickEdit_cancel(cnt){
    cnt.innerHTML = cnt.getElementsByTagName("textarea")[1].value
  }
  
  function quickEdit_save(cnt){
    var tmid = cnt.getElementsByTagName("input")[0].value.split(";");

    vX("forum.php?action=post2;topic="+tmid[0], function(e){
      vX("forum.php?html="+tmid[1], function(x){
        cnt.innerHTML = cnt.getElementsByTagName("textarea")[1].value;
        cnt.getElementsByTagName("div")[0].innerHTML = x;
      });
    },"edit="+tmid[1]+"&body="+encodeURIComponent(cnt.getElementsByTagName("textarea")[0].value))
    
    cnt.getElementsByTagName("textarea")[0].value = "Saving..."
    cnt.getElementsByTagName("textarea")[0].disabled = true;
  }