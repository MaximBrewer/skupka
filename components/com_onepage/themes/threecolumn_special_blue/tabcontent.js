
function linkToCustomersTab(loc) {
  
}

function edit_basket(el)
{
 var col = getElementsByClassName('op_update_form'); 
 for (var i = 0; i<col.length; i++ )
  {
    col[i].style.display = 'block'; 
  }
 col2 = getElementsByClassName('static_line'); 
 for (var j = 0; j<col2.length; j++ )
 {
   col2[j].style.display = 'none';
 }
 el.innerHTML = ''; 
 //d = document.getElementById('op_update_form'); 
 //d.style.display = 'block';
 return false;
}
function getLabel(what)
{
  var d = document.getElementById('label_'+what.id);
  if (d != null)
  return d; 
  else
  {
     if (typeof what.name != 'undefined')
	 {
     name = what.name;
	 d = document.getElementById('label_'+what.name+'_field'); 
	 return d;
	 }
   }
   // will cause an error
   return; 
}

function inputclear2(what)
{
 var d = getLabel(what); 
 if (d != null) 
 d.innerHTML = ''; 
 clearMissing(what); 
 
 
}

function clearMissing(what)
{
  if (typeof what.options != 'undefined' && (what.options != null))
  var id = what.name + '_div'; 
  else
  var id = what.id.replace('_field', '_div'); 
  
  what.className = what.className.split('invalid').join(''); 
  
  //console.log('clearmissing', id); 
  if (id.indexOf('_div')<0) return;
  var d = document.getElementById(id); 
  if (d != null)
  if (d.className.toString().indexOf('missing')>=0)
    {
	 //console.log('here3'); 
	 //console.log(id); 
	 d.style.color = 'transparent';
     d.className = 'formLabel';
	}

}

function inputclear(what){
//console.log('inputclear', what.id); 
  var d = getLabel(what); 
  if (d != null)
  d.innerHTML = '';
  clearMissing(what);   
  
 
  if (typeof jQuery != 'undefined') {
	   var ew = jQuery(what); 
	   if (ew.length < 0) return; 
	   
	   if (typeof what.onblur != 'undefined' && what.onblur != null)
	   {
		   var z = what.onblur.toString(); 
		   var xj = ew.attr('onblur'); 
		   if (typeof xj != 'undefined') {
		   xj = xj.split('javascript:').join('');
		   //console.log('xj', xj); 
		   if (z.indexOf(xj)<0) {
		      ew.blur(function() {
			    
				try { 
				eval(xj); 
				} catch(e) { ; }
			  });
		   }
		   }
		   
	   }
	   
	  
    
	var at = ew.attr('blur_added'); 
	if (!at) {
	  ew.attr('blur_added', 1); 
	  ew.blur(function() { 
	    inputreset(this);
	  }); 
	 ew.focus( function () {
	   inputclear2(this); 
	 }); 		 
	}
  }
else
{

  if (typeof what.onblur == 'undefined' || what.onblur == null)
  {
  what.onblur = function (evt) {
     inputreset(what);
   };
  }
  else
  {
  if (what.onblur.toString().indexOf('runSS')>=0)
  {
  what.onblur = function (evt) {
     inputreset(what);
	 Onepage.op_runSS(what, false, true); 
   };
  }
  else
  if (what.onblur.toString().indexOf('doublemail_')>=0)
  {
  what.onblur = function (evt) {
     Onepage.doubleEmailCheck(true);
	 inputreset(what);
   };
  
  }
  what.onfocus = function (evt) {
	inputclear2(what); 
  };
  }
}

}
function clearState(what)
{
 if (what.options != null)
 {
  var id = what.id+'_div';
  var d = document.getElementById(id); 
  if (d != null)
  if (what.options[what.selectedIndex].value != '')
  if (d.className.toString().indexOf('missing')>=0)
    {
	//console.log('hre4'); 
	 d.style.color = 'transparent';
     d.className = 'formLabel';
	}
 
 }


}

