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










global $Itemid;
if (!empty($newitemid))
$Itemid = $newitemid;

echo $intro_article; 


?>

<div id="top_basket_wrapper">
<?php
echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 
echo $op_coupon; // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
// 
echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on
// 
echo $google_checkout_button; // will load google checkout button if you have powersellersunite.com/googlecheckout installed
?>

<?php
if (!empty($paypal_express_button)) { ?>
<div id="op_paypal_express" >
 <?php echo $paypal_express_button; ?>
</div>
<?php } 


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
</div>
<div class="dob0">
<!-- main onepage div, set to hidden and will reveal after javascript test -->

<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-valid2ate" novalidate="novalidate" data-ajax="false">
<div class="dob1" id="dob1">
<div class="op_inner">
<?php 

$iter = 0;


if (empty($no_login_in_template))
{

?>
   
<?php
 if (VM_REGISTRATION_TYPE != 'NO_REGISTRATION') 
 {
 
?>

<!-- login box -->


<div data-role="fieldcontain">
<fieldset data-role="controlgroup" data-type="horizontal" >
			      <input type="radio" data-role="button" data-transition="fade" id="logintab_btn" onclick="javascript: return unhideFx(this, 'logintab', 'checkout_section');"  />
				  <label for="logintab_btn"><?php echo OPCLang::_('COM_ONEPAGE_SHOW_LOGIN'); ?></label>
				  <input checked="checked" type="radio" data-role="button" data-transition="fade" id="checkout_section_btn" class="ui-btn-active" onclick="javascript: return unhideFx(this, 'checkout_section', 'logintab');" />
				  <label for="checkout_section_btn"><?php echo OPCLang::_('COM_ONEPAGE_REGISTER_AND_CHECKOUT'); ?></label>
</fieldset>
</div>


                        	
								
								<div>
								<div>
								    
									  
									
									<div id="logintab" style="display: none;">
									    			
			<div data-role="fieldcontain">
			    <label for="username_login" id="label_username_login" class="userfields"><?php echo OPCLang::_('COM_VIRTUEMART_USERNAME'); ?></label>			
				<input type="text" id="username_login" name="username_login" value="" class="inputbox" size="20" autocomplete="off" placeholder="<?php echo addslashes(OPCLang::_('COM_VIRTUEMART_USERNAME')); ?>" />
				

				
			</div>
			
			<div data-role="fieldcontain">
				<label for="passwd_login" id="label_passwd_login" class="userfields"><?php echo OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') ?></label>
				<input type="password" id="passwd_login" placeholder="<?php echo addslashes(OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1')); ?>" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" value="" class="inputbox" size="20" onkeypress="return submitenter(this,event)" autocomplete="off" />
				
				

				
			</div>
			<input type="hidden" name="remember" value="yes" />
	<div >
	<div>
	(<a title="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');; ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?></a>)
	</div>
	<input type="button" name="LoginSubmit" class="op_login_button" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>
	
	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	<br style="clear: both;"/>
	</div>
	
									 
									</div>
								</div>
								
								</div>
								
								

							
							
<?php 
}
?>  
	 


<!-- user registration and fields -->

	
	   
                             
<?php	

}



?>
<div id="checkout_section">
<?php
if (!empty($registration_html))
{
?>
<h4><?php $iter++; echo $iter.'. ';?><?php echo OPCLang::_('COM_VIRTUEMART_YOUR_ACCOUNT_REG') ?> </h4>
<?php
echo $registration_html; 
}
?>
<h4><?php $iter++; echo $iter.'. ';?><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL') ?> </h4>
<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
								
<br style="clear: both;"/>

							
	  
	 




<!-- end user registration and fields -->
<!-- shipping address info -->
</div>
</div>
</div>
<div class="dob2" id="dob2">
<div class="op_inner">
<?php
if (NO_SHIPTO != '1') { ?>
	   <div class="op_rounded_fix" >  
                            
                                    
                                
                        	
	<?php if (empty($only_one_shipping_address_hidden)) { ?>							
<div data-role="fieldcontain">


<fieldset data-role="controlgroup" data-type="horizontal" >
<legend><h4><?php $iter++; echo $iter.'. ';  echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></h4>                           		
</legend>
<div class="shipto_wrapper">
<input <?php if (empty($op_shipto_opened)) echo 'checked="checked" '; ?> data-role="button" data-transition="fade"  type="radio" id="sachone2_btn" name="sa" value=""    onclick="unhideFx2(this, 'sachone', 'checkout_section');" />
<label for="sachone2_btn"><?php echo OPCLang::_('COM_VIRTUEMART_ACC_BILL_DEF'); ?></label>
								
<input <?php if (!empty($op_shipto_opened)) echo 'checked="checked" '; ?> data-role="button" data-transition="fade" type="radio" id="sachone" name="sa" value="adresaina" onkeypress="" onclick="unhideFx2(this, 'sachone2_btn', 'checkout_section');"/>
<label for="sachone">
								<?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');  ?>
</label>
</div>
</fieldset>


</div>

	<?php } else { ?>
	<label for="sachone">
								<?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');  ?>
</label>
								<input type="hidden" id="sachone" name="sa" value="adresaina" onkeypress="showSA(sa, 'idsa');" onclick="javascript: showSA(sa, 'idsa');" autocomplete="off" />								
								<?php } ?>

<div id="idsa" style="<?php if (empty($op_shipto_opened)) echo 'display: none;' ?>">
								
								
								  <?php echo $op_shipto; // will list shipping user fields from ps_userfield::listUserFields with modification of ids and javascripts ?>
</div>
								
								

							
	  </div>
	

<?php } ?>

<!-- end shipping address info -->

<div class="op_inside" <?php if (!empty($no_shipping) || ($shipping_inside_basket)) echo 'style="display: none;"'; ?>>

	 
	
                        	
								<!-- shipping methodd -->
								
<?php 

if ((empty($no_shipping)) && (empty($shipping_inside_basket)))
{

$iter++; echo '<h4>'.$iter.'. '; echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL').'</h4>'; 
}
?>

								<div id="ajaxshipping" resize="false" data-enhance="false">
								  
								<?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
								</div>
								
								
								
								<!-- end shipping methodd -->

							
	 
	 

</div>


<?php if (!empty($op_payment))
{

?>
<div id="payment_top_wrapper" <?php
if (!empty($force_hide_payment)) {
 echo ' style="display: none;" '; 
 
 }
 ?> >


                        	

<h4>
<?php 
$op_payment = str_replace('<br />', '', $op_payment); 


$iter++; echo $iter.'. '; echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); 
?></h4><?php
echo $op_payment; 

?>
</div>




								<!-- end shipping methodd -->

							
  
<?php 

} 
?>
 </div>	   
