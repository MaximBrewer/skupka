

/**
* @version		$Id: joomla.javascript.js 10389 2008-06-03 11:27:38Z pasamio $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL
* Joomla! is Free Software
*/

/**
* Writes a dynamically generated list
* @param string The parameters to insert into the <select> tag
* @param array A javascript array of list options in the form [key,value,text]
* @param string The key to display for the initial state of the list
* @param string The original key that was selected
* @param string The original item value that was selected
*/
op_writeDynaList: function( selectParams, source, key, orig_key, orig_val ) {
	//if (selectParams.indexOf('credit')>-1) alert('ok');
	var html = '\n	<select ' + selectParams + '>';
	var i = 0;
	for (x in source) {
		if (source[x][0] == key) {
			var selected = '';
			if ((orig_key == key && orig_val == source[x][1]) || (i == 0 && orig_key != key)) {
				selected = 'selected="selected"';
			}
			html += '\n		<option value="'+source[x][1]+'" '+selected+'>'+source[x][2]+'</option>';
		}
		i++;
	}
	html += '\n	</select>';
	
	document.writeln( html );
}

/**
* Changes a dynamically generated list
* @param string The name of the list to change
* @param array A javascript array of list options in the form [key,value,text]
* @param string The key to display
* @param string The original key that was selected
* @param string The original item value that was selected
*/
op_changeDynaList: function( listname, source, key, orig_key, orig_val ) {
	var list = eval( 'document.adminForm.' + listname );

	// empty the list
	if (typeof list != 'undefined' && (list != null))
	{;} else {return;}
	if (typeof list.options != 'undefined' && (list.options != null))
	{;} else {return;}
	
	for (i in list.options.length) {
		list.options[i] = null;
	}
	i = 0;
	for (x in source) {
		if (source[x][0] == key) {
			opt = new Option();
			opt.value = source[x][1];
			opt.text = source[x][2];

			if ((orig_key == key && orig_val == opt.value) || i == 0) {
				opt.selected = true;
			}
			list.options[i++] = opt;
		}
	}
	list.length = i;
},


// copyright: http://evolt.org/node/24700
// Submitted by JohnLloydJones on May 5, 2002 - 09:55.

op_isValidCardNumber: function (strNum)
{
   var nCheck = 0;
   var nDigit = 0;
   var bEven = false;
   
   for (n = strNum.length - 1; n >= 0; n--)
   {
      var cDigit = strNum.charAt (n);
      if (op_isDigit (cDigit))
      {
         var nDigit = parseInt(cDigit, 10);
         if (bEven)
         {
            if ((nDigit *= 2) > 9)
               nDigit -= 9;
         }
         nCheck += nDigit;
         bEven = ! bEven;
      }
      else if (cDigit != ' ' && cDigit != '.' && cDigit != '-')
      {
         return false;
      }
   }
   return (nCheck % 10) == 0;
},
op_isDigit: function (c)
{
   var strAllowed = "1234567890";
   return (strAllowed.indexOf (c) != -1);
},
op_isCardTypeCorrect: function (strNum, type)
{
   var nLen = 0;
   for (n = 0; n < strNum.length; n++)
   {
      if (op_isDigit (strNum.substring (n,n+1)))
         ++nLen;
   }
  
   if (type == 'VISA')
      return ((strNum.substring(0,1) == '4') && (nLen == 13 || nLen == 16));
   else if (type == 'AMEX')
      return ((strNum.substring(0,2) == '34' || strNum.substring(0,2) == '37') && (nLen == 15));
   else if (type == 'MC')
      return ((strNum.substring(0,2) == '51' || strNum.substring(0,2) == '52'
              || strNum.substring(0,2) == '53' || strNum.substring(0,2) == '54'
              || strNum.substring(0,2) == '55') && (nLen == 16));
   else if (type == 'DINERS')
      return ((strNum.substring(0,2) == '30' || strNum.substring(0,2) == '36'
				|| strNum.substring(0,2) == '38') && (nLen == 14));
   else if (type == 'DISCOVER')
      return ((strNum.substring(0,4) == '6011' ) && (nLen == 16));
   else if (type == 'JCB')
      return ((strNum.substring(0,4) == '3088' || strNum.substring(0,4) == '3096'
              || strNum.substring(0,4) == '3112' || strNum.substring(0,4) == '3158'
              || strNum.substring(0,4) == '3337' || strNum.substring(0,4) == '3528') && (nLen == 16));

   else
      return true;
	  
	  // stAn mod: this function checks for basic validation, but if type of card is not Visa, Amex or Master Card it still returns true
   
},

  /* Old function, not used any more
  */
