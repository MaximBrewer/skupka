function changeUlozenka(id, update, method_id) {
					window.ulozenkaVmId = method_id; 
				    if (typeof jQuery != 'undefined')
					 jQuery('.zasielka_div1').not('#ulozenka_branch_' + id).hide();
					document.getElementById('ulozenka_pobocka').value=id;
					
					var d = document.getElementById('ulozenka_saved'); 
					if (d != null)
					d.value = id; 
					if (update)
					{
					var dd = document.getElementById('ulozenka_branch_'+id); 
					if (dd != null) {
					dd.style.display='block'; }
					if (typeof jQuery != 'undefined')
					{
					  var el  = jQuery('#shipment_id_'+method_id); 
					  
					 
					  if (!el.is(':checked')) { 
					   jQuery('#shipment_id_'+method_id).prop("checked", true);
					   
					   window.shipment_id = method_id; 
					  }
					}
					else
					document.getElementById('shipment_id_'+method_id).onclick();
					}
					if (typeof Onepage != 'undefined')
					{
					Onepage.changeTextOnePage3();
					
					 if (typeof Onepage.getTotalsObj != 'undefined')
   {
	   var costs = Onepage.getTotalsObj(); 
	   var sh = costs.order_shipping.valuetxt; 
	   var d = document.getElementById('ulozenka_cena'); 
	   if (d != null)
	   {
		   d.innerHTML = sh; 
	   }
	   
   }
					}
					
					return false; 
				}
				
				
 var ulozenkaId = null;
 var ulozenkaIndex = null;  
 var ulozenkaVmId = 0; 
 var lastVmOpt = ''; 
 
 if (typeof ulozenka_error == 'undefined')
 var ulozenka_error = 'Vyberte pobočku Uloženky'; 
 
 function saveUlozenka()
 {
   var dx = document.getElementsByName('virtuemart_shipmentmethod_id'); 
   if (dx != null)
   for (var i = 0; i<dx.length; i++)
    {
	  if (dx[i].checked)
	  lastVmOpt = dx[i].id; 
	}
   var b = document.getElementById('ulozenka_pobocky'); 
   if (b != null)
     {
	   if (b.options != null)
	   if (b.selectedIndex != null)
	   if (b.selectedIndex > 0)
	   {
	    ulozenkaId = b.options[b.selectedIndex].value;
	    ulozenkaIndex =  b.selectedIndex; 
		//Onepage.op_log(ulozenkaId); 
	    return; 
	   }
	 }
 }		

function restoreUlozenka()
 {
   if (!window.ulozenkaVmId) return false; 
   if (lastVmOpt != 'shipment_id_'+ulozenkaVmId) return; 
   
   
   
   //Onepage.op_log(ulozenkaId); 
   
   if (typeof ulozenkaId != 'undefined')
   if (ulozenkaId != null)
   {
    var d = document.getElementById('ulozenka_pobocky'); 
	if (d!= null) 
	  {
	    if (d.options[ulozenkaIndex] != null)
		if (d.options[ulozenkaIndex].value != null)
		if (d.options[ulozenkaIndex].value == ulozenkaId)
		{
	     d.selectedIndex = ulozenkaIndex; 
		 var vmid = d.getAttribute('vmid'); 
		 changeUlozenka(ulozenkaId); 
		} 
	  }
    
   }
   
   if (typeof Onepage.getTotalsObj != 'undefined')
   {
	   var costs = Onepage.getTotalsObj(); 
	   var sh = costs.order_shipping.valuetxt; 
	   var d = document.getElementById('ulozenka_cena'); 
	   if (d != null)
	   {
		   d.innerHTML = sh; 
	   }
	   
   }

 }
 function updatePriceUlozenka()
 {
	 var shipping_id = Onepage.getVShippingRate();
	 if (shipping_id != ulozenkaVmId) return;
	 if (typeof Onepage.getTotalsObj != 'undefined')
   {
	   var costs = Onepage.getTotalsObj(); 
	   var sh = costs.order_shipping.valuetxt; 
	   var d = document.getElementById('ulozenka_cena'); 
	   if (d != null)
	   {
		   d.innerHTML = sh; 
	   }
	   
   }
 }
 
 function validateUlozenka()
	{
	  var method_id = window.ulozenkaVmId; 
	  
	 var ppp = document.getElementById('shipment_id_'+method_id);
     var ppp2 = document.getElementById("ulozenka_pobocky");
     if ((ppp != null) && (ppp2 != null))
     if (ppp.checked == true && (ppp2.value == 0 || ppp2.value == null)) {
	 
	ppp2.className += ' invalid'; 
	 
    alert(ulozenka_error); 
	
	
	
	
    return false; 
    }
	}
	
	if (typeof addOpcTriggerer != 'undefined')  {
    addOpcTriggerer('callSubmitFunct', 'validateUlozenka');
	addOpcTriggerer('callBeforeAjax', 'saveUlozenka()'); 
	addOpcTriggerer('callAfterRender', 'restoreUlozenka()'); 
	addOpcTriggerer('callAfterPaymentSelect', 'updatePriceUlozenka()'); 
	}
 