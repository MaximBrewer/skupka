var mod_productcustoms = {
	goTo: function(el) {
		var jel = jQuery(el); 
		var hm = jel.data('name'); 
		var result = false; 
		if (typeof hm === 'undefined') return false; 
		
		var stype = jel.attr('type'); 
		if (stype == 'checkbox') {
			result = true; 
		}
		else {
		var s = "input[name='"+jel.data('name')+"'][value='"+jel.data('value')+"']";  
		
		var se = jQuery(s); 
		if (se.length > 0) {
			if (se[0].checked == true) {
				se[0].checked = false;
			}
			else {
			  se[0].checked = true; 
			}
		}
		
		}
		var x = mod_productcustoms.getSelected(); 
		var tag = el.tagName.toLowerCase(); 
		if (tag === 'a') {
			el.href = x; 
			return true; 
		}
		else {
			document.location = x; 
		}
		
		return result; 
	},
	
	getSelected: function() {
		var ret = 'index.php?'; 
		jQuery('.productfilter_selector:checked').each( function() {
			var el = this; 
			if (ret !== '') ret += '&';
		    ret += el.name+'[]='+el.value; 
		}); 
		
		//current main category: 
		var obj = jQuery.parseJSON(window.getUrl); 
		delete obj.virtuemart_category_id; 
		delete obj.virtuemart_custom_id; 
		
		if (ret !== '') ret += '&';
		ret += jQuery.param(obj);
		/*
		if (typeof obj.virtuemart_category_id !== 'undefined') {
			 ret += '&virtuemart_category_id[]='+obj.virtuemart_category_id; 
		}
		*/
		console.log(ret); 
		return ret; 
	}
}