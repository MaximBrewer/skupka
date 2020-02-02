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
 * $Id: default_shopperform.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<?php if($this->isAdminUser) : ?>
	<?php 
	$users = $this->getUserList();
	$app = JFactory::getApplication();
	$search = $app->getUserStateFromRequest('proopc.changeshoper.usersearch', 'usersearch', '', 'STRING');
	?>
	<div class="proopc-change-shopper-cont">
		<h3 class="proopc-change-shopper-title"><?php echo vmText::_ ('COM_VIRTUEMART_CART_CHANGE_SHOPPER'); ?></h3>
		<div class="proopc-change-shopper-inner">
			<?php if(!empty($this->vmAdminID) && $this->juser->id != $this->vmAdminID) : ?>
				<div class="proopc-active-shopper">
					<span><?php echo vmText::_('COM_VIRTUEMART_CART_ACTIVE_ADMIN') . ' ' . $this->adminUser->name; ?></span>
				</div>
			<?php endif; ?>
			<form action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart', false); ?>" method="post" id="form-usersearch" class="proopc-form-inline">
				<div class="proopc-field-group">
					<input type="text" name="usersearch" id="usersearch" value="<?php echo $search ?>" onchange="this.form.submit();" placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>"/>
					<button type="submit" class="proopc-btn" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" class="proopc-btn proopc-clear-filter" onclick="return ProOPC.resetForm(this.form, '#usersearch');" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
				<input type="hidden" name="view" value="cart"/>
				<?php echo JHtml::_( 'form.token' ); ?>
			</form>
			<div class="clear"></div>
			<form action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart', false); ?>" method="post" class="proopc-form-inline">
				<div class="proopc-field-group">
					<?php echo JHtml::_('select.genericlist', $users, 'userID', 'class=""', 'id', 'displayedName', $this->cart->user->virtuemart_user_id, 'shopper_id'); ?>
					<button type="submit" class="proopc-btn" title="<?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?>"><?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?></button>
				</div>
				<input type="hidden" name="view" value="cart"/>
				<input type="hidden" name="task" value="changeShopper"/>
				<?php echo JHtml::_( 'form.token' ); ?>
			</form>
		</div>
	</div>
<?php endif; ?>
