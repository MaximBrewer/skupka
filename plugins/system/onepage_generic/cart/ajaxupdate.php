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

 	$stockhandle = VmConfig::get('stockhandle','none');
	$quantities = vRequest::getInt('quantityval');
	$stock = vRequest::getInt('stock');
	
	foreach($quantities as $key=>$quantity)
	{
	  if (isset($cart->cartProductsData[$key]) and !empty($quantity)) 
	  {
		  if($quantity != $cart->cartProductsData[$key]['quantity'])
			{
				  $productsleft  = $stock[$key];
				  if ($quantity > $productsleft )
				  {
					 if($productsleft>0 and ($stockhandle=='disableadd' or $stockhandle=='disableit_children') )
					 {
						$quantity = $productsleft;
						$errorMsg = vmText::sprintf('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_QUANTITY', $cart->products[$key]->product_name,$quantity);
						$errorarray = array();
						$errorarray['error'] = 1;
						$errorarray['msg'] = $errorMsg;
						$errorarray['defaultqty'] = $cart->cartProductsData[$key]['quantity'];
						$errorarray['vmid'] = $cart->cartProductsData[$key]['virtuemart_product_id'];
						echo json_encode($errorarray);
						exit;
					 }
					 else 
					 {
					 }
				 }
				 $vmid = $cart->cartProductsData[$key]['virtuemart_product_id'];
				 $cart->cartProductsData[$key]['quantity'] = $quantity;
			   }
		  }
		
	  }
	$cart->setCartIntoSession(true); 
	$errorarray = array();
	$errorarray['error'] = 0;
	echo json_encode($errorarray);
	exit;