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

class OPCorder {
   
   
   public static function createNewOrder($products, $productsdata, $user_id, $order_status, &$returnMsg, $debug=false, &$code=1, &$order_id=0, $virtuemart_shipmentmethod_id=0, $virtuemart_paymentmethod_id=0, $coupon_code='')
   {
      if (php_sapi_name() !== 'cli') {
		 $returnMsg = 'Access denied - use CRON to access php directly!'; 
		 $code = 22; 
		 return; 
	  }
	  
	  if ($debug) {
		  cliHelper::debug(' Creating new order from products data'); 
	  }
	  
	  $todo = array(); 
	  $p_key = -1; 
	  foreach ($products as $product_id=>$quantity) {
		  $p_key++; 
	      
		  if (empty($product_id)) continue; 
		  if (empty($quantity)) continue; 
		  
		  if (isset($productsdata[$product_id])) {
			  $attribute = $productsdata[$product_id]; 
		  }
		  else 
		  {
			  $attribute = array(); 
		  }
		  
		   if (!empty($attribute)) {
	    $custom = array(); 
	    $custom[$product_id] = $attribute; 
	   }
       else {
	    $custom = ''; 
	   }
	  
	  $virtuemart_product_ids = array(); 
	  $virtuemart_product_ids[$p_key] = $product_id; 
	  $quantityData = array(); 
	  $quantityData[$p_key] = $quantity; 
	  
	  $todo[$p_key] = new stdClass(); 
	  $todo[$p_key]->virtuemart_product_ids = $virtuemart_product_ids;
	  $todo[$p_key]->custom = $custom;
	  $todo[$p_key]->quantity = $quantityData; 
		  
		  
	  }
	  
	  
	   if ($debug) {
		   if (!empty($todo))
		  foreach ($todo as $p_key => $data) {
		     cliHelper::debug(' Preparing data, cart key:'.$p_key.' product_id:'.$data->virtuemart_product_ids[$p_key].' custom attributes:'.var_export($data->custom, true).' quantity: '.$data->quantity[$p_key]); 
		  }
	  }
	    if (empty($todo)) {
				$returnMsg = 'No products found!'; 
				$code = 76; 
				return; 
		}
	   self::createorder($todo, $user_id, $order_status, $returnMsg, $debug, $code, $order_id, $virtuemart_shipmentmethod_id, $virtuemart_paymentmethod_id, $coupon_code);
   }
   
   private static function createOrder($todo, $user_id, $order_status, &$returnMsg, $debug=false, &$code=1, &$order_id=0, $virtuemart_shipmentmethod_id=0, $virtuemart_paymentmethod_id=0, $coupon_code='')
   {
       $user_id = (int)$user_id; 
	   if (empty($user_id)) {
		   $returnMsg = 'Empty user_id !'; 
		   $code = 79; 
		   return; 
	   }
	  JFactory::getUser()->load($user_id); 
	  
	   if (!defined('K_TCPDF_THROW_EXCEPTION_ERROR'))
	   define('K_TCPDF_THROW_EXCEPTION_ERROR', true); 
 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
	  
	   if ($debug) {
		  
		  cliHelper::debug(' Before loading VM...'); 
	   }
	  
	  if (!class_exists('VmConfig')) {
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		  VmConfig::loadConfig(); 
		}
		
		if (!class_exists('VirtueMartCart'))
	    require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	
		if (!class_exists('CouponHelper'))
	    require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'coupon.php');
	
	  
	  if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
	  
	  
	  
	    $cart = VirtueMartCart::getCart(); 
		if (!empty($coupon_code)) {
			$cart->coupon_code = $coupon_code; 
		}
		
		
		
	  if (class_exists('cliHelper')) {
	   cliHelper::setErrorReporting($debug); 
	  }
	  
	  if ($debug) {
		  
		  cliHelper::debug(' After loading VM...'); 
	   }
	  
	  
	  $BT = OPCLoggedShopper::getUserInfos($user_id, 'BT'); 
	  if (empty($BT)) {
		  $returnMsg = 'Empty primary BT address !'; 
		  $code = 115; 
		  return; 
	  }
	  
	  
	  
