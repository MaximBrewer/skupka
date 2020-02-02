<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
* This is the default Basket Template. Modify as you like.
*
* @version $Id: basket_b2c.html.php 1377 2008-04-19 17:54:45Z gregdev $
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
<div id="basket_container">
	<div id="basket_cart">
		<div id="cart_header" class="vmsectiontableheader">
			<div class="op_col1_header"><?php echo OPCLang::_('COM_VIRTUEMART_CART_NAME') ?></div>
			<div class="op_col4"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SKU');?></div>
			<div class="op_col5 unit_column"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PRICE') ?></div>
			<div class="op_col6"><?php echo OPCLang::_('COM_VIRTUEMART_CART_QUANTITY') ?> / <?php echo OPCLang::_('COM_VIRTUEMART_CART_ACTION') ?></div>
			<div class="op_col7"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></div>
		</div>
		<div id="cart_products">
			<?php 
			$c = 0;
			foreach( $product_rows as $product ) {
			$c++; 
			if ($c&1) $i = '2'; else $i='1'; 
			$product['row_color'] = 'sectiontableentry'.$i; 
			 ?>
			<div  class="cart_prod <?php echo $product['row_color'] ?>">
				<div class="op_col1"><?php echo $this->op_show_image($product['product_full_image'], '', 40, 40, 'product'); ?></div>
				<div class="op_col2"><?php echo $product['product_name']; ?><br />
				<div class="product_s_desc" style=""><?php echo $product['info']->product_s_desc; ?></div>
				<br />
				<?php echo $product['product_attributes']; ?></div>
				<div class="op_col4"><?php echo $product['product_sku']; ?>&nbsp;</div>
				<div class="op_col5"><?php echo $product['product_price']; ?>&nbsp;</div>
				<div class="op_col6">
					<div style="position: relative;">
							<?php echo $product['update_form']; ?>
							<?php echo $product['delete_form']; ?>
					</div>
				</div>
				<div class="op_col7"><?php echo $product['subtotal']; ?></div>
			</div>
			<?php } ?>
		</div>
	</div>
	
	
<?php if (!empty($shipping_inside_basket))
{
?>	<div id="basket_shipping" class="vmsectiontableentry1">
		<div id="shipping_inside_basket_label"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING'); ?></div>
		<div id="shipping_inside_basket"><?php if (!empty($shipping_select)) echo $shipping_select; ?></div>
		<div id="shipping_inside_basket_cost"></div>
	</div>	
	
<?php
}


if (!empty($payment_inside_basket))
{
?>	<div id="basket_payment" class="vmsectiontableentry1">	
		<div id="payment_inside_basket_label"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?> </div>
		<div id="payment_inside_basket"><?php echo $payment_select; ?></div>
		<div id='payment_inside_basket_cost'></div>
	</div>
<?php
}

