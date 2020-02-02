function vmProcessOrders(el) {
	if (typeof jQuery == 'undefined') {
		alert('OPC: jQuery not installed !'); 
		return false; 
	}
	var data = jQuery(el).data('tid'); 
	
	jQuery('#opctid').val(data.tid); 
	var newhtml = ''; 
	var els = document.getElementsByName('cid[]'); 
	var n = 0; 
	if (els.length > 0) {
		for (var i=0; i<els.length; i++) {
			if (els[i].checked) {
				newhtml += '<input type="hidden" value="'+els[i].value+'" name="selectedorder_'+n+'" />'; 
				n++; 
			}
		}
	}
	
	console.log(newhtml); 
	if (newhtml === '') {
		alert('No orders selected !'); 
		return false; 
	}
	jQuery('#opc_inject_here').html(newhtml); 
	opcactions.submit(); 
	return false; 
	
}

function op_runCmd(cmd, element, extradata)
{
 if ((typeof(deb) == 'undefined') || (deb == null))
  deb = document.getElementById('opc_response');
 
 document.getElementById('cmd').value = cmd; 
 
 document.getElementById('scrolly').value = getScrollY();
 document.getElementById('op_curtab').value = getCurrentTab('order_general');
 if (opStop == false)
 {
 opStop = true;
 opTimer=setTimeout("op_timer()", 5000);
 }
 
 if (cmd == 'orderstatusset')
 {
  document.getElementById('task').value = 'orderstatusset';
  
   var include_comment = ''; 
  var order_comment = ''; 
  var current_order_status = ''; 
  var notify_customer = '';
  
  var order_id = element.name.split('order_status_').join(''); 
  var d = document.getElementById('notify_customer_'+order_id); 
  if (d != null)
  {
	   var order_status = element.options[element.selectedIndex].value;
	   if (d.checked)
	   {
		   notify_customer = '1'; 
	   }
  }
  else
  {
	   if (document.getElementById('notify_customer').checked)
		notify_customer = '1';
		
		 var ic = document.getElementById('include_comment'); 
		  if (document.getElementById('include_comment').checked)
   include_comment = 'Y';
  
  order_comment = urlencode(document.getElementById('order_comment_s').value);
  current_order_status = document.getElementById('current_order_status').value;
  var order_statush = document.getElementById('order_status');
  var order_status = order_statush.options[order_statush.selectedIndex].value;
  
  order_id = document.getElementById('order_id').value; 
  }
 
  
  var q = '&notify_customer='+notify_customer+'&include_comment='+include_comment+'&order_comment='+order_comment+'&current_order_status='+current_order_status+'&order_status='+order_status+'&cmd=OrderStatusSet&order_id='+order_id+'&orderid='+order_id;
  // order_item_id
  // order_number
  //form = document.getElementById('adminFormOPC'); 
  if (false)
  if (form != null) 
   {
     form.submit(); 
   }
  op_ajax(q);
  return true;
 }
 else
 if (cmd == 'updateJoomla')
 {
   name = getValue('userinfo_name');
   username = getValue('userinfo_username');
   pwd = getValue('userinfo_password');
   pwd2 = getValue('userinfo_password2');
   email = getValue('userinfo_email');
   gid = getValue('gid');
  
   if (pwd != pwd2)
   {
    alert("Passwords don't match!");
    return false;
   }
   q = '&name='+name+'&username='+username+'&pwd='+pwd+'&pwd2='+pwd2+'&email='+email+'&gid='+gid+'&cmd='+cmd;
   op_ajax(q);
   return false;
 }
 else
 if (cmd == 'orderitemstatusset')
 {
  if (document.getElementById('notify_customer').checked)
  notify_customer = 'Y';
  else notify_customer = '';
  
  //document.getElementById('task').value = 'orderstatusset'; 
  
  
  if (document.getElementById('include_comment').checked)
  include_comment = 'Y';
  else include_comment = '';
  
  order_comment = urlencode(document.getElementById('order_comment_s').value);
  if (element != null)
  {
   arr = element.id.split('_', 2);
   if (arr.length == 2)
   {
    current_order_status = document.getElementById('itemcurrentstatus_'+arr[1]).value;
    item_id = arr[1];
    
    order_statush = document.getElementById('order_status_'+item_id);
  	order_status = order_statush.options[order_statush.selectedIndex].value;
    q = '&notify_customer='+notify_customer+'&include_comment='+include_comment+'&order_comment='+order_comment+'&current_order_status='+current_order_status+'&order_status='+order_status+'&cmd=OrderItemStatusSet&order_item_id='+item_id;
    // order_item_id
    // order_number
    op_ajax(q);

    
   }
  }
  
 }
 else
 if (cmd == 'shipchange')
 {
  new_ship = document.getElementById('ship_to_id');
  user_info = new_ship.options[new_ship.selectedIndex].value;
  q = '&change_ship_to=1&ship_to='+user_info;
  document.getElementById('task').value = 'shipchange';
  adminForm.submit();
  return false;
 }
 else
 if (cmd == 'deleteItem')
 {
  document.getElementById('task').value = 'deleteItem';
  arr = element.id.split('_', 2);
  if (arr.length == 2)
  {
  document.getElementById('general_param').value = arr[1];
  adminForm.submit();
  return false;
  }
 }
 else
 if (cmd == 'quantityupdate')
 {
  document.getElementById('task').value = 'quantityupdate';
  arr = element.id.split('_', 2);
  if (arr.length == 2)
  {
  document.getElementById('general_param').value = arr[1];
  //document.getElementById('general_param1').value = document.getElementById('productquantity_'+arr[1]).value;
  adminForm.submit();
  return false;
  }
 }
 else
 if (cmd == 'productItemPrice')
 {
  document.getElementById('task').value = 'itempriceupdate';
  arr = element.id.split('_', 2);
  if (arr.length == 2)
  {
  document.getElementById('general_param').value = arr[1];
  //document.getElementById('general_param1').value = document.getElementById('productquantity_'+arr[1]).value;
  adminForm.submit();
  return false;
  }
 }
 else
 if (cmd == 'sendXmlMulti')
 {
   //for multi orders we will do a submit so it's easier to debug: 
	
	 
   arr = element.id.split('_');
   tid = arr[1]; 
   qo = "";
   if ((typeof(extradata) != 'undefined') && (extradata != null))
   {
    ar2 = extradata.split('_');
	qo = getSelectedOrders(ar2);
   }
   else
   {
    qo = getSelectedOrders();
    opShow('mytmps');
   }
   if (qo == '') { alert("No orders selected!"); opShow(); return false; }
//   op_localid = qo;
   q = '&tid='+tid+'&cmd='+cmd+qo;
   
   //+'&localid='+op_localid;  
   
   //op_ajax(q);
   
   window.location = op_url+'?'+op_params+q; 
   return false; 
   logMsg(q, 'blue');
   tmpElement = element;
   //changeStatus('PROCESSING', element);
   
   if (opTimer == null)
   opTimer=setTimeout("op_timer()", 5000);
   
   return false;
 }
 else
 if (cmd == 'sendXml')
 {
   document.getElementById('task').value = cmd;
   arr = element.id.split('_');
   tid = arr[1]; 
   var specials = getSpecials(tid);
   q = '&tid='+tid+'&cmd='+cmd+'&localid='+op_localid+specials;  
   element = document.getElementById('tid_'+tid);
   op_ajax(q);
   changeStatus('PROCESSING', element);
   opTimer=setTimeout("op_timer()", 5000);

   return false;
 }
 else
 if (cmd == 'sendEmail')
 {
   document.getElementById('task').value = cmd;
   arr = element.id.split('_');
   tid = arr[1]; 
   var specials = getSpecials(tid);
   q = '&tid='+tid+'&cmd='+cmd+'&localid='+op_localid+specials;  
   element = document.getElementById('tid_'+tid);
   op_ajax(q);
   return false;
 }
 else
 if (cmd == 'checkFile')
 {
  document.getElementById('task').value = cmd;
  q = '&cmd='+cmd; 
  op_ajax(q);
  return false;
 }
 else
 if (cmd == 'checkFile')
 {
  document.getElementById('task').value = cmd;
  q = '&cmd='+cmd; 
  op_ajax(q);
  return false;
 }
 else
 if (cmd == 'resendconfirm')
 {
  document.getElementById('task').value = cmd;
  q = '&cmd='+cmd; 
  op_ajax(q);
  return false;
 }
 else
 if (cmd != null)
 {
  document.getElementById('task').value = cmd;
  arr = element.id.split('_', 2);
  if (arr.length == 2)
  {
   document.getElementById('general_param').value = arr[1];
   //document.getElementById('general_param1').value = document.getElementById('productquantity_'+arr[1]).value;
   adminForm.submit();
   return false;
  }
  else adminForm.submit();
  
 }
}

