/**
* @package mod_vm_ajax_search
*
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* VM Live Product Search is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
*
* author: www.rupostel.com
*/

function search_vm_ajax_live_delayed(el, prods, lang, myid, url, order_by)
{
  
  
  searchel = el; 
  my_last_myid = myid; 
  
  var str = ajop_escape(el.value, myid); 
  
  
  
	  
  if (search_timer[myid] != null)
  {
   clearTimeout(search_timer[myid]); 
   
   if (str.length === 0) {
   hide_results_live(myid, true);
   hideLoader(myid); 
   adjustClearLink(); 
   return true; 
   }
  }
  
  startLoader(myid); 
  
  var optional_search = 0; 
  var d =  document.getElementById('optional_search'+myid);
  if (d != null)
  if ((d.checked) || (d.type == 'hidden'))
  {
  var optional_search = d.value; 
  }
  
  optional_search = parseInt(optional_search); 
  //if (typeof window.ajax_options == 'undefined') var ajax_options = ''; 
  if (typeof op_min_chars != 'undefined')
	  if (op_min_chars != 0)
       if (str.length < op_min_chars) {
		 hideLoader(myid); 
	     return true; 
	   }
  
  var vm_cat_id = 0; 
  var dc = document.getElementById('vm_cat_id'+myid); 
  if (dc != null)
  {
	  if (typeof dc.value != 'undefined')
	  vm_cat_id = dc.value; 
      else
	  if (typeof dc.options != 'undefined')
	  {
		  vm_cat_id = dc.options[dc.selectedIndex].value; 
	  }
  }
  vm_cat_id = '&vm_cat_id='+vm_cat_id; 
  
  var query = ajax_options+"&keyword="+str+"&prods="+prods+"&lang="+lang+"&myid="+myid+"&search_desc="+optional_search+'&order_by='+order_by+vm_cat_id;
  
  if (query == op_last_request)
   {
	
	var res = createResultDiv(myid); 
	
	if (res.style.display == 'none')
	 {
	   
	   if ((typeof jQuery != 'undefined') && (typeof jQuery.fx != 'undefined'))
	   jQuery(res).fadeIn(700, function() { ; }); 
	   else 
	   res.style.display = 'block'; 

	 }
	 else {
		 if (str.length != 0)
		 res.style.display = 'block';
	 }
	 
	 adjustPos(myid, res); 
	 hideLoader(myid); 
	 return true; 
   }
  else
  {
  op_last_request = query; 
   

  }
  
  

  if (prods == null) prods = 5;
  if (str.length==0)
  {
   hide_results_live(myid, true);
   return;
  }
  

 
  
  
  
  
  if ((el.className.indexOf('inactive_search')<0)
   && (el.className.indexOf('active_search')<0))
  {
	el.className += ' active_search'; 
  }
  
  
  
  el.className = el.className.split('inactive_search').join('active_search'); 
  if (typeof jQuery == 'undefined')
  {
  if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
	else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }

  // "/modules/mod_vm_ajax_search/ajax/index.php"
xmlhttp.open("GET", url, true);
xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
xmlhttp.onreadystatechange=resultResponse; 
xmlhttp.send(query);
   }
   else
   {
        var retObj = false; 
		var result = null; 
	    var ret = jQuery.ajax({
				type: "GET",
				url: url,
				data: query,
				cache: false,
				async: true,
				complete: function(datasRaw)
				  {
				    if (!(datasRaw.readyState==4 && datasRaw.status==200)) return;
					retObj = datasRaw; 
					if (datasRaw.readyState==4 && datasRaw.status==200) result = resultResponse(datasRaw); 
					return result; 
				  }
				
				
				});
   }
   
   if (typeof dataLayer != 'undefined') {
      dataLayer.push({'keyword': el.value, 'event': 'ajaxSearch'}); 
	  if (typeof console != 'undefined') {
	     console.log(dataLayer); 
	  }
   }
   

}
function submitSearch(el, myid, ids) {
	
  if (search_timer[myid] != null)
  {
   clearTimeout(search_timer[myid]); 
  }
  if (ids != '') {
	  var dd = document.getElementById(ids); 
	  if (dd.value === '') return; 
  }

  if (typeof el.form != 'undefined') 
  { 
     el.form.submit(); 
  }
  
  
}
var resultsbinded = false; 
function adjustPos(myid, res)
{
	
	if (op_componentID === '')
	{
    
	var el = document.getElementById('results_position_'+myid); 
	/*
	if (isFixed(el))
	{
		var position = jQuery(el).offset();
		
		res.style.left = position.left+'px'; 
		res.style.top = position.top+'px'; 
		res.style.position = 'fixed'; 
		return;
	    if (!resultsbinded)
		{
		jQuery(window).scroll(function () {
		   var position = jQuery('#results_position_'+myid).offset();
		   
		   res.style.left = position.left+'px'; 
		   res.style.top = position.top+'px'; 
		   res.position = 'fixed'; 
		   return;
		 }); 
		}
	}
	*/
	x=getX(el); 
	y=getY(el); 
	
	if (typeof console != 'undefined')
		if (console != null)
			if (typeof console.log != 'undefined')
				if (console.log != null)
				{
					
					/*
					console.log(el); 
					h = jQuery('html'); 
					console.log(h); 
					
					console.log('detected position: ', x, y); 
					*/
				}
	
    res.style.left = x+'px'; 
	res.style.top = y+5+'px'; 
	res.position = 'absolute'; 
	}
	
}

