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
* loaded from: \components\com_onepage\controllers\opc.php
* function runExt()
* 
*/
defined('_JEXEC') or die('Restricted access');
// support for USPS: 
$vm_id = JRequest::getVar('virtuemart_shipmentmethod_id', 0); 

 $shipping_method = JRequest::getVar('saved_shipping_id', ''); 
{
	
	
	
	  
	
	 
	 
	 // skipcart is not compatible, therefore don't use it: 
	 // $plugin = JPluginHelper::getPlugin( 'system', 'vmskipcart' );
	 
	
	 
	 // fedex
	 if (stripos($shipping_method, 'fedex_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		  //{"id":"03","code3":"USD","rate":8.58,"GuaranteedDaysToDelivery":[]}
		   
		   if (!empty($data))
		   {
			  JRequest::setVar('fedex_rate', (string)$data['id']);
		   }
		}
	 }
	 // end fedex
	 
	 $session = JFactory::getSession();
	 $session->set('vmcart_redirect', true,'vmcart_redirect');
	 
	 //fedex multi box support: 
	 unset($_SESSION['load_fedex_prices_from_session']); 
	 
	
	
	$klarna = JRequest::getVar('opc_payment_method_id', 0); 
	
	if (!empty($klarna))
	 {
	   JRequest::setVar('klarna_paymentmethod', $klarna); 
	 }
	 
	 $id = JRequest::getVar('branch_ppp', ''); 
	 if (!empty($id))
	 {
	  
	   $value = JRequest::getVar('branch_data_'.$id); 
	   
	   JRequest::setVar('shipmentPoint', $value); 
	   $_POST['shipmentPoint'] = $value;
	   $cart->lists['shipmentPoint'] = $value; 
	  $xmlfile = JPATH_CACHE .DIRECTORY_SEPARATOR. 'validboltlista.xml';
	  $_SESSION['vm_ppp_xml'] = $xmlfile;
	 
	 }

	 
	 //runExt name="ebrinstalments"
	  $session = JFactory::getSession();
	  $eid = JRequest::getVar('ebrinstalments', null); 
	  if (!is_null($eid))
	  {
	  
	  //object(stdClass)#804 (2) { ["ebrinstalments"]=> string(1) "0" ["ebrinstalmentsamount"]=> string(3) "421" } 
	   $obj = new stdClass(); 
	   $obj->ebrinstalments = $eid; 
	   $s = serialize($obj); 
	   $b = $session->set('eurobnk', $s, 'vm'); 
	  }
	  
	  
	  //HU, posta pont
	 $test = JRequest::getVar('select_adress', 0); 
	 $test2 = JRequest::getVar('postapont_id_' . $cart->virtuemart_shipmentmethod_id); 
	 if ((!empty($test)) && (is_numeric($test)) && (empty($test2)))
	 {
		$uid = $cart->virtuemart_shipmentmethod_id; 
		JRequest::setVar('postapont_id_' . $cart->virtuemart_shipmentmethod_id, $test);
		
		if ((!empty($cart->ST)) && (!empty($cart->ST['zip']))) $zip = $cart->ST['zip']; 
		else if ((!empty($cart->BT)) && (!empty($cart->BT['zip']))) $zip = $cart->BT['zip']; 
		else 
		{
			$t = JRequest::getVar('ugyfelform_iranyitoszam', ''); 
			if (!empty($t)) $zip = $t; 
		}
		
		
		
		JRequest::getVar('postapont_zip_' . $cart->virtuemart_shipmentmethod_id, $zip);
		JRequest::getVar('ugyfelform_iranyitoszam', $zip);
		
		
	 }
	  
}



if (!empty($vm_id)) {
  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pluginhelper.php'); 
  $element = OPCPluginHelper::getPluginElement('shipment', $vm_id, false);    
  jimport('joomla.filesystem.file');
  $element = JFile::makeSafe($element); 
  if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'shipment'.DIRECTORY_SEPARATOR.$element.DIRECTORY_SEPARATOR.'controller.php')) {
    include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'shipment'.DIRECTORY_SEPARATOR.$element.DIRECTORY_SEPARATOR.'controller.php'); 
  }
}
