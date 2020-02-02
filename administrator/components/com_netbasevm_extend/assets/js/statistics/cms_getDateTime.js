/*------------------------------------------------------------------------
 * JoomNB Advanced Virtuemart Invoices
* author    CMSMart Team
* copyright Copyright (C) 2014 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
-------------------------------------------------------------------------*/

function cms_getDateTime(tf)
{
	if(tf == 'a')
	{
		document.getElementById('cms_fromdate').value = 'all';
		document.getElementById('cms_todate').value = 'all';
		return;
	}
	
	document.getElementById('cms_submitdate').disabled = true;
	
	//Create ajax request for monitored questions
	var xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="components/com_netbasevm_extend/cms_getDateTime.php";
	url += "?sid="+Math.random();
	url=url+"&timeFrame="+ encodeURIComponent(tf);
	//alert (url);
	var handlerFunction = datesReceived(xmlHttp);
	//alert(xmlHttp);
	xmlHttp.onreadystatechange=handlerFunction;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}


function datesReceived(req)
{
	// Return an anonymous function that listens to the   
    // XMLHttpRequest instance  
	return function()
	{
		if (req.readyState==4 || req.readyState=="complete")
		{
			var str = req.responseText.split("|");
			document.getElementById('cms_fromdate').value = str[0];
			document.getElementById('cms_todate').value = str[1];
			document.getElementById('cms_fromdate').style.backgroundColor = '#98c9fa'
			document.getElementById('cms_todate').style.backgroundColor = '#98c9fa'
			document.getElementById('cms_submitdate').disabled = false;
		}
	}
}

function GetXmlHttpObject()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}