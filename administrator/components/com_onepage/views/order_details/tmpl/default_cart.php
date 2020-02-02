<?php
/*
*
* @copyright Copyright (C) 2007 - 2014 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
?>

		<form action="index.php" method="post" name="orderItemForm" id="orderItemForm"><!-- Update linestatus form -->
		<table class="table" cellspacing="0" cellpadding="0" id="itemTable" >
			<thead>
				<tr>
					<!--<th class="title" width="5%" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_ACTIONS') ?></th> -->
					<th class="title" width="3" align="left">#</th>
					<th class="title" width="47" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_QUANTITY') ?></th>
					<th class="title" width="*" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_NAME') ?></th>
					<th class="title" width="10%" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SKU') ?></th>
					<th class="title" width="10%"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_ITEM_STATUS') ?></th>
					<th class="title" width="50"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_NET') ?></th>
					<th class="title" width="50"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_BASEWITHTAX') ?></th>
					<th class="title" width="50"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_GROSS') ?></th>
					<th class="title" width="50"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_TAX') ?></th>
					<th class="title" width="50"> <?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_DISCOUNT') ?></th>
					<th class="title" width="5%"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></th>
				</tr>
			</thead>
		<?php $i=1;
		foreach ($this->orderdetails['items'] as $item) { ?>
			<!-- Display the order item -->
			<tr valign="top" ><?php /*id="showItem_<?php echo $item->virtuemart_order_item_id; ?>" data-itemid="<?php echo $item->virtuemart_order_item_id; ?>">*/ ?>
				<!--<td>
					<?php $removeLineLink=JRoute::_('index.php?option=com_virtuemart&view=orders&orderId='.$this->orderbt->virtuemart_order_id.'&orderLineId='.$item->virtuemart_order_item_id.'&task=removeOrderItem'); ?>
					<a class="vmicon vmicon-16-bug" title="<?php echo JText::_('remove'); ?>" onclick="javascript:confirmation('<?php echo $removeLineLink; ?>');"></a>

					<a href="javascript:enableItemEdit(<?php echo $item->virtuemart_order_item_id; ?>)"> <?php echo JHTML::_('image',  'administrator/components/com_virtuemart/assets/images/icon_16/icon-16-category.png', "Edit", NULL, "Edit"); ?></a>
				</td> -->
				<td>
					<?php echo ($i++)?>
				</td>
				<td>
					<span class='ordereditI'><?php echo $item->product_quantity; ?></span>
					<input class='orderedit' type="text" size="3" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_quantity]" value="<?php echo $item->product_quantity; ?>"/>
				</td>
				<td>
					<span class='ordereditI'><?php echo $item->order_item_name; ?></span>
					<?php 
					
					require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
					
					$create = true; 
					
					$media = OPCimage::getImageUrl($item->virtuemart_product_id, $create, 40, 40); 

					OPCimage::op_show_image($item->product_full_image, '', 40, 40, 'product'); ?>
					<input class='orderedit' type="text"  name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][order_item_name]" value="<?php echo $item->order_item_name; ?>"/><?php
						//echo $item->order_item_name;
						if (!empty($item->product_attribute)) {
								if(!class_exists('VirtueMartModelCustomfields'))require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'customfields.php');
								$product_attribute = VirtueMartModelCustomfields::CustomsFieldOrderDisplay($item,'BE');
							echo '<div>'.$product_attribute.'</div>';
						}
						$_dispatcher = JDispatcher::getInstance();
						$_returnValues = $_dispatcher->trigger('plgVmOnShowOrderLineBEShipment',array(  $this->orderID,$item->virtuemart_order_item_id));
						$_plg = '';
						foreach ($_returnValues as $_returnValue) {
							if ($_returnValue !== null) {
								$_plg .= $_returnValue;
							}
						}
						if ($_plg !== '') {
							echo '<table border="0" celspacing="0" celpadding="0">'
								. '<tr>'
								. '<td width="8px"></td>' // Indent
								. '<td>'.$_plg.'</td>'
								. '</tr>'
								. '</table>';
						}
					?>
					<?php if(empty($item->virtuemart_product_id)) { ?>
						<span class='orderedit'>Product ID:</span>
						<input class='orderedit' type="text" size="10" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][virtuemart_product_id]" value="<?php echo $item->virtuemart_product_id; ?>"/>
					<?php } ?>
				</td>
				<td>
					<span class='ordereditI'><?php echo $item->order_item_sku; ?></span>
					<input class='orderedit' type="text"  name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][order_item_sku]" value="<?php echo $item->order_item_sku; ?>"/>
				</td>
				<td align="center">
					<!--<?php echo $this->orderstatuslist[$item->order_status]; ?><br />-->
					<?php echo $this->itemstatusupdatefields[$item->virtuemart_order_item_id]; ?>

				</td>
				<td align="right" style="padding-right: 5px;">
					<?php
					$item->product_discountedPriceWithoutTax = (float) $item->product_discountedPriceWithoutTax;
					if (!empty($item->product_priceWithoutTax) && $item->product_discountedPriceWithoutTax != $item->product_priceWithoutTax) {
						echo '<span style="text-decoration:line-through">'.$this->currency->priceDisplay($item->product_item_price) .'</span><br />';
						echo '<span >'.$this->currency->priceDisplay($item->product_discountedPriceWithoutTax) .'</span><br />';
					} else {
						echo '<span >'.$this->currency->priceDisplay($item->product_item_price) .'</span><br />'; 
					}
					?>
					<input class='orderedit' type="text" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_item_price]" value="<?php echo $item->product_item_price; ?>"/>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay($item->product_basePriceWithTax); ?>
					<input class='orderedit' type="text" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_basePriceWithTax]" value="<?php echo $item->product_basePriceWithTax; ?>"/>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay($item->product_final_price); ?>
					<input class='orderedit' type="text" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_final_price]" value="<?php echo $item->product_final_price; ?>"/>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay( $item->product_tax); ?>
					<input class='orderedit' type="text" size="12" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_tax]" value="<?php echo $item->product_tax; ?>"/>
					<span style="display: block; font-size: 80%;" title="<?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE_DESC'); ?>">
						<input class='orderedit' type="checkbox" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][calculate_product_tax]" value="1" /> <label class='orderedit' for="calculate_product_tax"><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE'); ?></label>
					</span>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay( $item->product_subtotal_discount); ?>
					<input class='orderedit' type="text" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_subtotal_discount]" value="<?php echo $item->product_subtotal_discount; ?>"/>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php 
					$item->product_basePriceWithTax = (float) $item->product_basePriceWithTax;
					if(!empty($item->product_basePriceWithTax) && $item->product_basePriceWithTax != $item->product_final_price ) {
						echo '<span style="text-decoration:line-through" >'.$this->currency->priceDisplay($item->product_basePriceWithTax,$this->currency,$item->product_quantity) .'</span><br />' ;
					}
					elseif (empty($item->product_basePriceWithTax) && $item->product_item_price != $item->product_final_price) {
						echo '<span style="text-decoration:line-through">' . $this->currency->priceDisplay($item->product_item_price,$this->currency,$item->product_quantity) . '</span><br />';
					}
					echo $this->currency->priceDisplay($item->product_subtotal_with_tax);
					?>
					<input class='orderedit' type="hidden" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_subtotal_with_tax]" value="<?php echo $item->product_subtotal_with_tax; ?>"/>
				</td>
			</tr>

		<?php } ?>
			<tr id="updateOrderItemStatus">

					<td colspan="5">
						<!--
						&nbsp;<a class="newOrderItem" href="#"><span class="icon-nofloat vmicon vmicon-16-new"></span><?php echo JText::_('COM_VIRTUEMART_NEW_ITEM'); ?></a>
						&nbsp;&nbsp;
						-->
						<a class="updateOrderItemStatus" href="#"><span class="icon-nofloat vmicon vmicon-16-save"></span><?php echo JText::_('COM_VIRTUEMART_SAVE'); ?></a>
						&nbsp;&nbsp;
						<a href="#" onClick="javascript:cancelEdit(event);" ><span class="icon-nofloat vmicon vmicon-16-remove"></span><?php echo '&nbsp;'. JText::_('COM_VIRTUEMART_CANCEL'); ?></a>
						&nbsp;&nbsp;
						<a href="#" onClick="javascript:enableEdit(event);"><span class="icon-nofloat vmicon vmicon-16-edit"></span><?php echo '&nbsp;'. JText::_('COM_VIRTUEMART_EDIT'); ?></a>
						&nbsp;&nbsp;
						<a href="#" onClick="javascript:addNewLine(event,<?php echo $this->orderdetails['items'][0]->virtuemart_order_item_id ?>);"><span class="icon-nofloat vmicon vmicon-16-new"></span><?php echo '&nbsp;'. JText::_('JTOOLBAR_NEW'); ?></a>
					</td>

					<td colspan="6">
						<?php // echo JHTML::_('image',  'administrator/components/com_virtuemart/assets/images/vm_witharrow.png', 'With selected'); $this->orderStatSelect; ?>
						&nbsp;&nbsp;&nbsp;

					</td>
			</tr>
		<!--/table -->
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		<input type="hidden" name="virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>" />
		<input type="hidden" name="virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>" />
		<input type="hidden" name="order_total" value="<?php echo $this->orderbt->order_total; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form> <!-- Update linestatus form -->
		<!--table class="adminlist" cellspacing="0" cellpadding="0" -->
			<tr>
				<td align="left" colspan="1"><?php $editLineLink=JRoute::_('index.php?option=com_virtuemart&view=orders&orderId='.$this->orderbt->virtuemart_order_id.'&orderLineId=0&tmpl=component&task=editOrderItem'); ?>
				<!-- <a href="<?php echo $editLineLink; ?>" class="modal"> <?php echo JHTML::_('image',  'administrator/components/com_virtuemart/assets/images/icon_16/icon-16-editadd.png', "New Item"); ?>
				New Item </a>--></td>
				<td align="right" colspan="4">
				<div align="right"><strong> <?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SUBTOTAL') ?>:
				</strong></div>
				</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_subtotal); ?></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_tax); ?></td>
				<td align="right"> <?php echo $this->currency->priceDisplay($this->orderbt->order_discountAmount); ?></td>
				<td width="15%" align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_salesPrice); ?></td>
			</tr>
			<?php
			/* COUPON DISCOUNT */
			//if (VmConfig::get('coupons_enable') == '1') {

				if ($this->orderbt->coupon_discount > 0 || $this->orderbt->coupon_discount < 0) {
					?>
			<tr>
				<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?></strong></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><?php
				echo $this->currency->priceDisplay($this->orderbt->coupon_discount);  ?></td>
			</tr>
			<?php
				//}
			}?>



	<?php
		foreach($this->orderdetails['calc_rules'] as $rule){
			if ($rule->calc_kind == 'DBTaxRulesBill') { ?>
			<tr >
				<td colspan="5"  align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>

				<td align="right">
				<!--
					<?php echo  $this->currency->priceDisplay($rule->calc_amount);?>
					<input class='orderedit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>][calc_tax]" value="<?php echo $rule->calc_amount; ?>"/>
				-->
				</td>
				<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
				<td align="right"  style="padding-right: 5px;">
					<?php echo  $this->currency->priceDisplay($rule->calc_amount);?>
					<input class='orderedit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
				</td>
			</tr>
			<?php
			} elseif ($rule->calc_kind == 'taxRulesBill') { ?>
			<tr >
				<td colspan="5"  align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>
				<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
				<td align="right"> </td>
				<td align="right"  style="padding-right: 5px;">
					<?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?>
					<input class='orderedit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
				</td>
			</tr>
			<?php
			 } elseif ($rule->calc_kind == 'DATaxRulesBill') { ?>
			<tr >
				<td colspan="5"   align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>

				<td align="right"> </td>
				<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
				<td align="right"  style="padding-right: 5px;">
					<?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?>
					<input class='orderedit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
				</td>
			</tr>

			<?php
			 }

		}
		?>



			<tr>
				<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?>:</strong></td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment); ?>
					<input class='orderedit' type="text" size="8" name="order_shipment" value="<?php echo $this->orderbt->order_shipment; ?>"/>
				</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment_tax); ?>
					<input class='orderedit' type="text" size="12" name="order_shipment_tax" value="<?php echo $this->orderbt->order_shipment_tax; ?>"/>
				</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment+$this->orderbt->order_shipment_tax); ?></td>

			</tr>
			 <tr>
				<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT') ?>:</strong></td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment); ?>
					<input class='orderedit' type="text" size="8" name="order_payment" value="<?php echo $this->orderbt->order_payment; ?>"/>
				</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment_tax); ?>
					<input class='orderedit' type="text" size="12" name="order_payment_tax" value="<?php echo $this->orderbt->order_payment_tax; ?>"/>
				</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment+$this->orderbt->order_payment_tax); ?></td>

			 </tr>


			<tr>
				<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>:</strong></td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay($this->orderbt->order_billTaxAmount); ?>
					<input class='orderedit' type="text" size="12" name="order_billTaxAmount" value="<?php echo $this->orderbt->order_billTaxAmount; ?>"/>
					<span style="display: block; font-size: 80%;" title="<?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE_DESC'); ?>">
						<input class='orderedit' type="checkbox" name="calculate_billTaxAmount" value="1" checked /> <label class='orderedit' for="calculate_billTaxAmount"><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE'); ?></label>
					</span>
				</td>
				<td align="right" style="padding-right: 5px;"><strong><?php echo $this->currency->priceDisplay($this->orderbt->order_billDiscountAmount); ?></strong></td>
				<td align="right" style="padding-right: 5px;"><strong><?php echo $this->currency->priceDisplay($this->orderbt->order_total); ?></strong>
				</td>
			</tr>
			<?php if ($this->orderbt->user_currency_rate != 1.0) { ?>
			<tr>
				<td align="right" colspan="5"><em><?php echo JText::_('COM_VIRTUEMART_ORDER_USER_CURRENCY_RATE') ?>:</em></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><em><?php echo  $this->orderbt->user_currency_rate ?></em></td>
			</tr>
			<?php }
			?>
		</table>
		