op_hidePayments_obsolete: function() 
  {
     if (op_payment_disabling_disabled) return "";
     var sid = getIDvShippingRate();
	   var toClick = null;    
	   var clickit = false;
        var pms = document.getElementsByName("payment_method_id");
        if (pms != null)
        if ((sid != "") && (payconf[sid] !=null))
        {
          for (var c=0; c<pms.length; c++)
          {
           if (pms[c].type == 'select-one')
           {
             ind = pms[c].selectedIndex;
             if (ind<0) ind = 0;
             valu = pms[c].options[ind].value;
           }
           else
            valu = pms[c].value;
          
           if (valu != null)
          {
            if (sid!="")
            if (payconf[sid].toString().indexOf("/"+pms[c].value+",")>=0)
            {
             var sel = getPaymentId();

             if ((sel != null) && (sel!=""))
             {
             
              var selP = document.getElementById("payment_method_id_"+sel);
              if (selP.checked == true)
              { 
               
                toClick = pms[c];

              }
             }
            }
	    // if is payment to hide, than disable it           
	    if (sid!="")        
            if ((payconf[sid].toString().indexOf(","+pms[c].value+",")>=0) || (payconf[sid].toString().indexOf(","+pms[c].value+"/")>=0))
            {
              pms[c].disabled = true;
              if (pms[c].checked==true) {
               // so we have a disabled payment checked
                clickit = true;
              }
            }
            else
            {
            // if it is not, than enable it
               pms[c].disabled = false;
             // check fore generally disabled paymments
             if (op_disabled_payments != null)
             if (op_disabled_payments.toString().indexOf(","+pms[c].value+",")>=0)
               pms[c].disabled = true;
            }

            
            
          }
          }
        }
        else
        {
         // no shipping is selected, so let's show all methods
            // if it is not, than enable it
             for (var ff=0; ff<pms.length; ff++)
             {
               pms[ff].disabled = false;
             // check fore generally disabled paymments
             if (op_disabled_payments != null)
             if (op_disabled_payments.toString().indexOf(","+pms[ff].value+",")>=0)
               pms[ff].disabled = true;
              }
         
        }
        
    if ((toClick != null) && (clickit==true))
    {


     toClick.checked = true;
     toClick.click();

    }
    else {  }     

   return "";
  },

  
  	// returns internal id of shipping method ... only for standard shipping 
getIDvShippingRate: function()
	{
	 var svalue = getVShippingRate();
	 if ((svalue) && (svalue!=""))
	  {
	    svalue = url_decode_op(svalue);
	    scostarr = svalue.split("|");  
	    
	    if (scostarr)
	    if (scostarr[4]) return scostarr[4];
	    
	  }
	  return "";
	},
	
	
		// public method for url decoding
url_decode_op: function (string) {
		return this._utf8_decode(unescape(string));
	}

	
	
	
	// private method for UTF-8 decoding
_utf8_decode: function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	},
	
	 
	 
	  /*
	 * This function is triggered when clicked on payment methods when CC payments are there
	 */
runPayCC: function(msg_info, msg_text, msg3, curr, order_total)
	 {
	  //try
	  {
	   if (typeof changeCreditCardList == 'function')
	    {
	      changeCreditCardList();
	    }
	  }
	  //catch (e) 
	  {
	   
	  }
	  runPay(msg_info, msg_text, msg3, curr, order_total);
	  return true;
	 }