function search_vm_ajax_live(el, prods, lang, myid, url, order_by)
{	
  if (typeof order_by == 'undefined') {
	  if (typeof op_order_by != 'undefined') order_by = op_order_by; 
	  else
	  order_by = ''; 
  }
  if (!window.globalCall != null) clearTimeout(window.globalCall)  
  window.globalCall = setTimeout(function(){ search_vm_ajax_live_delayed(el, prods, lang, myid, url, order_by); },1000);	
  
}

function resultResponse(rawData)
{
  var myid = my_last_myid; 
  if ((typeof rawData != 'undefined') && ((typeof rawData == 'XMLHttpRequest') || (typeof rawData.readyState != 'undefined')))
	var xmlhttp2_local = rawData; 
	else
	if (typeof xmlhttp != 'undefined')
	var xmlhttp2_local = xmlhttp; 
	else return;
	
  if (xmlhttp2_local.readyState==4 && xmlhttp2_local.status==200)
    {
	 hideLoader(myid); 
	
	if (searchel != null)
	if (searchel.value == '')
	{
	hide_results_live(myid, true); 
	return; 
	}
	 
 	
	//left-column
	var wrd = 0; 
	
	if (op_componentID !== '')
	{
	
	if (op_rightID != '')
	{
	var right_area = jQuery('#'+op_rightID); 
	if (right_area.length == 0)
	 {
	   right_area = jQuery('.'+op_rightID); 
	   if (right_area.length > 0)
	   right_area = jQuery(right_area[0]); 
	 }
    }
	
	if (op_leftID != '')
	{
	var left_area = jQuery('#'+op_leftID); 
	if (left_area.length == 0)
	 {
	   left_area = jQuery('.'+op_leftID); 
	   if (left_area.length > 0)
	   left_area = jQuery(left_area[0]); 
	 }
    }
	
	
	
	var component_area = jQuery('#'+op_componentID); 
	  
	if (component_area.length == 0)
	 {
	   component_area = jQuery('.'+op_componentID); 
	   if (component_area.length > 0)
	   component_area = jQuery(component_area[0]); 
	 }
	
	var cd = component_area; 

	
	
	if (!component_area.attr('waschanged'))
	{
	 
	 storedw1 = component_area.width(); 
	 storedw = Math.floor(storedw1 - 1); 
	 
	 if (op_rightID != '')
	 {
	 storedw2 = right_area.width(); 
	 right_area.fadeOut(); 
	 }
	 else
	 storedw2 = 0; 
	 
	 if (op_leftID != '')
	 {
	 storedw3 = left_area.width(); 
	 left_area.fadeOut(); 
	 }
	 else storedw3 = 0; 
	 
	 
	 
	 
	 if (typeof op_resize_component == 'undefined')
	 op_resize_component = 1; 
	 if (op_resize_component == 1)
	 {
	  component_area.width(storedw1+storedw2+storedw3+'px'); 
	  component_area.attr('waschanged', true);
	 }
	 //varstoredfloat  = jQuery('#middle-column').css('float'); 
	 //jQuery('#middle-column').css('float', 'left'); 
	}
	
	if (savedContent == '')
	{
	  savedContent = jQuery(cd).html(); 
	}
	
	
	
	
	if (cd !=null)
	{
	 jQuery(cd).html(xmlhttp2_local.responseText); 
	 bindVM(myid); 
	}
	
	
	// end of component area 
	}
	
	
	if (op_componentID === '')
	{
    var res = createResultDiv(myid); 
    adjustPos(myid, res); 
	
	
    if (res != null)
	res.innerHTML=xmlhttp2_local.responseText;
	
	op_active_el = res;
	op_active_row = document.getElementById(res.id+'_0');
	op_active_row_n = 0;
	setActive(op_active_row, op_active_row_n); 
	
	if (res.style.display == 'none')
	 {
	   if ((typeof jQuery != 'undefined') && (typeof jQuery.fx != 'undefined'))
	   jQuery('#vm_ajax_search_results2'+myid).fadeIn(700, function() { ; }); 
	   else 
	   res.style.display = 'block'; 

	 }
	 else res.style.display = 'block'; 
	 }
	 
	 var el = document.getElementById('vm_ajax_search_search_str2'+myid); 
	 if (el != null)
	 {
	   el.className = el.className.split('active_search').join('inactive_search'); 
	 }
    }
	else {
		 if (xmlhttp2_local.readyState==4) {
			 hideLoader(myid); 
		 }
	}
}
function op_set_timeout()
{
  if (typeof op_ajax_debug != 'undefined')
  if (op_ajax_debug  == 1) return;
  search_timer[my_last_myid] = setTimeout(function(){ hide_now(my_last_myid); },1000);
}
function op_cancel_timeout(myid)
{
  clearTimeout(search_timer[myid]); 
}

