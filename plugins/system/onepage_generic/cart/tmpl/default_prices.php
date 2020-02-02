<?php 
/**
** Parts of this code is written by joomlaprofessionals.com Copyright (c) 2012, 2015 All Right Reserved.
** Many part of this code is from VirtueMart Team Copyright (c) 2004 - 2015. All rights reserved.
** Some parts might even be Joomla and is Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved. 
** http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
** This source is free software. This version may have been modified pursuant
** to the GNU General Public License, and as distributed it includes or
** is derivative of works licensed under the GNU General Public License or
** other free or open source software licenses.
**
** THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
** KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
** IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
** PARTICULAR PURPOSE.

** <author>Joomlaproffs / Virtuemart team</author>
** <email>info@joomlaprofessionals.com</email>
** <date>2015</date>
*/

defined('_JEXEC') or die('Restricted access');
 
$plugin=JPluginHelper::getPlugin('system','onepage_generic');
$params=new JRegistry($plugin->params);
?>

 <div class="price-summary opg-content opg-margin-small-top">
     <div class="spacer">

     <div class="opg-grid opg-text-right" id="couponpricediv">
		  <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2"><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT').': '; ?></div>
            <div class="price-amount price-type opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2" id="coupon_price"><?php echo $this->currencyDisplay->createPriceDiv('salesPriceCoupon','', $this->cart->pricesUnformatted['salesPriceCoupon'],true) ?></div>
            <div class="clear"></div>
        </div>
		<div class="product-subtotal opg-grid opg-text-right" id="coupon_taxfulldiv">
		  <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2">
		    <?php echo JText::_('PLG_SYSTEM_VMUIKIT_ONEPAGE_COUPON_TAX').":"; ?>
		  </div>
          <div class="price-amount price-type opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2" id="coupon_tax">
			<?php echo $this->currencyDisplay->createPriceDiv('couponTax','', @$this->cart->pricesUnformatted['couponTax'],false); ?>
		  </div>
          <div class="clear"></div>
        </div>
        <div class="product-subtotal  opg-grid opg-text-right" id="sales_pricefulldiv">
		  <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL').': '; ?></div>
            <div class="price-amount price-type opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2" id="sales_price"><?php echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted,true) ?></div>
            <div class="clear"></div>
        </div>
		<div class="product-subtotal   opg-grid opg-text-right" id="shipmentfulldiv">
		  <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2">
		          <?php echo JText::_('COM_VIRTUEMART_CART_SHIPPING').":"; ?>
			</div>
		        <div class="price-amount price-type opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2" id="shipment"><?php echo strip_tags($this->currencyDisplay->createPriceDiv('salesPriceShipment','', $this->cart->pricesUnformatted['salesPriceShipment'],false)); ?></div>
            <div class="clear"></div>
        </div>
		
		<div class="product-subtotal   opg-grid opg-text-right" id="paymentfulldiv">
		  <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2">
		          <?php echo JText::_('COM_VIRTUEMART_CART_PAYMENT').":"; ?>
			</div>
		        <div class="price-amount price-type opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2" id="paymentprice"><?php echo strip_tags($this->currencyDisplay->createPriceDiv('salesPricePayment','', $this->cart->pricesUnformatted['salesPricePayment'],false)); ?></div>
            <div class="clear"></div>
        </div>
		
         

			
		<div id="DBTaxRulesBill" style="display:none">
		<?php
		foreach($this->cart->cartData['DBTaxRulesBill'] as $rule){ ?>
            <div id="db_taxdiv_<?php echo $rule['virtuemart_calc_id']; ?>"  class="opg-grid opg-text-right">
                <div class="price-type price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2"><?php echo $rule['calc_name'].': ' ?></div>
                <div id="db_tax_amount_<?php echo $rule['virtuemart_calc_id']; ?>" class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],true); ?></div>
                <div class="clear"></div>
            </div>
			<?php } ?>
		</div>

		<div id="taxRulesBill" style="display:none">
		<?php
		foreach($this->cart->cartData['taxRulesBill'] as $rule){ 
		?>
            <div id="taxdiv_<?php echo $rule['virtuemart_calc_id']; ?>" class=" opg-grid opg-text-right">
                <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2"><?php echo $rule['calc_name'].': ' ?></div>
                <div id="tax_amount_<?php echo $rule['virtuemart_calc_id']; ?>" class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],true); ?></div>
                <div class="clear"></div>
            </div>
			<?php } ?>
		</div>
        <div id="DATaxRulesBill" style="display:none"> 
		<?php
		foreach($this->cart->cartData['DATaxRulesBill'] as $rule){ ?>
            <div id="dataxdiv_<?php echo $rule['virtuemart_calc_id']; ?>" class=" opg-grid opg-text-right">
                <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2"><?php echo $rule['calc_name'].': ' ?></div>
                <div id="datax_amount_<?php echo $rule['virtuemart_calc_id']; ?>" class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],true); ?></div>
                <div class="clear"></div>
            </div>
			<?php } ?>
	    </div>

		<?php if(!empty($this->cart->pricesUnformatted['billDiscountAmount'])) { ?>
        <div class=" opg-grid opg-text-right" id="total_amountfulldiv">
            <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2"><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT').': ' ?></div>
            <div class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2" id="total_amount"><?php echo $this->currencyDisplay->createPriceDiv('billDiscountAmount','', $this->cart->pricesUnformatted['billDiscountAmount'],true); ?></div>
            <div class="clear"></div>
        </div>
		<?php } ?>
		<?php // We Are in The Last Step
		if ( VmConfig::get('show_tax')) { ?>
            <div class="shipping-total opg-hidden">
                <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2">
					<?php echo JText::_('COM_VIRTUEMART_CART_SHIPPING_TAX').': ' ?>
                </div>
                <div class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2" id="shipment_tax"><?php echo $this->currencyDisplay->createPriceDiv('salesPriceShipment','', $this->cart->pricesUnformatted['shipmentTax'],true); ?></div>

                <div class="clear"></div>
            </div>
	    <?php } ?>
		
		<?php if ( VmConfig::get('show_tax')) { ?>
        <div class=" opg-grid opg-text-right" id="total_taxfulldiv">
            <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2" ><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT').': ' ?></div>
            <div class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2" id="total_tax" ><?php echo $this->currencyDisplay->createPriceDiv('billTaxAmount','', $this->cart->pricesUnformatted['billTaxAmount'],true) ?></div>
            <div class="clear"></div>
        </div>
		<?php } ?>

        <div class="total  opg-grid opg-text-right" id="bill_totalfulldiv">
            <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2 opg-text-large opg-text-primary opg-text-bold"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL').': ' ?></div>
            <div class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2 opg-text-large opg-text-primary opg-text-bold" id="bill_total"><?php echo $this->currencyDisplay->createPriceDiv('billTotal','', $this->cart->pricesUnformatted['billTotal'],true); ?></div>
            <div class="clear"></div>
        </div>


		<?php
		if ( $this->totalInPaymentCurrency && !empty($this->cart->BTaddress['fields']['first_name']['value']) && !empty($this->cart->BTaddress['fields']['city']['value'])) { ?>
            <div class="">
                <div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT').': ' ?></div>
                <div class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2"><?php echo $this->totalInPaymentCurrency;   ?></div>
                <div class="clear"></div>
            </div>
			<?php } ?>
    </div>
   </div>