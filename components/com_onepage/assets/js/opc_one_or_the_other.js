if (typeof addOpcTriggerer != 'undefined') {
  addOpcTriggerer('callSubmitFunct', 'checkOneOrTheOther'); 
}

function checkOneOrTheOther() {
   if ((typeof one_or_the_other != 'undefined') && (typeof one_or_the_other2 != 'undefined'))
   {
	   var isValid = true; 
	   var iFi = new Array(); 
	   var iFi2 = new Array(); 
	   var isB = Onepage.isBusinessCustomer(); 
	   
	   Onepage.fastValidation('', one_or_the_other, isValid, iFi, false, true); 
	   Onepage.fastValidation('', one_or_the_other2, isValid, iFi2, false, true); 
	   if ((one_or_the_other.length > 0) && (one_or_the_other2.length == one_or_the_other.length)) {
			for (var i=0; i<one_or_the_other.length; i++ ) {
			   var f1 = one_or_the_other[i]; 
			   var f2 = one_or_the_other2[i]; 
			   if ((iFi.indexOf(f1) >= 0) && (iFi2.indexOf(f2)>=0)) {
			
			
			
				// both fields are business: 
				/*
			 if ((business_fields.indexOf(f1) >= 0) && (business_fields.indexOf(f2) >= 0)) {
			   if (!isB) {
			
				   continue; 
			   }
			 }
			 */
			 
			 var b2 = Onepage.getBusinessState(); 
			 if ((business_fields2.indexOf(f1) >= 0) && (business_fields2.indexOf(f2))) {
			   if (!b2) {
			        continue; 
			   }
			 }
			 
			 if ((!b2) && (!isB)) {
			  if ((business_fields.indexOf(f1) >= 0) && (business_fields.indexOf(f2) >= 0)) {
					// both fields are business, but it's not a business customer: 
						 continue; 
					  }
			   }
			
			
			if (typeof op_userfields_named[f1] != 'undefined')
				var f1named = op_userfields_named[f1]; 
			if (typeof op_userfields_named[f2] != 'undefined')
				var f2named = op_userfields_named[f2]; 
		    
			     var msg = 'Missing value '+f1named+' or '+f2named; 
				 if (typeof COM_ONEPAGE_MISSING_ONE_OR_THE_OTHER != 'undefined') {
				   msg = COM_ONEPAGE_MISSING_ONE_OR_THE_OTHER.split('{field1}').join(f1named).split('{field2}').join(f2named); 
				 }
				 alert(msg); 
			     return false; 
			   }
			    
			   
			}
	   }
   }
   return true; 
}