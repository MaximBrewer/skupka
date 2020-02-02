<?php    
/**
 * Overrided portion of cart class for OPC2 on Virtuemart 2
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 *
 */

 // Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


class OPCcheckout 
{
     //public static $_triesValidateCoupon;
    var $cartProductsData = array();
	//public static $_triesValidateCoupon = 0;
	var $storedPrices = array(); 
	var $OPCCartData = array(); 
	var $useSSL = 1;
	var $url; 
	 var $prices = null;
	 var $pricesUnformatted = null;
	 var $pricesCurrency = null;
	 public static $opc_cart = null; 
     function __construct(&$cart) {
	 
		//self::$_triesValidateCoupon=0;
		self::$opc_cart =& $cart;
		self::$current_cart =& $cart; 
		/*
		foreach ($cart as $key => &$val)
		 {
		   $this->{$key} = $val; 
		 }
		 */
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 				
			$userFieldsModel = OPCmini::getModel('Userfields'); 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');
							  
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		return; 
	    
		
		
	}
	
	static $current_cart; 
	static $opc_controller; 
	static $new_order; 
	
	function checkoutData($redirect=true) {
	    
	

		
		$cart =& self::$current_cart; 


		
		$this->_redirect = false;
		$this->_inCheckOut = true;
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		$cart->_inCheckOut = true;
		
		
		
		
		if (!isset($cart->tosAccepted)) $cart->tosAccepted = 1; 
		$cart->tosAccepted = JRequest::getInt('tosAccepted', $cart->tosAccepted);


		if (!isset($cart->tos)) $cart->tos = 1; 
		$cart->tos = JRequest::getInt('tos', $cart->tosAccepted);

		
		if (!isset($cart->customer_comment)) $cart->customer_comment = ''; 
		
		$cart->customer_comment = JRequest::getVar('customer_comment', $cart->customer_comment);
		if (empty($cart->customer_comment))
		{
		  $cart->customer_comment = JRequest::getVar('customer_note', $cart->customer_comment);
		}
		
		$op_disable_shipto = OPCloader::getShiptoEnabled($cart); 
		if (empty($op_disable_shipto))
		{
			$shipto = JRequest::getVar('shipto', null); 
			
		if ($shipto != 'new')
		if (($cart->selected_shipto = $shipto) !== null) {
			//JModel::addIncludePath(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models');
			
			$userModel = OPCmini::getModel('user'); //JModel::getInstance('user', 'VirtueMartModel');
			$stData = $userModel->getUserAddressList(0, 'ST', $cart->selected_shipto);
			if (isset($stData[0]))
			$this->validateUserData('ST', $stData[0], $cart);
		}
		}
		else
		{
		    $cart->STsameAsBT = 1;
			$cart->ST = $cart->BT;
		}
		
		
		
		
		$cart->setCartIntoSession();

		$mainframe = JFactory::getApplication();
		
		if (isset($cart->cartProductsData))
		$count = count($cart->cartProductsData); 
		else $count = count($cart->products); 
		
		$qerrors = array(); 
		$redirectMsg = false; 
		
		
		
		
		if ($count == 0) {
			
			
			
			
			
		    $error = 'Error 120: '.OPCLang::_('COM_VIRTUEMART_CART_NO_PRODUCT'); 
			OPCloader::storeError($error); 
			
			
			
			
			
			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = OPCLang::_('COM_VIRTUEMART_CART_NO_PRODUCT'); 
			
			$this->redirect(JRoute::_($this->url, false,  VmConfig::get('useSSL', false)), $error);
		} else {
			//adjusted code for special stock handling
			if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
		  
		  $redirectMsg = ''; 		  
		  foreach ($cart->cartProductsData as $k=>&$p) {
		 
	      $adjustQ = false; 
		  $o_q = (float)$p['quantity']; 
		  $quantity = (float)$p['quantity']; 
		 
		   
		   $pid = $p['virtuemart_product_id']; 
		   $productModel = VmModel::getModel('product');
	       $productTemp = $productModel->getProduct($pid);
		   
		   //$res = $OPCcheckout->checkForQuantities($productTemp, $p['quantity'], $e, $adjustQ); 
		    $ret = $this->checkForQuantities($productTemp, $quantity, $redirectMsg, $adjustQ, true);
		    

			
				if ($ret === false)
				{
					$qerrors[$redirectMsg] = $redirectMsg; 
				}
			
		   }
		
		}
		else {
		$redirectMsg = ''; 
		foreach ($cart->products as &$p) {
		 
	     
		 $product = $p; 
		 $adjustQ = false; 
		 $o_q = (float)$product->quantity; 
		 $quantity = (float)$product->quantity; 
		 
		 $ret = $this->checkForQuantities($product, $quantity, $redirectMsg, $adjustQ, true);
		 if ($quantity != $o_q) {
				  if ($adjustQ) {
					  $product->quantity = $quantity; 
				      $cart->setCartIntoSession();
				  }
				}
				
				if ($ret === false)
				{
					$qerrors[$redirectMsg] = $redirectMsg; 
				}
		 
		}
		}
		
		//end adjusted code for special stock handling
			/*original code
			foreach ($cart->products as &$product) {
				$redirectMsg = ''; 
				$adjustQ = false; 
				
				$o_q = (float)$product->quantity; 
				$quantity = (float)$product->quantity; 
				
				$ret = $this->checkForQuantities($product, $quantity, $redirectMsg, $adjustQ, true);
				
				if ($quantity != $o_q) {
				  if ($adjustQ) {
					  $product->quantity = $quantity; 
				      $cart->setCartIntoSession();
				  }
				}
				
				if ($ret !== true)
				{
					$qerrors[] = $redirectMsg; 
				}
				
			}
			
			*/
			
		}

		if (!empty($qerrors)) {
					$redirectMsg = implode("<br />\n", $qerrors); 
					
					//$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), 'Error 135: '.$redirectMsg);
					//return false; 
					
					
					$error = 'Error 175: Quantity error ' ; 
					
					OPCloader::storeError($error.$redirectMsg); 
					$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
					if (empty($debug_plugins)) $error = $redirectMsg; 

					
					$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
					
				}

		
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		
		
