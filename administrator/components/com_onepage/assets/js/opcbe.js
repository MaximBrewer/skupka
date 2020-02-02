/* 
* This part handles Javascript functions of configurator view
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

function getOPCExts()
{
// this will be implemented in 2.0.227
return false; 
 	if ((typeof xmlhttp2 != 'undefined') && (xmlhttp2 != null))
	{
	 
	}
	else
	{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }
	}
    if (xmlhttp2!=null)
    {
   
    xmlhttp2.open("GET", '//cdn.rupostel.com/exts.json', true);
    
    //Send the proper header information along with the request
	xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
    xmlhttp2.onreadystatechange= op_get_EXT_response ;
    
	
	 
     xmlhttp2.send(); 
    }
    
 }

function ignoreMsg(el) {
	var jel = jQuery(el); 
	var hash = jel.attr('ignhash'); 
	document.getElementById('ignhash').value = hash; 
	submitbutton('ignoreMsg');
}
 
function fixCartFields()
{
	submitbutton('fix_cartfields');
	return false; 
}

function fixShopfunctions()
{
	submitbutton('fix_shopfunctions');
	return false; 
}

function fix_db_charset()
{
	submitbutton('fix_db_charset');
	return false; 
}
 
 
function op_get_EXT_response()
{

   
    

  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    // here is the response from request
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
	
	part = resp.indexOf('{'); 
	 if (part >=0) 
	 {
	 resp = resp.substr(part); 
	if ((JSON != null) && (typeof JSON.decode != 'undefined'))
	{
	try
	{
	 var reta = JSON.decode(resp); 
	}
	catch (e)
	 {
	   myLog('Error in Json data'); 
	   myLog(resp); 
	   myLog(xmlhttp2); 
	 }
	}
	else {
		if ((JSON != null) && (typeof JSON.parse != 'undefined'))
	{
	try
	{
	 var reta = JSON.parse(resp); 
	}
	catch (e)
	 {
	   myLog('Error in Json data'); 
	   myLog(resp); 
	   myLog(xmlhttp2); 
	 }
	}
	}
	}
//logic here
if (typeof reta != 'undefined') 
if (reta != null)
  {
     if (reta.exts != null)
	   {
	   
	    buildExtensions(reta.exts); 
	    
	   }
  }

    }
	}
	else
	{
	  if (xmlhttp2.readyState==4 && ((xmlhttp2.status>500)))
	   {
	   }
	}
}

function buildExtensions(exts)
{
 var d = document.getElementById('extension_list'); 
 
    for (var i=0; i<exts.length; i++)
		   {
		     var row = d.insertRow(-1);
			 var cell1 = row.insertCell(0);
			 var cell2 = row.insertCell(1);
			 var cell3 = row.insertCell(2);
			 cell1.innerHTML = exts[i].name;
			 cell2.innerHTML = exts[i].desc;
			 cell3.innerHTML = '<input rel="'+exts[i].link+'" id="opcinstaller_'+i+'" type="button" onclick="return submitbutton(\'installopcext\', this)" value="Download..." />'; 
			 //exts[i].name

		     myLog(exts[i].name); 
		   }
}

function op_unhideMenu(el, hideId)
{
  
  
  showId = 'menu_'+el.options[el.selectedIndex].value; 
  myLog(showId); 
  
  if (last_menu != null)
  document.getElementById(last_menu).style.display = 'none';  
  if (hideId != null)
  document.getElementById(hideId).style.display = 'none';  
  last_menu = showId; 
  document.getElementById(showId).style.display = 'inline-block';  
}

function op_unhideMenuVM(el, hideId)
{
  
  
  showId = 'vm_menu_'+el.options[el.selectedIndex].value; 
  myLog(showId); 
  
  if (last_menu_vm != null)
  document.getElementById(last_menu_vm).style.display = 'none';  
  if (hideId != null)
  document.getElementById(hideId).style.display = 'none';  
  last_menu_vm = showId; 
  document.getElementById(showId).style.display = 'inline-block';  
}


function sp_toggleD(showId)
{
 var s = document.getElementById("language_selector");
 if (s!=null)
 if (s.options != null)
 {
  for (var i=0; i<s.options.length; i++)
  {
   var val = s.options[i].value;
   var c = document.getElementById("lang_"+val+"_table");
   if (c!=null)
   if (c.style != null)
   if (c.style.display !=null)
   {
    if (s.options[i].selected)
    {
     c.style.display = '';
    } else
     c.style.display = 'none';
   }
  }
 }
}
function clearArticle(name)
{
  document.getElementById(name+'_id').value='';
  document.getElementById(name+'_name').value='';
  return true; 
}

function opc_search(el, where, what)
{

  if (!(what != null)) what = 'text'; 
  del = document.getElementById(where); 
  if (del != null)
   {
     val = el.value.toString().toLowerCase(); 
	 //myLog(val); 
	 for (var i=0; i<del.options.length; i++)
	   { 
	      if (what == 'text')
	      if (del.options[i].text.toString().toLowerCase().indexOf(val)>=0) 
		   {
		   del.options[i].selected = "selected"; 
		   continue; 
		   }
		  else 
		  if (del.options[i].value.toString().toLowerCase().indexOf(val)>=0) 
		   {
		   del.options[i].selected = "selected"; 
		   continue; 
		   }
	   }
   }
   //else myLog(where); 
}

function op_remove_line2(iter, where)
{
   table = document.getElementById(where); 
   d = document.getElementById('rowid2_'+iter); 
   if (d != null)
     {
	   d.parentNode.removeChild(d);
	 }
	 return false; 
}

function op_remove_line(iter, where)
{
   table = document.getElementById(where); 
   d = document.getElementById('rowid_'+iter); 
   if (d != null)
     {
	   d.parentNode.removeChild(d);
	 }
	 return false; 
}

function op_new_line2(line, where)
{
  line_iter2++; 
  table = document.getElementById(where); 
  if (table !=null)
   {
     var e = document.createElement('tr');
	 e.setAttribute('id', 'rowid2_'+line_iter2);
     e.id = 'rowid2_'+line_iter2; 
	 e.innerHTML = line.split('{num}').join(line_iter2).split('&lt;').join('<').split('&lg;').join('>').split('{br}').join("\n"); 
     
     table.appendChild(e); 
	 var e2 = document.getElementById('modal_link_op_oarticle_'+line_iter2); 
	 SqueezeBox.assign($$(e2), {
				parse: 'rel'
			});
	if (eval('typeof jSelectArticle_op_oarticle_'+line_iter2+' == "undefined"'))
	  {
	     var fun = 'window.jSelectArticle_op_oarticle_'+line_iter2+' = function(id, title, catid, object) { document.id("op_oarticle_'+line_iter2+'_id").value = id;  	document.id("op_oarticle_'+line_iter2+'_name").value = title; 	SqueezeBox.close(); } '; 
		 //alert(fun); 
		 eval(fun); 
		
	 }
	  
   }
   return false; 
}

function op_new_line(line, where)
{
  line_iter++; 
  var table = document.getElementById(where); 
  if (table !=null)
   {
     var e = document.createElement('tr');
	 e.setAttribute('id', 'rowid_'+line_iter);
     e.id = 'rowid_'+line_iter; 
	 e.innerHTML = line.split('{num}').join(line_iter); 
     
     table.appendChild(e); 
   }
   return false; 
}

function op_remove_line3(el)
{
	var e = jQuery(el); 
	var tr = e.parents('tr'); 
	if (tr.length > 0) tr.remove(); 
	return false; 
}
function op_new_line3(lineConf)
{
  var search = lineConf.num; 
  lineConf.iter++; 
  var where = lineConf.id; 
  var line = lineConf.line; 
  
  var table = document.getElementById(where); 
  if (table !=null)
   {
     var e = document.createElement('tr');
	 e.setAttribute('id', 'rowid_'+lineConf.iter);
     e.id = 'rowid_'+lineConf.iter; 
	 e.innerHTML = line.split('{'+search+'}').join(lineConf.iter); 
     
     table.appendChild(e); 
	 jQuery(table).trigger('refresh'); 
	 
   }
   return false; 
}

function addnew()
{
 var html = document.getElementById("comeshere");
 var oldVars = [];
 if (html != null)
 {
  htmlOrig = html.innerHTML;
  op_next++;
  myId = "_new_"+op_next;
  
  for (var i = 1; i<op_next; i++)
  {
   oldVars[i] = [];
   var h = document.getElementById('hidepsid__new_'+i);
   oldVars[i]['ship'] = getMultiple(h);
   var h1 = document.getElementById('hidep__new_'+i);
   oldVars[i]['hp'] = getMultiple(h1);
   var h2 = document.getElementById('hidepdef__new_'+i);
   oldVars[i]['def'] = getMultiple(h2);
  }
  html.innerHTML += html1+myId+html2+myId+html21+myId+html3+myId+html31+myId+html4+myId+html41+myId+html5;
  for (var i = 1; i<op_next; i++)
  {
   var h = document.getElementById('hidepsid__new_'+i);
   setMultiple(h, oldVars[i]['ship']);
   var h1 = document.getElementById('hidep__new_'+i);
   setMultiple(h1, oldVars[i]['hp']);
   var h2 = document.getElementById('hidepdef__new_'+i);
   setMultiple(h2, oldVars[i]['def']);

  }
 }
 return false;
}

function getMultiple(ob)
{
 var arSelected = new Array();
 for (var i = 0; i<ob.options.length; i++)
 if (ob.options[i].selected)
  arSelected.push(i);
 return arSelected;
}

function setMultiple(ob, arSelected)
{
 for (var i = 0; i<arSelected.length; i++)
  ob.options[arSelected[i]].selected = true;
}
function op_langedit(lang)
{
 var lc = document.getElementById("op_"+lang+"_changed");
 if (lc != null)
 lc.value = 'yes';
}
function submitbutton2(task)
{

 var d = document.getElementById('task');
 d.value = task;
 document.adminForm.submit();
 return true;
}

function op_checkHt()
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
	var myurl = document.URL; 
	var ma = myurl.split('administrator/'); 
	if (ma.length>0)
	{
	if (typeof checkurl == 'undefined')
	var onepageurl = ma[0]+'/components/com_onepage/assets/js/onepage.js'; 
	else onepageurl = checkurl; 
	xmlhttp2.open("GET", onepageurl, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp2.onreadystatechange= op_check_ht ;
    xmlhttp2.send(); 
    }
    //var url = op_ajaxurl+"index.php?option=com_onepage&view=config&format=raw";
    }
}

function op_check_ht()
{
   
	if ((xmlhttp2.status >= 500) || (xmlhttp2.status == 404))
	 {
	    var d = document.getElementById('delete_ht'); 
		if (d != null)
		 d.value = 1; 
		myLog('Will deletete htacess'); 
		return; 
	 }
	 
	  if (xmlhttp2.readyState==4 && xmlhttp2.status==200) {
		  var resp = xmlhttp2.responseText;
		  if (!((resp.length > 0) && (resp.charAt(0) === '<'))) {
			  var d = document.getElementById('delete_ht'); 
			  if (d != null)
			  d.value = 1; 
		      myLog('Will deletete htacess'); 
			  return ; 
		  }
		  
	  }
	 
    return true;
}

function opPrepare(from, to, theme)
{
  
}

function op_runExport(from, to, theme)
{
	if (!Date.now) {
    Date.now = function() { return new Date().getTime(); }
	}
	var filekey = Date.now(); 
	
	/*
	var testP = document.getElementById('xml_export_test').value; 
	if (testP != '') {
		from = 1; 
		to = 1; 
		nproducts = 1; 
	}
	*/
    //batch, nproducts
	batch = parseInt(document.getElementById('xml_export_num').value);
	if (to >= nproducts) 
	{
	to = nproducts; 
	}
	var steps = Math.ceil(nproducts / batch);
	var cur = steps - Math.round((nproducts - to) / batch)
	
	
	if ((from == 0) && (to != 0))
	{
	// if from is 0 then we need to add two more steps 
	document.getElementById('opc_response').innerHTML = ''; 
	steps++;
	cur = 0; 
	hasExtra = true; 
	finished = false; 	
	//to = 0; 
	}
	
	if (from == 1) 
	{
	 finished = false; 	
	 
	}
	
	if ((from == 1) && (to == nproducts))
	document.getElementById('opc_response').innerHTML = ''; 
	
	
	
	
	
	currentPosition = from; 
	
    
	myLog('From: '+from+' to '+to); 
	
   
    {
	
	var query = "dowork=dowork&filekey="+filekey; 
	
	
	if ((from == 0) && (to != 0)) {
		if ((typeof theme == 'undefined') || (!theme)) {
		  query += '&cleardir=1'; 	
		}
	}
	
	if (to >= nproducts) 
	{
	
	if (hasExtra)
	  {
			query += '&compress=0'; 
	  }
	}
	
	if (finished)
	if (hasExtra)
	if (to == 0)
	{
	query += '&compress=2'; 
	cur = steps; 
	}

	var msg = stepstxt.split('{x}').join(steps).split('{n}').join(cur); 
	document.getElementById('opc_status').innerHTML = msg; 
	
    var url = op_ajaxurl; //+"index.php?option=com_onepage&view=config&format=raw";
	
	// second round
	
	
	if ((typeof theme != 'undefined') && (theme)) {
		query += '&file='+theme; 
	}
	
	
	
	query += '&from='+from+'&to='+to; 
	
	myLog(query); 
    var delay = 5000; 
    if (from <= 1) delay = 1; 
	
	setTimeout(function() { 
	   op_ajaxCall('GET', url, query, false, '', op_get_export_response);  
	}, delay);
	
    
	}
	return false; 
 
}


