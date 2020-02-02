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
 * $Id: default_cartfields.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$hiddenFields = '';
$i = 0;
?>
<?php if(!empty($this->userFieldsCart['fields'])) : ?>
	<?php foreach($this->userFieldsCart['fields'] as $field) : ?>
		<?php if($field['hidden']) :
			$hiddenFields .= $field['formcode'] . "\n";
		else : ?>
			<?php $toolTip = !empty($field['tooltip']) ? ' class="hover-tootip" title="' . htmlspecialchars($field['tooltip']) . '"' : ''; ?>
			<?php if($field['name'] == 'customer_note' || $field['type'] == 'textarea') : ?>
				<div class="customer-comment-group">
					<label for="<?php echo $field['name'] ?>_field" class="comment">
						<span<?php echo $toolTip ?>><?php echo $field['title'] ?></span>
						<?php if($field['required']) : ?>
							<span class="asterisk">*</span>
						<?php endif ?>
					</label>
					<?php 
					$field['formcode'] = str_replace('rows="1"', 'rows="3"', $field['formcode']);
					if($field['required'])
					{
						$field['formcode'] = str_replace('<textarea', '<textarea required="required"', $field['formcode']);
					} ?>
					<?php echo strpos($field['formcode'], 'class="') ? 
					str_replace('class="', 'class="customer-comment proopc-customer-comment ', $field['formcode']) : 
					str_replace('<textarea', '<textarea class="customer-comment proopc-customer-comment"', $field['formcode']); ?>
				</div>
			<?php elseif($field['name'] == 'tos') : ?>
				<div class="cart-tos-group">
					<?php
					$required = ($field['required'] || VmConfig::get('agree_to_tos_onorder')) ? ' required="required" data-label="' . JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS') . '"' : '';
					
					$this->cart->prepareVendor();
					
					$tosValue = (is_array($this->cart->BT) && !empty($this->cart->BT['tos'])) ? $this->cart->BT['tos'] : 0;
					$tosValue = (!$tosValue && !empty($this->cart->cartfields) && !empty($this->cart->cartfields['tos'])) ? $this->cart->cartfields['tos'] : 0;
					$checkbox = VmHtml::checkbox ($field['name'], $tosValue, 1, 0, 'id="cart_tos_field" class="terms-of-service"' . $required);
					?>
					<?php if(VmConfig::get('oncheckout_show_legal_info', 1)) : ?>
						<?php if($this->params->get('tos_fancybox', 0)) : ?>
							<label for="cart_tos_field" class="checkbox prooopc-tos-label proopc-row">
								<?php echo $checkbox ?>
								<div class="terms-of-service-cont">
									<a href="#proopc-tos-fancy" class="terms-of-service" data-tos="fancybox"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?></a>
								</div>
							</label>
							<div class="soft-hide">
								<div id="proopc-tos-fancy" class="fancy-tos-container">
									<div class="fancy-tos-head">
										<button type="button" class="fancy-close"><span aria-hidden="true">&times;</span></button>
										<h3 class="fancy-tos-title"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h3>
									</div>
									<div class="fancy-tos-body">
										<p><?php echo $this->cart->vendor->vendor_terms_of_service; ?></p>
									</div>
								</div>
							</div>
						<?php else : ?>
							<label for="cart_tos_field" class="checkbox prooopc-tos-label proopc-row">
								<?php echo $checkbox ?>
								<div class="terms-of-service-cont">
									<a href="#proopc-tos-fancy" class="terms-of-service" data-toggle="bootmodal"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?></a>
								</div>
							</label>
							<div class="bootmodal fade" id="proopc-tos-fancy" tabindex="-1" role="dialog" aria-labelledby="tosLabel" aria-hidden="true">
								<div class="bootmodal-header">
									<button type="button" class="close" data-dismiss="bootmodal" aria-hidden="true">&times;</button>
									<h3 id="tosLabel"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h3>
								</div>
								<div class="bootmodal-body">
									<p><?php echo $this->cart->vendor->vendor_terms_of_service; ?></p>
								</div>
							</div>
						<?php endif; ?>
					<?php else : ?>
						<label for="cart_tos_field" class="checkbox prooopc-tos-label proopc-row">
							<?php echo $checkbox ?> <?php echo $field['title'] ?>
						</label>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<?php $i++; ?>
				<div class="<?php echo $field['name'] ?>-group custom-cart-field-<?php echo $i ?>">
					<div class="inner">
						<label for="<?php echo $field['name'] ?>_field" class="<?php echo $field['name'] ?>">
							<span<?php echo $toolTip ?>><?php echo $field['title'] ?></span>
							<?php if($field['required']) : ?>
								<span class="asterisk">*</span>
							<?php endif ?>
						</label>
						<?php echo $field['formcode'] ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>

<?php echo $hiddenFields; ?>