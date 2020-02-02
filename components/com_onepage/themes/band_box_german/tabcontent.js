
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
function syncEmails(el)
{
 var email = el.value; 
 var d = document.getElementById('email_field'); 
 if (d != null) 
  {
    d.value = email; 
  }
}


	


function onGuestPressed(el, event)
{
  //var fn = function() { myGuesCallback(el); }; 
  Onepage.op_runSS(el, false, true, 'checkemail', false, myGuesCallback);
  return false; 
}
function myGuesCallback(el) {  
  if (!Onepage.opc_checkEmail(true))
       {
	     //alert(email_error); 
	     return false;
       }
  
  if (typeof Onepage != 'undefined')
  if (typeof Onepage.emailCheckReg != 'undefined')
  if (!Onepage.emailCheckReg('guest_email')) return false; 

  if (typeof emailCheckReg != 'undefined')
  if (!emailCheckReg('guest_email')) return false; 
  
  
  /* remove required */
  
var d1 = document.getElementById('name_field'); 

if (d1 != null)
{

   d1.value = ''; 
   d1.className = d1.className.split('required').join(''); 
   d1.className = d1.className.split('invalid').join(''); 
  
  
}
var d1 = document.getElementById('username_field'); 
if (d1 != null)
{
   d1.value = ''; 
   d1.className = d1.className.split('required').join(''); 
   d1.className = d1.className.split('invalid').join(''); 
}

var d1 = document.getElementById('opc_password_field'); 
if (d1 != null)
{
  

  d1.value = ''; 
   d1.className = d1.className.split('required').join(''); 
   d1.className = d1.className.split('invalid').join(''); 
  
  
}

var d1 = document.getElementById('opc_password2_field'); 
if (d1 != null)
{
  
   d1.value = ''; 
   d1.className = d1.className.split('required').join(''); 
   d1.className = d1.className.split('invalid').join(''); 
}

	
   
   var d1 = document.getElementById('email_in_registration'); 
   if (d1 != null)
   {
     if (d1.value == '1')
	 {
	   var d2 = document.getElementById('email_field'); 
	   d2.value = ''; 
	   d2.className = d1.className.split('required').join(''); 
       d2.className = d1.className.split('invalid').join(''); 	  
	   
	   var d3 = document.getElementById('email2_field'); 
	   if (d3 != null)
	   {
	   d3.value = ''; 
	   d3.className = d1.className.split('required').join(''); 
	   d3.className = d1.className.split('invalid').join(''); 
	   }
	  }
	  else
	  {
	    var d2 = document.getElementById('email_field'); 
	    d2.value = document.getElementById('guest_email').value;
		d2.setAttribute('readonly', 'readonly'); 
	    d2.className = d1.className.split('required').join(''); 
        d2.className = d1.className.split('invalid').join(''); 
	  }
	}
	   

  /*end remove required */
  
  var d = document.getElementById('confirm_button_div'); 
  if (d != null)
  d.style.display = "block"; 
  
  
  var dx = document.getElementById('register_account'); 
  if (dx != null)
  dx.value = 0; 
  
  var d = document.getElementById('usersection'); 
  if (d != null)
  d.style.display = 'block'; 
  //el.style.display = 'none'; 
  
   var ee = document.getElementsByName('GuestSubmit'); 
  if (ee != null)
  for (var i=0; i<ee.length; i++) {
	  ee[i].style.display = 'none'; 
  }
  
  var r = document.getElementById('register_box');
  
  if (r != null)
  r.style.display = 'none'; 
  
  var l = document.getElementById('login_box');
  if (l!=null)
  l.style.display = 'none'; 
  
  var l = document.getElementById('guest_box');
  var l1 = document.getElementById('guest_container');
  if (l!=null)
  {
   l.style.width = '100%'; 
   l1.style.width = '50%';
   l1.style.whiteSpace = 'nowrap';
  }
  if (false)
  if (typeof jQuery != 'undefined')
  {
  if (r.style.display != 'none')
  jQuery("#register_box")
    .fadeTo(500, 0.5)
	.find("input").attr('disabled', 'disabled')
	.hover(function () {
        $(this).fadeTo(500, 1);
    }, function () {
        $(this).fadeTo(500, 0.2);
    });
  if (l.style.display != 'none')
	jQuery("#login_box")
    .fadeTo(500, 0.5)
	.find("input").attr('disabled', 'disabled')
    .find("a").attr('disabled', 'disabled')
	.hover(function () {
        $(this).fadeTo(500, 1);
    }, function () {
        $(this).fadeTo(500, 0.2);
    });
  }
  Onepage.op_runSS('init'); 
  return false; 
}