function op_runExportContinue(from, to, theme, filekey)
{
	
	
	
	batch = parseInt(document.getElementById('xml_export_num').value);
	if (to >= nproducts) 
	{
	to = nproducts; 
	}
	var steps = Math.ceil(nproducts / batch);
	var cur = steps - Math.round((nproducts - to) / batch)
	
	
	if ((from == 1) && (to == nproducts))
	document.getElementById('opc_response').innerHTML = ''; 
	
	
	
	
	
	currentPosition = from; 
	
    
	myLog('From: '+from+' to '+to+' step '+steps+' cur '+cur+' total products '+nproducts); 
	
   
    
	
	var query = "dowork=dowork&filekey="+filekey; 
	
	
	query += '&compress=2'; 
	
	

	var msg = stepstxt.split('{x}').join(steps).split('{n}').join(cur); 
	document.getElementById('opc_status').innerHTML = msg; 
	
    var url = op_ajaxurl; //+"index.php?option=com_onepage&view=config&format=raw";
	
	// second round
	
	
	// first round
	if (theme != null)
	query += "&file="+theme; 
	
	
	
	
	query += '&from='+from+'&to='+to; 
	
	myLog(query); 
    var delay = 5000; 
    if (from <= 1) delay = 1; 
	
	setTimeout(function() { 
	   op_ajaxCall('GET', url, query, false, '', op_get_export_response);  
	}, delay);
	
    
	
	return false; 
 
}



