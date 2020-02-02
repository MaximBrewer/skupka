function validatePickupDelivery()
{
 // what customer see is the most important:
 
 if (document.getElementById('pickup_checkbox').className.indexOf('button_checkbox_ed')>=0)
 {
  var p1 = document.getElementById('pickup') 
  if ((p1 != null) && (p1.value==''))
   {
      var f1t = document.getElementById('free_date_text'); 
	  if (f1t != null)
	   {
	    f1t.className = f1t.className.split('invalid').join('');   
	   }
      
     var p2 = document.getElementById('pickup_text');
	 if (p2 != null)
	 p2.className += ' invalid'; 
     return false; 
   }
  
  document.getElementById('free_or_pickup_selector').value='pickup'; 
 }
 else
 {
 
 var p1 = document.getElementById('free_date')
 if ((p1 != null) && (p1.value ==''))
  {
     p1 = document.getElementById('pickup_text')
	 if (p1 != null)
	 p1.className = p1.className.split('invalid').join(''); 
     p1 = document.getElementById('free_date_text');
	 if (p1 != null)
	 p1.className += ' invalid'; 
     return false; 

  }
 
    document.getElementById('free_or_pickup_selector').value='free'; 
 } 
 //d = document.getElementById('pickup_shipping');  
 //d.click(); 
 selectShippingMethod(); 
 
 // if slot/time is selected
 var d = document.getElementById('free_time'); 
 if (d != null)
 if (d.selectedIndex != null)
 if (d.options[d.selectedIndex].value == -1)
 {
   alert(already_reserved_error); 
    p1 = document.getElementById('free_time');
	 if (p1 != null)
	 p1.className += ' invalid'; 
   return false; 
 }
 return true; 
	  
				 
}
function beforeLoader(cmd)
{
  
  
  d = document.getElementById('adresaina'); 
  
  d2 = document.getElementById('ship_to_wrapper'); 
  if ((d2 != null) && (d2.style.display != 'none')) fshow = true; 
  else fshow = false; 
  
  if (isShippingOpen()) return 2;
  
  if (((d != null) && (d.checked == true)) || (fshow))
   {
     // another if, the shipping is valid here
	 
	 return 2; 
   }
 return 0; 
}

function isDelivery()
{
  t1 = document.getElementById('pickup_checkbox'); 
  if (t1 != null)
  if (t1.className.indexOf('button_checkbox_ed')>=0)
  return false;
  
  return true; 
}

function isShippingOpen()
{
  if (isDelivery())
   {
     d = document.getElementById('sachone'); 
	 if ((d != null) && (d.checked == true))
	  return true; 
   }
   
  return false; 
}

function afterResponse(html)
{
  
  d = document.getElementById('sachone'); 
  d2 = document.getElementById('ship_to_wrapper'); 
  if ((d2 != null) && (d2.style.display != 'none')) fshow = true; 
  else fshow = false; 
  
  
  //if (((d != null) && (d.checked == true)) || ((fshow)))
   {
 
     // another if, the shipping is valid here
    // pf_checkbox('delivery'); 
  //if (isShippingOpen() || fshow)
  //if (isDelivery())
  if (fshow)
  if (html != null)
  if (html.toString().indexOf('id="pickup_shipping"')<0)
   {
     
     d = document.getElementById('new_shipping_msg'); 
	 if ((typeof d != 'undefined') && (d!=null))
	  {
	    //alert('here'); 
	    op_shipping_div = d;
		
		return;
	  }
   }
   }
  d = document.getElementById('ajaxshipping'); 
  op_shipping_div = d; 
  
  // update time
  bindDatePickers(); 
  
  
  
  return;
   
}


function bindDatePickers() {
	var el1 = document.getElementById('pickup_text'); 
			init_pickup_datepicker(el1, false); 
			
			var el2 = document.getElementById('free_date_text'); 
			init_delivery_datepicker(el2, false); 
			
		   jQuery(el1).focusin( function() {
			   init_pickup_datepicker(el1, true); 	
		   }); 
		   jQuery(el2).focusin( function() {
				init_delivery_datepicker(el2, true); 
			
		   });	
	
	
}

