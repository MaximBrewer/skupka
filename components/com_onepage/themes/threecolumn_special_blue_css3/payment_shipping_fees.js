function setPricePayment() {
  setPriceSPWrap(); 
}

function setPriceSPWrap() {
  setPriceAll(); 
  try {
	  var shipping_id = Onepage.getInputIDShippingRate();
	  var payment_id = Onepage.getPaymentId();
	  setPriceShipping(shipping_id, payment_id); 
  }
  catch(e) {
  }
  
}

function setPriceAll() {
  jQuery('[name=virtuemart_shipmentmethod_id]').each( function() {
	  if (typeof this.value == 'undefined') return true; 
	   var id = this.id; 
	   jQuery('[name=virtuemart_paymentmethod_id]').each( function() {
		   if (typeof this.value == 'undefined') return true;  
		   var value = this.value; 
		     setPriceShipping(id, value); 
		   
	   } ); 
  } );
  
}

function setPriceShipping(shipping_id, payment_id) {
 
 var t = Onepage.getTotalsObj(shipping_id, payment_id); 
 
 if (typeof jQuery != 'undefined') {
    
	
	
	var j = jQuery('label[for='+shipping_id+'] .vmshipment_cost');
	if (j.length > 0) {
	 var html = j.html(); 
	 //var str = t.order_shipping.name+': '+t.order_shipping.valuetxt;
	 var str = t.order_shipping.valuetxt;
	
	 if (html.indexOf('(')>=0) str = ' ('+str+')'; 
	 j.html(str); 
	}
	


	
	
	var j = jQuery('label[for=payment_id_'+payment_id+'] .vmpayment_cost');
	if (j.length > 0) {
	 var html = j.html(); 
	 //var str = t.payment_discount.name+': '+t.payment_discount.valuetxt;
	 var str = t.payment_discount.valuetxt;
	
	 if (html.indexOf('(')>=0) str = ' ('+str+')'; 
	 j.html(str); 
	}
	else
	{
		var jP = jQuery('[name=virtuemart_paymentmethod_id][value='+payment_id+']'); 
		var id = jP.attr('id'); 
		if (typeof id != 'undefined') {
		var j = jQuery('label[for='+id+'] .vmpayment_cost');
			if (j.length > 0) {
			var html = j.html(); 
			//var str = t.payment_discount.name+': '+t.payment_discount.valuetxt;
			var str = t.payment_discount.valuetxt;
			
			if (html.indexOf('(')>=0) str = ' ('+str+')'; 
			j.html(str); 
		}
		}
	}


 }
}


if (typeof addOpcTriggerer != 'undefined')
addOpcTriggerer("callAfterPaymentSelect",  "setPriceSPWrap()"); 


if (typeof addOpcTriggerer != 'undefined')
addOpcTriggerer("callAfterShippingSelect",  "setPriceSPWrap()"); 
