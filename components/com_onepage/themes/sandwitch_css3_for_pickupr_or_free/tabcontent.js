function op_toggleVis(el)
{
   d = document.getElementById(el); 
   //console.log(d); 
   if (d != null)
    {
	  if (d.style.display == 'none') toggleHide(el, false); 
	  else toggleHide(el, true); 
	}
	
	return false;
	
}

function toggleHide(el, hide)
 {
   
   if (hide)
   {
   d = document.getElementById(el); 
   if (d != null)
    {
	  d.style.display = 'none'; 
	}
   }
   else
   {
   
   
   d = document.getElementById(el); 
   if (d != null)
    {
	  d.style.display = 'block'; 
	}
   
   
   }
 }
function hidecheckout(hide)
{
  if (hide)
   {
     toggleHide('opc_shipto_section', true); 
	 toggleHide('opc_shipping_section', true);
	 toggleHide('opc_payment_section', true); 
	 toggleHide('opc_tos_section', true); 
	 toggleHide('opc_bottom_section', true); 
	 toggleHide('opc_shipping_and_shipto_section', true); 
   }
  else 
   {
     toggleHide('opc_shipto_section', false); 
	 if (!op_noshipping)
	 toggleHide('opc_shipping_section', false);
	 toggleHide('opc_payment_section', false); 
	 toggleHide('opc_tos_section', false); 
	 toggleHide('opc_bottom_section', false); 
	 toggleHide('opc_shipping_and_shipto_section', false); 
   
   }
}
function opc_menuClick(id)
{
  document.getElementById(id+'_div').className = document.getElementById(id+'_div').className.split('opc_menu_inactive').join('opc_menu_active'); 
  
  document.getElementById(id+'_arrow').style.display = 'block'; 
  
  
  if (id != 'login')
  {
  document.getElementById('login_div').className = document.getElementById('login_div').className.split('opc_menu_active').join('opc_menu_inactive'); 
  document.getElementById('login_arrow').style.display = 'none'; 
  hidecheckout(false); 

  }
  else
  {
    hidecheckout(true); 
    document.getElementById('opc_customer_registration').style.display = 'none'; 
	document.getElementById('opc_login_section').style.display = 'block'; 
  }
  
  if (id != 'visitor')
  {
  document.getElementById('visitor_div').className = document.getElementById('visitor_div').className.split('opc_menu_active').join('opc_menu_inactive'); 
  document.getElementById('visitor_arrow').style.display = 'none';  
  }
  else
  {
    document.getElementById('opc_customer_registration').style.display = 'block'; 
	document.getElementById('opc_login_section').style.display = 'none'; 
	document.getElementById('opc_is_business').value = '0'; 
	
  for (var i=0; i<business_fields.length; i++)
   {
     bf = business_fields[i]; 
     g = document.getElementById('opc_business_'+bf); 
	 if (typeof g != 'undefined' && (g != null))
	 g.style.display = 'none'; 
	 
	 //console.log(bf); 
   }

	
  }
  
  if (id != 'business')
  {
  document.getElementById('business_div').className = document.getElementById('business_div').className.split('opc_menu_active').join('opc_menu_inactive'); 
  document.getElementById('business_arrow').style.display = 'none'; 
  if (typeof jQuery != 'undefined')
  jQuery('.businessonlysection').toggle(false); 
 
  }
    else
  {
  if (typeof jQuery != 'undefined')
  jQuery('.businessonlysection').toggle(true); 
    document.getElementById('opc_is_business').value = '1'; 
    document.getElementById('opc_customer_registration').style.display = 'block'; 
	document.getElementById('opc_login_section').style.display = 'none'; 
	for (var i=0; i<business_fields.length; i++)
    {
	 bf = business_fields[i]; 
     g = document.getElementById('opc_business_'+bf); 
	 if (typeof g != 'undefined' && (g != null))
	 g.style.display = 'block'; 
	 // console.log(bf); 
    }
  }

  
  
  return false; 
}

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
function op_logout()
{
 document.adminForm.option.value = op_com_user;
 document.adminForm.task.value = op_com_user_task_logout;
 document.adminForm.action = op_com_user_action_logout; 
 
 document.adminForm.submit();
 return true;

}
function op_login()
{
 /*
 	<input type="hidden" name="option" value="<?php echo vmIsJoomla( '1.5' ) ? 'com_user' : 'login'; ?>" /> 
	<input type="hidden" name="task" value="login" />

 */
 
 // we need to disable validation
 df = document.getElementById('adminForm'); 
 if (df != null)
  {
    df.className = '';
	document.formvalidator = null;
	document.adminForm.onsubmit = null; 
  }
 
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
// id = string, of an div with an ide which has to be altered
// el -> the underlying button element styled as checkbox
function alterButton(el, id)
{
  	el3 = ''; 
  //if (typeof el.checked == 'undefined')
   {
     if (el3 != '')
     el2 = document.getElementById(el3); 
	 d = document.getElementById(id); 
     if (el.className.indexOf('button_checkbox_uned')>=0)
	  {
	    if (el3 != '')
		{
	    el2.setAttribute('checked', true); 
		el2.checked = true; 
		el2.value='adresaina'; 
		}
		el.className = el.className.split('button_checkbox_uned').join('button_checkbox_ed'); 
		
		
		d.style.display = 'block';
	  }
	 else
	  {
	    if (el3 != '')
		{
	     el2.setAttribute('checked', false); 
		 el2.checked = false; 
		 el2.value=''; 
		}
		el.className = el.className.split('button_checkbox_ed').join('button_checkbox_uned'); 

		
	
   
       d.style.display = 'none';
   
		
	  }
   }
   
  
	//Onepage.op_runSS(el2);    
  //showSA(el2, id); 
  return false;
}



function showSA2(el, id)
{
	el3 = 'sachone'; 
  //if (typeof el.checked == 'undefined')
   {
     if (el3 != '')
     el2 = document.getElementById(el3); 
	 d = document.getElementById(id); 
     if (el.className.indexOf('button_checkbox_uned')>=0)
	  {
	    if (el3 != '')
		{
	    el2.setAttribute('checked', true); 
		el2.checked = true; 
		el2.value='adresaina'; 
		}
		el.className = el.className.split('button_checkbox_uned').join('button_checkbox_ed'); 
		
		
		d.style.display = 'block';
	  }
	 else
	  {
	    if (el3 != '')
		{
	     el2.setAttribute('checked', false); 
		 el2.checked = false; 
		 el2.value=''; 
		}
		el.className = el.className.split('button_checkbox_ed').join('button_checkbox_uned'); 

		
	
   
       d.style.display = 'none';
   
		
	  }
   }
   
  
	Onepage.op_runSS(el);    
  //showSA(el2, id); 
  return false;
}


function showSAreg(el)
{
   fields = new Array('username', 'password', 'opc_password', 'password2', 'opc_password2', 'opc_business_opc_password2', 'opc_business_opc_password'); 
   //if (typeof el.checked == 'undefined')
   {
     el2 = document.getElementById('register_account'); 

     if (el.className.indexOf('button_checkbox_uned')>=0)
	  {
	    el2.setAttribute('checked', true); 
		el2.checked = true; 
		el.className = el.className.split('button_checkbox_uned').join('button_checkbox_ed'); 
		el2.value='adresaina'; 
		
		
		
		for (var i=0; i<fields.length; i++)
		 {
		    d2 = document.getElementById(fields[i]+'_div'); 
			if (d2 != null)
			d2.style.display = ''; 
			d2 = document.getElementById(fields[i]+'_input'); 
			if (d2 != null)
			d2.style.display = ''; 
		 }
	  }
	 else
	  {
	    el2.setAttribute('checked', false); 
		el2.checked = false; 
		el.className = el.className.split('button_checkbox_ed').join('button_checkbox_uned'); 
		el2.value=''; 
       
	   		for (var i=0; i<fields.length; i++)
		 {
		    d2 = document.getElementById(fields[i]+'_div'); 
			if (d2 != null)
			d2.style.display = 'none'; 
			d2 = document.getElementById(fields[i]+'_input'); 
			if (d2 != null)
			d2.style.display = 'none'; 
		 }

   
		
	  }
   }
   return false;
}