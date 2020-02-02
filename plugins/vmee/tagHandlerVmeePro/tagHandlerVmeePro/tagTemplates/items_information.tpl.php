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
    
    <?php
    foreach ($order['items'] as $item) {
    	$qtt = $item->product_quantity ;
		$_link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $item->virtuemart_product_id;
	?>
        <tr style="display: inline" valign="top">
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
        
    	?>
	    	
    	<?php 
        
        if($is_show_product_name){
    	?>
    	<td align="left" style="display: inline">
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
       
			?>
	    	
	    <?php 
		
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
    	
		?>
			
		<?php 
		
		?>
        </tr>
        <tr>
          <td style="padding-top: 15px" colspan='2'>
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr >
      <td style="border-bottom: 1px solid #8C8C8C"> <area><?php
       		if (!empty($item->product_final_price) && $item->product_final_price != $item->product_priceWithoutTax) {
		    	echo '<span style="text-decoration:line-through;">'.$currency->priceDisplay($item->product_priceWithoutTax) .'</span><br />';
		    	echo '<span >'.$currency->priceDisplay($item->product_final_price) .'</span>';
		    } else {
		    	echo '<span >'.$currency->priceDisplay($item->product_final_price) .'</span>';
		    }
// 			echo $currency->priceDisplay($item->product_final_price); 
//			echo $currency->priceDisplay($item->product_subtotal_discount); 
//			echo $currency->priceDisplay($item->product_subtotal_with_tax, 0);
		?></area  >
   <area>
     X
     </area>
     <area>
     <?php echo $qtt; ?>
     </area>
      <area>
     =
     </area>
     <area>
     <?php 
    	 	$item->product_basePriceWithTax = (float) $item->product_basePriceWithTax;
		    if ( !empty($item->product_basePriceWithTax ) && $item->product_basePriceWithTax != $item->product_final_price ) {
				echo '<span style="text-decoration:line-through;>'.$currency->priceDisplay($item->product_basePriceWithTax, 0, $qtt ) .'<br /></span>' ;
			}
			echo $currency->priceDisplay($item->product_subtotal_with_tax, 0); ?>
     </area >
     </td>
   
      
    
     
    </tr>
  </tbody>
</table>

            </td>
        </tr>
		<?php
	}//close loop
	
	
	?>
	<tr>
		
	</tr>
	<?php
//    if(!$is_show_product_tax){ $colspan++; };
    
    
    ?>
		<tr>
			<td colspan="2" align="right">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    
    <tr>
      <td valign="bottom"> <p style="padding:11.25pt 0cm 0cm 0cm"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></p></td>
       <td align="right" style="padding-top: 15px"> <?php 
            if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
				<?php echo "<span >".$currency->priceDisplay($order_details->order_tax)."</span>" ?>
            <?php 
            }
            if($is_show_product_discount){  ?>
			<?php echo "<span>".$currency->priceDisplay($order_details->order_discountAmount )."</span>" ?>
			<?php 
            }
            if($is_show_product_total){ ?>
			<?php echo $currency->priceDisplay($order_details->order_salesPrice) ?>
			<?php 
            }
            
            ?></td>
    </tr>
    <tr>
      <td valign="bottom"><p style="padding:11.25pt 0cm 0cm 0cm"><?php echo JText::_('Доставка'); ?></p></td>
       <td align="right" style="padding-top: 15px"><?php echo $currency->priceDisplay($order_details->order_shipment + $order_details->order_shipment_tax); ?></td>
    </tr>
    <tr>
    <?php /*?>  <td><?php echo JText::_('Комиссия за оплату').' ('. $payment_name .')'; ?></td>
       <td align="right" style="padding-top: 15px"><?php 
		if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
	    	<?php echo "<span >" . $currency->priceDisplay($order_details->order_payment_tax) . "</span>" ?>
		<?php 
		}
        if($is_show_product_discount){
		?>
		
		<?php 
		}
        if($is_show_product_total){ ?>
		<?php echo $currency->priceDisplay($order_details->order_payment + $order_details->order_payment_tax); ?>
		<?php 
		}
        ?></td><?php */?>
    </tr>
    <tr>
      <td valign="bottom"><strong style="padding:11.25pt 0cm 0cm 0cm"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></strong></td>
       <td align="right" style="padding-top: 15px">
		<?php 
		if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
	    	<span ><?php echo $currency->priceDisplay($order_details->order_billTaxAmount); ?></span>
		<?php 
		}
        if($is_show_product_discount){
		?>
		<?php   echo $currency->priceDisplay($order_details->order_billDiscountAmount); ?> 
		<?php 
		}
        if($is_show_product_total){ ?>
		<strong><?php echo $currency->priceDisplay($order_details->order_total); ?></strong>
		<?php 
		}
        ?></td>
    </tr>
   
  </tbody>
</table>

            
           </td>

           
		</tr>
	<?php 
    
    
    
    if($is_show_coupon_discount){
		if ($order_details->coupon_code) {
			    $coupon_code=$order_details->coupon_code?' ('.$order_details->coupon_code.')':'';
		?>
		        <tr>
			    	<td align="right" colspan="<?php echo $colspan; ?>"><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT')?> <?php echo $coupon_code ?></td> 
			    <?php 
			    if ($is_show_product_tax && VmConfig::get('show_tax')) { ?>
					
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
		
		
	    	
		<?php 
		
		
		?>
	
		<?php 
		
            	 ?>
		<?php /*?><td align="right"><?php echo $currency->priceDisplay($order_details->order_shipment + $order_details->order_shipment_tax); ?></td><?php */?>
		<?php 
		
        ?>
    </tr>
<?php
	}
	
?>
   
    <?php 
    
	
    ?>
  
<?php
	
?>
</table>
<?php 
}
?>