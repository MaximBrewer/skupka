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
// fedex
	 if (stripos($shipping_method, 'fedex_id')!==false)
	 {
	 $session = JFactory::getSession();
	 /*
	 if (false)
	 if (empty($fedex_saved_semafor))
	 {
	  $fedex_saved = $session->get('fedex_rates', null, 'vm');
	  $fedex_saved_semafor = true; 
	 }
	 else
	 {
	    $session->set('fedex_rates', $fedex_saved , 'vm');
	 }
	 */
	 
	  $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data*');
	  
	  
	   
	   
	  
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('fedex_rate', (string)$data['id']);
			 //JRequest::setVar('cpsol_rate', (string)$data['rate']);
			 //JRequest::setVar('cpsol_shippingDate', (string)$data['shippingDate']);
			 //JRequest::setVar('cpsol_deliveryDate', (string)$data['deliveryDate']);
			 
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	}
	
		
	 
	 $fedex_multi = $session->get("shipping_services", ''); 
	 $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'value');
	 
	 foreach ($dataa as $k=>$zid)
	 {
	 if (!empty($zid) && (strpos($zid, ':')!==false))
	 {
		 
		 
	 if (!empty($fedex_multi))
	 {
	
	 $fi = explode(':', $zid); 
	 foreach ($fedex_multi as $key=>$fedex_rate)
	 {
		 $fedex_multi[$key]['baseRequest']['selected'] = $fi[1]; 
		 
	
	 }
	 $session->set('shipping_services', $fedex_multi); 
	
	 }
	 JRequest::setVar('virtuemart_shipmentmethod_id', $zid); 
	 
	 
	 }
	 }