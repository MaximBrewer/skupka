///////////////////////////////////////////////////////////////////////////////
// Copyright 2009 InteraMind Advanced Analytics, http://www.interamind.com 
// This file is part of VirtueMart Dashboard 
//
// VirtueMart Dashboard is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// VirtueMart Dashboard is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with VirtueMart Dashboard.  If not, see <http://www.gnu.org/licenses/>.
///////////////////////////////////////////////////////////////////////////////
var ordersTotalLife;
var ordersAvgLife;
var ordersTotalPeriod;
var ordersAvgPeriod;

function getOrdersTotals()
{
	//Create ajax request for monitored questions
	var xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="components/com_netbasevm_extend/helpers/statistics/gerOrdersTotals.php";
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
		document.getElementById("totalOrdersLifeTitle").innerHTML = totalTitle;
		document.getElementById("ordersTotalLife").innerHTML = ordersTotalLife;;
	}
	else
	{
		document.getElementById("totalOrdersLifeTitle").innerHTML = avgTitle;
		document.getElementById("ordersTotalLife").innerHTML = ordersAvgLife;;
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
		document.getElementById("totalOrdersPeriodTitle").innerHTML = totalTitle;
		document.getElementById("ordersTotalPeriod").innerHTML = ordersTotalPeriod;
	}
	else
	{
		document.getElementById("totalOrdersPeriodTitle").innerHTML = avgTitle;
		document.getElementById("ordersTotalPeriod").innerHTML = ordersAvgPeriod;
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