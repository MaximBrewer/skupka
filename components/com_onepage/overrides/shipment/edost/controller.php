<?php


if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
 


	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	 
	if (stripos($shipping_method, 'edost_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
		  $edost_id = (int)str_replace('edost_id_', '', $shipping_method); 
		  // example data-edost='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		  
		  if (!empty($data))
		   {
			 JRequest::setVar('edost', $edost_id); 
		     JRequest::setVar('edost_name', (string)$data['service']); 
			 JRequest::setVar('edost_rate', (float)$data['rate']);
			 JRequest::setVar('edost_code', (int)$data['code']); 
			 JRequest::setVar('edost_tariff', (int)$data['tariff']); 
			 $uid = $cart->virtuemart_shipmentmethod_id; 
		     JRequest::setVar('edost_name', (string)$data['service']); 
			 JRequest::setVar('edost_rate', (float)$data['rate']);
			 JRequest::setVar('edost_rate-'.$uid, (float)$data['rate']); 
			 $service = base64_decode($data['service']); 
			 $service = html_entity_decode($service, ENT_COMPAT, 'UTF-8'); 
			 $service = base64_encode($service); 
			 JRequest::setVar('edost_service', (string)$service);
			 JRequest::setVar('edost_name-'.$uid, (string)$service);
			 
			 
			
			
			
			 $session = JFactory::getSession();
        $d = new stdClass();
        $d->_edost_id = $edost_id; 
        $d->_edost_name = base64_decode($service);
        $d->_edost_rate = (float)$data['rate'];
        $d->_edost_code = (int)$data['code'];
        $d->_edost_tariff = (int)$data['tariff'];
        $d->_edost_virtuemart_shipmentmethod_id = $uid; 
        $session->set('edost', serialize($d), 'vm');
			
			 
			 
		   }
		}
	 }
	 // end support edost