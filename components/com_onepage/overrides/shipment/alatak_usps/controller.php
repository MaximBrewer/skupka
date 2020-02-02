<?php

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
 


	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	 
	if (stripos($shipping_method, 'usps_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		   
		  if (!empty($data))
		   {
		     JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('usps_rate', (float)$data['rate']);
			 $uid = $cart->virtuemart_shipmentmethod_id; 
		     JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('usps_rate', (float)$data['rate']);
			 JRequest::setVar('usps_rate-'.$uid, (float)$data['rate']); 
			 $service = base64_decode($data['service']); 
			 $service = html_entity_decode($service, ENT_COMPAT, 'UTF-8'); 
			 $service = base64_encode($service); 
			 JRequest::setVar('usps_service', (string)$service);
			 JRequest::setVar('usps_name-'.$uid, (string)$service);
			 
			  $session = JFactory::getSession();
			$sessionUspsData = new stdClass();

			$sessionUspsData->_usps_id = $uid; 
			$sessionUspsData->_usps_name = $service;
			$sessionUspsData->_usps_rate = (float)$data['rate']; 
			$sessionUsps = $session->set('usps', serialize($sessionUspsData), 'vm');
			 
			 
		   }
		}
	 }
	 // end support USPS