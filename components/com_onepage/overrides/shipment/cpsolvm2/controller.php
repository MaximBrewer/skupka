<?php

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
 


	
	 	// support for UPS: 
	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	
	
	if (stripos($shipping_method, 'cpsol_')!==false)
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
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('cpsol_name', (string)$data['name']);
			 JRequest::setVar('cpsol_rate', (string)$data['rate']);
			 JRequest::setVar('cpsol_shippingDate', (string)$data['shippingDate']);
			 JRequest::setVar('cpsol_deliveryDate', (string)$data['deliveryDate']);
			 JRequest::setVar('cpsol_zero_rate', (string)$data['zeroRate']);
			 JRequest::setVar('cpsol_radio', $data['cpsol_radio']); 
			 //$html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	 }