function pf_checkbox(active, inactive)
{
 //try
 
 
 {
  f1t = document.getElementById('free_date_text'); 
	  if (f1t != null)
	   {
	    f1t.className = f1t.className.split('invalid').join('');   
	   }

  // \'pickup\', \'orfree\'
  if (active == 'pickup')
   {
     d = document.getElementById('pickup_shipping'); 
	 document.getElementById('free_or_pickup_selector').value='pickup'; 
	 if (d != null)
	  {
	   //d.click(); 
	   d.checked = true;
	   //if (d.checked)
	    {
			      x1 = document.getElementById('pickup_checkbox')
				  if (x1 != null)
				  x1.className = x1.className.split('button_checkbox_uned').join('button_checkbox_ed');
			      x1 = document.getElementById('free_checkbox');
				  if (x1 != null)
				  x1.className = x1.className.split('button_checkbox_ed').join('button_checkbox_uned');
				 
				 idsa2 = document.getElementById('idsa');
				 if (idsa2 != null)
				  {
				    idsa2.style.display = 'none'; 
				  }
				 other = document.getElementById('new_shipping_msg');
				 if (other != null)
				  {
				   other.innerHTML = ''; 
				  }
				  
				  d = document.getElementById('d_item1'); 
				  if (d!=null)				  d.className = d.className.split('isselected').join('inactive'); 
				  //d.style.color = '#f1f1f2'; 				  
				  d = document.getElementById('d_item2'); 
				  if (d!=null)				  d.className = d.className.split('isselected').join('inactive'); 
				  //d.style.color = '#f1f1f2'; 
				  
				  
				  d = document.getElementById('d_item1'); 
				  if (d!=null)				  d.className = d.className.split('isselected').join('inactive'); 
				  //d.style.color = '#f1f1f2'; 
				  d = document.getElementById('d_item2'); 
				  if (d!=null)				  d.className = d.className.split('isselected').join('inactive'); 
				  //d.style.color = '#f1f1f2'; 
				  d = document.getElementById('pickup_text'); 
				  if (d!=null)
				  {
				  d.disabled = false; 
				  d.className = 'opcdatepicker2 isselected2'; 				  
				  }
				  d = document.getElementById('pickup_time'); 
				  if (d!=null)
				  {
				  d.disabled = false; 				  //d.style.color = '#6C6E70'; 	
				  //d.style.borderColor = '#6C6E70'; 				  d.className = 'isselected2'; 
				  }
				  
				  d = document.getElementById('p_item1'); 
				  if (d!=null)				  d.className = d.className.split('inactive').join('isselected'); 
				  //d.style.color = '#6C6E70'; 
				  d = document.getElementById('p_item2'); 
				  if (d!=null)				  d.className = d.className.split('inactive').join('isselected'); 
				  //d.style.color = '#6C6E70'; 
				  d = document.getElementById('free_date_text'); 
				  if (d!=null)
				  {
				  
				  d.disabled = true; 
				  //d.style.color = '#f1f1f2'; 
				  //d.style.borderColor = '#f1f1f2'; 	
				  d.className = 'opcdatepickerdisabled inactive2'; 
				  }				  				  
				  
				  d = document.getElementById('r_item');				  
				  if (d!=null)				   
				  {				      
				  d.className = d.className.split('isselected').join('inactive'); 				   
				  }				  				  
				  d = document.getElementById('free_route'); 				 				  
				  if (d!=null)				  
				  {				  
				  d.className = 'inactive2'; 				  
				  d.disabled = true; 				 				 				  
				  }
				  
				  d = document.getElementById('free_time'); 				 
				  if (d!=null)
				  {				  
				  d.className = 'inactive2'; 
				  d.disabled = true; 
				  //d.style.color = '#f1f1f2'; 	
				  //d.style.borderColor = '#f1f1f2'; 					  				 
				  }
				  
				  
				  
				  

		}
		
	  }
	  
	  d = document.getElementById('pickup_text'); 
		   if ((typeof d != 'undefined') && (d != null))
		    {
			  d.setAttribute('required', 'required'); 
			  d.setAttribute('aria-required', 'true'); 
			  if (d.className.indexOf('opcrequired')>=0) d.className = d.className.split('opcrequired').join(''); 
			  if (d.className.indexOf('required')<0) d.className += ' required isselected2'; 
			  
			  
			}
		 d = document.getElementById('pickup_time'); 
		   if ((typeof d != 'undefined') && (d != null))
		    {
			  d.setAttribute('required', 'required'); 
			  d.setAttribute('aria-required', 'true'); 
			  if (d.className.indexOf('opcrequired')>=0) d.className = d.className.split('opcrequired').join(''); 
			  if (d.className.indexOf('required')<0) d.className += ' required isselected2'; 
			  
			  
			}
	  
	  // hide ship to address fields
	  d2 = document.getElementById('ship_to_wrapper'); 
	  if ((typeof d2 != 'undefined') &&  (d2 != null))
	   {
	     d2.style.display = 'none'; 
	   }
	  d3 = document.getElementById('sachone'); 
	  if ((typeof d3 != 'undefined') &&  (d3 != null))
	   d3.checked = false; 

	  
     
   } else
   {
     document.getElementById('free_or_pickup_selector').value='free'; 
     d = document.getElementById('pickup_shipping'); 
	 if (d != null)
	  {
	   //d.click(); 
	   d.checked = true;

		  document.getElementById('pickup_checkbox').className = document.getElementById('pickup_checkbox').className.split('button_checkbox_ed').join('button_checkbox_uned'); 			
	      document.getElementById('free_checkbox').className = document.getElementById('pickup_checkbox').className.split('button_checkbox_uned').join('button_checkbox_ed'); 	
		
	  }
	  // show ship to address fields
	  d2 = document.getElementById('ship_to_wrapper'); 
	  if ((typeof d2 != 'undefined') &&  (d2 != null))
	   {
	     d2.style.display = 'inline-block'; 
	   }
	   
	   
	    d = document.getElementById('free_date_text'); 
		   if ((typeof d != 'undefined') && (d != null))
		    {
			  d.setAttribute('required', 'required'); 
			  d.setAttribute('aria-required', 'true'); 
			  if (d.className.indexOf('opcrequired')>=0) d.className = d.className.split('opcrequired').join(''); 
			  if (d.className.indexOf('required')<0) d.className += ' required isselected2'; 
			  
			  
			}
		 d = document.getElementById('free_time'); 
		   if ((typeof d != 'undefined') && (d != null))
		    {
			  d.setAttribute('required', 'required'); 
			  d.setAttribute('aria-required', 'true'); 
			  if (d.className.indexOf('opcrequired')>=0) d.className = d.className.split('opcrequired').join(''); 
			  if (d.className.indexOf('required')<0) d.className += ' required isselected2'; 
			  
			  
			}
			
				  d = document.getElementById('p_item1'); 
				  if (d!=null)				  d.className = d.className.split('isselected').join('inactive'); 
				  //d.style.color = '#f1f1f2'; 
				  d = document.getElementById('p_item2'); 
				  if (d!=null)				  d.className = d.className.split('isselected').join('inactive'); 
				  //d.style.color = '#f1f1f2'; 
				  d = document.getElementById('pickup_text'); 
				  if (d!=null)
				  {
				  d.disabled = true; 
				  //d.style.color = '#f1f1f2'; 
				  //d.style.borderColor = '#f1f1f2'; 
				   d.className = 'opcdatepickerdisabled inactive2'; 
				  }
				  d = document.getElementById('pickup_time'); 
				  if (d!=null)
				  {
				  d.disabled = true; 				  d.className = 'inactive2'; 
				  //d.style.color = '#f1f1f2'; 	
				  //d.style.borderColor = '#f1f1f2'; 
				  }
				  
				  d = document.getElementById('d_item1'); 
				  if (d!=null)				   d.className = d.className.split('inactive').join('isselected'); 
				  //d.style.color = '#6C6E70'; 
				  d = document.getElementById('d_item2'); 
				  if (d!=null)				  d.className = d.className.split('inactive').join('isselected'); 
				  //d.style.color = '#6C6E70'; 
				  d = document.getElementById('free_date_text'); 
				  if (d!=null)
				  {
				  d.disabled = false; 
				  //d.style.color = '#6C6E70'; 
				  //d.style.borderColor = '#6C6E70'; 
				  	d.className = 'opcdatepicker2 isselected2'; 			
				  }
				  d = document.getElementById('free_time'); 
				  if (d!=null)
				  {
				  d.disabled = false; 
				  //d.style.borderColor = '#6C6E70'; 
				  //d.style.color = '#6C6E70'; 
					  d.className = 'isselected2'; 			
				  }				  				  				   
				  d = document.getElementById('r_item');				  
				  if (d!=null)				   
				  {				      
				  d.className = d.className.split('inactive').join('isselected'); 				   
				  }				  				  
				  d = document.getElementById('free_route'); 				 				  
				  if (d!=null)				  
				  {				 				  
				  d.disabled = false; 				  
				  d.className = 'isselected2 required'; 				 				  
				  }	
				  

		
		updateTime(); 
	 
   }
      selectShippingMethod(); 
   }
   
   Onepage.changeTextOnePage(); 
   
   //catch(e)
   {
     //alert(e); 
    return false; 
   }
   // don't do any sort of submission on button click
   return false; 
}

