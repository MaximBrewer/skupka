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



// MISSING LANGUAGE STRINGS
$BUSINESS_TEXT = OPCLang::_('COM_ONEPAGE_BUSINESS_TEXT');  // Bezoeker 
$VISITOR_TEXT = OPCLang::_('COM_ONEPAGE_VISITOR_TEXT'); // Zakelijk
$CONTACT_ADDRESS = OPCLang::_('COM_ONEPAGE_CONTACT_ADDRESS');   // Contact adres

// general config: 
$config = OPCconfig::getValue('theme_config', $selected_template, 0, false, false); 

$hide_business_tab = false; 

if (!empty($config) && (isset($config->show_business)))
$hide_business_tab = $config->show_business; 


if (!empty($hide_business_tab)) $opc_is_business = false; 

global $Itemid; 


 
//joomore hacked;
global $jmshortcode;
if (!empty($jmshortcode) && (is_object($jmshortcode)))
$lang = $jmshortcode->shortcode;




if (!empty($newitemid))
$Itemid = $newitemid;

//if (empty($registration_html)) $no_login_in_template = true; 

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
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div">
<?php if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) {  ?>
<div class="continue_link_under_basket"><a href="<?php echo $continue_link ?>" class="continue_link"><?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a></div>
<?php 
} 

?>
<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="for2m-va2lidate" novalidate="novalidate">
<input type="hidden" name="opc_is_business" value="0" id="opc_is_business" />
<!-- login box -->


<div class="top_section">
<div class="opc_menu">
 <div class="menu_overflow"><div id="visitor_div" class="<?php if (empty($opc_is_business)) echo 'opc_menu_active'; else echo 'opc_menu_inactive'; ?> opc_menu_item"><button onclick="return opc_menuClick('visitor');"><span class="opc_title"><?php echo $VISITOR_TEXT; ?></span></button></div>
 <div id="visitor_arrow" class="arrow_visitor" <?php if (!empty($opc_is_business)) echo 'style="display: none;" '; ?> >
  <div class="opc_arrow_white">&nbsp;</div>
 <div class="opc_arrow">&nbsp;</div></div>
 </div>
 
 <div class="menu_overflow" <?php if (!empty($hide_business_tab)) echo ' style="display: none;" '; ?> >
 <div class="<?php if (!empty($opc_is_business)) echo 'opc_menu_active'; else echo 'opc_menu_inactive'; ?>  opc_menu_item" id="business_div" style="margin-top: 10px;"><button onclick="return opc_menuClick('business');"><span class="opc_title"><?php echo $BUSINESS_TEXT; ?></span></button></div>
  <div id="business_arrow" class="arrow_business" <?php if (empty($opc_is_business)) echo 'style="display: none;" '; ?> ><div class="opc_arrow_white">&nbsp;</div><div class="opc_arrow">&nbsp;</div></div>
 </div>


 
 <div class="menu_overflow" <?php if ($no_login_in_template) echo ' style="display: none;" '; ?> >
 <div class="opc_menu_inactive opc_menu_item" id="login_div" style="margin-top: 10px;"><button onclick="return opc_menuClick('login');"><span class="opc_title"><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></span></button></div><div id="login_arrow" class="arrow_login" style="display: none;"><div class="opc_arrow_white">&nbsp;</div><div class="opc_arrow">&nbsp;</div></div>
 </div>



</div>
<div class="opc_top_inner">
<div class="opc_customer" id="opc_customer_registration">
  <div class="opc_heading"><span class="opc_title"><?php echo $CONTACT_ADDRESS ?></span></div>
  <div class="opc_inside">
    <div>
  <?php echo $op_userfields;  
  echo $registration_html;
  ?>
    </div>
  </div>
</div>
<div class="opc_business">
</div>
<?php 


