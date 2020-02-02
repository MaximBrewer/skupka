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
 * $Id: default_login.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$return = JRoute::_('index.php?option=com_virtuemart&view=cart', false);
$twofactormethods = array();

if(version_compare(JVERSION, '3.0.0', 'ge'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';
	$twofactormethods = UsersHelper::getTwoFactorMethods();
}

$email_as_username = (int) $this->params->get('email_as_username', 2);
$style             = (int) $this->params->get('style', 1);
$button_text       = in_array($style, array(3, 4)) ? JText::_('COM_VIRTUEMART_LOGIN') : JText::_('PLG_VPONEPAGECHECKOUT_LOGIN_AND_CHECKOUT');
?>
<?php if($this->juser->guest) : ?>
	<?php if(!empty($this->social_login)) : ?>
		<div class="proopc-social-login">
			<?php echo $this->social_login ?>
		</div>
	<?php endif ?>
	<h4 class="proopc-subtitle"><?php echo JText::_('PLG_VPONEPAGECHECKOUT_ASK_FOR_LOGIN'); ?></h4>
	<form name="proopc-login" id="UserLogin" autocomplete="off">
		<div class="proopc-group">
			<div class="proopc-input-group-level">
				<?php if($email_as_username == 1) : ?>
					<label class="full-input" for="proopc-username"><?php echo vmText::_('COM_VIRTUEMART_EMAIL'); ?></label>
				<?php elseif($email_as_username == 2) : ?>
					<label class="full-input" for="proopc-username"><?php echo vmText::_('COM_VIRTUEMART_USERNAME'); ?> / <?php echo vmText::_('COM_VIRTUEMART_EMAIL'); ?></label>
				<?php else : ?>
					<label class="full-input" for="proopc-username"><?php echo vmText::_('COM_VIRTUEMART_USERNAME'); ?></label>
				<?php endif; ?>
			</div>
			<div class="proopc-input proopc-input-append">
				<?php if($this->params->get('email_as_username') == 1) : ?>
					<input type="email" id="proopc-username" name="username" class="inputbox input-medium" size="18" required />
				<?php else : ?>
					<input type="text" id="proopc-username" name="username" class="inputbox input-medium" size="18" required />
				<?php endif ?>
				<i class="status hover-tootip"></i>
			</div>
		</div>
		<div class="proopc-group">
			<div class="proopc-input-group-level">
				<label class="full-input" for="proopc-passwd"><?php echo JText::_('COM_VIRTUEMART_PASSWORD'); ?></label>
			</div>
			<div class="proopc-input proopc-input-append">
				<input id="proopc-passwd" type="password" name="password" class="inputbox input-medium" size="18" required />
				<i class="status hover-tootip"></i>
			</div>
		</div>
		<?php if (count($twofactormethods) > 1): ?>
		<div id="form-login-secretkey" class="proopc-group">
			<div class="proopc-input-group-level">
				<label class="full-input" for="proopc-secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY') ?></label>
			</div>
			<div class="proopc-input proopc-input-append">
				<input id="proopc-secretkey" autocomplete="off" type="text" name="secretkey" class="inputbox input-medium" size="18" />
			</div>
		</div>
		<?php endif; ?>
		<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="proopc-group">
				<div class="proopc-input proopc-input-append">
					<label for="proopc-remember" class="proopc-checkbox inline">
						<input type="checkbox" id="proopc-remember" name="remember" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
						<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>
					</label>
				</div>
			</div>
		<?php endif; ?>
		<div class="proops-login-inputs">
			<div class="proopc-group">
				<div class="proopc-input proopc-input-prepend">
					<button type="submit" id="proopc-task-loginajax" class="proopc-btn <?php echo $this->btn_class_2 ?>" disabled>
						<i id="proopc-login-process" class="proopc-button-process"></i>
						<?php echo $button_text ?>
					</button>
				</div>
			</div>
			<input type="hidden" name="ctask" value=""/>
			<input type="hidden" name="return" id="proopc-return"	value="<?php echo base64_encode($return); ?>" />
			<?php echo JHtml::_('form.token');?>
		</div>
		<div class="proops-login-inputs">
			<div class="proopc-group">
				<div class="proopc-input">
					<ul class="proopc-ul">
						<?php if($this->params->get('email_as_username') != 1) : ?>
							<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_USERNAME'); ?></a></li>
						<?php endif ?>
						<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
	</form>
<?php endif; ?>