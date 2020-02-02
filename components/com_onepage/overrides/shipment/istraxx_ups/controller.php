<?php

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
 


	
	 	// support for UPS: 
	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	if (stripos($shipping_method, 'ups_')!==false)
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
		     //JRequest::setVar('ups_name', (string)$data['service']); 
			 JRequest::setVar('ups_rate', $data['id']);
			 //JRequest::setVar('virtuemart_ups_rate', $data['id']); 
			 JRequest::setVar('ups_rate-'.$cart->virtuemart_shipmentmethod_id, $data['id']); 
			 
			 
			 $session = JFactory::getSession();
			 $sessionUps = $session->get('ups_rates', 0, 'vm');

			if (!empty($sessionUps)) {
				$ups_rates = json_decode($sessionUps, TRUE);
				
		    }
			
			$ups_rates[$data['id']]['id'] = $data['id'];
			$ups_rates[$data['id']]['code3'] = $data['code3'];
			$ups_rates[$data['id']]['rate'] = $data['rate'];
			$ups_rates[$data['id']]['GuaranteedDaysToDelivery'] = $data['GuaranteedDaysToDelivery'];
			$session->set('ups_rates', json_encode($ups_rates), 'vm');
			
			
			 
			 
		   }
		}
	 }
	 // end support UPS