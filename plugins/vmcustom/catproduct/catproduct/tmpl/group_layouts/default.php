<?php
/**
 *
 * @author SM planet - smplanet.net
 * @package VirtueMart
 * @subpackage custom
 * @copyright Copyright (C) 2012-2014 SM planet - smplanet.net. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 **/
	defined('_JEXEC') or die();

	foreach($group AS $product){

	if (isset($product['group_title']) && $product['group_title']) {
		$group_id++;
		echo '<tr class="row_attached_product">';
		echo '<td colspan="'.$colspan.'" style="text-align:center;">'.$product['group_title'].'</td>';
		echo '</tr>';
	} else {
/*	$price_per_unit = 'NA';
	if (isset($product['child']['product_packaging']) && $product['child']['product_packaging'] <> 0 ){
		$product_unit = $product['child']['product_unit'];
		switch ($product_unit) {
			case 'KG':
				$price_per_unit = round(((float) $product['prices']['salesPrice'] / (float) $product['child']['product_packaging']),2).' / '.$product_unit ;
				break;
			case 'L':
				$price_per_unit = round(((float) $product['prices']['salesPrice'] / (float) $product['child']['product_packaging']),2) ;
				break;
			case 'M':
				$price_per_unit = round(((float) $product['prices']['salesPrice'] / (float) $product['child']['product_packaging']),2) ;
				break;
			default:	
				$price_per_unit = round(((float) $product['prices']['salesPrice'] / (float) $product['child']['product_packaging']),2) ;
		}
	}
	print_r($price_per_unit);
	  //$product['child']['product_packaging'];
	  print_r($currency->_priceConfig);
	  */
	  	// min max box quantity
		$min_order_level = '0';
		$max_order_level = '0';
		$product_box = '0';
		if (isset($product['qparam'])) {
			$min_order_level = $product['qparam']['min'];
			$max_order_level = $product['qparam']['max'];
			$product_box = $product['qparam']['box'];
		}
	  //if attached product title
	  if (empty($product['child']['catproduct_inline_row'])) {		
	  	// callforprice url
	  $callforprice = 'index.php?option=com_virtuemart&view=productdetails&task=askquestion&tmpl=component&virtuemart_product_id='.$product['child']['virtuemart_product_id'];
	
		// start with showing product
		if ($check_stock == 1 && $product['child']['product_in_stock'] > 0 || $check_stock == 0){
		// hidden input with whole product data
		//product id
		echo '<input class="product_id" name="product_id" id="product_id_'.$i.'" value="'.$product['child']['virtuemart_product_id'].'" type="hidden">';
		//product name
		echo '<input class="product_name" name="product_name" id="product_name_'.$i.'" value="'.$product['child']['product_name'].'" type="hidden">';
		//product weight
		echo '<input class="product_weight" name="product_weight" id="product_weight_'.$i.'" value="'.$product['child']['product_weight'].'" type="hidden">';
		//product_weight_uom
		echo '<input class="product_weight_uom" name="product_weight_uom" id="product_weight_uom_'.$i.'" value="'.$product['child']['product_weight_uom'].'" type="hidden">';
		//product_length
		echo '<input class="product_length" name="product_length" id="product_length_'.$i.'" value="'.$product['child']['product_length'].'" type="hidden">';
		//product_width
		echo '<input class="product_width" name="product_width" id="product_width_'.$i.'" value="'.$product['child']['product_width'].'" type="hidden">';
		//product_height
		echo '<input class="product_height" name="product_height" id="product_height_'.$i.'" value="'.$product['child']['product_height'].'" type="hidden">';
		//product_lwh_uom
		echo '<input class="product_lwh_uom" name="product_lwh_uom" id="product_lwh_uom_'.$i.'" value="'.$product['child']['product_lwh_uom'].'" type="hidden">';
		//product_in_stock
		echo '<input class="product_in_stock" name="product_in_stock" id="product_in_stock_'.$i.'" value="'.$product['child']['product_in_stock'].'" type="hidden">';
		//basePrice
		$price = '0';
		if($see_price){$price = $currency->roundForDisplay($product['prices']['basePrice']);}
		echo '<input type="hidden" class="basePrice" name="basePrice" id="basePrice_'.$i.'" value="'.$price.'">';
		//basePriceWithTax
		$price = '0';
		if($see_price){$price = $currency->roundForDisplay($product['prices']['basePriceWithTax']);}
		echo '<input type="hidden" class="basePriceWithTax" name="basePriceWithTax" id="basePriceWithTax_'.$i.'" value="'.$price.'">';
		//salesPrice
		$price = '0';
		if($see_price){$price = $currency->roundForDisplay($product['prices']['salesPrice']);}
		echo '<input type="hidden" class="salesPrice" name="salesPrice" id="salesPrice_'.$i.'" value="'.$price.'">';
		//taxAmount
		$price = '0';
		if($see_price){$price = $currency->roundForDisplay($product['prices']['taxAmount']);}
		echo '<input type="hidden" class="taxAmount" name="taxAmount" id="taxAmount_'.$i.'" value="'.$price.'">';
		//priceWithoutTax
		$price = '0';
		if($see_price){$price = $currency->roundForDisplay($product['prices']['priceWithoutTax']);}
		echo '<input type="hidden" class="priceWithoutTax" name="priceWithoutTax" id="priceWithoutTax_'.$i.'" value="'.$price.'">';
		//discountAmount
		$price = '0';
		if($see_price){$price = $currency->roundForDisplay($product['prices']['discountAmount']);}
		echo '<input type="hidden" class="discountAmount" name="discountAmount" id="discountAmount_'.$i.'" value="'.$price.'">';
		
		echo '<input type="hidden" class="sum_weight" name="sum_weight" id="sum_weight_'.$i.'" value="0" >';
		echo '<input type="hidden" class="sum_basePrice" name="sum_basePrice" id="sum_basePrice_'.$i.'" value="0" >';
		echo '<input type="hidden" class="sum_basePriceWithTax" name="sum_basePriceWithTax" id="sum_basePriceWithTax_'.$i.'" value="0" >';
		echo '<input type="hidden" class="sum_taxAmount" name="sum_taxAmount" id="sum_taxAmount_'.$i.'" value="0" >';
		echo '<input type="hidden" class="sum_discountAmount" name="sum_discountAmount" id="sum_discountAmount_'.$i.'" value="0" >';
		echo '<input type="hidden" class="sum_priceWithoutTax" name="sum_priceWithoutTax" id="sum_priceWithoutTax_'.$i.'" value="0" >';
		echo '<input type="hidden" class="sum_salesPrice" name="sum_salesPrice" id="sum_salesPrice_'.$i.'" value="0" >';
		
		echo '<input type="hidden" class="min_order_level" name="min_order_level" id="min_order_level_'.$i.'" value="'.$min_order_level.'" >';
		echo '<input type="hidden" class="max_order_level" name="max_order_level" id="max_order_level_'.$i.'" value="'.$max_order_level.'" >';
		echo '<input type="hidden" class="product_box" name="product_box" id="product_box_'.$i.'" value="'.$product_box.'" >';
		echo '<input type="hidden" class="group_id" name="group_id" id="group_id_'.$i.'" value="'.$group_id.'" >';
		echo '<input type="hidden" class="def_group_qty" name="def_group_qty" id="def_group_qty_'.$i.'" value="'.$def_group_qty.'" >';
		}
		
		echo '<tr class="row_article" id="row_article_'.$i.'">';
		
		// show table with product
		//Product_image
		if ($parametri["show_image"] == 1) {
			if (isset($product['images'])) {
				$firsttime = true;
				echo '<td class="cell_image">';
				foreach ($product['images'] as $image) {
					if ($firsttime) {
						echo $image->displayMediaThumb('class="product-image"', true, 'rel="catproduct_images_'.$i.'"', true, false);
						$firsttime = false;
					} else {
						echo '<a title="'.$image->file_name.'" rel="catproduct_images_0" href="'.$image->file_url.'"></a>';
					}
				}
				echo '</td>';
			}
			else {
				echo '<td class="cell_image"></td>';
			}
		}
		if(VmConfig::get('usefancy',0)){
			$image_script .= 'jQuery(document).ready(function() {
			jQuery("a[rel=catproduct_images_'.$i.']").fancybox({
			"titlePosition" 	: "inside",
			"transitionIn"	:	"elastic",
			"transitionOut"	:	"elastic"
			});});';
		} else {
			$image_script .= 'jQuery(document).ready(function() {
			jQuery("a[rel=catproduct_images_'.$i.']").facebox();});';
		}

		//Product_ID
		if ($parametri["show_id"] == 1) {
			echo '<td class="cell_id">'.$product['child']['virtuemart_product_id'].'</td>';
		}
		//Product_SKU
		if ($parametri["show_sku"] == 1) {
			echo '<td class="cell_sku">'.$product['child']['product_sku'].'</td>';
		}
		// Product title
		if ($parametri["show_name"] == 1) {
			echo '<td class="cell_name">'.$product['child']['product_name'];
			echo '</td>';
		}
		
		// check for customfield
		if ($use_customfields == 1) {
			$customfield = '';
			if (isset( $product['child']['customfieldsCart'][0])) {
				foreach($product['child']['customfieldsCart'] as $customfields) {
					$test = (array) $customfields;
					$customfield .= ('<strong>'.$test['custom_title'].'</strong><br/>'.$test['display']);
				}
			}
			echo '<td class="cell_customfields">'.$customfield;
			echo '</td>';
		}
		
		// Product description
		if ($parametri["show_s_desc"] == 1) {
			echo '<td class="cell_s_desc">'.$product['child']['product_s_desc'].'</td>';
		}
		// Product weight
		if ($parametri["show_weight"] == 1) {
			echo '<td class="cell_weight">';
			if ($product['child']['product_weight'] <> 0) {
			echo round($product['child']['product_weight'],2).' '.$product['child']['product_weight_uom'];
			}
			echo '</td>';
		}
		// Product sizes
		if ($parametri["show_sizes"] == 1) {
			$sizes = '';
			echo '<td class="cell_size">';
			if ($product['child']['product_length'] <> 0) $sizes .= round($product['child']['product_length'],2);
			if ($product['child']['product_width'] <> 0) {
				if ($sizes <> '') {
					$sizes .= ' x ';
				}
				$sizes .= round($product['child']['product_width'],2);
			}
			if ($product['child']['product_height'] <> 0) {
				if ($sizes <> '') {
					$sizes .= ' x ';
				}
				$sizes .= round($product['child']['product_height'],2);
			}
			if ($sizes <> '') {
				$sizes .= ' '.$product['child']['product_lwh_uom'];
			}
			echo $sizes;
			echo '</td>';
		}
		// Product stock
		if ($parametri["show_stock"] == 1) {
			echo '<td class="cell_stock">'.$product['child']['product_in_stock'].'</td>';
		}
		if (isset($parametri["show_min_qty"]) && $parametri["show_min_qty"] == 1) { 
			$minqty = str_replace('"', "", $product["qparam"]["min"]);
			if ($minqty == '' || $minqty == 'null') $minqty = JText::_('CATPRODUCT_QTY_NA');
			echo '<td class="cell_stock">'.$minqty.'</td>';
		}
		if (isset($parametri["show_max_qty"]) && $parametri["show_max_qty"] == 1) { 
			$minqty = str_replace('"', "", $product["qparam"]["max"]);
			if ($minqty == '' || $minqty == 'null') $minqty = JText::_('CATPRODUCT_QTY_NA');
			echo '<td class="cell_stock">'.$minqty.'</td>';
		}
		if (isset($parametri["show_step_qty"]) && $parametri["show_step_qty"] == 1) { 
			$minqty = str_replace('"', "", $product["qparam"]["box"]);
			if ($minqty == '' || $minqty == 'null') $minqty = JText::_('CATPRODUCT_QTY_NA');
			echo '<td class="cell_stock">'.$minqty.'</td>';
		}
		
		// Product base price without tax
		if ($parametri["show_basePrice"] == 1 && $see_price) {
			if ($product['prices']['basePrice'] <= 0 and VmConfig::get ('askprice', 1)) {
				echo '<td class="cell_basePrice"><a class="ask-a-question bold" href="'.$callforprice.'" rel="nofollow" >'.JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE').'</a></td>';
			} else
			echo '<td class="cell_basePrice"><div id="basePrice_text_'.$i.'">'.$currency->createPriceDiv ('basePrice', '', $product['prices']).'</div></td>';
			//round($product['prices']['basePrice'],2).' '.JText::_('CATPRODUCT_CURRENCY').'</td>';
		}
		// Product base price with tax
		if ($parametri["show_basePriceWithTax"] == 1 && $see_price) {
			if ($product['prices']['basePriceWithTax'] <= 0 and VmConfig::get ('askprice', 1)) {
				echo '<td class="cell_basePriceWithTax"><a class="ask-a-question bold" href="'.$callforprice.'" rel="nofollow" >'.JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE').'</a></td>';
			} else
			echo '<td class="cell_basePriceWithTax"><div id="basePriceWithTax_text_'.$i.'">'.$currency->createPriceDiv ('basePriceWithTax', '', $product['prices']).'</div></td>';
			//.round($product['prices']['basePriceWithTax'],2).' '.JText::_('CATPRODUCT_CURRENCY').'</td>';
		}
		// Product final price without tax
		if ($parametri["show_priceWithoutTax"] == 1 && $see_price) {
			if ($product['prices']['priceWithoutTax'] <= 0 and VmConfig::get ('askprice', 1)) {
				echo '<td class="cell_priceWithoutTax"><a class="ask-a-question bold" href="'.$callforprice.'" rel="nofollow" >'.JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE').'</a></td>';
			} else
			echo '<td class="cell_priceWithoutTax"><div id="priceWithoutTax_text_'.$i.'">'.$currency->createPriceDiv ('priceWithoutTax', '', $product['prices']).'</div></td>';
			//.round($product['prices']['priceWithoutTax'],2).' '.JText::_('CATPRODUCT_CURRENCY').'</td>';
		}
		// Product final price with tax
		if ($parametri["show_salesPrice"] == 1 && $see_price) {
			if ($product['prices']['salesPrice'] <= 0 and VmConfig::get ('askprice', 1)) {
				echo '<td class="cell_salesPrice"><a class="ask-a-question bold" href="'.$callforprice.'" rel="nofollow" >'.JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE').'</a></td>';
			} else
			echo '<td class="cell_salesPrice"><div id="salesPrice_text_'.$i.'">'.$currency->createPriceDiv ('salesPrice', '', $product['prices']).'</div></td>';
			//.round($product['prices']['salesPrice'],2).' '.JText::_('CATPRODUCT_CURRENCY').'</td>';
		}
		// Tax amount
		if ($parametri["show_taxAmount"] == 1 && $see_price) {
			echo '<td class="cell_taxAmount"><div id="taxAmount_text_'.$i.'">'.$currency->createPriceDiv ('taxAmount', '', $product['prices']).'</td>';
			//.round($product['prices']['taxAmount'],2).' '.JText::_('CATPRODUCT_CURRENCY').'</td>';
		}
		// Discount amount
		if ($parametri["show_discountAmount"] == 1 && $see_price) {
			echo '<td class="cell_discountAmount"><div id="discountAmount_text_'.$i.'">'.$currency->createPriceDiv ('discountAmount', '', $product['prices']).'</td>';
			//.round($product['prices']['discountAmount'],2).' '.JText::_('CATPRODUCT_CURRENCY').'</td>';
		}

		// if stock checking is enabled
		if ($check_stock == 1 && $product['child']['product_in_stock'] > 0 || $check_stock == 0){
			//Quantity
			// min max box quantity
			$min_order_level = '0';
			$max_order_level = '0';
			$product_box = '0';
			if (isset($product['qparam'])) {
				$min_order_level = $product['qparam']['min'];
				$max_order_level = $product['qparam']['max'];
				$product_box = $product['qparam']['box'];
			}
			// min max box end
			if (!VmConfig::get('use_as_catalog', 0)  ) {
				if ($see_price) {
					echo '<td class="cell_quantity">';
					//function showQuantityButtons ($function = 1, $i = 0, $min_order_level = 0, $max_order_level = 0, $product_box = 0 , $cssclasses = '',  $styles = '') {
					//echo $this->showQuantityButtons($show_qty,$def_group_qty,$i,$min_order_level,$max_order_level,$product_box);
					echo '<span class="quantity-box"><input class="quantity-input js-recalculate" size="2" id="quantity_'.$i.'" name="quantity[]" value="0" type="text" onblur=\'changeQuantity('.$i.', "input", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\'></span>
					<span class="quantity-controls js-recalculate"><input class="quantity-controls quantity-plus" onclick=\'changeQuantity('.$i.', "plus", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' type="button">
					<input class="quantity-controls quantity-minus" onclick=\'changeQuantity('.$i.', "minus", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' type="button"></span>';
					echo '</td>';
				} else {
					echo '<td class="cell_quantity"><input type="hidden" id="quantity_'.$i.'" name="quantity[]" value="0">';
					if (isset($product['child']['askquestion_url'])) {
						echo '<a class="ask-a-question bold" href="'.$product['child']['askquestion_url'].'" rel="nofollow" >'.JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE').'</a>';
					}
					echo '</td>';
				}
			}
			// sum weight
			if ($parametri["show_sum_weight"] == 1 && $see_price) {
				echo '<td class="cell_sum_weight" id="cell_sum_weight_'.$i.'"><span>0.00 '.$product['child']['product_weight_uom'].'</span></td>';
			}
			// sum base price without tax
			if ($parametri["show_sum_basePrice"] == 1 && $see_price) {
				echo '<td class="cell_sum_basePrice" id="cell_sum_basePrice_'.$i.'"><span >'.$currency->createPriceDiv ('', '', '0.00').'</td>';
			}
			// sum base price with tax
			if ($parametri["show_sum_basePriceWithTax"] == 1 && $see_price) {
				echo '<td class="cell_sum_basePriceWithTax" id="cell_sum_basePriceWithTax_'.$i.'"><span>'.$currency->createPriceDiv ('', '', '0.00').'</td>';
			}
			// sum tax
			if ($parametri["show_sum_taxAmount"] == 1 && $see_price) {
				echo '<td class="cell_sum_taxAmount" id="cell_sum_taxAmount_'.$i.'"><span>'.$currency->createPriceDiv ('', '', '0.00').'</td>';
			}
			// sum discount
			if ($parametri["show_sum_discountAmount"] == 1 && $see_price) {
				echo '<td class="cell_sum_discountAmount" id="cell_sum_discountAmount_'.$i.'"><span>'.$currency->createPriceDiv ('', '', '0.00').'</td>';
			}
			// sum final price without tax
			if ($parametri["show_sum_priceWithoutTax"] == 1 && $see_price) {
				echo '<td class="cell_sum_priceWithoutTax" id="cell_sum_priceWithoutTax_'.$i.'"><span>'.$currency->createPriceDiv ('', '', '0.00').'</td>';
			}
			// sum final price with tax
			if ($parametri["show_sum_salesPrice"] == 1 && $see_price) {
				echo '<td class="cell_sum_salesPrice" id="cell_sum_salesPrice_'.$i.'"><span>'.$currency->createPriceDiv ('', '', '0.00').'</td>';
			}
			$i++;
		}	
		else {
			if ($stockhandle == 'disableadd') {// if notify button
				echo '<td align="middle" colspan="3">';
				echo '<a href="'.JURI::base().'/index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$product['child']['virtuemart_product_id'].'" class="notify" iswrapped="1">'.JText::_('CATPRODUCT_NOTIFYME').'</a>';
				echo '</td>';
			}
			else {// if no stock
				echo '<td align="middle" colspan="3"><span style="color:red;">'.JText::_('CATPRODUCT_OUTOFSTOCK').'</span></td>';
			}
		}
		echo '</tr>';
	  }
	  // if title for attached products
	  else {
		echo '<tr class="row_attached_product">';
		echo '<td colspan="'.$colspan.'" style="text-align:center;">'.$product['child']['catproduct_inline_row'].'</td>';
		echo '</tr>';
	  }
	}
}
 ?>