var isFixedCache = null; 

function isFixed(element)
{
	
    if (typeof jQuery == 'undefined') return false; 
    if (isFixedCache != null) return isFixedCache; 
	
    var $element = jQuery(element);
    var $checkElements = $element.add($element.parents());
    var isFixed = false;
    $checkElements.each(function(){
        if (jQuery(this).css("position") === "fixed") {
            isFixed = true;
            return false;
        }
    });
	isFixedCache = isFixed; 
    return isFixed;  
}

function createResultDiv(myid)
{
   var id = "vm_ajax_search_results2"+myid; 
   var res = document.getElementById(id);
   
   if (!(res != null))
	 {
	    if (typeof jQuery != 'undefined')
		{
	    // the search result div does not exists, let's create it: 
		jQuery('body').append('<div onmouseout="op_set_timeout();" onmouseover="op_cancel_timeout('+myid+')" class="res_a_s" id="'+id+'" style="position: absolute; z-index: 999; width: '+op_results_width+'">&nbsp;</div>'); 
		res = document.getElementById(id);
		}
		else
		{
		if (document.body != null)
		{
		var div = document.createElement('div'); 
		div.setAttribute('id', id); 
		div.setAttribute('class', "res_a_s"); 
		div.setAttribute('style', "width:"+op_results_width);
		document.body.appendChild(div);
		}
		}
	 }
	 
	 return res; 
   
}

