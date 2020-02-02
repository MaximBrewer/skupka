<?php
defined('_JEXEC') or die('Restricted access');

class OPChikashipping {
	public static function getShipToOpened() {
		return false; 
	}
	
	public static function getShippingEnabled() {
		//if shiping enabled, return false
		//if shipping disabled, return true
		
		return false; 
	}
	
	public static function getShipToEnabled() {
		if (!defined('NO_SHIPTO')) define('NO_SHIPTO', false); 
		return false; 
	}
	
	public static function getShippingPriceRAW() {
		$shipping_price = null; 
		$ref = OPChikaRef::getInstance(); 
		foreach($ref->cart->shipping as $shipping) {
					if(!isset($shipping->shipping_price) && isset($shipping->shipping_price_with_tax) ) {
						$shipping->shipping_price = $shipping->shipping_price_with_tax;
					}
					if(isset($shipping->shipping_price)) {
						if($shipping_price === null)
							$shipping_price = 0.0;
						if($taxes == 0 || empty($ref->this->options['price_with_tax']) || !isset($shipping->shipping_price_with_tax))
							$shipping_price += $shipping->shipping_price;
						else
							$shipping_price += $shipping->shipping_price_with_tax;
					}
				}
				return $shipping_price; 
	}
	public static function getShippingPrice() {
		$shipping_price = self::getShippingPriceRAW(); 
		if (!is_null($shipping_price)) {
			$ref = OPChikaRef::getInstance(); 
			return $ref->currencyClass->format($shipping_price, $ref->cart->full_total->prices[0]->price_currency_id);
		}
		return ''; 
	}
	
}