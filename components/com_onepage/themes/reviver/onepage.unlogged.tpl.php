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


 
//joomore hacked;
$comUserOption = 'com_users'; 
if (empty($registration_html)) $no_login_in_template = true; 
echo $intro_article; 
?>
<div id="top_basket_wrapper">
<?php
echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 
echo $op_coupon; // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on
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

if (!empty($paypal_express_button)) { ?>
<div id="op_paypal_express" style="float: right; clear: both; width: 100%; padding-top: 10px;">
 <?php echo $paypal_express_button; ?>
</div>
<?php } 


?>
</div>
<?php

?>
<!-- main onepage div, set to hidden and will reveal after javascript test -->
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div" class="cart-view">

<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-validate">

<!-- login box -->
<?php 
if (!empty($no_login_in_template))  {
 echo '<div style="display: none;">';
}
?>

<div class="op_inside loginsection login-box">
<ul id="tab_selector">
 <li  id="op_login_btn" onclick="javascript: return op_unhideFx(this, 'logintab', 'registertab');"><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></li>
 
 <li  id="op_register_btn" class="active" onclick="javascript: return op_unhideFx(this, 'registertab', 'logintab');" ><?php echo OPCLang::_('COM_VIRTUEMART_REGISTER') ?></li>
</ul>

								    <div id="registertab" class="tabs">
									 <fieldset>
									  <?php	echo $registration_html; ?>
									 </fieldset>
									</div>
									<div id="logintab" class="tabs" style="display: none;">
                                     <fieldset>
                                    	<h3 class="module-title"><span><span><?php echo JText::_('DR_VIRTUEMART_CART_TITLE'); ?></span></span></h3>
                                    <div class="width100">	
        <div class="userdata">
		<div id="form-login-username" class="control-group floatleft width50"> 
			<div class="controls">
				<div class="input formField2">
					<label for="modlgn-username" class="label"><?php echo JText::_('COM_VIRTUEMART_USERNAME'); ?></label>
                    <div class="clear"></div>
                   <input type="text" id="username_login" name="username_login" class="inputbox" size="20" autocomplete="off"  />
                     <div class="clear"></div>
                    <a href="<?php echo JRoute::_('index.php?option='.$comUserOption.'&view=remind'); ?>" class="remind" ><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_USERNAME'); ?></a>
				</div>
			</div>
            
		</div>
		<div id="form-login-password" class="control-group floatleft width50">
			<div class="controls">
				<div class="input formField">
					<label for="modlgn-passwd" class="label"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
                    <div class="clear"></div>
                    <input type="password" id="passwd_login"  name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="inputbox" size="20" onkeypress="return submitenter(this,event)"  autocomplete="off" />
                     <div class="clear"></div>
                    <a href="<?php echo JRoute::_('index.php?option='.$comUserOption.'&view=reset'); ?>" class="reset"><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?></a>
				</div>
			</div>
		</div>
        <div class="clear"></div>
		<div class="width100 remember">
            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
            <input  style="float:left;" type="checkbox" name="remember" id="remember_login" class="rememberinputbox" value="yes" checked="checked" />
            <label style="float:left; margin-left:10px;" class="control-label"  for="remember_login"><?php echo OPCLang::_('JGLOBAL_REMEMBER_ME'); ?></label>
             <div class="clear"></div>
         	  <?php else : ?>
            <input type="hidden" name="remember" value="yes" />
			<?php endif; ?>
			</div>
            <div class="clear"></div>
            <div id="form-login-submit">
			<div class="controls">
				<input type="button" name="LoginSubmit" class="button" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>
			</div>
		</div>
			

	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php if (method_exists('JUtility', 'getToken'))
	echo JUtility::getToken(); 
	else echo JSession::getFormToken(); ?>" value="1" />

	</div>
    </div>
		<div class="clear"></div>						
			
</fieldset>

									</div>
                                    </div>
<?php
if (!empty($no_login_in_template))  {
 echo '</div>';
}
?>

 <div class="clear"></div>
 <div class="cart-view">
		<h3 class="module-title"><span><span><?php echo OPCLang::_('DR_VIRTUEMART_CART_BILLING'); ?></span></span></h3>
		<div class="billing-box after">
			<div class="billto-shipto">
	<div class="width50 floatleft">
    <div class="text-indent" id="bill_to_section">

		<span class="font"><span class="vmicon vm2-billto-icon"></span>
		<?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></span>
		<?php // Output Bill To Address ?>
		<div class="output-billto">
		<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
		<div class="clear"></div>
		</div>
        </div>
		
		<?php if (!empty($delivery_date)) echo $delivery_date; ?>
		
		
	</div>

	<div class="width50 floatleft">
		 <div class="text-indent2" id="shipto_section">
		<span class="font"><span class="vmicon vm2-shipto-icon"></span>
		<?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></span>
       

		<?php // Output Bill To Address ?>
		<div class="output-shipto" >
		<div class="op_shipto_content">
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
                <br style="clear: both;"/>
				
				
				
            </div>
        <div class="clear"></div>
		</div>
		</div>
	</div>

	<div class="clear"></div>
</div>
		</div>
</div>
 <div class="clear"></div>
<!-- end shipping address info -->

<div class="cart-view shipping_method_section op_inside" <?php if ($no_shipping || ($shipping_inside_basket)) echo 'style="display: none;"'; ?>>
                            		<h3 class="module-title"><span><span><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'); ?></span></span></h3>

                        	<div class="op_rounded_content">
								<!-- shipping methodd -->
								<div id="ajaxshipping" style="width: 99%;">
								<?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
								</div>
								<br style="clear: both;"/>
								<!-- end shipping methodd -->

							</div>
</div>


