	var LF = String.fromCharCode(13) + String.fromCharCode(10);

	function isIE(){
        if(document.all) return(true);
        else return(false);
    }
	
/////////////////////////////////////////////   		
	//for tracing
	
	document.write("<div id=\"traceDiv\" style=\"position:absolute;width:250px;height:300px;top:100;left:700;display:none;overflow:auto;\"></div>");
	
	var traceNum = 0;
	function trace(text){
		//return(false); //remove me
		var div;
		placeX = 1000;
		if(arguments.length>1) placeX = arguments[1]; 
		
		div = document.getElementById("traceDiv");
		if(!div){
			alert(text);
			return(false);
		}	
		traceNum ++;
		div.style.backgroundColor = "#B9DBF0";
		div.style.background = "#B9DBF0";
		div.style.display = "";
		div.style.left = placeX;		
		div.innerHTML = "<br>" + traceNum + ":&nbsp;&nbsp;" + text + div.innerHTML;
	}
	
	function clearTrace(){
		var div;
		div = getElement("traceDiv");
		if(!div) return(false);		
		div.innerHTML = "";
		traceNum = 0;
	}
	
/////////////////////////////////////////////////////////	
	
    function getElement(objectName){
	   var obj = document.getElementById(objectName);
	   if(!obj){
 	      alert(objectName + " error - no object");
		  return(false);
	   }
	   return(obj)
	}

/////////////////////////////////////////////////// 

    function getElements(objectName){
	   var obj = document.getElementsByName(objectName);
	   if(!obj){
 	      alert(objectName + " error - no object");
		  return(false);
	   }
	   return(obj)
	}
	
/////////////////////////////////////////////////// 
	
	function displayElement(elementID,show){
		if(typeof show == "undefined") var show = true;
		var el = getElement(elementID);
		if(!el) return(false);
		
		if(show == true) el.style.display = "";
		else if(show == false) el.style.display = "none";
	}
	
	/////////////////////////////////////////////////// 
	
	function focusElement(elementID){
		var el = getElement(elementID);
		if(!el) return(false);
		el.focus();
	}
	
	/////////////////////////////////////////////////// 
	
	function enableElement(elementID,show){				
		if(typeof show == "undefined") var show = true;
		var el = getElement(elementID);		
		if(!el) return(false);
		el.disabled = (!show);		
	}
	