function updateTimeRVS(el)
{
  
}

function initDatePicker(el, shift, altDB) {
	if (pf_debug) console.log('init general: ', el.attr('id')); 
	el.datepicker({
				    minDate:shift,
					changeMonth: true,
					changeYear: true,
					beforeShowDay: beforeShowDayPF,
					onSelect: onSelectedDate,
					dateFormat:"d MM yy",
					altField: altDB,
					altFormat: "yy-mm-dd",
					setDate: (new Date())
					
				});
}
function init_delivery_datepicker(el, show) {
	
	if (pf_debug) console.log('inicializing delivery datepicker'); 	
	
			var shift = 0; 
			var d = document.getElementById('free_shift'); 
			if (d != null)
			shift = d.value; 
				
				shift = parseInt(shift); 
				if (shift == 'NaN') shift = 0; 
				
			var jel	= jQuery(el); 
			if (!jel.data('datepicker_loaded'))	 {
			  var altDB = jQuery('#free_date'); 
				
			  initDatePicker(jel, shift, altDB); 
			  jel.data('datepicker_loaded', true);
			}
			jel.datepicker('refresh'); 
			if (show) {
			 jel.datepicker('show'); 
			}
		
}

function init_pickup_datepicker(el, show) {
	
if (pf_debug) console.log('inicializing pickup datepicker'); 	
		    var shift = 0; 
			var d = document.getElementById('pick_shift'); 
			if (d != null)
			shift = d.value; 
				
				shift = parseInt(shift); 
				if (shift == 'NaN') shift = 0; 
				
				var jel	= jQuery(el); 
				if (!jel.data('datepicker_loaded'))	 {
				var altDB = jQuery('#pickup'); 
				initDatePicker(jel, shift, altDB); 
				jel.data('datepicker_loaded', true);
				}
				jel.datepicker('refresh'); 
				if (show) {
				jel.datepicker('show'); 
				}

				
}