		//But we check the data again to be sure
		if (empty($cart->BT)) {
			
			
		    $error = 'Error 147: Cart Bill To is empty' ; 
			OPCloader::storeError($error); 
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = 'Cart Bill To is empty'; 
			
			
			$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
			
		} else {
			
			$redirectMsg = $this->validateUserData('BT', null, $cart);
			
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			$redirectMsg2 = $this->validateUserData('cart', null, $cart);
			if (!empty($redirectMsg2))
			{
				if (!empty($redirectMsg)) $redirectMsg .= '<br />'; 
				$redirectMsg .= $redirectMsg2; 
			}
		}
			
			
		}
	
		if($cart->STsameAsBT!==0){
			$cart->ST = $cart->BT;
		} else {
			//Only when there is an ST data, test if all necessary fields are filled
			if (!empty($cart->ST)) {
				$redirectMsg3 = $this->validateUserData('ST', null, $cart);
			
				if ($redirectMsg3) {
					
					if (!empty($redirectMsg)) $redirectMsg .= '<br />'; 
					$redirectMsg .= $redirectMsg3; 
					
					//				$cart->setCartIntoSession();
					//$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), 'Error 169: '.$redirectMsg);
				}
			}
		}
		
		
		$default = new stdClass(); 
	 $default->enabled = false; 
     $config = OPCconfig::getValue('opc_delivery_date', '', 0, $default, true); 
	 if (!empty($config->enabled))
	  {
		   if (!empty($config->required))
		   {
			   
			   $dt = JRequest::getVar('opc_date_picker_store'); 
			   if (isset($_POST['opc_date_picker_store']))
			   if (empty($dt))
			   {
				   $redirectMsg4 = OPCLang::_('COM_ONEPAGE_CHOOSE_DESIRED_DELIVERY_DATE_ERROR');
				   if (!empty($redirectMsg)) $redirectMsg .= '<br />'; 
				   $redirectMsg .= $redirectMsg4; 
			   }
		   }
	  }
	  
	  
	  if ($redirectMsg) {
		    
		    $error = 'Error 156: '.$redirectMsg;
			OPCloader::storeError($error); 
			
			
			
			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = $redirectMsg; 

			
				$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
			}


		// Test Coupon
		$shipment = $cart->virtuemart_shipmentmethod_id; 
		$payment = $cart->virtuemart_paymentmethod_id; 
		
		

		//2.0.144: $prices = $cartClass->getCartPrices();
		
		
		$cart->virtuemart_shipmentmethod_id = $shipment; 
		$cart->virtuemart_paymentmethod_id = $payment; 
		
		//2.0.144 added
		if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		$calc = calculationHelper::getInstance();
		
		  
		  if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		  else $vm2015 = false; 
			if ($vm2015)
			{
			 $calc->setCartPrices(array()); 
			}
			
			
			if (method_exists($calc, 'getCartData'))
			{
	          $this->OPCCartData = $calc->getCartData();
			}
			else
			{
				$this->OPCCartData = $calc->_cartData; 
			}
			
		//  $cart->pricesUnformatted = $prices = $calc->getCheckoutPrices(  $cart, false, 'opc');
		$this->storedPrices = $cart->cartPrices = $cart->pricesUnformatted = $prices = OPCloader::getCheckoutPrices(  $cart, false, $vm2015, 'opc' ); //$calc->getCheckoutPrices(  $cart, false, 'opc');

 		
		
		$third = JRequest::getVar('third_address_opened', false); 
		if (!empty($third)) {
			
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_address.php'); 
		    $missinga = OPCthirdAddress::validateThirdAddress($cart);
			$cart->RDopen = true; 
		
			
			if (!empty($missinga)) {
			$errorMsg = 'OPC Error Code 347, '.implode('<br />',$missinga); 
			OPCloader::storeError($errorMsg); 
					  
			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			
			if (empty($debug_plugins)) $errorMsg = implode('<br />',$missinga);
					  
					  
					$this->redirect(JRoute::_($this->url, false,  VmConfig::get('useSSL', false)), $errorMsg);  
					  
			}	
		
		}
		
		
		// Check if a minimun purchase value is set
		if (($msg = $this->checkPurchaseValue($prices)) != null) {
			
		    $error = 'Error 205: '.$msg;
			OPCloader::storeError($error); 
			
			 
			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = $msg; 
			
			$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
		}
		
		$zeroSubtotal = false; 
		$zero_p = $zero_p_orig = OPCConfig::get('default_payment_zero_total', 0);  
		$zero_p = (int)$zero_p; 			 
		
		$force_zero_paymentmethod = OPCconfig::get('force_zero_paymentmethod', false); 
		if (empty($zero_p) && (!empty($force_zero_paymentmethod))) {
			$zero_p = JRequest::getInt('virtuemart_paymentmethod_id', 0); 
		}
		
		
		
		if (!empty($prices['billTotal']))
		{
		 $pX = floatval($prices['paymentValue']); 
		 $sX = 0; //floatval($prices['shipmentValue']); 
		 $testP = $pX + $sX; 
			
		 // special case for zero value orders, do not charge payment fee: 
		 if (($prices['billTotal'] == $pX) || ($prices['billTotal'] == $testP) || ($prices['billTotal'] == $sX))
		  {
		   $vm2015 = method_exists($calc, 'setCartPrices'); 
		   if ($vm2015)
			{
				$calc->setCartPrices(array()); 
			}
		   
		   $cart->virtuemart_paymentmethod_id = "-0"; 
		   $saved_id = $cart->virtuemart_shipmentmethod_id; 
		   $other = null; 
		   $prices = OPCloader::getCheckoutPrices($cart, false, $other);
		   if (is_null($prices))
		   {
			   $prices = $calc->_cart->cartPrices; 
		   }
		   
		   
			
		   if (!empty($zero_p)) {
		     $cart->virtuemart_paymentmethod_id = $zero_p; 
		   }
		   $cart->virtuemart_shipmentmethod_id = $saved_id;
		   $zeroSubtotal = true; 
		   
		 }
		 else {
			 //if bill total is not zero, make sure zero payment method cannot be selected: 
			 if (!empty($zero_p_orig)) {
				 if ($cart->virtuemart_paymentmethod_id == $zero_p_orig) {
					 $msg = OPCLang::_('COM_VIRTUEMART_CART_SETPAYMENT_PLUGIN_FAILED'); 
					 $error = 'Error 465: '.$msg;
					 OPCloader::storeError($error); 
					 $this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $msg);
				 }
			 }
			 
		 }
		 
		}
		else {
			
		   if (!empty($zero_p)) {
		    $cart->virtuemart_paymentmethod_id = $zero_p; 
		   }
		    $zeroSubtotal = true; 
		   
		}
		
		
		
		//2.0.144:end
		
		

		if (!empty($cart->couponCode)) {
			
			if (!class_exists('CouponHelper')) {
				require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'coupon.php');
			}
			
			$redirectMsg2 = CouponHelper::ValidateCouponCode($cart->couponCode, $prices['salesPrice']);
			/*
			stAn: OPC will not redirect the customer due to incorrect coupons here
			if (!empty($redirectMsg)) {
				$cart->couponCode = '';
				//				$this->setCartIntoSession();
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$cart->useXHTML,$cart->useSSL), $redirectMsg);
			}
			*/
		}
	  
	  
	  
	  
	  
		//Test Shipment and show shipment plugin
	    $op_disable_shipping = OPCloader::getShippingEnabled($cart); 
		if (empty($op_disable_shipping))
		{
		if (empty($cart->virtuemart_shipmentmethod_id)) {
			
			$redirectMsg = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE'); 
			
		    $error = 'Error 258: '.$redirectMsg; 
			OPCloader::storeError($error); 

			
			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = $redirectMsg; 

			
			$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
		} else {
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			//Add a hook here for other shipment methods, checking the data of the choosed plugin
			$dispatcher = JDispatcher::getInstance();
			$msg = ''; 
			
			$session = JFactory::getSession(); 
			$session->set('opc_in_trigger', true); 
			$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataShipmentOPC', array(  &$cart, &$msg));
			$session->set('opc_in_trigger', false); 
			
			foreach ($retValues as $retVal) {
				if ($retVal === true) {
					break; // Plugin completed succesful; nothing else to do
				} elseif ($retVal === false) {
					// Missing data, ask for it (again)
					$redirectMsg = $msg.OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE'); 
					
					$error = 'Error 272: '.$redirectMsg; 
					OPCloader::storeError($error); 

			

			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = $msg.'<br />'.OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE');

					
					$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
					// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
				}
			}
		}
		}
		

		
		
		
		// some shipping uset payment: 
		$cart->virtuemart_paymentmethod_id = $payment; 
	
		//echo 'hier ';
		//Test Payment and show payment plugin
		
		//$prices = $cart->cartPrices;  
		
		$total = (float)$prices['billTotal']; 
		
		
		
		if (($total > 0) && (empty($zeroSubtotal)))
		{
		if (empty($cart->virtuemart_paymentmethod_id)) {
			$redirectMsg = OPCLang::_('COM_VIRTUEMART_CART_NO_PAYMENT_SELECTED'); 
			
		    $error = 'Error 290: '.$redirectMsg;
			OPCloader::storeError($error); 

			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = $redirectMsg;
			
			$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
			
			
		} else {
			if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmpsplugin.php');
			JPluginHelper::importPlugin('vmpayment');
			//Add a hook here for other payment methods, checking the data of the choosed plugin
			$dispatcher = JDispatcher::getInstance();
			
			
			$session = JFactory::getSession(); 
			$session->set('opc_in_trigger', true); 

			$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataPayment', array( $cart));
			$session->set('opc_in_trigger', false); 
			
		$session = JFactory::getSession ();
		$sessionKlarna = $session->get ('Klarna', 0, 'vm');
		
			$redirectMsg = ''; 
			foreach ($retValues as $retVal) {
				if ($retVal === true) {
					break; // Plugin completed succesful; nothing else to do
				} elseif ($retVal === false) {
				
				$msg = JFactory::getSession()->get('application.queue');; 
				$msgq1 = JFactory::getApplication()->get('messageQueue', array()); 
		        $msgq2 = JFactory::getApplication()->get('_messageQueue', array()); 
				
				$res = array_merge($msg, $msgq1, $msgq2);
				$msg = $res; 
				
				if (empty($msg)) {
					$msg = $redirectMsg = OPCLang::_('COM_VIRTUEMART_CART_SETPAYMENT_PLUGIN_FAILED'); 
				}
				
				if (!empty($msg) && (is_array($msg)))
				$redirectMsg = implode('<br />', $msg); 
					
					
		    $error = 'Error 323: '.$redirectMsg;
			OPCloader::storeError($error); 
			


			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) {
				//if nothing in queue:
				if (empty($res)) {
					$error = $redirectMsg; 
				}
				else {
				  //queue will be shown: 
				  $error = '';
				}
			}
			
			//note, in core VM there is no message shown from here, the plugins should handle their own error messages
			
		     $this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
					// Missing data, ask for it (again)
					//$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', false, VmConfig::get('useSSL', false)), 'Error 323: '.$redirectMsg);
				// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
				}
			}
			
			
			
			
			
		}
		}
		else
		{
			 
			
		    $cart->virtuemart_paymentmethod_id = $zero_p; 
		}

		//restore the prices to the first calculation: 
		$cart->cartPrices = $prices; 
	
	
	
	// some payments unset shipping: 
	$shipment = $cart->virtuemart_shipmentmethod_id; 
		
		
			
	
		if (VmConfig::get('agree_to_tos_onorder', 1))
		{
		if (empty($cart->tosAccepted)) {
			
			$required = OPCUserFields::getIfRequired('agreed');
			if(!empty($required)){
				
		    $error = 'Error 339: '.OPCLang::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS');
			OPCloader::storeError($error); 
			

			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = OPCLang::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS');
			
			$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
				
				
			}
		}
		}
		/* stAn: 2.0.231: registered does not mean logged in, therefore we are going to disable this option with opc, so normal registration would still work when activation is enabled 
		if (empty($GLOBALS['is_dup']))
		if(VmConfig::get('oncheckout_only_registered',0)) {
			$currentUser = JFactory::getUser();
			if(empty($currentUser->id)){
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', false, VmConfig::get('useSSL', false)), OPCLang::_('COM_VIRTUEMART_CART_ONLY_REGISTERED') );
			}
		 }
		 */


		//Show cart and checkout data overview
		
		$cart->_inCheckOut = false;
		$cart->_dataValidated = true;

		$cart->setCartIntoSession();

		return true;
	}
	
	
	function doCurl($order)
	{
		
	 if (!function_exists('curl_multi_exec')) return; 
	 $ch = array(); 
	  include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	  if (!empty($curl_url) && (is_array($curl_url)))
	   {
	     $i = 0; 
	     foreach ($curl_url as $blink)
		  {
		   $i++; 
		   
		    $link = @base64_decode($blink); 
			if (strpos($link, 'http')===0)
			 {
			   	if (!function_exists('curl_init'))
				 return; 
		
			if (isset($order->email))
			$link = str_replace('{email}', $order->email, $link); 
			
			if (isset($order->first_name))
			$link = str_replace('{first_name}', $order->first_name, $link); 
			
			if (isset($order->last_name))
			$link = str_replace('{last_name}', $order->last_name, $link); 
			
			if (isset($order->virtuemart_order_id))
			$link = str_replace('{order_id}', $order->virtuemart_order_id, $link); 
			
			foreach ($order as $key=>$search)
			{
			  if (is_string($search))
			  $link = str_replace('{'.$key.'}', $search, $link); 
			}
			
			//$link = str_replace('{amount}', $order['details']->
			
		
			// http://arguments.callee.info/2010/02/21/multiple-curl-requests-with-php/
		
		    $ch[$i] = null; 
			$ch[$i] = curl_init($link); 
			$url = $link;
			curl_setopt ($ch[$i], CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, 0); 
			curl_setopt($ch[$i], CURLOPT_URL,$url); // set url to post to
			curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER,1); // return into a variable
			curl_setopt($ch[$i], CURLOPT_TIMEOUT, 4000); // times out after 4s
			curl_setopt($ch[$i], CURLOPT_POST, 0); 
			curl_setopt($ch[$i], CURLOPT_ENCODING , "gzip");
			curl_setopt($ch[$i], CURLOPT_CUSTOMREQUEST, 'GET');
		  }
		  }
		
		  $mh = curl_multi_init();
		  if (!empty($ch))
		  foreach ($ch as $key=>$v)
		   {
		     // build the multi-curl handle, adding both $ch
			curl_multi_add_handle($mh, $ch[$key]);
		   }
		   
		   // execute all queries simultaneously, and continue when all are complete
			$running = null;
			$start = microtime(true); 
			
			do {
			
				curl_multi_exec($mh, $running);
				$now = microtime(true); 
				if (($now-$start) > ($adwords_timeout / 1000)) 
				 {
				 $running = false;
				 break 1; 
				 }
				
			} while ($running);
		  
		 

	

			 }
		  
	   
	}
	
   
	
	private function getModifiedData(&$cart, $restore=false)
	{
		static $saved_cart_data; 
		
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 	
		if (!$restore)  
		{
		if (empty($cart->couponCode)) $cart->couponCode = ''; 
		$saved_cart_data['couponCode'] = $cart->couponCode; 
		
		$cart->couponCode = strip_tags($cart->couponCode); 
		$cart->couponCode = str_replace("\r\r\n", '', $cart->couponCode); 
		$cart->couponCode = str_replace("\r\n", '', $cart->couponCode); 
		$cart->couponCode = str_replace("\n", '', $cart->couponCode); 
		$cart->couponCode = trim($cart->couponCode); 
		
		if (!empty($custom_rendering_fields))
		{
			if ($opc_cr_type == 'save_none')
			{
				foreach ($custom_rendering_fields as $fname)
				{
					if (isset($cart->BT[$fname])) {
					
					if (!isset($saved_cart_data['BT'])) $saved_cart_data['BT'] = array(); 
					$saved_cart_data['BT'][$fname] = $cart->BT[$fname]; 
					
					
					$cart->BT[$fname] = ''; 
					}
					if (!empty($cart->ST))
					{
					 /*
					 if (isset($cart->ST['shipto_'.$fname])) 
					 {
					  if (!isset($saved_cart_data['ST'])) $saved_cart_data['ST'] = array(); 
					  $saved_cart_data['ST']['shipto_'.$fname] = $cart->ST['shipto_'.$fname]; 
					  $cart->ST['shipto_'.$fname] = ''; 
					 }
					 */
					 if (isset($cart->ST[$fname])) 
					  {
					  if (!isset($saved_cart_data['ST'])) $saved_cart_data['ST'] = array(); 
					  $saved_cart_data['ST'][$fname] = $cart->ST[$fname]; 
					  $cart->ST[$fname] = ''; 
					  }
					}
					
				}
			}
			else return;
		}
		else return; 
		}
		if ($restore)
		{
			$cart->couponCode = $saved_cart_data['couponCode']; 
			if (!empty($custom_rendering_fields))
			{
			if ($opc_cr_type == 'save_none')
			{
				if (empty($saved_cart_data)) return;
				if (!empty($saved_cart_data['ST']))
				foreach ($saved_cart_data['ST'] as $fname=>$val)
				{
					$cart->ST[$fname] = $val; 
				}
				if (!empty($saved_cart_data['BT']))
				foreach ($saved_cart_data['BT'] as $fname=>$val)
				{
					$cart->BT[$fname] = $val; 
					
					
				}

			}
			}
		}
	}
	function getModifiedOrder(&$order, &$cart)
	{
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 	
		if (!empty($custom_rendering_fields))
		{
			if ($opc_cr_type == 'save_none')
			{
				
				foreach ($custom_rendering_fields as $fname)
				{
					$order['details']['BT']->$fname = $cart->BT[$fname]; 
					if (!empty($order['details']['ST']))
					if (!empty($cart->ST))
					{



					 if (isset($cart->ST['shipto_'.$fname]))
					 if (isset($order['details']['ST']))
					 if (isset($order['details']['ST']->$key)) $order['details']['ST']->$key = $cart->ST['shipto_'.$fname];
				     if (isset($cart->ST[$fname]))
					 if (isset($order['details']['ST']))
					 if (isset($order['details']['ST']->$fname)) 
					 $order['details']['ST']->$fname = $cart->ST[$fname]; 
					}
					
				}
			}
			else return;
		}
		else return; 
		
	}
	/**
	 * This function is called, when the order is confirmed by the shopper.
	 *
	 * Here are the last checks done by payment plugins.
	 * The mails are created and send to vendor and shopper
	 * will show the orderdone page (thank you page)
	 *
	 */
	//function confirmedOrder(&$cart, $ref, &$order) {
	function confirmedOrder($novalidation=false) {
	
	    $cart =& self::$current_cart; 
		 
		

		 
		 $payment_id = $cart->virtuemart_paymentmethod_id; 
		 

		
		 
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		//Just to prevent direct call
		if (($cart->_dataValidated && $cart->_confirmDone) || ($novalidation)) {
			
			$orderModel = OPCmini::getModel('Orders'); 
			

			$this->getModifiedData($cart); 
			if (!isset($delivery_date))
			 {
			    $dt = JRequest::getVar('delivery_date', ''); 
				if (!empty($dt)) $delivery_date = $dt; 
				
				$dt = JRequest::getVar('opc_date_picker_store', ''); 
				if (!empty($dt)) 
				{
				$delivery_date = $dt; 
				JRequest::setVar('delivery_date', $dt); 
				}
			 }
			 
			 if ((!empty($delivery_selector) && (!empty($delivery_date))))
			 {
				 JRequest::setVar($delivery_selector, $delivery_date); 
				 JRequest::setVar('shipto_'.$delivery_selector, $delivery_date); 
				 
				 $cart->BT[$delivery_selector] = $delivery_date; 
				 if (!empty($cart->ST)) $cart->ST[$delivery_selector] = $delivery_date; 
			 }
			 
			
			 
			if (!empty($delivery_date))
			 {
			   $cart->delivery_date = $delivery_date; 
			 }
	
	
			
        	OPCUserFields::checkCart($cart); 
			
			$order_reuse = OPCconfig::get('order_reuse_fix', false); 
			if ((empty($order_reuse)) || (empty($cart->reuseOrder))) {
			 $cart->virtuemart_order_id = null; 
			 $cart->_inConfirm = false;
			}
			else
			{
				if (empty($cart->reuseOrder)) {
					$cart->virtuemart_order_id = null; 
					$cart->_inConfirm = false;
				
				}
				else
				{
					$cart->_inConfirm = true;
				}
			}
			
			
			/*stAn: recalculate once more*/
			unset($cart->BT['couponCode']);//just in case due to the way VM stores stuff 
			if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
			$calc = calculationHelper::getInstance();
		
		  
		    if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		    else $vm2015 = false; 
			if ($vm2015)
			{
			 $calc->setCartPrices(array()); 
			}
			
			
			if (method_exists($calc, 'getCartData'))
			{
	          $this->OPCCartData = $calc->getCartData();
			}
			else
			{
				$this->OPCCartData = $calc->_cartData; 
			}
			$this->storedPrices = $cart->cartPrices = $cart->pricesUnformatted = $prices = OPCloader::getCheckoutPrices(  $cart, false, $vm2015, 'opc' ); //$calc->getCheckoutPrices(  $cart, false, 'opc');
			/*end calculation... */
			$orderID = $orderModel->createOrderFromCart($cart);
			
			
			VirtueMartControllerOpc::emptyCache(); 

			
			
			// only on recent vm : 
			if (defined('VM_VERSION'))
			if (!empty($orderID))
			if (!empty($delivery_date))
			 {
			    $db = JFactory::getDBO(); 
			    $q = 'update `#__virtuemart_orders` set `delivery_date` = "'.$db->escape($delivery_date).'" where virtuemart_order_id = '.(int)$orderID.' limit 1'; 
				$db->setQuery($q); 
				$db->query(); 
				
				
			 }
			 
			 		
			
			
			$this->getModifiedData($cart, true); 
			
			$msgq1 = JFactory::getApplication()->get('messageQueue', array()); 
		    $msgq2 = JFactory::getApplication()->get('_messageQueue', array()); 

			
			$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
			if ($op_disable_shipping)
			{
			  //$q = 'update #__virtuemart_orders set 
			}
			
			
			if (empty($orderID)) {
				$mainframe = JFactory::getApplication();
				
				
		    $error = 'Error 637: Order creation failed !';
			OPCloader::storeError($error); 
			

				


			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (empty($debug_plugins)) $error = 'Order creation failed';
			
				$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
				
				
			}
			
			$cart->virtuemart_order_id = $orderID;
			
			/*
			if (defined('VM_VERSION') && (VM_VERSION >= 3))
			{
				$order = $orderModel->getMyOrderDetails($orderID);
				
			}
			else
		    */
			
			$orderModel = OPCmini::getModel('Orders'); 
			
		    VirtueMartControllerOpc::emptyCache(); 			
			$order= $orderModel->getOrder($orderID);
			
			
			
			
			$db = $dbj = JFactory::getDBO(); 
			
			$this->getModifiedOrder($order, $cart); 
		
			
			//if (!empty($order['details']['BT']->$opc_vat_key))
			{
				  
				  if (OPCmini::tableExists('onepage_moss'))
				  {
					  $f = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vat.php'; 
					  require_once($f); 
					  
					  $vat_id = ''; 
					
					  $opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
					  if (!empty($opc_vat_key)) {
					  if (isset($order['details']['BT']->$opc_vat_key)) {
					    $vat_id = $order['details']['BT']->$opc_vat_key;
					  }
					  }
					  
					  $prices = $this->storedPrices; 
					  $taxes = $this->OPCCartData;
					  
					  $tax_data = array(); 
					  $tax_data['prices'] = $prices; 
					  $tax_data['taxes'] = $taxes; 
					  $payment_data = json_encode($tax_data);
					  
					  // will add additional information about the order
					  OPCvat::addOrderId($vat_id, $orderID, $payment_data); 
					
					 
				  }
			}
			
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('vmcalculation');
			$session = JFactory::getSession(); 
			$session->set('opc_in_trigger', true); 
			$retValues = $dispatcher->trigger('plgVmOnCheckoutStorePricesOPC', array( $order, $this->storedPrices, $this->OPCCartData));
			$session->set('opc_in_trigger', false); 

			// $GLOBALS['is_dup']
			if (!empty($orderID))
			{
			
			if (!empty($GLOBALS['is_dup']) && (is_numeric($GLOBALS['is_dup'])))
			{
			  $GLOBALS['is_dup'] = (int)$GLOBALS['is_dup']; 
			 
			  $q = "update `#__virtuemart_orders` SET `virtuemart_user_id` = '".(int)$GLOBALS['is_dup']."' where virtuemart_order_id = '".(int)$orderID."' limit 1"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 
			  
			  $dbj = JFactory::getDBO(); 
			  $q = "update #__virtuemart_order_userinfos SET virtuemart_user_id = '".(int)$GLOBALS['is_dup']."' where virtuemart_order_id = '".(int)$orderID."' limit 3"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 

			  
			  $e = $dbj->getErrorMsg(); 
			 
			}
			else
			{
			
			if (!empty($GLOBALS['opc_new_user']) && (is_numeric($GLOBALS['opc_new_user'])))
			{
			  $GLOBALS['opc_new_user'] = (int)$GLOBALS['opc_new_user'];   
			  
			  $q = "update #__virtuemart_orders SET virtuemart_user_id = '".(int)$GLOBALS['opc_new_user']."' where virtuemart_order_id = '".(int)$orderID."' limit 1"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 
			  
			  $dbj = JFactory::getDBO(); 
			  $q = "update #__virtuemart_order_userinfos SET virtuemart_user_id = '".(int)$GLOBALS['opc_new_user']."' where virtuemart_order_id = '".(int)$orderID."' limit 2"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 

			  
			  $e = $dbj->getErrorMsg(); 
			 
			}
			}

			
			
			}
			//opc_new_user
			if (isset($order['details']['ST']))
			{
			if (empty($order['details']['ST']->email) && (!empty($order['details']['BT']->email))) $order['details']['ST']->email = $order['details']['BT']->email;
			}
// 			$cart = $this->getCart();
			
			if (isset($order['details']['BT']))
			$this->doCurl($order['details']['BT']); 
			
			
// 			$html="";
			
			if ( $order['details']['BT']->order_total <= 0) $no_payment = true; 
			else $no_payment = false; 
			
			
			$opc_copy_bt_st = OPCconfig::get('opc_copy_bt_st', false); 
			if (!empty($opc_copy_bt_st)) {
				
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
				OPCLoggedShopper::copyBTintoST($order); 
			}
			
			
			 $third = JRequest::getVar('third_address_opened', false); 
			 $switch_rd = OPCconfig::get('opc_switch_rd', false); 
			 if ((!empty($cart->RDopen)) || (!empty($third)) || ($switch_rd)) {
					
					require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_address.php'); 
					
					OPCthirdAddress::storeThirdAddress($cart, $order, 0, true);
			  }	
			
			 
			
			$dispatcher = JDispatcher::getInstance();
			if (empty($op_disable_shipping))	
			JPluginHelper::importPlugin('vmshipment');
			JPluginHelper::importPlugin('vmcustom');
			if (empty($no_payment))
			JPluginHelper::importPlugin('vmpayment');

		
			JPluginHelper::importPlugin('vmcustom');
			JPluginHelper::importPlugin('vmcalculation');
		
			$session = JFactory::getSession();
			$return_context = $session->getId();
			
				
			
		
			
			
			
			
			
			
			//$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));
			
		    $defhtml = '<div id="thank_you_page_comes_here"></div>'; 
			JRequest::setVar ('html', $defhtml);
			
			$session = JFactory::getSession(); 
			$session->set('opc_in_trigger', true); 

			
			// pairs the cookie with the database 
			$returnValues = $dispatcher->trigger('plgOpcOrderCreated', array($cart, $order ));
								 		
			if (class_exists('OPCPluginLoaded'))
			{
			// runs shipping confirm as first and payment as last
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPC', array('shipment', $cart, $order));
			
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPC', array('calculation', $cart, $order));
			
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPC', array('custom', $cart, $order));
			
			// reload the order because it does not include the above trigger info
			
			
			// we used detach event above, so we can now freely call the function again: 
			//$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));
			
			
			
			unset($order['customer_notified']); 
			
			$email_fix1 = OPCconfig::get('email_fix1', false); 
			
			if (!empty($email_fix1))
			{
				$mt = array($orderModel, 'notifyCustomer'); 
			if (is_callable($mt))
			if (method_exists($orderModel, 'notifyCustomer'))
			{
				$saved_status = $order['details']['BT']->order_status; 
				$orderstatusForShopperEmail = VmConfig::get('email_os_s',array('U','C','S','R','X'));
			    $orderstatusForVendorEmail = VmConfig::get('email_os_v',array('U','C','R','X'));
			    //((in_array($saved_status,$orderstatusForVendorEmail) && (!in_array( $new_status, $orderstatusForVendorEmail)))))
			    //  && (!in_array( $new_status, $orderstatusForShopperEmail)))
			    
				
				
				if (in_array($saved_status,$orderstatusForShopperEmail)) 
			    {
					
				  $orderModel->notifyCustomer($orderID, $order);		
			      //$order['customer_notified'] = 1; 
			    }
			
			}
			}
			
			
		
			try {
			$except = array('shipment', 'custom', 'calculation'); 
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPCExcept', array($except, &$cart, &$order));			
			}
			catch (Exception $e) {
			  JFactory::getApplication()->enqueueMessage($e, 'error'); 
			}
			
			$dispatcher = JDispatcher::getInstance();
			//$dispatcher = JEventDispatcher::getInstance(); 
			
			if (!empty(vmPlugin::$detached))
			foreach (vmPlugin::$detached as &$instance)
			{
		      $dispatcher->detach($instance); 
			}
			
			// re-attach events: 
			// stAn - we only dettached plgVmConfirmedOrder function
			// we detached all VMplugins, now we can run system plugins
			// important: they cannot further run shipment/payment plugins (display) because they are detached
			JPluginHelper::importPlugin('system'); 
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));
			
			
			
			
			if (!empty(vmPlugin::$detached))
			foreach (vmPlugin::$detached as &$instance)
			{
			 $dispatcher->attach($instance); 
			}
			
			vmPlugin::$detached = array(); 
			
			
			
			}
			else
			{
				$mt = array($orderModel, 'notifyCustomer'); 
			
			   if (is_callable($mt))
			   if (method_exists($orderModel, 'notifyCustomer'))
			   $orderModel->notifyCustomer($orderID, $order);
				
				
				$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));
			}
			 $returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPCSystem', array($cart, $order));
			
			$session->set('opc_in_trigger', false); 
			
			$order_id = $order['details']['BT']->virtuemart_order_id;
			VirtueMartControllerOpc::emptyCache(); 
			
			ob_start(); 
			
			
		   
			
			if ($order['details']['BT']->order_status != $zero_total_status)
			if ( $order['details']['BT']->order_total <= 0) { 
				
				
				$order['order_status'] = $zero_total_status;
				$order['customer_notified'] = 1;
				$order['comments'] = '';
				$orderModel->updateStatusForOneOrder($orderID, $order, true);
                

				$cart->emptyCart();
			}
			$output = ob_get_clean(); 
			
			VirtueMartControllerOpc::emptyCache(); 
			
			$order = $orderModel->getOrder($order_id); 
			$new_status = $order['details']['BT']->order_status;
			
			
			/*
			$orderstatusForShopperEmail = VmConfig::get('email_os_s',array('U','C','S','R','X'));
			$orderstatusForVendorEmail = VmConfig::get('email_os_v',array('U','C','R','X'));
			if ((in_array($saved_status,$orderstatusForShopperEmail) && (!in_array( $new_status, $orderstatusForShopperEmail))) && 
			 ((in_array($saved_status,$orderstatusForVendorEmail) && (!in_array( $new_status, $orderstatusForVendorEmail)))))
			
			if ($new_status == $saved_status)
			{
				
			}
			*/
			
			self::$new_order =& $order; 
			
			
			
			VirtueMartControllerOpc::emptyCache(); 
			
			
			/*  OLD CODE: 
			$except = array('shipment', 'custom', 'calculation'); 
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrderOPCExcept', array($except, $cart, $order));			
			*/
			
			//OPC: maybe we want to send emil before a redirect: 
			if (!empty($send_pending_mail))
			{
			
			if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');

		//Important, the data of the order update mails, payments and invoice should
		//always be in the database, so using getOrder is the right method
		
		$orderModel=OPCmini::getModel('orders');
		

		$payment_name = $shipment_name='';
		$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
		
		$msgqx1 = JFactory::getApplication()->get('messageQueue', array()); 
		$msgqx2 = JFactory::getApplication()->get('_messageQueue', array()); 
		$msgqx3 = JFactory::getApplication()->getMessageQueue(); 
		
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
		if (empty($op_disable_shipping))
		JPluginHelper::importPlugin('vmshipment');
		if (empty($no_payment))
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		
		$session = JFactory::getSession(); 
		$session->set('opc_in_trigger', true); 
		
		if (empty($op_disable_shipping))
		$returnValues = $dispatcher->trigger('plgVmonShowOrderPrintShipment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_shipmentmethod_id, &$shipment_name));
		if (empty($no_payment))
		$returnValues = $dispatcher->trigger('plgVmonShowOrderPrintPayment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_paymentmethod_id, &$payment_name));
		
		$session->set('opc_in_trigger', false); 
		
		

		$order['shipmentName']=$shipment_name;
		if (empty($no_payment))
		$order['paymentName']=$payment_name; 
		else $order['paymentName']='';

		
		if (!isset($vars)) $vars = array(); 
		
		$vars['orderDetails']=$order;
		if (!isset($vars['newOrderData'])) $vars['newOrderData'] = array(); 
	    $vars['newOrderData']['customer_notified']=1;

	
		$vars['url'] = 'url';
		
		$vars['doVendor'] = false;
		
		if (!empty($order['details']['BT']->virtuemart_vendor_id))
		$virtuemart_vendor_id = $order['details']['BT']->virtuemart_vendor_id; 
		else
		$virtuemart_vendor_id=1;

		
		$vendorModel = OPCmini::getModel('vendor');
		$vendor = $vendorModel->getVendor($virtuemart_vendor_id);
		$vars['vendor'] = $vendor;
		$vendorEmail = $vendorModel->getVendorEmail($virtuemart_vendor_id);
		
		if (empty($vendorEmail)) 
		 {
		     $db = JFactory::getDBO ();
			
			  $query = 'SELECT * FROM `#__virtuemart_vmusers`'; 
			  $db->setQuery($query); 
			  $res = $db->loadAssocList(); 
					
						
			$query = 'SELECT ju.email FROM `#__virtuemart_vmusers` as vmu, `#__users` as ju WHERE `virtuemart_vendor_id`=' . (int)$virtuemart_vendor_id.' and ju.id = vmu.virtuemart_user_id and vmu.user_is_vendor = 1 limit 0,1';
			$db->setQuery ($query);
			$vendorEmail = $db->loadResult ();

		   
		   
		 }
		
		
		$vars['vendorEmail'] = $vendorEmail;

		
		$vendor_emails = OPCconfig::get('vendor_emails', ''); 
		
		if (!empty($vendor_emails)) { 
		$vendor_emails =  html_entity_decode($vendor_emails); 
		// Send the email
		if (file_exists(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'view.html.php'))
		{
		  $e = OPCmini::parseCommas($vendor_emails, false); 
		 
		  foreach ($e as $vendorEmail) {
		  if (empty($vendorEmail)) continue; 
		  if (stripos($vendorEmail, '@')===false) continue; 
		  $vars['vendorEmail'] = $vendorEmail;
		  
		 
		  if (OPCcheckout::renderMail('invoice', $vendorEmail, $vars, null, true, false)) {
		   //ok
		   }
		  }
		}
		}
		$x = JFactory::getApplication()->set('messageQueue', $msgqx1); 
	    $x = JFactory::getApplication()->set('_messageQueue', $msgqx2); 

		if (OPCJ3)
		if (class_exists('ReflectionClass'))
		{
		   $a = JFactory::getApplication(); 
		   $reflectionClass = new ReflectionClass($a);
		   $property = $reflectionClass->getProperty('_messageQueue'); 
		   $property->setAccessible(true);
		 
		   $property->setValue($a, $msgqx3);
		   $x = JFactory::getApplication()->getMessageQueue(); 
		   
		}
		
		
		
		
		
			
			}
			
			
			/*
			jimport( 'joomla.plugin.helper' );
			$plugin = JPluginHelper::getPlugin('vmpayment', 'opctracking');
			if (!empty($plugin))
			{
			   $opctracking = true; 
			}
			else 
			{
			   $opctracking = false; 
			}
			*/
			
			
			//$html = JRequest::getVar('html', OPCLang::_('COM_VIRTUEMART_ORDER_PROCESSED'), null, 'string', JREQUEST_ALLOWRAW); 
			$html = JRequest::getVar('html', OPCLang::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
			$app = JFactory::getApplication(); 
			
			
			
			$utm_p_load = false; 
			$default = array(); 
			$utm_p = OPCConfig::getValue('opc_config', 'utm_payments', 0, $default, false, false);
			if (!empty($payment_id))
			{
			    if (in_array($payment_id, $utm_p))
				$utm_p_load = true; 
			}
			
			if (method_exists($app, 'input'))
			{
			$html2 = $app->input->get('html', OPCLang::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'RAW' ); 

			
			
			
			
			if ($utm_p_load)
			{
			 $html2 = str_replace('&amp;view=', '&amp;utm_nooverride=1&view=', $html2); 
			 $html2 = str_replace('&view=', '&utm_nooverride=1&view=', $html2); 
			 $app->input->set('html', $html2); 
			}

			
			}
			else 
			{
			$html2 = JRequest::getVar('html', OPCLang::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
			if ($utm_p_load)
			{
			
			$html2 = str_replace('&view=pluginresponse', '&utm_nooverride=1&view=pluginresponse', $html2); 
			$html2 = str_replace('&amp;view=pluginresponse', '&amp;utm_nooverride=1&amp;view=pluginresponse', $html2); 
			JRequest::setVar('html', $html2); 
			}
			}
			
			
			
			
			if ($html != $html2) $output .= $html2; 
			$output .= $html; 	


			ob_start(); 
			if ( $order['details']['BT']->order_total <= 0) { 
				$cart->emptyCart();
			}
			$output .= ob_get_clean(); 
			
			$x = JFactory::getApplication()->set('messageQueue', $msgq1); 
			$x = JFactory::getApplication()->set('_messageQueue', $msgq2); 

			
			VirtueMartControllerOpc::emptyCache(); 
			
			
			if (!empty($output))
			return $output; 
			
			
			// may be redirect is done by the payment plugin (eg: paypal)
			// if payment plugin echos a form, false = nothing happen, true= echo form ,
			// 1 = cart should be emptied, 0 cart should not be emptied

		
	 }

	}
	
	
	function secondMail($order, $vendorEmail)
	{
	   //OPC: maybe we want to send emil before a redirect: 
			//if (!empty($send_pending_mail))
			
			
			if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');

		//Important, the data of the order update mails, payments and invoice should
				//always be in the database, so using getOrder is the right method
		
		$orderModel=OPCmini::getModel('orders');
		

		$payment_name = $shipment_name='';
		$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
		
		$msgqx1 = JFactory::getApplication()->get('messageQueue', array()); 
		$msgqx2 = JFactory::getApplication()->get('_messageQueue', array()); 
		$msgqx3 = JFactory::getApplication()->getMessageQueue(); 
		
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
		if (empty($op_disable_shipping))
		JPluginHelper::importPlugin('vmshipment');
		if (empty($no_payment))
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		
		$session = JFactory::getSession(); 
		$session->set('opc_in_trigger', true); 
		
		if (empty($op_disable_shipping))
		$returnValues = $dispatcher->trigger('plgVmonShowOrderPrintShipment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_shipmentmethod_id, &$shipment_name));
		if (empty($no_payment))
		$returnValues = $dispatcher->trigger('plgVmonShowOrderPrintPayment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_paymentmethod_id, &$payment_name));
		
		
		$session->set('opc_in_trigger', false); 
		

		$order['shipmentName']=$shipment_name;
		if (empty($no_payment))
		$order['paymentName']=$payment_name; 
		else $order['paymentName']='';

		if (!isset($vars)) $vars = array(); 
		
		$vars['orderDetails']=$order;
		if (!isset($vars['newOrderData'])) $vars['newOrderData'] = array(); 
	    $vars['newOrderData']['customer_notified']=1;

	
		$vars['url'] = 'url';
		
		$vars['doVendor'] = false;
		
		if (!empty($order['details']['BT']->virtuemart_vendor_id))
		$virtuemart_vendor_id = $order['details']['BT']->virtuemart_vendor_id; 
		else
		$virtuemart_vendor_id=1;

		
		$vendorModel = OPCmini::getModel('vendor');
		$vendor = $vendorModel->getVendor($virtuemart_vendor_id);
		$vars['vendor'] = $vendor;
		
		
		
		$vars['vendorEmail'] = $vendorEmail;

		

		// Send the email
		if (file_exists(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'view.html.php'))
		{
			
		  if (OPCcheckout::renderMail('invoice', $vendorEmail, $vars, null, true, false)) {
		   //ok
		  }
		
		}
	
	    $x = JFactory::getApplication()->set('messageQueue', $msgqx1); 
	    $x = JFactory::getApplication()->set('_messageQueue', $msgqx2); 

		if (OPCJ3)
		if (class_exists('ReflectionClass'))
		{
		   $a = JFactory::getApplication(); 
		   $reflectionClass = new ReflectionClass($a);
		   $property = $reflectionClass->getProperty('_messageQueue'); 
		   $property->setAccessible(true);
		 
		   $property->setValue($a, $msgqx3);
		   $x = JFactory::getApplication()->getMessageQueue(); 
		   
		}
	
	
	}
	
	function adjustQuantitySteps($product, &$quantity, $down=false) {
		if (!empty($product->step_order_level)) {
		  $quantity = (float)$quantity; 
		  $step = (float)$product->step_order_level; 
		  if (!empty($step)) {
		  if (!empty($product->max_order_level))
		  $max = (float)$product->max_order_level; 
	      else
		  $max = PHP_INT_MAX; 
		  if (!empty($product->min_order_level)) {
		   $min = (float)$product->min_order_level; 
		  }
		  else $min = 0; 
		  
		  if (empty($min)) $min = $step; 
		   
		   $rx2 = $quantity / $step; 
		   $rx = round($rx2); 
		   $rx3 = ceil($rx2); 
		   if (!$down) {
		   
		   if ($rx != $rx2) {
			 // quantity was raised to upper step
		     $quantity = $rx3 * $step; 
		   }
			   
		   if ($rx < 1) {
			   // quantity was raised to min step
		     $quantity = $step; 
		   }
		   if ((!empty($min)) && ($quantity < $min) ) {
			 // quantity was raised
		     $quantity = $min; 
		   }
		  
		   }
		   else
		   {
			   if ($rx != $rx2) {
					// quantity was lowered to bottom quantity step
					$quantity = $rx2 * $step; 
				}
		   
			    if ($rx < 1) {
				// product is removed: 
				$quantity = 0; 
			}
		   if ((!empty($min)) && ($quantity < $min)) {
			 // product is removed: 
		     $quantity = 0; 
		   }
		   
		   }
		   if ($quantity > $max) {
			 // the max should be set within the quantity steps !!!
		     $quantity = $max; 
		   }
		  
		  
		  }
		  
		}
	}
	
	 function checkForQuantities(&$product, &$quantity=0,&$errorMsg='', &$adjustQ, $inCheckout=false) {
		
		$stockhandle = VmConfig::get('stockhandle','none');
		$mainframe = JFactory::getApplication();
		// Check for a valid quantity
		$q_orig = $quantity; 
		if (!is_numeric( $quantity)) {
			
			
			$errorMsg = OPCLang::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY', false);
			
			return false; 
			
		}
		
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('vmcustom');
		JPluginHelper::importPlugin('system');
		// return null for nothing
		// return true to not validate the quantity with OPC or VM
		// return false to return errorMsg
		$session = JFactory::getSession(); 
		
		$start_in = $session->get('opc_in_trigger', false); 
		$session->set('opc_in_trigger', true); 
			
	    $retValues = $dispatcher->trigger('plgVmOnCheckoutCheckStock', array(  &self::$current_cart, &$product, &$quantity, &$errorMsg, &$adjustQ, $inCheckout));
		
		$session->set('opc_in_trigger', $start_in); 
			
		foreach ($retValues as $v) {
		  if ($v === false) {
			
			
		     return false; 
		  }
		  if ($v === true) {
		    return true; 
		  }
		  
		}
		
		
		// Check for negative quantity
		if ($quantity < 0.00000001) {
			
			$errorMsg = $product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY', false);
			return false;
		}
		if (!empty($product->max_order_level))
		  $max = (float)$product->max_order_level; 
	      else
		  $max = PHP_INT_MAX; 
		  if (!empty($product->min_order_level)) {
		   $min = (float)$product->min_order_level; 
		  }
		  else { 
		    $min = 0; 
		  }
		
		if ($adjustQ)
		$this->adjustQuantitySteps($product, $quantity); 
		
		
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		if (empty($opc_stock_handling))
		{
		// Check to see if checking stock quantity
		if ($stockhandle!='none' && $stockhandle!='risetime') {

			$productsleft = $product->product_in_stock - $product->product_ordered;
			
			if ($quantity > $productsleft ){
				if($productsleft>0 and $stockhandle='disableadd'){
					$quantity = $productsleft;
					$this->adjustQuantitySteps($product, $quantity, true); 
					$errorMsg = $product->product_name.': '.OPCLang::sprintf('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_QUANTITY',$quantity);
					return false; 
				} else {
					
					$errorMsg = $product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					
					return false;
				}
			}
		}
		}
		else
		{
			
			if ($opc_stock_handling === 3) return true; 
			
			if (!empty($opc_stock_zero_weight))
			if (empty($product->product_weight)) return true; 
			 
			 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'stock.php'); 
			
			$status = OPCstock::getStatus($product); 
			if ($status === 0) return true; 
			
			if ($opc_stock_handling === 1)
			{
				 
				 
				  if ($status === 1)
				  {
					  $errorMsg = 'OPC Error Code 1160, '.$product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					 
					 OPCloader::storeError($errorMsg); 
					 

			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			
			if (empty($debug_plugins)) $errorMsg = $product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					 //JFactory::getApplication()->enqueueMessage($product->product_name.': '.$errorMsg, 'error'); 
					  return false; 
				  }
				  
				  if ($status === 3)
				  {
					  $errorMsg = 'OPC Error Code 1167, '.$product->product_name.': '.OPCLang::sprintf('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_QUANTITY');
					  OPCloader::storeError($errorMsg); 
					  			 

			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			
			if (empty($debug_plugins)) $errorMsg = $product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_QUANTITY');
					  
					  
					  return false; 
					  
					 
					  
				  }
				  
				  
			}
			else
			{
				// opc_stock_handling type 2, ignoring reserved products:
				  if ($status === 1)
				  {
					  $errorMsg = 'OPC Error Code 1181, '.$product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					 
					 OPCloader::storeError($errorMsg); 
				  			 

			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			
			if (empty($debug_plugins)) $errorMsg = $product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');

					 //JFactory::getApplication()->enqueueMessage($product->product_name.': '.$errorMsg, 'error'); 
					  return false; 
				  }
				  if ($status === 3)
				  {
					  $errorMsg = 'OPC Error Code 1187, '.$product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					  
					  OPCloader::storeError($errorMsg); 
					  
					  
			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			
			if (empty($debug_plugins)) $errorMsg = $product->product_name.': '.OPCLang::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					  return false; 
				  }
			}
			
			
		}

		// Check for the minimum and maximum quantities
		
		if ((!empty($min)) && ($quantity < $min)) {
			$errorMsg = $product->product_name.': '.OPCLang::sprintf('COM_VIRTUEMART_CART_MIN_ORDER', (int)$min);
			$quantity = $min; 
			return false;
		}
		if ((!empty($max)) && ($quantity > $max)) {
			$errorMsg = OPCLang::sprintf('COM_VIRTUEMART_CART_MAX_ORDER', (int)$max);
			$quantity = $max; 
			return false;
		}

		return true;
	}
	
	private static function renderMail ($viewName, $recipient, $vars = array(), $controllerName = NULL, $noVendorMail = FALSE,$useDefault=true) {
	   	if(!class_exists( 'VirtueMartControllerVirtuemart' )) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'virtuemart.php');

		$controller = new VirtueMartControllerVirtuemart();
		
		$controller->addViewPath( JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views' );

		$view = $controller->getView( $viewName, 'html' );
		if(!$controllerName) $controllerName = $viewName;
		$controllerClassName = 'VirtueMartController'.ucfirst( $controllerName );
		if(!class_exists( $controllerClassName )) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controllerName.'.php');

	
		$view->addTemplatePath( JPATH_VM_SITE.'/views/'.$viewName.'/tmpl' );

		$vmtemplate = VmConfig::get( 'vmtemplate', 'default' );
		if($vmtemplate == 'default') {
			if(JVM_VERSION >= 2) {
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery( $q );
			$template = $db->loadResult();
		} else {
			$template = $vmtemplate;
		}

		if($template) {
			$view->addTemplatePath( JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName );
		} 

		foreach( $vars as $key => $val ) {
			$view->$key = $val;
		}

		$user = FALSE;
		
		$user = OPCcheckout::sendVmMail( $view, $recipient, $noVendorMail );
	}
	
	
	
	/**
	 * With this function you can use a view to sent it by email.
	 * Just use a task in a controller
	 *
	 * @param string $view for example user, cart
	 * @param string $recipient shopper@whatever.com
	 * @param bool $vendor true for notifying vendor of user action (e.g. registration)
	 */

	private static function sendVmMail (&$view, $recipient, $noVendorMail = FALSE) {

		$jlang = JFactory::getLanguage();
		if(VmConfig::get( 'enableEnglish', 1 )) {
			$jlang->load( 'com_virtuemart', JPATH_SITE, 'en-GB', TRUE );
		}
		$jlang->load( 'com_virtuemart', JPATH_SITE, $jlang->getDefault(), TRUE );
		$jlang->load( 'com_virtuemart', JPATH_SITE, NULL, TRUE );

		if(!empty($view->orderDetails['details']['BT']->order_language)) {
			$jlang->load( 'com_virtuemart', JPATH_SITE, $view->orderDetails['details']['BT']->order_language, true );
			$jlang->load( 'com_virtuemart_shoppers', JPATH_SITE, $view->orderDetails['details']['BT']->order_language, true );
			$jlang->load( 'com_virtuemart_orders', JPATH_SITE, $view->orderDetails['details']['BT']->order_language, true );
		} else {
			if (method_exists('VmConfig', 'loadJLang'))
			{
			 VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
			 VmConfig::loadJLang('com_virtuemart_orders',TRUE);
			}
		}

		ob_start();

		$view->renderMailLayout( $noVendorMail, $recipient );
		$body = ob_get_contents();
		ob_end_clean();

		$subject = (isset($view->subject)) ? $view->subject : OPCLang::_( 'COM_VIRTUEMART_DEFAULT_MESSAGE_SUBJECT' );
		$mailer = JFactory::getMailer();
		$mailer->addRecipient( $recipient );
		$mailer->setSubject(  html_entity_decode( $subject) );
		$mailer->isHTML( VmConfig::get( 'order_mail_html', TRUE ) );
		$mailer->setBody( $body );
		
		
		
		if(!$noVendorMail) {
			$replyto[0] = $view->vendorEmail;
			$replyto[1] = $view->vendor->vendor_name;
			$mailer->addReplyTo( $replyto );
		}
		/*	if (isset($view->replyTo)) {
				 $mailer->addReplyTo($view->replyTo);
			 }*/

		if(isset($view->mediaToSend)) {
			foreach( (array)$view->mediaToSend as $media ) {
				$mailer->addAttachment( $media );
			}
		}

		// set proper sender
		$sender = array();
		if(!empty($view->vendorEmail) and VmConfig::get( 'useVendorEmail', 0 )) {
			$sender[0] = $view->vendorEmail;
			$sender[1] = $view->vendor->vendor_name;
		} else {
			// use default joomla's mail sender
			$app = JFactory::getApplication();
			$sender[0] = $app->getCfg( 'mailfrom' );
			$sender[1] = $app->getCfg( 'fromname' );
			if(empty($sender[0])){
				$config = JFactory::getConfig();
				if (method_exists($config, 'getValue'))
				$sender = array( $config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' ) );
				else
				$sender = array( $config->get( 'mailfrom' ), $config->get( 'fromname' ) );
			}
		}
		$mailer->setSender( $sender );
		
		// stAn, return the language to original: 
		$jlang = JFactory::getLanguage();
		if(VmConfig::get( 'enableEnglish', 1 )) {
			$jlang->load( 'com_virtuemart', JPATH_SITE, 'en-GB', TRUE );
		}
		
		$lang     = JFactory::getLanguage();
        $tag = $lang->getTag(); 
        $filename = 'com_virtuemart';
        $lang->load($filename, JPATH_ADMINISTRATOR, $tag, true);
        $lang->load($filename, JPATH_SITE, $tag, true);
		
		$jlang->load( 'com_virtuemart_shoppers', JPATH_SITE, $tag, true );
		$jlang->load( 'com_virtuemart_orders', JPATH_SITE, $tag, true );
		
		return $mailer->Send();
	}

	
	
	
	
		/**
	 * Check if a minimum purchase value for this order has been set, and if so, if the current
	 * value is equal or hight than that value.
	 * @author Oscar van Eijk
	 * @return An error message when a minimum value was set that was not eached, null otherwise
	 */
	 function checkPurchaseValue($prices) {
		
		$vendor = OPCmini::getModel('vendor'); 
		

		$vendor->setId(self::$opc_cart->vendorId);
		$store = $vendor->getVendor();
		if ($store->vendor_min_pov > 0) {
			
			if ($prices['salesPrice'] < $store->vendor_min_pov) {
				if (!class_exists('CurrencyDisplay'))
				require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
				$minValue = $currency->priceDisplay($min);
				return OPCLang::sprintf('COM_VIRTUEMART_CART_MIN_PURCHASE', $currency->priceDisplay($store->vendor_min_pov));
			}
		}
		return null;
	}
	
	function redirect($x, $y="")
	{
		
		if (!isset(VirtueMartControllerOpc::$isjson)) VirtueMartControllerOpc::$isjson = false; 
		if (!empty(VirtueMartControllerOpc::$isjson)) {
			{
				$arr = array(); 
				$arr['error'] = $y; 
				$VirtueMartControllerOpc = new VirtueMartControllerOpc; 
				return $VirtueMartControllerOpc->printJson($msg); 
			}
			
		}
		
		$cart = VirtuemartCart::getCart(); 
		$cart->setCartIntoSession(true);
		if (empty($this->url)) { 
		    $url = 'index.php?option=com_virtuemart&view=cart&nosef=1&error_redirect=1'; 
			$lang = OPCloader::getLangCode(); 
			if (!empty($lang))
		    $url .= '&lang='.$lang; 
		    $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
			if (!empty($newitemid)) $url .= '&Itemid='.$newitemid; 
			$this->url = $url; 
			
		}
		
	  $mainframe = JFactory::getApplication();
	  
	  if (php_sapi_name() === 'cli') {
	     echo $y."\n"; 
		 $mainframe->close(); 
		 die(0); 
	  }
	  
	  if (!empty($y)) {
		//VirtueMartControllerOpc::clearMsgs(); 
	    $mainframe->enqueueMessage($y, 'error'); 
	  }
	  
	  
	  $mainframe->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)));
	  
	  
	  $mainframe->close(); 
	  return; 
	}
		/**
	 * Test userdata if valid
	 *
	 * @author Max Milbers
	 * @param String if BT or ST
	 * @param Object If given, an object with data address data that must be formatted to an array
	 * @return redirectMsg, if there is a redirectMsg, the redirect should be executed after
	 */
	 function validateUserData($type='BT', $obj = null, $cart=null) {

	 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	 // we disable validation for ST address, because it is still missing at the front-end and shall be added as an optional feature
		if ($type == 'ST') return false; 
		
	 
		
		$userFieldsModel = OPCmini::getModel('userfields'); 
		
		
		
		if ($type == 'BT')
		$fieldtype = 'account'; else
		$fieldtype = 'shipment';

		if ($type == 'BT')
		if (empty($bt_fields_from))
		$fieldtype = 'registration'; 
	    else
		{
                   if ($bt_fields_from  === 1) $fieldtype = 'account'; 
                   else $fieldtype = 'cart'; 
		}
		
		if ($fieldtype === 'cart')
		if (!defined('VM_VERSION') || (VM_VERSION < 3))
		{
			 $fieldtype = 'registration'; 
		}
		else
		$fieldtype = 'registration'; 
	
		if ($type == 'cart')
		{
			$fieldtype = 'cart';
		}
	
		
		$params =array('required' => true, 'delimiters' => true, 'captcha' => true, 'system' => false);  
		$ignore = array('delimiter_userinfo', 'name','username', 'address_type_name', 'address_type', 'user_is_vendor', 'agreed', 'tos'); 
		
		$pwd_i = true; 
		$id = JFactory::getUser()->get('id'); 
		if (defined('VM_REGISTRATION_TYPE'))
		{
		if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION')
	    if (empty($id))
		{
			$pwd_i = false; 
		}
		if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
		{
			$register = JRequest::getVar('register_account', false); 
			if (!empty($register))
			{
				$pwd_i = false; 
			}
		}
		}
		
		if ($pwd_i)
		{
			$ignore[] = 'password'; 
			$ignore[] = 'password2'; 
			
		}
		
		$neededFields = $userFieldsModel->getUserFields(
		$fieldtype
		, $params
		, $ignore);

		
		
		$redirectMsg = false;
		$missinga = array(); 
		
		$i = 0 ;
        $missing = ''; 
		foreach ($neededFields as $field) {
			 $is_business = JRequest::getVar('opc_is_business', 0); 
	   
	    // we need to alter shopper group for business when set to: 
	     $is_business = JRequest::getVar('opc_is_business', 0); 
		 if (!empty($business_fields))
	     if (!$is_business)
		 {
		   // do not check if filled
		   if (in_array($field->name, $business_fields)) continue; 
		 }
		 if ($type=='ST')
		 if (!empty($shipping_obligatory_fields))
		 {
		   if (!in_array($field->name, $shipping_obligatory_fields)) continue; 
		 }
		   // manage required business fields when not business selected: 
		   //foreach ($business_fields as $fn)
	
	     if ($type == 'cart') $type = 'BT';
	
			if($field->required && empty($cart->{$type}[$field->name]) && $field->name != 'virtuemart_state_id'){
				$redirectMsg = OPCLang::sprintf('COM_VIRTUEMART_MISSING_VALUE_FOR_FIELD',OPCLang::_($field->title) );
				$i++;
				
				//more than four fields missing, this is not a normal error (should be catche by js anyway, so show the address again.
				if($type=='BT'){
				    $missinga[] = OPCLang::_($field->title); 
					$redirectMsg = OPCLang::_('COM_VIRTUEMART_CHECKOUT_PLEASE_ENTER_ADDRESS');
				}
			
			
			
			
			}

			if ($obj !== null && is_array($cart->{$type})) {
				$cart->{$type}[$field->name] = $obj->{$field->name};
			}

			//This is a special test for the virtuemart_state_id. There is the speciality that the virtuemart_state_id could be 0 but is valid.
			if ($field->name == 'virtuemart_state_id') {
				if (!class_exists('VirtueMartModelState')) require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'state.php');
				if(!empty($cart->{$type}['virtuemart_country_id']) && !empty($cart->{$type}['virtuemart_state_id']) ){
					if (!$msg = VirtueMartModelState::testStateCountry($cart->{$type}['virtuemart_country_id'], $cart->{$type}['virtuemart_state_id'])) {
						
						$redirectMsg = $msg;
					}
				}

			}
		}
		
		if (empty($redirectMsg)) return false; 
		
		$missing = implode(', ', $missinga); 
		
		$redirectMsg .= ' '.$missing; 
		return $redirectMsg;
	}
	/**
	 * Set the last error that occured.
	 * This is used on error to pass back to the cart when addJS() is invoked.
	 * @param string $txt Error message
	 * @author Oscar van Eijk
	 */
	public function setError($txt) {
		$this->_lastError = $txt;
	}
	
	// generic method to get the new data from original cart
	public function __get($name)
	{
	  if (isset(self::$opc_cart->{$name}))
	  return self::$opc_cart->{$name}; 
	  
	  return null; 
	}