function isRequired(what)
{
   // not required by opc as set up in shopper field tabs
 if (what.className.indexOf('opcrequired')>=0) return false;
 // not required at all
 if (what.className.indexOf('required')<0) return false;
 
 return true; 

}

function inputreset(what)
{
 
 
 if (what.value == '')	
 {
 var d = getLabel(what); 
 if (typeof what.name != 'undefined')
 if (d!=null)
 {
 var d2 = document.getElementById('saved_'+what.name+'_field'); 
 d.innerHTML = d2.value; 
 }
  if (typeof what.alt != 'undefined' && what.alt != null)
   {
     if (what.alt.indexOf('*')== (what.alt.length-1))
	  {
	    if (typeof what.options != 'undefined' && (what.options != null))
		id = what.name+'_div'; 
		else
	    id = what.id.replace('_field', '_div'); 
		//alert(id); 
		if (id.indexOf('_div')<0) return;
		var d = document.getElementById(id); 
		
		if (typeof d.className == 'undefined')
		 {
		 
		 }
		
		if (isRequired(what))
		if (d.className.toString().indexOf('missing')<0)
			{
			
				d.style.color = 'red';
				d.className = 'formLabel missing';
			}
	  }
   }
 }
 else {
  var id = what.name+'_div'; //.replace('_field', '_div'); 
  var d = document.getElementById(id); 
  if (d != null)
  if (d.className.toString().indexOf('missing')>=0)
    {
	 if (typeof d.htmlFor != 'undefined' && (d.htmlFor != null))
	 {
	 //console.log('here2'); 
	 d.style.color = ''; 
	 }
	 else
	 {
	  //d.style.color = 'transparent';
      d.className = 'formLabel';
	 }
	}
 }
 
 
}

function clickclear(thisfield, defaulttext) {
if (thisfield.value == defaulttext) {
thisfield.value = "";
}
}

function clickrecall(thisfield, defaulttext) {
if (thisfield.value == "") {
thisfield.value = defaulttext;
}
}

function t_hideFx(id)
{
  var d = document.getElementById(id); 
  if (d != null)
   d.style.display = 'none'; 

}

function t_unhide(id)
{
	
  var d = document.getElementById(id); 
  if (d != null)
   d.style.display = 'block'; 
}

function tabClick(tabid)
{
  
  // ul 
  var tab = document.getElementById(tabid);
  if (!(tab != null)) return; 
  var ul2 = tab.parentNode;
  var ul = ul2.parentNode;
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
  tab.parentNode.className = "selected";
  tab.className = "selected"
  
  var tabcon = document.getElementById(tab.rel);
  var parentn = document.getElementById('tabscontent');
  for (i=0; i<parentn.childNodes.length; i++)
  {
    if (typeof(parentn.childNodes[i].style) != 'undefined')
    if (parentn.childNodes[i].id != tab.rel)
    parentn.childNodes[i].style.display = 'none';
    else parentn.childNodes[i].style.display = 'block';
  }
  return false;
}