function op_get_export_response(rawData, async)
{
var lastfrom = -1; 
var lastto = -1; 
var filekey = '';  
var currenttheme = ''; 
if ((typeof rawData != 'undefined') && ((typeof rawData == 'XMLHttpRequest') || (typeof rawData.readyState != 'undefined')))
	var xmlhttp2_local = rawData; 
	else
	if (typeof xmlhttp2 != 'undefined')
	var xmlhttp2_local = xmlhttp2; 
	else return;
   
   var returnB = true; 
   
   if ((typeof xmlhttp2_local != 'undefined') && (xmlhttp2_local != null))
    {
   //if (opc_debug)
   myLog(xmlhttp2_local); 
   if (typeof xmlhttp2 != 'undefined')
   myLog(xmlhttp2); 
   }

   if (xmlhttp2_local.readyState==4 && xmlhttp2_local.status==200)
    {
    var resp = xmlhttp2_local.responseText;
    if (resp != null) 
    {
	  try {
		  console.log('debug resp', resp); 
		  var data = eval("(" + resp + ")");
	  }
	  catch (e) {
		  console.log(e); 
		  console.log(resp); 
		
	  }
		
		/*
	  if (typeof JSON.parse !== 'undefined') {
		  try {
			  var data = JSON.parse(resp); 
			  if ((data != null) && (typeof data.msg !== 'undefined')) {
				  
			*/	  
			if ((data != null) && (typeof data.msg !== 'undefined')) {
				  var str = data.msg; 
				  
					  
				  document.getElementById('opc_response').innerHTML += str; 
				  
				  if ((typeof data.finished !== 'undefined') && (data.finished)) return; 
				  
				   lastfrom = data.from; 
				   lastto = data.to;
				   filekey = data.filekey; 
				   currenttheme = data.theme;
				   maxreached = data.maxreached;
			}
			else {
				 console.log('ERROR', resp); 
				 document.getElementById('opc_response').innerHTML += resp; 
				 return; 
			}
			  
     
      
    }
	else {
		return; 
	}
	// step 0 leading to step 1 
	if (maxreached) {
		currentPosition = currentPosition+nproducts; 
	}
	else
	if (lastfrom >= 0) {
		
		currentPosition = lastfrom+batch;
		
	}
	else {
	if (currentPosition == 0)
	{
	 currentPosition = 1; 
	}
	else
	currentPosition += batch; 
	}
	
	
	
	
	if (currentPosition <= nproducts)
	op_runExportContinue(currentPosition, currentPosition+batch-1, currenttheme, filekey); 
	else
	{
	 op_runExportContinue(0, 0, currenttheme, filekey); 
	}
	
	}
	if (xmlhttp2_local.status > 400) 
	 {
	    
	    document.getElementById('opc_response').innerHTML += 'Error '+xmlhttp2.status; 
		if (typeof xmlhttp2_local.responseText != 'undefined')
		{
			document.getElementById('opc_response').innerHTML += xmlhttp2_local.responseText; 
		}
		myLog(xmlhttp2); 
	 }
	
    return true;
}


 
function op_ajaxCall(type, url, query, sync, contentType, callBack)
{

    if ((typeof contentType == 'undefined') || (!(contentType != null)) || (contentType == ''))
	contentType = "application/x-www-form-urlencoded; charset=utf-8"; 
	
	
   	if (typeof jQuery == 'undefined')
	{
	if ((typeof xmlhttp2 != 'undefined') && (xmlhttp2 != null))
	{
	 
	}
	else
	{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }
	}
    if (xmlhttp2!=null)
    {
	
	
	xmlhttp2.open(type, url, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", contentType);
    //xmlhttp2.setRequestHeader("Content-length", query.length);
    //xmlhttp2.setRequestHeader("Connection", "close");
	
    xmlhttp2.onreadystatechange= callBack ;
	

	
	 
     xmlhttp2.send(query); 
	  myLog(' Running Ajax with xhttp'); 
	 
	 }
	 }
	 else
	 {
	   
		  myLog(' Running Ajax with jQuery'); 
		
		{
	    jQuery.ajax({
				type: "POST",
				url: url,
				data: query,
				cache: false,
				complete: callBack,
				async: true
				
				});
		}
		

		
		
	
	 }
	 
	 
	 return false; 
}

