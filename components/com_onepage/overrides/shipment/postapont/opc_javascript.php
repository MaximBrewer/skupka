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

  function postaPontOPC'.$method->virtuemart_shipmentmethod_id.'()
  {
	  if (typeof postapontJS'.$method->virtuemart_shipmentmethod_id.' != \'undefined\')
	  postapontJS'.$method->virtuemart_shipmentmethod_id.'(); 
  }
  
  function postapontJS'.$method->virtuemart_shipmentmethod_id.'() {
					jQuery.noConflict();
					if (typeof ppapi == "undefined") return; 
					
						ppapi.linkZipField(\'ugyfelform_iranyitoszam\');
						ppapi.insertMap(\'postapontvalasztoapi\');
						ppapi.onSelect = function(data){
						jQuery(\'#postapont_id_'.$method->virtuemart_shipmentmethod_id.'\').val( data[\'id\'] )
						jQuery(\'#postapont_name_'.$method->virtuemart_shipmentmethod_id.'\').val( data[\'name\'] )
						jQuery(\'#postapont_zip_'.$method->virtuemart_shipmentmethod_id.'\').val( data[\'zip\'] )
						jQuery(\'#postapont_county_'.$method->virtuemart_shipmentmethod_id.'\').val( data[\'county\'] )
						jQuery(\'#postapont_address_'.$method->virtuemart_shipmentmethod_id.'\').val( data[\'address\'] )
						jQuery(\'#shipment_id_'.$method->virtuemart_shipmentmethod_id.'\').attr(\'checked\', \'checked\');
						
						jQuery(\'label[for="shipment_id_'.$method->virtuemart_shipmentmethod_id.'"] .vmshipment_extrainfo .vmpayment_cardinfo\').html( data[\'name\'] + " - " + data[\'zip\'] + " " + data[\'county\'] + ", " + data[\'address\'] )
						
						var me = jQuery("#shipment_id_'.$method->virtuemart_shipmentmethod_id.'"); 
						
						if (typeof me.is != \'undefined\')
						if (!me.is(":checked")) { me.click(); }
						
						};

						jQuery(\'label[for="shipment_id_'.$method->virtuemart_shipmentmethod_id.'"] .vmshipment_extrainfo\').insertAfter(\'label[for="shipment_id_'.$method->virtuemart_shipmentmethod_id.'"] .vmshipment_cost\')
						// jQuery(\'#ugyfelform_iranyitoszam\').insertAfter(\'#pp-select-postapont\')
						}
  					
 
// addOpcTriggerer(\'callBeforeAjax\', \'postaPontOPC'.$method->virtuemart_shipmentmethod_id.'()\'); 
addOpcTriggerer(\'callAfterRender\', \'postaPontOPC'.$method->virtuemart_shipmentmethod_id.'()\'); 


function postaPontOPCValidate'.$method->virtuemart_shipmentmethod_id.'() {
  var me = jQuery("#shipment_id_'.$method->virtuemart_shipmentmethod_id.'"); 
  if (typeof me.is != \'undefined\')
  if (me.is(":checked")) { 
		var d1 = document.getElementById("postapont_id_'.$method->virtuemart_shipmentmethod_id.'"); 
		if (d1.value == \'\') 
		{
			alert(\'Hiba: Nincs kiválasztva postafiók. A térképre klikkelve válasszon postofiókot.\'); 
			return false; 
		}
    }
	return true; 
						
}


addOpcTriggerer(\'callSubmitFunct\', \'postaPontOPCValidate'.$method->virtuemart_shipmentmethod_id.'\'); 
//]]> 



'; 



JFactory::getDocument()->addScriptDeclaration($js);



		$document = JFactory::getDocument();
		// google maps api
		$document->addScript( 'http://maps.googleapis.com/maps/api/js?sensor=false&language=hu&region=HU' );
		// postapont api, downloaded from here: http://www.postapont.hu/postapont_segedanyagok.zip
		$document->addScript( JUri::base().'plugins/vmshipment/postapont/postapont.js' );
		// $document->addScript( 'http://www.postapont.hu/static/javascripts/postapont-api.js' );
		// css
		$document->addStyleSheet( 'http://www.postapont.hu/static/css/postapont-api.css' );
		$document->addStyleSheet( JUri::base().'plugins/vmshipment/postapont/postapont.css' );