if (!empty($no_login_in_template))  {
 echo '<div style="display: none;">';
}
?>
<div class="opc_login" id="opc_login_section" style="display: none;">
	<div id="logintab">
	  <div class="opc_heading"><span class="opc_title"><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></span></div>
	  <div class="opc_inside">
		<div>
		   <div class="field_wrapper">
			<div class="formLabel">
			   <label for="username_login"><?php echo OPCLang::_('COM_VIRTUEMART_USERNAME'); ?>:</label>
			</div>
			<div class="formField">
			  <input type="text" id="username_login" name="username_login" class="inputbox" size="20" autocomplete="off" />
			</div>
			</div>
			<div class="field_wrapper">
			<div class="formLabel">
			 <label for="passwd_login"><?php echo OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') ?>:</label> 
			</div>
			<div class="formField">
				<input type="password" id="passwd_login" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="inputbox" size="20" onkeypress="return submitenter(this,event)"  autocomplete="off" />
			</div>
			</div>
	<?php 
	if (false)
	{
	if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<div class="field_wrapper">
	<div class="formLabel">	<label for="remember_login"><?php echo OPCLang::_('JGLOBAL_REMEMBER_ME'); ?></label></div>
	<div class="formField">
	<input type="checkbox" name="remember" id="remember_login" value="yes" checked="checked" />
	</div>
	</div>
	<?php else : ?>
	<input type="hidden" name="remember" value="yes" />
	<?php endif; ?>
	<div class="field_wrapper">
	<div>
	<span style="float: left; padding-right: 5%; padding-top: 5px;">
	(<a title="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');  ?></a>)
	</span>
	</div>
	</div>
	<?php
	}
	?>
	<div class="field_wrapper">
	 <div class="formLabel">&nbsp;</div>
	 <div class="formField">
	<button name="LoginSubmit" class="login_button" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></button>

	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	 </div>
	</div>
	</div>

									 
	</div>
 </div>
</div>
 
<?php
if (!empty($no_login_in_template))  {
 echo '</div>';
}
?> 
  
  
</div>
</div>



<!-- end user registration and fields -->
<!-- shipping address info -->

<div id="opc_shipping_and_shipto_section">
<div class="opc_section" <?php if ($no_shipping || ($shipping_inside_basket)) echo 'style="display: none;"'; ?> id="opc_shipping_section">
<?php 
// remove if you'd like a green wrapper
//if (false)
{
// END remove if you'd like a green wrapper
?>
<div class="opc_heading" ><span class="opc_title"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'); ?></span></div>
  <div class="opc_inside">
    <div>
							<!-- shipping methodd -->
<?php
// remove if you'd like a green wrapper
}
// END remove if you'd like a green wrapper
?>							
								<div id="ajaxshipping" style="width: 100%;">
								<?php 
								echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
								</div>
<?php
// remove if you'd like a green wrapper
//if (false)
{
// END remove if you'd like a green wrapper
?>								
								<br style="clear: both;"/>
								<!-- end shipping methodd -->
	</div>
   </div>
<?php 
// remove if you'd like a green wrapper
}
// END remove if you'd like a green wrapper
?>
</div>


<?php
// stAn we disable ship to section only to unlogged users
if (NO_SHIPTO != '1') { 
?>


<div class="opc_section">
<?php


echo $op_shipto;
?>

</div>
<?php 
}
?>
<?php
if (!empty($delivery_date)) { 
echo $delivery_date; 
 ?>

<?php } ?>
</div>
<!-- end shipping address info -->

<!-- payment method -->
<?php

?>
<div id="payment_top_wrapper" <?php 

if (!empty($force_hide_payment)) {
 echo ' style="display: none;" '; 
 
 }
 ?> >
<?php if (!empty($op_payment))
{
?>

<!-- end shipping address info -->
<div class="opc_section" id="opc_payment_section" >
<div class="opc_heading" ><span class="opc_title"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PAYMENT'); ?></span></div>
  <div class="opc_inside">
    <div>
							<!-- shipping methodd -->
								<?php echo $op_payment; ?>
								<br style="clear: both;"/>
								<!-- end shipping methodd -->
	</div>
   </div>

</div>


<?php 
} 
?>
</div>
  <?php
if (!empty($checkbox_products)) {  ?>
<!-- end shipping address info -->
<div class="opc_section checkbox_wrapper" id="checkbox_wrapper" >
<div class="opc_heading" ><span class="opc_title"><?php echo OPCLang::_('COM_ONEPAGE_CHECKBOX_SECTION'); ?></span></div>
  <div class="opc_inside">
    <div>
							
								<?php echo $checkbox_products; ?>
								<br style="clear: both;"/>
								
	</div>
   </div>

</div>


<?php 
} 
?>

<?php 



?>
<div class="opc_section opc_very_bottom" id="opc_bottom_section" >
<!-- end payment method -->


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

<div class="opc_section" id="opc_tos_section" style="width: 100%;">
<div class="opc_heading" ><span class="opc_title"><?php  echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?></span></div>
  <div class="opc_inside">
    <div>
							<!-- shipping methodd -->
								<?php echo $tos_con; ?>
								<br style="clear: both;"/>
								<!-- end shipping methodd -->
	</div>
   </div>

</div>



<?php 
}
}
?>
<div id="customernote_wrapper">

