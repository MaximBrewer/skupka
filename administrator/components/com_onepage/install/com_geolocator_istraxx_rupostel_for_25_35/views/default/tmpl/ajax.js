/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* GeoLocator is free software released under GNU/GPL  This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This file was created by www.rupostel.com team
*
*/
function op_runAjax(froms, tos, steps)
{
 
	step = steps; 
	from = froms; 
	to = tos; 
 
	if (step === 0)
	 setPercent(0); 
	
	
	chk = document.getElementById('localf').checked; 
	if (chk)
	{
	 if (step == 0)
	 {
	 step = 1;
	 setPercent(10); 
	 }
	 lf = '&localfile=true'; 
	 
	}
	else lf = '&localfile=false'; 
	
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
	dw = document.getElementById('download_url').value; 
	dw = geo_escape(dw); 
	if (to == 0) 
	{
	 to=document.getElementById('nrows').value;
	 to = parseInt(to);
	 rows = parseInt(to);
	}
    var query = 'from='+from+'&to='+to+lf+'&step='+step+'&durl='+dw;
    var url = op_ajaxurl+"?option=com_geolocator&view=default&format=raw&tmpl=component";
	
    xmlhttp2.open("POST", url, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //xmlhttp2.setRequestHeader("Content-length", query.length);
    //xmlhttp2.setRequestHeader("Connection", "close");
    xmlhttp2.onreadystatechange= op_get_geo_response ;
    document.getElementById('geo_progress').style.display = 'block';
	d2 = document.getElementById('geo_status_bar'); 
	if (step == 0)
	 setMsg('Downloading ... '); 
	else 
	if (step == 1)
	 setMsg('Extracting'); 
	//if (step == 2)
	
	if (step == 3)
	 setMsg('Cleaning temporary files');
    xmlhttp2.send(query); 
    
    
 }
 //alert('1');
}

function setPercent(p)
{
  d = document.getElementById('geo_status_bar').style.width = p+'%';
}

function setMsg(str)
 {
   document.getElementById('current_status').innerHTML = str+'<br />'+document.getElementById('current_status').innerHTML;
 }
function op_get_geo_response()
{
  
  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
	  d2 = document.getElementById('geo_status_bar'); 
	  if (resp.toString().indexOf('result_ok')>=0)
	  {
	    if (step == 0) 
		 {
		 setMsg('File downloaded OK');
		 setPercent(10);
		 }
		
		if (step == 2)
		{
		 to = parseInt(to) + parseInt(rows);
		 if (from == 0)
		 from = parseInt(from) + parseInt(rows) + 1;
		 else from = parseInt(from) + parseInt(rows);
		 
		 p = Math.round(((to / 200000)) * 100 * 0.6)
		 setPercent(20+p);
		}
		if (step < 2) 
		 {
		 setPercent(20);
		 step++;
		 }
		setMsg('Inserting to DB from rows '+from+' to '+to);
		myTimer = setTimeout(function(){op_runAjax(from, to, step)}, 10);
	  }
	  else
	  if ((resp.toString().indexOf('error_here')>=0) || (resp.toString().indexOf('finished_rows')>=0))
	   {
	     if (resp.toString().indexOf('error_here')>=0)
		 {
		 setMsg('Error !'); 
	     setMsg(resp);
		 }
		 setPercent(95);
	     step = 3; 
		 setMsg('Cleaning files'); 
		 myTimer = setTimeout(function(){op_runAjax(from, to, step)}, 10);
	   }
	  else
	  if (resp.toString().indexOf('finished_here')>=0)
	   {
	     setPercent(100);
	     setMsg('Temporary files deleted, data installed in #__geodata'); 
	   }
      else
	  {
	   d2.style.width = '100%';
	   if (resp != '')
	   setMsg(resp);
	   
	  }
    } // end response is ok
    
    }
	if (xmlhttp2.status != 200) 
	 {
	  setMsg(xmlhttp2.responseText);
	 }
    return true;
}

 function geo_escape(str)
  {
   if ((typeof(str) != 'undefined') && (str != null))
   {
     x = str.split("&").join("%26");
     return x;
   }
   else 
   return "";
  }