function updateTime()
{
  
  if (typeof custom_slots !== 'undefined') {
  
  var pe2 = document.getElementById('pickup_text'); 
  init_pickup_datepicker(pe2, false);  
  var dateX = jQuery( '#pickup_text' ).datepicker( "getDate" );
  
  var pe = document.getElementById('free_date_text'); 
  init_delivery_datepicker(pe, false); 
  
  var date = jQuery( '#free_date_text' ).datepicker( "getDate" );

  
  return getSlotsRVS()
  return; 
   //march 2017, no ajax... return getSlotsRVS(); 
  }
  
  //var route_id = el.options[el.selectedIndex].value; 
   var f1t = document.getElementById('free_time'); 
	  if (f1t != null)
	   {
	    f1t.className = f1t.className.split('invalid').join('');   
	   }
  
   if (custom_slots)
				  {
					op_replace_select2('free_time', 'hidden_free_time'); 
				  }
  deleteTime2(); 
  

}
function selectShippingMethod(select)
{

   var selecte = document.getElementById('free_or_pickup_selector'); 
   if (selecte != null)
   select = selecte.value; 
   else return; 
   
   if (select == 'free')
    {
	  var d = document.getElementById('free_shipping_method'); 
	  if (d != null)
	  jQuery(d).click(); 
	}
	else
	{
	  var d = document.getElementById('pickup_shipping'); 
	  if (d != null)
	  jQuery(d).click(); 
	
	}
}
// let define OPC triggerers
function deleteTime2(text, obj)
{
  
  var free_time = document.getElementById('free_time'); 
  if (!(free_time!=null)) return;
  var d = document.getElementById('free_date');  
  var val = d.value; 
  
  var route = document.getElementById('free_route'); 
  if (route != null)
  var route_id = route.options[route.selectedIndex].value; 
  else
  var route_id = ''; 
 
 
 Onepage.op_log('current date: ', val); 
 Onepage.op_log('disabled_times[val]', disabled_times[val]); 
  if (typeof disabled_times != 'undefined')
			  if (disabled_times[val] != null)
			   {
			      // first round: 
			      for (var i=0; i<free_time.options.length; i++)
				  {
				     free_time.options[i].setAttribute('del', 0); 
				  }
				  
				      //Onepage.op_log('free_time: ', free_time.options[free_time.selectedIndex].value); 
					  
					  //Onepage.op_log('free and route: '+free_time.options[free_time.selectedIndex].value+'_'+route_id ); 
				 
			     //ok, we have a disabled time, today
				 // second round
				 var changed = false; 
				 var disabled_options = 0; 
				 for (var j=0; j<disabled_times[val].length; j++)
				 for (var i=0; i<free_time.options.length; i++)
				  {
				      if (typeof free_disable_min == 'undefined') free_disable_min = 1; 
					  
					  
					  // Onepage.op_log(disabled_times[val][j], free_time.options[i].value); 
					  if ((free_time.options[i].value == disabled_times[val][j]) || ((free_time.options[i].value+'_'+route_id == disabled_times[val][j])))
					  {
					    //Onepage.op_log(free_time.options[i].value); 
						free_time.options[i].setAttribute('del', 1); 
						changed = true; 		
						Onepage.op_log('free and route match found: '+free_time.options[i].value+'_'+route_id ); 
						disabled_options++; 
						//free_time.options[i].remove(); 
					  }
					  else
					  if (!custom_slots) 
					  {
					    
					    //current check on 12:00
						var ar =  free_time.options[i].value.split(':'); 
						// disabled time 12:00
						var ar2 = disabled_times[val][j].split(':'); 
						
						//t1 = 600, current check
						var t1 = parseFloat(ar[0])*60+parseFloat(ar[1]); 
						//t2 = 600
						var t2 = parseFloat(ar2[0])*60+parseFloat(ar2[1]); 
					    // free disable min = 16
					  
					  // we should disable t1 = 615
					  // (615 > 616) || (615 < (600 - 16))
					  if (((t1 > (t2 + free_disable_min)) || (t1 < (t2 - free_disable_min))))
						 {
						  // do nothing now...
						  //Onepage.op_log(pickup_local[i], disabled_times[val][j], t1, t2, free_disable_min) 
						  
						 }
						 else
						 {
						  //Onepage.op_log('del');
						  Onepage.op_log(free_time.options[i].value); 
						  free_time.options[i].setAttribute('del', 1); 
						  //free_time.options[i].remove(); 
						  changed = true; 		
						 }
					  }
				  }
				  if (!changed) 
				  {
				  return false; 
				  }
				  
				  if (disabled_options == free_time.options.length)
				  {
				    op_replace_select2('free_time', 'no_options'); 
					return; 
				  }
				  // third round: 
				   // first round: 
				   var disabled_state = free_time.disabled; 
				   free_time.disabled = false; 
				   var len = free_time.options.length-1; 
			      for (var i=len; i>=0; i--)
				  {
				     var a = free_time.options[i].getAttribute('del', 0); 
					 if (a == 1) 
					  {
					  if (typeof free_time.options[i].style != 'undefined')
					  if (typeof free_time.options[i].style.display != 'undefined')
					   free_time.options[i].style.display = 'none'; 
					   
					   if (typeof free_time.remove != 'undefined')
					   free_time.remove(i); 
					   else
					   if (typeof free_time.options[i].remove != 'undefined')
					   free_time.options[i].remove(); 
					   
					   continue; 
					  }
					  else
					  {
					    if (typeof free_time.options[i].style != 'undefined')
					    if (typeof free_time.options[i].style.display != 'undefined')
					    free_time.options[i].style.display = ''; 
						
						continue; 
					  }
				  }
				  free_time.disabled = disabled_state; 
				  return true; 
			   }
}
function deleteTime(text, obj)
		{
		
		    var populate = true; 
			  var d = document.getElementById('free_date'); 
			  var val = d.value; 
			  if (typeof disabled_times != 'undefined')
			  if (disabled_times[val] != null)
			   {
			   
			   
					//Onepage.op_log('here'); 
					//free_disable_min			     
					//pickup_times
					//var free_disable_min = parseFloat(free_disable_min); 
					var d = document.getElementById('free_time'); 
					if (!(d!=null)) return;
					/*
					for(i=d.options.length-1;i>=0;i--)
					 {
					  //d.remove(i); 
					 }
					 */
					var pickup_local = pickup_times.slice(); 
					for (var j=0; j<disabled_times[val].length; j++)
					for (var i=0; i<pickup_local.length; i++)
					 {
					  if (typeof free_disable_min == 'undefined') free_disable_min = 1; 
						 //11:00 pickup_local[i];
						 //11:15 disabled_times[val][j] 
						 ar =  pickup_local[i].split(':'); 
						 ar2 = disabled_times[val][j].split(':'); 
						 t1 = parseFloat(ar[0])*60+parseFloat(ar[1]); 
						 
						 t2 = parseFloat(ar2[0])*60+parseFloat(ar2[1]); 
						 
						 // 6615 > (6600 + 15)
						 // 6615 < (6600 - 15)
						 if ((t1 > (t2 + free_disable_min)) || (t1 < (t2 - free_disable_min)))
						 {
						  // do nothing now...
						  //Onepage.op_log(pickup_local[i], disabled_times[val][j], t1, t2, free_disable_min) 
						 }
						 else
						 {
						  //Onepage.op_log('del'); 
						  pickup_local[i] = 'del'; 
						 }
						 
						 
					 }
					
					 for (var i=0; i<pickup_local.length; i++)
					  {
					     var pos = hasOption(d, pickup_local[i]); 
						 if (pos != null)
						 if (pickup_local[i] == 'del')
						  {
						    d.remove(pos); 
						  }
						  /*
					     if (pickup_local[i]!='del')
						 {
					      var opt = document.createElement('option');
						  opt.text = pickup_local[i];
						  opt.value = pickup_local[i];
						  d.options.add(opt);
						 }
						 */
						 populate = false; 
						 
					  }
					// Onepage.op_log(pickup_local); 
			   }
			   /*
			    if (false)
			    {
				
				   d = document.getElementById('free_time'); 
				  if (populate)
				  if (d.length != pickup_times.length)
				   {
				     for(i=d.options.length-1;i>=0;i--)
					 {
					  d.remove(i); 
					 }
					 for(i=0; i<pickup_times.length;i++)
					  {
					     var opt = document.createElement('option');
						  opt.text = pickup_times[i];
						  opt.value = pickup_times[i];
						  d.options.add(opt);
					  }
				   }
				}
				*/
		}
		
		
		function beforeShowDayPF(date, objDP){
		 
		  if ((this.id == 'free_date_text') && (pf_mode == 0)) return getVRS(date, objDP); 
		  if (this.id == 'pickup_text')
		  {
		    var shift = 0; 
			var d = document.getElementById('pick_shift'); 
			if (d != null)
			shift = d.value; 
		  }
		  else
		  {
			var shift = 0; 
			var d = document.getElementById('free_shift'); 
			if (d != null)
			shift = d.value; 
		  }
		  
		  
		  if (shift > 0)
		   {
		       if (isToday(date, this, objDP))
			   {
			     return [false, ''];
			   }
			   
			   

		   }
		   
          var day = date.getDay();
		  if (this.id == 'pickup_text')
		  {
			  
		     if (typeof pickup_custom_slots != 'undefined') {
				 return checkDisabledPickup(day); 
			 }
			 
			 for (var i=0; i<disabled_days_pickup.length; i++)
			{
		     if (day ==  disabled_days_pickup[i])
			 return  [false, ''];
			}
		  }
		  else
		  for (var i=0; i<disabled_days.length; i++)
		   {
		     if (day ==  disabled_days[i])
			 return  [false, ''];
		   }
		  
                      return [true, ''];
		}; 
		
		
		function checkDisabledPickup(day) {
			 if (day === 0) day = 7;  
			 if (typeof pickup_custom_slots[day] == 'undefined') 
				 return  [false, ''];
			 else 
				 return [true, ''];
			 
			}
		
		
		function adjustPickupSlots(obj) {
			 date = Date(); 
			 var currentDate = jQuery( '#'+obj.id ).datepicker( "getDate" );
			 
			 var day = currentDate.getDay();
			 if (day === 0) day = 7; 
			 
			var html = '<select>'; 
			var d = jQuery('#pickup_time'); 
			
			if (d.length > 0) {
				d.empty();
				if (typeof pickup_custom_slots[day] === 'undefined') return; 
				
				for (var prop in pickup_custom_slots[day]) {
				if (pickup_custom_slots[day].hasOwnProperty(prop)) {
						//html += '<option value="'+prop+'">'+obj[prop]+'</option>'; 
						d.append( new Option(pickup_custom_slots[day][prop],prop,false,false) );
				  } 
				}
				html += '</select>'; 
				//var ns = jQuery(html); 
			}
		}
		
		//this function is called once the specific date is clicked
		function onSelectedDate(text, obj)
		 {
		   
		   if (pf_mode === 0) {
			   if ((obj.id.indexOf('pickup')>=0) && (typeof pickup_custom_slots !== 'undefined')) 
			   return adjustPickupSlots(obj);  
			   else
			   if ((obj.id.indexOf('free')>=0))
			   return getSlotsRVS(text, obj); 
		   }
		   
		   if ((pf_mode !== 0) && (obj.id == 'free_date_text'))
			{
			//free_date
				
				
			  if (isToday(text, this, obj))
			  {
			    op_replace_select2('free_time', 'today_free_time'); 
				
			  }
			  else
			  {
			  op_replace_select2('free_time', 'hidden_free_time'); 
			  
			 
			  }
			  
			  
			if (obj.id == 'free_date_text')
			{
			  deleteTime2(text, obj); 
			 
			}
			}
			else
			if (obj.id == 'pickup_text')
			{
			
			
			
			if (isToday(text, this, obj))
			  {
			    op_replace_select2('pickup_time', 'today_pickup_time'); 
				
			  }
			  else
			  {
			 
			  op_replace_select2('pickup_time', 'hidden_pickup_time'); 
			 
			  }
			}

		   
		   
		   
		 }
		
		