//////////////////////////////////////////////////////////////////////////////	

    function valueElement(objectName,text){
	   var obj = document.getElementById(objectName);
	   if(!obj){
 	      alert(objectName + "error - no object");
		  return(false);
	   }
	   
	   if(obj.tagName == "TD" || obj.tagName == "DIV") obj.innerHTML = text;
		else obj.value = text;
		
	   return(obj)
	}
	
			
	//------------------------ select fuinctions -----------------------------------------
	
	//////////////////////////////////////////////////////////////////////////////
	// return selected option of select box
	function getSelectedOption(selectID){
		var i,selectedOption,select;
		selectedOption = false;
		select = getElement(selectID);
		for(i=0;i<select.options.length;i++) 
			if(select.options[i].selected){ selectedOption = select.options[i];}			
		return(selectedOption);
	}
	
	
	//////////////////////////////////////////////////////////////////////////////
	// deselect all options from select
	function unselectAllMultiple(selectID){
		var i,select;
		select = getElement(selectID);
		for(i=0;i<select.options.length;i++) select.options[i].selected = false;
	}
	
	//////////////////////////////////////////////////////////////////////////////	
	//finds in array some data. if found - return place, if not - return -1
	function searchInArray(arr,data){
		var i;
		for(i=0;i<arr.length;i++){
			if(arr[i] == data) return(i);
		}
		return(-1);
	}
	
	//////////////////////////////////////////////////////////////////////////////	
	// get all values in array - except the exceptions (saporated by comas) and filter by maskValues (saporated by coma)
	function getStrAllValuesFromSelect(selectMultID,sap,exValues,maskValues){
		var select,i,value,arrEx,j,flag,str,arrMask;
		select = getElement(selectMultID);		
		if(arguments.length<2){
			alert("getStrAllValuesFromSelect error - to few arguments");
			return(false);
		}
		if(arguments.length <= 2) exValues = "";	//empty exValues
		if(arguments.length <= 3) maskValues = "";	//empty maskValues
		
		arrEx = new Array();
		
		if(exValues.length>0) arrEx = exValues.split(",");
		if(maskValues.length>0) arrMask = maskValues.split(",");		
				
		str = "";		
		for(i=0;i<select.options.length;i++){
			value = select.options[i].value;
			flag = true;
						
			//if the item is not in the exceptions list , and the item is in the mask list: add it to string.				
			if(exValues.length>0 && searchInArray(arrEx,value) != -1) flag = false;
			else{
				if(maskValues.length>0 && searchInArray(arrMask,value) == -1) flag = false;
			}
			if(flag == true){
				if(str.length > 0) str += sap;
				str += value;
			}
		}
		return(str);
	}			
	
	//////////////////////////////////////////////////////////////////////////////
	// select multiple items by value
	function selectMultiple(selectID,values,sap){
		var i,select,arrValues,value;
		values = String(values);
		select = getElement(selectID);
		if(values.length == 0) return(false);
		arrValues = values.split(sap);
		if(arrValues.length == 0) return(false);
		for(i=0;i<select.options.length;i++){
			//trace(select.options[i].value);
			//trace(arrValues[0]);			
			for(j=0;j<arrValues.length;j++){
				if(select.options[i].value == arrValues[j]) select.options[i].selected = true;
			}
		}		
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// get the values coma saporated by saporator, or by coma. default - coma. if none - return false
	// if 3-th argument will be "names" - retrieve names.
	function getMultipleSelectValues(selectID,sap){
		var values,i,select,flagNames = false;
		if(arguments.length == 3 && arguments[2] == "names") flagNames = true;
		if(typeof sap != "string") var sap = ",";
		select = getElement(selectID);
		values = "";
		for(i=0;i<select.options.length;i++) 
			if(select.options[i].selected){ 
				if(values.length>0) values += sap;
				if(flagNames) values += select.options[i].text;
				else values += select.options[i].value;
			}
		return(values);
	}
	
/////////////////////////////////////////////   	
	//remove all items from a select
	function clearSelect(selectID){
		var select = getElement(selectID);
		while(select.options.length != 0) select.remove(0);
	}

	/////////////////////////////////////////////   	
	//find select option by value, and update text
	function updateSelectByValue(selectID,value,text){
		var select,selectedOption;
		selectedOption = false;
		select = getElement(selectID);
		for(i=0;i<select.options.length && selectedOption==false;i++) if(select.options[i].value == value) selectedOption = select.options[i];
		if(selectedOption){
			selectedOption.text = text;
		}		
	}
	
	/////////////////////////////////////////////   	
	//find select option by value, and return text. if not found - return -1
	function getSelectTextByValue(selectID,value){
		var select,selectedOption,text,i;
		text = "";
		selectedOption = false;
		select = getElement(selectID);
		for(i=0;i<select.options.length && selectedOption==false;i++) if(select.options[i].value == value) selectedOption = select.options[i];
		if(selectedOption) text = selectedOption.text;
		return(text);
	}
	
	/////////////////////////////////////////////////////////////////////
	//add some element to select.
	function addToSelect(selectID,text,value,dir){		
		var select,option,flagNew,optionOld,indexOld;
		select = document.getElementById(selectID);		
		option = document.createElement('option');
		flagNew = false;
		
		option.text = text;
		option.value = value;
		
		indexOld = -1;
		optionOld = null
		if(!dir) var dir = "up";
		if(dir.toLowerCase() == "down"){
			if(select.selectedIndex >= 0){
			   if(select.selectedIndex == select.length-1) optionOld = null;
			   else{
					indexOld = select.selectedIndex+1;
					optionOld = select.options[indexOld];
			   }	
			}			
		}
		else{
			if (select.selectedIndex >= 0){
				indexOld = select.selectedIndex;
				optionOld = select.options[indexOld];
			}
		}
		
			try {
				select.add(option, optionOld); // standards compliant; doesn't work in IE
			}
			catch(ex) {
				if(indexOld != -1) select.add(option, indexOld); // IE only
				else select.add(option); // IE only
			}
		if(select.multiple == false) option.selected = true;		
	}
	
	//////////////////////////////////////////////////////////////////////////////	
	//remove item from list , by the value
	function removeFromSelectByValue(selectID,value){
		var select,options,i,removeIndex,selectIndex;
		select = getElement(selectID);
		options = select.options;
		removeIndex = -1;
		for(i=0;i<select.options.length && removeIndex == -1;i++)
			if(select.options[i].value == value) removeIndex = i;
			
		if(removeIndex != -1){
			select.remove(removeIndex);
			
			//select next option
			if(removeIndex < select.options.length) selectIndex = removeIndex;
			else selectIndex = select.options.length-1;
			
			if(selectIndex>=0) select.options[selectIndex].selected = true;
		}
	}
	
/////////////////////////////////////////////////////////
	//Find absolute position on the screen of some element
	function findPos(obj){
	  var curleft = curtop = 0;
		if (obj.offsetParent) {
			curleft = obj.offsetLeft;
			curtop = obj.offsetTop;
			while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
			}
		}			
		return[curleft,curtop];
	}	
	
