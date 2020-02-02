<?php
defined('_JEXEC') or die('Restricted access');

class OPCHikaCache {
	public static function clear() {
			   $ref = OPChikaRef::getInstance(); 
			   $cartClass = hikashop_get('class.cart');
			   $cartClass->get('reset_cache'); 
			   
			   $checkoutHelper = hikashop_get('helper.checkout');
			   $checkoutHelper->cart = false; 
			   
			   $addressClass = hikashop_get('class.address');
			   $addressClass->cleanCaches(); 
			   
			   $zoneClass = hikashop_get('class.zone');
			   $zoneClass->get('reset_cache'); 
			   
			   $shippingClass = hikashop_get('class.shipping');
			   //$shipping = $shippingClass->get('reset_cache');
			  
			   
			   
	}
}