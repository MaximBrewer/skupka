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
$viewData = array(); 
$viewData['cssId'] = $psType . '_id_' . $method->virtuemart_shipmentmethod_id;; 
$viewData['socolissimo_url'] = $method->socolissimo_url; // . "?trReturnUrlKo=" . substr(JURI::base(), 0, -1) . JRoute::_('index.php?view=cart&task=edit_shipment&option=com_virtuemart&Itemid=' . JRequest::getInt('Itemid'));; 

$js = '


function opc_soco()
{
 
 var d = document.getElementById(\'plugin_socolissimo\'); 
 if (!(d != null)) return; 
 var query = \'\'; 
 jQuery(\'input[rel="socolissimo"]\').each(function() {
    // `this` is the div
	query += "&"+this.name+"="+encodeURIComponent(this.value); 
 });
 alert(query); 
 
 
 
 var d2 = document.getElementById("socolissimo_Target"); 
 if (!(d2 != null))
 {
   ifrm = document.createElement("IFRAME"); 
   ifrm.setAttribute("src", "'.trim($viewData['socolissimo_url']).'"+query); 
   ifrm.style.width = 640+"px"; 
   ifrm.id = "socolissimo_Target"; 
   ifrm.name = "socolissimo_Target"; 
   ifrm.style.height = 480+"px"; 
   d.appendChild(ifrm); 
 }
 
 jQuery("#socolissimo_Target").show();
 jQuery("#socolissimo_description").hide();


  /* 
   old_action =jQuery("#userForm").attr("action");
		 if ($("#' . $viewData['cssId'] . '").is(":not(:checked)")) {
				$("#userForm").attr("action", old_action);
		} else {
			
			
			$("#userForm").attr({action: "' . $viewData['socolissimo_url'] . '", target: "socolissimo_Target"}).submit();
			$("#userForm").attr({action: old_action, target: ""});
		}
		*/
}
addOpcTriggerer(\'callAfterShippingSelect\', \'opc_soco()\'); 
';
		$doc = JFactory::getDocument ();
		$doc->addScriptDeclaration ($js);
		
		