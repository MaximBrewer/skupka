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
 * $Id: default_advertisement.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<?php if(!empty($this->checkoutAdvertise) && $this->params->get('checkout_advertisement', 1)) : ?>
	<div id="proopc-advertise-box">
		<?php foreach($this->checkoutAdvertise as $checkoutAdvertise) : ?>
			<div class="checkout-advertise">
				<?php echo $checkoutAdvertise; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>