function myLog(msg)
{
   if (typeof console != 'undefined')
   if (typeof console.log != 'undefined')
    {
	  console.log(msg); 
	}
} 

function updateCat(el)
{
   if (typeof jQuery == 'undefined') 
   {
   alert('jQuery not found ! Please check your javascript error console'); 
   return;
   }
   
   //var mydata = jQuery.parseJSON( el.data );
       
	   
	   var mydata = el.options[el.selectedIndex].getAttribute('data');
	   myLog(mydata); 
	   
   jQuery.ajax({
  url: "index.php?option=com_onepage&view=pairing&tmpl=component&cmd=updateline&format=raw",
  type: 'POST',
  cache: false,
  data: 'data='+mydata,
  complete: function (data) {
   
   var resp = data.responseText; 
   
   myLog(resp); 
   
   var part = resp.indexOf('{"msg":'); 
	 if (part >=0) 
	 {
	 
	 if (part !== 0)
	 var resp = resp.substr(part); 
	 
	 
	if ((JSON != null) && (typeof JSON.decode != 'undefined'))
	{
	  var reta = JSON.decode(resp);
	}
	else
	if ((JSON != null) && (typeof JSON.parse != 'undefined'))
	{
	  var reta = JSON.parse(resp);
	}
	else
	{
	 
	  var reta = eval("(" + resp + ")");
	
	}
	//try
	if ((typeof reta != 'undefined') && (reta != null))
	{
	 myLog('....'); 
	 
	  myLog(reta.cat_id); 
	  if (typeof reta.cat_id != 'undefined')
	    {
		  var d = document.getElementById('cat_id_'+reta.cat_id); 
		   if (d != null)
		    d.innerHTML = reta.msg; 
		else
		{
		  var d = document.getElementById('cat_id_'+reta.cat_id+'_'+reta.entity); 
		   if (d != null)
		    d.innerHTML = reta.msg; 
			
		}
		}
	}
	//catch (e)
	 {
	  // myLog(e); 
      // myLog(data.responseText); 
     }
	 
	
	 }
	 }
  });
}

