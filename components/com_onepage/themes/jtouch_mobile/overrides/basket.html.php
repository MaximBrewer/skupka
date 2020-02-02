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
<div class="jtouch_basket_section">
 <?php
 if (!empty($continue_link)) { ?>
<div class="ui-grid-a">
	<div class="ui-block-a">
	 <a data-theme="b" data-role="button" class=" ui-btn-icon-left ui-icon-carat-l continue_link"  href="<?php echo $continue_link ?>"  ><?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING'); ?></a>
	</div>
	<div class="ui-block-b">
	
	 <a data-theme="b" data-role="button" class="ui-btn-icon-left ui-icon-carat-l continue_link"  href="#checkout_section"  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_REGISTER_GUEST_CHECKOUT'); ?></a>
	</div>
	
</div>
<?php 
}
?>



<div data-role="collapsible" data-collapsed="false" data-theme="e" data-content-theme="c mobile_basket" class="">
<h3><?php echo OPCLang::_('COM_VIRTUEMART_CART_TITLE'); ?></h3>
<div id="basket_container" >
<div class="inside" >
<div class="black-basket" >
		
            <div ><div ><div ><div >
            <div class="col-module_fix" >                                      
           
                            
                                                
        	
                         <div class="col-module_content">
                         
<?php 
if (false) { 
?>

  <div data-role="header" data-theme="d" class="ui-grid-c my-breakpoint op_basket_header op_basket_row">
    <div class="ui-block-a op_col2"><?php echo OPCLang::_('COM_VIRTUEMART_CART_NAME') ?></div>
    <div class="ui-block-b op_col6"><span class="quantity_label"><?php echo OPCLang::_('COM_VIRTUEMART_CART_QUANTITY') ?> / <?php echo OPCLang::_('COM_VIRTUEMART_CART_ACTION') ?></span>&nbsp;</div>
	<div class="ui-block-c op_col5"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PRICE') ?></div>
    <div class="ui-block-d op_col7"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></div>
  </div>
  
<?php 
}


$max = count($product_rows); 
$curr = 0; 
foreach( $product_rows as $product ) { 

 if (!empty($product['info']))
 if (!empty($product['info']->product_name)) $product_name_raw = $product['info']->product_name; 
 else $product_name_raw = $product['product_name']; 
 $curr++;
?>
	<div data-role="collapsible" data-theme="b" data-content-theme="d product_row" data-collapsed="false" data-inset="false" class="">
	<h3><?php echo $product_name_raw; ?></h3>
  <div   class="ui-grid-c my-breakpoint  op_basket_row <?php 
    if (($max) != $curr)
	 {
	   echo ' special_color'; 
	   
	 }
  ?>">
    <div class="ui-block-a op_col2_2"><?php 
	  echo $product['product_name']; 
	  ?>
	  <div class="hidden_unit_price"><?php echo $product['product_price']; ?></div>
	  <?php
	  echo 	  $product['product_attributes']; 
	  echo $this->op_show_image($product['product_full_image'], '', 50, 0, 'product'); 
	  ?>
	  </div>
    <div class="ui-block-b op_col4 opc_unit_price_column"><?php echo $product['product_price']; ?></div>
    <div class="ui-block-c op_col6"><?php echo $product['update_form'] ?>
		<?php echo $product['delete_form'] ?></div>
    <div class=" op_col5"><?php 
	//echo $product['product_price'] ?></div>
    <div class="ui-block-d op_col7"><?php echo $product['subtotal'] ?></div>
  </div>
  
  
</div>  
<?php } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
<?php if (!empty($shipping_inside_basket))
{
?>
  <div class="op_basket_row" >
    <div class="op_col1"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING'); ?></div>
    <div class="op_col2_3">
    
    <div id='shipping_inside_basket'><?php if (!empty($shipping_select)) echo $shipping_select; ?></div></div>
    <div class="op_col5_3" id="shipping_inside_basket_cost">&nbsp;</div>
  </div>

<?php
}
if (!empty($payment_inside_basket))
{
?>
  <div class="op_basket_row">
    <div class="op_col1"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?></div>
    <div class="op_col2_3"><?php echo $payment_select; ?></div>
    <div class="op_col5_3">&nbsp;<span id='payment_inside_basket_cost'></span></div>
  </div>

 
<?php
}
?>

  <div class="op_basket_row totals ui-grid-b" id="tt_order_subtotal_div_basket" >
	<div class="ui-block-a">&nbsp;</div>
    <div class="ui-block-b op_col1_4" id="tt_order_subtotal_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?>:</div>
	<div class="ui-block-c op_col5_3" id="tt_order_subtotal_basket"><?php echo $subtotal_display ?></div>
  </div>
  
  
<div  class="op_basket_row totals ui-grid-b" <?php if (empty($coupon_display)) echo ' style="display: none;" '; ?> > 
 <div class="ui-block-a">&nbsp;</div>
  <div class="ui-block-b op_col5_3" id="tt_order_discount_after_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?></div>
 <div id="tt_order_discount_after_basket" class="ui-block-c"><?php echo $coupon_display ?></div>
