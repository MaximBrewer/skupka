<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

?>
<fieldset class="vm-fieldset-pricelist">
<table
	class="cart-summary"
	cellspacing="0"
	cellpadding="0"
	border="0"
	width="100%">
<tr>
	<th class="vm-cart-item-name" ><?php echo vmText::_ ('COM_VIRTUEMART_CART_NAME') ?></th>
	<th class="vm-cart-item-sku" ><?php echo vmText::_ ('COM_VIRTUEMART_CART_SKU') ?></th>
	<th	class="vm-cart-item-basicprice" ><?php echo vmText::_ ('COM_VIRTUEMART_CART_PRICE') ?></th>
	<th	class="vm-cart-item-quantity" ><?php echo vmText::_ ('COM_VIRTUEMART_CART_QUANTITY') ?></th>
	<?php if (VmConfig::get ('show_tax')) {
		$tax = vmText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT');
		if(!empty($this->cart->cartData['VatTax'])){
			if(count($this->cart->cartData['VatTax']) < 2) {
				reset($this->cart->cartData['VatTax']);
				$taxd = current($this->cart->cartData['VatTax']);
				$tax = shopFunctionsF::getTaxNameWithValue($taxd['calc_name'],$taxd['calc_value']);
			}
		}
		?>
	<th class="vm-cart-item-tax" ><?php echo "<span  class='priceColor2'>" . $tax . '</span>' ?></th>
	<?php } ?>
	<th class="vm-cart-item-discount" ><?php echo "<span  class='priceColor2'>" . vmText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . '</span>' ?></th>
	<th class="vm-cart-item-total" ><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL') ?></th>
</tr>

<?php
$i = 1;

foreach ($this->cart->products as $pkey => $prow) {
	$product = $prow; 
	$prow->prices = array_merge($prow->prices,$this->cart->cartPrices[$pkey]);
?>

<tr style="vertical-align: top" class="sectiontableentry<?php echo $i ?>">
	<td class="vm-cart-item-name" >
		<input type="hidden" name="cartpos[]" value="<?php echo $pkey ?>">
		<?php if ($prow->virtuemart_media_id) { ?>
		<span class="cart-images">
			<?php
			if (!empty($prow->images[0])) {
				echo $prow->images[0]->displayMediaThumb ('', FALSE);
			} ?>
		</span>
		<?php } ?>
		<?php echo JHtml::link ($prow->url, $prow->product_name);
			echo $this->customfieldsModel->CustomsFieldCartDisplay ($prow); ?>
	</td>
	<td class="vm-cart-item-sku" ><?php  echo $prow->product_sku ?></td>
	<td class="vm-cart-item-basicprice" >
		<?php
		if (VmConfig::get ('checkout_show_origprice', 1) && $prow->prices['discountedPriceWithoutTax'] != $prow->prices['priceWithoutTax']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $prow->prices, TRUE, FALSE) . '</span><br />';
		}
		if ($prow->prices['discountedPriceWithoutTax']) {
			echo $this->currencyDisplay->createPriceDiv ('discountedPriceWithoutTax', '', $prow->prices, FALSE, FALSE, 1.0, false, true);
		} else {
			echo $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $prow->prices, FALSE, FALSE, 1.0, false, true);
		} ?>
	</td>
	<td class="vm-cart-item-quantity" ><?php
		if ($prow->step_order_level)
			$step=$prow->step_order_level;
		else
			$step=1;
		if($step==0)
			$step=1;
		?>
		<input type="text"
			onblur="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
			onclick="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
			onchange="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
			onsubmit="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
			title="<?php echo  vmText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="quantity-input js-recalculate" size="3" maxlength="4" name="quantity" id="quantity_for_<?php echo md5($product->cart_item_id); ?>"  value="<?php echo $prow->quantity ?>" />
		<button  onclick="return Onepage.updateProduct(this);" rel="<?php echo $product->cart_item_id.'|'.md5($product->cart_item_id); ?>" type="submit" class="vmicon vm2-add_quantity_cart" name="updatecart.<?php echo $pkey ?>" title="<?php echo  vmText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" ></button>
		<button rel="<?php echo $product->cart_item_id; ?>" onclick="return Onepage.deleteProduct(this);" type="submit" class="vmicon vm2-remove_from_cart" name="delete.<?php echo $pkey ?>" title="<?php echo vmText::_ ('COM_VIRTUEMART_CART_DELETE') ?>" ></button>
	</td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td class="vm-cart-item-tax" ><?php echo "<span class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('taxAmount', '', $prow->prices, FALSE, FALSE, $prow->quantity, false, true) . "</span>" ?></td>
	<?php } ?>
	<td class="vm-cart-item-discount" ><?php echo "<span class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('discountAmount', '', $prow->prices, FALSE, FALSE, $prow->quantity, false, true) . "</span>" ?></td>
	<td class="vm-cart-item-total">
		<?php
		if (VmConfig::get ('checkout_show_origprice', 1) && !empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceWithTax'] != $prow->prices['salesPrice']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceWithTax', '', $prow->prices, TRUE, FALSE, $prow->quantity) . '</span><br />';
		}
		elseif (VmConfig::get ('checkout_show_origprice', 1) && empty($prow->prices['basePriceWithTax']) && !empty($prow->prices['basePriceVariant']) && $prow->prices['basePriceVariant'] != $prow->prices['salesPrice']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $prow->prices, TRUE, FALSE, $prow->quantity) . '</span><br />';
		}
		echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $prow->prices, FALSE, FALSE, $prow->quantity) ?></td>