/////////////////////////////////////////////////////////
	// set absolute div to distanation div position
	function setDivToDivPos(srcID,dstID,dx,dy){
		if(!dx) var dx=0;
		if(!dy) var dy=0;
		
		var pos = findPos(getElement(dstID));
		var div = getElement(srcID);
		div.style.left = Number(pos[0]+dx)+"px";
		div.style.top = Number(pos[1]+dy)+"px";
	}
	
/////////////////////////////////////////////////////////
	//Get selected radio number. if not - return 0
	function getSelectedRadioNumber(radioID){
		var radios,i;
		radio = getElements(radioID);
		for(i=0;i<radio.length;i++){
			if(radio[i].checked) return(i);
		}
		return(-1);
	}
	
/////////////////////////////////////////////////////////
	// if the number has only one digit, add zero before it.
	function addZero(num){
		var str;
		str = String(num);
		if(Number(num)<10 && Number(num)>0) str = String("0" + num);
		return(str);
	}

/////////////////////////////////////////////////////////	
	// print array by trace name/id -> element	
	function print_r(arr){
		if(typeof arr!="object"){
			trace("print_r error , the given array argument is not object, but: " + typeof arr);
			return(false);
		}
		var i,x;		
		for(x in arr){
			trace(x + " - " + arr[x] + "");
		}
	}

/////////////////////////////////////////////////////////	
	
	function trim(str){
		return str.replace(/^\s+|\s+$/g,"");
	}
	
	
/////////////////////////////////////////////////////////	

	 //get view area sizes (set global 
	 function getViewSizes(){
		var viewWidth,viewHeight;
		//firefox
		 if(typeof window.innerWidth != 'undefined'){
		      viewWidth = window.innerWidth;
		      viewHeight = window.innerHeight;
		 }	 
		 //explorer
		 else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0){		     
		   viewWidth = document.documentElement.clientWidth;
		   viewHeight = document.documentElement.clientHeight;
		 }
		 //older explorer
		 else{
			   var bodies = document.getElementsByTagName('body');
			   if(!bodies){
					alert("getViewSizes (javascriptFunctions.js) error - the body not found");
					return([0,0]);
			   }
			   viewWidth = bodies[0].clientWidth;
			   viewHeight = bodies[0].clientHeight;
		 }
		  return([viewWidth,viewHeight]);
	 }
	 
/////////////////////////////////////////////////////////	

	//deside how the error will be printed	
	function printError(err){
		alert(err);
	}

/////////////////////////////////////////////////////////	
	// load external js code
	function loadJsCode(code){
		if(window.execScript) window.execScript(code)
		else window.eval(code);
	}

/////////////////////////////////////////////////////////
	//string left and string right functions:
	
	function left(str,n){
		if (n <= 0)
			return "";
		else if (n > String(str).length)
			return str;
		else
			return String(str).substring(0,n);
	}
	
	function right(str, n){
		if (n <= 0)
		   return "";
		else if (n > String(str).length)
		   return str;
		else {
		   var iLen = String(str).length;
		   return String(str).substring(iLen, iLen - n);
		}
	}
	