function startLoader(myid)
{
  
  jQuery('i[data-rel="vm_ajax_search_search_str2'+myid+'"]').addClass('active_search'); 
  
  adjustClearLink(); 
  
  var d = document.getElementById('vmajaxloaderpro'+myid); 
  if (!(d != null))
  prepareLoader(myid); 
  var d = document.getElementById('vmajaxloaderpro'+myid); 
  if (d != null)
  d.style.display = 'block'; 
}
function hideLoader(myid)
{
  var d = document.getElementById('vmajaxloaderpro'+myid); 
  if (d != null)
  d.style.display = 'none'; 

  jQuery('i[data-rel="vm_ajax_search_search_str2'+myid+'"]').removeClass('active_search');


}

function prepareLoader(myid)
{
  var d = document.getElementById('vmajaxloaderpro'+myid); 
  if (!(d != null))
    {
	   var target = getTargetPoint(); 
	   if (target !== null) {
	   if (typeof op_loaderimg == 'undefined') 
		   op_loaderimg = '/media/system/images/mootree_loader.gif'; 
	   jQuery(target).prepend('<div style="display: inline-block; height: 10px; width: 100%;"><div style="display: none" class="vmajaxloaderpro" id="vmajaxloaderpro'+myid+'"><img src="'+op_loaderimg+'" alt="Loading..." /></div>&nbsp;</div>'); 
	   }
	}
}
function bindVM(myid)
{
  hideLoader(myid); 
  if (typeof Virtuemart == 'undefined') return; 
  
  
  if (typeof Virtuemart.addtocart_popup === 'undefined')
  Virtuemart.addtocart_popup = 0; 

  if (typeof Virtuemart.updateDynamicUpdateListeners != 'undefined')
  Virtuemart.updateDynamicUpdateListeners();

  var target = getTargetPoint();   

  if (!(target !== null)) return; 
  
    var products = jQuery(target+" form.product"); 
	if (products.length > 0)
	{
     Virtuemart.product(products);
	}

  
			jQuery(target+" form.js-recalculate").each(function(){
				if (jQuery(this).find(".product-fields").length && !jQuery(this).find(".no-vm-bind").length) {
					var id= jQuery(this).find('input[name="virtuemart_product_id[]"]').val();
					if (id != null)  
					Virtuemart.setproducttype(jQuery(this),id);

				}
			});
}


function getRow(id)
{
  ida = id.split('_'); 
  return ida[ida.length - 1];
  
  
}

function getModuleId(id)
{
  ida = id.split('_'); 
  if (ida.length>2)
  return ida[ida.length - 3];
  else return 0; 
}

function op_hoverme(el)
{
/*
        var d = document.getElementsByName('op_ajax_results'); 
		for (var i = 0; i<d.length; i++)
		{
		  if (d[i].style.display != 'none')
		  {
		    if (op_active_row_n != getRow(d[i].id))
			{
		     op_active_el = d[i];
			 op_active_row = document.getElementById(d[i].id+'_0');
			 op_active_row_n = 0;
			}
			else
			{
			 op_active_row = document.getElementById(d[i].id+'_'+op_active_row_n);
			 if (op_active_row != null)
			 op_active_row.style.backgroundColor = 'white';
			}
			break;
		  }
		}
		if (op_active_el == null) return true;
 */
 setActive(el); 
}


