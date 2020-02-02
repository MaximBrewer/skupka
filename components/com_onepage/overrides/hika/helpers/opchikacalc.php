<?php
defined('_JEXEC') or die('Restricted access');
class OPCHikaCalc {
	public static function getPreCalcTotals() {
		$ref = OPChikaref::getInstance(); 
		$cart = OPCHikaCart::getCart(); 
		
		$ref->shippingClass->get('reset_cache');
		
		
		$totals = array(); 
		
		$shippings = $ref->shippingClass->getShippings($cart, true);
		$payments = $ref->paymentClass->getPayments($cart, true);
		
		foreach ($shippings as $k=>$m) {
		
		foreach ($payments as $k2=>$pm) {
		
		//$shipping_id = $m->shipping_id; 
		if (!isset($m->shipping_warehouse_id)) $m->shipping_warehouse_id = 0; 
		$shipping_id = 'shipping_radio_0_0__'.$m->shipping_warehouse_id.'__'.$m->shipping_type.'_'.$m->shipping_id;
		$payment_id = $pm->payment_id; 
		//$payment_id = 'payment_radio_0_0__'.$pm->payment_type.'_'.$pm->payment_id;
		
		if (empty($totals[$shipping_id])) $totals[$shipping_id] = array(); 
		if (empty($totals[$shipping_id][$payment_id])) $totals[$shipping_id][$payment_id] = array(); 
		
		
		if (isset($m->shipping_warehouse_id)) {
		  $ware_house_id = (int)$m->shipping_warehouse_id; 
		}
		else {
			$ware_house_id = 0; 
		}
		
		
		
		
		OPCHikaCart::set('cart_shipping_ids', array(0 => $m->shipping_id.'@'.$ware_house_id)); 
		OPCHikaCart::set('cart_payment_id', $pm->payment_id); 
		
		$cart = OPCHikaCart::getCart(); 
		
		$total = $cart->full_total->prices[0]->price_value_with_tax; 
		$totals[$shipping_id][$payment_id] = (array)$cart->full_total->prices[0]; 
		$totals[$shipping_id][$payment_id]['order_shipping'] = $m->shipping_price;
		$totals[$shipping_id][$payment_id]['payment_discount'] = $pm->payment_price;
		$totals[$shipping_id][$payment_id]['shipping_price'] = $m->shipping_price;
		$totals[$shipping_id][$payment_id]['payment_price'] = $pm->payment_price;
		
		$totals[$shipping_id][$payment_id]['order_total'] = $total;
		$totals[$shipping_id][$payment_id]['coupon_discount'] = 0;
		$totals[$shipping_id][$payment_id]['coupon_discount2'] = 0;
		$totals[$shipping_id][$payment_id]['tax'] = 0;
		$totals[$shipping_id][$payment_id]['order_subtotal'] = $cart->full_total->prices[0]->price_value_without_shipping_with_tax;
		//echo '<span style="color:red;">'.var_export($cart->full_total->prices[0], true).'</span>'; 
		
		$totals[$shipping_id][$payment_id]['order_subtotal'] = 0; 
		
		
		 }
		}
		return $totals; 
		
		
	}
}