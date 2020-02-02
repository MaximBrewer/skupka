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
 * $Id: default_payment.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<?php if($this->found_payment_method) : ?>
	<div class="inner-wrap">
		<form id="proopc-payment-form"<?php echo $this->section_class_suffix ? ' class="' . trim($this->section_class_suffix) . '"' : ''; ?>>
			<fieldset>
				<?php foreach($this->paymentplugins_payments as $paymentplugin_payments)
				{
					if(is_array($paymentplugin_payments))
					{
						foreach($paymentplugin_payments as $paymentplugin_payment)
						{
							echo $paymentplugin_payment;
							echo '<div class="clear proopc-method-end"></div>';
						}
					}
				} ?>
			</fieldset>
		</form>
	</div>
<?php else : ?>
	<div class="proopc-alert-error payment"><?php echo $this->payment_not_found_text ?></div>  
<?php endif; ?>
