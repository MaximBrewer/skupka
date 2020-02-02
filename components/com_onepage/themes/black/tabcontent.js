
function tabClick(tabid)
{
  
  // ul 
  tab = document.getElementById(tabid);
  ul2 = tab.parentNode;
  ul = ul2.parentNode;
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

function showE(el)
{
	if (typeof jQuery != 'undefined')
	{
		el = jQuery(el); 
		el.show(); 
		return; 
	}
	el.style.display = 'block'; 
	
}

function hideE(el)
{
	if (typeof jQuery != 'undefined')
	{
		el = jQuery(el); 
		el.hide(); 
		return; 
	}
	el.style.display = 'none'; 
	
}

function showCheckout()
{
	Onepage.ga('Checkout button clicked', 'Checkout navigation'); 
	
	var d1 = document.getElementById('checkout_top'); 
	var d2 = document.getElementById('full_checkout'); 
	if (d1 != null)
	{
		hideE(d1); 
	}
	if (d2 != null)
	{
		showE(d2); 
	}
	return false; 
	
}

function updateall()
{
   if (typeof jQuery != 'undefined')
   {
   
     //var b = jQuery( "#opc_basket" ); 
	 if (typeof opc_basket_wrap_id != 'undefined')
	 {
		 var b = document.getElementById(opc_basket_wrap_id); 
	 }
	 else
     var b = document.getElementById('opc_basket'); 
     
	 if (b != null)
	 Onepage.jQueryLoader(b, false); 
   
   }
  //var rel = Onepage.getAttr(el, 'rel'); 
  
  
  
  
	  
  var quantity = 0; 
  /*
  var hash = ''; 
  var cart_id = ''; 
  
  if (rel != null)
  {
  if (rel.toString().indexOf('|')>=0)
   {
		var arr = rel.split('|'); 
		cart_id = arr[0]; 
		hash = arr[1]; 
	}
	else
	{
	  cart_id = rel; 
	}

  if (hash != '')
  {
    // element change: 
	var d = document.getElementById('quantity_for_'+hash); 
	if (d != null) 
	el = d; 
	
  }
  if (typeof newQuantity == 'undefined')
  {
  if (typeof el.options != 'undefined')
  if (typeof el.selectedIndex != 'undefined')
   quantity = el.options[el.selectedIndex].value;  
   
  if (quantity == 0)
  if (typeof el.value != 'undefined')
  {
     quantity = el.value; 
  }
  }
  else
  {
	  quantity = newQuantity; 
  }
  }
  */
  
  /* example of an ajax update input for
  \components\com_onepage\themes\icetheme_thestore_custom\overrides\update_form_ajax.tpl.php
  
  <input id="quantity_for_<?php echo md5($product->cart_item_id); ?>" value="<?php echo $product->quantity; ?>" type="text" onchange="Onepage.qChange(this);" name="quantity" rel="<?php echo $product->cart_item_id; ?>" id="stepper1" class="quantity" min="0" max="999999" size="2" data-role="none" />
  
  */
  
  
  var array = ''; 
  
  var e = jQuery('.opcq'); 
	  
  jQuery('.opcq').each( function() {
	    var e = jQuery(this); 
		array += '&'+e.attr('name')+'='+e.val(); 
	  
  } 
  ); 
  
  
  
  
  
   
  
  var cmd = 'update_product'+array; 
  //var cmd = 'update_product&cart_virtuemart_product_id='+cart_id+'&quantity='+quantity; 
  
  if ((typeof opc_debug != 'undefined') && (opc_debug === true))
  Onepage.op_log(cmd); 

  return Onepage.op_runSS(this, false, true, cmd);
  
}

function returntocart()
{
	Onepage.ga('Return to cart clicked', 'Checkout navigation'); 
	
	var d1 = document.getElementById('checkout_top'); 
	var d2 = document.getElementById('full_checkout'); 
	if (d1 != null)
	{
		showE(d1); 
	}
	if (d2 != null)
	{
		hideE(d2); 
	}
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



if (typeof pay_btn != 'undefined')
pay_btn['default'] = 'Complete Order'; 




function callAfterShippingSelectM()
{
	
	 
	 
	if (typeof Onepage == 'undefined') return; 
	if (typeof jQuery == 'undefined') return; 
	
	
	
	 var ship_id = Onepage.getInputIDShippingRate();
	 
	if (ship_id == 'choose_shipping') return; 
	if (ship_id == 'shipment_id_0') return; 
	
	var my_label = ship_id; 
	
	
	var label = jQuery("label[for='"+ship_id+"'] .vmshipment_name"); 
	if (label != null)
	if (typeof label.text != 'undefined')
	{
	  	var my_label2 = label.text(); 
		if (my_label2.length > 0) my_label = my_label2; 
		
		my_label = my_label.split("\r\r\n").join(' ').split("\r\n").join(" ").split("\n").join(" ").trim(); 
	}
	
	
	var nameId = document.getElementById('shipping_name_position'); 
	if (nameId != null)
	{
		var ni = jQuery(nameId); 
		ni.html(my_label); 
		
		
	}
	 
}

if (typeof addOpcTriggerer != 'undefined')
{
  
  addOpcTriggerer('callAfterShippingSelect', 'callAfterShippingSelectM()'); 
  
  
}


var opc_basket_wrap_id = 'checkout_top'; 


 function hideStates(prefix) {
  if (!(prefix != null)) prefix = ''; 
	
  var d = jQuery('#'+prefix+'virtuemart_state_id'+'_div'); 
  if (d.length > 0) {
    d.hide(); 
  }
  var d = jQuery('#'+prefix+'virtuemart_state_id'+'_input'); 
  if (d.length > 0) {
    d.hide(); 
  }
}

function showStates(prefix) {
  if (!(prefix != null)) prefix = ''; 
  
  var d = jQuery('#'+prefix+'virtuemart_state_id'+'_div'); 
  if (d.length > 0) {
    d.show(); 
  }
  var d = jQuery('#'+prefix+'virtuemart_state_id'+'_input'); 
  if (d.length > 0) {
    d.show(); 
  }
}
//var callWhenHasStates = new Array(); 
//var callWhenNoStates = new Array(); 
if (typeof addOpcTriggerer != 'undefined') {
  addOpcTriggerer('callWhenHasStates', 'showStates(prefix)'); 
  addOpcTriggerer('callWhenNoStates', 'hideStates(prefix)'); 
}

jQuery(document).ready(function() {
  if (typeof OPCStates == 'undefined') return; 
  var elopc = document.getElementById('virtuemart_country_id'); 
  var value = ''; 
  if (elopc != null) {
  if (elopc.options != null) {
  value = elopc.options[elopc.selectedIndex].value; 
  }
  else
  if (elopc.value != null) {
  value = elopc.value; 
 }

  var statefor = eval('OPCStates.state_for_'+value);   
  if (typeof statefor == 'undefined') {
    hideStates(''); 
  }
  }
  
  var elopc = document.getElementById('shipto_virtuemart_country_id'); 
  var value = ''; 
  if (elopc != null) {
  if (elopc.options != null) {
  value = elopc.options[elopc.selectedIndex].value; 
  }
  else
  if (elopc.value != null) {
  value = elopc.value; 
 }

  var statefor = eval('OPCStates.state_for_'+value);   
  if (typeof statefor == 'undefined') {
    hideStates('shipto_'); 
  }
  }
  
  
}); 