/**
     * To set a payment method
     *
     * @author Max Milbers
     * @author Oscar van Eijk
     * @author Valerie Isaksen
     */
    function setpayment(&$cart) {

	/* Get the payment id of the cart */
	//Now set the payment rate into the cart
	if (defined('VM_VERSION') && (VM_VERSION >= 3))
	if (!empty($cart->cartPrices))
	{
		$cart->pricesUnformatted = $cart->cartPrices; 
	}
	$do_nothing = false; 
	if ($cart) {
		
		
		/*
		note: the proper payment method, or zero payment ID must be set before calling this lines - i.e. from user context, in JS
		if(isset($cart->pricesUnformatted['billTotal']) && empty($cart->pricesUnformatted['billTotal'])) {
		
		
			
			$do_nothing = true; 
		}
		else
		
		{
		if(isset($cart->pricesUnformatted['billSub']) && empty($cart->pricesUnformatted['billSub'])) {
			$billSub = $cart->pricesUnformatted['billSub']; 
			$billDiscountAmount = $cart->pricesUnformatted['billDiscountAmount']; 
			$couponValue = $cart->pricesUnformatted['couponValue']; 
			$test = $billSub - abs($couponValue); 
			if ($test === 0) {
				$do_nothing = true; 
			}
		 
			
		}
		}
		*/
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmpsplugin.php');
	    JPluginHelper::importPlugin('vmpayment');
	    //Some Paymentmethods needs extra Information like
	    $virtuemart_paymentmethod_id = JRequest::getInt('virtuemart_paymentmethod_id', '0');
		$cart->virtuemart_paymentmethod_id = $virtuemart_paymentmethod_id; 
	    //OLD: $cart->setPaymentMethod($virtuemart_paymentmethod_id);

	    //Add a hook here for other payment methods, checking the data of the choosed plugin
	    $_dispatcher = JDispatcher::getInstance();
		$msg = ''; 
		
		$cart->virtuemart_paymentmethod_id = $virtuemart_paymentmethod_id;

		/*
				$retValues = $_dispatcher->trigger('plgVmOnCheckoutCheckDataPayment', array( $cart));

				foreach ($retValues as $retVal) {
					if ($retVal === true) {
						$cart->setCartIntoSession();
						return true;  
						// Plugin completed succesful; nothing else to do
					} elseif ($retVal === false) {
						// Missing data, ask for it (again)
						$redirectMsg = OPCLang::_('COM_VIRTUEMART_CART_NO_PAYMENT_SELECTED'); 
						 $mainframe = JFactory::getApplication();
						 $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment',false,$this->useSSL), 'Error 68: '.$redirectMsg);
						// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
					}
				}
		*/
		$session = JFactory::getSession(); 
			$session->set('opc_in_trigger', true); 

	    $_retValues = $_dispatcher->trigger('plgVmOnSelectCheckPaymentOPC', array( &$cart, &$msg));
		$session->set('opc_in_trigger', false); 

	    $dataValid = true;
		
		
		
		
	    foreach ($_retValues as $_retVal) {
		if ($_retVal === true ) {
			
		
		    $cart->setCartIntoSession();
			// opc mod:
			return true; 
		   
		} else if ($_retVal === false ) {
		
		   $redirectMsg = $msg; 
		   if (empty($redirectMsg))
		   {
			   $redirectMsg = OPCLang::_('COM_VIRTUEMART_CART_NO_PAYMENT_SELECTED'); 
		   }
		   /*
		  
		   $redirectMsg = ''; 
		   if (empty($msg))
		   $msg = JFactory::getApplication()->getMessageQueue(); 
				if (!empty($msg) && (is_array($msg)))
				{
				  
				  foreach ($msg as $line)
				  {
				  if (is_array($line))
				  {
				   if (!empty($line['message']))
				    $redirectMsg .= $line['message'].'<br />'; 
				   }
				   else
				   {
				    $redirectMsg .= $line.'<br />'; 
				   }
				  }
				}
				else $redirectMsg = $msg; 
			*/	
			 $mainframe = JFactory::getApplication();
			 
		    $error = 'Error 99: '.$redirectMsg;
			
			OPCloader::storeError($error); 
			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			
			if (empty($debug_plugins)) $error = $redirectMsg;
			
			
			
				$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $error);
			 
		     
		    break;
		}
	    }
//			$cart->setDataValidation();	//Not needed already done in the getCart function

	    if ($cart->getInCheckOut()) {
		return true; 
	    }
	}
	

	return true; 
	parent::display();
    }
	
	    /**
     * Sets a selected shipment to the cart
     *
     * @author Max Milbers
     */
    public function setshipment(&$cart, $virtuemart_shipmentmethod_id_here=null, $redirect=true, $incheckout=true) {
		
		
		
	include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	if (!empty($op_disable_shipping)) return true; 
	/* Get the shipment ID from the cart */
	if (empty($virtuemart_shipmentmethod_id_here))
	$virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', '0');
	else $virtuemart_shipmentmethod_id = $virtuemart_shipmentmethod_id_here; 
	
	
	
	$mainframe = JFactory::getApplication();
	if (!empty($virtuemart_shipmentmethod_id)) {
	    //Now set the shipment ID into the cart
	    
		// general test: 
		$n_id = (int)$virtuemart_shipmentmethod_id; 
		if (!empty($n_id))
		{
			// check for non existent shipping method: 
			$db = JFactory::getDBO(); 
			$q = 'select virtuemart_shipmentmethod_id from #__virtuemart_shipmentmethods where virtuemart_shipmentmethod_id = '.(int)$n_id.' limit 1'; 
			$db->setQuery($q); 
			$n_id2 = $db->loadResult(); 
			
			
			
			$e = $db->getErrorMsg(); if (!empty($e)) {
			 JFactory::getApplication()->enqueueMessage($e, 'error'); 
		     return; 
			}
			if (empty($n_id2))
			{
				$redirectMsg = 'Error 155: '.OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE'); 
				if ($redirect)
				{
					
					$error = 'Error 120: '.OPCLang::_('COM_VIRTUEMART_CART_NO_PRODUCT'); 
					OPCloader::storeError($redirectMsg); 
			
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			
			if (empty($debug_plugins)) $redirectMsg = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE');
					
					
					
			
			
				   $this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $redirectMsg);
				   $mainframe->close(); 
				}
			}
		}
		
	    if (!empty($cart)) {
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		
		if (!empty($virtuemart_shipmentmethod_id))
		{
		$cart->virtuemart_shipmentmethod_id = $virtuemart_shipmentmethod_id; 
		}
		
		// let's do the opc validation first: 
		$_dispatcher = JDispatcher::getInstance();
		$msg = ''; 
		$session = JFactory::getSession(); 
			$session->set('opc_in_trigger', true); 
		$_retValues = $_dispatcher->trigger('plgVmOnSelectCheckShipmentOPC', array( &$cart, &$msg));
		$session->set('opc_in_trigger', false); 
		foreach ($_retValues as $r)
		{
			if ($r === false)
			{
				$redirectMsg = 'Error 180: '.$msg.OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE'); 
				if ($redirect)
				{
					
				$error = $redirectMsg;
			     OPCloader::storeError($error); 
					
			$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			
			if (empty($debug_plugins)) $redirectMsg = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE');
			
				$this->redirect(JRoute::_($this->url, false, VmConfig::get('useSSL', false)), $redirectMsg);
				JFactory::getApplication()->close(); 
				}
				return false; 
			}
		}
		
		
		
		
		if (method_exists($cart, 'setShipment'))
		{
		  $cart->setShipment($virtuemart_shipmentmethod_id);
		}
		else
		{
		  $cart->virtuemart_shipmentmethod_id = $virtuemart_shipmentmethod_id; 
		}
		//Add a hook here for other payment methods, checking the data of the choosed plugin
		/*
		$_retValues = $_dispatcher->trigger('plgVmOnSelectCheckShipment', array( &$cart));
		$dataValid = true;
		foreach ($_retValues as $_retVal) {
		    if ($_retVal === true ) {// Plugin completed succesfull; nothing else to do
			$cart->setCartIntoSession();
			// opc mod
			return true; 
			break;
		    } else if ($_retVal === false ) {
		       
			   $msg = JFactory::getSession()->get('application.queue');; 
				if (!empty($msg) && (is_array($msg)))
				$redirectMsg = implode('<br />', $msg); 
			
				if (empty($redirectMsg))
				{
					$redirectMsg = 'Error 219: '.OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPINGRATE'); 
				}
			   if ($redirect)
			   {
		       $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout',false,$this->useSSL), 'Error 218: '.$redirectMsg);
			   JFactory::getApplication()->close(); 
			   }
			   else return;
			break;
		    }
		}
		
		
		*/
		
		
		if ($incheckout)
		if (method_exists($cart, 'getInCheckOut'))
		if ($cart->getInCheckOut()) {
			//opc mod
			return true; 
		}
	    }
	}

	return true; 
	
    }



	
} 


