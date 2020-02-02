<?php 
/**
** Parts of this code is written by joomlaprofessionals.com Copyright (c) 2012, 2015 All Right Reserved.
** Many part of this code is from VirtueMart Team Copyright (c) 2004 - 2015. All rights reserved.
** Some parts might even be Joomla and is Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved. 
** http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
** This source is free software. This version may have been modified pursuant
** to the GNU General Public License, and as distributed it includes or
** is derivative of works licensed under the GNU General Public License or
** other free or open source software licenses.
**
** THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
** KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
** IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A 
** PARTICULAR PURPOSE.

** <author>Joomlaproffs / Virtuemart team</author>
** <email>info@joomlaprofessionals.com</email>
** <date>2015</date>
*/
defined('_JEXEC') or die('Restricted access');
$plugin=JPluginHelper::getPlugin('system','onepage_generic');
   $params=new JRegistry($plugin->params);
$button_primary_class  = $params->get("button_primary","opg-button-primary");
?>


<div class="opg-width-1-1 opg-panel opg-panel-box">
<h3><?php echo vmText::_ ('COM_VIRTUEMART_CART_CHANGE_SHOPPER'); ?></h3>
<form action="<?php echo JRoute::_ ('index.php'); ?>" method="post" class="opg-form">
	<table cellspacing="0" cellpadding="0" border="0" style="border:0px !important;">
		<tr style="border:0px;">
			<td  style="border:0px;">
				<input type="text" name="usersearch" size="20" maxlength="50">
				
				<button type="submit" name="searchShopper" class="opg-button <?php echo $button_primary_class; ?>"><?php echo vmText::_('COM_VIRTUEMART_SEARCH'); ?></button>
			</td>
			<td style="border:0px; width: 5%;"></td>
			<td style="border:0px;">
				<?php 
				if (!class_exists ('VirtueMartModelUser')) {
					require(VMPATH_ADMIN . DS . 'models' . DS . 'user.php');
				}

				$currentUser = $this->cart->user->virtuemart_user_id;
				echo JHtml::_('Select.genericlist', $this->userList, 'userID', 'class="" style="width: 200px"', 'id', 'displayedName', $currentUser,'userIDcart');
				?>
			</td>
			<td style="border:0px;">
				<button type="submit" name="changeShopper" class="opg-button <?php echo $button_primary_class; ?>"><?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?></button>
				
				<input type="hidden" name="view" value="cart"/>
				<input type="hidden" name="task" value="changeShopper"/>
			</td>
		</tr>
		<tr style="border:0px;">
			<td colspan="2" style="border:0px;"></td>
			<td colspan="2" style="border:0px;">
				<?php if($this->adminID && $currentUser != $this->adminID) { ?>
					<b><?php echo vmText::_('COM_VIRTUEMART_CART_ACTIVE_ADMIN') .' '.JFactory::getUser($this->adminID)->name; ?></b>
				<?php } ?>
				<?php echo JHtml::_( 'form.token' ); ?>
			</td>
		</tr>
	</table>
</form>
</div>

