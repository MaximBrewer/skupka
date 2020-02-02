
function tabClick(tabid)
{
  
  // ul 
  var ul = tabid.getParent().getParent();
  for (var i = 0; i<ul.childNodes.length; i++)
  {
   ul.childNodes[i].className = ""; 
   for (var j = 0; j<ul.childNodes[i].childNodes.length; j++)
    {
     if (ul.childNodes[i].childNodes[j].className == "selected")
     {
      ul.childNodes[i].childNodes[j].className = "";
     }
    }
   
  }
  // li
  tabid.getParent().className = "selected";
  tabid.className = "selected"
  
  var tabcon = document.getElementById(tabid.rel);
  var parentn = document.getElementById('tabscontent');
  for (i=0; i<parentn.childNodes.length; i++)
  {
    if (typeof(parentn.childNodes[i].style) != 'undefined')
    if (parentn.childNodes[i].id != tabid.rel)
    parentn.childNodes[i].style.display = 'none';
    else parentn.childNodes[i].style.display = 'block';
  }
  return false;
}

function op_login()
{
 /*
 	<input type="hidden" name="option" value="<?php echo vmIsJoomla( '1.5' ) ? 'com_user' : 'login'; ?>" /> 
	<input type="hidden" name="task" value="login" />

 */
 document.adminForm.option.value = op_com_user;
 document.adminForm.task.value = op_com_user_task;
 document.adminForm.action = op_com_user_action
 if (document.adminForm.username != null)
 document.adminForm.username.value = document.adminForm.username_login.value;
 else
 {
    var usern = document.createElement('input');
    usern.setAttribute('type', 'hidden');
    usern.setAttribute('name', 'username');
    usern.setAttribute('value', document.getElementById('username_login').value);
    document.adminForm.appendChild(usern);
 }

 document.adminForm.submit();
 return true;
}

function submitenter(el, e)
{
 var charCode;
    
    if(e && e.which){
        charCode = e.which;
    }else if(window.event){
        e = window.event;
        charCode = e.keyCode;
    }


if (charCode == 13)
   {
   op_login();
   return false;
   }
else
   return true;
}


function op_unhideFx(call, id1, id2)
{
  var d1 = document.getElementById(id1); 
  if (d1 != null)
   {
      if (jQuery != 'undefined')
	   {
	     jQuery('#'+id1).fadeIn(); 
	   }
	  else
	   d1.style.display = 'block'; 
   }
  var d1 = document.getElementById(id2); 
  if (d1 != null)
   {
      if (jQuery != 'undefined')
	   {
	     jQuery('#'+id2).fadeOut(); 
	   }
	  else
	   d1.style.display = 'block'; 
   }
  call.className += 'active'; 
  if (id1 == 'logintab') 
   {
     var d2 = document.getElementById('op_register_btn'); 
	 if (d2 != null)
	  {
	    d2.className = d2.className.split('active').join(''); 
	  }
     
   }
   
   if (id1 == 'registertab') 
   {
     var d2 = document.getElementById('op_login_btn'); 
	 if (d2 != null)
	  {
	    d2.className = d2.className.split('active').join(''); 
	  }
     
   }
}