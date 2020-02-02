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
 * $Id: default_shipment.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<?php if($this->found_shipment_method) : ?>
	<form id="proopc-shipment-form"<?php echo $this->section_class_suffix ? ' class="' . trim($this->section_class_suffix) . '"' : ''; ?>>
		<div class="inner-wrap">
			<fieldset>
				<?php foreach ($this->shipments_shipment_rates as $shipment_shipment_rates)
				{
					if(is_array($shipment_shipment_rates))
					{
						foreach ($shipment_shipment_rates as $shipment_shipment_rate)
						{
							echo $shipment_shipment_rate;
							echo '<div class="clear"></div>';
						}
					}
				} ?>
				
				<input type="hidden" name="proopc-savedShipment" id="proopc-savedShipment" value="<?php echo $this->cart->virtuemart_shipmentmethod_id ?>" />
			</fieldset>
		</div>
	</form>
<?php else : ?>
	<div class="proopc-alert-error"><?php echo $this->shipment_not_found_text ?></div>
<?php endif; ?>

