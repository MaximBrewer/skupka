<?php

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
 


	
	 	// support for UPS: 
	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	
	// support for ACS: by Dmitry Vadis <dmvadis@gmail.com / info@cmscript.net>
	if (stripos($shipping_method, 'acs_')!==false)
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
		     //JRequest::setVar('acs_name', (string)$data['service']); 
			 JRequest::setVar('acs_rate', $data['id']);
			
			 
			 
			 
		   }
		}
	 }
	 // end support ACS