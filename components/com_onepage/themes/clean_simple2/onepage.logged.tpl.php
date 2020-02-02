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






if (!empty($ajaxify_cart)) { ?>
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-donotvalidate" novalidate="novalidate">
<?php } 




/*
 Anything before and after this template is generated by a template file /pages/shop.cart.tpl.php of your VM template
 There you can change page title from Cart to Checkout and erase any text and button that are genereated under the checkout page 
*/
echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 


 // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
 ?>
 
<div class="coupon_right" <?php 
if (empty($cl)) {
  echo ' style="float: right;" '; 
}
 ?> >
<?php 
echo $op_coupon; 
?>
</div>

<div class="continue_and_coupon">
<div class="continue_left">
<?php
if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) {
$cl = true; 
  ?>
<div class="continue_shopping2"><a href="<?php echo $continue_link ?>" class="continue_link2"><?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a></div>
<?php 
} 
?>
</div>

</div>

<?php
echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on


if (!empty($checkoutAdvertises)) {
?>
<div id="checkout-advertise-box">
		<?php
		if (!empty($checkoutAdvertises)) {
			foreach ($checkoutAdvertises as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
				<?php
			}
		}
		?>
	</div>
<?php 
}

echo $intro_article; 
?>

<?php if (!empty($paypal_express_button)) { ?>
<div id="op_paypal_express" style="float: right; clear: both; width: 100%; padding-top: 10px;">
 <?php echo $paypal_express_button; ?>
</div>
<?php } ?>
<?php if (!empty($google_checkout_button)) { ?>
<div id="op_google_checkout" style="float: right; clear: both; width: 100%; padding-top: 10px;">
 <?php echo $google_checkout_button;  // will load google checkout button if you have powersellersunite.com/googlecheckout installed
 ?>
</div>
<?php } ?>

<br style="clear: both;" />

<?php 
if (empty($ajaxify_cart)) { 

?>

<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-dontvalidate" novalidate="novalidate">

<?php } ?>

<!-- start main onepage div, if javascript fails it will remain hidden -->
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div">

<!-- user info details -->
<div class="btandst">
<div style="<?php if (empty($no_shipto)) echo 'width: 49%;'; else echo 'width: 100%;'; ?>" class="bt_left">
<h3 class="shipping_h3"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></h3>
<fieldset class="address_fielset"><legend class="sectiontableheader"><?php echo JText::_('COM_VIRTUEMART_YOUR_ACCOUNT_DETAILS');  ?></legend>
<div id="onepage_userfieds">
<?php echo $op_userfields; ?>
</div>
</fieldset>
</div>

<!-- end of user info details -->
<!-- ship to address details -->

<div class="st_right">
<?php if ($no_shipto != '1') { ?>
<h3 class="shipping_h3"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></h3>
<fieldset class="address_fielset">
<legend class='sectiontableheader'><?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); // this is from conf file, it is a title for "Shipping Address" ?></legend>
<div id="onepage_useraddresses">
<?php echo $op_shipto; // user data and his shipping addresse, they are fetched from checkout/get_shipping_address.tpl.php ?>
<br style="line-height: 2em; clear: both;"/>

</div>
</fieldset>


<br style="clear: both;"/>
<?php } ?>

<?php if (!empty($third_address)) { ?>
<h3 class="shipping_h3"><?php echo OPCLang::_('COM_ONEPAGE_THIRD_ADDRESS'); ?></h3>
<fieldset class="other_address">
<?php echo $third_address; ?>
</fieldset>
<br style="clear: both;"/>
<?php } ?>


<!-- shipping methodd -->
<div class="op_inside" <?php if ($no_shipping || ($shipping_inside_basket)) echo 'style="display: none;"'; ?> >
<h3 class="shipping_h3"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'); ?></h3>
<fieldset>

<div id="ajaxshipping">
<?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
</div>
</fieldset>
</div>

<jdoc:include type="modules" name="opc_under_shipping_section" style="none" />
<br style="clear: both; padding-top: 10px;" />
<?php
if (!empty($delivery_date)) {
	
echo $delivery_date; 
 ?>

<?php } ?>	

<!-- end shipping methodd -->
<?php 
$comment = JText::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									$comment = JText::_('COM_VIRTUEMART_COMMENT'); 
									
									
?>





</div>

</div>
<!-- end ship to address details -->
<!-- customer note box -->
<br style="clear: both; padding-top: 10px;" />