// el is element of the row
function setActive(el, rown)
{
  if (rown == null)
   {
     rown = getRow(el.id); 
   }
  
 if (el == null) return;
 if (op_active_row!=null && (el != op_active_row))
 {
  // restore the original color
  //c = el.getAttribute('savedcolor');
  //if (c != null)
  //el.backgroundColor = c; 
  op_active_row.className = op_active_row.className.split(' selectedRow').join(''); 
  
  //op_active_row.style.backgroundColor = 'white';
 }
 op_active_row = el;
 
 if (rown != null)
 {  
	op_active_row_n = rown;
 }
 else
 {
 
 ida = el.id.split('_'); 
 op_active_row_n = ida[ida.length - 1];
 }

 if ((el.getAttribute('savedcolor') == null) || (el.getAttribute('savedcolor') == ''))
  el.setAttribute('savedcolor', el.style.backgroundColor); 
 
 c = el.getAttribute('savedcolor');
 
 //el.style.backgroundColor = el.savedcolor;  
 //el.focus(); 
 if (el.className.indexOf('selectedRow')<=0)
 el.className += ' selectedRow'; 
 
 op_active_row = el;

 
}



function getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop - el.scrollTop;
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
}

function hide_now(myid)
{
  // last check
  el = document.getElementById('vm_ajax_search_search_str2'+myid); 
  if (search_has_focus != null)
  if (!search_has_focus[myid])
  {
    if (typeof jQuery != 'undefined')
	{
     jQuery('#vm_ajax_search_results2'+myid).fadeOut('slow', function() { ; }); 
	}
	else
    document.getElementById('vm_ajax_search_results2'+myid).style.display = 'none'; 
  }
}

function getTarget()
{
  var component_area = jQuery('#'+op_componentID); 

	if (component_area.length == 0)
	 {
	   component_area = jQuery('.'+op_componentID); 
	   if (component_area.length > 0)
	   component_area = jQuery(component_area[0]); 
	 }
	
	var cd = component_area; 
	return cd; 
}

function getTargetPoint()
{
  
  if (!op_componentID) return null; 
  
  var component_area = jQuery('#'+op_componentID); 
  var ret = '#'+op_componentID; 
	if (component_area.length == 0)
	 {
	   component_area = jQuery('.'+op_componentID); 
	   if (component_area.length > 0)
	   {
	   component_area = jQuery(component_area[0]); 
	   ret = '.'+op_componentID; 
	   }
	 }
  return ret; 	
}

function hide_results_live(myid, hideother, now)
{ 

    if ((typeof now == 'undefined') || (!now)) now = false; 
	
	hideLoader(myid); 
	 var el = document.getElementById('vm_ajax_search_search_str2'+myid); 
	 if (el != null)
	 {
	   el.className = el.className.split('active_search').join('inactive_search'); 
	 } 

 if (hideother)
 if (savedContent.length > 0)
   {
    
	var cd = getTarget(); 
   jQuery(cd).html(savedContent);
   bindVM(myid); 
   	if (component_area.attr('waschanged'))
	{
	 
	 
	  
	 component_area.width(storedw+'px'); 
	 //jQuery('#wrapper2').width(storedw1+storedw2+storedw3+'px'); 
	 component_area.removeAttr('waschanged');
	 //jQuery('#middle-column').css('float', varstoredfloat); 
	 //jQuery('#left-column').fadeIn(); 
	 //jQuery('#right-column').fadeIn();
	 
	 if (op_rightID != '')
	{
	var right_area = jQuery('#'+op_rightID); 
	if (right_area.length == 0)
	 {
	   right_area = jQuery('.'+op_rightID); 
	   if (right_area.length > 0)
	   right_area = jQuery(right_area[0]); 
	 }
	 right_area.fadeIn(); 
    }
	
	if (op_leftID != '')
	{
	var left_area = jQuery('#'+op_leftID); 
	if (left_area.length == 0)
	 {
	   left_area = jQuery('.'+op_leftID); 
	   if (left_area.length > 0)
	   left_area = jQuery(left_area[0]); 
	 }
	 left_area.fadeIn(); 
    }
	 
	 
	}
  }
 
 if (typeof(search_timer)=='undefined')
  {
    var search_timer = new Array(); 
  }
 if (typeof(search_timer[myid])=='undefined')
  {
     search_timer[myid] = null; 
  }
  if (!now) {
	search_timer[myid] = setTimeout(function(){ hide_now(myid); },1000);
  }
  else {
	  hide_now(myid); 
  }
 return false; 
}