</tr>
	<?php
	$i = ($i==1) ? 2 : 1;
} ?>
<!--Begin of SubTotal, Tax, Shipment, Coupon Discount and Total listing -->
<?php if (VmConfig::get ('show_tax')) {
	$colspan = 3;
} else {
	$colspan = 2;
} ?>
<tr>
	<td colspan="4">&nbsp;</td>
	<td colspan="<?php echo $colspan ?>">
		<hr/>
	</td>
</tr>
<tr class="sectiontableentry1">
	<td colspan="4" style="text-align: right;"><?php echo vmText::_ ('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('taxAmount', '', $this->cart->cartPrices, FALSE, false, true) . "</span>" ?></td>
	<?php } ?>
	<td style="text-align: right;"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('discountAmount', '', $this->cart->cartPrices, FALSE) . "</span>" ?></td>
	<td style="text-align: right;" id="tt_order_subtotal_basket"><?php echo $subtotal_display ?></td>
</tr>

<?php
if (VmConfig::get ('coupons_enable')) {
?>
<tr class="sectiontableentry2">
	<td colspan="4" style="text-align: left;">
	
	
	
<div id="userForm" name="enterCouponCode" >
<?php
// If you have a coupon code, please enter it here:
?>  
	
	<input type="text" name="coupon_code" size="20" maxlength="50" class="coupon" alt="<?php echo $this->coupon_text ?>" placeholder="<?php echo $this->coupon_text ?>" value="<?php //echo $this->coupon_text; ?>" onblur="if(this.value=='') this.value='<?php echo $this->coupon_text; ?>';" onfocus="if(this.value=='<?php echo $this->coupon_text; ?>') this.value='';" />
    <span class="details-button">
    <input class="details-button" type="submit" name="setcoupon" title="<?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?>" value="<?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?>" onclick="return Onepage.setCouponAjax(this);"/>
    </span>

	
	
	
	
		
</div>


<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
		<?php
		echo $this->cart->cartData['couponCode'];
		echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')') : '';
		?>
	
		
	</td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ('couponTax', '', $this->cart->cartPrices['couponTax'], FALSE); ?> </td>
	<?php } ?>
	<td style="text-align: right;">&nbsp;</td>
	<td style="text-align: right;" id="tt_order_discount_after_basket"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceCoupon', '', $this->cart->cartPrices['salesPriceCoupon'], FALSE); ?> </td>
	<?php } else { ?>
	&nbsp;</td>
	<td colspan="<?php echo $colspan ?>" style="text-align: left;">&nbsp;</td>
	<?php }	?>
</tr>
<?php } ?>

<?php
foreach ($this->cart->cartData['DBTaxRulesBill'] as $rule) {
?>
<tr class="sectiontableentry<?php echo $i ?>">
	<td colspan="4" style="text-align: right;"><?php echo $rule['calc_name'] ?> </td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"></td>
	<?php } ?>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>&nbsp;</td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>&nbsp;</td>
</tr>
	<?php
	if ($i) {
		$i = 1;
	} else {
		$i = 0;
	}
} ?>

<?php

foreach ($this->cart->cartData['taxRulesBill'] as $rule) {
	if($rule['calc_value_mathop']=='avalara') continue;
	?>
<tr class="sectiontableentry<?php echo $i ?>">
	<td colspan="4" style="text-align: right;"><?php echo $rule['calc_name'] ?> </td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>&nbsp;</td>
	<?php } ?>
	<td style="text-align: right;"><?php ?> </td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>&nbsp;</td>
</tr>
	<?php
	if ($i) {
		$i = 1;
	} else {
		$i = 0;
	}
}

