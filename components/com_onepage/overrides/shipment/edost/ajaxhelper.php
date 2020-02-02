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


// support for edost: 
/*
<input data-dynamic-update="1" type="radio" class="edost-radio" name="virtuemart_shipmentmethod_id" checked="checked" onclick="javascript:Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);" data-edost="{&quot;rate&quot;:&quot;330&quot;,&quot;code&quot;:&quot;16&quot;,&quot;tariff&quot;:1,&quot;service&quot;:&quot;0J\/QrdCaICjQtNC+INGC0LXRgNC80LjQvdCw0LvQsCwgMS00INC00L3Rjyk=&quot;}" id="edost_id_0" value="4">
*/


	if (stripos($shipping_method, 'edost_')!==false)
	 {
	   $dataa = OPCTransform::getFT2($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-edost');
	  

	 
	   
	   $test = 'edost_id_'.$id; 
	   if (stripos($shipmentid, $test)!==false)
	   {
		   $edost_id = (int)str_replace($test, '', $shipmentid); 
		   
	   }
	    
	  
	  
	  
	    if (empty($edost_saved_semafor))
	 {
	  $edost_saved = $session->get('edost', null, 'vm');
	  $edost_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('edost', $edost_saved, 'vm'); 
	 }
	 
	   if (!empty($dataa))
	    {
		 
		   
		   $dt = reset($dataa); 
		  $data = @json_decode($dt, true); 
		  
		  
		   $tx = @base64_decode($data['service']); 
		   if (!empty($tx))
		   {
			  // $data['service'] = $tx; 
		   }
		 
		  if (!empty($data))
		   {
			   
			  
				   JRequest::setVar('edost', $edost_id); 
			   
			   
			  
		
			
			   
			 
		     JRequest::setVar('edost_name', (string)$data['service']); 
			 JRequest::setVar('edost_rate', (float)$data['rate']);
			 JRequest::setVar('edost_rate-'.$uid, (float)$data['rate']); 
			 $service = base64_decode($data['service']); 
			 $service = html_entity_decode($service, ENT_COMPAT, 'UTF-8'); 
			 
			 
			 
			 $service = base64_encode($service); 
			 
			 JRequest::setVar('edost_name', (string)$service);
			 JRequest::setVar('edost_name-'.$uid, (string)$service);
			
			
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
	 // end support edost