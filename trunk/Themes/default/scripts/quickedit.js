
  /*QuickEdit*/
  function quickEdit(tid, mid){
    vX("forum.php?bbcode="+mid, function(e){
      var el = document.getElementsByName("pcmid"+mid)[0];
      var bak = el.innerHTML;
      el.innerHTML = "<input type=\"hidden\" value=\""+tid+";"+mid+"\"><textarea name=\"editor\" style=\"width: 99%; height: 200px\">"+e+"</textarea><br><input type=\"button\" onClick=\"quickEdit_save(this.parentNode)\" value=\"Save\"><input type=\"button\" value=\"Cancel\" onClick=\"quickEdit_cancel(this.parentNode)\"><textarea name=\"backup\" style=\"display: none\">"+bak+"</textarea>";
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
        cnt.getElementsByTagName("p")[0].innerHTML = x;
      });
    },"edit="+tmid[1]+"&body="+cnt.getElementsByTagName("textarea")[0].value)
  }