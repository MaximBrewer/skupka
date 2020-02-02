<?php

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
 


	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	
if (stripos($shipping_method, 'zasilkovna_')!==false)
	 {
	 
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	   if (!empty($data))
	   {
	    $data = @base64_decode($data);  
		$data = @json_decode($data); 
		
		
		foreach ($data as $key=>$val)
		 {
		   if (strpos($key, 'branch_')!==false)
		   JRequest::setVar($key, $val); 
		   //echo $key.' '.$val;    
		 }
	   }
	   
	 }