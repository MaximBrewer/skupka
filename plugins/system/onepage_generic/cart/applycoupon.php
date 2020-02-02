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

 	$couponvalue = $input->getString('couponcode'); 
	$couponMessage = $cart->setCouponCode($couponvalue);
	$returndata = array();
	$returndata["wrongcoupon"] = true;
	if(JText::_("COM_VIRTUEMART_CART_COUPON_VALID") == $couponMessage)
	{
	$returndata["wrongcoupon"] = false;
	}
	$returndata["couponMessage"] = $couponMessage;
	echo json_encode($returndata);
	exit;