function catDropDowncatfilterChange(el, module_id)
{
	
    if (typeof module_id == 'undefined') module_id = 0; 
	if (!module_id) module_id = 0; 
	
	
   //global variables used: catDropDowncaturl, selectmodel, nomodels
   if (typeof catDropDowncaturl == 'undefined') {
     var url = '/index.php?option=com_ajax&module=virtuemart_catDropDowncatfilter&format=raw&nosef=1&module_id='+module_id; 
   }
   else
   {
	   if (catDropDowncaturl.constructor === Array)
	   {
		   var url =  catDropDowncaturl[module_id]; 
	   }
	   else
	   {
		   var url = catDropDowncaturl; 
	   }
   }
   var level = el.getAttribute('level', 1); 
   level = parseInt(level); 
   var lq  = '&level='+level; 
   
   var s1 = el.options[el.selectedIndex]; 
   var val = s1.value; 
   
   var dataString = '&ict='+val+lq
   var levelUp = level + 1; 
   
   
   
   if (typeof showProducts == 'undefined') {
	   var showP = true; 
   }
   else
   {
	   if (showProducts.constructor === Array) {
	     var showP = showProducts[module_id]; 
	   }
	   else
	   {
		   var showP = showProducts; 
	   }
   }
   
   if (!showP)
   {
	   var dplus = document.getElementById('level_'+module_id+'_'+levelUp); 
	   if (!(dplus != null))
	   {
		   return catDropDowncatselectproduct(el); 
	   }
   }
   
   if (val != '')
     {
	    jQuery.ajax({
			type: "GET",
			url: url,
			data: dataString,
			cache: true,
			success: function(html, status, xhr){
				populateSelect(html, showP, xhr, levelUp, module_id); 
				
			}
       
	  }); 
	 }
}
function getJson(html)
{
	var reta = {}; 
	var part = html.indexOf('{"'); 
	 if (part >=0) 
	 {
	 if (part !== 0)
	 html = html.substr(part); 
 
    if (typeof html.lastIndexOf != 'undefined')
	{
	var last = html.lastIndexOf('}'); 
	if (last > 0)
	{
	 html = html.substr(0, last+1); 
	 
	}
	}
	}
	
	if ((JSON != null) && (typeof JSON.parse != 'undefined'))
	{
	try
	{
	  reta = JSON.parse(html); 
	 
	}
	catch (e)
	 {
		console.log(e); 
	 }
	}
	if ((typeof reta == 'undefined') || (!(reta != null)))
	{
	  try { 
	   reta = eval("(" + html + ")");
	  
	  
	  } catch (e)
	  {
		console.log(e); 
	  }
	}
    
	return reta; 
}
function populateSelect(html, showProducts, xhr, levelUp, module_id)
{
	
	var retObj = getJson(html); 
	if (typeof retObj.cats == 'undefined') {
     console.log(retObj); 	 
	 console.log('error', html); 
	  return; 
	}
	if ((showProducts) && (typeof retObj.products != 'undefined'))
				{
				//var h = xhr.getResponseHeader("isProducts"); 
				
				
				 if (retObj.products.length > 0)
				 {
					 for (var i = levelUp; i<=10; i++)
					 {
						 var dS = 'level_'+module_id+'_'+i; 
						 var d = document.getElementById(dS); 
						 j = i+1; 
						 var dSplus = 'level_'+module_id+'_'+j; 
						 var dplus = document.getElementById(dSplus); 
						 
						 var dSp = 'products_'+module_id; 
						 var dSpe = jQuery('#'+dSp); 
						 // if dplus does not exists, then d is the latest
						 if (dSpe.length > 0) {
							 setHtml(retObj.products, dSpe);
							 if (typeof dSpe.chosen != 'undefined')
							 {
								 dSpe.trigger('chosen:updated'); 
							 }
						 }
						 /*
						 if (!(dplus != null))
						 {
							 
							 var w = jQuery(d); 
							 setHtml(retObj.products, w); 
							 break; 
						 }
						 */
						 // if both exists: 
						 var txt = ''; 
						if (typeof retObj.level_txt[j] != 'undefined')
						{
							txt = retObj.level_txt[j]; 
						}
						 if ((dplus != null) && (txt != ''))
						 {
							 /*
							 d = document.getElementById('levelwrap_'+module_id+'_'+i); 
							 if (d != null)
							 {
								 d.style.display = 'none'; 
							 }
							 */
							 var djplus = jQuery(dplus); 
							 setHtml(txt, djplus);
							 
						 }
						
						 
					 }
				 }
				
				}
			    //console.log(html); 
				/*
				 d = document.getElementById('levelwrap_'+module_id+'_'+levelUp); 
							 if (d != null)
							 {
								 d.style.display = 'block'; 
							 }
				*/
				var w = jQuery('#level_'+module_id+'_'+levelUp); 
				if (w.length > 0)
				{
				  setHtml(retObj.cats, w);
				}
				
				if (retObj.cats.length == 1) {
					 for (var i = levelUp; i<=10; i++)
					 {
						 
						 //j = i+1; 
						 j=i; 
						 var dSplus = 'levelwrap_'+module_id+'_'+j; 
						 var dplus = document.getElementById(dSplus); 
						 if (dplus != null) {
						 djplus = jQuery(dplus); 
						 djplus.hide(); 
						 
						 }
						 else
						 {
							 break; 
						 }
						
						
				     }
				}
				else
				{
					 for (var i = levelUp; i<=10; i++)
					 {
						 
						 //j = i+1; 
						 j=i; 
						 var dSplus = 'levelwrap_'+module_id+'_'+j; 
						 var dplus = document.getElementById(dSplus); 
						 if (dplus != null) {
						 djplus = jQuery(dplus); 
						 djplus.show(); 
						 
						 }
						 else
						 {
							 break; 
						 }
						
						
				     }
				}
				
				
}
function myLog(msg) {
 console.log(msg); 
}
function setHtml(html, w)
{
	
	if (w.length < 0) return; 
	
	var id = w.attr('id'); 
	myLog('id: '+id); 
	
	if (typeof w.empty != 'undefined') {
	w.empty(); }
	
	for (var i=0; i < html.length; i++) {
	  var o = jQuery(html[i]); 
	 // myLog('adding'+html[i]); 
	  w.append(o); 
	}
	if (typeof w.chosen != 'undefined')
	if (typeof w.trigger != 'undefined')
	{
	 w.trigger('chosen:updated'); 
	 w.trigger("liszt:updated");
	}
	return; 
	
}
function catDropDowncatselectproduct(el)
{
   var s1 = el.options[el.selectedIndex]; 
   var rel = ''; 
   
   if (typeof s1.getAttribute != 'undefined')
   rel = s1.getAttribute('rel'); 
   else
   if (typeof s1.rel != 'undefined')
   rel = s1.rel
   else
     {
	    rel = jQuery(el).attr('rel', ''); 
	 }
	//console.log(rel); 
	if (rel != '')
	 {
	   return window.location = rel; 
	 }
}


jQuery(document).ready( function() {
  var hideAll = jQuery('.domreadyhide'); 
  if (hideAll.length > 0) hideAll.hide(); 
}); 