/*------------------------------------------------------------------------
 * JoomNB Advanced Virtuemart Invoices
* author    CMSMart Team
* copyright Copyright (C) 2014 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
-------------------------------------------------------------------------*/


function cms_getsecs()
{
	//Create ajax request for monitored questions
	var xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url = "index.php?option=com_netbasevm_extend&task=setSession";
	url += "&sid="+Math.random();
	url=url+"&fd="+ encodeURIComponent(fromDate) + "&td=" + encodeURIComponent(toDate) + "&pd=" + encodeURIComponent(presetDate);
	//alert (url);
	var handlerFunction = sessionVarsReceived(xmlHttp);
	xmlHttp.onreadystatechange=handlerFunction;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}


function sessionVarsReceived(req)
{
	// Return an anonymous function that listens to the   
    // XMLHttpRequest instance  
	return function()
	{
		if (req.readyState==4 || req.readyState=="complete")
		{
			//we don't need to do anything
			return;
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