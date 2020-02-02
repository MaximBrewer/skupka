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

// missing language strings 
$CART_STRING = OPCLang::_('COM_ONEPAGE_CART_STRING'); // 'Inhound'; 
//$CART_STRING = 'Cart'; 

// echo '<h1>'.OPCLang::_('COM_VIRTUEMART_CART_TITLE').'</h1>'; 
?>

<div id="basket_container" style="float: left;">
<div class="inside" style="float: left;">
<div class="black-basket" style="float: left;">
		
            <div ><div ><div ><div>
            <div >                                      
           
                                       
        	
                         <div class="top_b">
                         
  <div class="opc_heading" ><span class="opc_title"><?php echo $CART_STRING; ?></span></div>
 
  <div class="product_wrapper">
    <div class="wapka_wapka_1">
  <div class="korzina_wapka1">Фото</div>
  <div class="korzina_wapka2">Наименование товара</div>
  <div class="korzina_wapka3">Цена</div>
  <div class="korzina_wapka5">Количество</div>
  <div class="korzina_wapka4">Сумма</div>
    </div>
    <div class="inside_product_wrapper">
<?php 
$max = count($product_rows); 
$curr = 0; 
foreach( $product_rows as $product ) { 

 $curr++;
	if (!class_exists('CurrencyDisplay'))
    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
   $currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
   $quant = $product['product_quantity'];
   $discount = $currencyDisplay->createPriceDiv('billDiscountAmount', '',  $product['prices']['discountAmount']*$quant, true, false, 1, true);
   
   $basePrice = $currencyDisplay->createPriceDiv('billDiscountAmount', '',  $product['prices']['basePrice'], true, false, 1, true);
   if ($product['prices']['basePrice'] === $product['prices']['salesPrice']) {
   $basePrice = ''; 
   }
?>
  <div class="op_basket_row <?php 
    if (($max) != $curr)
	 {
	  //echo ' opc_separator ';
	 }
  ?>">
  <div class="korzina_wapka_ob">
  <div class="korzina_wapka_tovar_1"><?php echo $this->op_show_image($product['product_full_image'], '', 40, 40, 'product'); ?></div>
   <div class="korzina_wapka_tovar_2_2">
     <div class="korzina_wapka_tovar_2"><div class="korzina_otstup_tovar_artikyl"><?php echo $product['product_name'] . $product['product_attributes'] ?></div>      <?php echo '<span class="abc">'.JText::_('COM_VIRTUEMART_PRODUCT_SKU') .':</span> '. $product['product_sku'] ?>
 
     </div>
   </div>
   <div class="korzina_wapka_tovar_3_3">
     <div class="korzina_wapka_tovar_3">
    <span class="zachernyt_ceny">   <div class="overr"><?php echo $basePrice; ?></div><?php echo $product['product_price'] ?>
       </div>
   </div>
   <div class="korzina_wapka_tovar_4_3">
     <div class="korzina_wapka_tovar_4">
       <?php echo $product['update_form'] ?>
       <?php echo $product['delete_form']; 
		?>
       </div>
   </div>
  <div class="korzina_wapka_tovar_5_3">
    <div class="korzina_wapka_tovar_5">
      <?php echo $product['subtotal'];?>
      </div>
  </div>
 </div>
 

	
    
   
	
    
  </div>
<?php } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
<?php if (!empty($shipping_inside_basket))
{
?>
  <div class="op_basket_row" style="padding-bottom: 4px;">
    <div class="op_col1">&nbsp;</div>
    <div class="op_col2_3">
    <div><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING'); ?></div>
    <div id='shipping_inside_basket'><?php if (!empty($shipping_select)) echo $shipping_select; ?></div></div>
    <div class="op_col5_3 opc_total_price"><div id='shipping_inside_basket_cost'></div></div>
  </div>

<?php
}
if (!empty($payment_inside_basket))
{
?>
  <div class="op_basket_row">
     <div class="op_col1">&nbsp;</div>
    
    <div class="op_col2_3">
	 <div><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?></div>
	<?php echo $payment_select; ?>
	</div>
    <div class="op_col5_3 opc_total_price">&nbsp;<span id='payment_inside_basket_cost'></span></div>
  </div>

 
<?php
}
?>