function isToday(dateText, obj, obj2)
{
  
  var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()).getTime();
  if (typeof obj2 == 'undefined')
  {
    // when called from sunday code
	if (typeof dateText == 'undefined') return; 
    selected = dateText.getTime();
  }
  else
  {
   var selected = new Date(obj2.currentYear,obj2.currentMonth, obj2.currentDay ).getTime();
  }
  if (today > selected) {
  return 'invalid'; 
  }
  else if (today < selected) return false; 
  else
	{  
	  return true; 
	}
}




function op_replace_select2(dest, src)
{
  destel = document.getElementById(dest);
  if (destel != null)
  {
  destel.options.length = 0;
  srcel = document.getElementById(src); 
  if (srcel != null)
  {
  if (srcel.options.length == destel.options.length) return;
  
  for (var i=0; i<srcel.options.length; i++)
   {
     var oOption = document.createElement("OPTION");
     //o = new Option(srcel.options[i].value, srcel.options[i].text); 
	 oOption.value = srcel.options[i].value; 
	 oOption.text = srcel.options[i].text;
     destel.options.add(oOption);
   }
   }
   else
   {
     Onepage.op_log(src); 
     var oOption = document.createElement("OPTION");
     //o = new Option(srcel.options[i].value, srcel.options[i].text); 
	 oOption.value = ''; 
	 oOption.text = ' - ';
     destel.options.add(oOption);
    
   }
   }
}


