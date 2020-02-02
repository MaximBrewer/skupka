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


require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
$default = new stdClass(); 
$config = OPCconfig::getValue('theme_config', $selected_template, 0, $default, false);

$has_guest_tab = true; 

if (!empty($config) && (isset($config->one_step)))
{
$use_multi_step = $config->one_step; 
$has_guest_tab = $config->has_guest_tab; 
}
else $use_multi_step = true; 
 



if (empty($registration_html)) 
{

$no_login_in_template = true; 
}

echo $intro_article; 


if (!empty($paypal_express_button)) { ?>
<div id="op_paypal_express" style="float: right; clear: both; width: 100%; padding-top: 10px;">
 <?php echo $paypal_express_button; ?>
</div>
<?php } 

echo $google_checkout_button; // will load google checkout button if you have powersellersunite.com/googlecheckout installed



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

?>

<?php

?>
<!-- main onepage div, set to hidden and will reveal after javascript test -->
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div">
<?php if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) {  ?>
<div id="continue_button"  >
	<input type="button" class="bandBoxStyle" onclick="location.href='<?php echo $continue_link; ?>';" value="<?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING'); ?>" />
 </div>
<?php 
} 

?>
<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-ivalidate" autocomplete="off">

<!-- login box -->

<div class="op_inside loginsection" <?php 
	$user = JFactory::getUser(); 
	$uid = $user->get('id'); 
	if (!empty($uid))  { echo 'style="display:none"';} 
	?>>
