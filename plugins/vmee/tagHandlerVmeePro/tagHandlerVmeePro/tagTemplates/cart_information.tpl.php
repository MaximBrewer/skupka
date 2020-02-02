<?php
/**
 * @copyright    Copyright (C) 2012 InteraMind Advanced Analytics. All rights reserved.
 
 **/


if(!$is_items_info_empty){
	$colspan = 1;
	?>


<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr align="left">
		<?php if($is_show_product_thumb){ 
			$colspan++; ?>
		<th>&nbsp;</th>
		<?php }	?>
		<?php if($is_show_product_sku){ 
			$colspan++; ?>
		<th align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SKU') ?>
		</th>
		<?php }	?>
		<?php if($is_show_product_quantity){ 
			$colspan++; ?>
		<th align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_QTY') ?>
		</th>
		<?php }	?>
		<?php if($is_show_product_name){ 
			$colspan++; ?>
		<th align="left"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_NAME_TITLE') ?>
		</th>
		<?php }	?>
		<?php if($is_show_product_price){ 
			$colspan++; ?>
		<th align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRICE') ?>
		</th>
		<?php if($is_show_tax){ ?>
		<th align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_TAX') ?>
		</th>
		<?php } ?>
		<?php } ?>
		<?php if($is_show_totals){  
			$colspan += 2; ?>
		<th align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SUBTOTAL_DISCOUNT_AMOUNT') ?>
		</th>
		<th align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>
		</th>
		<?php }	?>
	</tr>
	<?php
	foreach ($cart->products as $pkey =>$prow) {
		$_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $prow->virtuemart_product_id);
		?>
	<tr valign="top">
		<?php 
		if($is_show_product_thumb){
			if ( isset($prow->thumbUrl)) {
				echo '<td><img src="' . JUri::root() . $prow->thumbUrl . '" style="width: '. $productThumbWidth .'px; "/></td>';
			}
			else{
				echo '<td>&nbsp;</td>';
			}
		}
		if($is_show_product_sku){
			?>
		<td align="left"><?php echo $prow->product_sku; ?>
		</td>
		<?php 
		}
		if($is_show_product_quantity){
			?>
		<td align="left"><?php echo $prow->quantity; ?>
		</td>
		<?php 
		}
		if($is_show_product_name){
			?>
		<td align="left"><a href="<?php echo $_link; ?>"><?php echo $prow->product_name ?>
		</a></td>
		<?php 
		}
		if($is_show_product_price){
			?>
		<td align="right"><?php
		if ( !empty($prow->basePriceWithTax ) && $prow->basePriceWithTax != $prow->salesPrice ) {
			echo '<span >'.$prow->basePriceWithTax  .'</span><br />' ;
		}
		?> <?php echo $prow->salesPrice; ?>
		</td>
		<?php if($is_show_tax){ ?>
		<td align="right"><?php echo "<span>" .  $prow->subtotal_tax_amount   . "</span>" ?>
		</td>
		<?php 
		}
		}
		if($is_show_totals){
			?>
		<td align="right"><?php echo   $prow->subtotal_discount; ?>
		</td>
		<td align="right"><?php echo $prow->subtotal_with_tax  ; ?>
		</td>
		<?php 
		}
		?>
	</tr>
	<?php
	}
	if($is_show_subtotal){
		?>
	<tr>
		<td colspan="<?php echo $colspan-3; ?>" align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?>
		</td>

		<?php if ($is_show_tax) { ?>
		<td align="right"><?php echo "<span >".$cart->prices['taxAmount']."</span>" ?>
		</td>
		<?php } ?>
		<td align="right"><?php echo "<span>".$cart->prices['discountAmount'] ."</span>" ?>
		</td>
		<td align="right"><?php echo $cart->prices['salesPrice'] ?>
		</td>
	</tr>
	<?php 
	}
	if($is_show_coupon_discount){
		if ($cart->cartData['couponCode']) {
			$coupon_code=$cart->cartData['couponCode']?' ('.$cart->cartData['couponCode'].')':'';
			?>
	<tr>
		<td align="right" colspan="<?php echo $colspan - 2; ?>"><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT').$coupon_code?>
		</td>


		<?php if ($is_show_tax) { ?>
		<td align="right"><?php echo $cart->prices['couponTax']; ?>
		</td>
		<?php } ?>
		<td align="right"><?php echo $cart->prices['salesPriceCoupon']; ?>
		</td>
		<td align="right">&nbsp;</td>
	</tr>
	<?php
	?>

	<?php 
		}
	} ?>


	<?php
	if ($is_show_subtotal) {
		foreach($cart->cartData['DBTaxRulesBill'] as $rule){
			?>
	<tr>
		<td colspan="<?php echo $colspan-3; ?>" align="right"><?php echo $rule['calc_name'] ?>
		</td>

		<?php if ($is_show_tax) { ?>
		<td align="right"></td>
		<?php } ?>
		<td align="right"><?php echo  $cart->prices[$rule['virtuemart_calc_id'].'Diff'];  ?>
		</td>
		<td align="right"><?php echo  $cart->prices[$rule['virtuemart_calc_id'].'Diff'];  ?>
		</td>
	</tr>
	<?php
		}
		foreach($cart->cartData['taxRulesBill'] as $rule){
			?>
	<tr>
		<td colspan="<?php echo $colspan-3; ?>" align="right"><?php echo $rule['calc_name'] ?>
		</td>
		<?php if ( $is_show_tax) { ?>
		<td align="right"><?php echo $cart->prices[$rule['virtuemart_calc_id'].'Diff']; ?>
		</td>
		<?php } ?>
		<td align="right"><?php    ?>
		</td>
		<td align="right"><?php echo $cart->prices[$rule['virtuemart_calc_id'].'Diff'];   ?>
		</td>
	</tr>
	<?php
		}
		foreach($cart->cartData['DATaxRulesBill'] as $rule){
			?>
	<tr>
		<td colspan="<?php echo $colspan-3; ?>" align="right"><?php echo $rule['calc_name'] ?>
		</td>
		<?php if ($is_show_tax) { ?>
		<td align="right"><?php echo   $cart->prices[$rule['virtuemart_calc_id'].'Diff']; ?>
		</td>
		<?php } ?>
		<td align="right"><?php    ?>
		</td>
		<td align="right"><?php echo $cart->prices[$rule['virtuemart_calc_id'].'Diff'];  ?>
		</td>
	</tr>

	<?php
		}
	}
	if($is_show_shipping){
		?>

	<tr>
		<td align="right" colspan="<?php echo $colspan-3; ?>"><?php echo $cart->cartData['shipmentName'] ?>
		</td>

		<?php if ($is_show_tax) { ?>
		<td align="right"><?php echo "<span >" . $cart->prices['shipmentTax'] . "</span>" ?>
		</td>
		<?php } ?>
		<td align="right"><?php    ?>
		</td>
		<td align="right"><?php echo $cart->prices['salesPriceShipment']; ?>
		</td>

	</tr>
	<?php
	}
	if($is_show_totals){
		?>
	<tr>
		<td align="right" colspan="<?php echo $colspan-3; ?>"><?php echo $cart->cartData['paymentName'];  ?>
		</td>


		<?php if ($is_show_tax) { ?>
		<td align="right"><?php echo "<span >" . $cart->prices['paymentTax'] . "</span>" ?>
		</td>
		<?php } ?>
		<td align="right"><?php    ?>
		</td>
		<td align="right"><?php echo $cart->prices['salesPricePayment']; ?>
		</td>


	</tr>
	<tr>
		<td align="right" colspan="<?php echo $colspan-3; ?>"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>
		</strong></td>

		<?php if ($is_show_tax) { ?>
		<td align="right"><span><?php echo $cart->prices['billTaxAmount']; ?>
		</span></td>
		<?php } ?>
		<td align="right"><?php   echo $cart->prices['billDiscountAmount']; ?>
		</td>
		<td align="right"><strong><?php echo $cart->prices['billTotal']; ?>
		</strong></td>
	</tr>
	<?php 
	}
	?>
</table>
<?php 
}
?>