<div class="opc_heading" ><button name="stbutton" onkeypress="javascript:return alterButton(this, 'customer_note_id');" class="button_checkbox_uned" onclick="javascript: return alterButton(this, 'customer_note_id');" autocomplete="off" >
<div>&nbsp;</div>
<span class="opc_title"><?php 	
									$comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment; ?></span></button>
<?php 
?></div>
  <div class="opc_inside" id="customer_note_id" style="display: none;">
    <div>
								
								<div>
					<textarea rows="3" cols="30" name="customer_comment" id="customer_note_field" ></textarea>
								</div>
								
	</div>
  </div>
</div>

<div style="display: none;">
<!-- end of customer note -->



<div class="opc_heading" ><span class="opc_title"><?php  echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?></span></div>
  <div class="opc_inside">
    <div>
							<!-- shipping methodd -->
								                        	 <div>
                        	 <div style="width: 300px; float: left;">
							 <span id="customer_note_input" class="formField">
								<label for="customer_note_field"><?php 	
									$comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment; ?>:</label>
							   
							
							 </span>
							 <br style="clear: both;" />
							 <div id="payment_info" style="padding-top; 20px;"></div>
							 
 

                        	 </div>
                        	 
                        	 <div id="rbsubmit" style="width: 310px; float: right;">
                        	   <!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->
<div id="onepage_info_above_button">
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
 <div><br /></div>
<!-- content of next div will be changed by javascript, please don't change it's id -->
 
<!-- end of total amount and payment info -->
<!-- submit button -->
 <br />
 
<!-- end show TOS and checkbox before button -->

 
<br style="clear: both;"/>
</div>
<!-- end of submit button -->




                        	 </div>
                        	
                        	</div> 
								<br style="clear: both;"/>
								<!-- end shipping methodd -->
	</div>
   </div>

</div>

<!-- show TOS and checkbox before button -->

<?php 
echo $italian_checkbox; 
?>
<div class="field_wrapper" style="margin-bottom: 20px;">

<?php

if ($tos_required)
{

?>
   <div class="field_wrapper"  style="width: 100%;">
	<div id="agreed_div" class="formLabel " style="text-align: left; white-space: normal; width: 5%; float: left;">
	

<input value="1" type="checkbox" id="agreed_field" name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service"  required="required" autocomplete="off" />
    </div>
	<div style="width: 95%; float:left;">
					<label for="agreed_field" style="white-space: normal;"><?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					JHTMLOPC::_('behavior.modal', 'a.opcmodal'); 
					?><a target="_blank" rel="{handler: 'iframe', size: {x: 500, y: 400}}" class="opcmodal" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " > (<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?>)</a><?php } ?></label>

<div class="formField" id="agreed_input">
</div>
					
		
	</div>
	
</div>


<?php
}

?>
<?php echo $captcha; ?>
<div class="field_wrapper2" >
	<button style="right: 0; top:0;" id="confirmbtn_button" type="submit" autocomplete="off" <?php echo $op_onclick ?>  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></button>
 </div>
</div>
<!-- customer note box -->

</div>

<!-- end of tricks -->


</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
<div id="tracking_div"></div>


<br style="clear: both; float: none;" />
<br style="clear: both; float: left;" />

