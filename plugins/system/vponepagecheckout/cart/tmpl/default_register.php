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
 * $Id: default_register.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$style       = (int) $this->params->get('style', 1);
$button_text = in_array($style, array(3, 4)) ? JText::_('COM_VIRTUEMART_YOUR_ACCOUNT_REG') : JText::_('COM_VIRTUEMART_REGISTER_AND_CHECKOUT');
?>
<?php if(!empty($this->regFields['fields'])) : ?>
	<form id="UserRegistration" name="userForm" autocomplete="off">
		<?php foreach($this->regFields['fields'] as $name => $field) : ?>
			<?php $toolTip = !empty($field['tooltip']) ? ' class="hover-tootip" title="' . htmlspecialchars($field['tooltip']) . '"' : ''; ?>
			<div class="proopc-group">
				<div class="proopc-input-group-level">
					<label class="<?php echo $field['name'] ?> full-input" for="<?php echo $field['name'] ?>_field">
						<span<?php echo $toolTip ?>><?php echo vmText::_($field['title']) ?></span>
						<?php echo (strpos($field['formcode'], ' required') || $field['required'])  ? ' <span class="asterisk">*</span>' : ''; ?>
					</label>
				</div>
				<div class="proopc-input proopc-input-append"<?php echo $field['required'] ? ' data-required="true"' : ''; ?>>
					<?php echo str_replace(array('vm-chzn-select', '<input '), array('', '<input autocomplete="off" '), $field['formcode']); ?>
					<i class="status hover-tootip"></i>
					<?php if($field['name'] == 'password' && $this->params->get('live_validation', 1)) : ?>
						<div class="password-stregth">
							<?php echo JText::_('PLG_VPONEPAGECHECKOUT_PASSWORD_STRENGTH') ?>
							<span id="password-stregth"></span>
						</div>
						<div class="strength-meter"><div id="meter-status"></div></div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
		<div class="proops-login-inputs">
			<div class="proopc-group">
				<div class="proopc-input proopc-input-prepend">
					<button type="submit" id="proopc-task-registercheckout" class="proopc-btn <?php echo $this->btn_class_2 ?>" disabled>
						<i id="proopc-register-process" class="proopc-button-process"></i><?php echo $button_text ?>
					</button>
				</div>
			</div>
			<?php echo JHTML::_( 'form.token' ); ?>
		</div>
	</form>
<?php endif; ?>