function hasOption(obj, value)
		{
		   for(var i=obj.options.length-1;i>=0;i--)
		   {
		      if (obj.options[i].value == 'value')
			   return i; 
		   }
		   return null; 
		}

/* DEPRECATED 
function getRoutesRVS(text, obj2)
{
  var date = new Date(obj2.currentYear,obj2.currentMonth, obj2.currentDay );
  var ymd = (date.getFullYear())+'-'+(date.getMonth()+1)+'-'+(date.getDate()); 
  if (typeof data_rvs['data'][ymd] != 'undefined')
  if (data_rvs['data'][ymd] != null)
  if (data_rvs['data'][ymd] == '') return '';
		   else 
		   {
		      //[$v['route'].'_'.$v['slot'].'_'.$v['vehicle']] = $v['vehicle']; 
			  for (var key in data_rvs['data'][ymd]) {
			   {
		         if (pf_debug) console.log(key); 
				 var ka = key.split('_'); 
				 var route = ka[0]; 
				 var slot = ka[1]; 
				 var vehicle = ka[2]; 
				 
				 
				 op_replace_select2('free_route', 'route_name_hidden'); 
				 var d = document.getElementById('free_route'); 
				 for (var i=0; i<d.options.length; i++)
				  {
				     
				  }
				 
			   }
		   }
		   return; 
}
}
*/