<!-- end payment method -->
</div>
<div class="dob3" id="dob3">

<div class="op_inner">

<!-- customer note box -->
<!-- end of customer note -->



<h4><?php $iter++; echo $iter.'. '; echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></h4>
<div >
<div id="totalam" >
<div class="ui-grid-a" id="tt_order_subtotal_div" ><div id="tt_order_subtotal_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_order_subtotal" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_order_payment_discount_before_div" ><div id="tt_order_payment_discount_before_txt" class="bottom_totals_txt ui-block-a"></div><div class="bottom_totals ui-block-b" id="tt_order_payment_discount_before"></div></div>
<div class="ui-grid-a" id="tt_order_discount_before_div"><div id="tt_order_discount_before_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_order_discount_before" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_shipping_rate_div"><div id="tt_shipping_rate_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_shipping_rate" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_shipping_tax_div"><div id="tt_shipping_tax_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_shipping_tax" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_0_div"><div id="tt_tax_total_0_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_0" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_1_div"><div id="tt_tax_total_1_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_1" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_2_div"><div id="tt_tax_total_2_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_2" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_3_div"><div id="tt_tax_total_3_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_3" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_tax_total_4_div"><div id="tt_tax_total_4_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_tax_total_4" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a" id="tt_order_payment_discount_after_div"><div id="tt_order_payment_discount_after_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_order_payment_discount_after" class="bottom_totals ui-block-b"></div></div>
<div class="ui-grid-a"  id="tt_order_discount_after_div"><div id="tt_order_discount_after_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_order_discount_after" class="bottom_totals ui-block-b"></div></div>
<div id="tt_genericwrapper_bottom" class="ui-grid-a dynamic_lines_bottom" style="display: none;"><div class="bottom_totals_txt ui-block-a dynamic_col1_bottom">{dynamic_name}</div><div class="bottom_totals ui-block-b dynamic_col2_bottom">{dynamic_value}</div></div>
<div class="ui-grid-a" id="tt_total_div"><div id="tt_total_txt" class="bottom_totals_txt ui-block-a"></div><div id="tt_total" class="bottom_totals ui-block-b"></div></div>
</div>
<div class="op_hr" >&nbsp;</div>
</div>
                           
	   

                        	                        	 
                        	
							 <div data-role="fieldcontain">
								<label for="customer_note_field"><?php 
								    $comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment;
								
								?>:</label>
							   <textarea rows="3" cols="30" name="customer_comment" id="customer_note_field" placeholder="<?php echo addslashes($comment);?>"><?php if (!empty($cart->comment)) echo $cart->comment; ?></textarea>
							
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
<?php 
/*
* END of order total at the bottom
*/
?>
</div>
 
