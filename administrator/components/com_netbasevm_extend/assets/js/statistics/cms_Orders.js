/*------------------------------------------------------------------------
 * JoomNB Advanced Virtuemart Invoices
* author    CMSMart Team
* copyright Copyright (C) 2014 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
-------------------------------------------------------------------------*/

var maxDailyMonth;
var avgDailyMonth;
var maxDailyYear;
var avgDailyYear;
var avgDailyWeek;

function cms_getOrders()
{
	//Create ajax request for monitored questions
	var xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="components/com_netbasevm_extend/cms_Orders.php";
	url += "?sid="+Math.random();
	//alert (url);
	var handlerFunction = avgKpiReceived(xmlHttp);
	xmlHttp.onreadystatechange=handlerFunction;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}


function avgKpiReceived(req)
{
	// Return an anonymous function that listens to the   
    // XMLHttpRequest instance  
	return function()
	{
		if (req.readyState==4 || req.readyState=="complete")
		{
			var str = req.responseText.split("|");

			maxDailyMonth = parseFloat(str[0]);
			avgDailyMonth = parseFloat(str[1]);
			maxDailyYear = parseFloat(str[2]);
			avgDailyYear = parseFloat(str[3]);
			avgDailyWeek = parseFloat(str[4]);
			
			//drawDailyOrdersKpi();
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