function isChecked(b)
{
}


function updateOrderStatus(el, order_id)
{
	var d = document.getElementById('changed_'+order_id); 
	d.value = 1; 
	op_runCmd("orderstatusset", el);
}


function getSpecials(tid)
{
 col = document.getElementsByName('specialentry_'+tid);
 var rets = '';
 if (col != null)
 {
  for (var i = 0; i<col.length; i++)
  {
   rets += '&'+col[i].id+'='+urlencode(col[i].value);
  }
 }
 return rets;
}

// optional arr 
function getSelectedOrders(arr)
{
 var query = '';
 if ((typeof(arr) != 'undefined') && (arr != null))
 var d = arr;
 else
 var d = document.getElementsByName('order_id[]');
 
 for (var i=0; i<d.length; i++)
 {
  if (((typeof(arr) != 'undefined') && (arr != null)) )
  {
   order_id = d[i];
   query += '&selectedorder_'+i+'='+order_id;
  }
  else
  {
   if (d[i].checked != null && (d[i].checked))
   {
    order_id = d[i].value;
    query += '&selectedorder_'+i+'='+order_id;
   }
  }
 }
 
 return query;
}

// response from checkFile
function parseTemplateInfo(resp)
{
 console.log(resp); 
 resp = resp.toString();
 var s2 = 0;
 var s1 = 0;
 len = 0;
 dis = 0;
 i = 0;
 
 
 for (var i=0; i<100; i++)
 {
  
  //s1 = resp.indexOf("DATAS::"+i+"::");
  s1 = resp.indexOf("DATAS::", s2);
  //alert(s1);
  if (s1 < 0) break;
  len++;
  //s1 = resp.indexOf("DATAS", s2);
  s2 = resp.indexOf("DATAE", s1);
  if (s2 < 0) break;
  //if (typeof(s1)=='undefined') break;
  //alert(s2);
  data = resp.substring(s1, s2).split("::");
  tid = data[1]; 
  //logMsg("data1: "+data[1], 'red');
  //logMsg("opT: "+opTemplates[data[1]], 'green');
  //logMsg("status: "+data[3], 'yellow');
  // data[0]: datas, data[1]: tid, data[2]: link, data[3]: status  data[4]: eid data[5]: tablerow
  
  if (typeof console != 'undefined')
  console.log(data); 
  
  if (multiOrders != true)
  {
  var d2 = document.getElementById('tidpdf_'+data[1]);
  if (d2 != null)
  {
  changeStatus(data[3], tid);
  
  
  
 // alert('multioff');
  if (data[3] == 'CREATED')
  {
   //d.innerHTML = created_html;
  
   d2.href = urldecode(data[2]);
   /*
   d.onclick = null;
   d.target = "_blank";
   */
   //opTemplates[data[1]] = null;
   dis++;
   //logMsg('Added link'+data[2], 'blue');
 	}
 	}
  }
  else
  {
	
   d = document.getElementById('eid_'+data[5]);
   if (d != null)
   {
    d.innerHTML = data[6];
    
   }
   else
   {
    d = document.getElementById('invisible_row');
	
	var newItem = document.createElement("div");
    //d.innerHTML += '</tr><tr id="eid_'+data[5]+'" '+data[6];
    newItem.innerHTML = '<div id="eid_'+data[5]+'" >'+data[6]+'</div><br style="clear: both;"/>'+d.innerHTML;
	if (d.nextSibling) {
	  d.parentNode.insertBefore(newItem, d.nextSibling);
	}
	else
	{
		d.parentNode.appendChild(newItem);
	}
    //alert('row added');
   // alert('som tu'+data[5]); 
    //row.innerHTML = data[6];
   }
  }
 	
   }
 
 if (dis==len && dis>0)
 {
  // disable timer
  //opStop = true;
  //opTemplates = [];
  //clearTimeout(opTimer);
 }
}
 
