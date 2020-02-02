<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z RuposTel.com $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access 
defined('_JEXEC') or die;

class OPCTest {
	var $cart; 
	 public function runTest($cart=null, $skipfirst=false) {
		 require(__DIR__.DIRECTORY_SEPARATOR.'test.config.php'); 
		 
		 $dev = JRequest::getVar('dev', false); 
		 if (!$dev) {
		 if (empty($ip)) return; 
		 if (!in_array($_SERVER['REMOTE_ADDR'], $ip)) return; 
		 }
		 
		 self::getRequire(); 
		
		
		$calc = calculationHelper::getInstance(); 
		
		if (empty($cart))
		$cart = VirtuemartCart::getCart(); 
		
		$cart->prepareCartData(false); 
		$this->cart =& $cart; 
		$this->cart->couponCode = $coupon_code; 
		echo 'Coupon: '.$this->cart->couponCode."<br />\n"; 
		// canada, qeubeck
		$this->cart->BT = $BT; 
		
		#test case 1
		$prices = $cart->cartPrices; 
		
		if (!$skipfirst) {
		 $this->setCart($shipping_id, $payment_id); 
		 $prices = $this->doCalc($calc);
		}
		
		echo 'vmcalc cart: '; var_dump($prices['billTotal']); 
		//echo 'vmcalc calc: '; var_dump($calc->_cart->cartPrices['billTotal']); 
		$t1 = (float)$prices['billTotal'];
		echo "<br />\n"; 
		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doCalc($calc);
		
		$t22 = (float)$prices['billTotal'];
		if ($t1 != $t22) echo __LINE__.'<b style="color:red;">FAIL:double calling VM core function results in different total!</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 
		
		

		self::getRequireOPC(); 
		
		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doCalc($calc);
		
		$t22 = (float)$prices['billTotal'];
		if ($t1 != $t22) echo __LINE__.'<b style="color:red;">FAIL:calling VM core after opc loaded results in different total!</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 
		
		
		
		echo 'opcloader:'; 		
		echo "<br />\n"; 
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doOPCCalc();
		
		$t2 = (float)$prices['billTotal'];
		if ($t1 != $t2) echo __LINE__.'<b style="color:red;">FAIL:calling opcloader results in different calculation compared to core VM</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 

		echo 'no shipping:';
		$this->setCart(0, $payment_id); 
		
		$prices = $this->doOPCCalc();
		$t3 = (float)$prices['billTotal'];
		if ($t1 == $t3) echo __LINE__.'<b style="color:red;">FAIL:when shipping is zero, calculation must change!</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 
		
		echo 'shipping readded:'; 
		echo "<br />\n"; 
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doOPCCalc();
		$t4 = (float)$prices['billTotal'];
		if ($t1 != $t4) echo __LINE__.'<b style="color:red;">FAIL:when shipping is readded calculation must be equal to first calling of the totals</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 
		
		#test case 2
		$this->cart->couponCode = ''; 
		echo 'Coupon: '.$this->cart->couponCode."<br />\n"; 
		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doCalc($calc);
		
		echo 'vmcalc cart: '; var_dump($prices['billTotal']); 
		echo 'vmcalc calc: '; var_dump($calc->_cart->cartPrices['billTotal']); 
		echo "<br />\n"; 
		
		$t51 = (float)$prices['billTotal'];
		if ($t1 == $t51) echo __LINE__.'<b style="color:red;">FAIL:coupon is removed but calculation is the same as when it was added</b>'; 
		
		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doCalc($calc);
		
		
		
		$t55 = (float)$prices['billTotal'];
		if ($t51 != $t55) echo __LINE__.'<b style="color:red;">FAIL:double calling core calculation results in different total</b>'; 
		
		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doCalc($calc);
		
		
		
		echo 'opcloader:'; 		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doOPCCalc();
		
		$t5 = (float)$prices['billTotal'];
		if ($t5 != $t55) echo __LINE__.'<b style="color:red;">FAIL:opc loader results in different calculation compared to core vm calculation</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 

		echo 'no shipping:';
		$this->setCart(0, $payment_id); 
		
		$prices = $this->doOPCCalc();
		$t6 = (float)$prices['billTotal'];
		if ($t6 == $t5) echo __LINE__.'<b style="color:red;">FAIL:when shipping is removed total is still the same</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 
		
		echo 'shipping readded:'; 
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doOPCCalc();
		var_dump($prices['billTotal']); 
		$t7 = (float)$prices['billTotal'];
		if ($t7 != $t5) echo __LINE__.'<b style="color:red;">FAIL:when shipping is readded calculation must be the same as before</b>'; 
		echo "<br />\n"; 
		
		
		
	    #test case 3 - repeat 1
		$this->cart->couponCode = $coupon_code; 
		echo 'Coupon: '.$this->cart->couponCode."<br />\n"; 
		
		
		
		
		
		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doCalc($calc);
		echo 'vmcalc cart: '; var_dump($prices['billTotal']); 
		echo 'vmcalc calc: '; var_dump($calc->_cart->cartPrices['billTotal']); 
		echo "<br />\n"; 
		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doCalc($calc);
		
		$t8 = (float)$prices['billTotal'];
		if ($t8 != $t1) echo __LINE__.'<b style="color:red;">FAIL:readded coupon code results in different calculation</b>'; 
		
		echo 'opcloader:'; 		
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doOPCCalc();
		
		var_dump($prices['billTotal']); 
		$t9 = (float)$prices['billTotal'];
		if ($t9 != $t8) echo __LINE__.'<b style="color:red;">FAIL:calling opc calculation results in different total compared to core VM</b>'; 
		echo "<br />\n"; 

		echo 'no shipping:';
		$this->setCart(0, $payment_id); 
		
		$prices = $this->doOPCCalc();
		$t10 = (float)$prices['billTotal'];
		if ($t10 == $t9) echo __LINE__.'<b style="color:red;">FAIL:shipping was removed but the total did not change</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 
		
		echo 'shipping readded:'; 
		$this->setCart($shipping_id, $payment_id); 
		$prices = $this->doOPCCalc();
		$t10 = (float)$prices['billTotal'];
		if ($t10 != $t4) echo __LINE__.'<b style="color:red;">FAIL:shipping was readded and total must be the same as before</b>'; 
		var_dump($prices['billTotal']); 
		echo "<br />\n"; 
		
		
		
		
		
		
	 }
	 public function doOPCCalc() {
		//$this->cart->prepareCartData(false); 
	    $prices = OPCloader::getCheckoutPrices( $this->cart, false, $other);
		return $prices; 
	 }
	 public function doCalc(&$calc) {
		 //$this->cart->prepareCartData(false); 
		 $calc->getCheckoutPrices($this->cart, false);
		 return $calc->_cart->cartPrices; 
	 }
	 public function setCart($shipping_id, $payment_id) {
		
		$this->cart->virtuemart_shipmentmethod_id = $shipping_id; 
		$this->cart->virtuemart_paymentmethod_id = $payment_id; 
		$this->cart->automaticSelectedShipment = true; 
		$this->cart->automaticSelectedPayment = true; 
		$_REQUEST['virtuemart_shipmentmethod_id'] = $shipping_id; 
		JRequest::setVar('virtuemart_shipmentmethod_id', (int)$shipping_id); 
		$dispatcher = JDispatcher::getInstance();
		$_retValues = $dispatcher->trigger('plgVmOnSelectCheckShipment', array(   &$this->cart));
		$this->cart->setCartIntoSession(); 
	 }
	 public static function getRequire() {
		
		if (!class_exists('VmConfig')) {
		 require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		}
		VmConfig::loadConfig(); 
	
		if (!class_exists('VirtueMartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		
		
		if(!class_exists('calculationHelper')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		
	 }
	 
	 
	 public static function getRequireOPC() {
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
		
		 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	
		$config = new JModelConfig(); 
		$config->loadVmConfig(); 
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 }
}