if (!empty($op_coupon_ajax))
{
echo '<div id="basket_coupon">';
echo $op_coupon_ajax; 
echo '</div>';
}
?>
	
	
	<div id="basket_discount">
		<div <?php if (empty($discount_before)) echo ' style="display: none;" '; ?> id="tt_order_discount_before_div_basket" class="vmsectiontableentry1">
			<div class="discount_label"><?php echo OPCLang::_('COM_ONEPAGE_OTHER_DISCOUNT') ?>:</div>
			<div id="tt_order_discount_before_basket"  class="discount_desc"><?php echo $coupon_display_before ?></div>
		</div>
	  
		<div class="vmsectiontableentry1" id="tt_order_subtotal_div_basket">
			<div id="tt_order_subtotal_txt_basket" class="discount_label"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?>:</div> 
			<div id="tt_order_subtotal_basket" class="discount_desc" ><?php echo $subtotal_display ?></div>
		</div>
		
		<div style="display: none;" id="tt_order_payment_discount_before_div_basket" class="vmsectiontableentry1">
			<div class="discount_label" id="tt_order_payment_discount_before_txt_basket">:</div> 
			<div id="tt_order_payment_discount_before_basket"  class="discount_desc"></div>
		</div>
		
		<div style="display: none;" id="tt_order_payment_discount_after_div_basket" class="vmsectiontableentry1">
			<div id="tt_order_payment_discount_after_txt_basket" class="discount_label">:</div> 
			<div id="tt_order_payment_discount_after_basket"  class="discount_desc"></div>
		</div>
		
		<div id="tt_shipping_rate_div_basket" <?php if (($no_shipping == '1') || (!empty($shipping_inside_basket)) || (empty($order_shipping))) echo ' style="display:none;" '; ?> class="vmsectiontableentry1">
			<div class="discount_label"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?>: </div> 
			<div id="tt_shipping_rate_basket" class="discount_desc"><?php echo $order_shipping; ?></div>
		</div>
 <?php 
 if (empty($op_coupon_ajax))
 {
 
  ?>  
		<div <?php if (empty($discount_after)) echo ' style="display:none;" '; ?>class="vmsectiontableentry1" id="tt_order_discount_after_div_basket">
			<div id="tt_order_discount_after_txt_basket" class="discount_label"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>:</div> 
			<div id="tt_order_discount_after_basket" class="discount_desc"><?php echo $coupon_display ?></div>
		</div>
<?php 
 }
 ?> 
		<div class="vmsectiontableentry1">
			<div class="discount_label">&nbsp;</div>
			<div class="discount_desc"><hr /></div>
		</div>
	<div id="tt_tax_total_0_div_basket" style="display:none;" class="vmsectiontableentry1">
        <div class="discount_label" id="tt_tax_total_0_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div> 
		<div id="tt_tax_total_0_basket" class="discount_desc"><?php echo $tax_display ?></div>
	</div>
  <div id="tt_tax_total_1_div_basket" style="display:none;" class="vmsectiontableentry1">
        <div class="discount_label" id="tt_tax_total_1_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div> 
		<div class="discount_desc" id="tt_tax_total_1_basket"><?php echo $tax_display ?></div>
  </div>
  <div id="tt_tax_total_2_div_basket" style="display:none;" class="vmsectiontableentry1">
        <div class="discount_label" id="tt_tax_total_2_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div> 
		<div class="discount_desc" id="tt_tax_total_2_basket"><?php echo $tax_display ?></div>
  </div>
  <div id="tt_tax_total_3_div_basket" style="display:none;" class="vmsectiontableentry1">
        <div class="discount_label" id="tt_tax_total_3_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div>
		<div class="discount_desc" id="tt_tax_total_3_basket"><?php echo $tax_display ?></div>
  </div>
  <div id="tt_tax_total_4_div_basket" style="display:none;" class="vmsectiontableentry1">
        <div class="discount_label" id="tt_tax_total_4_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div> 
		<div class="discount_desc" id="tt_tax_total_4_basket"><?php echo $tax_display ?></div>
  </div>
   <div class="op_basket_row totals dynamic_lines vmsectiontableentry1"  id="tt_genericwrapper_basket" style="display: none;">
        <div class="discount_label dynamic_col1"   >{dynamic_name}: </div>
        <div class="discount_desc dynamic_col2"   >{dynamic_value}</div>
  </div>
   <?php if (!empty($opc_show_weight_display)) { ?>
   <div class="vmsectiontableentry1" >
        <div class="discount_label" ><?php echo OPCLang::_('COM_ONEPAGE_TOTAL_WEIGHT') ?>: </div>
        <div class="discount_desc" ><?php echo $opc_show_weight_display ?></div>
  </div>
    <?php } ?>
  
  <div class="vmsectiontableentry2" id="tt_total_basket_div_basket">
    <div class="discount_label"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>: </div>
	<div class="discount_desc" id="tt_total_basket"><strong><?php echo $order_total_display ?></strong></div>
  </div>
  
	</div>
 
</div>


