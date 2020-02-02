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
 * $Id: default_entrysingle.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$default = $this->params->get('registration_by_default', 0) ? 'register' : 'login';

if(!VmConfig::get('oncheckout_show_register') && $default == 'register')
{
	$default = 'login';
}
?>
<div id="proopc-entry-single" class="proopc-register-login">
	<?php if(!VmConfig::get('oncheckout_show_register') && VmConfig::get('oncheckout_only_registered')) : ?>
		<h3 class="proopc-process-title">
			<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">1</div>' : ''; ?>
			<?php echo JText::_('COM_VIRTUEMART_LOGIN') ?>
		</h3>
		<div class="proopc-inner only-login">
			<?php echo $this->loadTemplate('login'); ?>
		</div>
	<?php else : ?>
		<h3 class="proopc-process-title">
			<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">1</div>' : ''; ?>
			<?php echo JText::_('PLG_VPONEPAGECHECKOUT_REGISTER_OR_LOGIN') ?>
		</h3>
		<div class="proopc-inner">
			<?php if(VmConfig::get('oncheckout_show_register')) : ?>
				<label class="proopc-switch">
					<input type="radio" name="proopc-method" value="register"<?php echo ($default == 'register') ? ' checked' : ''; ?> autocomplete="off" />
					<?php echo JText::_('COM_VIRTUEMART_REGISTER_AND_CHECKOUT') ?>
				</label>
				<div class="proopc-reg-form<?php echo ($default == 'register') ? '' : ' soft-hide'; ?>">
					<h4 class="proopc-subtitle"><?php echo JText::_('PLG_VPONEPAGECHECKOUT_REGISTER_CONVINIENCE')?></h4>
					<div class="proopc-inner with-switch">
						<?php echo $this->loadTemplate('register'); ?>
					</div>
				</div>
			<?php endif; ?>
			<label class="proopc-switch">
				<input type="radio" name="proopc-method" value="login"<?php echo ($default == 'login') ? ' checked' : ''; ?> autocomplete="off" />
				<?php echo JText::_('PLG_VPONEPAGECHECKOUT_LOGIN_AND_CHECKOUT') ?>
			</label>
			<div class="proopc-login-form<?php echo ($default == 'login') ? '' : ' soft-hide'; ?>">
				<div class="proopc-inner with-switch">
					<?php echo $this->loadTemplate('login'); ?>
				</div>
			</div>
			<?php if(!VmConfig::get('oncheckout_only_registered')) : ?>
				<label class="proopc-switch">
					<input type="radio" name="proopc-method" value="guest"<?php echo ($default == 'guest') ? ' checked' : ''; ?> autocomplete="off" /> 
					<?php echo vmText::_('COM_VIRTUEMART_CHECKOUT_AS_GUEST') ?>
				</label>
				<?php if($this->params->get('show_registration_message', 1)) : ?>
					<div class="proopc-reg-advantages<?php echo ($default == 'guest') ? '' : ' soft-hide'; ?>">
						<?php
						$registration_message = trim($this->params->get('registration_message', ''));
						$registration_message = empty($registration_message) ? JText::_('PLG_VPONEPAGECHECKOUT_DEFAULT_REGISTRATION_ADVANTAGE_MSG') : $registration_message;
						echo $registration_message;
						?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>