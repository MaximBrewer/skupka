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
 * $Id: default_style4.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$checkout_step = 1;
$confirm_class = ' no-top-margin';
?>
<div id="proopc-system-message"></div>
<div class="proopc-finalpage">
	<div class="proopc-row">
		<h1 class="cart-page-title">
			<?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?>&nbsp;<span class="septa">/</span>&nbsp;<span><?php echo JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', '<span id="proopc-cart-totalqty">' . $this->productsCount . '</span>'); ?></span>
		</h1>
	</div>
	<div class="proopc-row">
		<div class="proopc-login-message-cont">
			<?php if(!$this->juser->guest) : ?>
				<?php echo $this->loadTemplate('logout'); ?>
			<?php endif; ?>
		</div>
		<?php if(!empty($this->continue_link)) : ?>
			<div class="proopc-continue-link">
				<a href="<?php echo $this->continue_link ?>"><?php echo vmText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a>
			</div>
		<?php endif; ?>
	</div>
	<form id="proopc-carttable-form">
		<div id="proopc-pricelist">
			<?php echo $this->loadTemplate('cartlist'); ?>
		</div>
		<input type="hidden" name="ctask" value="updateproduct" />
	</form>
	<div class="proopc-column3">
		<?php if($this->juser->guest && !$this->params->get('only_guest', 0)) : ?>
			<?php echo $this->loadTemplate('entrysingle'); ?>
			<?php $checkout_step++; ?>
		<?php endif; ?>
		<div class="proopc-bt-address">
			<h3 class="proopc-process-title">
				<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL') ?>
			</h3>
			<?php echo $this->loadTemplate('btaddress'); ?>
		</div>
	</div>
	<div class="proopc-column3">
		<?php
		$shipto_class_suffix = '';
		$shipment_class_suffix = '';
		$payment_class_suffix = '';
		
		if($this->params->get('hide_shipto', 0) && !empty($this->cart->STsameAsBT))
		{
			$shipto_class_suffix = ' hide';
			$shipment_class_suffix = ' proopc-no-margin-top';
			$checkout_step--;
		}
		?>
		<div class="proopc-st-address<?php echo $shipto_class_suffix; ?>">
			<h3 class="proopc-process-title">
				<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL') ?>
			</h3>
			<?php echo $this->loadTemplate('staddress'); ?>
		</div>
		<?php
		if($this->params->get('hide_shipment', 0))
		{
			$shipment_class_suffix .= ' hide';
			
			if($this->params->get('hide_shipto', 0))
			{
				$payment_class_suffix = ' proopc-no-margin-top';
			}
			
			$checkout_step--;
		}
		?>
		<div class="proopc-shipments<?php echo $shipment_class_suffix; ?>">
			<h3 class="proopc-process-title">
				<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_CART_SHIPPING')?>
			</h3>
			<div id="proopc-shipments">
				<?php echo $this->loadTemplate('shipment'); ?>
			</div>
		</div>
		<div class="proopc-payments<?php echo $payment_class_suffix; ?>">
			<h3 class="proopc-process-title">
				<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_CART_PAYMENT')?>
			</h3>
			<div id="proopc-payments">
				<?php echo $this->loadTemplate('payment'); ?>
			</div>
		</div>
		<?php if($this->params->get('handlerbund_compliant', 0)) : ?>
			<?php if (VmConfig::get('coupons_enable')) : ?>
				<div class="proopc-coupon">
					<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : ''; ?><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT')?></h3>
					<div id="proopc-coupon">
						<?php echo $this->loadTemplate('coupon'); ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="proopc-additional-info">
				<h3 class="proopc-process-title">
					<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('PLG_VPONEPAGECHECKOUT_ADDITIONAL_INFO')?>
				</h3>
				<div id="proopc-additional-info">
					<?php echo $this->loadTemplate('cartform'); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="proopc-column3 last">
		<?php if (VmConfig::get('coupons_enable') && !$this->params->get('handlerbund_compliant', 0)) : ?>
			<?php $confirm_class = ''; ?>
			<div class="proopc-coupon no-top-margin">
				<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT')?></h3>
				<div id="proopc-coupon">
					<?php echo $this->loadTemplate('coupon'); ?>
				</div>
			</div>
		<?php endif; ?>
		<div class="proopc-confirm-order<?php echo $confirm_class ?>">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU')?></h3>
			<div id="proopc-confirm-order">
				<?php echo $this->loadTemplate('confirm'); ?>
			</div>
			<?php echo $this->loadTemplate ('advertisement'); ?>
		</div>
	</div>
</div>