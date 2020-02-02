<?php
defined('_JEXEC') or die('Restricted access');

class OPCHikaCart {
	public static function set($key, $val) {
		$ref = OPChikaref::getInstance(); 
		$cart_id = (int)$ref->cart_id;
		
		if (!empty($cart_id)) {
		 $db = JFactory::getDBO(); 
		 $cart_table = hikashop_table('cart');
		 $ins_val = $val; 
		 if (($key === 'cart_shipping_ids') && (is_array($val))) {
			 $ins_val = implode(',', $val);
		 }
		 elseif (($key === 'cart_coupon') && (is_array($val))) {
			 $ins_val = implode("\r\n", $val);
		 }
		 
		 $q = 'update `'.$db->escape($cart_table).'` set `'.$db->escape($key)."` = '".$db->escape($ins_val)."', `cart_modified` = ".(int)time()." where cart_id = ".(int)$cart_id;
		 //echo $q."<br />\n"; 
		 $db->setQuery($q); 
		 $db->query(); 
		}
		$cart = self::getCart(); 
		$cart->{$key} = $val; 
		
	}
	
	public static function &getCart() {
		OPChikacache::clear(); 
		$ref = OPCHikaRef::getInstance(); 
		$cart = $ref->checkoutHelper->getCart(true);
		return $cart;
	}
		
}