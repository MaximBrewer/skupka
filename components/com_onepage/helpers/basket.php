<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCBasket {
	
 
public static function calculateSubtotal($cart)
{
	if (isset($cart->prices))
	$prices = $cart->prices; 
	else
	if (isset($cart->pricesUnformatted))
	$prices = $cart->pricesUnformatted; 
	
	$subtotal_price_display = OPCconfig::get('subtotal_price_display', 'salesPrice'); 
	
	
	
	if (empty($subtotal_price_display)) $subtotal_price_display = 'salesPrice'; 
	
	if (isset($prices[$subtotal_price_display]))
	if (($subtotal_price_display != 'diffTotals') && ($subtotal_price_display != 'product_subtotal'))
	{
		
	$order_subtotal = $prices[$subtotal_price_display]; 
	
	}
	
	
	if ($subtotal_price_display == 'diffTotals')
	{
		// difference of billTotal and billTaxAmount
	   $order_subtotal = $prices['billTotal'] - $prices['billTaxAmount']; 
	}
	
	if ($subtotal_price_display === 'product_subtotal')
	{
		$product = array(); 
		$subtotal = 0; 
		$product_price_display = OPCconfig::get('product_price_display', 'salesPrice'); 
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
			$products = $cart->cartProductsData; 
		}
		else {
		  $products = $cart->products;
		}
		
		
		
			foreach( $products as $pkey =>$pX )
			{
				
				if (empty($pX)) continue; 
				
				if (isset($cart->products[$pkey])) $prow = $cart->products[$pkey]; 
				else 
				{
					$px2 = (object)$pX; 
					$pid = (int)$pX2->virtuemart_product_id; 
					$q = (float)$pX2->quantity; 
					$productModel = VmModel::getModel('product');
					$prow = $productModel->getProduct($pid, true, true, true, $q);
					foreach ($pX2 as $pkx => $pkv) {
					   $prow->$pkx = $pkv; 
					}
				}
				
				
				
				if  (isset($cart->prices[$pkey]))
				  $currentPrice = $cart->prices[$pkey]; 
			  else
				  if (isset($prow->prices))
				  $currentPrice = $prow->prices; 
			  
			  
			  
			  if ($product_price_display == 'salesPrice')
			  {
			  if (isset($prow->prices))
			  $product['product_price'] = $currentPrice['salesPrice'];
			  else
			  if (isset($prow->salesPrice))
			  $product['product_price'] = $prow->salesPrice;
			  else
			   {
			     if (isset($prow->basePriceWithTax))
				 $product['product_price'] = $prow->basePriceWithTax; 
				 else
			     if (isset($prow->basePrice))
				 $product['product_price'] = $prow->basePrice; 
				 
			   }
			  }
			  else
			  {
			   if (isset($prow->prices))
			   $product['product_price'] = $currentPrice[$product_price_display];
			   else
			   {
			   if (isset($prow->$product_price_display))
			   $product['product_price'] = $prow->$product_price_display;
			   else 
			   if (isset($prow->salesPrice))
			     $product['product_price'] = $prow->salesPrice; 
			   }
			  }
			   if (!isset($product['product_price']))
			   {
			      if (isset($cart->pricesUnformatted[$pkey])) {
				  $price = $cart->pricesUnformatted[$pkey];
				  
				  $product['product_price'] = $price[$product_price_display]; 
				  }
			      
				  
			   }
			   
			   
			   
			   
			   $prow->quantity = (float)$prow->quantity; 
			   
			   if (empty($product['product_price']))
			   {
			      $product_price_display = 'salesPrice'; 
			      $price = $cart->pricesUnformatted[$pkey];
				  $product['product_price'] = $price['salesPrice']; 
			   }
			   
			    $price_raw = $product['product_price']; 
			   $product['subtotal'] = $prow->quantity * $price_raw;
			   
			
			   $subtotal += $product['subtotal']; 
			}
			
			return $subtotal; 
			
	}
	
	if (empty($order_subtotal))
	 {
	   $order_subtotal = $prices['salesPrice'];
	   $subtotal_price_display = 'salesPrice'; 

	 }
	
	return $order_subtotal; 
	
}
  public static function checkSteps($product, &$v) {
	  if (!empty($product->step_order_level)) {
	    $step = (int)$product->step_order_level; 
	  }
	  else
	  {
		  $step = 1; 
	  }
		  
		  
		  
		  if (!empty($product->max_order_level))
		  $max = (float)$product->max_order_level; 
	      else
		  $max = 0; 
	  
		  if (!empty($product->min_order_level)) {
		   $min = (float)$product->min_order_level; 
		  }
		  else $min = 0; 
		  
		  if (empty($min)) $min = $step;

		$v['step'] = $step; 
		$v['min'] = $min; 
		$v['max'] = $max; 
		
		if (!empty($max))
		if ($step !== 1) {
			$options = '<option value="0">0</option>'; 
			for ($i=$min; $i<=$max; $i=$i+$step ) {
				  $options .= '<option value="'.$i.'" '; 
				  $f1 = (float)$product->quantity; 
				  $f2 = (float)$i; 
				  if ($f1 === $f2) $options .= ' selected="selected" '; 
				  
				  
				  $options .= '>'; 
				  $i1 = round($i);
				  if ($i1 == $i) $options .= (int)$i; 
				  else $options .=  number_format($i, 2); 
				  $options .= '</option>'; 
				}
			
			$v['options'] = $options; 
			
			return true; 
		}
		return false; 
  
  }
  public static function getBasket(&$ref, $OPCloader, $withwrapper=true, &$op_coupon='', $shipping='', $payment='', $isexpress=false, $ismulti=0)
  {
       require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
   include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
   	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'cart_override.php'); 
   
   //before processing basket, let's re-check the quantities: 
  
   $OPCcheckout = new OPCcheckout($ref->cart); 
   $cart =& $ref->cart; 
   
   $rC = false; 
   OPCcheckout::$current_cart =& $ref->cart; 
   
   if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
			$products = $ref->cart->cartProductsData; 
		}
		else {
		  $products = $ref->cart->products;
		}
   
   foreach( $products as $pkey => $pX ) {
	  
	  if (isset($ref->cart->products[$pkey])) $prow = $ref->cart->products[$pkey]; 
				else 
				{
					
					$px2 = (object)$pX; 
					$pid = (int)$pX2->virtuemart_product_id; 
					$q = (float)$pX2->quantity; 
					$productModel = VmModel::getModel('product');
					$prow = $productModel->getProduct($pid, true, true, true, $q);
					foreach ($pX2 as $pkx => $pkv) {
					   $prow->$pkx = $pkv; 
					}
					
				}
	  
	   
      $quantity = $qO = (float)$prow->quantity; 
	  $e = ''; 
	  $adjustQ = true; 
	  $OPCcheckout->checkForQuantities($prow, $quantity, $e, $adjustQ); 
	  
	  
	  
	  if ($qO !== $quantity) {
	    //change quantity: 
		if (is_object($ref->cart->products[$pkey]))
		$ref->cart->products[$pkey]->quantity = $quantity; 
	
	
	
		if (isset($ref->cart->cartProductsData))
			if (isset($ref->cart->cartProductsData[$pkey]))
				if (isset($ref->cart->cartProductsData[$pkey]['quantity']))
				{
					if (is_array($ref->cart->cartProductsData[$pkey]))
					$ref->cart->cartProductsData[$pkey]['quantity'] = $quantity; 
				
					$rC = true; 
					if (isset($ref->cart->_productAdded))
							{
								$ref->cart->_productAdded = true; 
							}
				}
				      
	  }
	  ob_start(); 
	  if (method_exists($ref->cart, 'prepareCartData'))
	  $ref->cart->prepareCartData(); 
	  $zz = ob_get_clean(); 
	  if (!empty($e)) {
		   JFactory::getApplication()->enqueueMessage($e, 'error'); 
		  
		 }
   
   
   
   
   } // end of foreach products
   
   
   $extra_html = ''; 
   
   
   $has_k2 = OPCloader::tableExists('k2mart'); 
  
   if (!class_exists('ShopFunctions'))
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
   
   if (!method_exists('ShopFunctions', 'convertWeightUnit'))
   {
     $opc_show_weight = false; 
   }
   else {
	   $opc_show_sdesc = OPCconfig::get('opc_show_sdesc', false); 
   }
   
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	$productClass = OPCmini::getModel('product'); //new VirtueMartModelProduct(); 

   if (!class_exists('CurrencyDisplay'))
	require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
   $currencyDisplay = CurrencyDisplay::getInstance($ref->cart->pricesCurrency);
   
  
	
   $google_html = '';
    
    $VM_LANG = new op_languageHelper(); 
		  $product_rows = array(); 
		 
		  
		  if (empty($ref->cart))
		  {
		    $ref->cart =  VirtueMartCart::getCart();
		  }
		  
			$vm2015 = false; 
		  $ref->cart->prices = $ref->cart->pricesUnformatted = OPCloader::getCheckoutPrices(  $ref->cart, false, $vm2015, 'opc');
		 
		 
		 
		  
		  $useSSL = VmConfig::get('useSSL', 0);
		  
		  //$action_url = $OPCloader->getActionUrl($OPCloader, true); 
		  if (defined('VM_VERSION') && (VM_VERSION >= 3))
		  $action_url = JRoute::_('index.php?option=com_virtuemart&view=cart&task=updatecart&nosef=1'); 
		  else
		  $action_url = JRoute::_('index.php?option=com_virtuemart&view=cart&task=update&nosef=1'); 
		  
		  $xi=0; 
		  
		  if (isset($currencyDisplay->_priceConfig))
		  $savedConfig = $currencyDisplay->_priceConfig; 
			
			$product_price_display = OPCconfig::get('product_price_display', 'salesPrice'); 
			$subtotal_price_display = OPCconfig::get('subtotal_price_display', 'salesPrice'); 
			$coupon_price_display = OPCconfig::get('coupon_price_display', 'salesPriceCoupon'); 
			$other_discount_display = OPCconfig::get('other_discount_display', 'billDiscountAmount');
			$show_single_tax = OPCconfig::get('show_single_tax', false); 
			$opc_dynamic_lines = OPCconfig::get('opc_dynamic_lines', true); 
			
		   if (empty($product_price_display)) $product_price_display = 'salesPrice'; 
			  //$test_product_price_display = array($product_price_display, 'salesPrice', 'basePrice', 'priceWithoutTax', 'basePriceWithTax', 'priceBeforeTax', 'costPrice'); 
			  $test_product_price_display = array($product_price_display, 'salesPrice', 'basePrice', 'priceWithoutTax', 'basePriceWithTax', 'priceBeforeTax'); 
			  // check price config
			  $testf = false; 
			  foreach ($test_product_price_display as $product_price_display_test)
			  {
			  
			   $test = $currencyDisplay->createPriceDiv($product_price_display,'', '10',false,false, 1);
			   if (empty($test)) 
			    {
				   if (isset($currencyDisplay->_priceConfig))
				   	if (isset($currencyDisplay->_priceConfig[$product_price_display_test]))
					if (empty($currencyDisplay->_priceConfig[$product_price_display_test][0]))
					$currencyDisplay->_priceConfig[$product_price_display_test] = array(1, -1, 1);
			  
				  $testf = true; 
		   
				}
				else
				{
				  if (!isset($product_price_display_test2))
				  $product_price_display_test2 = $product_price_display_test; 
				}
			  }
			  
			  if (empty($testf))
			  $product_price_display = $product_price_display_test2; 
		  
		  $totalw = 0; 
		  
		
		  
		  $to_weight_unit = VmConfig::get('weight_unit_default', 'KG'); 
		  
		  
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
			$products = $ref->cart->cartProductsData; 
		}
		else {
		  $products = $ref->cart->products;
		}

		  
		  
		  	foreach( $products as $pkey =>$pX )
			{
				
				 if (empty($pX)) continue; 
				 
				 if (isset($ref->cart->products[$pkey])) $prow = $ref->cart->products[$pkey]; 
				else 
				{
				
					
					$px2 = (object)$pX; 
					$pid = (int)$pX2->virtuemart_product_id; 
					$q = (float)$pX2->quantity; 
					$productModel = VmModel::getModel('product');
					$prow = $productModel->getProduct($pid, true, true, true, $q);
					foreach ($pX2 as $pkx => $pkv) {
					   $prow->$pkx = $pkv; 
					}
					
				}
				
				
			if (!empty($opc_show_weight))
			 {
			   $totalw += (ShopFunctions::convertWeightUnit ((float)$prow->product_weight, $prow->product_weight_uom, $to_weight_unit) * (float)$prow->quantity);
			 }
			
			  $product = array();
			  $id = $prow->virtuemart_media_id;
			  if (empty($id)) $imgf = ''; 
			  else
			  {
			  /*
			  if (method_exists($productClass, 'addImages'))
			  {
			  $productClass->addImages($prow);
			  
			  
			  }
			  */
			  
			  {
			  if (is_array($id)) $id=reset($id); 
			  //$imgf = $OPCloader->getImageFile($id); 
			  
			      require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
				  
				  $imgf = OPCimage::getImageFile($id); 

			  
			  
			  }
			  }
			  
			  $product['product_full_image'] = $imgf;
			  $session = JFactory::getSession(); 
	           $urls = $session->get('product_urls', array()); 
			   if (!empty($urls)) $urls = json_decode($urls, true); 	   
		       if ((!empty($urls)) && (isset($urls[$prow->virtuemart_product_id])))
			   {
				   
				   $prow->url = $prow->link = $urls[$prow->virtuemart_product_id]; 
			   }
			  else
			  {
			 if (!empty($opc_only_parent_links))
			  {
			    if (!empty($prow->product_parent_id))
				 {
				    $parent = $prow->product_parent_id; 
					$prow->url = JRoute::_('index.php?option=com_virtuemart&virtuemart_product_id='.$parent.'&view=productdetails', true); 
				 }
			  }
			  }
			  
			  // check if k2 exists: 
			  
			

			  
			  if (!isset($prow->url))
			  { 
				if (isset($prow->link)) 
				 {
				 $prow->url = $prow->link;
				 if (strpos($prow->url, '&amp;')===false)
				   {
				     $prow->url = str_replace('&', '&amp;', $prow->url); 
				   }
				 }
				else
				$prow->url = JRoute::_('index.php?option=com_virtuemart&virtuemart_product_id='.$prow->virtuemart_product_id.'&view=productdetails', true); 
			  }
			  
			   if ($has_k2)
			   {
			      $db = JFactory::getDBO(); 
			      $q = 'select baseID from #__k2mart where referenceID = '.(int)$prow->virtuemart_product_id.' limit 0,1';
				  $db->setQuery($q); 
				  $k2_id = $db->loadResult(); 
				  
				  if (!empty($k2_id))
				   {
				      $prow->url = JRoute::_('index.php?option=com_k2&id='.$k2_id.'&view=item', true); 
					  
				   }
			   }
			  
			  
			   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'stock.php'); 

				$stock_class = ''; 
			  if (!empty($op_colorfy_products))
			  if (!empty($op_color_codes))
			  {
				 
				  
				  $type = OPCstock::getStatus($prow); 
				  
				  $stock_class = OPCstock::getClassName($type); 
				  if (!empty($stock_class))
				  {
					  OPCstock::includeCSS(); 
				  }
				  
			  }
			  $product['product_name_txt'] = $product['product_name'] = $prow->product_name; 
			  
			   $skip = false; 
			  $checkbox_products_display = OPCconfig::get('checkbox_products_display', false); 
			  $is_checkbox_product = false; 
			  if (!empty($checkbox_products))
			  {
				  if ((!empty($checkbox_products_display) && ($checkbox_products_display === true))  && (in_array($prow->virtuemart_product_id, $checkbox_products)))
				  {
					  $skip = true; 
				  }
				  if (in_array($prow->virtuemart_product_id, $checkbox_products)) {
					  $is_checkbox_product = true; 
				  }
			  }
			  
			  
			  $opc_no_cart_p_links = OPCconfig::get('opc_no_cart_p_links', false); 
			  
			  if ((empty($opc_no_cart_p_links)) && (empty($is_checkbox_product))) {
			  $product['product_name'] = JHTML::link($prow->url, $prow->product_name, ' class="opc_product_name  '.$stock_class.'_product" ' );
			  }
			  else
			  {
				  $product['product_name']  = '<span class="opc_product_name">'.$product['product_name'].'</span>'; 
			  }
			  
			  if (!isset($prow->url)) {
				  $product['product_link'] = ''; 
			  }
			  else { 
			   $product['product_link'] = $prow->url; 
			  }
			  
			  if (!empty($stock_class))
			  {
				$stock_label = OPCstock::getLabel($type); 
				
				
				
				if (!empty($stock_label))
				{
			    
			  
			    $product['product_stock_label'] = '<br class="stock_br" /><div class="stock_label"><span class="'.$stock_class.'">'.$stock_label.'</span></div>'; 
				$product['product_name'] .= $product['product_stock_label']; 
				}
			  }
			  
