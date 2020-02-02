<?php
defined('_JEXEC') or die('Restricted access');

class OPCHikaBasket {
	public static function getBasket($withwrapper=true, &$op_coupon='', $shipping='', $payment='', $isexpress=false, $ismulti=0) {
		$ref = OPChikaRef::getInstance(); 
		
		
		$url_itemid = ''; 
		
		$product_rows = array(); 
		$extra_html = ''; 
		
		$no_shipping = OPChikashipping::getShippingEnabled(); 
		
		/*unused variables*/
		$coupon_display_before = ''; 
			  $shipping_select = ''; 
			  $payment_select = ''; 
			  $discount_after = ''; 
			  $tax_display = ''; 
			  $discount_before = ''; 
			  $coupon_display_before = ''; 
			  $opc_show_weight_display = ''; 
			  $ismulti = 0; 
		/*unused variables END*/
		
		
		foreach ($ref->cart->products as $k=>$product) {
			
			$hikaproduct = array(); 
			
			if(empty($product->cart_product_quantity))
			continue;
			if($group && !empty($product->cart_product_option_parent_id))
			continue;
			$ref->productClass->addAlias($product);
			$hikaproduct['product_full_image'] = ''; 
			if (!empty($product->images)) {
			  $image = reset($product->images);
			  if (isset($image->file_path)) {
			  $hikaproduct['product_full_image'] = $ref->imageHelper->uploadFolder_url . $image->file_path;
			 }
			}
			
			$hikaproduct['product_price'] = strip_tags($ref->cartView->getDisplayProductPrice($product, true)); 
		    $hikaproduct['product_quantity'] = $product->cart_product_quantity; 
			$hikaproduct['product_link'] = hikashop_contentLink('product&task=show&cid=' . $product->product_id . '&name=' . $product->alias . $url_itemid, $product); 
			$hikaproduct['product_name'] = $product->product_name;
			$hikaproduct['product_sku'] = $product->product_code; 
			$hikaproduct['product_stock_label'] = ''; 
			$hikaproduct['product_attributes'] = ''; 
			$hikaproduct['info'] = $product; 
			$hikaproduct['product'] = $product; 
			$hikaproduct['product_id'] = $hikaproduct['virtuemart_product_id'] = $product->product_id; 
			$hikaproduct['subtotal'] = $ref->cartView->getDisplayProductPrice($product, false);
			
			$userSSL = OPChikaconfig::get('useSSL', 0); 
			$action_url = '#'; 
			
			$productCopy = $product; 
			$productCopy->cart_item_id = $product->cart_product_id; 
			$productCopy->quantity = $product->cart_product_quantity;
			
			
			 $v = array('product'=>$prow, 
			   'action_url'=>$action_url, 
			   'use_ssl'=>$useSSL, 
			   'useSSL'=>$useSSL,
			   'product' => $productCopy,
			   );
			
			$hasSteps = self::checkSteps($product, $v); 
			  $suffix = ''; 
			  if ($hasSteps) {
			    $suffix = '_steps'; 
			  }
			
			$hikaproduct['update_form'] = OPChikarenderer::fetch('update_form_ajax'.$suffix.'.tpl', $v); 
			$hikaproduct['delete_form'] = OPChikarenderer::fetch('delete_form_ajax.tpl', $v); 
			
			$product_rows[] = $hikaproduct; 
			
			 $span_subtotal = $hikaproduct['subtotal']; 
			  
			  //break; 
			  $extra_html .= '<div class="opccf_confirm_row">
			  <span class="opccf_quantity">'.$product->cart_product_quantity.'</span>
			  <span class="opccf_times">&nbsp;x&nbsp;</span>
			  <span class="opccf_productname">'.$product->product_name.'</span>
			  <span class="opccf_productsubtotal">'.$span_subtotal.'</span>
			  </div>'; 
			
		}
		
				$taxes = round($ref->cart->full_total->prices[0]->price_value_with_tax - $ref->cart->full_total->prices[0]->price_value, $ref->currencyClass->getRounding($ref->cart->full_total->prices[0]->price_currency_id));
				if(!empty($ref->cart->coupon)) {
					if($taxes == 0 || empty($ref->options['price_with_tax']))
					$coupon_display =  $ref->currencyClass->format($ref->cart->coupon->discount_value_without_tax * -1, $ref->cart->coupon->discount_currency_id);
					else
					$coupon_display = $ref->currencyClass->format($ref->cart->coupon->discount_value * -1, $ref->cart->coupon->discount_currency_id);
				}
				else {
				 $coupon_display = ''; 	
				}
				
				
				if(!empty($ref->options['price_with_tax']))
				$subtotal_display = $ref->currencyClass->format($ref->cart->total->prices[0]->price_value_with_tax,$ref->cart->total->prices[0]->price_currency_id);
				else
				$subtotal_display =  $ref->currencyClass->format($ref->cart->total->prices[0]->price_value,$ref->cart->total->prices[0]->price_currency_id);
		
			  $order_shipping = OPChikashipping::getShippingPrice(); 
			  
			  $op_coupon_ajax = self::getCouponHtml(); 
			  
			  $continue_link = OPChikacontinuelink::get(); 
			  
			  
			  
			  $order_total_display = $ref->currencyClass->format($ref->cart->full_total->prices[0]->price_value_with_tax, $ref->cart->full_total->prices[0]->price_currency_id);			  
			  $op_coupon = $op_coupon_ajax; 
			  
			  $disable_couponns = OPChikaconfig::get('coupons_enable', true); 
			  if (empty($disable_couponns))
			  $op_coupon = $op_coupon_ajax = ''; 
			  
			  
			  
			  
			  $vars = array ('product_rows' => $product_rows, 
						   'payment_inside_basket' => '',
						   'shipping_select' => '', 
						   'payment_select' => '', 
						   'shipping_inside_basket' => '', 
						   'coupon_display' => $coupon_display, 
						   'subtotal_display' => $subtotal_display, 
						   'no_shipping' => $no_shipping,
						   'order_total_display' => $order_total_display, 
						   'tax_display' => $tax_display, 
						   'op_coupon_ajax' => $op_coupon_ajax,
						   'continue_link' => $continue_link, 
						   'coupon_display_before' => $coupon_display_before,
						   'discount_before' => $discount_before,
						   'discount_after'=>$discount_after,
						   'order_shipping'=>$order_shipping,
						   'cart' => $ref->cart, 
						   'op_coupon'=>$op_coupon,
						   'opc_show_weight_display'=>$opc_show_weight_display,
						   'cart_id' => $ismulti,
						   
						   );
						   
		$html = OPChikarenderer::fetch('basket.html', $vars); 
		
		return $html; 
		
	}
	
	public static function getCouponHtml() {
		$ref = OPChikaRef::getInstance(); 
			  if (empty($ref->cart->coupon)) {
			    $coupon_text = JText::_('ADD');
			  }
			  else {
				  $coupon_text = JText::sprintf('HIKASHOP_COUPON_LABEL', $ref->cart->coupon->discount_code);
			  }
			  
			  $vars = array('coupon_text'=> $coupon_text, 
			  'coupon_display'=>$coupon_display); 
			  $op_coupon_ajax = OPChikarenderer::fetch('couponField_ajax', $vars); 
			  
			  return $op_coupon_ajax; 
	}
	
	public static function checkSteps($product, $vars) {
       return false; 		
	}
}