function disableShipTo() {
  if (typeof disable_shipto_per_shipping == 'undefined') return; 
  if (disable_shipto_per_shipping == '') return; 
  //disable_shipto_per_shipping
  var sid = Onepage.getVShippingRate(true); 
 // console.log(sid); 
  var toHide = false; 
  var e = disable_shipto_per_shipping.split(','); 
  for (var i = 0; i < e.length; i++) {
        if (e[i] === sid) {
            toHide = true;
        }
   
   }
   
   var d = document.getElementById('opc_shipto_wrapper'); 
	if (d != null) {
	
	var jel = jQuery(d); 
   
    if (toHide) return hideShipto(jel); 
    else return showShipto(jel); 
   }
   else {
	   console.log('Please add wrapper to your OPC theme Ship to section: <div id="opc_shipto_wrapper"> CONTENT </div>'); 
   }
}	
var emptyhtml = '';
function hideShipto(jel) {
	
	if (window.emptyhtml === '') window.emptyhtml = jQuery('<div id="opc_shipto_wrapper"><input type="hidden" id="sachone" name="sa" value="" autocomplete="off" /></div>'); 
	
	var hf = jQuery('#vmMainPageOPC'); 
	var test = hf.data('storedshipto'); 
	
	
	var storedHtml = jel.clone(); 
	
	if ((typeof test == 'undefined') || (!test)) {
	  hf.data('storedshipto', storedHtml); 
	}
	
	jel.replaceWith(window.emptyhtml); 
}

function showShipto(jel) {
	var hf = jQuery('#vmMainPageOPC'); 
	
	var jelOrig = hf.data('storedshipto'); 
	if ((typeof jelOrig !== 'undefined') && (jelOrig)) {
	 jel.replaceWith(jelOrig); 
	}
	//jel = jelOrig;
	//jel.replaceWith(jelOrig); 
	//jel.detach().html(jelOrig)

	
}

if (typeof addOpcTriggerer != 'undefined')
addOpcTriggerer("callAfterShippingSelect",  disableShipTo); 