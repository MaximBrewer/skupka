jQuery(document).ready(function(){



    var cc = ''; 
	if (typeof current_category != 'undefined')
	cc = '&current_category='+current_category; 
	
	
    var lastQ = 'index.php?option=com_virtuemart&view=plugin&type=vmcustom&name=vmaddtabs&cache=no&format=raw&entity=google'+cc; 
	
	vmAddTabs.callAjax(lastQ, '', vmAddTabs.fillOptions); 
	
    
});

var vmAddTabs =  {
	
	
callAjax: function(lastQ, data, callBack) {
jQuery.ajax({
      url: lastQ,
      dataType: "POST",
	  data: '',
	  cache: false,
	  complete: function(datasRaw) 
				  {
				    if (!(datasRaw.readyState==4 && datasRaw.status==200)) return;
					retObj = datasRaw; 
					if (datasRaw.readyState==4 && datasRaw.status==200) result = vmAddTabs.processData(datasRaw, callBack); 
					return result; 
				  },
	  async: true,
     
     
    });
},
processData: function(rawData, callBack) {
	// processing malformatted data (i.e. error reporting enabled)
	if ((typeof rawData != 'undefined') && ((typeof rawData == 'XMLHttpRequest') || (typeof rawData.readyState != 'undefined')))
	var xmlhttp2_local = rawData; 
	else
	if (typeof xmlhttp2 != 'undefined')
	var xmlhttp2_local = xmlhttp2; 
	else return;
   
   
   
   
    
	
  if (!(xmlhttp2_local.readyState==4 && xmlhttp2_local.status==200)) return; 
    
    // here is the response from request
    var resp = xmlhttp2_local.responseText;
    if (!(resp != null)) return; 
    
	  
	 var part = resp.indexOf('{'); 
	 if (part < 0) return;
	 
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
 
	if ((JSON != null) && (typeof JSON.decode != 'undefined'))
	{
	try
	{
	 var reta = JSON.decode(resp); 
	}
	catch (e)
	 {
		   console.log(e); 
	 }
	}
	if ((typeof reta == 'undefined') || (!(reta != null)))
	{
		try { 
	  var reta = eval("(" + resp + ")");
		}
	   catch (e) 
	   {
		   console.log(e); 
		   return; 
	   }
	}
	
	callBack(reta); 

},

fillOptions: function(reta) {

 	
var html = '<select onchange="vmAddTabs.storeMe(this)" id="google_cat_list" class="mychoseng" name="opt" style="min-width: 200px;" ></select>'; 
jQuery('#my_select_comes_here').html(html); 
	
		 jQuery.each(reta, function(k, item) { 
		   //var newO = jQuery('<option value="' + item.id + '" item-data="'+vmAddTabs.op_escape(JSON.stringify(item))+'">' + item.name + '</option>'); 
		   var newO = jQuery('<option value="' + item.id + '">' + item.name + '</option>'); 
		   //newO.attr('item-data', JSON.stringify(item)); 
		   jQuery('#google_cat_list').append(newO);
                
				
				
				});
		 
		
			  
			 jQuery('#google_cat_list').chosen({
	enable_select_all: false, 
	disable_search: false,
	disable_search_threshold: -1,
	allow_single_deselect: true,
	search_contains: true
	
		});
},
displayMsg: function(msg) {
 console.log(msg); 
},
storeMe: function(el)
{
	var data = el.options[el.selectedIndex].value;
	
	 var cc = ''; 
	 if (typeof current_category != 'undefined')
	 cc = '&current_category='+current_category; 
	 var lastQ = 'index.php?option=com_virtuemart&view=plugin&type=vmcustom&name=vmaddtabs&cache=no&format=raw&entity=google&cmd=store'+cc; 
	 var q = 'item_data_id='+data; 
	 
	 vmAddTabs.callAjax(lastQ, q, vmAddTabs.displayMsg); 
	
},
op_escape: function(str)
  {
   if ((typeof(str) != 'undefined') && (str != null))
   {
     var x = str.split('%').join('%25').split(' ').join('%20').split('$').join('%24').split('`').join('%60').split(':').join('%3A').split('[').join('%5B').split(']').join('%5D').split('+').join('%2B').split("&").join("%26").split("#").join("%23").split('"').join('&quot;');
	 
     return x;
   }
   else 
   return "";
  },
}