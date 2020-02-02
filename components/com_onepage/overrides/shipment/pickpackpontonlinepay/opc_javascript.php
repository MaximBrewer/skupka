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

$js = '
//<![CDATA[  
if (typeof jelolo == "undefined")
function jelolo3() {
	var e=document.getElementById("valasztott_ppk_ki");
	var d=document.getElementById("valasztott_ppk");
	var c=document.chooseShipmentRate;
	var a=document.getElementById("shipping_rate_ppk");
	var b=document.getElementById("shipment_id_'.$method->virtuemart_shipmentmethod_id.'");
	if(a.value!="0")
	{
	b.value="'.$method->virtuemart_shipmentmethod_id.'";
	e.value=a.value;
	b.checked=true; 
	if (typeof jQuery != \'undefined\')
	 jQuery(b).click(); 
	 Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);
	}
	else
	{
	b.checked=false;
	}
	return true;
	}
	function jelolo4()
	{
	var e=document.getElementById("valasztott_ppk_ki");
	var d=document.getElementById("valasztott_ppk");
	var b=document.getElementById("shipment_id_'.$method->virtuemart_shipmentmethod_id.'");
	var a=document.getElementById("shipping_rate_ppk");
	if(b.checked){
	a.options[0].selected="1";b.value="'.$method->virtuemart_shipmentmethod_id.'";e.value=a.value;
	jQuery(b).click(); 
	Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);
	}
	return true
	};
	
	function validatePPK()
	{
	   var ppp = document.getElementById(\'shipment_id_'.$method->virtuemart_shipmentmethod_id.'\');
 var ppp2 = document.getElementById("valasztott_ppk_ki");
 if (ppp.checked == true && (ppp2.value == 0 || ppp2.value == null)) {
    alert("Kérjük, válasszon szállítási címet a PicPack pontok közül");
    return false; 
    }
	}
	if (typeof addOpcTriggerer != \'undefined\')
addOpcTriggerer(\'callSubmitFunct\', \'validatePPK\');
	
	
//]]> 
'; 
JFactory::getDocument()->addScriptDeclaration($js);