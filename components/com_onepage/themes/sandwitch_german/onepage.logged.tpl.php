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

$config = OPCconfig::getValue('theme_config', $selected_template, 0, false, false); 

$logged_in_tab = 'COM_ONEPAGE_VISITOR_TEXT'; 
$hide_business_tab = false; 

if (!empty($config) && (isset($config->show_business)))
{
$hide_business_tab = $config->show_business; 
if (isset($config->logged_in_tab))
$logged_in_tab = $config->logged_in_tab; 
}



$BUSINESS_TEXT = JText::_($logged_in_tab); 


echo $intro_article; 
?><div id="top_basket_wrapper"><?php


echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on


echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 


?>
</div>

<!-- start main onepage div, if javascript fails it will remain hidden -->
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div">
<?php if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) {  ?>

<div class="continue_section" style="width:100%; position: relative; margin-bottom: 10px; float: left; clear: both;">
<div style="max-width: 50%;">
<div class="menu_overflow">
<div class="triangle-left">&nbsp;</div>


<div class="opc_menu_active opc_menu_item">
<div class="continue_link_under_basket opc_heading">
 <a href="<?php echo $continue_link ?>" class="continue_link_top" style="color: white;"><span class="span_continue opc_title"><?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></span></a></div>
</div>
</div>
</div>
</div>


<?php } ?>
<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-vali2date" novalidate>
<input type="hidden" name="opc_is_business" value="1" id="opc_is_business" />

<div class="top_section">

<div class="opc_top_inner">
<div class="opc_customer" id="opc_customer_registration">
  <div class="opc_heading"><span class="opc_title"><?php echo $CONTACT_ADDRESS; ?></span></div>
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
<div class="opc_login" id="opc_login_section" style="display: none;">
	<div id="logintab">
	  <div class="opc_heading"><span class="opc_title"><?php echo OPCLang::_('JLOGOUT'); ?></span></div>
	  <div class="opc_inside">
		<div>
		  <p><?php echo OPCLang::_('COM_ONEPAGE_NOTICE_YOUR_CART_WILL_GET_DELETED'); 
		  //Notice: Your cart will get deleted after logout
		  ?></p>
	<div class="field_wrapper">
	<input type="button" name="logout" class="button" value="<?php echo OPCLang::_('JLOGOUT'); ?>" onclick="javascript: return op_logout();"/>

	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	</div>
	</div>

									 
									</div>
 </div>
  </div>
</div>
</div>


<!-- end user registration and fields -->
<!-- shipping address info -->

<div id="opc_shipping_and_shipto_section">


<?php
// stAn we disable ship to section only to unlogged users

if (NO_SHIPTO != '1') { 
?>
<div class="opc_section" id="opc_shipping_section">

<div class="opc_heading"  style="margin-top: 10px;" ><span class="opc_title"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'); ?></span></div>
  <div class="opc_inside">
    <div>


<?php


echo $op_shipto;
?>
  </div>
  </div>
</div>
<?php
}

?>

<?php if ($no_shipping || ($shipping_inside_basket)) echo '<div class="opc_section" style="display: none;">'; ?>
<div class="opc_section" id="opc_shipping_section">
<?php 
// remove if you'd like a green wrapper
//if (false)

// END remove if you'd like a green wrapper
?>
<div class="opc_heading" ><span class="opc_title"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'); ?></span></div>
  <div class="opc_inside">
    <div>
							<!-- shipping methodd -->
<?php
// remove if you'd like a green wrapper

// END remove if you'd like a green wrapper
?>							
								<div id="ajaxshipping" style="width: 100%;">
								<?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
								</div>
<?php
// remove if you'd like a green wrapper
//if (false)

// END remove if you'd like a green wrapper
?>								
							
								<!-- end shipping methodd -->
	</div>
   </div>
</div>  
<?php if ($no_shipping || ($shipping_inside_basket)) echo '</div>'; ?>
<?php 
// remove if you'd like a green wrapper

// END remove if you'd like a green wrapper
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

if (!empty($force_hide_payment)) echo ' style="display: none;" '; ?> >
<?php 

