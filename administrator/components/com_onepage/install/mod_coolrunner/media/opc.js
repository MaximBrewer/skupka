
function getShippingId() {
	if ((typeof window.coolrunner_methods == 'undefined') ||
		(!window.coolrunner_methods.length) ||
		 (window.coolrunner_methods.length < 1))
			return 0; 
			
  var shipping_id = Onepage.getVShippingRate();
  shipping_id = parseInt(shipping_id); 
  if (coolrunner_methods.indexOf(shipping_id) >= 0)
  {
	  return shipping_id; 
	  
  }
  return 0; 
}

function displayGoogleMap()
{
	if ((typeof window.coolrunner_methods == 'undefined') ||
		(!window.coolrunner_methods.length) ||
		 (window.coolrunner_methods.length < 1))
			return; 
		
	var shipping_id = Onepage.getVShippingRate();
	shipping_id = parseInt(shipping_id); 
	var d = document.getElementById('coolrunner_map'); 
	if (d != null)
	{
	
	//if (shipping_id == 7)
	if (coolrunner_methods.indexOf(shipping_id) >= 0)
	{
		
	  jQuery('#google-modal').simplemodal({ overlayClose:true, opacity: 50, zIndex:9999, 
	  onClose: function(dialog) {
		  AllDroppoints.clear(); 
		  AllDroppoints.closeModal(); 
		  
	  }
	  });
	  var prev = d.style.display.toString(); 
	  d.style.display = 'block'; 
	  
	  AllDroppoints.allMarkers = []; 
	  AllDroppoints.addEvents();
	  if (!AllDroppoints.isLoaded)
	  {
	   AllDroppoints.start();
	  }
	  else
	  {
		  AllDroppoints.refreshMarkers(); 
		  google.maps.event.trigger(AllDroppoints.map,'resize');
	  }
	   
	  if (prev != 'block') 
	  {
		  if (typeof AllDroppoints != 'undefined')
		  {
		   //AllDroppoints.start();
		   AllDroppoints.collectAddress(); 
		  }
	  }
	  //jQuery('.payment_method_section2').css('margin-left', 0); 
	 
	}
	else
	if ((shipping_id === 0) || (isNaN(shipping_id))) { 
	;; // do nothing
	}
	else
	{
		
		//stan: only hide the map if the google map is fully loaded: 
		if (typeof AllDroppoints != 'undefined')
		if (!AllDroppoints.isLoaded)
		{
		   hideGoogle(d); 	
		 // we cannot hide google that fast otherwise it does not load... 
		 {
			 if (d.style.display != 'none')
		 setTimeout(function(){ hideGoogle(d); }, 5000);
		 }
		  
		  
		  
		}
		else
		{
		  hideGoogle(d); 	
		}
	}
	}
}
function updateAllMethods(searchLocation)
{
	   for (var i=0; i<window.coolrunner_methods.length;  i++) {
		   {
			  var id = window.coolrunner_methods[i]; 
		      var s1 = AllDroppoints.currentMethod; 
			   AllDroppoints.currentMethod = id; 
			   AllDroppoints.collectAddress(); 
			    
			 //AllDroppoints.setClosestDroppoint(droppoint); 
			 
			   AllDroppoints.find_closest_markers(AllDroppoints.allDroppoints, searchLocation, function() {
			 
			 }); 
			 
			 AllDroppoints.currentMethod = s1; 
							   
			   
			 AllDroppoints.currentMethod = s1; 
			   
		   
		   }
		   }
}
function updateAddress()
{
	if (typeof AllDroppoints != 'undefined')
		  {
			  	
		   //if (AllDroppoints.isLoaded) 
		   {
			AllDroppoints.getAllDroppoints(function() { 
			AllDroppoints.getClosestSearchLocation(updateAllMethods); 
		   //AllDroppoints.start();
			});
		   
		   }
		  }
		  
}
function displayGoogleMap2(id) {
	
	
	var d = document.getElementById('shipment_id_'+id); 
	if (d != null)
	{
		if (typeof AllDroppoints != 'undefined') { 
			AllDroppoints.currentMethod = id; 
			AllDroppoints.lastD = d; 
		}
	 jQuery(d).trigger('click'); 
     displayGoogleMap();
	 
	 jQuery('html, body').animate({
        scrollTop: jQuery("#simplesimplemodal-container").offset().top
		}, 2000);
	 
	}
	return false; 
}


function updateHtml() {	 
		
		  //shipment_id_7
		  if ((typeof coolrunner_methods == 'undefined') ||
		(!coolrunner_methods.length) ||
		 (coolrunner_methods.length < 1))
		 return; 
		 
		
		 
		 
		 
			for (var i=0;  i<coolrunner_methods.length; i++)
			{
				var m = coolrunner_methods[i]; 
				var id = parseInt(m); 
					if (!isNaN(id)) { 
					  var s = 'label[for="shipment_id_'+id+'"]'; 
					  jQuery(s).each(function() {
					     var qe = jQuery(this); 
						 
						 var he = document.getElementById('shpping_address_display'); 
						 if (he != null)
						 {
							 var zx = document.getElementById('shpping_address_display_'+id); 
							 if (!(zx != null)) {
							 var html = he.innerHTML; 
							 html = html.split('{id}').join(id); 
							  qe.append('<div class="shpping_address_display" id="shpping_address_display_'+id+'">'+html+'</div>'); 
							  
							  if (typeof AllDroppoints != 'undefined') { 
							  var s1 = AllDroppoints.currentMethod; 
								 AllDroppoints.currentMethod = id; 
							 var stored = AllDroppoints.getCurrentData(id); 
							 
							 if (stored !== false)
							 {
								
								 AllDroppoints.setClosestDroppoint(stored); 
								 
								
							 }
							 else
							 {
								 /*
							   AllDroppoints.getClosestLocation(function(droppoint) {
								   setClosest(droppoint); 
								    
							       AllDroppoints.currentMethod = s1; 
							   });
							   */
							 }	
							  }
							 }							  
							  
						 }
						
					  
					  });
					
					}
				
			}
			
			updateAddress(); 
			
			
		    
		  
}
function setClosest(droppoint) {
	//console.log(droppoint);
 AllDroppoints.setClosestDroppoint(droppoint); 
 //AllDroppoints.clear(); 
 //AllDroppoints.currentMarker = {}; 
 
 //AllDroppoints.currentMarker.droppoint = droppoint; 
 //alert('ok'); 
}
function hideGoogle(d)
{
	d.style.display = 'none'; 
	//jQuery('.payment_method_section').css('margin-left', '1%'); 
	
}
function restoreST()
{
	if (typeof AllDroppoints == 'undefined') return; 
	
	if ((typeof window.coolrunner_methods == 'undefined') ||
		(!window.coolrunner_methods.length) ||
		 (window.coolrunner_methods.length < 1))
			return; 
		
	var shipping_id = Onepage.getVShippingRate();
	shipping_id = parseInt(shipping_id); 
	
	
	// if not our method selected, unblock ST
	if (coolrunner_methods.indexOf(shipping_id) < 0)
	{
		
			AllDroppoints.restoreShipping()
	}
	else
	{
	  AllDroppoints.closeShipping(); 
	   
	}
	   
	   
  
	
}

if (typeof addOpcTriggerer != 'undefined') { 
//addOpcTriggerer('callAfterShippingSelect', 'displayGoogleMap()'); 
addOpcTriggerer('callAfterShippingSelect', 'restoreST()'); 
addOpcTriggerer('callBeforeAjax', 'updateAddress()'); 
addOpcTriggerer('callAfterAjax', 'updateHtml()'); 

}