/*
* This file sends feedback to your OPC if a tracking event was sucessfully shown
* 
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

function opc_pingDone(url, data)
{
if ((typeof t_xmlhttp2 != 'undefined') && (t_xmlhttp2 != null))
	{
	 
	}
	else
	{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
     var t_xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	 var t_xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }
	}
    if (t_xmlhttp2!=null)
    {
	 
	 t_xmlhttp2.open("POST", url, true);
     //Send the proper header information along with the request
     t_xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     t_xmlhttp2.onreadystatechange= function() { 
		if ((typeof t_xmlhttp2 != 'undefined') && (t_xmlhttp2 != null))
		{
		   
		
		}
	 } ;
     t_xmlhttp2.send(data); 
	}
}	

var opc_global_timer = null; 
var opc_global_timer_ms = 2000; 
var opc_global_timer_url = null; 
var opc_global_timer_count = 0; 
function opc_ping_status(url) {
	window.opc_global_timer_url = url; 
	window.opc_global_timer = window.setTimeout(opc_checkOrders, window.opc_global_timer_ms);
}

function opc_checkOrders() {
	if (!window.opc_global_timer_url) return; 
	if (typeof window.opc_current_order === 'undefined') return; 
	window.opc_global_timer_count++; 
	
	if (window.opc_global_timer_count > 10) {
		clearTimeout(window.opc_global_timer); 
		return; 
	}
	
	if (typeof jQuery !== 'undefined') {
		  jQuery.ajax({
				type: 'GET',
				url: opc_global_timer_url,
				data: window.opc_current_order,
				cache: false,
				complete: opc_pingstatus_resp,
				async: true
				
				});
	}
}

function opc_pingstatus_resp(jqXHR, textStatus) {
	
	
	if (jqXHR.responseText.indexOf('opc_no_changes_found')>=0) {
		//repeat the request: 
		window.opc_global_timer_ms = window.opc_global_timer_ms * 1.6;
		window.opc_global_timer = window.setTimeout(opc_checkOrders, window.opc_global_timer_ms);
		return;
	}
	if (jqXHR.responseText.indexOf('opc_order_status_changed')>=0) {
		clearTimeout(window.opc_global_timer); 
		location.reload(true);
	}
	clearTimeout(window.opc_global_timer); 
	
	
}