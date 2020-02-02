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
  <div class="op_basket_header op_basket_row" style="display: none;">
    <div class="op_col2"><?php echo 'Inhoud'; ?></div>
	<div class="op_col1">&nbsp;</div>
    <div class="op_col3">&nbsp;</div>
    <div class="op_col4"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SKU') ?></div>
    <div class="op_col6"><?php 
	//echo OPCLang::_('COM_VIRTUEMART_CART_QUANTITY')  echo OPCLang::_('COM_VIRTUEMART_CART_ACTION') 
	?>&nbsp;</div>
	<div class="op_col5">&nbsp;</div>
    <div class="op_col7"><?php 
	//echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') 
	
	// we will get rid of the : at the end of Price:
	
	$price_l = OPCLang::_('COM_VIRTUEMART_CART_PRICE');
	$price_l = trim($price_l); 
	if (substr($price_l, -1)===':')
	$price_l = substr($price_l, 0, strlen($price_l)-1); 
	$price_l = trim($price_l); 
	//echo  $price_l; ?></div>
  </div>
  <div class="product_wrapper">
    <div class="inside_product_wrapper">
<?php 
$max = count($product_rows); 
$curr = 0; 
foreach( $product_rows as $product ) { 


   $baseprice = $product['product']->prices['basePrice'];
   // include necessary classes
   $cart = VirtueMartCart::getCart();
   $currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
   // the baseprice must be enabled in the price display config otherwise this will return empty value on some VM versions
   $cd = $currencyDisplay->createPriceDiv('basePrice','', $baseprice,false,false, 1);
   // if you are running one of the latest VM versions, you can use: 
   $cd = $currencyDisplay->createPriceDiv('basePrice','', $baseprice,true,false); // which will return only the rounded amount with currency symbol
   // if it's disabled, you can trick vm to use one of your enabled prices such as
   // $cd = $currencyDisplay->createPriceDiv('salesPrice','', $baseprice,false,false, 1);
  
/*
Available fields for $product['product']->prices['... are : 
 ["costPrice"]=>
  string(7) "4.99000"
  ["basePrice"]=>
  float(3.83610086)
  ["basePriceVariant"]=>
  float(3.83610086)
  ["basePriceWithTax"]=>
  float(0)
  ["discountedPriceWithoutTax"]=>
  float(0)
  ["priceBeforeTax"]=>
  float(3.83610086)
  ["salesPrice"]=>
  float(3.83610086)
  ["taxAmount"]=>
  float(0)
  ["salesPriceWithDiscount"]=>
  float(0)
  ["salesPriceTemp"]=>
  float(3.83610086)
  ["unitPrice"]=>
  float(0.42623342888889)
  ["discountAmount"]=>
  float(-0)
  ["priceWithoutTax"]=>
  float(3.83610086)
  ["variantModification"]=>
  float(0)
  ["DBTax"]=>
  array(0) {
  }
  ["Tax"]=>
  array(0) {
  }
  ["VatTax"]=>
  array(0) {
  }
  ["DATax"]=>
  array(0) {
  }
}
*/


 $curr++;
?>
  <div class="op_basket_row <?php 
    if (($max) != $curr)
	 {
	  //echo ' opc_separator ';
	 }
  ?>">
  <div class="op_col1"><?php 
	
	echo $this->op_show_image($product['product_full_image'], '', 50, 50, 'product'); 
	
	?></div>
    <div class="op_col2_2"><h3><?php echo $product['product_name'].'</h3>' . $product['product_attributes'];
	 //if (empty($product['product_attributes'])) echo '<br />'; 
	 echo $product['product_price']; ?></div>
	
    <div class="op_col4"><?php echo $product['product_sku'] ?></div>
    <div class="op_col6"><div class="vertical_align"><div class="inside_v"><?php
	echo '<div class="ulabel">'.$product['update_form'].'</div>'; ?>
		<?php echo '<div class="dlabel">'.$product['delete_form'] ?></div></div></div></div>
	<div class="vertical_line">&nbsp;</div>
    <div class="op_col5"></div>
    <div class="op_col7"><div class="div1"><div class="div2"><?php 
	 echo $product['subtotal']; 
	?></div></div></div>
  </div>
<?php } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
<?php if (!empty($shipping_inside_basket))
{
?>
  <div class="op_basket_row" style="padding-bottom: 4px;">
    <div class="op_col1"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SHIPPING'); ?></div>
    <div class="op_col2_3" id='shipping_inside_basket'>
    <?php if (!empty($shipping_select)) echo $shipping_select;
	?></div>
    
    <div class="op_col5_3 opc_total_price"><div id='shipping_inside_basket_cost'></div></div>
  
  </div>

<?php
}
if (!empty($payment_inside_basket))
{
?>
  <div class="op_basket_row" >
    <div class="op_col1"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PAYMENT'); ?></div>
    <div class="op_col2_3"><?php echo $payment_select; ?></div>
    <div class="op_col5_3 opc_total_price">&nbsp;<span id='payment_inside_basket_cost'></span></div>
  </div>

 
<?php
}
?>
 <div class="opc_separator2">&nbsp;</div>

  <div class="op_basket_row totals" id="tt_order_subtotal_div_basket" >
    <div class="op_col1_4" id="tt_order_subtotal_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></div>
	<div class="op_col5_3 opc_total_price" id="tt_order_subtotal_basket"><?php echo $subtotal_display ?></div>
  </div>
 <div class="op_basket_row totals" <?php if (empty($discount_before)) echo ' style="display: none;" '; ?> id="tt_order_discount_before_div_basket">
    <div class="op_col1_4"  ><?php echo OPCLang::_('COM_ONEPAGE_OTHER_DISCOUNT'); ?>
    </div> 
    <div class="op_col5_3 opc_total_price"   id="tt_order_discount_before_basket"><?php echo $coupon_display_before; ?></div>
  </div>


  <div class="op_basket_row totals" style="display: none;" id="tt_order_payment_discount_before_div_basket">
    <div class="op_col1_4" id="tt_order_payment_discount_before_txt_basket">
    </div> 
    <div class="op_col5_3 opc_total_price" id="tt_order_payment_discount_before_basket"></div>
  </div>

  <div class="op_basket_row totals" <?php if (empty($discount_after)) echo ' style="display:none;" '; ?> id="tt_order_discount_after_div_basket">
    <div class="op_col1_4" id="tt_order_discount_after_txt_basket" ><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT'); ?>
    </div> 
    <div class="op_col5_3 opc_total_price"   id="tt_order_discount_after_basket"><?php echo $coupon_display ?></div>
  </div>
  <div class="op_basket_row totals" style="display: none;" id="tt_order_payment_discount_after_div_basket">
    <div class="op_col1_4" id="tt_order_payment_discount_after_txt_basket">
    </div> 
    <div class="op_col5_3 opc_total_price"   id="tt_order_payment_discount_after_basket"></div>
  </div>
  
 
  <div>
  <div class="op_basket_row totals" id="tt_shipping_rate_div_basket" <?php if (($no_shipping == '1') || (!empty($shipping_inside_basket)) || (empty($order_shipping))) echo ' style="display:none;" '; ?>>
	<div class="op_col1_4"  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?> </div> 
	<div class="op_col5_3 opc_total_price"   id="tt_shipping_rate_basket"><?php echo $order_shipping; ?></div>
  </div>
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
 
  <div class="op_basket_row totals" id="tt_total_basket_div_basket">
    <div class="op_col1_4"  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?> </div>
    <div class="op_col5_3 opc_total_price"   id="tt_total_basket"><strong><?php echo $order_total_display ?></strong></div>
  </div>
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