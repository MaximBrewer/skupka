function addNewTab(el) {
  /*
  var default_lang = document.getElementById('detected_default_lang').value; 
  var vmLangE = document.getElementById('vmlang'); 
  var vmlang = default_lang; 
  
  if (typeof vmLangE.value !== 'undefined') {
	  vmlang = vmLangE.value;
  }
  else if (typeof vmLangE.selectedIndex !== 'undefined') {
	  vmlang = vmLangE.options[vmLangE.selectedIndex].value; 
  }
  
  if (vmlang !== default_lang) {
	  alert('Adding new tabs is only possible via default language '+default_lang); 
	  return false; 
  }
	*/
  var d = document.getElementById('sys_add_new_tab'); 
  d.value = 1; 
  return Joomla.submitbutton('apply'); 
}



function removeTab(id) {

  var d = document.getElementById('sys_remove_tab'); 
  d.value = id; 
  return Joomla.submitbutton('apply'); 
}

function disableThis() {
	document.getElementById('sys_store_tab_content').value = 'disablethis'; 
	return Joomla.submitbutton('apply'); 
}

function copyParent(el) {
	el.style.display = 'none'; 
	document.getElementById('all_derived_tabs').style.display = 'block'; 
	document.getElementById('sys_store_tab_content').value = 'copyparent'; 
	return false;
}

function removeAll() {
	document.getElementById('sys_store_tab_content').value = 'removeall'; 
	return Joomla.submitbutton('apply'); 
}

function tabClickDefault(el) {
   if (typeof jQuery == 'undefined') return Joomla.submitbutton('apply'); ; 
   
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