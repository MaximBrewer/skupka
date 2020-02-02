
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



jQuery(document).ready(function($) {
    /*
	$('#op_login_btn').click(function() {
		$('#op_login_btn').addClass('active');
		$('#op_register_btn').removeClass('active');
		return false;
	});
	
	$('#op_register_btn').click(function() {
		$('#op_register_btn').addClass('active');
		$('#op_login_btn').removeClass('active');						  
		return false;
	});
	*/
	$( "#sachoneL" ).click(function() {
		//$( "#idsa" ).toggle();
		//jQuery('#sachone').click(); 
		/*
		 
		 var jel = jQuery(el); 
		 var checked = jel.prop('checked'); 
		 console.log(checked); 
		 if (!checked) {
		 jQuery(el).click(); 
		 return; 
		 }
		 //console.log(el.checked); 
		 var d = document.getElementById('idsa'); 
		 if (d != null)
			 if (d.style.display != 'none')
			 {
				// el.checked = true; 
			 }
		 */
		 var el = document.getElementById('sachone'); 
		Onepage.showSA(el, 'idsa'); 
	});
	
	var ra = jQuery('#register_account'); 
	if (ra.length > 0)
	{
		ra.change(function () { 
		  var e = jQuery(this); 
		  var checked = e.attr('checked');
		  return Onepage.showFields( checked, new Array('username', 'opc_password', 'opc_password2') );
		}
		); 
	}
	
	//onkeypress="showSA(this, 'idsa');" onclick="javascript: showSA(this, 'idsa');" 
	var ra = jQuery('#sachone'); 
	if (ra.length > 0)
	{
		ra.change(function () { 
		  var e = jQuery(this); 
		  var checked = e.attr('checked');
		  return Onepage.showSA( this, 'idsa' );
		}
		); 
	}
	
	var ra = jQuery('#op_login_btn'); 
	if (ra.length > 0)
	{
		ra.click(function () {
		  Onepage.op_unhide('logintab', 'registertab');
		  var d1 = document.getElementById('op_login_btn'); 
		  d1.className += ' active'; 
		  var d2 = document.getElementById('op_register_btn'); 
		  d2.className = d2.className.split('active').join(''); 
		  
		  var cw = jQuery('#checkout_wrapper'); 
		  if (cw.length > 0) cw.hide(); 
		}
		); 
	}
	var ra = jQuery('#op_register_btn'); 
	if (ra.length > 0)
	{
		  ra.click(function () {
		  
		  Onepage.op_unhide('registertab', 'logintab');
		  var d1 = document.getElementById('op_register_btn'); 
		  d1.className += ' active'; 
		  var d2 = document.getElementById('op_login_btn'); 
		  d2.className = d2.className.split('active').join(''); 
		  
		  var cw = jQuery('#checkout_wrapper'); 
		  if (cw.length > 0) cw.show(); 
		}); 
	}
	//op_register_btn
	//javascript: return op_unhide('registertab', 'logintab');
	
	
});	


