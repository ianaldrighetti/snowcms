/*vX Ajax Function. (C) Antimatter15 2008*/
function vX(u,f,p){var x=(window.ActiveXObject)?new ActiveXObject("Microsoft.XMLHTTP"):new XMLHttpRequest();
x.open(p?"POST":"GET",u,true);if(p) x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
x.onreadystatechange=function(){if(x.readyState==4&&x.status==200) f(x.responseText)};x.send(p)}