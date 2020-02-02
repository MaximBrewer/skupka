/*
* This function is ran by AJAX
* 
*/

function op_runSST(el, command)
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
	
   

    if (command == 'update')
    {
     translation = op_escape(el.value); 
	 //alert(translation); 
	 username = document.getElementById('nickname').value; 
	 
     var query = 'translation_var='+el.name+'&translation='+translation+'&nickname='+username+'&command=update';
	 
    }
	else
	if ((command == 'editcss') || (command == 'savecss'))
	{
		file = el.getAttribute('rel', ''); 
		css = ''; 
		if (command == 'savecss')
		{
			mycss = document.getElementById('css_here').value; 
			css = '&css='+op_escape(mycss); 
		}
		else
		{
			d = document.getElementById('savecssid'); 
			d.setAttribute('rel', file); 
		}
		
		colors = getColorsPost(); 
	 var query = 'command='+command+'&file='+file+colors+css;
	}
	else
	if ((command == 'preview') || (command=='savepreview'))
	{
		template = document.getElementById('current_template').value; 
		colors = getColorsPost(); 
		//alert(colors); 
		var query = 'command='+command+'&template='+op_escape(template)+colors;
	}
	else
    if (command == 'generate')
    {
	
     //el.onclick = "";
	  username = document.getElementById('nickname').value; 
     var query = 'command=generatefile&nickname='+op_escape(username);
    }
	else return false;
    var url = op_secureurl+"";
    xmlhttp2.open("POST", url, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //xmlhttp2.setRequestHeader("Content-length", query.length);
    //xmlhttp2.setRequestHeader("Connection", "close");
    xmlhttp2.onreadystatechange= op_get_SST_response ;
    
    xmlhttp2.send(query); 
    
    
 }
 return false;
}

function getColorsPost()
{
	colors = ''; 
		
		for (var i = 0; i<origColors.length; i++)
		{
		  d1 = document.getElementById('myColor'+origColors[i]); 
		  val = d1.value.split('#').join(''); 
		  if (val != origColors[i])
		  {
			colors += '&fromcolor_'+op_escape(origColors[i])+'_tocolor_'+op_escape(val)+'=1';  
		  }
		}
		return colors; 
}
  function op_escape(str)
  {
   if ((typeof(str) != 'undefined') && (str != null))
   {
	   
	   x = encodeURIComponent(str);
	   x = x.split('>').join('%3E'); 
	   x = x.split('<').join('%3C'); 
	   
       //x = str.split("&").join("%26");
     return x;
   }
   else 
   return "";
  }
  
  
function op_get_SST_response()
{

  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    // here is the response from request
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
        if (!op_css)
		{
		var a = resp.split('>><<');
		if (a.length > 0 && (a[0].indexOf('hash')>=0))
		{
		 id = a[0]; 
		 ai = id.indexOf('hash');
		 id = id.substr(ai); 
		 var delfo = document.getElementById(id);
		 if (delfo != null) 
		 {
		 delfo.innerHTML = a[1];
		 if (id.indexOf('generate')>=0)
		 {
		   delfo.href = a[2]; 
		   delfo.onlick = "return true;";
		 }
		 }
		 else 
		  {
		    
		  }
		}
		else 
		{
		 document.getElementById('resp_msg').innerHTML = resp + document.getElementById('resp_msg').innerHTML; 
		 
		}
		}
		else
		{
		  if (resp.indexOf('OPC_OK')>=0)
		  {
			  document.getElementById('hideme').style.display = 'none'; 
			  document.getElementById('css_here').value = ''; 
			  document.getElementById('css_here').setAttribute('rel', ''); 
		  }
		  else
		  if (resp.indexOf('OPC_DISPLAY_POPUP')>=0)
		  {
			  msg = resp.split('OPC_DISPLAY_POPUP').join(''); 
			  msg = msg.split('END_DEBUG').join('');
			  redirect = false; 
			  if (resp.indexOf('OPC_REDIRECT')>=0)
				  redirect = true; 
			  msg = msg.split('OPC_REDIRECT').join(''); 
			  alert(msg); 
			  if (redirect)
				  window.location = 'index.php?option=com_onepage';
		  }
		  else
		  if (resp.indexOf('END_DEBUG')<0)
		  {
		  if (resp != '')
		  {
		  document.getElementById('hideme').style.display = 'block'; 
		  document.getElementById('css_here').value = resp; 
		  }
		  }
		  else
		  {
			  if (typeof console != 'undefined')
				  if (typeof console.log != 'undefined')
				    console.log(resp); 
		  }
		  reloadIframe(); 
		}
       
    }
    
  } 
  else
  {
    if (typeof console != 'undefined')
	if (console != null)
	 console.log(xmlhttp2);
  }  
}

function opc_changeColor(el, mycolor)
{
c=document.getElementById('currentColor');
if (c.value != '')
{
	c2 = document.getElementById(c.value); 
	c2.style.backgroundColor = mycolor;
	c2.style.color= mycolor;
	c2.value=mycolor;
}
}
function opc_colorPicker(event, element)
{
	
	return colorPicker(event);
}
function onColorClick(el)
{
	d = document.getElementById('currentColor'); 
	if (d.value != '')
	{
	  d2 = document.getElementById(d.value); 	
	  d2.className = d2.className.split('active').join('unselected'); 
	}
	
	d.value = el.id; 
	el.className = el.className.split('unselected').join('active'); 
	
	
}
function reloadIframe()
{
	document.getElementById('previewiframe').contentWindow.location.reload(true);
	return false; 
}


function opc_onColorChange(el, orig) 
{
	
		newC = el.value.split('#').join(''); 
		origC = orig.split('#').join(''); 
		changedColors[origC] = newC; 
//	alert(newC); alert($origC); 
}

function opc_toggle(el, id)
{
	child=(el.firstElementChild||el.firstChild);
	d = document.getElementById(id); 
	if (d.style.display != 'none')
	{
		d.style.display = 'none';
		if (child != null)
		child.className = 'arrow-e'; 
	}
	else 
	{
	d.style.display = 'block'; 
	if (child != null)
	child.className = 'arrow-s'; 
	}
return false; 	
}