function changeStatus(status, element)
{
 if ((typeof(deb) == 'undefined') || (deb == null))
 deb = document.getElementById('opc_response');
 
 if (isNaN(element))
 {
  data = element.id.split('_');
  tid = data[1];
 }
 else tid = element;
 element = document.getElementById('tiddiv_'+tid+'_'+status);
 if (element != null)
 {
 element.style.display = '';
 if (status == 'PROCESSING')
  {
   document.getElementById('tiddiv_'+tid+'_NONE').style.display = 'none';
   document.getElementById('tiddiv_'+tid+'_ERROR').style.display = 'none';
   document.getElementById('tiddiv_'+tid+'_CREATED').style.display = 'none';
  }
 else
 if (status == 'CREATED')
 {
   document.getElementById('tiddiv_'+tid+'_PROCESSING').style.display = 'none';
   document.getElementById('tiddiv_'+tid+'_NONE').style.display = 'none';
   document.getElementById('tiddiv_'+tid+'_ERROR').style.display = 'none';
 }
 else
 if (status == 'ERROR')
  {
   document.getElementById('tiddiv_'+tid+'_PROCESSING').style.display = 'none';
   document.getElementById('tiddiv_'+tid+'_NONE').style.display = 'none';
   document.getElementById('tiddiv_'+tid+'_CREATED').style.display = 'none';
  }
 tmpElement = element;
 opTemplates[tid] = 'tiddiv_'+tid+'_'+status;
 }

}

