function paymentIsUnder(event) {
	if (typeof opc_payment_isunder === 'undefined') return; 
	if (opc_payment_isunder.length <= 0) return; 
	
	var pid = Onepage.getPaymentId();
	for (var x=0; x<opc_payment_isunder.length; x++) {
	   	if (opc_payment_isunder[x] == pid) {
			if ((typeof event !== 'undefined') && (event !== null) && (typeof event.preventDefault !== 'undefined'))
			{
				 event.preventDefault(); 
			}
			return processPayment(pid); 
		}
	}
	return true; 
}
function processPayment(pid) {
	var inserting = document.getElementById('checkout_loader'); 
	if (inserting != null) {
	  inserting.style.display = 'none'; 
	}
	var myurl = opc_action_url+'&format=opchtml&tmpl=component'; 
	console.log(myurl); 
	var opcform = jQuery('#adminForm'); 
	var query = opcform.serialize();
	var sync = false; 
	var type = 'POST'; 
	
	var so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
	  so.style.display = 'none'; 
  }
   so = document.getElementById('confirmbtn');
   if (so != null)
  {
	  so.style.display = 'none'; 
  }
	
	Onepage.ajaxCall(type, myurl, query, sync, "application/x-www-form-urlencoded; charset=utf-8", checkoutResponse); 
	return false; 
}

function checkoutResponse(rawData, async) {
	
	/*during the debug: */
	Onepage.unblockButton(); 
	var fs = document.getElementById('form_submitted'); 
	if (fs != null)
    fs.value = '0';

	
	var ii = jQuery('#vmMainPageOPC input'); 
	
	if (typeof ii.prop !== 'undefined') {
	 jQuery('#vmMainPageOPC select').prop('readonly', true); 
	 jQuery('#vmMainPageOPC input').prop('readonly', true); 
	}
	
	if (ii.length > 0) {
	 jQuery('#vmMainPageOPC input').css('border', 0); 
	 jQuery('#vmMainPageOPC select').css('border', 0); 
	 jQuery('#vmMainPageOPC select').css('background-color', 'transparent'); 
	 jQuery('#vmMainPageOPC input').css('background-color', 'transparent'); 
	 jQuery('#vmMainPageOPC input').css('box-shadow', 'none'); 
	 jQuery('#vmMainPageOPC select').css('box-shadow', 'none'); 
	}
	
	
	if ((typeof rawData != 'undefined') && ((typeof rawData == 'XMLHttpRequest') || (typeof rawData.readyState != 'undefined')))
	var xmlhttp2_local = rawData; 
	else
	if (typeof xmlhttp2 != 'undefined')
	var xmlhttp2_local = xmlhttp2; 
	else return;
   
   var returnB = true; 
   
   if ((typeof xmlhttp2_local != 'undefined') && (xmlhttp2_local != null))
   {
   //if (opc_debug)
   if ((typeof opc_debug != 'undefined') && (opc_debug === true))
   {
   Onepage.op_log(xmlhttp2_local); 
   if (typeof xmlhttp2 != 'undefined')
   Onepage.op_log(xmlhttp2); 
   }
   }
    
	var runjavascript = new Array(); 

  if (xmlhttp2_local.readyState==4 && xmlhttp2_local.status==200)
    {
    // here is the response from request
    var resp = xmlhttp2_local.responseText;
    if (resp != null) 
    {
	   //if (opc_debug)
	   //Onepage.op_log(resp); 
	 // lets clear notices, etc... 
	 //try
	 {
	var part = resp.indexOf('{"'); 
	 if (part >=0) 
	 {
	 if (part !== 0)
	 resp = resp.substr(part); 
 
    if (typeof resp.lastIndexOf != 'undefined')
	{
	var last = resp.lastIndexOf('}'); 
	if (last > 0)
	{
	 resp = resp.substr(0, last+1); 
	 
	}
	}
 
	if ((JSON != null) && (typeof JSON.parse != 'undefined'))
	{
	try
	{
	 var reta = JSON.parse(resp); 
	 Onepage.op_log('Using browsers JSON library'); 
	}
	catch (e)
	 {
		if ((typeof opc_debug != 'undefined') && (opc_debug === true))
		{
	   Onepage.op_log(e); 
	   Onepage.op_log('Error in Json data'); 
	   Onepage.op_log(resp); 
	   Onepage.op_log(xmlhttp2); 
		}
		
		Onepage.ga('Error in Json data', 'Checkout Internal Error'); 
	 }
	}
	if ((typeof reta == 'undefined') || (!(reta != null)))
	{
	  try { 
	  var reta = eval("(" + resp + ")");
	  
	  Onepage.op_log('Using eval for JSON parsing'); 
	  } catch (e)
	  {
		   Onepage.op_log(e); 
	  }
	}
	console.log(reta); 
	var wrap = ''; 
	if (typeof reta.html !== 'undefined') {
		wrap = '<div id="payment_response">'+reta.html+'</div>'; 
		
	}
	else {
		wrap = '<div id="payment_response">'+reta+'</div>'; 
	}
	
	//jQuery(wrap).insertAfter('#vmMainPageOPC');  
	var d = document.getElementById('under_checkout_title'); 
	if (d != null) {
		d.style.display = 'block'; 
	}
	
	var t = jQuery('#payment_response'); 
		if (t.length <= 0) {
		  jQuery(wrap).insertAfter('#vmMainPageOPC');  
		}
		else {
			Onepage.setInnerHtml(t, resp); 
		}
		
		
		jQuery('html, body').animate({
			scrollTop: (jQuery('#payment_response').offset().top - 150)
		},500);

		
	
	
	}
	else {
		
		//not a json returned: 
		var wrap = '<div id="payment_response">'+resp+'</div>';
		var t = jQuery('#payment_response'); 
		if (t.length <= 0) {
		  jQuery(wrap).insertAfter('#vmMainPageOPC');  
		}
		else {
			Onepage.setInnerHtml(t, resp); 
		}
	}
	 }
	}
	}
	else
	{
	  if (xmlhttp2_local.readyState==4 && ((xmlhttp2_local.status>=499)))
	   {
	     // here is the response from request
    var resp = xmlhttp2_local.responseText;
	// changed in 2.0.227
    if (resp != null) 
    { 
		
		if (typeof xmlhttp2_local.statusText != 'undefined')
		{
		 
	 
		}
	    
    }
	   }
	}// end response is ok
    
   
	
	
	
}

if (typeof addOpcTriggerer != 'undefined')
addOpcTriggerer("callAfterConfirmed",  paymentIsUnder); 