function op_runAjax()
{
   return true;  
	
	
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
    var url = op_ajaxurl+"index.php?option=com_onepage&view=config&format=raw";
	var query = ""; 
    xmlhttp2.open("POST", url, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp2.setRequestHeader("Content-length", query.length);
    xmlhttp2.setRequestHeader("Connection", "close");
    xmlhttp2.onreadystatechange= op_get_geo_response ;
    
	
	
    xmlhttp2.send(query); 
    
    
	}
 
}


function op_get_geo_response()
{
  
  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
	 d = document.getElementById('opc_language_editor').innerHTML = resp; 
     // CODE COMES HERE
    
    }
	}
	if (xmlhttp2.status != 200) 
	 {
	  
	 }
    return true;
}

function ext_chageList(el)
{
	if (el.options[el.selectedIndex].value == 'site')
	{
		e = document.getElementById('tr_ext_administrator').style.display = 'none'; 
		e = document.getElementById('tr_ext_site').style.display = 'block'; 
	}
	else
	{
		e = document.getElementById('tr_ext_administrator').style.display = 'block'; 
		e = document.getElementById('tr_ext_site').style.display = 'none'; 
		
	}
	return true; 
}


function installExt(id)
{
   var ide = document.getElementById('ext_id'); 
   ide.value = id; 
   ide.setAttribute('rel', id); 
   return submitbutton('installext', ide); 
}

	function submitbutton(task, el)
	{
	 if (task == 'template_upload') 
	  { 
	   document.adminForm.enctype = 'multipart/form-data';
	   document.adminForm.backview = 'exportpane';
	  }
	 if (task == 'template_update_upload')
	 {
	   document.adminForm.enctype = 'multipart/form-data';
	   document.adminForm.backview = 'exportpane';
	 }
	 if (typeof el != 'undefined')
	 if (el != null)
	  {
	    if (el.getAttribute('rel', '') != '')
		 {
		   var dd = document.getElementById('task2'); 
		   if (dd != null)
		    dd.value = el.getAttribute('rel', ''); 
		 }
	  }
	 var d = document.getElementById('task');
	 d.value = task;
	 document.adminForm.submit();
	 return true;
	}

	
		function submitbuttonCustom(task, el)
	{
	 if (task == 'template_upload') 
	  { 
	   document.adminForm.enctype = 'multipart/form-data';
	   document.adminForm.backview = 'exportpane';
	  }
	 if (task == 'template_update_upload')
	 {
	   document.adminForm.enctype = 'multipart/form-data';
	   document.adminForm.backview = 'exportpane';
	 }
	 if (typeof el != 'undefined')
	 if (el != null)
	  {
	    if (el.getAttribute('rel', '') != '')
		 {
		   var dd = document.getElementById('task2'); 
		   if (dd != null)
		    dd.value = el.getAttribute('rel', ''); 
		 }
	  }
	 var d = document.getElementById('task');
	 d.value = task;
	 document.adminForm.submit();
	 return true;
	}


	
		  function add_dpps()
		   {
		     d = document.getElementById('dpps_section_'+opc_last_dpps); 
			 if ((d != null))
			  {
			    newid = opc_last_dpps+1; 
			    newhtml = d.innerHTML
				.split('dpps_default_'+opc_last_dpps)
				.join('dpps_default_'+newid)
				.split('dpps_disable_'+opc_last_dpps)
				.join('dpps_disable_'+newid)
				.split('dpps_search_'+opc_last_dpps)
				.join('dpps_search_'+newid)
				.split('dpps_search['+opc_last_dpps+']')
				.join('dpps_search['+newid+']')
				.split('dpps_disable['+opc_last_dpps+']')
				.join('dpps_disable['+newid+']')
				.split('dpps_default['+opc_last_dpps+']')
				.join('dpps_default['+newid+']')
				.split('dpps_addhere_'+opc_last_dpps)
				.join('dpps_addhere_'+newid)
				
				
				d2 = document.getElementById('dpps_addhere_'+opc_last_dpps); 
				if (d2 != null)
				d2.innerHTML = '<div id="dpps_section_'+newid+'">'+newhtml+'</div>'; 
				opc_last_dpps++; 
			  }
			  return false; 
		   }
		   
		    function addMore(what, where)
		   {
		     
		     var d = document.getElementById(where); 
			 var other2 = what.split('{key}').join(keycount); 
			 var other2 = other2.split('{val}').join(''); 
			 var od = document.createElement('div');
			 od.innerHTML = other2; 
			 d.appendChild(od); // += other2; 
			 keycount++; 
			 return false; 
		   }
		   
		   function add_curl()
			 {
			   ah = document.getElementById('add_here'); 
			   if (ah != null)
			    {
				  ah.innerHTML += toAdd0+next+toAdd1;
				  next++; 
				}
				return false; 
			 }
			 
			 function initRows()
		{
		         var tables = document.getElementsByTagName("table"); 
		var b = 0; 
		for (var i = 0; i<tables.length; i++)
		 {
		   tables[i].className += ' adminlist'; 
		   for (var j=0; j<tables[i].rows.length; j++)
		     {
			    if (b>1) b = 0; 
			    tables[i].rows[j].className += ' row'+b; 
				//myLog(tables[i].rows[j].className); 
				b++;
			 }
		 }
    
		}
var currentPosition = 0; 		
var currentTheme = ''; 
var hasExtra = false; 
var finished = false; 

if (typeof jQuery != 'undefined')
jQuery(document).ready( function() 
{
	setTimeout(function () {
	var e = jQuery('[data-toggle="tab"]'); 
	if (e.length > 0) {
	  e.each( function() {
	     var i = jQuery(this); 
		 i.click( function() {
		    rememberTab(this); 
		 })
	  }); 
	}
	}, 3000); 	
}); 

function rememberTab(el) {
  var e = jQuery(el); 
  if (typeof e.attr == 'undefined') return; 
  var hrf = e.attr('href'); 
  if ((hrf != null) && (hrf != '')) {
  if (typeof jQuery.cookie != 'undefined')
  {
  hrf = hrf.split('#').join(''); 
  jQuery.cookie("opc_tab", hrf); 
  console.log(hrf); 
  }
	
  }

  
}