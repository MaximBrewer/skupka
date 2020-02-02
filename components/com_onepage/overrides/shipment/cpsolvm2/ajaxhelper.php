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

	// canada post: 
	
	 // canada post: 
	 if (stripos($shipping_method, 'cpsol_')!==false)
	 {
	 
	  if (empty($cpsol_saved_semafor))
	 {
	  $cpsol_saved = $session->get('cpsol_service', null, 'vm');
	  $cpsol_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('cpsol_service', $cpsol_saved, 'vm'); 
	 }
	 
	 
	 /*
	 <input type="radio" name="cpsol_radio" class="js-change-cpsol" data-cpsol="{&quot;name&quot;:&quot;Regular&quot;,&quot;rate&quot;:13.3,&quot;shippingDate&quot;:&quot;2014-06-20&quot;,&quot;deliveryDate&quot;:&quot;2014-06-24&quot;,&quot;deliveryDayOfWeek&quot;:&quot;3&quot;,&quot;nextDayAM&quot;:&quot;false&quot;,&quot;packingID&quot;:&quot;P_0&quot;,&quot;zeroRate&quot;:0}" id="cpsol_id_0" value="0">
	 */

 $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-cpsol');
 $value = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'value');	  

 $vmvalue = OPCTransform::getFT($shipping_method, 'input', 'virtuemart_shipmentmethod_id', 'type', 'hidden', '>', 'value');	  
 
 $vm_id = $vmvalue[0]; 
 
 
 
if (stripos($shipping_method, 'cpsol_radio')!==false)
{
  $shipping_method = str_replace(' checked="checked"', ' ', $shipping_method); 
  //virtuemart_shipmentmethod_id
  if (!defined('only_once_cp'))
  {
  $shipping_method = str_replace('virtuemart_shipmentmethod_id"', 'virtuemart_shipmentmethod_id_old"', $shipping_method); 
  define('only_once_cp', 1); 
  $shipping_method .= '<div style="display: none;" class="hidden_radio"><input type="radio" multielement="cpsol_radio" name="virtuemart_shipmentmethod_id" value="'.$vm_id.'" id="shipment_id_'.$vm_id.'" /></div>'; 
  }
  
  /*
  $js = '
<script type="text/javascript">  
//<![CDATA[
  function selectCspol()
   {
      var d = document.getElementById(\'shipment_id_'.$vm_id.'\'); 
	  if (jQuery != \'undefined\') jQuery(\'shipment_id_'.$vm_id.'\').click(); 
	  else
	  d.onclick(); 
   }
//]]>
</script>
  ';
*/  
  $shipping_method = str_replace('name="cpsol_radio"', 'name="cpsol_radio" onclick="selectCspol('.(int)$vm_id.');"', $shipping_method); 
  //$shipping_method = str_replace('cpsol_radio', 'virtuemart_shipmentmethod_id', $shipping_method); 
  /*
  foreach ($value as $cs_val)
  {
   $shipping_method = str_replace('id="cpsol_id_' . $cs_val . '"   value="' . $cs_val . '"', 
   'id="cpsol_id_' . $cs_val . '"   value="'.$vm_id.'"', $shipping_method);
  }
  */
  
}


	 

	   if (!empty($dataa))
	    {

		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		   $rate_id = str_replace('cpsol_id_', '', $shipmentid); 
		   
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('cpsol_name', (string)$data['name']);
			 JRequest::setVar('cpsol_rate', (string)$data['rate']);
			 JRequest::setVar('cpsol_shippingDate', (string)$data['shippingDate']);
			 JRequest::setVar('cpsol_deliveryDate', (string)$data['deliveryDate']);
			 JRequest::setVar('cpsol_zero_rate', (string)$data['zeroRate']);
			 JRequest::setVar('cpsol_radio', $rate_id); 
			 
			 $data['cpsol_radio'] = $rate_id; 
			 $newdata = json_encode($data); 
			 
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($newdata).'"/>';
			 
			 
		   }
		}
	
		
	}
	
	