function search_setText(text, e, myid)
{
 if (typeof op_ajax_debug != 'undefined')
 if (op_ajax_debug == 1) return; 
 search_has_focus[myid] = false; 
 //return;
 search_timer[myid] = setTimeout(function(){ hide_now(myid); },1000);
 //setTimeout( function() { hide_results_live(myid, false); }, 1000);
 //aj_inputreset(e);
 //e.value = text;
 //hide_results_live();
 return true;
}

/* 
     Example File From "JavaScript and DHTML Cookbook"
     Published by O'Reilly & Associates
     Copyright 2003 Danny Goodman
*/
function handleArrowKeys(evt, myid) {
    evt = (evt) ? evt : ((window.event) ? event : null);
	/*
	main = document.getElementById('vm_ajax_search_results2'+myid); 
	if (main.style.diplay == 'none') return true; 
	*/
    if (evt) {
	    
		// ctrl alt F
		if (evt.ctrlKey && evt.altKey && evt.keyCode == 70)
		{
			setFocusInput(); 
			return; 
        } 		

		
        var d = document.getElementsByName('op_ajax_results'); 
		for (var i = 0; i<d.length; i++)
		{
		  
		  if (d[i].style.display != 'none')
		  {
		    if (op_active_el != d[i])
			{
		     op_active_el = d[i];
			 //op_active_row = document.getElementById(d[i].id+'_0');
			 //op_active_row_n = 0;
			 
			}
			else
			{
			 //op_active_row = document.getElementById(d[i].id+'_'+op_active_row_n);
			 //if (op_active_row != null)
			 //op_active_row.style.backgroundColor = 'white';
			}
			break;
		  }
		}
		
		
		if (op_active_el == null) return true;
		
		
		
        switch (evt.keyCode) {
            case 37:
				
				
                break;    
            case 38:
				// up
				op_active_row_n = parseInt(op_active_row_n);
				op_active_row_n = op_active_row_n - 1;
				if (op_active_row_n < 0) op_active_row_n = 0;
				myid = getModuleId(op_active_el.id); 
                el = document.getElementById('vm_ajax_search_results2'+myid+'_'+op_active_row_n);
				
				setActive(el, op_active_row_n);
                return false;
            case 39:
				// right
                //alert('39'); 
                return false;
            case 40:
				// down
				op_active_row_n = parseInt(op_active_row_n);
				op_active_row_n = parseInt(op_active_row_n) + 1;
				if (parseInt(op_active_row_n) > op_maxrows) op_active_row_n = op_maxrows;
				myid = getModuleId(op_active_el.id); 
                el = document.getElementById('vm_ajax_search_results2'+myid+'_'+op_active_row_n);
				setActive(el, op_active_row_n);
                return false;
			case 27:
			  // escape
			  op_active_el.style.display = 'none';
			  return false;
			case 13:
				
			    // value of the row
				myid = getModuleId(op_active_el.id); 
				rown = op_active_row_n; 
				
				
				d1 = document.getElementById('vm_ajax_search_results2_'+myid+'_value_'+op_active_row_n);
				if (d1 != null) 
				 { 
				  // current input element
				    
				  	return op_processCmd(op_process_cmd, d1.value, op_active_el.id,  op_active_row_n); 
				 
				 
				 }
				 else
				 {
				  //alert (op_active_el.id+'_value_'+op_active_row_n);
				  //alert('2:'+d2.value);
				 }
				//op_active_el.style.display = 'none';
				return false;
         }
    }
}

