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
	$stockhandle = VmConfig::get('stockhandle','none');
	$check_stock = 0;
	$global_params = $viewData[3];
	$see_price = true;
	if (isset($global_params["show_prices"])) $see_price = $global_params["show_prices"];
	
	if ($global_params['use_default'] == 1) {
		$parametri = $global_params;
	}
	else {
		if (isset($viewData[2]->custom_param)) {
			$parametri = json_decode($viewData[2]->custom_param, true);
		} else {
			$parametri = (array) $viewData[2];
		}
	}
	if ($stockhandle == 'disableadd' || $stockhandle == 'disableit_children' || $stockhandle == 'disableit') { $check_stock = 1; }
	$colspan = 0;
	$callscript = '';
	/*if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
	$currency = CurrencyDisplay::getInstance ();*/
	if (!class_exists ('CatproductFunction')) { require(JPATH_PLUGINS . DS . 'vmcustom' . DS . 'catproduct' . DS . 'catproduct' . DS . 'helpers' . DS . 'catproductfunctions.php'); }
	$currency = new CatproductFunction();
	
	// for customfields support
	if (isset($viewData[2]->add_plugin_support)) {
		$use_customfields = $viewData[2]->add_plugin_support;
	} else if(isset($viewData[2]->custom_param)) {
		$use_customfields = json_decode($viewData[2]->custom_param, true);
		if (isset($use_customfields["add_plugin_support"])) {
			$use_customfields = $use_customfields["add_plugin_support"];
		} else {
		$use_customfields = 0;
		}
	} else {
		$use_customfields = 0;
	}
	$image_script  = '';
	?>
	
<style type="text/css">	
	.product-fields .product-field input { 
left: 0px !important;
}
</style>
	<form  ax:nowrap="1" action="index.php" method="post" name="catproduct_form" id="catproduct_form" onsubmit="<?php if ($use_customfields == 0) echo "handleToCart();"; else echo "handleToCartOneByOne();"; ?> return false;">