<?php 
if (false)
{
// this will show product subtotal with tax, remove if(false)
?>
<div style="display: none;">
<div class="op_basket_row totals" id="tt_static_total_div_basket" >
    <div class="op_col1_4"   id="tt_total_basket_static2"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></div>
	<div class="op_col5_3 opc_total_price" id="tt_order_subtotal_basket2">
	<?php 
	$product_subtotal = $totals_array['order_subtotal']+$totals_array['order_tax']; 
	echo $GLOBALS['CURRENCY_DISPLAY']->getFullValue($product_subtotal); ?>
	</div>
	</div>
</div>
<?php
}
?>
 <div class="div_basket_top">
   <div class="op_basket_row totals" id="tt_order_subtotal_div_basket" >
     <div class="op_col1_4" id="tt_order_discount_before_basket_txt" ><?php echo OPCLang::_('COM_ONEPAGE_OTHER_DISCOUNT');  ?>
       </div> 
     <div class="op_col5_3 opc_total_price"   id="tt_order_discount_before_basket"><?php echo $coupon_display_before ?></div>
   </div>
   <div class="op_basket_row totals" id="tt_order_subtotal_div_basket" >
     <div class="op_col1_4" id="tt_order_subtotal_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></div>
     <div class="op_col5_3 opc_total_price" id="tt_order_subtotal_basket"><?php echo $subtotal_display ?></div>
   </div>
 </div>
 


  <div class="op_basket_row totals" style="display: none;" id="tt_order_payment_discount_before_div_basket">
    <div class="op_col1_4" id="tt_order_payment_discount_before_txt_basket">
    </div> 
    <div class="op_col5_3 opc_total_price" id="tt_order_payment_discount_before_basket"></div>
  </div>

  <div class="op_basket_row totals" <?php if (empty($discount_after)) echo ' style="display:none;" '; ?> id="tt_order_discount_after_div_basket">
    <div class="op_col1_4" id="tt_order_discount_after_txt_basket" ><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>
    </div> 
    <div class="op_col5_3 opc_total_price"   id="tt_order_discount_after_basket"><?php echo $coupon_display ?></div>
  </div>
  <div class="op_basket_row totals" style="display: none;" id="tt_order_payment_discount_after_div_basket">
    <div class="op_col1_4" id="tt_order_payment_discount_after_txt_basket">
    </div> 
    <div class="op_col5_3 opc_total_price"   id="tt_order_payment_discount_after_basket"></div>
  </div>
  
 
  <div>
  
  </div>
  <div class="op_basket_row totals"  id="tt_tax_total_0_div_basket" style="display:none;" >
        <div class="op_col1_4"   id="tt_tax_total_0_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div> 
        <div class="op_col5_3 opc_total_price"   id="tt_tax_total_0_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals" id="tt_tax_total_1_div_basket" style="display:none;" >
        <div class="op_col1_4"   id="tt_tax_total_1_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div> 
        <div class="op_col5_3 opc_total_price"   id="tt_tax_total_1_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals"  id="tt_tax_total_2_div_basket" style="display:none;" >
        <div class="op_col1_4"   id="tt_tax_total_2_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div> 
        <div class="op_col5_3 opc_total_price"   id="tt_tax_total_2_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals" id="tt_tax_total_3_div_basket" style="display:none;" >
        <div class="op_col1_4"   id="tt_tax_total_3_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div>
        <div class="op_col5_3 opc_total_price"   id="tt_tax_total_3_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals" id="tt_tax_total_4_div_basket" style="display:none;" >
        <div class="op_col1_4"   id="tt_tax_total_4_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?> </div>
        <div class="op_col5_3 opc_total_price"   id="tt_tax_total_4_basket"><?php echo $tax_display ?></div>
  </div>
  <div class="op_basket_row totals dynamic_lines"  id="tt_genericwrapper_basket" style="display: none;">
        <div class="op_col1_4 opc_total_price dynamic_col1"   >{dynamic_name}: </div>
        <div class="op_col5_3 opc_total_price dynamic_col2"   >{dynamic_value}</div>
  </div>
  
  
 <?php if (!empty($opc_show_weight_display)) { ?>
   <div class="op_basket_row" >
        <div class="op_col1_4" ><?php echo OPCLang::_('COM_ONEPAGE_TOTAL_WEIGHT') ?>: </div>
        <div class="op_col5_3" ><?php echo $opc_show_weight_display ?></div>
  </div>
    <?php } ?>
 
  
  <?php if (!empty($continue_link)) { ?>
  <div class="op_basket_row totals">
    <div style="width: 100%; clear: both; display:none;">
  		 <a href="<?php echo $continue_link ?>" class="continue_link" ><span>
		 	<?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING'); ?></span>
		 </a>
	&nbsp;</div>
  </div>
 
  <?php } ?>


                         </div>
           </div>
           </div></div></div></div></div>
</div>
</div>
</div>
</div>