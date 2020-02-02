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


// support for USPS: 
	if (stripos($shipping_method, 'usps_')!==false)
	 {
	   $dataa = OPCTransform::getFT2($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-usps');
	  

	 
	   
	   $test = 'usps_id-'.$id.'_'; 
	   if (stripos($shipmentid, $test)!==false)
	   {
		   $usps_id = (int)str_replace($test, '', $shipmentid); 
		   
	   }
	    
	  
	  
	  
	    if (empty($usps_saved_semafor))
	 {
	  $usps_saved = $session->get('usps', null, 'vm');
	  $usps_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('usps', $usps_saved, 'vm'); 
	 }
	 
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		   $dt = reset($dataa); 
		  $data = @json_decode($dt, true); 
		   
		  
		   $tx = @base64_decode($data['service']); 
		   if (!empty($tx))
		   {
			  // $data['service'] = $tx; 
		   }
		 
		  if (!empty($data))
		   {
			   
			   if (!empty($usps_id))
			   {
				   JRequest::setVar('usps', $usps_id); 
			   }
			   
			   $uid = $cart->virtuemart_shipmentmethod_id; 
		     JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('usps_rate', (float)$data['rate']);
			 JRequest::setVar('usps_rate-'.$uid, (float)$data['rate']); 
			 $service = base64_decode($data['service']); 
			 $service = html_entity_decode($service, ENT_COMPAT, 'UTF-8'); 
			 
			 
			 
			 $service = base64_encode($service); 
			 JRequest::setVar('usps_service', (string)$service);
			 JRequest::setVar('usps_name-'.$uid, (string)$service);
			 
			
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dt).'"/>';
			 
			 
			 
			 
		   }
		   else
		   {
			   
		   }
		}
		else
		{
			
		}
		
			
	 }
	 // end support USPS