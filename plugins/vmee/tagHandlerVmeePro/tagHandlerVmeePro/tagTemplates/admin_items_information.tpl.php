<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Component
 **/

if(!$is_items_info_empty){
	$colspan = 0;
?>
<table  cellspacing="0" cellpadding="0" border="0" width="100%" style="<?php echo $main_table_style ?>">
    <tr align="left" >
    <?php if($is_show_product_thumb){ $colspan++; ?>
		<th style="<?php echo $table_th_style ?>">&nbsp;</th>
	<?php }	?>
	<?php if($is_show_product_sku){ $colspan++; ?>
		<th style="<?php echo $table_th_style ?>" align="left" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SKU') ?></th>
	<?php }	?>
	<?php if($is_show_product_quantity){ $colspan++; ?>
		<th style="<?php echo $table_th_style ?>" align="left" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_QTY') ?></th>
	<?php }	?>
	<?php if($is_show_product_name){ $colspan++; ?>
		<th style="<?php echo $table_th_style ?>" align="left" ><?php echo JText::_('COM_VIRTUEMART_PRODUCT_NAME_TITLE') ?></th>
	<?php }	?>
	<?php if($is_show_product_price){ $colspan++; ?>
		<th style="<?php echo $table_th_style ?>" align="right" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRICE') ?></th>
	<?php } ?>
	<?php if($is_show_product_tax && VmConfig::get('show_tax') ){ ?>
    	<th style="<?php echo $table_th_style ?>" align="right" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_TAX') ?></th>
	<?php } ?>
	<?php if($is_show_product_discount){   ?>
		<th style="<?php echo $table_th_style ?>" align="right" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SUBTOTAL_DISCOUNT_AMOUNT') ?></th>
	<?php }	?>
	<?php if($is_show_product_total){ ?>
		<th style="<?php echo $table_th_style ?>" align="right" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></th>
	<?php }	?>
    </tr>
    <?php
    foreach ($order['items'] as $item) {
    	$qtt = $item->product_quantity ;
		$_link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $item->virtuemart_product_id;
	?>
        <tr valign="top">
        <?php 
        if($is_show_product_thumb){
        	if ( isset($item->image_thumb)) {
        		//echo '<td><a href="'.$_link.'"><img src="' . JUri::root() . $item->image_thumb . '" style="width: '. $productThumbWidth .'px; "/></a></td>';
        		echo '<td><a href="'.$_link.'"><img width="'. $productThumbWidth .'" src="' . JUri::root() . $item->image_thumb . '" /></a></td>';
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
			<?php echo $qtt; ?>
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
       		if (!empty($item->product_final_price) && $item->product_final_price != $item->product_basePriceWithTax) {
		    	echo '<span style="text-decoration:line-through; display:none;">'.$currency->priceDisplay($item->product_basePriceWithTax) .'<br /></span>';
		    	echo '<span >'.$currency->priceDisplay($item->product_final_price) .'</span><br />';
		    } else {
		    	echo '<span >'.$currency->priceDisplay($item->product_final_price) .'</span><br />';
		    }
// 			echo $currency->priceDisplay($item->product_final_price); 
//			echo $currency->priceDisplay($item->product_subtotal_discount); 
//			echo $currency->priceDisplay($item->product_subtotal_with_tax, 0);
		?>
	    	</td>
	    <?php 
		}
    	if($is_show_product_tax && VmConfig::get('show_tax') ){ 
		?>
			<td align="right"><?php echo "<span>" . $currency->priceDisplay( $item->product_tax) . "</span>" ?></td>
		<?php 
    	}
    	if($is_show_product_discount){ 
		?>
	    	<td align="right" >
			<?php echo $currency->priceDisplay(  $item->product_subtotal_discount); ?>
	    	</td>
		<?php 
		}
    	if($is_show_product_total){ 
		?>
			<td align="right" >
			<?php 
    	 	$item->product_basePriceWithTax = (float) $item->product_basePriceWithTax;
		    if ( !empty($item->product_basePriceWithTax ) && $item->product_basePriceWithTax != $item->product_final_price ) {
				echo '<span style="text-decoration:line-through; display:none;">'.$currency->priceDisplay($item->product_basePriceWithTax, 0, $qtt ) .'<br /></span>' ;
			}
			echo $currency->priceDisplay($item->product_subtotal_with_tax, 0); ?>
	    	</td>
		<?php 
		}	
		?>
        </tr>
		<?php
	}//close loop
	
	$hr_colspan = $colspan;
	if($is_show_product_tax && VmConfig::get('show_tax'))
		$hr_colspan++;
	if($is_show_product_discount)
		$hr_colspan++;
	if($is_show_product_total)
		$hr_colspan++;
	?>
	<tr>
		<td colspan="<?php echo $hr_colspan; ?>"><hr style=" border-top: 1px; height:1px; color:#444"></td>
	</tr>
	<?php  
//    if(!$is_show_product_tax){ $colspan++; };
    
    if($is_show_subtotal){
    ?>
		<tr>
			<td colspan="<?php echo $colspan; ?>" align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td>

            <?php 
            if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
				<td align="right"><?php echo "<span >".$currency->priceDisplay($order_details->order_tax)."</span>" ?></td>
            <?php 
            }
            if($is_show_product_discount){  ?>
			<td align="right"><?php echo "<span>".$currency->priceDisplay($order_details->order_discountAmount )."</span>" ?></td>
			<?php 
            }
            if($is_show_product_total){ ?>
			<td align="right"><?php echo $currency->priceDisplay($order_details->order_salesPrice) ?></td>
			<?php 
            }
            
            ?>
		</tr>
	<?php 
    }
    
    
    if($is_show_coupon_discount){
		if ($order_details->coupon_code) {
			    $coupon_code=$order_details->coupon_code?' ('.$order_details->coupon_code.')':'';
		?>
		        <tr>
			    	<td align="right" colspan="<?php echo $colspan; ?>"><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT')?> <?php echo $coupon_code ?></td> 
			    <?php 
			    if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
					<td align="right">&nbsp;</td>
				<?php 
			    }
            	if($is_show_product_discount){  ?>
			    	<td align="right"><?php $currency->priceDisplay($order_details->coupon_discount); ?></td>
			    <?php 
            	}
            	if($is_show_product_total){ ?>
					<td align="right"><?php echo '- '.$currency->priceDisplay($order_details->coupon_discount); ?></td>
				<?php 
	            }
            	
	            ?>
		        </tr>
			<?php 
		}
	} 
	if ($is_show_subtotal) {
		foreach($order['calc_rules'] as $rule){
			if ($rule->calc_kind == 'DBTaxRulesBill') { ?>
			<tr>
				<td colspan="<?php echo $colspan; ?>" align="right"><?php echo $rule->calc_rule_name ?> </td>

                <?php 
                if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
					<td align="right"> </td>
                <?php 
                }
				if($is_show_product_discount){
				?>
				<td align="right"> <?php echo  $currency->priceDisplay($rule->calc_amount);  ?></td>
				 <?php 
            	}
            	if($is_show_product_total){ ?>
				<td align="right"><?php echo  $currency->priceDisplay($rule->calc_amount);  ?> </td>
				<?php 
            	}
            	?>
			</tr>
			<?php
			} elseif ($rule->calc_kind == 'taxRulesBill') { ?>
			<tr>
				<td colspan="<?php echo $colspan; ?>" align="right"><?php echo $rule->calc_rule_name ?> </td>
				<?php 
				if ( $is_show_product_tax) { ?>
					<td align="right"><?php echo $currency->priceDisplay($rule->calc_amount); ?> </td>
				<?php 
				}
				if($is_show_product_discount){
				?>
				<td align="right"><?php    ?> </td>
				<?php 
            	}
            	if($is_show_product_total){ ?>
				<td align="right"><?php echo $currency->priceDisplay($rule->calc_amount);   ?> </td>
				<?php 
            	}
            	?>
			</tr>
			<?php
			 } elseif ($rule->calc_kind == 'DATaxRulesBill') { ?>
			<tr>
				<td colspan="<?php echo $colspan; ?>" align="right"><?php echo $rule->calc_rule_name ?> </td>
				<?php 
				if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
					<td align="right"><?php echo   $currency->priceDisplay($rule->calc_amount); ?> </td>
				<?php 
				}
				if($is_show_product_discount){
				?>
					<td align="right"><?php    ?> </td>
				<?php 
				}
            	if($is_show_product_total){ ?>
					<td align="right"><?php echo $currency->priceDisplay($rule->calc_amount);  ?> </td>
				<?php 
            	}
            	?>
			</tr>

			<?php
			 }
		}
	}
	if($is_show_shipping){
	?>

    <tr>
		<td align="right" colspan="<?php echo $colspan; ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING').' ('. $shipment_name .')'; ?></td>
		<?php 
		if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
	    	<td align="right"><?php echo "<span >" . $currency->priceDisplay($order_details->order_shipment_tax) . "</span>" ?></td>
		<?php 
		}
		if($is_show_product_discount){
		?>
		<td align="right"> </td>
		<?php 
		}
            	if($is_show_product_total){ ?>
		<td align="right"><?php echo $currency->priceDisplay($order_details->order_shipment + $order_details->order_shipment_tax); ?></td>
		<?php 
		}
        ?>
    </tr>
