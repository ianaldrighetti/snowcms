var dropdown_array = [];

function new_dropdown(menu, trigger)
{
  if(!menu && !trigger)
    return;
  var li = _.array(menu.getElementsByTagName('li'));
  
  var timeout = null;
  
  function dclose()
  {
    menu.style.zIndex = 42
    
    if((new Date).getTime()-dopen.lastaction < 100 || (new Date).getTime()-dclose.lastaction < 100){
      return autoclose();
    }
    dclose.lastaction = (new Date).getTime()
   
    
    _.slide(0,menu,function()
    {
      menu.style.height = '';
      menu.style.visibility = '';
    })
  }
  
  function autoclose()
  {
    if(timeout == null)
    {
      timeout = setTimeout(dclose,100);
    }
  }
  
  dropdown_array.push([menu, trigger, function(){dclose()}]);
  
  function dopen()
  {
    dopen.lastaction = (new Date).getTime()
    for(var i = 0; i < dropdown_array.length; i++)
    {
      if(dropdown_array[i][0] != menu)
      {
        dropdown_array[i][2]();
      }
    }
    clearTimeout(timeout)
    timeout = null;
    if(menu.style.visibility != 'visible')
    {
      
      menu.style.zIndex = 9999989
      
      menu.style.visibility = 'visible';
      _.slide(1,menu,function()
      {
        menu.style.height = '';
        menu.style.visibility = 'visible';
      },5,20)
    }
  }
  
  for(var i = 0; i < li.length; i++)
  {
    _.on(menu, "mouseover", dopen);
    _.on(menu, "mouseout", autoclose);
  }
  _.on(trigger, "mouseover", dopen);
  _.on(trigger, "mouseout", autoclose);
  _.on(document, "click", function()
  {
    _.slide(0,menu,function()
    {
      menu.style.height = '';
      menu.style.visibility = ""
    })
  })
}