<div class="left_half">
<?php if (!empty($op_payment))
{
?>
<div id="payment_top_wrapper" <?php
if (!empty($force_hide_payment)) {
 echo ' style="display: none;" '; 
 
 }
 ?> >

<?php
if (!empty($hide_payment))
{
echo '<div style="display: none;">';
}
?>
<!-- payment method -->
<h3 class="payment_h3"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?></h3>
<fieldset>
<legend class="sectiontableheader"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL') ?>
</legend>
<?php echo $op_payment; ?>
</fieldset>
<?php 
if (!empty($hide_payment))
{
echo '</div>';
}


?></div><?php

}
?>


<!-- end payment method -->
<?php
if (!empty($checkbox_products)) {
	?><div class="op_inside checkbox_wrapper" style="padding-bottom: 10px;" ><h3 class="payment_h3"><?php echo OPCLang::_('COM_ONEPAGE_CHECKBOX_SECTION') ?></h3>
<fieldset>
<?php
	echo $checkbox_products; 
	?>
</fieldset>	&nbsp;
</div>
	<?php
}
?>
</div>



<div class="right_half st_right "  <?php if (!empty($hide_payment)) { ?> style="width: 100%; clear: both; margin: 0; padding: 0; "
<?php }?> >

<!-- end payment method -->
<h3 class="shipping_h3"><?php echo $comment; ?></h3>
<!-- end show TOS and checkbox before button -->
<fieldset>

<div id="customer_note_input">
	<textarea cols="50" rows="3" name="customer_note" id="customer_note_field"></textarea>
</div>
</fieldset>
</div>


<br style="clear: both"/>
<!-- end of customer note -->
<!-- show TOS and checkbox before button -->
<?php
	if(OPCLang::_('COM_VIRTUEMART_AGREEMENT_TOS')){
		$agreement_txt = OPCLang::_('COM_VIRTUEMART_AGREEMENT_TOS');
	}

	

?>


<?php if ($show_full_tos) { ?>
<h3 class="shipping_h3"><?php echo JText::_('COM_VIRTUEMART_CART_TOS'); // change this to 'Agreement' ?></h3>
<fieldset >
<!-- show full TOS -->
<?php echo  $tos_con;
?>
</fieldset>
<!-- end of full tos -->
<?php } 
?>




<!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->
<div id="onepage_info_above_button">
<div id="onepage_total_inc_sh" style="display: none;">
<!-- content of next div will be changed by javascript, please don't change it's id -->
<div id="totalam">
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_subtotal_div"><span id="tt_order_subtotal_txt" class="bottom_totals_txt"></span><span id="tt_order_subtotal" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_payment_discount_before_div"><span id="tt_order_payment_discount_before_txt" class="bottom_totals_txt"></span><span class="bottom_totals" id="tt_order_payment_discount_before"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_discount_before_div"><span id="tt_order_discount_before_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_before" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_shipping_rate_div"><span id="tt_shipping_rate_txt" class="bottom_totals_txt"></span><span id="tt_shipping_rate" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_shipping_tax_div"><span id="tt_shipping_tax_txt" class="bottom_totals_txt"></span><span id="tt_shipping_tax" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_0_div"><span id="tt_tax_total_0_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_0" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_1_div"><span id="tt_tax_total_1_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_1" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_2_div"><span id="tt_tax_total_2_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_2" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_3_div"><span id="tt_tax_total_3_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_3" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_4_div"><span id="tt_tax_total_4_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_4" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_payment_discount_after_div"><span id="tt_order_payment_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_payment_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_discount_after_div"><span id="tt_order_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_genericwrapper_bottom" class="bottomtotals dynamic_lines_bottom" style="display: none;"><span class="bottom_totals_txt dynamic_col1_bottom">{dynamic_name}</span><span class="bottom_totals dynamic_col2_bottom">{dynamic_value}</span><br class="op_clear"/></div>
<div id="tt_total_div" class="bottomtotals dynamic_lines_bottom"><span id="tt_total_txt" class="bottom_totals_txt"></span><span id="tt_total" class="bottom_totals"></span><br class="op_clear"/></div>
</div>
<?php 
/*
* END of order total at the bottom
*/
?>
</div>
<div><br /></div>
<!-- content of next div will be changed by javascript, please don't change it's id -->
<div id="payment_info" style="clear: both;"></div>
<!-- end of total amount and payment info -->
<!-- submit button -->
<?php if ($tos_required) { 
 include(__DIR__.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'tos.php'); 
} 


echo $italian_checkbox;
?>

<div class="opc_captcha"><?php echo $captcha;  ?></div>
<div id="onepage_submit_section" class="newclass">
<input type="submit" value="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'); ?>" id="confirmbtn" class="buttonopc" <?php echo $op_onclick ?> />
</div>	
</div>
<br style="clear: both;"/>
</div>
</form>

<!-- end of submit button -->
<!-- end of checkout form -->
<!-- end of main onepage div, if javascript fails it will remain hidden -->

<div id="tracking_div"></div>
