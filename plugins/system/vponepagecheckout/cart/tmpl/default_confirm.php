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
 * $Id: default_confirm.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<div class="inner-wrap">
	<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart&layout=order_done', $this->useXHTML, $this->useSSL); ?>">
		<?php if(!$this->params->get('handlerbund_compliant', 0)) : ?>
			<?php echo $this->loadTemplate ('cartfields'); ?>
		<?php endif; ?>
		<?php if(!VmConfig::get('use_as_catalog')) : ?>
			<div class="proopc-row proopc-checkout-box<?php echo $this->params->get('handlerbund_compliant', 0) ? ' proopc-checkout-box-splitted' : ''; ?>">
				<button type="button" id="proopc-order-submit" class="proopc-btn proopc-btn-lg <?php echo $this->btn_class_3 ?>" disabled>
					<?php echo JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?>
				</button>
			</div>
		<?php endif; ?>
	</form>
</div>
<?php 
// We have intentionally kept important hidden input fields outside the checkout form.
// They will be moved within the form by JavaScript when the cart is verified.
?>
<div id="proopc-hidden-confirm">
	<input type="hidden" name="STsameAsBT" value="<?php echo $this->cart->STsameAsBT ?>" />
	<input type="hidden" name="shipto" value="<?php echo $this->cart->selected_shipto ?>" />
	<input type="hidden" name="order_language" value="<?php echo $this->order_language; ?>" />
	<input type="hidden" name="task" value="confirm" />
	<input type="hidden" name="option" value="com_virtuemart" />
	<input type="hidden" name="view" value="cart" />
</div>