function op_timer()
{
  //deb.style.display = 'block';
  //logMsg('timerRunning,'+opTemplates.toString());
  //alert('som tu');
  //if (!opStop)
  opStop = true;
  {
   op_runCmd('checkFile', tmpElement);
   opTimer=setTimeout("op_timer()", 5000);
  }
}

function op_ajax(query)
{

    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	 xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    if (xmlhttp2!=null)
    {
    op_q = op_params + query;
    if (typeof(deb) == 'undefined')
	var deb = document.getElementById('opc_response');	
	
	
	if (typeof console != 'undefined')
	{
	  console.log(op_url+'?'+op_q); 
	}
	
    xmlhttp2.onreadystatechange= op_get_SS_response ;
    xmlhttp2.open("POST", op_url, true);
    //Send the proper header information along with the request
	xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//xmlhttp2.setRequestHeader("Content-length", op_q.length);
	//xmlhttp2.setRequestHeader("Connection", "close");
    
    xmlhttp2.send(op_q); 
    //deb.innerHTML += '';
    //+op_url+'?'+op_q;
    }
    else
    {
     alert('Error: Could not init Ajax !');
    }


}

function op_runAjax(element, onlyOrder, oquery)
{
 
	var id = element.id;
	var orig = 'orig_'+id;
	var orige = document.getElementById(orig);
	if (orige == null)
	 alert('NULL: '+orig);
	var val = orige.value;
	var val_new = urlencode(element.value);
	
	if (val != val_new)
	{
	
	 var q = '&fieldid='+id+'&origval='+val+'&newval='+val_new;
	 if (onlyOrder == true)
	 q += '&onlyorder=true';
	 else 
	 q += '&onlyorder=false';
	 
	 op_ajax(q);
	 orige.value = element.value;
    
    } else
    {
     alert('Error: values are identical');
     return false;
    }
}
function op_blur(element)
{
 op_runAjax(element);
}
function op_focus(element)
{
 if ((focusedE != null) && (focusedE != element)){ element.blur(); focusedE.focus(); return false; }
 
 var id = 'buttons_'+element.id;
 var x = document.getElementById(id);
 //alert(id);
 left = getX(element);
 top = getY(element);
 x.style.position = 'absolute';
 x.style.left = left+'px';
 //alert(element.type);
 if (element.type != 'select-one')
 top = top+element.clientHeight;
 else top = top + 18;
 x.style.top = top+'px';
 
 //x.style.backgroundColor = element.style.backgroundColor;
 if (element.type != 'select-one')
 x.style.width = element.clientWidth+'px';
 else x.style.width = '250px';
 
 x.style.display = '';
 focusedE = element;
 element.focus();
}
function opLight(element)
{
  if (focusedE == null)
  element.style.backgroundColor = '#B8B8B8';
} 
function op_key(element, event)
{
 if (event.keyCode == 13) 
 {
  op_update(element);
  return false;
 }
 if (event.keyCode == 27)
 {
  op_cancel(element);
  return false;
 }
 return false;
}
function opLightOut(element)
{
 if (focusedE == null)
 element.style.backgroundColor = 'white';
}
function op_update(element)
{
  if (focusedE != null)
  {
  op_runAjax(focusedE, false);
  op_cancel(element);
  }
}
function op_update_order(element)
{
if (focusedE != null)
{
 op_runAjax(focusedE, true);
 op_cancel(element);
}
}
function op_cancel(element)
{
 
 op_hide(element);
}
function op_hide(element)
{
 var id = 'buttons_'+focusedE.id;
 var x = document.getElementById(id);
 console.log('hiding ',x); 
 x.style.display = 'none';
 focusedE.style.backgroundColor = 'white';
 focusedE = null;
}
function getY( oElement )
{
var iReturnValue = 0;
while( oElement != null ) {
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

function opShow(div_id)
{
 if (typeof(div_id) == 'undefined' || (div_id==null)) div_id = 'mytmps';
 var d = document.getElementById(div_id);
 if (d!=null || (typeof(d)!='undefined'))
 {
  if (d.style.display != 'none') {
   d.style.display = 'none';
   console.log('hiding', d); 
  }
  else 
   d.style.display = '';
 }
 return false;
}

/*
* This is response function of AJAX
* Response is HTML code to be used inside noshippingheremsg DIV
*/       
function op_get_SS_response()
{
  	
  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    
    
    // here is the response from request
    var resp = xmlhttp2.responseText;
    if (resp)
    {
    		var deb = document.getElementById('opc_response');
			if (deb != null)
			deb.style.display = '';
			
	
      if (resp.toString().indexOf('DATAS')>=0)
      {
        parseTemplateInfo(resp);
        deb.innderHTML += resp; // only for debugging here
      }
      else
      {
      	
      
      
        deb.innerHTML = resp+deb.innerHTML;
      }
      
      
      // if response is ok, don't forget to chage value of old orig
    }
    }
    else
    {
     //deb.innerHTML = 'Error: '+xmlhttp2.readyState;
    }
    
    
    return true;
}

function logMsg(msg, color)
{
 if (typeof(deb) == 'undefined' || (deb == null))
  deb = document.getElementById("opc_response");
  if (!(deb != null)) return; 
  
 if (typeof(color) != 'undefined' && (color != null))
 {
  deb.innerHTML += '<span style="color:'+color+'">'+msg+'</span>';
 }
 else deb.innerHTML += msg;
}

function hide_debug() 
{ 
 return; 
 var deb = document.getElementById('opc_response');
 deb.style.display = '';
 //deb.innerHTML = '';
}
// http://phpjs.org/functions/urldecode:572
function urldecode (str) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer
    // +      input by: Ratheous
    // +   improved by: Orlando
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +      bugfixed by: Rob
    // +      input by: e-mike
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // %        note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // %        note 2: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on
    // %        note 2: pages served as UTF-8
    // *     example 1: urldecode('Kevin+van+Zonneveld%21');
    // *     returns 1: 'Kevin van Zonneveld!'
    // *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
    // *     returns 2: 'http://kevin.vanzonneveld.net/'
    // *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
    // *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
    return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}