foreach ($this->cart->cartData['DATaxRulesBill'] as $rule) {
	?>
<tr class="sectiontableentry<?php echo $i ?>">
	<td colspan="4" style="text-align: right;"><?php echo   $rule['calc_name'] ?> </td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;">&nbsp;</td>
	<?php } ?>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
</tr>
	<?php
	if ($i) {
		$i = 1;
	} else {
		$i = 0;
	}
}


{

	?>
<tr class="sectiontableentry1" style="vertical-align:top;">
	<td colspan="4" style="align:left;vertical-align:top;">
		<?php
		echo '<h3>'.vmText::_ ('COM_VIRTUEMART_CART_SELECTED_SHIPMENT').'</h3>';
		?><div class="payment_top_wrapper"><?php
		 if (!empty($shipping_select)) echo $shipping_select; 
        ?></div>
		
	
	</td>
	

	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php
		echo "<span class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('shipmentTax', '', $this->cart->cartPrices['shipmentTax'], FALSE) . "</span>"; ?> </td>
	<?php } ?>
	<td style="text-align: right;"><?php if($this->cart->cartPrices['salesPriceShipment'] < 0) echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], FALSE); ?></td>
	<td style="text-align: right;" id="shipping_inside_basket_cost"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], FALSE); ?> </td>
</tr>
<?php 

} 

?>

<tr class="sectiontableentry1"  style="vertical-align:top;">

	<td colspan="4" style="align:left;vertical-align:top;">
	<h3><?php echo vmText::_ ('COM_VIRTUEMART_CART_SELECTED_PAYMENT'); ?></h3>
	<div id="payment_top_wrapper">
		<?php
		
		echo $payment_select; 
		if (!empty($force_hide_payment)) {
			//echo ' style="display: none;" '; 
			
		}
		
		?></div>
		</td>

	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('paymentTax', '', $this->cart->cartPrices['paymentTax'], FALSE) . "</span>"; ?> </td>
	<?php } ?>
	<td style="text-align: right;" ><?php if($this->cart->cartPrices['salesPricePayment'] < 0) echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], FALSE); ?></td>
	<td style="text-align: right;" id="payment_inside_basket_cost"><?php  echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], FALSE); ?> </td>
</tr>


<tr>
	<td colspan="4">&nbsp;</td>
	<td colspan="<?php echo $colspan ?>">
		<hr/>
	</td>
</tr>

<tr class="sectiontableentry2">
	<td colspan="4" style="text-align: right;"><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL') ?>:</td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"> <?php echo "<span  class='priceColor2' id=\"tt_tax_total_0_basket\">" . $this->currencyDisplay->createPriceDiv ('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], FALSE) . "</span>" ?> </td>
	<?php } ?>
	<td style="text-align: right;"> <?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('billDiscountAmount', '', $this->cart->cartPrices['billDiscountAmount'], FALSE) . "</span>" ?> </td>
	<td style="text-align: right;"><strong id="tt_total_basket"><?php echo $this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->cartPrices['billTotal'], FALSE); ?></strong></td>
</tr>

<?php

//Show VAT tax separated
if(!empty($this->cart->cartData)){
	if(!empty($this->cart->cartData['VatTax'])){
		$c = count($this->cart->cartData['VatTax']);
		if (!VmConfig::get ('show_tax') or $c>1) {
			if($c>0){ ?>

<tr class="sectiontableentry2">
	<td colspan="3">&nbsp;</td>
	<td colspan="2" style="text-align: left;border-bottom: 1px solid #333;"><?php echo vmText::_ ('COM_VIRTUEMART_TOTAL_INCL_TAX') ?></td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td>&nbsp;</td>
	<?php } ?>
	<td>&nbsp;</td>
</tr>
			<?php
			}
			foreach( $this->cart->cartData['VatTax'] as $vatTax ) {
				if(!empty($vatTax['result'])) { ?>
<tr class="sectiontableentry<?php echo $i ?>">
	<td colspan="3">&nbsp;</td>
	<td style="text-align: right;"><?php echo shopFunctionsF::getTaxNameWithValue($vatTax['calc_name'],$vatTax['calc_value']) ?></td>
	<td style="text-align: right;"><span class="priceColor2"><?php echo $this->currencyDisplay->createPriceDiv( 'taxAmount', '', $vatTax['result'], FALSE, false, 1.0,false,true ) ?></span></td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td >&nbsp;</td>
	<?php } ?>
	<td>&nbsp;</td>
</tr>
				<?php
				}
			}
		}
	}
}
?>

</table>
</fieldset>
<?php
