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
 * $Id: default_guest.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$emailField = !empty($this->regFields['fields']) && isset($this->regFields['fields']['email']) ?
              $this->regFields['fields']['email'] : null;
$toolTip = !empty($emailField['tooltip']) ? ' class="hover-tootip" title="' . htmlspecialchars($emailField['tooltip']) . '"' : '';
?>
<form method="post" id="GuestUser" autocomplete="off">
	<?php if(!empty($emailField) && is_array($emailField)) : ?>
		<div class="proopc-group">
			<div class="proopc-input-group-level">
				<label class="<?php echo $emailField['name'] ?> full-input" for="<?php echo $emailField['name'] ?>_field">
					<span<?php echo $toolTip ?>><?php echo JText::_($emailField['title']) ?></span>
					<span class="asterisk">*</span>
				</label>
			</div>
			<div class="proopc-input proopc-input-append">
				<?php echo $emailField['formcode'] ?>
				<i class="status hover-tootip"></i>
			</div>
		</div>
	<?php endif; ?>
	<div class="proops-login-inputs">	
		<div class="proopc-group">
			<div class="proopc-input proopc-input-prepend">
				<button type="submit" id="proopc-task-guestcheckout" class="proopc-btn <?php echo $this->btn_class_2 ?>" disabled>
					<i id="proopc-guest-process" class="proopc-button-process"></i><?php echo JText::_('COM_VIRTUEMART_CHECKOUT_AS_GUEST') ?>
				</button>
			</div>
		</div>
		<input type="hidden" name="ctask" value="savebtaddress" />
		<?php echo JHTML::_('form.token'); ?>
	</div>	
</form>