if (!empty($op_payment))
{
?>

<!-- end shipping address info -->
<div class="opc_section" id="opc_payment_section" >
<div class="opc_heading" ><span class="opc_title"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PAYMENT'); ?></span></div>
  <div class="opc_inside">
    <div>
							<!-- shipping methodd -->
								<?php echo $op_payment; ?>
								
								<!-- end shipping methodd -->
	</div>
   </div>

</div>


<?php 
} 
?>
</div>
<!-- end payment method -->

<!-- end payment method -->
  
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



<!-- end ship to address details -->



<?php
	
	
	
if(($show_full_tos) || ($tos_required))
{
?>

				                                                   	
<!-- remove this section if you have 'must agree to tos' disabled' -->


<!-- show full TOS -->
	
<!-- end of full tos -->

<?php 

{
?>
<div class="opc_section" id="opc_tos_section">
<div class="opc_heading" ><span class="opc_title"><?php  echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?></span></div>
  <div class="opc_inside">
    <div>
							<!-- shipping methodd -->
						
								<?php 
								if (!empty($show_full_tos)) { 
								echo $tos_con; ?>
								<br style="clear: both;"/>
								<?php } ?>
								
								
								

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



				
		
	</div>
	
</div>

<?php
}

?>	
</div>								
								
								
	</div>
   </div>

</div>



<?php 
}
}
?>
<!-- end of customer note -->
<div class="opc_section opc_very_bottom" style="margin-top: 20px;" id="opc_bottom_section">
<div id="customernote_wrapper">

<div class="opc_heading" >
<span class="opc_title"><?php 	
									$comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment; ?></span>
<?php 
// echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); 
// OR echo OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');  
?></div>
  <div class="opc_inside" id="customer_note_id">
    <div>
								
								<div>
					<textarea rows="3" cols="30" name="customer_comment" id="customer_note_field" ></textarea>
								</div>
								
	</div>
  </div>
</div>




		                                                   	
<!-- remove this section if you have 'must agree to tos' disabled' -->


<!-- show full TOS -->
<?php
echo $op_coupon; // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
?>

<div class="op_basket_row totals" id="tt_shipping_rate_div_basket" <?php if (($no_shipping == '1') || (!empty($shipping_inside_basket)) || (empty($order_shipping))) echo ' style="display:none;" '; ?>>
	<div class="op_col1_4"  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?> </div> 
	<div class="op_col5_3 opc_total_price"   id="tt_shipping_rate_basket"><?php echo $order_shipping; ?></div>
  </div>
  <div class="op_basket_row totals" id="tt_total_basket_div_basket">
    <div class="op_col1_4"  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?> </div>
    <div class="op_col5_3 opc_total_price"   id="tt_total_basket"><strong><?php echo $order_total_display ?></strong></div>
  </div>

<div class="field_wrapper" style="margin-bottom: 20px;">

<div class="field_wrapper2" >
	<button style="right: 0; top:0;" id="confirmbtn_button" type="submit" autocomplete="off" <?php echo $op_onclick ?>  ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></button>
 </div>
</div>
 
</div>		

<!-- customer note box -->

<!-- end of customer note -->
<!-- some little tricks for virtuemart classes -->
<input type="hidden" name="checkout_last_step" value="1" /><input type="hidden" name="page" value="checkout.onepage" /><input type="hidden" name="onepage" value="1" /><input type="hidden" name="checkout_this_step[]" value="CHECK_OUT_GET_SHIPPING_METHOD" /><input type="hidden" name="checkout_this_step[]" value="CHECK_OUT_GET_PAYMENT_METHOD" /><input type="hidden" name="checkout_this_step[]" value="CHECK_OUT_GET_FINAL_CONFIRMATION" />
<!-- end of tricks -->
<br style="clear: both;"/>
<?php echo $captcha; ?>
<br style="clear: both;"/>
</form>

<!-- end of submit button -->
<!-- end of checkout form -->
<!-- end of main onepage div, if javascript fails it will remain hidden -->
</div>
<div id="tracking_div"></div>


<br style="clear: both; float: none;" />
<br style="clear: both; float: left;" />