function onRegisterPressed(el, event)
{
//Onepage.op_runSS(el, false, true, 'checkemail', false, handleRegisterCallback);
//var fn = function() { handleRegisterCallback(el); }; 
Onepage.op_runSS(el, false, true, 'checkusername', false, handleRegisterCallback);
return false; 
}
function handleRegisterCallback(el) {

var d1 = document.getElementById('name_field'); 
var invalid = false; 
if (d1 != null)
{
  if (d1.value == '')
  {
   d1.className += ' invalid'; 
   invalid = true; 
  }
}
var d1 = document.getElementById('username_field'); 
if (d1 != null)
{
  if (d1.value == '')
  {
   d1.className += ' invalid'; 
   invalid = true; 
  }
  else
  d1.className = d1.className.split('invalid').join('');

}

var d1 = document.getElementById('opc_password_field'); 
if (d1 != null)
{
  

  if (d1.value == '')
  {
   d1.className += ' invalid'; 

   invalid = true; 
  }
  else
  d1.className = d1.className.split('invalid').join('');
  
  var pwd = d1.value; 
}

var d1 = document.getElementById('opc_password2_field'); 
if (d1 != null)
{
  
   if (typeof pwd != 'undefined')
   if (d1.value != pwd)
   {
     invalid = true; 
	 alert(op_pwderror); 
	 return false; 
   }
   if (d1.value == '')
  {
   d1.className += ' invalid'; 

   invalid = true; 
  }
  else
  d1.className = d1.className.split('invalid').join('');
}

	var wasValid = true; 
   if (typeof opc_checkUsername != 'undefined')
   if (!opc_checkUsername(wasValid))
   {
	   alert(username_error); 
	   return false;
   }
   
   
   if (typeof Onepage != 'undefined')
   if (typeof Onepage.opc_checkUsername != 'undefined')
   if (!Onepage.opc_checkUsername(wasValid))
   {
	   alert(username_error); 
	   return false;
   }
   
   
   var d1 = document.getElementById('email_in_registration'); 
   if (d1 != null)
   {
     if (d1.value == '1')
	 {
	   var d2 = document.getElementById('email_field'); 
	  
	   
	   var d3 = document.getElementById('email2_field'); 
	   if (d3 != null)
	   {
	   if (d3.value == '')
	   {
	     d3.className += ' invalid'; 
   
		invalid = true; 
	   }
	   else d3.className = d3.className.split('invalid').join(''); 
	   if (d3.value != d2.value)
	     {
		   d3.className += ' invalid'; 
           return false; 
		 }
		 else
		 {
		  var dx = document.getElementById('email2_info'); 
		  if (dx != null) dx.style.display = 'none'; 
		 }
		 
	   }
	    
	   if (typeof Onepage != 'undefined')
	   if (!Onepage.emailCheckReg()) 
	   {
	   invalid = true; 
	   return false; 
	   }
	   
       if (!Onepage.opc_checkEmail(wasValid))
       {
	     //alert(email_error); 
	     return false;
       }
	 
	 }
   }

if (invalid)
{
 alert(op_general_error); 
 return false; 
}

var d1 = document.getElementById('register_account');
if (d1 != null)
d1.value = '1';  

var d1 = document.getElementById('login_box'); 
d1.style.display = 'none'; 

var d1 = document.getElementById('guest_box'); 
d1.style.display = 'none'; 

var d = document.getElementById('confirm_button_div'); 
  if (d != null)
  d.style.display = "block"; 

var d1 = document.getElementById('register_box'); 
var d2 = document.getElementById('register_container');
d1.style.width = '100%'; 
d2.style.width = '50%';
d2.style.whiteSpace = 'nowrap';

var d = document.getElementById('usersection'); 
  if (d != null)
  d.style.display = 'block'; 
  //el.style.display = 'none'; 
  
   var ee = document.getElementsByName('RegisterSubmit'); 
  if (ee != null)
  for (var i=0; i<ee.length; i++) {
	  ee[i].style.display = 'none'; 
  }
  
  var g = document.getElementById('guest_box');
  var l = document.getElementById('login_box');
  if (false)
  if (typeof jQuery != 'undefined')
  {
  if (g.style.display != 'none')
  jQuery("#guest_box")
    .fadeTo(500, 0.5) 
	.find("input").attr('disabled', 'disabled')
    .hover(function () {
        $(this).fadeTo(500, 1);
    }, function () {
        $(this).fadeTo(500, 0.2);
    });
	if (l.style.display != 'none')
	jQuery("#login_box")
    .fadeTo(500, 0.5)
	.find("input").attr('disabled', 'disabled')
	.find("a").bind('click', false)
    .hover(function () {
        $(this).fadeTo(500, 1);
    }, function () {
        $(this).fadeTo(500, 0.2);
    });
  }
  Onepage.op_runSS('init'); 
  return false; 
}


function setPaymentToggle()
{
   if (typeof op_payment_inside != 'undefined') 
   if (op_payment_inside) return; 
   
   if (typeof jQuery == 'undefined') return; 
   var payment_id = Onepage.getPaymentId();
   
   
    jQuery('.ccDetails').hide();
	jQuery('.vmpayment_cardinfo').hide(); 
	jQuery('.vmpayment_description').hide(); 
	jQuery('.ccDetails_'+payment_id).show(); 
	jQuery('.vmpayment_cardinfo_'+payment_id).show(); 
	jQuery('.vmpayment_description_'+payment_id).show();
 
  
}

if (typeof addOpcTriggerer != 'undefined')
addOpcTriggerer("callAfterPaymentSelect",  "setPaymentToggle()"); 