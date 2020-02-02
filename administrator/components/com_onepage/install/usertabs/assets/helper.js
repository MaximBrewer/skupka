function addNewTab(el) {
  
  return Joomla.submitbutton('apply'); 
}

function removeTab(id) {

  var d = document.getElementById('sys_remove_tab'); 
  d.value = id; 
  return Joomla.submitbutton('apply'); 
}

function tabClickDefault(el) {
   if (typeof jQuery == 'undefined') return; 
   
   var j = jQuery(el); 
   var cID = j.attr('id'); 
   
   jQuery('.litab').each(function() { 
     var els = jQuery(this); 
	 
    
	 var r = els.attr('rel'); 
	 var lId = els.attr('id'); 
	 if (lId == cID) {
	 var d = document.getElementById(r); 
     if (d != null) {
       var dj = jQuery(d); 
	   dj.show(); 
     }
	 els.addClass('selected'); 
	 }
	 else
	 {
	  var d = document.getElementById(r); 
      if (d != null) {
       var dj = jQuery(d); 
	   dj.hide(); 
      }	 
	   els.removeClass('selected'); 
	  
	 }
	 
   }); 
  return false; 
}