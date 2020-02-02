<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
* This is the default Basket Template. Modify as you like.
*
* @version jQueryId: basket_b2c.html.php 1377 2008-04-19 17:54:45Z gregdev jQuery
* @package VirtueMart
* @subpackage templates
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/


?>
<div id="basket_container" class="cart-view basket_container">

<div class="button"><a class="btn btn-info" href="<?php echo JRoute::_('index.php?option=com_multiorders&view=editcart&cart_id='.$cart_id.'&nosef=1'); ?>">Define Order...</a>
</div>

<div class="inside">
<div class="black-basket cart-summary">
 

		
            <div class="col-module_fix">                                      
             <div class="col-module_content" >
  <div class="op_basket_row op_basket_header">
    <div class="op_col1">&nbsp;</div>
    <div class="op_col2"><?php echo OPCLang::_('COM_VIRTUEMART_CART_NAME') ?></div>
    <div class="op_col3">&nbsp;</div>
    <div class="op_col4" style="display: none;"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SKU') ?>&nbsp;</div>
    <div class="op_col5"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PRICE') ?></div>
    <div class="op_col6"><?php echo OPCLang::_('COM_VIRTUEMART_CART_QUANTITY') ?></div>
    <div class="op_col7"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></div>
  </div>
<?php 
$i=1;
foreach( $product_rows as $product ) { 

/*
DEVELOPER INFORMATION
If you need any other specific information about the product being showed in the basket you can use the following variables in the theme: 
$product['info'] is an instance of VirtueMartModelProduct->getProduct($product['product_id'], $front=true, $calc=false, $onlypublished=false);

To get instance of the single product information associated with the cart without any extra info, you can use: 
$product['product']

All of the variables used in this file are defined in: 
\components\com_onepage\helpers\loader.php
Please don't modify loader.php if you plan to update OPC on bug fix releases. 

Tested Example to show manufacturer info: 


if (!empty($product['info']->virtuemart_manufacturer_id))
{
echo $product['info']->mf_name; 
}
*/

?>
  <div class="op_basket_row op_basket_rows section<?php echo $i ?> set">
    <div class="op_col1">&nbsp;</div>
    <div class="op_col2">
	<div class="product_image">
	<?php echo $this->op_show_image($product['product_full_image'], '', 50, 50, 'product'); ?></div>
	<div class="product_name">
	<div class="cart-title">
	<?php echo $product['product_name']; ?>
	</div>
	<?php echo $product['product_attributes'] ?>&nbsp;
	</div>
	</div>
    <div class="op_col4" style="display: none;"><?php echo $product['product_sku'] ?>&nbsp;</div>
    <div class="op_col5"><?php 
	
	
	echo $product['product_price'] ?>&nbsp;</div>
    <div class="op_col6"><div class="product_quantity"><?php echo $product['product_quantity'] ?></div><?php echo $product['delete_form'];?></div>
    <div class="op_col7"><?php echo $product['subtotal'] ?>&nbsp;</div>
  </div>
  
<?php $i = ($i==1) ? 2 : 1; } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
<?php if (!empty($shipping_inside_basket))
{
	
?>
  <div class="op_basket_row custom_chec" >
    <div class="op_col1">&nbsp;</div>
   
    
	<div class="op_col2" id='shipping_inside_basket'><?php if (!empty($shipping_select)) echo $shipping_select; ?></div>
	<div class="op_col5">
	<div><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING'); ?></div>
	
	</div>
    
	
	<div class="op_col6">&nbsp;</div>
    <div class="op_col7"><div id='shipping_inside_basket_cost'></div></div>
  </div>

<?php
}
if (!empty($payment_select))
{
?>
  <div class="op_basket_row custom_chec" style="display: none;">
    <div class="op_col1"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?></div>
    <div class="op_col2_3"><?php echo $payment_select; ?></div>
    <div class="op_col5_3">&nbsp;<span id='payment_inside_basket_cost'></span></div>
  </div>

 
<?php
}
?>
   <div class="op_basket_row custom_chec none">
    <div style="width: 100%; clear: both;">&nbsp;</div>
  </div>

 <div class="op_basket_row custom_chec" <?php if (empty($discount_before) || empty($coupon_display)) echo ' style="display: none;" '; ?> id="tt_order_discount_before_div_basket">
    <div class="op_col1_4" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>:
    </div> 
    <div class="op_col5_3" align="right" id="tt_order_discount_before_basket"><?php echo $coupon_display ?></div>
  </div>
  
 


                         </div>
           </div>
</div>
</div>
</div>




<script type="text/javascript">
			function setEqualHeight(columns)  
 {  
 var tallestcolumn = 0;  
 columns.each(  
 function()  
 {  
 currentHeight = jQuery(this).height();  
 if(currentHeight > tallestcolumn)  
 {  
 tallestcolumn  = currentHeight;  
 }  
 }  
 );  
 columns.height(tallestcolumn);  
 } 
 			function setEqualHeight2(columns)  
 {  
 var tallestcolumn = 0;  
 columns.each(  
 function()  
 {  
 currentHeight = jQuery(this).height();  
 if(currentHeight > tallestcolumn)  
 {  
 tallestcolumn  = currentHeight;  
 }  
 }  
 );  
 columns.height(tallestcolumn);  
 }  
jQuery(document).ready(function() {  
 setEqualHeight(jQuery(".op_basket_row.op_basket_header  > div"));  
  setEqualHeight2(jQuery(".op_basket_row.op_basket_rows  > div"));

});  
	</script>
