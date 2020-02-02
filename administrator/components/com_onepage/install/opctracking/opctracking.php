<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

if (!defined('JPATH_VM_PLUGINS'))
{
   if (!class_exists('VmConfig'))
   require(JPATH_ADMINISTRATOR. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'config.php'); 
		 
   VmConfig::loadConfig(); 
}

if (!class_exists('vmPSPlugin')) {
   
	require(JPATH_SITE. DIRECTORY_SEPARATOR .'administrator'. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_virtuemart'. DIRECTORY_SEPARATOR .'plugins'. DIRECTORY_SEPARATOR .'vmpsplugin.php');
}


class plgVmPaymentOpctracking extends vmPSPlugin
{

   // this is called as the first event after creating an order, 
   // but before any payment or shipping triggers are called
   // it pairs the cookie with the actual order 
  
  public function plgOpcOrderCreated($cart, $order)
  {
    $this->plgVmConfirmedOrder2($cart, $order); 
  }
  
  
  
  
	public function checkConditions ($cart, $method, $cart_prices) {
	  return null; 
	}
	public function plgVmOnSelectCheckPayment($id)
	{
	 return null; 
	}
    
	public function plgVmConfirmedOrderOPCExcept ($except, &$cart, &$order)
	{
	  $this->plgVmConfirmedOrder2($cart, $order); 
	}
	static $delay; 
	static $_storedOrder; 
	
	public function plgVmConfirmedOrder($cart, $order)
	{
	 $this->plgVmConfirmedOrder2($cart, $order); 
	}
	
	public function plgVmConfirmedOrder2($cart, $data)
	{
		
		
		
	  if (!self::_check()) return; 
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
	  
	  if (defined('OPCTRACKINGORDERCREATED')) return; 
	  
	  if (class_exists('plgSystemOpctrackingsystem'))
	  plgSystemOpctrackingsystem::_tyPageMod($data, false);  
	  
	  
	  define('OPCTRACKINGORDERCREATED', 1); 
	
	
	  if (!is_object($data))
	  {
	  if (isset($data['details']['BT']))
	  {
	   //self::$delay = true; 
	   $order = $data['details']['BT']; 
	   if (isset($order->order_status)) $status = $order->order_status; 
	   else $status = 'P'; 
	   
	   if (class_exists('plgSystemOpctrackingsystem'))
	   if (method_exists('plgSystemOpctrackingsystem', 'orderCreated'))
	   plgSystemOpctrackingsystem::orderCreated($order, 'P');  
	  }
	  }
	  else
	  if (isset($data->virtuemart_order_id))
	  {
		
		   if (isset($data->order_status)) $status = $data->order_status; 
	       else $status = 'P'; 
		  
	       
		   if (class_exists('plgSystemOpctrackingsystem'))
	       if (method_exists('plgSystemOpctrackingsystem', 'orderCreated'))
	       plgSystemOpctrackingsystem::orderCreated($data, $status);  
		   
		  
		  
		  
	  }
	}
	
	public function plgVmOnUpdateOrderPayment(&$data,$old_order_status)
	{
	  
	  if (!self::_check()) return; 
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
	  if (defined('OPCTRACKINGORDERCREATED')) return; 
	  else define('OPCTRACKINGORDERCREATED', 1); 
	  
	  if (class_exists('plgSystemOpctrackingsystem'))
	  if (method_exists('plgSystemOpctrackingsystem', 'orderCreated'))
	  plgSystemOpctrackingsystem::orderCreated($data, $old_order_status);  
	  
	  
	}
	
	//added in VM3
	public function plgVmCouponUpdateOrderStatus($data,$old_order_status)
	{
	   
	  if (!self::_check()) return; 
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
	  if (defined('OPCTRACKINGORDERCREATED')) return; 
	  else define('OPCTRACKINGORDERCREATED', 1); 
	  
	  if (class_exists('plgSystemOpctrackingsystem'))
	  if (method_exists('plgSystemOpctrackingsystem', 'orderCreated'))
	  plgSystemOpctrackingsystem::orderCreated($data, $old_order_status);  
	  
	  
	}

	public function plgVmOnPaymentNotification() {
		$html = ''; 
		$this->plgVmOnPaymentResponseReceived($html); 
	}
	
	public function plgVmOnPaymentResponseReceived(&$html)
	{
		$doc = JFactory::getDocument(); 
		$type = $doc->getType(); 
		
		
		if (self::_check()) {
			
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php')) 
			{
			 JHTMLOPC::script('opcping.js', 'components/com_onepage/assets/js/', false);
			 $pingUrl = OPCtrackingHelper::getUrl().'index.php?option=com_onepage&task=pingstatus&nosef=1&format=raw&tmpl=component'; 
			 $js = "\n".'if (typeof opc_ping_status !== \'undefined\') { opc_ping_status(\''.$pingUrl.'\'); } else { if (typeof jQuery !== \'undefined\') {	jQuery(document).ready( function() { opc_ping_status(\''.$pingUrl.'\'); }); } } '."\n";
			 if (class_exists('plgSystemOpctrackingsystem')) {
			  if (empty(plgSystemOpctrackingsystem::$_js)) plgSystemOpctrackingsystem::$_js = array(); 
			  plgSystemOpctrackingsystem::$_js[] = $js; 
			  
			  
			 }
			}
		}
		
	    if (empty($html)) $html = '&nbsp;'; 
	   if (empty(self::$_storedOrder))
	   {
	    if (!class_exists('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'orders.php');
		}
		if (!class_exists('shopFunctionsF')) {
			require(JPATH_VM_SITE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'shopfunctionsf.php');
		}
		// PPL, iDeal, heidelpay: 
	    $order_number = JRequest::getString('on', 0);
		// eway:
		if (empty($order_number))
		 {
		   $order_number = JRequest::getString('orderid', 0);
		   if (empty($order_number))
		    {
			   //systempay
			  $order_number = JRequest::getString('order_id', 0);
			}
			if (empty($order_number))
			{
				$order_number = JRequest::getString('ordernumber', 0); 
			}
			
			
		 }
		 
		  if (empty($order_number)) return; 
		  
		$orderModel = VmModel::getModel('orders');
	    $virtuemart_order_id = (int)VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
		if (empty($virtuemart_order_id)) return;
		self::emptyCache(); 
	    self::$_storedOrder = $orderModel->getOrder($virtuemart_order_id);
		
		
		
	   }
	   
	   //if (!empty(self::$_storedOrder))
	   //$ret = self::_tyPageMod(self::$_storedOrder, false, $html); 
   
        if (class_exists('plgSystemOpctrackingsystem'))
		$ret = plgSystemOpctrackingsystem::_tyPageMod(self::$_storedOrder, false, $html); 
	    if (!empty($ret)) $html = $ret; 
	   
	   if (class_exists('plgSystemOpctrackingsystem')) {
			plgSystemOpctrackingsystem::setCurrentOrderStatus(self::$_storedOrder); 
		}

	}
	
		private static function emptyCache() {
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
					$orderModel = OPCmini::getModel('Orders'); 
				if (method_exists($orderModel, 'emptyCache')) {
				  $orderModel->emptyCache(); 
				}
				$order_tables = array('orders', 'userinfos', 'order_items', 'order_userinfos', 'order_calc_rules', 'order_histories', 'order_item_histories', 'invoices', 'order_items'); 
				
				foreach ($order_tables as $tb) {
				  $tb = $orderModel->getTable($tb);
				  if (method_exists($tb, 'emptyCache')) {
				    $tb->emptyCache(); 
				  }
				}

	
	}
	
	
	//$returnValues = $dispatcher->trigger('plgVmOnCheckoutAdvertise', array( $this->cart, &$checkoutAdvertise));
	static function _tyPageMod(&$data, $afterrender=false, $html='')
	{
		
		if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
		if (empty($html))
		$html = JRequest::getVar('html', '', 'default', 'STRING', JREQUEST_ALLOWRAW);
		
		if (!empty($html))
		{
		
		   if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'thankyou.php')) 
		    {
			  require_once(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'thankyou.php'); 
			  return OPCThankYou::updateHtml($html, $data, $afterrender); 
			}
		}
		
				$user = JFactory::getUser(); 
				$id = $user->id; 
				$user = new JUser($id); 
				$session = JFactory::getSession(); 
				$session->set('user', $user); 

		
		return ''; 
	}
	
	public function plgVmOnCheckoutAdvertise($cart, &$html)
	{
	
	  // we will create hash only when cart view calls checkoutAdvertise
	  if (class_exists('plgSystemOpctrackingsystem'))
	  plgSystemOpctrackingsystem::registerCart(); 
	}
	
	
	
	public function plgVmOnUpdateOrderShipment(&$data,$old_order_status)
	{
	
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
	
	  if (defined('OPCTRACKINGORDERCREATED')) return; 
	  else define('OPCTRACKINGORDERCREATED', 1); 
	  
	  if (!self::_check()) return; 
	  if (class_exists('plgSystemOpctrackingsystem'))
	  if (method_exists('plgSystemOpctrackingsystem', 'orderCreated'))
	  plgSystemOpctrackingsystem::orderCreated($data, $old_order_status);  
	    
	   
	
	}
	
	
	
	private static function _check()
	{
		
		if (php_sapi_name() === 'cli') return false; 
		
	  	$app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return false;
		}
		if (!file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'opctracking.php')) return false;
		
		
		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') return false;

		$doc = JFactory::getDocument(); 
		 $class = strtoupper($class); 
		// never run in an ajax context !
		$arr = array('JDOCUMENTHTML', 'JOOMLA\CMS\DOCUMENT\HTMLDOCUMENT'); 
		if (!in_array($class, $arr)) {
		  return false; 
		}
	 
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
		
		if(version_compare(JVERSION,'3.0.0','ge')) 
		require_once(JPATH_ADMINISTRATOR. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'compatibilityj3.php'); 
		else
	    require_once(JPATH_ADMINISTRATOR. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'compatibilityj2.php'); 

		
		require_once(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'opctracking.php'); 
		
		return true; 

	}
	
	
	
	

		
}

class plgSystemOpctracking extends plgVmPaymentOpctracking {

}