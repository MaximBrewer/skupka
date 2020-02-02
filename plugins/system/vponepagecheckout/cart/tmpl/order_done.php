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
 * $Id: order_done.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

echo '<div class="vm-wrap vm-order-done">';

if($this->display_title)
{
	echo '<h3>' . vmText::_('COM_VIRTUEMART_CART_ORDERDONE_THANK_YOU') . '</h3>';
}

// Everything here is displayed by payment method plugin.
// It is exactly same as standard VirtueMart order done layout. We just need to print it as it is.
echo $this->html;

if(vRequest::getBool('display_loginform', true) && !JFactory::getUser()->guest && class_exists('shopFunctionsF'))
{
	echo shopFunctionsF::getLoginForm();
}

echo '</div>';