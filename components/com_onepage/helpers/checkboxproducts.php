<?php

class OPCCheckBoxProducts {
	
	
  public static function getCheckBoxProductsHtml($view, $loader)
  {
	  if (empty($view->cart)) $cart =& VirtuemartCart::getCart(); 
	  else $cart =& $view->cart; 
	  
	  
	  
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	 $productClass = OPCmini::getModel('product'); 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  //include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	  
	  
	  $checkbox_order_start = OPCconfig::get('checkbox_order_start', 0); 
	  
	  if (!empty($checkbox_order_start)) {
		  $checkbox_order_start = floatval($checkbox_order_start); 
		  
		  if (isset($cart->cartPrices['basePrice'])) {
		    $basePrice = $cart->cartPrices['basePrice']; 
		  }
		  else {
			  if (isset($cart->pricesUnformatted['basePrice'])) {
				  $basePrice = $cart->pricesUnformatted['basePrice']; 
			  }
		  }
		  $basePrice = (float)$basePrice; 
		 
		  if ($basePrice < $checkbox_order_start) return ''; 
		  
	  }
	  
	  $checkbox_products = OPCconfig::get('checkbox_products', array()); 
	  $product_price_display = OPCconfig::get('product_price_display', 'salesPrice'); 
	  
	   if (!class_exists('CurrencyDisplay'))
	require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
   $currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
	  
	   $checkbox_products_displaytype = OPCconfig::get('checkbox_products_displaytype', false); 
	  if (empty($checkbox_products)) return ''; 
	  $vars = array(); 
	  $vars['products'] = array(); 
	  foreach ($checkbox_products as $k=>$v)
	  {
		
		  
		  $v = (int)$v; 
		  $product = $productClass->getProduct($v, true, true, false, 1); 
		  if (empty($product)) continue; 
		  if (empty($product->product_name)) continue; 
		  
		  $vars['products'][$v] = array(); 
		  $vars['products'][$v]['product'] = $product; 
		  $vars['products'][$v]['product_name'] = $product->product_name; 
		  
		  $price = 1; 
		  if (isset($product->prices[$product_price_display])) {
		    $price = $product->prices[$product_price_display]; 
			$price = (float)$price; 
			if ($price <= 0.0001) $price = 0; 
		  }
		  else {
			  $price = 0; 
		  }
		  if (!empty($price)) {
		   if (!empty($checkbox_products_displaytype)) {
		    $price = OPCmini::displayPrice($price); 
			
		   }
		   else {
		    $price = $currencyDisplay->createPriceDiv($product_price_display,'', $product->prices,false,true, 1);
		   }
		   $price = str_replace('block', 'inline-block', $price); 
		  }
		  else {
			  $price = ''; 
		  }
		  
		  
		  $price = str_replace('class="', 'class="opc_price_general opc_', $price); 
		  
		  $vars['products'][$v]['price'] = $price; 
		  
		  $vars['products'][$v]['onchange'] = $vars['onchange'] = ' onchange="javascript:Onepage.checkBoxProduct(this);" '; 
		  
		  $vars['products'][$v]['checked'] = ''; 
		  
		  foreach ($cart->products as $cart_id => $p)
		  {
			  $id = (int)$p->virtuemart_product_id; 
			  if ($id === $v) 
			  {
				  $vars['products'][$v]['checked'] = ' checked="checked" '; 
			  }
		  }
		  
		  
	  }
	 
	  if (!empty($checkbox_products_displaytype)) {
		  $html = $loader->fetch($loader, 'checkbox_products_select', $vars); 
	  }
	  else {
	   $html = $loader->fetch($loader, 'checkbox_products', $vars); 
	  }
	  
	  
	  return $html; 
	  
  }
  
  public static function getCheckBoxProductsDynamicLines(&$cart, &$currencyDisplay, &$prices)
  {
	  
	  $checkbox_products_display = OPCconfig::get('checkbox_products_display', false); //empty = cart, true = cart without q, 2 = cart with quantity
	  
	  if (empty($checkbox_products_display)) return; 
	  //case 2:
	  if ($checkbox_products_display !== true) return; 
	  
	  
	    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
	  $checkbox_products = OPCconfig::get('checkbox_products', array()); 
	  $product_price_display = OPCconfig::get('product_price_display', 'salesPrice'); 
	  
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  $productClass = OPCmini::getModel('product'); 
	  
	   
	  //if (!empty($checkbox_products_display)) return array(); 
	  if (empty($checkbox_products)) return array(); 
	  $mainframe = JFactory::getApplication();
	  $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );	 
	  
	  
	  
	  if (empty($cart->products))
	  {
		  if (defined('VM_VERSION') && (VM_VERSION >= 3))
						{
							ob_start(); 
							$cart->prepareCartData(); 
							$zz = ob_get_clean(); 
						}
	  }
	  $ret = array(); 
	  foreach ($cart->products as $cart_id => $p)
	  {
		  if (in_array($p->virtuemart_product_id, $checkbox_products))
		  {
			  
			 
			 $ret2 = array(); 
			 $ret2['name'] = $p->product_name; 
			 
			 if (isset($prices[$cart_id]))
			 {
				 
			 $price = $prices[$cart_id][$product_price_display]; //$product->prices[$product_price_display]; 
			 }
			 else
			 if (isset($prices[$p->virtuemart_product_id]))
			 {
				 $price = $prices[$cart_id][$product_price_display]; //$product->prices[$product_price_display]; 
			 }
			 if (empty($price)) $price = '0.00001'; 
			 
			 $price = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $price,false);
			 $ret2['value'] = $price; 
		     
			 $ret[] = $ret2; 
		  }
	  }
	  
	  
	  
	  
	  
	  return $ret; 
	  
  }
  
  
}