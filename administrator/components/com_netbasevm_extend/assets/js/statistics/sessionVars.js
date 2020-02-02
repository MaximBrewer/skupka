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
function setSessionVars()
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