<!-- content of next div will be changed by javascript, please don't change it's id -->
 
<!-- end of total amount and payment info -->
<!-- submit button -->

 
 <!-- show TOS and checkbox before button -->
<?php
	if(OPCLang::_('COM_VIRTUEMART_AGREEMENT_TOS')){
		$agreement_txt = OPCLang::_('COM_VIRTUEMART_AGREEMENT_TOS');
	}



if ($show_full_tos) { ?>
<!-- show full TOS -->
	
<?php echo $tos_con; ?>
<!-- end of full tos -->
<?php } 
	
if ($tos_required)
{

{

?>
	<div id="agreed_div" class="formLabel " >
	<input value="1" type="checkbox" id="agreed_field"  name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service" <?php if (VmConfig::get('agree_to_tos_onorder', 1)) echo ' required="required" '; ?> autocomplete="off" />

					<label for="agreed_field"><?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					JHTMLOPC::_('behavior.modal', 'a.opcmodal'); 
					?><a target="_blank" rel="{handler: 'iframe', size: {x: 500, y: 400}}" class="opcmodal" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " ><br />
					<?php 
					$text = OPCLang::_('COM_VIRTUEMART_CART_TOS'); 
					$text = trim($text); 
					if (!empty($text))
					{
					?>
					(<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?>)
					<?php 
					}
					?>
					</a><?php } ?></label>
				
		
	</div>
	<div class="formField" id="agreed_input">
	<?php echo $italian_checkbox; ?>
</div>


<?php
}

}
?>
<!-- end show TOS and checkbox before button -->


<br style="clear: both;"/>
</div>
<!-- end of submit button -->




                        	 </div>
                        	
                        	
                        	
                        	<div style="clear: both;"></div>
	  
	 

</div>
<div class="bottom_button">
 <div id="payment_info"></div>
	<button id="confirmbtn_button" type="submit" <?php echo $op_onclick ?>  ><span id="confirmbtn"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></span></button>
 </div>
</div>
<!-- end of tricks -->
<?php
echo $captcha; 
?> 

</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->

</div>
<div id="tracking_div"></div>

<script type="text/javascript">


</script>
<br style="end_br" />

<br style="clear: both; float: none;" />
<br style="clear: both; float: left;" />
<script>
var opc_autoresize = false; 
</script>
<div style="min-height: 400px; width: 100%;">&nbsp;</div>