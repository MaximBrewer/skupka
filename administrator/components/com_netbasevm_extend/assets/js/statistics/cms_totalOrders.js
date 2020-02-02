/*------------------------------------------------------------------------
* Advanced Virtuemart Invoices
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
* version 2.0
-------------------------------------------------------------------------*/

var ordersTotalLife;
var ordersAvgLife;
var ordersTotalPeriod;
var ordersAvgPeriod;

function cms_getTotalOrders()
{
	//Create ajax request for monitored questions
	var xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="components/com_netbasevm_extend/cms_getTotalOrders.php";
	url += "?sid="+Math.random();
	url=url+"&fd="+ encodeURIComponent(fromDate) + "&td=" + encodeURIComponent(toDate);
	//alert (url);
	var handlerFunction = ordersReceived(xmlHttp);
	xmlHttp.onreadystatechange=handlerFunction;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}


function ordersReceived(req)
{
	// Return an anonymous function that listens to the   
    // XMLHttpRequest instance  
	return function()
	{
		if (req.readyState==4 || req.readyState=="complete")
		{	
			var str = req.responseText.split("|");
			ordersTotalLife = str[0];
			ordersAvgLife = str[1];
			ordersTotalPeriod = str[2];
			ordersAvgPeriod = str[3];
			avgTitlePeriod = str[4];
			totalTitlePerios = str[5];
			avgTitleLife = str[6];
			totalTitleLife = str[7];
			cs = str[8];
			
			displayOrdersTotalsLife(false,avgTitleLife,totalTitleLife);
			displayOrdersTotalsPeriod(false,avgTitlePeriod,totalTitlePerios);
		}
	}
}


function displayOrdersTotalsLife(allowSwitch,avgTitle,totalTitle)
{
	if(allowSwitch == true)
	{
		totalOrAvgLife = totalOrAvgLife == 't' ? 'a' : 't';
	}
	if(totalOrAvgLife == 't')
	{
		//document.getElementById("totalOrdersLifeTitle").innerHTML = totalTitle;
		document.getElementById("currency_symbol").innerHTML = cs;
		document.getElementById('cms_totalalltime').innerHTML = ordersTotalLife;
	}
	else
	{
		//document.getElementById("totalOrdersLifeTitle").innerHTML = avgTitle;
		document.getElementById("currency_symbol").innerHTML = cs;
		document.getElementById('cms_totalalltime').innerHTML = ordersTotalLife;
	}
}

function displayOrdersTotalsPeriod(allowSwitch,avgTitle, totalTitle)
{
	if(allowSwitch == true)
	{
		totalOrAvgPeriod = totalOrAvgPeriod == 't' ? 'a' : 't';
	}
	if(totalOrAvgPeriod == 't')
	{
		//document.getElementById("totalOrdersPeriodTitle").innerHTML = totalTitle;
		document.getElementById("cms_totalpritime").innerHTML = ordersTotalPeriod;
	}
	else
	{
		//document.getElementById("totalOrdersPeriodTitle").innerHTML = avgTitle;
		//document.getElementById("cms_totalpritime").innerHTML = ordersAvgPeriod;
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