function op_login()
{
 
 document.getElementById('opc_option').value = op_com_user;
 //document.adminForm.task.value = op_com_user_task;
 document.getElementById('opc_task').value = op_com_user_task;
 
 document.adminForm.action = op_com_user_action;
 document.adminForm.controller.value = 'user'; 
 document.adminForm.view.value = ''; 
 
 //alert(op_com_user_task+' '+op_com_user_action+' '+op_com_user); 
 //return false;
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
if (typeof jQuery != 'undefined')
jQuery(document).ready(function($)
{
  try
  {
  jQuery('#adminForm').find(':text').each(function(){
	if (typeof this.value != 'undefined')
	if (this.value != '')
	 inputclear(this); 
  })
  } catch (e) {
  
  }
  
  resetHeight(); 
  

});
function delayed_resetheight()
{
 if (typeof jQuery == 'undefined') return; 
 var d = document.getElementById('dob1'); 
 if (!(d != null)) return; 
  var d2 = document.getElementById('dob2'); 
  if (!(d2 != null)) return; 
   
   var d3 = document.getElementById('dob3'); 
  if (!(d3 != null)) return; 
  
    var h1 = jQuery('#dob1').height();
  var h2 = jQuery('#dob2').height();
  var h3 = jQuery('#dob3').height();
  var h4 = Math.max(h1, h2, h3); 
  //console.log(h1,h2,h3);
  var d1 = d;
  d1.style.minHeight = h4+'px'; 
  var topE = jQuery(d); 
  if (topE.length <= 0) return;   
  var pE = topE.position(); 
  if (typeof pE.top == 'undefined') return; 
  var top = parseInt(pE.top); 
  //top = d1.offsetTop; 
  var d2 = document.getElementById(d2); 
  if (d2 != null) {
  //top2 = d2.offsetTop;
  if (jQuery(d2).length > 0) {
 var jp = jQuery(d2).position(); 
 if (typeof jp.top !== 'undefined') {
  var top2 = parseInt(jp.top); 
  if (top == top2)  
  d2.style.minHeight = h4+'px'; 
  }
  }
  }
 
  if (d3 != null) {
  //top3 = d3.offsetTop; 
  var top3 = parseInt(jQuery(d3).position().top); 
  if (top == top3)
  d3.style.minHeight = h4+'px'; 
  }
  //console.log(top, top2, top3); 
  /*
  h1 = jQuery('#dob1').height(h4);
  h2 = jQuery('#dob2').height(h4);
  h3 = jQuery('#dob3').height(h4);
  */
  //console.log('here...'+h4); 

}
function resetHeight()
{
 setTimeout("delayed_resetheight()",2000)
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
function callAfterShippingSelectM()
{
	
	 
	 
	if (typeof Onepage == 'undefined') return; 
	if (typeof jQuery == 'undefined') return; 
	
	
	
	 var ship_id = Onepage.getInputIDShippingRate();
	 
	
	
	var my_label = op_shipping_txt; 
	
	
	var label = jQuery("label[for='"+ship_id+"'] .vmshipment_name"); 
	if (label != null)
	if (typeof label.text != 'undefined')
	{
	  	var my_label2 = label.text(); 
		if (my_label2.length > 0) my_label = my_label2; 
		
		my_label = my_label.split("\r\r\n").join(' ').split("\r\n").join(" ").split("\n").join(" ").trim(); 
	}
	
	
	var nameId = jQuery('.shipping_name_position'); 
	if (nameId.length > 0)
	{
		
		nameId.each(function () { 
		this.innerHTML = my_label; }); 
		
		//console.log(my_label); 
	}
	
	 
}

function callAfterPaymentSelectM()
{
	
	 
	 
	if (typeof Onepage == 'undefined') return; 
	if (typeof jQuery == 'undefined') return; 
	
	
	
	 
	 var payment_id = Onepage.getPaymentId();
	
	
	var my_label = op_payment_fee_txt; 
	
	var label = jQuery("label[for='payment_id_"+payment_id+"'] .vmpayment_name"); 
	//var label = jQuery("label[for='"+payment_id+"'] .vmpayment_name"); 
	if (label != null)
	if (typeof label.text != 'undefined')
	{
	  	var my_label2 = label.text(); 
		if (my_label2.length > 0) my_label = my_label2; 
		
		my_label = my_label.split("\r\r\n").join(' ').split("\r\n").join(" ").split("\n").join(" ").trim(); 
	}
	
	
	var nameId = jQuery('.payment_name_position'); 
	if (nameId.length > 0)
	{
		
		nameId.each(function () { 
		this.innerHTML = my_label; }); 
		
		//console.log(my_label); 
	}
	
	 
}
/* uncoment to get the shipping label into the totals lines: 
if (typeof addOpcTriggerer != 'undefined')
{
  
  addOpcTriggerer('callAfterShippingSelect', 'callAfterShippingSelectM()'); 
}
*/


/* uncoment to get the payment label into the totals lines: 
if (typeof addOpcTriggerer != 'undefined') {
addOpcTriggerer("callAfterPaymentSelect",  "callAfterPaymentSelectM()"); 
}
*/

if (typeof addOpcTriggerer != 'undefined')
addOpcTriggerer("callAfterPaymentSelect",  "setPaymentToggle()"); 