function getCurrentRoute()
{
  		   var d = document.getElementById('free_route'); 
		   if (d != null)
		    {
			   var route = d.options[d.selectedIndex].value; 
			   return route; 
		    }
			return -1; 
}

function getSlotsRVS(text, obj2)
{
  if (!(data_rvs != null)) return; 
  //var date = new Date(obj2.currentYear,obj2.currentMonth, obj2.currentDay );
 
  //var date = jQuery( '#'+obj2.id ).datepicker( "getDate" );
  var delivery_datepicker = jQuery( '#free_date_text' );
  var date = delivery_datepicker.datepicker( "getDate" );
  var ymd = (date.getFullYear())+'-'+(date.getMonth()+1)+'-'+(date.getDate()); 
  var route = getCurrentRoute(); 
  
  jQuery('#free_time').empty();
  
  
  
  var found = populateSlotsRVS(route, ymd); 
  

		  //if we cannot find slots, we need to shift the date: 
		  if (!found)
		  for (var i=1; i<=31; i++) {
			  date.setDate(date.getDate() + 1);
			  ymd = getYMD(date);
			  if (populateSlotsRVS(route, ymd)) {
				  delivery_datepicker.datepicker( "setDate", date );
				  return; 
				  
			  }
		  }
		   
		   return; 
}


function getCurrentRoute() {
	var d = document.getElementById('free_route'); 
	if (d != null) {
		return d.options[d.selectedIndex].value; 
	}
}
		//this function decides if the date is available in the datepicker, the function is iterated upon each displayed day, don't do any UI updates here !
		function getVRS(date, objDP)
		{
		 
           var ymd = (date.getFullYear())+'-'+(date.getMonth()+1)+'-'+(date.getDate()); 
		   var mret = [true, '']; 
		   
		   
		   var res = hasDatas(ymd); 
		   if (res == false)
		   return [false, ''];
		   
		   if (res == true)
		   return [true, '']; 
	       
		   
		   if (typeof data_rvs !== 'undefined') {
			   var current_route = getCurrentRoute(); 
			   
			   if (typeof data_rvs['routes'][current_route] !== 'undefined') {
				   if (typeof data_rvs['routes'][current_route][ymd] !== 'undefined') {
					    if (data_rvs['routes'][current_route][ymd].length > 0) {
							 return [true, '']; 
						}							
				   }
			   }
			   //['ymd']
		   }
		   
		   return [false, ''];
		   
		   //stAn: we disabled ajax checks in March 2017
		   
			if (typeof getDateUrl == 'undefined') return; 
		  jQuery.ajax({
			url: getDateUrl+'&cd='+ymd,
			cache: false, 
			context: document.body, 
			processData: false,
			type: "GET",
		    complete: 
					function (datasRaw, textStatus) {
					
					if (!(datasRaw.readyState==4 && datasRaw.status==200)) return;
						
					var datas = parseJson(datasRaw); 
						processDatas(datas); 
						
						res = hasDatas(ymd); 
						if (res == false)
						return [false, ''];
		   
						if (res == true)
						return [true, '']; 
						
					}
					});
		  
		   return mret; 
		  //return [false, ''];
		}
		
		//$("#pickup_text").datepicker('setDate', (new Date()) );
		//$("#pickup_text").datepicker('minDate', pick_shift );
		//$("#free_date_text").datepicker('minDate', free_shift );
		function parseJson(datas)
			{
					
					var json = datas.responseText; 
					var datasJson = jQuery.parseJSON(json); 
					if (!(datasJson != null)) 
					{ 
					   //the json deserialization failed 
					   part = datas.responseText.indexOf('{"'); 
					   resp = datas.responseText.substr(part); 
					   datasJson = jQuery.parseJSON(resp); 
					}
					return datasJson; 
			}
			
			function getYMD(date) {
				return (date.getFullYear())+'-'+(date.getMonth()+1)+'-'+(date.getDate()); 
			}

