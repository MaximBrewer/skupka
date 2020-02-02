
<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

?>




<div id="basket_container">
<div class="inside" >
<div class="black-basket" >
		
            <div ><div ><div ><div >
            <div class="col-module_fix" >                                      
                         <div class="col-module_content" >
                         
<div class="korzina_wapka1">Фото</div>
<div class="korzina_wapka2">Наименование товара</div>
<div class="korzina_wapka3">Цена</div>
<div class="korzina_wapka3">Количество</div>
<div class="korzina_wapka4">Сумма</div>
<div class="korzina_cliar"></div>

 
<?php 
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
//$basePrice = number_format($product['prices']['basePrice'],2, ',', ' ');
$quant = $product['product_quantity']; 
//$discount= number_format($product['prices']['discountAmount']*$quant,2, ',', ' ');




if (!class_exists('CurrencyDisplay'))
	require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
   $currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
//-   $discount = $currencyDisplay->createPriceDiv('billDiscountAmount', '',  $product['prices']['discountAmount']*$quant, true, false, 1, true); // *!* 2017-02-02
   $discount = $currencyDisplay->createPriceDiv('billDiscountAmount', '',  $product['prices']['discountAmount'], true, false, 1, true); // *!* 2017-02-02
   
   $basePrice = $currencyDisplay->createPriceDiv('billDiscountAmount', '',  $product['prices']['basePrice'], true, false, 1, true);
   
   
?>


 <div class="korzina_wapka_ob">
  <div class="korzina_wapka_tovar_1"><?php echo $this->op_show_image($product['product_full_image'], '', 40, 40, 'product'); ?></div>
   <div class="korzina_wapka_tovar_2_2">
     <div class="korzina_wapka_tovar_2"><div class="korzina_otstup_tovar_artikyl"><?php echo $product['product_name'] . $product['product_attributes'] ?></div>      <?php echo $product['product_sku'] ?></div>
   </div>
   <div class="korzina_wapka_tovar_3_3">
     <div class="korzina_wapka_tovar_3">
       <?php echo $basePrice; ?>&nbsp;<?php echo $discount; ?>
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
<div class="korzina_cliar"></div>

 

<?php } ?>
<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="korzina_itogo_1">Итого:</td>
    <td width="50%" align="right" class="korzina_itogo_2"><?php echo $subtotal_display ?></td>
  </tr>
</table>
<?php if (!empty($shipping_inside_basket))
{
?>
  <div class="op_basket_row" >
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
  <div class="op_basket_row">
    <div class="op_col1">&nbsp;</div>
    
    <div class="op_col2_3">
	<div><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?></div>
	<?php echo $payment_select; ?></div>
    <div class="op_col5_3">&nbsp;<span id='payment_inside_basket_cost'></span></div>
  </div>

 
<?php
}
?>
 
  
  
  </div>

  
  
	

  
  
 
 
  
    <?php if (!empty($opc_show_weight_display)) { ?>
   <div class="op_basket_row" >
        <div class="op_col1_4" ><?php echo OPCLang::_('COM_ONEPAGE_TOTAL_WEIGHT') ?>: </div>
        <div class="op_col5_3" ><?php echo $opc_show_weight_display ?></div>
  </div>
    <?php } ?>
  
  <?php 
  
  if (!empty($continue_link)) { ?>
  <div class="op_basket_row">
    <div style="width: 100%; clear: both;">
  		 <a href="<?php echo $continue_link ?>" class="continue_link_ice" ><span>
		 	<?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING'); ?></span>
		 </a>
	&nbsp;</div>
  </div>
  <?php } ?>


              </div>
           </div>
    </div></div></div></div>
</div>
</div>