function op_processCmd(cmd, value, id, row)
{
  if (cmd == 'href')
   document.location = value; 
  return false; 
}
function setFocusInput()
{
/*
   var search_timer = new Array(); 
		  var search_has_focus = new Array(); 
		  var op_active_el = null;
		  var op_active_row = null;
          var op_active_row_n = parseInt("0");
		  var op_last_request = ""; 
          var op_process_cmd = "href"; 
		  var op_controller = ""; 
		  var op_lastquery = "";
		  var op_maxrows = '.$prods.'; 
		  var op_lastinputid = "vm_ajax_search_search_str2'.$myid.'";
		  var op_currentlang = "'.$clang.'";
		  var op_lastmyid = "'.$myid.'"; 
		  var op_ajaxurl = "'.$url.'";
		 */
		 d = document.getElementById(op_lastinputid); 
		 aj_inputclear(d, op_maxrows+1, op_currentlang, op_lastmyid, op_ajaxurl); 
		 d.focus(); 
}
function aj_inputclear(what, prods, lang, myid, url){
  search_has_focus[myid] = true; 
  //op_savedtext[myid] = document.getElementById('label_'+what.id).innerHTML; 
  //document.getElementById('label_'+what.id).innerHTML = ''; 
  if (what.value != '')
   {
     search_vm_ajax_live(what, prods, lang, myid, url); 
   }
}

function aj_inputreset(what)
{
 //if (what.value == '')
 //document.getElementById('label_'+what.id).innerHTML = document.getElementById('saved_'+what.id).value; 
}

function getY( oElement )
{
var iReturnValue = 0;
while( oElement != null ) {
	if (oElement.offsetTop > 0)
iReturnValue += oElement.offsetTop;
oElement = oElement.offsetParent;
}
return iReturnValue;
}

function getX( oElement )
{
var iReturnValue = 0;
while( oElement != null ) {
iReturnValue += oElement.offsetLeft;
oElement = oElement.offsetParent;
}
return iReturnValue;
} 


  function ajop_escape(str, myid)
  {
   
   //var x1 = document.getElementById('results_re_2'+myid);
   
   //if (x1 == null || (typeof x1 == 'undefined')) str = '';
   if ((typeof(str) != 'undefined') && (str != null))
   {
     x = str.split("&").join("%26");
     x = str.split(" ").join("%20");
	 
     return x;
   }
   else 
   {
   
   return "";
   }
   return "";
  }

function aj_redirect(id)
{

  x = document.getElementById(id);
  if (x!=null)
  {
    if (x.href != null)
    {
      window.location = x.href;
     
    }
    else
    {
      
    }
  }
  
}

function adjustClearLink() {
	var jc = jQuery('i.clearable'); 
			
			if (jc.length >  0) {
				jc.each (function () { 
				var jc2 = jQuery(this); 
				
				var mainID = jc2.data('rel'); 
				if (typeof mainID !== 'undefined') {
					var si = jQuery('#'+mainID); 
					if (si.val() !== '') {
						jc2.show();
					}
					else {
						jc2.hide(); 
						hideLoader(myid); 
					}
					
					var myid = mainID.split('vm_ajax_search_search_str2').join(''); 
					
				}
				else return;
				
				
				var isBinded = jc2.data('clickBinded'); 
				if (!isBinded) {
				jc2.click( function() {
				jc2.data('clickBinded', true); 
				var el = jQuery(this); 
				var mainID = el.data('rel'); 
				if (typeof mainID !== 'undefined') {
					var si = jQuery('#'+mainID); 
					if (si.length > 0) {
						si.val(''); 
						hideLoader(myid); 
						jc2.hide(); 
						
						
						hide_results_live(myid, true, true);
						
						
						si.focus(); 
						
						
					}
				}
				
				});
				}
				})
			}
			
		
}
if (typeof jQuery !== 'undefined') {
		jQuery(document).ready( function() {
			adjustClearLink(); 
		}); 
}


var savedContent = ''; 
var storedw1 = 0; var storedw2 = 0; var storedw3 = 0; var storedw = 0;  varstoredfloat = ''; 
var searchel = null; 
var my_last_myid = ''; 
var globalCall = null; 