function populateSlotsRVS(route, ymd) {
	var found = false; 
	  if (typeof data_rvs['routes'] != 'undefined')
  if (typeof data_rvs['routes'][route] != 'undefined')
  if (typeof data_rvs['routes'][route][ymd] != 'undefined')
		   {
		      var slots = data_rvs['routes'][route][ymd]; 
			  if (pf_debug) console.log('route', route, 'ymd', ymd, 'slots', slots); 
			  for (var slot in slots)
			  if (slots.hasOwnProperty(slot)) 
			  {
				    var slot_txt = data_rvs['slots'][slot]; 
					var slot_id = slot; 
					if (pf_debug)  console.log('filling slots', slot_txt); 
					jQuery('#free_time').append( new Option(slot_txt,slot_id,false,false) );
					found = true; 
			  }
		   
		      
		   }
		   return found; 
}
			
function processDatas(datas)
{
  if (pf_debug) console.log('processDatas', datas); 
  if (typeof data_rvs.data != 'undefined')
  {
    for (var key in datas.data)
	 {
	   if (datas.data.hasOwnProperty(key)) 
	   data_rvs.data[key] = datas.data[key]; 
	 }

if (typeof data_rvs.routes != 'undefined')
	 {
   for (var key in datas.routes)
	 {
	   if (datas.routes.hasOwnProperty(key)) 
		data_rvs.routes[key] = datas.routes[key];
	 }
	 }	 
	 
 
  }
  else {
	  //clear and set: 
	  data_rvs = datas; 
  }
  
  
  
  
}

function hasDatas(ymd)
		{
		 
		 var toReturn = -1; 
		 
		  if (!(data_rvs != null)) return false; 
		
		    if (typeof data_rvs.data != 'undefined')
		   if (typeof data_rvs.data[ymd] != 'undefined')
		   if (data_rvs.data[ymd] != null)
		   if (data_rvs.data[ymd] == '') 
		   toReturn = false; 
		   else toReturn = true; 
		   
		   if (toReturn == true)
		   {
		   var d = document.getElementById('free_route'); 
		   if (d != null)
		    {
			   var route = d.options[d.selectedIndex].value; 
			   
			  
			  if (typeof data_rvs['routes'] != 'undefined')
		      if (typeof data_rvs['routes'][route] != 'undefined')
		      if (typeof data_rvs['routes'][route][ymd] != 'undefined')
			  {
		       return true; 
			  }
		      else 
			  {
			  
			  return false; 
			  }
			}
		   }
		   
		    //console.log('hasDatas route:',route); 
			//   console.log('hasDatas route:',data_rvs['routes'][route]); 
		   
		   if (pf_debug)  console.log('ymd:',ymd, toReturn, data_rvs); 
		   //return false; 
		   return -1; 
		}
			
jQuery(document).ready( function($) {

return;
if (typeof getDateUrl == 'undefined') return; 
			  jQuery.ajax({
			async: true,
			url: getDateUrl,
			cache: true, 
			context: document.body, 
			processData: false,
			
		    complete: 
					function (datasRaw, textStatus) {
					
					if (!(datasRaw.readyState==4 && datasRaw.status==200)) return;
						
					var datas = parseJson(datasRaw); 
						
						processDatas(datas); 
						
					}
					});

		}); 			

if (typeof data_rvs == 'undefined') {		
 var data_rvs = []; 				
}


jQuery(document).ready( function() {
		   
		   
		  updateTime(); 
		  
		  
		  var pickups = document.getElementById("pickup_shipping"); 
		  if (!(pickups != null)) return; 
		  
		  selectShippingMethod(); 
		  var pick_shift = 0; 
	
		  bindDatePickers(); 
		

		
		if (typeof pickup_custom_slots !== 'undefined') return; 
		
		var d = document.getElementById('free_date_text'); 
		if (d != null)
		{
		 text = d.value; 
		 deleteTime2(text, d); 
		 return; 
		}
		
		
		if (custom_slots)
		 {
		   Onepage.op_log('here delete'); 
		   //updateTime(); 
		 }
		
		
		});