</div> 
	


  <div class="op_basket_row totals ui-grid-b" style="display: none;" id="tt_order_payment_discount_before_div_basket">
	<div class="ui-block-a">&nbsp;</div>
    <div class="op_col1_4 ui-block-b" id="tt_order_payment_discount_before_txt_basket">:
    </div> 
    <div class="op_col5_3 ui-block-c" id="tt_order_payment_discount_before_basket"></div>
  </div>

  
  <div class="op_basket_row totals ui-grid-b" style="display: none;" id="tt_order_payment_discount_after_div_basket">
	<div class="ui-block-a">&nbsp;</div>
    <div class="op_col1_4 ui-block-b" id="tt_order_payment_discount_after_txt_basket">:
    </div> 
    <div class="op_col5_3 ui-block-c"   id="tt_order_payment_discount_after_basket"></div>
  </div>
  
  <div class="op_basket_row totals ui-grid-b" <?php if (empty($coupon_display_before)) echo ' style="display: none;" '; ?> id="tt_order_discount_before_div_basket">
    <div class="op_col1_4 ui-block-b"  ><?php echo OPCLang::_('COM_ONEPAGE_OTHER_DISCOUNT') ?>:
    </div> 
    <div class="op_col5_3 ui-block-c"   id="tt_order_discount_before_basket"><?php echo $coupon_display_before; ?></div>
  </div>
  <div class="op_basket_row totals ui-grid-b" id="tt_shipping_rate_div_basket" <?php if (($no_shipping == '1') || (!empty($shipping_inside_basket)) || (empty($order_shipping))) echo ' style="display:none;" '; ?>>
	<div class="ui-block-a">&nbsp;</div>
	<div class="op_col1_4 ui-block-b"  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?>: </div> 
	<div class="op_col5_3 ui-block-c"   id="tt_shipping_rate_basket"><?php echo $order_shipping; ?></div>
  </div>
  <div class="op_basket_row totals ui-grid-b"  id="tt_tax_total_0_div_basket" style="display:none;" >
		<div class="ui-block-a">&nbsp;</div>
        <div class="op_col1_4 ui-block-b"   id="tt_tax_total_0_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div> 
        <div class="op_col5_3 ui-block-c"   id="tt_tax_total_0_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals ui-grid-b" id="tt_tax_total_1_div_basket" style="display:none;" >
		<div class="ui-block-a">&nbsp;</div>
        <div class="op_col1_4 ui-block-b"   id="tt_tax_total_1_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div> 
        <div class="op_col5_3 ui-block-c"   id="tt_tax_total_1_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals ui-grid-b"  id="tt_tax_total_2_div_basket" style="display:none;" >
		<div class="ui-block-a">&nbsp;</div>
        <div class="op_col1_4 ui-block-b"   id="tt_tax_total_2_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div> 
        <div class="op_col5_3 ui-block-c"   id="tt_tax_total_2_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals ui-grid-b" id="tt_tax_total_3_div_basket" style="display:none;" >
		<div class="ui-block-a">&nbsp;</div>
        <div class="op_col1_4 ui-block-b"   id="tt_tax_total_3_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div>
        <div class="op_col5_3 ui-block-c"   id="tt_tax_total_3_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals ui-grid-b" id="tt_tax_total_4_div_basket" style="display:none;" >
        <div class="ui-block-a">&nbsp;</div>
        <div class="op_col1_4 ui-block-b"   id="tt_tax_total_4_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </div>
        <div class="op_col5_3 ui-block-c"   id="tt_tax_total_4_basket"><?php echo $tax_display ?></div>
  </div>
  
  <div class="op_basket_row totals dynamic_lines ui-grid-b"  id="tt_genericwrapper_basket" style="display: none;">
		<div class="ui-block-a">&nbsp;</div>
        <div class="op_col1_4 dynamic_col1 ui-block-b"   >{dynamic_name}: </div>
        <div class="op_col5_3 dynamic_col2 ui-block-c"   >{dynamic_value}</div>
  </div>
  
  <?php if (!empty($opc_show_weight_display)) { ?>
   <div class="op_basket_row totals ui-grid-b"  id="tt_weight_div_basket" >
		<div class="ui-block-a">&nbsp;</div>
        <div class="op_col1_4 ui-block-b"   ><?php echo OPCLang::_('COM_ONEPAGE_TOTAL_WEIGHT') ?>: </div>
        <div class="op_col5_3 ui-block-c"   ><?php echo $opc_show_weight_display ?></div>
  </div>
  <?php } ?>
  <div class="opc_basket_sep">&nbsp;</div>
  <div class="op_basket_row totals ui-grid-b" id="tt_total_basket_div_basket">
    <div class="ui-block-a">&nbsp;</div>
    <div class="op_col1_4 ui-block-b"  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>: </div>
    <div class="op_col5_3 ui-block-c"   id="tt_total_basket"><?php echo $order_total_display ?></div>
  </div>
  
 
  


                         </div>
           </div>
           </div></div></div></div>
</div>
</div>
</div>
</div>

</div>