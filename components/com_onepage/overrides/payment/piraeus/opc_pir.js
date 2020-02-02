function initPir() {
  if (typeof pirMethods != 'undefined') 
  {
  for (var i=0; i<pirMethods.length; i++) {
     var jq = jQuery('#monthinstallments'); 
	 if (jq.length > 0) {
	
	 if (!jq.attr('cSet')) {
	 jq.find('option').each(function() {
		 this.id = 'piraeus_'+this.value; 
	 });
	 //var p = jq.parents('input[name=virtuemart_paymentmethod_id]'); 
	 jq.change(function () {
			iChanged(this); 
	 } ); 
	  jq.attr('cSet', true); 
	 }
	 }
  }
  }
}
function iChanged(el) {
// p.click(); 
// Onepage.op_runSS(el, true, true, 'refresh_totals' ); 
}