<?php
	}
	if($is_show_payment_fee){
?>
    <tr>
		<td align="right" colspan="<?php echo $colspan; ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT').' ('. $payment_name .')'; ?></td>
		<?php 
		if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
	    	<td align="right"><?php echo "<span >" . $currency->priceDisplay($order_details->order_payment_tax) . "</span>" ?></td>
		<?php 
		}
        if($is_show_product_discount){
		?>
		<td align="right"> </td>
		<?php 
		}
        if($is_show_product_total){ ?>
		<td align="right"><?php echo $currency->priceDisplay($order_details->order_payment + $order_details->order_payment_tax); ?></td>
		<?php 
		}
        ?>
    </tr>
    <?php 
    }
	if($is_show_totals){
    ?>
    <tr>
		<td align="right" colspan="<?php echo $colspan; ?>"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></strong></td>
	
		<?php 
		if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
	    	<td align="right"><span ><?php echo $currency->priceDisplay($order_details->order_billTaxAmount); ?></span></td>
		<?php 
		}
        if($is_show_product_discount){
		?>
		<td align="right"><?php   echo $currency->priceDisplay($order_details->order_billDiscountAmount); ?> </td>
		<?php 
		}
        if($is_show_product_total){ ?>
		<td align="right"><strong><?php echo $currency->priceDisplay($order_details->order_total); ?></strong></td>
		<?php 
		}
        ?>
    </tr>
<?php 
	}
?>
</table>
<?php 
}
?>