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


	// pickup or delivery opc plugin: 
	
	 if (stripos($shipping_method, 'data-pickup=')!==false)
	 {
	  $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data*');
	  
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('service', (string)$data['service']);
			 
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	}
	// pickup or delivery opc plugin end