// url encode function from http://phpjs.org/functions/urlencode:573
// urlencode should be identical to urlencode in php
function urlencode (str) {
    // URL-encodes string  
    // 
    // version: 1009.2513
    // discuss at: http://phpjs.org/functions/urlencode    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer    // +      input by: Ratheous
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Joris
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // %          note 1: This reflects PHP 5.3/6.0+ behavior    // %        note 2: Please be aware that this function expects to encode into UTF-8 encoded strings, as found on
    // %        note 2: pages served as UTF-8
    // *     example 1: urlencode('Kevin van Zonneveld!');
    // *     returns 1: 'Kevin+van+Zonneveld%21'
    // *     example 2: urlencode('http://kevin.vanzonneveld.net/');    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
    // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
    str = (str+'').toString();
        // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
    // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
                                                                   replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}

function getValue(id)
{
 var el = document.getElementById(id);
 if (typeof(el) != 'undefined' && (el != null))
 {
   
   if (el.type.toString().indexOf('select')>=0)
   {
    var index = el.selectedIndex;
    if (index != null)
    {
     if (typeof(el.options) != 'undefined' && (el.options != null))
     {
      if (el.options[index] != null)
      {
       if (el.options[index].value != null)
       {
        //alert(urlencode(el.options[index].value));
        return urlencode(el.options[index].value);
       }
      }
     }
    }
   }
   if (typeof(el.value) != 'undefined' && (el.value != null))
   {
    
     return (urlencode(el.value));
   }
 }
 
 return "";
}
// copyrights: http://www.crondesign.com/projects/downloads/scrollfix.js
// with mods of rupostel
function getScrollY() {
    var x = 0, y = 0;
    if( typeof( window.pageYOffset ) == 'number' ) {
        // Netscape
        x = window.pageXOffset;
        y = window.pageYOffset;
    } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
        // DOM
        x = document.body.scrollLeft;
        y = document.body.scrollTop;
    } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
        // IE6 standards compliant mode
        x = document.documentElement.scrollLeft;
        y = document.documentElement.scrollTop;
    }
    return y;
}
// copyrights: http://www.crondesign.com/projects/downloads/scrollfix.js
function setScrollXY(x, y) {
    window.scrollTo(x, y);
}

