<?php 
/**   
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
class OPCcarthelper {
  public static function deleteCart()
  {
	
     if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	  
	   
	 if (!class_exists('VirtueMartCart'))
	   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	   
	 $cart = VirtueMartCart::getCart(false); 
	 if (empty($cart)) return; 
	 if (!empty($cart->products))
	 $cart->emptyCart();
	
	 
	  if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		  $calc = calculationHelper::getInstance(); 
		  
		  if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		  else $vm2015 = false; 
			if ($vm2015)
			{
			$calc->setCartPrices(array()); 
			}
			
			
  }
  
     static function tableExists($table)
  {
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
 
   $q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   if (!empty($r)) 
	   {
	   
	   return true;
	   }
   return false;
  }

  
  public static function installTable()
  {
	  
	if (self::tableExists('virtuemart_plg_opccart')) return; 
	
	$isM55 = true; 
	if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php')) {
       require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	   $isM55 = OPCmini::isMysql('5.6.5'); 
	}
  if (!$isM55) {
     $myisam = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opccart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `hash` varchar(255) CHARACTER SET ascii NOT NULL,
  `user_id` int(11) NOT NULL,
  `extra` varchar(255) NOT NULL DEFAULT \'\',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT \'0\',
  `modified` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `modified_by` int(11) NOT NULL DEFAULT \'0\',
   PRIMARY KEY (`id`),
   UNIQUE KEY `hash` (`hash`),
   KEY `mod` (`modified`)
   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380'; 
   
   $inno = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opccart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `hash` varchar(255) CHARACTER SET ascii NOT NULL,
  `user_id` int(11) NOT NULL,
  `extra` varchar(255) NOT NULL DEFAULT \'\',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT \'0\',
  `modified` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `modified_by` int(11) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `mod` (`modified`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380'; 
  }
  else
  {
	   $myisam = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opccart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `hash` varchar(255) CHARACTER SET ascii NOT NULL,
  `user_id` int(11) NOT NULL,
  `extra` varchar(255) NOT NULL DEFAULT \'\',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT \'0\',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL DEFAULT \'0\',
   PRIMARY KEY (`id`),
   UNIQUE KEY `hash` (`hash`),
   KEY `mod` (`modified`)
   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380'; 
   
   $inno = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_plg_opccart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `hash` varchar(255) CHARACTER SET ascii NOT NULL,
  `user_id` int(11) NOT NULL,
  `extra` varchar(255) NOT NULL DEFAULT \'\',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL DEFAULT \'0\',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `mod` (`modified`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380'; 
  }
   $db = JFactory::getDBO(); 
   $db->setQuery($inno); 
   $db->query(); 
   $e = $db->getErrorMsg(); 
   if (!empty($e))
     {
       $db->setQuery($myisam); 
       $db->query(); 
       $e = $db->getErrorMsg(); 
	   if (!empty($e))
	     {
		   JFactory::getApplication()->enqueueMessage('OPC Cart Plugin could not create tables: '.$e, 'error');
		 }
	 }
  }
  public static function cartKeyToAttributes($cart_key)
   {
     $a1 = explode('::', $cart_key); 
	   
	   if (count($a1) <= 1) {
	   $customPrices = null; 
	   return $customPrices; 
	   }
	   $a2 = explode(';', $a1[1]); 
	   
	   if (count($a2) <= 1) 
	   {
	   $customPrices = null;
	   return $customPrices; 
	   }
	   
	   
	   foreach ($a2 as $val)
	    {
		  if (empty($val)) continue; 
		  $a3 = explode(':', $val); 
		  
		  if (count($a3) <= 1) {
		   continue; 
		  }
		  
		  $customPrices[0][$a3[1]] = $a3[0]; 
		  
		}
		return $customPrices; 
   }
   public static function getCartHashLine($cart_key, $quantity, $custom='')
   {
     if (!empty($custom)) 
	 {
	   $custom = json_encode($custom); 
	   $custom = md5($custom);
	 }
	 
	 if (is_array($quantity)) $quantity = json_encode($quantity); 
	 if (is_array($cart_key)) $cart_key = json_encode($cart_key); 
	 if (is_array($custom) && (empty($custom))) $custom = ''; 
	 
	 
	 
     return '___'.$cart_key.'_'.$quantity.'_'.$custom; 
   }
   
   public static function setVMLANG() {
	 
	  if (!class_exists('VmConfig'))
		    {
			     if (!class_exists('VmConfig'))
				require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
				VmConfig::loadConfig ();

			}
			if(!defined('VMLANG'))		
			if (method_exists('VmConfig', 'setdbLanguageTag')) {
			   VmConfig::setdbLanguageTag();
			}
	 
    
	if ((!defined('VMLANG')) && (!empty(VmConfig::$vmlang))) {
	  define('VMLANG', VmConfig::$vmlang); 
	}
		
 }
   
  public static function getProducts($hash, $user_id=0)
   {
    
     if (defined('GETPRODUCTSCART')) return; 
	 else define('GETPRODUCTSCART', 1); 
	 
     $productLine = OPCcarthelper::getLine($hash, $user_id);
	
	
	 if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	   
	  self::setVMLANG(); 
	   
	 if (!class_exists('VirtueMartCart'))
	   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	 
	 if (empty($productLine)) return; 
	 if (empty($productLine['cart'])) return; 
	
	
	
	
	if (defined('VM_VERSION') && (VM_VERSION >= 3))
	{
	    $serialized_cart =   $productLine['cart'];
		$test = @unserialize($serialized_cart); 
		
		if (!empty($test))
		{
		 
		 
		 // just a small customization to support vm3 cart: 
		 /*
		 if (class_exists('ReflectionClass'))
		 {
		  $reflectionClass = new ReflectionClass('VirtueMartCart');
		  $_c = $reflectionClass->getProperty('_cart'); 
		  //$reflectionClass->setStaticPropertyValue('_cart', $test); 
		  $_c->setAccessible(true);
		  $_c->setValue('');
		  $_c->setValue($test);
		 
		 }
		 */
		 
		 
		 $jdata = json_encode($test); 
		 //$newcart = VirtuemartCart::getCart(true, array(), $jdata); 
		 $newcart = VirtuemartCart::getCart(); 
		 
		 $newcart->cartProductsData = $test->cartProductsData; 
		 $newcart->BT = $test->BT; 
		 $newcart->ST = $test->ST; 
		 $newcart->STsameAsBT = $test->STsameAsBT; 
		 $newcart->products = array(); 
		 $newcart->prepareCartData(true); 
		 $newcart = VirtuemartCart::getCart(); 
		 
		 
		 
		
		 $cart_hash = ''; 
  		foreach ($newcart->cartProductsData as $k=>$i)
		{
		 
		  $cart_hash .= self::getCartHashLine($i['virtuemart_product_id'], $i['quantity'], $i['customProductData'] ); 

		}
		
		  return $cart_hash; 		  
		}
		
		
		return; 
	}
	 
	 //VM2 code below: 
	 
	 $products2 = $productLine['cart']; 
	 $pr = $products2; 
	 if (empty($pr)) return; 
	
	  
	 $products = json_decode($pr, true); 
	 if (empty($products)) return; 
	 //if (!is_array($cart)) return; 
	
	
	 
	
	 $pModel = VmModel::getModel('product');
	 
	 $cart = VirtueMartCart::getCart(false); 
	 $added = 0; 
	 
	
	 
	 $saved = JRequest::get('default'); 
	 
	 
	 $i = 0; 
	 $cart_hash = ''; 
	 foreach ($products as $key=>$product)
	  {
	    
		
		{
		
		
		
		$cart_key = $product['cart_item_id']; 
		
		
		
		$customPrices = self::cartKeyToAttributes($cart_key); 
		
		
		JRequest::setVar('customPrice', $customPrices); 
		
		
		
		}
		
		JRequest::setVar('virtuemart_category_id', array($i=>$product['virtuemart_category_id']), 'default'); 
		JRequest::setVar('virtuemart_product_id', array($i => (int)$product['virtuemart_product_id'])); 
		JRequest::setVar('quantity', array($i => (int)$product['quantity'])); 

		
		$cart_hash .= self::getCartHashLine($cart_key, $product['quantity']); 
		
		$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array');
		$selected = JRequest::getVar ('virtuemart_product_id',0);
		
		
		$s = ''; 
		$tmpProduct = $pModel->getProduct((int)$product['virtuemart_product_id'], true, true,true,1);
		$tmpProduct = $pModel->getProduct((int)$product['virtuemart_product_id'], true, true,true,(int)$product['quantity']);
		
		$cart->add($virtuemart_product_ids, $s); 
		
		$ign = array('customPlugin', 'quantity', 'customfieldsCart', 'customPrices', 'customfields'); 
		
		JRequest::setVar('customPrice', null); 
		
		
		
		
		$i++; 
	  }
	 
	 if (isset($saved['virtuemart_product_id']))
	 JRequest::setVar('virtuemart_product_id', $saved['virtuemart_product_id'], 'default', true); 

	 if (isset($saved['virtuemart_category_id']))
	 JRequest::setVar('virtuemart_category_id', $saved['virtuemart_category_id'], 'default', true); 

	 if (isset($saved['customPrice']))
	 JRequest::setVar('customPrice', $saved['customPrice'], 'default', true); 

	 if (isset($saved['quantity']))
	 JRequest::setVar('quantity', $saved['quantity'], 'default', true); 

	 
	 if (method_exists($cart, 'setPreferred'))
	 $cart->setPreferred();
	 
	 if (method_exists($cart, 'prepareCartViewData'))
	 $cart->prepareCartViewData(); 
	 
	 	if (method_exists($cart, 'prepareCartData'))
			{
				$cart->prepareCartData(false); 
			}

	 
	 
	 $cart->setCartIntoSession();
	 return $cart_hash;
	 
	 if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		  $calc = calculationHelper::getInstance(); 
		  
		  if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		  else $vm2015 = false; 
			if ($vm2015)
			{
			  $calc->setCartPrices(array()); 
			}
	 $cart->virtuemart_shipmentmethod_id = 0; 
	 $cart->payment_shipmentmethod_id = 0; 
     $cart->setCartIntoSession(); 			
	 
   }
   
   public static function checkLast()
   {
     $view = JRequest::getVar('view', ''); 
	 $controller = JRequest::getVar('controller', ''); 
	 $task = JRequest::getVar('task', ''); 
	 $option = JRequest::getVar('option', ''); 
	 $cmd = JRequest::getVar('cmd', ''); 
	 
	 
	 if (($option == 'com_virtuemart') || ($option = 'com_onepage'))
	 if ((($view == 'cart') || ($controller == 'cart')) || ($view=='opc'))
	 if (($task == 'delete') || ($cmd == 'update_product'))
	  {
		  
	     if (!class_exists('VmConfig'))	  
	     {
	       require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	       VmConfig::loadConfig(); 
	     }
	   
	   if (!class_exists('VirtueMartCart'))
	      require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	   
	   $cart = VirtueMartCart::getCart(false); 
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
		  if ((count($cart->cartProductsData) == 1) || (empty($cart->cartProductsData))) {

			  return true; 
		  }
		  else return false; 
		}
	    else {
	    if (count($cart->products)==1)
		{
		
	    return true; 
		}
		else return false; 
		}
	  }
	  
	  
	  if ($task === 'updatecart') {
	    $q = JRequest::getVar('quantity', -1); 
		if (empty($q)) return true; 
		if (is_array($q)) {
			$sum = 0; 
		  foreach ($q as $qq) {
			  $sum += $qq; 
		  }
		  if (empty($sum)) return true; 
		}
		
	  }
	  
	  
	  return false; 
   }
  public static function storeProducts($hash)
  {
  
     if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	   
	   $cart = OPCcarthelper::hasProducts(true);
	   OPCcarthelper::updateLine($hash, $cart); 
	   return; 
	  
	  
  }
  public static function pD()
  {
    return;
    $x = debug_backtrace(); 
	foreach ($x as $y) echo $y['file'].' '.$y['line']."<br />\n"; 
	die(); 
  }
  public static function removeLine($hash)
   {
	   
	   OPCcarthelper::installTable(); 
      //self::pd(); 
      $db = JFactory::getDBO(); 
	  $q = "delete from #__virtuemart_plg_opccart where hash = ".$db->quote($hash)." limit 1"; 
	try {
		$db->setQuery($q); 
	    $db->query(); 
	}
	catch (Exception $e)
	{
		
	}
	
   }
  public static function updateLine($hash, $car2t)
  {
	  
         if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	   
	 if (!class_exists('VirtueMartCart'))
	   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	   
	   
	  
	   $cart = VirtueMartCart::getCart(false);   
	   if (defined('VM_VERSION') && (VM_VERSION >= 3))
	   {
	      $cartS = $cart->getCartDataToStore(); 
		  unset($cartS->_cryptedFields); 
		  
		  $bt = array(); 
		  if (!empty($cartS->BT))
		  {
		  
		  $keys = $cartS->BT; 
		 
		  foreach ($cartS->BT as $v=>$k)
		   {
		     if (substr($v, 0, 1)=='*') continue; 
			 if (substr($v, 0, 1)=='_') continue; 
		     if (stripos($v, "\000")===0) continue; 
			 
		     $bt[$v] = $k; 
		   }
		     unset($cartS->BT); 
			$cartS->BT = $bt; 

		  }
		  $st = array(); 
		   if (!empty($cartS->ST))
		   {
		  foreach ($cartS->ST as $v=>$k)
		   {
		     if (substr($v, 0, 1)=='*') continue; 
			 if (substr($v, 0, 1)=='_') continue; 
		     if (stripos($v, "\000")===0) continue; 
		     $st[$v] = $k; 
		   }
		    unset($cartS->ST); 
			$cartS->ST = $st; 
		   }
		   

		  $cartS = serialize($cartS); 
		  
		  
		   
	   }
	 else
	 {
    $products = $cart->products;
	if (empty($products)) return; 
	/*
	foreach ($products as $key=>$val)
	 foreach ($val as $key2 => $val2)
	 {
	    if (is_object($val))
		if (get_class($val) != 'stdClass')
		unset($products[$key]->$key2); 
	    if (empty($products[$key]->quantity)) 
		unset($products[$key]); 
	 }
	 */
	 
	
	 if (empty($products)) return; 
	 
	 
	 $store = array(); 
	 foreach ($products as $key=>$val)
	 {
	 $product = array(); 
	 $product['virtuemart_manufacturer_id'] = $val->virtuemart_manufacturer_id; 
	 $product['quantity'] = $val->quantity ; 
	 $product['virtuemart_category_id'] = $val->virtuemart_category_id; 
	 $product['virtuemart_product_id'] = $val->virtuemart_product_id; 
	 $product['cart_item_id'] = $val->cart_item_id; 
	 $store[$product['cart_item_id']] = $product; 
	 
	 }
	
	$cartS = json_encode($store); 
	if (empty($cartS)) return; 
	
	}
	OPCcarthelper::installTable(); 
    $db = JFactory::getDBO(); 
    $cartS = $db->quote($cartS); 
	$hash = $db->quote($hash); 
	$user_id = (int)JFactory::getUser()->get('id'); 
    $q = "insert into `#__virtuemart_plg_opccart`  (`id`, `cart`, `hash`, `user_id`, `created`, `created_by`, `modified`, `modified_by`) ";
	$q .= " values (NULL, ".$cartS.", ".$hash.", ".$user_id.", NOW(), ".$user_id.", NOW(), ".$user_id.") on duplicate key "; 
	$q .= "update `cart` = ".$cartS.", `modified`=NOW(), `modified_by`=".$user_id." ";
	
	
	
	try
	{
	$db->setQuery($q); 
	$db->query(); 
	}
	catch (Exception $e)
	{
		
	}
	
	
  }
  
  public static function removeOld($timeout)
  {
    $unix = time() - $timeout; 
	
	$time = JFactory::getDate($unix); 
	if (method_exists($time, 'toMySQL'))
	$mysqltime = $time->toMySQL(); 
	else $mysqltime = $time->toSql(); 
	OPCcarthelper::installTable(); 
	$db = JFactory::getDBO(); 
	$q = "delete from #__virtuemart_plg_opccart where (modified < ".$db->quote($mysqltime)." and modified > 0)"; 
	try
	{
	$db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); 
	}
	catch (Exception $e)
	{
		
	}
	
	
  }
  
  public static function getLine($hash, $user_id=0)
   {
	  if (!self::tableExists('virtuemart_plg_opccart')) return array(); 
     $db = JFactory::getDBO(); 
	 $q = "select * from `#__virtuemart_plg_opccart` where hash = ".$db->quote($hash)." "; 
	 if (!empty($user_id)) $q .= ' and created_by = '.(int)$user_id; 
	 $q .= " limit 0,1"; 
	 try
	 {
	 $db->setQuery($q); 
	 $res = $db->loadAssoc(); 
	 }
	 catch (Exception $e)
	 {
		 return array(); 
	 }
	 if (empty($res)) return array(); 
	 $ret = array(); 
	 foreach ($res as $key=>$val)
	  {
	    $ret[$key] = $val; 
	  }
	 return $ret; 
	 
   }
   
   public static function hasProducts($retObj=false)
	{
	
	 if (!class_exists('VmConfig'))	  
	   {
	    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	    VmConfig::loadConfig(); 
	   }
	
	if (!class_exists('VmImage'))
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' . DIRECTORY_SEPARATOR . 'image.php');

	 if (!class_exists('VirtueMartCart'))
	   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	 
	 if (!class_exists('calculationHelper'))
		require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'calculationh.php');
		
		
		 $cart = VirtueMartCart::getCart(false); 
		 
		 
	
	/*	 
	if (method_exists($cart, 'prepareCartData'))
			{
				$cart->prepareCartData(false); 
			}
		*/
	 $session = JFactory::getSession(); 
	 $last = $session->get('opccart_last_hash', null, 'opc'); 
	 
	 if ((!empty($cart->products)) || (!empty($cart->cartProductsData )))
	  {
	    $cart_hash = ''; 
		if (!empty($cart->cartProductsData))
		{
		foreach ($cart->cartProductsData as $k=>$i)
		{
		  
		  $cart_hash .= self::getCartHashLine($i['virtuemart_product_id'], $i['quantity'], $i['customProductData'] ); 
		}
		}
		else
		if (!empty($cart->products))
		{
	    foreach ($cart->products as $key=>$val)
		  {
		     $cart_hash .= self::getCartHashLine($key, $val->quantity); 
		  }
		 } 
		 
	  $session->set('opccart_last_hash', $cart_hash, 'opc'); 
	  return $cart_hash; 
	  }
	 else {
		 if (!empty($last)) {
		    
		 }
		 return false; 
		 
	 }
	
	   $session = JFactory::getSession(); 
	   $cartS = $session->get('vmcart', 0, 'vm');
	   if (empty($cartS)) return false; 
	  try {
	    $cart = @unserialize($cartS); 
		if (empty($cart)) return;
	  } catch (Exception $e) {
		 return null;
	  }
	  
	  if (!empty($cart->products)) 
	  {
	   if ($retObj) return $cart; 
	   return true; 
	  }
	  else return false; 
	  
	  return null; 
	}

}