<table style="width:100%;" class="catproducttable">
<caption><?php echo JText::_('CATPRODUCT_TABLE_TITLE') ?></caption>
<thead>
<tr>
	<?php 
	if ($parametri["show_image"] == 1) { ?>
		<th class="cell_image"><?php echo JText::_('CATPRODUCT_TABLE_IMAGE_FIELD') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_id"] == 1) { ?>
		<th class="cell_id"><?php echo JText::_('CATPRODUCT_TABLE_ID_FIELD') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_sku"] == 1) { ?>
		<th class="cell_sku"><?php echo JText::_('CATPRODUCT_TABLE_SKU_FIELD') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_name"] == 1) { ?>
		<th class="cell_name"><?php echo JText::_('CATPRODUCT_TABLE_NAME_FIELD') ?></th>
	<?php $colspan += 1;}
	if ($use_customfields == 1) { ?>
		<th class="cell_customfields"><?php echo JText::_('CATPRODUCT_TABLE_CUSTOMFIELDS') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_s_desc"] == 1) { ?>
		<th class="cell_name"><?php echo JText::_('CATPRODUCT_TABLE_DESC_FIELD') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_weight"] == 1) { ?>
		<th class="cell_weight"><?php echo JText::_('CATPRODUCT_TABLE_WEIGHT') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_sizes"] == 1) { ?>
		<th class="cell_size"><?php echo JText::_('CATPRODUCT_TABLE_SIZES') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_stock"] == 1) { ?>
		<th class="cell_stock"><?php echo JText::_('CATPRODUCT_TABLE_STOCK') ?></th>
	<?php $colspan += 1;}
	if (isset($parametri["show_min_qty"]) && $parametri["show_min_qty"] == 1) { ?>
		<th class="cell_stock"><?php echo JText::_('CATPRODUCT_TABLE_MIN_QTY') ?></th>
	<?php $colspan += 1;}
	if (isset($parametri["show_max_qty"]) && $parametri["show_max_qty"] == 1) { ?>
		<th class="cell_stock"><?php echo JText::_('CATPRODUCT_TABLE_MAX_QTY') ?></th>
	<?php $colspan += 1;}
	if (isset($parametri["show_step_qty"]) && $parametri["show_step_qty"] == 1) { ?>
		<th class="cell_stock"><?php echo JText::_('CATPRODUCT_TABLE_STEP_QTY') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_basePrice"] == 1 && $see_price) { ?>
		<th class="cell_basePrice"><?php echo JText::_('CATPRODUCT_TABLE_BASEPRICE') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_basePriceWithTax"] == 1 && $see_price) { ?>
		<th class="cell_basePriceWithTax"><?php echo JText::_('CATPRODUCT_TABLE_BASEPRICEWITHTAX') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_priceWithoutTax"] == 1 && $see_price) { ?>
		<th class="cell_priceWithoutTax"><?php echo JText::_('CATPRODUCT_TABLE_PRICEWITHOUTTAX') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_salesPrice"] == 1 && $see_price) { ?>
		<th class="cell_salesPrice"><?php echo JText::_('CATPRODUCT_TABLE_SALESPRICE') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_taxAmount"] == 1 && $see_price) { ?>
		<th class="cell_taxAmount"><?php echo JText::_('CATPRODUCT_TABLE_TAXAMOUNT') ?></th>
	<?php $colspan += 1;}
	if ($parametri["show_discountAmount"] == 1 && $see_price) { ?>
		<th class="cell_discountAmount"><?php echo JText::_('CATPRODUCT_TABLE_DISCOUNTAMOUNT') ?></th>
	<?php $colspan += 1;} 
	
	// now Quantity
	if (!VmConfig::get('use_as_catalog', 0)  ) { if ($see_price) { ?>
	<th class="cell_quantity"><?php echo JText::_('CATPRODUCT_TABLE_QUANTITY'); $colspan += 1; ?></th>
	<?php } else { ?>
	<th class="cell_quantity"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ASKPRICE'); } ?></th> <?php  }
	
	if ($parametri["show_sum_weight"] == 1) { ?>
		<th class="cell_sum_weight"><?php echo JText::_('CATPRODUCT_TABLE_SUM_WEIGHT') ?></th>
	<?php $colspan += 1;$callscript .= 'show_sum(art_id,uom,"sum_weight_","cell_sum_weight_");';
	}
	if ($parametri["show_sum_basePrice"] == 1) { ?>
		<th class="cell_sum_basePrice"><?php echo JText::_('CATPRODUCT_TABLE_SUM_BASEPRICE') ?></th>
	<?php $colspan += 1;$callscript .= 'show_sum(art_id,unit,"sum_basePrice_","cell_sum_basePrice_");';
	}
	if ($parametri["show_sum_basePriceWithTax"] == 1) { ?>
		<th class="cell_sum_basePriceWithTax"><?php echo JText::_('CATPRODUCT_TABLE_SUM_BASEPRICEWITHTAX') ?></th>
	<?php $colspan += 1;$callscript .= 'show_sum(art_id,unit,"sum_basePriceWithTax_","cell_sum_basePriceWithTax_");';
	}	
	if ($parametri["show_sum_taxAmount"] == 1) { ?>
		<th class="cell_sum_taxAmount"><?php echo JText::_('CATPRODUCT_TABLE_SUM_TAXAMOUNT') ?></th>
	<?php $colspan += 1;$callscript .= 'show_sum(art_id,unit,"sum_taxAmount_","cell_sum_taxAmount_");';
	}
	if ($parametri["show_sum_discountAmount"] == 1) { ?>
		<th class="cell_sum_discountAmount"><?php echo JText::_('CATPRODUCT_TABLE_SUM_DISCOUNTAMOUNT') ?></th>
	<?php $colspan += 1;$callscript .= 'show_sum(art_id,unit,"sum_discountAmount_","cell_sum_discountAmount_");';
	}
	if ($parametri["show_sum_priceWithoutTax"] == 1) { ?>
		<th class="cell_sum_priceWithoutTax"><?php echo JText::_('CATPRODUCT_TABLE_SUM_PRICEWITHOUTTAX') ?></th>
	<?php $colspan += 1;$callscript .= 'show_sum(art_id,unit,"sum_priceWithoutTax_","cell_sum_priceWithoutTax_");';
	}
	if ($parametri["show_sum_salesPrice"] == 1) { ?>
		<th class="cell_sum_salesPrice"><?php echo JText::_('CATPRODUCT_TABLE_SUM_SALESPRICE') ?></th>
	<?php $colspan += 1;$callscript .= 'show_sum(art_id,unit,"sum_salesPrice_","cell_sum_salesPrice_");';
	}
 ?>
 </tr>
</thead>
<tbody>
<?php
$i = 0;
$group_id = 0;
foreach ($viewData[0] as $group) { 
	//print_r($group['params']);
	if (isset($group['params']['layout']) && $group['params']['layout'] <> '') $layout = $group['params']['layout'];
	else $layout = 'default.php';
	if (isset($group['params']['def_qty']) && $group['params']['def_qty'] <> '') $def_group_qty = $group['params']['def_qty'];
	else $def_group_qty = '1';
	if (isset($group['params']['layout']) && $group['params']['show_qty'] <> '') $show_qty = $group['params']['show_qty'];
	else $show_qty = '1';
	
	ob_start();
	include (JPATH_ROOT.DS.'plugins'.DS.'vmcustom'.DS.'catproduct'.DS.'catproduct'.DS.'tmpl'.DS.'group_layouts'.DS.$layout);
//	include (JPATH_ROOT.DS.'plugins'.DS.'vmcustom'.DS.'catproduct'.DS.'catproduct'.DS.'tmpl'.DS.'checkbox1.php');
//	include (JPATH_ROOT.DS.'plugins'.DS.'vmcustom'.DS.'catproduct'.DS.'catproduct'.DS.'tmpl'.DS.'radio1.php');
	
	//include 'default1.php';
	$output =	ob_get_clean();
	echo $output;
}

	// total weight
	if ($parametri["show_total_weight"] == 1 && $see_price) {
		echo '<tr class="row_total_weight">
			<td colspan="'.($colspan-1).'" align="right">'.JText::_('CATPRODUCT_TABLE_TOTAL_WEIGHT').'</td>
			<td align="right" id="total_weight">0.00 '.$product['child']['product_weight_uom'].'</td>
		</tr>';
		$callscript .= 'total_field("sum_weight","total_weight",uom);';
	}
	// total base price without tax
	if ($parametri["show_total_basePrice"] == 1 && $see_price) {
		echo '<tr class="row_total_basePrice">
			<td colspan="'.($colspan-1).'" align="right">'.JText::_('CATPRODUCT_TABLE_TOTAL_BASEPRICE').'</td>
			<td align="right" id="total_basePrice">'.$currency->createPriceDiv ('', '', '0.00').'</td>
		</tr>';
		$callscript .= 'total_field("sum_basePrice","total_basePrice",unit);';
	}
	// total base price with tax
	if ($parametri["show_total_basePriceWithTax"] == 1 && $see_price) {
		echo '<tr class="row_total_basePriceWithTax">
			<td colspan="'.($colspan-1).'" align="right">'.JText::_('CATPRODUCT_TABLE_TOTAL_BASEPRICEWITHTAX').'</td>
			<td align="right" id="total_basePriceWithTax">'.$currency->createPriceDiv ('', '', '0.00').'</td>
		</tr>';
		$callscript .= 'total_field("sum_basePriceWithTax","total_basePriceWithTax",unit);';
	}
	// total tax amount
	if ($parametri["show_total_taxAmount"] == 1 && $see_price) {
		echo '<tr class="row_total_taxAmount">
			<td colspan="'.($colspan-1).'" align="right">'.JText::_('CATPRODUCT_TABLE_TOTAL_TAXAMOUNT').'</td>
			<td align="right" id="total_taxAmount">'.$currency->createPriceDiv ('', '', '0.00').'</td>
		</tr>';
		$callscript .= 'total_field("sum_taxAmount","total_taxAmount",unit);';
	}
	// total discount amount
	if ($parametri["show_total_discountAmount"] == 1 && $see_price) {
		echo '<tr class="row_total_discountAmount">
			<td colspan="'.($colspan-1).'" align="right">'.JText::_('CATPRODUCT_TABLE_TOTAL_DISCOUNTAMOUNT').'</td>
			<td align="right" id="total_discountAmount">'.$currency->createPriceDiv ('', '', '0.00').'</td>
		</tr>';
		$callscript .= 'total_field("sum_discountAmount","total_discountAmount",unit);';
	}
	// total final price without tax
	if ($parametri["show_total_priceWithoutTax"] == 1 && $see_price) {
		echo '<tr class="row_total_priceWithoutTax">
			<td colspan="'.($colspan-1).'" align="right">'.JText::_('CATPRODUCT_TABLE_TOTAL_PRICEWITHOUTTAX').'</td>
			<td align="right" id="total_priceWithoutTax">'.$currency->createPriceDiv ('', '', '0.00').'</td>
		</tr>';
		$callscript .= 'total_field("sum_priceWithoutTax","total_priceWithoutTax",unit);';
	}
	// total final price with tax
	if ($parametri["show_total_salesPrice"] == 1 && $see_price) {
		echo '<tr class="row_total_salesPrice">
			<td colspan="'.($colspan-1).'" align="right">'.JText::_('CATPRODUCT_TABLE_TOTAL_SALESPRICE').'</td>
			<td align="right" id="total_salesPrice">'.$currency->createPriceDiv ('', '', '0.00').'</td>
		</tr>';
		$callscript .= 'total_field("sum_salesPrice","total_salesPrice",unit);';
	}
	// if use as catalog
	if (!VmConfig::get('use_as_catalog', 0) && $see_price) {
	?>
	<tr>
		<td colspan="<?php echo $colspan ?>" class="cell_addToCart" align="right">
		<span class="addtocart-button" style="float:right;">
  <input type="submit" name="addtocart" class="addtocart-button" value="<?php echo JText::_('CATPRODUCT_ADDTOCART') ?>" title="<?php echo JText::_('CATPRODUCT_ADDTOCART') ?>">
</span>
		</td>
		</tr>
		<?php } ?>
	</tbody>
	</table>

  <input name="option" value="com_virtuemart" type="hidden">
  <input name="view" value="cart" type="hidden">
  <input name="task" value="addJS" type="hidden">	
  <input name="format" value="json" type="hidden">	
</form>

<div id="catproduct-loading">
    <img src="<?php echo JURI::root(true) ?>/plugins/vmcustom/catproduct/catproduct/css/ajax-loader.gif" />
 </div>

<?php

	// preventing 2 x load javascript
	$version = new JVersion();
	static $textinputjs;
	if ($textinputjs) return true;
	$textinputjs = true ;
	$document = JFactory::getDocument();
	//min_order_level=null|max_order_level=null|product_box=null
	$document->addScriptDeclaration('
	function updateSumPrice(art_id) {
		var uom = jQuery("#product_weight_uom_"+art_id).val();
		var unit = "'.$currency->getSymbol().'";
		if (jQuery("#virtuemart_product_id"+art_id).attr("type") == "radio") {
		jQuery("#catproduct_form input[name^=\'virtuemart_product_id[]\']").each(function(){
			art_id = jQuery(this).attr("id");
			art_id = art_id.replace("virtuemart_product_id","");
			if (jQuery("#catproduct_form input[id=\'virtuemart_product_id"+art_id+"\']:checked").length != 0) {
			quantity = getQuantity(art_id);}
			else { quantity = 0;}
			sum_field(art_id,quantity,"product_weight_","sum_weight_");
			sum_field(art_id,quantity,"basePrice_","sum_basePrice_");
			sum_field(art_id,quantity,"basePriceWithTax_","sum_basePriceWithTax_");
			sum_field(art_id,quantity,"taxAmount_","sum_taxAmount_");
			sum_field(art_id,quantity,"discountAmount_","sum_discountAmount_");
			sum_field(art_id,quantity,"priceWithoutTax_","sum_priceWithoutTax_");
			sum_field(art_id,quantity,"salesPrice_","sum_salesPrice_");
			'.$callscript.'
		});
		} else {
		quantity = getQuantity(art_id); 
		sum_field(art_id,quantity,"product_weight_","sum_weight_");
		sum_field(art_id,quantity,"basePrice_","sum_basePrice_");
		sum_field(art_id,quantity,"basePriceWithTax_","sum_basePriceWithTax_");
		sum_field(art_id,quantity,"taxAmount_","sum_taxAmount_");
		sum_field(art_id,quantity,"discountAmount_","sum_discountAmount_");
		sum_field(art_id,quantity,"priceWithoutTax_","sum_priceWithoutTax_");
		sum_field(art_id,quantity,"salesPrice_","sum_salesPrice_");
		'.$callscript.'
		}
	}
	');
	$document->addScriptDeclaration($image_script);
	
	
	$document->addScriptDeclaration('function removeNoQ (text) {
		return text.replace("'.JText::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY').'","'.JText::_('COM_VIRTUEMART_CART_PRODUCT_ADDED').'");
	}');
	if ($version->RELEASE <> '1.5') {
		$document->addScript(JURI::root(true). "/plugins/vmcustom/catproduct/catproduct/js/javascript.js");
		$document->addStyleSheet(JURI::root(true). "/plugins/vmcustom/catproduct/catproduct/css/catproduct.css");
	}
	else {
		$document->addScript(JURI::root(true). "/plugins/vmcustom/catproduct/js/catproduct.js");
		$document->addStyleSheet(JURI::root(true). "/plugins/vmcustom/catproduct/css/catproduct.css");
	}

 ?>