	  $ST = OPCLoggedShopper::getUserInfos($user_id, 'ST'); 
	  $RD = OPCLoggedShopper::getUserInfos($user_id, 'RD'); 
	  
	  
	  $date = JFactory::getDate();
	  $created_on = $date->toSQL();
	  
	  $BT['created_on'] = $ST['created_on'] = $RD['created_on'] = $created_on; 
	  if (!empty($order_status)) {
	   $BT['order_status'] = $ST['order_status'] = $RD['order_status'] = $order_status; 
	  }
	  
	  $cart->BT = $BT; 
	  if (!empty($ST)) {
	    $cart->ST = $ST; 
		$cart->STsameAsBT = false; 
	  }
	  else {
		  $cart->ST = $BT; 
	  }
	  
	  if (!empty($RD)) {
	    $cart->RD = $RD; 
		$cart->RDopen = true; 		
	  }
	  else {
		  $render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
	      $switch_rd = OPCconfig::get('opc_switch_rd', false); 
	      $skip = array('virtuemart_userinfo_id', 'virtuemart_order_userinfo_id', 'virtuemart_user_id', 'address_type'); 
	      if (!empty($render_in_third_address)) {
	     
	      $opc_btrd_def = OPCconfig::get('opc_btrd_def', false); 
	
	
	if (((empty($cart->RD)) && (!empty($cart->ST))) && (empty($opc_btrd_def))) {
		$cart->RD = $cart->ST; 
		$cart->RDopen = true; 		
	}
	else
	if ((empty($cart->RD)) && (!empty($cart->BT))) {
		$cart->RD = $cart->BT;
		$cart->RDopen = true; 		
	}
		  
		  
		  
	  }
	  
	  }
	  
	  
	  
	  
	  
	  
	  
	  $db = JFactory::getDBO(); 
	  foreach ($todo as $p_key => $data) {
		  
		 $virtuemart_product_ids = $data->virtuemart_product_ids; 
		 
		 $product_id = reset($virtuemart_product_ids); 
		 if (empty($product_id)) continue; 
		 
		 $product_id = (int)$product_id; 
		 $q = 'select `virtuemart_product_id` from `#__virtuemart_products` where `virtuemart_product_id` = '.(int)$product_id.' limit 0,1'; 
		 $db->setQuery($q); 
		 $product_id = $db->loadResult(); 
		 
		 if (empty($product_id)) {
			$returnMsg = 'Product could not be loaded !'; 
			$code = 194; 
		    return; 
		 }
		 $product_id = (int)$product_id; 
		 $productModel = VmModel::getModel('product'); 
	     $tmpProduct = $productModel->getProduct($product_id, true, true,true,1);
		 // tmpProduct may not return false on non-existent product !
	     if ((empty($tmpProduct)) || (empty($tmpProduct->virtuemart_product_id))) {
	        $returnMsg = 'Product could not be loaded !'; 
			$code = 188; 
		    return; 
	      }
		 
		 
		 
		 JRequest::setVar('quantity', $data->quantity); 
		 JRequest::setVar('customProductData', $data->custom); 
		 $s = ''; 
	     $ret = $cart->add($virtuemart_product_ids, $s); 
		 if (!empty($s)) {
			 
			 $returnMsg = 'Error adding product to the cart: '.(int)$product_id; 
			 $code = 200; 
			 return; 
			 
		 if ($debug) {
		    cliHelper::debug($s); 
		 }
		 
		 }
	  }
	  
	  
	  
	  
	   
	if (defined('VM_VERSION') && (VM_VERSION >= 3))
	{
	 ob_start(); 
	 $cart->prepareCartData(); 
	 $zz = ob_get_clean(); 
	 
	}
	
	
	
