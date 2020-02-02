<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This loads before first ajax call is done, this file is called per each shipping html generated
*/
defined('_JEXEC') or die('Restricted access');


// this code illustrates on how to unset a shipping html per SKU

$cart = VirtuemartCart::getCart(); 
$products = $cart->products;
	foreach($products as $product)
	{
      $product_sku = $product->product_sku;
	  break;
	}
	
	
	$db = JFactory::getDBO();
	$query = 'SELECT * FROM vmshippingcharge WHERE sku ="'.$product_sku.'"';
	$db->setQuery($query);
	$result = $db->loadObject();
	if($result)
	{

	}
	else
	{
	
	
      $html = ' '; 
	}