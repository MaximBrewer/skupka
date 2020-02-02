<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

?>
<div id="basket_container" class="cart-view">
<div class="inside">
<div class="black-basket cart-summary">
 

		 <h3 class="module-title"><span><span><?php echo OPCLang::_('COM_VIRTUEMART_CART_TITLE'); ?></span></span></h3>
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
	<?php echo $this->op_show_image($product['product_full_image'], '', 100, 100, 'product'); ?>&nbsp;
	<?php echo '<div class="cart-title">'.$product['product_name'] .'</div>'. $product['product_attributes'] ?>&nbsp;
	<br />
<?php	

if (!empty($product['info']->product_s_desc))
{
//echo $product['info']->product_s_desc; 
}
?>
	</div>
    <div class="op_col4" style="display: none;"><?php echo $product['product_sku'] ?>&nbsp;</div>
    <div class="op_col5"><?php echo $product['product_price'] ?>&nbsp;</div>
    <div class="op_col6"><?php echo $product['update_form'] ?><?php echo $product['delete_form'];?></div>
    <div class="op_col7"><?php echo $product['subtotal'] ?>&nbsp;</div>
  </div>
  
<?php $i = ($i==1) ? 2 : 1; } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
<?php if (!empty($shipping_inside_basket))
{
?>
  <div class="op_basket_row custom_chec" >
    <div class="op_col1">&nbsp;</div>
    <div class="op_col2_3">
    <div><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING'); ?></div>
    <div id='shipping_inside_basket'><?php if (!empty($shipping_select)) echo $shipping_select; ?></div></div>
    <div class="op_col5_3"><div id='shipping_inside_basket_cost'></div></div>
  </div>

<?php
}
if (!empty($payment_inside_basket))
{
?>
  <div class="op_basket_row custom_chec" >
    <div class="op_col1_4"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?>
    <div class="payment_select_place"><?php echo $payment_select; ?></div></div>
    <div class="op_col5_3">&nbsp;<span id='payment_inside_basket_cost'></span></div>
  </div>

 
<?php
}
?>
   <div class="op_basket_row custom_chec none">
    <div style="width: 100%; clear: both;">&nbsp;</div>
  </div>

 <div class="op_basket_row custom_chec" <?php if (empty($discount_before) || empty($coupon_display)) echo ' style="display: none;" '; ?> id="tt_order_discount_before_div_basket">
    <div class="op_col1_4" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>
    </div> 
    <div class="op_col5_3" align="right" id="tt_order_discount_before_basket"><?php echo $coupon_display ?></div>
  </div>
  
  <div class="op_basket_row custom_chec" id="tt_order_subtotal_div_basket">
    <div class="op_col1_4" id="tt_order_subtotal_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></div>
	<div class="op_col5_3" id="tt_order_subtotal_basket"><?php echo $subtotal_display ?></div>
  </div>

  <div class="op_basket_row custom_chec" style="display: none;" id="tt_order_payment_discount_before_div_basket">
    <div class="op_col1_4" id="tt_order_payment_discount_before_txt_basket">
    </div> 
    <div class="op_col5_3" id="tt_order_payment_discount_before_basket"></div>
  </div>

  
  <div class="op_basket_row custom_chec" style="display: none;" id="tt_order_payment_discount_after_div_basket">
    <div class="op_col1_4" id="tt_order_payment_discount_after_txt_basket">
    </div> 
    <div class="op_col5_3 " align="right" id="tt_order_payment_discount_after_basket"></div>
  </div>
  

  <div class="op_basket_row custom_chec" id="tt_shipping_rate_div_basket" <?php if (($no_shipping == '1') || (!empty($shipping_inside_basket)) || (empty($order_shipping))) echo ' style="display:none;" '; ?> >
	<div class="op_col1_4" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?> </div> 
	<div class="op_col5_3" align="right" id="tt_shipping_rate_basket"><?php echo $order_shipping; ?></div>
  </div>
  <div class="op_basket_row custom_chec" <?php if (empty($discount_after)) echo ' style="display:none;" '; ?> id="tt_order_discount_after_div_basket">
    <div class="op_col1_4" id="tt_order_discount_after_txt_basket" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>
    </div> 
    <div class="op_col5_3" align="right" id="tt_order_discount_after_basket"><?php echo $coupon_display ?></div>
  </div>
  <div class="op_basket_row custom_chec" style="display: none;">
    <div style="width: 100%; clear: both;">&nbsp;</div>
  </div>
  <div class="op_basket_row custom_chec"  id="tt_tax_total_0_div_basket" style="clear:both; <?php if (empty($tax_display)) echo 'display: none;'; ?>" >
        <div class="op_col1_4" align="right" id="tt_tax_total_0_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div> 
        <div class="op_col5_3" align="right" id="tt_tax_total_0_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row custom_chec" id="tt_tax_total_1_div_basket" style="display:none;" >
        <div class="op_col1_4" align="right" id="tt_tax_total_1_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div> 
        <div class="op_col5_3" align="right" id="tt_tax_total_1_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row custom_chec"  id="tt_tax_total_2_div_basket" style="display:none;" >
        <div class="op_col1_4" align="right" id="tt_tax_total_2_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div> 
        <div class="op_col5_3" align="right" id="tt_tax_total_2_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row custom_chec" id="tt_tax_total_3_div_basket" style="display:none;" >
        <div class="op_col1_4" align="right" id="tt_tax_total_3_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div>
        <div class="op_col5_3" align="right" id="tt_tax_total_3_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row custom_chec" id="tt_tax_total_4_div_basket" style="display:none;" >
        <div class="op_col1_4" align="right" id="tt_tax_total_4_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div>
        <div class="op_col5_3" align="right" id="tt_tax_total_4_basket"><?php echo $tax_display ?></div>
  </div>
  
   <div class="op_basket_row custom_chec totals dynamic_lines "  id="tt_genericwrapper_basket" style="display: none;">
        <div class="op_col1_4 discount_label dynamic_col1"   >{dynamic_name} </div>
        <div class="op_col5_3 discount_desc dynamic_col2"   >{dynamic_value}</div>
  </div>
  
  
  <div class="op_basket_row custom_chec total_total">
    <div class="op_col1_4" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL'); ?> </div>
    <div class="op_col5_3" align="right" id="tt_total_basket"><strong><?php echo $order_total_display ?></strong></div>
  </div>


                         </div>
           </div>
</div>
</div>
</div>



