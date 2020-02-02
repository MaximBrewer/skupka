AllDroppoints = {
	allDroppoints : [],
	allMarkers : [],
	visibleMarkers : [],
	searchMarker : false,
	markerCluster : false,
	isLoaded : false,
	currentMarker: false,
	markerTheme: 'theme3',
	shippingTheme: 'theme2',
	maxClosest: 5,
	disableShiptoOPC: true,
	currentMethod: 0,
	cachedGeo: {},
	lastHtmlCache: '',
	lastD: null,
	start: function() {
		if (!AllDroppoints.isLoaded)
		AllDroppoints.startPreloader();
		AllDroppoints.getAllDroppoints();
		
		if (!AllDroppoints.isLoaded)
		AllDroppoints.addEvents();
		
	},
	clear: function() {
		AllDroppoints.map = null; 
		//AllDroppoints.allDroppoints = []; 
		AllDroppoints.allMarkers = []; 
		AllDroppoints.visibleMarkers = []; 
		AllDroppoints.searchMarker = false; 
		AllDroppoints.markerCluster = false; 
		AllDroppoints.isLoaded = false; 
		//AllDroppoints.currentMarker = false; 
		
	},
	startPreloader : function() {
		jQuery('#all-droppoint-map').addClass('preloading');
		var opts = {
			lines: 11, // The number of lines to draw
			length: 3, // The length of each line
			width: 10, // The line thickness
			radius: 30, // The radius of the inner circle
			scale: 1.0, // Scales overall size of the spinner
			corners: 1, // Corner roundness (0..1)
			color: '#000', // #rgb or #rrggbb or array of colors
			opacity: 0.25, // Opacity of the lines
			rotate: 0, // The rotation offset
			direction: 1, // 1: clockwise, -1: counterclockwise
			speed: 1, // Rounds per second
			trail: 50, // Afterglow percentage
			fps: 20, // Frames per second when using setTimeout() as a fallback for CSS
			zIndex: 2e9, // The z-index (defaults to 2000000000)
			className: 'spinner', // The CSS class to assign to the spinner
			top: '50%', // Top position relative to parent
			left: '50%', // Left position relative to parent
			shadow: false, // Whether to render a shadow
			hwaccel: false, // Whether to use hardware acceleration
			position: 'absolute' // Element positioning
		};
		var spinner = new Spinner(opts).spin();
		jQuery('#all-droppoint-map').append(spinner.el);		
	},
	
	stopPreloader : function() {
		jQuery('#all-droppoint-map').removeClass('preloading');
		jQuery('#all-droppoint-map').find('.spinner').remove();
	},
	initmap: function() {
		console.log('google map initialized'); 
	},
	refreshMarkers: function() {
	  if ((AllDroppoints.allDroppoints.length > 0) || (Object.keys(AllDroppoints.allDroppoints).length > 0))
		{
			if (AllDroppoints.allMarkers.length === 0)
			{
				for(var carrier in AllDroppoints.allDroppoints) {
					if (AllDroppoints.allDroppoints.hasOwnProperty(carrier)) {
						
					
					
					
					var cC = carrier.toString(); 
					if ((typeof AllDroppoints.allMarkers[carrier] === 'undefined') || ((AllDroppoints.allMarkers[carrier].length === 0)))
					{
				  
					if (AllDroppoints.skipCarier(carrier)) continue; 
						
						
					 AllDroppoints.allMarkers[carrier] = AllDroppoints.createDroppointMarkers(AllDroppoints.allDroppoints[carrier]);
				    }
					
					}
				}
			}
		}
	},
	getAllDroppoints: function(callBack) {
		 if ((AllDroppoints.allDroppoints.length > 0) || (Object.keys(AllDroppoints.allDroppoints).length > 0))
		{
			if (AllDroppoints.allMarkers.length == 0)
			{
				for(var carrier in AllDroppoints.allDroppoints) {
					if (AllDroppoints.allDroppoints.hasOwnProperty(carrier)) {
						
				
				 var cC = carrier.toString(); 
				if ((typeof AllDroppoints.allMarkers[carrier] === 'undefined') || ((AllDroppoints.allMarkers[carrier].length === 0))) {
				 
				if (AllDroppoints.skipCarier(carrier)) continue; 
					
						
						
					 AllDroppoints.allMarkers[carrier] = AllDroppoints.createDroppointMarkers(AllDroppoints.allDroppoints[carrier]);
					 }
					
					}
				}
			}
			if (typeof callBack != 'undefined')
				return callBack(); 
			
			AllDroppoints.collectAddress();
		    AllDroppoints.showVisibleMarkersInCluster(); 
			
			return;
		}
		
		jQuery.ajax(
			{
				type:'GET',
				url: siteUrl+"modules/mod_coolrunner/media/all_droppoints.json",
				data:{
					
				},
				beforeSend: function() {
					
				}
			}
		)
		.done(function(data, textStatus, jqXHR) {
			try { 
			if(typeof data == 'string') {
				data = JSON.parse(data);
			}
			}
			catch(e) {
			  return; 
			}
			/*
			for (var i=0; i<data.pdk.length; i++)
			{
				if (data.pdk[i].name.indexOf('Posthus Christiansborg')>=0)
				{
			       console.log(data.pdk[i]); 
				}
			 
			}
			*/
			for (var courier in data)
			{
			 if (data.hasOwnProperty(courier)) {
				 var cC = courier.toString(); 
				
					
				
				 
				// if (AllDroppoints.skipCarier(courier)) continue; 
				 
			    AllDroppoints.allDroppoints[cC] = data[courier];
			 }
			}
			
			if (typeof callBack != 'undefined')
			{
				return callBack(); 
			}
			
			AllDroppoints.renderMapWithDroppoints();
		})
		.fail(function() {
			// ERROR
		})
		.always(function() {

		});
	},	
	//http://stackoverflow.com/questions/4057665/google-maps-api-v3-find-nearest-markers
	
	/* pass dropoints, geolocation, callback
	 OR markers, current marker
	 OR makers
	 */
	 find_closest_markers: function( markers, currentM, callBack ) { 
	 
	 if (typeof callBack !== 'undefined')
	 {
		 var arrayMarkers = []; 
		 var j = 0; 
		 		for(var carrier in AllDroppoints.allDroppoints) {
					if (AllDroppoints.allDroppoints.hasOwnProperty(carrier)) {
					
					if (AllDroppoints.skipCarier(carrier)) continue; 
					
					for(var i=0; i < AllDroppoints.allDroppoints[carrier].length; i++) {
						{
							
							arrayMarkers.push(AllDroppoints.allDroppoints[carrier][i]); 
						}
					}
					}
				}
		markers = arrayMarkers;		
	 }
	
	if (AllDroppoints.currentMethod == 21)
	{
		console.log('droppoints 21 hhh'); 
	}
	
	if ((typeof currentM != 'undefined') && (currentM != null))
	{
		if (typeof currentM.location != 'undefined')
		{
			var lat1 = currentM.location.lat(); 
			var lon1 = currentM.location.lng(); 
		}
		else
		{
		 var lat1 = currentM.position.lat(); 
		 var lon1 = currentM.position.lng(); 
		}
		
	}
	else
	{
	  var map = AllDroppoints.getMap();  
	 var center = map.getCenter();
	 if (typeof center == 'undefined') return; 
	 var lat1 = center.lat(); 
	 var lon1 = center.lng(); 
	}
	
	
    var pi = Math.PI;
    var R = 6371; //equatorial radius
    var distances = [];
    var closest = -1;
	
    for( i=0;i<markers.length; i++ ) {  
		if (typeof markers[i] == 'undefined') return []; 
        
		if (typeof markers[i].position != 'undefined')
		{
		 var lat2 = markers[i].position.lat();
         var lon2 = markers[i].position.lng();
		}
		else
		{
			var lat2 = markers[i].coordinate.latitude; 
			var lon2 = markers[i].coordinate.longitude
		}			
		

        var chLat = lat2-lat1;
        var chLon = lon2-lon1;

        var dLat = chLat*(pi/180);
        var dLon = chLon*(pi/180);

        var rLat1 = lat1*(pi/180);
        var rLat2 = lat2*(pi/180);

        var a = Math.sin(dLat/2) * Math.sin(dLat/2) + 
                    Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(rLat1) * Math.cos(rLat2); 
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
        var d = R * c;

        distances[i] = d;
		markers[i].distance = d; 
		
        if ( closest == -1 || d < distances[closest] ) {
            closest = i;
        }
    }
	
	var m2 = markers.sort(function(a, b) {
      if (a.distance < b.distance) return -1; 
	  if (a.distance > b.distance) return 1; 
	  return 0; 
	});
	
	if (typeof callBack != 'undefined')
	{
		callBack(markers[0]); 
	}
	else
	{
	if (typeof currentM != 'undefined')
    AllDroppoints.show10closest(m2, currentM); 
	else
	AllDroppoints.show10closest(m2); 
	}
    // (debug) The closest marker is:
   
},
    getSelectedMarker: function(markers)
	{
		if (typeof markers == 'undefined') return false; 
		for (var i=0;  i < markers.length; i++)
		{
		 if (typeof markers[i] == 'undefined') continue; 
		 var marker = markers[i]; 
		//if (markers.hasOwnProperty(marker))
		{
			if (typeof marker.droppoint != 'undefined')
			{
				var d = AllDroppoints.getCurrentData(); 
				if (d === false)
				{
					break; 
				}
				else
				{
					if (d.droppoint_id === marker.droppoint.droppoint_id)
					{
						AllDroppoints.currentMarker =  marker;
						return AllDroppoints.currentMarker; 
					}
				}
			}
		}
		}
		return false; 
	
	},
	show10closest: function(markers, currentM) {
		
			   
	       var s = AllDroppoints.getSelectedMarker(markers); 
		   if ((typeof currentM == 'undefined') &&
		   (s != false))
		   {
			     currentM = s;
		   }
		  
	   

		
	  var max = AllDroppoints.maxClosest; 
	  var d = document.getElementById('closest_points'); 
	  var html = '<fieldset>'; 
	  var htmlLines = ''; 
	  
	  var wrapHtml = document.getElementById('select_display_id'); 
	  var lineHtml = document.getElementById('line_display_id'); 
	  if ((wrapHtml != null) && (lineHtml != null))
	  {
		  html = wrapHtml.innerHTML; 
		  var lhtmlo = lineHtml.innerHTML; ; 
		  var lhtml = ''; 
		  
		  for (var i = 0; i < max; i++) { 
		  if (typeof markers[i] === 'undefined') continue; 
		  lhtml = lhtmlo; 
		  
	      var s = false; 
	      if (typeof currentM != 'undefined')
	      {
		   var id = currentM.droppoint.droppoint_id;
		   if (markers[i].droppoint.droppoint_id == id)
		   {
			   s = true; 
		   }
	      }
		  
		  lhtml = lhtml
		  .split('{droppoint_id}')
		  .join(markers[i].droppoint.droppoint_id)
		  .split('{carrier}')
		  .join(markers[i].droppoint.carrier)
		  .split('{name}')
		  .join(markers[i].droppoint.name)
		  .split('{address.street}')
		  .join(markers[i].droppoint.address.street)
		  .split('{address.postal_code}')
		  .join(markers[i].droppoint.address.postal_code)
		  .split('{address.city}')
		  .join(markers[i].droppoint.address.city); 
		  
		  
	  
	   if (!s) {
		   lhtml = lhtml
		   .split('checked="checked')
		   .join(''); 
	   }
	    
		htmlLines += lhtml; 
	   
	    }
	    html = html.split('{lines_display}').join(htmlLines); 
	  }
	  else
	  {
		  // use default: 
	  
	  for (var i = 0; i < max; i++) { 
	   if (typeof markers[i] === 'undefined') continue; 
	   var s = false; 
	   if (typeof currentM != 'undefined')
	   {
		   var id = currentM.droppoint.droppoint_id;
		   if (markers[i].droppoint.droppoint_id == id)
		   {
			   s = true; 
		   }
	   }
	   html += '<input type="radio" value="'+markers[i].droppoint.droppoint_id+'" id="point_'+markers[i].droppoint.droppoint_id+'" '; 
	   if (s) {
		   html += ' checked="checked" '; 
	   }
	   
	   html += ' name="currently_selected_droppoint" /><label for="point_'+markers[i].droppoint.droppoint_id+'"><span class="carrier">'+markers[i].droppoint.carrier+'</span><span class="point_name">'+markers[i].droppoint.name+'</span><span class="point_street">'+markers[i].droppoint.address.street+'</span>, <span class="point_zip">'+markers[i].droppoint.address.postal_code+'</span> <span class="point_city">'+markers[i].droppoint.address.city+'</span></label>'; 
	  }
	  html += '</fieldset>'; 
	  }
	  
	  if (AllDroppoints.lastHtmlCache != html) { 
	  // do not use innerHTML if not needed: 
	  
	  d.innerHTML = html; 
	  AllDroppoints.lastHtmlCache = html; 
	  }
	  
	  
	  //bind the markers: 
	   for (var i = 0; i < max; i++) { 
	   if (typeof markers[i] === 'undefined') continue; 
	   var d = document.getElementById('point_'+markers[i].droppoint.droppoint_id); 
	   var dz = jQuery(d).data('alreadySet'); 
	   if (dz != true) { 
	   jQuery(d).data('alreadySet', true); 
	   jQuery(d).data('marker', markers[i]); 
	   jQuery(d).click( function() {
		 var el = jQuery(this); 
	     var marker = el.data('marker'); 
		 AllDroppoints.toggleBounce(marker, true); 
	   }); 
	   }
	   }
	  
	  
	},
	
	getClosestSearchLocation: function(callback) {
	    var zipcode = jQuery('#zip_field').val();
		
		if (zipcode == '') {
		  // if zip code is empty try to load the marker
		  
		}
		
		var street = jQuery('#address_1_field').val();
		var country = 'DK'; 
		
		var searchString = street + ", " + zipcode + ", " + country;
		
	   if(typeof AllDroppoints.geocoder == "undefined") {
			AllDroppoints.geocoder = new google.maps.Geocoder();	
		}

		
		AllDroppoints.geocoder.geocode({ address : searchString }, function(results,status){
		  if (status == google.maps.GeocoderStatus.OK) {
		   if ((results.length > 0) && (typeof results[0].geometry != 'undefined'))
		   {
		     var searchLocation = results[0].geometry;
			 callback(searchLocation); 
		   }
		  }
		    
		}); 
	},
	getClosestLocation: function(callback) {
		
		AllDroppoints.getAllDroppoints(function() { 
		//var markers = AllDroppoints.getCurrentMarkers(); 
		
		
		AllDroppoints.getClosestSearchLocation(function(searchLocation) {
		  AllDroppoints.find_closest_markers(AllDroppoints.allDroppoints, searchLocation, 
				callback); 
		}); 
		
		/*
		AllDroppoints.geocoder.geocode({ address : street + ", " + zipcode + ", " + country }, function(results,status){
				
			if (status == google.maps.GeocoderStatus.OK) {
				var searchLocation = results[0].geometry;
				AllDroppoints.find_closest_markers(AllDroppoints.allDroppoints, searchLocation, 
				callback); 
			}
		});
		
		*/
		}); 
		
	},
	
	searchAddressCached: function(map, inputObj, callBack) {
	   
	   var key = inputObj.address; //.toString(); 
	   console.log('searching...', key); 
	   if (typeof AllDroppoints.cachedGeo[key] !== 'undefined') {
		   console.log('address found in cache!'); 
		   return callBack(map, AllDroppoints.cachedGeo[key]); 
	   }
	   AllDroppoints.geocoder.geocode(inputObj, function(results,status){ 
			if (status == google.maps.GeocoderStatus.OK) {
				AllDroppoints.cachedGeo[key] = results[0].geometry.location; 
				console.log(results); 
				return callBack(map, AllDroppoints.cachedGeo[key]); 
				
				
			} else {
				console.log('address search error !'); 
				console.log(google.maps.GeocoderStatus); 
				//console.log(results,status);
			}
			
	   }); 
	},
	setMapCenterToLocation: function(map, searchLocation) {
	  
	  var l1 = searchLocation.lat(); 
	  var l2 = searchLocation.lng(); 
	  console.log('new location', l1, l2); 
	  map.setCenter(searchLocation);
	  map.setZoom(13);
	  console.log('we found the address !'); 
	  AllDroppoints.showVisibleMarkersInCluster(); 
	},
	/* narrows map to the entered address */
	narrowMap : function(marker) {
		
		var map = AllDroppoints.getMap(); 
		if(typeof AllDroppoints.geocoder == "undefined") {
			AllDroppoints.geocoder = new google.maps.Geocoder();	
		}
		if ((typeof marker === 'undefined') || (!(marker != null))) {
		zipcode = jQuery('#search-zipcode').val();
		street = jQuery('#search-street').val();
		country = jQuery('#search-country').val();
		
		AllDroppoints.searchAddressCached(map, { address : street + ", " + zipcode + ", " + country }, AllDroppoints.setMapCenterToLocation); 
		
		
		
		}
		else
		{
			// narrow map to the selected marker
			var searchLocation = marker.position; 
			
			map.setCenter(searchLocation);
			//map.setZoom(13);
			
		}
		
		
		
	},	
	skipCarier: function(carrier) {
	  var cC = carrier.toString(); 
	  
	  //console.log('carier', cC, AllDroppoints.currentMethod); 
	  
	  if ((coolrunner_methods_post.indexOf(AllDroppoints.currentMethod)<0) && (cC === 'pdk')) {
		  //console.log('skipping', cC); 
		  return true;  
	  }
				 if ((coolrunner_methods_dao.indexOf(AllDroppoints.currentMethod)<0) && (cC === 'dao'))
				 {
					 //console.log('skipping', cC); 
					 return true; 
				 }
				 if ((coolrunner_methods_gls.indexOf(AllDroppoints.currentMethod)<0) && (cC === 'gls')) {
					 //console.log('skipping', cC); 
					 return true; 
				 }				 
				 
				 //console.log('accepted:', cC); 
				 return false; 
				 
	},
	renderMapWithDroppoints : function() {
//		var sw = new google.maps.LatLng(53,7);
//		var ne = new google.maps.LatLng(59,13.5);
		
			//56.184964,11.651714
		
		
		
		AllDroppoints.bounds = new google.maps.LatLngBounds();
		var c = document.getElementById("all-droppoint-map-canvas"); 
		AllDroppoints.map = AllDroppoints.getMap(); 
		
		
		
		//AllDroppoints.map = new google.maps.Map(document.getElementById("all-droppoint-map-canvas"));
		
		
		for(var carrier in AllDroppoints.allDroppoints) {
			 if (AllDroppoints.allDroppoints.hasOwnProperty(carrier)) {
			
			if ((typeof AllDroppoints.allMarkers[carrier] === 'undefined') || ((AllDroppoints.allMarkers[carrier].length === 0))) {
			if (AllDroppoints.skipCarier(carrier)) continue; 
			
			AllDroppoints.allMarkers[carrier] = AllDroppoints.createDroppointMarkers(AllDroppoints.allDroppoints[carrier]);
			  }
			 }
		}
		
		AllDroppoints.map.fitBounds(AllDroppoints.bounds);
		AllDroppoints.map.setZoom(13);
		
		AllDroppoints.showVisibleMarkersInCluster();
		
		AllDroppoints.collectAddress(); 
		
		
		
		
		AllDroppoints.stopPreloader();
				
	},
	
	getMap: function()
	{
		var latlng = new google.maps.LatLng(56.18,11.65);
		
		var mapOptions = {
			center: latlng,
			zoom: 13,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			draggable: true,
			zoomControl: true,
			scrollwheel: true,
			disableDoubleClickZoom: true,
			keyboardShortcuts: false,
			streetViewControl: false
		};
		
		var c = document.getElementById("all-droppoint-map-canvas"); 
		
		redo = false; 
		/*
		if (c != null)
		{
			if (c.innerHTML.length < 100) {
			 redo = true; 
			}
		}
		*/
		
		if ((!redo) && (typeof AllDroppoints.map != 'undefined') && (AllDroppoints.map != null)) 
		{
			return AllDroppoints.map; 
		}
		
		AllDroppoints.map = new google.maps.Map(c, mapOptions);
		
		AllDroppoints.isLoaded = true; 
		return AllDroppoints.map; 
	},
	
	
	
setOPCAddress: function(address, prefix, suffix)
{
	
  if (typeof prefix === 'undefined') prefix = ''; 
  if (!(prefix != null)) prefix = ''; 

  if (typeof suffix === 'undefined') suffix = '_field'; 
  if (!(suffix != null)) suffix = '_field'; 
  
  
  if (address != null)
    { ;; } else return;


  if ((prefix === 'shipto_') && (suffix === '_field'))
  if (AllDroppoints.disableShiptoOPC)
  {
	   if (typeof Onepage != 'undefined')
	   {
          AllDroppoints.closeShipping(); 
	   }
	   return;
  }




  var d = document.getElementById(prefix+'address_type_name'+suffix); 
  if (d != null)
	if (address.name != null)
  if (address.name != '')
  if (typeof d.value != 'undefined')
  d.value = address.name; 
  else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.name;   

  
    /* sync first and last name */
  if (prefix != '')
  {
	  var d = document.getElementById('first_name'+suffix); 
	  if (d != null)
	  {
		  var d2 = document.getElementById(prefix+'first_name'+suffix); 
		  if ((typeof d2.value != 'undefined') && (typeof d.value != 'undefined') && (d2.value == ''))
		  d2.value = d.value
	  }
  }
  
    if (prefix != '')
  {
	  var d = document.getElementById('last_name'+suffix); 
	  if (d != null)
	  {
		  var d2 = document.getElementById(prefix+'last_name'+suffix); 
		   if ((typeof d2.value != 'undefined') && (typeof d.value != 'undefined') && (d2.value == ''))
		  d2.value = d.value
	  }
  }
/* end sync names */

  

  d = document.getElementById(prefix+'email'+suffix); 
  if (d != null)
  if (address.email != null)
  if (address.email != '')
  //if (d.value == '')
  if (typeof d.value != 'undefined')
  d.value = address.email; 
  else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.email; 

  d = document.getElementById(prefix+'phone_1'+suffix); 
  if (d != null)
  if (address.telno != null)
  if (address.telno != '')
  //if (d.value == '')
	  if (typeof d.value != 'undefined')
  d.value = address.telno; 
else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.telno; 

  d = document.getElementById(prefix+'first_name'+suffix); 
  if (d != null)
  if (address.fname != null)
  if (address.fname != '')
  //if (d.value == '')
	  if (typeof d.value != 'undefined')
  d.value = address.fname; 
else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.fname; 

  d = document.getElementById(prefix+'company_name'+suffix); 
  if (d != null)
  if (address.company != null)
  if (address.company != '')
  //if (d.value == '')
	  if (typeof d.value != 'undefined')
  d.value = address.company; 
else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.company; 

  d = document.getElementById(prefix+'last_name'+suffix); 
  if (d != null)
  if (address.lname != null)
  if (address.lname != '')
  //if (d.value == '')
	  if (typeof d.value != 'undefined')
  d.value = address.lname; 
else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.lname; 
  
  d = document.getElementById(prefix+'zip'+suffix); 
  if (d != null)
  if (address.zip != null)
  if (address.zip != '')
  //if (d.value == '')
	  if (typeof d.value != 'undefined')
  d.value = address.zip; 
else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.zip; 
  
   d = document.getElementById(prefix+'city'+suffix); 
  if (d != null)
  if (address.city != null)
  if (address.city != '')
  //if (d.value == '')
	  if (typeof d.value != 'undefined')
  d.value = address.city; 
else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.city; 
  
  d = document.getElementById(prefix+'address_1'+suffix); 
  if (d != null)
  if (address.street != null)
  if (address.street != '')
  //if (d.value == '')
  {
	  if (typeof d.value != 'undefined')
	  {
   d.value = address.street; 
   if (address.house_number != null)
   if (address.house_number != '')
   d.value += ' '+address.house_number;
   
   if (address.house_extension != null)
   if (address.house_extension != '')
   d.value += ' '+address.house_extension;
	  }
	  else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.street; 
  }
  
  if (typeof address.address_2 != 'undefined')
	  if (address.address_2 != '')
	  {
		d = document.getElementById(prefix+'address_2'+suffix); 
		if (typeof d.value != 'undefined')
		d.value = address.address_2; 
	else if (typeof d.innerHTML != 'undefined') d.innerHTML = address.address_2; 
	  }
  
  
  
 
},

setOPCAddressByClass: function(droppoint, prefix, suffix, wrapper)
{
	
	var sf = AllDroppoints.getCurrentSuffix(); 
	
	
	var address = {}; 
		address.email = ''; 
		address.telno = ''; 
		address.fname = ''; 
		address.lname = ''; 
		address.company = droppoint.carrier; 
		address.city = droppoint.address.city; 
		address.zip = droppoint.address.postal_code; 
		address.street = droppoint.address.street; 
		address.name = droppoint.name; 
		address.address_2 = droppoint.address.street; 
		address.country = '';
	
	
  if (typeof prefix === 'undefined') prefix = ''; 
  if (!(prefix != null)) prefix = ''; 

  if (typeof suffix === 'undefined') suffix = '_field'; 
  if (!(suffix != null)) suffix = '_field'; 
  
  
  if (suffix == '_shipping')
  {
	  if (AllDroppoints.currentMethod === 0) return;
	 
	  if (AllDroppoints.skipCarier(droppoint.carrier)) {
		   console.log('skipping display: '+AllDroppoints.currentMethod,droppoint.carrier); 
		  return;
	  }
  }
  
  var src = siteUrl+'modules/mod_coolrunner/media/'+AllDroppoints.shippingTheme+'/'+droppoint.carrier+'.png'; 
  if (typeof wrapper != 'undefined')
  {
	  wrapper = wrapper+sf; 
	  var w = jQuery('.'+wrapper); 
	    w.each( function() {
	    var e = jQuery(this); 
		var html = e.html(); 
		html = html.split('{address_type_name_shipping}').join('<img src="'+src+'" class="droppoint_img" alt="'+droppoint.name+'"/>'); 
		e.html(html); 
	    e.show(); 
	  }); 
  }
  
  var imgs = jQuery('.'+wrapper+' img.droppoint_img'); 
  imgs.each(function() { 
   var e = jQuery(this); 
   e.attr('src', src); 
  }); 
  
  if (address != null)
    { ;; } else return;

  prefix = '.'+prefix; 
  prefix = '.'+wrapper+' '+prefix; 

  console.log(prefix+'address_type_name'+suffix); 
  
  var d = jQuery(prefix+'address_type_name'+suffix); 
 if (d.length > 0)
	if (address.company != null)
  if (address.company != '')
  d.html(address.company); 


var d = jQuery(prefix+'name'+suffix); 
 if (d.length > 0)
	if (address.name != null)
  if (address.name != '')
  d.html(address.name); 

d = jQuery(prefix+'address_1'+suffix); 
  if (d.length > 0)
  if (address.street != null)
  if (address.street != '')
  {
	  
	  
		  var a = ''; 
   a = address.street
   if (address.house_number != null)
   if (address.house_number != '')
   a += ' '+address.house_number;
   
   if (address.house_extension != null)
   if (address.house_extension != '')
   a += ' '+address.house_extension;
	
	d.html(a); 

	  
	  
  }


  d = jQuery(prefix+'zip'+suffix); 
  if (d.length > 0)
  if (address.zip != null)
  if (address.zip != '')
  //if (d.value == '')
  d.html(address.zip); 
  
   d = jQuery(prefix+'city'+suffix); 
  if (d.length > 0)
  if (address.city != null)
  if (address.city != '')
  d.html(address.city);

  
  
  
  
  
  
  
 
},
	getCurrentSuffix: function() {
		
		return '_'+AllDroppoints.currentMethod; 
	},
	storeAddress: function()
	{
		var marker = AllDroppoints.currentMarker; 
		
		
		if (marker !== false) {
		 AllDroppoints.setOPCAddressByClass(marker.droppoint, '', '_shipping', 'shipping_address_wrap'); 
		 AllDroppoints.setCurrentData(marker.droppoint); 
		}
		AllDroppoints.clear(); 
		AllDroppoints.closeModal(); 
		
	},
	closeModal: function() {
		jQuery.simplemodal.close();
		if (AllDroppoints.lastD != null) {
		jQuery('html, body').animate({
        scrollTop: jQuery(AllDroppoints.lastD).offset().top
		}, 2000);
		}
	},
	collectAddress: function()
	{
		if (typeof Onepage != 'undefined')
			if (typeof Onepage.setKlarnaAddress != 'undefined')
			{
				var shipping_open = false; //Onepage.shippingOpen(); 
					
					//if (shipping_open)
					//prefix = 'shipto_'; 
					//else 
						var prefix = ''; 
				
				var suffix = '_field'; 
				
				var zip = document.getElementById(prefix+'zip'+suffix); 
				if (!shipping_open)
				{
				 var address_2 = document.getElementById(prefix+'address_2'+suffix); 
				 var address_1 = document.getElementById(prefix+'address_1'+suffix); 
				}
				else
				{
					var address_2 = document.getElementById(prefix+'address_1'+suffix); 
				    var address_1 = document.getElementById(prefix+'address_2'+suffix); 
				}
				
				var sz = document.getElementById('search-zipcode'); 
				var ss = document.getElementById('search-street'); 
				var sc = document.getElementById('search-country'); 
				if (sz != null)
				{
				if ((zip != null) && (zip != ''))
				{
				  sz.value = zip.value; 	
				}
				else
				{
					var zip = document.getElementById('zip'+suffix); 
					if ((zip != null) && (zip != ''))
					{
					   sz.value = zip.value; 	
					}
					
					
				}
				
				if (ss != null)
				{
				
				if ((address_1 != null) && (address_1 != ''))
				{
					ss.value = address_1.value; 	
				}
				else
				{
					
					if ((address_2 != null) && (address_2 != ''))
					{
					   ss.value = address_2.value; 	
					}
					
					
				}
				}
				
				}
				
				
				
				AllDroppoints.narrowMap(); 
				
			}
	},
	
	getCurrentMarkers: function() {
	var markers = []; 
	
	if (AllDroppoints.allMarkers.length == 0)
			{
				for(var carrier in AllDroppoints.allDroppoints) {
					 if (AllDroppoints.allDroppoints.hasOwnProperty(carrier)) {
				if ((typeof AllDroppoints.allMarkers[carrier] === 'undefined') || ((AllDroppoints.allMarkers[carrier].length === 0)))  {		 
						 if (AllDroppoints.skipCarier(carrier)) continue; 
						 
					AllDroppoints.allMarkers[carrier] = AllDroppoints.createDroppointMarkers(AllDroppoints.allDroppoints[carrier]);
					
					 markers = markers.concat(AllDroppoints.allMarkers[carrier]);
					
					
				}
				else
				{
					markers = markers.concat(AllDroppoints.allMarkers[carrier]);
				}
					 }
				}
			}
	
	AllDroppoints.getSelectedMarker(markers); 
	
	/*
	jQuery("#carrier-selection input[type=checkbox]").each(function(){

			if(this.checked) {
				var key = this.id.replace('-droppoints',''); 
				if (typeof AllDroppoints.allMarkers[key] != 'undefined')
				{
				  if (!AllDroppoints.skipCarier(key)) {
					
				  markers = markers.concat(AllDroppoints.allMarkers[key]);
				  }
				}
			} 

		});
		*/
		return markers; 
	},
	/* displayes the cluster */
	showVisibleMarkersInCluster : function() {

		var markers = AllDroppoints.getCurrentMarkers();
		var markers_options = { gridSize:20, maxZoom : 14, minimumClusterSize : 15 };	
		
		
		if(!AllDroppoints.markerCluster) {
			AllDroppoints.markerCluster = new MarkerClusterer(AllDroppoints.map, markers, markers_options);		
		} else {
			AllDroppoints.markerCluster.clearMarkers();
			AllDroppoints.markerCluster.addMarkers(markers);
		}
		
		AllDroppoints.find_closest_markers(markers); 
		AllDroppoints.stopPreloader();
	},
	
	createDroppointMarkers : function(droppoints) {
		var markers = [];
		var j = 0;
		while (j < droppoints.length) {
			var droppoint = droppoints[j];
			var droppoint_id = droppoint.droppoint_id;
			
			if (droppoint.coordinate.latitude === 0) {
				j++; 
				continue; 
			}
			
			var myLatlng = new google.maps.LatLng(droppoint.coordinate.latitude, droppoint.coordinate.longitude);
			if (typeof AllDroppoints.bounds == 'undefined')
			{
				AllDroppoints.bounds  = new google.maps.LatLngBounds();
			}
			AllDroppoints.bounds.extend(myLatlng);		
			var marker = new google.maps.Marker({
				position: myLatlng,
				title: droppoint.name,
				icon: window.siteUrl+'/modules/mod_coolrunner/media/'+AllDroppoints.markerTheme+'/'+ droppoint.carrier + '.png',
				
				droppoint: droppoint,
				carrier: droppoint.carrier
			});
			
			//console.log(marker.title); 
			
			google.maps.event.addListener(marker, 'click',function() {
				AllDroppoints.toggleBounce(this);
			});
			markers.push(marker);
			j++;
		}

		return markers;
	},
	
	toggleBounce : function(marker, fromClick) {
		if(typeof infowindow != 'undefined')
		{
			infowindow.close();
			infowindow = null;
		}
		infowindow = new google.maps.InfoWindow({
			content: AllDroppoints.getInfoWindowContent(marker.droppoint)
		});
		
		//console.log(marker.droppoint); 
		var address = {}; 
		address.email = ''; 
		address.telno = ''; 
		address.fname = ''; 
		address.lname = ''; 
		address.company = marker.droppoint.carrier; 
		address.city = marker.droppoint.address.city; 
		address.zip = marker.droppoint.address.postal_code; 
		address.street = marker.droppoint.name; 
		address.name = marker.droppoint.name; 
		address.address_2 = marker.droppoint.address.street; 
		address.country = '';
		
		if (typeof Onepage != 'undefined')
			if (typeof Onepage.setKlarnaAddress != 'undefined')
			{
				var shipping_open = Onepage.shippingOpen(); 
				prefix = 'shipto_'; 
				
				var suffix = '_field'; 
				AllDroppoints.setOPCAddress(address, prefix, suffix); 
				AllDroppoints.setOPCAddress(address, '', '_html'); 
				
				var d = document.getElementById('selected_drop_address'); 
				if (d != null)
					d.style.display = 'block'; 
				
				
				
				AllDroppoints.setCurrentData(marker.droppoint);
				/*
				var d = document.getElementById('coolrunner_pobocky'); 
				 if (d!=null)
				 { 
			 d.value = JSON.stringify(marker.droppoint); 
			 if (typeof jQuery.cookie != 'undefined')
			 jQuery.cookie("address_data", JSON.stringify(marker.droppoint));
				 }
				 
				}
				else
				if (d != null)
				{
				  d.value = JSON.stringify(marker.droppoint); 
				  if (typeof jQuery.cookie != 'undefined')
				  jQuery.cookie("address_data", JSON.stringify(marker.droppoint));
				}
				*/
				
				
				
				
			}
			
		var markers = AllDroppoints.getCurrentMarkers(); 
		
		if ((typeof fromClick === 'undefined') || (!(fromClick != null)))
		{ AllDroppoints.find_closest_markers(markers, marker); }
		else {
		  AllDroppoints.narrowMap(marker); 
		}
	
		AllDroppoints.currentMarker = marker; 
		
		
		//setTimeout(function() { 
		  var map = AllDroppoints.getMap();  
		  //infowindow.position = marker.position; 
		  var marker = AllDroppoints.currentMarker; 
		  infowindow.open(map,marker);
		  
		  var id = marker.droppoint.droppoint_id;
		   var d = document.getElementById('point_'+id); 
		   if (d!= null)
		   {
			if (!d.checked) {
		      jQuery(d).trigger('click'); 
			}
		   }
		  
		//}, 1000); 
		if (AllDroppoints.disableShiptoOPC)
		{
			AllDroppoints.closeShipping();
		}
		else
		{
			AllDroppoints.openShipping();
		}
		
	},
	openShipping: function() {
		if (typeof Onepage === 'undefined') return; 
	var shipping_open = Onepage.shippingOpen();
	   if (!shipping_open)
	   {
		   // open shipping: 
					var sa = document.getElementById('sachone'); 
					jQuery(sa).click(); 
					//Onepage.showSA(sa, 'idsa');
	   }
	
	},
	restoreShipping: function() {
		 if (AllDroppoints.disableShiptoOPC)
			{
	   
		     var sa = document.getElementById('sachone'); 
			 
			 if (sa != null)
			 {
		
				sa.disabled = false; 
		
			}
			}
	},
	closeShipping: function() {
		if (typeof Onepage === 'undefined') return; 
		var shipping_open = Onepage.shippingOpen();
		 var sa = document.getElementById('sachone'); 
		if (shipping_open)
	   {
		    
			 
			 if (sa != null)
			 {
			    jQuery(sa).click(); 
				
				

				 
			 }
	   }
	   
	   if (sa != null)
			 {
				 sa.disabled = true; 
				 jQuery(sa).trigger('refresh'); 
			 }
		
	},
	// if cookie is set, this functino is ignored
	setClosestDroppoint: function(droppoint) {
		/*
		if (typeof jQuery.cookie != 'undefined')
	    var c = jQuery.cookie("address_data"); 
	
		if ((typeof c != 'undefined') && (c != null))
		{
			return AllDroppoints.getSetLastAddress(); 
		}
		*/
		
		AllDroppoints.setCurrentData(droppoint); 
		
		
	    return AllDroppoints.setOPCAddressByClass(droppoint, '', '_shipping', 'shipping_address_wrap'); 							  
							  
	},
	getCurrentData: function(id) {
		
		var skipstore = false; 
		if ((typeof id === 'undefined') || (!(id != null)))
		{
			id = AllDroppoints.currentMethod; 
		}
		else
		{
			skipstore = true; 
		}
		
		if (id == 21)
		{
			console.log('21 here...'); 
		}
		
	    var d = document.getElementById('coolrunner_pobocky'+id); 
		
		if (d != null) {
			try {
				var t1 = JSON.parse(d.value); 
				if (t1 != null)
				{
					if (typeof t1.carrier != 'undefined')
					{
						if (!AllDroppoints.skipCarier(t1.carrier)) {
						
						// we found already stored data: 
						if (!skipstore) { 
						 AllDroppoints.setCurrentData(t1); 
						}
						return t1; 
						}
					}
				}
			}
			catch(e) { 
			
			}
		}
							  
		//set part (get from cookie and store into html): 
		if (typeof jQuery.cookie != 'undefined')
		var c = jQuery.cookie("address_data"+id); 
	
		if ((typeof c != 'undefined') && (c != null))
		{
			var droppoint = JSON.parse(c); 
			if (droppoint != null)
			{
			  //d.value = JSON.stringify(droppoint); 
			  if (typeof droppoint.carrier != 'undefined')
			  {
				  // wrong data are stored: 
				  if (AllDroppoints.skipCarier(droppoint.carrier)) {
					  return false;
				  }
			  }
			  
			  return droppoint; 
			} 
		}
		
		return false; 
		
	},
	setCurrentData: function(droppoint) {
		
		if (AllDroppoints.currentMethod === 0) return; 
		if (droppoint === false) return; 
		if (AllDroppoints.skipCarier(droppoint.carrier)) return; 
		
		var d = document.getElementById('coolrunner_pobocky'+AllDroppoints.currentMethod); 
				// create the input: 
				if (!(d!=null)) {
				var html = '<input type="hidden" value="" id="coolrunner_pobocky'+AllDroppoints.currentMethod+'" name="coolrunner_pobocky'+AllDroppoints.currentMethod+'">';
				
				var f = jQuery("#adminForm"); 
				if (f.length > 0)
				{
				if (typeof f.append != 'undefined')
					f.append(html)
				}
				else
				{
					var f = jQuery("#checkoutForm"); 
				if (f.length > 0)
				{
				if (typeof f.append != 'undefined')
					f.append(html)
				}
				else
				{
					// we've got a problem - wo do not know where to input: 
					var f = jQuery("form"); 
					f.each(function() { 
					 var e = jQuery(this); 
					 e.append(html); 
					}); 
				}
				}
				}
		  d = jQuery('.coolrunner_pobocky'+AllDroppoints.currentMethod); 
		  d.each(function() { 
		  //d = document.getElementById('coolrunner_pobocky'+AllDroppoints.currentMethod); 
							  
							  var e = jQuery(this); 
							  /*
							  if (d != null)
							  {
								  d.value = JSON.stringify(droppoint); 
							  }
							  */
							  e.val(JSON.stringify(droppoint)); 
		  });
		  
		  //set cookie: 
		  //set part (get from cookie and store into html): 
		  if (typeof jQuery.cookie != 'undefined') {
		
		var c = jQuery.cookie("address_data"+AllDroppoints.currentMethod); 
		/*
		if ((typeof c != 'undefined') && (c != null))
		{
			console.log('storing droppoint into cookie', AllDroppoints.currentMethod, droppoint); 
			var droppoint = JSON.parse(c); 
			if (droppoint != null)
			{
		      d.value = JSON.stringify(droppoint); 
			}
		}
		else
			*/
		{
			console.log('storing droppoint into new cookie', AllDroppoints.currentMethod, droppoint); 
			 jQuery.cookie("address_data"+AllDroppoints.currentMethod, JSON.stringify(droppoint));
		}
		
		//generic cookie: 
		
		
		  
		  }
	},
	// cookie has the highest priority here
	getSetLastAddress: function() {
		
		
		 var d = document.getElementById('coolrunner_pobocky'+AllDroppoints.currentMethod); 
		
							  
		//set part (get from cookie and store into html): 
		if (typeof jQuery.cookie != 'undefined')
		var c = jQuery.cookie("address_data"+AllDroppoints.currentMethod); 
	
		if ((typeof c != 'undefined') && (c != null))
		{
			var droppoint = JSON.parse(c); 
			if (droppoint != null)
			{
			  //d.value = JSON.stringify(droppoint); 
			  AllDroppoints.setCurrentData(droppoint); 
			  
			 
		
		return AllDroppoints.setOPCAddressByClass(droppoint, '', '_shipping', 'shipping_address_wrap'); 
			  
			}
		}
		
		
		
							  
							  
							  if (d != null)
							  {
							   var j = d.value; 
							   if (j != '') {
							     var droppoint = JSON.parse(j);
								 if (droppoint != null)
								 {
									 if (typeof jQuery.cookie != 'undefined')
									 jQuery.cookie("address_data", JSON.stringify(droppoint));
									 
		
		
		
		AllDroppoints.setOPCAddressByClass(droppoint, '', '_shipping', 'shipping_address_wrap'); 
		return;
								 }
							   }
							  }
							  
		
	},
	delayedInfoWindow: function() {
		
	},
	getInfoWindowContent : function(droppoint) {
		var content = jQuery('<div>').addClass('droppoint-infowindow-content');
		var carrierLogo = jQuery('<img>',{src:window.siteUrl+'modules/mod_coolrunner/media/'+AllDroppoints.shippingTheme+'/'+ droppoint.carrier + '.png'}).addClass('carrier-logo');
		var name = jQuery('<strong>').addClass('name').text(droppoint.name);
		var address = jQuery('<span>').addClass('address').text(droppoint.address.street + ', ' + droppoint.address.postal_code + ' ' + droppoint.address.city);
		content.append(carrierLogo).append(name).append(address);
		if(droppoint.opening_hours)
		{
			var openinghourslist = jQuery('<ul>').addClass('opening-hours');
			for(var property in droppoint.opening_hours) 
			{
				if (droppoint.opening_hours.hasOwnProperty(property)) {
					var li = jQuery('<li>');
					var weekdaytext = jQuery("#all-droppoint-opening-hours-weekday-"+property.toLowerCase()).html();
					var weekday = jQuery('<div>').addClass("droppoint-weekday").text(weekdaytext);
					
					var openinghours = jQuery('<div>').addClass("droppoint-openinghours");
					var from = jQuery('<span>').addClass("from").text(droppoint.opening_hours[property].from);
					var to = jQuery('<span>').addClass("to").text(droppoint.opening_hours[property].to);
					openinghours.append(from);
					openinghours.append(to);
					li.append(weekday);
					li.append(openinghours);
					openinghourslist.append(li);
				}
			}
			content.append(openinghourslist);
		}
		
		return jQuery('<div>').append(content).html();
	},
	addEvents : function() {
		var el = jQuery('#carrier-selection input[type=checkbox]'); 
		if (el.length > 0) {
			atch = jQuery.data(el, 'atch'); 
		if (!atch){
		jQuery.data(el, 'atch', true); 
		  el.on('change', function(e){
			  console.log('filter pressed'); 
			AllDroppoints.showVisibleMarkersInCluster();
		});
		}
		}
		el = jQuery('.narrow-droppoints .search-droppoints button'); 
		if (el.length > 0) {
		 atch = jQuery.data(el, 'atch'); 
		if (!atch){
		jQuery.data(el, 'atch', true); 
		 el.on('click', function(e){
			console.log('search clicked'); 
			AllDroppoints.narrowMap();
		});	
		}
		}
		el = jQuery('#search-zipcode, #search-street'); 
		if (el.length > 0) {
		atch = jQuery.data(el, 'atch'); 
		if (!atch){
		jQuery.data(el, 'atch', true); 
		el.on('keyup', function(e){
			
			if(e.keyCode == 13) {
				console.log('enter pressed'); 
				AllDroppoints.narrowMap();
			}
		});
		}
		}
	}
};

if (typeof window.siteUrl === 'undefined')
window.siteUrl = '/'; 