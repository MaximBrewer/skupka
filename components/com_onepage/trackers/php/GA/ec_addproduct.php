<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

if (isset($pid))
{
$product_id = $pid; 
}
else
{

$product_id = $order_item->order_item_sku; 
if (empty($product_id)) $product_id = $order_item->virtuemart_product_id; 

}

$coupon_code = ''; 
if ((isset($this->order) && (isset($this->order['details']['BT']->coupon_code))))
{
$coupon_code = $this->order['details']['BT']->coupon_code; 
}
else
{
  if (isset($cart) && ($cart instanceof VirtuemartCart))
   {
      $coupon_code = $cart->couponCode; 
   }
}

if (!empty($product)) $order_item = $product; 
if (empty($product)) $product = $order_item; 

$price = 0; 
if (!empty($product->product_final_price))
$price = $product->product_final_price; 
else
if (!empty($product->prices))
{
 $price = $product->prices['salesPrice']; 
}

if (!empty($product->product_quantity))
$q = $product->product_quantity; 
else $q = 1; 

?>

   ga('<?php echo $tracker_name.'.'; ?>ec:addProduct', {
      'id': "<?php echo $this->escapeDouble($product_id); ?>",
      'name': "<?php echo $this->escapeDouble($product->product_name); ?>",
      'category': "<?php echo $this->escapeDouble($product->category_name ); ?>",
	  'brand': "<?php if (!empty($product->mf_name)) 
	  echo $this->escapeDouble($product->mf_name); ?>",
	  'variant': "<?php echo $this->escapeDouble($product->product_sku); ?>",
      'price': <?php echo number_format($price, 2, '.', ''); ?>,
	  'coupon': "<?php echo $this->escapeDouble($coupon_code ); ?>",
      'quantity': <?php echo number_format($q , 0, '.', ''); ?>
   });

   
   if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: enhanced EC addproduct'); 
	  }