function getCurrentTab(paneName)
{
    return paneName; 
    var zDT=document.getElementById(paneName).getElementsByTagName('dt'); 
    if (zDT != null)
    {
    var zDD=document.getElementById(paneName).parentNode.getElementsByTagName('dd');
    if (zDD != null)
    for(var i=0;i<zDT.length;i++){
	   if (zDT[i].className=='open') 
	   {
	    ret = zDT[i].id+'___'+i;
	    
	    return ret;
	   }
    }
    }
	return false;
}

function setCurrentTab(paneName, id___index)
{
 // ___
 current = getCurrentTab(paneName);
 if (current != null)
 if (current != id___index)
 {
 arr = id___index.split('___');
 arr2 = current.split('___');
 if (arr.length==2)
 {
   id = arr[0];
   index = arr[1];
   idc = arr2[0];
   indexc = arr2[1];
   if (id == '' || (idc == '')) return false;
   document.getElementById(idc).className='closed';
   document.getElementById(id).className='open';
   zDT=document.getElementById(paneName).getElementsByTagName('dt'); 
   zDD=document.getElementById(paneName).parentNode.getElementsByTagName('dd')[index].style.display = 'block';
   zDD=document.getElementById(paneName).parentNode.getElementsByTagName('dd')[indexc].style.display = 'none';
   console.log('hiding', document.getElementById(paneName).parentNode.getElementsByTagName('dd')[indexc]); 
 }
 }
}

function op_init()
{
 scrollY = document.getElementById('scrolly').value;
 setScrollXY(0, scrollY);
 opctab = document.getElementById('op_curtab').value;
 setCurrentTab('order_general',  opctab);
 
}

function op_saveVars(el)
{
 el.href += '&op_curtab='+getCurrentTab('order_general');
 return true;
}


function checkAll(num)
{
	var e = document.getElementsByName('order_id[]'); 
	if (e.length == 0)
	{
		e = document.getElementsByName('order_id'); 
		
	}
	if (e.length > 0)
	{
		var checkThis = true; 
		if (e[0].checked) 
			checkThis = false; 
		 
		
		
		for (var i=0;i<e.length; i++ )
		{
			e[i].checked = checkThis; 
		}
	}
	
}