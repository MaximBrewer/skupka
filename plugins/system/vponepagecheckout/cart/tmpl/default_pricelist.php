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
 * $Id: default_pricelist.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$total_colspan       = VmConfig::get('show_tax') ? 7 : 6; 
$total_colspan       = $this->params->get('hide_discount', 0) ? ($total_colspan - 1) : $total_colspan;
$subheading_colspan  = $this->params->get('hide_sku', 0) ? 3 : 4;
$taxTitle            = JText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT');
$document            = JFactory::getDocument();

// Get dynamic Tax Column Heading
if($this->params->get('tax_column_name', 'standard') == 'dynamic')
{
	if (VmConfig::get ('show_tax') && !empty($this->cart->cartData['VatTax']))
	{
		reset($this->cart->cartData['VatTax']);
		$taxd = current($this->cart->cartData['VatTax']);
		$taxTitle = $taxd['calc_name'] . ' ' . rtrim(trim($taxd['calc_value'], '0'), '.') . '%';
	}
}

$css = '';
// If resposive then set the dynamic css for the price list table layout
if($this->params->get('responsive', 1))
{
	if(VmConfig::get('show_tax'))
	{ 
		$css .= $this->helper->cleanCSS("
			@media (max-width: 767px) {
				.cart-p-list td:nth-of-type(1):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_NAME') . "';
				}
				.cart-p-list td:nth-of-type(2):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_SKU') . "';
				}
				.cart-p-list td:nth-of-type(3):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_PRICE') . "';
				}
				.cart-p-list td:nth-of-type(4):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_QUANTITY') . "';
				}
				.cart-p-list td:nth-of-type(5):before {
					content: '" . $taxTitle . "';
				}
				.cart-p-list td:nth-of-type(6):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . "';
				}
				.cart-p-list td:nth-of-type(7):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_TOTAL') . "';
				}
				.cart-sub-total td:nth-of-type(1):before,
				.cart-coupon-row td:nth-of-type(1):before,
				.discount-rule-per-bill td:nth-of-type(1):before,
				.tax-rule-per-bill td:nth-of-type(1):before,
				.shipping-row td:nth-of-type(1):before,
				.payment-row td:nth-of-type(1):before,
				.grand-total td:nth-of-type(1):before,
				.grand-total-p-currency td:nth-of-type(1):before {
					content: '';
				}
				.cart-sub-total td:nth-of-type(2):before,
				.cart-coupon-row td:nth-of-type(2):before,
				.discount-rule-per-bill td:nth-of-type(2):before,
				.tax-rule-per-bill td:nth-of-type(2):before,
				.shipping-row td:nth-of-type(2):before,
				.payment-row td:nth-of-type(2):before,
				.grand-total td:nth-of-type(2):before,
				.grand-total-p-currency td:nth-of-type(2):before {
					content: '" . $taxTitle . "';
				}
				.cart-sub-total td:nth-of-type(3):before,
				.cart-coupon-row td:nth-of-type(3):before,
				.discount-rule-per-bill td:nth-of-type(3):before,
				.tax-rule-per-bill td:nth-of-type(3):before,
				.shipping-row td:nth-of-type(3):before,
				.payment-row td:nth-of-type(3):before,
				.grand-total td:nth-of-type(3):before,
				.grand-total-p-currency td:nth-of-type(3):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . "';
				}
				.cart-sub-total td:nth-of-type(4):before,
				.cart-coupon-row td:nth-of-type(4):before,
				.discount-rule-per-bill td:nth-of-type(4):before,
				.tax-rule-per-bill td:nth-of-type(4):before,
				.shipping-row td:nth-of-type(4):before,
				.payment-row td:nth-of-type(4):before,
				.grand-total td:nth-of-type(4):before,
				.grand-total-p-currency td:nth-of-type(4):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_TOTAL') . "';
				}
			}
		");
	} 
	else 
	{
		$css .= $this->helper->cleanCSS("
			@media (max-width: 767px) {
				.cart-p-list td:nth-of-type(1):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_NAME') . "';
				}
				.cart-p-list td:nth-of-type(2):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_SKU') . "';
				}
				.cart-p-list td:nth-of-type(3):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_PRICE') . "';
				}
				.cart-p-list td:nth-of-type(4):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_QUANTITY') . "';
				}
				.cart-p-list td:nth-of-type(5):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . "';
				}
				.cart-p-list td:nth-of-type(6):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_TOTAL') . "';
				}
				.cart-sub-total td:nth-of-type(1):before,
				.cart-coupon-row td:nth-of-type(1):before,
				.discount-rule-per-bill td:nth-of-type(1):before,
				.tax-rule-per-bill td:nth-of-type(1):before,
				.shipping-row td:nth-of-type(1):before,
				.payment-row td:nth-of-type(1):before,
				.grand-total td:nth-of-type(1):before,
				.grand-total-p-currency td:nth-of-type(1):before {
					content: '';
				}
				.cart-sub-total td:nth-of-type(2):before,
				.cart-coupon-row td:nth-of-type(2):before,
				.discount-rule-per-bill td:nth-of-type(2):before,
				.tax-rule-per-bill td:nth-of-type(2):before,
				.shipping-row td:nth-of-type(2):before,
				.payment-row td:nth-of-type(2):before,
				.grand-total td:nth-of-type(2):before,
				.grand-total-p-currency td:nth-of-type(2):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . "';
				}
				.cart-sub-total td:nth-of-type(3):before,
				.cart-coupon-row td:nth-of-type(3):before,
				.discount-rule-per-bill td:nth-of-type(3):before,
				.tax-rule-per-bill td:nth-of-type(3):before,
				.shipping-row td:nth-of-type(3):before,
				.payment-row td:nth-of-type(3):before,
				.grand-total td:nth-of-type(3):before,
				.grand-total-p-currency td:nth-of-type(3):before {
					content: '" . JText::_('COM_VIRTUEMART_CART_TOTAL') . "';
				}
			}
		");
	}
}
if($this->params->get('hide_discount', 0))
{
	$css .= $this->helper->cleanCSS("
		th.col-discount,
		td.col-discount {
			display: none !important;
		}
	");
}
if($this->params->get('hide_sku', 0))
{
	$css .= $this->helper->cleanCSS("
		th.col-sku,
		td.col-sku {
			display: none !important;
		}
	");
}
if(!empty($css))
{
	$document->addStyleDeclaration($css);
}
?>
<table class="cart-summary proopc-table-striped<?php echo $this->section_class_suffix; ?>" width="100%" border="0">

	<thead>
		<tr>
			<th class="col-name" align="left">
				<span><?php echo JText::_('COM_VIRTUEMART_CART_NAME') ?></span>
			</th>
			<th class="col-sku" align="left">
				<span><?php echo JText::_('COM_VIRTUEMART_CART_SKU') ?></span>
			</th>
			<th class="col-price" align="right">
				<span><?php echo JText::_('COM_VIRTUEMART_CART_PRICE') ?></span>
			</th>
			<th class="col-qty" align="right">
				<span><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY') ?></span>
			</th>
			<?php if(VmConfig::get('show_tax')) : ?>
				<th class="col-tax" align="right">
					<span><?php echo $taxTitle ?></span>
				</th>
			<?php endif; ?>
			<th class="col-discount" align="right">
				<span><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') ?></span>
			</th>
			<th class="col-total" align="right">
				<span><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?></span>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php // Show all products ?>
		<?php foreach($this->cart->products as $pkey => $prow) : ?>
			<tr valign="top" class="cart-p-list">
				<td class="col-name" align="left" >
					<?php if($prow->virtuemart_media_id && !empty($prow->images[0]) && VmConfig::get('oncheckout_show_images')) : ?>
						<div class="cart-product-description with-image clearfix">
							<div class="cart-images">
								<?php echo $prow->images[0]->displayMediaThumb('class="img-responsive"', false); ?>
							</div>
							<?php 
							echo JHTML::link($prow->url, $prow->product_name);
							echo $this->customfieldsModel->CustomsFieldCartDisplay($prow); 
							?>
						</div>
					<?php else : ?>
						<div class="cart-product-description">
							<?php 
							echo JHtml::link($prow->url, $prow->product_name);
							echo $this->customfieldsModel->CustomsFieldCartDisplay($prow);
							?>
						</div>
					<?php endif; ?>
				</td>
				<td class="col-sku" align="left" >
					<span class="product-sku-text"><?php echo $prow->product_sku ?></span>
				</td>
				<td class="col-price nowrap" align="right" >
					<?php if ($prow->prices['discountedPriceWithoutTax']) : ?>
						<span class="PricediscountedPriceWithoutTax"><?php echo $this->currencyDisplay->priceDisplay($prow->prices['discountedPriceWithoutTax']); ?></span>
					<?php else : ?>
						<span class="PricebasePriceVariant"><?php echo $this->currencyDisplay->priceDisplay($prow->prices['basePriceVariant']); ?></span>
					<?php endif; ?>
				</td>
				<td class="col-qty cart-p-qty nowrap" align="right" >
					<?php
					$step = $prow->step_order_level ? (float) $prow->step_order_level : 1;
					$step = ($step == 0) ? 1 : $step;
					$min = $prow->min_order_level ? (float) $prow->min_order_level : 1;
					$min = ($min == 0) ? 1 : $min;
					$max = $prow->max_order_level ? (float) $prow->max_order_level : null;
					$max = ($max == 0) ? null : $max;
					?>
					<div class="proopc-input-append">
						<input type="number" class="input-ultra-mini proopc-qty-input" size="1" maxlength="4" name="quantity[<?php echo $pkey; ?>]" value="<?php echo $prow->quantity ?>" data-quantity="<?php echo $prow->quantity ?>" step="<?php echo (int) $step ?>" min="<?php echo $min ?>"<?php echo $max ? ' max="' . $max . '"' : ''; ?> />
						<button class="proopc-btn <?php echo $this->btn_class_1 ?> proopc-task-updateqty" name="updatecart.<?php echo $pkey ?>" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" disabled><i class="proopc-icon-refresh"></i></button>
					</div>
					<button class="remove_from_cart proopc-btn <?php echo $this->btn_class_1 ?> proopc-task-deleteproduct" name="delete.<?php echo $pkey ?>" title="<?php echo JText::_('COM_VIRTUEMART_CART_DELETE') ?>" data-vpid="<?php echo $prow->cart_item_id  ?>" disabled><i class="proopc-icon-trash"></i></button>
				</td>
				<?php if(VmConfig::get('show_tax')) : ?>
					<td class="col-tax nowrap" align="right">
						<?php if(!empty($prow->prices['taxAmount'])) : ?>
							<?php echo $this->currencyDisplay->createPriceDiv('taxAmount', '', $prow->prices, false, false, $prow->quantity); ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>
				<td class="col-discount nowrap" align="right">
					<?php if(!empty($prow->prices['discountAmount'])) : ?>
						<?php echo $this->currencyDisplay->createPriceDiv('discountAmount', '', $prow->prices, false, false, $prow->quantity); ?>
					<?php endif; ?>
				</td>
				<td class="col-total nowrap" align="right">
					<?php if (VmConfig::get('checkout_show_origprice', 1) && !empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceWithTax'] != $prow->prices['salesPrice']) : ?>
						<span class="line-through"><?php echo $this->currencyDisplay->createPriceDiv('basePriceWithTax', '', $prow->prices, true, false, $prow->quantity); ?></span><br/>
					<?php elseif(VmConfig::get ('checkout_show_origprice', 1) && empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceVariant'] != $prow->prices['salesPrice']) : ?>
						<span class="line-through"><?php echo $this->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, true, false, $prow->quantity); ?></span><br/>
					<?php endif; ?>
					<?php echo $this->currencyDisplay->createPriceDiv('salesPrice', '', $prow->prices, false, false, $prow->quantity); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		
		<?php // Show a blank row ?>
		<tr class="blank-row vpopc-price">
			<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>"></td>
			<?php if(VmConfig::get('show_tax')) : ?>
				<td class="col-tax"></td>
			<?php endif; ?>
			<td class="col-discount"></td>
			<td class="col-total"></td>
		</tr>
		
		<?php // Show Product Total ?>
		<tr class="cart-sub-total vpopc-price">
			<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="right">
				<span><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?>:</span>
			</td>
			<?php if(VmConfig::get('show_tax')) : ?>
				<td class="col-tax nowrap" align="right">
					<?php if(!empty($this->cart->cartPrices['taxAmount'])) : ?>
						<span class="PricetaxAmount"><?php echo $this->currencyDisplay->priceDisplay($this->cart->cartPrices['taxAmount']); ?></span>
					<?php endif; ?>
				</td>
			<?php endif; ?>
			<td class="col-discount nowrap" align="right">
				<?php if(!empty($this->cart->cartPrices['discountAmount'])) : ?>
					<span class="PricediscountAmount"><?php echo $this->currencyDisplay->priceDisplay($this->cart->cartPrices['discountAmount']); ?></span>
				<?php endif; ?>
			</td>
			<td class="col-total nowrap" align="right">
				<?php if(!empty($this->cart->cartPrices['salesPrice'])) : ?>
					<span class="PricesalesPrice"><?php echo $this->currencyDisplay->priceDisplay($this->cart->cartPrices['salesPrice']); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		
		<?php // Do we need to show the rest of the price list table ?>
		<?php if($this->params->get('show_full_pricelist_firststage', 0) || $this->finalStage) : ?>
			
			<?php // Show applied discount coupon if enabled ?>
			<?php if (VmConfig::get('coupons_enable') && !empty($this->cart->cartData['couponCode'])) : ?>
				<tr class="cart-coupon-row">
					<td class="coupon-form-col" colspan="<?php echo $subheading_colspan ?>" align="left">
						<span><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>:</span>
						<div class="coupon-details">
							<?php echo $this->cart->cartData['couponCode']; ?>
							<?php echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')' ): ''; ?>
						</div>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax nowrap" align="right">
							<?php echo $this->currencyDisplay->createPriceDiv('couponTax', '', $this->cart->cartPrices['couponTax'], false); ?>
						</td>
					<?php endif; ?>
					<td class="col-discount"></td>
					<td class="col-total nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('salesPriceCoupon', '', $this->cart->pricesUnformatted['salesPriceCoupon'], false); ?>
					</td>
				</tr>
			<?php endif; ?>
			
			<?php // Show Discount Before Tax Rules per Bill if available ?>
			<?php foreach($this->cart->cartData['DBTaxRulesBill'] as $rule) : ?>
				<tr class="tax-per-bill discount-rule-per-bill vpopc-price">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="right">
						<span><?php echo $rule['calc_name'] ?>:</span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax"></td>
					<?php endif; ?>
					<td class="col-discount nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
					<td class="col-total nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			
			<?php // Show Discount Before Tax Rules per Bill if available ?>
			<?php foreach($this->cart->cartData['taxRulesBill'] as $rule) : ?>
				<tr class="tax-per-bill tax-rule-per-bill vpopc-price">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="right">
						<span><?php echo $rule['calc_name'] ?>:</span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax nowrap" align="right">
							<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
						</td>
					<?php endif; ?>
					<td class="col-discount"></td>
					<td class="col-total nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			
			<?php // Show Discount After Tax Rules per Bill if available ?>
			<?php foreach($this->cart->cartData['DATaxRulesBill'] as $rule) : ?>
				<tr class="tax-per-bill discount-rule-per-bill vpopc-price">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="right">
						<span><?php echo $rule['calc_name'] ?>:</span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax"></td>
					<?php endif; ?>
					<td class="col-discount nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
					<td class="col-total nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
				</tr>
			<?php endforeach; ?>

			<?php // Show a blank row ?>
			<tr class="blank-row">
				<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>"></td>
				<?php if(VmConfig::get('show_tax')) : ?>
					<td class="col-tax"></td>
				<?php endif; ?>
				<td class="col-discount"></td>
				<td class="col-total"></td>
			</tr>

			<?php // Show selected Shipment Method ?>
			<?php if(!$this->params->get('hide_shipment', 0)) : ?>
			<tr class="shipping-row">
				<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>" align="left">
					<?php echo $this->cart->cartData['shipmentName']; ?>
				</td>
				<?php if(VmConfig::get('show_tax')) : ?>
					<td class="col-tax nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('shipmentTax', '', $this->cart->cartPrices['shipmentTax'], false); ?>
					</td>
				<?php endif; ?>
				<td class="col-discount nowrap" align="right">
					<?php if($this->cart->cartPrices['salesPriceShipment'] < 0) : ?>
						<?php echo $this->currencyDisplay->createPriceDiv('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], false); ?>
					<?php endif; ?>
				</td>
				<td class="col-total nowrap" align="right">
					<?php echo $this->currencyDisplay->createPriceDiv('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], false); ?>
				</td>
			</tr>
			<?php endif; ?>
			
			<?php // Show selected Payment Method ?>
			<tr class="payment-row">
				<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>" align="left">
					<?php echo $this->cart->cartData['paymentName']; ?>
				</td>
				<?php if (VmConfig::get('show_tax')) : ?>
					<td class="col-tax nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv ('paymentTax', '', $this->cart->cartPrices['paymentTax'], false) ?>
					</td>
				<?php endif; ?>
				<td class="col-discount nowrap" align="right">
					<?php if($this->cart->cartPrices['salesPricePayment'] < 0) : ?>
						<?php echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], false); ?>
					<?php endif; ?>
				</td>
				<td class="col-total nowrap" align="right">
					<?php echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], false); ?>
				</td>
			</tr>
			
			<?php // Show a blank row ?>
			<tr class="blank-row">
				<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>"></td>
				<?php if(VmConfig::get('show_tax')) : ?>
					<td class="col-tax"></td>
				<?php endif; ?>
				<td class="col-discount"></td>
				<td class="col-total"></td>
			</tr>
			
			<?php // Show cart total ?>
			<tr class="grand-total vpopc-price">
				<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="right">
					<span><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?>:</span>
				</td>
				<?php if(VmConfig::get('show_tax')) : ?>
					<td class="col-tax nowrap" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], false) ?>
					</td>
				<?php endif; ?>
				<td class="col-discount nowrap" align="right">
					<?php echo $this->currencyDisplay->createPriceDiv('billDiscountAmount', '', $this->cart->cartPrices['billDiscountAmount'], false) ?>
				</td>
				<td class="col-total nowrap" align="right">
					<?php echo $this->currencyDisplay->createPriceDiv('billTotal', '', $this->cart->cartPrices['billTotal'], false); ?>
				</td>
			</tr>
			
			<?php // Show cart total in payment currency ?>
			<?php if(!empty($this->totalInPaymentCurrency)) : ?>
				<tr class="grand-total-p-currency vpopc-price">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="right">
						<span><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>:</span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax nowrap" align="right">
							<?php echo $this->currencyDisplay->createPriceDiv('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], false) ?>
						</td>
					<?php endif; ?>
					<td class="col-discount"></td>
					<td class="col-total nowrap" align="right">
						<span class="PricesalesPrice"><?php echo $this->totalInPaymentCurrency; ?></span>
					</td>
				</tr>
			<?php endif; ?>

			<?php
			// Show VAT/Taxes Separately 
			$show = $this->params->get('show_taxes_separately', 0) && VmConfig::get('show_tax') && !empty($this->cart->cartData['VatTax']);
			$css  = '';
			$num  = 1;
			?>
			<?php if($show) : ?>
			
				<?php // Show a blank row ?>
				<tr class="blank-row">
					<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>"></td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax"></td>
					<?php endif; ?>
					<td class="col-discount"></td>
					<td class="col-total"></td>
				</tr>

				<tr class="tax-per-bill separate-tax-heading vpopc-price">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>">
						<span class="proopc-uppercase"><?php echo JText::_ ('COM_VIRTUEMART_TOTAL_INCL_TAX') ?></span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax"></td>
					<?php endif; ?>
					<td class="col-discount"></td>
					<td class="col-total"></td>
				</tr>
				
				<?php foreach($this->cart->cartData['VatTax'] as $vatTax) : ?>
					<?php if(!empty($vatTax['result'])) : ?>
						<?php $rowName = 'vattax-per-bill-' . $num; ?>
						<tr class="tax-per-bill tax-rule-per-bill vpopc-price separate-rule-per-bill <?php echo $rowName ?>">
							<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="right">
								<span><?php echo shopFunctionsF::getTaxNameWithValue($vatTax['calc_name'], $vatTax['calc_value']) ?>:</span>
							</td>
							<td class="col-tax">
								<?php echo $this->currencyDisplay->createPriceDiv('taxAmount', '', $vatTax['result'], false, false, 1.0, false, true) ?>
							</td>
							<td class="col-discount"></td>
							<td class="col-total"></td>
						</tr>
						<?php
						$num++;
						if($this->params->get('responsive', 1))
						{
							$css .= '.tax-rule-per-bill.' . $rowName . ' td:nth-of-type(2):before {content: "'. shopFunctionsF::getTaxNameWithValue($vatTax['calc_name'], $vatTax['calc_value']) . '"}';
						} ?>
					<?php endif; ?>
				<?php endforeach; ?>
				
				<?php if(!empty($css))
				{
					$css = '@media (max-width: 767px) {' . $css . '}';
					$css = $this->helper->cleanCSS($css);
					$document->addStyleDeclaration($css);
				} ?>
				
			<?php endif; ?>

		<?php endif; ?>
		
		<?php // Show Checkout advertisements generated by coupon plugin, payment plugin and shipment plugin ?>
		<?php $style = (int) $this->params->get('style', 1); ?>
		<?php if(!empty($this->checkoutAdvertise) && (!$this->finalStage || in_array($style, array(3, 4)))) : ?>
			<?php foreach($this->checkoutAdvertise as $html) : ?>
				<tr class="checkout-advertise-row payment-advertise">
					<td class="col-advertisement" colspan="<?php echo $total_colspan ?>">
						<div id="proopc-payment-advertise-table">
							<div class="checkout-advertise">
								<?php echo $html; ?>
							</div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
			
	</tbody>
</table>
<input type="hidden" name="vpopc_pp_express_selected" id="vpopc_pp_express_selected" value="<?php echo $this->paypal_express_selected; ?>" />
<input type="hidden" id="vpopc_selected_paymentmethod_id" value="<?php echo $this->cart->virtuemart_paymentmethod_id; ?>" />
<input type="hidden" id="vpopc_selected_shipmentmethod_id" value="<?php echo $this->cart->virtuemart_shipmentmethod_id; ?>" />