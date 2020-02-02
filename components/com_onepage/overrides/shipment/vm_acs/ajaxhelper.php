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

	 // support for ACS
	if (stripos($shipping_method, 'acs_')!==false)
	 {
	    if (empty($acs_saved_semafor))
	 {
	  $acs_saved = $session->get('acs_rates', null, 'vm');
	  $acs_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('acs_rates', $acs_saved, 'vm'); 
	 }
	 
	 unset($_SESSION['load_fedex_prices_from_session']); 
	   $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-acs');
	  
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('acs_rate', (string)$data['id']);
			
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	 }
	 // end support ACS
	