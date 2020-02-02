<?php
/* 
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
defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
//<![CDATA[  
		function weboxj(szamlalo){
			var c=document.chooseShipmentRate;
			var m = document.getElementById("shipment_id_<?php echo $method->virtuemart_shipmentmethod_id; ?>"); 

		
        //Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);		
		var sel = document.getElementById('shipping_rate_webox'); 		
		if (sel != null)
        mutat(sel.selectedIndex,0,szamlalo);
		m.click(); 
		return true;
		}
		
		
		
  
		function mutat(idmutat,idrejt,szam) {
    obj = document.getElementsByTagName("div");
	
	for (i=0; i<szam; i++) { 
	
	obj["mutat"+i].style.display = "none"; 
	}
//	alert (idmutat);
	obj["mutat"+idmutat].style.display = "block";
    return true;
    }
	
	var popwindow
var popwindowwidth=500
var popwindowheight=400
var popwindowwidthuj=800
var popwindowheightuj=877
var popwindowtop=20
var popwindowURL="http://www.criscomp.hu"
var popwindowleft=(screen.width-popwindowwidth)/2
var popwindowleftuj=(screen.width-popwindowwidthuj)/2
var marginright
var pagecenter

function showIt(popdoksi) {
    if(!popwindow || popwindow.closed){
	popwindow = window.open(popdoksi, "weboxablak", "toolbar=no,titlebar=0,scrollbars=yes,width="+popwindowwidth+",height="+popwindowheight+",top="+popwindowtop+",left="+(popwindowleft)+"");
    popwindow.focus();
	if (document.all) {
		marginright = screen.width+50
	}
	if (document.layers) {
		marginright = screen.width+50
	}

	} else 
	{
	popwindow.focus();
		popwindow = window.open(popdoksi, "weboxablak", "toolbar=no,titlebar=0,scrollbars=no,width="+popwindowwidth+",height="+popwindowheight+",top="+popwindowtop+",left="+(popwindowleft)+"");
	}
	pagecenter=Math.floor(marginright/2)-Math.floor(popwindowwidth/2);
	
	

}

function validateWebox()
	{
	   var ppp = document.getElementById('shipment_id_<?php echo $method->virtuemart_shipmentmethod_id; ?>');
	   var ppp2 = document.getElementById("shipping_rate_webox");
 
 if (ppp2 != null)
 if (ppp.checked == true) {
	var val = ppp2.options[ppp2.selectedIndex]; 
	if (val != null) val = val.value; 
	if (val == 0)
	{
     alert("Kérjük, válasszon szállítási címet a Webox terminálok közül");
     return false; 
	}
    }
	}
	if (typeof addOpcTriggerer != 'undefined')
addOpcTriggerer('callSubmitFunct', 'validateWebox');

//]]> 
</script>