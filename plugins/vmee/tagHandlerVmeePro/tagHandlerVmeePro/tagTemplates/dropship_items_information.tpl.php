<?php
/**
 * @copyright    Copyright (C) 2012 InteraMind Advanced Analytics. All rights reserved.
 
 **/


if(!$is_items_info_empty){
	$colspan = 0;
?>


<table  cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr align="left" >
    <?php if($is_show_product_thumb){ $colspan++; ?>
		<th>&nbsp;</th>
	<?php }	?>
	<?php if($is_show_product_sku){ $colspan++; ?>
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SKU') ?></th>
	<?php }	?>
	<?php if($is_show_product_quantity){ $colspan++; ?>
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_QTY') ?></th>
	<?php }	?>
	<?php if($is_show_product_name){ $colspan++; ?>
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_PRODUCT_NAME_TITLE') ?></th>
	<?php }	?>
	<?php if($is_show_product_price){ $colspan++; ?>
		<th align="right" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRICE') ?></th>
		<?php if($is_show_tax){ ?>
    		<th align="right" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_TAX') ?></th>
		<?php } ?>
	<?php } ?>
	<?php if($is_show_totals){  $colspan ++; ?>
		<th align="right" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></th>
	<?php }	?>
    </tr>
    <?php
    $sub_total = 0.00;
    $sub_tax = 0.00;
    $bFilterFound = false;
    foreach ($order['items'] as $item) {
    if(!empty($filterName)){
		switch ($filterName){
			case 'vendor_id':
				if($item->vendor_id !== $filterVal){
					continue 2;
				}
			case 'manufacturer_id':
				if($item->manufacturer_id !== $filterVal){
					continue 2;
				}
				break;
		}
    }
    $bFilterFound = true;
    $sub_total += $item->product_quantity * $item->product_final_price;
    $sub_tax += $item->product_tax;
	$_link = JUri::root() . 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $item->virtuemart_product_id;
	?>
        <tr valign="top">
        <?php 
        if($is_show_product_thumb){
        	if ( isset($item->image_thumb)) {
        		echo '<td><img src="' . JUri::root() . $item->image_thumb . '" style="width: '. $productThumbWidth .'px; "/></td>';
        	}
        }
        if($is_show_product_sku){
        ?>
	    	<td align="left" >
			<?php echo $item->order_item_sku; ?>
	    	</td>
    	<?php 
        }
        if($is_show_product_quantity){
    	?>
	    	<td align="left" >
			<?php echo $item->product_quantity; ?>
	    	</td>
    	<?php 
        }
        if($is_show_product_name){
    	?>
    	<td align="left" >
    	    <a href="<?php echo $_link; ?>"><?php echo $item->order_item_name; ?></a>

		<?php
		if (!empty($item->product_attribute)) {
		    if (!class_exists('VirtueMartModelCustomfields'))
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'customfields.php');
		    $product_attribute = VirtueMartModelCustomfields::CustomsFieldOrderDisplay($item,'FE');
		    echo '<div>' . $product_attribute . '</div>';
		}
		?>
    	</td>
		<?php 
        }
        if($is_show_product_price){
			?>
	    	<td align="right" >
		    <?php
		    if ( !empty($item->product_basePriceWithTax ) && $item->product_basePriceWithTax != $item->product_final_price ) {
				echo '<span >'.$vendor->currency->priceDisplay($item->product_basePriceWithTax ) .'</span><br />' ;
			}
			?>
			<?php echo $vendor->currency->priceDisplay($item->product_final_price); ?>
	    	</td>
		    <?php if($is_show_tax){ ?>
				<td align="right"><?php echo "<span>" . $vendor->currency->priceDisplay( $item->product_tax  ) . "</span>" ?></td>
		    <?php 
			}
		}
		if($is_show_totals){ 
		?>
			<td align="right" >
			<?php echo $vendor->currency->priceDisplay($item->product_subtotal_with_tax  ); ?>
	    	</td>
		<?php 
		}
		?>
        </tr>
	<?php
    }
    if($bFilterFound == true){
    if($is_show_subtotal){
    ?>
		<tr>
			<td colspan="<?php echo $colspan-1; ?>" align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td>

                        <?php if ($is_show_tax) { ?>
			<td align="right"><?php echo "<span >".$vendor->currency->priceDisplay($sub_tax)."</span>" ?></td>
                        <?php } ?>
			<td align="right"><?php echo $vendor->currency->priceDisplay($sub_total) ?></td>
		</tr>
<?php 
    }
?>
</table>
<?php 
}
}
?>
