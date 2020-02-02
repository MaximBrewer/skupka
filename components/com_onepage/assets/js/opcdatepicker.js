/*
* OPC Date Picker helper
* 
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
var OPCDatePicker =  {
 noSunday : function(date, objDP) {
      // console.log(date); 
	  //console.log(typeof date); 
	  var dow =  date.getDay();
	  var mret = [true, ''];  
	  var mretneg = [false, '']; 
	  
	 
	  
	  if (jQuery.inArray(dow, opc_datepicker_disableddays) != -1)
	  return mretneg;
	  
	  //var date = new Date(obj2.currentYear,obj2.currentMonth, obj2.currentDay );
      var ymd = (date.getFullYear())+'-'+(date.getMonth()+1)+'-'+(date.getDate()); 
	  
	  
	  if (jQuery.inArray(ymd, opc_datepicker_hollidays) != -1)
	  return mretneg;	  
		//2014-11-26,2014-11-27
     
	 return mret; 
	 //return [false, ''];
}, 
 validate: function()
  {  
     var d = document.getElementById('opc_date_picker'); 
	 if (d != null)
	  {
	     
	     if (d.value == '') 
		 {
		  alert(COM_ONEPAGE_CHOOSE_DESIRED_DELIVERY_DATE_ERROR); 
		  d.className += ' invalid'; 
		  return false; 
		 }
		 d.className = d.className.split('invalid').join(''); 
		 
	  }
    return true; 
  }
}