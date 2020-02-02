<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

$Itemid = JRequest::getVar('Itemid'); 
	if (!empty($Itemid)) $Itemid = '&Itemid='.$Itemid; 
	$lang = JRequest::getVar('lang'); 
	if (!empty($Itemid)) $lang = '&lang='.$lang; 
	$opc_theme = JRequest::getVar('opc_theme', ''); 
	if (!empty($opc_theme)) $opc_theme = '&opc_theme='.$opc_theme; 
	$checkouturl =  JRoute::_('index.php?option=com_virtuemart&view=cart&showbottom=1&nosef=1'.$lang.$Itemid.$opc_theme, true);


?>
	<div class="opc_basket_bottom">
	



	<div id="basket_coupon"><?php 
if (!empty($op_coupon_ajax))
{
echo $op_coupon_ajax; 
}
?></div>
	
	
<div class="op_inside" <?php if ($no_shipping || (!empty($shipping_inside_basket))) echo 'style="display: none;"'; ?> >	
<div class="shipping_section">
<?php if (!empty($shipping_inside_basket))
{
?>	
<div id="ajaxshipping">
<?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
</div>
<?php
}
?>

</div>
	
</div>
	
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
			<div class="discount_label" id="tt_order_payment_discount_before_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT'); ?>:</div> 
			<div id="tt_order_payment_discount_before_basket"  class="discount_desc"></div>
		</div>
		
		<div style="display: none;" id="tt_order_payment_discount_after_div_basket" class="vmsectiontableentry1">
			<div id="tt_order_payment_discount_after_txt_basket" class="discount_label"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT'); ?>:</div> 
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
 else
 {
	 ?>
	 <div id="couponcode_field_txt_discount" class="discount_label"> 
 <span <?php if (empty($coupon_display)) echo ' style="display: none;" '; ?> id="tt_order_discount_after_div_basket">&nbsp;
  <span id="tt_order_discount_after_txt_basket"><?php echo OPCLang::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>
  </span>
 </span>
</div> 
	
<div id="couponcode_field_discount" class="discount_desc">
	<span>
	<span id="tt_order_discount_after_basket"><?php echo $coupon_display ?>
	</span>
	</span>
</div>

	 <?php
 }
 
 ?> 
	
	
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
  
   <?php if (!empty($opc_show_weight_display)) { ?>
   <div class="vmsectiontableentry1" >
        <div class="discount_label" ><?php echo OPCLang::_('COM_ONEPAGE_TOTAL_WEIGHT') ?>: </div>
        <div class="discount_desc" ><?php echo $opc_show_weight_display ?></div>
  </div>
    <?php } ?>
	 <div class="op_basket_row totals dynamic_lines vmsectiontableentry1"  id="tt_genericwrapper_basket" style="display: none;">
        <div class="discount_label dynamic_col1"   >{dynamic_name}: </div>
        <div class="discount_desc dynamic_col2"   >{dynamic_value}</div>
  </div>
  
  <div class="checkbox_products">
   <?php echo $checkbox_products; ?>
  </div>
  
  
  <div class="vmsectiontableentry2" id="tt_total_basket_div_basket">
    <div class="discount_label"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>: </div>
	<div class="discount_desc" id="tt_total_basket"><img src="<?php 
		$full_url = Juri::root(); 
	if (substr($full_url, -1) != '/') $full_url .= '/'; 
	
   //$extJs .= ' var op_loader_img = "'.$full_url.'media/system/images/mootree_loader.gif";';  
   $url = $full_url.'components/com_onepage/themes/extra/img/loader1.gif';   
   echo $url; 
	?>" width="10px" height="10px" alt="" /><?php //echo $order_total_display ?></div>
	<div class="discount_currency"><?php 
	 echo $currency_code_3; 
	?></div>
  </div>
  
  
  <div  <?php if (!empty($one_step)) echo ' style="display: none;" '; ?>>
  <div class="checkout_button_wrap cwrap1" <?php if (!empty($one_step)) echo ' style="display: none;" '; ?>>
    <a href="<?php echo $checkouturl; ?>#full_checkout" class="opcbutton myGreenBackground checkout_button" ><?php echo JText::_('COM_VIRTUEMART_ORDER_REGISTER_GUEST_CHECKOUT'); ?></a>
  </div>
  </div>
  
	</div><!-- end basket discount area -->
 <div class="bottom_notice">
<?php  echo $intro_article; ?>
 </div><!-- end bottom notice -->
 <div class="advertisearea">
<?php

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
?> </div> <?php 
}		
			?>
				<div class="paypal_safer">
				<?php $userid = JFactory::getUser()->get('id'); 
				if (empty($no_login_in_template) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION')) 
				if (empty($userid)) {
			     
				?>
				<div class="relative ">
				<div class="width30 absolute aligntop loginarea">
			<div class="" >
			  <input type="username" id="username_login" name="username_login" class="myinputstyle forcestyle" size="20" autocomplete="username" placeholder="<?php echo OPCLang::_('COM_VIRTUEMART_USERNAME'); ?>" />
			</div>
			
			<div class=" " >
				<input type="password" id="passwd_login" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="myinputstyle" size="20" onkeypress="return submitenter(this,event)"  autocomplete="password" placeholder="<?php echo OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') ?>" />
			</div>
	
	<input type="hidden" name="remember" value="yes" />
	
	<div id="loginbtnfield" class="formField3" >
	
	<input type="button" name="LoginSubmit" class="opcbutton myGreenBackground checkout_button login_button" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>
	
	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	</div>
	
	<span class="lost_pwd" >
	<a title="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');  ?></a>
	</span>
	
				</div>
				</div>
<div  <?php if (!empty($one_step)) echo ' style="display: none;" '; ?>>				
  <div class="checkout_button_wrap cwrap2">
    <a href="<?php echo $checkouturl; ?>#full_checkout" class="opcbutton myGreenBackground checkout_button" ><?php echo JText::_('COM_VIRTUEMART_ORDER_REGISTER_GUEST_CHECKOUT'); ?></a>
  </div>
</div>  
				<?php } ?>
				
				</div>

			
</div><!-- end advertisearea -->

 
 
</div><!-- end opc_basket_bottom -->