<div id="register_box" <?php
	
	if (empty($registration_html))  { echo 'style="display:none"';}
		else if (empty($has_guest_tab) || (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION' || (!empty($no_login_in_template)))) echo ' style="width:50%; " '; ?>>
	<div id="register_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_REGISTER') ?></div>
	<div id="register_container">
	<span><?php echo OPCLang::_('COM_ONEPAGE_REGISTER_TEXT') ?></span>
	<?php	echo $registration_html; ?>
	<div class="formField" id="registerbtnfield" >
	<input type="button" name="RegisterSubmit" class="submitbtn bandBoxRedStyle" value="<?php echo OPCLang::_('COM_ONEPAGE_CREATEACCOUNT'); ?>" onclick="return onRegisterPressed(this); " />
	</div>
	</div>
</div>
<div id="login_box" <?php 
	if (!empty($no_login_in_template)) { echo 'style="display:none"; ';}
	else if (empty($has_guest_tab) && empty($registration_html)) { echo 'style="width:100%;"'; }
		else if (empty($has_guest_tab) || empty($registration_html) || (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION')) echo ' style="width: 50%; " '; ?>>
	<div id="login_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></div> 
	<div id="login_container"><span><?php echo OPCLang::_('COM_ONEPAGE_LOGIN_TEXT'); ?></span>
			<div class="formLabel">
			   <label for="username_login"><?php echo OPCLang::_('COM_VIRTUEMART_USERNAME'); ?>:</label>
			</div>
			<div class="formField">
			  <input type="text" id="username_login" name="username_login" class="inputbox" size="20" autocomplete="off" />
			</div>
			<div class="formLabel">
			 <label for="passwd_login"><?php echo OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') ?>:</label> 
			</div>
			<div class="formField" >
				<input type="password" id="passwd_login" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="inputbox" size="20" onkeypress="return submitenter(this,event)"  autocomplete="off" />
			</div>
	
	<input type="hidden" name="remember" value="yes" />
	
	<div id="loginbtnfield" class="formField" >
	<div class="LoginSubmit">
	
	<div class="forgotpwd">
	(<a title="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');  ?></a>)
	</div>
	
	  <div id="loginbdiv">
	<input type="button" name="LoginSubmit" class="submitbtn bandBoxRedStyle loginbtn" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>
	  </div>
	
	</div>
	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	</div>

</div>
</div>
<?php 
?>
<div id="guest_box" <?php 
	if (empty($has_guest_tab) || (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION')) echo ' style="display: none; " '; 
	else if (empty($registration_html) || (!empty($no_login_in_template))) { echo 'style="width:50%; "';} ?> >
<div id="guest_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_ONEPAGE_REGISTER_AND_CHECKOUT'); ?></div>
 <div id="guest_container"><span><?php echo OPCLang::_('COM_ONEPAGE_GUEST_TEXT'); ?></span>
     <div class="formLabel">
		<label for="guest_email"><?php echo OPCLang::_('COM_VIRTUEMART_EMAIL'); ?></label>
	</div>
	<div class="formField">
		<input type="text" id="guest_email" name="guest_email" class="inputbox" size="20" autocomplete="off" onblur="javascript: Onepage.email_check(this);" onkeyup="syncEmails(this);" <?php 
		
		if (!empty($cart->BT['email'])) echo ' value="'.$cart->BT['email'].'"'; 
		
		?> />
	</div>
	<div class="formField" id="guestbtnfield" >
		<input type="button" name="GuestSubmit" class="submitbtn bandBoxRedStyle" value="<?php echo OPCLang::_('COM_ONEPAGE_SUBMIT'); ?>" onclick="return onGuestPressed(this); " />
	</div>
</div>
	</div>
</div>
 <br style="clear: both;"/>


<!-- user registration and fields -->

<div id="usersection" <?php if (empty($uid) && (!empty($use_multi_step) ) && (!empty($registration_html))) echo ' style="display: none;" ';?> >
<div id="billTo_box">
	<div id="billTo_head" class = "bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></div>
	<div id="billTo_container"><?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
	</div>
</div>


<!-- end user registration and fields -->
<!-- shipping address info -->
<?php 
// stAn we disable ship to section only to unlogged users

if (NO_SHIPTO != '1') { ?>

<div id="shipTo_box">
	<div id="shipTo_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></div>
	<div id="shipTo_container">
		<label <?php if (!empty($only_one_shipping_address_hidden)) echo ' style="display: none;" '; ?> >
								<?php if (empty($only_one_shipping_address_hidden)) { ?>
								<input type="checkbox" id="sachone" <?php if (!empty($op_shipto_opened)) echo ' checked="checked" '; ?> name="sa" value="adresaina" onkeypress="showSA(sa, 'idsa');" onclick="javascript: showSA(sa, 'idsa');" autocomplete="off" />
								<?php } else { ?>
								<input type="hidden" id="sachone" name="sa" value="adresaina" onkeypress="showSA(sa, 'idsa');" onclick="javascript: showSA(sa, 'idsa');" autocomplete="off" />								
								<?php } ?>
								<?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');  ?></label>
								<div id="idsa" style="<?php if (empty($op_shipto_opened)) echo 'display: none;'; ?>">
		<?php echo $op_shipto; // will list shipping user fields from ps_userfield::listUserFields with modification of ids and javascripts ?>
		</div>
	</div>
</div>
						
<?php }

 ?>
 
 

<!-- end shipping address info -->

<div id="shipping_box" <?php if ($no_shipping || ($shipping_inside_basket)) echo 'style="display: none;"'; ?>>
	<div id="shipping_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'); ?></div>
	<div id="shipping_container">	
	<!-- shipping methodd -->
		<div id="ajaxshipping" style="width: 90%;">
		<?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
		</div>
		<!-- end shipping methodd -->
	</div>
</div>
	<?php
if (!empty($delivery_date)) { 
echo $delivery_date; 
 ?>

<?php } ?>	

<!-- payment method -->
<?php if (!empty($op_payment))
{

?>
<div id="payment_top_wrapper" <?php
if (!empty($force_hide_payment)) {
 echo ' style="display: none;" '; 
 
 }
 ?> >

<div id="payment_box">
	<div id="payment_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PAYMENT'); ?></div>
	<div id="payment_container"><?php echo $op_payment; ?></div>

</div>	
</div>

<?php 

} 
?>

<?php
if (!empty($checkbox_products)) {
	?><div class="op_inside checkbox_wrapper bandbox_wrapper"  >
	<div id="payment_head" class="bandBoxStyle bandBoxStyleHeader"><?php echo OPCLang::_('COM_ONEPAGE_CHECKBOX_SECTION'); ?></div>
	<div id="payment_container"><?php echo $checkbox_products; ?></div>
	</div>
	<?php
}
?>
<!-- end payment method -->

<!-- customer note box -->
<br style="clear:both;">
<?php
	
if($show_full_tos)
{
?>

				                                                   	
<!-- remove this section if you have 'must agree to tos' disabled' -->


<!-- show full TOS -->
	
<!-- end of full tos -->

<?php 

{
?>
<div id="fullTos_box">
	<div id="fullTos_head" class="bandBoxStyle"><?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?></div>
	<div id="fullTos_container"><?php echo $tos_con; ?>
	</div>
</div>

<?php 
}
}
?>




<!-- end of customer note -->
<div id="comment_box" <?php if (!$show_full_tos) echo ' style="width: 100%;" '; ?> >
	<div id="comment_head" class="bandBoxStyle"><?php 
	$comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
	if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
	echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
	else echo $comment;
	?></div>
	<div id="comment_container">
	<span id="customer_note_input"<?php if (!$show_full_tos) echo ' style="width: 52%;" '; ?> class="formField" >
								<label for="customer_note_box"><?php 	
									$comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment; ?>:</label>
							   <textarea rows="3" cols="30" name="customer_comment" id="customer_note_box" ></textarea>
							
							 </span>
							  
							 
							 <div id="payment_info" style="padding-top: 20px; clear:both"></div>
                        	 
                        	 <div id="rbsubmit" >
                        	   <!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->
		
							   <div id="onepage_info_above_button" >
<div style="display:none;">							   
<div id="onepage_total_inc_sh">
<?php
/*
 content of next divs will be changed by javascript, please don't change it's id, you may freely format it and if you add any content of txt fields it will not be overwritten by javascript 
*/
?>
<div id="totalam">
<div id="tt_order_subtotal_div"><span id="tt_order_subtotal_txt" class="bottom_totals_txt"></span><span id="tt_order_subtotal" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_payment_discount_before_div"><span id="tt_order_payment_discount_before_txt" class="bottom_totals_txt"></span><span class="bottom_totals" id="tt_order_payment_discount_before"></span><br class="op_clear"/></div>
<div id="tt_order_discount_before_div"><span id="tt_order_discount_before_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_before" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_shipping_rate_div"><span id="tt_shipping_rate_txt" class="bottom_totals_txt"></span><span id="tt_shipping_rate" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_shipping_tax_div"><span id="tt_shipping_tax_txt" class="bottom_totals_txt"></span><span id="tt_shipping_tax" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_0_div"><span id="tt_tax_total_0_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_0" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_1_div"><span id="tt_tax_total_1_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_1" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_2_div"><span id="tt_tax_total_2_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_2" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_3_div"><span id="tt_tax_total_3_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_3" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_4_div"><span id="tt_tax_total_4_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_4" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_payment_discount_after_div"><span id="tt_order_payment_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_payment_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_discount_after_div"><span id="tt_order_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_total_div"><span id="tt_total_txt" class="bottom_totals_txt"></span><span id="tt_total" class="bottom_totals"></span><br class="op_clear"/></div>
</div>
<?php 
/*
* END of order total at the bottom
*/
?>
</div> 
</div>
 <div></div>
<!-- content of next div will be changed by javascript, please don't change it's id -->
 
<!-- end of total amount and payment info -->
<!-- submit button -->
 <br />
 
 

</div>
 </div>
</div>
</div>


<!-- show TOS and checkbox before button -->
<?php

if ($tos_required)
{

?>
<div id="comment_box" <?php if (!$show_full_tos) echo ' style="width: 100%;" '; ?> >
	<div id="comment_head" class="bandBoxStyle"><?php 
	echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
	?></div>
	<div id="comment_container">


	<div id="agreed_div" class="formLabel " >
	


					<label for="agreed_field">
					<input value="1" type="checkbox" id="agreed_field" name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service"  required="required" autocomplete="off" />
					<?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					JHTMLOPC::_('behavior.modal', 'a.opcmodal'); 
					
					?><br /><a target="_blank" rel="{handler: 'iframe', size: {x: 500, y: 400}}" class="opcmodal" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " >(<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS').' *'; ?>)</a><?php } ?></label>
				
		
	<?php 
	
echo $italian_checkbox;  
?>
	
	</div>
	
</div>
</div>

<?php
}
?>
<!-- end show TOS and checkbox before button -->



</div>
<div id="top_basket_wrapper">
<?php

echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on

echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 
//echo $op_coupon; // will show coupon if enabled from common/couponField.tpl.php with corrected width to size






?>
</div>

<div id="rbsubmit2">

<div id="continue_button" style="float: left; clear: left; width: 40%;" >




<?php if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) {  ?>

	<input type="button" class="bandBoxStyle bottomcontinue" onclick="location.href='<?php echo $continue_link; ?>';" value="<?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING'); ?>" />
<?php 
} 

?>

	</div>

 <div id="confirm_button_div" <?php if (empty($uid) && (!empty($use_multi_step) ) && (!empty($registration_html))) echo ' style="display: none;" ';?> >
	<input id="confirmbtn_button" type="submit" class="submitbtn bandBoxRedStyle" autocomplete="off" <?php echo $op_onclick ?>  value="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?>" />
 </div>
</div>
<br style="clear: both;"/>

<!-- end of submit button -->


			

                        	 
 <div><?php echo $captcha; ?></div>
 


<!-- end of tricks -->


</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
<div id="tracking_div"></div>



<br style="clear: both; float: none;" />
<br style="clear: both; float: left;" />