if (((!defined('VM_VERSION')) || (VM_VERSION < 3)) && ((isset($prow->customfields)) && (!is_array($prow->customfields))))
{			  

			  if (!empty($opc_editable_attributes))
			  $product['product_attributes'] = '<div style="clear:both;">'.OPCrenderer::getCustomFields($prow->virtuemart_product_id, $prow->cart_item_id, $prow->quantity, $ref->cart ).'</div>'; 
			  else
			  $product['product_attributes'] = $prow->customfields;
			  
			  
			  
}
else 
{
	$product['product_attributes'] = ''; 
}



if (defined('VM_VERSION') && (VM_VERSION >= 3))
{
  unset($prow->param); 
  
  
  
  if (isset($prow->customfields) && (is_array($prow->customfields)))
			  {
				
				//fix vm3.2.1 bug: 
				OPCrenderer::filterCustomFields($prow); 
				
			    $customfieldsModel = OPCmini::getModel ('Customfields');
			    if (!empty($prow->customfields)) {
				  $product['product_attributes'] = $customfieldsModel->CustomsFieldCartDisplay ($prow);
				}
				else {
					$product['product_attributes'] = ''; 
				}
	
  
  if (!empty($opc_editable_attributes))
  $product['product_attributes'] .= '<div style="clear:both;">'.OPCrenderer::getCustomFields($prow->virtuemart_product_id, $prow->cart_item_id, $prow->quantity, $ref->cart ).'</div>'; 

			 }
  
  
 				
  
}


			  
			  
			  
			  $product['product_sku'] =  $prow->product_sku;

			  
			 
			  // end price test
			  	
			  
			   if (isset($prow->quantity))
			   $product['product_quantity'] =  $prow->quantity;    
			   if (isset($prow->min_order_level))
			   $product['min_order_level'] =  $prow->min_order_level;
			   if (isset($prow->max_order_level))
			   $product['max_order_level'] =  $prow->max_order_level;
			  
			  //$product_model = $OPCloader->getModel('product');
			 $xi++;
			  if (empty($no_extra_product_info)) {
			   
				  $prowcopy = $productClass->getProduct($prow->virtuemart_product_id, true);
				
			  }
			  else $prowcopy = $prow; 
			  
			 $product['info'] = $prowcopy; 
			  $product['product'] = $prow;
		$opc_show_sdesc = OPCconfig::get('opc_show_sdesc', false); 
		
		if (!empty($opc_show_sdesc)) {
			$desc = ''; 
			if (!empty($product['product']->product_s_desc)) {
				$desc = '<div class="product_s_desc" style="clear: both;">'.$product['product']->product_s_desc.'</div>'; 
			}
			else
			if (!empty($product['info']->product_s_desc))  {
				$desc = '<div class="product_s_desc" style="clear: both;">'.$product['info']->product_s_desc.'</div>'; 
				
			}
			$product['product_attributes'] = $desc.$product['product_attributes']; 
		}
			  
			  
			  
			  
			  if  (isset($ref->cart->prices[$pkey]))
				  $currentPrice = $ref->cart->prices[$pkey]; 
			  else
				  if (isset($prow->prices))
				  $currentPrice = $prow->prices; 
			  
			  if ($product_price_display == 'salesPrice')
			  {
			  if (isset($prow->prices))
			  $product['product_price'] = $currentPrice['salesPrice'];
			  else
			  if (isset($prow->salesPrice))
			  $product['product_price'] = $prow->salesPrice;
			  else
			   {
			     if (isset($prow->basePriceWithTax))
				 $product['product_price'] = $prow->basePriceWithTax; 
				 else
			     if (isset($prow->basePrice))
				 $product['product_price'] = $prow->basePrice; 
				 
			   }
			  }
			  else
			  {
			   if (isset($prow->prices))
			   $product['product_price'] = $currentPrice[$product_price_display];
			   else
			   {
			   if (isset($prow->$product_price_display))
			   $product['product_price'] = $prow->$product_price_display;
			   else 
			   if (isset($prow->salesPrice))
			     $product['product_price'] = $prow->salesPrice; 
			   }
			  }
			   if (!isset($product['product_price']))
			   {
			      
				  $price = $ref->cart->pricesUnformatted[$pkey];
				  $product['product_price'] = $price[$product_price_display]; 
			      
				  
			   }
			  
			   if (empty($product['product_price']))
			   {
			      $product_price_display = 'salesPrice'; 
			      $price = $ref->cart->pricesUnformatted[$pkey];
				  $product['product_price'] = $price['salesPrice']; 
			   }
			  
			  
			  $price_raw = $product['product_price']; 
			  $product['price_raw'] = $price_raw; 
			 
			  // the quantity is not working up to 2.0.4
			  
			  $product['product_id'] = $product['virtuemart_product_id'] = $prow->virtuemart_product_id; 
			  
			  $google_html .= '<input type="hidden" name="prod_id" value="'.$prow->virtuemart_product_id.'" />
			   <input type="hidden" name="prodsku_'.$prow->virtuemart_product_id.'" id="prodsku_'.$prow->virtuemart_product_id.'" value="'.OPCloader::slash($prow->product_sku, false).'" />
			   <input type="hidden" name="prodname_'.$prow->virtuemart_product_id.'" id="prodname_'.$prow->virtuemart_product_id.'" value="'.OPCloader::slash($prow->product_name, false).'" />
			   <input type="hidden" name="prodq_'.$prow->virtuemart_product_id.'" id="prodq_'.$prow->virtuemart_product_id.'" value="'.$prow->quantity.'" />
			   <input type="hidden" name="produprice_'.$prow->virtuemart_product_id.'" id="produprice_'.$prow->virtuemart_product_id.'" value="'.$price_raw.'" />
			    <input type="hidden" name="prodcat_'.$prow->virtuemart_product_id.'" id="prodcat_'.$prow->virtuemart_product_id.'" value="'.$prow->category_name.'" />
			   
			   
			  '; 
			  
			 
			  
			 
			 
			 
			 if (isset($ref->cart->pricesUnformatted[$pkey]))
			  $price =  $ref->cart->pricesUnformatted[$pkey]; 
			 else 
			   $price = $prow->prices; 
			   
		   
		      $product['prices'] = $price; 
			  $product['prices_formatted'] = array(); 
			  if ($vm2015)
			  foreach ($price as $key=>$pricev)
			  {
				  //if (!isset($price[$key]))
				  if (!empty($pricev))
				  {
					if (empty($price[$key])) $price[$key] = '0.0000000000001'; 
				    $product['prices_formatted'][$key] = $currencyDisplay->createPriceDiv($key,'', $price,false,true, 1);
				  }
			  }
			  
		   if (empty($price[$product_price_display])) $price[$product_price_display] = '0.0000000000001'; 
			  $product['product_price'] = $currencyDisplay->createPriceDiv($product_price_display,'', $price,false,true, 1);
			
			
			  /*
			  if (false)
			  if (empty($product['product_price']))
			  {
			    // ok, we have a wrong type selected here
				if ($product_price_display == 'salesPrice') 
				$product['product_price'] = $currencyDisplay->createPriceDiv('basePrice','', $price,false,false, 1);
				if (empty($product['product_price']))
				$product['product_price'] = $currencyDisplay->createPriceDiv('priceWithoutTax','', $price,false,false, 1);
				if (empty($product['product_price']))
				$product['product_price'] = $currencyDisplay->createPriceDiv('basePriceWithTax','', $price,false,false, 1);
				if (empty($product['product_price']))
				$product['product_price'] = $currencyDisplay->createPriceDiv('priceBeforeTax','', $price,false,false, 1);
				if (empty($product['product_price']))
				$product['product_price'] = $currencyDisplay->createPriceDiv('costPrice','', $price,false,false, 1);
				

				 
			  }
			  */
			  
			 
			  
			  $product['product_price'] = str_replace('class="', 'class="opc_price_general opc_', $product['product_price']); 
			  if (!isset($prow->cart_item_id)) $prow->cart_item_id = $pkey;
			  
			   $v = array('product'=>$prow, 
			   'action_url'=>$action_url, 
			   'use_ssl'=>$useSSL, 
			   'useSSL'=>$useSSL);
			 
			  $hasSteps = self::checkSteps($prow, $v); 
			  $suffix = ''; 
			  if ($hasSteps) {
			    $suffix = '_steps'; 
			  }
			
		      if (!empty($ajaxify_cart))
			  {
			   
			  $update_form = $OPCloader->fetch($OPCloader, 'update_form_ajax'.$suffix.'.tpl', $v); 
			  
			  if (empty($update_form)) {
			    $update_form = $OPCloader->fetch($OPCloader, 'update_form_ajax.tpl', $v); 
			  }
			  
			  $delete_form = $OPCloader->fetch($OPCloader, 'delete_form_ajax.tpl', $v); 
				
			  
			  
			  }
			  else
			  {
			  $update_form = $OPCloader->fetch($OPCloader, 'update_form'.$suffix.'.tpl', $v); 
			  if (empty($update_form)) {
			    $update_form = $OPCloader->fetch($OPCloader, 'update_form.tpl', $v); 
			  }
			  
			  $delete_form = $OPCloader->fetch($OPCloader, 'delete_form.tpl', $v); 
			  $op_coupon_ajax = ''; 
			  }
			  if (empty($update_form))
			  {
				   if (!empty($ajaxify_cart))
				   {
			  
		      $product['update_form'] = '<input type="text" title="'.OPCLang::_('COM_VIRTUEMART_CART_UPDATE').'" class="inputbox" size="3" name="quantity" id="quantity_for_'.md5($prow->cart_item_id).'" value="'.$prow->quantity.'" /><a class="updatebtn" title="'.OPCLang::_('COM_VIRTUEMART_CART_DELETE').'" href="#" rel="'.$prow->cart_item_id.'|'.md5($prow->cart_item_id).'"> </a>';
		  
			  $product['delete_form'] = '<a class="deletebtn" title="'.OPCLang::_('COM_VIRTUEMART_CART_DELETE').'" href="#" rel="'.$prow->cart_item_id.'"> </a>';
				   }
				   else
				   {
			  $product['update_form'] = '<form action="'.$action_url.'" method="post" style="display: inline;">
				<input type="hidden" name="option" value="com_virtuemart" />
				<input type="text" title="'.OPCLang::_('COM_VIRTUEMART_CART_UPDATE').'" class="inputbox" size="3" name="quantity" value="'.$prow->quantity.'" />
				<input type="hidden" name="view" value="cart" />
				<input type="hidden" name="task" value="update" />
				<input type="hidden" name="cart_virtuemart_product_id" value="'.$prow->cart_item_id.'" />
				<input type="submit" class="updatebtn" name="update" title="'.OPCLang::_('COM_VIRTUEMART_CART_UPDATE').'" value=" "/>
			  </form>'; 
			  
			  if (defined('VM_VERSION') && (VM_VERSION >= 3))
			  $product['delete_form'] = '<a class="deletebtn" title="'.OPCLang::_('COM_VIRTUEMART_CART_DELETE').'" href="'.JRoute::_('index.php?option=com_virtuemart&view=cart&task=delete.'.$prow->cart_item_id.'&cart_virtuemart_product_id='.$prow->cart_item_id, true, $useSSL  ).'"> </a>'; 
			  else
			  $product['delete_form'] = '<a class="deletebtn" title="'.OPCLang::_('COM_VIRTUEMART_CART_DELETE').'" href="'.JRoute::_('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id='.$prow->cart_item_id, true, $useSSL  ).'"> </a>'; 
				   
				   }
			  }
			  else
			  {
			    $product['update_form'] = $update_form; 
			    $product['delete_form'] = $delete_form; 
			  }
			  
			  
			   if (!empty($ajaxify_cart))
			   {
				   $xz1 = stripos($product['update_form'], 'onclick='); 
				   $xz2 = stripos($product['delete_form'], 'onclick='); 
				   if ($xz1 === false)
				   {
				    $product['update_form'] = str_replace('href=', 'onclick="return Onepage.updateProduct(this);" href=', $product['update_form']);
				   }
				   if ($xz2 === false)
				   {
				    $product['delete_form'] = str_replace('href=', 'onclick="return Onepage.deleteProduct(this);" href=', $product['delete_form']); 
				   }
				   
			   }
			   
			   
			   
			   //vm3 update: 
			   
			   if (defined('VM_VERSION') && (VM_VERSION >= 3))
			   {
			   $product['update_form'] = str_replace('name="quantity"', 'name="quantity['.$prow->cart_item_id.']"', $product['update_form']); 			   
			   $product['update_form'] = str_replace('value="update"', 'value="updatecart"', $product['update_form']); 
			   }
			   //end vm3 update
			   
			   
			    
			  $checkbox_products = OPCconfig::get('checkbox_products', array()); 
			  $noupdate = false; 
			  if ((!empty($checkbox_products)) && (in_array($prow->virtuemart_product_id, $checkbox_products))) {
				  if ($checkbox_products_display !== 2)
				  $product['update_form'] = '&nbsp;'; 
			  }
			   
			   
			  //if (isset($prow->prices))
			  {
			  $product['subtotal'] = $prow->quantity * $price_raw;
			   
			  }
			  //else
			  //$product['subtotal'] = $prow->subtotal_with_tax;
			  
			  
			  
			  
			  
			  
			  // this is fixed from 2.0.4 and would not be needed
			  if (isset($ref->cart->pricesUnformatted[$pkey]))
			  $copy = $ref->cart->pricesUnformatted[$pkey];
			  else $copy = $prow->prices; 
			  //$copy['salesPrice'] = $copy['subtotal_with_tax']; 
			  $copy[$product_price_display] = $product['subtotal']; 
			  
			 
			  if (empty($copy[$product_price_display])) {
			  $copy[$product_price_display] = 0.000000001;
			  }
			  $name_noformat = array(); 
			   $name = array(); 
			  if (isset($ref->cart->prices[$pkey]['VatTax'])) {
				   foreach ($ref->cart->prices[$pkey]['VatTax'] as $k=>$r)
				   {
					   $name[] = '<span class="tax_name" >('.$r[0].')</span>'; 
					   $name_noformat[] = $r[0]; 
				   }
			  }
			  
			  $product['subtotal'] = $currencyDisplay->createPriceDiv($product_price_display,'', $copy,false,true, 1);
			  $product['subtotal'] = str_replace('class="', 'class="opc_', $product['subtotal']); 
			  // opc vars
			 $product['tax_name'] = array();  
			 $opc_tax_name_display = OPCconfig::get('opc_tax_name_display', false); 
			  if (!empty($name)) {
			 if (!empty($opc_tax_name_display)) { 
			 
			    $product['subtotal']  .= '<br />'.implode('<br />', $name); 
			  }
			  $product['tax_name'] = $name; 
			   $product['tax_name_noformat'] = $name_noformat; 
			  
			 }
			 
			 
			  
			 
			  
			  if (!$skip)
			  $product_rows[] = $product; 
			  
			  $array = array('<div', '</div', 'div '); 
			  $to_array = array('<span', '</span', 'span '); 
			  
			  $span_subtotal = str_replace($array, $to_array, $product['subtotal']); 
			  
			  //break; 
			  $extra_html .= '<div class="opccf_confirm_row">
			  <span class="opccf_quantity">'.$prow->quantity.'</span>
			  <span class="opccf_times">&nbsp;x&nbsp;</span>
			  <span class="opccf_productname">'.$prow->product_name.'</span>
			  <span class="opccf_productsubtotal">'.$span_subtotal.'</span>
			  </div>'; 
			 
			 
			}
			
			
			$extra_html = '
			<div class="dialog_wrapper" style="display: none;">
			 <div class="modal fade no-close" id="opc_cf_jquery_modal" tabindex="-1" role="dialog">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Dialog</h3>
		      </div>
			   <div id="opc_cf_dialog" class="modal-body no-close">'.$extra_html.'
			    <div id="opc_cf_totals" style="display: none;">
				  <span class="row_name">{row_name}</span>
				  <span class="row_value">{row_value}</span>
				</div>
			  </div>
			 </div>
			</div>'; 
			
			//$shipping_inside_basket = false;
			  $shipping_select = $shipping;
			  $payment_select = $payment;
			if (!empty($ref->cart->prices['salesPriceCoupon']))
			{
			 if (empty($coupon_price_display)) $coupon_price_display = 'salesPriceCoupon'; 
			 
			 $coupon_display = $currencyDisplay->createPriceDiv($coupon_price_display,'', $ref->cart->prices,false,true, 1);//$ref->cart->prices['salesPriceCoupon']; 
			 $coupon_display = str_replace('class="', 'class="opc_', $coupon_display); 
			}
			else $coupon_display = ''; 
			
			if (!empty($coupon_display))
			{
			  $discount_after = true; 
			}
			else $discount_after = false; 
			
			//if (!empty($ref->cart->prices['billDiscountAmount']))
			{
			  if (empty($other_discount_display)) $other_discount_display = 'billDiscountAmount'; 
			  switch ($other_discount_display)
			  {
			    case 'billDiscountAmount': 
				$coupon_display_before = $currencyDisplay->createPriceDiv('billDiscountAmount','', $ref->cart->prices,false,false, 1);
				if (empty($ref->cart->prices['billDiscountAmount'])) $coupon_display_before = ''; 
				break; 
				
				case 'discountAmount': 
				$coupon_display_before = $currencyDisplay->createPriceDiv('discountAmount','', $ref->cart->prices,false,false, 1);
				if (empty($ref->cart->prices['discountAmount'])) $coupon_display_before = ''; 
				
				case 'minus': 
				$billD = abs($ref->cart->prices['billDiscountAmount']); 
				foreach ($ref->cart->prices as $key=>$val)
				{
				   if (!empty($ref->cart->products[$key]))
				   if (is_array($val))
				   {
				     $billD -= abs($val['subtotal_discount']); 
				   }
				}
				$billD = abs($billD) * (-1);
				$prices_new['billTotal'] = $billD;
				if (!empty($billD))
				$coupon_display_before = $currencyDisplay->createPriceDiv('billTotal','', $prices_new,false,false, 1);
				else 
				$coupon_display_before = ''; 
				break; 
				case 'sum': 
				$billD = 0; 
				foreach ($ref->cart->prices as $key=>$val)
				{
				   if (!empty($ref->cart->products[$key]))
				   if (is_array($val))
				   {
				     $billD += $val['subtotal_discount']; 
				   }
				}
				$billD = abs($billD) * (-1); 
				$prices_new['billTotal'] = $billD; 
				if (!empty($billD))
				$coupon_display_before = $currencyDisplay->createPriceDiv('billTotal','', $prices_new,false,false, 1);
				else $coupon_display_before = ''; 
				
				break; 
				
				
			  }
			 
			  $coupon_display_before = str_replace('class="', 'class="opc_', $coupon_display_before); 
			}
			//else $coupon_display_before = ''; 
			$opc_show_weight_display = ''; 
			
			$virtuemart_currency_id = OPCloader::getCurrency($ref->cart); 
			
			if (!empty($opc_show_weight) && (!empty($totalw)))
			{
			  if (method_exists($currencyDisplay, 'getDecimalSymbol'))
			  {
			   $dec = $currencyDisplay->getDecimalSymbol(); 
			   $th = $currencyDisplay->getThousandsSeperator(); 
			  }
			  else
			   {
				   
				   $c2 = OPCmini::getCurInfo($virtuemart_currency_id); 
				   $dec = $c2['currency_decimal_symbol']; 
				   $th = $c2['currency_thousands']; 
			   }
			  $w = VmConfig::get('weight_unit_default', 'KG'); 
			  $w = strtoupper($w); 
			  if ($w == 'OZ') $w = 'OUNCE'; 
			  $unit = JText::_('COM_VIRTUEMART_UNIT_SYMBOL_'.$w); 
			  if ($unit == 'COM_VIRTUEMART_UNIT_SYMBOL_'.$w) $unit =  $w = VmConfig::get('weight_unit_default', 'kg'); 
			  $opc_show_weight_display = number_format($totalw, 2, $dec, $th).' '.$unit; 
			}
			
			
			
			
			
			if (!empty($ajaxify_cart))
			{
			 $coupon_text = $ref->cart->couponCode ? OPCLang::_('COM_VIRTUEMART_COUPON_CODE_CHANGE') : OPCLang::_('COM_VIRTUEMART_COUPON_CODE_ENTER');
			  $vars = array('coupon_text'=> $coupon_text, 
			  'coupon_display'=>$coupon_display); 
			  $op_coupon_ajax = $OPCloader->fetch($OPCloader, 'couponField_ajax', $vars); 
			  if (stripos($op_coupon_ajax, 'Onepage.setCouponAjax')===false)
			  $op_coupon_ajax = str_replace('type="button', 'onclick="return Onepage.setCouponAjax(this);" type="button', $op_coupon_ajax); 
			}
			
			
			/*
			 if (empty($subtotal_price_display)) $subtotal_price_display = 'salesPrice'; 
			  if ($subtotal_price_display != 'diffTotals')
			 {
			$subtotal_display = $currencyDisplay->createPriceDiv($subtotal_price_display,'', $ref->cart->prices,false,false, 1);
			
			  if ($subtotal_price_display == 'basePriceWithTax')
			  if (stripos($subtotal_display, ' ></span')!==false)
			   {
					$subtotal_price_display = 'salesPrice'; 
					$subtotal_display = $currencyDisplay->createPriceDiv($subtotal_price_display,'', $ref->cart->prices,false,false, 1);
			     
			   
			  
			   }
			}
			else
			{
				$subtotal = $ref->cart->prices['billTotal'] - $ref->cart->prices['billTaxAmount']; 
				
				$arr = array('diffTotals'=>$subtotal); 
				
				$subtotal_display = $currencyDisplay->createPriceDiv($subtotal_price_display,'', $arr,false,false, 1);
			}
			*/
			$order_subtotal = self::calculateSubtotal($ref->cart); 
			
			
			$order_subtotal = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $order_subtotal,false);
			$arr = array('product_subtotal_opc'=>$order_subtotal); 
			$subtotal_display = $currencyDisplay->createPriceDiv('product_subtotal_opc','', $arr,false,false, 1);
			
			
			//$ref->cart->prices['salesPrice'];
			$subtotal_display = str_replace('class="', 'class="opc_', $subtotal_display); 
			
			
			
			$prices = $ref->cart->prices; 
	if (!isset($prices[$subtotal_price_display.'Shipment']))
	{
	if ($subtotal_price_display != 'salesPrice')
	$order_shipping = $prices['shipmentValue'];
	else
	$order_shipping = $prices['salesPriceShipment']; 
	}
	else
	$order_shipping = $prices[$subtotal_price_display.'Shipment']; 
	if (!empty($order_shipping))
	{
	 
	 
	 $order_shipping = $currencyDisplay->convertCurrencyTo( $virtuemart_currency_id, $order_shipping,false);
	 
	 
	 $test = $currencyDisplay->createPriceDiv($product_price_display,'', $order_shipping,false,true, 1);
	 if (!empty($test)) $order_shipping = $test; 
	 $order_shipping = str_replace('class="', 'class="opc_', $order_shipping); 
	}
	else $order_shipping = ''; 
			
			
			$continue_link = $OPCloader->getContinueLink($ref); 
			$order_total_display = $currencyDisplay->createPriceDiv('billTotal','', $ref->cart->prices,false,false, 1); //$ref->cart->prices['billTotal']; 
			$order_total_display = str_replace('class="', 'class="opc_', $order_total_display); 
			// this will need a little tuning
			if (isset($ref->cart->cartData['taxRulesBill']) && (is_array($ref->cart->cartData['taxRulesBill'])))
			foreach($ref->cart->cartData['taxRulesBill'] as $rule){ 
				$rulename = $rule['calc_name'];
				if (!empty($ref->cart->prices[$rule['virtuemart_calc_id'].'Diff']))
				{
				$tax_display = $currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $ref->cart->prices,false,false, 1); //$ref->cart->prices[$rule['virtuemart_calc_id'].'Diff'];  
				$tax_display = str_replace('class="', 'class="opc_', $tax_display); 
				}
				else $tax_display = ''; 
	  	    }
			
			$op_disable_shipping = OPCloader::getShippingEnabled($ref->cart);
			
			if ((!empty($payment_discount_before)) && (!empty($coupon_display_before)))
			$discount_before = true; 
			else $discount_before = false; 
			
			$disable_couponns = VmConfig::get('coupons_enable', true); 
			if (empty($disable_couponns))
			$op_coupon = $op_coupon_ajax = ''; 
			
			if (!empty($op_coupon_ajax))
			$op_coupon = $op_coupon_ajax; 
			
			if ($isexpress)
			$payment_inside_basket = false; 
			
			if (!isset($op_coupon_ajax)) $op_coupon_ajax = ''; 
			
			if (empty($payment_inside_basket)) $payment_select = ''; 
			if (empty($shipping_inside_basket)) $shipping_select = ''; 

			if ($discount_before === $discount_after) {
				$discount_before = ''; 
			}
			
			if (empty($tax_display)) $tax_display = ''; 
			if (empty($op_disable_shipping)) $op_disable_shipping = false; 
			$no_shipping = $op_disable_shipping;
			$vars = array ('product_rows' => $product_rows, 
						   'payment_inside_basket' => $payment_inside_basket,
						   'shipping_select' => $shipping_select, 
						   'payment_select' => $payment_select, 
						   'shipping_inside_basket' => $shipping_inside_basket, 
						   'coupon_display' => $coupon_display, 
						   'subtotal_display' => $subtotal_display, 
						   'no_shipping' => $no_shipping,
						   'order_total_display' => $order_total_display, 
						   'tax_display' => $tax_display, 
						   'VM_LANG' => $VM_LANG,
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
 //original cart support: 
 $ref->cart->cartData['shipmentName'] = ''; 
 $ref->cart->cartData['paymentName'] = ''; 
 
 $totalInPaymentCurrency = $ref->cart->prices['billTotal']; //$ref->getTotalInPaymentCurrency();
 
 $cd = CurrencyDisplay::getInstance($ref->cart->pricesCurrency);  
 $layoutName = 'default';
 
 $confirm = 'confirm'; 
 $shippingText = ''; 
 $paymentText = ''; 
 $checkout_link_html = ''; 
 $useSSL = VmConfig::get('useSSL', 0);
 $useXHTML = true;
 $checkoutAdvertise = ''; 
 
 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
 $renderer = OPCrenderer::getInstance(); 
 
 if (!empty($opc_confirm_dialog))
 {
	 $renderer->css('confirm_dialog'); 
	 
	 
	 
	 
 }
 
 if (method_exists($renderer, 'assignRef'))
 {
 $renderer->assignRef('cart', $renderer->cart); 
 $renderer->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
 $renderer->assignRef('layoutName', $layoutName);
 $renderer->assignRef('select_shipment_text', $shippingText);
 $renderer->assignRef('checkout_task', $confirm);
 $renderer->assignRef('currencyDisplay', $cd);
 $renderer->assignRef('select_payment_text', $paymentText);
 $renderer->assignRef('checkout_link_html', $checkout_link_html);					   
 $renderer->assignRef('useSSL', $useSSL);
 $renderer->assignRef('useXHTML', $useXHTML);
 $renderer->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
 $renderer->assignRef('checkoutAdvertise', $checkoutAdvertise);
 }
 
	$vars['currencyDisplay'] = $currencyDisplay; 

			if (!empty($ismulti))
			{
				$mt = 'basket_multi.html'; 
			}
			else
			{
				$mt = 'basket.html'; 
			}
			
		     if (empty($use_original_basket))
			 {
			 
			$html = $renderer->fetch($OPCloader, $mt, $vars); 
			}
			else
			{
				 
			$html = $renderer->fetchBasket($OPCloader, $mt, $vars); 
			
			
			}
			
			$html = $html.$google_html.$extra_html;
			
			if ($withwrapper)
			$html = '<div id="opc_basket">'.$html.'</div><!-- end id opc_basket -->'; 
			if (!empty($op_no_basket))
			{
			$html = '<div class="nobasket" style="display: none;">'.$html.'</div><!-- end class nobasket -->'; 
			
			}
			if (isset($currencyDisplay->_priceConfig))
		    $currencyDisplay->_priceConfig = $savedConfig; 			
			
			
			
			$ret = $html;
			return $ret;
		
  }

}