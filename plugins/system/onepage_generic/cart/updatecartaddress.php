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

$msg = '';
$_dispatcher = JDispatcher::getInstance();
$_retValues = $_dispatcher->trigger('plgVmOnSelectCheckPayment', array( $cart, &$msg));
$dataValid = true;
foreach ($_retValues as $_retVal) 
{
	if ($_retVal === true ) 
	{
		$cart->setCartIntoSession();
		break;
    }
}

$app = JFactory::getApplication();
$postData = $app->input->post->getArray();


$cart->STsameAsBT= $app->input->getString('STsameAsBT');
if($cart->STsameAsBT =='1') {
	$cart->ST=0;
	$cart->selected_shipto = 0;
} else {
	$cart->STsameAsBT=0;
	if(!strlen($postData['shipto_address_type_name'])) {
		$postData['shipto_address_type_name']='ST';
	}
	$cart->saveAddressInCart($postData,'ST', true, 'shipto_');
}


$cart->saveAddressInCart($postData,'BT');
	
$user = JFactory::getUser();

if($user->id > 0)
{
  $postData['address_type'] = 'BT';
  $userModel = VmModel::getModel('user');
  $postData['virtuemart_user_id'] = $user->id;
  
  $userModel->storeAddress($postData);
}
if(!empty($postData['customer_note']))
{
 $cart->BT['customer_note'] = $postData['customer_note'];
 $cart->setCartIntoSession(false,true);
}
foreach($this->userFieldsCart['fields'] as $name => $cartfield)
{
 if($cartfield['required'] == 1)
 {
	 if($cartfield['name'] == "tos")
	 {
	   $cart->cartfields['tos'] = 1;
	 }
	 else
	 {
	   $cart->cartfields[$name] = $cartfield['title'];
	 }
 }
}
$cart->_blockConfirm = false;
$cart->setCartIntoSession(false,true);
$cart->saveCartFieldsInCart();


$dataarray  = array();
$dataarray['success'] = 1;
if($task != "completecheckout")
{
echo json_encode($dataarray);
exit;
}