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
<h1><?php echo OPCLang::_('COM_VIRTUEMART_CART_TITLE'); ?></h1>
<table width="100%" cellspacing="0" cellpadding="4" border="0" style="border:1px solid #BEBCB7;margin-bottom:10px;">

  <tr align="left" >
        <th colspan="3" class="vmsectiontableheader name"><?php echo OPCLang::_('COM_VIRTUEMART_CART_NAME') ?></th>
        <th class="vmsectiontableheader"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SKU') ?></th>
	<th class="vmsectiontableheader"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PRICE') ?></th>
	<th class="vmsectiontableheader"><?php echo OPCLang::_('COM_VIRTUEMART_CART_QUANTITY') ?> / <?php echo OPCLang::_('COM_VIRTUEMART_CART_ACTION') ?></th>
	<th class="vmsectiontableheader delsumma" style="text-align: right;"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></th>
  </tr>
<?php 
$c = 0;
foreach( $product_rows as $product ) {
$c++; 
if ($c&1) $i = '2'; else $i='1'; 
$product['row_color'] = 'sectiontableentry'.$i; 
 ?>
  <tr valign="top" class="<?php echo $product['row_color'] ?>"><?php
  $img = $this->op_show_image($product['product_full_image'], '', 100, 100, 'product'); 
  
  $z = ((empty($img)) || ($img == '&nbsp;')); 
  ?>
	<td class="prod_name" colspan="3"><div class="image_checkout" <?php if ($z) echo 'style="display: none; " '; ?> ><?php 
	echo $img; 
	
	?></div><br <?php if ($z) echo 'style="display: none; " '; ?> /><?php echo $product['product_name'] . $product['product_attributes'] ?></td>
	<td class="prod_sku" ><?php echo $product['product_sku'] ?></td>
	<td class="prod_price" align="left"><?php echo $product['product_price'] ?></td>
	<td class="prod_update" valign="top" >
	  <div style="position: relative;">
		<?php echo $product['update_form'] ?>
		<?php echo $product['delete_form'] ?>
	  </div>
	</td>
    <td class="prod_total" align="right"><?php echo $product['subtotal'] ?></td>
  </tr>
<?php } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
<!-- start of checkout form -->


<?php if (!empty($shipping_inside_basket))
{
?>
  <tr class="vmsectiontableentry1">
    <td align="left"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING'); ?></td> 
    <td colspan="3" align="left"><div id='shipping_inside_basket'><?php if (!empty($shipping_select)) echo $shipping_select; ?></div></td> 
    <td colspan="3" align="right"><div id='shipping_inside_basket_cost'></div></td>
  </tr>
<?php
}
if (!empty($payment_select))
{
?>
  <tr class="vmsectiontableentry1">
    <td align="left"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?></td> 
    <td colspan="3" align="left"><?php echo $payment_select; ?></td> 
    <td colspan="3" align="right"><div id='payment_inside_basket_cost'></div></td>
  </tr>
 
<?php
}
?>
  <tr <?php if (empty($discount_before)) echo ' style="display: none;" '; ?> id="tt_order_discount_before_div_basket" class="vmsectiontableentry1">
    <td colspan="6" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>:
    </td> 
    <td colspan="1" align="right" id="tt_order_discount_before_basket"><?php echo $coupon_display ?></td>
  </tr>
  <tr class="vmsectiontableentry1" id="tt_order_subtotal_div_basket">
    <td colspan="6" align="right" id="tt_order_subtotal_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?>:</td> 
    <td colspan="1" align="right" id="tt_order_subtotal_basket"><?php echo $subtotal_display ?></td>
  </tr>
  <tr style="display: none;" id="tt_order_payment_discount_before_div_basket" class="vmsectiontableentry1">
    <td colspan="6" align="right" id="tt_order_payment_discount_before_txt_basket">:
    </td> 
    <td colspan="1" align="right" id="tt_order_payment_discount_before_basket"></td>
  </tr>
  <tr style="display: none;" id="tt_order_payment_discount_after_div_basket" class="vmsectiontableentry1">
    <td colspan="6" align="right" id="tt_order_payment_discount_after_txt_basket">:
    </td> 
    <td colspan="1" align="right" id="tt_order_payment_discount_after_basket"></td>
  </tr>
 
  <tr id="tt_shipping_rate_div_basket" <?php if (($no_shipping == '1') || (!empty($shipping_inside_basket))) echo ' style="display:none;" '; ?> class="vmsectiontableentry1">
	<td colspan="6" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?>: </td> 
	<td colspan="1" align="right" id="tt_shipping_rate_basket"></td>
  </tr>
  <tr <?php if (empty($discount_after) || (empty($coupon_display))) echo ' style="display:none;" '; ?>class="vmsectiontableentry1" id="tt_order_discount_after_div_basket">
    <td colspan="6" id="tt_order_discount_after_txt_basket" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>:
    </td> 
    <td colspan="1" align="right" id="tt_order_discount_after_basket"><?php echo $coupon_display ?></td>
  </tr>
  <tr class="empty_td">
    <td colspan="7">&nbsp;</td>
  </tr>
  <tr class="total_td">
  	<td class="t3 coupon_section" colspan="4"><?php 
	echo $op_coupon; 
	?></td>
	
    <td class="vmsectiontableentry2 t1" colspan="1" align="right"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>: </td>
    <td class="t2" colspan="1">&nbsp;</td>
    <td class="vmsectiontableentry2 t3" colspan="1" align="right" id="tt_total_basket"><strong><?php echo $order_total_display ?></strong></td>
  </tr>
  <tr id="tt_tax_total_0_div_basket" style="display:none;" class="vmsectiontableentry1">
  		<td class="b0" colspan="4">&nbsp;</td>
        <td class="b1" colspan="1" align="right" valign="top" id="tt_tax_total_0_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </td> 
        <td class="b2" colspan="1">&nbsp;</td>
        <td class="b3" colspan="1" align="right" id="tt_tax_total_0_basket"><?php echo $tax_display ?></td>
  </tr>
  <tr id="tt_tax_total_1_div_basket" style="display:none;" class="vmsectiontableentry1">
        <td colspan="6" align="right" valign="top" id="tt_tax_total_1_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </td> 
        <td colspan="1" align="right" id="tt_tax_total_1_basket"><?php echo $tax_display ?></td>
  </tr>
  <tr id="tt_tax_total_2_div_basket" style="display:none;" class="vmsectiontableentry1">
        <td colspan="4" align="right" valign="top" id="tt_tax_total_2_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </td> 
        <td colspan="3" align="right" id="tt_tax_total_2_basket"><?php echo $tax_display ?></td>
  </tr>
  <tr id="tt_tax_total_3_div_basket" style="display:none;" class="vmsectiontableentry1">
        <td colspan="6" align="right" valign="top" id="tt_tax_total_3_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </td> 
        <td colspan="1" align="right" id="tt_tax_total_3_basket"><?php echo $tax_display ?></td>
  </tr>
  <tr id="tt_tax_total_4_div_basket" style="display:none;" class="vmsectiontableentry1">
        <td colspan="6" align="right" valign="top" id="tt_tax_total_4_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </td> 
        <td colspan="1" align="right" id="tt_tax_total_4_basket"><?php echo $tax_display ?></td>
  </tr>
  
</table>