	 if ($debug) {
		  
		  foreach ($cart->products as $p_key => $product) {
			  
		     cliHelper::debug(' Cart created, cart key:'.$p_key.' product_id:'.$product->virtuemart_product_id.' quantity: '.$product->quantity); 
		  }
		  $bts = 'cart->BT - '; 
		  foreach ($cart->BT as $k=>$v) {
			  $bts .= $k.':'.$v.' '; 
		  }
		  $bts .= "\n"; 
		  cliHelper::debug($bts); 
		  if (!empty($cart->ST)) {
		  $bts = 'cart->ST - '; 
		  foreach ($cart->ST as $k=>$v) {
			  $bts .= $k.':'.$v.' '; 
		  }
		  $bts .= "\n"; 
		  cliHelper::debug($bts); 
		  }
		  else {
			  cliHelper::debug(' No ST Address found !');   
		  }
		  
		  if (!empty($cart->RD)) {
		  $bts = 'cart->RD - '; 
		  foreach ($cart->RD as $k=>$v) {
			  $bts .= $k.':'.$v.' '; 
		  }
		  $bts .= "\n"; 
		  cliHelper::debug( $bts ); 
		  }
		  else {
			  cliHelper::debug(' No Third Address found !');   
		  }
		  
	  }
	  
	
	
	
	  
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'cart_override.php'); 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	  
		
		// load user first
	 if ($debug) {
		 cliHelper::debug(' Setting zero ID payment and shipping '); 
	 }
	 
	 $cart->virtuemart_shipmentmethod_id = $virtuemart_shipmentmethod_id; 
	 $cart->virtuemart_paymentmethod_id = $virtuemart_paymentmethod_id; 
	 
	 $OPCcheckout = new OPCcheckout($cart); 
	 
	 self::doCalc($cart, $OPCcheckout); 
	 self::couponHandler($cart->coupon_code, $cart->cartData, $cart->cartPrices);  
	 self::doCalc($cart, $OPCcheckout); 
	 
	 OPCcheckout::$current_cart =& $cart; 
	  
	 if ($debug) {
		 cliHelper::debug(' Before importing system plugins '); 
	 }
	  
	 JPluginHelper::importPlugin('system'); 
	 
	 
	 if ($debug) {
		 cliHelper::debug(' After importing system plugins '); 
	 }
	 
	 //important - NO VALIDATION IS DONE VIA CLI !
	
 
	 try {
	 $OPCcheckout->confirmedOrder(true);
	 }
	 catch (Exception $e) {
		  if (empty(OPCcheckout::$new_order)) {
		  $err = 'OPC CLI: Error creating order !'; 
		  if ($debug) {
			  
			  $returnMsg = $err.$e; 
			  $code = 301; 
			  return; 
		  }
		  }
		  else {
			  //order was created, but something else went wrong... 
			  if (!empty(OPCcheckout::$new_order['details']['BT'])) {
			   
			   $order_id = $orderID = (int)$order['details']['BT']->virtuemart_order_id; 
			   if ($debug) {
			     $returnMsg = 'Order '.(int)$order_id.' was created, but something else went wrong ! '.$e; 
			   }
			   else {
				   $returnMsg = 'Order '.(int)$order_id.' was created, but something else went wrong ! '; 
			   }
			   $code = 0; 
			  }
		  }
		 
	 }
	 
		if ($debug) {
		self::showMsgs(); 
		}
		$order =& OPCcheckout::$new_order; 
			 
	    if (!empty($order)) {
				$orderID = $order['details']['BT']->virtuemart_order_id; 
				if (empty($returnMsg)) {
			      $returnMsg = 'OPC CLI: New order created: '.(int)$orderID; 
				}
			  
		if (!defined('VM_VERSION') || (VM_VERSION < 3)) {
		if (!empty($order_status))
		self::updateOrderStatus($orderID, $order_status); 
		}
		   if ((!empty($orderID)) && (!empty($order['details']['BT']))) {
		    $code = 0; 
		    $order_id = $orderID; 
		   }
		   else {
			   $code = 329; 
			   $returnMsg = 'Order not created properly'; 
		   }
		}
		else {
			  $code = 331; 
			  $returnMsg = 'OPC CLI: Error creating order !'; 
		}
		
		
		if ($debug) {
			cliHelper::debug(' After order created event'); 
		}
		
   }
   //returns false on error, this function is intended to be used in CLI !!! 
   public static function createOrderFromOrderLine($order_items_id, $user_id=0, $order_status='', &$returnMsg='', $debug=false, &$code=1, &$order_id=0, $virtuemart_shipmentmethod_id=0, $virtuemart_paymentmethod_id=0) 
   {
	   
	   
	  if (php_sapi_name() !== 'cli') {
		  $returnMsg = 'Access denied - use CRON to access php directly!'; 
		  $code = 22; 
		  return; 
	  }
	   
	   if ((!is_array($order_items_id)) || (empty($order_items_id))) { 
		  $returnMsg = 'Empty order_item_id !'; 
		  $code = 341;
		  return false; 
	  
	   }
	   
	   $ip_address = ''; 
	  
	  $todo = array(); 
	  
	  
	  
	  $prev_order_id = 0; 
	  $prev_user_id = 0; 
	  
	  $p_key = -1; 
	   $db = JFactory::getDBO(); 
	  foreach ($order_items_id as $order_item_id=>$quantity) {
	  $p_key++; 
	 
	  $q = 'select * from `#__virtuemart_order_items` where `virtuemart_order_item_id` = '.(int)$order_item_id.' limit 0,1'; 
	  $db->setQuery($q); 	  
	  $order_item = $db->loadAssoc(); 
	  
		
		 if (empty($order_item_id)) continue; 
		  if (empty($quantity)) continue; 
	  
	  if (empty($order_item)) {
		  $returnMsg = 'Missing order item line in the DB !'; 
		  $code = 368;
		  return false; 
	  }
	  if (empty($quantity)) {
	     $quantity =  (float)$order_item['product_quantity']; 
	  }
	  
	  $order_id =  (int)$order_item['virtuemart_order_id'];  
	  
	  if (!empty($prev_order_id) && ($order_id != $prev_order_id)) {
		   $returnMsg = 'Mixing more orders !'; 
		   $code = 379;
		   return false; 
	  }
	  
	  $prev_order_id = $order_id; 
	  
	  $q = 'select * from `#__virtuemart_order_userinfos` where `virtuemart_order_id` = '.(int)$order_id.' limit 0,1'; 
	  $db->setQuery($q); 	  
	  $order = $db->loadAssoc(); 
	  if (empty($order)) {
	    $returnMsg = 'Missing order information in the DB !'; 
		return false; 
	  }
	  
	  if (empty($ip_address)) {
	      $q = 'select ip_address from `#__virtuemart_orders` where `virtuemart_order_id` = '.(int)$order_id.' limit 0,1'; 
	      $db->setQuery($q); 	  
	      $ip_address = $db->loadResult(); 
		  if (!empty($ip_address)) $_SERVER['REMOTE_ADDR'] = $ip_address; 
	  }
	  
	  
	  $user_id_order = (int)$order['virtuemart_user_id']; 
	  if (empty($user_id_order)) {
	    $user_id_order = (int)$user_id; 
	  }
	 
	  
	  if (empty($user_id_order)) {
	    $returnMsg = 'User not found !'; 
		$code = 410; 
		return false; 
	  }
	  
	   if (!empty($prev_user_id) && ($user_id_order != $prev_user_id)) {
		   $returnMsg = 'Mixing more users !'; 
		   $code = 416;
		   return false; 
	   }
	   $prev_user_id = $user_id_order; 
	  
	  $attribute = $order_item['product_attribute']; 
	  if (!empty($attribute)) $attribute = json_decode($attribute, true); 
	  
	  $product_id = (int)$order_item['virtuemart_product_id']; 
	  
	   if (empty($product_id)) continue; 
	 
	  
	  $q = 'select `virtuemart_product_id` from `#__virtuemart_products` where `virtuemart_product_id` = '.(int)$product_id.' limit 0,1'; 
		 $db->setQuery($q); 
		 $product_id = (int)$db->loadResult(); 
	  
	   if (empty($product_id)) continue; 
	 
	   
	   if (!empty($attribute)) {
	    $custom = array(); 
	    $custom[$product_id] = $attribute; 
	   }
       else {
	    $custom = ''; 
	   }
	  
	  $virtuemart_product_ids = array(); 
	  $virtuemart_product_ids[$p_key] = $product_id; 
	  $quantityData = array(); 
	  $quantityData[$p_key] = $quantity; 
	  
	  $todo[$p_key] = new stdClass(); 
	  $todo[$p_key]->virtuemart_product_ids = $virtuemart_product_ids;
	  $todo[$p_key]->custom = $custom;
	  $todo[$p_key]->quantity = $quantityData; 
	  
	  
	  
	  
	  }
	  if (empty($todo)) {
				$returnMsg = 'No products found!'; 
				$code = 576; 
				return; 
		}
	  self::createorder($todo, $user_id_order, $order_status, $returnMsg, $debug, $code, $order_id,$virtuemart_shipmentmethod_id, $virtuemart_paymentmethod_id); 
	 
   }
   
   public static function updateOrderStatus($order_id, $order_status, $notified=0) {
	   
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
				$modelOrder = OPCmini::getModel('orders');
				//$order_id = $order['details']['BT']->virtuemart_order_id; 
				VirtueMartControllerOpc::emptyCache(); 
				$lastOrder = $modelOrder->getOrder($order_id); 
				$lastOrder['customer_notified'] = $notified;
				if (!isset($lastOrder['comments'])) $lastOrder['comments'] = ''; 
				if (isset($lastOrder['details']['BT']))
				{
			    $lastOrder['order_status'] = $order_status;
				$order_id = $lastOrder['details']['BT']->virtuemart_order_id; 
				}
				if (!empty($order_id))
				$modelOrder->updateStatusForOneOrder($order_id, $lastOrder, false);
	 
   }
   public static function showMsgs() {
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
	   $msgs = VirtueMartControllerOpc::prepareClearMsgs(); 
	   
	   $m = array(); 
	   foreach ($msgs as $k=>$v) {
		   if (is_array($v)) {
			   foreach ($v as $k2=>$v2) {
				   if (is_string($v2)) {
				     $m[$v2] = $v2; 
				   }
				   else 
					   if (is_array($v2)) {
						   foreach ($v2 as $k3=>$v3) {
							   if (is_string($v2)) {
										$m[$v3] = $v3; 
									}
						   }
					   }
			   }
		   }
		   else {
			   if (is_string($v)) $m[$v] = $v; 
		   }
	   }
	  if (!empty($m)) {
	  
	  foreach ($m as $msg) {
		  cliHelper::debug($msg); 
	  }
	  }
	   
   }
   
   
   public static function couponHandler($_code='', &$cartData, &$cartPrices) {

		JPluginHelper::importPlugin('vmcoupon');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmCouponHandler', array($_code,&$cartData, &$cartPrices));
		if(!empty($returnValues)){
			foreach ($returnValues as $returnValue) {
				if ($returnValue !== null  ) {
					return $returnValue;
				}
			}
		}
   }
   
   public static function doCalc(&$cart, &$OPCcheckout) {
	   
	   JPluginHelper::importPlugin('vmcoupon');
	   JPluginHelper::importPlugin('vmpayment');
	   JPluginHelper::importPlugin('vmshipment');
	   JPluginHelper::importPlugin('system');
	   
	   
	   $calc = calculationHelper::getInstance(); 
	    if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		  else $vm2015 = false; 
			if ($vm2015)
			{
			 $calc->setCartPrices(array()); 
			}
			
			
			
			
		//  $cart->pricesUnformatted = $prices = $calc->getCheckoutPrices(  $cart, false, 'opc');
		$OPCcheckout->storedPrices = $cart->cartPrices = $cart->pricesUnformatted = $prices = OPCloader::getCheckoutPrices(  $cart, false, $vm2015, 'opc' ); //$calc->getCheckoutPrices(  $cart, false, 'opc');
			if (method_exists($calc, 'getCartData'))
			{
	          $cart->cartData = $OPCcheckout->OPCCartData = $calc->getCartData();
			}
			else
			{
				$OPCcheckout->OPCCartData = $cart->cartData; 
			}
			
	    return $prices; 
   }
}