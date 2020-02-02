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
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCPluginHelper {
  
  public static function getPluginElement($type, $vmid, $extra=false)
  {
	 if (strpos($vmid, ':')!==false) {
	    $a = explode(':', $vmid); 
		foreach ($a as $test) {
		  if (is_numeric($test)) {
		    $vmid = (int)$test; 
			break; 
		  }
		}
	 }
	  
	  
    $db = JFactory::getDBO(); 
	if ($extra)
	$q = 'select * from `#__virtuemart_'.$db->escape($type).'methods` where `virtuemart_'.$db->escape($type).'method_id` = '.(int)$vmid.' limit 0,1'; 
	else
	$q = 'select `'.$db->escape($type).'_element` from `#__virtuemart_'.$db->escape($type).'methods` where `virtuemart_'.$db->escape($type).'method_id` = '.(int)$vmid.' limit 0,1'; 
	$db->setQuery($q); 
	if ($extra)
	 {
	   $res = $db->loadAssoc(); 
	   if (!empty($res)) return $res; 
	   else return array(); 
	 }
	 else 
	 {
	  $res = $db->loadResult(); 
	  return $res; 
	 }
	
  }

 public static function getPluginData(&$cart)
 {
   
	
   $dispatcher = JDispatcher::getInstance();
   $data = array(); 
   $object = new stdClass(); 
   $object->id = ''; 
   $object->data = ''; 
   $object->where = ''; 
   $returnValues = $dispatcher->trigger('plgGetOpcData', array(&$data, &$cart, $object));   
   return $data; 
 }

 public static function getPayment(&$ref, &$OPCloader, &$num, $ajax=false, $isexpress=false)
 {
	
	 
	 if ($isexpress)
	 {
	    $reta = array(); 
		$reta['html'] = '<input type="hidden" name="virtuemart_paymentmethod_id" value="'.$ref->cart->virtuemart_paymentmethod_id.'" />'; 
		$reta['extra'] = ''; 
		return $reta;
	 }
	 
	 
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
    	$payment_not_found_text='';
		$payments_payment_rates=array();
		

		require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
		
		if (class_exists('op_languageHelper'))
		{
			$cv = get_class($ref); 
		
		if (($cv === 'VirtuemartCartView') && (!$ref->checkPaymentMethodsConfigured())) {


		if (method_exists($renderer, 'assignRef'))
		{
			$renderer->assignRef('paymentplugins_payments', $payments_payment_rates);
			$payment_m = ''; 
			$renderer->assignRef('found_payment_method', $payment_m);
		}
		}
		}
		
			 $session = JFactory::getSession(); 
		$data = $session->get('opc_fields', '', 'opc'); 
		if (empty($data)) $data = array(); 
		else
		$data = @json_decode($data, true); 
		if (!empty($data['p_id']))
			$payment_default = $data['p_id']; 

		
		$p = JRequest::getVar('payment_method_id', $payment_default);
		
		if (empty($p))
		$selectedPayment = empty($ref->cart->virtuemart_paymentmethod_id) ? 0 : $ref->cart->virtuemart_paymentmethod_id;
		else $selectedPayment = $p; 
		
		// set missing fields for klarna
		OPCloader::prepareBT($ref->cart); 
		
		$dpps = array(); 
		
		$shipping = JRequest::getVar('shipping_rate_id', ''); 
		
		
		//if ($ajax)
		if (!empty($shipping))
		if (!empty($disable_payment_per_shipping))
		{
		
		$session = JFactory::getSession(); 
		$dpps = $session->get('dpps', null); 
		if (empty($dpps))
		 $OPCloader->getShipping($ref, $ref->cart, true); 
		$dpps = $session->get('dpps', null); 
		if (!empty($dpps))
		 {
		   
		 }
		}
		// 
		if (!empty($shipping))
		{
		  if (!empty($shipping))
		$ref->cart->virtuemart_shipmentmethod_id=$shipping; 
		
		$vm2015 = false; 
	$ref->cart->prices = $ref->cart->cartPrices = $ref->cart->pricesUnformatted = OPCloader::getCheckoutPrices(  $ref->cart, false, $vm2015, 'opc');
		}
		
		
		
		if (empty($ref->cart->cartPrices))
		{
			$vm2015 = false; 
			$ref->cart->prices = $ref->cart->cartPrices = OPCloader::getCheckoutPrices($ref->cart, false, $vm2015); 
		}
		$paymentplugins_payments = array();
		
		if ($p === 'none') $p = 0; 
		
		if ($payment_default === 'none')
		{
		   $paymentplugins = array(); 
		    $psel = '<input onclick="javascript: Onepage.runPay(\'\',\'\',op_textinclship, op_currency, 0)" type="radio" name="virtuemart_paymentmethod_id" id="payment_id_0"   value="0" not_a_valid_payment="not_a_valid_payment" '; 
			if ((empty($p) || ($p === 'none'))) {
			$psel .= ' selected="selected" '; 
			$payment_default = 0; 
			}
			$psel .= ' />
<label for="payment_id_0"><span class="vmpayment"><span class="vmpayment_name">-'.OPCLang::_('COM_VIRTUEMART_CART_EDIT_PAYMENT').'- </span></span></label>'; 
		 
$paymentplugins[] = $psel; 

		}
		
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'ajaxhelper.php'); 
		$bhelper = new basketHelper;			
		//$bhelper->createDefaultAddress($ref, $ref->cart); 	 
		//old: 2.0.208 and prior: $returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array($ref->cart, $selectedPayment, &$paymentplugins_payments));
		//plgVmDisplayListFEPaymentOPCNocache
		
		//
		$stopen = JRequest::getVar('shiptoopen', 0); 
		if ($stopen === 'false') $stopen = 0; 
		if (empty($stopen)) 
		{
		$sa = JRequest::getVar('sa', ''); 
		if ($sa == 'adresaina') $stopen = 1; 
		}
		
		if (empty($stopen))
        {
			//$ref->cart->ST = 0; 
		}
		
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPaymentOPCNocache', array( &$ref->cart, $selectedPayment, &$paymentplugins_payments));
		
		
		
		if (empty($returnValues))
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array( $ref->cart, $selectedPayment, &$paymentplugins_payments));

		
		
		
		// if no payment defined
		$found_payment_method = false;
		$n = 0; 
		$debug = ''; 
		
		
		foreach ($paymentplugins_payments as $p1)
		if (is_array($p1))
		$n += count($p1);
		
		if ($n > 0) $found_payment_method = true;
		
		
		$num = $n; 
		
		
		
		
		
		
		if (!$found_payment_method) {
			$link=''; // todo
			$payment_not_found_text = OPCLang::sprintf('COM_VIRTUEMART_CART_NO_PAYMENT_METHOD_PUBLIC', '<a href="'.$link.'">'.$link.'</a>');
		}
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'ajaxhelper.php'); 
    $bhelper = new basketHelper; 
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
	$ret = array(); 
	if ($found_payment_method) {
		
		$sorted = array();
		$unknown = array(); 
		
		//reorder plugins first: 
		foreach ($paymentplugins_payments as $paymentplugin_payments) {
		    if (is_array($paymentplugin_payments)) {
			foreach ($paymentplugin_payments as $paymentplugin_payment) {
			    if (empty($paymentplugin_payment)) continue; 
			    $id = OPCTransform::getFT($paymentplugin_payment, 'input', 'virtuemart_paymentmethod_id', 'name', 'virtuemart_paymentmethod_id', '>', 'value');				
				
				if (empty($id))
				{
					continue; 
				}
				
				if (is_array($id)) $id = reset($id); 
				
				
				if (empty($id))
				{
					continue; 
				}
				
				if (is_numeric($id))
				{
				 $ind = (int)$id;	
				 
				 $paymentplugin_payment = '<div id="opc_payment_wrap_'.$ind.'" class="opc_payment_wrap opc_payment_wrap_'.$ind.'">'.$paymentplugin_payment.'</div>'; 
				 
				 if (empty($sorted[$ind]))
				 $sorted[$ind] = $paymentplugin_payment;
				 else $unknown[] = $paymentplugin_payment;

				}
				else
				{
					$ind = $id; //reset($id); 
				if (is_numeric($ind))
				{
				 //$ind = (int)$ind;	
				 
				 $paymentplugin_payment = '<div id="opc_payment_wrap_'.$ind.'" class="opc_payment_wrap opc_payment_wrap_'.$ind.'">'.$paymentplugin_payment.'</div>'; 
				 
				 if (empty($sorted[$ind]))
				 $sorted[$ind] = $paymentplugin_payment;
				 else $unknown[] = $paymentplugin_payment;
				}
				else {
					$paymentplugin_payment = '<div id="opc_payment_wrap_unknown" class="opc_payment_wrap opc_payment_wrap_unknown">'.$paymentplugin_payment.'</div>'; 
					$unknown[] = $paymentplugin_payment;
				}
				}
			}
		}
		}
		$ret2 = array(); 
		if (!empty($sorted))
		{
		 $dbj = JFactory::getDBO(); 
		 $dbj->setQuery("select * from #__virtuemart_paymentmethods where published = '1' order by ordering asc limit 999"); 
		 $list = $dbj->loadAssocList(); 
		 $msg = $dbj->getErrorMsg(); 
		 if (!empty($msg)) { echo $msg;  }
		 $sortedfinal = array(); 
		 if (!empty($list))
		 {
		 foreach ($list as $pme)
		  {
		    if (!empty($sorted[$pme['virtuemart_paymentmethod_id']]))
			$sortedfinal[] = $sorted[$pme['virtuemart_paymentmethod_id']];
		
		  }
		  if (empty($unknown)) $unknown = array(); 
		  if (!empty($sortedfinal))	  
		  $ret2 = array_merge($sortedfinal, $unknown); 
		  
	      $paymentplugins_payments  = array($ret2); 
	  
		  }
		  }
		
		
		if (!empty($payment_inside))
		{	 
		 
		 $ret2 = OPCTransform::paymentToSelect($paymentplugins_payments, $shipping, $dpps);
		 if (!empty($ret2))
		 {
		  $ret = array($ret2['select']); 
		  $extra = $ret2['extra']; 
		  /*
		  foreach ($ret['extra'] as $key=>$val)
		   {
		     $extra[$key] = $val; 
		   }
		   */
		   
		   
		 }
		
		 
		}
		
		
		
		if (empty($payment_inside) || (empty($ret)))
		{
		
		
		 $ret = array(); 
		foreach ($paymentplugins_payments as $paymentplugin_payments) {
		    if (is_array($paymentplugin_payments)) {
			foreach ($paymentplugin_payments as $paymentplugin_payment) {
				
				$id = OPCTransform::getFT($paymentplugin_payment, 'input', 'virtuemart_paymentmethod_id', 'name', 'virtuemart_paymentmethod_id', '>', 'value');				
				
				if (is_array($id)) $id = reset($id); 
				
				$paymentplugin_payment = str_replace('class="vmpayment_description"', 'class="vmpayment_description vmpayment_description_'.$id.'"', $paymentplugin_payment); 
				//vmpayment_cardinfo
				$paymentplugin_payment = str_replace('class="vmpayment_cardinfo"', 'class="vmpayment_cardinfo vmpayment_cardinfo_'.$id.'"', $paymentplugin_payment); 
				//ccDetails
				$paymentplugin_payment = str_replace('class="ccDetails"', 'class="ccDetails ccDetails_'.$id.'"', $paymentplugin_payment); 
				
			 OPCloader::opcDebug('checking shipping '.$shipping, 'payment 2 tranform'); 
				 OPCloader::opcDebug('dpps:', 'payment 2 tranform'); 
				 OPCloader::opcDebug($dpps, 'payment 2 tranform'); 
				 OPCloader::opcDebug('dpps_disable:', 'payment 2 tranform'); 
				 OPCloader::opcDebug($dpps_disable, 'payment 2 tranform'); 
				if (!empty($shipping))
				if (!empty($dpps))
				if (!empty($disable_payment_per_shipping))
				{
				  foreach ($dpps_disable as $k=>$v)
				   {
				     if (!empty($dpps[$k]))
					 foreach ($dpps[$k] as $y=>$try)
					 {
					 
				     if ((int)$dpps[$k][$y] == (int)$shipping)
				     if ($dpps_disable[$k] == $id)
					 {
					 OPCloader::opcDebug('disabling payment id '.$id.' for shipping id '.$shipping, 'payment 2 tranform'); 
					 $paymentplugin_payment = ''; 
					 continue 3; 
					 }
					 }
				   }
				}
				// PPL Pro fix
				$paymentplugin_payment = str_replace('<br/><a href="'.JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment&Itemid=' . JRequest::getInt('Itemid'), false).'">'.JText::_('VMPAYMENT_PAYPAL_CC_ENTER_INFO').'</a>', '', $paymentplugin_payment); 
				$paymentplugin_payment = str_replace('name="virtuemart_paymentmethod_id"', 'name="virtuemart_paymentmethod_id" onclick="javascript: Onepage.runPay(\'\',\'\',op_textinclship, op_currency, 0)" ', $paymentplugin_payment); 
			    $ret[] = $paymentplugin_payment;
				
				
				if (($n === 1) && (!empty($hide_payment_if_one)))
				 {
				 
				   
				    $paymentplugin_payment = str_replace('type="radio"', 'type="hidden"', $paymentplugin_payment);  
				 }
				
			
				
			}
		    }
		}
		
		
		
    
		  }
    } else {
	 $ret[] = $payment_not_found_text.'<input type="hidden" name="virtuemart_paymentmethod_id" id="opc_missing_payment" value="0" />';
    }
	
	if (empty($payment_inside))
	if (!empty($ret))
		if (!empty($paymentplugins))
		{
		   $ret = array_merge($paymentplugins, $ret); 
		   
		   
		}
	
	
	
	   $vars = array('payments' => $ret, 
				 'cart'=> $ref->cart, );
				

		require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
		//return $renderer->fetch($ref, $template, $vars, $new); 
	   $html = $renderer->fetch($ref, 'list_payment_methods.tpl', $vars); 
	   
 $pid = JRequest::getVar('payment_method_id', ''); 
 
 
 if (!empty($pid) && (is_numeric($pid)))
 {
 if (strpos($html, 'value="'.$pid.'"')!==false)
 {
 
	$html = str_replace('checked="checked"', '', $html); 
	$html = str_replace(' checked', ' ', $html); 
	$html = str_replace('value="'.$pid.'"', 'value="'.$pid.'" checked="checked" ', $html); 
 }
 
 }
 else
 if (strpos($html, 'value="'.$payment_default.'"')!==false)
 {
	$html = str_replace('checked="checked"', '', $html); 
	$html = str_replace(' checked', ' ', $html); 
	$html = str_replace('value="'.$payment_default.'"', 'value="'.$payment_default.'" checked="checked" ', $html); 
 }
 
 $html = str_replace('"radio"', '"radio" autocomplete="off" ', $html); 
 
 if (!$payment_inside)
 if (strpos($html, 'checked')===false) 
 {
   
	$x1 = strpos($html, 'name="virtuemart_paymentmethod_id"');
	if ($x1 !== false)
	 {
	    $html = substr($html, 0, $x1).' checked="checked" '.substr($html, $x1); 
	 }
	 else
	 {
	    // we've got no method here !
	 }
	  
 }
 // klarna compatibility
 $count = 0; 
 $html = str_replace('name="klarna_paymentmethod"', 'name="klarna_paymentmethod_opc"', $html, $count);
 
 $html .= '<input type="hidden" name="opc_payment_method_id" id="opc_payment_method_id" value="" />';  
 if ($count>0)
 if (!defined('klarna_opc_id'))
 {
 $html .= '<input type="hidden" name="klarna_opc_method" id="klarna_opc_method" value="" />'; 
 
 
 define('klarna_opc_id', 1); 
 }
   
   $reta = array(); 
   $reta['html'] = $html; 
   if (!empty($extra)) $reta['extra'] = $extra; 
   else $reta['extra'] = ''; 
   return $reta;
 }

 
  public static function triggerSystemPlugin($event, $args) {
		    
			$dispatcher = JDispatcher::getInstance();
			
			$plgs = array(); 
			$dispatcher->trigger('plgVmDetachVmPlugins', array(&$plgs));			
			
			
			if (!empty($plgs))
			foreach ($plgs as &$instance)
			{
		      $dispatcher->detach($instance); 
			}
			

			// re-attach events: 
			// stAn - we only dettached plgVmConfirmedOrder function
			// we detached all VMplugins, now we can run system plugins
			// important: they cannot further run shipment/payment plugins (display) because they are detached
			JPluginHelper::importPlugin('system'); 
			$returnValues = $dispatcher->trigger($event, $args);
			
			if (!empty($plgs))
			foreach ($plgs as &$instance)
			{
			 $dispatcher->attach($instance); 
			}
			
			
			
	  
  }
 
  
}