<!-- payment method -->
<?php if (!empty($op_payment))
{

?>

<div id="payment_top_wrapper" <?php
if (!empty($force_hide_payment)) {
 echo ' style="display: none;" '; 
 
 }
 ?> >
 
<div class="cart-view payment_method_section op_inside">
                            <h3 class="module-title"><span><span><?php echo OPCLang::_('COM_VIRTUEMART_CART_PAYMENT'); ?></span></span></h3>
                        	<div class="op_rounded_content">


<?php echo $op_payment; ?>
<br style="clear: both;"/>
								<!-- end shipping methodd -->

							</div>
</div>



</div>
<?php 

} 
?>
<!-- end payment method -->

<!-- customer note box -->

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
 <div class="clear"></div>
<div class="op_inside cart-view custoterms">
                             <h3 class="module-title"><span><span><?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?></span></span></h3>
                        	<div class="op_rounded_content">
								<?php 
                                    echo $tos_con;
                                ?>
								<!-- end shipping methodd -->
							</div>
</div>
<?php 
}
}
?>
<!-- end of customer note -->
 <div class="clear"></div>

<div id="checkoutForm" class="op_inside  cart-view">

                            <h3 class="module-title"><span><span><?php 
									$comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment;
									
									?></span></span></h3>
                        	<div class="op_rounded_content">
                        	 <div>
                        	 <div id="customer_note_block" >
							 <span id="customer_note_input" class="formField">
								<label for="customer_note_field"><?php 	
									$comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment; ?>:</label>
							   <textarea rows="3" cols="30" name="customer_comment" id="customer_note_field" class="customer-comment" ></textarea>
							
							 </span>
							 <br style="clear: both;" />
							 <div id="payment_info" style="padding-top; 20px;"></div>
                        	 </div>
                        	 <div id="rbsubmit" >
                        	   <!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->

							   <div id="onepage_info_above_button">
<div id="onepage_total_inc_sh">
<?php
/*
 content of next divs will be changed by javascript, please don't change it's id, you may freely format it and if you add any content of txt fields it will not be overwritten by javascript 
*/
?>
<div id="totalam">
<div id="tt_order_subtotal_div" class="op_basket_row custom_chec"><span id="tt_order_subtotal_txt" class="bottom_totals_txt"></span><span id="tt_order_subtotal" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_payment_discount_before_div" class="op_basket_row custom_chec"><span id="tt_order_payment_discount_before_txt" class="bottom_totals_txt"></span><span class="bottom_totals" id="tt_order_payment_discount_before"></span><br class="op_clear"/></div>
<div id="tt_order_discount_before_div" class="op_basket_row custom_chec"><span id="tt_order_discount_before_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_before" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_shipping_rate_div" class="op_basket_row custom_chec"><span id="tt_shipping_rate_txt" class="bottom_totals_txt"></span><span id="tt_shipping_rate" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_shipping_tax_div" class="op_basket_row custom_chec"><span id="tt_shipping_tax_txt" class="bottom_totals_txt"></span><span id="tt_shipping_tax" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_0_div" class="op_basket_row custom_chec"><span id="tt_tax_total_0_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_0" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_1_div" class="op_basket_row custom_chec"><span id="tt_tax_total_1_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_1" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_2_div" class="op_basket_row custom_chec"><span id="tt_tax_total_2_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_2" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_3_div" class="op_basket_row custom_chec"><span id="tt_tax_total_3_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_3" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_4_div" class="op_basket_row custom_chec"><span id="tt_tax_total_4_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_4" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_payment_discount_after_div" class="op_basket_row custom_chec"><span id="tt_order_payment_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_payment_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_discount_after_div" class="op_basket_row custom_chec"><span id="tt_order_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_total_div" class="op_basket_row custom_chec"><span id="tt_total_txt" class="bottom_totals_txt"></span><span id="tt_total" class="bottom_totals"></span><br class="op_clear"/></div>
</div>
<?php 
/*
* END of order total at the bottom
*/
?>
</div>
 
<!-- content of next div will be changed by javascript, please don't change it's id -->
 
<!-- end of total amount and payment info -->
<!-- submit button -->
 <div class="clear"></div>
 
 <!-- show TOS and checkbox before button -->
<?php

if ($tos_required)
{

?>
	<div id="agreed_div" class="formLabel2 " style="width: 80%;">
	

<input value="1" type="checkbox" id="agreed_field" name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service"  required="required" autocomplete="off" />
					<label for="agreed_field"><?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					?><a target="_blank" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " >(<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?>)</a><?php } ?></label>
				
		
	</div>
	<div class="formField" id="agreed_input">
</div>


<?php
}
echo $italian_checkbox;
?>
<!-- end show TOS and checkbox before button -->
<?php echo $captcha; ?>							

 <div id="confirmbtn_area">
	<button id="confirmbtn_button" class="vm-button-correct" type="submit" autocomplete="off" onclick="<?php echo $onsubmit; ?>" ><span><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></span></button>
 </div>
<br style="clear: both;"/>
</div>
<!-- end of submit button -->




                        	 </div>
                        	
                        	</div> 
                        	</div>
                        	<div style="clear: both;"></div>
	  
	
</div>

<!-- end of tricks -->


</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
<div id="tracking_div"></div>


<script type="text/javascript">
 jQuery().ready(function() {
	var $ = jQuery; 
	if (typeof $('input[placeholder]').placeholder != 'undefined')
	$('input[placeholder]').placeholder();
	$('#op_login_btn').click(function() {
		$('#op_login_btn').addClass('active');
		$('#op_register_btn').removeClass('active');
		return false;
	});
	
	$('#op_register_btn').click(function() {
		$('#op_register_btn').addClass('active');
		$('#op_login_btn').removeClass('active');						  
		return false;
	});
});	
	</script>