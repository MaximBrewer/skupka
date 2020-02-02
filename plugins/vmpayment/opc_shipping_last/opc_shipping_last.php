<?php
/**
 * @version		$Id: opc.php$
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');



if (!class_exists('VmConfig'))
{
	require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	VmConfig::loadConfig(); 
}
if (!class_exists('vmPlugin'))
{
if (!JFactory::getApplication()->isAdmin())
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'); 
}
if (!class_exists('vmPSplugin'))
{
	
	require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpsplugin.php'); 
}
//class plgVmShipmentOpc_shipping_last extends vmPSPlugin {
class plgVmpaymentOpc_shipping_last extends vmPSPlugin {

	/**
	 * @param object $subject
	 * @param array  $config
	 */
	function __construct (& $subject, $config) {

		parent::__construct ($subject, $config);
/*
		$this->_loggable = TRUE;
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		$this->tableFields = array(); 
		$varsToPush = array(); 
		$this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);
		//vmdebug('Muh constructed plgVmShipmentWeight_countries',$varsToPush);
	*/
	}

	//public function plgVmOnSelectedCalculatePriceShipment( $cart, &$cartPrices, &$sn  )
	public function checkConditions ($cart, $method, $cart_prices) {
	  return null; 
	}
	
	public function plgVmCouponUpdateOrderStatus($data, $old_order_status)
	{
		
	  if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) return;
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php'); 
	  
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	

	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 	  
	  
	   OPCplugin::checkGiftCoupon($data, $old_order_status);  
	  
	  //email_fix3: 
	  $default = array(); 
			$email_fix3 = OPCConfig::getValue('opc_config', 'email_fix3', 0, $default, false, false);
			if (!empty($email_fix3)) {
				$order = $data; 
				$orderModel = OPCmini::getModel('Orders'); 
				if (is_object($order)) {
					$pid = $order->virtuemart_paymentmethod_id; 
					$orderId = (int)$order->virtuemart_order_id; 
					$order = $orderModel->getOrder($orderId); 
				}
				else 
				{
				 $pid = (int)$order['details']['BT']->virtuemart_paymentmethod_id; 
				 $orderId = (int)$order['details']['BT']->virtuemart_order_id; 
				}
				
				
				
	  
		  if (in_array($pid, $email_fix3)) 
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
					$order['order_status'] = $saved_status; 
					$order['customer_notified'] = 1;
				    //$order['comments'] = '';
					
				    $orderModel->notifyCustomer($orderId, $order);	


			    }
			
			}
			}
			}
	  
	}
	
	
	public function plgVmOnSelectedCalculatePriceShipment2(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name)
	{
		
			
		static $hrc; 
		
		
		if (!empty($cart->couponCode)) {
			
			
		if (empty($cartPrices['shipmentValue'])) 
		$cartPrices['shipmentValue'] = 0.00000000001; 
	    if (empty($cartPrices['salesPriceShipment']))
		$cartPrices['salesPriceShipment'] = 0.00000000001;
		/*
		if (empty($hrc)) {
		 $hrc = true; 
		 $dispatcher = JDispatcher::getInstance();
		 $returnValues = $dispatcher->trigger('plgVmCouponHandler', array($cart->couponCode,&$cart->cartData, &$cartPrices));
		 $hrc = false;  
		 

		}
		*/
		
		}
		
		return 0; 
	}

	
	// vm3.0.6 forces automatic shipment and payment even when disabled: 
	public function plgVmOnCheckAutomaticSelectedShipment($cart, $prices, &$counter)
	{
		
		if (!class_exists('plgSystemOpc')) return; 
		if (empty($counter)) $counter = 1; 
		return "-0"; 
	}
	public function plgVmOnCheckAutomaticSelectedPayment($cart, $prices, &$counter)
	{
		if (!class_exists('plgSystemOpc')) return; 
		if (empty($counter)) $counter = 1; 
		return "-0"; 
	}
	
	function plgVmgetEmailCurrency($virtuemart_paymentmethod_id, $virtuemart_order_id, &$emailCurrencyId) {

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		$default = false; 
		$config = OPCconfig::get('override_payment_currency', $default); 
		$virtuemart_order_id = (int)$virtuemart_order_id; 
		
		
		if (!empty($virtuemart_order_id))
		if (!empty($config))
		{
			$db = JFactory::getDBO(); 
			$q = 'select `user_currency_id` from #__virtuemart_orders where virtuemart_order_id = '.(int)$virtuemart_order_id; 
		    $db->setQuery($q); 
			$c = $db->loadResult(); 
			if (!empty($c))
			{
				$emailCurrencyId = $c; 
				return $c; 
			}
			
		}
		

	}
	
	
}	
	