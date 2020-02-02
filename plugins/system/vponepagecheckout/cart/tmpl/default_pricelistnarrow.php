<?php
/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 3 $
 * $LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
 * $Id: default_pricelistnarrow.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<div class="inner-wrap">
	<table class="proopc-cart-summery<?php echo $this->section_class_suffix; ?>" width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class="col-name" align="left"><?php echo JText::_('COM_VIRTUEMART_CART_NAME') ?></th>
				<th class="col-qty" align="center"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY') ?></th>
				<th class="col-total" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?></th>
			</tr>
		</thead>
		<?php
		$i = 1;
		foreach($this->cart->products as $pkey => $prow) : ?>
		<tbody class="proopc-cart-product" data-details="proopc-product-details<?php echo $i ?>">
			<tr valign="top" class="proopc-cart-entry<?php echo $i ?> proopc-p-list" >
				<td class="col-name">
					<?php 
						echo JHTML::link($prow->url, $prow->product_name);
						echo $this->customfieldsModel->CustomsFieldCartDisplay($prow); 
					?>
					<div class="proopc-p-price vpopc-price">
						<span><?php echo trim(JText::_('COM_VIRTUEMART_CART_PRICE')) ?>: </span>
						<?php if ($prow->prices['discountedPriceWithoutTax']) : ?>
							<span class="PricediscountedPriceWithoutTax nowrap"><?php echo $this->currencyDisplay->priceDisplay($prow->prices['discountedPriceWithoutTax']); ?></span>
						<?php else : ?>
							<span class="PricebasePriceVariant nowrap"><?php echo $this->currencyDisplay->priceDisplay($prow->prices['basePriceVariant']); ?></span>
						<?php endif; ?>
					</div>
					<?php if(!$this->params->get('hide_sku', 0)) : ?>
						<div class="proopc-p-sku">
							<?php echo JText::_('COM_VIRTUEMART_CART_SKU') . ': '. $prow->product_sku; ?>
						</div>
					<?php endif; ?>
				</td>
				<td class="col-qty" align="center">
					<?php echo $prow->quantity ?>
				</td>
				<td class="col-total nowrap" colspan="1" align="right">
					<?php if (VmConfig::get('checkout_show_origprice', 1) && !empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceWithTax'] != $prow->prices['salesPrice']) : ?>
						<span class="line-through"><?php echo $this->currencyDisplay->createPriceDiv('basePriceWithTax', '', $prow->prices, true, false, $prow->quantity); ?></span><br/>
					<?php elseif(VmConfig::get ('checkout_show_origprice', 1) && empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceVariant'] != $prow->prices['salesPrice']) : ?>
						<span class="line-through"><?php echo $this->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, true, false, $prow->quantity); ?></span><br/>
					<?php endif; ?>
					<?php echo $this->currencyDisplay->createPriceDiv('salesPrice', '', $prow->prices, false, false, $prow->quantity) ?>
				</td>
			</tr>
			<?php // Start - Mouse Over Details ?>
			<tr id="proopc-product-details<?php echo $i ?>" class="proopc-product-hover soft-hide">
				<td colspan="4">
					<div class="proopc_arrow_box">
					<table class="proopc-p-info-table">
						<tr>
							<?php if($prow->virtuemart_media_id && !empty($prow->images[0]) && VmConfig::get('oncheckout_show_images')) {  ?>
								<td colspan="2">
									<div class="proopc-product-image">
										<div class="p-info-inner">
											<?php echo $prow->images[0]->displayMediaThumb('class="img-reponsive"', false); ?>
										</div>
									</div>
									<div class="proopc-p-info">
										<div class="p-info-inner">
											<div class="proopc-product-name">
												<?php 
												echo JHTML::link($prow->url, $prow->product_name);
												echo $this->customfieldsModel->CustomsFieldCartDisplay($prow); 
												?>
											</div>
										</div>
									</div>
								</td>
							<?php } else { ?>
								<td colspan="2">
									<div class="proopc-p-info noimage">
										<div class="p-info-inner">
											<div class="proopc-product-name"><?php echo JHTML::link($prow->url, $prow->product_name) . $this->customfieldsModel->CustomsFieldCartDisplay($prow); ?></div>
										</div>
									</div>
								</td>
							<?php } ?>
						</tr>
						<tr class="add-padding">
							<td width="35%" class="proopc-qty-title"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY') ?></td>
							<td width="65%">
								<?php
								$step = $prow->step_order_level ? (float) $prow->step_order_level : 1;
								$step = ($step == 0) ? 1 : $step;
								$min = $prow->min_order_level ? (float) $prow->min_order_level : 1;
								$min = ($min == 0) ? 1 : $min;
								$max = $prow->max_order_level ? (float) $prow->max_order_level : null;
								$max = ($max == 0) ? null : $max;
								?>
								<div class="proopc-qty-update">
									<div class="proopc-input-append">
										<input type="number" class="input-ultra-mini proopc-qty-input" size="1" maxlength="4" name="quantity[<?php echo $pkey; ?>]" value="<?php echo $prow->quantity ?>" data-quantity="<?php echo $prow->quantity ?>" step="<?php echo (int) $step ?>" min="<?php echo $min ?>"<?php echo $max ? ' max="' . $max . '"' : ''; ?> />
										<button class="proopc-btn <?php echo $this->btn_class_1 ?> proopc-task-updateqty" name="updatecart.<?php echo $pkey ?>" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" disabled><i class="proopc-icon-refresh"></i></button>
									</div>
								</div>
								<span class="proopc-delete-product">
									<button class="remove_from_cart proopc-btn <?php echo $this->btn_class_1 ?> proopc-task-deleteproduct" name="delete.<?php echo $pkey ?>" title="<?php echo JText::_('COM_VIRTUEMART_CART_DELETE') ?>" data-vpid="<?php echo $prow->cart_item_id  ?>" disabled><i class="proopc-icon-trash"></i></button>
								</span>
							</td>
						</tr>
						<tr class="add-padding add-padding-top vpopc-price">
							<td width="35%"><?php echo JText::_('COM_VIRTUEMART_CART_PRICE') ?></td>
							<td class="col-price nowrap" width="65%" align="right">
								<?php if ($prow->prices['discountedPriceWithoutTax']) : ?>
									<span class="PricediscountedPriceWithoutTax"><?php echo $this->currencyDisplay->priceDisplay($prow->prices['discountedPriceWithoutTax']); ?></span>
								<?php else : ?>
									<span class="PricebasePriceVariant"><?php echo $this->currencyDisplay->priceDisplay($prow->prices['basePriceVariant']); ?></span>
								<?php endif; ?>
							</td>
						</tr>
						<?php if(VmConfig::get('show_tax') && !empty($prow->prices['taxAmount'])) : ?>
							<tr class="add-padding vpopc-price">
								<td width="35%"><?php  echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') ?></td>
								<td class="col-price nowrap" width="65%" align="right">
									<?php echo $this->currencyDisplay->createPriceDiv ('taxAmount', '', $prow->prices, false, false, $prow->quantity) ?>
								</td>
							</tr>
						<?php endif; ?>
						<?php if(!empty($prow->prices['discountAmount'])) : ?>
							<tr class="add-padding vpopc-price">
								<td width="35%"><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') ?></td>
								<td class="col-price nowrap" width="65%" align="right">
									<?php echo $this->currencyDisplay->createPriceDiv('discountAmount', '', $prow->prices, false, false, $prow->quantity) ?>
								</td>
							</tr>
						<?php endif; ?>
						<tr class="add-padding add-padding-bottom vpopc-price">
							<td width="35%"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?></td>
							<td class="col-total-price nowrap" width="65%" align="right">
								<?php echo $this->currencyDisplay->createPriceDiv('salesPrice', '', $prow->prices, false, false, $prow->quantity) ?>
							</td>
						</tr>
					</table>
					</div>
				</td>
			</tr>
		</tbody>
		<?php 
		$i++;
		endforeach; ?>
		
		<tbody class="proopc-hint">
			<tr class="proopc-hint-row">
				<td colspan="3" align="left">
					<?php echo JText::_('PLG_VPONEPAGECHECKOUT_PRODUCT_HOVER_HINT'); ?>
				</td>
			</tr>
		</tbody>
		
		<tbody class="proopc-subtotal vpopc-price">
			<tr class="proopc-cart-sub-total">
				<td class="sub-headings" colspan="2" align="left">
					<?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?>
				</td>
				<td class="col-total" align="right">
					<?php echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted,false); ?>
				</td>
			</tr>
		</tbody>
		
		<?php if (VmConfig::get('coupons_enable') && !empty($this->cart->cartData['couponCode'])) : ?>
			<tbody class="proopc-coupon-details vpopc-price">
				<tr class="cart-coupon-row">
					<td class="coupon-form-col" colspan="2" align="left">
						<?php 
						echo '<span>';
						echo $this->cart->cartData['couponCode'] ;
						echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')' ): '';
						echo '</span>';
						if (VmConfig::get('show_tax') && !empty($this->cart->cartPrices['couponTax']))
						{
							echo '<div class="coupon-tax vpopc-price">';
							echo $this->currencyDisplay->createPriceDiv('couponTax', 'COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT', $this->cart->cartPrices['couponTax'], false);
							echo '</div>';
						}
						?>
					</td>
					<td class="col-total nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv ('salesPriceCoupon', '', $this->cart->cartPrices['salesPriceCoupon'], false); ?>
					</td>
				</tr>
			</tbody>
		<?php endif; ?>

		<?php if(count($this->cart->cartData['DBTaxRulesBill']) || count($this->cart->cartData['taxRulesBill']) || count($this->cart->cartData['DATaxRulesBill'])) : ?>
			<tbody class="proopc-bill-taxrules vpopc-price">
				<?php foreach($this->cart->cartData['DBTaxRulesBill'] as $rule) : ?>
					<tr class="tax-per-bill dbtax-row">
						<td class="sub-headings" colspan="2">
							<div class="dbtax-cal-name"><?php echo $rule['calc_name'] ?></div>
						</td>
						<td class="col-total nowrap vpopc-price" align="right">
							<?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?> 
						</td>
					</tr>
				<?php endforeach; ?>
			
				<?php foreach($this->cart->cartData['taxRulesBill'] as $rule) : ?>
					<tr class="tax-per-bill tax-row vpopc-price">
						<td class="sub-headings" colspan="2">
							<div class="tax-cal-name"><?php echo $rule['calc_name'] ?></div>
						</td>
						<td class="col-total nowrap vpopc-price" align="right">
							<?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			
			<?php foreach($this->cart->cartData['DATaxRulesBill'] as $rule) : ?>
				<tr class="tax-per-bill datax-row vpopc-price">
					<td class="sub-headings" colspan="2">
						<div class="datax-cal-name"><?php echo $rule['calc_name'] ?></div>
					</td>
					<td class="col-total nowrap vpopc-price" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		<?php endif; ?>
			
		<?php if(!$this->params->get('hide_shipment', 0)) : ?>
			<tbody class="poopc-shipment-table">
				<tr>
					<td class="shipping-heading" colspan="2" align="left">
						<?php echo $this->cart->cartData['shipmentName']; ?>
						<?php if(VmConfig::get('show_tax') && !empty($this->cart->cartPrices['shipmentTax'])) : ?>
							<div class="proopc-taxcomponent">
								<?php echo $this->currencyDisplay->createPriceDiv ('shipmentTax', 'COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT', $this->cart->cartPrices['shipmentTax'], false) ?>
							</div>
						<?php endif; ?>
					</td>
					<td class="col-total vpopc-price" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], false); ?>
					</td>
				</tr>
			</tbody>
		<?php endif; ?>
			
			<tbody class="poopc-payment-table">
				<tr>
					<td class="payment-heading" colspan="2" align="left">
						<?php echo $this->cart->cartData['paymentName']; ?>
						<?php if(VmConfig::get('show_tax') && !empty($this->cart->cartPrices['paymentTax'])) : ?>
							<div class="proopc-taxcomponent">
								<?php echo $this->currencyDisplay->createPriceDiv ('paymentTax', 'COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT', $this->cart->cartPrices['paymentTax'], false) ?>
							</div>
						<?php endif; ?>
					</td>
					<td class="col-total nowrap vpopc-price" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], false); ?>
					</td>
				</tr>
			</tbody>
			
			<tbody class="proopc-grand-total vpopc-price">
				<?php if (VmConfig::get('show_tax') && !empty($this->cart->cartPrices['billTaxAmount'])) : ?>
					<tr class="grand-total">
						<td class="sub-headings" colspan="2" align="left">
							<?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') ?>
						</td>
						<td class="col-total nowrap" align="right">
							<?php echo $this->currencyDisplay->createPriceDiv ('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], false) ?>
						</td>
					</tr>
				<?php endif; ?>
				<?php if(!empty($this->cart->cartPrices['billDiscountAmount'])) : ?>
					<tr class="grand-total">
						<td class="sub-headings" colspan="2" align="left">
							<?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') ?>
						</td>
						<td class="col-total nowrap" align="right">
							<?php echo $this->currencyDisplay->createPriceDiv ('billDiscountAmount', '', $this->cart->cartPrices['billDiscountAmount'], false) ?>
						</td>
					</tr>
				<?php endif; ?>
				<tr class="grand-total">
					<td class="sub-headings" colspan="2" align="left">
						<?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?>
					</td>
					<td class="col-total nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('billTotal','', $this->cart->pricesUnformatted['billTotal'],false); ?>
					</td>
				</tr>
			</tbody>

			<?php // Show VAT/Taxes Separately ?>
			<?php if($this->params->get('show_taxes_separately', 0) && VmConfig::get('show_tax') && !empty($this->cart->cartData['VatTax'])) : ?>
				<tbody class="proopc-bill-taxrules proopc-separate-tax vpopc-price">
					<tr class="tax-per-bill separate-tax-heading-n">
						<td class="sub-headings" colspan="3">
							<?php echo JText::_ ('COM_VIRTUEMART_TOTAL_INCL_TAX') ?>
						</td>
					</tr>
					<?php foreach($this->cart->cartData['VatTax'] as $vatTax) : ?>
						<?php if(!empty($vatTax['result'])) : ?>
							<tr class="tax-per-bill datax-row">
								<td class="sub-headings" colspan="2">
									<div class="datax-cal-name"><?php echo shopFunctionsF::getTaxNameWithValue($vatTax['calc_name'], $vatTax['calc_value']) ?></div>
								</td>
								<td class="col-total nowrap" align="right">
									<?php echo $this->currencyDisplay->createPriceDiv('taxAmount', '', $vatTax['result'], false, false, 1.0, false, true) ?>
								</td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				</tbody>
			<?php endif; ?>


			<?php if ($this->totalInPaymentCurrency) : ?>
				<tbody class="proopc-grand-total-p-currency vpopc-price">
					<tr class="grand-total-p-currency">
						<td class="sub-headings" colspan="2" align="left">
							<?php echo JText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>
						</td>
						<td class="col-total nowrap" align="right">
							<span class="PricesalesPrice"><?php echo $this->totalInPaymentCurrency; ?></span>
						</td>
					</tr>
				</tbody>
			<?php endif; ?>
	</table>
	<input type="hidden" name="vpopc_pp_express_selected" id="vpopc_pp_express_selected" value="<?php echo $this->paypal_express_selected; ?>" />
	<input type="hidden" id="vpopc_selected_paymentmethod_id" value="<?php echo $this->cart->virtuemart_paymentmethod_id; ?>" />
	<input type="hidden" id="vpopc_selected_shipmentmethod_id" value="<?php echo $this->cart->virtuemart_shipmentmethod_id; ?>" />
</div>