//--------------------------------------------------------------------------

	function getSrcElementFromEvent(e){
		if(isIE()) return(e.srcElement);
		return(e.target);
	}
		
	
	//------------------------------------------------------------------------------------------------------------------------------------------------------
	// dump functions
	
	function dump_a(obj){
		var ret_val="<blockquote>";
		if(typeof obj == 'object' || typeof obj == 'function')
		{
		//ret_val += '<font color=green>***'+obj.name+'***</font><br />';
		for(var i in obj){
			ret_val += ('<b>' + i + '</b> (' + typeof obj[i] + ') :' + obj[i] + '<hr color=LightSkyBlue  /><br />');
	//			if  (typeof obj[i] == 'object')// && (bool == true))
	//				ret_val += "<hr color=DarkGreen />"+ dump(obj[i]) +"<hr color=DarkGreen /";
			}
		}
		else ret_val = ('(' + typeof obj + ') :' + obj + '<br>');
		ret_val += "</blockquote>";
		return ret_val;
	}


	function dump_all(obj,count){
		//if(obj == null)
		if(typeof obj == 'undefined')
			return "undefined";
		if( obj == 'parent')
			return "Pt4567";
			
	//	var ret_val= "";
		var ct = ( count == null)? 3 : parseInt(count)-1
			
		if  (ct > 0)
		{
			//ret_val += ct+ ('(' + typeof obj + ') :' + obj + '<br>')+dump_all(obj,ct);
			
			/**/
		
			var ret_val = '<blockquote>' + ('<br /><b>' + ct + '</b><hr color=DarkSlateBlue />');
			 
			if(typeof obj == 'object')
			{
				for(var i in obj){
					ret_val += ('<b>' + i + '</b> (' + typeof obj[i] + ') :' + obj[i] + '<hr color=LightSkyBlue  /><br />');
					if (i != 'parent'){
						if  ((typeof obj[i] == 'object') )// && ((count == null)  ))
							ret_val += "<hr color=DarkGreen />"+ dump_all(obj[i],ct) +"<hr color=DarkGreen /";
					}
					else
						ret_val += "<hr color=FireBrick  /><b>XXX parant</b><hr color=FireBrick  /";
					}
			}
			else
					ret_val += ('<b>' + obj + '</b> (' + typeof obj + ') :' + obj + '<hr color=LightSkyBlue  /><br />');
			
			ret_val += '</blockquote>';
			/**/
		}
		if (ret_val == null)
			return "stoped looking";
		else
			return ret_val;
	}

	///////////////////////////////////////////////////////////////


	function print_blank(txt,objName){
		var preText = (objName == null) ? '' : objName;	
	 	if (objName != '')
			preText =  '<font color=green>***'+objName+'***</font><br />';

		var a = window.open("about:blank");
		
		a.document.writeln(preText+txt);
		a.document.close();
		
	}

	///////////////////////////////////////////////////////////////

	function dmp(obj,depth){
		var depth = (depth == null) ? 0 : depth;
		var preText = '';
		if (depth>1){
			print_blank(dump_all(obj,depth),obj.name);
		}
		else{
			print_blank(dump_a(obj),obj.name);
			//preText =  '<font color=green>***'+obj.name+'***</font><br />';
		}
	}

//--------------------------------------------------------------------------
//get array with all form elements
function getObjFormElements(formID){
	var obj = new Object();
	var form = getElement(formID);
	var name,value,type,flagUpdate;
	
	//enabling all form items connected to mx
	for(var i=0; i<form.elements.length; i++){
		name = form.elements[i].name;		
		value = form.elements[i].value;
		type = form.elements[i].type;
		
		flagUpdate = true;
		switch(type){
			case "checkbox":
				value = form.elements[i].checked;
			break;
			case "radio":
				if(form.elements[i].checked == false) flagUpdate = false;				
			break;
		}
		if(flagUpdate == true && name != undefined) obj[name] = value;
	}		
	return(obj);
}
	
	
///////////////////////////////////////////////////////////////
			
	function switchItems(id1,id2){
		item1 = getElement(id1);
		item2 = getElement(id2);
		item1.style.display = "none";
		item2.style.display = "";
	}
	
	//--------------------------------------------------------------------------
	//get random number
	function getRandomNum(lbound, ubound) {
		return(Math.floor(Math.random() * (ubound - lbound)) + lbound);
	}
	
	//--------------------------------------------------------------------------
	//get random character
	function getRandomChar(number, lower, upper, other) {
		var numberChars = "0123456789";
		var lowerChars = "abcdefghijklmnopqrstuvwxyz";
		var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		var otherChars = "`~!@#$%^&*()-_=+[{]}\\|;:'\",<.>/? ";
		var charSet = "";
		
			if (number == true)
				charSet += numberChars;
			if (lower == true)
				charSet += lowerChars;
			if (upper == true)
				charSet += upperChars;
			if (other == true)
				charSet += otherChars;
			
		return charSet.charAt(getRandomNum(0, charSet.length));
	}
	
	//------------------------------------------------------------------------------------------------
	//generate password (first - char, lowercase)
	function generatePassword(len){
		var str = getRandomChar(false,true,false,false);	//get char only		
		for(var i=0;i<len-1;i++)
			str += getRandomChar(true,false,false,false);
		return(str);
	}
	
	//------------------------------------------------------------------------------------------------
	// do some functions when click enter
	function doOnEnter(evt,func){
		if(evt.keyCode == 13){
			eval(func);
		}
	}
	
	
	
	
	